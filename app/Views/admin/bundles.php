<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<?php
$formBundles = [];
foreach ($bundles as $bundle) {
    $bundle['items'] = $itemsByBundle[(int) $bundle['id']] ?? [];
    $formBundles[(string) $bundle['id']] = $bundle;
}
$variantChoices = [];
foreach ($products as $product) {
    foreach ($product['varian'] as $variant) {
        if (!is_array($variant) || (int) ($variant['stok'] ?? 0) < 1) continue;
        $variantChoices[] = [
            'value' => (string) $product['id'] . '|' . (string) $variant['nama'],
            'label' => (string) $product['nama'] . ' — ' . (string) $variant['nama'],
        ];
    }
}
$dateInput = static fn ($value): string => $value ? str_replace(' ', 'T', substr((string) $value, 0, 16)) : '';
?>
<main class="admin-bundles">
    <header class="admin-bundles__head">
        <div><p>MERCHANDISING</p><h1>Paket furniture</h1><span>Susun set dari produk dan varian aktif. Harga paket mengikuti harga terbaru dan stok komponen; semua jadwal menggunakan WIB.</span></div>
        <a href="/admin/product">Kembali ke katalog</a>
    </header>
    <?php if ($message = session()->getFlashdata('success')): ?><p class="admin-bundles__notice is-success" role="status"><?= esc($message); ?></p><?php endif; ?>
    <?php if ($message = session()->getFlashdata('error')): ?><p class="admin-bundles__notice" role="alert"><?= esc($message); ?></p><?php endif; ?>

    <section class="admin-bundles__editor" aria-labelledby="bundle-form-title">
        <div class="admin-bundles__section-head"><div><p>EDITOR PAKET</p><h2 id="bundle-form-title">Buat paket baru</h2></div><button type="button" id="bundleReset" class="admin-bundles__quiet">Reset form</button></div>
        <form id="bundleForm" method="post" action="/admin/bundles">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="bundleId" value="">
            <div class="admin-bundles__fields">
                <label>Nama paket<input name="name" id="bundleName" maxlength="180" required></label>
                <label>Slug URL<input name="slug" id="bundleSlug" maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*" required></label>
                <label>Nama Inggris<input name="name_en" id="bundleNameEn" maxlength="180"></label>
                <label>Diskon paket (%)<input name="discount_percent" id="bundleDiscount" type="number" min="0" max="99.99" step="0.01" value="0" required></label>
                <label class="admin-bundles__wide">Deskripsi<textarea name="description" id="bundleDescription" rows="2" maxlength="1500"></textarea></label>
                <label class="admin-bundles__wide">Description (English)<textarea name="description_en" id="bundleDescriptionEn" rows="2" maxlength="1500"></textarea></label>
                <label>Mulai (opsional)<input type="datetime-local" name="starts_at" id="bundleStarts"></label>
                <label>Selesai (opsional)<input type="datetime-local" name="ends_at" id="bundleEnds"></label>
                <label class="admin-bundles__active"><input type="checkbox" name="active" id="bundleActive" value="1"> Tampilkan saat jadwal aktif dan stok tersedia</label>
            </div>
            <div class="admin-bundles__component-head"><div><h3>Komponen paket</h3><p>Minimal dua produk-varian berbeda. Stok setiap komponen dicek kembali saat checkout.</p></div><button type="button" id="addBundleItem" class="admin-bundles__quiet">Tambah komponen</button></div>
            <div id="bundleItems" class="admin-bundles__items"></div>
            <div class="admin-bundles__submit"><span>Diskon diterapkan per komponen dan dibulatkan ke rupiah; kupon tidak dapat digabung untuk paket.</span><button type="submit">Simpan paket</button></div>
        </form>
    </section>

    <section class="admin-bundles__list" aria-labelledby="bundle-list-title">
        <div class="admin-bundles__section-head"><div><p>DAFTAR</p><h2 id="bundle-list-title">Paket tersimpan <small><?= count($bundles); ?></small></h2></div></div>
        <?php if ($bundles === []): ?><p class="admin-bundles__empty">Belum ada paket. Buat paket pertama melalui editor di atas.</p><?php else: ?>
            <div class="admin-bundles__table-wrap"><table><thead><tr><th>Paket</th><th>Komponen</th><th>Diskon</th><th>Jadwal</th><th>Status</th><th></th></tr></thead><tbody>
            <?php foreach ($bundles as $bundle): $components = $itemsByBundle[(int) $bundle['id']] ?? []; ?>
                <tr>
                    <td><strong><?= esc($bundle['name']); ?></strong><small>/bundles/<?= esc($bundle['slug']); ?></small></td>
                    <td><?= count($components); ?> produk</td>
                    <td><?= esc(number_format((float) $bundle['discount_percent'], 2, ',', '.')); ?>%</td>
                    <td><small><?= esc($dateInput($bundle['starts_at'] ?? null) ?: 'Tanpa mulai'); ?><br><?= esc($dateInput($bundle['ends_at'] ?? null) ?: 'Tanpa akhir'); ?></small></td>
                    <td><span class="admin-bundles__state <?= (int) $bundle['active'] === 1 ? 'is-on' : ''; ?>"><?= (int) $bundle['active'] === 1 ? 'Aktif' : 'Disimpan'; ?></span></td>
                    <td><button type="button" class="admin-bundles__edit" data-bundle-id="<?= (int) $bundle['id']; ?>">Edit</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table></div>
        <?php endif; ?>
    </section>
