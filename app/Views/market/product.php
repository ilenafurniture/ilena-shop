<?= $this->extend("market/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1;">
            <h6 class="text-center mb-2">List Produk</h6>
            <div class="container-table">
                <div class="header-table">
                    <div style="flex: 1;">Gambar Produk</div>
                    <div style="flex: 2;">Nama Produk</div>
                    <div style="flex: 2;">Varian Produk</div>
                    <div style="flex: 2;">Harga Produk</div>
                    <div style="flex: 1;">Action</div>
                </div>
                <?php foreach ($produk as $ind_p => $p) { ?>
                <?php foreach (json_decode($p['varian'],true) as $ind_v => $v ) { ?>
                <div class="isi-table">
                    <div style="flex: 1;" onclick="pergiKeProduct('')"><img
                            style="width: 50px; height: 50px; object-fit:cover;"
                            src="/viewvar/<?= $p['id'] ?>/<?= $ind_v +1 ?>" alt="">
                    </div>
                    <div style="flex: 2;" class="d-flex flex-column align-items-start justify-content-center"
                        onclick="pergiKeProduct('')">
                        <p class="fw-bold m-0" style="font-size: 20px;"><?= $p['nama']; ?></p>
                        <p class="m-0" style="color: grey; font-size: 13px;"><?= $p['id']?></p>
                    </div>
                    <div style="flex: 2;"><?= $v['nama']?></div>
                    <div style="flex: 2;">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                    <div style="flex: 1;">
                        <a class="btn-default-merah" href="/market/addcart/<?= $p['id'] ?>/<?= $v['nama'] ?>">Beli</a>
                        <div style="flex: 1;">
                            <div style="display:flex; border: 1px solid grey; border-radius: 0.5em;">
                                <div style="width: 30px; height: 30px;" class="number-left" onclick="kurangJumlah()">
                                </div>
                                <input type="number" style="height: 30px;" name="jumlah" class="number-quantity"
                                    value="1">
                                <div style="width: 30px; height: 30px;" class="number-right" onclick="tambahJumlah()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>