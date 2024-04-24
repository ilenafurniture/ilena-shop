<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>


<<div class="d-flex align-items-center">
    <div class="container">
        <div class="justify-content-center">
            <div class="text-center">
                <h1>Pembayaran Berhasil</h1>
                <p>Terima kasih telah melakukan pembayaran.</p>
                <i class="bi bi-check-circle text-success mt-4 mb-4"></i>
                <div class="mb-3">
                    <a href="<?= base_url(); ?>" class="btn btn-primary1 btn-lg me-3 mb-2">Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </div>
    </div>


    <?= $this->endSection(); ?>