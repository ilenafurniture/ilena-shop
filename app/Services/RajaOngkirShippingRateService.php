<?php

namespace App\Services;

/** Live RajaOngkir Shipping Cost fallback; never called when APM has a quote. */
final class RajaOngkirShippingRateService
{
    private const BASE_URL = 'https://rajaongkir.komerce.id/api/v1';

    private string $lastError = '';

    public function lastError(): string
    {
        return $this->lastError;
    }

    public function isConfigured(): bool
    {
        return trim((string) env('RAJAONGKIR_SHIPPING_API_KEY', '')) !== ''
            && preg_match('/^\d{1,12}$/', trim((string) env('RAJAONGKIR_ORIGIN_LOCATION_ID', ''))) === 1
            && $this->couriers() !== '';
    }

    public function rates(array $address, array $cart, bool $forceRefresh = false): array
    {
        $this->lastError = '';
        $apiKey = trim((string) env('RAJAONGKIR_SHIPPING_API_KEY', ''));
        $originId = trim((string) env('RAJAONGKIR_ORIGIN_LOCATION_ID', ''));
        $couriers = $this->couriers();
        if ($apiKey === '' || preg_match('/^\d{1,12}$/', $originId) !== 1 || $couriers === '') {
            $this->lastError = 'not_configured';
            log_message('warning', 'RajaOngkir fallback skipped: configuration incomplete (key={key}, origin_id={origin}, couriers={couriers}).', [
                'key' => $apiKey !== '' ? 'set' : 'missing',
                'origin' => preg_match('/^\d{1,12}$/', $originId) === 1 ? 'valid' : 'missing_or_invalid',
                'couriers' => $couriers !== '' ? 'valid' : 'missing',
            ]);
            return [];
        }

        $destinationId = $this->destinationId($address, $apiKey, $forceRefresh);
        if ($destinationId === null) {
            return [];
        }

        $weightGrams = $this->cartWeightGrams($cart);
        if ($weightGrams < 1 || $weightGrams > 1_000_000_000) {
            $this->lastError = 'product_weight_missing_or_invalid';
            log_message('warning', 'RajaOngkir fallback skipped: cart has no valid positive package weight (cart_items={count}).', [
                'count' => count($cart),
            ]);
            return [];
        }

        $payload = [
            'origin' => (int) $originId,
            'destination' => $destinationId,
            'weight' => $weightGrams,
            'courier' => $couriers,
            'price' => 'lowest',
        ];
        $cacheKey = 'ilena:rajaongkir-rates:v1:' . hash('sha256', serialize($payload));
        if (!$forceRefresh) {
            try {
                $cached = cache()->get($cacheKey);
                if (is_array($cached)) {
                    return $cached;
                }
            } catch (\Throwable $error) {
                // Cache is an optimization only.
            }
        }

        try {
            $response = $this->client()->post(self::BASE_URL . '/calculate/domestic-cost', [
                'headers' => ['key' => $apiKey, 'Accept' => 'application/json'],
                'form_params' => $payload,
            ]);
            $status = $response->getStatusCode();
            $data = json_decode((string) $response->getBody(), true);
            if ($status < 200 || $status >= 300) {
                $this->lastError = 'provider_http_' . $status;
                log_message('warning', 'RajaOngkir rate request failed (http={status}, provider_code={code}).', [
                    'status' => $status,
                    'code' => $this->safeProviderCode($data),
                ]);
                return [];
            }
            if (!is_array($data) || !is_array($data['data'] ?? null)) {
                $this->lastError = 'invalid_provider_response';
                log_message('warning', 'RajaOngkir rate response was invalid (http={status}, provider_code={code}).', [
                    'status' => $status,
                    'code' => $this->safeProviderCode($data),
                ]);
                return [];
            }

            $rates = $this->mapRates($data['data'], (string) ($address['kab'] ?? ''));
            if ($rates === []) {
                $this->lastError = 'provider_no_rates';
                log_message('notice', 'RajaOngkir returned no usable shipping options (rows={rows}).', ['rows' => count($data['data'])]);
                return [];
            }
            try {
                cache()->save($cacheKey, $rates, CacheTtl::jitter(30));
            } catch (\Throwable $error) {
                // A successful live quote remains usable if cache storage is down.
            }
            return $rates;
        } catch (\Throwable $error) {
            $this->lastError = 'provider_unavailable';
            log_message('warning', 'RajaOngkir rate request failed ({type}).', ['type' => get_class($error)]);
            return [];
        }
    }

