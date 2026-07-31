<?php

namespace App\Services;

class FreeShippingService
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? WRITEPATH . 'free_shipping_regions.json';
    }

    public function getConfig(): array
    {
        $default = [
            'active' => false,
            'label' => 'Gratis ongkir wilayah',
            'province_ids' => [],
            'province_names' => [],
            'updated_at' => null,
        ];

        if (!is_file($this->path)) {
            return $default;
        }

        $json = json_decode((string) file_get_contents($this->path), true);
        if (!is_array($json)) {
            return $default;
        }

        return array_merge($default, [
            'active' => (bool)($json['active'] ?? false),
            'label' => trim((string)($json['label'] ?? $default['label'])),
            'province_ids' => array_values(array_unique(array_map('strval', (array)($json['province_ids'] ?? [])))),
            'province_names' => array_values(array_unique(array_filter(array_map('trim', (array)($json['province_names'] ?? []))))),
            'updated_at' => $json['updated_at'] ?? null,
        ]);
    }

    public function save(array $config): bool
    {
        $payload = [
            'active' => !empty($config['active']),
            'label' => trim((string)($config['label'] ?? 'Gratis ongkir wilayah')) ?: 'Gratis ongkir wilayah',
            'province_ids' => array_values(array_unique(array_filter(array_map('strval', (array)($config['province_ids'] ?? []))))),
            'province_names' => array_values(array_unique(array_filter(array_map('trim', (array)($config['province_names'] ?? []))))),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return (bool) file_put_contents(
            $this->path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function isEligible(array $address): bool
    {
        $config = $this->getConfig();
        if (!$config['active']) {
            return false;
        }

        $provId = (string)($address['prov_id'] ?? '');
        if ($provId !== '' && in_array($provId, $config['province_ids'], true)) {
            return true;
        }

        $provName = $this->normalize((string)($address['prov'] ?? ''));
        foreach ($config['province_names'] as $name) {
            if ($provName !== '' && $provName === $this->normalize($name)) {
                return true;
            }
        }

        return false;
    }

    public function applyToRates(array $address, array $rates): array
    {
        if (!$this->isEligible($address)) {
            return $rates;
        }

        $config = $this->getConfig();
        foreach ($rates as &$rate) {
            $original = (int)($rate['harga'] ?? 0);
            $rate['harga_asli'] = $original;
            $rate['harga'] = 0;
            $rate['gratis_ongkir'] = true;
            $rate['gratis_ongkir_label'] = $config['label'];
        }
        unset($rate);

        return $rates;
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['provinsi', 'propinsi'], '', $value);
        $value = preg_replace('/[^a-z0-9]+/i', '', $value) ?? '';
        return $value;
    }
}
