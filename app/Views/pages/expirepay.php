<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// === helper kecil (tidak mengubah flow sistem) ===
$waktuExpireEpoch = strtotime($dataMid['expiry_time']);
$bulan = $bulan ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$waktuExpireFix = date("d", $waktuExpireEpoch) . " " . $bulan[(int)date("m", $waktuExpireEpoch) - 1] . " " . date("Y H:i:s", $waktuExpireEpoch);
?>

<style>
/* ===== Scoped: order-expired (aman tidak ganggu halaman lain) ===== */
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
    margin: 6px 0 12px;
    text-align: center;
}

.order-expired .bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
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
    grid-template-columns: 1.05fr .95fr;
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

.order-expired .num-lg {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
    margin: 0;
}

.order-expired .muted {
    color: #6b7280;
}

.order-expired .center {
    text-align: center;
}

.order-expired .method-logo {
    width: 90px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 6px auto 8px;
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
    font-size: 16px;
    font-weight: 700;
    letter-spacing: .2px;
}

/* tabel item */
.order-expired .mini .head,
.order-expired .mini .row {
    display: grid;
    grid-template-columns: 1.3fr .5fr .7fr;
    gap: 6px;
    align-items: center;
}

.order-expired .mini .head {
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11.5px;
    text-transform: uppercase;
    color: #6b7280;
}

.order-expired .mini .row {
    padding: 8px 0;
    border-bottom: 1px solid #fafafa;
}

.order-expired .mini .row:last-child {
    border-bottom: 0;
}

.order-expired .mini .scroll {
    max-height: 260px;
    overflow: auto;
}

/* toast */
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
    <h1 class="title">Pembayaran Kedaluwarsa</h1>

    <!-- Bar ID Pesanan -->
    <div class="bar">
        <span class="chip">
            <i class="material-icons" style="font-size:16px;">receipt_long</i>
            ID: <b><?= $pemesanan['id_midtrans']; ?></b>
        </span>
        <button class="copy-btn" onclick="copytext('<?= $pemesanan['id_midtrans']; ?>')" title="Salin ID Pesanan">
            <i class="material-icons" style="font-size:16px;">content_copy</i> Salin
        </button>
    </div>

    <div class="card pad">
        <!-- Ringkasan singkat -->
        <div class="grid pad" style="padding-top:0;">
            <div class="card pad">
                <div class="center">
                    <p class="label m-0">Jumlah Tagihan</p>
                    <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-top:4px;">
                        <p class="num-lg m-0">Rp <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></p>
                        <button class="copy-btn" onclick="copytext('<?= (int)$dataMid['gross_amount']; ?>')"
                            title="Salin nominal">
                            <i class="material-icons" style="font-size:16px;">content_copy</i>
                        </button>
                    </div>
                </div>

                <div class="hr"></div>

                <div class="center">
                    <p class="label m-0">Pembayaran berakhir pada</p>
                    <p class="num-lg m-0" style="margin-top:4px;"><?= $waktuExpireFix; ?> WIB</p>
                    <p class="muted m-0" style="margin-top:2px;">Silakan lakukan pemesanan ulang jika masih ingin
                        melanjutkan.</p>
                </div>
            </div>

            <div class="card pad">
                <p class="label m-0 center">Nomor Virtual Account</p>
                <img class="method-logo" src="/img/pembayaran/<?= $bank; ?>.webp" alt="<?= $bank; ?>">
                <div class="kvs" style="margin-top:6px;">
                    <div>
                        <div class="k">Virtual Account</div>
                        <div class="v"><?= $va_number; ?></div>
                    </div>
                    <button class="copy-btn" onclick="copytext('<?= $va_number; ?>')" title="Salin VA">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                </div>
                <p class="muted m-0 center" style="margin-top:6px;">Nomor VA tidak lagi aktif karena melewati batas
                    waktu.</p>
            </div>
        </div>

        <div class="hr"></div>

        <!-- Produk yang dibeli (hanya item barang, sembunyikan voucher/ongkir/admin) -->
        <div class="card pad">
            <p class="m-0" style="font-weight:700;">Produk yang dibeli</p>
            <div class="mini mt-1">
                <div class="head">
                    <div class="center">Nama</div>
                    <div class="center">Jumlah</div>
                    <div class="center">Harga</div>
                </div>
                <div class="scroll">
                    <?php foreach ($items as $i):
              $nm = strtolower(trim($i['name']));
              if (in_array($nm, ['voucher','flash sale','biaya ongkir','biaya admin'])) continue;
          ?>
                    <div class="row">
                        <p class="m-0 center fw-bold"><?= $i['name']; ?></p>
                        <p class="m-0 center fw-bold"><?= (int)$i['quantity']; ?></p>
                        <p class="m-0 center fw-bold">Rp <?= number_format((int)$i['price'], 0, ',', '.'); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="hr"></div>

        <!-- Petunjuk Pembayaran -->
        <p class="m-0 center" style="font-weight:700;">Petunjuk Pembayaran</p>
        <div class="accordion accordion-flush mt-1" id="accordionFlushExample">
            <?php foreach ($caraPembayaran as $ind_c => $c) { ?>
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
                    <div class="accordion-body" style="font-size:13.5px; line-height:1.55;">
                        <p class="mb-0"><?= $c['isi']; ?></p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="hr"></div>

        <!-- CTA -->
        <div style="display:flex; justify-content:center;">
            <a href="/order" class="btn-default-merah">Kembali ke Pesanan Saya</a>
        </div>
    </div>
</div>

<!-- Toast copy -->
<div id="toastCopy" class="order-expired toast">Disalin</div>

<script>
// Guard: jika halaman ini dipakai terpisah & copytext belum ada, definisikan ringan
if (typeof copytext !== 'function') {
    window.copytext = function(text) {
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
}
</script>

<?= $this->endSection(); ?>