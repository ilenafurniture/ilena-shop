# Ilena Hybrid AI + Human Chat — server activation

## Runtime behavior

- Guest conversations are owned by a random, HttpOnly, SameSite=Lax browser token; only its SHA-256 hash is stored.
- The storefront makes no chat request until a visitor opens the widget. It polls briefly only while the widget is open; it does not hold an LSAPI request open.
- AI jobs are queued in MySQL and answered by the independent CLI worker, never by the customer-facing PHP request.
- Inactive conversations and their messages are removed after 180 days by the worker to bound database growth (`ILENA_CHAT_RETENTION_DAYS` can be set from 30 to 730 days).
- Without an AI API key, product retrieval and human handoff still work, but generated AI replies are replaced by a grounded fallback message.
- Staff handoff is available from 08:00 to 16:00 WIB (Asia/Jakarta). Customers can still send messages after hours; the assistant confirms the hours, stores the message for the admin inbox, and offers a direct WhatsApp link.
- Customer chat sends are limited per visitor and per client IP; provider calls run only from the worker, require HTTPS on port 443, pin a public IPv4 address, do not follow redirects, and cap response size.
- The assistant answers only Ilena furniture/store questions. Off-topic questions receive a short, polite redirect to WhatsApp.
- Staff access uses the `support_chat` RBAC permission. Dev always has access; operational roles include the permission going forward.

## 1. Database

Run the pending CodeIgniter migrations with the same LSPHP 8.3 runtime used by the site:

```bash
cd /mine/ilenafurniture.com
/usr/local/lsws/lsphp83/bin/lsphp ops/spark-cli.php migrate
```

The migration creates `ilena_chat_conversations`, `ilena_chat_messages`, `ilena_chat_jobs`, `ilena_chat_knowledge`, and `ilena_chat_product_specs`. The existing product table is not altered. Product colors/materials/dimensions are entered in **Admin → Live Chat → Spesifikasi produk**.

## 2. Dev-managed AI provider

The Dev role configures an OpenAI-compatible provider from **Admin → Live Chat → Provider AI**. This supports providers that offer a free model tier; use their exact HTTPS Chat Completions endpoint, model ID, and Bearer API key. The settings form is hidden from operational staff and checks Dev role again server-side.

Before saving a provider API key, add a separate encryption key to the server-only `.env`:

```dotenv
ILENA_CHAT_CREDENTIAL_KEY="GENERATE_AT_LEAST_32_RANDOM_CHARACTERS"
ILENA_CHAT_RETENTION_DAYS=180
```

Generate the key once with `openssl rand -hex 32`. Keep it stable and private: changing it makes the stored provider credential undecryptable. The API key is encrypted before it is stored in the settings table. The worker reads it only when processing a queued chat job. Never commit either secret or send it in chat. Existing `ILENA_CHAT_AI_ENDPOINT`, `ILENA_CHAT_AI_MODEL`, and `ILENA_CHAT_AI_API_KEY` environment settings remain a fallback until a Dev saves a provider profile.

The provider must accept the OpenAI Chat Completions request/response shape and Bearer authentication; a regular Gemini-native endpoint, for example, is not interchangeable unless the provider offers a compatible endpoint. The worker sends only the customer's latest question and retrieved public product/knowledge snippets; it does not send customer email or the full conversation history.

### OpenRouter Free quick setup

The Dev-only **Gunakan OpenRouter Free** button fills the compatible endpoint and model ID:

```text
Endpoint: https://openrouter.ai/api/v1/chat/completions
Model:    openrouter/free
```

This router automatically selects an available free model, but it does **not** create credentials. Create an OpenRouter account/API key in its dashboard, paste the key into the API key field, enable the provider, and save. Free model availability and rate limits can change; use the fallback/human handoff when provider capacity is unavailable. Review the provider's data handling before sending customer questions to it. The linked `awesome-free-llm-apis` repository is a catalog, not a source of reusable API keys.

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
3. As Dev, open **Provider AI**, enter the selected free-tier provider's endpoint, model ID, and key, then enable it. If using an existing environment-based key for the first save, leave the key field blank and it will be encrypted into the settings table.
4. Add verified product specs for colors, materials, and dimensions. Recommendations remain grounded in active catalog products and use their existing product detail and cover-image routes.
5. Open `/admin/support-chat`, send a test message from a private browser window, verify the assistant response, and test **Minta bantuan staf** and a staff reply.

The provider encryption key, provider profile, database migration, and systemd service are required for generative AI. Without a configured provider, the storefront remains usable with grounded catalog matching and human handoff; the assistant uses a fallback response.
