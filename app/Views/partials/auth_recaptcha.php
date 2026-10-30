<?php $captchaConfig = config(\Config\AuthCaptcha::class); ?>
<?php if ($captchaConfig->enabled): ?>
<?php $captchaError = is_string($error ?? null) ? trim($error) : ''; ?>
<?php $captchaLocale = service('request')->getLocale() === 'en' ? 'en' : 'id'; ?>
<section class="ilena-auth-captcha<?= $captchaError !== '' ? ' has-error' : ''; ?>" aria-label="<?= esc(lang('Store.authCaptchaLabel'), 'attr'); ?>">
    <div class="ilena-auth-captcha__heading">
        <span class="ilena-auth-captcha__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3.2 19 6v5.2c0 4.4-2.9 7.9-7 9.6-4.1-1.7-7-5.2-7-9.6V6l7-2.8Z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="ilena-auth-captcha__copy">
            <strong><?= esc(lang('Store.authCaptchaLabel')); ?></strong>
            <span><?= esc(lang('Store.authCaptchaHint')); ?></span>
        </span>
    </div>

    <?php if (trim($captchaConfig->siteKey) !== ''): ?>
        <div class="ilena-auth-captcha__widget">
            <div class="g-recaptcha" data-sitekey="<?= esc($captchaConfig->siteKey, 'attr'); ?>"
                data-callback="ilenaCaptchaVerified" data-expired-callback="ilenaCaptchaExpired"
                data-error-callback="ilenaCaptchaFailed"></div>
        </div>
        <p class="ilena-auth-captcha__message" role="<?= $captchaError !== '' ? 'alert' : 'status'; ?>" aria-live="<?= $captchaError !== '' ? 'assertive' : 'polite'; ?>">
            <?= esc($captchaError !== '' ? $captchaError : lang('Store.authCaptchaPrompt')); ?>
        </p>
    <?php else: ?>
        <p class="ilena-auth-captcha__message is-error" role="alert"><?= esc(lang('Store.authCaptchaUnavailable')); ?></p>
    <?php endif; ?>
</section>
<?php if (trim($captchaConfig->siteKey) !== ''): ?>
<script>
window.ilenaCaptchaSetState = function (state, message) {
    document.querySelectorAll('.ilena-auth-captcha').forEach(function (root) {
        root.classList.toggle('is-verified', state === 'verified');
        root.classList.toggle('has-error', state === 'error');
        var status = root.querySelector('.ilena-auth-captcha__message');
        if (!status) return;
        status.textContent = message;
        status.setAttribute('role', state === 'error' ? 'alert' : 'status');
        status.setAttribute('aria-live', state === 'error' ? 'assertive' : 'polite');
    });
};
window.ilenaCaptchaVerified = function () {
    window.ilenaCaptchaSetState('verified', <?= json_encode(lang('Store.authCaptchaVerified'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
};
window.ilenaCaptchaExpired = function () {
    window.ilenaCaptchaSetState('error', <?= json_encode(lang('Store.authCaptchaExpired'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
};
window.ilenaCaptchaFailed = function () {
    window.ilenaCaptchaSetState('error', <?= json_encode(lang('Store.authCaptchaConnectionFailed'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
};
</script>
<script src="https://www.google.com/recaptcha/api.js?hl=<?= esc($captchaLocale, 'attr'); ?>" async defer></script>
<?php endif; ?>
<?php endif; ?>
