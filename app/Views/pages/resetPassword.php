<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="container ilena-auth-page ilena-auth-page--compact">
    <section class="ilena-auth-page__form" aria-labelledby="reset-password-title">
        <p class="ilena-auth-page__eyebrow"><?= esc(lang('Store.ilenaAccountLabel')); ?></p>
        <h1 id="reset-password-title" class="ilena-auth-page__title"><?= esc(lang('Store.resetPasswordTitle')); ?></h1>
        <?php if (!empty($msg)): ?><div class="pemberitahuan my-3" role="alert"><?= esc(ilena_localize_notice((string) $msg)); ?></div><?php endif; ?>
        <form method="post" action="<?= esc(ilena_store_path('/reset-password/' . $token), 'attr'); ?>">
            <?= csrf_field(); ?>
            <div class="mb-3">
                <label for="new-password" class="form-label"><?= esc(lang('Store.newPasswordLabel')); ?></label>
                <div class="input-group">
                    <input id="new-password" class="form-control" type="password" name="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                    <button type="button" class="input-group-text ilena-auth-page__password-toggle" data-password-toggle aria-label="<?= esc(lang('Store.showPassword'), 'attr'); ?>" aria-controls="new-password" aria-pressed="false">
                        <span class="material-icons" aria-hidden="true">visibility</span>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label"><?= esc(lang('Store.confirmNewPasswordLabel')); ?></label>
                <div class="input-group">
                    <input id="confirm-password" class="form-control" type="password" name="password_confirm" autocomplete="new-password" minlength="12" maxlength="72" required>
                    <button type="button" class="input-group-text ilena-auth-page__password-toggle" data-password-toggle aria-label="<?= esc(lang('Store.showPassword'), 'attr'); ?>" aria-controls="confirm-password" aria-pressed="false">
                        <span class="material-icons" aria-hidden="true">visibility</span>
                    </button>
                </div>
            </div>
            <button class="btn btn-default btn-block" type="submit"><?= esc(lang('Store.updatePassword')); ?></button>
        </form>
    </section>
</main>
<script>
(() => {
    const showLabel = <?= json_encode(lang('Store.showPassword'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const hideLabel = <?= json_encode(lang('Store.hidePassword'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const field = document.getElementById(button.getAttribute('aria-controls'));
        if (!field) return;
        button.addEventListener('click', () => {
            const visible = field.type === 'password';
            field.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? hideLabel : showLabel);
            button.querySelector('.material-icons').textContent = visible ? 'visibility_off' : 'visibility';
        });
    });
})();
</script>
<?= $this->endSection(); ?>
