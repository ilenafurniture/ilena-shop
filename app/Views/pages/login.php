<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container">
    <div class="row justify-content-center m-5">
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="mb-4">Login Akun</h5>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Masukkan Email">
                        <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" placeholder="Masukkan Kata Sandi">
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
                        <p class="text-center">Belum punya akun? <a href="/register">Buat sekarang</a></p>
                    </div>
                </form>
                <div class="text-center mb-1">
                    <hr class="my-4">
                    <!-- <span class="fw-small">Atau</span> -->
                </div>
                <div class="mb-2 d-flex w-100 justify-content-center">
                    <button type="button" class="btn btn-default btn-block ">Login sebagai
                        Tamu</button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}

document.getElementById("loginForm").addEventListener("submit", function(event) {
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;

    if (!email || !validateEmail(email)) {
        document.getElementById("email").classList.add("is-invalid");
        event.preventDefault();
    } else {
        document.getElementById("email").classList.remove("is-invalid");
    }

    if (!password) {
        document.getElementById("password").classList.add("is-invalid");
        event.preventDefault();
    } else {
        document.getElementById("password").classList.remove("is-invalid");
    }
});

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}
</script>
<?= $this->endSection(); ?>