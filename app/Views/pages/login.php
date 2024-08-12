<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten">
    <div class="row justify-content-center">
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
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group mb-3 has-validation mb-3">
                        <input name="sandi" type="password" class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" placeholder="Masukkan Kata Sandi">
                        <span class="input-group-text d-flex justify-content-center align-items-center" onclick="togglePassword(event)">
                            <i class="material-icons">remove_red_eye</i>
                        </span>
                        <div class="invalid-feedback">Mohon masukkan kata sandi.</div>
                    </div>
                    <!-- <div class="mb-3">
                        <input type="checkbox" onclick="togglePassword()" id="showPassword">
                        <label for="showPassword" class="form-label" style="font-size:small;">Tampilkan Kata
                            Sandi</label>
                    </div> -->
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