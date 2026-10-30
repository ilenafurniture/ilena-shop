<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// ====== Guard & fallback supaya view aman ======
$voucher           = isset($voucher) && is_array($voucher) ? $voucher : [];
$selected          = $voucher['selected'] ?? null;
$list              = isset($voucher['list']) && is_array($voucher['list']) ? $voucher['list'] : [];
$user              = isset($user) && is_array($user) ? $user : ['nama'=>'', 'no_hp'=>'', 'email'=>'', 'alamat'=>''];
$keranjang         = isset($keranjang) && is_array($keranjang) ? $keranjang : [];
$listPaymentMethod = isset($listPaymentMethod) && is_array($listPaymentMethod) ? $listPaymentMethod : [];
$paymentMethod     = $paymentMethod ?? '';
$indexAddress      = (int)($indexAddress ?? 0);
$hargaTotal        = (float)($hargaTotal ?? 0);
$hargaOngkir       = (float)($hargaOngkir ?? 0);
$flashSale         = (float)($flashSale ?? 0);
$biayaAdmin        = (int)($biayaAdmin ?? 0);
$grossAmount       = (int)($grossAmount ?? 0);
$msg               = $msg ?? '';

// badge “member baru” hanya untuk informasi (tidak auto-apply)
$isMemberBaruSelected = false;
if (is_array($selected)) {
    $selTarget = strtolower($selected['target'] ?? '');
    $selNama   = strtolower($selected['nama'] ?? '');
    $isMemberBaruSelected = ($selTarget === 'baru') || str_contains($selNama, 'member baru');
}
?>

<style>
/* ================= THEME-COMPAT (ikut gaya kamu) ================= */
:root {
    --ink: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --card: #fff;
    --card-weak: #fafafa;
    --brand: var(--merah);
}

.payment-shell {
    font-size: 14px;
    color: var(--ink);
}

.payment-bc {
    font-size: 12px;
    color: var(--muted);
    margin: 8px 0 14px;
}

.payment-bc a {
    color: var(--muted);
    text-decoration: none;
}

.payment-bc .active {
    color: var(--ink);
    font-weight: 600;
}

.grid-two {
    display: grid;
    gap: 16px;
    grid-template-columns: 1.15fr .85fr;
}

@media (max-width:992px) {
    .grid-two {
        grid-template-columns: 1fr;
    }
}

/* Card */
.card-soft {
    border: 1px solid var(--line);
    border-radius: 12px;
    background: var(--card);
    box-shadow: 0 1px 10px rgba(0, 0, 0, .03);
}

.card-soft .hd {
    padding: 10px 14px;
    background: linear-gradient(180deg, var(--card) 0%, var(--card-weak) 100%);
    border-bottom: 1px solid #eef0f3;
    border-radius: 12px 12px 0 0;
    font-weight: 700;
    letter-spacing: -.02em;
    color: var(--ink);
    font-size: 14px;
}

.card-soft .bd {
    padding: 12px 14px;
}

/* Key-value */
.kv {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 8px 14px;
}

.kv .k {
    color: var(--muted);
    font-size: 12px;
}

.kv .v {
    color: var(--ink);
    font-weight: 600;
}

@media (max-width:576px) {
    .kv {
        grid-template-columns: 1fr;
    }
}

/* Item keranjang */
.item-row {
    display: flex;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px dashed var(--line);
}

.item-row:last-child {
    border-bottom: 0;
}

.item-row img {
    width: 74px;
    height: 74px;
    border-radius: 8px;
    object-fit: cover;
    flex: 0 0 74px;
}

.item-info {
    display: flex;
    gap: 12px;
    font-size: 13px;
}

.item-info .k {
    color: var(--muted);
    min-width: 100px;
}

.item-info .v {
    color: var(--ink);
    font-weight: 600;
}

/* ================== METODE PEMBAYARAN (stabil & responsif) ================== */
.container-pembayaran {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(98px, 1fr));
    gap: 10px;
}

.item-logo-pembayaran {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 54px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: #fff;
    transition: box-shadow .15s, border-color .15s, transform .08s;
    padding: 8px 10px;
    text-decoration: none;
}

