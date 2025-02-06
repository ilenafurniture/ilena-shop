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

<h1 class="header-title text-center mb-4 mt-4">Pembayaran Berhasil</h1>

<!-- Animasi Success -->
<div class="success-animation text-center">
    <i class="material-icons">check_circle</i>
</div>

<div class="content-center mb-3">
    <p class="text-center content-text">Pesanan akan segera kami proses. Simpan URL halaman ini untuk melihat status
        pesanan. Atau dapat login sebagai member kami untuk melihat seluruh pesanan Anda.</p>
</div>

<div class="py-2 text-light w-100 text-center" style="background-color: var(--dark); letter-spacing: -1px;">
    ID Pesanan : <b id="transaction_id"><?= $pemesananSelected['id_midtrans']; ?></b>
    <button class="copy-btn" onclick="copytext('<?= $pemesananSelected['id_midtrans']; ?>')">
        <i class="material-icons">content_copy</i></button>
</div>

<div class="order-content mx-auto">
    <!-- Identitas Pembeli -->
    <div class="buyer-info mb-4 border-bottom pb-3">
        <h4 class="mb-3 text-center">Identitas Pembeli</h4>
        <div class="d-flex flex-column flex-md-row justify-content-between">
            <div class="buyer-details">
                <p id="buyer_name"><strong>Nama:</strong> <?= $pemesananSelected['nama']; ?></p>
                <p id="buyer_email"><strong>Email:</strong> <?= $pemesananSelected['email']; ?></p>
                <p id="buyer_phone"><strong>Telepon:</strong> <?= $pemesananSelected['nohp']; ?></p>
            </div>
            <div class="buyer-address mt-3 mt-md-0">
                <p id="buyer_address"><strong>Alamat:</strong> <?= $pemesananSelected['alamat']; ?></p>
            </div>
        </div>
    </div>

    <!-- Jumlah Tagihan dan Metode Pembayaran -->
    <div class="payment-info mb-4">
        <div class="payment-row d-flex justify-content-center align-items-center">
            <!-- Jumlah Tagihan -->
            <div class="payment-detail text-center">
                <p class="payment-title">Jumlah Tagihan</p>
                <div class="d-flex align-items-end justify-content-center gap-2">
                    <div id="transaction_value" class="d-none"><?= (int)$dataMid['gross_amount']; ?></div>
                    <h3 class="m-0">Rp <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></h3>
                    <button class="copy-btn mb-1" onclick="copytext('<?= (int)$dataMid['gross_amount']; ?>')">
                        <i class="material-icons">content_copy</i>
                    </button>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="payment-method text-center">
                <p class="payment-title">Metode Pembayaran</p>
                <?php if ($dataMid['payment_type'] == 'credit_card') { ?>
                <h4 class="m-0">Kartu Kredit</h4>
                <img class="payment-icon" src="/img/pembayaran/kartu_kredit_icon.png" alt="Kartu Kredit">
                <?php } else { ?>
                <img class="payment-method-logo" src="/img/pembayaran/<?= $bank; ?>.webp" alt="">
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Daftar Item -->
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

    <!-- Ekspedisi dan Resi -->
    <div class="row-info justify-content-between w-100 mb-4 pb-3">
        <div style="flex: 1">
            <p class="mb-2">Ekspedisi</p>
            <?php if ($pemesananSelected['resi'] != 'Menunggu pengiriman') { ?>
            <div class="d-flex justify-content-between gap-2">
                <img src="/img/kurir/<?= strtolower(explode(" ", $kurir['nama'])[0]); ?>.png" alt=""
                    class="courier-image">
                <div style="flex: 1;">
                    <p class="mb-0 fw-bold"><?= strtoupper($kurir['nama']) ?> <?= $kurir['deskripsi'] ?></p>
                    <?php if (isset($kurir['estimasi'])) { ?>
                    <p class="m-0">Estimasi pengiriman <?= $kurir['estimasi'] ?> Hari</p>
                    <?php } ?>
                </div>
            </div>
            <?php } else { ?>
            <div>
                <p class="m-0">Barang masih kami proses untuk pengiriman</p>
                <p class="m-0 text-secondary">*Jika pemesanan lebih dari jam 12:00 akan kami proses di esok harinya</p>
            </div>
            <?php } ?>
            <p class="mb-0 mt-3">Resi</p>
            <div class="d-flex gap-1">
                <p class="tracking-number"><?= $pemesananSelected['resi']; ?></p>
                <?php if ($pemesananSelected['resi'] != 'Menunggu pengiriman') { ?>
                <button class="copy-btn mb-1" onclick="copytext('<?= $pemesananSelected['resi']; ?>')"><i
                        class="material-icons">content_copy</i></button>
                <?php } ?>
            </div>
            <p class="mb-0 mt-3">Status Pesanan</p>
            <span
                class="badge rounded-pill <?= getStatusClass($pemesananSelected['status']); ?>"><?= ucfirst($pemesananSelected['status']); ?></span>
        </div>
    </div>

    <?php if (session()->get('isLogin')) { ?>
    <div class="w-100 d-flex justify-content-center mt-4">
        <a href="/order" class="btn-order">Pesanan Saya</a>
    </div>
    <?php } ?>
</div>

<!-- PHP Function untuk Status -->
<?php
function getStatusClass($status) {
    switch ($status) {
        case 'Menunggu Pembayaran':
            return "text-bg-primary";
        case 'Proses':
            return "text-bg-warning";
        case 'Dikirim':
            return "text-bg-info";
        case 'Selesai':
            return "text-bg-success";
        case 'Dibatalkan':
            return "text-bg-danger";
        case 'Gagal':
            return "text-bg-danger";
        default:
            return "text-bg-dark";
    }
}
?>




<?= $this->endSection(); ?>