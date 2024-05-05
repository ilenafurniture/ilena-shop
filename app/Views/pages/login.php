<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container">
    <div class="row justify-content-center m-5">
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="mb-3 teks-sedang">Login Akun</h5>
                <?php if ($val['msg']) { ?>
                    <div class="pemberitahuan my-1" role="alert">
                        <?= $val['msg']; ?>
                    </div>
                <?php } ?>
                <form id="loginForm" action="/actionlogin" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>" placeholder="Masukkan Email" value="<?= old('email'); ?>">
                        <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input name="sandi" type="password" class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" placeholder="Masukkan Kata Sandi">
                        <div class="invalid-feedback">Mohon masukkan kata sandi.</div>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" onclick="togglePassword()" id="showPassword">
                        <label for="showPassword" class="form-label" style="font-size:small;">Tampilkan Kata
                            Sandi</label>
                    </div>
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
    function togglePassword() {
        var passwordField = document.querySelector('input[name="sandi"]');
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
</script>
<?= $this->endSection(); ?>