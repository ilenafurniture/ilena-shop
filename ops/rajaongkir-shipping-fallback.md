# RajaOngkir fallback for checkout

Checkout uses the local APM tariff first. RajaOngkir is queried only when APM has no valid tariff for the selected destination. The customer sees up to the three cheapest RajaOngkir options. If neither source provides a quote, checkout keeps the CS confirmation path and does not invent a shipping charge.

Configure these values in the VPS application's `.env`; never put the secret in Git or browser JavaScript:

```dotenv
RAJAONGKIR_SHIPPING_API_KEY=Shipping-Cost-API-key
RAJAONGKIR_ORIGIN_LOCATION_ID=RajaOngkir-origin-location-id
RAJAONGKIR_COURIERS=jne:sicepat:jnt:tiki:pos:ninja:wahana:lion
```

Use the **Shipping Cost API key**, not a key for Shipping Delivery. Obtain the origin location ID from RajaOngkir's Shipping Cost domestic-destination search using the warehouse postcode. Confirm the returned province, city, district, and postcode refer to the actual dispatch point before saving the ID. The destination location is resolved from the saved address postcode plus province, city/regency, district, and village names; the resolved ID is cached for 30 days. Checkout rates are cached for 30 seconds; final checkout revalidates a RajaOngkir option against a fresh rate.

The rate request sends total cart weight in grams from the product's package weight (falling back to actual weight only when package weight is absent), and requests the lowest price. Keep the courier codes limited to services enabled for the account. RajaOngkir's published Starter tier currently lists 100 Shipping Cost API hits per day; APM-first routing and short-lived quote caching limit calls, but monitor usage in the provider dashboard.

After adding or changing these settings, restart the LiteSpeed PHP workers and confirm the application returns APM rates for covered destinations and RajaOngkir options for a destination absent from the APM file. Logs report safe error categories and numeric provider codes only; they do not include the API key or customer address.
