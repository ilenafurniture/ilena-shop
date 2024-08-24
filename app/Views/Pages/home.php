<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php if ($msg_active) { ?>
<div id="modal-voucher" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; z-index: 99;"
    class="d-flex justify-content-center align-items-center">
    <div style="border-radius: 10px; overflow: hidden; background-color: white; width: 80%; max-width: 500px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);"
        class="p-5">
        <h1 class="teks-sedang mb-3">Klaim voucher diskon 5% Anda sekarang juga</h1>
        <p class="text-secondary">*S&K diskon ini hanya berlaku 1 bulan sejak menjadi member kami</p>
        <a href="/product" class="btn-default w-100 text-center mb-2">Beli Produk</a>
        <button class="btn-teks-aja mx-auto" onclick="closeModalVoucher()">Nanti</button>
    </div>
</div>
<script>
function closeModalVoucher() {
    document.getElementById('modal-voucher').classList.add('d-none')
    document.getElementById('modal-voucher').classList.remove('d-flex')
}
</script>
<?php } ?>


<div id="login-modal"
    style="position: fixed; background-color: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100vw; height: 100svh; z-index: 99;"
    class="d-none justify-content-center align-items-center">
    <div style="width: fit-content; height: fit-content;  overflow: hidden; position:relative;">
        <div style="position: absolute;"
            class="w-100 h-100 d-flex flex-column justify-content-between align-items-center">
            <div class="d-flex justify-content-end w-100 py-1 px-3">
                <p class="m-0 d-block" style="cursor: pointer; font-size:18px; font-weight:bold; color:white;"
                    onclick="closeLoginModel()">X</p>
            </div>
            <div class="d-flex flex-column align-items-center">
                <a href="/register" class="btn-lonjong putih mb-2">Daftar Sekarang</a>
                <p class="text-center" style="color:#fcf7da;">Sudah punya akun? <a href="/login" class="btn-teks-aja"
                        style="display: inline; color: white">Login akun</a></p>
            </div>
        </div>
        <img src="<?= base_url('/img/foto/diskon.webp') ?>" style="width: 400px; height: 400px; object-fit: cover"
            alt="">
    </div>
</div>


