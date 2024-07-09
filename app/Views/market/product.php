<?= $this->extend("market/layout/template"); ?>
<?= $this->section("content"); ?>
<?php
$hitungPag = ceil(count($produk) / 10);
$pag = 1;
if (isset($_GET['pag'])) $pag = (int)$_GET['pag'];
$produkLama = array_slice($produk, ($pag - 1) * 10);
$produk = [];
for ($i = 0; $i < 10; $i++) {
    if (isset($produkLama[$i]))
        array_push($produk, $produkLama[$i]);
}
?>
<div style="padding: 2em;">
    <div style="flex:1;">
        <div class="d-flex justify-content-between">
            <h1 class="teks-sedang mb-2">List Produk</h1>
            <form action="/market/actionfind" method="post">
                <input placeholder="Cari produk" style="text-transform: capitalize;" class="input text-dark" name="cari" type="text">
            </form>
        </div>
        <div class="container-table">
            <div class="header-table">
                <div style="flex: 1;">Gambar Produk</div>
                <div style="flex: 2;">Nama Produk</div>
                <div style="flex: 2;">Varian Produk</div>
                <div style="flex: 2;">Harga Produk</div>
                <div style="flex: 1;">Action</div>
            </div>
            <?php foreach ($produk as $ind_p => $p) { ?>
                <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                    <div class="isi-table">
                        <div style="flex: 1;" onclick="pergiKeProduct('')"><img style="width: 50px; height: 50px; object-fit:cover;" src="/viewvar/<?= $p['id'] ?>/<?= $ind_v + 1 ?>" alt="">
                        </div>
                        <div style="flex: 2;" class="d-flex flex-column align-items-start justify-content-center" onclick="pergiKeProduct('')">
                            <p class="fw-bold m-0" style="font-size: 20px;"><?= $p['nama']; ?></p>
                            <p class="m-0" style="color: grey; font-size: 13px;"><?= $p['id'] ?></p>
                        </div>
                        <div style="flex: 2;"><?= $v['nama'] ?></div>
                        <div style="flex: 2;">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                        <div style="flex: 1;">
                            <?php
                            $ketemu = false;
                            foreach ($keranjang as $k) { ?>
                                <?php if ($p['id'] == $k['id_barang'] && $v['nama'] == $k['varian']) { ?>
                                    <div style="flex: 1;">
                                        <div style="display:flex; border: 1px solid grey; border-radius: 0.5em;">
                                            <a style="width: 30px; height: 30px;" class="number-left" href="/market/reducecart/<?= $k['id'] ?>"></a>
                                            <input type="number" style="height: 30px;" name="jumlah" class="number-quantity" disabled value="<?= $k['jumlah'] ?>">
                                            <a style="width: 30px; height: 30px;" class="number-right" href="/market/addcart/<?= $k['id_barang'] ?>/<?= $k['varian'] ?>"></a>
                                        </div>
                                    </div>
                                <?php
                                    $ketemu = true;
                                }
                            }
                            if (!$ketemu) {  ?>
                                <a class="btn-default-merah" href="/market/addcart/<?= $p['id'] ?>/<?= $v['nama'] ?>">Beli</a>
                            <?php } ?>

                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="container-pag">
            <?php if ($pag > 1) { ?>
                <a class="item-pag" href="/market/product?pag=<?= $pag - 1; ?>"><i class="material-icons">chevron_left</i></a>
            <?php } ?>
            <?php for ($i = 0; $i < $hitungPag; $i++) { ?>
                <a class="item-pag <?= $pag == ($i + 1) ? 'active' : ''; ?>" href="/market/product?pag=<?= $i + 1; ?>"><?= $i + 1; ?></a>
            <?php } ?>
            <?php if ($pag < $hitungPag) { ?>
                <a class="item-pag" href="/market/product?pag=<?= $pag + 1; ?>"><i class="material-icons">chevron_right</i></a>
            <?php } ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>