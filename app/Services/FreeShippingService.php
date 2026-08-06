<?php

namespace App\Services;

class FreeShippingService
{
    private string $path;
    private string $table = 'app_settings';
    private string $settingKey = 'free_shipping_regions';

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? WRITEPATH . 'free_shipping_regions.json';
    }

    public function getConfig(): array
    {
        $dbConfig = $this->readFromDatabase();
        if (is_array($dbConfig)) {
            return $this->normalizeConfig($dbConfig);
        }

        $fileConfig = $this->readFromFile();
        if (is_array($fileConfig)) {
            // Migrasi otomatis dari file lama ke database saat database sudah siap.
            $this->saveToDatabase($fileConfig);
            return $this->normalizeConfig($fileConfig);
        }

        return $this->defaultConfig();
    }

    public function save(array $config): bool
    {
        $payload = $this->normalizeConfig(array_merge($config, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]));

        $savedToDatabase = $this->saveToDatabase($payload);
        $savedToFile = $this->saveToFile($payload);

        return $savedToDatabase || $savedToFile;
    }

    private function defaultConfig(): array
    {
        return [
            'active' => false,
            'label' => 'Gratis ongkir wilayah',
            'province_ids' => [],
            'province_names' => [],
            'updated_at' => null,
        ];
    }

    private function normalizeConfig(array $config): array
    {
        $default = $this->defaultConfig();

        return array_merge($default, [
            'active' => !empty($config['active']),
            'label' => trim((string)($config['label'] ?? $default['label'])) ?: $default['label'],
            'province_ids' => array_values(array_unique(array_filter(array_map('strval', (array)($config['province_ids'] ?? []))))),
            'province_names' => array_values(array_unique(array_filter(array_map('trim', (array)($config['province_names'] ?? []))))),
            'updated_at' => $config['updated_at'] ?? null,
        ]);
    }

    private function readFromFile(): ?array
    {
        if (!is_file($this->path)) {
            return null;
        }

        $json = json_decode((string) file_get_contents($this->path), true);
        if (!is_array($json)) {
            return null;
        }

        return $json;
    }

    private function saveToFile(array $payload): bool
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return (bool) file_put_contents(
            $this->path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function readFromDatabase(): ?array
    {
        try {
            $db = \Config\Database::connect();
            if (!$this->ensureTable($db)) {
                return null;
            }

            $row = $db->table($this->table)
                ->select('setting_value')
                ->where('setting_key', $this->settingKey)
                ->get()
                ->getRowArray();

            if (!$row || !isset($row['setting_value'])) {
                return null;
            }

            $config = json_decode((string)$row['setting_value'], true);
            return is_array($config) ? $config : null;
        } catch (\Throwable $th) {
            log_message('error', 'Gagal membaca gratis ongkir dari database: ' . $th->getMessage());
            return null;
        }
    }

    private function saveToDatabase(array $payload): bool
    {
        try {
            $db = \Config\Database::connect();
            if (!$this->ensureTable($db)) {
                return false;
            }

            $data = [
                'setting_key' => $this->settingKey,
                'setting_value' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'updated_at' => $payload['updated_at'] ?? date('Y-m-d H:i:s'),
            ];

            $exists = $db->table($this->table)
                ->where('setting_key', $this->settingKey)
                ->countAllResults() > 0;

            if ($exists) {
                return (bool)$db->table($this->table)
                    ->where('setting_key', $this->settingKey)
                    ->update($data);
            }

            return (bool)$db->table($this->table)->insert($data);
        } catch (\Throwable $th) {
            log_message('error', 'Gagal menyimpan gratis ongkir ke database: ' . $th->getMessage());
            return false;
        }
    }

    private function ensureTable($db): bool
    {
        if ($db->tableExists($this->table)) {
            return true;
        }

        $forge = \Config\Database::forge();
        $forge->addField([
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $forge->addKey('setting_key', true);

        return (bool)$forge->createTable($this->table, true);
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
