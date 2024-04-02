<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten">
    <div class="baris-ke-kolom">
        <div class="limapuluh-ke-seratus">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Produk Kami</a></li>
                    <li class="breadcrumb-item"><a href="/">Meja</a></li>
                    <li class="breadcrumb-item">Meja TV</li>
                    </li>
                </ol>
            </nav>
            <h1 class="teks-besar mb-2"><?= $produk['nama'] ?></h1>
            <div class="d-flex gap-2 mb-3">
                <p class="harga">Rp
                    <?= number_format($produk['harga'] * (100 - $produk['diskon']) / 100, 0, ',', '.'); ?></p>
                <p class="harga-diskon">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
            </div>
            <p><?= $produk['deskripsi']['deskripsi'] ?></p>
            <div class="container-varian mb-3">
                <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                <input id="varian<?= $ind_v ?>" value="<?= $v['urutan_gambar'] ?>" type="radio" name="varian">
                <label for="varian<?= $ind_v ?>"><span style="background-color: <?= $v['kode'] ?>"></span></label>
                <?php } ?>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <div class="number-control">
                    <div class="number-left" onclick="kurangJumlah()"></div>
                    <input type="number" name="jumlah" class="number-quantity" value="1">
                    <div class="number-right" onclick="tambahJumlah()"></div>
                </div>
                <a id="btn-keranjang" href="/addcart/<?= $produk['id'] ?>/<?= $produk['varian'][0]['nama'] ?>/1"
                    class="btn-default-merah">Keranjang</a>
            </div>
            <?php if ($produk['tokped'] || $produk['shopee'] || $produk['tiktok']) { ?>
            <div class="mt-4">
                <p class="mb-2">
                    Produk ini juga tersedia di
                </p>
                <div>
                    <?php if ($produk['tokped']) { ?>
                    <a href="<?= $produk['tokped']; ?>" title="Tokopedia" target="blank"><img
                            src="/img/logo/tokopedia.png" class="marketplace"></a>
                    <?php } ?>
                    <?php if ($produk['shopee']) { ?>
                    <a href="<?= $produk['shopee']; ?>" title="Shopee" target="blank"><img src="/img/logo/shopee.png"
                            class="marketplace"></a>
                    <?php } ?>
                    <?php if ($produk['tiktok']) { ?>
                    <a href="<?= $produk['tiktok']; ?>" title="Tiktok" target="blank"><img
                            src="/img/logo/tiktokshop.svg" class="marketplace"></a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <a class="btn-teks-aja my-3" href="/wishlist"><i class="material-icons">bookmark_border</i> Tambah ke
                wishlist</a>

            <div class="accordion accordion-flush" id="accordionFlushExample">
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
                            <p class="mb-0 fw-bold">Dimensi Asli</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Panjang : <?= $produk['deskripsi']['dimensi']['asli']['panjang'] ?> cm
                                    </p>
                                </li>
                                <li>
                                    <p class="mb-0">
                                        Lebar : <?= $produk['deskripsi']['dimensi']['asli']['lebar'] ?> cm
                                    </p>
                                </li>
                                <li>
                                    <p class="mb-0">
                                        Tinggi : <?= $produk['deskripsi']['dimensi']['asli']['tinggi'] ?> cm
                                    </p>
                                </li>
                                <li>
                                    <p class="mb-0">
                                        Berat : <?= $produk['deskripsi']['dimensi']['asli']['berat'] ?> kg
                                    </p>
                                </li>
                            </ul>
                            <p class="mb-0 mt-1 fw-bold">Dimensi Paket </p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Panjang : <?= $produk['deskripsi']['dimensi']['paket']['panjang'] ?> cm
                                    </p>
                                </li>
                                <li>
                                    <p class="mb-0">
                                        Lebar : <?= $produk['deskripsi']['dimensi']['paket']['lebar'] ?> cm
                                    </p>
                                </li>
                                <li>
                                    <p class="mb-0">
                                        Tinggi : <?= $produk['deskripsi']['dimensi']['paket']['tinggi'] ?> cm
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

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                            PERAWATAN
                        </button>
                    </h2>
                    <div id="flush-collapse2" class="accordion-collapse collapse"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <p class="mb-0 fw-bold">Bagaimana cara merawat barang ini?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        <?= $produk['deskripsi']['perawatan'] ?>
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="limapuluh-ke-seratus">
            <div class="d-flex justify-content-end">
                <div class="d-flex align-items-start flex-column gap-2">
                    <!-- <div class="d-flex align-items-end gap-2">
                        <p id="urutan-skrg" class="m-0 fw-bold" style="font-size:24px; line-height: 24px">
                            01
                        </p>
                        <p class="m-0" id="jumlah-urutan">
                            / 0
                        </p>
                    </div>
                    <div>
                        <button style="background-color:white; border: 0;">
                            < </button>
                                <button class="ms-5" style="background-color:white; border: 0;"
                                    onclick="nextGambar()">></button>
                    </div> -->
                </div>
            </div>
            <div class="mt-3">
                <img class="img-detail-prev" src="/viewvar/<?= $produk['id'] ?>/1">
            </div>
            <div class="container-img-detail-select mb-3 mt-3">
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
                        const imgElm = document.querySelector(".img-detail-prev");
                        imgElm.src =
                            "/viewvar/<?= $produk['id']; ?>/" + e.target.value
                            .split("-")[0];
                    })
                });
                </script>
            </div>
        </div>
    </div>
</div>
<script>
const btnKeranjangElm = document.getElementById('btn-keranjang');
const radioVarianElm = document.querySelectorAll('input[name="varian"]');
const varian = JSON.parse('<?= json_encode($produk['varian']) ?>');
console.log(varian)
radioVarianElm.forEach(elm => {
    elm.addEventListener('change', (e) => {
        const imgElm = document.querySelector(".img-detail-prev");
        imgElm.src =
            "/viewvar/<?= $produk['id']; ?>/" + e.target.value.split(",")[0];

        const containerImgDetailElm = document.querySelector(".container-img-detail-select");
        containerImgDetailElm.innerHTML = "";
        const urutanGambar = e.target.value.split(",");
        urutanGambar.forEach((urutan, ind_x) => {
            containerImgDetailElm.innerHTML += '<input id="gambar' + ind_x +
                '" type="radio" name="gambar" value="' + urutan +
                '"><label class="img-detail-select" for="gambar' + ind_x +
                '"><img src="/viewvar/<?= $produk['id'] ?>/' + urutan + '"></label>'
        })

        btnKeranjangElm.href = "/addcart/<?= $produk['id'] ?>/" + e.target.value + "/1"
        cari tahu siapa yg punya e target value(urutan gambar) yg ada di varian

        const radioImgElm = document.querySelectorAll('input[name="gambar"]');
        radioImgElm.forEach(elm1 => {
            elm1.addEventListener('change', (elmVar) => {
                const imgElm = document.querySelector(".img-detail-prev");
                imgElm.src =
                    "/viewvar/<?= $produk['id']; ?>/" + elmVar.target.value
                    .split("-")[0];
            })
        });
    })
});
const jumlahBarangElm = document.querySelector('input[name="jumlah"]');

function kurangJumlah() {
    if (Number(jumlahBarangElm.value) > 1) {
        jumlahBarangElm.value--
    }
}

function tambahJumlah() {
    jumlahBarangElm.value++
}
</script>



<?= $this->endSection(); ?>adioIm