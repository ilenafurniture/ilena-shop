<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten d-flex justify-content-center align-items-center">
    <div style="width: 60%;">
        <h1 class="teks-sedang text-center">Verifikasi</h1>
        <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1 mx-auto" style="width: fit-content;" role="alert">
                <?= $val['msg']; ?>
            </div>
        <?php } ?>
        <p class="text-center">Masukkan kode OTP dibawah ini!</p>
        <form action="/actionverify" method="post">
            <?= csrf_field(); ?>
            <div class="form mb-1">
                <input type="number" style="letter-spacing: 2em" autofocus class="text-center form-control <?= ($val['val_verify']) ? "is-invalid" : ""; ?>" name="otp">
                <div class="invalid-feedback">
                    <?= $val['val_verify']; ?>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-2">
                <button class="btn-default-merah" type="submit">Verifikasi</button>
            </div>
        </form>
        <p class="mt-3 text-sm-center">OTP belum terkirim? <a href="/kirimotp" class="btn-teks-aja" style="display: inline;">Klik disini</a> untuk mengirim ulang</p>
    </div>
</div>
<?= $this->endSection(); ?>