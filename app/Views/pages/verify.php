<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten d-flex justify-content-center align-items-center ilena-auth-page ilena-auth-page--verify">
    <div class="ilena-auth-page__form">
        <p class="ilena-auth-page__eyebrow text-center"><?= lang('Store.accountSecurity'); ?></p>
        <h1 class="teks-sedang text-center"><?= lang('Store.verifyEmailTitle'); ?></h1>
        <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1 mx-auto" style="width: fit-content;" role="alert">
                <?= $val['msg']; ?>
            </div>
        <?php } ?>
        <p class="text-center"><?= lang('Store.enterOtp'); ?></p>
        <form action="<?= esc(ilena_store_path('/actionverify'), 'attr'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="form mb-1">
                <input id="otp" type="number" inputmode="numeric" style="letter-spacing: 2em" autofocus class="text-center form-control <?= ($val['val_verify']) ? "is-invalid" : ""; ?>" name="otp">
                <div class="invalid-feedback">
                    <?= $val['val_verify']; ?>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-2">
                <button class="btn-default-merah" type="submit"><?= lang('Store.verify'); ?></button>
            </div>
        </form>
        <form action="<?= esc(ilena_store_path('/kirimotp'), 'attr'); ?>" method="post" class="mt-3 text-sm-center">
            <?= csrf_field(); ?>
            <span><?= lang('Store.otpNotReceived'); ?></span>
            <button type="submit" class="btn-teks-aja" style="display:inline; border:0; background:none;"><?= lang('Store.resendOtp'); ?></button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
