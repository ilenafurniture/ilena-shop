<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// ====== helper kecil (tidak mengubah sistem) ======
$waktuExpireEpoch = strtotime($dataMid['expiry_time']);
$bulan = $bulan ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$waktuExpireFix = date("d", $waktuExpireEpoch) . " " . $bulan[(int)date("m", $waktuExpireEpoch) - 1] . " " . date("Y H:i:s", $waktuExpireEpoch);
$selisihDetik = max(0, $waktuExpireEpoch - time());
$waktuStart = sprintf('%02d:%02d:%02d', floor($selisihDetik/3600), floor(($selisihDetik%3600)/60), $selisihDetik%60);
?>

<style>
/* ===== Scoped: order-wait (aman tidak ganggu halaman lain) ===== */
.order-wait {
    max-width: 980px;
    margin: 18px auto 28px;
    padding: 0 12px;
    color: #111827;
    font-size: 13.5px;
}

.order-wait .title {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.02em;
    margin: 6px 0 12px;
    text-align: center;
}

.order-wait .bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.order-wait .chip {
    background: var(--dark);
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    gap: 6px;
    align-items: center;
}

.order-wait .copy-btn {
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

.order-wait .copy-btn:hover {
    background: #f9fafb;
}

.order-wait .card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
}

.order-wait .pad {
    padding: 14px;
}

.order-wait .hr {
    border: 0;
    border-top: 1px dashed #e5e7eb;
    margin: 12px 0;
}

.order-wait .grid {
    display: grid;
    grid-template-columns: 1.05fr .95fr;
    gap: 14px;
}

@media (max-width: 992px) {
    .order-wait .grid {
        grid-template-columns: 1fr;
    }
}

.order-wait .label {
    font-size: 11.5px;
    letter-spacing: .06em;
    color: #6b7280;
    text-transform: uppercase;
}

.order-wait .num-lg {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
    margin: 0;
}

.order-wait .count {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.03em;
}

.order-wait .muted {
    color: #6b7280;
}

.order-wait .center {
    text-align: center;
}

.order-wait .method-logo {
    width: 90px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 6px auto 8px;
}

.order-wait .kvs {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-items: center;
    padding: 10px 12px;
    border: 1px dashed #e5e7eb;
    border-radius: 10px;
    background: #fafafa;
}

.order-wait .kvs .k {
    font-size: 12px;
    color: #6b7280;
}

.order-wait .kvs .v {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: .2px;
}

/* tabel item */
.order-wait .mini .head,
.order-wait .mini .row {
    display: grid;
    grid-template-columns: 1.3fr .5fr .7fr;
    gap: 6px;
    align-items: center;
}

.order-wait .mini .head {
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11.5px;
    text-transform: uppercase;
    color: #6b7280;
}

.order-wait .mini .row {
    padding: 8px 0;
    border-bottom: 1px solid #fafafa;
}

.order-wait .mini .row:last-child {
    border-bottom: 0;
}

.order-wait .mini .scroll {
    max-height: 260px;
    overflow: auto;
}

/* toast */
.order-wait .toast {
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

.order-wait .toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-4px);
}
</style>

<div class="order-wait">
    <h1 class="title">Menunggu Pembayaran</h1>

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
        <!-- Grid: Ringkasan & VA -->
        <div class="grid pad" style="padding-top:0;">
            <!-- Kolom kiri: jumlah & timer -->
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
                    <p class="label m-0">Waktu Pembayaran</p>
                    <p class="count waktu m-0" style="margin-top:4px;"><?= $waktuStart; ?></p>
                    <p class="muted m-0" style="margin-top:2px;">Bayar sebelum: <?= $waktuExpireFix; ?> WIB</p>
                </div>
            </div>

            <!-- Kolom kanan: VA -->
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
                <p class="muted m-0 center" style="margin-top:6px;">Gunakan nomor di atas sesuai bank yang dipilih.</p>
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
            <a href="" class="btn-default-merah">Saya telah membayar</a>
        </div>
    </div>
</div>

<!-- Toast copy -->
<div id="toastCopy" class="order-wait toast">Disalin</div>

<script>
// Countdown stabil (pakai epoch dari server, tanpa reload loop)
(function() {
    const expiryTimeElm = document.querySelectorAll(".waktu");
    const expireTime = <?= (int)$waktuExpireEpoch ?> * 1000; // ms

    function tick() {
        const now = Date.now();
        let diff = expireTime - now;

        if (diff <= 0) {
            expiryTimeElm.forEach(elm => elm.textContent = "00:00:00");
            return; // tidak reload
        }
        const hh = String(Math.floor(diff / 3600000)).padStart(2, '0');
        diff %= 3600000;
        const mm = String(Math.floor(diff / 60000)).padStart(2, '0');
        diff %= 60000;
        const ss = String(Math.floor(diff / 1000)).padStart(2, '0');
        expiryTimeElm.forEach(elm => elm.textContent = `${hh}:${mm}:${ss}`);
    }
    tick();
    setInterval(tick, 1000);
})();

// Helper copy + toast (tidak ubah sistem)
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