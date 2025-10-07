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

$bulan = $bulan ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$waktuExpire = strtotime($dataMid['expiry_time']);
$waktuCurr = strtotime("+7 Hours");
$waktuSelisih = $waktuExpire - $waktuCurr;
$waktu = date("H:i:s", $waktuSelisih);
$waktuExpireFix = date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire);
?>

<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* ===== Scoped: order-expired (tidak ganggu halaman lain) ===== */
.order-expired {
    max-width: 980px;
    margin: 18px auto 28px;
    padding: 0 12px;
    color: #111827;
    font-size: 13.5px;
}

.order-expired .title {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.02em;
    text-align: center;
    margin: 6px 0 10px;
}

.order-expired .subtitle {
    text-align: center;
    color: #6b7280;
    margin-bottom: 12px;
}

.order-expired .bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.order-expired .chip {
    background: var(--dark);
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    gap: 6px;
    align-items: center;
}

.order-expired .badge-expired {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.order-expired .copy-btn {
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

.order-expired .copy-btn:hover {
    background: #f9fafb;
}

.order-expired .card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
}

.order-expired .pad {
    padding: 14px;
}

.order-expired .hr {
    border: 0;
    border-top: 1px dashed #e5e7eb;
    margin: 12px 0;
}

.order-expired .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 992px) {
    .order-expired .grid {
        grid-template-columns: 1fr;
    }
}

.order-expired .label {
    font-size: 11.5px;
    letter-spacing: .06em;
    color: #6b7280;
    text-transform: uppercase;
}

.order-expired .num {
    font-size: 18px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.02em;
}

.order-expired .text {
    font-size: 14px;
    margin: 0;
}

.order-expired .muted {
    color: #6b7280;
}

.order-expired .method-logo {
    width: 90px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 6px auto 10px;
}

.order-expired .kvs {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-items: center;
    padding: 10px 12px;
    border: 1px dashed #e5e7eb;
    border-radius: 10px;
    background: #fafafa;
}

.order-expired .kvs .k {
    font-size: 12px;
    color: #6b7280;
}

.order-expired .kvs .v {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: .2px;
}

.order-expired .dim {
    opacity: .65;
}

.order-expired .note {
    background: #fff7ed;
    border: 1px solid #ffedd5;
    color: #9a3412;
    padding: 10px 12px;
    border-radius: 10px;
    font-size: 13px;
}

.order-expired .accordion-button {
    font-size: 13px;
}

