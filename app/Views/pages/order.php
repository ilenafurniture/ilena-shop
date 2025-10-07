<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* ===== Scoped styles: aman, tidak ganggu halaman lain ===== */
:root {
    --card-r: 14px;
    --soft: #f8fafc;
    --line: #e5e7eb;
    --ink: #111827;
    --muted: #6b7280;
}

/* Overlay edit sandi */
#edit-sandi {
    backdrop-filter: blur(2px);
}

#edit-sandi .modal-card {
    width: min(520px, 92vw);
    border-radius: 16px;
    border: 1px solid var(--line);
    box-shadow: 0 18px 60px rgba(0, 0, 0, .18);
}

#edit-sandi h2 {
    font-weight: 800;
    letter-spacing: -.02em;
}

#edit-sandi .btn-teks-aja {
    text-decoration: none;
}

/* Kartu kiri (akun) */
.account-card {
    border-radius: var(--card-r);
    border: 1px solid var(--line);
    background: #fff;
    box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
}

.account-card h2 {
    font-weight: 800;
    letter-spacing: -.02em;
}

.account-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .6rem 0;
}

.account-row p {
    margin: 0;
}

/* Table pesanan */
.container-table {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
}

.header-table,
.isi-table {
    display: grid;
    grid-template-columns: 1.6fr 1.2fr 1.2fr 1fr 1fr;
    gap: .5rem;
    padding: .9rem 1rem;
}