<?php if (!session()->get('isLogin')) { ?>
<script>
const loginModalElm = document.getElementById('login-modal')
let opened = false;
document.body.onscroll = (e) => {
    if (!window.sessionStorage.getItem('close-login-modal')) {
        const scrollingElm = e.target.scrollingElement;
        const hasil = Math.round(
            (scrollingElm.scrollTop /
                (scrollingElm.scrollHeight -
                    scrollingElm.clientHeight)) *
            100
        );
        if (hasil > 50 && !opened) {
            loginModalElm.classList.add("d-flex")
            loginModalElm.classList.remove("d-none")
            opened = true
        }
    }
};

function closeLoginModel() {
    loginModalElm.classList.remove("d-flex")
    loginModalElm.classList.add("d-none")
    window.sessionStorage.setItem('close-login-modal', true)
    opened = false
    // setTimeout(() => {
    //     loginModalElm.classList.add("d-flex")
    //     loginModalElm.classList.remove("d-none")
    // }, 15000);
}

if (window.innerWidth <= 600) {
    loginModalElm.classList.add('p-3');
}
</script>
<?php } ?>
<div style="background-color:#f6e9dd;">
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="img-teks">
                    <h1 class="teks-besar mb-2" style="color:#f3e9a8;">Modern & Stylish Furniture</h1>
                    <h5 class="teks-sedang mb-2 show-block-ke-hide" style="color:#4e2700;">Be Yourself With The Best
                        Choice</h5>
                </div>
                <img src="<?= base_url('/img/foto/gambar-hero.webp') ?>" alt="Gambar Hero" class="d-block w-100" style="
                height: 100%;
                object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="<?= base_url('/img/foto/gambar-hero2 edit.webp') ?>" alt="Gambar Hero" class="d-block w-100"
                    style="
                
                height: 100%;
                object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="Gambar Hero" class="d-block w-100"
                    style="
                height: 100%;
                object-fit: cover;">
            </div>
        </div>
        <button class="carousel-control-prev show-block-ke-hide" type="button"
            data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next show-block-ke-hide" type="button"
            data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container mt-5 mb-3 show-block-ke-hide">
        <div class="d-flex gap-4 h-100 w-100">
            <a class="img-kategori">
                <video autoplay muted loop>
                    <source src="<?= base_url('/img/v1.mp4') ?>" type="video/mp4">
                </video>
            </a>
            <a class="img-kategori">
                <video autoplay muted loop>
                    <source src="<?= base_url('/img/v2.mp4') ?>" type="video/mp4">
                </video>
            </a>
            <a class="img-kategori">
                <video autoplay muted loop>
                    <source src="<?= base_url('/img/v3.mp4') ?>" type="video/mp4">
                </video>
            </a>
        </div>
    </div>

    <div class="container my-4 hide-ke-show-block">
        <div class="d-flex gap-1 h-100 w-100">
            <a class="img-kategori">
                <video autoplay muted loop>
                    <source src="<?= base_url('/img/v1.mp4') ?>" type="video/mp4">
                </video>
            </a>
            <a class="img-kategori">
                <video autoplay muted loop>
                    <source src="<?= base_url('/img/v2.mp4') ?>" type="video/mp4">
                </video>
            </a>
            <a class="img-kategori">
                <video autoplay muted loop>
                    <source src="<?= base_url('/img/v3.mp4') ?>" type="video/mp4">
                </video>
            </a>
        </div>
    </div>

    <div class="container justify-content-between mb-4 align-items-end show-flex-ke-hide">
        <h1 class="teks-besar">Ilena<br>Furniture</h1>
        <h1 class="teks-sedang mb-1">Hasil Sebuah<br>Proses</h1>
    </div>
    <div class="container justify-content-between align-items-end hide-ke-show-flex mb-3">
        <h1 class="teks-besar">Ilena<br>Furniture</h1>
        <h1 class="teks-sedang mb-1">Hasil Sebuah<br>Proses</h1>
    </div>

    <div class="container">
        <video autoplay muted loop style="
        width: 100%;
        height: 80%;
        object-fit: cover;
        ">
            <source src="<?= base_url('/img/video.mp4') ?>" type="video/mp4">
        </video>
    </div>

    <div class="mt-5 mb-3 show-block-ke-hide">
        <div class="d-flex">
            <div style="flex:1; position:relative;">
                <div
                    style="position:absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); backdrop-filter: blur(0.5px);">
                </div>
                <div
                    style="position:absolute; top:0; left:0; right:0; bottom:0; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1;">
                    <h4 class="teks-besar" style="color:white;">Up to 5%</h4>
                    <p style="color:white;">Segera claim diskon anda sebelum berakhir</p>
                    <a href="/product" class="btn-default-hitam">SHOP THE SALE</a>
                </div>
                <img src="<?= base_url('/img/foto/l1.webp') ?>"
                    style="width:100%; height:100%; object-fit:cover; background-size: cover; background-color: rgba(0,0,0,0.5); background-repeat:no-repeat; background-blend-mode: color;">
            </div>

            <div style="flex:1; position:relative;">
                <div
                    style="position:absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); backdrop-filter: blur(0.5px);">
                </div>
                <div
                    style="position:absolute; top:0; left:0; right:0; bottom:0; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1;">
                    <h4 class="teks-besar" style="color:white;">Up to 5%</h4>
                    <p style="color:white;">Segera claim diskon anda sebelum berakhir</p>
                    <a href="/product" class="btn-default-hitam">SHOP THE SALE</a>
                </div>
                <img src="<?= base_url('/img/foto/home1.webp') ?>"
                    style="width:100%; height:100%; object-fit:cover; background-size: cover; background-color: rgba(0,0,0,0.5); background-repeat:no-repeat; background-blend-mode: color;">
            </div>
        </div>
    </div>



    <div class="container justify-content-center mb-4 align-items-end d-flex">
        <h1 class="teks-besar">Our Gallery</h1>
    </div>
    <div class="container show-block-ke-hide">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap:1px;">
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/1.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/2.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/3.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
                <div
                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); color: white;">
                    <h4>Special Offer</h4>
                    <p>Grab it now!</p>
                    <a href="/product"
                        style="color: white; background-color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Learn
                        More</a>
                </div>
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/4.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/5.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/6.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
                <div
                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); color: white;">
                    <h4>Special Offer</h4>
                    <p>Grab it now!</p>
                    <a href="/product"
                        style="color: white; background-color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Learn
                        More</a>
                </div>
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/7.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
                <div
                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); color: white;">
                    <h4>Special Offer</h4>
                    <p>Grab it now!</p>
                    <a href="/product"
                        style="color: white; background-color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Learn
                        More</a>
                </div>
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/8.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
            </div>
            <div style="position: relative;">
                <img src="<?= base_url('/img/foto/9.webp') ?>"
                    style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;">
            </div>
        </div>
    </div>


    <div class="container hide-ke-show-block">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap:1px;">
            <img src="<?= base_url('/img/foto/1.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/2.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/3.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/4.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/5.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/6.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/7.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/8.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
            <img src="<?= base_url('/img/foto/9.webp') ?>" style="width: 100%; aspect-ratio: 1 /1; object-fit:cover;;">
        </div>
    </div>

    <div class="mt-5 mb-3" style="position:relative;">
        <div style="position:absolute"
            class="p-5 d-flex flex-column w-100 h-100 justify-content-center align-items-center">
            <!-- <h4 class="teks-besar" style="color:brown;">Up to 5%</h4> -->
            <h3 style="color:white;">Find Out More</h3>
            <a href="/product" class="btn-default-hitam">Click Here</a>
        </div>
        <img src="<?= base_url('/img/foto/home1.webp') ?>" style="width: 100%; height:50svh; object-fit: cover; background-color: rgba(0,0,0, 0.5);
  background-repeat:no-repeat; background-blend-mode: color;">
    </div>

    <div class="container mt-5 mb-3 show-block-ke-hide">
        <h1 class="teks-sedang justify-content-between" style="font-weight:600; letter-spacing:-1px;">Produk Populer
        </h1>
    </div>

    <div class="container hide-ke-show-block mt-5 mb-3">
        <h1 class="teks-besar justify-content-between">Produk Populer</h1>
    </div>

    <div class="container mb-4">
        <div class="container-card1">
            <?php foreach ($produk as $ind_p => $p) { ?>
            <div class="card1">
                <div style="position: relative;">
                    <span class="card1-content-img-kiri"
                        <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                    <div class="d-flex flex-column gap-2 card1-content-img-kanan">
                        <?= in_array($p['id'], $wishlist) ? '<a class="card1-btn-img" href="/delwishlist/' . $p['id'] . '"><i class="material-icons">bookmark</i></a>' : '<a class="card1-btn-img" href="/addwishlist/' . $p['id'] . '"><i class="material-icons">bookmark_border</i></a>' ?>
                        <a id="card<?= $ind_p ?>" class="card1-btn-img"
                            href="/addcart/<?= $p['id'] ?>/<?= json_decode($p['varian'], true)[0]['nama'] ?>/1"><i
                                class="material-icons">shopping_cart</i></a>
                    </div>
                    <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>">
                        <img id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt="">
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
                                "/viewvar/<?= $p['id']; ?>/" + e.target.value.split("-")[0].split(",")[
                                    0];

                            btnKeranjang<?= $ind_p ?>Elm.href = "/addcart/<?= $p['id'] ?>/" + e.target
                                .value.split("-")[1] + "/1";
                        })
                    });
                    </script>
                </div>
                <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="text-dark">
                    <p class="text-secondary text-sm-start m-0"><?= ucwords($p['kategori']); ?></p>
                    <h5 style="font-size:18px;"><?= str_replace('Tv', 'TV', ucwords($p['nama'])); ?></h5>
                    <div class="d-flex gap-2">
                        <p class="harga">Rp <?= number_format($p['harga'] * (100 - $p['diskon']) / 100, 0, ',', '.'); ?>
                        </p>
                        <?php if ($p['diskon'] > 0) { ?>
                        <p class="harga-diskon">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                        <?php } ?>
                    </div>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="container mb-5 d-flex justify-content-center">
        <a href="/product" class="btn-lonjong">Lihat Semua Produk <i class="material-icons">arrow_forward</i></a>
    </div>

    <!-- <div style="background-image: url('../img/foto/gambar-hero2 edit.webp');
    background-size: cover; background-position: center; background-repeat: no-repeat; color: white; background-color: rgba(0, 0, 0, 0.5); background-blend-mode: color;"
        class="py-5 d-none">
        <div class="img-teks" style="height: 100%; color: white; background-color: rgba(0, 0, 0, 0.5);">
            <div class="container baris-ke-kolom gap-5">
                <div style="flex: 1" class="d-flex justify-content-start gap-4">
                    <img src="../img/gratis ongkir.png" alt="" style="width: 70px; height: 40px">
                    <div>
                        <p class="fw-bold mb-1" style="font-size: 20px;">Free Ongkir 100%</p>
                        <p class="m-0">Dapatkan keuntungan gratis pengiriman 100% untuk wilayah Jawa, Madura, & Bali tanpa
                            minimum belanja</p>
                    </div>
                </div>
                <div style="flex: 1" class="d-flex justify-content-start gap-4">
                    <img src="../img/eco friendly.png" alt="" style="width: 50px; height: 50px">
                    <div>
                        <p class="fw-bold mb-1" style="font-size: 20px;">Eco Friendly</p>
                        <p class="m-0">Dibuat dari bahan ramah lingkungan yang tidak berbahaya bagi kelangsungan manusia,
                            bumi, dan lingkungan</p>
                    </div>
                </div>
                <div style="flex: 1" class="d-flex justify-content-start gap-4">
                    <img src="../img/seluruh indo.png" alt="" style="width: 70px; height: 60px">
                    <div>
                        <p class="fw-bold mb-1" style="font-size: 20px;">Bebas kirim seluruh Indonesia</p>
                        <p class="m-0">Bekerjasama dengan mitra ekspedisi yang telah menjangkau pengiriman aman & terpercaya
                            ke seluruh Indonesia</p>
                    </div>
                </div>
            </div>
        </div>
        <img class="img-besar" style="height: 300px;" src="../img/foto/gambar-hero2 edit.webp" alt="Gambar Hero">
    </div> -->
    <div style="background-color: whitesmoke;" class="py-5">
        <div class="container baris-ke-kolom gap-5">
            <div style="flex: 1" class="d-flex gap-4 align-items-center">

                <img src="../img/gratis ongkir oren1.png" alt="" style="width: 70px;">

                <div>
                    <p class="fw-bold mb-1" style="font-size: 20px;">Free Ongkir 100%</p>
                    <p class="m-0">Dapatkan keuntungan gratis pengiriman untuk wilayah Jawa, Madura, & Bali tanpa
                        minimum belanja</p>
                </div>
            </div>
            <div style="flex: 1" class="d-flex gap-4 align-items-center">

                <img src="../img/svlk oren.png" alt="" style="width: 70px;">

                <div>
                    <p class="fw-bold mb-1" style="font-size: 20px;">Eco Friendly</p>
                    <p class="m-0">Dibuat dari bahan ramah lingkungan yang tidak berbahaya bagi kelangsungan manusia,
                        bumi, dan lingkungan</p>
                </div>
            </div>
            <div style="flex: 1" class="d-flex gap-4 align-items-center">
                <div style="width: 70px;" class="d-flex justify-content-center">
                    <img src="../img/seluruh indo oren1.png" alt="" style="width: 50px;">
                </div>
                <div style="flex:1;">
                    <p class="fw-bold mb-1" style="font-size: 20px;">Bebas kirim seluruh Indonesia</p>
                    <p class="m-0">Bekerjasama dengan mitra ekspedisi yang telah menjangkau pengiriman aman & terpercaya
                        ke seluruh Indonesia</p>
                </div>
            </div>
        </div>
        <!-- </div> -->
        <!-- <img class="img-besar" style="height: 300px;" src="../img/foto/gambar-hero2 edit.webp" alt="Gambar Hero"> -->
    </div>
</div>

<script>
function togglePassword(e) {
    var passwordField = document.querySelector('input[name="sandi"]');
    if (passwordField.type === "password") {
        passwordField.type = "text";
        e.target.style.color = 'var(--merah)'
    } else {
        passwordField.type = "password";
        e.target.style.color = 'black'
    }
}
</script>
<?= $this->endSection(); ?>