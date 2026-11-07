# Optional Biteship rate fallback

The checkout keeps APM as the primary rate source and displays at most the three cheapest Biteship quotes as secondary options when configured.

Set these values in the VPS `.env` (never commit the live API key):

```dotenv
BITESHIP_API_KEY=
BITESHIP_ORIGIN_POSTAL_CODE=
BITESHIP_COURIERS=
```

- `BITESHIP_API_KEY`: Biteship API token, stored only in the server environment.
- `BITESHIP_ORIGIN_POSTAL_CODE`: five-digit postal code of the actual dispatch warehouse.
- `BITESHIP_COURIERS`: comma-separated courier codes enabled in the Biteship account, for example `jne,sicepat,wahana`.

All three settings are required; if any are missing, Biteship stays disabled and APM continues to work normally. Destination postal code comes from the customer's selected saved address. The quote request uses each cart item's package weight and available dimensions. It is cached briefly to avoid duplicate rate requests.

When a customer selects Biteship, the selected service and exact price are checked again against a fresh quote at final order submission. If the service disappears or the price changes, checkout returns to shipping selection instead of charging the stale amount.

Promotion policy is enforced server-side:

- If APM returns at least one valid rate, only APM rates can receive Ilena's free-shipping promotion. Biteship quotes remain paid alternatives.
- If APM returns no valid rate and Biteship does, Biteship becomes the fallback and may receive the existing promotion only when its region, minimum-order, and maximum-subsidy conditions pass.
- If neither provider returns a usable quote, checkout continues to the existing manual confirmation/contact flow.

The integration requests rates only. It does not create Biteship shipping orders or labels.
