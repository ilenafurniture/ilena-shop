<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <div class="mb-4">
            <h1 class="teks-sedang">Keranjang</h1>
            <p style="color: grey;">2 barang</p>
        </div>
        <div class="container-keranjang">
            <?php foreach ($keranjang as $index => $k) { ?>
            <div class="item-keranjang">
                <div style="width: 150px;">
                    <img src="<?= $k['src_gambar'] ?>" alt="">
                </div>
                <div style="flex: 1;" class="d-flex flex-column">
                    <h1 class="nama-barang"><?= $k['detail']['nama'] ?></h1>
                    <p class="my-2">Varian: <?= ucfirst($k['varian']) ?></p>
                    <div class="d-flex align-items-end flex-grow-1">
                        <div class="d-flex align-items-center gap-4">
                            <div class="number-control">
                                <div class="number-left"></div>
                                <input type="number" name="number" class="number-quantity" value="<?= $k['jumlah'] ?>">
                                <div class="number-right"></div>
                            </div>
                            <a href="#" class="btn-teks-aja m-0">Hapus</a>
                        </div>
                    </div>
                </div>
                <div style="width: 150px;">
                    <p style="font-weight:bold; font-size: 18px; letter-spacing: -1px; text-align: right;">
                        Rp <?= number_format($k['detail']['harga'], 0, ',', '.'); ?>
                    </p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h4 style="letter-spacing: -1px">Pesanan</h4>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    harga
                </p>
                <p class="fw-bold m-0">
                    Rp 5,000.000
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Biaya Admin
                </p>
                <p class="fw-bold m-0">
                    Rp 5,000
                </p>
            </div>
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp 5,500.000
                </p>
            </div>
            <a href="/address" class="btn-default-merah w-100 mt-4 text-center">Checkout</a>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>