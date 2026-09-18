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

$totalProdukFiltered = count($produk);
$totalProdukAktif = count(array_filter($produk, static fn($item) => !empty($item['active'])));
$totalKoleksi = count($koleksi ?? []);
$hitungPag = ceil($totalProdukFiltered / 10);
$pag = 1;
if (isset($_GET['pag'])) $pag = (int)$_GET['pag'];
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
    return base_url('product-cover/' . $id) . '?slot=' . urlencode($slot) . '&v=' . $version;
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
    padding: 4px 2px 2rem;
}

.admin-product-hero {
    background: rgba(255, 255, 255, .86);
    border: 1px solid #eef2f7;
    border-radius: 22px;
    padding: 20px;
    box-shadow: 0 18px 45px rgba(15, 23, 42, .06);
    margin-bottom: 16px;
}

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
    font-size: 28px;
    font-weight: 850;
    letter-spacing: -.045em;
    color: #0f172a;
}

.meta-line {
    color: var(--slate-600);
    font-size: 13px
}

.admin-stat-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.admin-stat-card {
    padding: 13px 14px;
    border-radius: 16px;
    border: 1px solid #eef2f7;
    background: #f8fafc;
}

.admin-stat-card span {
    display: block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #94a3b8;
    margin-bottom: 4px;
}

