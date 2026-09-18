<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<?php
$productCount = count($products ?? []);
$old = static function (string $key, $default = '') {
    return old($key) ?? $default;
};
?>

<style>
.bulk-page {
    padding: 8px 0 32px;
}
.bulk-hero,
.bulk-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, .06);
}
.bulk-hero {
    padding: 22px;
    margin-bottom: 16px;
}
.bulk-title {
    margin: 0;
    font-size: 28px;
    font-weight: 850;
    letter-spacing: -.045em;
    color: #0f172a;
}
.bulk-subtitle {
    margin: 7px 0 0;
    color: #64748b;
    font-size: 14px;
}
.bulk-alert {
    margin-top: 14px;
    border-radius: 14px;
    padding: 12px 14px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    font-size: 13px;
}
.bulk-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 16px;
}
.bulk-card {
    padding: 18px;
}
.bulk-card h2 {
    margin: 0 0 12px;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
}
.bulk-list {
    display: grid;
    gap: 8px;
}
.bulk-item {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
    align-items: center;
    padding: 11px 12px;
    border: 1px solid #eef2f7;
    border-radius: 14px;
    background: #f8fafc;
}
.bulk-item strong {
    display: block;
    color: #111827;
    font-size: 14px;
    line-height: 1.25;
}
.bulk-item span {
    color: #64748b;
    font-size: 12px;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    border-radius: 999px;
    padding: 0 10px;
    font-size: 12px;
    font-weight: 700;
}
.status-on { background: #dcfce7; color: #166534; }
.status-off { background: #fee2e2; color: #991b1b; }
.bulk-field {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 14px;
    margin-bottom: 12px;
}
.bulk-field-head {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 10px;
}
.bulk-field-head label {
    margin: 0;
    font-weight: 800;
    color: #0f172a;
}
.bulk-field small {
    display: block;
    margin-top: 6px;
    color: #64748b;
    line-height: 1.4;
}
.bulk-check {
    width: 18px;
    height: 18px;
    accent-color: #b31217;
}
.bulk-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    margin-top: 16px;
}
.btn-bulk-primary {
    border: 0;
    background: #b31217;
    color: #fff;
    min-height: 44px;
    border-radius: 12px;
    padding: 0 16px;
    font-weight: 800;
    box-shadow: 0 12px 24px rgba(179, 18, 23, .2);
}
.btn-bulk-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 0 16px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    color: #111827;
    text-decoration: none;
    font-weight: 700;
}
@media (max-width: 980px) {
    .bulk-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="bulk-page">
    <div class="bulk-hero">
        <h1 class="bulk-title">Edit Massal Produk</h1>
        <p class="bulk-subtitle"><?= $productCount; ?> produk dipilih. Centang field yang mau diubah, field lain tetap aman.</p>
        <div class="bulk-alert">
            Untuk mencegah salah data, edit massal ini hanya untuk harga, diskon, status, kategori, subkategori, dan tag ruangan.
            Gambar, varian/stok detail, dan deskripsi tetap diedit satu per satu.
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
    <?php endif; ?>

    <form action="/admin/product-bulk-edit" method="post">
        <?= csrf_field(); ?>
        <?php foreach (($ids ?? []) as $id): ?>
            <input type="hidden" name="ids[]" value="<?= esc($id); ?>">
        <?php endforeach; ?>

        <div class="bulk-layout">
            <div class="bulk-card">
                <h2>Produk yang akan terkena perubahan</h2>
                <div class="bulk-list">
                    <?php foreach (($products ?? []) as $product): ?>
                        <div class="bulk-item">
                            <div>
                                <strong><?= esc($product['nama'] ?? '-'); ?></strong>
                                <span>#<?= esc($product['id'] ?? '-'); ?> • Rp <?= number_format((int)($product['harga'] ?? 0), 0, ',', '.'); ?> • Diskon <?= (int)($product['diskon'] ?? 0); ?>%</span>
                            </div>
                            <span class="status-pill <?= !empty($product['active']) ? 'status-on' : 'status-off'; ?>">
                                <?= !empty($product['active']) ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bulk-card">
                <h2>Field yang mau diubah</h2>

                <div class="bulk-field">
                    <div class="bulk-field-head">
                        <input class="bulk-check" type="checkbox" id="update_harga" name="update_harga" value="1">
                        <label for="update_harga">Ubah harga</label>
                    </div>
                    <input class="form-control" type="number" name="harga" min="0" step="1000" value="<?= esc($old('harga', '0')); ?>" placeholder="Contoh: 1250000">
                    <small>Harga yang sama akan diterapkan ke semua produk terpilih.</small>
                </div>

                <div class="bulk-field">
                    <div class="bulk-field-head">
                        <input class="bulk-check" type="checkbox" id="update_diskon" name="update_diskon" value="1">
                        <label for="update_diskon">Ubah diskon</label>
                    </div>
                    <input class="form-control" type="number" name="diskon" min="0" max="100" value="<?= esc($old('diskon', '0')); ?>" placeholder="0 - 100">
                    <small>Masukkan persen diskon, contoh 10 untuk 10%.</small>
                </div>

                <div class="bulk-field">
                    <div class="bulk-field-head">
                        <input class="bulk-check" type="checkbox" id="update_active" name="update_active" value="1">
                        <label for="update_active">Ubah status tampil</label>
                    </div>
                    <select class="form-select" name="active">
                        <option value="1">Aktif / tampil di website</option>
                        <option value="0">Nonaktif / sembunyikan</option>
                    </select>
                </div>

                <div class="bulk-field">
                    <div class="bulk-field-head">
                        <input class="bulk-check" type="checkbox" id="update_kategori" name="update_kategori" value="1">
                        <label for="update_kategori">Ubah koleksi</label>
                    </div>
                    <select class="form-select" name="kategori">
                        <?php foreach (($koleksi ?? []) as $k): ?>
                            <option value="<?= esc($k['id']); ?>"><?= esc($k['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bulk-field">
                    <div class="bulk-field-head">
                        <input class="bulk-check" type="checkbox" id="update_subkategori" name="update_subkategori" value="1">
                        <label for="update_subkategori">Ubah subkategori</label>
                    </div>
                    <select class="form-select" name="subkategori">
                        <?php foreach (($jenis ?? []) as $j): ?>
                            <option value="<?= esc($j['id']); ?>"><?= esc($j['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bulk-field">
                    <div class="bulk-field-head">
                        <input class="bulk-check" type="checkbox" id="update_ruangan" name="update_ruangan" value="1">
                        <label for="update_ruangan">Ubah tag ruangan</label>
                    </div>
                    <label class="d-block mb-2"><input type="checkbox" name="ruang_tamu" value="1"> Ruang tamu</label>
                    <label class="d-block mb-2"><input type="checkbox" name="ruang_keluarga" value="1"> Ruang keluarga</label>
                    <label class="d-block"><input type="checkbox" name="ruang_tidur" value="1"> Ruang tidur</label>
                    <small>Jika field ini dicentang, tag ruangan lama akan diganti sesuai pilihan di sini.</small>
                </div>

                <div class="bulk-actions">
                    <a class="btn-bulk-secondary" href="/admin/product">Batal</a>
                    <button class="btn-bulk-primary" type="submit" onclick="return confirm('Simpan perubahan untuk <?= $productCount; ?> produk terpilih?')">
                        Simpan untuk <?= $productCount; ?> produk
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>
