<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<div class="container d-flex konten gap-5">
    <div style="width: 200px;">
        <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Kategori
        </div>
        <div class="collapse py-2" id="collapseExample">
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="checkbox-filter-1" class="inp-cbx filter" value="kategori-meja" <?= isset($_GET['kategori']) ? (in_array("meja", explode("-", $_GET['kategori'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-1" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Meja</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="checkbox-filter-2" class="inp-cbx filter" value="kategori-lemari" <?= isset($_GET['kategori']) ? (in_array("lemari", explode("-", $_GET['kategori'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-2" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Lemari</p>
                </label>
            </div>
        </div>

        <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="false" aria-controls="collapseExample1">
            Varian
        </div>
        <div class="collapse py-2" id="collapseExample1">
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="checkbox-filter-3" class="inp-cbx filter" value="varian-winge" <?= isset($_GET['varian']) ? (in_array("winge", explode("-", $_GET['varian'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-3" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                        </svg></span>
                    <p>Wingge</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="checkbox-filter-4" class="inp-cbx filter" value="varian-mahoni" <?= isset($_GET['varian']) ? (in_array("mahoni", explode("-", $_GET['varian'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-4" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Mahoni</p>
                </label>
            </div>
        </div>

        <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="false" aria-controls="collapseExample2">
            Harga
        </div>
        <div class="collapse py-2" id="collapseExample2">
            <div class="checkbox-wrapper-46">
                <input type="radio" name="harga" id="checkbox-filter-5" class="inp-cbx filter" value="harga-0" style="display: none;" <?= isset($_GET['harga']) ? (in_array("0", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-5" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                        </svg></span>
                    <p>
                        < Rp 5000.000</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="radio" name="harga" id="checkbox-filter-6" class="inp-cbx filter" value="harga-1" style="display: none;" <?= isset($_GET['harga']) ? (in_array("1", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-6" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Rp 5000.000 - Rp 10.000.000</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="radio" name="harga" id="checkbox-filter-7" class="inp-cbx filter" value="harga-2" style="display: none;" <?= isset($_GET['harga']) ? (in_array("2", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-7" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Rp 10.000.000 - Rp 15.000.000</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="radio" name="harga" id="checkbox-filter-8" class="inp-cbx filter" value="harga-3" style="display: none;" <?= isset($_GET['harga']) ? (in_array("3", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-8" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Rp 15.000.000 - 20.000.000</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="radio" name="harga" id="checkbox-filter-8" class="inp-cbx filter" value="harga-4" style="display: none;" <?= isset($_GET['harga']) ? (in_array("4", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                <label for="checkbox-filter-8" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>> 20.000.000</p>
                </label>
            </div>
        </div>
        <a id="btn-filter" class="mt-2 btn-lonjong">Terapkan</a>
    </div>
    <div class="flex-grow-1">
        <?php if (session()->get('role') == '1') { ?>
            <div class="d-flex justify-content-between">
                <h1 class="teks-sedang">List Product</h1>
                <a href="/addproduct" class="btn-default-merah">Tambah Produk</a>
            </div>
        <?php } ?>
        <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Produk Kami</a></li>
                <li class="breadcrumb-item"><a href="/">Meja</a></li>
                <li class="breadcrumb-item">Meja TV</li>
                </li>
            </ol>
        </nav>
        <?php
        if (isset($_GET['kategori'])) {
            $kategori = explode("-", $_GET['kategori']);
            $produkLama = $produk;
            $produk = [];
            foreach ($kategori as $k) {
                foreach ($produkLama as $p) {
                    if ($k == $p['kategori']) {
                        array_push($produk, $p);
                    }
                }
            }
        }
        if (isset($_GET['varian'])) {
            $varian = explode("-", $_GET['varian']);
            $produkLama = $produk;
            $produk = [];
            foreach ($varian as $k) {
                foreach ($produkLama as $p) {
                    $varianProdukSelected = json_decode($p['varian'], true);
                    foreach ($varianProdukSelected as $vp) {
                        if ($k == $vp['nama']) {
                            if (!in_array($p, $produk))
                                array_push($produk, $p);
                        }
                    }
                }
            }
        }
        if (isset($_GET['harga'])) {
            $produkLama = $produk;
            $produk = [];
            foreach ($produkLama as $p) {
                switch ($_GET['harga']) {
                    case '0':
                        if ((int)$p['harga'] < 5000000)
                            array_push($produk, $p);
                        break;
                    case '1':
                        if (5000000 <= (int)$p['harga'] && (int)$p['harga'] < 10000000) {
                            array_push($produk, $p);
                        }
                        break;
                    case '2':
                        if (10000000 <= (int)$p['harga'] && (int)$p['harga'] < 15000000)
                            array_push($produk, $p);
                        break;
                    case '3':
                        if (15000000 <= (int)$p['harga'] && (int)$p['harga'] < 20000000)
                            array_push($produk, $p);
                        break;
                    default:
                        if (20000000 <= (int)$p['harga'])
                            array_push($produk, $p);
                        break;
                }
            }
        }
        ?>
        <div class="container-card1">
            <?php foreach ($produk as $ind_p => $p) { ?>
                <div class="card1">
                    <div style="position: relative;" onclick="pergiKeProduct('<?= $p['id']; ?>')" class="cursor-pointer">
                        <div class="card1-content-img">
                            <span><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                            <div class="d-flex flex-column gap-2">
                                <?= session()->get('role') == '1' ? '<a class="card1-btn-img" href="/editproduct/' . $p['id'] . '"><i class="material-icons">edit</i></a>' : '' ?>
                                <?= in_array($p['id'], $wishlist) ? '<a class="card1-btn-img" href="/delwishlist/' . $p['id'] . '"><i class="material-icons">bookmark</i></a>' : '<a class="card1-btn-img" href="/addwishlist/' . $p['id'] . '"><i class="material-icons">bookmark_border</i></a>' ?>
                                <a id="card<?= $ind_p ?>" class="card1-btn-img" href="/addcart/<?= $p['id'] ?>/<?= json_decode($p['varian'], true)[0]['nama'] ?>/1"><i class="material-icons">shopping_cart</i></a>
                            </div>
                        </div>
                        <a href="/product/<?= $p['id']; ?>">
                            <img id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt="">
                        </a>
                    </div>
                    <div class="container-varian mb-1">
                        <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                            <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>" value="<?= $v['urutan_gambar'] ?>-<?= $v['nama'] ?>" type="radio" name="varian<?= $ind_p ?>">
                            <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>"><span style="background-color: <?= $v['kode'] ?>"></span></label>
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
<script>
    const btnFilterElm = document.getElementById('btn-filter');
    btnFilterElm.addEventListener('click', () => {
        const filterInputElm = document.querySelectorAll(".filter:checked");
        let arrFilter = [];
        let valueFilter = [];
        filterInputElm.forEach(filterinp => {
            // console.log(filterinp.value);
            if (!arrFilter.includes(filterinp.value.split("-")[0])) {
                arrFilter.push(filterinp.value.split("-")[0]);
                valueFilter.push(filterinp.value.split("-")[1]);
            } else {
                valueFilter[valueFilter.length - 1] += '-' + filterinp.value.split("-")[1]
            }
        });
        console.log(arrFilter)
        console.log(valueFilter)
        let strUrl = '/product?'
        arrFilter.forEach((fil, ind_fil) => {
            strUrl += "&" + fil + "=" + valueFilter[ind_fil]
        })
        console.log(strUrl)
        window.location.href = strUrl
    })

    function pergiKeProduct(id_produk) {
        window.location.href = "/product/" + id_produk
    }
</script>

<?= $this->endSection(); ?>