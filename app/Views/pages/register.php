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
</style>

<div class="container ilena-auth-page ilena-auth-page--register ilena-auth-page--compact">
        <div class="ilena-auth-page__form">
            <p class="ilena-auth-page__eyebrow"><?= lang('Store.startJourney'); ?></p>
            <h1 class="ilena-auth-page__title"><?= lang('Store.createIlenaAccount'); ?></h1>

            <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1" role="alert">
                <?= $val['msg']; ?>
            </div>
            <?php } ?>

            <form id="registerForm" action="<?= esc(ilena_store_path('/actionregister'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="auth_form_nonce" value="<?= esc($authFormNonce ?? '', 'attr'); ?>">
                <!-- Honeypot (TANPA mengganggu backend). Jika bot isi ini, submit akan diblok di JS -->
                <div class="hp-wrap" aria-hidden="true">
                    <label>Website
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </label>
                    <label>Referral
                        <input type="text" name="ref_code" tabindex="-1" autocomplete="off">
                    </label>
                    <!-- token & timestamp non-krusial (front-end only) -->
                    <input type="text" name="abt_token" tabindex="-1" autocomplete="off">
                    <input type="text" name="abt_elapsed" tabindex="-1" autocomplete="off">
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

                <div class="mb-3">
                    <label for="nohp" class="form-label"><?= lang('Store.phoneNumber'); ?></label>
                    <input id="nohp" name="nohp" type="tel" autocomplete="tel" inputmode="numeric" pattern="[0-9]{10,15}" required aria-describedby="nohp-help nohp-feedback" title="<?= esc(lang('Store.phoneDigitsTitle'), 'attr'); ?>" class="form-control <?= ($val['val_nohp']) ? "is-invalid" : ""; ?>"
                        placeholder="<?= esc(lang('Store.enterPhoneNumber'), 'attr'); ?>" value="<?= old('nohp'); ?>">
                    <div class="invalid-feedback" id="nohp-feedback"><?= esc($val['val_nohp'] ?: lang('Store.phoneDigitsError')); ?></div>
                    <div class="form-text" id="nohp-help"><?= lang('Store.phoneDigitsHint'); ?></div>
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
                    <button id="btnSubmit" type="submit" class="btn btn-default btn-block" disabled><?= lang('Store.registerSubmit'); ?></button>
                </div>
                <div id="ab-msg" aria-live="polite"></div>

                <div>
                    <p class="ilena-auth-page__switch"><?= lang('Store.alreadyHaveAccount'); ?>
                        <a href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= lang('Store.signInNow'); ?></a>
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
    const tkn = form.querySelector('input[name="abt_token"]');
    const elps = form.querySelector('input[name="abt_elapsed"]');
    const agreement = document.getElementById('validation-syarat');
    const syaratFeedback = document.getElementById('syarat-feedback');
    const phone = document.getElementById('nohp');
    const email = document.getElementById('email');

    // Covers typing, paste and autofill. The server independently enforces the same policy.
    phone.addEventListener('input', () => {
        phone.value = phone.value.replace(/[^0-9]/g, '');
    });
    email.addEventListener('blur', () => {
        email.value = email.value.trim().toLowerCase();
    });

    const start = Date.now();
    const minWaitMs = 4000;
    let interacted = false;
    let enabled = false;
    tkn.value = Math.random().toString(36).slice(2) + Date.now().toString(36);
    const markInteract = () => {
        interacted = true;
        maybeEnable();
    };
    form.addEventListener('mousemove', markInteract, {
        once: true
    });
    form.addEventListener('touchstart', markInteract, {
        once: true
    });
    form.addEventListener('keydown', markInteract, {
        once: true
    });
    form.addEventListener('scroll', markInteract, {
        once: true
    });

    function maybeEnable() {
        const waited = Date.now() - start;
        const canSubmit = interacted && waited >= minWaitMs && agreement.checked;
        if (canSubmit) {
            btn.removeAttribute('disabled');
            enabled = true;
            showMsg('');
            if (syaratFeedback) syaratFeedback.textContent = '';
        } else {
            btn.setAttribute('disabled', 'disabled');
            enabled = false;
        }
    }
    const iv = setInterval(() => {
        maybeEnable();
        if (enabled) clearInterval(iv);
    }, 250);

    function showMsg(text) {
        if (!text) {
            msg.classList.remove('show');
            msg.textContent = '';
            return;
        }
        msg.textContent = text;
        msg.classList.add('show');
    }
    const throttleKey = 'ab-reg-throttle';
    try {
        const last = Number(localStorage.getItem(throttleKey) || '0');
        if (Date.now() - last < 5000) {
            showMsg(<?= json_encode(lang('Store.waitToRetry')); ?>);
        }
    } catch (e) {}

    agreement.addEventListener('change', () => {
        if (agreement.checked && syaratFeedback) syaratFeedback.textContent = '';
        maybeEnable();
    });

    form.addEventListener('submit', function(e) {
        elps.value = String(Date.now() - start);
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
        if (Date.now() - start < minWaitMs) {
            e.preventDefault();
            showMsg(<?= json_encode(lang('Store.tooFast')); ?>);
            return;
        }
        if (!interacted) {
            e.preventDefault();
            showMsg(<?= json_encode(lang('Store.interactBeforeSubmit')); ?>);
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

        try {
            localStorage.setItem(throttleKey, String(Date.now()));
        } catch (e) {}
    });
})();
</script>

<?= $this->endSection(); ?>