    private function destinationId(array $address, string $apiKey, bool $forceRefresh): ?int
    {
        $postalCode = preg_replace('/\D+/', '', (string) ($address['kodepos'] ?? '')) ?? '';
        if (preg_match('/^\d{5}$/', $postalCode) !== 1) {
            $this->lastError = 'destination_postcode_missing';
            log_message('warning', 'RajaOngkir destination lookup skipped: destination postcode missing or invalid.');
            return null;
        }

        $cacheKey = 'ilena:rajaongkir-destination:v1:' . hash('sha256', implode('|', [
            $postalCode,
            $this->normalize((string) ($address['prov'] ?? '')),
            $this->normalize((string) ($address['kab'] ?? '')),
            $this->normalize((string) ($address['kec'] ?? '')),
            $this->normalize((string) ($address['desa'] ?? '')),
        ]));
        if (!$forceRefresh) {
            try {
                $cached = cache()->get($cacheKey);
                if (is_int($cached) && $cached > 0) {
                    return $cached;
                }
            } catch (\Throwable $error) {
                // Cache is an optimization only.
            }
        }

        try {
            $response = $this->client()->get(self::BASE_URL . '/destination/domestic-destination', [
                'headers' => ['key' => $apiKey, 'Accept' => 'application/json'],
                'query' => ['search' => $postalCode, 'limit' => 100, 'offset' => 0],
            ]);
            $data = json_decode((string) $response->getBody(), true);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300 || !is_array($data) || !is_array($data['data'] ?? null)) {
                $this->lastError = 'destination_lookup_failed';
                log_message('warning', 'RajaOngkir destination lookup failed (http={status}, provider_code={code}).', [
                    'status' => $response->getStatusCode(),
                    'code' => $this->safeProviderCode($data),
                ]);
                return null;
            }

            $candidates = array_values(array_filter($data['data'], fn ($row): bool => is_array($row)
                && $this->normalize((string) ($row['zip_code'] ?? '')) === $postalCode
                && $this->samePlace((string) ($row['province_name'] ?? ''), (string) ($address['prov'] ?? ''))
                && $this->samePlace((string) ($row['city_name'] ?? ''), (string) ($address['kab'] ?? ''))));

            // Narrow by district and village when the API provides those fields.
            foreach ([['district_name', 'kec'], ['subdistrict_name', 'desa']] as [$apiField, $addressField]) {
                $expected = trim((string) ($address[$addressField] ?? ''));
                if ($expected === '') {
                    continue;
                }
                $narrowed = array_values(array_filter($candidates, fn (array $row): bool => $this->samePlace((string) ($row[$apiField] ?? ''), $expected)));
                if ($narrowed !== []) {
                    $candidates = $narrowed;
                }
            }

            $ids = array_values(array_unique(array_filter(array_map(static fn (array $row): int => (int) ($row['id'] ?? 0), $candidates))));
            if (count($ids) !== 1) {
                $this->lastError = $ids === [] ? 'destination_not_found' : 'destination_ambiguous';
                log_message('notice', 'RajaOngkir destination match was not unique (candidate_ids={count}).', ['count' => count($ids)]);
                return null;
            }
            try {
                cache()->save($cacheKey, $ids[0], CacheTtl::jitter(2_592_000));
            } catch (\Throwable $error) {
                // Live lookup is still usable without cache.
            }
            return $ids[0];
        } catch (\Throwable $error) {
            $this->lastError = 'destination_lookup_unavailable';
            log_message('warning', 'RajaOngkir destination lookup failed ({type}).', ['type' => get_class($error)]);
            return null;
        }
    }

    private function mapRates(array $rows, string $destination): array
    {
        $rates = [];
        $seen = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['name'] ?? ''));
            $courier = strtolower(trim((string) ($row['code'] ?? '')));
            $service = trim((string) ($row['service'] ?? ''));
            $price = $this->extractPrice($row['cost'] ?? null);
            if ($name === '' || $courier === '' || $courier === 'anteraja' || $service === '' || $price < 1) {
                continue;
            }
            $identity = $courier . ':' . strtolower($service);
            if (isset($seen[$identity])) {
                continue;
            }
            $seen[$identity] = true;
            $rates[] = [
                'nama' => $name,
                'deskripsi' => trim((string) ($row['description'] ?? $service)),
                'harga' => $price,
                'estimasi' => $this->formatEtd((string) ($row['etd'] ?? '')),
                'provider' => 'rajaongkir',
                'service_code' => $identity,
                'tariff_id' => $identity,
                'destination' => $destination,
                'free_shipping_eligible' => false,
                'logo' => $this->logoFilename($courier),
            ];
        }

        usort($rates, static fn (array $left, array $right): int => ((int) $left['harga']) <=> ((int) $right['harga']));
        return array_slice($rates, 0, 3);
    }

    private function cartWeightGrams(array $cart): int
    {
        $totalKg = 0.0;
        foreach ($cart as $item) {
            $product = $item['detail'] ?? null;
            $quantity = (int) ($item['jumlah'] ?? 0);
            if (!is_array($product) || $quantity < 1 || $quantity > 100) {
                return 0;
            }
            $description = json_decode((string) ($product['deskripsi'] ?? '{}'), true);
            $package = is_array($description['dimensi']['paket'] ?? null) ? $description['dimensi']['paket'] : [];
            $actual = is_array($description['dimensi']['asli'] ?? null) ? $description['dimensi']['asli'] : [];
            $packageWeight = (float) ($package['berat'] ?? 0);
            $actualWeight = (float) ($actual['berat'] ?? 0);
            $weight = $packageWeight > 0 ? $packageWeight : $actualWeight;
            if (!is_finite($weight) || $weight <= 0) {
                return 0;
            }
            $totalKg += $weight * $quantity;
        }
        return $totalKg > 0 ? (int) ceil($totalKg * 1000) : 0;
    }

    private function couriers(): string
    {
        $raw = trim((string) env('RAJAONGKIR_COURIERS', 'jne:sicepat:jnt:tiki:pos:ninja:wahana:lion'));
        $codes = array_values(array_unique(array_filter(array_map(static fn (string $code): string => strtolower(trim($code)), preg_split('/[:,]+/', $raw) ?: []), static fn (string $code): bool => $code !== 'anteraja' && preg_match('/^[a-z0-9_-]{2,30}$/', $code) === 1)));
        return implode(':', array_slice($codes, 0, 20));
    }

    private function client()
    {
        return service('curlrequest', [
            'connect_timeout' => 3,
            'timeout' => 8,
            'verify' => true,
            'http_errors' => false,
            'allow_redirects' => false,
        ]);
    }

    private function extractPrice($cost): int
    {
        if (is_numeric($cost)) {
            return max(0, (int) $cost);
        }
        if (is_array($cost)) {
            foreach ($cost as $entry) {
                if (is_array($entry) && is_numeric($entry['value'] ?? null)) {
                    return max(0, (int) $entry['value']);
                }
            }
        }
        return 0;
    }

    private function formatEtd(string $etd): string
    {
        if (preg_match('/(\d+)\s*(?:-|–|sampai|to)\s*(\d+)/iu', $etd, $match) === 1) {
            return $match[1] . ' - ' . $match[2];
        }
        if (preg_match('/\b(\d+)\b/u', $etd, $match) === 1) {
            return $match[1];
        }
        return '';
    }

    private function logoFilename(string $courier): string
    {
        return [
            'anteraja' => 'anteraja', 'jne' => 'jne', 'jnt' => 'J&T', 'j&t' => 'J&T',
            'lion' => 'lion', 'ninja' => 'ninja', 'pos' => 'pos', 'sicepat' => 'sicepat',
            'tiki' => 'tiki', 'wahana' => 'wahana',
        ][$courier] ?? '';
    }

    private function safeProviderCode($data): string
    {
        $code = is_array($data) ? ($data['meta']['code'] ?? null) : null;
        return (is_int($code) || (is_string($code) && preg_match('/^\d{1,12}$/', $code) === 1)) ? (string) $code : 'none';
    }

    private function normalize(string $value): string
    {
        return preg_replace('/[^\pL\pN]+/u', '', mb_strtolower(trim($value), 'UTF-8')) ?? '';
    }

    private function samePlace(string $left, string $right): bool
    {
        $left = $this->normalize($left);
        $right = $this->normalize($right);
        $left = preg_replace('/^(kabupaten|kotaadministrasi|kota|provinsi|propinsi)/u', '', $left) ?? $left;
        $right = preg_replace('/^(kabupaten|kotaadministrasi|kota|provinsi|propinsi)/u', '', $right) ?? $right;
        return $left !== '' && $left === $right;
    }
}
