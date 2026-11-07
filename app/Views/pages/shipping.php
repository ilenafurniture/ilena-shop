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
            <?php if (empty($shippingUnavailable)): ?>
            <span class="ilena-shipping-heading__count"><?= count($kurir); ?> <?= lang('Store.servicesAvailable'); ?></span>
            <?php endif; ?>
        </header>
        <?php if ($checkoutMessage = session()->getFlashdata('msg')): ?>
        <p class="ilena-shipping-session-notice" role="status"><?= esc($checkoutMessage); ?></p>
        <?php endif; ?>
        <div class="container-kurir my-4">
            <?php if (!empty($shippingUnavailable)): ?>
            <section class="ilena-shipping-unavailable" role="status">
                <span class="material-icons" aria-hidden="true">support_agent</span>
                <h2><?= esc(lang('Store.shippingCoverageUnavailable')); ?></h2>
                <p><?= esc(service('request')->getLocale() === 'en'
                    ? 'The delivery cost for this address needs confirmation. No estimated fee has been added to your order.'
                    : 'Ongkir untuk alamat ini perlu dikonfirmasi. Biaya pengiriman belum ditambahkan ke pesanan.'); ?></p>
                <p class="ilena-shipping-unavailable__hint"><?= esc(lang('Store.shippingQuoteWhatsAppHint')); ?></p>
                <a class="btn-default-merah" href="<?= esc($shippingContactUrl, 'attr'); ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('Store.shippingContactSupport')); ?></a>
            </section>
            <?php else: ?>
            <?php foreach ($kurir as $index_k => $k) { ?>
            <input type="radio" name="kurir" id="kurir<?= $index_k ?>" value="<?= $k['harga']?>"
                data-rate-index="<?= esc((string) $index_k, 'attr'); ?>"
                data-store-courier="<?= !empty($k['gratis_ongkir']) ? '1' : '0'; ?>"
                <?= $index_k <= 0 ? 'checked' : '' ?>>
            <label for="kurir<?= $index_k ?>" class="item-kurir">
                <div class="ilena-shipping-option__details">
                    <span class="visually-hidden"><?= esc(trim((string) ($k['nama'] ?? '') . ' ' . (string) ($k['deskripsi'] ?? ''))); ?></span>
                    <p class="mb-1"><?= lang('Store.deliveryEstimate'); ?>
                        <?php if (trim((string) ($k['estimasi'] ?? '')) !== ''): ?>
                            <?= esc($k['estimasi']); ?> <?= lang('Store.days'); ?>
                        <?php else: ?>
                            <?= lang('Store.deliveryEstimateUnavailable'); ?>
                        <?php endif; ?>
                    </p>
                    <p class="mb-1" style="font-weight: 600;">
                        <?php if (!empty($k['gratis_ongkir'])): ?>
                            <span class="text-success"><?= lang('Store.storeCourier'); ?></span>
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
                <div class="ilena-shipping-option__brand" aria-hidden="true">
                    <?php $logoFile = (string) ($k['logo'] ?? ''); ?>
                    <?php if (($k['provider'] ?? '') === 'apm'): ?>
                    <span class="ilena-apm-wordmark" aria-label="APM Logistics"><strong>APM</strong><small>LOGISTICS</small></span>
                    <?php elseif ($logoFile !== ''): ?>
                    <img src="<?= esc(base_url('img/kurir/' . $logoFile . '.png'), 'attr'); ?>" alt="Logo <?= esc($k['nama'], 'attr'); ?>" width="76" height="34" loading="lazy" decoding="async" onerror="this.replaceWith(Object.assign(document.createElement('span'), {className: 'material-icons', textContent: 'local_shipping', ariaHidden: 'true'}))">
                    <?php else: ?>
                    <span class="material-icons" aria-hidden="true">local_shipping</span>
                    <?php endif; ?>
                </div>
            </label>
            <?php
                $shippingReferencePrice = max(0, (int) ($k['harga_asli'] ?? $k['harga'] ?? 0));
                $highShippingCost = empty($k['gratis_ongkir']) && $hargaTotal > 0
                    && $shippingReferencePrice >= (int) ceil($hargaTotal * 0.25);
            ?>
            <?php if ($highShippingCost): ?>
            <p class="ilena-shipping-promo-contact">
                <?= esc(lang('Store.shippingHighCostHint')); ?>
                <a href="<?= esc($shippingContactUrl, 'attr'); ?>" target="_blank" rel="noopener noreferrer">
                    <?= esc(lang('Store.shippingAskPromo')); ?>
                </a>
            </p>
            <?php endif; ?>
            <?php } ?>
            <?php endif; ?>
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
                    <?= !empty($shippingUnavailable) ? (service('request')->getLocale() === 'en' ? 'To be confirmed' : 'Perlu konfirmasi') : (!empty($kurir[0]['gratis_ongkir']) ? lang('Store.storeCourier') : 'Rp ' . number_format($kurir[0]['harga'], 0, ',', '.')); ?>
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
                    <?= !empty($shippingUnavailable) ? esc(lang('Store.shippingTotalPending')) : 'Rp ' . number_format($hargaKeseluruhan, 0, ',', '.'); ?>
                </p>
            </div>
            <a id="btn-payment" <?= count($alamat) > 0 && empty($shippingUnavailable) ? 'href="' . esc(ilena_store_path('/payment/0'), 'attr') . '"' : '' ?>
                class="btn-default-merah <?= count($alamat) > 0 && empty($shippingUnavailable) ? '' : 'disabled' ?> w-100 mt-4 text-center"><?= lang('Store.addressDetails'); ?></a>
        </div>
    </div>
