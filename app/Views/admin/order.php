<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
[data-bs-toggle="tooltip"] {
    color: gray;
}

/* ====== Toolbar ====== */
.orders-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: linear-gradient(180deg, #fff, #fbfdff);
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 10px 12px;
    margin: 0 0 14px 0;
    box-shadow: 0 10px 24px rgba(2, 6, 23, .06);
}

.orders-toolbar .left {
    display: flex;
    align-items: flex-start;
    gap: 10px
}

.orders-toolbar h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -.2px
}

.orders-toolbar .meta {
    margin: 0;
    color: #64748b;
    font-size: 12px
}

.orders-toolbar .tools {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap
}

.f-field {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: .5rem .7rem;
}

.f-field input,
.f-field select {
    border: 0;
    outline: 0;
    background: transparent;
    font-size: 14px;
}

.btn-soft {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    background: #fff;
    border: 1px solid #e5e7eb;
    padding: .55rem .75rem;
    border-radius: 10px;
}

/* ====== List row looks like thin cards (non-breaking) ====== */
.orders-wrap .order-entry {
    border: 1px solid #eef2f7;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 8px 18px rgba(2, 6, 23, .06);
}

.orders-wrap .order-entry .isi-table {
    cursor: pointer;
}

.orders-wrap .order-entry:hover {
    box-shadow: 0 14px 32px rgba(2, 6, 23, .10);
}

/* Header strip style */
.container-table .header-table {
    background: #f8fafc;
    border: 1px solid #e9eef5;
    border-radius: 10px;
    padding: 10px 12px;
    font-weight: 700;
}

/* Sticky header on desktop list scroll */
.container-table.show-block-ke-hide {
    position: relative;
}

.container-table.show-block-ke-hide .header-table {
    position: sticky;
    top: 0;
    z-index: 2;
}

/* Offcanvas quick actions */
.btnBawah {
    min-width: 130px;
}
</style>

<!-- ===== MODAL: EDIT RESI (asli, tidak diubah) ===== -->
<div id="edit-resi" class="d-none justify-content-center align-items-center"
    style="position: fixed; top: 0; left: 0; height: 100svh; width: 100vw; background-color: rgba(0,0,0,0.8); z-index: 101;">
    <div class="p-4" style="background-color: white; border-radius: 1em">
        <form action="/admin/actioneditresi" method="post">
            <h5 class="teks-sedang mb-2">Edit resi</h5>
            <div class="mb-1">
                <label>Nama Ekspedisi</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-1">
                <label>Layanan Ekspedisi</label>
                <input type="text" name="deskripsi" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Resi</label>
                <input type="text" name="resi" class="form-control" required>
            </div>
            <div class="d-flex gap-1 justify-content-end">
                <button type="submit" class="btn-default">Simpan</button>
                <button type="button" class="btn-teks-aja" onclick="closeEditResi()">Batal</button>
            </div>
            <input type="text" name="idMid" class="d-none">
        </form>
    </div>
</div>

