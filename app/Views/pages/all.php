<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php
$productImageStorage = strtolower((string) env('PRODUCT_IMAGE_STORAGE', 'local'));
$productImageCdn = $productImageStorage === 'r2' ? rtrim((string) env('R2_PUBLIC_URL', ''), '/') : '';
$productCdnImageUrl = static function (string $key, int $version) use ($productImageCdn): string {
    return $productImageCdn !== '' ? $productImageCdn . '/' . ltrim($key, '/') . '?v=' . $version : '';
};
$productCoverUrl = function (array $product): string {
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
        if (is_file(FCPATH . $candidate)) {
            $sourceRelative = $candidate;
            break;
        }
    }
    $sourceAbsolute = FCPATH . $sourceRelative;
    $sourceExists = is_file($sourceAbsolute);
    $fileVersion = $sourceExists ? (int) filemtime($sourceAbsolute) : 0;
    $dbVersion = !empty($product['tgl_update']) ? (int) strtotime((string) $product['tgl_update']) : 0;
    // Serve an existing local 300px thumbnail directly, bypassing the image
    // controller (and its remote-storage existence checks) for catalog tiles.
    $isLocalThumbnail = $sourceExists
        && in_array($sourceRelative, ['img/barang/300/' . $id . '.webp', 'uploads/product-images/300/' . $id . '.webp'], true);
    $fullSizeMtime = 0;
    foreach ([
        'uploads/product-images/1000/' . $id . '-' . $slot . '.webp',
        'uploads/product-images/3000/' . $id . '-' . $slot . '.webp',
        'img/barang/1000/' . $id . '-' . $slot . '.webp',
        'img/barang/3000/' . $id . '-' . $slot . '.webp',
    ] as $fullSizeCandidate) {
        if (is_file(FCPATH . $fullSizeCandidate)) {
            $fullSizeMtime = max($fullSizeMtime, (int) filemtime(FCPATH . $fullSizeCandidate));
        }
    }
    $imageStorage = strtolower((string) env('PRODUCT_IMAGE_STORAGE', 'local'));
    $thumbnailIsCurrent = $sourceExists && (!$fullSizeMtime || $fileVersion >= $fullSizeMtime);
    $fileVersion = max($fileVersion, $fullSizeMtime);
    if ($imageStorage !== 'r2' && $isLocalThumbnail && $thumbnailIsCurrent) {
        return base_url($sourceRelative) . '?v=' . max($fileVersion, $dbVersion);
    }
    return base_url('product-cover/' . $id) . '?slot=' . urlencode($slot) . '&size=300&v=' . max($fileVersion, $dbVersion);
};
$isSmartSearch = isset($find);
$smartQuery = (string) ($find ?? '');
$catalogRoute = $isSmartSearch ? '/find' : '/product';
$catalogHeading = (string) ($title ?? 'Produk Kami');
$hideGenericCatalogIntro = !$isSmartSearch && mb_strtolower(trim($catalogHeading)) === 'produk kami';
?>
<?php
if (isset($kategori)) $_GET['koleksi'] = $kategori;
// PHP decodes raw `+` query separators as spaces, but JS previously escaped
// a literal plus as `%2B`. Normalize both formats (and repeated query values)
// before applying facets, rendering checked states, or building pagination.
foreach (['koleksi', 'jenis', 'ruang', 'varian'] as $multiFacet) {
    if (!isset($_GET[$multiFacet])) continue;
    $rawFacetValues = is_array($_GET[$multiFacet]) ? $_GET[$multiFacet] : [(string) $_GET[$multiFacet]];
    $normalizedFacetValues = [];
    foreach ($rawFacetValues as $rawFacetValue) {
        if (!is_scalar($rawFacetValue)) continue;
        foreach (preg_split('/[\s+,]+/u', trim((string) $rawFacetValue), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $facetValue) {
            $facetValue = trim($facetValue);
            if ($facetValue !== '' && !in_array($facetValue, $normalizedFacetValues, true)) {
                $normalizedFacetValues[] = $facetValue;
            }
        }
    }
    $_GET[$multiFacet] = implode(' ', $normalizedFacetValues);
}
// A price range is a single-valued facet. Ignore malformed/repeated values
// instead of letting an array or unknown range reach the price switch.
if (isset($_GET['harga'])) {
    if (!is_scalar($_GET['harga']) || !in_array((string) $_GET['harga'], ['0', '1', '2', '3', '4'], true)) {
        unset($_GET['harga']);
    } else {
        $_GET['harga'] = (string) $_GET['harga'];
    }
}
$hasAppliedCatalogFilters = isset($_GET['harga']) || !empty($_GET['koleksi']) || !empty($_GET['jenis']) || !empty($_GET['ruang']) || !empty($_GET['varian']);
if (isset($_GET['koleksi'])) {
    if ($_GET['koleksi'] != '') {
        $koleksi = explode(" ", $_GET['koleksi']);
        $produkLama = $produk;
        $produk = [];
        foreach ($koleksi as $k) {
            foreach ($produkLama as $p) {
                if (strtolower(str_replace("-", " ", $k)) == strtolower($p['kategori'])) {
                    array_push($produk, $p);
                }
            }
        }
    }
}
if (isset($_GET['jenis']) && !in_array(strtolower($_GET['jenis']), ['diskon','bundling'])) {
    if ($_GET['jenis'] != '') {
        $jenis = explode(" ", $_GET['jenis']);
        $produkLama = $produk;
        $produk = [];
        foreach ($jenis as $j) {
            foreach ($produkLama as $p) {
                if (strtolower(str_replace("-", " ", $j)) == strtolower($p['subkategori'])) {
                    array_push($produk, $p);
                }
            }
        }
    }
}

if (isset($_GET['varian'])) {
    if ($_GET['varian'] != '') {
        $varian = explode(" ", $_GET['varian']);
        $produkLama = $produk;
        $produk = [];
        $produkTerpilih = [];
        foreach ($varian as $v) {
            foreach ($produkLama as $p) {
                $varianProdukSelected = json_decode($p['varian'], true);
                foreach ($varianProdukSelected as $vp) {
                    if (strtolower(str_replace("-", " ", $v)) == strtolower($vp['nama'])) {
                        $produkKey = 'id:' . (string) $p['id'];
                        if (!isset($produkTerpilih[$produkKey])) {
                            array_push($produk, $p);
                            $produkTerpilih[$produkKey] = true;
                        }
                    }
                }
            }
        }
    }
}
if (isset($_GET['harga'])) {
    if ($_GET['harga'] != '') {
        $produkLama = $produk;
        $produk = [];
        foreach ($produkLama as $p) {
            // Use the same effective sale-price calculation as product cards
            // and checkout, including scheduled discounts and rounding.
            $hargaDiskon = \App\Services\CatalogPricing::unitPrice($p);
            switch ($_GET['harga']) {
                case '0':
                    if ($hargaDiskon < 1000000)
                        array_push($produk, $p);
                    break;
                case '1':
                    if (1000000 <= $hargaDiskon && $hargaDiskon < 3000000) {
                        array_push($produk, $p);
                    }
                    break;
                case '2':
                    if (3000000 <= $hargaDiskon && $hargaDiskon < 5000000)
                        array_push($produk, $p);
                    break;
                case '3':
                    if (5000000 <= $hargaDiskon && $hargaDiskon < 10000000)
                        array_push($produk, $p);
                    break;
                default:
                    if (10000000 <= $hargaDiskon)
                        array_push($produk, $p);
                    break;
            }
        }
    }
}
if (isset($_GET['ruang'])) {
    if ($_GET['ruang'] != '') {
        $ruang = explode(" ", $_GET['ruang']);
        $produkLama = $produk;
        $produk = [];
        foreach ($ruang as $r) {
            foreach ($produkLama as $p) {
                if ($p['ruang_' . $r]) {
                    array_push($produk, $p);
                }
            }
        }
    }
}

// Keep search context and selected facets when navigating result pages.
$catalogPageUrl = static function (int $page) use ($catalogRoute, $isSmartSearch, $smartQuery): string {
    $params = [];
    if ($isSmartSearch && $smartQuery !== '') $params['q'] = $smartQuery;
    foreach (['koleksi', 'jenis', 'harga', 'ruang', 'varian'] as $key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') $params[$key] = $_GET[$key];
    }
    if ($page > 1) $params['pag'] = $page;
    return $catalogRoute . ($params ? '?' . http_build_query($params) : '');
};
$catalogWithoutPriceParams = [];
if ($isSmartSearch && $smartQuery !== '') $catalogWithoutPriceParams['q'] = $smartQuery;
foreach (['koleksi', 'jenis', 'ruang', 'varian'] as $facetKey) {
    if (isset($_GET[$facetKey]) && is_scalar($_GET[$facetKey]) && $_GET[$facetKey] !== '') {
        $catalogWithoutPriceParams[$facetKey] = (string) $_GET[$facetKey];
    }
}
$catalogRemovePriceUrl = $catalogRoute . ($catalogWithoutPriceParams ? '?' . http_build_query($catalogWithoutPriceParams) : '');

//nggabungin produk sejenis
$produkLama = $produk;
$produk = [];
$arrNamaProduk = [];
foreach ($produkLama as $p) {
    $namaKey = 'nama:' . strtolower((string) $p['nama']);
    if (!isset($arrNamaProduk[$namaKey])) {
        array_push($produk, $p);
        $arrNamaProduk[$namaKey] = true;
    }
}

$catalogTotalResults = count($produk);
$hitungPag = ceil(count($produk) / 10);
$pag = 1;
if (isset($_GET['pag'])) $pag = (int)$_GET['pag'];
$produkLama = array_slice($produk, ($pag - 1) * 10);
$produk = [];
for ($i = 0; $i < 10; $i++) {
    if (isset($produkLama[$i]))
        array_push($produk, $produkLama[$i]);
}

$seluruhFilter = [];
if (isset($_GET['koleksi'])) {
    $koleksiArr = explode(' ', $_GET['koleksi']);
    foreach ($koleksiArr as $k) {
        array_push($seluruhFilter, $k);
    }
}
if (isset($_GET['jenis'])) {
    $koleksiArr = explode(' ', $_GET['jenis']);
    foreach ($koleksiArr as $k) {
        array_push($seluruhFilter, $k);
    }
}
if (isset($_GET['ruang'])) {
    $koleksiArr = explode(' ', $_GET['ruang']);
    foreach ($koleksiArr as $k) {
        array_push($seluruhFilter, $k);
    }
}

?>
<div class="container d-flex justify-content-center ilena-catalog-page">
    <div class="konten baris-ke-kolom">
        <div id="catalogFilterSidebar" class="show-block-ke-hide container-filter-desktop is-collapsed" inert aria-hidden="true"
            style="position: sticky; top: 50px; height:fit-content">
            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false"
                aria-controls="collapseExample">
                Koleksi
            </div>
            <div class="collapse py-2" id="collapseExample">
                <?php foreach ($koleksiJenis['koleksi'] as $ind_k => $k) { ?>
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-1-<?= $ind_k ?>" class="inp-cbx filter" name="koleksi"
                        value="<?= str_replace(" ", "-", strtolower($k['nama'])); ?>"
                        <?= isset($_GET['koleksi']) ? (in_array(str_replace(" ", "-", strtolower($k['nama'])), explode(" ", $_GET['koleksi'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-1-<?= $ind_k ?>" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p><?= ucfirst($k['nama']); ?></p>
                    </label>
                </div>
                <?php } ?>
            </div>

            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="false"
                aria-controls="collapseExample1">
                Jenis
            </div>
            <div class="collapse py-2" id="collapseExample1">
                <?php foreach ($koleksiJenis['jenis'] as $ind_j => $j) { ?>
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-2-<?= $ind_j ?>" class="inp-cbx filter" name="jenis"
                        value="<?= str_replace(" ", "-", strtolower($j['nama'])); ?>"
                        <?= isset($_GET['jenis']) ? (in_array(str_replace(" ", "-", strtolower($j['nama'])), explode(" ", $_GET['jenis'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-2-<?= $ind_j ?>" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p><?= ucfirst($j['nama']); ?></p>
                    </label>
                </div>
                <?php } ?>
            </div>

            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample4" aria-expanded="false"
                aria-controls="collapseExample4">
                Room Set
            </div>
            <div class="collapse py-2" id="collapseExample4">
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-10" class="inp-cbx filter" name="ruang" value="tamu"
                        <?= isset($_GET['ruang']) ? (in_array('tamu', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-10" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Ruang Tamu</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-11" class="inp-cbx filter" name="ruang" value="keluarga"
                        <?= isset($_GET['ruang']) ? (in_array('keluarga', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-11" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Ruang Keluarga</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-12" class="inp-cbx filter" name="ruang" value="tidur"
                        <?= isset($_GET['ruang']) ? (in_array('tidur', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-12" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Ruang Tidur</p>
                    </label>
                </div>
            </div>

            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="false"
                aria-controls="collapseExample2">
                Harga
            </div>
            <div class="collapse py-2" id="collapseExample2">
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-5" class="inp-cbx filter" value="0"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("0", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-5" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                            </svg></span>
                        <p>Di bawah Rp 1.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-6" class="inp-cbx filter" value="1"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("1", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-6" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 1.000.000 – < Rp 3.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-7" class="inp-cbx filter" value="2"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("2", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-7" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 3.000.000 – < Rp 5.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-8" class="inp-cbx filter" value="3"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("3", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-8" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 5.000.000 – < Rp 10.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-9" class="inp-cbx filter" value="4"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("4", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-9" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 10.000.000 ke atas</p>
                    </label>
                </div>
            </div>
            <div class="d-flex gap-3 mt-2">
                <a class="btn-filter btn-lonjong">Terapkan</a>
                <?php if (!empty($_GET['koleksi']) || !empty($_GET['jenis']) || !empty($_GET['harga']) || !empty($_GET['ruang'])) { ?>
                <a class="btn-teks-aja" href="<?= $isSmartSearch ? '/find?' . http_build_query(['q' => $smartQuery]) : '/product'; ?>">Hapus Filter</a>
                <?php } ?>
            </div>
        </div>
        <div style="flex: 1;">
            <!-- <div style="position: absolute;" class="w-100"> -->
            <header class="ilena-catalog-intro <?= $hideGenericCatalogIntro ? 'ilena-catalog-intro--compact' : ''; ?>">
                <div>
                    <p class="ilena-catalog-intro__eyebrow"><?= $isSmartSearch ? 'PENCARIAN KATALOG' : 'ILENA <span>/</span> FURNITURE'; ?></p>
                    <?php if ($isSmartSearch || !$hideGenericCatalogIntro) { ?>
                    <h1><?= esc($isSmartSearch ? 'Temukan furniture pilihan' : $catalogHeading); ?></h1>
                    <?php } ?>
                    <?php if ($isSmartSearch) { ?>
                    <p class="ilena-catalog-intro__description">Kami mencarikan furniture yang paling mendekati kebutuhan dan gaya ruang Anda.</p>
                    <?php } ?>
                </div>
                <div class="ilena-catalog-intro__aside">
                    <?php if (session()->get('role') == '1') { ?>
                    <a href="/admin/addproduct" class="ilena-catalog-add">Tambah Produk <span aria-hidden="true">↗</span></a>
                    <?php } ?>
                    <p><strong><?= number_format($catalogTotalResults, 0, ',', '.'); ?></strong><span>DESAIN</span></p>
                </div>
            </header>

        <div class="hide-ke-show-block">
            <nav style="--bs-breadcrumb-divider: '/'; position: relative; height: <?= count($seluruhFilter) > 0 ? '80px' : '36px'; ?>"
                aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/product">Produk</a></li>
                </ol>
                <div class="container-badge-filter mb-2" style="position: absolute;">
                    <?php foreach ($seluruhFilter as $ind_f => $f) {
                        if ($f) { ?>
                    <div class="item-badge-filter">
                        <p><?= ucwords(str_replace('-', ' ', $f)); ?></p>
                    </div>
                    <?php }
                    } ?>
                </div>
            </nav>

            <input type="checkbox" id="container-filter" class="d-none">
            <label for="container-filter" class="w-100">
                <div class="d-flex gap-2 align-items-center w-100 justify-content-center btn-lonjong">
                    <i class="material-icons">filter_list</i>
                    <p class="m-0">Filter</p>
                </div>
            </label>
            <div class="container-filter mt-2">
                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExampleMobile" aria-expanded="false"
                    aria-controls="collapseExampleMobile">
                    Koleksi
                </div>
                <div class="collapse py-2" id="collapseExampleMobile">
                    <?php foreach ($koleksiJenis['koleksi'] as $ind_k => $k) { ?>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-1-<?= $ind_k ?>-hp" class="inp-cbx filter"
                            name="koleksi1" value="<?= str_replace(" ", "-", strtolower($k['nama'])); ?>"
                            <?= isset($_GET['koleksi']) ? (in_array(str_replace(" ", "-", strtolower($k['nama'])), explode(" ", $_GET['koleksi'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-1-<?= $ind_k ?>-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p><?= ucfirst($k['nama']); ?></p>
                        </label>
                    </div>
                    <?php } ?>
                </div>

                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample1Mobile" aria-expanded="false"
                    aria-controls="collapseExample1Mobile">
                    Jenis
                </div>
                <div class="collapse py-2" id="collapseExample1Mobile">
                    <?php foreach ($koleksiJenis['jenis'] as $ind_j => $j) { ?>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-2-<?= $ind_j ?>-hp" class="inp-cbx filter"
                            name="jenis1" value="<?= str_replace(" ", "-", strtolower($j['nama'])); ?>"
                            <?= isset($_GET['jenis']) ? (in_array(str_replace(" ", "-", strtolower($j['nama'])), explode(" ", $_GET['jenis'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-2-<?= $ind_j ?>-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p><?= ucfirst($j['nama']); ?></p>
                        </label>
                    </div>
                    <?php } ?>
                </div>

                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample4Mobile" aria-expanded="false"
                    aria-controls="collapseExample4Mobile">
                    Room Set
                </div>
                <div class="collapse py-2" id="collapseExample4Mobile">
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-10-hp" class="inp-cbx filter" name="ruang1"
                            value="tamu"
                            <?= isset($_GET['ruang']) ? (in_array('tamu', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-10-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Ruang Tamu</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-11-hp" class="inp-cbx filter" name="ruang1"
                            value="keluarga"
                            <?= isset($_GET['ruang']) ? (in_array('keluarga', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-11-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Ruang Keluarga</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-12-hp" class="inp-cbx filter" name="ruang1"
                            value="tidur"
                            <?= isset($_GET['ruang']) ? (in_array('tidur', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-12-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Ruang Tidur</p>
                        </label>
                    </div>
                </div>

                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample2Mobile" aria-expanded="false"
                    aria-controls="collapseExample2Mobile">
                    Harga
                </div>
                <div class="collapse py-2" id="collapseExample2Mobile">
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-5-hp" class="inp-cbx filter" value="0"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("0", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-5-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                                </svg></span>
                            <p>Di bawah Rp 1.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-6-hp" class="inp-cbx filter" value="1"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("1", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-6-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 1.000.000 – < Rp 3.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-7-hp" class="inp-cbx filter" value="2"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("2", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-7-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 3.000.000 – < Rp 5.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-8-hp" class="inp-cbx filter" value="3"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("3", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-8-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 5.000.000 – < Rp 10.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-9-hp" class="inp-cbx filter" value="4"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("4", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-9-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 10.000.000 ke atas</p>
                        </label>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-2">
                    <a class="btn-filter btn-lonjong">Terapkan</a>
                    <?php if (!empty($_GET['koleksi']) || !empty($_GET['jenis']) || !empty($_GET['harga']) || !empty($_GET['ruang'])) { ?>
                    <a class="btn-teks-aja" href="<?= $isSmartSearch ? '/find?' . http_build_query(['q' => $smartQuery]) : '/product'; ?>">Hapus Filter</a>
                    <?php } ?>
                </div>
            </div>
        </div>

            <?php if (session()->get('role') == '1') { ?>
            <p class="ilena-catalog-admin-note">Katalog produk</p>
            <?php } ?>
            <div class="d-flex gap-4 align-items-stretch mb-3 ilena-catalog-toolbar">
                <div style="width: fit-content;" class="show-flex-ke-hide justify-content-center align-items-center">
                    <button class="btn btn-sm btn-outline-dark d-flex align-items-center" type="button" data-filter-sidebar-toggle aria-controls="catalogFilterSidebar" aria-expanded="false" aria-label="Tampilkan filter" onclick="showFilterDesktop()">
                        <i class="material-icons" aria-hidden="true">arrow_forward_ios</i>
                        <p class="m-0" style="line-height: 7px;">Filter</p>
                    </button>
                </div>
                <div style="width: 1px; background-color:darkgrey;" class="show-block-ke-hide"></div>
                <div class="d-flex align-items-center">
                    <nav style="--bs-breadcrumb-divider: '/'; position: relative; <?= (isset($_GET['koleksi']) || isset($_GET['jenis']) || isset($_GET['ruang'])) ? 'height: 60px' : ''; ?>"
                        aria-label="breadcrumb" class="show-block-ke-hide">
                        <ol class="breadcrumb <?= count($seluruhFilter) > 0 ? 'mb-2' : 'm-0'; ?>">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/product">Produk</a></li>
                            <!-- <li class="breadcrumb-item">Meja TV</li> -->
                        </ol>
                        <div class="container-badge-filter" style="position: absolute;">
                            <?php foreach ($seluruhFilter as $ind_f => $f) {
                                if ($f) { ?>
                            <div class="item-badge-filter">
                                <p><?= ucwords(str_replace('-', ' ', $f)); ?></p>
                            </div>
                            <?php }
                            } ?>
                        </div>
                    </nav>
                </div>
            </div>
            <?php if ($isSmartSearch) { ?>
            <div class="ilena-smart-search-summary" role="status" aria-live="polite">
                <div><span class="ilena-smart-search-summary__label">Hasil pencarian</span><h2>“<?= esc($find); ?>”</h2></div>
                <p><strong><?= number_format($catalogTotalResults, 0, ',', '.'); ?></strong> produk ditemukan</p>
            </div>
            <?php if (!empty($smartFilters)) { ?>
            <div class="ilena-smart-search-chips" aria-label="Kriteria yang dikenali dari pencarian">
                <span class="ilena-smart-search-chips__label">Cocok dengan</span>
                <?php foreach ($smartFilters as $smartFilter) { ?><span class="badge rounded-pill text-bg-light"><?= esc($smartFilter); ?></span><?php } ?>
            </div>
            <?php } ?>
            <?php if (empty($produk)) { ?>
            <section class="ilena-smart-search-empty" aria-live="polite">
                <h2>Belum menemukan produk yang cocok</h2>
                <p>Coba kata kunci yang lebih umum atau pilih salah satu pencarian yang mendekati kebutuhan Anda.</p>
                <?php if (!empty($smartSuggestions)) { ?>
                <p class="ilena-smart-search-empty__label">Pencarian yang mungkin Anda maksud</p>
                <div class="ilena-smart-search-chips">
                    <?php foreach ($smartSuggestions as $suggestion) { ?>
                    <a href="<?= esc('/find?' . http_build_query(['q' => $suggestion['nama'] ?? '']), 'attr'); ?>"><?= esc($suggestion['nama'] ?? 'Lihat produk'); ?></a>
                    <?php } ?>
                </div>
                <?php } ?>
                <p class="mt-3 mb-0"><a href="/product">Lihat semua produk</a></p>
            </section>
            <?php } ?>
            <?php } ?>
            <?php if (!$isSmartSearch && $catalogTotalResults === 0) { ?>
            <section class="ilena-catalog-empty" role="status" aria-live="polite">
                <p class="ilena-catalog-empty__eyebrow">Belum ada hasil</p>
                <h2>Tidak ada produk yang cocok dengan kombinasi filter ini.</h2>
                <p><?= isset($_GET['harga']) ? 'Rentang harga dihitung dari harga setelah diskon. Coba rentang harga lain atau kurangi pilihan koleksi.' : ($hasAppliedCatalogFilters ? 'Coba kurangi pilihan koleksi atau jenis, atau reset filter untuk melihat seluruh katalog.' : 'Produk sedang tidak tersedia. Silakan periksa kembali beberapa saat lagi.'); ?></p>
                <div class="ilena-catalog-empty__actions">
                    <?php if (isset($_GET['harga'])) { ?><a href="<?= esc($catalogRemovePriceUrl, 'attr'); ?>">Hapus filter harga</a><?php } ?>
                    <?php if ($hasAppliedCatalogFilters) { ?><a href="<?= esc($isSmartSearch ? '/find?' . http_build_query(['q' => $smartQuery]) : '/product', 'attr'); ?>">Reset semua filter</a><?php } ?>
                </div>
            </section>
            <?php } ?>
            <div class="container-card1 ilena-catalog-grid">
                <?php foreach ($produk as $ind_p => $p) { ?>
                <?php $cardVersion = !empty($p['tgl_update']) ? (int) strtotime((string) $p['tgl_update']) : time(); ?>
                <div class="card1" data-product-card>
                    <div style="position: relative;">
                        <span class="card1-content-img-kiri"
                            <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                        <div class="d-flex flex-column gap-2 card1-content-img-kanan">
                            <?php if (session()->get('role') == '1') { ?>
                            <a class="card1-btn-img" href="/admin/editproduct/<?= $p['id'] ?>" title="Edit produk"><i class="material-icons">edit</i></a>
                            <?php }
                                if (in_array($p['id'], $wishlist)) { ?>
                            <form action="/delwishlist/<?= $p['id'] ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit" class="card1-btn-img" aria-label="Hapus <?= esc($p['nama'], 'attr'); ?> dari favorit" title="Hapus dari favorit"><i
                                        class="material-icons">favorite</i></button>
                            </form>
                            <?php } else { ?>
                            <form action="/addwishlist/<?= $p['id'] ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit" class="card1-btn-img" aria-label="Simpan <?= esc($p['nama'], 'attr'); ?> ke favorit" title="Simpan ke favorit"><i
                                        class="material-icons">favorite_border</i></button>
                            </form>
                            <?php } ?>
                            <form method="post" id="card<?= $ind_p ?>"
                                action="/addcart/<?= $p['id'] ?>/<?= rawurlencode(json_decode($p['varian'], true)[0]['nama']) ?>/1"><?= csrf_field(); ?><button class="card1-btn-img" aria-label="Tambah <?= esc($p['nama'], 'attr'); ?> ke keranjang" title="Tambah ke keranjang"><i class="material-icons">shopping_bag</i>
                                </button>
                            </form>
                        </div>
                        <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="gambar">
                            <?php
                                $cardVariantsForImage = json_decode($p['varian'] ?? '[]', true) ?: [];
                                $cardSlotsForImage = array_values(array_filter(array_map('trim', explode(',', (string) ($cardVariantsForImage[0]['urutan_gambar'] ?? '1')))));
                                $cardSlot = (string) ($cardSlotsForImage[0] ?? '1');
                                $cardFallback = base_url('product-cover/' . rawurlencode((string) $p['id'])) . '?slot=' . rawurlencode($cardSlot) . '&size=300&v=' . $cardVersion;
                                $cardCdnSrc = $productCdnImageUrl('products/300/' . rawurlencode((string) $p['id']) . '.webp', $cardVersion);
                                $cardLocalSrc = is_file(FCPATH . 'uploads/product-images/300/' . $p['id'] . '.webp')
                                    ? base_url('uploads/product-images/300/' . rawurlencode((string) $p['id']) . '.webp') . '?v=' . $cardVersion
                                    : (is_file(FCPATH . 'img/barang/300/' . $p['id'] . '.webp') ? base_url('img/barang/300/' . rawurlencode((string) $p['id']) . '.webp') . '?v=' . $cardVersion : '');
                                $cardLocal1000Relative = 'uploads/product-images/1000/' . $p['id'] . '-' . $cardSlot . '.webp';
                                $cardLegacy1000Relative = 'img/barang/1000/' . $p['id'] . '-' . $cardSlot . '.webp';
                                $cardLocal1000Src = is_file(FCPATH . $cardLocal1000Relative)
                                    ? base_url($cardLocal1000Relative) . '?v=' . $cardVersion
                                    : (is_file(FCPATH . $cardLegacy1000Relative) ? base_url($cardLegacy1000Relative) . '?v=' . $cardVersion : '');
                                $cardSlotCandidates = [];
                                foreach ($cardVariantsForImage as $variantForImage) {
                                    $cardSlotCandidates = array_merge($cardSlotCandidates, explode(',', (string) ($variantForImage['urutan_gambar'] ?? '')));
                                }
                                $cardLocalSlots = array_values(array_filter(array_unique(array_map('trim', $cardSlotCandidates)), static fn($slot) => $slot !== '' && is_file(FCPATH . 'uploads/product-images/1000/' . $p['id'] . '-' . $slot . '.webp')));
                                $cardCdnSrcset = $productImageCdn !== ''
                                    ? $cardCdnSrc . ' 300w, ' . $productCdnImageUrl('products/1000/' . rawurlencode((string) $p['id']) . '-' . rawurlencode($cardSlot) . '.webp', $cardVersion) . ' 1000w'
                                    : '';
                            ?>
                            <img class="img-pic" id="img<?= $ind_p ?>"
                                src="<?= esc($cardLocalSrc !== '' ? $cardLocalSrc : ($cardCdnSrc !== '' ? $cardCdnSrc : $productCoverUrl($p)), 'attr') ?>"
                                <?= $cardLocalSrc === '' && $cardCdnSrc !== '' ? 'data-image-fallback="' . esc($cardFallback, 'attr') . '"' : '' ?>
                                <?= $cardLocalSrc !== '' && $cardLocal1000Src !== '' ? 'srcset="' . esc($cardLocalSrc . ' 300w, ' . $cardLocal1000Src . ' 1000w', 'attr') . '"' : ($cardLocalSrc === '' && $cardCdnSrc !== '' ? 'srcset="' . esc($cardCdnSrcset, 'attr') . '"' : '') ?>
                                sizes="(max-width: 600px) 46vw, (max-width: 1100px) 30vw, 300px"
                                width="300" height="300" alt="<?= esc($p['nama'], 'attr') ?>"
                                loading="<?= $ind_p < 2 ? 'eager' : 'lazy' ?>" fetchpriority="<?= $ind_p === 0 ? 'high' : 'auto' ?>" decoding="async"
                                onload="this.classList.add('is-loaded');this.closest('.gambar')?.classList.add('is-image-loaded')"
                                onerror="if(this.dataset.imageFallback&&!this.dataset.fallbackTried){this.dataset.fallbackTried='1';this.removeAttribute('srcset');this.src=this.dataset.imageFallback;return;}this.classList.add('is-loaded');this.closest('.gambar')?.classList.add('is-image-loaded')">
                            <?php if (is_file(FCPATH . 'uploads/product-images/hover/' . $p['id'] . '.webp') || is_file(FCPATH . 'img/barang/hover/' . $p['id'] . '.webp') || !empty($p['gambar_hover'])) { ?>
                            <img class="img-pic-hover lazy-hover-product"
                                data-src="<?= esc(is_file(FCPATH . 'uploads/product-images/hover/' . $p['id'] . '.webp') ? (base_url('uploads/product-images/hover/' . rawurlencode((string) $p['id']) . '.webp') . '?v=' . $cardVersion) : ($productCdnImageUrl('products/hover/' . rawurlencode((string) $p['id']) . '.webp', $cardVersion) ?: (base_url('viewpichover/' . $p['id']) . '?v=' . $cardVersion)), 'attr') ?>"
                                data-image-fallback="<?= esc(base_url('viewpichover/' . rawurlencode((string) $p['id'])) . '?v=' . $cardVersion, 'attr') ?>"
                                onerror="if(this.dataset.imageFallback){this.onerror=null;this.src=this.dataset.imageFallback;}"
                                alt="<?= esc($p['nama'], 'attr') ?>" loading="lazy" decoding="async">
                            <?php } ?>
                        </a>
                    </div>
                    <div class="container-varian mb-1 d-flex">
                        <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                        <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>"
                            value="<?= esc($v['urutan_gambar'], 'attr') ?>-<?= esc($v['nama'], 'attr') ?>" aria-label="Warna <?= esc($v['nama'], 'attr') ?>" type="radio"
                            name="varian<?= $ind_p ?>">
                        <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>" title="<?= esc($v['nama'], 'attr') ?>"><span
                                style="background-color: <?= $v['kode'] ?>"></span></label>
                        <?php } ?>
                        <script>
                        const btnKeranjang<?= $ind_p ?>Elm = document.getElementById("card<?= $ind_p ?>");
                        const varian<?= $ind_p ?>Elm = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');
                        const productImageCdnBase<?= $ind_p ?> = <?= json_encode($productImageCdn, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
                        const localProductSlots<?= $ind_p ?> = <?= json_encode($cardLocalSlots, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
                        varian<?= $ind_p ?>Elm.forEach(elm => {
                            elm.addEventListener('change', (e) => {
                                const img<?= $ind_p ?>Elm = document.getElementById("img<?= $ind_p ?>");
                                const selectedSlot = e.target.value.split("-")[0].split(",")[0];
                                const fallbackImage = "<?= base_url('product-cover/' . rawurlencode((string) $p['id'])) ?>?slot=" + encodeURIComponent(selectedSlot) + "&v=<?= $cardVersion ?>";
                                img<?= $ind_p ?>Elm.removeAttribute('srcset');
                                img<?= $ind_p ?>Elm.onerror = productImageCdnBase<?= $ind_p ?> && !localProductSlots<?= $ind_p ?>.includes(selectedSlot) ? function () {
                                    this.onerror = null;
                                    this.src = fallbackImage;
                                } : null;
                                img<?= $ind_p ?>Elm.src = localProductSlots<?= $ind_p ?>.includes(selectedSlot)
                                    ? "<?= base_url('uploads/product-images/1000/' . rawurlencode((string) $p['id'])) ?>-" + encodeURIComponent(selectedSlot) + ".webp?v=<?= $cardVersion ?>"
                                    : productImageCdnBase<?= $ind_p ?>
                                    ? productImageCdnBase<?= $ind_p ?> + "/products/1000/<?= rawurlencode((string) $p['id']) ?>-" + encodeURIComponent(selectedSlot) + ".webp?v=<?= $cardVersion ?>"
                                    : fallbackImage;

                                btnKeranjang<?= $ind_p ?>Elm.action = "/addcart/<?= $p['id'] ?>/" + e
                                    .target
                                    .value.split("-")[1] + "/1";
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
            <!-- <div class="container-pag">
                <?php if ($pag > 1) { ?>
                <a class="item-pag"
                    href="<?= esc($catalogPageUrl($pag - 1), 'attr'); ?>"><i
                        class="material-icons">arrow_back_ios</i></a>
                <?php } ?>
                <?php for ($i = 0; $i < $hitungPag; $i++) { ?>
                <a class="item-pag <?= $pag == ($i + 1) ? 'active' : ''; ?>"
                    href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $i + 1; ?>"><?= $i + 1; ?></a>
                <?php } ?>
                <?php if ($pag < $hitungPag) { ?>
                <a class="item-pag"
                    href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $pag + 1; ?>"><i
                        class="material-icons">arrow_forward_ios</i></a>
                <?php } ?>
            </div> -->
            <div class="container-pag">
                <?php
                $start = max(1, $pag - 2); // Mulai dari dua halaman sebelumnya
                $end = min($hitungPag, $pag + 2); // Selesai di dua halaman berikutnya

                // Menampilkan tombol 'Prev'
                if ($pag > 1) { ?>
                <a class="item-pag"
                    href="<?= esc($catalogPageUrl($pag - 1), 'attr'); ?>" aria-label="Halaman sebelumnya"><i
                        class="material-icons">arrow_back_ios</i></a>
                <?php } ?>

                <?php
                // Menampilkan angka pagination dari $start sampai $end
                for ($i = $start; $i <= $end; $i++) { ?>
                <a class="item-pag <?= $pag == $i ? 'active' : ''; ?>"
                    href="<?= esc($catalogPageUrl($i), 'attr'); ?>"><?= $i; ?></a>
                <?php } ?>

                <?php
                // Menampilkan tombol 'Next'
                if ($pag < $hitungPag) { ?>
                <a class="item-pag"
                    href="<?= esc($catalogPageUrl($pag + 1), 'attr'); ?>" aria-label="Halaman berikutnya"><i
                        class="material-icons">arrow_forward_ios</i></a>
                <?php } ?>
            </div>

            <!-- </div> -->
            <!-- Artikel kategori -->
            <?php if (isset($_GET['jenis'])) {
                switch ($_GET['jenis']) {
                    case 'bookshelf': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Simpan Buku Lebih Rapi di Bookshelf</h5>
                    <p class="text-justify">Bookshelf adalah salah satu furniture yang bisa membuat ruangan jadi lebih
                        menarik dan membuat koleksi buku-buku tertata rapi dalam satu tempat pada sebuah bookcase.
                        Umumnya, desain rak buku mirip dengan lemari, hanya saja biasanya tidak memiliki penutup atau
                        kuncian pintu dan memiliki banyak sekat-sekat. Bentuknya yang terbuka inilah, yang bisa menjadi
                        space menarik untuk memajang koleksi buku dengan lebih aman dan rapi agar tidak mudah rusak.
                        Selain itu, desain ini juga akan memudahkan saat hendak mengambil atau menyimpan kembali buku di
                        rak.</p>

                    <h5>Macam-Macam Model Bookshelves</h5>
                    <p class="text-justify">Sekarang ini, rak buku memiliki banyak model yang menarik dengan
                        berbagai aksen unik yang menjadi daya tarik dari furniture satu ini. Beragam model ini juga
                        dipengaruhi dengan kebutuhan konsumen yang beragam pula. Preferensi mulai dari model, bahan, dan
                        ukuran ini membuat para pengrajin seperti Ilena Furniture sebagai produsen semangat untuk
                        mengembangkan produk rak buku. salah satu produknya adalah bookshelf Ilena Cabana yang memiliki
                        4 tingkat space rak buku plus sebuah laci di bagian bawahnya. Selain modelnya yang stylish,
                        koleksi bookcase Ilena ini juga terbuat dari bahan-bahan berkualitas yang kokoh dan awet lama.
                        Bahan kayu mahoni dengan dikombinasikan MDF grade A serta laminasi veneer jati yang terkenal
                        dengan serat kayu khasnya yang unik. Pertimbangan inilah yang menjadikan furniture unggulan yang
                        dapat digunakan sebagaimana fungsinya dalam jangka waktu yang lama.
                    <p>
                    <h5>Cara Perawatan Yang Mudah</h5>
                    <p class="text-justify mb-1">Beberapa tips & trik menjaga rak buku agar tetap bersih dan rapi ini
                        bisa ditiru secara
                        mandiri
                        tanpa bantuan profesional:</p>
                    <ol>
                        <li>Bersihkan secara rutin dan berkala</li>
                        <p class="text-justify mb-1">Sama seperti pada furniture lain pada umumnya, bookshelves juga
                            harus mendapat perhatian
                            dengan cara dibersihkan secara rutin dan berkala. Hal ini dikarenakan tidak menutup
                            kemungkinan jika bookcase kotor karena debu yang menutup sehingga bisa mengotori buku
                            koleksi dan mengurangi estetika di bookcase itu sendiri. Jadwalkan secara rutin pembersihan,
                            semisal seminggu sekali untuk hasil yang optimal.</p>
                        <li>Kelompokkan Jenis Buku</li>
                        <p class="text-justify mb-1">Jika sudah bisa memastikan rak buku bersih, kini saatnya untuk
                            menata koleksi buku yang dimiliki. Susun buku dengan rapih dan berurutan sesuai dengan abjad
                            dari penulis, judul, atau cara lainnya untuk mempermudah saat sedang mencari dan menaruh
                            kembali buku pada tempatnya.</p>
                        <li>Pakai Penyangga Buku</li>
                        <p class="text-justify mb-1">Jika dirasa rak buku masih kurang membuat buku-buku terlihat rapi,
                            maka cara mudahnya adalah
                            dengan menambahkan penyangga buku untuk memisahkan koleksi buku dan memastikan kolekis buku
                            tersimpan dengan tegak dan terlihat lebih rapi. Penyangga buku ini juga bisa dijadikan sekat
                            tambahan dan space kosong sampingnya bisa dimanfaatkan sebagai tempat payangan foto, atau
                            barang-barang kesayangan seperti akrilik atau lainnya.</p>
                    </ol>
                    <p class="text-justify">Itulah beberapa cara merawat rak buku yang sederhana dan dapat ditiru
                        secara mandiri di rumah.
                        Dengan ini, dapat memastikan bookcase tetap terjaga kebersihan dan kerapihannya sehingga dapat
                        menambah estetika ruang.
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'dresser-3-drawer dresser-4-drawer dresser-5-drawer dresser-6-drawer dresser-tall-cabinet': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Kenapa Perlu Punya Drawer?</h5>
                    <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                        berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                        opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                        bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                        barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                        memiliki drawer dalam hunian:</p>
                    <ol>
                        <li>Tempat Praktis Menyimpan Barang</li>
                        <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                            menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                            untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                            Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                            ada tempat khususnya sendiri.</p>
                        <li>Hemat Ruang</li>
                        <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                            menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                            utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                        <li>Memaksimalkan Keindahan Ruang</li>
                        <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                            bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                            ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                            untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                            kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                        <li>Furniture Multifungsi</li>
                        <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                            membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                            dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                            dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                            jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                            nyamuk atau serangga lain di dalamnya.</p>
                        <li>Mudah Diakses</li>
                        <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                            dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                            Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                            perlu banyak repot.</p>
                    </ol>
                    <h5>Temukan Berbagai Model Dresser Stylish</h5>
                    <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                        hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                        cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                        material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                        menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                        memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                        Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                        dari member Kami!
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'dresser-3-drawer': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Kenapa Perlu Punya Drawer?</h5>
                    <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                        berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                        opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                        bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                        barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                        memiliki drawer dalam hunian:</p>
                    <ol>
                        <li>Tempat Praktis Menyimpan Barang</li>
                        <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                            menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                            untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                            Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                            ada tempat khususnya sendiri.</p>
                        <li>Hemat Ruang</li>
                        <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                            menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                            utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                        <li>Memaksimalkan Keindahan Ruang</li>
                        <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                            bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                            ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                            untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                            kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                        <li>Furniture Multifungsi</li>
                        <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                            membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                            dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                            dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                            jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                            nyamuk atau serangga lain di dalamnya.</p>
                        <li>Mudah Diakses</li>
                        <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                            dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                            Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                            perlu banyak repot.</p>
                    </ol>
                    <h5>Temukan Berbagai Model Dresser Stylish</h5>
                    <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                        hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                        cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                        material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                        menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                        memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                        Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                        dari member Kami!
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'dresser-4-drawer': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Kenapa Perlu Punya Drawer?</h5>
                    <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                        berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                        opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                        bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                        barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                        memiliki drawer dalam hunian:</p>
                    <ol>
                        <li>Tempat Praktis Menyimpan Barang</li>
                        <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                            menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                            untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                            Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                            ada tempat khususnya sendiri.</p>
                        <li>Hemat Ruang</li>
                        <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                            menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                            utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                        <li>Memaksimalkan Keindahan Ruang</li>
                        <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                            bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                            ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                            untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                            kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                        <li>Furniture Multifungsi</li>
                        <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                            membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                            dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                            dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                            jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                            nyamuk atau serangga lain di dalamnya.</p>
                        <li>Mudah Diakses</li>
                        <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                            dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                            Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                            perlu banyak repot.</p>
                    </ol>
                    <h5>Temukan Berbagai Model Dresser Stylish</h5>
                    <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                        hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                        cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                        material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                        menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                        memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                        Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                        dari member Kami!
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'dresser-5-drawer': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Kenapa Perlu Punya Drawer?</h5>
                    <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                        berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                        opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                        bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                        barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                        memiliki drawer dalam hunian:</p>
                    <ol>
                        <li>Tempat Praktis Menyimpan Barang</li>
                        <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                            menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                            untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                            Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                            ada tempat khususnya sendiri.</p>
                        <li>Hemat Ruang</li>
                        <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                            menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                            utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                        <li>Memaksimalkan Keindahan Ruang</li>
                        <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                            bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                            ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                            untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                            kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                        <li>Furniture Multifungsi</li>
                        <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                            membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                            dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                            dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                            jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                            nyamuk atau serangga lain di dalamnya.</p>
                        <li>Mudah Diakses</li>
                        <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                            dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                            Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                            perlu banyak repot.</p>
                    </ol>
                    <h5>Temukan Berbagai Model Dresser Stylish</h5>
                    <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                        hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                        cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                        material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                        menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                        memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                        Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                        dari member Kami!
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'dresser-6-drawer': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Kenapa Perlu Punya Drawer?</h5>
                    <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                        berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                        opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                        bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                        barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                        memiliki drawer dalam hunian:</p>
                    <ol>
                        <li>Tempat Praktis Menyimpan Barang</li>
                        <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                            menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                            untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                            Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                            ada tempat khususnya sendiri.</p>
                        <li>Hemat Ruang</li>
                        <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                            menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                            utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                        <li>Memaksimalkan Keindahan Ruang</li>
                        <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                            bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                            ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                            untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                            kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                        <li>Furniture Multifungsi</li>
                        <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                            membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                            dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                            dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                            jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                            nyamuk atau serangga lain di dalamnya.</p>
                        <li>Mudah Diakses</li>
                        <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                            dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                            Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                            perlu banyak repot.</p>
                    </ol>
                    <h5>Temukan Berbagai Model Dresser Stylish</h5>
                    <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                        hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                        cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                        material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                        menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                        memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                        Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                        dari member Kami!
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'king-bed queen-bed single-bed': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Manfaat Tidur Pakai Divan</h5>
                    <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                        kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                    <ol>
                        <li>Memperlancar Sirkulasi udara</li>
                        <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                            baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                            disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                            menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                        <li>Lebih Baik bagi Kesehatan</li>
                        <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                            jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                            diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                            kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                        </p>
                        <li>Sebagai Tempat Penyimpanan Tambahan</li>
                        <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                            Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                            hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                        <li>Memaksimalkan Estetika Kamar Tidur</li>
                        <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                            estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                            selaras dengan tema dalam kamar tidur.</p>
                    </ol>
                    <h5>Rekomendasi Ukuran Bed</h5>
                    <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                        salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                    </p>
                    <ol>
                        <li>Single Size (90 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                            kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                            tempat.</p>
                        <li>Double Size (120 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                            bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                            terbatas.
                        </p>
                        <li>Queen Size (160 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                            mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                            space untuk bergerak leluasa.</p>
                        <li>King Size (180 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                            dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                            lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                        <li>Super King Size (200 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                            tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                            luas paling besar diantara kamar tidur lain.</p>
                    </ol>
                    <h5>Belanja Ranjang Tidur Minimalis</h5>
                    <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                        minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                        mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                        biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                        ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                        estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                        di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                        website ini.
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'king-bed': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Manfaat Tidur Pakai Divan</h5>
                    <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                        kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                    <ol>
                        <li>Memperlancar Sirkulasi udara</li>
                        <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                            baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                            disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                            menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                        <li>Lebih Baik bagi Kesehatan</li>
                        <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                            jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                            diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                            kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                        </p>
                        <li>Sebagai Tempat Penyimpanan Tambahan</li>
                        <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                            Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                            hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                        <li>Memaksimalkan Estetika Kamar Tidur</li>
                        <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                            estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                            selaras dengan tema dalam kamar tidur.</p>
                    </ol>
                    <h5>Rekomendasi Ukuran Bed</h5>
                    <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                        salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                    </p>
                    <ol>
                        <li>Single Size (90 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                            kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                            tempat.</p>
                        <li>Double Size (120 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                            bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                            terbatas.
                        </p>
                        <li>Queen Size (160 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                            mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                            space untuk bergerak leluasa.</p>
                        <li>King Size (180 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                            dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                            lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                        <li>Super King Size (200 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                            tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                            luas paling besar diantara kamar tidur lain.</p>
                    </ol>
                    <h5>Belanja Ranjang Tidur Minimalis</h5>
                    <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                        minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                        mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                        biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                        ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                        estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                        di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                        website ini.
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'queen-bed': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Manfaat Tidur Pakai Divan</h5>
                    <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                        kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                    <ol>
                        <li>Memperlancar Sirkulasi udara</li>
                        <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                            baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                            disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                            menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                        <li>Lebih Baik bagi Kesehatan</li>
                        <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                            jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                            diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                            kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                        </p>
                        <li>Sebagai Tempat Penyimpanan Tambahan</li>
                        <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                            Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                            hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                        <li>Memaksimalkan Estetika Kamar Tidur</li>
                        <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                            estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                            selaras dengan tema dalam kamar tidur.</p>
                    </ol>
                    <h5>Rekomendasi Ukuran Bed</h5>
                    <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                        salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                    </p>
                    <ol>
                        <li>Single Size (90 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                            kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                            tempat.</p>
                        <li>Double Size (120 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                            bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                            terbatas.
                        </p>
                        <li>Queen Size (160 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                            mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                            space untuk bergerak leluasa.</p>
                        <li>King Size (180 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                            dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                            lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                        <li>Super King Size (200 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                            tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                            luas paling besar diantara kamar tidur lain.</p>
                    </ol>
                    <h5>Belanja Ranjang Tidur Minimalis</h5>
                    <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                        minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                        mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                        biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                        ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                        estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                        di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                        website ini.
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'single-bed': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Manfaat Tidur Pakai Divan</h5>
                    <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                        kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                    <ol>
                        <li>Memperlancar Sirkulasi udara</li>
                        <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                            baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                            disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                            menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                        <li>Lebih Baik bagi Kesehatan</li>
                        <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                            jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                            diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                            kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                        </p>
                        <li>Sebagai Tempat Penyimpanan Tambahan</li>
                        <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                            Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                            hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                        <li>Memaksimalkan Estetika Kamar Tidur</li>
                        <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                            estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                            selaras dengan tema dalam kamar tidur.</p>
                    </ol>
                    <h5>Rekomendasi Ukuran Bed</h5>
                    <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                        salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                    </p>
                    <ol>
                        <li>Single Size (90 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                            kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                            tempat.</p>
                        <li>Double Size (120 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                            bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                            terbatas.
                        </p>
                        <li>Queen Size (160 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                            mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                            space untuk bergerak leluasa.</p>
                        <li>King Size (180 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                            dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                            lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                        <li>Super King Size (200 x 200 cm)</li>
                        <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                            tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                            luas paling besar diantara kamar tidur lain.</p>
                    </ol>
                    <h5>Belanja Ranjang Tidur Minimalis</h5>
                    <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                        minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                        mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                        biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                        ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                        estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                        di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                        website ini.
                    </p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'coffee-table': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Mengapa Perlu Miliki Coffee Table?</h5>
                    <p class="text-justify">Apabila menyebut kata “coffee table” maka yang pertama terbesit adalah meja
                        untuk meletakkan cangkir kopi. Meskipun tidak salah, akan tetapi coffee table memiliki fungsi
                        yang lebih daripada hanya sebagai tempat meletakkan kopi saja. Pada dasarnya, meja ini biasa
                        ditempatkan di area ruang tamu sehingga bisa disebut juga sebagai meja tamu, meja sofa, meja
                        tatami, meja lounge, living room table, tea table dan masih banyak lagi sebutan lainnya.</p>
                    <p class="text-justify">Center table pada umumnya digunakan untuk menambahkan rasa nyaman saat duduk
                        di sofa untuk menunggu sesuatu atau bercengkrama dengan keluarga, sahabat, hingga kolega. Di
                        meja lounge ini bisa juga untuk meletakkan barang selain kopi seperti camilan, buku, remote TV,
                        vas bunga, dan aksesoris lainnya.</p>
                    <h5>Tips Memilih Meja Kopi yang Tetap dengan Kebutuhan</h5>
                    <p class="text-justify">Karena fungsinya yang mengedepankan kenyamanan, maka beberapa hal ini perlu
                        dipertimbangkan agar mendapatkan ukuran dan desain meja lounge yang sesuai dengan kebutuhan.</p>
                    <ol>
                        <li>Pilih Meja Tamu Sesuai dengan Fungsinya</li>
                        <p class="text-justify mb-1">Meja kopi bukan hanya sebagai tempat meletakkan minuman atau
                            makanan ringan, tetapi juga bisa berfungsi lebih dari itu. Jika kamu sering menerima tamu,
                            pilih meja dengan permukaan yang cukup luas agar nyaman digunakan. Jika ingin yang lebih
                            praktis, pilih living room table dengan rak tambahan di bawahnya untuk menyimpan majalah,
                            buku, atau remote TV. Bagi yang memiliki ruang tamu minimalis, center table multifungsi
                            dengan laci atau desain lipat bisa menjadi pilihan terbaik.</p>
                        <li>Perhatikan Ukuran dan Dimensi Meja</li>
                        <p class="text-justify mb-1">Jangan sampai center table yang dipilih malah membuat ruang tamu
                            terasa sempit atau tidak proporsional. Pastikan ukurannya sesuai dengan luas ruangan dan
                            tinggi sofa agar nyaman digunakan. Sebagai patokan umum, meja tatami idealnya memiliki
                            tinggi sekitar 40-50 cm dan panjang sekitar setengah atau dua pertiga dari panjang sofa.
                            Jika ruang tamu kecil, pilih living room table dengan desain minimalis dan ukuran yang lebih
                            compact agar tidak mengganggu mobilitas.</p>
                        <li>Selaraskan Bentuk dengan Luas Ruang Tamu</li>
                        <p class="text-justify mb-1">Bentuk tea table juga harus disesuaikan dengan tata letak dan luas
                            ruangan. Meja berbentuk persegi atau persegi panjang cocok untuk ruang tamu yang luas dan
                            memiliki sofa panjang. Jika ruang tamu cukup kecil atau memiliki banyak perabot, meja
                            berbentuk bundar atau oval bisa menjadi pilihan karena tidak memiliki sudut tajam dan lebih
                            aman, terutama jika ada anak kecil di rumah.</p>
                        <li>Pastikan Pilih yang Sesuai dengan Tema Ruangan</li>
                        <p class="text-justify mb-1">Meja sofa yang selaras dengan tema ruangan akan membuat tampilan
                            lebih harmonis dan estetis. Jika ruang tamu bergaya minimalis, pilih meja dengan desain
                            simpel dan warna netral seperti putih, hitam, atau kayu alami. Untuk tema klasik atau
                            vintage, meja tatami dengan material kayu solid dan ukiran detail bisa menjadi pilihan. Jika
                            suka tampilan modern dan elegan, meja dengan kombinasi kaca dan logam bisa memberikan kesan
                            mewah.</p>
                    </ol>
                    <h5>Beli Meja Kopi di Ilena Furniture</h5>
                    <p class="text-justify">Jika sedang berencana membeli furniture coffee table, Anda berada di tempat
                        yang tepat! Karena di Ilena furniture Anda bisa menemukan berbagai model meja sofa dengan
                        berbagai ukuran dan desain yang sesuai dengan selera. Mulai dari tea table dengan modern,
                        minimalis, industrial, japandi, etnic, dan lain sebagainya.</p>
                    <p class="text-justify">Temukan berbagai produk pilihan dengan kualitas terbaik dengan harga yang
                        terbaik pula. Makin hemat dengan gabung jadi member Ilena dan dapatkan penawaran khusus hanya
                        untuk Anda!</p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'console-table': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <p class="text-justify">Console table atau entryway table merupakan meja kecil yang biasanya akan
                        dijumpai pada area dekat dinding serta menjadi bagian dari dekorasi ruangan. Tidak hanya sekedar
                        digunakan untuk tempat penyimpanan saja, akan tetapi meletakkan meja konsol di ruangan bisa
                        menaikkan value ruang menjadi lebih dari sebelumnya. Karena pada dasarnya setiap ruang berharga
                        dengan fungsinya masing-masing, hall table mempertegas dengan look yang lebih bergengsi dari
                        ruangan tersebut. Mulai dari ruang tamu, lorong, atau bahkan tempat tidur. Furniture entryway
                        table ini bisa melebur dengan indah menjadi bagian dari dekorasi ruangan selain dari fungsinya
                        sebagai penambah area penyimpanan saja.</p>
                    <h5>Tips Memilih Console Table</h5>
                    <p class="text-justify">Memilih sofa table yang tepat memerlukan perhatian terhadap beberapa aspek
                        penting. Berikut adalah tips untuk mendapatkan hall table yang sesuai dengan gaya dan kebutuhan
                        hunian.</p>
                    <ol>
                        <li>Sesuaikan dengan Gaya Interior</li>
                        <p class="text-justify mb-1">Meja foyer harus selaras dengan konsep interior rumah. Jika rumah
                            bergaya klasik, pilih meja dengan ukiran detail dan material kayu berkualitas tinggi seperti
                            mahoni atau jati. Untuk gaya modern minimalis, meja dengan desain simpel dan material yang
                            tepat seperti kayu. Sementara itu, bagi yang menyukai estetika kontemporer, perpaduan
                            material marmer dan besi bisa memberikan kesan eksklusif.</p>
                        <li>Perhatikan Ukuran dan Proporsi</li>
                        <p class="text-justify mb-1">Ukuran sofa table harus seimbang dengan ruangan tempatnya
                            ditempatkan. Meja yang terlalu besar dapat membuat ruangan terasa sempit, sedangkan meja
                            yang terlalu kecil bisa tampak tenggelam dalam dekorasi. Pastikan tinggi meja tidak melebihi
                            90 cm agar tetap proporsional dan nyaman digunakan.</p>
                        <li>Pilih Material Berkualitas</li>
                        <p class="text-justify mb-1">Kualitas material sangat menentukan daya tahan dan estetika accent
                            table. Material seperti kayu solid ataupun engineering wood untuk memberikan kesan hangat
                            dan klasik, sementara marmer memberikan tampilan mewah dan eksklusif. Jika menginginkan
                            sentuhan industrial, accent table dengan kombinasi kayu dan besi bisa menjadi pilihan yang
                            menarik.</p>
                        <li>Fungsionalitas yang Optimal</li>
                        <p class="text-justify mb-1">Selain sebagai elemen dekoratif, hall table juga bisa berfungsi
                            sebagai tempat penyimpanan tambahan. Pilih meja dengan laci atau rak terbuka jika ingin
                            menyimpan barang seperti kunci, buku, atau aksesoris dekoratif. Jika digunakan sebagai meja
                            rias, pastikan ada ruang cukup untuk menempatkan cermin dan perlengkapan kecantikan.</p>
                        <li>Warna dan Finishing yang Elegan</li>
                        <p class="text-justify mb-1">Pemilihan warna dan finishing yang tepat akan memperkuat kesan
                            mewah pada meja foyer. Warna netral seperti putih, hitam, atau abu-abu memberikan kesan
                            modern dan elegan. Sementara itu, finishing glossy atau matte bisa dipilih sesuai preferensi
                            pribadi untuk menyesuaikan dengan keseluruhan tampilan ruangan.</p>
                        <li>Tambahan Dekorasi Yang Selaras</li>
                        <p class="text-justify mb-1">Untuk mempercantik tampilan sofa table, tambahkan dekorasi yang
                            sesuai seperti vas bunga, lampu meja, atau cermin dinding. Pastikan elemen dekoratif ini
                            tetap proporsional dan tidak mengganggu fungsi utama meja foyer tersebut.</p>
                    </ol>
                    <h5>Beli Console Table Terpercaya dan Terjamin Kualitasnya</h5>
                    <p class="text-justify">Apabila sedang mencari referensi produk accent table dengan desain dan
                        material yang berkualitas dan sudah teruji di pasar internasional, Ilena furniture adalah
                        pilihan tepat! Hanya dibuat dari bahan berkualitas yang sudah teruji dan ramah lingkungan.
                        Desain yang dibuat merupakan mahakarya yang diperuntukkan untuk menyempurnakan keindahan
                        interior ruangan Anda.</p>
                    <p class="text-justify">Dapatkan untung lebih seru dengan join member Ilena dan klaim berbagai
                        kesempatan berharga dalam berbelanja hanya dengan melakukan pembelian di website Ilena
                        Furniture.</p>
                    <p class="text-justify">Lihat produk entryway table dan cari yang sesuai dengan tema serta
                        preferensi masing-masing. Karena kami percaya, setiap ruang melambangkan suatu identitas dari
                        pemilik ruangan tersebut.</p>
                    <p class="text-justify">Jadikan lebih indah bersama dengan Ilena Furniture.</p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                    case 'side-table': ?>
            <hr class="my-5">
            <div class="container">
                <div class="my-3 container-meta">
                    <div class="overlay-meta"></div>
                    <h5>Pengertian Meja Nakas</h5>
                    <p class="text-justify">Meja nakas atau yang dikenal juga sebagai side table adalah salah satu
                        furniture yang biasanya diletakkan pada samping tempat tidur. Meja ini memiliki banyak fungsi
                        seperti meletakkan barang-barang kecil yang diperlukan sebelum tidur, hingga aksesoris tambahan
                        untuk percantik kamar. Beberapa hal yang harus diperhatikan saat memilih nakas minimalis:</p>
                    <ol>
                        <li>Ukuran meja</li>
                        <p class="text-justify mb-1">Side table biasanya tersedia dalam ukuran kecil dan sederhana
                            supaya bisa hemat tempat</p>
                        <li>Tinggi meja</li>
                        <p class="text-justify mb-1">Pada umumnya tinggi end table berkisar dengan ketinggian kasur. Hal
                            ini ditujukan agar saat menaruh dan menyimpan barang di nightstand table, dapat dengan mudah
                            dalam jangkauan tangan saat sedang di atas tempat tidur.</p>
                        <li>Bentuk</li>
                        <p class="text-justify mb-1">Model meja ini sangatlah beragam walaupun ukurannya yang cukup
                            ramping. Mulai dari persegi panjang, bulat, ataupun persegi.</p>
                        <li>Material pembuatan</li>
                        <p class="text-justify mb-1">Bahan yang umumnya digunakan untuk membuat nakas minimalis ini bisa
                            berasal dari bahan kayu, metal, kaca, plastik, maupun kombinasi.</p>
                    </ol>
                    <h5>Dimana Meja Nakas dapat Difungsikan?</h5>
                    <p class="text-justify">Secara mudahnya, end table merupakan salah satu furniture simpel yang bisa
                        dimanfaatkan di berbagai ruangan sesuai dengan kebutuhan. Berikut ini rekomendasi ruangan yang
                        cocok dijadikan tempat menaruh furniture ini:</p>
                    <ol>
                        <li>Kamar Tidur</li>
                        <p class="text-justify mb-1">Bedside table dengan permukaan atau top table yang mudah dijangkau
                            saat menyimpan barang menjadikan nightstand cocok diletakkan pada samping tempat tidur.
                            Barang-barang seperti buku, kacamata, handphone, air minum, atau lainnya bisa dengan mudah
                            diambil jika barang-barang tersebut berada di jangkauan tangan. Selain itu bedside table
                            juga bisa dijadikan tempat menaruh lampu tidur untuk pencahayaan tambahan Tak hanya itu
                            saja, aksesoris seperti frame foto,jam alarm, vas bunga atau tanaman kecil juga bisa
                            diletakkan pada meja ini.</p>
                        <li>Ruang Tamu</li>
                        <p class="text-justify mb-1">Bedside table juga bisa jadi opsi terbaik untuk diletakkan pada
                            ruang tamu sebagai tempat bersantai. Meja ini bisa digunakkan meletakkan minuman atau
                            camilan supaya mudah dijangkau. Tak hanya itu, juga bisa untuk menaruh remot TV, remot AC,
                            majalah, atau koleksi buku di side table. Selain menampilkan kesan rapi, nightstand ini juga
                            bisa dijadikan space untuk menambah estetika dalam ruangan.</p>
                        <li>Ruang Kerja</li>
                        <p class="text-justify mb-1">Lamp table bisa juga dijadikan tempat untuk menyimpan banyak barang
                            yang menunjang pekerjaan atau sekolah. Mulai dari alat tulis, catatan, alat tulis, atau
                            koleksi buku yang sering digunakan saat bekerja. Penambahan lampu belajar di lamp table juga
                            bisa jadi ide yang bagus untuk meningkatkan produktivitas kerja sehingga pekerjaan dapat
                            selesai lebih cepat. </p>
                        <li>Dapur</li>
                        <p class="text-justify mb-1">Sekilas mungkin terdengar agak aneh, tapi jika berani untuk
                            menambah nakas minimalis ini mencoba maka akan timbul suasana yang berbeda dan lebih segar
                            di mata. Jadi tidak hanya menambah tempat penyimpanan barang saja, tapi lamp table ini bisa
                            pula menambah kesan estetika pada ruang dapur.</p>
                    </ol>
                    <h5>Cari Meja Nakas di Ilena Furniture</h5>
                    <p class="text-justify">Apabila Anda sedang mencari referensi model end table dengan gaya modern dan
                        minimalis terbaru. Terbuat dari bahan premium pilihan berupa engineering wood maupun metal yang
                        awet lama sehingga cocok untuk penggunaan lama. Anda bisa memilih berbagai suasana mulai dari
                        modern, minimalis, industrial, japandi, dan berbagai tema ruangan lainnya yang bisa dipadukan
                        dengan furniture dari Ilena. Dibandrol dengan kisaran harga yang cukup terjangkau sehingga
                        sangat worth it untuk pembelian dalam jumlah banyak.</p>
                    <p class="text-justify">Jadilah bagian dari teman Ilena dan dapatkan berbagai keuntungan membership
                        yang tersedia terbatas hanya untuk Anda.</p>
                </div>
            </div>
            <div class=" d-flex justify-content-center mb-5">
                <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
            </div>
            <?php break;
                }
            } ?>
        </div>
    </div>
</div>
<script>
const btnFilterElm = document.querySelectorAll('.btn-filter');
const filterKoleksiElm = document.querySelectorAll('input[name="koleksi"]')
const filterJenisElm = document.querySelectorAll('input[name="jenis"]')
const filterHargaElm = document.querySelectorAll('input[name="harga"]')
const filterRuangElm = document.querySelectorAll('input[name="ruang"]')
const filterKoleksi1Elm = document.querySelectorAll('input[name="koleksi1"]')
const filterJenis1Elm = document.querySelectorAll('input[name="jenis1"]')
const filterHarga1Elm = document.querySelectorAll('input[name="harga1"]')
const filterRuang1Elm = document.querySelectorAll('input[name="ruang1"]')
let urlCur = <?= json_encode($catalogRoute, JSON_UNESCAPED_SLASHES); ?>;
const smartSearchQuery = <?= json_encode($isSmartSearch ? $smartQuery : '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
let koleksiParam = ''
let jenisParam = ''
let hargaParam = ''
let ruangParam = ''
let bukaMeta = false;

function showFilterDesktop() {
    const containerFilterDesktopElm = document.querySelector('.container-filter-desktop');
    const toggle = document.querySelector('[data-filter-sidebar-toggle]');
    if (!containerFilterDesktopElm || !toggle) return;
    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
    containerFilterDesktopElm.classList.toggle('is-collapsed', isExpanded);
    toggle.setAttribute('aria-expanded', String(!isExpanded));
    containerFilterDesktopElm.toggleAttribute('inert', isExpanded);
    containerFilterDesktopElm.setAttribute('aria-hidden', String(isExpanded));
    toggle.setAttribute('aria-label', isExpanded ? 'Tampilkan filter' : 'Sembunyikan filter');
    const icon = toggle.querySelector('.material-icons');
    if (icon) icon.textContent = isExpanded ? 'arrow_forward_ios' : 'arrow_back_ios';
}

btnFilterElm.forEach(elm => {
    elm.addEventListener('click', () => {
        koleksiParam = ''
        jenisParam = ''
        hargaParam = ''
        ruangParam = ''
        console.log(window.innerWidth)
        if (window.innerWidth > 600) {
            filterKoleksiElm.forEach((koleksiElm) => {
                if (koleksiElm.checked) {
                    if (koleksiParam == '')
                        koleksiParam += koleksiElm.value
                    else
                        koleksiParam += ' ' + koleksiElm.value
                }
            });
            filterJenisElm.forEach((jenisElm) => {
                if (jenisElm.checked) {
                    if (jenisParam == '')
                        jenisParam += jenisElm.value
                    else
                        jenisParam += ' ' + jenisElm.value
                }
            });
            filterHargaElm.forEach((hargaElm) => {
                if (hargaElm.checked) {
                    hargaParam = hargaElm.value
                }
            });
            filterRuangElm.forEach((ruangElm) => {
                if (ruangElm.checked) {
                    if (ruangParam == '')
                        ruangParam += ruangElm.value
                    else
                        ruangParam += ' ' + ruangElm.value
                }
            });
        } else {
            filterKoleksi1Elm.forEach((koleksiElm) => {
                if (koleksiElm.checked) {
                    if (koleksiParam == '')
                        koleksiParam += koleksiElm.value
                    else
                        koleksiParam += ' ' + koleksiElm.value
                }
            });
            filterJenis1Elm.forEach((jenisElm) => {
                if (jenisElm.checked) {
                    if (jenisParam == '')
                        jenisParam += jenisElm.value
                    else
                        jenisParam += ' ' + jenisElm.value
                }
            });
            filterHarga1Elm.forEach((hargaElm) => {
                if (hargaElm.checked) {
                    hargaParam = hargaElm.value
                }
            });
            filterRuang1Elm.forEach((ruangElm) => {
                if (ruangElm.checked) {
                    if (ruangParam == '')
                        ruangParam += ruangElm.value
                    else
                        ruangParam += ' ' + ruangElm.value
                }
            });
        }

        // console.log(urlCur + '?koleksi=' + koleksiParam + '&jenis=' + jenisParam + '&harga=' + hargaParam)
        let paramArr = [];
        if (smartSearchQuery !== '') paramArr.push('q=' + encodeURIComponent(smartSearchQuery))
        if (koleksiParam != '') paramArr.push('koleksi=' + encodeURIComponent(koleksiParam))
        if (jenisParam != '') paramArr.push('jenis=' + encodeURIComponent(jenisParam))
        if (hargaParam != '') paramArr.push('harga=' + encodeURIComponent(hargaParam))
        if (ruangParam != '') paramArr.push('ruang=' + encodeURIComponent(ruangParam))
        window.location.href = urlCur + (paramArr.length ? '?' + paramArr.join('&') : '')
    })
})
// btnFilterElm.forEach(element => {
//     element.addEventListener('click', () => {
//         const filterInputElm = document.querySelectorAll(".filter:checked");
//         let arrFilter = [];
//         let valueFilter = [];
//         filterInputElm.forEach(filterinp => {
//             // console.log(filterinp.value);
//             if (!arrFilter.includes(filterinp.value.split("-")[0])) {
//                 arrFilter.push(filterinp.value.split("-")[0]);
//                 valueFilter.push(filterinp.value.split("-")[1]);
//             } else {
//                 valueFilter[valueFilter.length - 1] += '-' + filterinp.value.split("-")[1]
//             }
//         });
//         console.log(arrFilter)
//         console.log(valueFilter)
//         const pathUrl = window.location.pathname
//         let strUrl = pathUrl + '?'
//         arrFilter.forEach((fil, ind_fil) => {
//             strUrl += "&" + fil + "=" + valueFilter[ind_fil]
//         })
//         console.log(strUrl)
//         window.location.href = strUrl
//     })
// });

function pergiKeProduct(id_produk) {
    window.location.href = "/product/" + id_produk
}

function openMeta(e) {
    const containerMeta = document.querySelector('.container-meta');
    if (bukaMeta) {
        containerMeta.classList.remove('show')
        bukaMeta = false;
        e.target.innerHTML = 'Lihat selengkapnya'
    } else {
        containerMeta.classList.add('show')
        bukaMeta = true;
        e.target.innerHTML = 'Lebih sedikit'
    }
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
</script>

<?= $this->endSection(); ?>