.order-expired .actions {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

.order-expired .btn-order {
    text-decoration: none;
    padding: 10px 18px;
    background: #ff4747;
    color: #fff;
    border-radius: 8px;
    text-align: center;
    font-size: 14px;
    display: inline-block;
}

.order-expired .btn-order:hover {
    background: #e04040;
}

.order-expired .btn-ghost {
    text-decoration: none;
    padding: 10px 18px;
    background: #fff;
    color: #111827;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

/* Toast copy */
.order-expired .toast {
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

.order-expired .toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-4px);
}
</style>

<div class="order-expired">
    <h1 class="title">Pesanan Kedaluwarsa</h1>
    <p class="subtitle">Waktu pembayaran untuk pesanan ini sudah berakhir.</p>

    <!-- Bar: ID + status + copy -->
    <div class="bar">
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span class="chip">
                <i class="material-icons" style="font-size:16px;">receipt_long</i>
                ID: <b><?= $pemesananSelected['id_midtrans']; ?></b>
            </span>
            <span class="badge-expired">Kedaluwarsa</span>
        </div>
        <button class="copy-btn" onclick="copytext('<?= $pemesananSelected['id_midtrans']; ?>')"
            title="Salin ID Pesanan">
            <i class="material-icons" style="font-size:16px;">content_copy</i> Salin
        </button>
    </div>

    <div class="card pad">
        <!-- Ringkasan 2 kolom -->
        <div class="grid">
            <div>
                <p class="label m-0">Jumlah Tagihan</p>
                <p class="num m-0">Rp <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></p>
            </div>
            <div>
                <p class="label m-0">Waktu Kedaluwarsa</p>
                <p class="text m-0"><b><?= $waktuExpireFix; ?> WIB</b></p>
            </div>
        </div>

        <hr class="hr">

        <!-- Metode & VA (dinonaktifkan secara visual) -->
        <div class="grid">
            <div class="card pad">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                    <div>
                        <p class="text m-0"><b>Metode Pembayaran</b></p>
                        <p class="muted m-0">Nomor/QR di bawah sudah tidak aktif.</p>
                    </div>
                    <span class="badge-expired">Tidak Aktif</span>
                </div>
                <img class="method-logo dim" src="/img/pembayaran/<?= $bank; ?>.webp" alt="<?= $bank; ?>">
                <?php if ($bank === 'mandiri' && !empty($biller_code)): ?>
                <div class="kvs dim" style="margin-top:6px;">
                    <div>
                        <div class="k">Biller Code</div>
                        <div class="v"><?= $biller_code; ?></div>
                    </div>
                    <button class="copy-btn" onclick="copytext('<?= $biller_code; ?>')" title="Salin Biller Code">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                </div>
                <?php endif; ?>
                <?php if ($va_number !== '' && $bank !== 'card'): ?>
                <div class="kvs dim" style="margin-top:6px;">
                    <div>
                        <div class="k">
                            <?= ($bank === 'toko' || $bank === 'market') ? 'Keterangan' : 'Virtual Account'; ?></div>
                        <div class="v"><?= $va_number; ?></div>
                    </div>
                    <?php if($bank !== 'toko' && $bank !== 'market'): ?>
                    <button class="copy-btn" onclick="copytext('<?= $va_number; ?>')" title="Salin">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p class="muted m-0 dim" style="margin-top:6px;">Tidak ada nomor yang ditampilkan untuk metode ini.</p>
                <?php endif; ?>
            </div>

            <!-- Petunjuk (tetap ditampilkan jika ingin melakukan pembayaran baru) -->
            <div class="card pad">
                <p class="text m-0"><b>Petunjuk Pembayaran</b></p>
                <p class="muted m-0">Gunakan panduan berikut bila membuat pembayaran baru.</p>
                <div class="accordion accordion-flush mt-2" id="accordionFlushExample">
                    <?php foreach ($carapembayaranSelected as $ind_c => $c) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $ind_c ?>"
                                aria-expanded="false" aria-controls="flush-collapse<?= $ind_c ?>">
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
        </div>

        <hr class="hr">

        <!-- Catatan & Aksi -->
        <div class="note">
            Nomor pembayaran pada pesanan ini sudah kedaluwarsa. Jika kamu ingin melanjutkan, silakan buat pesanan baru.
            Jika kamu sudah membayar sebelumnya, status akan diperbarui otomatis setelah diverifikasi.
        </div>

        <hr class="hr">

        <div class="actions">
            <a href="/order" class="btn-order">Kembali ke Pesanan Saya</a>
            <a href="/" class="btn-ghost">Belanja Lagi</a>
        </div>
    </div>
</div>

<!-- Toast copy -->
<div id="toastCopy" class="order-expired toast">Disalin</div>

<?= $this->endSection(); ?>

<script>
// Helper copy + toast (UI saja, tidak mengubah flow)
function copytext(text) {
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(String(text));
    } else {
        const el = document.createElement('textarea');
        el.value = String(text);
        el.style.position = 'fixed';
        el.style.top = '-1000px';
        document.body.appendChild(el);
        el.select();
        try {
            document.execCommand('copy');
        } catch (e) {}
        document.body.removeChild(el);
    }
    const toast = document.getElementById('toastCopy');
    if (!toast) return;
    toast.textContent = 'Disalin';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 1200);
}
</script>