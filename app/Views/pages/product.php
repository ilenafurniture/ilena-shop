<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php
$punyaGambarHoverDetail = is_file(FCPATH . 'img/barang/hover/' . $produk['id'] . '.webp') || !empty($produk['gambar_hover']);
$imageSlotExists = function (string $slot) use ($produk): bool {
    $slot = trim($slot);
    if ($slot === '') return false;
    return is_file(FCPATH . 'img/barang/1000/' . $produk['id'] . '-' . $slot . '.webp')
        || is_file(FCPATH . 'img/barang/3000/' . $produk['id'] . '-' . $slot . '.webp');
};
$filterExistingSlots = function ($urutan) use ($imageSlotExists): array {
    $slots = array_values(array_filter(array_map('trim', explode(',', (string) $urutan))));
    $existing = array_values(array_filter($slots, $imageSlotExists));
    return $existing ?: ($slots ?: ['1']);
};
$gambarAwalDetail = '1';
if (!empty($produk['varian'][0]['urutan_gambar'])) {
    $urutanAwal = $filterExistingSlots($produk['varian'][0]['urutan_gambar']);
    $gambarAwalDetail = $urutanAwal[0] ?? '1';
}
$assetVersion = function (string $relativePath) use ($produk): string {
    $path = FCPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
    $fileVersion = is_file($path) ? (int) filemtime($path) : 0;
    $dbVersion = !empty($produk['tgl_update']) ? (int) strtotime((string) $produk['tgl_update']) : time();
    return (string) max($fileVersion, $dbVersion);
};
$barangImgUrl = function (string $size, string $slot) use ($produk, $assetVersion): string {
    $relative = 'img/barang/' . $size . '/' . $produk['id'] . '-' . $slot . '.webp';
    $route = $size === '3000' ? 'viewvar3000' : 'viewvar';
    return base_url($route . '/' . $produk['id'] . '/' . $slot) . '?v=' . $assetVersion($relative);
};
$hoverImgUrl = function () use ($produk, $assetVersion): string {
    $relative = 'img/barang/hover/' . $produk['id'] . '.webp';
    return base_url('viewpichover/' . $produk['id']) . '?v=' . $assetVersion($relative);
};
$productCoverUrl = function (array $product): string {
    $id = $product['id'] ?? '';
    $varian = json_decode($product['varian'] ?? '[]', true) ?: [];
    $slot = '1';
    if (!empty($varian[0]['urutan_gambar'])) {
        $slots = array_values(array_filter(array_map('trim', explode(',', (string) $varian[0]['urutan_gambar']))));
        $slot = $slots[0] ?? '1';
    }
    $sourceRelative = 'img/barang/1000/' . $id . '-' . $slot . '.webp';
    $sourceAbsolute = FCPATH . $sourceRelative;
    if (!is_file($sourceAbsolute)) {
        $sourceRelative = 'img/barang/3000/' . $id . '-' . $slot . '.webp';
        $sourceAbsolute = FCPATH . $sourceRelative;
    }
    if (!is_file($sourceAbsolute)) {
        $sourceRelative = 'img/barang/300/' . $id . '.webp';
        $sourceAbsolute = FCPATH . $sourceRelative;
    }
    $fileVersion = is_file($sourceAbsolute) ? (int) filemtime($sourceAbsolute) : time();
    $dbVersion = !empty($product['tgl_update']) ? (int) strtotime((string) $product['tgl_update']) : 0;
    return base_url('product-cover/' . $id) . '?slot=' . urlencode($slot) . '&v=' . max($fileVersion, $dbVersion);
};
?>
<style>
.product-detail-page {
    --ilena-red: #b31217;
    --ink: #0f172a;
    --muted: #64748b;
    --line: #e5e7eb;
    --soft: #f8fafc;
    padding-top: 8px;
    padding-bottom: 48px;
}
.product-detail-page .konten {
    max-width: 1180px;
}
.detail-breadcrumb {
    margin-bottom: 18px;
    font-size: 13px;
}
.detail-breadcrumb a {
    color: #64748b;
    text-decoration: none;
}
.detail-breadcrumb a:hover {
    color: var(--ilena-red);
}
.product-detail-card {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(380px, .92fr);
    gap: 30px;
    align-items: start;
    background: linear-gradient(180deg, #fff 0%, #fbfbfc 100%);
    border: 1px solid #eef2f7;
    border-radius: 28px;
    padding: 26px;
    box-shadow: 0 24px 70px rgba(15, 23, 42, .08);
}
.product-info-panel,
.product-gallery-panel {
    min-width: 0;
}
.product-detail-card > .limapuluh-ke-seratus {
    width: 100% !important;
}
.product-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 11px;
    border: 1px solid #fee2e2;
    border-radius: 999px;
    background: #fff7f7;
    color: var(--ilena-red);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.product-title-row {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    justify-content: space-between;
    margin: 12px 0 10px;
}
.product-title {
    margin: 0;
    color: var(--ink);
    font-size: clamp(30px, 4vw, 52px);
    line-height: .98;
    letter-spacing: -.055em;
    font-weight: 900;
}
.product-category-link {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: var(--ink);
    flex: 0 0 auto;
}
.product-category-link:hover {
    color: var(--ilena-red);
    border-color: #fecaca;
}
.price-wrap {
    align-items: baseline;
    flex-wrap: wrap;
    padding: 14px 0 18px;
    border-bottom: 1px solid #eef2f7;
}
.price-wrap .harga {
    font-size: clamp(22px, 3vw, 32px);
    font-weight: 900;
    letter-spacing: -.04em;
    color: var(--ilena-red);
    margin: 0;
}
.price-wrap .harga-diskon {
    color: #94a3b8;
    margin: 0;
}
.product-description {
    margin: 18px 0;
    color: #334155;
    font-size: 15px;
    line-height: 1.75;
}
.option-block {
    margin: 16px 0;
}
.option-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    color: var(--ink);
    font-size: 13px;
    font-weight: 850;
}
.container-varian {
    gap: 9px;
    flex-wrap: wrap;
}
.container-varian input,
.container-img-detail-select input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.container-varian label {
    width: 38px;
    height: 38px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dbe3ef;
    background: #fff;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.container-varian label:hover {
    transform: translateY(-1px);
    border-color: #94a3b8;
}
.container-varian label span {
    width: 24px;
    height: 24px;
    border-radius: 999px;
    display: block;
    border: 1px solid rgba(15, 23, 42, .15);
}
.container-varian input:checked + label {
    border-color: var(--ilena-red);
    box-shadow: 0 0 0 4px rgba(179, 18, 23, .11);
}
.size-options {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.size-options .btn-default-abu {
    min-height: 38px;
    border-radius: 999px;
    padding: 8px 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
}
.purchase-card {
    margin-top: 20px;
    padding: 14px;
    border: 1px solid #eef2f7;
    border-radius: 18px;
    background: #fff;
}
.purchase-card .btn-default-merah {
    min-height: 46px;
    border-radius: 14px;
    padding-left: 18px;
    padding-right: 18px;
    font-weight: 850;
}
.save-action .btn-teks-aja {
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 12px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
}
.marketplace-box {
    padding: 14px;
    border: 1px solid #eef2f7;
    border-radius: 16px;
    background: #f8fafc;
}
.product-detail-page .accordion {
    border: 1px solid #eef2f7;
    border-radius: 18px;
    overflow: hidden;
}
.product-detail-page .accordion-button {
    font-size: 13px;
    letter-spacing: .04em;
}
.product-gallery-panel {
    position: sticky;
    top: 92px;
}
.product-main-image-wrap {
    position: relative;
    overflow: visible;
    border-radius: 24px;
    background: #f1f5f9;
    border: 1px solid #e5e7eb;
}
.product-main-image-wrap img.img-detail-prev {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    display: block;
    border-radius: 24px;
}
figure.img-detail-prev {
    z-index: 20;
    border-radius: 20px;
    box-shadow: 0 18px 50px rgba(15, 23, 42, .18);
    pointer-events: none;
}
.thumb-strip {
    margin-top: 14px;
    overflow-x: auto;
    padding: 3px 2px 8px;
}
.container-img-detail-select {
    display: flex;
    gap: 10px;
    min-width: max-content;
}
.img-detail-select {
    margin: 0;
    border: 2px solid transparent;
    border-radius: 16px;
    padding: 3px;
    background: #fff;
    cursor: pointer;
    transition: border-color .15s ease, transform .15s ease;
}
.img-detail-select:hover {
    transform: translateY(-1px);
    border-color: #cbd5e1;
}
.container-img-detail-select input:checked + .img-detail-select {
    border-color: var(--ilena-red);
}
.img-detail-select img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 12px;
    display: block;
}
.related-heading {
    margin-top: 42px;
    text-align: center;
    color: var(--ink);
    font-weight: 850;
    letter-spacing: -.02em;
}
@media (max-width: 991px) {
    .product-detail-card {
        grid-template-columns: 1fr;
        padding: 16px;
        border-radius: 22px;
    }
    .product-gallery-panel {
        position: static;
        order: -1;
    }
    .product-title {
        font-size: 34px;
    }
    .img-detail-select img {
        width: 62px;
        height: 62px;
    }
    .purchase-card .d-flex {
        flex-wrap: wrap;
    }
}
</style>

