<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="container-table">
        <div class="header-table">
            <div style="flex: 1;">Gambar</div>
            <div style="flex: 2;">Nama Produk</div>
            <div style="flex: 2;">ID Produk</div>
            <div style="flex: 1;">Jumlah Stock</div>
        </div>

        <div class="isi-table">
            <div style="flex: 1;"><img style="width:50px; height: 50px; object-fit:cover;" id="img"
                    src="../img/contoh.webp" alt="">
            </div>
            <div style="flex: 2;">ALD Jati Rasa</div>
            <div style="flex: 2;">1273522</div>
            <div style="flex: 1;">
                <div style="display:flex; border: 1px solid grey; border-radius: 0.5em;">
                    <div style="width: 30px; height: 30px;" class="number-left" onclick="kurangJumlah()"></div>
                    <input type="number" style="height: 30px;" name="jumlah" class="number-quantity" value="1">
                    <div style="width: 30px; height: 30px;" class="number-right" onclick="tambahJumlah()"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>