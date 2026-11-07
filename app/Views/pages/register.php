<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
.hp-wrap {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0 0 0 0) !important;
    clip-path: inset(50%) !important;
    border: 0 !important;
    white-space: nowrap !important;
}

#ab-msg {
    font-size: 12px;
    color: #b91c1c;
    display: none;
}

#ab-msg.show {
    display: block;
}

.agreement-box {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fafafa;
}

.agreement-box input {
    width: 18px;
    height: 18px;
    margin-top: 3px;
    accent-color: var(--merah);
    cursor: pointer;
}

.agreement-box label {
    line-height: 1.45;
    cursor: pointer;
}

.agreement-box a {
    color: var(--merah);
    font-weight: 700;
    text-decoration: underline;
}
.ilena-auth-divider{display:flex;align-items:center;gap:12px;margin:18px 0;color:#777;font-size:.82rem}
.ilena-auth-divider:before,.ilena-auth-divider:after{content:"";height:1px;flex:1;background:#e7e4e1}
.ilena-google-signin{width:100%;min-height:46px;display:flex;align-items:center;justify-content:center;gap:10px;border:1px solid #dedbd8;border-radius:8px;background:#fff;color:#242424;text-decoration:none;font-weight:600;transition:background .15s ease,border-color .15s ease}
.ilena-google-signin:hover{background:#faf9f7;border-color:#c8c3bf;color:#111}
</style>

<div class="container ilena-auth-page ilena-auth-page--register ilena-auth-page--compact">
        <div class="ilena-auth-page__form">
            <p class="ilena-auth-page__eyebrow"><?= lang('Store.startJourney'); ?></p>
            <h1 class="ilena-auth-page__title"><?= lang('Store.createIlenaAccount'); ?></h1>
            <p class="ilena-auth-page__intro"><?= esc(lang('Store.registrationEmailNote')); ?></p>

            <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1" role="alert">
                <?= esc(ilena_localize_notice((string) $val['msg'])); ?>
            </div>
            <?php } ?>

            <?php if (!empty($googleLoginEnabled)): ?>
                <a class="ilena-google-signin" href="<?= esc(ilena_store_path('/auth/google'), 'attr'); ?>" rel="nofollow">
                    <svg aria-hidden="true" viewBox="0 0 48 48" width="20" height="20"><path fill="#4285F4" d="M43.6 24.5c0-1.4-.1-2.8-.4-4.1H24v7.8h11c-.5 2.5-1.9 4.6-4 6v5h6.5c3.8-3.5 6.1-8.6 6.1-14.7z"/><path fill="#34A853" d="M24 44c5.5 0 10.1-1.8 13.5-4.9l-6.5-5c-1.8 1.2-4.1 2-7 2-5.3 0-9.8-3.6-11.4-8.4H5.9v5.2C9.3 39.6 16.1 44 24 44z"/><path fill="#FBBC05" d="M12.6 27.7a12 12 0 0 1 0-7.4v-5.2H5.9a20 20 0 0 0 0 17.8l6.7-5.2z"/><path fill="#EA4335" d="M24 11.9c3 0 5.7 1 7.8 3.1l5.8-5.8C34.1 5.8 29.5 4 24 4 16.1 4 9.3 8.4 5.9 15.1l6.7 5.2c1.6-4.8 6.1-8.4 11.4-8.4z"/></svg>
                    <span><?= esc(lang('Store.continueWithGoogle')); ?></span>
                </a>
                <div class="ilena-auth-divider" aria-hidden="true"><span><?= esc(lang('Store.orContinueWith')); ?></span></div>
            <?php endif; ?>

            <form id="registerForm" action="<?= esc(ilena_store_path('/actionregister'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="auth_form_nonce" value="<?= esc($authFormNonce ?? '', 'attr'); ?>">
                <!-- Honeypot fields; the server rejects submissions that fill them. -->
                <div class="hp-wrap" aria-hidden="true">
                    <label>Website
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </label>
                    <label>Referral
                        <input type="text" name="ref_code" tabindex="-1" autocomplete="off">
                    </label>
                </div>

                <div class="mb-3">
                    <label for="fullname" class="form-label"><?= lang('Store.fullNameLabel'); ?></label>
                    <input id="fullname" name="nama" type="text" autocomplete="name" required maxlength="100" aria-describedby="fullname-feedback" class="form-control <?= ($val['val_nama']) ? "is-invalid" : ""; ?>"
                        placeholder="<?= esc(lang('Store.enterFullName'), 'attr'); ?>" value="<?= old('nama'); ?>">
                    <div class="invalid-feedback" id="fullname-feedback"><?= esc($val['val_nama'] ?: lang('Store.enterFullName')); ?></div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required maxlength="191" autocapitalize="none" spellcheck="false" aria-describedby="email-feedback"
                        class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>"
                        placeholder="<?= esc(lang('Store.enterEmail'), 'attr'); ?>" value="<?= old('email'); ?>">
                    <div class="invalid-feedback" id="email-feedback"><?= esc($val['val_email'] ?: lang('Store.activeEmailHint')); ?></div>
                </div>

                <label for="password" class="form-label">Kata sandi</label>
                <div class="input-group mb-3 has-validation">
                    <input id="password" name="sandi" type="password" autocomplete="new-password" required minlength="12" maxlength="72" aria-describedby="password-help password-feedback"
                        class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>"
                        placeholder="<?= esc(lang('Store.createPassword'), 'attr'); ?>">
                    <button type="button" class="input-group-text ilena-auth-page__password-toggle"
                        aria-label="Tampilkan kata sandi" aria-controls="password" aria-pressed="false" onclick="togglePassword(event)">
                        <i class="material-icons" aria-hidden="true">visibility</i>
                    </button>
                    <div class="invalid-feedback" id="password-feedback"><?= esc($val['val_sandi'] ?: 'Kata sandi minimal 12 karakter.'); ?></div>
                </div>
                <div class="form-text mb-3" id="password-help"><?= lang('Store.passwordLengthHint'); ?></div>

                <div class="mb-3">
                    <div class="agreement-box <?= ($val['val_syarat'] ?? false) ? "is-invalid" : ""; ?>">
                        <input type="checkbox" id="validation-syarat" name="validasi-syarat" value="1" required
                            <?= old('validasi-syarat') ? 'checked' : ''; ?>>
                        <label for="validation-syarat" class="m-0">
                            <?= lang('Store.acceptTermsLead'); ?>
                            <a href="<?= esc(ilena_store_path('/syarat'), 'attr'); ?>" target="_blank" rel="noopener"><?= lang('Store.registrationTerms'); ?></a>.
                        </label>
                    </div>
                    <div class="invalid-feedback d-block" id="syarat-feedback">
                        <?= esc($val['val_syarat'] ?? ''); ?>
                    </div>
                </div>

                <?= view('partials/auth_recaptcha', ['error' => $captchaError ?? null], ['saveData' => false]); ?>
                <div class="mb-3 d-flex w-100 justify-content-center">
                    <button id="btnSubmit" type="submit" class="btn btn-default btn-block"><?= lang('Store.registerSubmit'); ?></button>
                </div>
                <div id="ab-msg" aria-live="polite"></div>

                <div>
                    <p class="ilena-auth-page__switch"><?= lang('Store.alreadyHaveAccount'); ?>
                        <a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= lang('Store.signInNow'); ?></a>
                    </p>
                    <p class="ilena-auth-page__switch" role="note"><?= esc(lang('Store.verificationRegisterHint')); ?>
                        <a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= esc(lang('Store.verificationLoginLink')); ?></a>
                    </p>
                </div>
            </form>
        </div>
</div>

<script>
function togglePassword(e) {
    var passwordField = document.querySelector('input[name="sandi"]');
    var show = passwordField.type === 'password';
    passwordField.type = show ? 'text' : 'password';
    e.currentTarget.setAttribute('aria-pressed', String(show));
    e.currentTarget.setAttribute('aria-label', show ? <?= json_encode(lang('Store.hidePassword')); ?> : <?= json_encode(lang('Store.showPassword')); ?>);
    e.currentTarget.querySelector('i').textContent = show ? 'visibility_off' : 'visibility';
}
(function() {
    const form = document.getElementById('registerForm');
    const btn = document.getElementById('btnSubmit');
    const msg = document.getElementById('ab-msg');
    const hp1 = form.querySelector('input[name="website"]');
    const hp2 = form.querySelector('input[name="ref_code"]');
    const agreement = document.getElementById('validation-syarat');
    const syaratFeedback = document.getElementById('syarat-feedback');
    const email = document.getElementById('email');

    email.addEventListener('blur', () => {
        email.value = email.value.trim().toLowerCase();
    });

    function showMsg(text) {
        if (!text) {
            msg.classList.remove('show');
            msg.textContent = '';
            return;
        }
        msg.textContent = text;
        msg.classList.add('show');
    }
    agreement.addEventListener('change', () => {
        if (agreement.checked && syaratFeedback) syaratFeedback.textContent = '';
    });

    form.addEventListener('submit', function(e) {
        if (!agreement.checked) {
            e.preventDefault();
            if (syaratFeedback) syaratFeedback.textContent = <?= json_encode(lang('Store.acceptTermsError')); ?>;
            showMsg(<?= json_encode(lang('Store.acceptTermsError')); ?>);
            return;
        }
        if (hp1.value.trim() !== '' || hp2.value.trim() !== '') {
            e.preventDefault();
            showMsg(<?= json_encode(lang('Store.blockedBot')); ?>);
            return;
        }
        if (btn.dataset.submitted === '1') {
            e.preventDefault();
            showMsg(<?= json_encode(lang('Store.requestProcessing')); ?>);
            return;
        }
        btn.dataset.submitted = '1';
        btn.setAttribute('disabled', 'disabled');
        btn.textContent = <?= json_encode(lang('Store.processing')); ?>;

    });
})();
</script>

<?= $this->endSection(); ?>
