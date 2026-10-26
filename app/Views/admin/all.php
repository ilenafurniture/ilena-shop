<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<?php
$koleksiterpilih = '';
if (isset($_GET['koleksi'])) {
    if ($_GET['koleksi'] != '') {
        $koleksiselect = $_GET['koleksi'];
        $produkLama = $produk;
        $produk = [];
        foreach ($produkLama as $p) {
            if (strtolower(str_replace("-", " ", $koleksiselect)) == strtolower($p['kategori'])) {
                array_push($produk, $p);
            }
        }
        $koleksiterpilih = $koleksiselect;
    }
}

$queryProduk = trim((string) ($_GET['q'] ?? ''));
if ($queryProduk !== '') {
    $queryFolded = mb_strtolower($queryProduk, 'UTF-8');
    $produk = array_values(array_filter($produk, static function (array $item) use ($queryFolded): bool {
        $searchText = mb_strtolower(implode(' ', [
            (string) ($item['nama'] ?? ''),
            (string) ($item['kategori'] ?? ''),
            (string) ($item['subkategori'] ?? ''),
            (string) ($item['id'] ?? ''),
        ]), 'UTF-8');
        return mb_strpos($searchText, $queryFolded, 0, 'UTF-8') !== false;
    }));
}

$totalProdukFiltered = count($produk);
$hitungPag = (int) ceil($totalProdukFiltered / 10);
$pag = max(1, (int) ($_GET['pag'] ?? 1));
if ($hitungPag > 0) $pag = min($pag, $hitungPag);
$paginationFilters = [];
if ($koleksiterpilih !== '') $paginationFilters['koleksi'] = $koleksiterpilih;
if ($queryProduk !== '') $paginationFilters['q'] = $queryProduk;
$produkLama = array_slice($produk, ($pag - 1) * 10);
$produk = [];
for ($i = 0; $i < 10; $i++) {
    if (isset($produkLama[$i]))
        array_push($produk, $produkLama[$i]);
}
$barangThumbUrl = function ($product) {
    $id = is_array($product) ? ($product['id'] ?? '') : (string) $product;
    $varian = is_array($product) ? ($product['varian'] ?? []) : [];
    if (is_string($varian)) $varian = json_decode($varian, true) ?: [];
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
    $dbVersion = is_array($product) && !empty($product['tgl_update']) ? (int) strtotime((string) $product['tgl_update']) : 0;
    $version = max($fileVersion, $dbVersion);
    return base_url('product-cover/' . rawurlencode($id)) . '?slot=' . urlencode($slot) . '&size=300&v=' . $version;
};
?>

<style>
:root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1f2937;
    --red: #b31217;
}

.page-wrap {
    max-width: 1480px;
    margin: 0 auto;
    padding: 8px 16px 2rem;
}

.admin-product-hero {
    background: rgba(255, 255, 255, .86);
    border: 1px solid #eef2f7;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 6px 24px rgba(15, 23, 42, .045);
    margin-bottom: 16px;
}

.product-overview {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: end;
    gap: 18px;
}

.overview-copy p { margin: 0; color: var(--slate-500); font-size: 13px; }
.overview-stats { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
.overview-stat { min-width: 105px; padding: 9px 12px; border: 1px solid var(--slate-200); border-radius: 12px; background: #fff; }
.overview-stat strong { display: block; color: #172033; font-size: 17px; line-height: 1.2; font-variant-numeric: tabular-nums; }
.overview-stat span { color: var(--slate-500); font-size: 11px; }

.page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
}

.page-title {
    margin: 0 0 6px;
    line-height: 1.1;
    font-size: 24px;
    font-weight: 850;
    letter-spacing: -.045em;
    color: #0f172a;
}

.meta-line {
    color: var(--slate-600);
    font-size: 13px
}

/* Toolbar */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 14px 0 14px;
    flex-wrap: wrap;
}

.toolbar-left {
    display: flex;
    gap: 10px;
    align-items: center;
    flex: 1;
}

@media (max-width: 640px) {
    .page-wrap { padding: 12px 10px 24px; }
    .admin-product-hero { padding: 14px; }
    .page-head { align-items: flex-start; flex-direction: column; }
    .toolbar-left { width: 100%; flex-wrap: wrap; }
    .searchbox { min-width: 100%; }
}

.toolbar-right {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-default-merah {
    white-space: nowrap;
}

.btn.btn-outline-dark {
    white-space: nowrap;
}

.bulk-toolbar {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    margin: 0 0 14px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .05);
}

.bulk-toolbar.is-visible {
    display: flex;
}

.bulk-toolbar strong {
    color: #111827;
}

.bulk-actions-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.bulk-edit-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    border-radius: 10px;
    padding: 0 13px;
    background: #b31217;
    color: #fff;
    text-decoration: none;
    font-weight: 800;
}