.header-table {
    background: linear-gradient(180deg, #fafafa, #f3f4f6);
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .06em;
    border-bottom: 1px solid var(--line);
}

.isi-table {
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s ease;
}

.isi-table:last-child {
    border-bottom: 0;
}

.isi-table:hover {
    background: #f9fafb;
}

/* Offcanvas (detail pesanan) */
.container-offcanvas {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: min(720px, 92vw);
    background: #fff;
    transform: translateX(100%);
    transition: transform .25s ease-out;
    border-left: 1px solid var(--line);
    padding: 16px;
    overflow-y: auto;
    box-shadow: -16px 0 40px rgba(0, 0, 0, .08);
}

.container-offcanvas.show {
    transform: translateX(0);
}

.container-offcanvas h3 {
    font-weight: 800;
    letter-spacing: -.02em;
}

.container-offcanvas .garis {
    display: block;
    height: 1px;
    background: var(--line);
}

/* Grid ringkas kanan */
.tigapuluh-ke-seratus .card {
    border: 1px solid var(--line);
    border-radius: var(--card-r);
    box-shadow: 0 6px 24px rgba(0, 0, 0, .06);
}

/* Badge status */
.badge-status {
    display: inline-block;
    padding: .28rem .5rem;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 700;
}

.badge-status.proses {
    background: #ecfeff;
    color: #0369a1;
}

.badge-status.menunggu {
    background: #fff7ed;
    color: #c2410c;
}

.badge-status.kadaluarsa {
    background: #f1f5f9;
    color: #475569;
}

.badge-status.ditolak,
.badge-status.gagal {
    background: #ffe4e6;
    color: #e11d48;
}

.badge-status.dibatalkan {
    background: #f3f4f6;
    color: #6b7280;
}

.badge-status.refund {
    background: #f0fdf4;
    color: #166534;
}

/* Responsif */
@media (max-width: 768px) {

    .header-table,
    .isi-table {
        grid-template-columns: 1.6fr 1.2fr .9fr .9fr 1fr;
        font-size: .95rem;
    }
}

/* Tombol kecil offcanvas */
.btn-close-ghost {
    border: 1px solid var(--line);
    background: #fff;
    border-radius: 10px;
    padding: .35rem .6rem;
}
</style>

<div id="edit-sandi"
    style="position: fixed; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.55); z-index: 100; top: 0; left: 0;"
    class="<?= $msgSandi ? 'd-flex' : 'd-none'; ?> justify-content-center align-items-center">
    <div class="p-4 bg-light modal-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Ganti Sandi</h2>
            <button type="button" class="btn-close-ghost" onclick="closeEditSandi()">
                <i class="material-icons">close</i>
            </button>
        </div>

        <?php if ($msgSandi) { ?>
        <div class="pemberitahuan my-2 mx-auto" style="width: fit-content;" role="alert">
            <?= $msgSandi; ?>
        </div>
        <?php } ?>

        <form action="/editsandi/order" method="post" novalidate>
            <div class="mb-2">
                <label class="mb-1">Sandi baru Anda</label>
                <input type="password" name="sandi" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="mb-1">Konfirmasi Sandi</label>
                <input type="password" class="form-control" oninput="konfirmasiSandi(event)" required
                    name="sandiKonfirm">
                <div class="invalid-feedback">
                    Kata sandi tidak cocok
                </div>
            </div>
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn-default">Ubah</button>
                <button type="button" class="btn-teks-aja" onclick="closeEditSandi()">Batal</button>
            </div>
        </form>
    </div>
</div>

<div class="container konten baris-ke-kolom">
    <!-- Kolom kanan (ringkasan akun) ditaruh duluan di DOM kecil? tetap: tidak ubah sistem -->
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4 account-card">
            <h2>Akun Saya</h2>

            <div class="account-row mt-2">
                <p>Email</p>
                <p class="fw-bold"><?= $email; ?></p>
            </div>

            <div class="account-row">
                <p>Sandi</p>
                <a class="btn-teks-aja" onclick="openGantiSandi()">Ganti Sandi</a>
            </div>

            <span class="garis my-2"></span>

            <a href="/account" class="mb-2 btn-default-abu w-100 text-center">Profile</a>
            <a href="/order" class="mb-2 btn-default-abu w-100 text-center">Pesanan</a>
            <a href="/logout" class="btn-default-merah w-100 text-center">Keluar</a>
        </div>
    </div>

    <!-- Kolom kiri (daftar pesanan) -->
    <div style="flex:1;">
        <div class="mb-4">
            <h1 class="teks-sedang m-0" style="letter-spacing:-.02em;">Pesanan</h1>
            <p class="m-0" style="color: grey;">
                <?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pemesanan
            </p>
        </div>

        <!-- Desktop -->
        <div style="overflow-x: auto;" class="show-block-ke-hide">
            <div class="container-table">
                <div class="header-table">
                    <div>ID Pesanan</div>
                    <div>Tanggal</div>
                    <div>Penerima</div>
                    <div>Harga</div>
                    <div>Status</div>
                </div>

                <?php foreach ($pesanan as $ind_p => $p) { ?>
                <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)" title="Lihat detail pesanan">
                    <div class="text-truncate"><?= $p['id_midtrans']; ?></div>
                    <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                        ?>
                    <div><?= $tgl; ?></div>
                    <div class="text-truncate"><?= $p['nama']; ?></div>
                    <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                    <div>
                        <?php
                          $s = strtolower($p['status']);
                          $cls = 'badge-status ';
                          if(in_array($s, ['proses','capture','settlement'])) $cls.='proses';
                          elseif($s==='menunggu pembayaran' || $s==='pending') $cls.='menunggu';
                          elseif($s==='kadaluarsa' || $s==='expire') $cls.='kadaluarsa';
                          elseif($s==='ditolak' || $s==='deny' || $s==='gagal' || $s==='failure') $cls.='gagal';
                          elseif($s==='dibatalkan' || $s==='cancel') $cls.='dibatalkan';
                          elseif(strpos($s,'refund')!==false) $cls.='refund';
                          else $cls.='kadaluarsa';
                        ?>
                        <span class="<?= $cls; ?>"><?= $p['status']; ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- Mobile -->
        <div style="overflow-x: auto;" class="hide-ke-show-block">
            <div class="container-table" style="width: 660px;">
                <div class="header-table">
                    <div>ID Pesanan</div>
                    <div>Tanggal</div>
                    <div>Penerima</div>
                    <div>Harga</div>
                    <div>Status</div>
                </div>

                <?php foreach ($pesanan as $ind_p => $p) { ?>
                <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)">
                    <div class="text-truncate"><?= $p['id_midtrans']; ?></div>
                    <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                        ?>
                    <div><?= $tgl; ?></div>
                    <div class="text-truncate"><?= $p['nama']; ?></div>
                    <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                    <div>
                        <?php
                          $s = strtolower($p['status']);
                          $cls = 'badge-status ';
                          if(in_array($s, ['proses','capture','settlement'])) $cls.='proses';
                          elseif($s==='menunggu pembayaran' || $s==='pending') $cls.='menunggu';
                          elseif($s==='kadaluarsa' || $s==='expire') $cls.='kadaluarsa';
                          elseif($s==='ditolak' || $s==='deny' || $s==='gagal' || $s==='failure') $cls.='gagal';
                          elseif($s==='dibatalkan' || $s==='cancel') $cls.='dibatalkan';
                          elseif(strpos($s,'refund')!==false) $cls.='refund';
                          else $cls.='kadaluarsa';
                        ?>
                        <span class="<?= $cls; ?>"><?= $p['status']; ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas detail -->
<div class="container-offcanvas" style="z-index: 100;">
    <div class="d-flex justify-content-end">
        <button class="btn btn-light btn-close-ghost" onclick="closeDetail()" title="Tutup">
            <i class="material-icons">close</i>
        </button>
    </div>

    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="m-0 w-50" style="letter-spacing: -1px;">ID Pesanan</p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="id">#ILENA00001</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;">Nama Penerima</p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="nama">—</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;">Nohp Penerima</p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="nohp">—</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;">Alamat Penerima</p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="alamat">—</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;">Email Pemesan</p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="email">—</p>
    </div>

    <h3 class="my-3">Produk</h3>
    <div id="item">
        <div class="d-flex mb-1 border-bottom pb-1">
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p>
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p>
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p>
        </div>
    </div>

    <span class="garis my-4"></span>

    <div class="d-flex justify-content-center gap-2">
        <a href="" class="btn-default" id="btn-invoice">Invoice</a>
        <a href="" class="btn-default" id="btn-detail">Detail Pemesanan</a>
    </div>

    <div class="my-3 text-center" style="color:var(--muted); font-size:.85rem;">
        Tekan <b>Esc</b> atau tombol <i class="material-icons" style="font-size:1rem;vertical-align:-2px;">close</i>
        untuk menutup.
    </div>
</div>

<script>
/* Data asli dari controller (tetap) */
const pesanan = JSON.parse('<?= $pesananJson; ?>');

/* DOM refs */
const idElm = document.getElementById("id");
const namaElm = document.getElementById("nama");
const nohpElm = document.getElementById("nohp");
const alamatElm = document.getElementById("alamat");
const emailElm = document.getElementById("email");
const itemElm = document.getElementById("item");
const containerOffcanvasElm = document.querySelector(".container-offcanvas");
const btnInvoiceElm = document.getElementById('btn-invoice');
const btnDetailElm = document.getElementById('btn-detail');

const sandiElm = document.querySelector('input[name="sandi"]');
const editSandiElm = document.getElementById('edit-sandi');

/* Helpers */
function rupiah(n) {
    try {
        return new Intl.NumberFormat('id-ID').format(Number(n || 0));
    } catch (e) {
        return n;
    }
}

/* Edit sandi overlay */
function openGantiSandi() {
    editSandiElm.classList.remove('d-none');
    editSandiElm.classList.add('d-flex');
}

function closeEditSandi() {
    editSandiElm.classList.add('d-none');
    editSandiElm.classList.remove('d-flex');
}

function konfirmasiSandi(e) {
    if (sandiElm.value !== e.target.value) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
}
/* Tutup overlay sandi saat klik di luar kartu */
editSandiElm.addEventListener('click', (ev) => {
    if (ev.target === editSandiElm) closeEditSandi();
});

/* Offcanvas detail pesanan */
function openDetail(indP) {
    const p = pesanan[indP];
    if (!p) return;

    idElm.textContent = p.id_midtrans || '-';
    namaElm.textContent = p.nama || '-';
    nohpElm.textContent = p.nohp || '-';
    alamatElm.textContent = p.alamat || '-';
    emailElm.textContent = p.email || '-';

    // header
    itemElm.innerHTML =
        '<div class="d-flex mb-1 border-bottom pb-1">' +
        '<p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p>' +
        '<p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p>' +
        '<p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p>' +
        '</div>';

    (p.items || []).forEach(el => {
        if (el.name !== 'Biaya Ongkir' && el.name !== 'Biaya Admin') {
            itemElm.innerHTML +=
                '<div class="d-flex py-1">' +
                '<p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + (el
                    .name || '-') + '</p>' +
                '<p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + (el
                    .quantity || 0) + '</p>' +
                '<p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp ' +
                rupiah(el.price) + '</p>' +
                '</div>';
        }
    });

    btnInvoiceElm.href = '/invoice/' + p.id_midtrans;
    btnDetailElm.href = '/orderdetail/' + (String(p.status || '').toLowerCase()) + '?idorder=' + p.id_midtrans;

    containerOffcanvasElm.classList.add("show");
}

function closeDetail() {
    containerOffcanvasElm.classList.remove("show");
}
/* Tutup offcanvas saat tekan ESC */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDetail();
});
</script>

<?= $this->endSection(); ?>