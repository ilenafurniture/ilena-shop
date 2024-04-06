<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten ">
    <div class="container d-flex justify-content-between mb-4">
        <div>
            <h3>Wishlist (<?= count($wishlist) <= 0 ?'0': count($wishlist) ?>)</h3>
        </div>
        <div>
            <button class="btn-lonjong" type="sumbit">Beli semua</button>
        </div>
    </div>
    <div class="container mb-5">
        <div class="container-card1">
            <?php foreach ($produk as $ind_p => $p) { ?>
            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                        <div class="d-flex flex-column gap-2">
                            <a class="card1-btn-img" href="/addwishlist/<?= $p['id']?>"><i
                                    class="material-icons"><?= in_array($p['id'], $wishlist) ? 'bookmark' : 'bookmark_border' ?></i></a>
                            <a id="card<?= $ind_p ?>" class="card1-btn-img"
                                href="/addcart/<?= $p['id'] ?>/<?= json_decode($p['varian'],true)[0]['nama'] ?>/1"><i
                                    class="material-icons">shopping_cart</i></a>
                        </div>
                    </div>
                    <a href="/product/<?= $p['id']; ?>">
                        <img id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt="">
                    </a>
                </div>
                <div class="container-varian mb-1">
                    <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                    <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>" value="<?= $v['urutan_gambar'] ?>-<?= $v['nama'] ?>"
                        type="radio" name="varian<?= $ind_p ?>">
                    <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>"><span
                            style="background-color: <?= $v['kode'] ?>"></span></label>
                    <?php } ?>
                    <script>
                    const btnKeranjang<?= $ind_p ?>Elm = document.getElementById("card<?= $ind_p ?>");
                    const varian<?= $ind_p ?>Elm = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');
                    varian<?= $ind_p ?>Elm.forEach(elm => {
                        elm.addEventListener('change', (e) => {
                            console.log(e.target.value)
                            const img<?= $ind_p ?>Elm = document.getElementById("img<?= $ind_p ?>");
                            img<?= $ind_p ?>Elm.src =
                                "/viewvar/<?= $p['id']; ?>/" + e.target.value.split("-")[0].split(",")[
                                    0];

                            btnKeranjang<?= $ind_p ?>Elm.href = "/addcart/<?= $p['id'] ?>/" + e.target
                                .value.split("-")[1] + "/1";
                        })
                    });
                    </script>
                </div>
                <h5><?= $p['nama']; ?></h5>
                <div class="d-flex gap-2">
                    <p class="harga">Rp <?= number_format($p['harga'] * (100 - $p['diskon']) / 100, 0, ',', '.'); ?></p>
                    <p class="harga-diskon">Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                    </p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>