<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php $isEnglish = service('request')->getLocale() === 'en'; ?>
<?php
$displayProductName = ilena_product_display_name($produk, $isEnglish ? 'en' : 'id');
$defaultVariant = $produk['varian'][0] ?? ['nama' => 'default', 'stok' => 0, 'urutan_gambar' => '1'];
$sanitizeProductHtml = static function (string $html): string {
    if (!class_exists(\DOMDocument::class)) return nl2br(esc(strip_tags($html)));
    $dom = new \DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?><div id="ilena-description-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $root = $dom->getElementById('ilena-description-root');
    if (!$root) return esc(strip_tags($html));
    $allowed = ['p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'h4', 'blockquote', 'a', 'div', 'span'];
    $render = static function ($node) use (&$render, $allowed): string {
        if ($node instanceof \DOMText) return esc($node->nodeValue ?? '');
        if (!$node instanceof \DOMElement) return '';
        $tag = strtolower($node->tagName);
        if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math'], true)) return '';
        $children = '';
        foreach ($node->childNodes as $child) $children .= $render($child);
        if (!in_array($tag, $allowed, true)) return $children;
        if ($tag === 'br') return '<br>';
        $attributes = '';
        if ($tag === 'a') {
            $href = trim($node->getAttribute('href'));
            if ($href !== '' && preg_match('/^(https?:\/\/|mailto:|tel:|\/|#)/i', $href)) {
                $attributes = ' href="' . esc($href, 'attr') . '" rel="noopener noreferrer"';
            }
        }
        return '<' . $tag . $attributes . '>' . $children . '</' . $tag . '>';
    };
    $clean = '';
    foreach ($root->childNodes as $child) $clean .= $render($child);
    return $clean;
};
$punyaGambarHoverDetail = is_file(FCPATH . 'uploads/product-images/hover/' . $produk['id'] . '.webp')
    || is_file(FCPATH . 'img/barang/hover/' . $produk['id'] . '.webp')
    || !empty($produk['gambar_hover']);
$imageSlotExists = function (string $slot) use ($produk): bool {
    $slot = trim($slot);
    if ($slot === '') return false;
    return is_file(FCPATH . 'img/barang/1000/' . $produk['id'] . '-' . $slot . '.webp')
        || is_file(FCPATH . 'img/barang/3000/' . $produk['id'] . '-' . $slot . '.webp')
        || is_file(FCPATH . 'uploads/product-images/1000/' . $produk['id'] . '-' . $slot . '.webp')
        || is_file(FCPATH . 'uploads/product-images/3000/' . $produk['id'] . '-' . $slot . '.webp');
};
$filterExistingSlots = function ($urutan) use ($imageSlotExists): array {
    $slots = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $urutan)) )));
    $existing = array_values(array_filter($slots, $imageSlotExists));
    return $existing ?: ($slots ?: ['1']);
};
$gambarAwalDetail = '1';
if (!empty($defaultVariant['urutan_gambar'])) {
    $urutanAwal = $filterExistingSlots($defaultVariant['urutan_gambar']);
    $gambarAwalDetail = $urutanAwal[0] ?? '1';
}
$detailSlotsByVariant = array_map(
    static fn(array $variant): array => $filterExistingSlots($variant['urutan_gambar'] ?? ''),
    $produk['varian'] ?? []
);
$sizeOptionMap = [];
foreach ($produkSemua ?? [] as $sizeIndex => $sizeProduct) {
    $sizeDescription = json_decode($sizeProduct['deskripsi'] ?? '{}', true) ?: [];
    $sizeLength = trim((string) ($sizeDescription['dimensi']['asli']['panjang'] ?? '-'));
    $sizeKey = $sizeLength === '-' ? 'missing-' . $sizeIndex : mb_strtolower($sizeLength);
    if (!isset($sizeOptionMap[$sizeKey]) || (int) $sizeIndex === (int) $indexNama) {
        $sizeOptionMap[$sizeKey] = ['index' => $sizeIndex, 'product' => $sizeProduct, 'length' => $sizeLength];
    }
}
$sizeOptions = array_values($sizeOptionMap);
$productImageStorage = strtolower((string) env('PRODUCT_IMAGE_STORAGE', 'local'));
$productImageCdn = $productImageStorage === 'r2' ? rtrim((string) env('R2_PUBLIC_URL', ''), '/') : '';
$assetVersion = function (string $relativePath) use ($produk): string {
    $path = FCPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
    $fileVersion = is_file($path) ? (int) filemtime($path) : 0;
    $dbVersion = !empty($produk['tgl_update']) ? (int) strtotime((string) $produk['tgl_update']) : time();
    return (string) ($fileVersion > 0 ? $fileVersion : $dbVersion);
};
$productSlotVersion = function (string $slot) use ($produk, $assetVersion): string {
    $mtimes = [];
    foreach ([
        'uploads/product-images/300/' . $produk['id'] . '-' . $slot . '.webp',
        'img/barang/300/' . $produk['id'] . '-' . $slot . '.webp',
        'uploads/product-images/1000/' . $produk['id'] . '-' . $slot . '.webp',
        'uploads/product-images/3000/' . $produk['id'] . '-' . $slot . '.webp',
        'img/barang/1000/' . $produk['id'] . '-' . $slot . '.webp',
        'img/barang/3000/' . $produk['id'] . '-' . $slot . '.webp',
    ] as $relativePath) {
        $path = FCPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
        if (is_file($path)) $mtimes[] = (int) filemtime($path);
    }

    return $mtimes === []
        ? $assetVersion('uploads/product-images/300/' . $produk['id'] . '-' . $slot . '.webp')
        : (string) max($mtimes);
};
$detailImageVersions = [];
foreach ($detailSlotsByVariant as $slots) {
    foreach ($slots as $slot) $detailImageVersions[(string) $slot] = $productSlotVersion((string) $slot);
}
$barangImgUrl = function (string $size, string $slot) use ($produk, $assetVersion, $productSlotVersion, $productImageCdn): string {
    $cdnSize = in_array($size, ['300', '1000', '3000'], true) ? $size : '1000';
    $version = $productSlotVersion($slot);
    $uploadedRelative = 'uploads/product-images/' . $cdnSize . '/' . $produk['id'] . '-' . $slot . '.webp';
    if (is_file(FCPATH . $uploadedRelative)) return base_url($uploadedRelative) . '?v=' . $version;
    $relative = 'img/barang/' . $size . '/' . $produk['id'] . '-' . $slot . '.webp';
    if (is_file(FCPATH . $relative)) {
        return base_url($relative) . '?v=' . $version;
    }
    if ($size === '300') {
        return base_url('product-cover/' . rawurlencode((string) $produk['id']))
            . '?slot=' . rawurlencode($slot) . '&size=300&v=' . $version;
    }
    if ($productImageCdn !== '') {
        return $productImageCdn . '/products/' . $cdnSize . '/' . rawurlencode((string) $produk['id']) . '-' . rawurlencode($slot) . '.webp?v=' . $version;
    }
    $route = $size === '3000' ? 'viewvar3000' : 'viewvar';
    return base_url($route . '/' . $produk['id'] . '/' . $slot) . '?v=' . $version;
};
$hoverImgUrl = function () use ($produk, $assetVersion, $productImageCdn): string {
    $uploadedRelative = 'uploads/product-images/hover/' . $produk['id'] . '.webp';
    if (is_file(FCPATH . $uploadedRelative)) return base_url($uploadedRelative) . '?v=' . $assetVersion($uploadedRelative);
    $relative = 'img/barang/hover/' . $produk['id'] . '.webp';
    if (is_file(FCPATH . $relative)) {
        return base_url($relative) . '?v=' . $assetVersion($relative);
    }
    if ($productImageCdn !== '') return $productImageCdn . '/products/hover/' . rawurlencode((string) $produk['id']) . '.webp?v=' . $assetVersion($uploadedRelative);
    return base_url('viewpichover/' . $produk['id']) . '?v=' . $assetVersion($relative);
};
$detailVersion = !empty($produk['tgl_update']) ? (int) strtotime((string) $produk['tgl_update']) : time();
$productCoverUrl = function (array $product) use ($productImageCdn): string {
    $id = $product['id'] ?? '';
    $varian = json_decode($product['varian'] ?? '[]', true) ?: [];
    $slot = '1';
    if (!empty($varian[0]['urutan_gambar'])) {
        $slots = array_values(array_filter(array_map('trim', explode(',', (string) $varian[0]['urutan_gambar']))));
        $slot = $slots[0] ?? '1';
    }
    $candidates = [
        'img/barang/300/' . $id . '.webp',
        'uploads/product-images/300/' . $id . '.webp',
        'img/barang/1000/' . $id . '-' . $slot . '.webp',
        'img/barang/3000/' . $id . '-' . $slot . '.webp',
    ];
    $sourceRelative = $candidates[0];
    foreach ($candidates as $candidate) {
        if (is_file(FCPATH . $candidate)) { $sourceRelative = $candidate; break; }
    }
    $sourceAbsolute = FCPATH . $sourceRelative;
    $sourceExists = is_file($sourceAbsolute);
    $fileVersion = $sourceExists ? (int) filemtime($sourceAbsolute) : 0;
    $dbVersion = !empty($product['tgl_update']) ? (int) strtotime((string) $product['tgl_update']) : 0;
    $isLocalThumbnail = $sourceExists && in_array($sourceRelative, [
        'img/barang/300/' . $id . '.webp', 'uploads/product-images/300/' . $id . '.webp',
    ], true);
    $fullSizeMtime = 0;
    foreach ([
        'uploads/product-images/1000/' . $id . '-' . $slot . '.webp',
        'uploads/product-images/3000/' . $id . '-' . $slot . '.webp',
        'img/barang/1000/' . $id . '-' . $slot . '.webp',
        'img/barang/3000/' . $id . '-' . $slot . '.webp',
    ] as $fullSizeCandidate) {
        if (is_file(FCPATH . $fullSizeCandidate)) $fullSizeMtime = max($fullSizeMtime, (int) filemtime(FCPATH . $fullSizeCandidate));
    }
    $fileVersion = max($fileVersion, $fullSizeMtime);
    if (!$sourceExists && $productImageCdn !== '') {
        return $productImageCdn . '/products/300/' . rawurlencode((string) $id) . '.webp?v=' . max($fileVersion, $dbVersion);
    }
    if (strtolower((string) env('PRODUCT_IMAGE_STORAGE', 'local')) !== 'r2' && $isLocalThumbnail && (!$fullSizeMtime || (int) filemtime($sourceAbsolute) >= $fullSizeMtime)) {
        return base_url($sourceRelative) . '?v=' . max($fileVersion, $dbVersion);
    }
    return base_url('product-cover/' . $id) . '?slot=' . urlencode($slot) . '&size=300&v=' . max($fileVersion, $dbVersion);
};
?>
<style>
/* Aksen halus saja: tetap mempertahankan struktur UI lama */
.product-detail-soft .breadcrumb {
    font-size: 13px;
}
.product-detail-soft .breadcrumb a {
    color: #6b7280;
    text-decoration: none;
}
.product-detail-soft .breadcrumb a:hover {
    color: #b31217;
}
.product-detail-soft .baris-ke-kolom-reverse.w-100 {
    gap: 28px;
    align-items: flex-start;
}
.product-detail-soft .teks-besar {
    letter-spacing: -0.03em;
    line-height: 1.05;
}
.product-detail-soft .harga {
    letter-spacing: -0.02em;
}
.product-detail-soft .img-detail-prev {
    border-radius: 18px;
}
.product-detail-soft img.img-detail-prev {
    box-shadow: 0 14px 34px rgba(15, 23, 42, .08);
    background: #f8fafc;
}
.product-detail-soft .container-img-detail-select {
    gap: 10px;
    padding-bottom: 4px;
}
.product-detail-soft .img-detail-select {
    border-radius: 12px;
    padding: 3px;
    border: 1px solid #e5e7eb;
    background: #fff;
    transition: border-color .15s ease, transform .15s ease, box-shadow .15s ease;
}
.product-detail-soft .img-detail-select:hover {
    transform: translateY(-1px);
    border-color: #cbd5e1;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
}
.product-detail-soft .container-img-detail-select input:checked + .img-detail-select {
    border-color: #b31217;
    box-shadow: 0 0 0 3px rgba(179, 18, 23, .10);
}
.product-detail-soft .img-detail-select img {
    border-radius: 9px;
}
.product-detail-soft .container-varian {
    gap: 9px;
    flex-wrap: wrap;
}
.product-detail-soft .container-varian label {
    transition: transform .15s ease, box-shadow .15s ease;
}
.product-detail-soft .container-varian label:hover {
    transform: translateY(-1px);
}
.product-detail-soft .container-varian input:checked + label span {
    box-shadow: 0 0 0 3px #fff, 0 0 0 5px rgba(179, 18, 23, .35);
}
.detail-mini-label {
    margin: 0 0 8px;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    letter-spacing: .02em;
}
.product-detail-soft .btn-default-abu {
    border-radius: 999px;
}
.product-detail-soft #btn-keranjang .btn-default-merah,
.product-detail-soft .number-control {
    min-height: 44px;
}
.product-detail-soft #container-market {
    border: 1px solid #eeeeee;
    border-radius: 14px;
    padding: 12px 14px;
    background: #fafafa;
}
.product-detail-soft .accordion {
    border-top: 1px solid #f1f1f1;
}
@media (max-width: 768px) {
    .product-detail-soft .baris-ke-kolom-reverse.w-100 {
        gap: 16px;
    }
    .product-detail-soft img.img-detail-prev {
        border-radius: 16px;
    }
}
</style>
<div class="container d-flex flex-column align-items-center product-detail-soft">
    <div class="konten w-100 ilena-product-detail">
        <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb" class="show-block-ke-hide">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= esc(lang('Store.productDetails')); ?></a></li>
                <li class="breadcrumb-item"><a
                        href="<?= esc(ilena_store_path('/product?koleksi=' . rawurlencode((string) $produk['kategori'])), 'attr'); ?>"><?= esc(ilena_catalog_type_label((string) $produk['kategori'])); ?></a>
                </li>
                <li class="breadcrumb-item"><a
                        href="<?= esc(ilena_store_path('/product?jenis=' . rawurlencode((string) $produk['subkategori'])), 'attr'); ?>"><?= esc(ilena_catalog_type_label((string) $produk['subkategori'])); ?></a>
                </li>
            </ol>
        </nav>
        <div class="baris-ke-kolom-reverse w-100 ilena-product-layout">
            <div class="limapuluh-ke-seratus ilena-product-info">
                <div class="ilena-product-heading">
                    <p class="ilena-product-eyebrow"><?= esc(ilena_catalog_type_label((string) $produk['kategori'])); ?> <span>/</span> ILENA FURNITURE</p>
                    <div class="ilena-product-title-row">
                        <h1 class="teks-besar"><?= esc(str_replace('Tv', 'TV', ucwords($displayProductName))) ?></h1>
                        <a href="<?= esc(ilena_store_path('/product?koleksi=' . rawurlencode((string) $produk['kategori'])), 'attr'); ?>" class="ilena-product-collection-link" aria-label="<?= esc(lang('Store.viewCollection') . ' ' . $produk['kategori'], 'attr'); ?>"><?= esc(lang('Store.viewCollection')); ?> <span aria-hidden="true">↗</span></a>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-3 ilena-product-price">
                    <p class="harga">Rp
                        <?= number_format($produk['harga'] * (100 - $produk['diskon']) / 100, 0, ',', '.'); ?></p>
                    <?php if ($produk['diskon'] > 0) { ?>
                    <p class="harga-diskon">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                    <?php } ?>
                </div>
                <div class="ilena-product-options gap-2 hide-ke-show-flex">
                    <section class="ilena-option-group" aria-labelledby="mobile-variant-label">
                    <div class="ilena-option-heading"><p class="detail-mini-label w-100" id="mobile-variant-label"><?= esc(lang('Store.colorVariant')); ?></p></div>
                    <div class="container-varian mb-3 d-flex" role="radiogroup" aria-labelledby="mobile-variant-label">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                        <input id="varian<?= $ind_v ?>-mobile"
                            <?= $ind_v === 0 ? 'checked' : '' ?>
                            value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>-<?= $ind_v ?>"
                            data-slot="<?= esc($v['urutan_gambar'], 'attr') ?>"
                            data-name="<?= esc($v['nama'], 'attr') ?>"
                            data-index="<?= $ind_v ?>"
                            data-stok="<?= (int)($v['stok'] ?? 0) ?>"
                            aria-label="<?= esc(sprintf(lang('Store.colorForProduct'), ilena_variant_display_name((string) $v['nama'], null, (string) ($v['nama_en'] ?? ''))), 'attr') ?>"
                            type="radio" name="varian-mobile">
                        <label for="varian<?= $ind_v ?>-mobile" title="<?= esc(ilena_variant_display_name((string) $v['nama'], null, (string) ($v['nama_en'] ?? '')), 'attr') ?>"><span aria-hidden="true"
                                style="background-color: <?= esc($v['kode'], 'attr') ?>"></span></label>
                        <?php } ?>
                    </div></section>
                    <section class="ilena-option-group" aria-labelledby="mobile-size-label">
                    <div class="ilena-option-heading"><p class="detail-mini-label w-100" id="mobile-size-label"><?= esc(lang('Store.chooseSize')); ?></p><span class="ilena-option-caption"><?= esc(lang('Store.length')); ?></span></div>
                    <div class="ilena-size-options">
                        <?php foreach ($sizeOptions as $sizeOption) { $ind_ps = $sizeOption['index']; $ps = $sizeOption['product']; ?>
                        <a href="<?= esc(ilena_store_path('/product/' . rawurlencode(strtolower(str_replace(' ', '-', (string) $ps['nama']))) . '/' . (int) $ind_ps), 'attr'); ?>"
                            class="btn-default-abu ilena-size-option <?= $ind_ps == $indexNama ? 'border border-dark is-active' : '' ?>" <?= $ind_ps == $indexNama ? 'aria-current="page"' : '' ?>><?= esc($sizeOption['length']) ?><span>mm</span></a>
                        <?php } ?>
                    </div></section>
                </div>
                <div class="ilena-product-description"><?= $sanitizeProductHtml((string) (($isEnglish && trim((string) ($produk['deskripsi']['deskripsi_en'] ?? '')) !== '') ? $produk['deskripsi']['deskripsi_en'] : ($produk['deskripsi']['deskripsi'] ?? ''))); ?></div>
                <div class="ilena-product-options gap-2 show-flex-ke-hide">
                    <section class="ilena-option-group" aria-labelledby="desktop-variant-label">
                    <div class="ilena-option-heading"><p class="detail-mini-label" id="desktop-variant-label"><?= esc(lang('Store.colorVariant')); ?></p></div>
                    <div class="container-varian mb-3 d-flex" role="radiogroup" aria-labelledby="desktop-variant-label">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                        <input id="varian<?= $ind_v ?>"
                            <?= $ind_v === 0 ? 'checked' : '' ?>
                            value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>-<?= $ind_v ?>"
                            data-slot="<?= esc($v['urutan_gambar'], 'attr') ?>"
                            data-name="<?= esc($v['nama'], 'attr') ?>"
                            data-index="<?= $ind_v ?>"
                            data-stok="<?= (int)($v['stok'] ?? 0) ?>"
                            aria-label="<?= esc(sprintf(lang('Store.colorForProduct'), ilena_variant_display_name((string) $v['nama'], null, (string) ($v['nama_en'] ?? ''))), 'attr') ?>"
                            type="radio"
                            name="varian">
                        <label for="varian<?= $ind_v ?>" title="<?= esc(ilena_variant_display_name((string) $v['nama'], null, (string) ($v['nama_en'] ?? '')), 'attr') ?>"><span aria-hidden="true"
                                style="background-color: <?= esc($v['kode'], 'attr') ?>"></span></label>
                        <?php } ?>
                    </div></section>
                    <section class="ilena-option-group" aria-labelledby="desktop-size-label">
                    <div class="ilena-option-heading"><p class="detail-mini-label" id="desktop-size-label"><?= esc(lang('Store.chooseSize')); ?></p><span class="ilena-option-caption"><?= esc(lang('Store.length')); ?></span></div>
                    <div class="ilena-size-options">
                        <?php foreach ($sizeOptions as $sizeOption) { $ind_ps = $sizeOption['index']; $ps = $sizeOption['product']; ?>
                        <a href="<?= esc(ilena_store_path('/product/' . rawurlencode(strtolower(str_replace(' ', '-', (string) $ps['nama']))) . '/' . (int) $ind_ps), 'attr'); ?>"
                            class="btn-default-abu ilena-size-option <?= $ind_ps == $indexNama ? 'border border-dark is-active' : '' ?>" <?= $ind_ps == $indexNama ? 'aria-current="page"' : '' ?>><?= esc($sizeOption['length']) ?><span>mm</span></a>
                        <?php } ?>
                    </div></section>
                </div>
                <?php $stockQuantity = max(0, (int) ($defaultVariant['stok'] ?? 0)); ?>
                <div class="ilena-product-stock <?= $stockQuantity > 0 ? 'is-available' : 'is-unavailable'; ?>" id="ilena-product-stock" role="status" aria-live="polite"
                    data-stock-available="<?= esc(lang('Store.statusInStock'), 'attr'); ?>"
                    data-stock-unavailable="<?= esc(lang('Store.unavailable'), 'attr'); ?>"
                    data-stock-quantity="<?= esc(lang('Store.stockUnitsAvailable'), 'attr'); ?>">
                    <span class="ilena-product-stock__icon" aria-hidden="true"><?= $stockQuantity > 0 ? '✓' : '–'; ?></span>
                    <span class="ilena-product-stock__label" data-stock-label><?= esc($stockQuantity > 0 ? lang('Store.statusInStock') : lang('Store.unavailable')); ?></span>
                    <span class="ilena-product-stock__quantity" data-stock-count <?= $stockQuantity < 1 ? 'hidden' : ''; ?>><?= esc(sprintf(lang('Store.stockUnitsAvailable'), number_format($stockQuantity, 0, ',', '.'))); ?></span>
                </div>
                <?php if (session()->get('role') == '1') { ?>
                <div class="d-flex gap-2">
                    <a class="btn-default d-flex justify-content-center align-items-center"
                        href="/admin/editproduct/<?= $produk['id']; ?>">
                        <i class="material-icons">edit</i>
                        <p class="m-0"><?= esc(lang('Store.productEdit')); ?></p>
                    </a>
                    <!-- <a class="btn-default-merah d-flex justify-content-center align-items-center"
                        href="/admin/deleteproduct/<?= $produk['id']; ?>">
                        <i class="material-icons">delete</i>
                        <p class="m-0"><?= esc(lang('Store.removeProduct')); ?></p>
                    </a> -->
                <form action="/admin/deleteproduct/<?= $produk['id']; ?>" method="post">
                    <?= csrf_field(); ?>
                        <button class="btn-default-merah d-flex justify-content-center align-items-center"
                            type="submit"><i class="material-icons">delete</i>
                            <p class="m-0"><?= esc(lang('Store.removeProduct')); ?></p>
                        </button>
                    </form>
                </div>
                <?php } else { ?>
                <div class="d-flex gap-2 align-items-stretch ilena-product-purchase">
                    <div class="number-control">
                        <button type="button" class="number-left" onclick="kurangJumlah()" aria-label="<?= esc(lang('Store.decreaseQuantity'), 'attr'); ?>"></button>
                        <input type="number" name="jumlah" class="number-quantity" value="1" min="1" aria-label="<?= esc(lang('Store.purchaseQuantity'), 'attr'); ?>">
                        <button type="button" class="number-right" onclick="tambahJumlah()" aria-label="<?= esc(lang('Store.increaseQuantity'), 'attr'); ?>"></button>
                    </div>
                    <form id="btn-keranjang" method="post"
                        action="<?= $defaultVariant['stok'] > 0 ? ilena_store_path('/addcart/' . $produk['id'] . '/' . rawurlencode($defaultVariant['nama']) . '/1') : ''; ?>">
                        <?= csrf_field(); ?>
                        <button class="btn-default-merah <?= $defaultVariant['stok'] > 0 ? '' : 'disabled'; ?>"
                            <?= $defaultVariant['stok'] > 0 ? '' : 'disabled'; ?>
                            type="submit"><?= $defaultVariant['stok'] > 0 ? esc(lang('Store.cart')) : esc(lang('Store.soldOut')); ?></button>
                    </form>

                </div>
                <?php } ?>
                <p id="info-habis" class="ilena-preorder-note mt-2 <?= $defaultVariant['stok'] <= 0 ? '' : 'd-none'; ?>" role="status" aria-live="polite">
                    <?= esc(lang('Store.preorderLead')); ?>
                    <a href="https://wa.me/+628112938158" class="btn-teks-aja" style="display: inline;"><?= esc(lang('Store.customerService')); ?></a>.
                </p>
                <?php if ($produk['tokped'] || $produk['shopee'] || $produk['tiktok']) { ?>
                <div class="mt-4 <?= $defaultVariant['stok'] <= 0 ? 'd-none' : ''; ?>" id="container-market">
                    <p class="mb-2"><?= esc(lang('Store.alsoAvailableOn')); ?></p>
                    <div>
                        <?php if ($produk['tokped']) { ?>
                        <a href="<?= esc((string) $produk['tokped'], 'attr'); ?>" rel="noopener noreferrer" title="Tokopedia" target="_blank"><img
                                src="/img/logo/tokopedia.png" class="marketplace"></a>
                        <?php } ?>
                        <?php if ($produk['shopee']) { ?>
                        <a href="<?= esc((string) $produk['shopee'], 'attr'); ?>" rel="noopener noreferrer" title="Shopee" target="_blank"><img
                                src="/img/logo/shopee.png" class="marketplace"></a>
                        <?php } ?>
                        <?php if ($produk['tiktok']) { ?>
                        <a href="<?= esc((string) $produk['tiktok'], 'attr'); ?>" rel="noopener noreferrer" title="Tiktok" target="_blank"><img
                                src="/img/logo/tiktokshop.svg" class="marketplace"></a>
                        <?php } ?>
                    </div>
                </div>
                <?php }
                if (in_array($produk['id'], $wishlist)) { ?>
                <form action="<?= esc(ilena_store_path('/delwishlist/' . $produk['id']), 'attr'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn-teks-aja my-3"><i class="material-icons">favorite</i><?= esc(lang('Store.removeFromWishlist')); ?></button>
                </form>
                <?php } else { ?>
                <form action="<?= esc(ilena_store_path('/addwishlist/' . $produk['id']), 'attr'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn-teks-aja my-3"><i
                            class="material-icons">favorite_border</i><?= esc(lang('Store.saveToWishlist')); ?></button>
                </form>
                <?php } ?>
                <hr>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                                <?= esc(lang('Store.specificationsHeading')); ?>
                            </button>
                        </h2>
                        <div id="flush-collapse2" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p class="mb-0">
                                    <?= esc((string) (($isEnglish && trim((string) ($produk['deskripsi']['perawatan_en'] ?? '')) !== '') ? $produk['deskripsi']['perawatan_en'] : ($produk['deskripsi']['perawatan'] ?? lang('Store.maintenanceInfoMissing')))) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse1" aria-expanded="false" aria-controls="flush-collapse1">
                                <?= esc(lang('Store.dimensionsHeading')); ?>
                            </button>
                        </h2>
                        <div id="flush-collapse1" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p class="mb-0 fw-bold"><?= esc(lang('Store.productDimensions')); ?></p>
                                <?php
                                    $diagramDimensions = $produk['deskripsi']['dimensi']['asli'] ?? [];
                                    $diagramLength = trim((string) ($diagramDimensions['panjang'] ?? '-'));
                                    $diagramWidth = trim((string) ($diagramDimensions['lebar'] ?? '-'));
                                    $diagramHeight = trim((string) ($diagramDimensions['tinggi'] ?? '-'));
                                ?>
                                <div class="ilena-dimension-diagram" role="img" aria-label="<?= esc(lang('Store.dimensionDiagramLabel'), 'attr'); ?>">
                                    <svg viewBox="0 0 260 150" aria-hidden="true" focusable="false">
                                        <path d="M48 54 133 25l78 29-84 31-79-31Zm0 0v61l79 29V85m0 59V85l84-31v61l-84 29" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        <path d="M48 126 126 148m8-64 77-29m8 4v61" fill="none" stroke="#8d2e2b" stroke-width="1.5" stroke-dasharray="3 3"/>
                                        <text x="54" y="143"><?= esc(lang('Store.lengthLabel')); ?> <?= esc($diagramLength); ?> mm</text>
                                        <text x="147" y="47"><?= esc(lang('Store.widthLabel')); ?> <?= esc($diagramWidth); ?> mm</text>
                                        <text x="216" y="92" transform="rotate(90 216 92)"><?= esc(lang('Store.heightLabel')); ?> <?= esc($diagramHeight); ?> mm</text>
                                    </svg>
                                </div>
                                <ul>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.lengthLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['asli']['panjang'] ?? '-')) ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.widthLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['asli']['lebar'] ?? '-')) ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.heightLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['asli']['tinggi'] ?? '-')) ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.weightLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['asli']['berat'] ?? '-')) ?> kg
                                        </p>
                                    </li>
                                </ul>
                                <p class="mb-0 mt-1 fw-bold"><?= esc(lang('Store.packageDimensions')); ?></p>
                                <ul>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.lengthLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['paket']['panjang'] ?? '-')) ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.widthLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['paket']['lebar'] ?? '-')) ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.heightLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['paket']['tinggi'] ?? '-')) ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            <?= esc(lang('Store.weightLabel')); ?> : <?= esc((string) ($produk['deskripsi']['dimensi']['paket']['berat'] ?? '-')) ?> kg
                                        </p>
                                    </li>
                                </ul>
                                <?php
                                    $fitDimensions = $produk['deskripsi']['dimensi']['asli'] ?? [];
                                    $fitLength = is_numeric($fitDimensions['panjang'] ?? null) ? (float) $fitDimensions['panjang'] : 0;
                                    $fitWidth = is_numeric($fitDimensions['lebar'] ?? null) ? (float) $fitDimensions['lebar'] : 0;
                                    $fitHeight = is_numeric($fitDimensions['tinggi'] ?? null) ? (float) $fitDimensions['tinggi'] : 0;
                                    $fitReady = $fitLength > 0 && $fitWidth > 0 && $fitHeight > 0;
                                ?>
                                <section class="ilena-fit-guide" data-fit-guide data-fit-dimensions="<?= esc(json_encode([$fitLength, $fitWidth, $fitHeight]), 'attr'); ?>" aria-labelledby="ilena-fit-guide-title">
                                    <h3 id="ilena-fit-guide-title"><?= esc(lang('Store.fitGuideTitle')); ?></h3>
                                    <p><?= esc($fitReady ? lang('Store.fitGuideIntro') : lang('Store.fitDimensionsMissing')); ?></p>
                                    <?php if ($fitReady): ?>
                                    <div class="ilena-fit-guide__fields">
                                        <label><?= esc(lang('Store.doorWidth')); ?><input type="number" inputmode="decimal" min="1" step="1" data-fit-width required></label>
                                        <label><?= esc(lang('Store.doorHeight')); ?><input type="number" inputmode="decimal" min="1" step="1" data-fit-height required></label>
                                    </div>
                                    <button type="button" class="ilena-fit-guide__button" data-fit-check><?= esc(lang('Store.checkFit')); ?></button>
                                    <p class="ilena-fit-guide__result" data-fit-result aria-live="polite"></p>
                                    <span class="ilena-fit-guide__messages" hidden data-pass="<?= esc(lang('Store.fitResultPass'), 'attr'); ?>" data-fail="<?= esc(lang('Store.fitResultFail'), 'attr'); ?>" data-invalid="<?= esc(lang('Store.fitResultInvalid'), 'attr'); ?>"></span>
                                    <?php endif; ?>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="limapuluh-ke-seratus ilena-product-gallery">
                <div class="ilena-product-gallery__stage">
                    <figure class="img-detail-prev ilena-product-zoom d-none" aria-hidden="true"
                        data-zoom-src="<?= esc(base_url('viewvar3000/' . rawurlencode((string) $produk['id']) . '/' . rawurlencode($gambarAwalDetail) . '?v=' . ($detailImageVersions[$gambarAwalDetail] ?? $detailVersion)), 'attr') ?>"
                        style="background-size: cover;">
                    </figure>
                    <img class="img-detail-prev"
                        <?= $defaultVariant['stok'] <= 0 ? 'style="filter: grayscale(90%)"' : ''; ?>
                        src="<?= esc($barangImgUrl('1000', $gambarAwalDetail), 'attr') ?>"
                        data-image-fallback="<?= esc(base_url('product-cover/' . rawurlencode((string) $produk['id'])) . '?slot=' . rawurlencode($gambarAwalDetail) . '&v=' . ($detailImageVersions[$gambarAwalDetail] ?? $detailVersion), 'attr') ?>"
                        onerror="if(this.dataset.imageFallback){this.onerror=null;this.src=this.dataset.imageFallback;}"
