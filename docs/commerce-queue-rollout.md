# ILENA durable checkout queue rollout

## What this changes

The commerce queue is a MySQL/InnoDB-backed outbox. The order row and its unique `checkout-charge:<order-id>` job are committed in one transaction. The web request can redirect immediately after durable persistence; a long-running worker calls Midtrans and saves payment instructions. Card/token charges remain synchronous because their token is request-scoped. Payment status changes, stock consumption, voucher consumption, and fulfillment remain under the authenticated Midtrans notification/status reconciliation flow. When async checkout is enabled, payment-state emails are also enqueued in the same payment-transition transaction; invoice PDFs remain generated on demand when the customer opens/downloads the invoice, so no large PDF work runs for an unrequested document.

Job payloads can contain checkout contact/payment metadata. They are cleared on successful completion; failed/uncertain jobs retain payload for reconciliation. Restrict database access and retain backups accordingly. No Redis service is required.

## Production rollout (after code deploy)

1. Confirm the deploy checkout is clean and the release commit is the intended one.
2. Run the app migration with the production PHP binary:
   `sudo -u deploy-ilena /usr/local/lsws/lsphp83/bin/lsphp ops/spark-cli.php migrate`
3. Install the worker unit:
   `sudo cp ops/ilena-commerce-worker.service.example /etc/systemd/system/ilena-commerce-worker.service`
4. Start and inspect the worker:
   `sudo systemctl daemon-reload && sudo systemctl enable --now ilena-commerce-worker`
   `sudo systemctl is-active ilena-commerce-worker`
   `sudo journalctl -u ilena-commerce-worker -n 50 --no-pager`
5. Permit the existing deploy account to restart only this service. Add `/etc/sudoers.d/ilena-commerce-worker-deploy` with:
   `deploy-ilena ALL=(root) NOPASSWD: /usr/bin/systemctl restart ilena-commerce-worker.service, /usr/bin/systemctl is-active --quiet ilena-commerce-worker.service`
   Set mode `0440` and validate it with `visudo -cf` before use. The GitHub deployment workflow restarts this worker only when the unit is enabled.
6. Keep `CHECKOUT_ASYNC_ENABLED=false` until migration and worker are active. Then set `CHECKOUT_ASYNC_ENABLED=true` in `.env`; do not print the file or credentials. Restart LSAPI using the server's established restart marker and verify the public origin.
7. Place a controlled low-value order and verify: one unique job; immediate processing page; one worker completion; payment instructions visible; one Midtrans transaction; and paid callback consumes stock/voucher exactly once.

Queue counts for operations (run in the application database):
`SELECT status, job_type, COUNT(*) AS jobs, MIN(created_at) AS oldest FROM commerce_jobs GROUP BY status, job_type ORDER BY status, job_type;`

## Failure and retry rules

- Idempotent Midtrans calls use the persisted checkout operation key and retry up to five attempts with exponential delay.
- Unsupported/non-idempotent payment types are never automatically retried after an ambiguous timeout or worker crash. Their job enters `uncertain`; reconcile Midtrans status before any manual replay.
- Email jobs use bounded retries. SMTP is at-least-once: if the SMTP server accepted a message but the worker lost its connection before recording success, a retry can send a duplicate; the unique job key prevents duplicate enqueueing, not duplicates already accepted by SMTP.
- HTTP 4xx rejection marks the order failed and releases stock/voucher reservations.
- A completed job clears its payload. Investigate `failed`/`uncertain` jobs before replay; never delete or blindly re-enqueue them.

## Rollback

Set `CHECKOUT_ASYNC_ENABLED=false` and restart LSAPI. Existing queued jobs still need the commerce worker until drained or reconciled; do not stop it while jobs are queued/processing. The migration's `down()` intentionally preserves financial job records. Existing synchronous checkout remains available after the flag is disabled.

## Capacity validation

Before increasing traffic, measure worker throughput and queue age with representative product/voucher contention, Midtrans latency, and callback retries. Database-backed queuing prevents web workers from waiting on gateway latency; it does not make one VPS capable of one million simultaneous requests. Add controlled admission/rate limits and scale web, worker, and database capacity based on measured saturation.
