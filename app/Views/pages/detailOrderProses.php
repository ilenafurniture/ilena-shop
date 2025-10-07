<?php
$id_order = $_GET['idorder'];
$pemesananSelectedArr = array_filter($pemesananAll, function ($p) use ($id_order) {
    return $p['id_midtrans'] == $id_order;
});
if (count($pemesananSelectedArr) <= 0) {
    header('Location: /order');
    die();
}
$pemesananSelected = array_values($pemesananSelectedArr)[0];
if(in_array($pemesananSelected['status'], $statusSelain)){
    header('Location: '.'/orderdetail/'.strtolower($pemesananSelected['status']).'?idorder='.$id_order);
    die();
}

$dataMid = $pemesananSelected['data_mid'];
$biller_code = "";
$bank = "";
switch ($dataMid['payment_type']) {
    case 'bank_transfer':
        if (isset($dataMid['permata_va_number'])) {
            $va_number = $dataMid['permata_va_number'];
            $bank = "permata";
        } else {
            $va_number = $dataMid['va_numbers'][0]['va_number'];
            $bank = $dataMid['va_numbers'][0]['bank'];
        }
        break;
    case 'echannel':
        $va_number = $dataMid['bill_key'];
        $biller_code = $dataMid['biller_code'];
        $bank = "mandiri";
        break;
    case 'qris':
        $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
        $bank = "qris";
        break;
    case 'toko':
        $va_number = 'PEMBAYARAN TOKO';
        $bank = "toko";
        break;
    case 'market':
        $va_number = 'PEMBAYARAN MARKETPLACE';
        $bank = "market";
        break;
    case 'credit_card':
        $va_number = '';
        $bank = "card";
        break;
    default:
        $va_number = "";
        break;
}
$items = $pemesananSelected['items'];
$carapembayaranSelected = $carapembayaran[$bank] ?? [];

// waktu (pakai settlement/transaction kalau ada untuk halaman sukses)
$bulan = $bulan ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$waktuExpire = strtotime($dataMid['expiry_time']);
$waktuCurr = strtotime("+7 Hours");
$waktuSelisih = $waktuExpire - $waktuCurr;
$waktu = date("H:i:s", $waktuSelisih);
$waktuExpireFix = date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire);

$tsTransaksiRaw = $dataMid['settlement_time'] ?? ($dataMid['transaction_time'] ?? null);
$waktuTransaksiFix = null;
if ($tsTransaksiRaw) {
    $tsTr = strtotime($tsTransaksiRaw);
    $waktuTransaksiFix = date("d", $tsTr) . " " . $bulan[(int)date("m", $tsTr) - 1] . " " . date("Y H:i:s", $tsTr) . " WIB";
}

// kurir aman
$kurir = $pemesananSelected['kurir'] ?? [];
if (!is_array($kurir)) {
    $kurir = json_decode($kurir, true) ?: [];
}

// filter hanya produk
$excludeNames = ['voucher','flash sale','biaya admin','biaya ongkir'];
$showItems = array_values(array_filter($items, function($it) use ($excludeNames){
    $nm = strtolower($it['name']);
    if (in_array($nm, $excludeNames)) return false;
    if (strpos($nm, 'potongan') !== false) return false;
    return true;
}));
$grossAmount = (int)($dataMid['gross_amount'] ?? 0);
?>
<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* ===== Scoped: order-success (modern & responsif) ===== */
.order-success {
    max-width: 980px;
    margin: 16px auto 28px;
    padding: 0 12px;
    font-size: 13.5px;
    color: #111827
}

.order-success .card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px
}

.order-success .pad {
    padding: 14px
}

.order-success .hr {
    border: 0;
    border-top: 1px dashed #e5e7eb;
    margin: 12px 0
}

.order-success .title {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: -.02em;
    margin: 0 0 6px
}

.order-success .muted {
    color: #6b7280
}

.order-success .center {
    text-align: center
}

.order-success .between {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap
}

.order-success .chip {
    background: var(--dark);
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    gap: 6px;
    align-items: center
}

.order-success .pill {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 12px
}

.order-success .num-lg {
    font-size: clamp(16px, 2.6vw, 18px);
    font-weight: 800;
    letter-spacing: -.02em;
    margin: 0
}

.order-success .copy-btn {
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 8px;
    padding: 6px 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #374151
}

.order-success .copy-btn:hover {
    background: #f9fafb
}

/* Grid auto-fit → rapi di berbagai lebar */
.order-success .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 14px
}

/* Tabel item: bisa scroll horizontal di layar kecil */
.order-success .table-mini {
    overflow-x: auto;
    border-radius: 8px
}

