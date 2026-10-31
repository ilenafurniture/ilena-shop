<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="container ilena-auth-page ilena-auth-page--compact">
    <section class="ilena-auth-page__form" aria-labelledby="reset-request-title">
        <p class="ilena-auth-page__eyebrow"><?= esc(lang('Store.ilenaAccountLabel')); ?></p>
        <h1 id="reset-request-title" class="ilena-auth-page__title"><?= esc(lang('Store.resetPasswordTitle')); ?></h1>
        <p><?= esc(lang('Store.resetPasswordDescription')); ?></p>
        <?php if (!empty($msg)): ?><div class="pemberitahuan my-3" role="status"><?= esc($msg); ?></div><?php endif; ?>
        <form method="post" action="<?= esc(ilena_store_path('/forgot-password'), 'attr'); ?>">
            <?= csrf_field(); ?>
            <div aria-hidden="true" style="position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);"><label>Website<input name="company_website" tabindex="-1" autocomplete="off"></label></div>
            <div class="mb-3"><label for="reset-email" class="form-label"><?= esc(lang('Store.email')); ?></label>
                <input id="reset-email" class="form-control" type="email" name="email" autocomplete="email" maxlength="191" required></div>
            <button class="btn btn-default btn-block" type="submit"><?= esc(lang('Store.sendResetLink')); ?></button>
        </form>
        <p class="ilena-auth-page__switch mt-3"><a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= esc(lang('Store.signIn')); ?></a></p>
    </section>
</main>
<?= $this->endSection(); ?>
