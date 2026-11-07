<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten d-flex justify-content-center align-items-center ilena-auth-page ilena-auth-page--verify">
    <div class="ilena-auth-page__form">
        <p class="ilena-auth-page__eyebrow text-center"><?= lang('Store.accountSecurity'); ?></p>
        <h1 class="teks-sedang text-center"><?= lang('Store.verifyEmailTitle'); ?></h1>
        <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1 mx-auto" style="width: fit-content;" role="alert">
                <?= esc(ilena_localize_notice((string) $val['msg'])); ?>
            </div>
        <?php } ?>
        <p class="text-center"><?= esc(lang('Store.verifyEmailDescription')); ?></p>
        <p class="text-center ilena-verify__destination"><?= esc(lang('Store.otpSentTo')); ?> <strong><?= esc($verificationEmail ?? ''); ?></strong></p>
        <p class="text-center ilena-verify__hint"><?= esc(lang('Store.otpValidityHint')); ?></p>
        <form action="<?= esc(ilena_store_path('/actionverify'), 'attr'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="form mb-1">
                <label for="otp" class="form-label"><?= esc(lang('Store.otpCodeLabel')); ?></label>
                <input id="otp" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required autofocus aria-describedby="otp-error" class="text-center form-control ilena-verify__code <?= ($val['val_verify']) ? "is-invalid" : ""; ?>" name="otp" value="<?= esc(old('otp') ?? '', 'attr'); ?>">
                <div class="invalid-feedback" id="otp-error">
                    <?= esc($val['val_verify'] ?? ''); ?>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-2">
                <button class="btn-default-merah" type="submit"><?= lang('Store.verify'); ?></button>
            </div>
        </form>
        <form action="<?= esc(ilena_store_path('/kirimotp'), 'attr'); ?>" method="post" class="mt-3 text-sm-center">
            <?= csrf_field(); ?>
            <span><?= esc(lang('Store.otpNotReceived')); ?></span>
            <button type="submit" class="btn-teks-aja" style="display:inline; border:0; background:none;"><?= lang('Store.resendOtp'); ?></button>
            <span id="otp-resend-countdown" data-seconds="<?= (int) ($otpResendRemaining ?? 0); ?>" aria-live="polite"></span>
        </form>
    </div>
</div>
<style>
.ilena-verify__destination{margin:.25rem 0;color:#45413e}.ilena-verify__hint{font-size:.9rem;color:#6e6863}.ilena-verify__code{max-width:19rem;margin-inline:auto;font-size:1.35rem;font-weight:700;letter-spacing:.48em;padding-inline:.5rem}#otp-resend-countdown{display:block;margin-top:.35rem;font-size:.85rem;color:#6e6863}
</style>
<script>
(function(){
    const input=document.getElementById('otp');
    const countdown=document.getElementById('otp-resend-countdown');
    if(input) input.addEventListener('input',()=>{input.value=input.value.replace(/\D/g,'').slice(0,6);});
    if(!countdown)return;
    let remaining=Math.max(0,Number(countdown.dataset.seconds)||0);
    const label=<?= json_encode(lang('Store.otpResendCountdown'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    function render(){
        if(remaining<=0){countdown.textContent='';return;}
        const minutes=String(Math.floor(remaining/60)).padStart(2,'0');
        const seconds=String(remaining%60).padStart(2,'0');
        countdown.textContent=label+' '+minutes+':'+seconds;
        remaining-=1;
        window.setTimeout(render,1000);
    }
    render();
})();
</script>
<?= $this->endSection(); ?>