.order-success .table-mini .head,
.order-success .table-mini .row {
    display: grid;
    grid-template-columns: minmax(140px, 1fr) 80px 140px;
    gap: 8px;
    align-items: center;
    min-width: 420px
}

.order-success .table-mini .head {
    padding: 8px 6px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11.5px;
    text-transform: uppercase;
    color: #6b7280;
    background: #fafafa
}

.order-success .table-mini .row {
    padding: 10px 6px;
    border-bottom: 1px solid #fafafa
}

.order-success .table-mini .row:last-child {
    border-bottom: 0
}

/* Info pembeli: alamat & ID wrap aman */
.order-success .kv {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 8px;
    padding: 5px 0
}

.order-success .kv span:last-child {
    word-break: break-word;
    white-space: pre-line
}

@media (max-width:560px) {
    .order-success .kv {
        grid-template-columns: 1fr
    }
}

.order-success .method-logo {
    width: 84px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 6px auto 10px
}

/* Toast copy */
.order-success .toast {
    position: fixed;
    left: 50%;
    bottom: 18px;
    transform: translateX(-50%);
    background: #111827;
    color: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    opacity: 0;
    pointer-events: none;
    transition: .25s ease;
    z-index: 9999
}

.order-success .toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-4px)
}

/* Success icon */
.order-success .success i {
    font-size: 42px;
    color: #22c55e
}

/* Header ID band: biar ID panjang nggak pecah layout */
.idband {
    overflow-wrap: anywhere
}

/* Fine tune mobile */
@media (max-width:420px) {
    .order-success .copy-btn {
        padding: 5px 7px
    }

    .order-success .table-mini .head,
    .order-success .table-mini .row {
        grid-template-columns: minmax(120px, 1fr) 64px 120px;
        min-width: 360px
    }
}
</style>



