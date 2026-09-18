<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<?php
$isEdit = !empty($editPartner);
$formAction = $isEdit ? '/admin/partners/' . $editPartner['id'] : '/admin/partners';
$val = static function (string $key, $default = '') use ($editPartner) {
    return old($key) ?? ($editPartner[$key] ?? $default);
};
?>

<style>
.partner-admin-wrap { display:grid; gap:18px; }
.partner-admin-hero,
.partner-admin-card {
    background:#fff;
    border:1px solid #eef2f7;
    border-radius:22px;
    box-shadow:0 18px 45px rgba(15,23,42,.06);
}
.partner-admin-hero { padding:22px; display:flex; justify-content:space-between; gap:14px; align-items:flex-start; }
.partner-admin-hero h1 { margin:0 0 6px; font-size:28px; font-weight:900; letter-spacing:-.045em; color:#0f172a; }
.partner-admin-hero p { margin:0; color:#64748b; font-size:14px; }
.partner-admin-grid { display:grid; grid-template-columns:420px minmax(0,1fr); gap:18px; align-items:start; }
.partner-admin-card { padding:18px; }
.partner-admin-card h2 { margin:0 0 14px; font-size:17px; font-weight:850; color:#0f172a; }
.form-hint { color:#64748b; font-size:12px; margin-top:5px; }
.partner-list { display:grid; gap:10px; }
.partner-row {
    display:grid;
    grid-template-columns:74px minmax(0,1fr) auto;
    gap:12px;
    align-items:center;
    padding:11px;
    border:1px solid #eef2f7;
    border-radius:16px;
    background:#fbfcff;
}
.partner-thumb {
    width:74px; height:58px; border-radius:12px; object-fit:cover; background:#f1f5f9; border:1px solid #e5e7eb;
}
.partner-empty-thumb {
    width:74px; height:58px; border-radius:12px; display:flex; align-items:center; justify-content:center;
    background:#f8fafc; color:#94a3b8; border:1px dashed #cbd5e1;
}
.partner-row h3 { margin:0; font-size:14px; font-weight:850; color:#0f172a; }
.partner-row p { margin:3px 0 0; color:#64748b; font-size:12px; line-height:1.35; }
.partner-actions { display:flex; gap:6px; align-items:center; }
.partner-actions a,
.partner-actions button {
    width:38px; height:38px; border-radius:11px; border:1px solid #e5e7eb; background:#fff; color:#111827;
    display:inline-flex; align-items:center; justify-content:center; text-decoration:none;
}
.partner-actions button.danger { color:#b91c1c; background:#fff5f5; border-color:#fecaca; }
.status-pill { display:inline-flex; margin-top:5px; padding:3px 8px; border-radius:999px; font-size:11px; font-weight:800; }
.status-on { background:#dcfce7; color:#166534; }
.status-off { background:#fee2e2; color:#991b1b; }
.btn-ilena { background:#b31217; color:#fff; border:0; min-height:44px; border-radius:12px; padding:0 16px; font-weight:850; }
.btn-soft { background:#fff; color:#111827; border:1px solid #e5e7eb; min-height:44px; border-radius:12px; padding:0 14px; text-decoration:none; display:inline-flex; align-items:center; }
@media (max-width: 980px) {
    .partner-admin-grid { grid-template-columns:1fr; }
    .partner-admin-hero { flex-direction:column; }
    .partner-row { grid-template-columns:62px minmax(0,1fr); }
    .partner-actions { grid-column:1 / -1; justify-content:flex-end; }
}
</style>

<div class="partner-admin-wrap">
    <div class="partner-admin-hero">
        <div>
            <h1>Partner Ilena</h1>
            <p>Kelola daftar toko/mitra yang tampil di halaman <b>/partner</b>. Simple: isi nama, kota, alamat, link Maps, foto, titik koordinat.</p>
        </div>
        <a class="btn-soft" href="/partner" target="_blank">Lihat halaman partner</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
    <?php endif; ?>

    <div class="partner-admin-grid">
        <div class="partner-admin-card">
            <h2><?= $isEdit ? 'Edit Partner' : 'Tambah Partner'; ?></h2>
            <form action="<?= esc($formAction); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama toko / partner</label>
                    <input class="form-control" name="name" value="<?= esc($val('name')); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Kota</label>
                    <input class="form-control" name="city" value="<?= esc($val('city')); ?>" required placeholder="Contoh: Surabaya">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat</label>
                    <textarea class="form-control" name="address" rows="3" required><?= esc($val('address')); ?></textarea>
                    <div class="form-hint">Kalau link Maps kosong, sistem otomatis membuat link dari alamat.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Link Google Maps</label>
                    <input class="form-control" name="maps_url" value="<?= esc($val('maps_url')); ?>" placeholder="https://maps.app.goo.gl/...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">URL foto toko</label>
                    <input class="form-control" name="image_url" value="<?= esc($val('image_url')); ?>" placeholder="https://...webp">
                    <div class="form-hint">Bisa pakai URL dari image server/hosting. Jika kosong akan tampil placeholder.</div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Latitude</label>
                        <input class="form-control" name="lat" value="<?= esc($val('lat')); ?>" placeholder="-7.123456">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Longitude</label>
                        <input class="form-control" name="lng" value="<?= esc($val('lng')); ?>" placeholder="112.123456">
                    </div>
                </div>
                <div class="row align-items-end">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Urutan</label>
                        <input class="form-control" type="number" name="sort_order" value="<?= esc($val('sort_order', count($partners ?? []) + 1)); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="d-flex gap-2 align-items-center">
                            <input type="checkbox" name="active" value="1" <?= (string)$val('active', '1') === '1' ? 'checked' : ''; ?>>
                            <span class="fw-bold">Tampilkan</span>
                        </label>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-ilena" type="submit"><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Partner'; ?></button>
                    <?php if ($isEdit): ?><a class="btn-soft" href="/admin/partners">Batal edit</a><?php endif; ?>
                </div>
            </form>
        </div>

        <div class="partner-admin-card">
            <h2>Daftar Partner (<?= count($partners ?? []); ?>)</h2>
            <div class="partner-list">
                <?php if (empty($partners)): ?>
                    <p class="text-muted mb-0">Belum ada partner dari admin. Tambahkan data pertama lewat form kiri.</p>
                <?php endif; ?>
                <?php foreach (($partners ?? []) as $partner): ?>
                    <div class="partner-row">
                        <?php if (!empty($partner['image_url'])): ?>
                            <img class="partner-thumb" src="<?= esc($partner['image_url']); ?>" alt="<?= esc($partner['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="partner-empty-thumb"><i class="material-icons">storefront</i></div>
                        <?php endif; ?>
                        <div>
                            <h3><?= esc($partner['name']); ?></h3>
                            <p><?= esc($partner['city']); ?> • <?= esc($partner['address']); ?></p>
                            <span class="status-pill <?= !empty($partner['active']) ? 'status-on' : 'status-off'; ?>"><?= !empty($partner['active']) ? 'Tampil' : 'Nonaktif'; ?></span>
                        </div>
                        <div class="partner-actions">
                            <a href="/admin/partners?edit=<?= esc($partner['id']); ?>" title="Edit"><i class="material-icons">edit</i></a>
                            <a href="<?= esc($partner['maps_url'] ?: '#'); ?>" target="_blank" title="Maps"><i class="material-icons">map</i></a>
                            <form action="/admin/partners/delete/<?= esc($partner['id']); ?>" method="post" onsubmit="return confirm('Hapus partner <?= esc($partner['name'], 'js'); ?>?')">
                                <?= csrf_field(); ?>
                                <button class="danger" type="submit" title="Hapus"><i class="material-icons">delete</i></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