.item-logo-pembayaran:hover {
    transform: translateY(-1px);
}

.item-logo-pembayaran.active {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px color-mix(in oklab, var(--brand) 16%, transparent);
}

.item-logo-pembayaran img {
    width: 100%;
    height: 100%;
    max-width: 82px;
    max-height: 28px;
    object-fit: contain;
    image-rendering: -webkit-optimize-contrast;
    filter: saturate(.95) contrast(1.02);
}

@media (max-width:480px) {
    .container-pembayaran {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Toggle section (Redeem & Daftar Voucher) */
.section-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 10px 12px;
    background: #fff;
    cursor: pointer;
}

.section-toggle p {
    margin: 0;
    font-weight: 600;
    color: var(--ink);
}

.section-toggle i {
    color: var(--muted);
    font-size: 20px;
}

/* Redeem box */
.redeem-box {
    display: none;
    padding: 12px;
    border: 1px dashed var(--line);
    border-radius: 10px;
    background: var(--card-weak);
}

.redeem-box .form-control {
    height: 38px;
    font-size: 14px;
    border-radius: 10px;
    border: 1px solid var(--line);
}

/* Daftar Voucher */
.voucher-list {
    display: none;
    margin-top: 10px;
}

.voucher-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border: 1px solid var(--line);
    background: #fff;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 8px;
}

.voucher-item.active {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px color-mix(in oklab, var(--brand) 15%, transparent);
}

.voucher-item .name {
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -.02em;
}

.voucher-item .meta {
    font-size: 12px;
    color: var(--muted);
    margin-top: 2px;
}

.voucher-item .badge {
    background: color-mix(in oklab, var(--brand) 15%, white);
    color: var(--brand);
    border: 1px dashed var(--brand);
    border-radius: 999px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 700;
}

.voucher-item .rec {
    background: #eef6ff;
    color: #1d4ed8;
    border: 1px dashed #93c5fd;
}

/* Ringkasan */
.sum-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 4px 0;
}

.sum-row .k {
    color: var(--ink);
}

.sum-row .v {
    color: var(--ink);
    font-weight: 700;
}

.sum-total {
    font-size: 15px;
}

/* Buttons */
.btn-default-merah {
    background: var(--brand);
    border: 1px solid var(--brand);
    border-radius: 10px;
    padding: 10px 14px;
    color: #fff;
    font-weight: 700;
    font-size: 14px;
}

.btn-default-merah:hover {
    filter: brightness(.96);
    color: #fff;
}

.btn-plain {
    display: inline-block;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 8px 12px;
    font-weight: 600;
    font-size: 13px;
    color: var(--brand);
    text-decoration: none;
}

/* Alerts */
.alert-soft {
    background: color-mix(in oklab, var(--brand) 6%, white);
    border: 1px solid color-mix(in oklab, var(--brand) 18%, white);
    color: var(--ink);
    padding: 8px 10px;
    border-radius: 8px;
    font-size: 13px;
}

/* Utilities */
.small {
    font-size: 12px;
    color: var(--muted);
}

hr {
    border: none;
    border-top: 1px solid var(--line);
    margin: 8px 0;
}

.mt-2 {
    margin-top: 8px;
}

.mb-2 {
    margin-bottom: 8px;
}
</style>

