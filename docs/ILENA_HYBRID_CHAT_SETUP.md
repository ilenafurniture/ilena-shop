# Ilena Hybrid AI + Human Chat — server activation

## Runtime behavior

- Guest conversations are owned by a random, HttpOnly, SameSite=Lax browser token; only its SHA-256 hash is stored.
- The storefront makes no chat request until a visitor opens the widget. It polls briefly only while the widget is open; it does not hold an LSAPI request open.
- AI jobs are queued in MySQL and answered by the independent CLI worker, never by the customer-facing PHP request.
- Inactive conversations and their messages are removed after 180 days by the worker to bound database growth (`ILENA_CHAT_RETENTION_DAYS` can be set from 30 to 730 days).
- Without an AI API key, product retrieval and human handoff still work, but generated AI replies are replaced by a grounded fallback message.
- Staff access uses the `support_chat` RBAC permission. Dev always has access; operational roles include the permission going forward.

## 1. Database

Run the pending CodeIgniter migrations with the same LSPHP 8.3 runtime used by the site:

```bash
cd /mine/ilenafurniture.com
/usr/local/lsws/lsphp83/bin/lsphp ops/spark-cli.php migrate
```

The migration creates `ilena_chat_conversations`, `ilena_chat_messages`, `ilena_chat_jobs`, `ilena_chat_knowledge`, and `ilena_chat_product_specs`. The existing product table is not altered. Product colors/materials/dimensions are entered in **Admin → Live Chat → Spesifikasi produk**.

## 2. AI provider configuration

Add these values to the server-only `.env`; do not commit the key:

```dotenv
ILENA_CHAT_AI_ENDPOINT="https://YOUR_OPENAI_COMPATIBLE_PROVIDER/v1/chat/completions"
ILENA_CHAT_AI_MODEL="YOUR_CHAT_MODEL"
ILENA_CHAT_AI_API_KEY="YOUR_SECRET_KEY"
ILENA_CHAT_RETENTION_DAYS=180
```

The provider must accept the OpenAI Chat Completions request/response shape. Keep the credential server-side. The worker sends only the customer's latest question and retrieved public product/knowledge snippets; it does not send customer email or the full conversation history.

## 3. Background worker (systemd; no cron)

Copy `ops/ilena-chat-worker.service.example` to `/etc/systemd/system/ilena-chat-worker.service`, replace `YOUR_APP_USER` with the Linux account that can read `.env` and write `writable/`, then run:

```bash
systemctl daemon-reload
systemctl enable --now ilena-chat-worker
systemctl status ilena-chat-worker --no-pager
journalctl -u ilena-chat-worker -n 50 --no-pager
```

The worker runs independently from LSAPI and sleeps when there are no queued AI replies. Do not add a cron job for it.

## 4. Admin and content

1. Open `/admin/rbac` as Dev and confirm the relevant staff role has **Live Chat & Knowledge Base**.
2. Add and publish approved warranty, shipping, FAQ, and policy content under **Knowledge base**. A draft is never sent to the assistant.
3. Add verified product specs for colors, materials, and dimensions. Recommendations remain grounded in active catalog products and use their existing product detail and cover-image routes.
4. Open `/admin/support-chat`, send a test message from a private browser window, verify the assistant response, and test **Minta bantuan staf** and a staff reply.

The API key, database migration, and systemd service are activation requirements. Before they are configured, the existing site remains usable and the widget falls back to WhatsApp plus grounded catalog matching.
