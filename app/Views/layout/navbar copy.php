<nav class="nav show-block-ke-hide" style="background-color: black;">
    <div class="container py-3">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <div style="width: calc(100% / 3)">
                <form action="/actionfind" method="post">
                    <input placeholder="Cari produk" style="text-transform: capitalize;" class="input" name="cari"
                        type="text">
                </form>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-center">
                <a href="/">
                    <img src="<?php echo base_url('/img/Logo Putih Ilena 1.png'); ?>" alt="logo ilena" height="30em">
                </a>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-end">
                <?php if (session()->get('isLogin')) { ?>
                <?php if (session()->get('role') == '0' || session()->get('role') == '4') { ?>
                <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                <a href="/account" class="btn"><i class="material-icons text-light">person_outline</i></a>
                <?php } else if (session()->get('role') == '1') { ?>
                <a href="/admin/product" class="btn d-flex align-items-center">
                    <i class="material-icons text-light">chevron_left</i>
                    <p class="m-0 text-light">Admin</p>
                </a>
                <a href="/logout" class="btn" style="padding-right: 0"><i
                        class="material-icons text-light">exit_to_app</i></a>
                <?php } else if (session()->get('role') == '2') { ?>
                <a href="/gudang/listorder" class="btn d-flex align-items-center">
                    <i class="material-icons text-light">chevron_left</i>
                    <p class="m-0 text-light">Gudang</p>
                </a>
                <a href="/logout" class="btn" style="padding-right: 0"><i
                        class="material-icons text-light">exit_to_app</i></a>
                <?php } else if (session()->get('role') == '3') { ?>
                <a href="/market/product" class="btn d-flex align-items-center">
                    <i class="material-icons text-light">chevron_left</i>
                    <p class="m-0 text-light">Marketplace</p>
                </a>
                <a href="/logout" class="btn" style="padding-right: 0"><i
                        class="material-icons text-light">exit_to_app</i></a>
                <?php } ?>
                <?php } else { ?>
                <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                <a href="/login" class="btn"><i class="material-icons text-light">person_outline</i></a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>
<div style="background-color: #474747;" class="py-1 show-block-ke-hide">
    <p class="m-0 text-center" style="color: white;">Lebih hemat dengan Free Ongkir hingga 100%</p>
</div>
<style>
.container-navbar-list-nav {
    background-color: whitesmoke;
    position: sticky;
    top: -1px;
    z-index: 99;
    justify-content: center;
}

@media (max-width: 1146px) {
    .container-navbar-list-nav {
        justify-content: start;
        padding-inline: 2em;
        overflow-x: scroll;
    }
}
</style>
<div class="w-100 show-flex-ke-hide container-navbar-list-nav">
    <div class="d-flex align-items-center py-2 gap-5">
        <?php foreach ($navbar as $ind_n => $n) { ?>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=<?= str_replace(' ', '-', $ind_n) ?>"><?= ucwords($ind_n) ?></a>
            <div class="child-list-nav" style="overflow: hidden;">
                <div class="container d-flex align-items-start py-4">
                    <div style="flex: 1; display: grid; grid-template-columns: repeat(3, 1fr); row-gap: 1em;">
                        <?php if($ind_n == 'dresser' || $ind_n == 'bed') { ?>
                        <?php foreach ($n as $ind_n_k => $n_k) { ?>
                        <div>
                            <p class="m-0" style="font-size:14px;">Jelajahi <?= ucwords($ind_n_k) ?></p>
                            <div class="ms-2">
                                <?php foreach ($n_k as $koleksi) { ?>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=<?= str_replace(' ', '-', $koleksi['koleksi']) ?>&jenis=<?= str_replace(' ', '-', $ind_n_k) ?>"
                                    onmouseover="hoverListNav('<?= str_replace(' ', '', $ind_n) ?><?= array_search($ind_n_k,array_keys($n), true) + 1 ?>-<?= str_replace(' ', '', $koleksi['koleksi']) ?>')"><?= ucwords($koleksi['koleksi']) ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <div>
                            <p class="m-0" style="font-size:14px;">Jelajahi <?= ucwords($ind_n) ?></p>
                            <div class="ms-2">
                                <?php foreach ($n as $koleksi) { ?>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=<?= str_replace(' ', '-', $koleksi['koleksi']) ?>&jenis=<?= str_replace(' ', '-', $ind_n) ?>"
                                    onmouseover="hoverListNav('<?= str_replace(' ', '', $ind_n) ?>1-<?= str_replace(' ', '', $koleksi['koleksi']) ?>')"><?= ucwords($koleksi['koleksi']) ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div style="flex:1;">
                        <?php if($ind_n == 'dresser' || $ind_n == 'bed') { ?>
                        <?php foreach ($n as $ind_n_k => $n_k) { ?>
                        <?php foreach ($n_k as $ind_k => $koleksi) { ?>
                        <div class="d-<?= $ind_k == 0 && (array_search($ind_n_k,array_keys($n), true) == 0) ? 'flex' : 'none' ?> gap-4 mb-3 <?= str_replace(' ', '', $ind_n) ?>-penjelasan"
                            id="<?= str_replace(' ', '', $ind_n) ?><?= array_search($ind_n_k,array_keys($n), true) + 1 ?>-<?= str_replace(' ', '', $koleksi['koleksi']) ?>">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="<?= base_url('img/barang/300/'.$koleksi['id']) ?>.webp"
                                    alt="<?= $ind_n ?> <?= $koleksi['koleksi'] ?>">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3"><?= ucwords($koleksi['koleksi']) ?></h3>
                                <?= str_replace("<p>", '<p style="text-align: justify" class="mb-2">', json_decode($koleksi['deskripsi'], true)['deskripsi']) ?>
                                <a href="<?= base_url('/product/' . str_replace(' ', '-', strtolower($koleksi['nama']))) ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        <?php } else { ?>
                        <?php foreach ($n as $ind_k => $koleksi) { ?>
                            <div class="d-<?= $ind_k == 0 ? 'flex' : 'none' ?> gap-4 mb-3 <?= str_replace(' ', '', $ind_n) ?>-penjelasan"
                                id="<?= str_replace(' ', '', $ind_n) ?>1-<?= str_replace(' ', '', $koleksi['koleksi']) ?>">
                                <div style="flex: 1;">
                                    <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                        src="<?= base_url('img/barang/300/'.$koleksi['id']) ?>.webp"
                                        alt="<?= $ind_n ?> <?= $koleksi['koleksi'] ?>">
                                </div>
                                <div style="flex: 1;">
                                    <h3 class="teks-sedang mb-3"><?= ucwords($koleksi['koleksi']) ?></h3>
                                    <?= str_replace("<p>", '<p style="text-align: justify" class="mb-2">', json_decode($koleksi['deskripsi'], true)['deskripsi']) ?>
                                    <a href="<?= base_url('/product/' . str_replace(' ', '-', strtolower($koleksi['nama']))) ?>"
                                        style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                        selengkapnya..</a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php } ?>

                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan
                            logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan
                            kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan nama
                            Ilena.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="bg-list-nav"></div>
    </div>
</div>
<script>
function hoverListNav(idElm) {
    const arrPenjelasanElm = document.querySelectorAll('.' + idElm.split('-')[0].slice(0, -1) + '-penjelasan');
    // console.log(arrPenjelasanElm)
    arrPenjelasanElm.forEach(penjelasanElm => {
        penjelasanElm.classList.add('d-none')
        penjelasanElm.classList.remove('d-flex')
    });
    // console.log(idElm)
    const itemElm = document.getElementById(idElm)
    itemElm.classList.remove('d-none')
    itemElm.classList.add('d-flex')
}
</script>