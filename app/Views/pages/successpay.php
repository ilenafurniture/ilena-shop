<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// ---- Optional: breakdown ringan untuk tampilan (tidak mengubah sistem) ----
$subtotal = 0;
$diskonVoucher = 0;
$diskonFlash = 0;
$biayaAdmin = 0;

foreach ($items as $it) {
    $name = strtolower(trim($it['name']));
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
$totalBayar = (int)$dataMid['gross_amount'];
?>

<style>
/* ===== Scoped: order-success (tidak ganggu halaman lain) ===== */
.order-success {
    max-width: 980px;
    margin: 18px auto 28px;
    padding: 0 12px;
    color: #111827;
    font-size: 13.5px;
}

.order-success .hero {
    display: grid;
    place-items: center;
    text-align: center;
    gap: 8px;
    margin-bottom: 14px;
}

.order-success .hero .title {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.02em;
    margin: 0;
}

.order-success .hero .desc {
    color: #6b7280;
    margin: 0;
    max-width: 820px;
}

.order-success .bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.order-success .chip {
    background: var(--dark);
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    gap: 6px;
    align-items: center;
}

.order-success .badge-ok {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
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
    color: #374151;
}

.order-success .copy-btn:hover {
    background: #f9fafb;
}

.order-success .card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
}

.order-success .pad {
    padding: 14px;
}

.order-success .hr {
    border: 0;
    border-top: 1px dashed #e5e7eb;
    margin: 12px 0;
}

.order-success .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 992px) {
    .order-success .grid {
        grid-template-columns: 1fr;
    }
}

.order-success .label {
    font-size: 11.5px;
    letter-spacing: .06em;
    color: #6b7280;
    text-transform: uppercase;
}

.order-success .num-lg {
    font-size: 18px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.02em;
}

.order-success .num-md {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    letter-spacing: -.02em;
}

.order-success .muted {
    color: #6b7280;
}

.order-success .center {
    text-align: center;
}

.order-success .method-logo {
    width: 90px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 6px auto 10px;
}

.order-success .kv {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 8px;
    padding: 5px 0;
}

@media (max-width:520px) {
    .order-success .kv {
        grid-template-columns: 1fr;
    }
}

.order-success .mini .head,
.order-success .mini .row {
    display: grid;
    grid-template-columns: 1.2fr .5fr .7fr;
    gap: 6px;
    align-items: center;
}

.order-success .mini .head {
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11.5px;
    text-transform: uppercase;
    color: #6b7280;
}

.order-success .mini .row {
    padding: 8px 0;
    border-bottom: 1px solid #fafafa;
}

.order-success .mini .row:last-child {
    border-bottom: 0;
}

.order-success .mini .scroll {
    max-height: 260px;
    overflow: auto;
}

.order-success .list-line {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    padding: 6px 0;
}

.order-success .list-line.total {
    border-top: 1px dashed #e5e7eb;
    margin-top: 6px;
    padding-top: 10px;
    font-weight: 800;
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
    z-index: 9999;
}

.order-success .toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(-4px);
}
</style>

