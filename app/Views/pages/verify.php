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
        <?php if (!empty($verificationRecovery)): ?>
            <p class="text-center"><?= esc(lang('Store.verificationRecoveryDescription')); ?></p>
            <form action="<?= esc(ilena_store_path('/kirimotp'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="form mb-3">
                    <label for="recovery-email" class="form-label"><?= esc(lang('Store.email')); ?></label>
                    <input id="recovery-email" name="email" type="email" autocomplete="email" maxlength="191" required autofocus class="form-control" placeholder="<?= esc(lang('Store.enterEmail'), 'attr'); ?>" value="<?= esc($recoveryEmail ?? '', 'attr'); ?>">
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn-default-merah" type="submit"><?= esc(lang('Store.requestVerificationCode')); ?></button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-center"><?= esc(lang('Store.verifyEmailDescription')); ?></p>
            <p class="text-center ilena-verify__destination"><?= esc(lang('Store.otpSentTo')); ?> <strong><?= esc($verificationEmail ?? ''); ?></strong></p>
            <p class="text-center ilena-verify__hint"><?= esc(lang('Store.otpValidityHint')); ?></p>
            <form action="<?= esc(ilena_store_path('/actionverify'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?>
                <fieldset class="form mb-1 ilena-verify__fieldset" aria-describedby="otp-error">
                    <legend class="form-label"><?= esc(lang('Store.otpCodeLabel')); ?></legend>
                    <div class="ilena-verify__digits<?= !empty($val['val_verify']) ? ' is-invalid' : ''; ?>" role="group" aria-label="<?= esc(lang('Store.otpCodeLabel'), 'attr'); ?>">
                        <?php for ($digitIndex = 0; $digitIndex < 6; $digitIndex++): ?>
                            <input
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="<?= $digitIndex === 0 ? '6' : '1'; ?>"
                                required
                                name="otp_digits[]"
                                class="form-control ilena-verify__digit"
                                data-otp-digit
                                aria-label="<?= esc(sprintf(lang('Store.otpDigitPosition'), $digitIndex + 1), 'attr'); ?>"
                                aria-describedby="otp-error"
                                <?= !empty($val['val_verify']) ? 'aria-invalid="true"' : ''; ?>
                                <?= $digitIndex === 0 ? 'id="otp-digit-1" autocomplete="one-time-code" autofocus' : 'autocomplete="off"'; ?>
                            >
                        <?php endfor; ?>
                    </div>
                    <div class="invalid-feedback" id="otp-error">
                        <?= esc($val['val_verify'] ?? ''); ?>
                    </div>
                </fieldset>
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
        <?php endif; ?>
    </div>
</div>
<style>
.ilena-verify__destination{margin:.25rem 0;color:#45413e}
.ilena-verify__hint{font-size:.9rem;color:#6e6863}
.ilena-verify__fieldset{min-width:0;border:0;padding:0}
.ilena-verify__digits{display:grid;grid-template-columns:repeat(6,minmax(0,2.8rem));justify-content:center;gap:.55rem}
.ilena-verify__digit{height:3.25rem;padding:.25rem;text-align:center;font-size:1.35rem;font-weight:700;color:#262321;border:1px solid #c9c1bb;border-radius:.55rem;background:#fff;transition:border-color .16s ease,box-shadow .16s ease}
.ilena-verify__digit:focus{border-color:#982f3b;box-shadow:0 0 0 .2rem rgb(152 47 59 / 16%);outline:0}
.ilena-verify__digits.is-invalid .ilena-verify__digit{border-color:#b42318}
.ilena-verify__fieldset .invalid-feedback{display:block;min-height:1.25rem;text-align:center}
#otp-resend-countdown{display:block;margin-top:.35rem;font-size:.85rem;color:#6e6863}
@media(max-width:380px){.ilena-verify__digits{grid-template-columns:repeat(6,minmax(0,2.45rem));gap:.38rem}.ilena-verify__digit{height:3rem;font-size:1.2rem}}
</style>
<script>
(function(){
    const inputs=Array.from(document.querySelectorAll('[data-otp-digit]'));
    const countdown=document.getElementById('otp-resend-countdown');
    if(inputs.length){
        const setDigits=(value,start=0)=>{
            const digits=String(value).replace(/\D/g,'').slice(0,inputs.length);
            inputs.forEach((field,index)=>{if(index>=start)field.value='';});
            Array.from(digits).forEach((digit,index)=>{if(start+index<inputs.length)inputs[start+index].value=digit;});
            const focusIndex=Math.min(start+digits.length,inputs.length-1);
            inputs[focusIndex].focus();
            inputs[focusIndex].select();
        };
        inputs.forEach((input,index)=>{
            input.addEventListener('input',()=>{
                const value=input.value.replace(/\D/g,'');
                if(value.length>1){setDigits(value,index);return;}
                input.value=value.slice(0,1);
                if(input.value&&index<inputs.length-1)inputs[index+1].focus();
            });
            input.addEventListener('keydown',(event)=>{
                if(event.key==='Backspace'&&!input.value&&index>0){inputs[index-1].focus();inputs[index-1].value='';event.preventDefault();}
                else if(event.key==='ArrowLeft'&&index>0){inputs[index-1].focus();event.preventDefault();}
                else if(event.key==='ArrowRight'&&index<inputs.length-1){inputs[index+1].focus();event.preventDefault();}
            });
            input.addEventListener('paste',(event)=>{
                const pasted=(event.clipboardData||window.clipboardData).getData('text');
                if(!pasted)return;
                event.preventDefault();
                setDigits(pasted,index);
            });
        });
    }
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
