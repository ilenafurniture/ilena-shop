<?php

namespace App\Services;

class MetaCapiSettingsService
{
    private string $table = 'app_settings';
    private string $settingKey = 'meta_capi';

    public function getConfig(): array
    {
        $dbConfig = $this->readFromDatabase();
        if (is_array($dbConfig)) {
            return $this->normalizeConfig($dbConfig);
        }

        return $this->envConfig();
    }

    public function save(array $config): bool
    {
        $current = $this->getConfig();
        $payload = $this->normalizeConfig(array_merge($current, $config, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]));

        return $this->saveToDatabase($payload);
    }

    public function maskedToken(string $token): string
    {
        $token = trim($token);
        if ($token === '') return '';
        if (strlen($token) <= 16) return str_repeat('*', strlen($token));

        return substr($token, 0, 8) . str_repeat('*', 12) . substr($token, -6);
    }

    private function envConfig(): array
    {
        return $this->normalizeConfig([
            'enabled' => filter_var(env('META_CAPI_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'pixel_id' => env('META_PIXEL_ID', ''),
            'access_token' => env('META_ACCESS_TOKEN', ''),
            'graph_version' => env('META_GRAPH_VERSION', 'v26.0'),
            'test_event_code' => env('META_TEST_EVENT_CODE', 'TEST90559'),
            'updated_at' => null,
        ]);
    }

    private function normalizeConfig(array $config): array
    {
        $graphVersion = trim((string)($config['graph_version'] ?? 'v26.0')) ?: 'v26.0';
        $graphVersion = ltrim($graphVersion, '/');
        if (!preg_match('/^v\d+\.\d+$/', $graphVersion)) {
            $graphVersion = 'v26.0';
        }

        return [
            'enabled' => !empty($config['enabled']),
            'pixel_id' => trim((string)($config['pixel_id'] ?? '')),
            'access_token' => trim((string)($config['access_token'] ?? '')),
            'graph_version' => $graphVersion,
            'test_event_code' => trim((string)($config['test_event_code'] ?? '')),
            'updated_at' => $config['updated_at'] ?? null,
        ];
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
            log_message('error', 'Gagal membaca pengaturan Meta CAPI dari database: ' . $th->getMessage());
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
            log_message('error', 'Gagal menyimpan pengaturan Meta CAPI ke database: ' . $th->getMessage());
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
}
