<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div class="d-none justify-content-center align-items-center w-100" id="modal-alert" style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white" style="width:fit-content; border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <div style="padding: 2em;" class="d-flex justify-content-center align-items-center flex-column gap-4">
            <form action="/gudang/actionaddajuan" method="post">
                <div class="mb-2">
                    <div class="form-floating mb-1">
                        <input type="text" class="form-control" id="floatingInput" placeholder="name@example.com" name="nama">
                        <label for="floatingInput">Atas Nama</label>
                    </div>
                    <div class="form-floating mb-1">
                        <input type="text" class="form-control" id="floatingInput1" placeholder="name@example.com" name="kendala">
                        <label for="floatingInput1">Kendala</label>
                    </div>
                    <input type="text" style="display: none;" name="urutan">
                </div>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn-default">Ajukan</button>
                    <button type="button" class="btn-default-merah" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1">
            <h6 class="text-center mb-2">Pesanan Telah Selesai</h6>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Pesanan</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Nama dan varian</div>
                    <div style="flex: 2;">ID Barang</div>
                    <div style="flex: 3;">Action</div>
                </div>
                <?php
                $no = 1;
                foreach ($pesanan as $p) {
                    if ($p['packed']) {
                ?>
                        <div class="isi-table">
                            <div style="flex: 1;"><?= $no; ?></div>
                            <div style="flex: 2;"><?= $p['id_pesanan']; ?></div>
                            <div style="flex: 2;"><?= $p['tanggal']; ?></div>
                            <div style="flex: 3;"><?= $p['nama']; ?></div>
                            <div style="flex: 2;"><?= $p['id_barang']; ?></div>
                            <div style="flex: 3;">
                                <button class="btn-default" onclick="ajukan('<?= $p['id'] ?>')">Ajukan print ulang</button>
                            </div>
                        </div>
                <?php
                        $no++;
                    }
                } ?>
            </div>
        </div>
    </div>
</div>
<script>
    const modalAlertElm = document.getElementById("modal-alert");

    function ajukan(urutan) {
        document.querySelector('input[name="urutan"]').value = urutan;
        modalAlertElm.classList.remove("d-none");
        modalAlertElm.classList.add("d-flex");
    }

    function closeModal() {
        modalAlertElm.classList.add("d-none");
        modalAlertElm.classList.remove("d-flex");
    }
</script>

<?= $this->endSection(); ?>