onmousemove="zoom(event)" onmouseleave="mouseoff(event)" alt="<?= esc($displayProductName, 'attr') ?>" width="1000" height="1000" decoding="async" fetchpriority="high">
                </div>
                <div class="mb-3 mt-3" style="overflow: auto">
                    <div class="container-img-detail-select"
                        <?= $defaultVariant['stok'] <= 0 ? 'style="filter: grayscale(90%)"' : ''; ?>>
                        <?php foreach ($filterExistingSlots($defaultVariant['urutan_gambar']) as $indx => $p_v) { ?>
                        <input <?= $indx == 0 ? 'checked' : '' ?> id="gambar<?= $indx ?>" type="radio" name="gambar"
                            aria-label="<?= esc(sprintf(lang('Store.productPhotoNumber'), $indx + 1), 'attr'); ?>" value="<?= $p_v ?>">
                        <label class="img-detail-select" for="gambar<?= $indx ?>" title="<?= esc(sprintf(lang('Store.productPhotoNumber'), $indx + 1), 'attr'); ?>"><img
                                src="<?= esc($barangImgUrl('300', $p_v), 'attr') ?>"
                                data-image-fallback="<?= esc(base_url('product-cover/' . rawurlencode((string) $produk['id'])) . '?slot=' . rawurlencode((string) $p_v) . '&v=' . ($detailImageVersions[(string) $p_v] ?? $detailVersion), 'attr') ?>"
                                onerror="if(this.dataset.imageFallback){this.onerror=null;this.src=this.dataset.imageFallback;}"
alt="<?= esc($displayProductName, 'attr') ?>" width="300" height="300" loading="lazy" decoding="async"></label>
                        <?php } ?>
                        <?php if ($punyaGambarHoverDetail) { ?>
                        <input id="gambar-hover" type="radio" name="gambar" aria-label="<?= esc(lang('Store.surfaceDetail'), 'attr'); ?>" value="hover">
                        <label class="img-detail-select" for="gambar-hover" title="<?= esc(lang('Store.surfaceDetail'), 'attr'); ?>"><img
                                src="<?= esc($hoverImgUrl(), 'attr') ?>"
                                data-image-fallback="<?= esc(base_url('viewpichover/' . rawurlencode((string) $produk['id'])) . '?v=' . $detailVersion, 'attr') ?>"
                                onerror="if(this.dataset.imageFallback){this.onerror=null;this.src=this.dataset.imageFallback;}"
alt="<?= esc($displayProductName, 'attr') ?>" width="300" height="300" loading="lazy" decoding="async"></label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="ilena-product-aftercare">
                <nav class="ilena-product-help" aria-label="<?= esc(lang('Store.productHelpTitle'), 'attr'); ?>">
                    <h3><?= esc(lang('Store.productHelpTitle')); ?></h3>
                    <a href="<?= esc(ilena_store_path('/faq#garansi-produk'), 'attr'); ?>"><?= esc(lang('Store.productWarrantyLink')); ?><span aria-hidden="true">↗</span></a>
                    <a href="<?= esc(ilena_store_path('/faq#perawatan'), 'attr'); ?>"><?= esc(lang('Store.productCareLink')); ?><span aria-hidden="true">↗</span></a>
                    <a href="<?= esc(ilena_store_path('/faq#pengiriman'), 'attr'); ?>"><?= esc(lang('Store.productDeliveryLink')); ?><span aria-hidden="true">↗</span></a>
                    <a href="<?= esc(ilena_store_path('/faq#perakitan'), 'attr'); ?>"><?= esc(lang('Store.productAssemblyLink')); ?><span aria-hidden="true">↗</span></a>
                </nav>
                <p class="ilena-product-policy-note"><?= esc(lang('Store.productDamageNotice')); ?></p>
                <?php
                    $feedbackEnglish = $isEnglish;
                    $feedbackRows = is_array($publicProductFeedback ?? null) ? $publicProductFeedback : [];
                    $initialVariantName = (string) ($defaultVariant['nama'] ?? '');
                    $feedbackMember = session()->get('isLogin') && (string) session()->get('active') === '1'
                        && in_array((string) session()->get('role'), ['0', '4'], true)
                        && filter_var((string) session()->get('email'), FILTER_VALIDATE_EMAIL);
                ?>
                <section class="ilena-commerce-panel" aria-labelledby="ilena-product-feedback-title">
                    <?php if ($notice = session()->getFlashdata('commerce_notice')): ?><p class="ilena-commerce-notice" role="status"><?= esc($notice); ?></p><?php endif; ?>
                    <div class="ilena-commerce-panel__heading">
                        <div><span><?= $feedbackEnglish ? 'CUSTOMER VOICE' : 'SUARA PELANGGAN'; ?></span><h2 id="ilena-product-feedback-title"><?= $feedbackEnglish ? 'Reviews & questions' : 'Ulasan & pertanyaan'; ?></h2></div>
                        <a href="https://ilenafurniture.net/" class="ilena-commerce-panel__designer" target="_blank" rel="noopener noreferrer"><?= $feedbackEnglish ? 'Custom interior consultation' : 'Konsultasi interior custom'; ?> ↗</a>
                    </div>
                    <?php if ($feedbackRows === []): ?><p class="ilena-commerce-panel__muted"><?= $feedbackEnglish ? 'Be the first to share a helpful product note.' : 'Jadilah yang pertama berbagi pengalaman tentang produk ini.'; ?></p><?php endif; ?>
                    <?php foreach ($feedbackRows as $feedback): ?>
                    <article class="ilena-commerce-feedback">
                        <div class="ilena-commerce-feedback__meta"><strong><?= esc((string) ($feedback['author_name'] ?? 'Pelanggan')); ?></strong>
                            <?php if ((int) ($feedback['verified_purchase'] ?? 0) === 1): ?><span><?= $feedbackEnglish ? 'Verified buyer' : 'Pembeli terverifikasi'; ?></span><?php endif; ?>
                            <?php if (($feedback['feedback_type'] ?? '') === 'review' && !empty($feedback['rating'])): ?><span aria-label="<?= esc((string) $feedback['rating']); ?> out of 5 stars">★ <?= (int) $feedback['rating']; ?>/5</span><?php endif; ?>
                        </div><p><?= esc((string) ($feedback['body'] ?? '')); ?></p>
                    </article>
                    <?php endforeach; ?>
                    <?php if (!$feedbackMember): ?>
                    <p class="ilena-commerce-panel__muted"><?= $feedbackEnglish ? 'Sign in to ask a product question. Ratings are reserved for members who completed a purchase.' : 'Masuk untuk mengajukan pertanyaan produk. Penilaian hanya tersedia bagi member yang telah menyelesaikan pembelian.'; ?> <a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= $feedbackEnglish ? 'Sign in to continue' : 'Masuk untuk melanjutkan'; ?></a></p>
                    <?php else: ?>
                    <details class="ilena-commerce-panel__details"><summary><?= $feedbackEnglish ? 'Ask a question or review this product' : 'Ajukan pertanyaan atau ulas produk'; ?></summary>
                        <form action="<?= esc(ilena_store_path('/commerce/feedback'), 'attr'); ?>" method="post" class="ilena-commerce-form">
                            <?= csrf_field(); ?><input type="hidden" name="product_id" value="<?= esc((string) $produk['id'], 'attr'); ?>">
                            <label><?= $feedbackEnglish ? 'What would you like to do?' : 'Apa yang ingin Anda lakukan?'; ?><select name="feedback_type" data-feedback-type><option value="question"><?= $feedbackEnglish ? 'Ask a product question' : 'Ajukan pertanyaan produk'; ?></option><option value="review"><?= $feedbackEnglish ? 'Write a product review' : 'Tulis ulasan produk'; ?></option></select></label>
                            <div class="ilena-commerce-form__review-fields" data-feedback-review-fields hidden>
                                <label><?= $feedbackEnglish ? 'Order number' : 'Nomor pesanan'; ?><input name="order_id" maxlength="191" data-feedback-order disabled required></label>
                                <label><?= $feedbackEnglish ? 'Your rating' : 'Penilaian Anda'; ?><select name="rating" data-feedback-rating disabled required><option value="5">5 — <?= $feedbackEnglish ? 'Excellent' : 'Sangat baik'; ?></option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option></select></label>
                                <small><?= $feedbackEnglish ? 'A rating is accepted only when this account owns a completed order containing this product.' : 'Penilaian hanya diterima jika akun ini memiliki pesanan selesai yang mencakup produk ini.'; ?></small>
                            </div>
                            <label><?= $feedbackEnglish ? 'Your note' : 'Pesan Anda'; ?><textarea name="body" minlength="8" maxlength="2000" rows="3" required></textarea></label>
                            <label class="ilena-commerce-consent"><input type="checkbox" name="publication_consent" value="1" required> <?= $feedbackEnglish ? 'I agree that my name and submission may be published after moderation.' : 'Saya setuju nama dan kiriman saya dapat ditampilkan setelah ditinjau.'; ?></label>
                            <button type="submit" class="ilena-commerce-panel__submit"><?= $feedbackEnglish ? 'Send for review' : 'Kirim untuk ditinjau'; ?></button>
                            <small><?= $feedbackEnglish ? 'Submissions are moderated and only approved content is published.' : 'Kiriman ditinjau terlebih dahulu; hanya konten yang disetujui akan ditampilkan.'; ?></small>
                        </form>
                    </details>
                    <?php endif; ?>
                    <details class="ilena-commerce-panel__details" data-back-stock-alert <?= (int) ($defaultVariant['stok'] ?? 0) > 0 ? 'hidden' : ''; ?>><summary><?= $feedbackEnglish ? 'Notify me when available' : 'Beri tahu saat tersedia'; ?></summary>
                        <?php if (!session()->get('isLogin') || (string) session()->get('active') !== '1'): ?><p class="ilena-commerce-panel__muted"><?= $feedbackEnglish ? 'Sign in to save a stock alert.' : 'Masuk ke akun untuk menyimpan pemberitahuan stok.'; ?> <a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= $feedbackEnglish ? 'Sign in' : 'Masuk'; ?></a></p><?php else: ?>
                        <form action="<?= esc(ilena_store_path('/commerce/alert'), 'attr'); ?>" method="post" class="ilena-commerce-form">
                            <?= csrf_field(); ?><input type="hidden" name="product_id" value="<?= esc((string) $produk['id'], 'attr'); ?>"><input type="hidden" name="alert_type" value="back_in_stock"><input type="hidden" name="variant_name" value="<?= esc($initialVariantName, 'attr'); ?>" data-commerce-variant>
                            <label><?= $feedbackEnglish ? 'Account email' : 'Email akun'; ?><input type="email" value="<?= esc((string) (session()->get('email') ?? ''), 'attr'); ?>" readonly required></label>
                            <label class="ilena-commerce-consent"><input type="checkbox" name="consent" value="1" required> <?= $feedbackEnglish ? 'I agree to receive one availability update for this product.' : 'Saya setuju menerima satu pemberitahuan ketersediaan produk ini.'; ?></label>
                            <button type="submit" class="ilena-commerce-panel__submit"><?= $feedbackEnglish ? 'Save alert' : 'Simpan pemberitahuan'; ?></button>
                        </form>
                        <?php endif; ?>
                    </details>
                    <details class="ilena-commerce-panel__details"><summary><?= $feedbackEnglish ? 'Notify me if the price drops' : 'Beri tahu jika harga turun'; ?></summary>
                        <?php if (!session()->get('isLogin') || (string) session()->get('active') !== '1'): ?><p class="ilena-commerce-panel__muted"><?= $feedbackEnglish ? 'Sign in to save a price alert.' : 'Masuk ke akun untuk menyimpan pemberitahuan harga.'; ?> <a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= $feedbackEnglish ? 'Sign in' : 'Masuk'; ?></a></p><?php else: ?>
                        <form action="<?= esc(ilena_store_path('/commerce/alert'), 'attr'); ?>" method="post" class="ilena-commerce-form">
                            <?= csrf_field(); ?><input type="hidden" name="product_id" value="<?= esc((string) $produk['id'], 'attr'); ?>"><input type="hidden" name="alert_type" value="price_drop"><input type="hidden" name="variant_name" value="<?= esc($initialVariantName, 'attr'); ?>" data-commerce-variant>
                            <label><?= $feedbackEnglish ? 'Account email' : 'Email akun'; ?><input type="email" value="<?= esc((string) (session()->get('email') ?? ''), 'attr'); ?>" readonly required></label>
                            <label class="ilena-commerce-consent"><input type="checkbox" name="consent" value="1" required> <?= $feedbackEnglish ? 'I agree to receive one price-drop email for this product.' : 'Saya setuju menerima satu email saat harga produk ini turun.'; ?></label>
                            <button type="submit" class="ilena-commerce-panel__submit"><?= $feedbackEnglish ? 'Save price alert' : 'Simpan pemberitahuan harga'; ?></button>
                        </form>
                        <?php endif; ?>
                    </details>
                </section>
        </div>
        <div class="ilena-mobile-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?= esc(lang('Store.productGalleryLabel'), 'attr'); ?>">
            <button class="ilena-mobile-lightbox__close" type="button" aria-label="<?= esc(lang('Store.closeGallery'), 'attr'); ?>">&times;</button>
            <button class="ilena-mobile-lightbox__nav ilena-mobile-lightbox__prev" type="button" aria-label="<?= esc(lang('Store.previousPhoto'), 'attr'); ?>">‹</button>
            <div class="ilena-mobile-lightbox__viewport"><img class="ilena-mobile-lightbox__image" alt=""></div>
            <button class="ilena-mobile-lightbox__nav ilena-mobile-lightbox__next" type="button" aria-label="<?= esc(lang('Store.nextPhoto'), 'attr'); ?>">›</button>
            <div class="ilena-mobile-lightbox__tools">
                <button class="ilena-mobile-lightbox__zoom-out" type="button" aria-label="<?= esc(lang('Store.zoomOutPhoto'), 'attr'); ?>">−</button>
                <span class="ilena-mobile-lightbox__counter" aria-live="polite"></span>
                <button class="ilena-mobile-lightbox__zoom-in" type="button" aria-label="<?= esc(lang('Store.zoomInPhoto'), 'attr'); ?>">+</button>
            </div>
            <p class="ilena-mobile-lightbox__hint"><?= esc(lang('Store.zoomHint')); ?></p>
        </div>
        <?php if (count($produkSejenis) > 0) { ?>
        <section class="ilena-product-related" aria-labelledby="related-products-title">
        <div class="ilena-product-related-heading"><span><?= esc(lang('Store.relatedPicks')); ?></span><h2 id="related-products-title"><?= esc(lang('Store.youMayAlsoLike')); ?></h2></div>
        <div class="container-card1 ilena-catalog-grid ilena-product-related-grid">
            <?php foreach ($produkSejenis as $ind_p => $p) { ?>
            <?php
                $relatedVersion = !empty($p['tgl_update']) ? (int) strtotime((string) $p['tgl_update']) : time();
                $relatedVariants = json_decode($p['varian'] ?? '[]', true);
                $relatedVariants = is_array($relatedVariants) ? $relatedVariants : [];
                $relatedVariantsByName = [];
                foreach ($relatedVariants as $relatedIndex => $relatedVariant) {
                    if (!is_array($relatedVariant)) continue;
                    $relatedName = trim((string) ($relatedVariant['nama'] ?? ''));
                    $relatedKey = $relatedName !== '' ? mb_strtolower($relatedName) : 'unnamed-' . $relatedIndex;
                    if (!isset($relatedVariantsByName[$relatedKey])) {
                        $relatedVariant['nama'] = $relatedName !== '' ? $relatedName : 'Varian ' . ($relatedIndex + 1);
                        $relatedVariant['stok'] = max(0, (int) ($relatedVariant['stok'] ?? 0));
                        $relatedVariant['urutan_gambar'] = (string) ($relatedVariant['urutan_gambar'] ?? '1');
                        $relatedVariant['kode'] = (string) ($relatedVariant['kode'] ?? '#ddd');
                        $relatedVariantsByName[$relatedKey] = $relatedVariant;
                        continue;
                    }
                    $previousSlots = array_filter(array_map('trim', explode(',', (string) ($relatedVariantsByName[$relatedKey]['urutan_gambar'] ?? ''))));
                    $duplicateSlots = array_filter(array_map('trim', explode(',', (string) ($relatedVariant['urutan_gambar'] ?? ''))));
                    $relatedVariantsByName[$relatedKey]['urutan_gambar'] = implode(',', array_values(array_unique(array_merge($previousSlots, $duplicateSlots))));
                    $relatedVariantsByName[$relatedKey]['stok'] = max((int) ($relatedVariantsByName[$relatedKey]['stok'] ?? 0), (int) ($relatedVariant['stok'] ?? 0));
                }
                $relatedVariantData = array_values($relatedVariantsByName);
                $cardVarianAwal = $relatedVariantData[0]['nama'] ?? 'default';
                $cardVarianAwalStok = (int) ($relatedVariantData[0]['stok'] ?? 0);
                $relatedSlots = array_values(array_filter(array_map('trim', explode(',', (string) ($relatedVariantData[0]['urutan_gambar'] ?? '1')))));
                $relatedCardSlot = $relatedSlots[0] ?? '1';
                $relatedProductUrl = ilena_store_path('/product/' . rawurlencode(str_replace(' ', '-', (string) $p['nama'])));
                $relatedImageFallback = base_url('product-cover/' . rawurlencode((string) $p['id'])) . '?slot=' . rawurlencode($relatedCardSlot) . '&size=300&v=' . $relatedVersion;
                $relatedCdnImage = $productImageCdn !== '' ? $productImageCdn . '/products/300/' . rawurlencode((string) $p['id']) . '-' . rawurlencode($relatedCardSlot) . '.webp?v=' . $relatedVersion : '';
                $relatedLocalSlot300Relative = 'uploads/product-images/300/' . $p['id'] . '-' . $relatedCardSlot . '.webp';
                $relatedLegacySlot300Relative = 'img/barang/300/' . $p['id'] . '-' . $relatedCardSlot . '.webp';
                $relatedLocalImage = is_file(FCPATH . $relatedLocalSlot300Relative)
                    ? base_url($relatedLocalSlot300Relative) . '?v=' . $relatedVersion
                    : (is_file(FCPATH . $relatedLegacySlot300Relative) ? base_url($relatedLegacySlot300Relative) . '?v=' . $relatedVersion : '');
                $relatedLocal1000Relative = 'uploads/product-images/1000/' . $p['id'] . '-' . $relatedCardSlot . '.webp';
                $relatedLegacy1000Relative = 'img/barang/1000/' . $p['id'] . '-' . $relatedCardSlot . '.webp';
                $relatedLocal1000Image = is_file(FCPATH . $relatedLocal1000Relative)
                    ? base_url($relatedLocal1000Relative) . '?v=' . $relatedVersion
                    : (is_file(FCPATH . $relatedLegacy1000Relative) ? base_url($relatedLegacy1000Relative) . '?v=' . $relatedVersion : '');
            ?>
            <div class="card1" data-product-card>
                <div style="position: relative;">
                    <span class="card1-content-img-kiri"
                        <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                    <div class="d-flex flex-column gap-2 card1-content-img-kanan">
                        <?php if (session()->get('role') == '1') { ?>
                        <a class="card1-btn-img" href="/admin/editproduct/<?= esc((string) $p['id'], 'attr') ?>" title="<?= esc(lang('Store.editProductTitle'), 'attr') ?>" aria-label="<?= esc(sprintf(lang('Store.productEdit') . ' %s', $p['nama']), 'attr') ?>"><i class="material-icons">edit</i></a>
                        <?php }
                                if (in_array($p['id'], $wishlist)) { ?>
                        <form action="<?= esc(ilena_store_path('/delwishlist/' . rawurlencode((string) $p['id'])), 'attr'); ?>" method="post">
                            <?= csrf_field(); ?>
                            <button type="submit" class="card1-btn-img" aria-label="<?= esc(sprintf(lang('Store.removeProductFromWishlist'), $p['nama']), 'attr') ?>"><i class="material-icons">favorite</i></button>
                        </form>
                        <?php } else { ?>
                        <form action="<?= esc(ilena_store_path('/addwishlist/' . rawurlencode((string) $p['id'])), 'attr'); ?>" method="post">
                            <?= csrf_field(); ?>
                            <button type="submit" class="card1-btn-img" aria-label="<?= esc(sprintf(lang('Store.saveProductToWishlist'), $p['nama']), 'attr') ?>"><i
                                    class="material-icons">favorite_border</i></button>
                        </form>
                        <?php } ?>
                        <form method="post" id="card<?= $ind_p ?>"
                            action="<?= $cardVarianAwalStok > 0 ? ilena_store_path('/addcart/' . rawurlencode((string) $p['id']) . '/' . rawurlencode($cardVarianAwal) . '/1') : '' ?>"><?= csrf_field(); ?><button type="submit" class="card1-btn-img" aria-label="<?= esc(sprintf(lang('Store.addProductToCart'), $p['nama']), 'attr') ?>" title="<?= esc(lang('Store.addToCartTitle'), 'attr') ?>" <?= $cardVarianAwalStok > 0 ? '' : 'disabled' ?>><i class="material-icons">shopping_bag</i>
                            </button>
                        </form>
                    </div>
                    <a href="<?= esc($relatedProductUrl, 'attr') ?>" class="gambar">
                        <img class="img-pic" id="img<?= $ind_p ?>"
                            src="<?= esc($relatedLocalImage !== '' ? $relatedLocalImage : ($relatedCdnImage !== '' ? $relatedCdnImage : $relatedImageFallback), 'attr') ?>"
                            <?= $relatedLocalImage === '' && $relatedCdnImage !== '' ? 'data-image-fallback="' . esc($relatedImageFallback, 'attr') . '"' : '' ?>
                            <?= $relatedLocalImage !== '' && $relatedLocal1000Image !== '' ? 'srcset="' . esc($relatedLocalImage . ' 300w, ' . $relatedLocal1000Image . ' 1000w', 'attr') . '"' : '' ?>
                            sizes="(max-width: 600px) 46vw, (max-width: 1100px) 30vw, 300px"
                            width="300" height="300" alt="<?= esc($p['nama'], 'attr') ?>" loading="lazy" decoding="async"
                            onload="this.classList.add('is-loaded');this.closest('.gambar')?.classList.add('is-image-loaded')"
                            onerror="if(this.dataset.imageFallback&&!this.dataset.fallbackTried){this.dataset.fallbackTried='1';this.removeAttribute('srcset');this.src=this.dataset.imageFallback;return;}this.classList.add('is-loaded');this.closest('.gambar')?.classList.add('is-image-loaded')">
                        <?php if (is_file(FCPATH . 'uploads/product-images/hover/' . $p['id'] . '.webp') || is_file(FCPATH . 'img/barang/hover/' . $p['id'] . '.webp') || !empty($p['gambar_hover'])) { ?>
                        <img class="img-pic-hover lazy-hover-product"
                            data-src="<?= esc(is_file(FCPATH . 'uploads/product-images/hover/' . $p['id'] . '.webp') ? (base_url('uploads/product-images/hover/' . rawurlencode((string) $p['id']) . '.webp') . '?v=' . $relatedVersion) : ($productImageCdn !== '' ? $productImageCdn . '/products/hover/' . rawurlencode((string) $p['id']) . '.webp?v=' . $relatedVersion : (is_file(FCPATH . 'img/barang/hover/' . $p['id'] . '.webp') ? base_url('img/barang/hover/' . $p['id'] . '.webp') : base_url('viewpichover/' . $p['id']) . '?v=' . $relatedVersion)), 'attr') ?>"
                            data-image-fallback="<?= esc(base_url('viewpichover/' . rawurlencode((string) $p['id'])) . '?v=' . $relatedVersion, 'attr') ?>" onerror="if(this.dataset.imageFallback){this.onerror=null;this.src=this.dataset.imageFallback;}" alt="" aria-hidden="true" loading="lazy" decoding="async">
                        <?php } ?>
                    </a>
                </div>
                <div class="container-varian mb-1 d-flex" role="radiogroup" aria-label="<?= esc(sprintf(lang('Store.colorForProduct'), $p['nama']), 'attr') ?>">
                    <?php foreach ($relatedVariantData as $ind_v => $v) { ?>
                    <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>"
                        <?= $ind_v === 0 ? 'checked' : '' ?>
                        value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>"
                        data-slot="<?= esc($v['urutan_gambar'], 'attr') ?>"
                        data-name="<?= esc($v['nama'], 'attr') ?>"
                        data-stok="<?= (int)($v['stok'] ?? 0) ?>"
                        type="radio" name="varian<?= $ind_p ?>" aria-label="<?= esc(sprintf(lang('Store.colorForProduct'), $v['nama']), 'attr') ?>">
                    <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>" title="<?= esc($v['nama'], 'attr') ?>"><span
                            style="background-color: <?= esc((string) $v['kode'], 'attr') ?>"></span></label>
                    <?php } ?>
                    <script>
                    const btnKeranjang<?= $ind_p ?>Elm = document.getElementById("card<?= $ind_p ?>");
                    const varian<?= $ind_p ?>Elm = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');
                    varian<?= $ind_p ?>Elm.forEach(elm => {
                        elm.addEventListener('change', (e) => {
                            const img<?= $ind_p ?>Elm = document.getElementById("img<?= $ind_p ?>");
                            const slot = (e.target.dataset.slot || e.target.value.split("-")[0] || "1").split(",")[0];
                            const namaVarian = e.target.dataset.name || e.target.value.split("-").slice(1).join("-") || "default";
                            const stok = Number(e.target.dataset.stok || 0);
                            // Keep the selected variant's slot and thumbnail source in sync.
                            img<?= $ind_p ?>Elm.removeAttribute('srcset');
                            img<?= $ind_p ?>Elm.dataset.imageFallback = "<?= base_url('product-cover/' . rawurlencode((string) $p['id'])) ?>?slot=" + encodeURIComponent(slot) + '&size=300&v=<?= $relatedVersion ?>';
                            img<?= $ind_p ?>Elm.dataset.fallbackTried = '';
                            img<?= $ind_p ?>Elm.classList.remove('is-loaded');
                            img<?= $ind_p ?>Elm.closest('.gambar')?.classList.remove('is-image-loaded');
                            img<?= $ind_p ?>Elm.src = <?= json_encode($productImageCdn !== '' ? $productImageCdn . '/products/300/' . rawurlencode((string) $p['id']) . '-' : base_url('product-cover/' . rawurlencode((string) $p['id'])) . '?slot=') ?> + encodeURIComponent(slot) + <?= json_encode($productImageCdn !== '' ? '.webp?v=' . $relatedVersion : '&size=300&v=' . $relatedVersion) ?>;

                            btnKeranjang<?= $ind_p ?>Elm.action = stok > 0 ? <?= json_encode(ilena_store_path('/addcart/' . $p['id'] . '/'), JSON_UNESCAPED_SLASHES) ?> + encodeURIComponent(namaVarian) + "/1" : "";
                            const quickBtn = btnKeranjang<?= $ind_p ?>Elm.querySelector('button');
                            if (quickBtn) quickBtn.disabled = stok <= 0;
                        })
                    });
                    </script>
                </div>
                <a href="<?= esc($relatedProductUrl, 'attr') ?>" class="text-dark related-card-info">
                    <p class="related-card-category"><?= esc(ilena_catalog_type_label((string) $p['kategori'])); ?></p>
                    <h5 class="related-card-name"><?= esc(str_replace('Tv', 'TV', ucwords(ilena_product_display_name($p)))); ?></h5>
                    <div class="d-flex gap-2 related-card-prices">
                        <p class="harga">Rp
                            <?= number_format($p['harga'] * (100 - $p['diskon']) / 100, 0, ',', '.'); ?></p>
                        <?php if ($p['diskon'] > 0) { ?>
                        <p class="harga-diskon">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                        <?php } ?>
                    </div>
                </a>
            </div>
            <?php } ?>
        </div>
        </section>
        <?php } ?>
    </div>
</div>
<script>
const btnKeranjangElm = document.getElementById('btn-keranjang');
const radioVarianElm = document.querySelectorAll('input[name="varian"], input[name="varian-mobile"]');
const varian = <?= json_encode($produk['varian'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE) ?>;
const teksInfoHabisElm = document.getElementById('info-habis');
const containerMarketElm = document.getElementById('container-market');
let varianSelected = <?= json_encode($defaultVariant['nama'] ?? 'default', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
let jumlahSelected = "1";
let isStokHabis = <?= $defaultVariant['stok'] == '0' ? 'true' : 'false' ?>;
const stokByVarianName = <?= json_encode(array_column($produk['varian'], 'stok', 'nama'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const stockState = document.getElementById('ilena-product-stock');
function updateStockState(stock) {
    if (!stockState) return;
    const available = Math.max(0, Number(stock) || 0);
    stockState.classList.toggle('is-available', available > 0);
    stockState.classList.toggle('is-unavailable', available <= 0);
    stockState.querySelector('[data-stock-label]').textContent = available > 0 ? stockState.dataset.stockAvailable : stockState.dataset.stockUnavailable;
    stockState.querySelector('[data-stock-count]').textContent = stockState.dataset.stockQuantity.replace('%s', new Intl.NumberFormat(<?= json_encode($isEnglish ? 'en' : 'id'); ?>).format(available));
    stockState.querySelector('[data-stock-count]').hidden = available < 1;
    stockState.querySelector('.ilena-product-stock__icon').textContent = available > 0 ? '✓' : '–';
}
const detailVersion = "<?= $detailVersion ?>";
const detailImageVersions = <?= json_encode($detailImageVersions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
function detailSlotVersion(slot) { return String(detailImageVersions[String(slot)] || detailVersion); }
const detailSlotsByVariant = <?= json_encode($detailSlotsByVariant, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const detailBase1000 = "/viewvar/" + encodeURIComponent(<?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>) + "/";
const detailBase3000 = "/viewvar3000/" + encodeURIComponent(<?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>) + "/";
const detailR2CdnBase = <?= json_encode($productImageCdn, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const detailHoverUrl = <?= json_encode($hoverImgUrl(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const detailProductId = <?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const detailStatic1000 = <?= json_encode(base_url('uploads/product-images/1000/'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const detailStatic3000 = <?= json_encode(base_url('uploads/product-images/3000/'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const detailLocalSlots1000 = <?= json_encode(array_values(array_filter(array_unique(array_merge(...array_map(static fn($v) => array_map('trim', explode(',', (string)($v['urutan_gambar'] ?? ''))), $produk['varian']))), static fn($slot) => $slot !== '' && is_file(FCPATH . 'uploads/product-images/1000/' . $produk['id'] . '-' . $slot . '.webp')))) ?>;
const detailLegacySlots1000 = <?= json_encode(array_values(array_filter(array_unique(array_merge(...array_map(static fn($v) => array_map('trim', explode(',', (string)($v['urutan_gambar'] ?? ''))), $produk['varian']))), static fn($slot) => $slot !== '' && is_file(FCPATH . 'img/barang/1000/' . $produk['id'] . '-' . $slot . '.webp')))) ?>;
const detailLocalSlots3000 = <?= json_encode(array_values(array_filter(array_unique(array_merge(...array_map(static fn($v) => array_map('trim', explode(',', (string)($v['urutan_gambar'] ?? ''))), $produk['varian']))), static fn($slot) => $slot !== '' && is_file(FCPATH . 'uploads/product-images/3000/' . $produk['id'] . '-' . $slot . '.webp')))) ?>;
const detailLegacySlots3000 = <?= json_encode(array_values(array_filter(array_unique(array_merge(...array_map(static fn($v) => array_map('trim', explode(',', (string)($v['urutan_gambar'] ?? ''))), $produk['varian']))), static fn($slot) => $slot !== '' && is_file(FCPATH . 'img/barang/3000/' . $produk['id'] . '-' . $slot . '.webp')))) ?>;
function encodedCartUrl(productId, varianName, qty) {
    return <?= json_encode(ilena_store_path('/addcart/'), JSON_UNESCAPED_SLASHES) ?> + productId + "/" + encodeURIComponent(varianName || "default") + "/" + Math.max(1, Number(qty || 1));
}
function detailImageUrl(slot) {
    const version = detailSlotVersion(slot);
    if (detailLocalSlots1000.includes(String(slot))) return detailStatic1000 + detailProductId + "-" + encodeURIComponent(slot) + ".webp?v=" + version;
    if (detailLegacySlots1000.includes(String(slot))) return "/img/barang/1000/" + detailProductId + "-" + encodeURIComponent(slot) + ".webp?v=" + version;
    if (detailR2CdnBase) return detailR2CdnBase + "/products/1000/" + encodeURIComponent(detailProductId) + "-" + encodeURIComponent(slot) + ".webp?v=" + version;
    return detailBase1000 + slot + "?v=" + version;
}
function detailZoomUrl(slot) {
    const version = detailSlotVersion(String(slot).split(',')[0]);
    if (detailLocalSlots3000.includes(String(slot))) return detailStatic3000 + detailProductId + "-" + encodeURIComponent(slot) + ".webp?v=" + version;
    if (detailLegacySlots3000.includes(String(slot))) return "/img/barang/3000/" + detailProductId + "-" + encodeURIComponent(slot) + ".webp?v=" + version;
    // Resolve a legacy high-resolution file only when zoom is actually invoked;
    // the endpoint falls back to the 1000px image for products without one.
    return detailBase3000 + slot + "?v=" + version;
}
const galleryThumbs = document.querySelector('.container-img-detail-select');
function showSelectedProductPhoto(selectedPhoto) {
    if (!selectedPhoto) return;
    const zoomLayer = document.querySelector('.ilena-product-gallery__stage > .ilena-product-zoom');
    const mainPhoto = document.querySelector('.ilena-product-gallery__stage > img.img-detail-prev');
    if (!zoomLayer || !mainPhoto) return;
    const isHoverPhoto = selectedPhoto.value === 'hover';
    const slot = String(selectedPhoto.value || selectedPhoto.dataset.slot || '').split(',')[0].trim();
    if (!isHoverPhoto && !slot) return;
    const photoKey = isHoverPhoto ? 'hover' : slot;
    if (mainPhoto.dataset.gallerySelection === photoKey) return;
    mainPhoto.dataset.gallerySelection = photoKey;
    const requestedPhotoUrl = isHoverPhoto ? detailHoverUrl : detailImageUrl(slot);
    const routeFallbackUrl = isHoverPhoto ? '' : detailBase1000 + encodeURIComponent(slot) + '?v=' + detailSlotVersion(slot);
    zoomLayer.classList.add('d-none');
    zoomLayer.setAttribute('aria-hidden', 'true');
    zoomLayer.style.backgroundImage = '';
    zoomLayer.dataset.loadedSource = '';
    mainPhoto.onerror = () => {
        if (mainPhoto.dataset.galleryFallbackTried !== photoKey && routeFallbackUrl && requestedPhotoUrl !== routeFallbackUrl) {
            mainPhoto.dataset.galleryFallbackTried = photoKey;
            mainPhoto.src = routeFallbackUrl;
            return;
        }
        mainPhoto.onerror = null;
        mainPhoto.src = <?= json_encode(base_url('img/ilena-image-placeholder.png'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    };
    mainPhoto.onload = () => { delete mainPhoto.dataset.galleryFallbackTried; };
    if (isHoverPhoto) {
        window.isHoverDetailSelected = true;
        zoomLayer.dataset.zoomSrc = '';
        mainPhoto.src = requestedPhotoUrl;
        return;
    }
    window.isHoverDetailSelected = false;
    zoomLayer.dataset.zoomSrc = detailZoomUrl(slot);
    mainPhoto.src = requestedPhotoUrl;
}
galleryThumbs?.addEventListener('change', (event) => {
    const selectedPhoto = event.target instanceof Element ? event.target.closest('input[name="gambar"]') : null;
    showSelectedProductPhoto(selectedPhoto);
});
galleryThumbs?.addEventListener('click', (event) => {
    const label = event.target instanceof Element ? event.target.closest('label.img-detail-select[for]') : null;
    if (!label) return;
    const selectedPhoto = document.getElementById(label.htmlFor);
    if (selectedPhoto instanceof HTMLInputElement) showSelectedProductPhoto(selectedPhoto);
});

// Mobile-only full-screen product gallery. High-resolution images are requested only on demand.
const mobileLightbox = document.querySelector('.ilena-mobile-lightbox');
const lightboxImage = mobileLightbox?.querySelector('.ilena-mobile-lightbox__image');
const lightboxViewport = mobileLightbox?.querySelector('.ilena-mobile-lightbox__viewport');
const lightboxCounter = mobileLightbox?.querySelector('.ilena-mobile-lightbox__counter');
let lightboxIndex = 0;
let lightboxScale = 1;
let lightboxX = 0;
let lightboxY = 0;
let lightboxPointers = new Map();
let lightboxGesture = null;
let lightboxPreviousFocus = null;

function lightboxPhotos() {
    return Array.from(galleryThumbs?.querySelectorAll('input[name="gambar"]') || []).map((input) => {
        const thumb = galleryThumbs.querySelector(`label[for="${CSS.escape(input.id)}"] img`);
        const hover = input.value === 'hover';
        const slot = String(input.value || '').split(',')[0].trim();
        return {
            input,
            src: hover ? detailHoverUrl : detailZoomUrl(slot),
            fallback: thumb?.currentSrc || thumb?.src || document.querySelector('.ilena-product-gallery__stage > img.img-detail-prev')?.currentSrc || '',
            alt: hover ? 'Detail permukaan produk' : `${document.querySelector('.ilena-product-info .teks-besar')?.textContent.trim() || 'Foto produk'} — foto ${input.getAttribute('aria-label')?.replace(/\D/g, '') || ''}`
        };
    }).filter((photo) => photo.src);
}

function resetLightboxZoom() {
    lightboxScale = 1;
    lightboxX = 0;
    lightboxY = 0;
    if (lightboxImage) lightboxImage.style.transform = '';
}

function renderLightboxPhoto(index) {
    const photos = lightboxPhotos();
    if (!photos.length || !lightboxImage || !mobileLightbox) return;
    lightboxIndex = (index + photos.length) % photos.length;
    const photo = photos[lightboxIndex];
    photo.input.checked = true;
    showSelectedProductPhoto(photo.input);
    resetLightboxZoom();
    lightboxImage.onerror = () => {
        lightboxImage.onerror = null;
        if (photo.fallback && lightboxImage.src !== photo.fallback) lightboxImage.src = photo.fallback;
    };
    lightboxImage.src = photo.src;
    lightboxImage.alt = photo.alt;
    if (lightboxCounter) lightboxCounter.textContent = `${lightboxIndex + 1} / ${photos.length}`;
    mobileLightbox.classList.toggle('has-multiple', photos.length > 1);
}

function openMobileLightbox() {
    if (!mobileLightbox || window.innerWidth > 700 || window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    const photos = lightboxPhotos();
    if (!photos.length) return;
    lightboxPreviousFocus = document.activeElement;
    document.body.classList.add('ilena-lightbox-open');
    mobileLightbox.classList.add('is-open');
    mobileLightbox.setAttribute('aria-hidden', 'false');
    renderLightboxPhoto(Math.max(0, photos.findIndex((photo) => photo.input.checked)));
    mobileLightbox.querySelector('.ilena-mobile-lightbox__close')?.focus({ preventScroll: true });
}

function closeMobileLightbox() {
    if (!mobileLightbox) return;
    mobileLightbox.classList.remove('is-open');
    mobileLightbox.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('ilena-lightbox-open');
    lightboxPointers.clear();
    if (lightboxPreviousFocus instanceof HTMLElement) lightboxPreviousFocus.focus({ preventScroll: true });
}

document.querySelector('.ilena-product-gallery__stage > img.img-detail-prev')?.addEventListener('click', openMobileLightbox);
mobileLightbox?.querySelector('.ilena-mobile-lightbox__close')?.addEventListener('click', closeMobileLightbox);
mobileLightbox?.querySelector('.ilena-mobile-lightbox__prev')?.addEventListener('click', () => renderLightboxPhoto(lightboxIndex - 1));
mobileLightbox?.querySelector('.ilena-mobile-lightbox__next')?.addEventListener('click', () => renderLightboxPhoto(lightboxIndex + 1));
mobileLightbox?.addEventListener('click', (event) => { if (event.target === mobileLightbox) closeMobileLightbox(); });
document.addEventListener('keydown', (event) => {
    if (!mobileLightbox?.classList.contains('is-open')) return;
    if (event.key === 'Escape') closeMobileLightbox();
    if (event.key === 'ArrowLeft') renderLightboxPhoto(lightboxIndex - 1);
    if (event.key === 'ArrowRight') renderLightboxPhoto(lightboxIndex + 1);
});

function applyLightboxZoom(scale) {
    lightboxScale = Math.max(1, Math.min(4, scale));
    if (lightboxScale === 1) { lightboxX = 0; lightboxY = 0; }
    if (lightboxImage) lightboxImage.style.transform = `translate3d(${lightboxX}px, ${lightboxY}px, 0) scale(${lightboxScale})`;
    mobileLightbox?.classList.toggle('is-zoomed', lightboxScale > 1);
}

mobileLightbox?.querySelector('.ilena-mobile-lightbox__zoom-in')?.addEventListener('click', () => applyLightboxZoom(lightboxScale + .5));
mobileLightbox?.querySelector('.ilena-mobile-lightbox__zoom-out')?.addEventListener('click', () => applyLightboxZoom(lightboxScale - .5));
lightboxViewport?.addEventListener('pointerdown', (event) => {
    lightboxViewport.setPointerCapture(event.pointerId);
    lightboxPointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
    if (lightboxPointers.size === 2) {
        const points = Array.from(lightboxPointers.values());
        lightboxGesture = { distance: Math.hypot(points[0].x - points[1].x, points[0].y - points[1].y), scale: lightboxScale };
    } else {
        lightboxGesture = { x: event.clientX, y: event.clientY, startX: lightboxX, startY: lightboxY, moved: false };
    }
});
lightboxViewport?.addEventListener('pointermove', (event) => {
    if (!lightboxPointers.has(event.pointerId) || !lightboxGesture) return;
    lightboxPointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
    if (lightboxPointers.size >= 2) {
        const points = Array.from(lightboxPointers.values());
        const distance = Math.hypot(points[0].x - points[1].x, points[0].y - points[1].y);
        if (lightboxGesture.distance) applyLightboxZoom(lightboxGesture.scale * distance / lightboxGesture.distance);
        return;
    }
    const dx = event.clientX - lightboxGesture.x;
    const dy = event.clientY - lightboxGesture.y;
    if (Math.abs(dx) + Math.abs(dy) > 8) lightboxGesture.moved = true;
    if (lightboxScale > 1) {
        lightboxX = lightboxGesture.startX + dx;
        lightboxY = lightboxGesture.startY + dy;
        applyLightboxZoom(lightboxScale);
    }
});
const endLightboxGesture = (event) => {
    if (!lightboxPointers.has(event.pointerId)) return;
    const gesture = lightboxGesture;
    const dx = gesture && 'x' in gesture ? event.clientX - gesture.x : 0;
    lightboxPointers.delete(event.pointerId);
    if (lightboxPointers.size < 2) lightboxGesture = null;
    if (lightboxScale <= 1 && Math.abs(dx) > 55) renderLightboxPhoto(lightboxIndex + (dx < 0 ? 1 : -1));
};
lightboxViewport?.addEventListener('pointerup', endLightboxGesture);
lightboxViewport?.addEventListener('pointercancel', endLightboxGesture);
lightboxImage?.addEventListener('dblclick', () => applyLightboxZoom(lightboxScale > 1 ? 1 : 2));

radioVarianElm.forEach(elm => {
    elm.addEventListener('change', (e) => {
        radioVarianElm.forEach(peer => {
            if (peer !== e.target && peer.dataset.index === e.target.dataset.index) peer.checked = true;
        });
        const variantIndex = Number(e.target.dataset.index || e.target.value.split("-")[2] || 0);
        const slotValue = (detailSlotsByVariant[variantIndex] || [e.target.dataset.slot || e.target.value.split("-")[0]]).join(",");
        const varianName = e.target.dataset.name || e.target.value.split("-").slice(1, -1).join("-") || e.target.value.split("-")[1] || '';
        const varianFullSelected = varian[variantIndex] || { stok: e.target.dataset.stok || 0, urutan_gambar: slotValue, nama: varianName };
        updateStockState(varianFullSelected.stok);
        const imgElm = document.querySelector("figure.img-detail-prev");
        const imgFixElm = document.querySelector("img.img-detail-prev");
        window.isHoverDetailSelected = false;
        imgElm.dataset.zoomSrc = detailZoomUrl(slotValue.split(",")[0]);
        imgElm.style.backgroundImage = "";
        imgElm.dataset.loadedSource = "";
        imgFixElm.src = detailImageUrl(slotValue.split(",")[0]);

        const containerImgDetailElm = galleryThumbs;
        containerImgDetailElm.replaceChildren();
        const urutanGambar = slotValue.split(",").filter(Boolean);
        urutanGambar.forEach((urutan, ind_x) => {
            const input = document.createElement('input');
            input.id = 'gambar' + ind_x;
            input.type = 'radio';
            input.name = 'gambar';
            input.value = urutan;
            input.dataset.slot = urutan;
            input.setAttribute('aria-label', 'Foto produk ' + (ind_x + 1));
            input.checked = ind_x === 0;
            const label = document.createElement('label');
            label.className = 'img-detail-select';
            label.htmlFor = input.id;
            label.title = 'Foto produk ' + (ind_x + 1);
            const thumbnail = document.createElement('img');
            thumbnail.src = <?= json_encode(base_url('product-cover/' . rawurlencode((string) $produk['id'])), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> + '?slot=' + encodeURIComponent(urutan) + '&size=300&v=' + detailSlotVersion(urutan);
            thumbnail.alt = '';
            thumbnail.loading = 'lazy';
            thumbnail.decoding = 'async';
            label.append(thumbnail);
            containerImgDetailElm.append(input, label);
        });
        <?php if ($punyaGambarHoverDetail) { ?>
        const hoverInput = document.createElement('input');
        hoverInput.id = 'gambar-hover';
        hoverInput.type = 'radio';
        hoverInput.name = 'gambar';
        hoverInput.value = 'hover';
        hoverInput.setAttribute('aria-label', 'Lihat detail permukaan');
        const hoverLabel = document.createElement('label');
        hoverLabel.className = 'img-detail-select';
        hoverLabel.htmlFor = 'gambar-hover';
        hoverLabel.title = 'Lihat detail permukaan';
        const hoverThumbnail = document.createElement('img');
        hoverThumbnail.src = detailHoverUrl;
        hoverThumbnail.alt = '';
        hoverThumbnail.loading = 'lazy';
        hoverThumbnail.decoding = 'async';
        hoverLabel.append(hoverThumbnail);
        containerImgDetailElm.append(hoverInput, hoverLabel);
        <?php } ?>

        if (Number(varianFullSelected.stok) <= 0) {
            imgFixElm.style = 'filter: grayscale(90%)';
            containerImgDetailElm.style = 'filter: grayscale(90%)';
            if (containerMarketElm) containerMarketElm.classList.add('d-none')
        } else {
            imgFixElm.style = 'filter: grayscale(0%)';
            containerImgDetailElm.style = 'filter: grayscale(0%)';
            if (containerMarketElm) containerMarketElm.classList.remove('d-none')
        }

        if (btnKeranjangElm) {
            const cartButton = btnKeranjangElm.querySelector('button[type="submit"]');
            if (Number(varianFullSelected.stok) > 0) {
                if (cartButton) { cartButton.textContent = 'Keranjang'; cartButton.disabled = false; cartButton.classList.remove('disabled'); }
                btnKeranjangElm.action = encodedCartUrl(<?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>, varianName, jumlahSelected);
                teksInfoHabisElm.classList.add('d-none')
                isStokHabis = false;
            } else {
                if (cartButton) { cartButton.textContent = 'Stok habis'; cartButton.disabled = true; cartButton.classList.add('disabled'); }
                btnKeranjangElm.action = '';
                teksInfoHabisElm.classList.remove('d-none')
                isStokHabis = true;
            }
        }
        varianSelected = varianName;
        syncQuantitySelection();

    })
});

document.querySelectorAll('[data-fit-guide]').forEach((guide) => {
    const button = guide.querySelector('[data-fit-check]');
    const widthInput = guide.querySelector('[data-fit-width]');
    const heightInput = guide.querySelector('[data-fit-height]');
    const result = guide.querySelector('[data-fit-result]');
    const messages = guide.querySelector('.ilena-fit-guide__messages');
    if (!button || !widthInput || !heightInput || !result || !messages) return;
    button.addEventListener('click', () => {
        const openingWidth = Number(widthInput.value);
        const openingHeight = Number(heightInput.value);
        if (!Number.isFinite(openingWidth) || !Number.isFinite(openingHeight) || openingWidth <= 0 || openingHeight <= 0) {
            result.textContent = messages.dataset.invalid;
            result.className = 'ilena-fit-guide__result is-invalid';
            return;
        }
        const dimensions = JSON.parse(guide.dataset.fitDimensions || '[]').map(Number);
        const fits = [[dimensions[0], dimensions[1]], [dimensions[0], dimensions[2]], [dimensions[1], dimensions[2]]]
            .some(([faceWidth, faceHeight]) => faceWidth > 0 && faceHeight > 0 && faceWidth <= openingWidth && faceHeight <= openingHeight);
        result.textContent = fits ? messages.dataset.pass : messages.dataset.fail;
        result.className = 'ilena-fit-guide__result ' + (fits ? 'is-pass' : 'is-fail');
    });
});
const jumlahBarangElm = document.querySelector('input[name="jumlah"]');
function syncQuantitySelection() {
    if (!jumlahBarangElm) return;
    const availableStock = Number(stokByVarianName[varianSelected] || 0);
    const maxQuantity = availableStock > 0 ? availableStock : 1;
    jumlahBarangElm.max = String(maxQuantity);
    const requestedQuantity = Math.floor(Number(jumlahBarangElm.value) || 1);
    const safeQuantity = Math.min(maxQuantity, Math.max(1, requestedQuantity));
    jumlahBarangElm.value = String(safeQuantity);
    jumlahSelected = String(safeQuantity);
    if (btnKeranjangElm && !isStokHabis) {
        btnKeranjangElm.action = encodedCartUrl(<?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>, varianSelected, safeQuantity);
    }
}
if (jumlahBarangElm) {
    jumlahBarangElm.addEventListener('change', syncQuantitySelection);
    syncQuantitySelection();
}

function kurangJumlah() {
    if (!isStokHabis) {
        if (Number(jumlahBarangElm.value) > 1) {
            jumlahBarangElm.value--
            btnKeranjangElm.action = encodedCartUrl(<?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>, varianSelected, jumlahBarangElm.value);
            jumlahSelected = jumlahBarangElm.value;
        }
    }
}

function tambahJumlah() {
    if (!isStokHabis) {
        const maxStok = Number(stokByVarianName[varianSelected] || 999);
        if (Number(jumlahBarangElm.value) >= maxStok) return;
        jumlahBarangElm.value++;
        btnKeranjangElm.action = encodedCartUrl(<?= json_encode((string) $produk['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>, varianSelected, jumlahBarangElm.value);
        jumlahSelected = jumlahBarangElm.value;
    }
}

function zoom(e) {
    const stage = e.currentTarget.closest('.ilena-product-gallery__stage');
    const zoomLayer = stage && stage.querySelector('.ilena-product-zoom');
    const source = zoomLayer && zoomLayer.dataset.zoomSrc;
    if (!stage || !zoomLayer || !source || window.innerWidth <= 700 || window.matchMedia('(hover: none)').matches || window.isHoverDetailSelected) return;
    if (zoomLayer.dataset.loadedSource !== source) {
        zoomLayer.style.backgroundImage = "url('" + source.replace(/'/g, '%27') + "')";
        zoomLayer.dataset.loadedSource = source;
    }
    const rect = e.currentTarget.getBoundingClientRect();
    const x = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
    const y = Math.max(0, Math.min(100, ((e.clientY - rect.top) / rect.height) * 100));
    zoomLayer.style.backgroundPosition = x + '% ' + y + '%';
    zoomLayer.classList.remove('d-none');
    zoomLayer.setAttribute('aria-hidden', 'false');
}

function mouseoff(e) {
    const zoomLayer = e.currentTarget.closest('.ilena-product-gallery__stage')?.querySelector('.ilena-product-zoom');
    if (zoomLayer) { zoomLayer.classList.add('d-none'); zoomLayer.setAttribute('aria-hidden', 'true'); }
}

document.querySelectorAll('.gambar').forEach((card) => {
    const hoverImg = card.querySelector('.lazy-hover-product');
    if (!hoverImg) return;
    const loadHover = () => {
        if (!hoverImg.hasAttribute('src') && hoverImg.dataset.src) {
            hoverImg.src = hoverImg.dataset.src;
        }
    };
    card.addEventListener('mouseenter', loadHover, { once: true });
    card.addEventListener('touchstart', loadHover, { once: true, passive: true });
});

document.querySelectorAll('[data-feedback-type]').forEach((selector) => {
    const form = selector.closest('form');
    const reviewFields = form?.querySelector('[data-feedback-review-fields]');
    const orderField = form?.querySelector('[data-feedback-order]');
    const ratingField = form?.querySelector('[data-feedback-rating]');
    const syncReviewFields = () => {
        const isReview = selector.value === 'review';
        if (reviewFields) reviewFields.hidden = !isReview;
        if (orderField) { orderField.disabled = !isReview; orderField.required = isReview; }
        if (ratingField) { ratingField.disabled = !isReview; ratingField.required = isReview; }
    };
    selector.addEventListener('change', syncReviewFields);
    syncReviewFields();
});
</script>

<?= $this->endSection(); ?>
