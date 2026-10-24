<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten ilena-auth-page ilena-auth-page--login">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 ilena-auth-page__form">
                <p class="ilena-auth-page__eyebrow">Selamat datang kembali</p>
                <h1 class="mb-3 teks-sedang">Masuk ke akun Anda</h1>
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
                        <input id="email" name="email" type="email" class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>" placeholder="Masukkan Email" value="<?= old('email'); ?>">
                        <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                    </div>
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group mb-3 has-validation mb-3">
                        <input id="password" name="sandi" type="password" class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" placeholder="Masukkan Kata Sandi">
                        <span class="input-group-text d-flex justify-content-center align-items-center" onclick="togglePassword(event)">
                            <i class="material-icons" style="cursor: default; -webkit-user-select: none; -ms-user-select: none; user-select: none;">remove_red_eye</i>
                        </span>
                        <div class="invalid-feedback">Mohon masukkan kata sandi.</div>
                    </div>
                    <!-- <div class="mb-3">
                        <input type="checkbox" onclick="togglePassword()" id="showPassword">
                        <label for="showPassword" class="form-label" style="font-size:small;">Tampilkan Kata
                            Sandi</label>
                    </div> -->
                    <?= view('partials/auth_recaptcha', [], ['saveData' => false]); ?>
                    <div class="mb-3 d-flex w-100 justify-content-center">
                        <button type="submit" class="btn btn-default btn-block">Login</button>
                    </div>
                    <div>
                        <p class="text-center">Belum punya akun? <a href="/register" class="btn-teks-aja" style="display: inline;">Buat sekarang</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function togglePassword(e) {
        var passwordField = document.querySelector('input[name="sandi"]');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            e.target.style.color = 'var(--merah)'
        } else {
            passwordField.type = "password";
            e.target.style.color = 'black'
        }
    }
</script>
<?= $this->endSection(); ?>