.bulk-edit-link:hover {
    color: #fff;
    filter: brightness(.96);
}

.bulk-clear-btn {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #334155;
    min-height: 38px;
    border-radius: 10px;
    padding: 0 12px;
    font-weight: 700;
}

.product-select,
#selectAllProducts,
#selectAllProductsMobile {
    width: 18px;
    height: 18px;
    accent-color: #b31217;
}

.filter-select {
    min-width: 220px;
}

.searchbox {
    position: relative;
    flex: 1;
    min-width: 240px;
}

.searchbox input {
    width: 100%;
    padding: 10px 12px 10px 38px;
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    outline: none;
    background: #fff;
}

.searchbox input:focus {
    border-color: var(--slate-300);
    box-shadow: 0 0 0 3px rgba(100, 116, 139, .12)
}

.searchbox .material-icons {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: var(--slate-500);
}

/* Table-like cards (desktop) */
.container-table {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 16px 42px rgba(15, 23, 42, .05);
}

.header-table,
.isi-table {
    display: flex;
    gap: 12px;
    align-items: center;
}

.header-table {
    padding: 12px 14px;
    background: linear-gradient(#fff, #fff), linear-gradient(to bottom, #fff 0%, var(--slate-50) 100%);
    border-bottom: 1px solid var(--slate-200);
    color: #90a0b5;
    font-size: 12px;
    letter-spacing: .3px;
    text-transform: uppercase;
    font-weight: 700;
}

.isi-table {
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-100);
    transition: background .15s;
}

.isi-table:hover {
    background: #fcfcfd;
}

.isi-table:last-child {
    border-bottom: 0;
}

/* Columns */
.col-center {
    text-align: center;
}

.price {
    font-variant-numeric: tabular-nums;
    font-weight: 600;
    color: #111827;
}

.stok {
    font-size: 13px;
    color: var(--slate-700);
}

