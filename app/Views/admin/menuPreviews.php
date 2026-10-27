<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<style>
.menu-preview-page { display:grid; gap:20px; color:#282521; }
.menu-preview-head { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; padding:8px 0 18px; border-bottom:1px solid #e8e3dc; }
.menu-preview-head h1 { margin:0 0 6px; font:500 clamp(24px,3vw,34px)/1.15 Georgia,serif; letter-spacing:-.025em; }
.menu-preview-head p { margin:0; color:#716a62; font-size:13px; line-height:1.6; }
.menu-preview-list { display:grid; }
.menu-preview-row { display:grid; grid-template-columns:minmax(150px,.8fr) minmax(250px,1.2fr) 132px; gap:20px; align-items:center; padding:16px 0; border-bottom:1px solid #eeeae4; }
.menu-preview-label strong { display:block; font-size:14px; font-weight:650; }
.menu-preview-label span { display:block; margin-top:3px; color:#81796f; font-size:11px; }
.menu-preview-select { width:100%; min-height:42px; padding:0 12px; border:1px solid #ddd7cf; border-radius:3px; background:#fff; color:#302b25; font:inherit; font-size:13px; }
.menu-preview-thumb { width:132px; height:88px; display:block; object-fit:cover; background:#f6f3ee; }
.menu-preview-submit { display:flex; justify-content:flex-end; padding-top:8px; }
.menu-preview-submit button { min-height:42px; padding:0 19px; border:0; border-radius:2px; background:#282521; color:#fff; font-size:12px; letter-spacing:.04em; }
@media(max-width:760px) {
  .menu-preview-head { align-items:flex-start; flex-direction:column; }
  .menu-preview-row { grid-template-columns:minmax(0,1fr) 92px; gap:9px 14px; }
  .menu-preview-label { grid-column:1 / -1; }
  .menu-preview-thumb { width:92px; height:68px; grid-column:2; grid-row:2; }
  .menu-preview-select { grid-column:1; grid-row:2; min-width:0; }
  .menu-preview-submit button { width:100%; }
}
</style>

<main class="menu-preview-page">
    <header class="menu-preview-head">
        <div>
            <h1>Foto Preview Menu</h1>
            <p>Pilih hanya foto preview yang ingin ditampilkan di menu. Baris tanpa pilihan tidak memuat gambar.</p>
        </div>
        <a class="btn btn-outline-secondary btn-sm" href="/" target="_blank" rel="noopener">Lihat website</a>
    </header>

    <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <form method="post" action="/admin/menu-previews">
        <?= csrf_field(); ?>
        <div class="menu-preview-list">
            <?php foreach (($previews ?? []) as $index => $pair): ?>
            <?php $selectedId = (string) ($pair['selected_product_id'] ?? ''); ?>
            <div class="menu-preview-row">
                <div class="menu-preview-label">
                    <strong><?= esc(ucfirst($pair['category'])); ?></strong>
                    <span><?= esc(ucfirst($pair['subcategory'])); ?></span>
                    <input type="hidden" name="previews[<?= $index ?>][category]" value="<?= esc($pair['category'], 'attr'); ?>">
                    <input type="hidden" name="previews[<?= $index ?>][subcategory]" value="<?= esc($pair['subcategory'], 'attr'); ?>">
                </div>
                <select class="menu-preview-select" name="previews[<?= $index ?>][product_id]" aria-label="Produk preview <?= esc($pair['category'] . ' ' . $pair['subcategory'], 'attr'); ?>" data-preview-select>
                    <option value="">Tanpa foto preview</option>
                    <?php foreach (($pair['products'] ?? []) as $product): ?>
                    <option value="<?= esc((string) $product['id'], 'attr'); ?>" <?= (string) $product['id'] === $selectedId ? 'selected' : ''; ?>><?= esc($product['nama']); ?> · #<?= esc((string) $product['id']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($selectedId !== ''): ?>
                <img class="menu-preview-thumb" src="<?= esc(base_url('product-cover/' . rawurlencode($selectedId) . '?size=300'), 'attr'); ?>" alt="Preview <?= esc($pair['category'] . ' ' . $pair['subcategory'], 'attr'); ?>" loading="lazy" decoding="async" data-preview-image>
                <?php else: ?>
                <span class="menu-preview-thumb" data-preview-empty>Tanpa foto</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($previews)): ?>
        <div class="menu-preview-submit"><button type="submit">Simpan pilihan preview</button></div>
        <?php else: ?>
        <p class="text-secondary">Belum ada produk aktif untuk ditampilkan.</p>
        <?php endif; ?>
    </form>
</main>

<script>
document.querySelectorAll('[data-preview-select]').forEach((select) => {
    select.addEventListener('change', () => {
        const row = select.closest('.menu-preview-row');
        if (!row) return;
        let image = row.querySelector('[data-preview-image]');
        const emptyState = row.querySelector('[data-preview-empty]');
        if (!select.value) {
            image?.remove();
            if (!emptyState) {
                const empty = document.createElement('span');
                empty.className = 'menu-preview-thumb';
                empty.dataset.previewEmpty = '';
                empty.textContent = 'Tanpa foto';
                row.append(empty);
            }
            return;
        }
        if (!image) {
            image = document.createElement('img');
            image.className = 'menu-preview-thumb';
            image.alt = 'Preview produk terpilih';
            image.loading = 'lazy';
            image.decoding = 'async';
            image.dataset.previewImage = '';
            row.append(image);
        }
        emptyState?.remove();
        image.src = `/product-cover/${encodeURIComponent(select.value)}?size=300`;
    });
});
</script>

<?= $this->endSection(); ?>
