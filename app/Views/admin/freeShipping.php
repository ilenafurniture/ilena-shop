<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<?php
$config = $config ?? [];
$hasOldInput = old('form_submitted') === '1';
$selectedIds = array_map('strval', (array)($hasOldInput ? (old('province_ids') ?? []) : ($config['province_ids'] ?? [])));
$selectedKabupatenIds = array_map('strval', (array)($hasOldInput ? (old('kabupaten_ids') ?? []) : ($config['kabupaten_ids'] ?? [])));
$hasSelectedIds = $selectedIds !== [] || $selectedKabupatenIds !== [];
$selectedNames = !$hasSelectedIds
    ? ($hasOldInput ? (old('province_names') ?? '') : ($config['province_names'] ?? []))
    : [];
if (is_string($selectedNames)) {
    $selectedNames = preg_split('/[\r\n,]+/', $selectedNames) ?: [];
} else {
    $selectedNames = (array)$selectedNames;
}
$promoActive = $hasOldInput ? old('active') === '1' : !empty($config['active']);
$minimumOrderAmount = $hasOldInput ? old('minimum_order_amount') : ($config['minimum_order_amount'] ?? 0);
$maximumShippingRate = $hasOldInput ? old('maximum_shipping_rate') : ($config['maximum_shipping_rate'] ?? 0);
$normalizeRegionName = static function ($value): string {
    $value = strtolower(trim((string)$value));
    $value = str_replace(['provinsi', 'propinsi'], '', $value);
    $value = preg_replace('/[^a-z0-9]+/i', '', $value) ?? '';
    return match ($value) {
        'jakarta', 'daerahkhususibukotajakarta' => 'dkijakarta',
        'yogyakarta', 'daerahistimewayogyakarta' => 'diyogyakarta',
        default => $value,
    };
};
$selectedNormalizedNames = array_values(array_filter(array_map($normalizeRegionName, $selectedNames)));
$kabupatenByProvince = [];
foreach ((array)($kabupaten ?? []) as $city) {
    $parentId = (string)($city['provinsi_id'] ?? '');
    $kabupatenByProvince[$parentId][] = $city;
}
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
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 310px), 1fr));
    gap: 10px;
}
.province-group {
    align-self: start;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    overflow: clip;
}
.province-group[hidden] { display: none; }
.province-group[hidden] { display: none; }
.province-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 48px;
    padding: 10px 12px;
}
.province-heading:hover { background: #f9fafb; }
.region-check {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 44px;
    cursor: pointer;
}
.region-check input { width: 18px; height: 18px; flex: 0 0 auto; accent-color: #a3262d; }
.region-check--province { flex: 1; font-weight: 700; }
.province-count {
    min-width: 1.8em;
    padding: 2px 7px;
    border-radius: 999px;
    background: #f3f4f6;
    color: #374151;
    text-align: center;
    font-size: 12px;
}
.city-details > summary {
    display: flex;
    align-items: center;
    min-height: 44px;
    padding: 5px 8px;
    color: #5c2529;
    font-size: 12px;
    font-weight: 700;
    list-style: none;
    cursor: pointer;
}
.city-details > summary::-webkit-details-marker { display: none; }
.city-details > summary:hover { color: #a3262d; text-decoration: underline; }
.city-details > summary:focus-visible { outline: 3px solid rgba(163, 38, 45, .3); outline-offset: 2px; }
.city-details[open] > summary { color: #a3262d; }
.city-list { padding: 3px 12px 12px 38px; border-top: 1px solid #f0f1f3; }
.city-list[hidden] { display: none; }
.region-check--city { border-bottom: 1px solid #f3f4f6; }
.region-check--city:last-child { border-bottom: 0; }
.region-check[hidden] { display: none; }
.region-search { max-width: 460px; }
.region-selection-summary { color: #374151; font-size: 13px; font-weight: 600; }
.province-group:focus-within { border-color: #a3262d; }
.region-search:focus-visible, .region-check input:focus-visible, .preset-btn:focus-visible {
    outline: 3px solid rgba(163, 38, 45, .3);
    outline-offset: 2px;
}
.region-check, .preset-btn { cursor: pointer; }
.preset-btn:active { transform: translateY(1px); }
.preset-btn {
    border: 1px solid #ef4444;
    color: #b91c1c;
    background: #fff5f5;
    border-radius: 999px;
    padding: 8px 14px;
    font-weight: 700;
}
.preset-btn:hover { background: #fee2e2; }
.free-ship-feedback { margin: 0 0 18px; padding: 13px 15px; border-left: 3px solid; font-size: 13px; line-height: 1.5; }
.free-ship-feedback--success { border-color: #54745d; background: #f3f8f3; color: #304b37; }
.free-ship-feedback--error { border-color: #a3262d; background: #fff6f5; color: #672327; }
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

    <?php if (!empty($error)): ?>
        <div class="free-ship-feedback free-ship-feedback--error" role="alert"><?= esc($error); ?></div>
    <?php elseif (!empty($msg)): ?>
        <div class="free-ship-feedback free-ship-feedback--success" role="status"><?= esc($msg); ?></div>
    <?php endif; ?>

    <form action="/admin/free-shipping" method="post" class="free-ship-card p-4">
        <?= csrf_field(); ?>
        <input type="hidden" name="form_submitted" value="1">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <label class="free-ship-switch mb-0">
                <input type="checkbox" name="active" value="1" <?= $promoActive ? 'checked' : ''; ?>>
                <span class="fw-bold">Aktifkan gratis ongkir</span>
            </label>
            <button type="button" class="preset-btn" id="preset-jawa-bali">
                Pilih Jawa, Madura, Bali
            </button>
        </div>

        <div class="mb-4">
            <label for="label" class="form-label fw-bold">Label promo</label>
            <input type="text" id="label" name="label" class="form-control"
                maxlength="120" required value="<?= esc($hasOldInput ? (old('label') ?? '') : ($config['label'] ?? 'Gratis ongkir wilayah')); ?>"
                placeholder="Contoh: Gratis ongkir Jawa, Madura, Bali">
            <div class="form-text">Label ini disimpan ke data kurir saat ongkir digratiskan.</div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-2">
                <div>
                    <label for="region-search" class="form-label fw-bold mb-1">Wilayah gratis ongkir</label>
                    <div class="form-text">Centang satu provinsi untuk mencakup semua kota/kabupaten, atau buka provinsinya untuk memilih kota tertentu.</div>
                </div>
            </div>

            <?php if (!empty($provinsi)): ?>
                <input type="search" id="region-search" class="form-control region-search mb-2" placeholder="Cari provinsi atau kota/kabupaten..." autocomplete="off" aria-describedby="region-selection-summary">
                <p id="region-selection-summary" class="region-selection-summary mb-2" aria-live="polite"></p>
                <div class="province-grid" id="free-shipping-regions">
                    <?php foreach ($provinsi as $p): ?>
                        <?php
                            $id = (string)($p['id'] ?? '');
                            $label = (string)($p['label'] ?? $id);
                            $cities = $kabupatenByProvince[$id] ?? [];
                            $isChecked = $hasOldInput
                                ? in_array($id, $selectedIds, true)
                                : (in_array($id, $selectedIds, true)
                                    || (!$hasSelectedIds && in_array($normalizeRegionName($label), $selectedNormalizedNames, true)));
                            $selectedCityCount = count(array_filter($cities, static fn(array $city): bool => in_array((string)($city['id'] ?? ''), $selectedKabupatenIds, true)));
                        ?>
                        <?php $cityPanelId = 'kabupaten-' . (preg_replace('/[^a-zA-Z0-9_-]/', '-', $id) ?: 'wilayah'); ?>
                        <section class="province-group" data-province="<?= esc($label, 'attr'); ?>">
                            <div class="province-heading">
                                <label class="region-check region-check--province">
                                    <input type="checkbox" name="province_ids[]" value="<?= esc($id, 'attr'); ?>" <?= $isChecked ? 'checked' : ''; ?> aria-label="Cakup seluruh wilayah <?= esc($label, 'attr'); ?>">
                                    <span><?= esc($label); ?></span>
                                </label>
                                <span class="province-count" data-count-for="<?= esc($id, 'attr'); ?>"><?= $isChecked ? 'Semua' : $selectedCityCount; ?></span>
                            </div>
                            <?php if ($cities !== []): ?>
                            <details class="city-details" id="<?= esc($cityPanelId, 'attr'); ?>">
                                <summary>Pilih kota/kabupaten</summary>
                                <div class="city-list">
                                <?php if ($cities !== []): ?>
                                    <?php foreach ($cities as $city): ?>
                                        <?php $cityId = (string)($city['id'] ?? ''); $cityLabel = (string)($city['label'] ?? $cityId); ?>
                                        <label class="region-check region-check--city" data-city="<?= esc($cityLabel, 'attr'); ?>">
                                            <input type="checkbox" name="kabupaten_ids[]" value="<?= esc($cityId, 'attr'); ?>" <?= in_array($cityId, $selectedKabupatenIds, true) ? 'checked' : ''; ?> aria-label="Cakup <?= esc($cityLabel, 'attr'); ?>">
                                            <span><?= esc($cityLabel); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="form-text">Daftar kota/kabupaten belum tersedia.</span>
                                <?php endif; ?>
                                </div>
                            </details>
                            <?php else: ?>
                                <div class="city-list"><span class="form-text">Daftar kota/kabupaten belum tersedia.</span></div>
                            <?php endif; ?>
                        </section>
                    <?php endforeach; ?>
                </div>
                <p id="region-no-results" class="form-text mt-2 mb-0" role="status" hidden>Wilayah tidak ditemukan. Coba kata kunci lain.</p>
            <?php else: ?>
                <div class="alert alert-warning mb-0">
                    Data provinsi belum terbaca dari database. Pilih wilayah melalui kolom nama cadangan.
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="province_names" class="form-label fw-bold">Nama provinsi cadangan</label>
            <textarea id="province_names" name="province_names" class="form-control" rows="4"
                placeholder="Satu nama per baris, contoh:&#10;DKI Jakarta&#10;Jawa Barat&#10;Bali"><?= esc(implode("\n", $selectedNames)); ?></textarea>
            <div class="form-text">
                Cadangan jika data wilayah belum tersedia. Jika memilih provinsi atau kota/kabupaten di atas, pilihan tersebut menjadi acuan dan nama cadangan diabaikan.
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="minimum_order_amount" class="form-label fw-bold">Minimum belanja (Rp)</label>
                <input type="number" id="minimum_order_amount" name="minimum_order_amount" class="form-control" min="0" max="9999999999" step="1" value="<?= esc((string)$minimumOrderAmount, 'attr'); ?>">
                <div class="form-text">0 berarti tanpa minimum. Dihitung dari subtotal produk sebelum voucher dan ongkir.</div>
            </div>
            <div class="col-md-6">
                <label for="maximum_shipping_rate" class="form-label fw-bold">Batas tarif kurir (Rp)</label>
                <input type="number" id="maximum_shipping_rate" name="maximum_shipping_rate" class="form-control" min="0" max="9999999999" step="1" value="<?= esc((string)$maximumShippingRate, 'attr'); ?>">
                <div class="form-text">0 berarti tanpa batas. Gratis berlaku jika tarif kurir tidak melebihi batas ini.</div>
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
    .replace(/[^a-z0-9]+/g, '')
    .replace(/^(jakarta|daerahkhususibukotajakarta)$/, 'dkijakarta')
    .replace(/^(yogyakarta|daerahistimewayogyakarta)$/, 'diyogyakarta');

document.getElementById('preset-jawa-bali')?.addEventListener('click', () => {
    const normalizedPreset = presetNames.map(normalizeRegionName);
    const provinceOptions = document.querySelectorAll('.region-check--province');

    provinceOptions.forEach((label) => {
        const input = label.querySelector('input[type="checkbox"]');
        const group = label.closest('.province-group');
        const name = normalizeRegionName(group?.dataset.province);
        if (input) input.checked = normalizedPreset.includes(name);
        group?.querySelectorAll('.region-check--city input[type="checkbox"]').forEach((cityInput) => { cityInput.checked = false; });
    });

    document.getElementById('province_names').value = provinceOptions.length === 0 ? presetNames.join("\n") : '';
    document.querySelector('input[name="active"]').checked = true;
    updateRegionSummary();
});

const regionContainer = document.getElementById('free-shipping-regions');
const regionSearch = document.getElementById('region-search');
const normalizeSearch = (value) => (value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('id').trim();

function updateRegionSummary() {
    if (!regionContainer) return;
    const provinceInputs = [...regionContainer.querySelectorAll('.region-check--province input')];
    const cityInputs = [...regionContainer.querySelectorAll('.region-check--city input')];
    const fullProvinceCount = provinceInputs.filter((input) => input.checked).length;
    const cityCount = cityInputs.filter((input) => input.checked).length;
    const summary = document.getElementById('region-selection-summary');
    if (summary) summary.textContent = `${fullProvinceCount} provinsi penuh, ${cityCount} kota/kabupaten khusus dipilih`;

    regionContainer.querySelectorAll('.province-group').forEach((group) => {
        const provinceInput = group.querySelector('.region-check--province input');
        const checkedCities = group.querySelectorAll('.region-check--city input:checked').length;
        const count = group.querySelector('.province-count');
        if (count) count.textContent = provinceInput?.checked ? 'Semua' : String(checkedCities);
    });
}

regionContainer?.addEventListener('change', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement) || target.type !== 'checkbox') return;
    const group = target.closest('.province-group');
    if (target.name === 'province_ids[]' && target.checked) {
        group?.querySelectorAll('.region-check--city input[type="checkbox"]').forEach((input) => { input.checked = false; });
    } else if (target.name === 'kabupaten_ids[]' && target.checked) {
        const provinceInput = group?.querySelector('.region-check--province input');
        if (provinceInput) provinceInput.checked = false;
    }
    updateRegionSummary();
});

regionSearch?.addEventListener('input', () => {
    const query = normalizeSearch(regionSearch.value);
    const groups = [...(regionContainer?.querySelectorAll('.province-group') ?? [])];
    let visibleGroupCount = 0;
    groups.forEach((group) => {
        const provinceMatches = normalizeSearch(group.dataset.province).includes(query);
        const cityLabels = [...group.querySelectorAll('.region-check--city')];
        let matchingCityFound = false;
        cityLabels.forEach((label) => {
            const matches = !query || normalizeSearch(label.dataset.city).includes(query);
            label.hidden = !matches;
            if (matches) matchingCityFound = true;
        });
        group.hidden = Boolean(query) && !provinceMatches && !matchingCityFound;
        if (!group.hidden) visibleGroupCount += 1;
        const cityDetails = group.querySelector('.city-details');
        if (query && !provinceMatches && matchingCityFound && cityDetails) cityDetails.open = true;
        if (!query && cityDetails) cityDetails.open = false;
    });
    const noResults = document.getElementById('region-no-results');
    if (noResults) noResults.hidden = !query || visibleGroupCount > 0;
});

updateRegionSummary();
</script>

<?= $this->endSection(); ?>
