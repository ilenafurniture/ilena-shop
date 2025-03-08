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
if (in_array($pemesananSelected['status'], $statusSelain)) {
    header('Location: '.'/orderdetail/'.strtolower($pemesananSelected['status']).'?idorder='.$id_order);
    die();
}

$dataMid = $pemesananSelected['data_mid'];

// Mendefinisikan jumlah tagihan
$jumlahTagihan = number_format($dataMid['gross_amount'], 0, ',', '.');

// Mendefinisikan alasan pembatalan (gunakan nilai default jika tidak ada)
$alasanBatal = isset($pemesananSelected['alasan_batal']) ? $pemesananSelected['alasan_batal'] : 'Tidak Diketahui';

$items = $pemesananSelected['items'];
$waktuExpire = strtotime($dataMid['expiry_time']);
$waktuCurr = strtotime("+7 Hours");
$waktuSelisih = $waktuExpire - $waktuCurr;
$waktu = date("H:i:s", $waktuSelisih);
$waktuExpireFix = date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire);
?>

<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    color: #333;
    padding-top: 20px;
}

.header-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    text-align: center;
    margin-bottom: 30px;
}

.order-id {
    background-color: #333;
    color: white;
    font-size: 18px;
    padding: 10px 0;
    letter-spacing: -0.5px;
    text-align: center;
}

.order-summary {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-top: 20px;
}

.order-summary .row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
}

.order-summary .col-12 {
    flex: 1;
}

.order-summary h3 {
    font-size: 32px;
    font-weight: 600;
    letter-spacing: -1px;
}

.order-summary p {
    font-size: 18px;
}

.copy-btn {
    font-size: 18px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #ff4747;
}

.btn-order {
    text-decoration: none;
    padding: 12px 24px;
    background-color: #ff4747;
    color: white;
    border-radius: 5px;
    text-align: center;
    font-size: 16px;
    display: inline-block;
    margin-top: 30px;
    transition: background-color 0.3s ease;
}

.btn-order:hover {
    background-color: #e04040;
}

/* Responsiveness */
@media (max-width: 768px) {
    .order-id {
        font-size: 16px;
    }

    .order-summary h3 {
        font-size: 28px;
    }

    .order-summary p {
        font-size: 14px;
    }

    .btn-order {
        font-size: 14px;
        padding: 10px 20px;
    }
}
</style>
<div class="my-4">
    <h1 class="header-title">Pesanan Dibatalkan</h1>
    <div class="order-id">
        ID Pesanan: <b><?= $pemesananSelected['id_midtrans']; ?></b>
        <button class="copy-btn" onclick="copytext('<?= $pemesananSelected['id_midtrans']; ?>')">
            <i class="material-icons">content_copy</i>
        </button>
    </div>

    <div class="container">
        <div class="order-summary">
            <div class="row mb-3 pb-3 border-bottom">
                <div class="col-12 col-md-6">
                    <p class="m-0">Jumlah Tagihan</p>
                    <h3 class="m-0">Rp <?= $jumlahTagihan; ?></h3>
                </div>
                <div class="col-12 col-md-6">
                    <p class="m-0">Alasan Pembatalan</p>
                    <h5 class="m-0"><?= $alasanBatal; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="text-center">
            <a href="/order" class="btn-order">Kembali ke Pesanan Saya</a>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<script>
function copytext(text) {
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    alert('ID Pesanan telah disalin!');
}
</script>