<div class="order-success">
    <!-- Hero -->
    <div class="hero">
        <i class="material-icons" style="color:#10b981;font-size:44px;">check_circle</i>
        <h1 class="title">Pembayaran Berhasil</h1>
        <p class="desc">Pesanan akan segera kami proses. Simpan URL halaman ini untuk memantau status pesanan. Atau
            login sebagai member untuk melihat seluruh riwayat pesanan Anda.</p>
    </div>

    <!-- Bar: ID + status + copy -->
    <div class="bar">
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span class="chip">
                <i class="material-icons" style="font-size:16px;">receipt_long</i>
                ID: <b><?= $pemesanan['id_midtrans']; ?></b>
            </span>
            <span class="badge-ok">Berhasil</span>
        </div>
        <button class="copy-btn" onclick="copytext('<?= $pemesanan['id_midtrans']; ?>')" title="Salin ID Pesanan">
            <i class="material-icons" style="font-size:16px;">content_copy</i> Salin
        </button>
    </div>

    <div class="card pad">
        <!-- Ringkasan atas -->
        <div class="grid pad" style="padding-top:0;">
            <div class="center">
                <p class="label m-0">Jumlah Tagihan</p>
                <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:4px;">
                    <p class="num-lg m-0">Rp <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></p>
                    <button class="copy-btn" onclick="copytext('<?= (int)$dataMid['gross_amount']; ?>')"
                        title="Salin nominal">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                </div>
            </div>
            <div class="center">
                <p class="label m-0">Metode Pembayaran</p>
                <?php if ($dataMid['payment_type'] == 'credit_card') { ?>
                <p class="num-md m-0" style="margin-top:4px;">Kartu Kredit</p>
                <?php } else { ?>
                <img class="method-logo" src="/img/pembayaran/<?= $bank; ?>.webp" alt="<?= $bank; ?>">
                <?php } ?>
            </div>
        </div>

        <hr class="hr">

        <!-- Dua kolom: kiri pengiriman/resi/status, kanan items + rincian -->
        <div class="grid pad" style="padding-top:0;">
            <!-- Kiri: Ekspedisi / Resi / Status -->
            <div class="card pad">
                <p class="num-md m-0" style="margin-bottom:6px;">Ekspedisi</p>

                <?php if ($pemesanan['resi'] != 'Menunggu pengiriman') { ?>
                <div style="display:flex;gap:10px;align-items:center;">
                    <img src="/img/kurir/<?= strtolower(explode(" ", $kurir['nama'])[0]); ?>.png" alt=""
                        style="width: 90px; object-fit:contain">
                    <div style="flex:1;">
                        <p class="m-0" style="font-weight:700;letter-spacing:-.02em;"><?= strtoupper($kurir['nama']) ?>
                            <?= $kurir['deskripsi'] ?></p>
                        <?php if (isset($kurir['estimasi'])) { ?>
                        <p class="m-0 muted">Estimasi <?= $kurir['estimasi'] ?> hari</p>
                        <?php } ?>
                    </div>
                </div>
                <?php } else { ?>
                <div>
                    <p class="m-0">Barang sedang diproses untuk pengiriman.</p>
                    <p class="m-0 muted">*Pesanan di atas pukul 12:00 akan diproses esok hari.</p>
                </div>
                <?php } ?>

                <div class="hr"></div>

                <p class="m-0">Resi</p>
                <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
                    <p class="m-0" style="font-weight:700;"><?= $pemesanan['resi']; ?></p>
                    <?php if ($pemesanan['resi'] != 'Menunggu pengiriman') { ?>
                    <button class="copy-btn" onclick="copytext('<?= $pemesanan['resi']; ?>')" title="Salin Resi">
                        <i class="material-icons" style="font-size:16px;">content_copy</i>
                    </button>
                    <?php } ?>
                </div>

                <div class="hr"></div>

                <p class="m-0">Status Pesanan</p>
                <span class="badge rounded-pill <?php
          switch ($pemesanan['status']) {
            case 'Menunggu Pembayaran': echo 'text-bg-primary'; break;
            case 'Proses': echo 'text-bg-warning'; break;
            case 'Dikirim': echo 'text-bg-info'; break;
            case 'Selesai': echo 'text-bg-success'; break;
            case 'Dibatalkan': echo 'text-bg-danger'; break;
            case 'Gagal': echo 'text-bg-danger'; break;
            default: echo 'text-bg-dark'; break;
          } ?>">
                    <?= ucfirst($pemesanan['status']); ?>
                </span>
            </div>

            <!-- Kanan: Items & Rincian Pembayaran -->
            <div class="card pad">
                <p class="num-md m-0">Ringkasan Pesanan</p>

                <!-- Tabel items (hanya produk, sembunyikan voucher/ongkir/admin di sini) -->
                <div class="mini mt-1">
                    <div class="head">
                        <div class="center">Nama</div>
                        <div class="center">Jumlah</div>
                        <div class="center">Harga</div>
                    </div>
                    <div class="scroll">
                        <?php foreach ($items as $i) {
                $nm = strtolower(trim($i['name']));
                if (in_array($nm, ['voucher', 'flash sale', 'biaya ongkir', 'biaya admin'])) continue;
            ?>
                        <div class="row">
                            <p class="m-0 center fw-bold"><?= $i['name']; ?></p>
                            <p class="m-0 center fw-bold"><?= (int)$i['quantity']; ?></p>
                            <p class="m-0 center fw-bold">Rp <?= number_format((int)$i['price'], 0, ',', '.'); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="hr"></div>

                <!-- Rincian Pembayaran -->
                <p class="num-md m-0">Rincian Pembayaran</p>
                <div class="mt-1">
                    <div class="list-line"><span class="muted">Subtotal</span><span><b>Rp
                                <?= number_format($subtotal, 0, ',', '.'); ?></b></span></div>
                    <?php if ($diskonVoucher > 0): ?>
                    <div class="list-line"><span class="muted">Diskon Voucher</span><span>- Rp
                            <?= number_format($diskonVoucher, 0, ',', '.'); ?></span></div>
                    <?php endif; ?>
                    <?php if ($diskonFlash > 0): ?>
                    <div class="list-line"><span class="muted">Flash Sale</span><span>- Rp
                            <?= number_format($diskonFlash, 0, ',', '.'); ?></span></div>
                    <?php endif; ?>
                    <?php if ($biayaAdmin > 0): ?>
                    <div class="list-line"><span class="muted">Biaya Admin</span><span>Rp
                            <?= number_format($biayaAdmin, 0, ',', '.'); ?></span></div>
                    <?php endif; ?>
                    <div class="list-line total"><span>Total Bayar</span><span>Rp
                            <?= number_format($totalBayar, 0, ',', '.'); ?></span></div>
                </div>
            </div>
        </div>

        <?php if (session()->get('isLogin')) { ?>
        <div class="hr"></div>
        <div style="display:flex;justify-content:center;">
            <a href="/order" class="btn-default-merah">Pesanan Saya</a>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Toast copy -->
<div id="toastCopy" class="order-success toast">Disalin</div>

<?= $this->endSection(); ?>

<script>
// Helper copy + toast (tidak mengubah sistem/flow)
(function() {
    if (typeof window.copytext !== 'function') {
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
})();
</script>