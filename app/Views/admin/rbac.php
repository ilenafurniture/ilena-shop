<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<?php
$permissions = $permissions ?? [];
$roles = $roles ?? [];
$assignments = $assignments ?? [];
$decodePermissions = $decodePermissions ?? static fn($v) => [];
?>

<style>
.rbac-page { max-width: 1180px; margin: 0 auto; }
.rbac-card {
    background:#fff; border:1px solid #e9ecef; border-radius:18px;
    box-shadow:0 10px 24px rgba(15,23,42,.06);
}
.rbac-title { font-weight:800; letter-spacing:-1px; }
.rbac-muted { color:#6b7280; }
.rbac-grid { display:grid; grid-template-columns: 1fr 1fr; gap:18px; }
.permission-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(230px,1fr)); gap:10px; }
.permission-option {
    display:flex; align-items:flex-start; gap:10px; padding:12px;
    border:1px solid #e5e7eb; border-radius:12px; min-height:72px; background:#fff;
}
.permission-option input { width:18px; height:18px; margin-top:3px; }
.role-box { border:1px solid #edf0f3; border-radius:14px; padding:14px; background:#fcfcfd; }
.chip { display:inline-flex; padding:4px 9px; border-radius:999px; background:#fee2e2; color:#991b1b; font-size:12px; font-weight:700; margin:2px; }
@media(max-width: 992px){ .rbac-grid{grid-template-columns:1fr;} }
</style>

<div class="rbac-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="rbac-title mb-1">Role & Akses Admin</h1>
            <p class="rbac-muted mb-0">Buat role, centang fitur yang boleh diakses, lalu assign ke email user.</p>
        </div>
        <a href="/admin/product" class="btn btn-light border">Kembali</a>
    </div>

    <?php if (!empty($msg)): ?><div class="alert alert-success"><?= esc($msg); ?></div><?php endif; ?>
    <?php if (!empty($err)): ?><div class="alert alert-danger"><?= esc($err); ?></div><?php endif; ?>

    <div class="rbac-grid mb-4">
        <form action="/admin/rbac/role" method="post" class="rbac-card p-4">
            <h2 class="h5 fw-bold mb-2">Tambah Role Baru</h2>
            <p class="rbac-muted">Contoh: Staff Produk, Admin Order, Marketing.</p>

            <div class="mb-3">
                <label class="form-label fw-bold" for="role_name">Nama Role</label>
                <input id="role_name" name="role_name" class="form-control" placeholder="Contoh: Staff Produk" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Fitur yang boleh diakses</label>
                <div class="permission-grid">
                    <?php foreach ($permissions as $key => $perm): ?>
                        <label class="permission-option">
                            <input type="checkbox" name="permissions[]" value="<?= esc($key); ?>">
                            <span>
                                <strong><?= esc($perm['label']); ?></strong><br>
                                <small class="text-muted"><?= esc($perm['description']); ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-default-merah px-4">Simpan Role</button>
            </div>
        </form>

        <form action="/admin/rbac/assign" method="post" class="rbac-card p-4">
            <h2 class="h5 fw-bold mb-2">Assign Role ke User</h2>
            <p class="rbac-muted">User yang sudah punya akun bisa login admin sesuai role yang dipilih.</p>

            <div class="mb-3">
                <label class="form-label fw-bold" for="email">Email User</label>
                <textarea id="email" name="email" class="form-control" rows="4" placeholder="staff1@ilenafurniture.com&#10;staff2@ilenafurniture.com" required></textarea>
                <div class="form-text">Bisa isi beberapa email, pisahkan dengan enter atau koma.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold" for="role_id">Role</label>
                <select id="role_id" name="role_id" class="form-select" required>
                    <option value="">Pilih role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role['id']); ?>"><?= esc($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-default-merah px-4">Simpan Akses User</button>
            </div>
        </form>
    </div>

    <div class="rbac-card p-4 mb-4">
        <h2 class="h5 fw-bold mb-3">Role Tersimpan</h2>
        <?php if (empty($roles)): ?>
            <div class="alert alert-warning mb-0">Belum ada role custom.</div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($roles as $role): ?>
                    <?php $rolePerms = $decodePermissions($role['permissions'] ?? '[]'); ?>
                    <div class="role-box">
                        <form action="/admin/rbac/role/<?= esc($role['id']); ?>" method="post">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                                <input name="role_name" class="form-control fw-bold" style="max-width:320px" value="<?= esc($role['role_name']); ?>" required>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-dark">Update</button>
                                    <button type="submit" form="delete-role-<?= esc($role['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus role ini?')">Hapus</button>
                                </div>
                            </div>
                            <div class="permission-grid">
                                <?php foreach ($permissions as $key => $perm): ?>
                                    <label class="permission-option">
                                        <input type="checkbox" name="permissions[]" value="<?= esc($key); ?>" <?= in_array($key, $rolePerms, true) ? 'checked' : ''; ?>>
                                        <span><strong><?= esc($perm['label']); ?></strong><br><small class="text-muted"><?= esc($perm['description']); ?></small></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </form>
                        <form id="delete-role-<?= esc($role['id']); ?>" action="/admin/rbac/role/delete/<?= esc($role['id']); ?>" method="post"></form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="rbac-card p-4">
        <h2 class="h5 fw-bold mb-3">User dengan Role Custom</h2>
        <?php if (empty($assignments)): ?>
            <div class="alert alert-warning mb-0">Belum ada user yang diberi role custom.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Permission</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $row): ?>
                            <?php $rowPerms = $decodePermissions($row['permissions'] ?? '[]'); ?>
                            <tr>
                                <td class="fw-bold"><?= esc($row['email']); ?></td>
                                <td><?= esc($row['role_name'] ?? '-'); ?></td>
                                <td>
                                    <?php foreach ($rowPerms as $permKey): ?>
                                        <span class="chip"><?= esc($permissions[$permKey]['label'] ?? $permKey); ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td class="text-end">
                                    <form action="/admin/rbac/assign/delete" method="post" onsubmit="return confirm('Hapus akses user ini?')">
                                        <input type="hidden" name="email" value="<?= esc($row['email']); ?>">
                                        <button class="btn btn-sm btn-outline-danger">Hapus Akses</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
