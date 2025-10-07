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
    header('Location: ' . '/orderdetail/' . strtolower($pemesananSelected['status']) . '?idorder=' . $id_order);
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
            $va_number = $dataMid['va_numbers'][0]['va_number'] ?? '';
            $bank = $dataMid['va_numbers'][0]['bank'] ?? 'bca';
        }
        break;
    case 'echannel':
        $va_number = $dataMid['bill_key'];
        $biller_code = $dataMid['biller_code'];
        $bank = "mandiri";
        break;
    case 'gopay':
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
$carapembayaranSelected = $carapembayaran[$bank] ?? [];

$waktuExpire = strtotime($dataMid['expiry_time']);
$waktuCurr   = strtotime("+7 Hours");
$waktuSelisih = $waktuExpire - $waktuCurr;
$waktu = date("H:i:s", $waktuSelisih);
$bulan = $bulan ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$waktuExpireFix = date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire);

/* === Breakdown pembayaran dari $items === */
$subtotal = 0;
$diskonVoucher = 0;
$diskonFlash = 0;
$biayaAdmin = 0;
foreach ($items as $it) {
    $name = strtolower($it['name']);
    if ($name === 'voucher') {
        $diskonVoucher += abs((int)$it['price']);
    } elseif ($name === 'flash sale') {
        $diskonFlash += abs((int)$it['price']);
    } elseif ($name === 'biaya admin') {
        $biayaAdmin += (int)$it['price'];
    } else {
        if ($name !== 'biaya ongkir') {
            $subtotal += ((int)$it['price'] * (int)$it['quantity']);
        }
    }
}
$grossAmount = (int)($dataMid['gross_amount'] ?? ($subtotal - $diskonVoucher - $diskonFlash + $biayaAdmin));
$statusText = $pemesananSelected['status'] ?? 'Menunggu Pembayaran';

/* === FILTER: hanya produk fisik untuk tabel Ringkasan Pesanan === */
$excludeNames = ['voucher','flash sale','biaya admin','biaya ongkir'];
$showItems = array_values(array_filter($items, function($it) use ($excludeNames) {
    $nm = strtolower($it['name']);
    if (in_array($nm, $excludeNames)) return false;
    // jika ada item potongan lain yang bukan produk
    if (strpos($nm, 'potongan') !== false) return false;
    return true;
}));
?>

<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* ===== Scoped: order-simple (ringan & elegan) ===== */
.order-simple {
    max-width: 960px;
    margin: 16px auto 28px;
    padding: 0 12px;
    font-size: 13.5px;
    color: #111827;
}

.order-simple .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 992px) {
    .order-simple .grid {
        grid-template-columns: 1fr;
    }
}

.order-simple .card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
}

.order-simple .pad {
    padding: 14px;
}

.order-simple .hr {
    border: 0;
    border-top: 1px dashed #e5e7eb;
    margin: 12px 0;
}

.order-simple .muted {
    color: #6b7280;
}

.order-simple .title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 6px;
    letter-spacing: -.02em;
}

.order-simple .eyebrow {
    font-size: 11.5px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.order-simple .pill {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 12px;
}

.order-simple .chip {
    background: var(--dark);
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    gap: 6px;
    align-items: center;
}

.order-simple .num-lg {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
    margin: 0;
}

.order-simple .num-md {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.02em;
    margin: 0;
}

.order-simple .row-center {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.order-simple .between {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
}

.order-simple .center {
    text-align: center;
}

.order-simple .copy-btn {
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 8px;
    padding: 6px 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #374151;
}

.order-simple .copy-btn:hover {
    background: #f9fafb;
}

.order-simple .method-logo {
    width: 84px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 6px auto 10px;
}

.order-simple .qris {
    width: 200px;
    max-width: 78vw;
    height: auto;
    border: 1px solid #eee;
    border-radius: 10px;
    display: block;
    margin: 8px auto;
}

.order-simple .kvs {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-items: center;
    padding: 10px 12px;
    border: 1px dashed #e5e7eb;
    border-radius: 10px;
    background: #fafafa;
}

.order-simple .kvs .k {
    font-size: 12px;
    color: #6b7280;
}

.order-simple .kvs .v {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: .2px;
}

.order-simple .table-mini .head,
.order-simple .table-mini .row {
    display: grid;
    grid-template-columns: 1.2fr .5fr .7fr;
    gap: 6px;
    align-items: center;
}

.order-simple .table-mini .head {
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11.5px;
    text-transform: uppercase;
    color: #6b7280;
}

.order-simple .table-mini .row {
    padding: 8px 0;
    border-bottom: 1px solid #fafafa;
}

.order-simple .table-mini .row:last-child {
    border-bottom: 0;
}

.order-simple .table-mini .scroll {
    max-height: 240px;
    overflow: auto;
}

.order-simple .list-line {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    padding: 6px 0;
}

.order-simple .list-line.total {
    border-top: 1px dashed #e5e7eb;
    margin-top: 6px;
    padding-top: 10px;
    font-weight: 800;
}

.order-simple .kv {
    display: grid;
    grid-template-columns: 110px 1fr;
    gap: 8px;
    padding: 5px 0;
}

@media (max-width:520px) {
    .order-simple .kv {
        grid-template-columns: 1fr;
    }
}

/* Toast copy */
.order-simple .toast {
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
    z-index: 9999;
}

.order-simple .toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-4px);
}

