<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php $isEnglish = service('request')->getLocale() === 'en'; ?>

<main class="ilena-payment-state ilena-payment-cancel">
    <span class="ilena-payment-state__icon" aria-hidden="true">
        <i class="material-icons">do_not_disturb_on</i>
    </span>
    <p class="ilena-payment-state__eyebrow"><?= $isEnglish ? 'PAYMENT STATUS' : 'STATUS PEMBAYARAN'; ?></p>
    <h1><?= $isEnglish ? 'Payment cancelled' : 'Pembayaran dibatalkan'; ?></h1>
    <p class="ilena-payment-state__description"><?= $isEnglish ? 'The transaction was cancelled before completion. Return to the home page and choose a product to try again.' : 'Transaksi dibatalkan sebelum selesai. Anda dapat kembali ke beranda dan memilih produk untuk mencoba lagi.'; ?></p>
    <a href="<?= esc($isEnglish ? site_url('en') : base_url(), 'attr'); ?>" class="ilena-payment-state__action"><?= $isEnglish ? 'Back to home' : 'Kembali ke beranda'; ?> <span aria-hidden="true">↗</span></a>
</main>


    <?= $this->endSection(); ?>
