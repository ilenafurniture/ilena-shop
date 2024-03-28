<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten">
    <div>
        <div>
            <h1 class="teks-besar">Keranjang</h1>
            <p class="teks-sedang">2 barang</p>
        </div>
        <div class="container-keranjang">
            <div class="item-keranjang">
                <div>
                    <img src="/img/contoh.webp" alt="">
                </div>
                <div>
                    <h1>Nama Barang</h1>
                    <p>Varian</p>
                    <div class="d-flex gap-2">
                        <div class="number-control">
                            <div class="number-left"></div>
                            <input type="number" name="number" class="number-quantity" value="1">
                            <div class="number-right"></div>
                        </div>
                        <a href="#" class="btn-teks-aja">Hapus</a>
                    </div>
                </div>
                <div>
                    <h3>Rp 500,000.00</h3>
                </div>
            </div>
        </div>
    </div>
    <div></div>

</div>


<?= $this->endSection(); ?>