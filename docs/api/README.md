# ILENA Customer API v1

This API is the customer-facing foundation for native Android/iOS clients. It does not expose admin, staff, warehouse, or developer operations; those remain website-only.

## Current scope

- Public catalog, categories, and paginated Indonesian address data.
- Customer password sign-in, profile, and device-token logout.
- Google customer sign-in using a verified ID token, a server-issued single-use nonce, and exact-primary-email linking only.
- Native customer registration, six-digit email verification, and verification-code resend. CAPTCHA is enforced if the website's server-side CAPTCHA setting is enabled.
- Customer-owned addresses, persistent cart, wishlist, and order history.
- Delivery estimates calculated from the customer's saved address/cart. Estimates are informational; checkout must recalculate price, availability, promotion eligibility, and delivery options.
- Native checkout quote and order creation. Checkout recomputes prices, stock, automatic promotions and delivery; reserves stock, persists the order and payment job, then returns `202` while the commerce worker requests Midtrans payment instructions. Poll the authenticated order endpoint for payment state and instructions.
- OpenAPI contract: `openapi-v1.yaml`.

## Authentication

1. `POST /api/v1/auth/login` with JSON `email` and `password`.
2. Store `data.access_token` only in Android Keystore / iOS Keychain, never in plain preferences or logs.
3. Send `Authorization: Bearer <access_token>` on protected endpoints.
4. `POST /api/v1/auth/logout` revokes only the current device token. Tokens expire after 30 days. Re-authenticate after expiry or revocation.

Customer accounts must be email-verified before password sign-in. Admin/staff accounts and suspended accounts are rejected. Password recovery sends the existing secure single-use ILENA website link; completing a reset invalidates the customer's existing API tokens. Google sign-in calls `GET /api/v1/auth/google/nonce`, passes `data.nonce` as the OIDC nonce in the native Google sign-in request, then sends the returned signed ID token and the same nonce to `POST /api/v1/auth/google`. The server validates Google JWKS signature, issuer, audience, time claims, verified email, and the unused nonce. Configure the native OAuth client so the ID-token audience matches server `GOOGLE_OAUTH_CLIENT_ID`. Existing accounts auto-link only when Google's verified email exactly matches the primary account email; alias-only matches require signing in to the existing account and linking from account settings.

## Native checkout sequence

1. Keep cart, addresses and region selection in native screens; call `POST /api/v1/account/shipping/estimate` with the saved `address_id` and show the returned delivery choices.
2. Before the final review/payment confirmation, call `POST /api/v1/account/checkout/quote` with `address_id` and the selected `option_id`. Render only server-calculated lines, promotions, shipping and payment totals.
3. After the customer confirms, call `POST /api/v1/account/checkout/orders` with those IDs and the payment method. Include a new UUID `Idempotency-Key`; reuse the same key/body only when retrying that exact network request. `202` means order/payment setup is processing, not that payment is complete.
4. Poll `GET /api/v1/account/orders/{order_id}` with bounded backoff until `payment.state` is `awaiting_payment`, `failed`, or `needs_reconciliation`. Display the payment instructions returned by the API. Never send or log card PAN/CVV; tokenize with the provider's native SDK and send only its token.
5. Admin/staff work remains website-only. This repository currently contains the server API, not a compiled Android/iOS client; native screens must be built against this contract. Order cancellation/returns, customer reviews/Q&A and push notifications remain separate API work before complete feature parity can be claimed.

## Example requests

```http
GET /api/v1/products?q=sofa&page=1&per_page=20&locale=id
```

```http
POST /api/v1/auth/login
Content-Type: application/json

{"email":"customer@example.com","password":"..."}
```

```http
POST /api/v1/auth/register
Content-Type: application/json

{"name":"Customer Name","email":"customer@example.com","phone":"081234567890","password":"at-least-12-characters","accept_terms":true,"locale":"id"}
```

The response is `202`; then call `POST /api/v1/auth/verify-email` with the same email and the six-digit code. Successful verification returns the bearer token. `POST /api/v1/auth/resend-verification` sends a replacement code for a pending account and returns a generic message.

Password recovery uses `POST /api/v1/auth/password-reset` and, after the customer follows the emailed link, the existing ILENA website reset page. Native deep-link password reset is not enabled until Android App Links/iOS Universal Links are configured and verified.

Google sign-in nonce sequence:

```http
GET /api/v1/auth/google/nonce
```

```http
POST /api/v1/auth/google
Content-Type: application/json

{"id_token":"<signed Google ID token>","nonce":"<nonce returned by the API>"}
```

The nonce is short-lived, single-use, and stored only as a SHA-256 hash. If sign-in fails after challenge consumption, request a fresh nonce.

```http
PUT /api/v1/account/cart
Authorization: Bearer <token>
Content-Type: application/json

{"items":[{"product_id":"PRODUCT_ID","variant":"Natural","quantity":1}]}
```

```http
POST /api/v1/account/shipping/estimate
Authorization: Bearer <token>
Content-Type: application/json

{"address_id":0}
```


## Operational rollout

1. Deploy code and run the project's normal migration command so `customer_api_tokens`, `customer_google_auth_nonces`, and `customer_checkout_idempotency` exist; ensure `ilena-commerce-worker` is enabled and active.
2. Verify catalog/region reads, customer sign-in, cross-account 404 behavior, token revocation, cart/address ownership, quote recalculation, duplicate idempotency replay, stock contention, queued payment instructions, webhook settlement and checkout failure cleanup in staging.
3. Set `GOOGLE_OAUTH_CLIENT_ID` on the server before enabling native Google sign-in; keep Google client secrets and all shipping/payment provider secrets server-side.
4. Run an Android/iOS client against staging before public release. Preserve TLS verification and do not embed provider or server secrets in the app.
5. Keep API v1 under `/api/v1`; add a new version for breaking response changes.

Run the project test suite with PHP 8.2+ (prefer PHP 8.3). Never enable production mobile checkout until the staging payment, webhook and recovery paths have been exercised end-to-end.
