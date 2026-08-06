<?php

namespace App\Services;

class AuditLogService
{
    private string $table = 'audit_logs';

    public function record(array $data): bool
    {
        try {
            $db = \Config\Database::connect();
            if (!$this->ensureTable($db)) {
                return false;
            }

            $this->deleteOldLogs();

            return (bool)$db->table($this->table)->insert([
                'actor_name' => $this->limit($data['actor_name'] ?? '-', 150),
                'actor_email' => $this->limit($data['actor_email'] ?? '-', 190),
                'role_label' => $this->limit($data['role_label'] ?? 'Admin', 80),
                'activity' => $this->limit($data['activity'] ?? 'Melakukan aktivitas admin.', 255),
                'description' => $data['description'] ?? '',
                'method' => $this->limit($data['method'] ?? '', 12),
                'url' => $this->limit($data['url'] ?? '', 500),
                'ip_address' => $this->limit($data['ip_address'] ?? '', 80),
                'user_agent' => $this->limit($data['user_agent'] ?? '', 500),
                'request_data' => json_encode($this->cleanData($data['request_data'] ?? []), JSON_UNESCAPED_UNICODE),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $th) {
            log_message('error', 'Gagal menyimpan audit log: ' . $th->getMessage());
            return false;
        }
    }

    public function getRecent(int $limit = 200): array
    {
        try {
            $db = \Config\Database::connect();
            if (!$this->ensureTable($db)) {
                return [];
            }

            $this->deleteOldLogs();

            return $db->table($this->table)
                ->orderBy('created_at', 'desc')
                ->limit(max(1, min($limit, 500)))
                ->get()
                ->getResultArray();
        } catch (\Throwable $th) {
            log_message('error', 'Gagal membaca audit log: ' . $th->getMessage());
            return [];
        }
    }

    public function deleteOldLogs(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$this->ensureTable($db)) {
                return;
            }

            $db->table($this->table)
                ->where('created_at <', date('Y-m-d H:i:s', strtotime('-6 months')))
                ->delete();
        } catch (\Throwable $th) {
            log_message('error', 'Gagal menghapus audit log lama: ' . $th->getMessage());
        }
    }

    public function shouldRecord(string $method, string $path): bool
    {
        $path = trim(strtolower($path), '/');

        if ($path === 'admin/activity-log' || str_starts_with($path, 'admin/activity-log/')) {
            return false;
        }

        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return true;
        }

        return (bool)preg_match(
            '~/(action|delete|active|acc|deny|confirm|toggle|finalize|create|update|edit-save|koreksi|buat|add|remove|cancel)~i',
            '/' . $path
        );
    }

    public function makeHumanActivity(string $method, string $path): string
    {
        $path = trim($path, '/');
        $parts = array_values(array_filter(explode('/', $path)));
        $last = strtolower((string)end($parts));
        $area = $this->humanizeArea($path);

        if (str_contains($path, 'free-shipping')) {
            return 'Mengubah pengaturan gratis ongkir';
        }
        if (str_contains($path, 'product')) {
            if (str_contains($path, 'delete')) return 'Menghapus produk';
            if (str_contains($path, 'active')) return 'Mengubah status aktif produk';
            return strtoupper($method) === 'POST' && str_contains($path, 'edit') ? 'Mengubah data produk' : 'Menyimpan data produk';
        }
        if (str_contains($path, 'voucher')) {
            if (str_contains($path, 'delete')) return 'Menghapus voucher';
            if (str_contains($path, 'toggle')) return 'Mengubah status voucher';
            return 'Menyimpan data voucher';
        }
        if (str_contains($path, 'article')) {
            return str_contains($path, 'delete') ? 'Menghapus artikel' : 'Menyimpan data artikel';
        }
        if (str_contains($path, 'order')) {
            return 'Mengubah data pesanan';
        }
        if (str_contains($path, 'surat-jalan') || str_contains($path, 'suratjalan')) {
            return 'Mengubah data surat jalan';
        }
        if (str_contains($path, 'mutasi')) {
            return 'Mengubah data mutasi stok';
        }
        if (str_contains($path, 'project-interior')) {
            return 'Mengubah data project interior';
        }
        if (str_contains($path, 'homelayout')) {
            return 'Mengubah tampilan halaman utama';
        }

        return 'Melakukan perubahan pada ' . $area . ($last ? ' (' . $last . ')' : '');
    }

    public function makeHumanDescription(string $activity, string $method, string $path, array $requestData = []): string
    {
        $items = [];
        foreach ($this->cleanData($requestData) as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', array_slice(array_map('strval', $value), 0, 8));
            }
            $value = trim((string)$value);
            if ($value === '') {
                continue;
            }
            $items[] = $this->humanizeKey((string)$key) . ': ' . $this->limit($value, 120);
            if (count($items) >= 8) {
                break;
            }
        }

        $desc = $activity . '.';
        $desc .= ' Halaman/fitur: ' . $this->humanizeArea($path) . '.';
        $desc .= ' Cara perubahan: ' . strtoupper($method) . '.';
        if (!empty($items)) {
            $desc .= ' Data yang dikirim: ' . implode('; ', $items) . '.';
        }

        return $desc;
    }

    private function ensureTable($db): bool
    {
        if ($db->tableExists($this->table)) {
            return true;
        }

        $forge = \Config\Database::forge();
        $forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'actor_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'actor_email' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'role_label' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'activity' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'method' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true],
            'url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'request_data' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
        ]);
        $forge->addKey('id', true);
        $forge->addKey('created_at');

        return (bool)$forge->createTable($this->table, true);
    }

    private function cleanData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $hiddenKeys = ['password', 'sandi', 'token', 'csrf', 'secret', 'key', 'apikey', 'api_key', 'authorization'];
        $clean = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string)$key);
            foreach ($hiddenKeys as $hidden) {
                if (str_contains($lowerKey, $hidden)) {
                    $clean[$key] = '[disembunyikan]';
                    continue 2;
                }
            }
            $clean[$key] = is_array($value) ? $this->cleanData($value) : $this->limit((string)$value, 250);
        }

        return $clean;
    }

    private function humanizeArea(string $path): string
    {
        $path = trim(str_replace(['admin/', '-', '_'], ['', ' ', ' '], strtolower($path)));
        $path = preg_replace('/\d+/', '', $path) ?? $path;
        $path = trim(preg_replace('/\s+/', ' ', $path) ?? $path);
        return $path ? ucwords($path) : 'Admin';
    }

    private function humanizeKey(string $key): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $key));
    }

    private function roleLabel($role): string
    {
        return match ((string)$role) {
            '1' => 'Admin',
            '2' => 'Gudang',
            '3' => 'Marketplace',
            default => 'User',
        };
    }

    public function currentActorData(): array
    {
        return [
            'actor_name' => session()->get('nama') ?: session()->get('name') ?: session()->get('email') ?: '-',
            'actor_email' => session()->get('email') ?: '-',
            'role_label' => $this->roleLabel(session()->get('role')),
        ];
    }

    private function limit(string $value, int $max): string
    {
        return mb_strlen($value) > $max ? mb_substr($value, 0, $max - 3) . '...' : $value;
    }
}
