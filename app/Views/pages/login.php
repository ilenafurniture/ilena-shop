<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div style="flex:1">
    <form action=" post" class="form-custom">
        <div class="d-flex justify-content-center">
            <div style="flex:1">
                <h1>test</h1>
            </div>
            <div style="flex:1.5; padding: 5em;">
                <h5 class="mb-4">Create Accounts</h5>
                <div class="d-flex border-bottom gap-2 align-items-center mb-3">
                    <i class="material-icons ">person_outline</i>
                    <input type="email" placeholder="Masukan Email">
                </div>
                <div class=" d-flex border-bottom gap-2 align-items-center mb-3">
                    <i class="material-icons ">lock</i>
                    <input type="password" placeholder="Masukan password" id="passworku">
                </div>
                <div>
                    <input type="checkbox" onclick="liatPassword()" id="showw">
                    <label for="showw" style="font-size:small;" class="text-no-select">Show Password</label>
                </div>
                <div>
                    <button class="btn-default">Membuat Akun</button>
                </div>
                <div>
                    <p>Sudah mempunyai akun?<a href="">Masuk Sekarang</a></p>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
function liatPassword() {
    var x = document.getElementById("passworku");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>
<?= $this->endSection(); ?>