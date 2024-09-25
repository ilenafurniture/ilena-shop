<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container d-flex flex-column align-items-center">
    <div class="konten w-100">
        <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb" class="show-block-ke-hide">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/product">Produk Kami</a></li>
                <li class="breadcrumb-item"><a
                        href="/product?koleksi=<?= ucfirst($produk['kategori']); ?>"><?= ucfirst($produk['kategori']); ?></a>
                </li>
                <li class="breadcrumb-item"><a
                        href="/product?jenis=<?= ucfirst($produk['subkategori']); ?>"><?= ucfirst($produk['subkategori']); ?></a>
                </li>
                </li>
            </ol>
        </nav>
        <div class="baris-ke-kolom-reverse w-100">
            <div class="limapuluh-ke-seratus">
                <h1 class="teks-besar mb-2"><?= str_replace('Tv', 'TV', ucwords($produk['nama'])) ?></h1>
                <div class="d-flex gap-2 mb-3">
                    <p class="harga">Rp
                        <?= number_format($produk['harga'] * (100 - $produk['diskon']) / 100, 0, ',', '.'); ?></p>
                    <?php if ($produk['diskon'] > 0) { ?>
                        <p class="harga-diskon">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                    <?php } ?>
                </div>
                <p><?= $produk['deskripsi']['deskripsi'] ?></p>
                <div class="gap-2 show-flex-ke-hide">
                    <div class="container-varian mb-3 d-flex">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                            <input id="varian<?= $ind_v ?>"
                                value="<?= $v['urutan_gambar'] ?>-<?= $v['nama'] ?>-<?= $ind_v ?>" type="radio"
                                name="varian">
                            <label for="varian<?= $ind_v ?>"><span
                                    style="background-color: <?= $v['kode'] ?>"></span></label>
                        <?php } ?>
                    </div>
                    <div class="d-flex gap-1 mb-2">
                        <?php foreach ($produkSemua as $ind_ps => $ps) { ?>
                            <a href="/product/<?= strtolower(str_replace(' ', '-', $ps['nama'])) ?>/<?= $ind_ps ?>"
                                class="btn-default-abu <?= $ind_ps == $indexNama ? 'border border-dark' : '' ?>"><?= json_decode($ps['deskripsi'], true)['dimensi']['asli']['panjang'] ?>
                                mm</a>
                        <?php } ?>
                    </div>
                </div>
                <?php if (session()->get('role') == '1') { ?>
                    <div class="d-flex gap-2">
                        <a class="btn-default d-flex justify-content-center align-items-center"
                            href="/admin/editproduct/<?= $produk['id']; ?>">
                            <i class="material-icons">edit</i>
                            <p class="m-0">Edit</p>
                        </a>
                        <a class="btn-default-merah d-flex justify-content-center align-items-center"
                            href="/admin/deleteproduct/<?= $produk['id']; ?>">
                            <i class="material-icons">delete</i>
                            <p class="m-0">Hapus</p>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="d-flex gap-2 align-items-stretch">
                        <div class="number-control">
                            <div class="number-left" onclick="kurangJumlah()"></div>
                            <input type="number" name="jumlah" class="number-quantity" value="1">
                            <div class="number-right" onclick="tambahJumlah()"></div>
                        </div>
                        <a id="btn-keranjang"
                            href="<?= $produk['varian'][0]['stok'] > 0 ? '/addcart/' . $produk['id'] . '/' . $produk['varian'][0]['nama'] . '/1' : ''; ?>"
                            class="btn-default-merah <?= $produk['varian'][0]['stok'] > 0 ? '' : 'disabled'; ?>"><?= $produk['varian'][0]['stok'] > 0 ? 'Keranjang' : 'Stok habis'; ?></a>
                    </div>
                <?php } ?>
                <p id="info-habis" class="mt-2 <?= $produk['varian'][0]['stok'] <= 0 ? '' : 'd-none'; ?>"
                    style="font-size: 10px;">*Produk ini bisa di pre-order dengan menghubungi <a href=""
                        class="btn-teks-aja" style="display: inline;">Customer Service</a> kami</p>
                <?php if ($produk['tokped'] || $produk['shopee'] || $produk['tiktok']) { ?>
                    <div class="mt-4 <?= $produk['varian'][0]['stok'] <= 0 ? 'd-none' : ''; ?>" id="container-market">
                        <p class="mb-2">
                            Produk ini juga tersedia di
                        </p>
                        <div>
                            <?php if ($produk['tokped']) { ?>
                                <a href="<?= $produk['tokped']; ?>" title="Tokopedia" target="_blank"><img
                                        src="/img/logo/tokped_logo.webp" class="marketplace"></a>
                            <?php } ?>
                            <?php if ($produk['shopee']) { ?>
                                <a href="<?= $produk['shopee']; ?>" title="Shopee" target="_blank"><img
                                        src="/img/logo/shopee_logo.webp" class="marketplace"></a>
                            <?php } ?>
                            <?php if ($produk['tiktok']) { ?>
                                <a href="<?= $produk['tiktok']; ?>" title="Tiktok" target="_blank"><img
                                        src="/img/logo/tiktokshop.svg" class="marketplace"></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?= in_array($produk['id'], $wishlist) ? '<a class="btn-teks-aja my-3" href="/delwishlist/' . $produk['id'] . '"><i class="material-icons">bookmark</i> Buang</a>' : '<a class="btn-teks-aja my-3" href="/addwishlist/' . $produk['id'] . '"><i class="material-icons">bookmark_border</i> Simpan</a>' ?>
                <hr>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                                SPESIFIKASI PRODUK
                            </button>
                        </h2>
                        <div id="flush-collapse2" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p class="mb-0">
                                    <?= $produk['deskripsi']['perawatan'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse1" aria-expanded="false" aria-controls="flush-collapse1">
                                DIMENSI
                            </button>
                        </h2>
                        <div id="flush-collapse1" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p class="mb-0 fw-bold">Dimensi Produk</p>
                                <ul>
                                    <li>
                                        <p class="mb-0">
                                            Panjang : <?= $produk['deskripsi']['dimensi']['asli']['panjang'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Lebar : <?= $produk['deskripsi']['dimensi']['asli']['lebar'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Tinggi : <?= $produk['deskripsi']['dimensi']['asli']['tinggi'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Berat : <?= $produk['deskripsi']['dimensi']['asli']['berat'] ?> kg
                                        </p>
                                    </li>
                                </ul>
                                <p class="mb-0 mt-1 fw-bold">Dimensi Kemasan</p>
                                <ul>
                                    <li>
                                        <p class="mb-0">
                                            Panjang : <?= $produk['deskripsi']['dimensi']['paket']['panjang'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Lebar : <?= $produk['deskripsi']['dimensi']['paket']['lebar'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Tinggi : <?= $produk['deskripsi']['dimensi']['paket']['tinggi'] ?> mm
                                        </p>
                                    </li>
                                    <li>
                                        <p class="mb-0">
                                            Berat : <?= $produk['deskripsi']['dimensi']['paket']['berat'] ?> kg
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="limapuluh-ke-seratus">
                <div>
                    <figure class="img-detail-prev d-none"
                        style="background-image: url('/viewvar3000/<?= $produk['id'] ?>/1'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;">
                    </figure>
                    <img class="img-detail-prev"
                        <?= $produk['varian'][0]['stok'] <= 0 ? 'style="filter: grayscale(90%)"' : ''; ?>
                        src="/viewvar/<?= $produk['id'] ?>/1" onmousemove="zoom(event)" onmouseleave="mouseoff(event)">
                </div>
                <div class="mb-3 mt-3" style="overflow: auto">
                    <div class="container-img-detail-select"
                        <?= $produk['varian'][0]['stok'] <= 0 ? 'style="filter: grayscale(90%)"' : ''; ?>>
                        <?php foreach (explode(",", $produk['varian'][0]['urutan_gambar']) as $indx => $p_v) { ?>
                            <input <?= $indx == 0 ? 'checked' : '' ?> id="gambar<?= $indx ?>" type="radio" name="gambar"
                                value="<?= $p_v ?>">
                            <label class="img-detail-select" for="gambar<?= $indx ?>"><img
                                    src="/viewvar/<?= $produk['id'] ?>/<?= $p_v ?>"></label>
                        <?php } ?>
                        <script>
                            const radioImgElm = document.querySelectorAll('input[name="gambar"]');
                            radioImgElm.forEach(elm => {
                                elm.addEventListener('change', (e) => {
                                    const imgElm = document.querySelector("figure.img-detail-prev");
                                    const imgFixElm = document.querySelector("img.img-detail-prev");
                                    imgElm.style =
                                        "background-image: url('" + "/viewvar3000/<?= $produk['id']; ?>/" +
                                        e.target.value
                                        .split("-")[0] +
                                        "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
                                    imgFixElm.src = "/viewvar/<?= $produk['id']; ?>/" + e.target.value
                                        .split("-")[0];
                                })
                            });
                        </script>
                    </div>
                </div>
                <div class="gap-2 hide-ke-show-flex">
                    <div class="container-varian mb-3 d-flex">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                            <input id="varian<?= $ind_v ?>" value="<?= $v['urutan_gambar'] ?>-<?= $v['nama'] ?>"
                                type="radio" name="varian">
                            <label for="varian<?= $ind_v ?>"><span
                                    style="background-color: <?= $v['kode'] ?>"></span></label>
                        <?php } ?>
                    </div>
                    <div class="d-flex gap-1 mb-2">
                        <?php foreach ($produkSemua as $ind_ps => $ps) { ?>
                            <a href="/product/<?= strtolower(str_replace(' ', '-', $ps['nama'])) ?>/<?= $ind_ps ?>"
                                class="btn-default-abu <?= $ind_ps == $indexNama ? 'border border-dark' : '' ?>"><?= json_decode($ps['deskripsi'], true)['dimensi']['asli']['panjang'] ?>
                                mm</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (count($produkSejenis) > 0) { ?>
            <hr class="mt-5">
            <p class="text-center">Anda mungkin juga suka</p>
            <div class="container-card1">
                <?php foreach ($produkSejenis as $ind_p => $p) { ?>
                    <div class="card1">
                        <div style="position: relative;" onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')"
                            class="cursor-pointer">
                            <div class="card1-content-img">
                                <span
                                    <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                                <div class="d-flex flex-column gap-2">
                                    <?= session()->get('role') == '1' ? '<a class="card1-btn-img" href="/admin/editproduct/' . $p['id'] . '"><i class="material-icons">edit</i></a>' : '' ?>
                                    <?= in_array($p['id'], $wishlist) ? '<a class="card1-btn-img" href="/delwishlist/' . $p['id'] . '"><i class="material-icons">bookmark</i></a>' : '<a class="card1-btn-img" href="/addwishlist/' . $p['id'] . '"><i class="material-icons">bookmark_border</i></a>' ?>
                                    <a id="card<?= $ind_p ?>" class="card1-btn-img"
                                        href="/addcart/<?= $p['id'] ?>/<?= json_decode($p['varian'], true)[0]['nama'] ?>/1"><i
                                            class="material-icons">shopping_cart</i></a>
                                </div>
                            </div>
                            <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="gambar">
                                <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic" id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt="">
                                <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic-hover" id="img<?= $ind_p ?>" src="/viewpichover/<?= $p['id']; ?>" alt="">
                            </a>
                        </div>
                        <div class="container-varian mb-1 d-flex">
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
                                            "/viewvar/<?= $p['id']; ?>/" + e.target.value.split("-")[0].split(
                                                ",")[
                                                0];

                                        btnKeranjang<?= $ind_p ?>Elm.href = "/addcart/<?= $p['id'] ?>/" + e
                                            .target
                                            .value.split("-")[1] + "/1";
                                    })
                                });
                            </script>
                        </div>
                        <p class="text-secondary text-sm-start m-0"><?= ucwords($p['kategori']); ?></p>
                        <h5 style="font-size:18px;"><?= str_replace('Tv', 'TV', ucwords($p['nama'])); ?></h5>
                        <div class="d-flex gap-2">
                            <p class="harga">Rp <?= number_format($p['harga'] * (100 - $p['diskon']) / 100, 0, ',', '.'); ?></p>
                            <?php if ($p['diskon'] > 0) { ?>
                                <p class="harga-diskon">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    const figureElm = document.querySelector('figure');
    const btnKeranjangElm = document.getElementById('btn-keranjang');
    const radioVarianElm = document.querySelectorAll('input[name="varian"]');
    const varian = JSON.parse('<?= json_encode($produk['varian']) ?>');
    const teksInfoHabisElm = document.getElementById('info-habis');
    const containerMarketElm = document.getElementById('container-market');
    console.log(varian)
    let varianSelected = "<?= $produk['varian'][0]['nama'] ?>";
    let jumlahSelected = "1";
    radioVarianElm.forEach(elm => {
        elm.addEventListener('change', (e) => {
            const varianFullSelected = varian[Number(e.target.value.split("-")[2])];
            const imgElm = document.querySelector("figure.img-detail-prev");
            const imgFixElm = document.querySelector("img.img-detail-prev");
            imgElm.style =
                "background-image: url('" + "/viewvar3000/<?= $produk['id']; ?>/" + e.target.value.split(
                    "-")[0].split(",")[0] +
                "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
            imgFixElm.src = "/viewvar/<?= $produk['id']; ?>/" + e.target.value.split("-")[0].split(",")[0];

            const containerImgDetailElm = document.querySelector(".container-img-detail-select");
            containerImgDetailElm.innerHTML = "";
            const urutanGambar = e.target.value.split("-")[0].split(",");
            urutanGambar.forEach((urutan, ind_x) => {
                containerImgDetailElm.innerHTML += '<input id="gambar' + ind_x +
                    '" type="radio" name="gambar" value="' + urutan +
                    '"><label class="img-detail-select" for="gambar' + ind_x +
                    '"><img src="/viewvar/<?= $produk['id'] ?>/' + urutan + '"></label>'
            })

            if (Number(varianFullSelected.stok) <= 0) {
                imgFixElm.style = 'filter: grayscale(90%)';
                containerImgDetailElm.style = 'filter: grayscale(90%)';
                containerMarketElm.classList.add('d-none')
            } else {
                imgFixElm.style = 'filter: grayscale(0%)';
                containerImgDetailElm.style = 'filter: grayscale(0%)';
                containerMarketElm.classList.remove('d-none')
            }

            if (btnKeranjangElm) {
                if (Number(varianFullSelected.stok) > 0) {
                    btnKeranjangElm.innerHTML = 'Keranjang'
                    btnKeranjangElm.href = "/addcart/<?= $produk['id'] ?>/" + e.target.value.split("-")[1] +
                        "/" + jumlahSelected;
                    btnKeranjangElm.classList.remove('disabled')
                    teksInfoHabisElm.classList.add('d-none')
                } else {
                    btnKeranjangElm.innerHTML = 'Stok habis'
                    btnKeranjangElm.href = ''
                    btnKeranjangElm.classList.add('disabled')
                    teksInfoHabisElm.classList.remove('d-none')
                }
            }
            varianSelected = e.target.value.split("-")[1];

            const radioImgElm = document.querySelectorAll('input[name="gambar"]');
            radioImgElm.forEach(elm1 => {
                elm1.addEventListener('change', (elmVar) => {
                    const imgElm = document.querySelector("figure.img-detail-prev");
                    const imgFixElm = document.querySelector("img.img-detail-prev");
                    imgElm.style =
                        "background-image: url('" + "/viewvar3000/<?= $produk['id']; ?>/" +
                        elmVar.target.value.split("-")[0].split(",")[0] +
                        "'); background-size: cover; position: absolute; transform: translateX(-410px); width: 400px; height: 400;"
                    imgFixElm.src = "/viewvar/<?= $produk['id']; ?>/" + elmVar.target.value
                        .split("-")[0].split(",")[0]
                })
            });
        })
    });
    const jumlahBarangElm = document.querySelector('input[name="jumlah"]');

    function kurangJumlah() {
        if (Number(jumlahBarangElm.value) > 1) {
            jumlahBarangElm.value--
            btnKeranjangElm.href = "/addcart/<?= $produk['id'] ?>/" + varianSelected + "/" +
                jumlahBarangElm.value;
            jumlahSelected = jumlahBarangElm.value;
        }
    }

    function tambahJumlah() {
        jumlahBarangElm.value++;
        btnKeranjangElm.href = "/addcart/<?= $produk['id'] ?>/" + varianSelected + "/" +
            jumlahBarangElm.value;
        jumlahSelected = jumlahBarangElm.value;
    }

    function zoom(e) {
        if (window.innerWidth > 600) {
            figureElm.classList.remove('d-none')
            figureElm.style.backgroundSize = "auto"
            const widthGambar = e.target.offsetWidth;
            const gmbrPosition = [
                (e.offsetX / widthGambar) * 100,
                (e.offsetY / widthGambar) * 100,
            ];
            figureElm.style.backgroundPosition =
                gmbrPosition[0] + "% " + gmbrPosition[1] + "%";
        }
    }

    function mouseoff(e) {
        if (window.innerWidth > 600) {
            figureElm.classList.add('d-none')
            figureElm.style.backgroundSize = "cover"
        }
    }
</script>

<?= $this->endSection(); ?>