<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom ilena-shipping-page">
    <div style="flex:1;">
        <nav class="path ilena-checkout-steps" aria-label="Tahapan checkout">
            <a href="<?= esc(ilena_store_path('/address'), 'attr'); ?>"><?= lang('Store.address'); ?></a><span aria-hidden="true">/</span>
            <span aria-current="step"><?= lang('Store.shipping'); ?></span><span aria-hidden="true">/</span>
            <span><?= lang('Store.addressDetails'); ?></span>
        </nav>
        <header class="ilena-shipping-heading">
            <div>
                <p class="ilena-shipping-heading__eyebrow"><?= lang('Store.step'); ?> 02 <span>/</span> <?= lang('Store.shipping'); ?></p>
                <h1><?= lang('Store.shippingServices'); ?></h1>
                <p class="ilena-shipping-heading__description"><?= lang('Store.shippingDescription'); ?></p>
            </div>
            <span class="ilena-shipping-heading__count"><?= count($kurir); ?> <?= lang('Store.servicesAvailable'); ?></span>
        </header>
        <div class="container-kurir my-4">
            <?php foreach ($kurir as $index_k => $k) { ?>
            <input type="radio" name="kurir" id="kurir<?= $index_k ?>" value="<?= $k['harga']?>"
                <?= $index_k <= 0 ? 'checked' : '' ?>>
            <label for="kurir<?= $index_k ?>" class="item-kurir">
                <div style="flex: 1;">
                    <p class="mb-1 nama"><?= esc(strtoupper($k['nama'])) ?> <?= esc($k['deskripsi']) ?></p>
                    <?php if($k['estimasi']) { ?>
                    <p class="mb-1"><?= lang('Store.deliveryEstimate'); ?> <?= esc($k['estimasi']) ?> <?= lang('Store.days'); ?></p>
                    <?php } ?>
                    <p class="mb-1" style="font-weight: 600;">
                        <?php if (!empty($k['gratis_ongkir'])): ?>
                            <span class="text-success"><?= lang('Store.free'); ?></span>
                            <span class="text-secondary text-decoration-line-through ms-2">
                                Rp <?= number_format((int)($k['harga_asli'] ?? 0), 0, ',', '.'); ?>
                            </span>
                        <?php else: ?>
                            Rp <?= number_format($k['harga'], 0, ',', '.'); ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($k['gratis_ongkir_label'])): ?>
                        <p class="mb-1 text-success ilena-shipping-benefit">
                            <?= esc($k['gratis_ongkir_label']); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div style="width:fit-content" class="show-block-ke-hide">
                    <img src="/img/kurir/<?= esc($k['nama'], 'attr') ?>.png" alt="" width="76" height="34" loading="lazy" onerror="this.style.display='none'">
                </div>
            </label>
            <?php } ?>
        </div>
    </div>
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h4><?= lang('Store.orderSummary'); ?></h4>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    <?= lang('Store.price'); ?>
                </p>
                <p class="fw-bold m-0">
                    Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    <?= lang('Store.shippingCost'); ?>
                </p>
                <p id="harga-ongkir" class="fw-bold m-0">
                    <?= !empty($kurir[0]['gratis_ongkir']) ? lang('Store.free') : 'Rp ' . number_format($kurir[0]['harga'], 0, ',', '.'); ?>
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    <?= lang('Store.paymentAdminFee'); ?>
                </p>
                <p class="fw-bold m-0 text-secondary">
                    <?= lang('Store.calculatedLater'); ?>
                </p>
            </div>
            <p class="m-0 text-secondary ilena-shipping-fee-note">
                <?= lang('Store.paymentFeeNote'); ?>
            </p>
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    <?= lang('Store.total'); ?>
                </p>
                <p class="fw-bold m-0" id="harga-keseluruhan">
                    Rp <?= number_format($hargaKeseluruhan, 0, ',', '.'); ?>
                </p>
            </div>
            <a id="btn-payment" <?= count($alamat) > 0 ? 'href="' . esc(ilena_store_path('/payment/0'), 'attr') . '"' : '' ?>
                class="btn-default-merah <?= count($alamat) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center"><?= lang('Store.addressDetails'); ?></a>
        </div>
    </div>
</div>
<script>
const radioKurirElm = document.querySelectorAll('input[name="kurir"]');
const hargaOngkirElm = document.getElementById('harga-ongkir');
const hargaKeseluruhan = document.getElementById('harga-keseluruhan');
const btnPaymentElm = document.getElementById('btn-payment');
const storeRoutePrefix = <?= json_encode(service('request')->getLocale() === 'en' ? '/en' : ''); ?>;

radioKurirElm.forEach((element, ind) => {
    element.addEventListener('change', (e) => {
        hargaOngkirElm.innerHTML = Number(e.target.value) <= 0 ? <?= json_encode(lang('Store.free')); ?> : 'Rp ' + e.target.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        hargaKeseluruhan.innerHTML = 'Rp ' + (Number(e.target.value) + <?= $hargaTotal ?>)
            .toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                ".");
        btnPaymentElm.href = storeRoutePrefix + '/payment/' + ind;
    })
});
</script>


<?= $this->endSection(); ?>
