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
    case'gopay':
        $va_number = $dataMid['payment_type'];
        $bank = "gopay";
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
$carapembayaranSelected = $carapembayaran[$bank];
$waktuExpire = strtotime($dataMid['expiry_time']);
$waktuCurr = strtotime("+7 Hours");
$waktuSelisih = $waktuExpire - $waktuCurr;
$waktu = date("H:i:s", $waktuSelisih);
$waktuExpireFix = date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire);
?>

<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
.order-content {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.row-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.header-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.transaction-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.transaction-info .payment-method {
    margin-top: 1rem;
}

.tracking-number {
    font-size: 16px;
}

.copy-btn {
    font-size: 18px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #333;
}

.courier-image {
    width: 100px;
    object-fit: contain;
    margin-bottom: 10px;
}

.btn-order {
    text-decoration: none;
    display: inline-block;
    padding: 12px 24px;
    background-color: #ff4747;
    color: white;
    border-radius: 5px;
    text-align: center;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.btn-order:hover {
    background-color: #e04040;
}

.content-text {
    font-size: 16px;
    line-height: 1.5;
    color: #333;
}

.buyer-info {
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.buyer-info h4 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
}

.buyer-info p {
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

.buyer-details,
.buyer-address {
    flex: 1;
}

/* Payment Section */
.payment-info {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.payment-detail,
.payment-method {
    flex: 1;
    padding: 15px;
}

.payment-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.payment-method {
    text-align: center;
}

.payment-icon {
    width: 50px;
    margin-top: 10px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.payment-method-logo {
    max-width: 150px;
    margin-top: 10px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.copy-btn {
    font-size: 18px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #ff4747;
}

/* Responsiveness */
@media (max-width: 768px) {
    .payment-row {
        flex-direction: column;
        text-align: center;
    }

    .payment-detail,
    .payment-method {
        padding: 10px;
        text-align: center;
    }

    .payment-method-logo {
        max-width: 120px;
    }

    .payment-title {
        font-size: 16px;
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .payment-detail h3 {
        font-size: 24px;
    }

    .buyer-info {
        padding: 8px;
    }

    .buyer-info h4 {
        font-size: 16px;
    }

    .buyer-info p {
        font-size: 13px;
    }

    .copy-btn {
        margin-top: 10px;
    }
}

/* Animasi Sukses */
.success-animation i {
    font-size: 50px;
    color: green;
    animation: bounce 1s ease infinite;
}

@keyframes bounce {
    0% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-10px);
    }

    100% {
        transform: translateY(0);
    }
}
</style>
<h1 class="header-title text-center mb-4 mt-4">Menunggu Pembayaran</h1>
<div class="py-1 text-light w-100 text-center" style="background-color: var(--dark); letter-spacing: -1px;">ID
    Pesanan :
    <b><?= $pemesananSelected['id_midtrans']; ?></b>
</div>
<div class="payment-info mb-4">
    <div class="konten mx-auto" style="max-width: 1000px;">
        <div class="baris-ke-kolom justify-content-between mb-2 W-100 mb-3 border-bottom pb-3">
            <div class="payment-detail text-center">
                <p class="payment-title">Jumlah Tagihan</p>
                <div class="d-flex align-items-end justify-content-center gap-2">
                    <div id="transaction_value" class="d-none"><?= (int)$dataMid['gross_amount']; ?></div>
                    <h3 class="m-0" style=" letter-spacing: -3px; font-weight:600;">Rp
                        <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></h3>
                    <button class="copy-btn mb-1" onclick="copytext('<?= (int)$dataMid['gross_amount']; ?>')">
                        <i class="material-icons">content_copy</i>
                    </button>
                </div>
            </div>
            <div class="payment-detail text-center">
                <div class="flex-column align-items-end show-flex-ke-hide">
                    <p class="m-0">Waktu Pembayaran</p>
                    <h3 class="m-0 waktu" style=" letter-spacing: -3px; font-weight:600;"><?= $waktu; ?>
                    </h3>
                    <p class="m-0">Selesaikan pembayaran Anda sebelum</p>
                    <p class="m-0"><?= $waktuExpire; ?> WIB</p>
                </div>
                <div class="flex-column hide-ke-show-flex">
                    <p class="m-0">Waktu Pembayaran</p>
                    <h3 class="m-0 waktu" style="font-size: 40px; letter-spacing: -3px; font-weight:600;"><?= $waktu; ?>
                    </h3>
                    <p class="m-0">Selesaikan pembayaran Anda sebelum</p>
                    <p class="m-0"><?= $waktuExpireFix; ?> WIB</p>
                </div>
            </div>
        </div>
        <div class="baris-ke-kolom mb-2 W-100 mb-3 border-bottom pb-3">
            <div style="flex: 1">
                <div>
                    <p class="payment-method text-center">Nomor Virtual Account</p>
                    <img class="payment-method-logo" src="/img/pembayaran/<?= $bank; ?>.webp" alt="">
                    <div class="d-flex align-items-end justify-content-center gap-2 my-4">
                        <h3 class="m-0" style=" letter-spacing: -3px; font-weight:600;">
                            <?= $va_number; ?></h3>
                        <button class="copy-btn mb-1" onclick="copytext('<?= $va_number; ?>')"><i
                                class="material-icons">content_copy</i></button>
                    </div>
                </div>
                <p class="mb-3 text-center">Simpan nomor virtual account diatas
                    untuk melakukan pembayaran sesuai bank yang telah
                    dipilih</p>
            </div>
            <div style="flex: 1;">
                <p class="text-center" style="font-size: 20px; letter-spacing: -1px; font-weight:600;">Produk yang
                    dibeli</p>
                <div class="order-items">
                    <div id="item-list">
                        <div class="d-flex mb-1 border-bottom pb-1">
                            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1;">Nama</p>
                            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1;">Jumlah</p>
                            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1;">Harga</p>
                        </div>
                        <?php foreach ($items as $i) { ?>
                        <div class="d-flex py-2">
                            <p class="d-block fw-bold m-0 text-center" style="flex: 1;"><?= $i['name']; ?></p>
                            <p class="d-block fw-bold m-0 text-center" style="flex: 1;"><?= $i['quantity']; ?></p>
                            <p class="d-block fw-bold m-0 text-center" style="flex: 1;">Rp
                                <?= number_format($i['price'], 0, ',', '.'); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center" style="font-size: 20px; letter-spacing: -1px; font-weight:600;">Petunjuk Pembayaran</p>
        <div class="accordion accordion-flush" id="accordionFlushExample">
            <?php foreach ($carapembayaranSelected as $ind_c => $c) { ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                        data-bs-target="#flush-collapse<?= $ind_c ?>" aria-expanded="false"
                        aria-controls="flush-collapse1">
                        <?= $c['nama']; ?>
                    </button>
                </h2>
                <div id="flush-collapse<?= $ind_c ?>" class="accordion-collapse collapse"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <p class="mb-0"><?= $c['isi']; ?></p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="w-100 d-flex justify-content-center mt-4">
            <a href="" class="btn-default-merah">Saya telah membayar</a>
        </div>
    </div>
</div>
<script>
const expiryTimeElm = document.querySelectorAll(".waktu");
const de = new Date('<?= $dataMid['expiry_time']; ?>');
const expireTime = de.getTime();
const dc = new Date();

setInterval(() => {
    const currTime = new Date().getTime();
    let dselisih = expireTime - currTime;

    const hours = String(Math.floor(dselisih / (1000 * 60 * 60))).padStart(2, '0');
    dselisih %= (1000 * 60 * 60);
    const minutes = String(Math.floor(dselisih / (1000 * 60))).padStart(2, '0');
    dselisih %= (1000 * 60);
    const seconds = String(Math.floor(dselisih / 1000)).padStart(2, '0');

    expiryTimeElm.forEach(elm => {
        elm.innerHTML = `${hours}: ${minutes}: ${seconds}`;
    })
    if (Number(hours) < 0 && Number(minutes) < 0 && Number(seconds) < 0) {
        window.location.reload();
    }
}, 1000);
</script>
<?= $this->endSection(); ?>