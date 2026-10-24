<?php $captchaConfig = config(\Config\AuthCaptcha::class); ?>
<?php if ($captchaConfig->enabled): ?>
<div class="ilena-auth-captcha" role="group" aria-label="Verifikasi keamanan">
    <?php if (trim($captchaConfig->siteKey) !== ''): ?>
        <div class="g-recaptcha" data-sitekey="<?= esc($captchaConfig->siteKey, 'attr'); ?>"></div>
    <?php endif; ?>
    <p class="small text-muted mt-2" role="status" aria-live="polite">Selesaikan verifikasi keamanan sebelum melanjutkan.</p>
</div>
<?php if (trim($captchaConfig->siteKey) !== ''): ?>
<script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>
<?php endif; ?>
<?php endif; ?>