<div style="padding: 2em;">
    <!-- ===== Toolbar baru (client-side only) ===== -->
    <div class="orders-toolbar">
        <div class="left">
            <div>
                <h2>Pesanan Pelanggan Online</h2>
                <p class="meta">
                    <span id="totalCount"><?= count($pesanan) <= 0 ? 0 : count($pesanan) ?></span> pesanan
                    • <span id="visibleCount"><?= count($pesanan) <= 0 ? 0 : count($pesanan) ?></span> tampil
                </p>
            </div>
        </div>
        <div class="tools">
            <label class="f-field" title="Cari ID/Nama/Status">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#94a3b8" viewBox="0 0 24 24">
                    <path d="M10 18a8 8 0 1 1 5.293-14.001A8 8 0 0 1 10 18Zm12 3-6.104-6.104" />
                </svg>
                <input id="orderSearch" type="search" placeholder="Cari…">
            </label>
            <label class="f-field">
                <select id="orderStatus">
                    <option value="">Semua Status</option>
                    <option value="menunggu pembayaran">Menunggu Pembayaran</option>
                    <option value="proses">Proses</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                    <option value="gagal">Gagal</option>
                </select>
            </label>
            <button class="btn-soft" type="button" id="resetFilter">Reset</button>
            <a class="btn-default-merah" href="/admin/order/add">Tambah</a>
        </div>
    </div>

    <!-- ===== Desktop ===== -->
    <div class="container-table show-block-ke-hide orders-wrap">
        <div class="header-table border-buttom border-dark">
            <div style="flex: 2;">ID Pesanan</div>
            <div style="flex: 2;">Tanggal</div>
            <div style="flex: 2;">Penerima</div>
            <div style="flex: 2;">Harga</div>
            <div style="flex: 1;">Status</div>
        </div>
        <div class="d-flex flex-column gap-2" id="ordersDesktop">
            <?php foreach ($pesanan as $ind_p => $p) { ?>
            <?php
                    $transactionTime = strtotime($p['data_mid']['transaction_time']);
                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y", $transactionTime);
                    $qtext = strtolower($p['id_midtrans'].' '.$tgl.' '.$p['nama'].' '.$p['status']);
                ?>
            <div class="px-3 py-2 order-entry" data-row data-status="<?= strtolower($p['status']); ?>"
                data-q="<?= htmlspecialchars($qtext, ENT_QUOTES, 'UTF-8'); ?>"
                data-amount="<?= (int)$p['data_mid']['gross_amount']; ?>" data-date="<?= (int)$transactionTime; ?>">
                <div class="isi-table d-flex align-items-center" onclick="openDetail(<?= $ind_p; ?>, event)">
                    <div style="flex: 2;"><?= $p['id_midtrans']; ?></div>
                    <div style="flex: 2;"><?= $tgl; ?></div>
                    <div style="flex: 2;"><?= $p['nama']; ?></div>
                    <div style="flex: 2;">Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?>
                    </div>
                    <div style="flex: 1;">
                        <span class="badge rounded-pill <?php
                                switch ($p['status']) {
                                    case 'Menunggu Pembayaran': echo 'text-bg-primary'; break;
                                    case 'Proses': echo 'text-bg-warning'; break;
                                    case 'Dikirim': echo 'text-bg-info'; break;
                                    case 'Selesai': echo 'text-bg-success'; break;
                                    case 'Dibatalkan':
                                    case 'Gagal': echo 'text-bg-danger'; break;
                                    default: echo 'text-bg-dark'; break;
                                } ?>">
                            <?= ucfirst($p['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-1 border-top pt-1">
                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Label barang" class="btn"
                        href="/admin/labelbarang/<?= $p['id_midtrans']; ?>" target="_blank"><i
                            class="material-icons">label_outline</i></a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice" class="btn"
                        href="/invoice/<?= $p['id_midtrans']; ?>"><i class="material-icons">description</i></a>
                    <?php if ($p['status'] == 'Proses' || $p['status'] == 'Dikirim') { ?>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit resi" class="btn"
                        onclick="openEditResi('<?= $p['id_midtrans']; ?>')"><i class="material-icons">edit</i></a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Surat jalan (<?= $p['status_print'] ? $p['status_print'] : 'No status' ?>)"
                        class="btn" <?= $p['status_print'] == 'sudah print' ? 'style="color: var(--merah);"' : '' ?>
                        href="/admin/suratjalan/<?= $p['id_midtrans']; ?>" target="_blank"><i
                            class="material-icons">local_shipping</i></a>
                    <?php } ?>
                    <?php if ($p['status'] == 'Menunggu Pembayaran') { ?>
                    <a class="btn" href="/cancelorder/<?= $p['id_midtrans']; ?>"><i class="material-icons"
                            style="color: var(--merah);">cancel</i></a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- ===== Mobile ===== -->
    <div class="hide-ke-show-block">
        <div class="container-table" style="width: 700px; overflow-x: auto;">
            <div class="header-table border-buttom border-dark">
                <div style="flex: 1;">ID Pesanan</div>
                <div style="flex: 2;">Tanggal</div>
                <div style="flex: 2;">Penerima</div>
                <div style="flex: 2;">Harga</div>
                <div style="flex: 1;">Status</div>
                <div style="flex: 2;" class="d-flex justify-content-center">Action</div>
            </div>
            <div id="ordersMobile" class="d-flex flex-column gap-2">
                <?php foreach ($pesanan as $ind_p => $p) { ?>
                <?php
                    $transactionTime = strtotime($p['data_mid']['transaction_time']);
                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y", $transactionTime);
                    $qtext = strtolower($p['id_midtrans'].' '.$tgl.' '.$p['nama'].' '.$p['status']);
                ?>
                <div class="order-entry" data-row data-status="<?= strtolower($p['status']); ?>"
                    data-q="<?= htmlspecialchars($qtext, ENT_QUOTES, 'UTF-8'); ?>"
                    data-amount="<?= (int)$p['data_mid']['gross_amount']; ?>" data-date="<?= (int)$transactionTime; ?>">
                    <div class="isi-table d-flex" onclick="openDetail(<?= $ind_p; ?>, event)">
                        <div style="flex: 1;"><?= $p['id_midtrans']; ?></div>
                        <div style="flex: 2;"><?= $tgl; ?></div>
                        <div style="flex: 2;"><?= $p['nama']; ?></div>
                        <div style="flex: 2;">Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?>
                        </div>
                        <div style="flex: 1;">
                            <span class="badge rounded-pill <?php
                                switch ($p['status']) {
                                    case 'Menunggu Pembayaran': echo 'text-bg-primary'; break;
                                    case 'Proses': echo 'text-bg-warning'; break;
                                    case 'Dikirim': echo 'text-bg-info'; break;
                                    case 'Selesai': echo 'text-bg-success'; break;
                                    case 'Dibatalkan':
                                    case 'Gagal': echo 'text-bg-danger'; break;
                                    default: echo 'text-bg-dark'; break;
                                } ?>">
                                <?= ucfirst($p['status']); ?>
                            </span>
                        </div>
                        <div style="flex: 2;" class="d-flex justify-content-center">
                            <a class="btn" href="/admin/labelbarang/<?= $p['id_midtrans']; ?>" target="_blank"><i
                                    class="material-icons">label_outline</i></a>
                            <a class="btn" href="/invoice/<?= $p['id_midtrans']; ?>"><i
                                    class="material-icons">description</i></a>
                            <?php if ($p['status'] == 'Proses' || $p['status'] == 'Dikirim') { ?>
                            <a class="btn" href="/editresi/<?= $p['id_midtrans']; ?>"><i
                                    class="material-icons">edit</i></a>
                            <?php } ?>
                            <?php if ($p['status'] == 'Menunggu Pembayaran' || $p['status'] == 'Proses' || $p['status'] == 'Dikirim') { ?>
                            <a class="btn" href="/cancelorder/<?= $p['id_midtrans']; ?>"><i class="material-icons"
                                    style="color: var(--merah);">cancel</i></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- ===== Offcanvas detail (asli, tidak diubah selain tampilan) ===== -->
<div class="container-offcanvas">
    <div class="d-flex justify-content-end mb-2"><button class="btn btn-light" onclick="closeDetail()"><i
                class="material-icons">close</i></button></div>
    <div style="border-radius: 10px; padding: 1em 1.5em; background-color: whitesmoke;" class="mb-2">
        <h3 style="letter-spacing: -1px;" class="mb-2">Customer</h3>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="m-0 w-50" style="letter-spacing: -1px;">ID Pesanan</p>
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="id">#ILENA00001</p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">Nama Penerima</p>
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="nama">-</p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">Nohp Penerima</p>
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="nohp">-</p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">Alamat Penerima</p>
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="alamat">-</p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">Email Pemesan</p>
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="email">-</p>
        </div>
    </div>
    <div style="border-radius: 10px; padding: 1em 1.5em; background-color: whitesmoke;">
        <h3 style="letter-spacing: -1px;" class="mb-2">Produk</h3>
        <div id="item">
            <div class="d-flex mb-1 border-bottom pb-1">
                <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama
                </p>
                <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah
                </p>
                <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga
                </p>
            </div>
        </div>
    </div>

    <div class="d-flex gap-1 mt-3 justify-content-center">
        <a href="" class="btn-default btnBawah">Invoice</a>
        <a href="" class="btn-default btnBawah">Edit resi</a>
        <a href="" class="btn-default-merah btnBawah">Cancel Pesanan</a>
        <a href="" class="btn-default btnBawah">Detail</a>
    </div>
</div>

<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el))
</script>

<!-- ===== Original JS (dipertahankan) + Filter/Search tambahan ===== -->
<script>
const pesanan = JSON.parse('<?= $pesananJson; ?>');
const idElm = document.getElementById("id");
const namaElm = document.getElementById("nama");
const nohpElm = document.getElementById("nohp");
const alamatElm = document.getElementById("alamat");
const emailElm = document.getElementById("email");
const itemElm = document.getElementById("item");
const containerOffcanvasElm = document.querySelector(".container-offcanvas");
const btnBawahElm = document.querySelectorAll(".btnBawah");
const editResiElm = document.getElementById('edit-resi');

function openEditResi(idMid) {
    editResiElm.classList.remove('d-none')
    editResiElm.classList.add('d-flex')
    document.querySelector('input[name="idMid"]').value = idMid
}

function closeEditResi() {
    editResiElm.classList.add('d-none')
    editResiElm.classList.remove('d-flex')
}

function openDetail(indP, event) {
    const pesananSelected = pesanan[indP];
    idElm.innerHTML = pesananSelected.id_midtrans
    namaElm.innerHTML = pesananSelected.nama
    nohpElm.innerHTML = pesananSelected.nohp
    alamatElm.innerHTML = pesananSelected.alamat.alamat_lengkap
    emailElm.innerHTML = pesananSelected.email
    itemElm.innerHTML =
        '<div class="d-flex mb-1 border-bottom pb-1"><p class="d-block fw-bold m-0 text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p></div>';
    pesananSelected.items.forEach(element => {
        itemElm.innerHTML +=
            '<div class="d-flex py-1"><p class="d-block fw-bold m-0" style="flex: 1; letter-spacing: -1px;">' +
            element.name +
            '</p><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' +
            element.quantity +
            '</p><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp ' +
            element.price + '</p></div>';
    });
    btnBawahElm[0].href = "/invoice/" + pesananSelected.id_midtrans
    btnBawahElm[1].href = "/editresi/" + pesananSelected.id_midtrans
    btnBawahElm[2].href = "/cancelorder/" + pesananSelected.id_midtrans
    btnBawahElm[3].href = "/order/" + pesananSelected.id_midtrans
    containerOffcanvasElm.classList.add("show")
}

function closeDetail() {
    containerOffcanvasElm.classList.remove("show")
}

/* ====== FILTER & SEARCH (client-side, non-breaking) ====== */
const qInput = document.getElementById('orderSearch');
const statusSel = document.getElementById('orderStatus');
const resetBtn = document.getElementById('resetFilter');
const totalCountEl = document.getElementById('totalCount');
const visibleCountEl = document.getElementById('visibleCount');

// Gabungkan desktop + mobile rows
const rows = Array.from(document.querySelectorAll('[data-row]'));
totalCountEl.textContent = rows.length;

function normalize(s) {
    return (s || '').toString().toLowerCase().trim();
}

function applyFilter() {
    const term = normalize(qInput.value);
    const st = normalize(statusSel.value);
    let visible = 0;
    rows.forEach(row => {
        const q = row.dataset.q || normalize(row.textContent);
        const rs = row.dataset.status || '';
        const okSearch = !term || q.includes(term);
        const okStatus = !st || rs === st;
        const show = okSearch && okStatus;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    visibleCountEl.textContent = visible;
}

qInput.addEventListener('input', applyFilter);
statusSel.addEventListener('change', applyFilter);
resetBtn.addEventListener('click', () => {
    qInput.value = '';
    statusSel.value = '';
    applyFilter();
});

// init
applyFilter();
</script>

<?= $this->endSection(); ?>