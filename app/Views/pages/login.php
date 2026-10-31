<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container ilena-auth-page ilena-auth-page--login ilena-auth-page--compact">
            <div class="ilena-auth-page__form">
                <p class="ilena-auth-page__eyebrow"><?= esc(lang('Store.welcomeBack')); ?></p>
                <h1 class="ilena-auth-page__title"><?= esc(lang('Store.signInTitle')); ?></h1>
                <?php if ($val['msg']) { ?>
                    <div class="pemberitahuan my-1" role="alert">
                        <?= $val['msg']; ?>
                    </div>
                <?php } ?>
                <form id="loginForm" action="<?= esc(ilena_store_path('/actionlogin'), 'attr'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="auth_form_nonce" value="<?= esc($authFormNonce ?? '', 'attr'); ?>">
                    <div class="hp-wrap" aria-hidden="true" style="position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);">
                        <label><?= esc(lang('Store.fillHoneypot')); ?><input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><?= esc(lang('Store.email')); ?></label>
                        <input id="email" name="email" type="email" autocomplete="username" class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>" placeholder="<?= esc(lang('Store.enterEmail'), 'attr'); ?>" value="<?= old('email'); ?>">
                        <div class="invalid-feedback"><?= esc(lang('Store.invalidEmail')); ?></div>
                    </div>
                    <label for="password" class="form-label"><?= esc(lang('Store.password')); ?></label>
                    <div class="input-group mb-3 has-validation">
                        <input id="password" name="sandi" type="password" autocomplete="current-password" class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" placeholder="<?= esc(lang('Store.enterPassword'), 'attr'); ?>">
                        <button type="button" class="input-group-text ilena-auth-page__password-toggle" aria-label="<?= esc(lang('Store.showPassword'), 'attr'); ?>" aria-controls="password" aria-pressed="false" onclick="togglePassword(event)">
                            <i class="material-icons" aria-hidden="true">visibility</i>
                        </button>
                        <div class="invalid-feedback"><?= esc(lang('Store.invalidPassword')); ?></div>
                    </div>
                    <!-- <div class="mb-3">
                        <input type="checkbox" onclick="togglePassword()" id="showPassword">
                        <label for="showPassword" class="form-label" style="font-size:small;">Tampilkan Kata
                            Sandi</label>
                    </div> -->
                    <?= view('partials/auth_recaptcha', ['error' => $captchaError ?? null], ['saveData' => false]); ?>
                    <div class="mb-3 d-flex w-100 justify-content-center">
                        <button type="submit" class="btn btn-default btn-block"><?= esc(lang('Store.signIn')); ?></button>
                    </div>
                    <?php if (!empty($googleLoginEnabled)): ?>
                    <div class="ilena-auth-divider" aria-hidden="true"><span><?= esc(lang('Store.orContinueWith')); ?></span></div>
                    <a class="ilena-google-signin" href="<?= esc(ilena_store_path('/auth/google'), 'attr'); ?>" rel="nofollow">
                        <svg aria-hidden="true" viewBox="0 0 48 48" width="20" height="20"><path fill="#4285F4" d="M43.6 24.5c0-1.4-.1-2.8-.4-4.1H24v7.8h11c-.5 2.5-1.9 4.6-4 6v5h6.5c3.8-3.5 6.1-8.6 6.1-14.7z"/><path fill="#34A853" d="M24 44c5.5 0 10.1-1.8 13.5-4.9l-6.5-5c-1.8 1.2-4.1 2-7 2-5.3 0-9.8-3.6-11.4-8.4H5.9v5.2C9.3 39.6 16.1 44 24 44z"/><path fill="#FBBC05" d="M12.6 27.7a12 12 0 0 1 0-7.4v-5.2H5.9a20 20 0 0 0 0 17.8l6.7-5.2z"/><path fill="#EA4335" d="M24 11.9c3 0 5.7 1 7.8 3.1l5.8-5.8C34.1 5.8 29.5 4 24 4 16.1 4 9.3 8.4 5.9 15.1l6.7 5.2c1.6-4.8 6.1-8.4 11.4-8.4z"/></svg>
                        <span><?= esc(lang('Store.continueWithGoogle')); ?></span>
                    </a>
                    <?php endif; ?>
                    <div>
                        <p class="ilena-auth-page__switch"><a href="<?= esc(ilena_store_path('/forgot-password'), 'attr'); ?>"><?= esc(lang('Store.forgotPassword')); ?></a></p>
                        <p class="ilena-auth-page__switch"><?= esc(lang('Store.noAccount')); ?> <a href="<?= esc(ilena_store_path('/register'), 'attr'); ?>"><?= esc(lang('Store.registerNow')); ?></a></p>
                    </div>
                </form>
            </div>
</div>
<style>
.ilena-auth-divider{display:flex;align-items:center;gap:12px;margin:18px 0;color:#777;font-size:.82rem}.ilena-auth-divider:before,.ilena-auth-divider:after{content:"";height:1px;flex:1;background:#e7e4e1}.ilena-google-signin{width:100%;min-height:46px;display:flex;align-items:center;justify-content:center;gap:10px;border:1px solid #dedbd8;border-radius:8px;background:#fff;color:#242424;text-decoration:none;font-weight:600;transition:background .15s ease,border-color .15s ease}.ilena-google-signin:hover{background:#faf9f7;border-color:#c8c3bf;color:#111}
</style>
<script>
    function togglePassword(e) {
        var passwordField = document.querySelector('input[name="sandi"]');
        var show = passwordField.type === 'password';
        passwordField.type = show ? 'text' : 'password';
        e.currentTarget.setAttribute('aria-pressed', String(show));
        e.currentTarget.setAttribute('aria-label', show ? <?= json_encode(lang('Store.hidePassword')); ?> : <?= json_encode(lang('Store.showPassword')); ?>);
        e.currentTarget.querySelector('i').textContent = show ? 'visibility_off' : 'visibility';
    }
</script>
<?= $this->endSection(); ?>
