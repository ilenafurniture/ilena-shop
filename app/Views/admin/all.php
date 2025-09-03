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

$hitungPag = ceil(count($produk) / 10);
$pag = 1;
if (isset($_GET['pag'])) $pag = (int)$_GET['pag'];
$produkLama = array_slice($produk, ($pag - 1) * 10);
$produk = [];
for ($i = 0; $i < 10; $i++) {
    if (isset($produkLama[$i]))
        array_push($produk, $produkLama[$i]);
}
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
    padding: 2rem;
}

.page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.page-title {
    margin: 0;
    line-height: 1.2
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
    margin: 10px 0 4px;
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
    border: 1px solid var(--slate-200);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(0, 0, 0, .04);
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
    <div class="page-head">
        <div>
            <h1 class="teks-sedang page-title">Produk Saya</h1>
            <div class="meta-line"><?= count($produk); ?> Produk pada halaman
                ini<?= $koleksiterpilih ? " • Koleksi: <b>".htmlspecialchars(str_replace('-', ' ', $koleksiterpilih))."</b>" : '' ?>
            </div>
        </div>
        <div class="toolbar-right">
            <a href="/admin/producttable" class="btn btn-outline-dark">Export</a>
            <a href="/admin/addproduct" class="btn-default-merah">Tambah Produk</a>
            <a href="/admin/changepic" class="btn-default-merah">Resize Img</a>
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

    <!-- Desktop-style table -->
    <div class="container-table show-block-ke-hide">
        <div class="header-table">
            <div style="flex: .8; color:black;"><strong>Gambar</strong></div>
            <div style="flex: 2; color:black;"><strong>Nama & ID</strong></div>
            <div style="flex: 1; color:black;"><strong>Harga</strong></div>
            <div style="flex: 1.5; color:black;"><strong>Stok</strong></div>
            <div style="flex: 1; color:black;"><strong>Status</strong></div>
            <div style="flex: 1; color:black;" class="col-center"><strong>Action</strong></div>
        </div>

        <?php foreach ($produk as $ind_p => $p) { ?>
        <div class="isi-table" data-filter="<?= strtolower($p['nama'].' '.$p['kategori'].' '.$p['id']); ?>">
            <div style="flex: .8; cursor:pointer" onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                <img style="width: 70px; height: 70px; object-fit:cover; border-radius:12px; border:1px solid var(--slate-200)"
                    id="img<?= $ind_p ?>" src="<?= base_url('img/barang/300/' . $p['id'] . '.webp'); ?>"
                    alt="<?= htmlspecialchars($p['nama'], ENT_QUOTES); ?>">
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
                    <input onchange="ubahStatus('<?= $p['id']; ?>')" class="yep" id="check-apple<?= $ind_p ?>"
                        type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                    <label for="check-apple<?= $ind_p ?>"></label>
                </div>
            </div>

            <div style="flex: 1;" class="col-center">
                <div class="actions">
                    <a class="btn-icon" href="/admin/editproduct/<?= $p['id']; ?>" title="Edit"><i
                            class="material-icons">edit</i></a>
                    <form action="/admin/deleteproduct/<?= $p['id']; ?>" method="post" style="display:inline-flex">
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
                <div style="flex: 1; color:black;"><strong>Gambar</strong></div>
                <div style="flex: 1.4; color:black;"><strong>Nama & ID</strong></div>
                <div style="flex: 1; color:black;"><strong>Harga</strong></div>
                <div style="flex: 1; color:black;"><strong>Stok</strong></div>
                <div style="flex: .8; color:black;"><strong>Status</strong></div>
                <div style="flex: .8; color:black;" class="col-center"><strong>Aksi</strong></div>
            </div>

            <?php foreach ($produk as $ind_p => $p) { ?>
            <div class="isi-table" data-filter="<?= strtolower($p['nama'].' '.$p['kategori'].' '.$p['id']); ?>">
                <div style="flex: 1; cursor:pointer"
                    onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                    <img style="width: 50px; height: 50px; object-fit:cover; border-radius:10px; border:1px solid var(--slate-200)"
                        id="img<?= $ind_p ?>" src="<?= base_url('img/barang/300/' . $p['id'] . '.webp'); ?>"
                        alt="<?= htmlspecialchars($p['nama'], ENT_QUOTES); ?>">
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
                        <input onchange="ubahStatus('<?= $p['id']; ?>')" class="yep" id="check-apple-m<?= $ind_p ?>"
                            type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                        <label for="check-apple-m<?= $ind_p ?>"></label>
                    </div>
                </div>

                <div style="flex: .8;" class="col-center">
                    <div class="actions" style="justify-content:center;">
                        <a class="btn-icon" href="/admin/editproduct/<?= $p['id']; ?>" title="Edit"><i
                                class="material-icons">edit</i></a>
                        <a class="btn-icon btn-del" href="/admin/deleteproduct/<?= $p['id']; ?>" title="Hapus"><i
                                class="material-icons">delete</i></a>
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

function ubahStatus(id_produk) {
    async function fetchUpdate() {
        await fetch('/admin/activeproduct/' + id_produk);
    }
    fetchUpdate();
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
</script>

<?= $this->endSection(); ?>