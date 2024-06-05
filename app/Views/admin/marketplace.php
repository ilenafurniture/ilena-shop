<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">List Konfirmasi Ulang</h1>
        </div>
    </div>
    <div class="container-table mt-4">
        <div class="header-table">
            <div style="flex: 1;">No</div>
            <div style="flex: 2;">Id Marketplace</div>
            <div style="flex: 2;">Produk yang dibeli</div>
            <div style="flex: 2;">Alamat Pembeli</div>
            <div style="flex: 2;">Jumlah produk</div>
            <div style="flex: 2;">Action</div>
        </div>

        <div class="isi-table">
            <div style="flex: 1;">1</div>
            <div style="flex: 2;">SILN0001</div>
            <div style="flex: 2;">ALD 30056 - Wange</div>
            <div style="flex: 2;">Jakarta,indonesia</div>
            <div style="flex: 2;">1</div>
            <div style="flex: 2;" class="gap-2">
                <a class="btn-default" href="/editproduct/" <i class=" material-icons">Konfirmasi</i></a>
                <a class="btn-default-merah" href="/editproduct" /<i class=" material-icons">Tolak</i></a>
            </div>
        </div>

        <div class="isi-table">
            <div style="flex: 1;">2</div>
            <div style="flex: 2;">TILN0001</div>
            <div style="flex: 2;">MTV 100 - Putih</div>
            <div style="flex: 2;">bali,indonesia</div>
            <div style="flex: 2;">1</div>
            <div style="flex: 2; color:green;">Selesai</div>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>