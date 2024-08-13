<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container d-flex justify-content-center align-items-center">
    <div class="konten d-flex justify-content-center align-items-center">
        <div class="card p-4 limapuluh-ke-seratus">
            <h1 class="mb-3 teks-sedang">Register Akun</h1>
            <?php if ($val['msg']) { ?>
                <div class="pemberitahuan my-1" role="alert">
                    <?= $val['msg']; ?>
                </div>
            <?php } ?>
            <form id="registerForm" action="/actionregister" method="post">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Nama Lengkap</label>
                    <input name="nama" type="text" class="form-control <?= ($val['val_nama']) ? "is-invalid" : ""; ?>"
                        placeholder="Masukkan Nama Lengkap" value="<?= old('nama'); ?>">
                    <div class="invalid-feedback">Mohon masukkan nama lengkap.</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input name="email" type="email"
                        class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>"
                        placeholder="Masukkan Email" value="<?= old('email'); ?>">
                    <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                </div>
                <div class="mb-3">
                    <label for="nohp" class="form-label">Nomor Telepon</label>
                    <input name="nohp" type="text" class="form-control <?= ($val['val_nohp']) ? "is-invalid" : ""; ?>"
                        placeholder="Masukkan Nomor Telepon" value="<?= old('nohp'); ?>">
                    <div class="invalid-feedback">Mohon masukkan nomor telepon yang valid.</div>
                </div>
                <label for="sandi" class="form-label">Kata Sandi</label>
                <div class="input-group mb-3 has-validation">
                    <input name="sandi" type="password"
                        class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" id="password"
                        placeholder="Masukkan Kata Sandi">
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
                <div class="mb-3 d-flex w-100 justify-content-center">
                    <button type="submit" class="btn btn-default btn-block">Register</button>
                </div>
                <div>
                    <p class="text-center">Sudah punya akun? <a href="/login" class="btn-teks-aja"
                            style="display: inline;">Masuk sekarang</a></p>
                </div>
            </form>
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