.stock-summary { min-width: 0; }
.stock-summary-total { display: block; margin-bottom: 5px; color: #172033; font-size: 12px; font-weight: 800; }
.stock-chip-list { display: flex; flex-wrap: wrap; gap: 4px; }
.stock-chip { display: inline-flex; align-items: center; gap: 5px; max-width: 100%; padding: 3px 7px; border: 1px solid #e8edf3; border-radius: 999px; background: #f8fafc; color: #475569; font-size: 11px; line-height: 1.25; }
.stock-chip strong { color: #172033; font-variant-numeric: tabular-nums; }
.stock-chip.is-empty { color: #9f1239; background: #fff7f8; border-color: #ffe1e5; }
.status-control { display:flex; align-items:center; gap:8px; }
.status-label { min-width: 48px; color: #64748b; font-size: 11px; font-weight: 700; }
.status-label.is-live { color:#166534; }

.id-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Roboto Mono", monospace;
    color: var(--slate-600);
    font-size: 12px;
}

/* Badge kategori */
.badge-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 8px;
    border-radius: 999px;
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    font-size: 12px;
    color: #0f172a;
}

.badge-chip i {
    font-size: 14px;
    color: var(--slate-500)
}

/* Actions */
.actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid var(--slate-200);
    background: #fff;
    color: #111827;
    transition: .15s;
    text-decoration: none;
}

.btn-icon:hover {
    background: var(--slate-50);
}

.btn-del {
    background: #fff5f5;
    border-color: #ffd3cf;
    color: #b42318;
}

.btn-del:hover {
    background: #ffecec;
}

/* Responsive blocks */
.show-block-ke-hide {
    display: block;
}

.hide-ke-show-block {
    display: none;
}

@media (max-width: 860px) {
    .admin-product-hero {
        padding: 16px;
        border-radius: 18px;
    }

    .page-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .product-overview { grid-template-columns: 1fr; align-items: start; }
    .overview-stats { justify-content: flex-start; }
    .overview-stat { min-width: 0; flex: 1; }

    .toolbar-right {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .toolbar-left,
    .searchbox {
        width: 100%;
        min-width: 0;
    }

    .filter-select {
        min-width: 140px;
    }

    .show-block-ke-hide {
        display: none;
    }

    .hide-ke-show-block {
        display: block;
    }
}

/* Mini table (mobile wrapper) */
.mini-wrap {
    width: 720px;
}

/* membuat scroll-x */
.mini-text-sm {
    font-size: 12px;
}

.mini-price {
    font-variant-numeric: tabular-nums;
}

/* Pagination */
.container-pag {
    margin-top: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
}

.item-pag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border-radius: 10px;
    border: 1px solid var(--slate-200);
    color: #111827;
    text-decoration: none;
    background: #fff;
}

.item-pag:hover {
    background: var(--slate-50)
}

.item-pag.active {
    background: var(--red);
    color: #fff;
    border-color: transparent
}

/* Ilena admin catalog: warm, restrained editorial finish. */
.page-wrap {
    max-width: 1540px;
    padding: 10px clamp(12px, 1.8vw, 26px) 36px;
    color: #302d25;
    font-family: Inter, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    font-size: 13.5px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}
.admin-product-hero {
    border-color: #eae5db;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 8px 26px rgba(57, 48, 34, .045);
    padding: 19px 20px 15px;
}
.page-title { color: #29271f; font-size: 24px; font-weight: 620; letter-spacing: -.035em; line-height: 1.2; }
.overview-copy p, .meta-line { color: #77736c; }
.overview-stat { min-width: 112px; border-color: #ece7de; border-radius: 9px; background: #fbfaf7; }
.overview-stat strong { color: #302d25; font-size: 16px; font-weight: 620; }
.overview-stat span { color: #817b70; font-size: 11.5px; }
.toolbar { margin: 12px 0; }
.searchbox input, .filter-select .form-select {
    min-height: 41px;
    border-color: #e5e0d6;
    border-radius: 8px;
    color: #302d25;
    background-color: #fff;
    font-size: 13.5px;
}
.searchbox input:focus, .filter-select .form-select:focus { border-color: #a28e6b; box-shadow: 0 0 0 3px rgba(162, 142, 107, .13); }
.btn-default-merah, .bulk-edit-link { border-radius: 8px; background: #66583f; box-shadow: none; }
.btn-default-merah:hover, .bulk-edit-link:hover { background: #51452f; }
.container-table { border-color: #eae5db; border-radius: 12px; box-shadow: 0 8px 28px rgba(57, 48, 34, .045); }
.header-table { background: #f8f6f1; border-bottom-color: #eae5db; color: #777165; font-size: 10.5px; letter-spacing: .1em; }
.isi-table { min-height: 91px; border-bottom-color: #f0ede7; transition: background .18s ease; }
.isi-table:hover { background: #fbfaf7; }
.price { color: #403a2f; font-size: 13.5px; font-weight: 620; }
.stock-summary-total { color: #514a3d; font-weight: 650; }
.stock-chip { border-color: #e9e5dc; background: #faf9f6; color: #625e54; }
.stock-chip.is-empty { color: #963f37; background: #fcf4f2; border-color: #efdbd6; }
.id-mono { color: #8b8579; font-size: 11.5px; }
.badge-chip { border-color: #e9e2d5; background: #f8f5ef; color: #62563f; font-size: 11.5px; }
.badge-chip i { color: #8b7959; }
.btn-icon { width: 36px; height: 36px; border-color: #e6e1d7; border-radius: 9px; color: #5b5344; }
.btn-icon:hover { background: #f6f2ea; border-color: #d4c8b4; }
.btn-del { background: #fff; border-color: #eadbd7; color: #a5463d; }
.status-label { color: #817b70; }
.status-label.is-live { color: #456c50; }
.bulk-toolbar { border-color: #eae5db; border-radius: 11px; box-shadow: 0 8px 24px rgba(57, 48, 34, .04); }
.bulk-edit-link { background: #66583f; }
@media (max-width: 860px) {
    .page-wrap { padding: 10px 12px 28px; }
    .admin-product-hero { padding: 15px; border-radius: 11px; }
    .overview-stat { padding: 8px 10px; }
    .mini-wrap { border-radius: 10px; }
}
</style>

<div class="page-wrap">

    <!-- Header -->
    <div class="admin-product-hero">
        <div class="product-overview">
            <div class="overview-copy">
                <h1 class="page-title">Katalog Produk</h1>
                <p>Atur produk, harga, stok, dan visibilitas.</p>
            </div>
            <div class="toolbar-right">
                <a href="/admin/producttable" class="btn btn-outline-dark" aria-label="Export daftar produk">Export</a>
                <a href="/admin/addproduct" class="btn-default-merah">Tambah Produk</a>
            </div>
        </div>
        <div class="overview-stats" aria-label="Ringkasan katalog">
            <div class="overview-stat"><strong><?= number_format((int)($jumlah_produk ?? 0), 0, ',', '.'); ?></strong><span>Total produk</span></div>
            <div class="overview-stat"><strong data-stat-value="live"><?= number_format((int)($jumlah_tayang ?? 0), 0, ',', '.'); ?></strong><span>Aktif</span></div>
            <div class="overview-stat"><strong data-stat-value="hidden"><?= number_format((int)($jumlah_draft ?? 0), 0, ',', '.'); ?></strong><span>Nonaktif</span></div>
        </div>
        <div class="meta-line" style="margin-top:10px;">
            <?= number_format($totalProdukFiltered, 0, ',', '.'); ?> hasil<?= $koleksiterpilih ? " · " . esc(str_replace('-', ' ', $koleksiterpilih)) : '' ?><?= $queryProduk !== '' ? ' · “' . esc($queryProduk) . '”' : '' ?>
        </div>
    </div>

    <!-- Filter dan pencarian untuk seluruh katalog -->
    <div class="toolbar">
        <form class="toolbar-left" id="productFilterForm" method="get" action="/admin/product">
            <div class="filter-select">
                <select class="form-select" name="koleksi" aria-label="Filter koleksi" onchange="this.form.submit()">
                    <option value="" <?= $koleksiterpilih == '' ? 'selected' : '' ?>>Semua koleksi</option>
                    <?php foreach ($koleksi as $k) { ?>
                    <option value="<?= esc(str_replace(' ', '-', $k['nama']), 'attr') ?>"
                        <?= $koleksiterpilih == str_replace(' ', '-', $k['nama']) ? 'selected' : '' ?>>
                        <?= esc($k['nama']) ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="searchbox">
                <i class="material-icons">search</i>
                <input id="filterInput" name="q" type="search" value="<?= esc($queryProduk, 'attr') ?>" placeholder="Cari produk…" aria-label="Cari semua produk">
            </div>
            <button class="btn-default-merah" type="submit">Cari</button>
            <?php if ($queryProduk !== '' || $koleksiterpilih !== ''): ?>
                <a class="btn btn-outline-secondary" href="/admin/product">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($totalProdukFiltered === 0): ?>
        <div class="alert alert-light border" role="status">Produk tidak ditemukan.</div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
    <?php endif; ?>

    <div id="bulkToolbar" class="bulk-toolbar" aria-live="polite">
        <div>
            <strong><span id="bulkCount">0</span> produk dipilih</strong>
            <div class="meta-line">Edit cepat untuk harga, diskon, status, kategori, subkategori, dan tag ruangan.</div>
        </div>
        <div class="bulk-actions-inline">
            <a id="bulkEditLink" class="bulk-edit-link" href="/admin/product-bulk-edit">Edit massal</a>
            <button id="bulkClearBtn" class="bulk-clear-btn" type="button">Bersihkan pilihan</button>
        </div>
    </div>

    <!-- Desktop-style table -->
    <div class="container-table show-block-ke-hide">
        <div class="header-table">
            <div style="flex: .35; color:black;" class="col-center">
                <input id="selectAllProducts" type="checkbox" aria-label="Pilih semua produk di halaman ini">
            </div>
            <div style="flex: .8; color:black;"><strong>Gambar</strong></div>
            <div style="flex: 2; color:black;"><strong>Nama & ID</strong></div>
            <div style="flex: 1; color:black;"><strong>Harga</strong></div>
            <div style="flex: 1.5; color:black;"><strong>Stok</strong></div>
            <div style="flex: 1; color:black;"><strong>Status</strong></div>
            <div style="flex: 1; color:black;" class="col-center"><strong>Action</strong></div>
        </div>

        <?php foreach ($produk as $ind_p => $p) { ?>
        <div class="isi-table" data-filter="<?= strtolower($p['nama'].' '.$p['kategori'].' '.$p['id']); ?>">
            <div style="flex: .35;" class="col-center">
                <input class="product-select" type="checkbox" value="<?= esc($p['id']); ?>"
                    aria-label="Pilih produk <?= esc($p['nama']); ?>" onclick="event.stopPropagation();">
            </div>
            <div style="flex: .8; cursor:pointer" onclick="pergiKeProduct('<?= esc(str_replace(' ', '-', $p['nama']), 'js'); ?>')">
                <img style="width: 70px; height: 70px; object-fit:cover; border-radius:12px; border:1px solid var(--slate-200)"
                    id="img<?= $ind_p ?>" src="<?= $barangThumbUrl($p); ?>"
                    alt="<?= htmlspecialchars($p['nama'], ENT_QUOTES); ?>" loading="lazy" decoding="async">
            </div>

            <div style="flex: 2; cursor:pointer" class="d-flex flex-column align-items-start justify-content-center"
                onclick="pergiKeProduct('<?= esc(str_replace(' ', '-', $p['nama']), 'js'); ?>')">
                <div class="badge-chip" style="margin-bottom:4px;">
                    <i class="material-icons">label</i><?= esc(ucfirst((string)($p['kategori'] ?? ''))); ?>
                </div>
                <p class="fw-bold m-0" style="font-size: 15px; letter-spacing:-.2px; color:#111827">
                    <?= esc(strtoupper((string)($p['nama'] ?? ''))); ?></p>
                <p class="m-0 id-mono">#<?= esc($p['id'] ?? ''); ?></p>
            </div>

            <div style="flex: 1;" class="price">Rp <?= number_format((int)$p['harga'], 0, ',', '.'); ?></div>

            <div style="flex: 1.5;" class="stok">
                <div class="stock-summary">
                    <span class="stock-summary-total"><?= (int)($p['stok_total'] ?? 0); ?> unit · <?= (int)($p['jumlah_varian'] ?? 0); ?> varian</span>
                    <div class="stock-chip-list">
                        <?php foreach (($p['varian'] ?? []) as $variant): if (!is_array($variant)) continue; $variantStock = max(0, (int)($variant['stok'] ?? 0)); ?>
                        <span class="stock-chip <?= $variantStock === 0 ? 'is-empty' : ''; ?>"><?= esc($variant['nama'] ?? 'Varian'); ?> <strong><?= $variantStock; ?></strong></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div style="flex: 1;">
                <div class="status-control">
                  <div class="checkbox-apple">
                    <input onchange="ubahStatus('<?= $p['id']; ?>', this)" class="yep" id="check-apple<?= $ind_p ?>"
                        data-product-id="<?= esc($p['id'], 'attr'); ?>" type="checkbox" aria-label="Ubah status tayang <?= esc($p['nama'], 'attr'); ?>" <?= $p['status_tayang'] ? 'checked' : ''; ?>>
                    <label for="check-apple<?= $ind_p ?>"></label>
                  </div>
                  <span data-status-label="<?= esc($p['id'], 'attr'); ?>" class="status-label <?= $p['status_tayang'] ? 'is-live' : ''; ?>"><?= $p['status_tayang'] ? 'Tayang' : 'Off'; ?></span>
                </div>
            </div>

            <div style="flex: 1;" class="col-center">
                <div class="actions">
                    <a class="btn-icon" href="/admin/editproduct/<?= esc($p['id'], 'attr'); ?>" title="Edit produk" aria-label="Edit <?= esc($p['nama'], 'attr'); ?>"><i
                            class="material-icons">edit</i></a>
                    <form action="/admin/deleteproduct/<?= $p['id']; ?>" method="post" style="display:inline-flex"
                        onsubmit="return confirm('Hapus produk <?= esc($p['nama'], 'js'); ?>? Data gambar produk juga akan ikut dihapus.')">
                        <?= csrf_field(); ?>
                        <button class="btn-icon btn-del" type="submit" title="Hapus">
                            <i class="material-icons">delete</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Mobile-friendly (scroll-x) -->
    <div class="hide-ke-show-block" style="overflow:auto;">
        <div class="container-table mini-wrap">
            <div class="header-table">
                <div style="flex: .45; color:black;" class="col-center">
                    <input id="selectAllProductsMobile" type="checkbox" aria-label="Pilih semua produk di halaman ini">
                </div>
                <div style="flex: 1; color:black;"><strong>Gambar</strong></div>
                <div style="flex: 1.4; color:black;"><strong>Nama & ID</strong></div>
                <div style="flex: 1; color:black;"><strong>Harga</strong></div>
                <div style="flex: 1; color:black;"><strong>Stok</strong></div>
                <div style="flex: .8; color:black;"><strong>Status</strong></div>
                <div style="flex: .8; color:black;" class="col-center"><strong>Aksi</strong></div>
            </div>

            <?php foreach ($produk as $ind_p => $p) { ?>
            <div class="isi-table" data-filter="<?= strtolower($p['nama'].' '.$p['kategori'].' '.$p['id']); ?>">
                <div style="flex: .45;" class="col-center">
                    <input class="product-select" type="checkbox" value="<?= esc($p['id']); ?>"
                        aria-label="Pilih produk <?= esc($p['nama']); ?>" onclick="event.stopPropagation();">
                </div>
                <div style="flex: 1; cursor:pointer"
                    onclick="pergiKeProduct('<?= esc(str_replace(' ', '-', $p['nama']), 'js'); ?>')">
                    <img style="width: 50px; height: 50px; object-fit:cover; border-radius:10px; border:1px solid var(--slate-200)"
                        id="img<?= $ind_p ?>" src="<?= $barangThumbUrl($p); ?>"
                        alt="<?= htmlspecialchars($p['nama'], ENT_QUOTES); ?>" loading="lazy" decoding="async">
                </div>

                <div style="flex: 1.4; cursor:pointer"
                    class="d-flex flex-column align-items-start justify-content-center"
                    onclick="pergiKeProduct('<?= esc(str_replace(' ', '-', $p['nama']), 'js'); ?>')">
                    <span class="badge-chip" style="margin-bottom:4px;">
                        <i class="material-icons">label</i><?= esc(ucfirst((string)($p['kategori'] ?? ''))); ?>
                    </span>
                    <p class="fw-bold m-0 mini-text-sm" style="letter-spacing:-.2px;"><?= esc(ucwords((string)($p['nama'] ?? ''))); ?></p>
                    <p class="m-0 id-mono">#<?= esc($p['id'] ?? ''); ?></p>
                </div>

                <div style="flex: 1;" class="mini-text-sm mini-price">Rp
                    <?= number_format((int)$p['harga'], 0, ',', '.'); ?></div>

                <div style="flex: 1;" class="mini-text-sm">
                    <span class="stock-summary-total"><?= (int)($p['stok_total'] ?? 0); ?> unit</span>
                    <div class="stock-chip-list">
                        <?php foreach (($p['varian'] ?? []) as $variant): if (!is_array($variant)) continue; $variantStock = max(0, (int)($variant['stok'] ?? 0)); ?>
                        <span class="stock-chip <?= $variantStock === 0 ? 'is-empty' : ''; ?>"><?= esc($variant['nama'] ?? 'Varian'); ?> <strong><?= $variantStock; ?></strong></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="flex: .8;">
                    <div class="status-control">
                      <div class="checkbox-apple">
                        <input onchange="ubahStatus('<?= $p['id']; ?>', this)" class="yep" id="check-apple-m<?= $ind_p ?>"
                            data-product-id="<?= esc($p['id'], 'attr'); ?>" type="checkbox" aria-label="Ubah status tayang <?= esc($p['nama'], 'attr'); ?>" <?= $p['status_tayang'] ? 'checked' : ''; ?>>
                        <label for="check-apple-m<?= $ind_p ?>"></label>
                      </div>
                      <span data-status-label="<?= esc($p['id'], 'attr'); ?>" class="status-label <?= $p['status_tayang'] ? 'is-live' : ''; ?>"><?= $p['status_tayang'] ? 'Tayang' : 'Off'; ?></span>
                    </div>
                </div>

                <div style="flex: .8;" class="col-center">
                    <div class="actions" style="justify-content:center;">
                        <a class="btn-icon" href="/admin/editproduct/<?= esc($p['id'], 'attr'); ?>" title="Edit produk" aria-label="Edit <?= esc($p['nama'], 'attr'); ?>"><i
                                class="material-icons">edit</i></a>
                        <form action="/admin/deleteproduct/<?= $p['id']; ?>" method="post" style="display:inline-flex"
                            onsubmit="return confirm('Hapus produk <?= esc($p['nama'], 'js'); ?>? Data gambar produk juga akan ikut dihapus.')">
                            <?= csrf_field(); ?>
                            <button class="btn-icon btn-del" type="submit" title="Hapus">
                                <i class="material-icons">delete</i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Pagination (tetap sama) -->
    <div class="container-pag">
        <?php if ($pag > 1) { ?>
        <a class="item-pag"
            href="/admin/product?<?= esc(http_build_query(array_merge($paginationFilters, ['pag' => $pag - 1])), 'attr') ?>">
            <i class="material-icons">chevron_left</i>
        </a>
        <?php } ?>

        <?php for ($i = 0; $i < $hitungPag; $i++) { ?>
        <a class="item-pag <?= $pag == ($i + 1) ? 'active' : ''; ?>"
            href="/admin/product?<?= esc(http_build_query(array_merge($paginationFilters, ['pag' => $i + 1])), 'attr') ?>">
            <?= $i + 1; ?>
        </a>
        <?php } ?>

        <?php if ($pag < $hitungPag) { ?>
        <a class="item-pag"
            href="/admin/product?<?= esc(http_build_query(array_merge($paginationFilters, ['pag' => $pag + 1])), 'attr') ?>">
            <i class="material-icons">chevron_right</i>
        </a>
        <?php } ?>
    </div>
</div>

<script>
async function ubahStatus(id_produk, checkbox) {
    const desiredStatus = checkbox && checkbox.checked ? '1' : '0';
    const toggles = Array.from(document.querySelectorAll('.yep')).filter(input => input.dataset.productId === String(id_produk));
    const checkedBefore = toggles.map(input => input.checked);
    toggles.forEach(input => { input.disabled = true; input.checked = desiredStatus === '1'; });
    try {
        const tokenName = document.querySelector('meta[name="csrf-token-name"]')?.content;
        const tokenHash = document.querySelector('meta[name="csrf-token-hash"]')?.content;
        const headers = {'Accept': 'application/json'};
        if (tokenName && tokenHash) headers['X-CSRF-TOKEN'] = tokenHash;
        const response = await fetch('/admin/activeproduct/' + encodeURIComponent(id_produk), {
            method: 'POST',
            headers,
            body: new URLSearchParams({active: desiredStatus})
        });
        if (!response.ok) throw new Error('Gagal update status');
        const result = await response.json();
        if (String(result.active) !== desiredStatus) throw new Error('Status yang tersimpan tidak sesuai');
        const wasLive = checkedBefore[0] === true;
        const isLiveNow = desiredStatus === '1';
        if (wasLive !== isLiveNow) {
            ['live', 'hidden'].forEach(key => {
                const stat = document.querySelector('[data-stat-value="' + key + '"]');
                if (!stat) return;
                const current = Number((stat.textContent || '0').replace(/\D/g, '')) || 0;
                const next = current + (key === 'live' ? (isLiveNow ? 1 : -1) : (isLiveNow ? -1 : 1));
                stat.textContent = new Intl.NumberFormat('id-ID').format(Math.max(0, next));
            });
        }
        document.querySelectorAll('[data-status-label]').forEach(label => {
            if (label.dataset.statusLabel !== String(id_produk)) return;
            const isLive = desiredStatus === '1';
            label.textContent = isLive ? 'Tayang' : 'Off';
            label.classList.toggle('is-live', isLive);
        });
    } catch (error) {
        toggles.forEach((input, index) => { input.checked = checkedBefore[index]; });
        alert('Status produk gagal diubah. Coba ulangi beberapa saat lagi.');
    } finally {
        toggles.forEach(input => { input.disabled = false; });
    }
}

function pergiKeProduct(nama_produk) {
    window.location.href = "/product/" + nama_produk
}

(function() {
    const toolbar = document.getElementById('bulkToolbar');
    const countEl = document.getElementById('bulkCount');
    const editLink = document.getElementById('bulkEditLink');
    const clearBtn = document.getElementById('bulkClearBtn');
    const selectAll = document.getElementById('selectAllProducts');
    const selectAllMobile = document.getElementById('selectAllProductsMobile');

    function uniqueCheckedIds() {
        const ids = [];
        document.querySelectorAll('.product-select:checked').forEach((checkbox) => {
            if (!ids.includes(checkbox.value)) ids.push(checkbox.value);
        });
        return ids;
    }

    function updateToolbar() {
        const ids = uniqueCheckedIds();
        if (countEl) countEl.textContent = ids.length;
        if (toolbar) toolbar.classList.toggle('is-visible', ids.length > 0);
        if (editLink) {
            editLink.href = '/admin/product-bulk-edit?ids=' + encodeURIComponent(ids.join(','));
            editLink.setAttribute('aria-disabled', ids.length ? 'false' : 'true');
        }
    }

    function setAllVisible(checked) {
        document.querySelectorAll('.product-select').forEach((checkbox) => {
            const row = checkbox.closest('.isi-table');
            if (row && row.style.display === 'none') return;
            checkbox.checked = checked;
        });
        updateToolbar();
    }

    document.querySelectorAll('.product-select').forEach((checkbox) => {
        checkbox.addEventListener('change', updateToolbar);
    });

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            setAllVisible(this.checked);
            if (selectAllMobile) selectAllMobile.checked = this.checked;
        });
    }
    if (selectAllMobile) {
        selectAllMobile.addEventListener('change', function() {
            setAllVisible(this.checked);
            if (selectAll) selectAll.checked = this.checked;
        });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            document.querySelectorAll('.product-select').forEach((checkbox) => checkbox.checked = false);
            if (selectAll) selectAll.checked = false;
            if (selectAllMobile) selectAllMobile.checked = false;
            updateToolbar();
        });
    }
})();
</script>

<?= $this->endSection(); ?>
