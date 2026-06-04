<?php

namespace App\Services;

use Config\Shipping as ShippingConfig;

class ShippingService
{
    private ShippingConfig $config;

    public function __construct(?ShippingConfig $config = null)
    {
        $this->config = $config ?? config('Shipping');
    }

    public function rates(array $address, array $cart): array
    {
        $rates = [];

        if ($this->config->provider === 'biteship') {
            $rates = $this->biteshipRates($address, $cart);
        }

        if (empty($rates)) {
            $rates = $this->manualFallbackRates($address, $cart);
        }

        usort($rates, static fn ($a, $b) => ((int) $a['harga']) <=> ((int) $b['harga']));

        return $rates;
    }

    private function biteshipRates(array $address, array $cart): array
    {
        if ($this->config->biteshipApiKey === '' || $this->config->originPostalCode === '') {
            log_message('warning', 'Biteship shipping dilewati: BITESHIP_API_KEY atau SHIPPING_ORIGIN_POSTAL_CODE belum diisi.');
            return [];
        }

        $destinationPostalCode = preg_replace('/\D+/', '', (string) ($address['kodepos'] ?? ''));
        if ($destinationPostalCode === '') {
            log_message('warning', 'Biteship shipping dilewati: kode pos tujuan kosong.');
            return [];
        }

        $payload = [
            'origin_postal_code' => (int) preg_replace('/\D+/', '', $this->config->originPostalCode),
            'destination_postal_code' => (int) $destinationPostalCode,
            'couriers' => $this->config->couriers,
            'items' => $this->biteshipItems($cart),
        ];

        if (empty($payload['items'])) {
            log_message('warning', 'Biteship shipping dilewati: item checkout kosong.');
            return [];
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.biteship.com/v1/rates/couriers',
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => max(1, $this->config->connectTimeout),
            CURLOPT_TIMEOUT => max(1, $this->config->timeout),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'authorization: ' . $this->config->biteshipApiKey,
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            log_message('error', 'Biteship shipping error: ' . $error);
            return [];
        }

        $decoded = json_decode((string) $response, true);
        if ($httpCode < 200 || $httpCode >= 300 || !is_array($decoded)) {
            log_message('error', 'Biteship shipping gagal. HTTP ' . $httpCode . ' response: ' . substr((string) $response, 0, 500));
            return [];
        }

        $pricing = $decoded['pricing'] ?? [];
        if (!is_array($pricing)) {
            return [];
        }

        $rates = [];
        foreach ($pricing as $rate) {
            $price = (int) ($rate['price'] ?? $rate['shipping_fee'] ?? 0);
            if ($price <= 0) {
                continue;
            }

            $courierCode = strtolower((string) ($rate['courier_code'] ?? $rate['company'] ?? 'kurir'));
            $serviceName = trim((string) ($rate['courier_service_name'] ?? $rate['description'] ?? ''));
            $courierName = trim((string) ($rate['courier_name'] ?? strtoupper($courierCode)));

            $rates[] = [
                'nama' => $courierCode,
                'deskripsi' => trim($courierName . ($serviceName !== '' ? ' ' . $serviceName : '')),
                'harga' => $price,
                'estimasi' => (string) ($rate['shipment_duration_range'] ?? ''),
                'provider' => 'biteship',
                'service_code' => (string) ($rate['courier_service_code'] ?? ''),
            ];
        }

        return $rates;
    }

    private function biteshipItems(array $cart): array
    {
        $items = [];

        foreach ($cart as $item) {
            $product = $item['detail'] ?? [];
            if (empty($product)) {
                continue;
            }

            $package = $this->packageDimension($product);
            $quantity = max(1, (int) ($item['jumlah'] ?? 1));
            $price = $this->itemPrice($product);

            $items[] = [
                'name' => (string) ($product['nama'] ?? 'Produk Ilena'),
                'description' => (string) ($item['varian'] ?? ''),
                'category' => 'home_accessories',
                'value' => max(1, $price),
                'length' => max(1, (int) ceil($package['panjang'] / 10)),
                'width' => max(1, (int) ceil($package['lebar'] / 10)),
                'height' => max(1, (int) ceil($package['tinggi'] / 10)),
                'weight' => max(1, (int) ceil($this->chargeableKg($package) * 1000)),
                'quantity' => $quantity,
            ];
        }

        return $items;
    }

    private function manualFallbackRates(array $address, array $cart): array
    {
        if (!$this->config->manualFallbackEnabled || $this->config->manualFallbackDefaultPrice <= 0) {
            return [];
        }

        $weightKg = $this->cartChargeableKg($cart);
        $price = $this->config->manualFallbackDefaultPrice + (int) ceil($weightKg * $this->config->manualFallbackPricePerKg);

        return [[
            'nama' => $this->config->manualFallbackCourierCode,
            'deskripsi' => $this->config->manualFallbackCourierName,
            'harga' => max(1, $price),
            'estimasi' => $this->config->manualFallbackEstimate,
            'provider' => 'manual',
            'destination' => trim(($address['kab'] ?? '') . ', ' . ($address['prov'] ?? ''), ', '),
        ]];
    }

    private function packageDimension(array $product): array
    {
        $description = json_decode((string) ($product['deskripsi'] ?? '{}'), true);
        $package = is_array($description) ? ($description['dimensi']['paket'] ?? []) : [];
        $actual = is_array($description) ? ($description['dimensi']['asli'] ?? []) : [];
        $dimension = is_array($package) && !empty($package) ? $package : (is_array($actual) ? $actual : []);

        return [
            'panjang' => (float) ($dimension['panjang'] ?? 0),
            'lebar' => (float) ($dimension['lebar'] ?? 0),
            'tinggi' => (float) ($dimension['tinggi'] ?? 0),
            'berat' => (float) ($dimension['berat'] ?? 0),
        ];
    }

    private function chargeableKg(array $dimension): float
    {
        $volumetric = ceil(($dimension['panjang'] / 10) * ($dimension['lebar'] / 10) * ($dimension['tinggi'] / 10) / 3500);
        $actual = (float) ($dimension['berat'] ?? 0);

        return max(1, $volumetric, $actual);
    }

    private function cartChargeableKg(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $product = $item['detail'] ?? [];
            if (empty($product)) {
                continue;
            }

            $total += $this->chargeableKg($this->packageDimension($product)) * max(1, (int) ($item['jumlah'] ?? 1));
        }

        return max(1, $total);
    }

    private function itemPrice(array $product): int
    {
        $price = (float) ($product['harga'] ?? 0);
        $discount = (float) ($product['diskon'] ?? 0);

        if ($discount > 0) {
            $price *= (100 - $discount) / 100;
        }

        return (int) round($price);
    }
}