</div>
<script>
const radioKurirElm = document.querySelectorAll('input[name="kurir"]');
const hargaOngkirElm = document.getElementById('harga-ongkir');
const hargaKeseluruhan = document.getElementById('harga-keseluruhan');
const btnPaymentElm = document.getElementById('btn-payment');
const storeRoutePrefix = <?= json_encode(service('request')->getLocale() === 'en' ? '/en' : ''); ?>;
const storeCourierLabel = <?= json_encode(lang('Store.storeCourier')); ?>;
const freeShippingLabel = <?= json_encode(lang('Store.free')); ?>;

radioKurirElm.forEach((element) => {
    element.addEventListener('change', (e) => {
        hargaOngkirElm.textContent = e.target.dataset.storeCourier === '1'
            ? storeCourierLabel
            : (Number(e.target.value) <= 0 ? freeShippingLabel : 'Rp ' + e.target.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        hargaKeseluruhan.innerHTML = 'Rp ' + (Number(e.target.value) + <?= $hargaTotal ?>)
            .toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                ".");
        btnPaymentElm.href = storeRoutePrefix + '/payment/' + e.target.dataset.rateIndex;
    })
});
</script>

<style>
.ilena-shipping-unavailable{max-width:620px;padding:28px 22px;text-align:center;border:1px solid #e8e3df;background:#fffdfb;border-radius:14px}
.ilena-shipping-session-notice{margin:0 0 14px;padding:11px 14px;border-left:3px solid #e84a49;background:#fbf7f4;color:#514943;font-size:13px;line-height:1.5}
.ilena-shipping-unavailable>.material-icons{font-size:34px;color:#8d2e32}
.ilena-shipping-unavailable h2{font-size:19px;font-weight:700;margin:12px 0 8px}
.ilena-shipping-unavailable p{color:#646a70;line-height:1.6;margin:0 0 18px}
.ilena-shipping-option__details{flex:1;min-width:0}
.ilena-shipping-option__brand{flex:0 0 92px;display:flex;align-items:center;justify-content:center;min-width:76px}
.ilena-shipping-option__brand img{display:block;max-width:92px;width:100%;height:40px;object-fit:contain}
@media(max-width:575.98px){.ilena-shipping-option__brand{flex-basis:68px;min-width:60px}.ilena-shipping-option__brand img{max-width:68px;height:32px}}
</style>


<?= $this->endSection(); ?>
