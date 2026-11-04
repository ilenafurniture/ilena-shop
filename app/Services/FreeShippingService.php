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
            return self::normalizeConfig($dbConfig);
        }

        $fileConfig = $this->readFromFile();
        if (is_array($fileConfig)) {
            // Migrasi otomatis dari file lama ke database saat database sudah siap.
            $this->saveToDatabase($fileConfig);
            return self::normalizeConfig($fileConfig);
        }

        return self::defaultConfig();
    }

    public function save(array $config): bool
    {
        $payload = self::normalizeConfig(array_merge($config, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]));

        $savedToDatabase = $this->saveToDatabase($payload);
        if ($savedToDatabase) {
            // The database is authoritative whenever it is reachable. The file
            // remains a recovery copy and must not make a failed DB update look
            // successful when an older DB value would still take precedence.
            $this->saveToFile($payload);
            return true;
        }

        $databaseConfig = $this->readFromDatabase();
        if (is_array($databaseConfig)) {
            return false;
        }

        // Keep the legacy file fallback only when the DB setting is absent or
        // unavailable, so getConfig() can continue serving the saved policy.
        return $this->saveToFile($payload);
    }

    private static function defaultConfig(): array
    {
        return [
            'active' => false,
            'label' => 'Gratis ongkir wilayah',
            'province_ids' => [],
            'kabupaten_ids' => [],
            'province_names' => [],
            'minimum_order_amount' => 0,
            'maximum_shipping_rate' => 0,
            'updated_at' => null,
        ];
    }

    private static function normalizeConfig(array $config): array
    {
        $default = self::defaultConfig();

        return array_merge($default, [
            'active' => in_array($config['active'] ?? false, [true, 1, '1'], true),
            'label' => trim((string)($config['label'] ?? $default['label'])) ?: $default['label'],
            'province_ids' => array_values(array_unique(array_filter(array_map(
                static fn($value): string => is_scalar($value) ? trim((string)$value) : '',
                (array)($config['province_ids'] ?? [])
            )))),
            'kabupaten_ids' => array_values(array_unique(array_filter(array_map(
                static fn($value): string => is_scalar($value) ? trim((string)$value) : '',
                (array)($config['kabupaten_ids'] ?? [])
            )))),
            'province_names' => array_values(array_unique(array_filter(array_map(
                static fn($value): string => is_scalar($value) ? trim((string)$value) : '',
                (array)($config['province_names'] ?? [])
            )))),
            'minimum_order_amount' => max(0, (int)($config['minimum_order_amount'] ?? 0)),
            'maximum_shipping_rate' => max(0, (int)($config['maximum_shipping_rate'] ?? 0)),
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
        $temporaryPath = null;
        try {
            $dir = dirname($this->path);
            if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
                return false;
            }

            $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $temporaryPath = @tempnam($dir, '.free-shipping-');
            if ($temporaryPath === false || file_put_contents($temporaryPath, $json, LOCK_EX) === false) {
                return false;
            }

            if (!@rename($temporaryPath, $this->path)) {
                return false;
            }

            $temporaryPath = null;
            return true;
        } catch (\Throwable $th) {
            log_message('error', 'Gagal menyimpan cadangan gratis ongkir: ' . $th->getMessage());
            return false;
        } finally {
            if (is_string($temporaryPath) && is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
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

    public function isEligible(array $address, ?float $orderAmount = null): bool
    {
        return self::addressIsEligible($address, $this->getConfig(), $orderAmount);
    }

    public static function addressIsEligible(array $address, array $config, ?float $orderAmount = null): bool
    {
        $config = self::normalizeConfig($config);
        if (!$config['active']) {
            return false;
        }

        if ($config['minimum_order_amount'] > 0
            && ($orderAmount === null || $orderAmount < $config['minimum_order_amount'])) {
            return false;
        }

        $provinceIds = $config['province_ids'];
        $provinceId = trim((string)($address['prov_id'] ?? ''));
        $kabupatenIds = $config['kabupaten_ids'];
        $kabupatenId = trim((string)($address['kab_id'] ?? ''));
        if ($provinceIds !== [] || $kabupatenIds !== []) {
            // Explicit IDs are the source of truth. Provinces cover every city
            // inside them; selected cities add narrower coverage elsewhere.
            return ($provinceId !== '' && in_array($provinceId, $provinceIds, true))
                || ($kabupatenId !== '' && in_array($kabupatenId, $kabupatenIds, true));
        }

        // Name matching is a fallback mode, used only when no province IDs are
        // configured (for legacy addresses or regions absent from the ID list).
        $provinceName = self::normalize((string)($address['prov'] ?? ''));
        if ($provinceName === '') {
            return false;
        }

        foreach ($config['province_names'] as $name) {
            if ($provinceName === self::normalize($name)) {
                return true;
            }
        }

        return false;
    }

    public function applyToRates(array $address, array $rates, ?float $orderAmount = null): array
    {
        return self::applyConfigToRates($address, $rates, $orderAmount, $this->getConfig());
    }

    public static function applyConfigToRates(array $address, array $rates, ?float $orderAmount, array $config): array
    {
        $config = self::normalizeConfig($config);
        foreach ($rates as &$rate) {
            if (!is_array($rate)) {
                continue;
            }
            if (!empty($rate['gratis_ongkir'])) {
                $rate['harga'] = max(0, (int)($rate['harga_asli'] ?? $rate['harga'] ?? 0));
            }
            unset($rate['harga_asli'], $rate['gratis_ongkir'], $rate['gratis_ongkir_label']);
        }
        unset($rate);

        if (!self::addressIsEligible($address, $config, $orderAmount)) {
            return $rates;
        }

        foreach ($rates as &$rate) {
            if (!is_array($rate)) {
                continue;
            }
            // Payment and order creation re-evaluate rates already transformed
            // on the shipping step. Preserve the true pre-promo amount.
            $original = max(0, (int)($rate['harga'] ?? 0));

            if ($config['maximum_shipping_rate'] > 0 && $original > $config['maximum_shipping_rate']) {
                continue;
            }

            $rate['harga_asli'] = $original;
            $rate['harga'] = 0;
            $rate['gratis_ongkir'] = true;
            $rate['gratis_ongkir_label'] = $config['label'];
        }
        unset($rate);

        return $rates;
    }

    private static function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['provinsi', 'propinsi'], '', $value);
        $value = preg_replace('/[^a-z0-9]+/i', '', $value) ?? '';

        return match ($value) {
            'jakarta', 'daerahkhususibukotajakarta' => 'dkijakarta',
            'yogyakarta', 'daerahistimewayogyakarta' => 'diyogyakarta',
            default => $value,
        };
    }
}
