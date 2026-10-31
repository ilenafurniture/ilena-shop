<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="container ilena-auth-page ilena-auth-page--compact">
    <section class="ilena-auth-page__form" aria-labelledby="reset-password-title">
        <p class="ilena-auth-page__eyebrow"><?= esc(lang('Store.ilenaAccountLabel')); ?></p>
        <h1 id="reset-password-title" class="ilena-auth-page__title"><?= esc(lang('Store.resetPasswordTitle')); ?></h1>
        <?php if (!empty($msg)): ?><div class="pemberitahuan my-3" role="alert"><?= esc($msg); ?></div><?php endif; ?>
        <form method="post" action="<?= esc(ilena_store_path('/reset-password/' . $token), 'attr'); ?>">
            <?= csrf_field(); ?>
            <div class="mb-3"><label for="new-password" class="form-label"><?= esc(lang('Store.newPasswordLabel')); ?></label>
                <input id="new-password" class="form-control" type="password" name="password" autocomplete="new-password" minlength="12" maxlength="72" required></div>
            <div class="mb-3"><label for="confirm-password" class="form-label"><?= esc(lang('Store.confirmNewPasswordLabel')); ?></label>
                <input id="confirm-password" class="form-control" type="password" name="password_confirm" autocomplete="new-password" minlength="12" maxlength="72" required></div>
            <button class="btn btn-default btn-block" type="submit"><?= esc(lang('Store.updatePassword')); ?></button>
        </form>
    </section>
</main>
<?= $this->endSection(); ?>
