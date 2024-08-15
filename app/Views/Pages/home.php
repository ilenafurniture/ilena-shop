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
    <div class="d-flex" style="width: 70%; height: 80%; border-radius: 1em; overflow: hidden">
        <div class="show-block-ke-hide w-50" style="position: relative;">
            <div style="position: absolute;" class="p-5">
                <div style="height: 30px;"></div>
                <h1 class="text-light teks-besar mb-3 hide-di-1350">JOIN OUR<br>MEMBERSHIP</h1>
                <h1 class="text-light mb-3 show-di-1350" style="font-weight: 700;">JOIN OUR<br>MEMBERSHIP</h1>
                <p class="text-light" style="font-size: 20px; width: 70%;">Daftarkan diri Anda sekarang dan jadilah yang
                    pertama tahu beragam produk baru, promo eksklusif, event terdekat, inspirasi, tips & trik serta
                    masih banyak lagi manfaat lainnya!</p>
                <span class="d-block mt-4" style="height: 1px; width: 40%; background-color: white;"></span>
            </div>
            <img src="../img/foto/gambar-hero.webp" class="w-100 h-100" style="object-fit: cover;" alt="">
        </div>
        <div class="limapuluh-ke-seratus d-flex flex-column" style="background-color: white;">
            <div class="d-flex justify-content-end align-items-center px-3" style="height: 30px;">
                <p class="m-0 d-block" style="cursor: pointer;" onclick="closeLoginModel()">X</p>
            </div>
            <div class="d-flex flex-column align-items-center px-5 gap-2 pt-5" style="flex: 1;">
                <img src="<?php echo base_url('/img/LogoIlena.png'); ?>" alt="logo ilena"
                    style="height: 25px; aspect-ratio: 2/12;">
                <p style="text-align: center;">Bergabunglah menjadi member berharga Kami dan dapatkan keuntungan pada
                    pembelanjaan pertama!</p>
                <div class="w-100">
                    <form id="registerForm" action="/actionregister" method="post">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Nama Lengkap</label>
                            <input name="nama" type="text" class="form-control" placeholder="Masukkan Nama Lengkap">
                            <div class="invalid-feedback">Mohon masukkan nama lengkap.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" placeholder="Masukkan Email">
                            <div class="invalid-feedback">Mohon masukkan alamat email yang valid.</div>
                        </div>
                        <div class="mb-3">
                            <label for="nohp" class="form-label">Nomor Telepon</label>
                            <input name="nohp" type="text" class="form-control" placeholder="Masukkan Nomor Telepon">
                            <div class="invalid-feedback">Mohon masukkan nomor telepon yang valid.</div>
                        </div>
                        <label for="sandi" class="form-label">Kata Sandi</label>
                        <div class="input-group mb-3">
                            <input name="sandi" type="password" class="form-control" id="password"
                                placeholder="Masukkan Kata Sandi">
                            <span class="input-group-text d-flex justify-content-center align-items-center"
                                onclick="togglePassword(event)">
                                <i class="material-icons" style="cursor: default; -webkit-user-select: none; -ms-user-select: none; user-select: none;">remove_red_eye</i>
                            </span>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <input type="checkbox" id="validation-syarat" name="validasi-syarat" required>
                            <label for="validation-syarat">
                                <p class="m-0">Dengan ini Anda menyetujui syarat dan ketentuan pendaftaran.</p>
                            </label>
                        </div>
                        <div class="mb-3 d-flex w-100 justify-content-center">
                            <button type="submit" class="btn btn-default btn-block">Daftar Sekarang</button>
                        </div>
                        <div>
                            <p class="text-center">Sudah punya akun? <a href="/login" class="btn-teks-aja"
                                    style="display: inline;">Login akun</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!session()->get('isLogin')) { ?>
    <script>
        const loginModalElm = document.getElementById('login-modal')
        let opened = false;
        // document.body.onscroll = (e) => {
        //     const scrollingElm = e.target.scrollingElement;
        //     const hasil = Math.round(
        //         (scrollingElm.scrollTop /
        //             (scrollingElm.scrollHeight -
        //                 scrollingElm.clientHeight)) *
        //         100
        //     );
        //     if (hasil > 50 && !opened) {
        //         loginModalElm.classList.add("d-flex")
        //         loginModalElm.classList.remove("d-none")
        //         opened = true
        //     }
        // };

        // function closeLoginModel() {
        //     loginModalElm.classList.remove("d-flex")
        //     loginModalElm.classList.add("d-none")
        //     // window.sessionStorage.setItem('close-login-modal', true)
        //     // opened = false
        //     setTimeout(() => {
        //         loginModalElm.classList.add("d-flex")
        //         loginModalElm.classList.remove("d-none")
        //     }, 15000);
        // }

        // if (window.innerWidth <= 600) {
        //     loginModalElm.children[0].style.width = '95%'
        //     loginModalElm.children[0].style.height = '95%'
        // }
    </script>
<?php } ?>
<div>
    <div>
        <div class="img-teks">
            <h1 class="teks-besar mb-2" style="color:#f3e9a8;">Modern & Stylish Furniture</h1>
            <h5 class="teks-sedang mb-2 show-block-ke-hide" style="color:#4e2700;">Be Yourself With The Best Choice</h5>
            <div class="gap-4 anak-img-teks show-flex-ke-hide">
                <a href="/product" style="color:white;" class="fw-bold">Selengkapnya ></a>
                <a href="/product" style="color:white;" class="fw-bold">Beli Sekarang ></a>
            </div>
        </div>
        <!-- <img class="img-besar" src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Gambar Hero"> -->
        <img class="img-besar" src="../img/foto/gambar-hero.webp" alt="Gambar Hero">
        <!-- <img class="img-besar" src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=2069&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Gambar Hero"> -->
    </div>

    <div class="container my-5 show-block-ke-hide">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="d-flex gap-3 h-100">
                        <a href="" class="img-kategori ">
                            <img src="https://i.ibb.co.com/qWbQK9D/DSC02221.jpg" alt="">
                        </a>
                        <!-- <a href="" class="img-kategori ">
                            <img src="https://images.unsplash.com/photo-1675485470862-9af548e93466?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="">
                        </a> -->
                        <a href="" class="img-kategori">
                            <img src="https://i.ibb.co.com/WfvBDck/DSC02674.jpg" alt="">
                        </a>
                        <!-- <a href="" class="img-kategori">
                            <img src="https://plus.unsplash.com/premium_photo-1676968003017-ae30ca56309d?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="">
                        </a> -->
                        <a href="" class="img-kategori">
                            <img src="https://i.ibb.co.com/9t2fnn5/DSC02897.jpg" alt="">
                        </a>
                        <!-- <a href="" class=" img-kategori">
                            <img src="https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="">
                        </a> -->
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="container mt-5 mb-4 hide-ke-show-block">
        <div id="carouselExampleAutoplaying1" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="d-flex gap-3 h-100">
                        <a href="" class="img-kategori ">
                            <img src="https://i.ibb.co.com/qWbQK9D/DSC02221.jpg" alt="">
                        </a>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-flex gap-3 h-100">
                        <a href="" class="img-kategori">
                            <img src="https://i.ibb.co.com/WfvBDck/DSC02674.jpg" alt="">
                        </a>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-flex gap-3 h-100">
                        <a href="" class="img-kategori">
                            <img src="https://i.ibb.co.com/9t2fnn5/DSC02897.jpg" alt="">
                        </a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying1"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying1"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="container justify-content-between mb-5 align-items-end show-flex-ke-hide">
        <h1 class="teks-besar">Ilena<br>Furniture</h1>
        <h1 class="teks-sedang mb-1">Hasil Sebuah<br>Proses</h1>
    </div>
    <div class="container justify-content-between align-items-end hide-ke-show-flex mb-4">
        <h1 class="teks-besar">Ilena<br>Furniture</h1>
        <h1 class="teks-sedang mb-1">Hasil Sebuah<br>Proses</h1>
    </div>

    <div class="container mb-4">
        <video class="video-besar" autoplay muted loop>
            <source src="../img/video.mp4" type="video/mp4">
        </video>
    </div>
    <div>
        <!-- <img class="img-besar"
            src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
            alt="Gambar Hero"> -->
    </div>
    <div class="container">
        <hr>
    </div>
    <div class="container mb-5 show-block-ke-hide">
        <h1 class="teks-besar justify-content-between">Produk Populer</h1>
    </div>

    <div class="container hide-ke-show-block mb-4">
        <h1 class="teks-besar justify-content-between">Produk Populer</h1>
    </div>

    <div class="container mb-5">
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

    <div style="background-image: url('../img/foto/gambar-hero2 edit.webp');
    background-size: cover; background-position: center; background-repeat: no-repeat; color: white; background-color: rgba(0, 0, 0, 0.5); background-blend-mode: color;"
        class="py-5 d-none">
        <!-- <div class="img-teks" style="height: 100%; color: white; background-color: rgba(0, 0, 0, 0.5);"> -->
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
        <!-- </div> -->
        <!-- <img class="img-besar" style="height: 300px;" src="../img/foto/gambar-hero2 edit.webp" alt="Gambar Hero"> -->
    </div>
    <div style="background-color: whitesmoke;" class="py-5">
        <!-- <div class="img-teks" style="height: 100%; color: white; background-color: rgba(0, 0, 0, 0.5);"> -->
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
                <img src="../img/seluruh indo oren1.png" alt="" style="width: 70px;">
                <div>
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