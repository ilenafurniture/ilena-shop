<?php

namespace App\Services;

class AdminRbacService
{
    private string $rolesTable = 'admin_rbac_roles';
    private string $userRolesTable = 'admin_rbac_user_roles';

    public function permissions(): array
    {
        return [
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
            'rbac' => ['label' => 'Role & Akses Admin', 'description' => 'Buat role dan atur akses user admin.'],
        ];
    }

    public function routePermission(string $path): ?string
    {
        $path = trim($path, '/');

        $rules = [
            'admin/rbac' => 'rbac',
            'admin/producttable' => 'products',
            'admin/product' => 'products',
            'admin/addproduct' => 'products',
            'admin/editproduct' => 'products',
            'admin/deleteproduct' => 'products',
            'admin/activeproduct' => 'products',
            'admin/product-old' => 'products',
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
            'admin/changepic' => 'content',
            'admin/free-shipping' => 'shipping',
            'admin/meta-capi' => 'meta_capi',
            'admin/activity-log' => 'activity_log',
            'analytics' => 'analytics',
        ];

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
        return $this->hasPermission($email, $this->routePermission($path));
    }

    public function hasAnyAdminAccess(string $email): bool
    {
        return count($this->userPermissions($email)) > 0;
    }

    public function firstAllowedAdminUrl(string $email): string
    {
        $map = [
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
            'rbac' => '/admin/rbac',
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
        if ($exists) {
            return (bool)$db->table($this->userRolesTable)->where('email', $email)->update($data);
        }

        return (bool)$db->table($this->userRolesTable)->insert($data);
    }

    public function removeAssignment(string $email): bool
    {
        $this->ensureTables();
        return (bool)\Config\Database::connect()
            ->table($this->userRolesTable)
            ->where('email', strtolower(trim($email)))
            ->delete();
    }

    public function userPermissions(string $email): array
    {
        $email = strtolower(trim($email));
        if ($email === '') return [];

        try {
            $this->ensureTables();
            $row = \Config\Database::connect()
                ->table($this->userRolesTable . ' ur')
                ->select('r.permissions')
                ->join($this->rolesTable . ' r', 'r.id = ur.role_id', 'inner')
                ->where('ur.email', $email)
                ->get()
                ->getRowArray();

            $permissions = json_decode((string)($row['permissions'] ?? '[]'), true);
            return is_array($permissions) ? array_values(array_filter(array_map('strval', $permissions))) : [];
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