<div class="payment-shell container ilena-payment-page">
    <div class="payment-bc">
        <a href="<?= esc(ilena_store_path('/address'), 'attr'); ?>"><?= lang('Store.address'); ?></a> &nbsp;>&nbsp; <span class="active"><?= lang('Store.addressDetails'); ?></span>
    </div>

    <header class="ilena-payment-heading">
        <div>
            <p class="ilena-payment-heading__eyebrow"><?= lang('Store.step'); ?> 03 <span>/</span> <?= lang('Store.payment'); ?></p>
            <h1><?= lang('Store.reviewOrder'); ?></h1>
            <p class="ilena-payment-heading__description"><?= lang('Store.reviewOrderHint'); ?></p>
        </div>
        <span class="ilena-payment-heading__count"><?= count($keranjang); ?> <?= lang('Store.orderItems'); ?></span>
    </header>

    <?php if (!empty($msg)) : ?>
    <div class="pemberitahuan my-1 w-100" style="width:fit-content" role="alert"><?= esc($msg); ?></div>
    <?php endif; ?>

    <div class="grid-two">
        <!-- =================== LEFT =================== -->
        <div>
            <!-- Informasi Pembeli -->
            <div class="card-soft mb-2">
                <div class="hd"><?= lang('Store.buyerInfo'); ?></div>
                <div class="bd">
                    <div class="kv">
                        <div class="k"><?= lang('Store.fullName'); ?></div>
                        <div class="v"><?= esc($user['nama'] ?? ''); ?></div>
                        <div class="k"><?= lang('Store.phone'); ?></div>
                        <div class="v"><?= esc($user['no_hp'] ?? ''); ?></div>
                        <div class="k">Email</div>
                        <div class="v"><?= esc($user['email'] ?? ''); ?></div>
                        <div class="k"><?= lang('Store.address'); ?></div>
                        <div class="v"><?= esc($user['alamat'] ?? ''); ?></div>
                    </div>
                </div>
            </div>

            <!-- Informasi Barang -->
            <div class="card-soft mb-2">
                <div class="hd"><?= lang('Store.itemInfo'); ?></div>
                <div class="bd">
                    <?php if (count($keranjang) > 0): ?>
                    <?php foreach ($keranjang as $k):
              $nama    = $k['detail']['nama']   ?? '';
              $varian  = $k['varian']            ?? '';
              $jumlah  = (int)($k['jumlah']      ?? 0);
              $harga   = (float)($k['detail']['harga']  ?? 0);
              $diskon  = (float)($k['detail']['diskon'] ?? 0);
              $src     = $k['src_gambar']        ?? '';
              $hargaSatuan = (int)round($harga * (100 - $diskon) / 100);
            ?>
                    <div class="item-row">
                        <img src="<?= esc($src); ?>" alt="produk">
                        <div style="flex:1">
                            <div style="font-weight:700;color:var(--ink);letter-spacing:-.02em;"><?= esc($nama); ?>
                            </div>
                            <div class="item-info mt-1">
                                <div class="k"><?= lang('Store.variantLabel'); ?></div>
                                <div class="v"><?= esc($varian); ?></div>
                            </div>
                            <div class="item-info">
                                <div class="k"><?= lang('Store.quantity'); ?></div>
                                <div class="v"><?= $jumlah; ?> <?= lang('Store.pieces'); ?></div>
                            </div>
                            <div class="item-info">
                                <div class="k"><?= lang('Store.price'); ?></div>
                                <div class="v">Rp <?= number_format($hargaSatuan, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="small"><?= lang('Store.emptyCart'); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="card-soft">
                <div class="hd"><?= lang('Store.paymentMethod'); ?></div>
                <div class="bd">
                    <div class="container-pembayaran">
                        <?php foreach ($listPaymentMethod as $l) : ?>
                        <a href="<?= esc(ilena_store_path('/payment/method/' . rawurlencode($l) . '/' . $indexAddress), 'attr'); ?>"
                            class="item-logo-pembayaran <?= ($l == $paymentMethod) ? 'active' : '' ?>">
                            <img src="/img/pembayaran/<?= esc($l); ?>.webp" alt="<?= esc($l); ?>">
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div id="alert-cc" class="small mt-2 d-none">cek</div>
                </div>
            </div>
        </div>

        <!-- =================== RIGHT =================== -->
        <div>
            <!-- Redeem Voucher -->
            <div class="section-toggle mb-2" onclick="toggleRedeem()">
                <p class="m-0"><?= lang('Store.redeemVoucher'); ?></p>
                <i class="material-icons">expand_more</i>
            </div>
            <div id="redeemBox" class="redeem-box mb-2">
                <form action="<?= esc(ilena_store_path('/redeemvoucher/' . $indexAddress), 'attr'); ?>" method="post" class="d-flex gap-2 flex-wrap"
                    novalidate>
                    <?= function_exists('csrf_field') ? csrf_field() : '' ?>
                    <input type="text" name="code" class="form-control" placeholder="<?= esc(lang('Store.enterVoucher'), 'attr'); ?>" required
                        style="flex:1;min-width:180px;">
                    <button type="submit" class="btn-default-merah"><?= lang('Store.apply'); ?></button>
                </form>
                <div class="small mt-2">
                    <?= lang('Store.voucherHelp'); ?>
                </div>
            </div>


            <!-- Daftar Voucher -->
            <?php if (count($list) > 0) : ?>
            <div class="section-toggle mb-2" onclick="toggleVoucherList()">
                <p class="m-0">
                    <?= is_array($selected) ? ucwords(esc($selected['nama'] ?? lang('Store.chooseVoucher'))) : lang('Store.chooseVoucher'); ?>
                </p>
                <i class="material-icons">arrow_forward_ios</i>
            </div>
            <div id="voucherList" class="voucher-list">
                <?php foreach ($list as $v):
            $vId     = (int)($v['id'] ?? 0);
            $vNama   = (string)($v['nama'] ?? '');
            // fallback nominal/nilai + satuan/tipe
            $vNom    = (string)($v['nominal'] ?? ($v['nilai'] ?? '0'));
            $vSatRaw = (string)($v['satuan'] ?? ($v['tipe'] ?? 'persen'));
            $vSat    = ($vSatRaw === 'persen') ? 'persen' : 'rupiah';
            $vTarget = strtolower($v['target'] ?? '');
            $isMemberBaru = ($vTarget === 'baru') || str_contains(strtolower($vNama), 'member baru');
            $isActive     = (is_array($selected) && ((int)($selected['id'] ?? 0) === $vId));
            $isRec        = !empty($v['recommended']);
            $estCut       = isset($v['estimated_cut']) ? (int)$v['estimated_cut'] : null;
          ?>
                <div class="voucher-item <?= $isActive ? 'active' : ''; ?>">
                    <div>
                        <div class="name">
                            <?= ucwords(esc($vNama)); ?>
                            <?php if ($isMemberBaru): ?><span class="badge"><?= lang('Store.newMember'); ?></span><?php endif; ?>
                            <?php if ($isRec): ?><span class="badge rec"><?= lang('Store.recommended'); ?></span><?php endif; ?>
                        </div>
                        <div class="meta">
                            <?= lang('Store.discount'); ?> <?= esc($vNom).' '.esc($vSat); ?>
                            <?php if ($estCut !== null): ?> · <?= lang('Store.potentialSavings'); ?>: Rp
                            <?= number_format($estCut,0,',','.'); ?><?php endif; ?>
                        </div>
                    </div>
                    <?php if ($isActive): ?>
                    <form action="<?= esc(ilena_store_path('/cancelvoucher/' . $vId . '-' . $indexAddress), 'attr'); ?>" method="post" class="m-0">
                        <?= csrf_field(); ?><button type="submit" class="btn-plain"><?= lang('Store.removeVoucher'); ?></button>
                    </form>
                    <?php else: ?>
                    <form action="<?= esc(ilena_store_path('/usevoucher/' . $vId . '-' . $indexAddress), 'attr'); ?>" method="post" class="m-0">
                        <?= csrf_field(); ?><button type="submit" class="btn-plain"><?= lang('Store.useVoucher'); ?></button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Ringkasan -->
            <div class="card-soft mt-2">
                <div class="hd"><?= lang('Store.orderSummary'); ?></div>
                <div class="bd">
                    <?php if ($isMemberBaruSelected): ?>
                    <div class="alert-soft mb-2"><?= lang('Store.congratulations'); ?></div>
                    <?php endif; ?>

                    <div class="sum-row">
                        <div class="k"><?= lang('Store.price'); ?></div>
                        <div class="v">Rp <?= number_format((int)$hargaTotal, 0, ',', '.'); ?></div>
                    </div>

                    <?php if (is_array($selected)) : ?>
                    <div class="sum-row">
                        <div class="k"><?= lang('Store.voucherDiscount'); ?></div>
                        <div class="v">- Rp <?= number_format((int)($selected['rupiah'] ?? 0), 0, ',', '.'); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($flashSale > 0) : ?>
                    <div class="sum-row">
                        <div class="k"><?= lang('Store.flashSaleDiscount'); ?></div>
                        <div class="v">- Rp <?= number_format((int)$flashSale, 0, ',', '.'); ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="sum-row">
                        <div class="k">
                            <?= lang('Store.shippingCost'); ?>
                            <?php if (!empty($kurir) && is_array($kurir)): ?>
                            <div class="small">
                                <?= strtoupper(esc($kurir['nama'] ?? '')); ?>
                                <?= esc($kurir['deskripsi'] ?? ''); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="v">
                            <?php if (!empty($kurir['gratis_ongkir'])): ?>
                                <span class="text-success fw-bold"><?= lang('Store.free'); ?></span>
                                <div class="small text-secondary text-decoration-line-through">
                                    Rp <?= number_format((int)($kurir['harga_asli'] ?? 0), 0, ',', '.'); ?>
                                </div>
                            <?php else: ?>
                                Rp <?= number_format((int)$hargaOngkir, 0, ',', '.'); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="sum-row">
                        <div class="k">
                            <?= lang('Store.paymentAdminFee'); ?>
                            <div class="small"><?= strtoupper(esc($paymentMethod)); ?></div>
                        </div>
                        <div class="v">Rp <?= number_format((int)$biayaAdmin, 0, ',', '.'); ?></div>
                    </div>
                    <hr>
                    <div class="sum-row sum-total">
                        <div class="k"><?= lang('Store.total'); ?></div>
                        <div class="v">Rp <?= number_format((int)$grossAmount, 0, ',', '.'); ?></div>
                    </div>

                    <?php if ((string)session()->get('role') === '5' && (new \App\Services\AdminRbacService())->hasPermission((string)session()->get('email'), 'orders_online')) : ?>
                    <form method="post" action="/admin/ordertoko/<?= esc($indexAddress, 'url'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="shop_order_token" value="<?= esc($shopOrderToken ?? '', 'attr'); ?>">
                        <button type="submit" class="btn-default-merah w-100 mt-2"><?= lang('Store.placeOrder'); ?></button>
                    </form>
                    <?php else : ?>
                    <button onclick="bayar(event)" class="btn-default-merah w-100 mt-2"><?= lang('Store.pay'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function bayar(e) {
    e.preventDefault();
    const button = e.currentTarget || e.target;
    if (button.dataset.submitting === '1') return;
    button.dataset.submitting = '1';
    button.disabled = true;
    button.innerHTML = <?= json_encode(lang('Store.processing')); ?>;
    const timeStamp = Math.floor(Date.now() / 1000);
    const form = document.getElementById('checkout-submit-form');
    form.action = <?= json_encode(ilena_store_path('/actionpaycore/')); ?> + btoa(timeStamp + ":" + "<?= $indexAddress; ?>");
    form.submit();
}

function toggleRedeem() {
    const box = document.getElementById('redeemBox');
    if (!box) return;
    box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
}

function toggleVoucherList() {
    const list = document.getElementById('voucherList');
    if (!list) return;
    list.style.display = (list.style.display === 'none' || list.style.display === '') ? 'block' : 'none';
}
</script>
<script>
(function() {
    const hasMsg = "<?= $msg ? '1':'0' ?>";
    if (hasMsg === '1') {
        const box = document.getElementById('redeemBox');
        if (box && (box.style.display === '' || box.style.display === 'none')) box.style.display = 'block';
    }
    const hasVoucherList = <?= count($list) > 0 ? 'true' : 'false' ?>;
    if (hasVoucherList) {
        const list = document.getElementById('voucherList');
        const hasSelected = <?= is_array($selected) ? 'true' : 'false' ?>;
        if (list && (hasSelected || hasMsg === '1')) list.style.display = 'block';
    }
})();
</script>


<form id="checkout-submit-form" method="post" hidden><?= function_exists('csrf_field') ? csrf_field() : '' ?><input type="hidden" name="tokencc" id="checkout-tokencc" value=""><input type="hidden" name="checkout_nonce" value="<?= esc($checkoutNonce ?? '', 'attr'); ?>"></form>

<?= $this->endSection(); ?>
