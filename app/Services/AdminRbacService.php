<?php

namespace App\Services;

class AdminRbacService
{
    private string $rolesTable = 'admin_rbac_roles';
    private string $userRolesTable = 'admin_rbac_user_roles';

    public function permissions(): array
    {
        return [
            'dashboard' => ['label' => 'Dashboard Admin', 'description' => 'Akses ringkasan operasional admin.'],
            'products' => ['label' => 'Produk', 'description' => 'Lihat, tambah, edit, aktif/nonaktif produk.'],
            'vouchers' => ['label' => 'Voucher', 'description' => 'Kelola voucher dan pemakaian voucher.'],
            'orders_online' => ['label' => 'Pesanan Online', 'description' => 'Lihat pesanan online, input resi, surat jalan, reprint.'],
            'orders_offline' => ['label' => 'Pesanan Offline', 'description' => 'Kelola order offline, invoice, DP, surat jalan offline.'],
            'project_interior' => ['label' => 'Project Interior', 'description' => 'Kelola project interior, pembayaran, invoice, surat jalan.'],
            'mutasi' => ['label' => 'Mutasi Stok', 'description' => 'Kelola mutasi, konfirmasi mutasi, label barang.'],
            'content' => ['label' => 'Konten', 'description' => 'Kelola artikel dan home layout.'],
            'shipping' => ['label' => 'Gratis Ongkir', 'description' => 'Atur wilayah gratis ongkir.'],
            'analytics' => ['label' => 'Analytics', 'description' => 'Akses analytics, export, blacklist analytics.'],
            'meta_capi' => ['label' => 'Meta CAPI', 'description' => 'Atur Pixel ID, token, Graph API, dan test event code.'],
            'activity_log' => ['label' => 'Log Aktivitas', 'description' => 'Lihat log aktivitas admin.'],
            'spam_cleanup' => ['label' => 'Cleanup Spam Akun', 'description' => 'Lihat dan hapus akun register spam/injection.'],
            'customers' => ['label' => 'Akun Pelanggan', 'description' => 'Lihat status akun dan kelola penangguhan pelanggan.'],
            'marketplace' => ['label' => 'Workspace Marketplace', 'description' => 'Kelola katalog dan pesanan melalui workspace marketplace.'],
        ];
    }

    /** Permissions for the standard operational Admin role; technical controls remain Dev-only. */
    public function operationalAdminPermissions(): array
    {
        return array_values(array_diff(array_keys($this->permissions()), ['meta_capi', 'spam_cleanup']));
    }

    private function isOperationalAdminRoleName(string $name): bool
    {
        return in_array(strtolower(trim($name)), ['admin', 'admin operasional'], true);
    }