</main>

<template id="bundleItemTemplate">
    <div class="admin-bundles__item-row">
        <label>Produk & varian<select name="items[__INDEX__][product_variant]" required><option value="">Pilih komponen</option><?php foreach ($variantChoices as $choice): ?><option value="<?= esc($choice['value'], 'attr'); ?>"><?= esc($choice['label']); ?></option><?php endforeach; ?></select></label>
        <label>Jumlah<input name="items[__INDEX__][quantity]" type="number" min="1" max="100" value="1" required></label>
        <button type="button" class="admin-bundles__remove" aria-label="Hapus komponen">Hapus</button>
    </div>
</template>
<script type="application/json" id="bundleData"><?= json_encode($formBundles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?></script>
<script>
(() => {
  const form = document.getElementById('bundleForm');
  const rows = document.getElementById('bundleItems');
  const template = document.getElementById('bundleItemTemplate');
  const csrfInput = form.querySelector('input[type="hidden"]');
  const bundles = JSON.parse(document.getElementById('bundleData').textContent || '{}');
  const addRow = (component = null) => {
    const index = rows.children.length;
    const fragment = template.content.cloneNode(true);
    const row = fragment.querySelector('.admin-bundles__item-row');
    row.innerHTML = row.innerHTML.replaceAll('__INDEX__', String(index));
    if (component) {
      row.querySelector('select').value = `${component.product_id}|${component.variant_name}`;
      row.querySelector('input[type="number"]').value = component.quantity;
    }
    row.querySelector('.admin-bundles__remove').addEventListener('click', () => row.remove());
    rows.append(fragment);
  };
  const reset = () => {
    form.reset();
    document.getElementById('bundleId').value = '';
    form.action = '/admin/bundles';
    document.getElementById('bundle-form-title').textContent = 'Buat paket baru';
    document.getElementById('bundleDiscount').value = '0';
    rows.replaceChildren();
    addRow(); addRow();
    if (csrfInput) csrfInput.name = '<?= csrf_token(); ?>';
  };
  document.getElementById('addBundleItem').addEventListener('click', () => addRow());
  document.getElementById('bundleReset').addEventListener('click', reset);
  document.querySelectorAll('[data-bundle-id]').forEach(button => button.addEventListener('click', () => {
    const bundle = bundles[button.dataset.bundleId];
    if (!bundle) return;
    form.reset(); rows.replaceChildren();
    document.getElementById('bundleId').value = bundle.id;
    document.getElementById('bundleName').value = bundle.name || '';
    document.getElementById('bundleSlug').value = bundle.slug || '';
    document.getElementById('bundleNameEn').value = bundle.name_en || '';
    document.getElementById('bundleDescription').value = bundle.description || '';
    document.getElementById('bundleDescriptionEn').value = bundle.description_en || '';
    document.getElementById('bundleDiscount').value = bundle.discount_percent || '0';
    document.getElementById('bundleStarts').value = (bundle.starts_at || '').replace(' ', 'T').slice(0, 16);
    document.getElementById('bundleEnds').value = (bundle.ends_at || '').replace(' ', 'T').slice(0, 16);
    document.getElementById('bundleActive').checked = Number(bundle.active) === 1;
    (bundle.items || []).forEach(addRow);
    form.action = `/admin/bundles/${encodeURIComponent(bundle.id)}`;
    document.getElementById('bundle-form-title').textContent = 'Edit paket';
    form.scrollIntoView({behavior: 'smooth', block: 'start'});
  }));
  reset();
})();
</script>
<style>
.admin-bundles{--bi:#29241f;--bm:#6e655d;--bl:#e7e0d8;max-width:1320px;margin:0 auto;padding:20px 18px 56px;color:var(--bi)}.admin-bundles__head,.admin-bundles__section-head,.admin-bundles__component-head,.admin-bundles__submit{display:flex;align-items:center;justify-content:space-between;gap:18px}.admin-bundles__head{padding:18px 0 24px;border-bottom:1px solid var(--bl);margin-bottom:20px}.admin-bundles__head p,.admin-bundles__section-head p{margin:0 0 7px;color:#9b252b;font-size:.69rem;font-weight:800;letter-spacing:.15em}.admin-bundles h1,.admin-bundles h2,.admin-bundles h3{margin:0;letter-spacing:-.035em}.admin-bundles h1{font-size:clamp(1.7rem,3vw,2.4rem)}.admin-bundles__head span,.admin-bundles__component-head p{display:block;margin-top:7px;color:var(--bm);font-size:.9rem}.admin-bundles__head>a{color:var(--bi);font-weight:700}.admin-bundles__editor,.admin-bundles__list{padding:20px 0;border-top:1px solid var(--bl)}.admin-bundles__section-head{margin-bottom:18px}.admin-bundles__section-head h2{font-size:1.35rem}.admin-bundles__section-head small{color:var(--bm);font-size:.8rem}.admin-bundles__fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 18px}.admin-bundles label{display:grid;gap:6px;font-size:.82rem;font-weight:700}.admin-bundles input:not([type=checkbox]),.admin-bundles select,.admin-bundles textarea{width:100%;min-height:42px;padding:9px 11px;border:1px solid #cfc7bf;background:#fff;color:var(--bi);font:inherit;font-weight:400}.admin-bundles textarea{resize:vertical}.admin-bundles__wide{grid-column:1/-1}.admin-bundles__active{display:flex!important;align-items:center;gap:8px}.admin-bundles__component-head{margin:22px 0 12px}.admin-bundles__component-head h3{font-size:1rem}.admin-bundles__component-head p{margin-bottom:0}.admin-bundles__item-row{display:grid;grid-template-columns:minmax(0,1fr) 130px auto;gap:12px;align-items:end;padding:12px 0;border-bottom:1px solid var(--bl)}.admin-bundles__quiet,.admin-bundles__remove,.admin-bundles__edit{min-height:40px;padding:0 14px;background:#fff;border:1px solid #bdb4ab;color:var(--bi);font:inherit;font-weight:700;cursor:pointer}.admin-bundles__remove{color:#8f1d22}.admin-bundles__submit{margin-top:18px}.admin-bundles__submit span{max-width:680px;color:var(--bm);font-size:.82rem;line-height:1.5}.admin-bundles__submit button{min-height:44px;padding:0 22px;border:0;background:#981f26;color:#fff;font:inherit;font-weight:750;cursor:pointer}.admin-bundles__table-wrap{overflow:auto}.admin-bundles table{width:100%;border-collapse:collapse}.admin-bundles th,.admin-bundles td{padding:12px 10px;border-bottom:1px solid var(--bl);text-align:left;vertical-align:middle}.admin-bundles th{font-size:.75rem;color:var(--bm);text-transform:uppercase;letter-spacing:.06em}.admin-bundles td small{display:block;margin-top:4px;color:var(--bm);font-size:.76rem}.admin-bundles__state{font-size:.78rem;font-weight:700;color:var(--bm)}.admin-bundles__state.is-on{color:#256343}.admin-bundles__notice{margin:0 0 16px;padding:12px 15px;border-left:3px solid #981f26;background:#faf3f1}.admin-bundles__notice.is-success{border-color:#28754f;background:#f2f8f4}.admin-bundles__empty{padding:20px 0;color:var(--bm)}
@media(max-width:700px){.admin-bundles{padding:12px 10px 34px}.admin-bundles__head{align-items:flex-start;flex-direction:column}.admin-bundles__fields{grid-template-columns:1fr}.admin-bundles__wide{grid-column:auto}.admin-bundles__item-row{grid-template-columns:minmax(0,1fr) 85px}.admin-bundles__remove{grid-column:2}.admin-bundles__submit{align-items:stretch;flex-direction:column}.admin-bundles__submit button{width:100%}}
</style>
<?= $this->endSection(); ?>
