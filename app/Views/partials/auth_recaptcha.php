<?php $captchaConfig = config(\Config\AuthCaptcha::class); ?>
<?php if ($captchaConfig->enabled): ?>
<?php $captchaError = is_string($error ?? null) ? trim($error) : ''; ?>
<section class="ilena-auth-captcha<?= $captchaError !== '' ? ' has-error' : ''; ?>" aria-label="Verifikasi keamanan">
    <div class="ilena-auth-captcha__heading">
        <span class="ilena-auth-captcha__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3.2 19 6v5.2c0 4.4-2.9 7.9-7 9.6-4.1-1.7-7-5.2-7-9.6V6l7-2.8Z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="ilena-auth-captcha__copy">
            <strong>Verifikasi keamanan</strong>
            <span>Langkah singkat untuk melindungi akun Anda.</span>
        </span>
    </div>

    <?php if (trim($captchaConfig->siteKey) !== ''): ?>
        <div class="ilena-auth-captcha__widget">
            <div class="g-recaptcha" data-sitekey="<?= esc($captchaConfig->siteKey, 'attr'); ?>"
                data-callback="ilenaCaptchaVerified" data-expired-callback="ilenaCaptchaExpired"
                data-error-callback="ilenaCaptchaFailed"></div>
        </div>
        <p class="ilena-auth-captcha__message" role="<?= $captchaError !== '' ? 'alert' : 'status'; ?>" aria-live="<?= $captchaError !== '' ? 'assertive' : 'polite'; ?>">
            <?= esc($captchaError !== '' ? $captchaError : 'Centang verifikasi untuk melanjutkan.'); ?>
        </p>
    <?php else: ?>
        <p class="ilena-auth-captcha__message is-error" role="alert">Verifikasi keamanan sedang tidak tersedia. Silakan coba beberapa saat lagi.</p>
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
    window.ilenaCaptchaSetState('verified', 'Verifikasi berhasil. Anda dapat melanjutkan.');
};
window.ilenaCaptchaExpired = function () {
    window.ilenaCaptchaSetState('error', 'Verifikasi kedaluwarsa. Centang kembali untuk melanjutkan.');
};
window.ilenaCaptchaFailed = function () {
    window.ilenaCaptchaSetState('error', 'Verifikasi tidak tersambung. Periksa koneksi lalu coba lagi.');
};
</script>
<script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>
<?php endif; ?>
<?php endif; ?>
