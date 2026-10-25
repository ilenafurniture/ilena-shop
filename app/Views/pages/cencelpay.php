<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>


<main class="ilena-payment-state ilena-payment-cancel">
    <span class="ilena-payment-state__icon" aria-hidden="true">
        <i class="material-icons">do_not_disturb_on</i>
    </span>
    <p class="ilena-payment-state__eyebrow">STATUS PEMBAYARAN</p>
    <h1>Pembayaran dibatalkan</h1>
    <p class="ilena-payment-state__description">Transaksi dibatalkan sebelum selesai. Anda dapat kembali ke beranda dan memilih produk untuk mencoba lagi.</p>
    <a href="<?= esc(base_url(), 'attr'); ?>" class="ilena-payment-state__action">Kembali ke beranda <span aria-hidden="true">↗</span></a>
</main>


    <?= $this->endSection(); ?>
