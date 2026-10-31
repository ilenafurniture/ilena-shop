<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container d-flex justify-content-center">
    <div class="konten">
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= esc(ilena_store_path('/'), 'attr'); ?>"><?= lang('Store.home'); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= lang('Store.privacyTitle'); ?>
                </li>
            </ol>
        </nav>
        <p class="text-center text-muted"><?= lang('Store.privacyUpdated'); ?></p>
        <h1 class="text-center"><?= lang('Store.privacyTitle'); ?></h1>
        <h5 class="mb-4 text-center" style="color: var(--hijau)">Ilena Furniture</h5>
        <hr class="my-5">
        <p><?= lang('Store.privacyIntro'); ?></p>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.privacyDataTitle'); ?></h3>
        <ul>
            <li><?= lang('Store.privacyAccountData'); ?></li>
            <li><?= lang('Store.privacyOrderData'); ?></li>
            <li><?= lang('Store.privacyChatData'); ?></li>
            <li><?= lang('Store.privacyTechnicalData'); ?></li>
        </ul>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.privacyPurposeTitle'); ?></h3>
        <p><?= lang('Store.privacyPurposeBody'); ?></p>
        <p><?= lang('Store.privacyGoogleBody'); ?></p>
        <p><?= lang('Store.privacyAiBody'); ?></p>
        <p><?= lang('Store.privacyCookiesBody'); ?></p>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.privacyRightsTitle'); ?></h3>
        <p><?= lang('Store.privacyRightsBody'); ?></p>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.privacyRetentionTitle'); ?></h3>
        <p><?= lang('Store.privacyRetentionBody'); ?></p>
        <p><?= lang('Store.privacyChoicesBody'); ?></p>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.privacyUpdatesTitle'); ?></h3>
        <p class="mb-5"><?= lang('Store.privacyUpdatesBody'); ?></p>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.contactUsTitle'); ?></h3>
        <p><?= lang('Store.privacyContactBody'); ?></p>
        <div class="d-flex">
            <div style="width: 100px;">
                <p class="m-0"><?= lang('Store.email'); ?></p>
            </div>
            <div style="width: 10px;">
                <p class="m-0">:</p>
            </div>
            <div style="flex: 1;">
                <p class="m-0"><a href="mailto:cs@ilenafurniture.com">cs@ilenafurniture.com</a></p>
            </div>
        </div>
        <div class="d-flex mb-5">
            <div style="width: 100px;">
                <p class="m-0">WhatsApp</p>
            </div>
            <div style="width: 10px;">
                <p class="m-0">:</p>
            </div>
            <div style="flex: 1;">
                <p class="m-0"><a href="https://wa.me/628112938158" target="_blank" rel="noopener noreferrer">0811-2938-158</a></p>
            </div>
        </div>

        <h3 style="letter-spacing: -1px;"><?= lang('Store.ourProductsServices'); ?></h3>
        <p><?= lang('Store.privacyProductsBody'); ?></p>
        <p><?= lang('Store.privacyDeliverySupport'); ?></p>
        <ul>
            <li>
                <p class="m-0"><?= lang('Store.privacyDeliveryPoint'); ?></p>
            </li>
            <li>
                <p class="m-0"><?= lang('Store.privacyAssemblyPoint'); ?></p>
            </li>
        </ul>
        <p class="mb-5"><?= lang('Store.privacyCatalogBody'); ?></p>

        <h3 style="letter-spacing: -1px;" class="mb-3"><?= lang('Store.shippingReturnsTitle'); ?></h3>
        <h6><?= lang('Store.shippingPolicyTitle'); ?></h6>
        <p><?= lang('Store.shippingPolicyBody'); ?></p>
        <p><?= lang('Store.shippingEstimateBody'); ?></p>

        <h6><?= lang('Store.deliveryTimeTitle'); ?></h6>
        <p><?= lang('Store.deliveryTimeBody'); ?></p>
        <p><?= lang('Store.deliveryInspectionBody'); ?></p>

        <h6><?= lang('Store.shippingStatusTitle'); ?></h6>
        <p><?= lang('Store.shippingStatusBody'); ?></p>

        <h6><?= lang('Store.returnsTitle'); ?></h6>
        <p><?= lang('Store.returnsBody'); ?></p>
    </div>
</div>
<?= $this->endSection(); ?>