<div class="container product-detail-page d-flex flex-column align-items-center">
    <div class="konten w-100">
        <nav class="detail-breadcrumb show-block-ke-hide" style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/product">Produk Kami</a></li>
                <li class="breadcrumb-item"><a
                        href="/product?koleksi=<?= $produk['kategori']; ?>"><?= ucfirst($produk['kategori']); ?></a>
                </li>
                <li class="breadcrumb-item"><a
                        href="/product?jenis=<?= $produk['subkategori']; ?>"><?= ucfirst($produk['subkategori']); ?></a>
                </li>
                </li>
            </ol>
        </nav>
        <div class="baris-ke-kolom-reverse product-detail-card w-100">
            <div class="limapuluh-ke-seratus product-info-panel">
                <span class="product-kicker"><?= esc(ucfirst($produk['kategori'])); ?> • <?= esc(ucfirst($produk['subkategori'])); ?></span>
                <div class="product-title-row">
                    <h1 class="product-title"><?= str_replace('Tv', 'TV', ucwords($produk['nama'])) ?></h1>
                    <a href="/product?koleksi=<?= $produk['kategori']; ?>" class="product-category-link" aria-label="Lihat produk <?= esc(ucfirst($produk['kategori'])); ?>">
                        <i class="material-icons d-inline" style="font-size: 22px;">open_in_new</i>
                        <span class="popup"> Lihat Produk <?= ucfirst($produk['kategori']); ?></span>
                        <style>
                        .product-category-link {
                            position: relative;
                        }

                        .popup {
                            visibility: hidden;
                            opacity: 0;
                            width: 120px;
                            background-color: black;
                            color: #fff;
                            text-align: center;
                            border-radius: 6px;
                            padding: 5px 5px;
                            position: absolute;
                            bottom: 240%;
                            left: 50%;
                            transform: translateX(-50%) translateY(10px);
                            z-index: 1;
                            transition: opacity 0.6s, transform 0.6s;
                        }

                        .popup::before {
                            content: "";
                            position: absolute;
                            top: 100%;
                            left: 50%;
                            margin-left: -5px;
                            border-width: 5px;
                            border-style: solid;
                            border-color: black transparent transparent transparent;
                        }

                        .product-category-link:hover .popup {
                            visibility: visible;
                            opacity: 1;
                            transform: translateX(-50%) translateY(0);
                        }
                        </style>
                    </a>
                </div>

                <div class="price-wrap d-flex gap-2 mb-3">
                    <p class="harga">Rp
                        <?= number_format($produk['harga'] * (100 - $produk['diskon']) / 100, 0, ',', '.'); ?></p>
                    <?php if ($produk['diskon'] > 0) { ?>
                    <p class="harga-diskon">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                    <?php } ?>
                </div>
                <div class="product-description"><?= $produk['deskripsi']['deskripsi'] ?></div>
                <div class="gap-2 show-flex-ke-hide">
                    <div class="option-block">
                        <div class="option-label"><span>Pilih warna / varian</span><small id="selected-varian-label"><?= esc($produk['varian'][0]['nama'] ?? 'default'); ?></small></div>
                    <div class="container-varian mb-3 d-flex">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                        <input id="varian<?= $ind_v ?>"
                            value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>-<?= $ind_v ?>"
                            data-slot="<?= esc($v['urutan_gambar'], 'attr') ?>"
                            data-name="<?= esc($v['nama'], 'attr') ?>"
                            data-index="<?= $ind_v ?>"
                            data-stok="<?= (int)($v['stok'] ?? 0) ?>"
                            type="radio"
                            name="varian" <?= $ind_v == 0 ? 'checked' : ''; ?>>
                        <label for="varian<?= $ind_v ?>"><span
                                style="background-color: <?= $v['kode'] ?>"></span></label>
                        <?php } ?>
                    </div>
                    </div>
                    <div class="option-block">
                        <div class="option-label"><span>Pilihan ukuran</span></div>
                    <div class="size-options mb-2">
                        <?php foreach ($produkSemua as $ind_ps => $ps) { ?>
                        <a href="/product/<?= strtolower(str_replace(' ', '-', $ps['nama'])) ?>/<?= $ind_ps ?>"
                            class="btn-default-abu <?= $ind_ps == $indexNama ? 'border border-dark' : '' ?>"><?= (json_decode($ps['deskripsi'] ?? '{}', true) ?? [])['dimensi']['asli']['panjang'] ?? '-' ?>
                            mm</a>
                        <?php } ?>
                    </div>
                    </div>
                </div>
                <?php if (session()->get('role') == '1') { ?>
                <div class="purchase-card d-flex gap-2">
                    <a class="btn-default d-flex justify-content-center align-items-center"
                        href="/admin/editproduct/<?= $produk['id']; ?>">
                        <i class="material-icons">edit</i>
                        <p class="m-0">Edit</p>
                    </a>
                    <!-- <a class="btn-default-merah d-flex justify-content-center align-items-center"
                        href="/admin/deleteproduct/<?= $produk['id']; ?>">
                        <i class="material-icons">delete</i>
                        <p class="m-0">Hapus</p>
                    </a> -->
                    <form action="/admin/deleteproduct/<?= $produk['id']; ?>" method="post">
                        <button class="btn-default-merah d-flex justify-content-center align-items-center"
                            type="submit" onclick="return confirm('Hapus produk ini?')"><i class="material-icons">delete</i>
                            <p class="m-0">Hapus</p>
                        </button>
                    </form>
                </div>
                <?php } else { ?>
                <div class="purchase-card">
                <div class="d-flex gap-2 align-items-stretch">
                    <div class="number-control">
                        <div class="number-left" onclick="kurangJumlah()"></div>
                        <input type="number" name="jumlah" class="number-quantity" value="1">
                        <div class="number-right" onclick="tambahJumlah()"></div>
                    </div>
                    <form id="btn-keranjang" method="post"
                        action="<?= $produk['varian'][0]['stok'] > 0 ? '/addcart/' . $produk['id'] . '/' . rawurlencode($produk['varian'][0]['nama']) . '/1' : ''; ?>">
                        <button class="btn-default-merah <?= $produk['varian'][0]['stok'] > 0 ? '' : 'disabled'; ?>"
                            <?= $produk['varian'][0]['stok'] > 0 ? '' : 'disabled'; ?>
                            type="submit"><?= $produk['varian'][0]['stok'] > 0 ? 'Keranjang' : 'Stok habis'; ?></button>
                    </form>

                </div>
                </div>
                <?php } ?>
                <p id="info-habis" class="mt-2 <?= $produk['varian'][0]['stok'] <= 0 ? '' : 'd-none'; ?>"
                    style="font-size: 10px;">*Produk ini bisa di pre-order dengan menghubungi <a
                        href="https://wa.me/+628112938158" class="btn-teks-aja" style="display: inline;">Customer
                        Service</a> kami</p>
                <?php if ($produk['tokped'] || $produk['shopee'] || $produk['tiktok']) { ?>
                <div class="marketplace-box mt-4 <?= $produk['varian'][0]['stok'] <= 0 ? 'd-none' : ''; ?>" id="container-market">
                    <p class="mb-2">
                        Produk ini juga tersedia di
                    </p>
                    <div>
                        <?php if ($produk['tokped']) { ?>
                        <a href="<?= $produk['tokped']; ?>" title="Tokopedia" target="_blank"><img
                                src="/img/logo/tokopedia.png" class="marketplace"></a>
                        <?php } ?>
                        <?php if ($produk['shopee']) { ?>
                        <a href="https://shopee.co.id/ilenafurnitureofficial" title="Shopee" target="_blank"><img
                                src="/img/logo/shopee.png" class="marketplace"></a>
                        <?php } ?>
                        <?php if ($produk['tiktok']) { ?>
                        <a href="<?= $produk['tiktok']; ?>" title="Tiktok" target="_blank"><img
                                src="/img/logo/tiktokshop.svg" class="marketplace"></a>
                        <?php } ?>
                    </div>
                </div>
                <?php }
                if (in_array($produk['id'], $wishlist)) { ?>
                <form class="save-action" action="/delwishlist/<?= $produk['id'] ?>" method="post">
                    <button type="submit" class="btn-teks-aja my-3"><i class="material-icons">bookmark</i>Hapus</button>
                </form>
                <?php } else { ?>
                <form class="save-action" action="/addwishlist/<?= $produk['id'] ?>" method="post">
                    <button type="submit" class="btn-teks-aja my-3"><i
                            class="material-icons">bookmark_border</i>Simpan</button>
                </form>
                <?php } ?>
                <hr>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                                SPESIFIKASI PRODUK
                            </button>
                        </h2>
                        <div id="flush-collapse2" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p class="mb-0">
                                    <?= $produk['deskripsi']['perawatan'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse1" aria-expanded="false" aria-controls="flush-collapse1">
                                DIMENSI
                            </button>
                        </h2>
                        <div id="flush-collapse1" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p class="mb-0 fw-bold">Dimensi Produk</p>
                                <ul>
                                    <li>
                                        <p class="mb-0">
                                            Panjang : <?= $produk['deskripsi']['dimensi']['asli']['panjang'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Lebar : <?= $produk['deskripsi']['dimensi']['asli']['lebar'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Tinggi : <?= $produk['deskripsi']['dimensi']['asli']['tinggi'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Berat : <?= $produk['deskripsi']['dimensi']['asli']['berat'] ?> kg
                                        </p>
                                    </li>
                                </ul>
                                <p class="mb-0 mt-1 fw-bold">Dimensi Kemasan</p>
                                <ul>
                                    <li>
                                        <p class="mb-0">
                                            Panjang : <?= $produk['deskripsi']['dimensi']['paket']['panjang'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Lebar : <?= $produk['deskripsi']['dimensi']['paket']['lebar'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Tinggi : <?= $produk['deskripsi']['dimensi']['paket']['tinggi'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Berat : <?= $produk['deskripsi']['dimensi']['paket']['berat'] ?> kg
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="limapuluh-ke-seratus product-gallery-panel">
                <div class="product-main-image-wrap">
                    <figure class="img-detail-prev d-none"
                        style="background-image: url('<?= $barangImgUrl('3000', $gambarAwalDetail) ?>'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;">
                    </figure>
                    <img class="img-detail-prev"
                        <?= $produk['varian'][0]['stok'] <= 0 ? 'style="filter: grayscale(90%)"' : ''; ?>
                        src="<?= $barangImgUrl('1000', $gambarAwalDetail) ?>" onmousemove="zoom(event)"
                        onmouseleave="mouseoff(event)" alt="<?= esc($produk['nama']); ?>">
                </div>
                <div class="thumb-strip mb-3 mt-3">
                    <div class="container-img-detail-select"
                        <?= $produk['varian'][0]['stok'] <= 0 ? 'style="filter: grayscale(90%)"' : ''; ?>>
                        <?php foreach ($filterExistingSlots($produk['varian'][0]['urutan_gambar']) as $indx => $p_v) { ?>
                        <input <?= $indx == 0 ? 'checked' : '' ?> id="gambar<?= $indx ?>" type="radio" name="gambar"
                            value="<?= $p_v ?>">
                        <label class="img-detail-select" for="gambar<?= $indx ?>"><img
                                src="<?= $barangImgUrl('1000', $p_v) ?>"></label>
                        <?php } ?>
                        <?php if ($punyaGambarHoverDetail) { ?>
                        <input id="gambar-hover" type="radio" name="gambar" value="hover">
                        <label class="img-detail-select" for="gambar-hover"><img
                                src="<?= $hoverImgUrl() ?>"></label>
                        <?php } ?>
                        <script>
                        const radioImgElm = document.querySelectorAll('input[name="gambar"]');
                        radioImgElm.forEach(elm => {
                            elm.addEventListener('change', (e) => {
                                const imgElm = document.querySelector("figure.img-detail-prev");
                                const imgFixElm = document.querySelector("img.img-detail-prev");
                                if (e.target.value === "hover") {
                                    window.isHoverDetailSelected = true;
                                    imgElm.classList.add("d-none");
                                    imgElm.style =
                                        "background-image: url('/viewpichover/<?= $produk['id']; ?>?v=" + Date.now() + "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
                                    imgFixElm.src = "/viewpichover/<?= $produk['id']; ?>?v=" + Date.now();
                                    return;
                                }
                                window.isHoverDetailSelected = false;
                                imgElm.style =
                                    "background-image: url('" +
                                    "/viewvar3000/<?= $produk['id']; ?>/" +
                                    e.target.value
                                    .split("-")[0] +
                                    "?v=" + Date.now() + "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
                                imgFixElm.src = "/viewvar/<?= $produk['id']; ?>/" + e.target
                                    .value
                                    .split("-")[0] + '?v=' + Date.now();
                            })
                        });
                        </script>
                    </div>
                </div>
                <div class="gap-2 hide-ke-show-flex">
                    <div class="option-block w-100">
                        <div class="option-label"><span>Pilih warna / varian</span></div>
                    <div class="container-varian mb-3 d-flex">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                        <input id="varian-mobile<?= $ind_v ?>"
                            value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>-<?= $ind_v ?>"
                            data-slot="<?= esc($v['urutan_gambar'], 'attr') ?>"
                            data-name="<?= esc($v['nama'], 'attr') ?>"
                            data-index="<?= $ind_v ?>"
                            data-stok="<?= (int)($v['stok'] ?? 0) ?>"
                            type="radio" name="varian" <?= $ind_v == 0 ? 'checked' : ''; ?>>
                        <label for="varian-mobile<?= $ind_v ?>"><span
                                style="background-color: <?= $v['kode'] ?>"></span></label>
                        <?php } ?>
                    </div>
                    </div>
                    <div class="option-block w-100">
                        <div class="option-label"><span>Pilihan ukuran</span></div>
                    <div class="size-options mb-2">
                        <?php foreach ($produkSemua as $ind_ps => $ps) { ?>
                        <a href="/product/<?= strtolower(str_replace(' ', '-', $ps['nama'])) ?>/<?= $ind_ps ?>"
                            class="btn-default-abu <?= $ind_ps == $indexNama ? 'border border-dark' : '' ?>"><?= (json_decode($ps['deskripsi'] ?? '{}', true) ?? [])['dimensi']['asli']['panjang'] ?? '-' ?>
                            mm</a>
                        <?php } ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (count($produkSejenis) > 0) { ?>
        <hr class="mt-5">
        <h2 class="related-heading">Anda mungkin juga suka</h2>
        <div class="container-card1">
            <?php foreach ($produkSejenis as $ind_p => $p) { ?>
            <div class="card1">
                <div style="position: relative;">
                    <span class="card1-content-img-kiri"
                        <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                    <div class="d-flex flex-column gap-2 card1-content-img-kanan">
                        <?php if (session()->get('role') == '1') { ?>
                        <a class="card1-btn-img" href="/admin/editproduct/<?= $p['id'] ?>" title="Edit produk"><i class="material-icons">edit</i></a>
                        <?php }
                                if (in_array($p['id'], $wishlist)) { ?>
                        <form action="/delwishlist/<?= $p['id'] ?>" method="post">
                            <button type="submit" class="card1-btn-img"><i class="material-icons">bookmark</i></button>
                        </form>
                        <?php } else { ?>
                        <form action="/addwishlist/<?= $p['id'] ?>" method="post">
                            <button type="submit" class="card1-btn-img"><i
                                    class="material-icons">bookmark_border</i></button>
                        </form>
                        <?php } ?>
                        <?php $cardVarianAwal = json_decode($p['varian'], true)[0]['nama'] ?? 'default'; ?>
                        <form method="post" id="card<?= $ind_p ?>"
                            action="/addcart/<?= $p['id'] ?>/<?= rawurlencode($cardVarianAwal) ?>/1"><button class="card1-btn-img"><i class="material-icons">shopping_cart</i>
                            </button>
                        </form>
                    </div>
                    <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="gambar">
                        <img class=" img-pic" id="img<?= $ind_p ?>"
                            src="<?= $productCoverUrl($p) ?>" alt="" loading="lazy" decoding="async">
                        <img class=" img-pic-hover" id="img<?= $ind_p ?>"
                            src="<?= base_url('viewpichover/' . $p['id']) ?>?v=<?= !empty($p['tgl_update']) ? strtotime($p['tgl_update']) : time() ?>" alt="" loading="lazy" decoding="async"> 
                    </a>
                </div>
                <div class="container-varian mb-1 d-flex">
                    <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                    <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>"
                        value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>"
                        data-slot="<?= esc($v['urutan_gambar'], 'attr') ?>"
                        data-name="<?= esc($v['nama'], 'attr') ?>"
                        data-stok="<?= (int)($v['stok'] ?? 0) ?>"
                        type="radio" name="varian<?= $ind_p ?>">
                    <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>"><span
                            style="background-color: <?= $v['kode'] ?>"></span></label>
                    <?php } ?>
                    <script>
                    const btnKeranjang<?= $ind_p ?>Elm = document.getElementById("card<?= $ind_p ?>");
                    const varian<?= $ind_p ?>Elm = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');
                    varian<?= $ind_p ?>Elm.forEach(elm => {
                        elm.addEventListener('change', (e) => {
                            console.log(e.target.value)
                            const img<?= $ind_p ?>Elm = document.getElementById("img<?= $ind_p ?>");
                            const slot = (e.target.dataset.slot || e.target.value.split("-")[0] || "1").split(",")[0];
                            const namaVarian = e.target.dataset.name || e.target.value.split("-").slice(1).join("-") || "default";
                            const stok = Number(e.target.dataset.stok || 0);
                            img<?= $ind_p ?>Elm.src =
                                "<?= base_url('viewvar/' . $p['id'] .'/') ?>" + slot + '?v=' + Date.now();

                            btnKeranjang<?= $ind_p ?>Elm.action = stok > 0 ? "/addcart/<?= $p['id'] ?>/" + encodeURIComponent(namaVarian) + "/1" : "";
                            const quickBtn = btnKeranjang<?= $ind_p ?>Elm.querySelector('button');
                            if (quickBtn) quickBtn.disabled = stok <= 0;
                        })
                    });
                    </script>
                </div>
                <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="text-dark">
                    <p class="text-secondary text-sm-start m-0"><?= strtolower($p['kategori']); ?></p>
                    <h5 style="font-size:18px;"><?= str_replace('Tv', 'TV', ucwords($p['nama'])); ?></h5>
                    <div class="d-flex gap-2">
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
        <?php } ?>
    </div>
</div>
<script>
const figureElm = document.querySelector('figure');
const btnKeranjangElm = document.getElementById('btn-keranjang');
const radioVarianElm = document.querySelectorAll('input[name="varian"]');
const varian = JSON.parse('<?= json_encode($produk['varian']) ?>');
const teksInfoHabisElm = document.getElementById('info-habis');
const containerMarketElm = document.getElementById('container-market');
const selectedVarianLabelElm = document.getElementById('selected-varian-label');
let varianSelected = <?= json_encode($produk['varian'][0]['nama'] ?? 'default') ?>;
let jumlahSelected = "1";
let isStokHabis = <?= $produk['varian'][0]['stok'] == '0' ? 'true' : 'false' ?>;
const stokByVarianName = <?= json_encode(array_column($produk['varian'], 'stok', 'nama')) ?>;
function encodedCartUrl(productId, varianName, qty) {
    return "/addcart/" + productId + "/" + encodeURIComponent(varianName || "default") + "/" + Math.max(1, Number(qty || 1));
}
radioVarianElm.forEach(elm => {
    elm.addEventListener('change', (e) => {
        const slotValue = e.target.dataset.slot || e.target.value.split("-")[0];
        const varianName = e.target.dataset.name || e.target.value.split("-").slice(1, -1).join("-") || e.target.value.split("-")[1] || '';
        const varianFullSelected = varian[Number(e.target.dataset.index || e.target.value.split("-")[2] || 0)] || { stok: e.target.dataset.stok || 0, urutan_gambar: slotValue, nama: varianName };
        if (selectedVarianLabelElm) selectedVarianLabelElm.textContent = varianName || 'default';
        const imgElm = document.querySelector("figure.img-detail-prev");
        const imgFixElm = document.querySelector("img.img-detail-prev");
        imgElm.style =
            "background-image: url('" + "/viewvar3000/<?= $produk['id']; ?>/" + slotValue.split(",")[0] +
            "?v=" + Date.now() + "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
        imgFixElm.src = "/viewvar/<?= $produk['id']; ?>/" + slotValue.split(",")[0] + '?v=' + Date.now();

        const containerImgDetailElm = document.querySelector(".container-img-detail-select");
        containerImgDetailElm.innerHTML = "";
        const urutanGambar = slotValue.split(",");
        urutanGambar.forEach((urutan, ind_x) => {
            containerImgDetailElm.innerHTML += '<input id="gambar' + ind_x +
                '" type="radio" name="gambar" value="' + urutan + '"' + (ind_x === 0 ? ' checked' : '') +
                '"><label class="img-detail-select" for="gambar' + ind_x +
                '"><img src="/viewvar/<?= $produk['id'] ?>/' + urutan +
                '?v=' + Date.now() + '"></label>'
        })
        <?php if ($punyaGambarHoverDetail) { ?>
        containerImgDetailElm.innerHTML += '<input id="gambar-hover" type="radio" name="gambar" value="hover">' +
            '<label class="img-detail-select" for="gambar-hover">' +
            '<img src="/viewpichover/<?= $produk['id'] ?>?v=' + Date.now() + '"></label>';
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
            if (Number(varianFullSelected.stok) > 0) {
                btnKeranjangElm.children[0].innerHTML = 'Keranjang'
                btnKeranjangElm.action = encodedCartUrl("<?= $produk['id'] ?>", varianName, jumlahSelected);
                btnKeranjangElm.children[0].classList.remove('disabled')
                teksInfoHabisElm.classList.add('d-none')
                isStokHabis = false;
            } else {
                btnKeranjangElm.children[0].innerHTML = 'Stok habis'
                btnKeranjangElm.action = ''
                btnKeranjangElm.children[0].classList.add('disabled')
                teksInfoHabisElm.classList.remove('d-none')
                isStokHabis = true;
            }
        }
        varianSelected = varianName;

        const radioImgElm = document.querySelectorAll('input[name="gambar"]');
        radioImgElm.forEach(elm1 => {
            elm1.addEventListener('change', (elmVar) => {
                const imgElm = document.querySelector("figure.img-detail-prev");
                const imgFixElm = document.querySelector("img.img-detail-prev");
                if (elmVar.target.value === "hover") {
                    window.isHoverDetailSelected = true;
                    imgElm.classList.add("d-none");
                    imgElm.style =
                        "background-image: url('/viewpichover/<?= $produk['id']; ?>?v=" + Date.now() + "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
                    imgFixElm.src = "/viewpichover/<?= $produk['id']; ?>?v=" + Date.now();
                    return;
                }
                window.isHoverDetailSelected = false;
                imgElm.style =
                    "background-image: url('" +
                    "/viewvar3000/<?= $produk['id']; ?>/" +
                    elmVar.target.value.split("-")[0].split(",")[0] +
                    "?v=" + Date.now() + "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
                imgFixElm.src = "/viewvar/<?= $produk['id']; ?>/" + elmVar
                    .target.value
                    .split("-")[0].split(",")[0] + '?v=' + Date.now()
            })
        });
    })
});
const jumlahBarangElm = document.querySelector('input[name="jumlah"]');

function kurangJumlah() {
    if (!isStokHabis) {
        if (Number(jumlahBarangElm.value) > 1) {
            jumlahBarangElm.value--
            btnKeranjangElm.action = encodedCartUrl("<?= $produk['id'] ?>", varianSelected, jumlahBarangElm.value);
            jumlahSelected = jumlahBarangElm.value;
        }
    }
}

function tambahJumlah() {
    if (!isStokHabis) {
        const maxStok = Number(stokByVarianName[varianSelected] || 999);
        if (Number(jumlahBarangElm.value) >= maxStok) return;
        jumlahBarangElm.value++;
        btnKeranjangElm.action = encodedCartUrl("<?= $produk['id'] ?>", varianSelected, jumlahBarangElm.value);
        jumlahSelected = jumlahBarangElm.value;
    }
}

function zoom(e) {
    if (window.innerWidth > 600 && !window.isHoverDetailSelected) {
        figureElm.classList.remove('d-none')
        figureElm.style.backgroundSize = "auto"
        const widthGambar = e.target.offsetWidth;
        const gmbrPosition = [
            (e.offsetX / widthGambar) * 100,
            (e.offsetY / widthGambar) * 100,
        ];
        figureElm.style.backgroundPosition =
            gmbrPosition[0] + "% " + gmbrPosition[1] + "%";
    }
}

function mouseoff(e) {
    if (window.innerWidth > 600) {
        figureElm.classList.add('d-none')
        figureElm.style.backgroundSize = "cover"
    }
}
</script>

<?= $this->endSection(); ?>