    public function routePermission(string $path): ?string
    {
        $path = trim($path, '/');
        if ($path === 'admin') {
            return 'dashboard';
        }

        $rules = [
            'admin/order/offline/interior' => 'project_interior',
            'editarticle' => 'content',
            'changepic' => 'products',
            'gantilokasi' => 'products',
            'cancelorder' => 'orders_online',
            'admin/rbac' => 'rbac',
            'admin/product-bulk-edit' => 'products',
            'admin/menu-previews' => 'content',
            'admin/producttable' => 'products',
            'admin/product' => 'products',
            'admin/addproduct' => 'products',
            'admin/editproduct' => 'products',
            'admin/deleteproduct' => 'products',
            'admin/activeproduct' => 'products',
            'admin/voucher' => 'vouchers',
            'admin/order/online' => 'orders_online',
            'admin/order/add' => 'orders_online',
            'admin/order' => 'orders_online',
            'admin/actioneditresi' => 'orders_online',
            'admin/reprint' => 'orders_online',
            'admin/suratjalan' => 'orders_online',
            'admin/marketplace' => 'orders_online',
            'admin/confirm-mp' => 'orders_online',
            'admin/edit-mp' => 'orders_online',
            'admin/accreprint' => 'orders_online',
            'admin/denyreprint' => 'orders_online',
            'admin/order/offline' => 'orders_offline',
            'admin/order-offline' => 'orders_offline',
            'admin/getitemsoffline' => 'orders_offline',
            'admin/invoice-offline' => 'orders_offline',
            'admin/invoice-offline-dp' => 'orders_offline',
            'admin/surat-offline' => 'orders_offline',
            'admin/surat-jalan/offline' => 'orders_offline',
            'admin/actionbuatinvoice' => 'orders_offline',
            'admin/actionbuatdp' => 'orders_offline',
            'admin/actionaccorderoffline' => 'orders_offline',
            'admin/benerinsurat' => 'orders_offline',
            'admin/project-interior' => 'project_interior',
            'admin/mutasiconfirm' => 'mutasi',
            'admin/mutasi' => 'mutasi',
            'admin/actionaddmutasi' => 'mutasi',
            'admin/accmutasi' => 'mutasi',
            'admin/denymutasi' => 'mutasi',
            'admin/labelbarang' => 'mutasi',
            'admin/article' => 'content',
            'admin/addarticle' => 'content',
            'admin/editarticle' => 'content',
            'admin/deletearticle' => 'content',
            'admin/addgaleriarticle' => 'content',
            'admin/homelayout' => 'content',
            'admin/changepic' => 'products',
            'admin/partners' => 'content',
            'admin/seo-check' => 'products',
            'admin/surat-koreksi' => 'orders_offline',
            'admin/free-shipping' => 'shipping',
            'admin/meta-capi' => 'meta_capi',
            'admin/activity-log' => 'activity_log',
            'admin/spam-cleanup' => 'spam_cleanup',
            'admin/customers' => 'customers',
            'admin/ordertoko' => 'orders_online',
            'market' => 'marketplace',
            'analytics' => 'analytics',
        ];

        // Most-specific match first; admin/order must never shadow offline.
        uksort($rules, static fn($a, $b) => strlen($b) <=> strlen($a));
        foreach ($rules as $prefix => $permission) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $permission;
            }
        }

        return null;
    }

    public function hasPermission(?string $email, ?string $permission): bool
    {
        if ($permission === null || $permission === '') {
            return true;
        }

        if ((string)session()->get('role') === '1') {
            return true;
        }

        $allowed = $this->userPermissions((string)$email);
        return in_array($permission, $allowed, true);
    }

    public function canAccessPath(?string $email, string $path): bool
    {
        $normalizedPath = trim($path, '/');
        if (($normalizedPath === 'admin/rbac' || str_starts_with($normalizedPath, 'admin/rbac/'))
            && (string) session()->get('role') !== '1'
        ) {
            return false;
        }

        $permission = $this->routePermission($path);
        // Shared SJ screens must authorize the source, not require the offline module
        // for a staff member assigned only to project interior.
        if ((string)session()->get('role') !== '1'
            && preg_match('~^admin/surat-jalan/offline/(\d+)(?:/.*)?$~', $normalizedPath, $match)
        ) {
            try {
                $db = \Config\Database::connect();
                $shipment = $db->table('surat_jalan')
                    ->select('id_pesanan')
                    ->where('id', (int) $match[1])
                    ->get()
                    ->getRowArray();

                // Compare a column to a bound scalar instead of joining two text
                // columns whose legacy production collations may differ.
                $isInterior = false;
                if ($shipment && isset($shipment['id_pesanan'])) {
                    $isInterior = $db->table('project_interior')
                        ->where('kode_sj', (string) $shipment['id_pesanan'])
                        ->countAllResults() > 0;
                }

                return $this->hasPermission($email, $isInterior ? 'project_interior' : 'orders_offline');
            } catch (\Throwable $th) {
                log_message('error', 'RBAC shipment source lookup failed: {err}', ['err' => $th->getMessage()]);
                return false;
            }
        }
        if ($permission === null) {
            return (string) session()->get('role') === '1';
        }

        return $this->hasPermission($email, $permission);
    }

    /** Confirm a privileged session still matches the persisted account state. */
    public function sessionAccountMatchesDatabase(?string $email, ?string $sessionRole): bool
    {
        $email = strtolower(trim((string) $email));
        $sessionRole = (string) $sessionRole;
        if ($email === '' || !in_array($sessionRole, ['1', '5'], true)) {
            return false;
        }

        try {
            $db = \Config\Database::connect();
            $user = $db->table('user')
                ->select('role, active')
                ->where('email', $email)
                ->get(1)
                ->getRowArray();

            return $user !== null
                && (string) ($user['active'] ?? '') === '1'
                && (string) ($user['role'] ?? '') === $sessionRole;
        } catch (\Throwable $th) {
            log_message('error', 'RBAC session account check failed: {err}', ['err' => $th->getMessage()]);
            return false;
        }
    }

    /** Validate the current account before granting privileged online-order access. */
    public function canAccessOnlineOrdersAsStaff(?string $email, ?string $sessionRole): bool
    {
        if (!$this->sessionAccountMatchesDatabase($email, $sessionRole)) {
            return false;
        }

        if ((string) $sessionRole === '1') {
            return true;
        }

        return $this->hasPermission((string) $email, 'orders_online');
    }

    public function hasAnyAdminAccess(string $email): bool
    {
        return count($this->userPermissions($email)) > 0;
    }

    public function firstAllowedAdminUrl(string $email): string
    {
        $map = [
            'dashboard' => '/admin',
            'products' => '/admin/product',
            'vouchers' => '/admin/voucher',
            'orders_online' => '/admin/order/online',
            'orders_offline' => '/admin/order/offline/sale',
            'project_interior' => '/admin/project-interior',
            'mutasi' => '/admin/mutasi',
            'content' => '/admin/article',
            'shipping' => '/admin/free-shipping',
            'analytics' => '/analytics',
            'meta_capi' => '/admin/meta-capi',
            'activity_log' => '/admin/activity-log',
            'spam_cleanup' => '/admin/spam-cleanup',
            'customers' => '/admin/customers',
            'marketplace' => '/market/product',
        ];

        $allowed = $this->userPermissions($email);
        foreach ($map as $permission => $url) {
            if (in_array($permission, $allowed, true)) {
                return $url;
            }
        }

        return '/';
    }

    public function roles(): array
    {
        $this->ensureTables();
        return \Config\Database::connect()
            ->table($this->rolesTable)
            ->orderBy('role_name', 'asc')
            ->get()
            ->getResultArray();
    }

    public function role(?int $id): ?array
    {
        if (!$id) return null;
        $this->ensureTables();
        return \Config\Database::connect()
            ->table($this->rolesTable)
            ->where('id', $id)
            ->get()
            ->getRowArray() ?: null;
    }

    public function saveRole(?int $id, string $name, array $permissions): bool
    {
        $this->ensureTables();
        $valid = array_keys($this->permissions());
        $permissions = array_values(array_intersect(array_unique($permissions), $valid));
        if ($this->isOperationalAdminRoleName($name)) {
            $permissions = $this->operationalAdminPermissions();
        }

        $data = [
            'role_name' => trim($name),
            'permissions' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        if ($id) {
            return (bool)$db->table($this->rolesTable)->where('id', $id)->update($data);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        return (bool)$db->table($this->rolesTable)->insert($data);
    }

    public function deleteRole(int $id): bool
    {
        $this->ensureTables();
        $db = \Config\Database::connect();
        $db->table($this->userRolesTable)->where('role_id', $id)->delete();
        return (bool)$db->table($this->rolesTable)->where('id', $id)->delete();
    }

    public function assignments(): array
    {
        $this->ensureTables();
        return \Config\Database::connect()
            ->table($this->userRolesTable . ' ur')
            ->select('ur.email, ur.role_id, ur.updated_at, r.role_name, r.permissions')
            ->join($this->rolesTable . ' r', 'r.id = ur.role_id', 'left')
            ->orderBy('ur.email', 'asc')
            ->get()
            ->getResultArray();
    }

    public function syncAssignedUserRoles(): int
    {
        $this->ensureTables();
        $db = \Config\Database::connect();
        $rows = $db->table($this->userRolesTable)->select('email')->get()->getResultArray();

        $count = 0;
        foreach ($rows as $row) {
            $email = strtolower(trim((string)($row['email'] ?? '')));
            if ($email === '') continue;

            $user = $db->table('user')->select('role')->where('email', $email)->get()->getRowArray();
            if ($user && in_array((string)($user['role'] ?? ''), ['0', '3', '4', '5'], true)) {
                $db->table('user')->where('email', $email)->update(['role' => '5']);
                $count++;
            }
        }

        return $count;
    }

    public function assignRole(string $email, int $roleId): bool
    {
        $this->ensureTables();
        $email = strtolower(trim($email));
        if ($email === '' || !$this->role($roleId)) {
            return false;
        }

        $db = \Config\Database::connect();
        $data = [
            'email' => $email,
            'role_id' => $roleId,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $exists = $db->table($this->userRolesTable)->where('email', $email)->countAllResults() > 0;
        $saved = $exists
            ? (bool)$db->table($this->userRolesTable)->where('email', $email)->update($data)
            : (bool)$db->table($this->userRolesTable)->insert($data);

        if ($saved && $db->tableExists('user')) {
            $user = $db->table('user')->select('role')->where('email', $email)->get()->getRowArray();
            if ($user && in_array((string)($user['role'] ?? ''), ['0', '3', '4', '5'], true)) {
                $db->table('user')->where('email', $email)->update(['role' => '5']);
            }
        }

        return $saved;
    }

    public function removeAssignment(string $email): bool
    {
        $this->ensureTables();
        $email = strtolower(trim($email));
        $db = \Config\Database::connect();
        $deleted = (bool)$db
            ->table($this->userRolesTable)
            ->where('email', $email)
            ->delete();

        if ($deleted && $db->tableExists('user')) {
            $user = $db->table('user')->select('role')->where('email', $email)->get()->getRowArray();
            if ($user && (string)($user['role'] ?? '') === '5') {
                $db->table('user')->where('email', $email)->update(['role' => '0']);
            }
        }

        return $deleted;
    }

    public function userPermissions(string $email): array
    {
        $email = strtolower(trim($email));
        if ($email === '') return [];

        try {
            $this->ensureTables();
            $row = \Config\Database::connect()
                ->table($this->userRolesTable . ' ur')
                ->select('r.role_name, r.permissions')
                ->join($this->rolesTable . ' r', 'r.id = ur.role_id', 'inner')
                ->where('ur.email', $email)
                ->get()
                ->getRowArray();

            $permissions = json_decode((string)($row['permissions'] ?? '[]'), true);
            if ($this->isOperationalAdminRoleName((string)($row['role_name'] ?? ''))) {
                return $this->operationalAdminPermissions();
            }

            if (!is_array($permissions)) {
                return [];
            }

            return array_values(array_intersect(
                array_unique(array_map('strval', $permissions)),
                array_keys($this->permissions()),
            ));
        } catch (\Throwable $th) {
            log_message('error', 'RBAC user permission error: {err}', ['err' => $th->getMessage()]);
            return [];
        }
    }

    public function decodePermissions($permissions): array
    {
        if (is_array($permissions)) return $permissions;
        $decoded = json_decode((string)$permissions, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function ensureTables(): void
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        if (!$db->tableExists($this->rolesTable)) {
            $forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'role_name' => ['type' => 'VARCHAR', 'constraint' => 100],
                'permissions' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->createTable($this->rolesTable, true);
        }

        if (!$db->tableExists($this->userRolesTable)) {
            $forge->addField([
                'email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'role_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('email', true);
            $forge->createTable($this->userRolesTable, true);
        }
    }
}
