# Google sign-in setup

Google sign-in is intentionally hidden until all three server-side values are set. Never commit the client secret or paste it into chat.

## Google Cloud Console

1. Create or select a Google Cloud project and configure its OAuth consent screen with the Ilena Furniture app name, support contact, and approved domains.
2. Create an OAuth client of type **Web application**.
3. Add the site's exact origin to **Authorized JavaScript origins**: `https://ilenafurniture.com`.
4. Add this exact **Authorized redirect URI**: `https://ilenafurniture.com/auth/google/callback`.
5. Keep the Client ID and Client Secret private; configure them only on the production server.

## Server `.env`

```dotenv
GOOGLE_OAUTH_CLIENT_ID="YOUR_CLIENT_ID.apps.googleusercontent.com"
GOOGLE_OAUTH_CLIENT_SECRET="YOUR_CLIENT_SECRET"
GOOGLE_OAUTH_REDIRECT_URI="https://ilenafurniture.com/auth/google/callback"
```

Then deploy the application and run its pending CodeIgniter migrations. The migration creates `user_google_identities` (only Google `sub`, linked Ilena email, provider, and link timestamp) plus `user_password_resets` (hashed, expiring, one-time reset tokens). Google sign-in accepts verified Google email claims and customer roles only; it does not issue an admin role. Existing email/password accounts must sign in first and explicitly connect Google from the account page; the app does not silently link identities based on matching email. If OAuth configuration is absent or provider verification fails, the user returns to the regular email sign-in flow.

Use a separate OAuth client for local development and register its local callback URI in that client; do not change the production redirect URI to test locally.
