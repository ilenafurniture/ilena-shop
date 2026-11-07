<?php

namespace App\Services;

/** Optional live Biteship rate lookup. Credentials and origin stay in environment config. */
final class BiteshipShippingRateService
{
    private string $lastError = '';

    public function lastError(): string
    {
        return $this->lastError;
    }

    public function isConfigured(): bool
    {
        return trim((string) env('BITESHIP_API_KEY', '')) !== ''
            && preg_match('/^\d{5}$/', trim((string) env('BITESHIP_ORIGIN_POSTAL_CODE', ''))) === 1
            && trim((string) env('BITESHIP_COURIERS', '')) !== '';
    }

    public function rates(array $address, array $cart, bool $forceRefresh = false): array
    {
        $this->lastError = '';
        $apiKey = trim((string) env('BITESHIP_API_KEY', ''));
        $originPostal = trim((string) env('BITESHIP_ORIGIN_POSTAL_CODE', ''));
        $destinationPostal = preg_replace('/\D+/', '', (string) ($address['kodepos'] ?? '')) ?? '';
        $couriers = $this->normalizeCouriers((string) env('BITESHIP_COURIERS', ''));

        if ($apiKey === '' || preg_match('/^\d{5}$/', $originPostal) !== 1 || $couriers === '') {
            $this->lastError = 'not_configured';
            return [];
        }
        if (preg_match('/^\d{5}$/', $destinationPostal) !== 1) {
            $this->lastError = 'destination_postcode_missing';
            return [];
        }

        $items = $this->buildItems($cart);
        if ($items === []) {
            $this->lastError = 'package_dimensions_or_weight_missing';
            return [];
        }

        $payload = [
            'origin_postal_code' => (int) $originPostal,
            'destination_postal_code' => (int) $destinationPostal,
            'couriers' => $couriers,
            'items' => $items,
        ];

        $cacheKey = 'ilena:biteship-rates:v1:' . hash('sha256', serialize($payload));
        if (!$forceRefresh) {
            try {
                $cached = cache()->get($cacheKey);
                if (is_array($cached)) {
                    return $cached;
                }
            } catch (\Throwable $error) {
                // Cache is an optimization only; a cache outage must not block quoting.
            }
        }

        try {
            $client = service('curlrequest', [
                'connect_timeout' => 3,
                'timeout' => 7,
                'verify' => true,
                'http_errors' => false,
                'allow_redirects' => false,
            ]);
            $response = $client->post('https://api.biteship.com/v1/rates/couriers', [
                'headers' => [
                    'Authorization' => $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                $this->lastError = 'provider_http_' . $response->getStatusCode();
                log_message('warning', 'Biteship rates request returned HTTP {status}.', ['status' => $response->getStatusCode()]);
                return [];
            }
            $body = (string) $response->getBody();
            if (strlen($body) > 1_000_000) {
                $this->lastError = 'provider_response_too_large';
                return [];
            }
            $data = json_decode($body, true);
            if (!is_array($data) || empty($data['success']) || !is_array($data['pricing'] ?? null)) {
                $this->lastError = 'invalid_provider_response';
                return [];
            }
            $rates = $this->mapPricing($data['pricing'], (string) ($address['kab'] ?? ''));
            if ($rates !== []) {
                try {
                    cache()->save($cacheKey, $rates, 30);
                } catch (\Throwable $error) {
                    // The live quote remains usable without caching.
                }
            }
            return $rates;
        } catch (\Throwable $error) {
            $this->lastError = 'provider_unavailable';
            log_message('warning', 'Biteship rates request failed ({type}).', ['type' => get_class($error)]);
            return [];
        }
    }

    private function normalizeCouriers(string $couriers): string
    {
        $codes = array_values(array_unique(array_filter(array_map(
            static fn (string $code): string => strtolower(trim($code)),
            explode(',', $couriers)
        ), static fn (string $code): bool => preg_match('/^[a-z0-9_-]{2,30}$/', $code) === 1)));
        return implode(',', array_slice($codes, 0, 20));
    }

    private function buildItems(array $cart): array
    {
        $items = [];
        foreach ($cart as $item) {
            $product = $item['detail'] ?? null;
            $quantity = (int) ($item['jumlah'] ?? 0);
            if (!is_array($product) || $quantity < 1 || $quantity > 100) {
                return [];
            }
            $description = json_decode((string) ($product['deskripsi'] ?? '{}'), true);
            $description = is_array($description) ? $description : [];
            $package = is_array($description['dimensi']['paket'] ?? null) ? $description['dimensi']['paket'] : [];
            $actual = is_array($description['dimensi']['asli'] ?? null) ? $description['dimensi']['asli'] : [];
            $dimensions = array_merge($actual, $package);
            $weightKg = (float) ($dimensions['berat'] ?? $actual['berat'] ?? 0);
            $weightGrams = (int) ceil($weightKg * 1000);
            $name = trim((string) ($product['nama'] ?? 'Furniture Ilena'));
            if ($weightGrams < 1 || $weightGrams > 1_000_000 || $name === '') {
                return [];
            }

            $apiItem = [
                'name' => mb_substr($name, 0, 120, 'UTF-8'),
                'value' => max(1, (int) round((float) ($product['harga'] ?? 1))),
                'weight' => $weightGrams,
                'quantity' => $quantity,
            ];
            foreach (['length' => 'panjang', 'width' => 'lebar', 'height' => 'tinggi'] as $apiDimension => $sourceDimension) {
                $size = (float) ($dimensions[$sourceDimension] ?? 0);
                if ($size > 0 && $size <= 1000) {
                    $apiItem[$apiDimension] = (int) ceil($size);
                }
            }
            $items[] = $apiItem;
        }
        return $items;
    }

    private function mapPricing(array $pricing, string $destination): array
    {
        $rates = [];
        $seen = [];
        foreach ($pricing as $row) {
            if (!is_array($row)) {
                continue;
            }
            $price = (int) ($row['price'] ?? 0);
            $courierCode = strtolower(trim((string) ($row['courier_code'] ?? '')));
            $serviceCode = strtolower(trim((string) ($row['courier_service_code'] ?? $row['type'] ?? '')));
            $courierName = trim((string) ($row['courier_name'] ?? ''));
            $serviceName = trim((string) ($row['courier_service_name'] ?? ''));
            if ($price < 1 || $courierCode === '' || $courierName === '' || $serviceCode === '') {
                continue;
            }
            $identity = $courierCode . ':' . $serviceCode;
            if (isset($seen[$identity])) {
                continue;
            }
            $seen[$identity] = true;
            $rates[] = [
                'nama' => $courierName,
                'deskripsi' => $serviceName !== '' ? $serviceName : trim((string) ($row['description'] ?? '')),
                'harga' => $price,
                'estimasi' => $this->formatDuration((string) ($row['duration'] ?? '')),
                'provider' => 'biteship',
                'service_code' => $identity,
                'tariff_id' => $identity,
                'destination' => $destination,
                'free_shipping_eligible' => false,
                'logo' => $this->logoFilename($courierCode),
            ];
        }
        return $rates;
    }

    private function logoFilename(string $courierCode): string
    {
        return [
            'anteraja' => 'anteraja',
            'dakota' => 'dakota',
            'indah' => 'indah',
            'jnt' => 'J&T',
            'j&t' => 'J&T',
            'jne' => 'jne',
            'lion' => 'lion',
            'ninja' => 'ninja',
            'pos' => 'pos',
            'sicepat' => 'sicepat',
            'tiki' => 'tiki',
            'wahana' => 'wahana',
        ][$courierCode] ?? '';
    }

    private function formatDuration(string $duration): string
    {
        if (preg_match('/(\d+)\s*(?:-|–|sampai|to)\s*(\d+)/iu', $duration, $match) === 1) {
            return $match[1] . ' - ' . $match[2];
        }
        if (preg_match('/\b(\d+)\b/u', $duration, $match) === 1) {
            return $match[1];
        }
        return '';
    }
}
