<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container">
    <div class="konten">
        <h1 class="ilena-terms-title"><?= lang('Store.termsTitle'); ?></h1>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= esc(ilena_store_path('/'), 'attr'); ?>"><?= lang('Store.home'); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= lang('Store.termsTitle'); ?></li>
            </ol>
        </nav>

        <h2><?= lang('Store.termsIntroTitle'); ?></h2>
        <p><?= lang('Store.termsIntroBody'); ?></p>

        <h2><?= lang('Store.websiteUseTitle'); ?></h2>
        <p><?= lang('Store.websiteUseBody'); ?></p>

        <h2><?= lang('Store.companyProfileTitle'); ?></h2>
        <p><?= lang('Store.companyProfileBody'); ?></p>

        <h2><?= lang('Store.productInfoTitle'); ?></h2>
        <p><?= lang('Store.productInfoBody'); ?></p>

        <h2><?= lang('Store.termsOrdersTitle'); ?></h2>
        <p><?= lang('Store.termsOrdersBody'); ?></p>

        <h2><?= lang('Store.termsAccountsTitle'); ?></h2>
        <p><?= lang('Store.termsAccountsBody'); ?></p>

        <h2><?= lang('Store.termsChatTitle'); ?></h2>
        <p><?= lang('Store.termsChatBody'); ?></p>

        <h2><?= lang('Store.contentUseTitle'); ?></h2>
        <p><?= lang('Store.contentUseTerms'); ?></p>
        <p><?= lang('Store.prohibitedUseTerms'); ?></p>

        <h2><?= lang('Store.promotionInfoTitle'); ?></h2>
        <p><?= lang('Store.promotionInfoBody'); ?></p>

        <h2><?= lang('Store.serviceInfoTitle'); ?></h2>
        <p><?= lang('Store.serviceInfoBody'); ?></p>
        <p><?= lang('Store.serviceHoursTerms'); ?></p>

        <h2><?= lang('Store.termsChangesTitle'); ?></h2>
        <p><?= lang('Store.termsChangesBody'); ?></p>
    </div>
</div>
<?= $this->endSection(); ?>
