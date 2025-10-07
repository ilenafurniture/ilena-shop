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

// Jumlah tagihan
$jumlahTagihan = number_format($dataMid['gross_amount'], 0, ',', '.');

// Alasan pembatalan
$alasanBatal = isset($pemesananSelected['alasan_batal']) ? $pemesananSelected['alasan_batal'] : 'Tidak Diketahui';

// (Opsional) waktu — tetap dihitung agar konsisten, meskipun tidak ditampilkan
$bulan = $bulan ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$waktuExpire = strtotime($dataMid['expiry_time'] ?? 'now');
$waktuCurr   = strtotime("+7 Hours");
$waktuSelisih = $waktuExpire - $waktuCurr;
$waktu = date("H:i:s", $waktuSelisih);
$waktuExpireFix = date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire);

// Items tetap tersedia bila nanti mau ditampilkan/diolah
$items = $pemesananSelected['items'];
?>
<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* ===== Scoped: order-cancel (tidak ganggu halaman lain) ===== */
.order-cancel {
    max-width: 980px;
    margin: 18px auto 28px;
    padding: 0 12px;
    color: #111827;
    font-size: 13.5px;
}

.order-cancel .title {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.02em;
    text-align: center;
    margin: 6px 0 10px;
}

.order-cancel .subtitle {
    text-align: center;
    color: #6b7280;
    margin-bottom: 12px;
}

.order-cancel .bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.order-cancel .chip {
    background: var(--dark);
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    gap: 6px;
    align-items: center;
}

.order-cancel .badge-cancel {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.order-cancel .copy-btn {
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

.order-cancel .copy-btn:hover {
    background: #f9fafb;
}

.order-cancel .card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
}

.order-cancel .pad {
    padding: 14px;
}

.order-cancel .hr {
    border: 0;
    border-top: 1px dashed #e5e7eb;
    margin: 12px 0;
}

.order-cancel .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 992px) {
    .order-cancel .grid {
        grid-template-columns: 1fr;
    }
}

.order-cancel .label {
    font-size: 11.5px;
    letter-spacing: .06em;
    color: #6b7280;
    text-transform: uppercase;
}

.order-cancel .num {
    font-size: 18px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.02em;
}

.order-cancel .text {
    font-size: 14px;
    margin: 0;
}

.order-cancel .muted {
    color: #6b7280;
}

.order-cancel .note {
    background: #fff7ed;
    border: 1px solid #ffedd5;
    color: #9a3412;
    padding: 10px 12px;
    border-radius: 10px;
    font-size: 13px;
}

.order-cancel .actions {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

.order-cancel .btn-order {
    text-decoration: none;
    padding: 10px 18px;
    background: #ff4747;
    color: #fff;
    border-radius: 8px;
    text-align: center;
    font-size: 14px;
    display: inline-block;
}

.order-cancel .btn-order:hover {
    background: #e04040;
}

.order-cancel .btn-ghost {
    text-decoration: none;
    padding: 10px 18px;
    background: #fff;
    color: #111827;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

.order-cancel .toast {
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

.order-cancel .toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-4px);
}
</style>

<div class="order-cancel">
    <h1 class="title">Pesanan Dibatalkan</h1>
    <p class="subtitle">Pesanan kamu tidak diproses lebih lanjut. Detail ringkasan dibawah ini.</p>

    <!-- Bar: ID + status + copy -->
    <div class="bar">
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span class="chip">
                <i class="material-icons" style="font-size:16px;">receipt_long</i>
                ID: <b><?= $pemesananSelected['id_midtrans']; ?></b>
            </span>
            <span class="badge-cancel">Dibatalkan</span>
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
                <p class="num m-0">Rp <?= $jumlahTagihan; ?></p>
            </div>
            <div>
                <p class="label m-0">Alasan Pembatalan</p>
                <p class="text m-0"><b><?= $alasanBatal; ?></b></p>
            </div>
        </div>

        <hr class="hr">

        <!-- Catatan -->
        <div class="note">
            Jika kamu merasa pembatalan ini tidak sesuai, silakan hubungi CS kami dengan menyertakan
            <b>ID pesanan</b> di atas. Kamu juga bisa membuat pesanan baru bila masih ingin melanjutkan pembelian.
        </div>

        <hr class="hr">

        <!-- Aksi -->
        <div class="actions">
            <a href="/order" class="btn-order">Kembali ke Pesanan Saya</a>
            <a href="/" class="btn-ghost">Belanja Lagi</a>
        </div>
    </div>
</div>

<!-- Toast copy -->
<div id="toastCopy" class="order-cancel toast">Disalin</div>

<?= $this->endSection(); ?>

<script>
// Helper copy + toast (UI saja, tidak mengubah sistem)
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
    toast.textContent = 'ID Pesanan disalin';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 1200);
}
</script>