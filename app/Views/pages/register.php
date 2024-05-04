<!-- register.php -->

<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container">
    <div class="row justify-content-center m-5">
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="mb-4">Register Akun</h5>
                <form id="registerForm">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="fullname" placeholder="Masukkan Nama Lengkap">
                        <div class="invalid-feedback">Mohon masukkan nama lengkap.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Masukkan Email">
                        <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" placeholder="Masukkan Nomor Telepon">
                        <div class="invalid-feedback">Mohon masukkan nomor telepon yang valid.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" placeholder="Masukkan Kata Sandi">
                        <div class="invalid-feedback">Mohon masukkan kata sandi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="password" class="form-control" id="confirmPassword"
                            placeholder="Konfirmasi Kata Sandi">
                        <div class="invalid-feedback">Mohon konfirmasi kata sandi.</div>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" onclick="togglePassword()" id="showPassword">
                        <label for="showPassword" class="form-label" style="font-size:small;">Tampilkan Kata
                            Sandi</label>
                    </div>
                    <div class="mb-3 d-flex w-100 justify-content-center">
                        <button type="submit" class="btn btn-default btn-block">Register</button>
                    </div>
                    <div>
                        <p class="text-center">Sudah punya akun? <a href="/login">Masuk sekarang</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    var confirmPasswordField = document.getElementById("confirmPassword");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        confirmPasswordField.type = "text";
    } else {
        passwordField.type = "password";
        confirmPasswordField.type = "password";
    }
}

document.getElementById("registerForm").addEventListener("submit", function(event) {
    var fullname = document.getElementById("fullname").value;
    var email = document.getElementById("email").value;
    var phone = document.getElementById("phone").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;

    if (!fullname || !email || !phone || !password || !confirmPassword) {
        event.preventDefault();
        alert("Mohon isi semua bidang.");
        return;
    }

    if (!validateEmail(email)) {
        document.getElementById("email").classList.add("is-invalid");
        event.preventDefault();
        return;
    } else {
        document.getElementById("email").classList.remove("is-invalid");
    }

    if (!validatePhone(phone)) {
        document.getElementById("phone").classList.add("is-invalid");
        event.preventDefault();
        return;
    } else {
        document.getElementById("phone").classList.remove("is-invalid");
    }

    if (password !== confirmPassword) {
        document.getElementById("confirmPassword").classList.add("is-invalid");
        event.preventDefault();
        return;
    } else {
        document.getElementById("confirmPassword").classList.remove("is-invalid");
    }
});

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^\d{10,12}$/;
    return re.test(phone);
}
</script>
<?= $this->endSection(); ?>