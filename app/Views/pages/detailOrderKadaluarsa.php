<?php
// Pastikan id_order ada dalam URL
$id_order = $_GET['idorder'];
$pemesananSelectedArr = array_filter($pemesananAll, function ($p) use ($id_order) {
    return $p['id_midtrans'] == $id_order;
});
if (count($pemesananSelectedArr) <= 0) {
    header('Location: /order');
    die();
}

$pemesananSelected = array_values($pemesananSelectedArr)[0];

// Cek jika status pesanan tidak sesuai, redirect ke halaman yang sesuai
if (in_array($pemesananSelected['status'], $statusSelain)) {
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

<h1 class="teks-besar text-center mb-3 mt-4">Pesanan Kadaluarsa</h1>
<div class="py-1 text-light w-100 text-center" style="background-color: var(--dark); letter-spacing: -1px;">
    ID Pesanan : <b><?= $pemesananSelected['id_midtrans']; ?></b>
</div>

<div class="container">
    <div class="konten mx-auto" style="max-width: 1000px;">
        <!-- Menampilkan informasi tagihan -->
        <div class="row mb-3 pb-3 border-bottom">
            <div class="col-12 col-md-6">
                <p class="m-0">Jumlah Tagihan</p>
                <h3 class="m-0" style="font-size: 40px; letter-spacing: -3px; font-weight:600;">Rp
                    <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></h3>
            </div>
            <div class="col-12 col-md-6">
                <p class="m-0">Waktu Kadaluarsa</p>
                <h5 class="m-0"><?= $waktuExpireFix; ?></h5> <!-- Menampilkan waktu kadaluarsa -->
            </div>
        </div>

        <!-- Menampilkan nomor virtual account -->
        <div class="row mb-3 pb-3 border-bottom">
            <div class="col-12 col-md-6">
                <p class="m-0">Nomor Virtual Account</p>
                <img src="/img/pembayaran/<?= $bank; ?>.webp" alt="Pembayaran <?= $bank; ?>" class="img-fluid mb-3">
                <div class="d-flex align-items-end gap-2">
                    <h3 class="m-0" style="font-size: 40px; letter-spacing: -3px; font-weight:600;">
                        <?= $va_number; ?></h3>
                    <button class="btn-teks-aja hitam mb-1" onclick="copytext('<?= $va_number; ?>')"><i
                            class="material-icons">content_copy</i></button>
                </div>
                <p class="mb-3">Simpan nomor virtual account di atas untuk melakukan pembayaran sesuai bank yang telah
                    dipilih</p>
            </div>
        </div>

        <!-- Petunjuk Pembayaran -->
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
    </div>
</div>

<?= $this->endSection(); ?>

<script>
// Membuat fungsi countdown untuk waktu kadaluarsa (jika dibutuhkan)
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