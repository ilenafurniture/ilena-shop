<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<?php
$config = $config ?? [];
$selectedIds = array_map('strval', (array)($config['province_ids'] ?? []));
$selectedNames = (array)($config['province_names'] ?? []);
$javaBaliNames = [
    'DKI Jakarta',
    'Banten',
    'Jawa Barat',
    'Jawa Tengah',
    'DI Yogyakarta',
    'Jawa Timur',
    'Bali',
];
?>

<style>
.free-ship-page { max-width: 1100px; margin: 0 auto; }
.free-ship-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}
.free-ship-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
}
.free-ship-title { letter-spacing: -1px; font-weight: 800; }
.free-ship-muted { color: #6b7280; }
.free-ship-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    background: #f8fafc;
}
.province-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 10px;
}
.province-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    min-height: 48px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
}
.province-option:hover { background: #f9fafb; }
.province-option input { width: 18px; height: 18px; }
.preset-btn {
    border: 1px solid #ef4444;
    color: #b91c1c;
    background: #fff5f5;
    border-radius: 999px;
    padding: 8px 14px;
    font-weight: 700;
}
.preset-btn:hover { background: #fee2e2; }
@media (max-width: 768px) {
    .free-ship-header { flex-direction: column; }
}
</style>

<div class="free-ship-page">
    <div class="free-ship-header mb-4">
        <div>
            <h1 class="free-ship-title mb-1">Gratis Ongkir</h1>
            <p class="free-ship-muted mb-0">
                Atur wilayah yang mendapat ongkir Rp 0 saat customer memilih kurir checkout.
            </p>
        </div>
        <a href="/admin/product" class="btn btn-light border">Kembali</a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= esc($msg); ?></div>
    <?php endif; ?>

    <form action="/admin/free-shipping" method="post" class="free-ship-card p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <label class="free-ship-switch mb-0">
                <input type="checkbox" name="active" value="1" <?= !empty($config['active']) ? 'checked' : ''; ?>>
                <span class="fw-bold">Aktifkan gratis ongkir</span>
            </label>
            <button type="button" class="preset-btn" id="preset-jawa-bali">
                Pilih Jawa, Madura, Bali
            </button>
        </div>

        <div class="mb-4">
            <label for="label" class="form-label fw-bold">Label promo</label>
            <input type="text" id="label" name="label" class="form-control"
                value="<?= esc($config['label'] ?? 'Gratis ongkir wilayah'); ?>"
                placeholder="Contoh: Gratis ongkir Jawa, Madura, Bali">
            <div class="form-text">Label ini disimpan ke data kurir saat ongkir digratiskan.</div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-2">
                <div>
                    <label class="form-label fw-bold mb-1">Provinsi gratis ongkir</label>
                    <div class="form-text">Centang provinsi tujuan yang ingin ongkirnya menjadi Rp 0.</div>
                </div>
            </div>

            <?php if (!empty($provinsi)): ?>
                <div class="province-grid">
                    <?php foreach ($provinsi as $p): ?>
                        <?php
                            $id = (string)($p['id'] ?? '');
                            $label = (string)($p['label'] ?? $id);
                        ?>
                        <label class="province-option" data-name="<?= esc($label); ?>">
                            <input type="checkbox" name="province_ids[]" value="<?= esc($id); ?>"
                                <?= in_array($id, $selectedIds, true) ? 'checked' : ''; ?>>
                            <span><?= esc($label); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mb-0">
                    Data provinsi belum terbaca dari database. Pakai kolom nama provinsi di bawah ini.
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="province_names" class="form-label fw-bold">Nama provinsi cadangan</label>
            <textarea id="province_names" name="province_names" class="form-control" rows="4"
                placeholder="Satu nama per baris, contoh:&#10;DKI Jakarta&#10;Jawa Barat&#10;Bali"><?= esc(implode("\n", $selectedNames)); ?></textarea>
            <div class="form-text">
                Dipakai sebagai pencocokan tambahan berdasarkan nama provinsi pada alamat customer.
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <button type="reset" class="btn btn-light border">Reset</button>
            <button type="submit" class="btn-default-merah px-4">Simpan Pengaturan</button>
        </div>
    </form>
</div>

<script>
const presetNames = <?= json_encode($javaBaliNames); ?>;
const normalizeRegionName = (value) => (value || '')
    .toLowerCase()
    .replace(/provinsi|propinsi/g, '')
    .replace(/[^a-z0-9]+/g, '');

document.getElementById('preset-jawa-bali')?.addEventListener('click', () => {
    const normalizedPreset = presetNames.map(normalizeRegionName);

    document.querySelectorAll('.province-option').forEach((label) => {
        const name = normalizeRegionName(label.dataset.name);
        const input = label.querySelector('input[type="checkbox"]');
        if (input && normalizedPreset.includes(name)) {
            input.checked = true;
        }
    });

    document.getElementById('province_names').value = presetNames.join("\n");
    document.querySelector('input[name="active"]').checked = true;
});
</script>

<?= $this->endSection(); ?>
