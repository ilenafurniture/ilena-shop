<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container ilena-auth-page ilena-auth-page--login ilena-auth-page--compact">
            <div class="ilena-auth-page__form">
                <p class="ilena-auth-page__eyebrow">Selamat datang kembali</p>
                <h1 class="ilena-auth-page__title">Masuk ke akun Anda</h1>
                <?php if ($val['msg']) { ?>
                    <div class="pemberitahuan my-1" role="alert">
                        <?= $val['msg']; ?>
                    </div>
                <?php } ?>
                <form id="loginForm" action="/actionlogin" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="auth_form_nonce" value="<?= esc($authFormNonce ?? '', 'attr'); ?>">
                    <div class="hp-wrap" aria-hidden="true" style="position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);">
                        <label>Jangan isi kolom ini<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" autocomplete="username" class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>" placeholder="Masukkan email" value="<?= old('email'); ?>">
                        <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                    </div>
                    <label for="password" class="form-label">Kata sandi</label>
                    <div class="input-group mb-3 has-validation">
                        <input id="password" name="sandi" type="password" autocomplete="current-password" class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" placeholder="Masukkan kata sandi">
                        <button type="button" class="input-group-text ilena-auth-page__password-toggle" aria-label="Tampilkan kata sandi" aria-controls="password" aria-pressed="false" onclick="togglePassword(event)">
                            <i class="material-icons" aria-hidden="true">visibility</i>
                        </button>
                        <div class="invalid-feedback">Mohon masukkan kata sandi.</div>
                    </div>
                    <!-- <div class="mb-3">
                        <input type="checkbox" onclick="togglePassword()" id="showPassword">
                        <label for="showPassword" class="form-label" style="font-size:small;">Tampilkan Kata
                            Sandi</label>
                    </div> -->
                    <?= view('partials/auth_recaptcha', ['error' => $captchaError ?? null], ['saveData' => false]); ?>
                    <div class="mb-3 d-flex w-100 justify-content-center">
                        <button type="submit" class="btn btn-default btn-block">Masuk</button>
                    </div>
                    <div>
                        <p class="ilena-auth-page__switch">Belum punya akun? <a href="/register">Daftar sekarang</a></p>
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
        e.currentTarget.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        e.currentTarget.querySelector('i').textContent = show ? 'visibility_off' : 'visibility';
    }
</script>
<?= $this->endSection(); ?>