/* Sticky CTA mobile */
@media (max-width:768px) {
    .order-simple .cta-sticky {
        position: sticky;
        bottom: 10px;
        z-index: 2;
        display: flex;
        justify-content: center;
    }

    .order-simple .cta-sticky .btn-default-merah {
        width: 100%;
        max-width: 480px;
    }
}
</style>

<div class="order-simple">
    <!-- Header: ID & Status -->
    <div class="between" style="margin-bottom:10px;">
        <div class="between" style="gap:8px;">
            <span class="chip"><i class="material-icons" style="font-size:16px;">receipt_long</i> ID:
                <b><?= $pemesananSelected['id_midtrans']; ?></b></span>
            <span class="pill"><?= $statusText; ?></span>
        </div>
        <button class="copy-btn" onclick="copytext('<?= $pemesananSelected['id_midtrans']; ?>')"
            title="Salin ID Pesanan">
            <i class="material-icons" style="font-size:16px;">content_copy</i> Salin
        </button>
    </div>

    <div class="card pad">
        <!-- Ringkasan singkat -->
        <div class="grid pad" style="padding-top:0;">
            <div class="center">
                <p class="eyebrow m-0">Total Tagihan</p>
                <div class="row-center" style="margin-top:4px;">
                    <p class="num-lg m-0">Rp <?= number_format($grossAmount, 0, ',', '.'); ?></p>
                    <button class="copy-btn" onclick="copytext('<?= (int)$grossAmount; ?>')" title="Salin nominal">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                </div>
            </div>
            <div class="center">
                <p class="eyebrow m-0">Hitung Mundur</p>
                <p class="num-md waktu m-0" style="margin-top:4px;"><?= $waktu; ?></p>
                <p class="muted m-0" style="margin-top:2px;">Batas: <?= $waktuExpireFix; ?> WIB</p>
            </div>
        </div>

        <div id="expiredNote" class="muted d-none center" style="margin-top:6px;">
            Waktu pembayaran habis. Jika sudah transfer, status akan ter-update otomatis. Kamu bisa cek di menu
            <a href="/order">Pesanan</a>.
        </div>

        <hr class="hr">

        <!-- Dua kolom: Metode & Ringkasan -->
        <div class="grid pad" style="padding-top:0;">
            <!-- Metode Pembayaran -->
            <div class="card pad">
                <div class="between" style="align-items:flex-start;">
                    <div>
                        <p class="title">Metode Pembayaran</p>
                        <p class="muted m-0">Gunakan info berikut untuk menyelesaikan pembayaran.</p>
                    </div>
                    <span class="pill"><?= strtoupper($bank); ?></span>
                </div>
                <img class="method-logo" src="/img/pembayaran/<?= $bank; ?>.webp" alt="<?= $bank; ?>">

                <?php if ($bank === 'qris'): ?>
                <div class="center">
                    <img class="qris" src="<?= $va_number; ?>" alt="QRIS">
                    <p class="muted m-0">Scan QR via aplikasi pembayaran yang mendukung QRIS.</p>
                </div>

                <?php elseif ($bank === 'mandiri' && !empty($biller_code)): ?>
                <div class="kvs" style="margin-top:6px;">
                    <div>
                        <div class="k">Biller Code</div>
                        <div class="v"><?= $biller_code; ?></div>
                    </div>
                    <button class="copy-btn" onclick="copytext('<?= $biller_code; ?>')" title="Salin Biller Code">
                        <i class="material-icons" style="font-size:16px;">content_copy</i></button>
                </div>
                <div class="kvs" style="margin-top:6px;">
                    <div>
                        <div class="k">Virtual Account</div>
                        <div class="v"><?= $va_number; ?></div>
                    </div>
                    <button class="copy-btn" onclick="copytext('<?= $va_number; ?>')" title="Salin VA">
                        <i class="material-icons" style="font-size:16px;">content_copy</i></button>
                </div>
                <p class="muted m-0" style="margin-top:6px;">Masukkan Biller Code & VA saat bayar via ATM/MBanking
                    Mandiri.</p>

                <?php elseif ($bank === 'card'): ?>
                <p class="muted m-0">Pembayaran kartu diproses otomatis. Jika status belum berubah, tunggu beberapa saat
                    atau lihat panduan di bawah.</p>

                <?php else: ?>
                <div class="kvs" style="margin-top:6px;">
                    <div>
                        <div class="k">
                            <?= ($bank === 'toko' || $bank === 'market') ? 'Keterangan' : 'Virtual Account'; ?></div>
                        <div class="v"><?= $va_number; ?></div>
                    </div>
                    <?php if(!empty($va_number) && $bank !== 'toko' && $bank !== 'market'): ?>
                    <button class="copy-btn" onclick="copytext('<?= $va_number; ?>')" title="Salin VA">
                        <i class="material-icons" style="font-size:16px;">content_copy</i></button>
                    <?php endif; ?>
                </div>
                <p class="muted m-0" style="margin-top:6px;">Transfer sesuai metode bank yang dipilih.</p>
                <?php endif; ?>
            </div>

            <!-- Ringkasan Pesanan & Pembayaran & Penerima -->
            <div class="card pad">
                <p class="title">Ringkasan Pesanan</p>
                <div class="table-mini mt-1">
                    <div class="head">
                        <div class="center">Nama</div>
                        <div class="center">Jumlah</div>
                        <div class="center">Harga</div>
                    </div>
                    <div class="scroll">
                        <?php foreach ($showItems as $i) { ?>
                        <div class="row">
                            <p class="m-0 center fw-bold"><?= $i['name']; ?></p>
                            <p class="m-0 center fw-bold"><?= (int)$i['quantity']; ?></p>
                            <p class="m-0 center fw-bold">Rp <?= number_format((int)$i['price'], 0, ',', '.'); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <hr class="hr">

                <p class="title">Rincian Pembayaran</p>
                <div class="mt-1">
                    <div class="list-line">
                        <span class="muted">Subtotal</span>
                        <span><b>Rp <?= number_format($subtotal, 0, ',', '.'); ?></b></span>
                    </div>
                    <?php if ($diskonVoucher > 0): ?>
                    <div class="list-line">
                        <span class="muted">Diskon Voucher</span>
                        <span>- Rp <?= number_format($diskonVoucher, 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($diskonFlash > 0): ?>
                    <div class="list-line">
                        <span class="muted">Flash Sale</span>
                        <span>- Rp <?= number_format($diskonFlash, 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($biayaAdmin > 0): ?>
                    <div class="list-line">
                        <span class="muted">Biaya Admin</span>
                        <span>Rp <?= number_format($biayaAdmin, 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="list-line total">
                        <span>Total Bayar</span>
                        <span>Rp <?= number_format($grossAmount, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <hr class="hr">

                <p class="title">Info Penerima</p>
                <div class="mt-1">
                    <div class="kv"><span
                            class="muted">Nama</span><span><b><?= $pemesananSelected['nama']; ?></b></span></div>
                    <div class="kv"><span class="muted">No. HP</span><span><?= $pemesananSelected['nohp']; ?></span>
                    </div>
                    <div class="kv"><span class="muted">Alamat</span><span><?= $pemesananSelected['alamat']; ?></span>
                    </div>
                    <div class="kv"><span class="muted">Email</span><span><?= $pemesananSelected['email']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="hr">

        <!-- Langkah Pembayaran -->
        <div class="pad" style="padding-top:0;">
            <p class="title center">Langkah Pembayaran</p>
            <div class="accordion accordion-flush mt-1" id="accordionFlushExample">
                <?php foreach ($carapembayaranSelected as $ind_c => $c) { ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse<?= $ind_c ?>" aria-expanded="false"
                            aria-controls="flush-collapse<?= $ind_c ?>">
                            <?= $c['nama']; ?>
                        </button>
                    </h2>
                    <div id="flush-collapse<?= $ind_c ?>" class="accordion-collapse collapse"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body" style="font-size:13px; line-height:1.5;">
                            <p class="mb-0"><?= $c['isi']; ?></p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <hr class="hr">

        <!-- CTA -->
        <div class="pad cta-sticky" style="padding-top:0;">
            <a href="" class="btn-default-merah">Saya sudah membayar</a>
        </div>
    </div>
</div>

<!-- Toast copy -->
<div id="toastCopy" class="order-simple toast">Disalin</div>

<script>
// Countdown stabil pakai epoch server → tidak reload loop
const els = document.querySelectorAll(".waktu");
const expire = <?= (int)$waktuExpire ?> * 1000; // ms
const t = setInterval(() => {
    const now = Date.now();
    let s = expire - now;
    if (s <= 0) {
        clearInterval(t);
        els.forEach(e => e.textContent = "00:00:00");
        const note = document.getElementById('expiredNote');
        if (note) note.classList.remove('d-none');
        return;
    }
    const hh = String(Math.floor(s / 3600000)).padStart(2, '0');
    s %= 3600000;
    const mm = String(Math.floor(s / 60000)).padStart(2, '0');
    s %= 60000;
    const ss = String(Math.floor(s / 1000)).padStart(2, '0');
    els.forEach(e => e.textContent = `${hh}:${mm}:${ss}`);
}, 1000);

// Helper copy + toast
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