<div class="order-success">

    <div class="py-2 text-light w-100 text-center"
        style="letter-spacing:-.2px;border-radius:8px;color: #000000   ;background: var(--dark);">
        ID Pesanan : <span id="transaction_id" class="fw-bold"
            style="color: var(--primary);"><?= $pemesananSelected['id_midtrans']; ?></span>
        <button class="copy-btn" onclick="copytext('<?= $pemesananSelected['id_midtrans']; ?>')"
            aria-label="Salin ID Pesanan">
            <i class="material-icons" style="font-size:16px;">content_copy</i>
        </button>
    </div>
    <hr>
    <h1 class="header-title text-center mb-3 mt-4" style="font-size:22px;letter-spacing:-.5px;">Pembayaran Berhasil</h1>
    <div class="success center mb-2"><i class="material-icons">check_circle</i></div>
    <hr>

    <!-- Identitas Pembeli -->
    <div class="card pad mb-3">
        <p class="title center">Identitas Pembeli</p>
        <div class="kv"><span class="muted">Nama</span><span><b><?= $pemesananSelected['nama']; ?></b></span></div>
        <div class="kv"><span class="muted">Email</span><span><?= $pemesananSelected['email']; ?></span></div>
        <div class="kv"><span class="muted">Telepon</span><span><?= $pemesananSelected['nohp']; ?></span></div>
        <div class="kv"><span class="muted">Alamat</span><span><?= $pemesananSelected['alamat']; ?></span></div>
    </div>

    <!-- Nominal + Metode -->
    <div class="card pad mb-3">
        <div class="grid">
            <div class="center">
                <p class="muted m-0" style="font-size:11.5px;letter-spacing:.06em;text-transform:uppercase;">Jumlah
                    Tagihan</p>
                <div style="display:inline-flex;align-items:center;gap:8px;margin-top:4px;">
                    <div id="transaction_value" class="d-none"><?= (int)$grossAmount; ?></div>
                    <p class="num-lg m-0">Rp <?= number_format($grossAmount, 0, ',', '.'); ?></p>
                    <button class="copy-btn" onclick="copytext('<?= (int)$grossAmount; ?>')" title="Salin nominal"
                        aria-label="Salin jumlah tagihan">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                </div>
            </div>
            <div class="center">
                <p class="muted m-0" style="font-size:11.5px;letter-spacing:.06em;text-transform:uppercase;">Metode
                    Pembayaran</p>
                <?php if (($dataMid['payment_type'] ?? '') === 'credit_card') { ?>
                <p class="m-0" style="font-weight:700;">Kartu Kredit/Debit</p>
                <?php } else { ?>
                <img class="method-logo" src="/img/pembayaran/<?= $bank; ?>.webp" alt="<?= $bank; ?>">
                <?php } ?>
                <?php if ($waktuTransaksiFix): ?>
                <p class="muted m-0">Waktu transaksi: <?= $waktuTransaksiFix; ?></p>
                <?php else: ?>
                <p class="muted m-0">Waktu transaksi: -</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ringkasan Item -->
    <div class="card pad mb-3">
        <p class="title">Ringkasan Item</p>
        <?php if (count($showItems) > 0): ?>
        <div class="table-mini mt-1">
            <div class="head">
                <div class="center">Nama</div>
                <div class="center">Jumlah</div>
                <div class="center">Harga</div>
            </div>
            <?php foreach ($showItems as $i) { ?>
            <div class="row">
                <p class="m-0 center fw-bold"><?= $i['name']; ?></p>
                <p class="m-0 center fw-bold"><?= (int)$i['quantity']; ?></p>
                <p class="m-0 center fw-bold">Rp <?= number_format((int)$i['price'], 0, ',', '.'); ?></p>
            </div>
            <?php } ?>
        </div>
        <?php else: ?>
        <p class="muted m-0">Tidak ada item produk untuk ditampilkan.</p>
        <?php endif; ?>
    </div>

    <!-- Ekspedisi & Resi -->
    <div class="card pad mb-3">
        <p class="title">Pengiriman</p>
        <div>
            <p class="m-0 muted">Ekspedisi</p>
            <?php if ($pemesananSelected['resi'] != 'Menunggu pengiriman' && !empty($kurir) && !empty($kurir['nama'])) { ?>
            <div class="between" style="gap:10px;margin-top:6px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <img src="/img/kurir/<?= strtolower(explode(" ", $kurir['nama'])[0]); ?>.png" alt=""
                        style="width:70px;object-fit:contain">
                    <div>
                        <p class="m-0 fw-bold"><?= strtoupper($kurir['nama']); ?> <?= $kurir['deskripsi'] ?? ''; ?></p>
                        <?php if (isset($kurir['estimasi'])) { ?>
                        <p class="m-0 muted">Estimasi <?= $kurir['estimasi']; ?> hari</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } else { ?>
            <div style="margin-top:6px;">
                <p class="m-0">Barang masih kami proses untuk pengiriman.</p>
                <p class="m-0 muted" style="font-size:12.5px">*Pesanan setelah jam 12:00 diproses di hari berikutnya.
                </p>
            </div>
            <?php } ?>

            <div style="margin-top:10px;">
                <p class="m-0 muted">Resi</p>
                <div style="display:flex;align-items:center;gap:6px;margin-top:4px;flex-wrap:wrap;">
                    <span style="font-weight:700;word-break:break-word"><?= $pemesananSelected['resi']; ?></span>
                    <?php if ($pemesananSelected['resi'] != 'Menunggu pengiriman') { ?>
                    <button class="copy-btn" onclick="copytext('<?= $pemesananSelected['resi']; ?>')" title="Salin Resi"
                        aria-label="Salin nomor resi">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                    <?php } ?>
                </div>
            </div>

            <div style="margin-top:10px;">
                <p class="m-0 muted">Status Pesanan</p>
                <span class="badge rounded-pill <?= getStatusClass($pemesananSelected['status']); ?>"
                    style="margin-top:4px;">
                    <?= ucfirst($pemesananSelected['status']); ?>
                </span>
            </div>
        </div>
    </div>

    <?php if (session()->get('isLogin')) { ?>
    <div class="center">
        <a href="/order" class="btn-order"
            style="display:inline-block;padding:10px 18px;background:#ff4747;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
            Pesanan Saya
        </a>
    </div>
    <?php } ?>
</div>

<?php
function getStatusClass($status) {
    switch ($status) {
        case 'Menunggu Pembayaran': return "text-bg-primary";
        case 'Proses': return "text-bg-warning";
        case 'Dikirim': return "text-bg-info";
        case 'Selesai': return "text-bg-success";
        case 'Dibatalkan':
        case 'Gagal': return "text-bg-danger";
        default: return "text-bg-dark";
    }
}
?>

<div id="toastCopy" class="order-success toast">Disalin</div>

<script>
// helper copy + toast
function copytext(text) {
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(String(text));
    } else {
        const ta = document.createElement('textarea');
        ta.value = String(text);
        ta.style.position = 'fixed';
        ta.style.top = '-1000px';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy');
        } catch (e) {}
        document.body.removeChild(ta);
    }
    const toast = document.getElementById('toastCopy');
    if (!toast) return;
    toast.textContent = 'Disalin';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 1200);
}
</script>

<?= $this->endSection(); ?>