.admin-stat-card strong {
    display: block;
    font-size: 22px;
    line-height: 1;
    color: #111827;
    letter-spacing: -.04em;
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

    .toolbar-right {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .admin-stat-grid {
        grid-template-columns: repeat(3, minmax(96px, 1fr));
        overflow-x: auto;
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
</style>

<div class="page-wrap">

    <!-- Header -->
    <div class="admin-product-hero">
        <div class="page-head">
            <div>
                <h1 class="page-title">Produk Saya</h1>
                <div class="meta-line"><?= $totalProdukFiltered; ?> produk ditemukan<?= $koleksiterpilih ? " • Koleksi: <b>".htmlspecialchars(str_replace('-', ' ', $koleksiterpilih))."</b>" : '' ?></div>
            </div>
            <div class="toolbar-right">
                <a href="/admin/producttable" class="btn btn-outline-dark">Export</a>
                <a href="/admin/addproduct" class="btn-default-merah">Tambah Produk</a>
                <a href="/admin/changepic" class="btn-default-merah">Resize Img</a>
            </div>
        </div>

        <div class="admin-stat-grid">
            <div class="admin-stat-card">
                <span>Total Produk</span>
                <strong><?= number_format($totalProdukFiltered, 0, ',', '.'); ?></strong>
            </div>
            <div class="admin-stat-card">
                <span>Produk Aktif</span>
                <strong><?= number_format($totalProdukAktif, 0, ',', '.'); ?></strong>
            </div>
            <div class="admin-stat-card">
                <span>Koleksi</span>
                <strong><?= number_format($totalKoleksi, 0, ',', '.'); ?></strong>
            </div>
        </div>
    </div>

    <!-- Toolbar filter & search (frontend-only) -->
    <div class="toolbar">
        <div class="toolbar-left">
            <div class="filter-select">
                <!-- Selected adalah atribut yang aktif -->
                <select class="form-select" onchange="gantikoleksi(event)">
                    <option value="semua" <?= $koleksiterpilih == '' ? 'selected' : '' ?>>Semua</option>
                    <?php foreach ($koleksi as $k) { ?>
                    <option value="<?= str_replace(' ', '-', $k['nama']) ?>"
                        <?= $koleksiterpilih == str_replace(' ', '-', $k['nama']) ? 'selected' : '' ?>>
                        <?= $k['nama'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="searchbox">
                <i class="material-icons">search</i>
                <input id="filterInput" type="text" placeholder="Cari nama, kategori, atau ID…">
            </div>
        </div>
    </div>

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
            <div style="flex: .8; cursor:pointer" onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                <img style="width: 70px; height: 70px; object-fit:cover; border-radius:12px; border:1px solid var(--slate-200)"
                    id="img<?= $ind_p ?>" src="<?= $barangThumbUrl($p); ?>"
                    alt="<?= htmlspecialchars($p['nama'], ENT_QUOTES); ?>" loading="lazy" decoding="async">
            </div>

            <div style="flex: 2; cursor:pointer" class="d-flex flex-column align-items-start justify-content-center"
                onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                <div class="badge-chip" style="margin-bottom:4px;">
                    <i class="material-icons">label</i><?= ucfirst($p['kategori']); ?>
                </div>
                <p class="fw-bold m-0" style="font-size: 15px; letter-spacing:-.2px; color:#111827">
                    <?= strtoupper($p['nama']); ?></p>
                <p class="m-0 id-mono">#<?= $p['id']; ?></p>
            </div>

            <div style="flex: 1;" class="price">Rp <?= number_format((int)$p['harga'], 0, ',', '.'); ?></div>

            <div style="flex: 1.5;" class="stok"><?= strtolower($p['allstok']); ?></div>

            <div style="flex: 1;">
                <div class="checkbox-apple">
                    <input onchange="ubahStatus('<?= $p['id']; ?>', this)" class="yep" id="check-apple<?= $ind_p ?>"
                        type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                    <label for="check-apple<?= $ind_p ?>"></label>
                </div>
            </div>

            <div style="flex: 1;" class="col-center">
                <div class="actions">
                    <a class="btn-icon" href="/admin/editproduct/<?= $p['id']; ?>" title="Edit"><i
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
                    onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                    <img style="width: 50px; height: 50px; object-fit:cover; border-radius:10px; border:1px solid var(--slate-200)"
                        id="img<?= $ind_p ?>" src="<?= $barangThumbUrl($p); ?>"
                        alt="<?= htmlspecialchars($p['nama'], ENT_QUOTES); ?>" loading="lazy" decoding="async">
                </div>

                <div style="flex: 1.4; cursor:pointer"
                    class="d-flex flex-column align-items-start justify-content-center"
                    onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                    <span class="badge-chip" style="margin-bottom:4px;">
                        <i class="material-icons">label</i><?= ucfirst($p['kategori']); ?>
                    </span>
                    <p class="fw-bold m-0 mini-text-sm" style="letter-spacing:-.2px;"><?= ucwords($p['nama']); ?></p>
                    <p class="m-0 id-mono">#<?= $p['id']; ?></p>
                </div>

                <div style="flex: 1;" class="mini-text-sm mini-price">Rp
                    <?= number_format((int)$p['harga'], 0, ',', '.'); ?></div>

                <div style="flex: 1;" class="mini-text-sm"><?= strtolower($p['allstok']); ?></div>

                <div style="flex: .8;">
                    <div class="checkbox-apple">
                        <input onchange="ubahStatus('<?= $p['id']; ?>', this)" class="yep" id="check-apple-m<?= $ind_p ?>"
                            type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                        <label for="check-apple-m<?= $ind_p ?>"></label>
                    </div>
                </div>

                <div style="flex: .8;" class="col-center">
                    <div class="actions" style="justify-content:center;">
                        <a class="btn-icon" href="/admin/editproduct/<?= $p['id']; ?>" title="Edit"><i
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
            href="/admin/product?pag=<?= $pag - 1; ?><?= isset($_GET['koleksi']) ? '&koleksi=' . $_GET['koleksi'] : ''; ?>">
            <i class="material-icons">chevron_left</i>
        </a>
        <?php } ?>

        <?php for ($i = 0; $i < $hitungPag; $i++) { ?>
        <a class="item-pag <?= $pag == ($i + 1) ? 'active' : ''; ?>"
            href="/admin/product?pag=<?= $i + 1; ?><?= isset($_GET['koleksi']) ? '&koleksi=' . $_GET['koleksi'] : ''; ?>">
            <?= $i + 1; ?>
        </a>
        <?php } ?>

        <?php if ($pag < $hitungPag) { ?>
        <a class="item-pag"
            href="/admin/product?pag=<?= $pag + 1; ?><?= isset($_GET['koleksi']) ? '&koleksi=' . $_GET['koleksi'] : ''; ?>">
            <i class="material-icons">chevron_right</i>
        </a>
        <?php } ?>
    </div>
</div>

<script>
function gantikoleksi(e) {
    if (e.target.value == 'semua') {
        window.location.href = window.location.pathname;
    } else {
        window.location.href = window.location.pathname + '?koleksi=' + e.target.value;
    }
}

async function ubahStatus(id_produk, checkbox) {
    const checkedBefore = checkbox ? !checkbox.checked : null;
    if (checkbox) checkbox.disabled = true;
    try {
        const response = await fetch('/admin/activeproduct/' + encodeURIComponent(id_produk), {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (!response.ok) throw new Error('Gagal update status');
    } catch (error) {
        if (checkbox && checkedBefore !== null) checkbox.checked = checkedBefore;
        alert('Status produk gagal diubah. Coba ulangi beberapa saat lagi.');
    } finally {
        if (checkbox) checkbox.disabled = false;
    }
}

function pergiKeProduct(nama_produk) {
    window.location.href = "/product/" + nama_produk
}

// Filter frontend (tidak mengubah sistem)
(function() {
    const input = document.getElementById('filterInput');
    if (!input) return;

    const selectors = [
        '.show-block-ke-hide .isi-table',
        '.hide-ke-show-block .isi-table'
    ];

    input.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(row => {
                const bag = row.getAttribute('data-filter') || '';
                row.style.display = q ? (bag.includes(q) ? '' : 'none') : '';
            });
        });
    });
})();

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
