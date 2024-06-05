<?= $this->extend("market/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1;">
            <h6 class="text-center mb-2">List Produk</h6>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Produk</div>
                    <div style="flex: 2;">Nama Produk</div>
                    <div style="flex: 2;">Varian Produk</div>
                    <div style="flex: 3;">Action</div>
                </div>

                <div class="isi-table">
                    <div style="flex: 1;">1</div>
                    <div style="flex: 2;">MTR00021</div>
                    <div style="flex: 2;">MTV 100</div>
                    <div style="flex: 2;">Putih</div>
                    <div style="flex: 3;" class="gap-2">
                        <a class="btn-default" href="/editproduct/" <i class=" material-icons">Konfirmasi</i></a>
                        <a class="btn-default-merah" href="/editproduct" /<i class=" material-icons">Tolak</i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>