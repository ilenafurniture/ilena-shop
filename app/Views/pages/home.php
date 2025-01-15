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



<div id="login-modal" class="d-none">
    <div style="position: fixed; background-color: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100vw; height: 100svh; z-index: 99;"
        class="show-flex-ke-hide justify-content-center align-items-center">
        <div style="width: fit-content; height: fit-content;  overflow: hidden; position:relative;">
            <div style="position: absolute;"
                class="p-3 w-100 h-100 d-flex flex-column justify-content-between align-items-center">
                <div class="d-flex justify-content-end w-100 py-1 px-3">
                    <p class="m-0 d-block" style="cursor: pointer; font-size:18px; font-weight:bold; color:black;"
                        onclick="closeLoginModel()">X</p>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <a href="/register" class="btn-diskon mb-2">Daftar Sekarang</a>
                    <p class="text-center" style="color:black;">Sudah punya akun? <a href="/login" class="btn-teks-aja"
                            style="display: inline; color: black">Login</a></p>
                </div>
            </div>
            <img src="<?= base_url('/img/foto/diskon2.webp') ?>" style="width: 400px; height: 400px; object-fit: cover"
                alt="">
        </div>
    </div>
    <div style="position: fixed; background-color: white; bottom: 0; left: 0; width: 100%; height: fit-content; z-index: 99;"
        class="hide-ke-show-flex flex-column justify-content-center align-items-center p-4">
        <div class="d-flex justify-content-end w-100" style="position: relative; margin-bottom: -10px;">
            <button class="btn-teks-aja" onclick="closeLoginModel()">X</button>
        </div>
        <h1 class="teks-sedang">Klaim diskon 5%</h1>
        <p class="text-secondary">Untuk pembelian pertama</p>
        <a href="/register" class="btn-default w-100 text-center mb-2">Join Our Membership</a>
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
            loginModalElm.classList.remove("d-none")
            opened = true
        }
    }
};

function closeLoginModel() {
    loginModalElm.classList.add("d-none")
    window.sessionStorage.setItem('close-login-modal', true)
    opened = false
}
</script>
<?php } ?>




<!--Tampilan Home Baru -->
<!-- Tampilan SLide Promo -->
<style>
.slider {
    overflow: hidden;
    width: 100%;
    height: 400px;
    position: relative;
}

.slides {
    height: 100%;
}


.slider a {
    height: 100%;
    width: 100vw;
    display: block;
    transition: 0.3s;
    position: absolute;
}

.slider a.kiri {
    transform: translateX(-100vw);
    transition: 0.3s;
}

.slider a.active {
    transform: translateX(0vw);
    transition: 0.3s;
}

.slider a.kanan {
    transform: translateX(100vw);
    transition: 0.3s;
}

.slides a img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
}

.dot {
    width: 15px;
    height: 15px;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s;
    border: 1px solid black;
}

.dot.active {
    background-color: black;
}

@media (max-width: 768px) {
    .slider {
        width: 100%;
        height: auto;
    }
}
</style>
<div class="show-flex-ke-hide">
    <div class="slider">
        <div class="slides">
            <a href="<?= base_url('product/side-table-ilena-cabana/1') ?>" class="active"><img
                    src="<?= base_url('img/benner/benner1.png') ?>" alt="Slide 1"></a>
            <a href="<?= base_url('product/side-table-ilena-cabana/1') ?>" class="kanan"><img
                    src="<?= base_url('img/benner/benner1.png') ?>" alt="Slide 2"></a>
            <a href="<?= base_url('product/side-table-ilena-cabana/1') ?>" class="kanan"><img
                    src="<?= base_url('img/benner/benner1.png') ?>" alt="Slide 3"></a>
        </div>
        <div class="dots">
            <div class="dot active" onclick="currentSlide(0)"></div>
            <div class="dot" onclick="currentSlide(1)"></div>
            <div class="dot" onclick="currentSlide(2)"></div>
        </div>
    </div>
</div>
<script>
let slideIndex = 0;
const slides = document.querySelectorAll('.slides a');
const dots = document.querySelectorAll('.dot');

function showSlide(index) {
    const totalSlides = slides.length;
    // if (index < 0) slideIndex = totalSlides - 1;
    if (index >= totalSlides) {
        slideIndex = 0;
        slides.forEach((slide, i) => {
            if (i === 0) {
                slide.classList.add('active')
                slide.classList.remove('kiri')
            } else {
                slide.classList.remove('active')
                slide.classList.remove('kiri')
                slide.classList.add('kanan')
            }
        });
    } else {
        slides.forEach((slide, i) => {
            if (i < slideIndex) {
                slide.classList.add('kiri');
                slide.classList.remove('active');
                slide.classList.remove('kanan');
            } else if (i === slideIndex) {
                slide.classList.add('active');
                slide.classList.remove('kanan');
                slide.classList.remove('kiri');
            } else {
                slide.classList.remove('active');
                slide.classList.add('kanan');
                slide.classList.remove('kiri');
            }
        });
    }

    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === slideIndex);
    });
}

function currentSlide(index) {
    slideIndex = index;
    showSlide(slideIndex);
}

// Auto slide 
setInterval(() => {
    slideIndex++;
    showSlide(slideIndex);
}, 5000);
</script>

<!-- End Tampilan Side Promo -->
<!-- bagian windows -->
<div class="show-flex-ke-hide p-5"
    style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/banner1-3 comp.png'); background-size: cover; width:100%; height:80svh;">
    <div>
        <h1 class="teks-besar mb-2">Modern & Stylish<br>Furniture</h1>
        <p class="teks-sedang py-1 px-2" style="color: white; background-color: black; width:180px; font-size:30px;">
            True To You
        </p>
        <div style="width:150px; height:20px; background-color:white;"></div>
    </div>
</div>
<!-- bagian HP -->
<div class="hide-ke-show-flex px-5"
    style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/banner1-3 comp.png'); background-size: cover; width:100%; height:60svh;">
    <div>
        <h1 class="teks-besar mt-5" style="font-size:34px;">Modern & Stylish<br>Furniture</h1>
        <p class="teks-sedang px-1" style="color: white; background-color: black; font-size:14px; width:80px;">True To
            You
        </p>
        <div style="width:60px; height:10px; background-color:white;"></div>
    </div>
</div>

<!-- Bagian Windows -->
<style>
.kontenLN {
    position: relative;
    flex: 2;
    background: url('./img/foto/0 comp.png');
    background-size: cover;
    background-position: center;
    corsor: pointer;
    transition-delay: 0.4s;
}

.overlayBed,
.overlayMeja {
    max-width: 0px;
    overflow: hidden;
    position: absolute;
    /* background: rgba(0, 0, 0, 0.5); */
    background: var(--orentua);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.6s ease;
    transition-delay: 0.4s;
    text-wrap: nowrap;
    corsor: pointer;
    border-radius: 100px;
    padding: 0.5em 0em;
    box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.5);
}

.kontenLN:hover .overlayBed,
.kontenLN:hover .overlayMeja {
    max-width: 300px;
    transition: all 0.8s ease;
    padding: 0.5em 2em;
}

.overlayBed {
    top: 360px;
    left: 220px;
}

.overlayBed p:first-child {
    font-size: 20px;
}

.overlayMeja {
    bottom: 200px;
    left: 690px;
}

.overlayMeja p:first-child {
    font-size: 20px;
}

.overlayBed a,
.overlayMeja a {
    text-decoration: none;
    color: white;
    font-weight: bold;
}
</style>
<div class="show-flex-ke-hide align-items-stretch" style="width:100%; height:80svh;">
    <a class="d-flex align-items-center ps-5" href="<?= base_url('product/bufet-tv-ilena-cabana') ?>"
        style="text-decoration:none; flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/11 comp.png'); background-size: cover; background-position:center;">
        <div>
            <p class="text-light">Make you feel</p>
            <h1 class="teks-besar text-light mb-5">Elegant</h1>
            <div style="width:120px; height:2px; background-color:white;"></div>
        </div>
    </a>
    <div style="flex:1;" class=" d-flex flex-column">
        <a class="d-flex align-items-center ps-5" href="<?= base_url('product/bufet-tv-ilena-cabana') ?>"
            style="text-decoration:none; flex: 1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/n1 comp.png'); background-size: cover; background-position:center;">
            <div>
                <h1 class="teks-besar text-light mb-3">Luxury</h1>
                <div class="d-flex gap-3">
                    <div style="width:20px; height:20px; background-color:white;"></div>
                    <div style="width:20px; height:20px; border: 2px solid white;"></div>
                </div>
            </div>
        </a>
        <a class="d-flex" style="text-decoration:none; flex: 1" href="<?= base_url('product/bufet-tv-ilena-cabana') ?>">
            <div class="d-flex align-items-center ps-5"
                style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/s comp.png'); background-size: cover; background-position:center;">
                <div>
                    <h1 class="teks-besar text-light mb-3">Simply</h1>
                    <div class="d-flex gap-3">
                        <div style="width:20px; height:20px; border: 2px solid white;"></div>
                        <div style="width:20px; height:20px; background-color:white;"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Bagian HP -->
<div class="hide-ke-show-flex flex-column pt-2 gap-2" style="width:100%; height:80svh;">
    <a href="<?= base_url('product/bufet-tv-ilena-cabana') ?>" class="d-flex align-items-center px-5"
        style="text-decoration:none; flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/11 comp.png'); background-size: cover; background-position:center;">
        <div>
            <p class="text-light ms-2 m-0" style="font-size:12px;">Make you feel</p>
            <h1 class="text-light ms-2" style="font-size:28px;">Elegant</h1>
            <div style="width:60px; height:1px; background-color:white;" class="m-2"></div>
        </div>
    </a>
    <a class="d-flex align-items-center px-5" href="<?= base_url('product/bufet-tv-ilena-cabana') ?>"
        style="text-decoration:none; flex: 1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/n1 comp.png'); background-size: cover; background-position:center;">
        <div>
            <h1 class="text-light ms-2" style="font-size:28px;">Luxury</h1>
            <div class="d-flex gap-1 ms-2">
                <div style="width:15px; height:15px; background-color:white;"></div>
                <div style="width:15px; height:15px; border: 1px solid white;"></div>
            </div>
        </div>
    </a>
    <a href="<?= base_url('product/bufet-tv-ilena-cabana') ?>" class="d-flex align-items-center px-5"
        style="text-decoration:none; flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/s comp.png'); background-size: cover; background-position:center;">
        <div>
            <h1 class="text-light ms-2" style="font-size:28px;">Simply</h1>
            <div class="d-flex gap-1 ms-2">
                <div style="width:15px; height:15px; border: 1px solid white;"></div>
                <div style="width:15px; height:15px; background-color:white;"></div>
            </div>
        </div>
    </a>
</div>
<!-- END BAGIAN HP -->

<!-- BAGIAN SLIDE -->
<div style="position: absolute; background-color: rgba(255, 0, 0, 0.5); height: 0px;" class="w-100" id="counter-slide">
    <div class="container" style="height: 0px;">
        <div class="d-flex flex-column align-items-center gap-2 pt-5" style="width:20px;">
            <p class="m-0 fw-bold" style="font-size:10px;">01</p>
            <div style="height:30px; width:2px; background-color:black;"></div>
            <p class="m-0 fw-bold" style="font-size:10px;">06</p>
        </div>
    </div>
</div>
<div class="scroll-home go1">
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <h1 class="teks-besar m-0">Industrial</h1>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Menjadi diri sendiri dengan berkreasi sesuai kata hati masihkah terasa sulit? Mari mulai
                    dengan berbenah ruang yang merefleksikan karakter diri. Industrial Series hadir untuk menjadi teman
                    yang tepat untuk berdiri tegak tanpa takut menjadi beda, unik, menawan dengan elegan.<Br>Mari mulai
                    dari sekarang, bersama Ilena.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/industrial/3 comp.png'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/industrial/2 comp.png'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/industrial/1 comp.png'); background-size: cover; background-position: center; height:100%; background-repeat: no-repeat;">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <h1 class="teks-besar m-0">Sorely</h1>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan logam tebal. Dibuat
                    dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan kenyamanan dan ketenangan
                    bagi setiap penghuninya. Kami berbagi semangat kami dengan nama Ilena.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/sorely/3 comp.png'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/sorely/2 comp.png'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/sorely/1 comp.png'); background-size: cover; background-position:center; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <h1 class="teks-besar m-0">Water Case</h1>
                <h1 style=" font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Secara spesial dirancang untuk memberikan kehangatan dan kenyamanan pada hunian rumah.
                    water case series hadir dengan menunjukkan kesederhanaan sebuah desain yang memiliki fungsi sesuai
                    dengan kebutuhan dan memperindah rumah dengan sentuhan interior yang bersahaja dalam balutan gaya
                    modern klasik ala Ilena Furniture.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/watercase/3 comp.png'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/watercase/2 comp.png'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/watercase/1 comp.png'); background-size: cover; background-position:center; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <h1 class="teks-besar m-0">Plint Base</h1>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Ilena memaknai minimalis sebagai mahakarya indah yang dibalut dalam kesederhanaan. Dengan
                    kepraktisan fungsinya, plint base menjawab kebutuhan furniture secara menyeluruh dan relevan hingga
                    dalam waktu berdekade lamanya. Inilah Classic modern yang Anda butuhkan</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/plintbase/4 comp.png'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/plintbase/2 comp.png'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/plintbase/1 comp.png'); background-size: cover; background-position:center; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <h1 class="teks-besar m-0">Cut Out</h1>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Kami percaya bahwa sebuah ruangan didesain dengan sepenuh hati akan memberikan energi
                    positif bagi setiap penghuninya. Bekal inilah yang membuat Ilena terus melakukan inovasi untuk
                    menghadirkan furniture terbaik bagi Anda. Cut Out hadir dengan series dalam balutan gaya minimalis
                    dengan mengadopsi budaya Jepang yang terkenal mengutamakan fungsi dan kepraktisan. Desainnya yang
                    sederhana dengan sedikit aksen memudahkan Anda untuk merawat dan menempatkan dalam segala konsep
                    ruang menjadi lebih sempurna dengan Cut Out series dari Ilena.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/cutout/1 comp.png'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/cutout/2 comp.png'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/cutout/4 comp.png'); background-size: cover; background-position:center; ">
        </div>
    </div>

    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <h1 class="teks-besar m-0">Orca</h1>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Merancang dengan sepenuh hati furniture bertemakan modern dengan sentuhan warna basic
                    yang menjadi aksen menonjol jadi ciri khas dari series Orca. Hadir dengan menonjolkan teksture khas
                    kayu yang unik dipadukan dengan finishing satin yang solid. Desain ini dipersembahkan untuk Anda
                    yang menyukai perabotan kayu dengan sentuhan modern masa kini.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/orca/3 comp.png'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/orca/2 comp.png'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/orca/1 comp.png'); background-size: cover; background-position:center; ">
        </div>
    </div>
</div>

<div class="scroll-home-input">
    <div>
        <input id="scrollhome1" type="radio" name="scrollHome" checked>
        <label for="scrollhome1"></label>
        <input id="scrollhome2" type="radio" name="scrollHome">
        <label for="scrollhome2"></label>
        <input id="scrollhome3" type="radio" name="scrollHome">
        <label for="scrollhome3"></label>
        <input id="scrollhome4" type="radio" name="scrollHome">
        <label for="scrollhome4"></label>
        <input id="scrollhome5" type="radio" name="scrollHome">
        <label for="scrollhome5"></label>
        <input id="scrollhome6" type="radio" name="scrollHome">
        <label for="scrollhome6"></label>
    </div>
</div>

<script>
const scrollHomeElm = document.querySelector('.scroll-home');
const counterSlideElm = document.getElementById('counter-slide');
const numCounterSlideElm = document.querySelectorAll('#counter-slide p');
// console.log(numCounterSlideElm)
const inputScrollHomeElm = document.querySelectorAll('input[name="scrollHome"]');
// console.log(scrollHomeElm.children);
scrollHomeElm.onscroll = () => {
    let x = scrollHomeElm.scrollLeft;
    // console.log(x);
    const widthInnerStg = window.innerWidth / 2;
    if (x % window.innerWidth < widthInnerStg) {
        counterSlideElm.style.opacity = 1 - ((x % widthInnerStg) / widthInnerStg) / 1;
        counterSlideElm.style.transform = 'translateX(-' + (x % widthInnerStg) + 'px)';
        console.log('translateX(-' + (x % widthInnerStg) + ')')
    } else {
        counterSlideElm.style.opacity = ((x % widthInnerStg) / widthInnerStg) / 1;
        counterSlideElm.style.transform = 'translateX(-' + (widthInnerStg - (x % widthInnerStg)) + 'px)';
        console.log('translateX(-' + (widthInnerStg - (x % widthInnerStg)) + ')')
    }

    // console.log(Math.floor(x / (window.innerWidth * 90 / 100)))
    inputScrollHomeElm.forEach(inputnya => {
        inputnya.removeAttribute('checked');
    });
    inputScrollHomeElm[Math.floor(x / (window.innerWidth * 90 / 100))].setAttribute('checked', '');

    numCounterSlideElm[0].innerHTML = '0' + (Math.floor(x / (window.innerWidth * 90 / 100)) + 1)
    numCounterSlideElm[1].innerHTML = '0' + inputScrollHomeElm.length;
}
inputScrollHomeElm.forEach((inputnya, indInput) => {
    inputnya.addEventListener('change', () => {
        // console.log('input ' + indInput + ' berubah')
        scrollHomeElm.scrollLeft = window.innerWidth * indInput
    });

});
</script>
<!-- END BAGIAN SERIES  -->



<!-- Bagian Desktop -->

<div class="show-flex-ke-hide align-items-stretch" style="width:100%; height:80svh; position:relative;">
    <div class="d-flex align-items-center" style="flex:1;">
        <div class="p-5">
            <p class="mb-1 ms-1" style="letter-spacing:1em;">ILENA</p>
            <h1 class="teks-besar" style="font-size:80px;">On The Fance,</h1>
            <h1 style="color:#7B441c; font-size:80px;" class="teks-besar">Let's Look New Arrival
            </h1>
        </div>
    </div>
    <div class="kontenLN">
        <div class="overlayBed">
            <a href="https://example.com/detail-bed" class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Bed Cabana</p>
                    <p class="m-0 fw-regular">Cabana</p>
                </div>
                <i class="material-icons">arrow_forward</i>
            </a>
        </div>
        <div class="overlayMeja">
            <a href="https://example.com/detail-bed" class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Side Table</p>
                    <p class="m-0 fw-regular">Cabana</p>
                </div>
                <i class="material-icons">arrow_forward</i>
            </a>
        </div>
    </div>
</div>
</div>

<!-- Bagian Hp -->
<div class="hide-ke-show-flex align-items-stretch" style="width:100%; height:40svh; position:relative;">
    <div class="d-flex align-items-center" style="flex:1;">
        <div class="px-5">
            <p class="mb-1 ms-1" style="letter-spacing:1em;">ILENA</p>
            <h1 class="teks-sedang" style="font-size:40px;">On The Fance,</h1>
            <h1 style="color:#7B441c; font-size:40px;" class="teks-sedang">Let's Look New Arrival
            </h1>
        </div>
    </div>
</div>
<div class="hide-ke-show-flex align-items-stretch" style="width:100%; height:40svh; position:relative;">
    <div class="w-100"
        style="height:40svh; flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/0 comp.png'); background-size: cover; background-position:center;">
    </div>
</div>

<!-- Bagian Windows -->
<div class="show-flex-ke-hide align-items-stretch" style="width:100%; height:40svh;">
    <a href="<?= base_url('product/bufet-tv-ilena-cabana') ?>" style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/foto/inaugural comp.png');
    background-size: cover; background-position:top;">
    </a>
    <div style="flex:1; background-color:#7B441c; position:relative;">
        <div class="d-flex p-5 h-100 w-100 text-light" style="flex:1; position:absolute;">
            <div style="flex:1;" class="h-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="mb-3 teks-sedang fw-bold">Inaugural Season</h3>
                    <p>Ilena hadir pertama kali dengan memperkenalkan 10 series terbaik. Terinspirasi dari berbagai
                        hal
                        baik dengan harapan dapat membawa kebaikan dalam setiap furniture Kami, untuk Anda.</p>
                </div>
            </div>
            <div class="d-flex h-100 align-items-end justify-content-end" style="width:100px;">
                <a onclick="modalseries()" class="border-bottom gap-2 d-flex align-items-center kesana"
                    style="cursor: pointer;">
                    <p class="m-0">Lihat series</p><i class="material-icons text-light">arrow_forward</i>
                </a>
            </div>
        </div>
    </div>
    <div style="flex:1; background-color:white;" class="d-flex flex-column justify-content-center align-items-center">
        <p class="teks-sedang">EST</p>
        <h1 class=" mb-2 teks-besar" style="text-weight:600px; font-size:100px; color:  #7B441c;">2024</h1>
        <p class="m-0 fw-bold" style=" letter-spacing:1em;">ILENA</p>
    </div>
</div>

<!-- Bagian HP -->
<div class="hide-ke-show-flex" style="width:100%; height:40svh;">
    <div style="flex:1; background-color:#7B441c; position:relative;">
        <div class="d-flex p-5 h-100 w-100 text-light flex-column" style="flex:1; position:absolute;">
            <div style="flex:1;" class="h-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="mb-2 teks-besar fw-bold" style="font-size:36px;">Inaugural Season</h3>
                    <p>Ilena hadir pertama kali dengan memperkenalkan 6 series terbaik. Terinspirasi dari berbagai
                        hal
                        baik dengan harapan dapat membawa kebaikan, untuk Anda.</p>
                </div>
            </div>
            <div class="d-flex h-100 align-items-end justify-content-end w-100">
                <a onclick="modalseries()" class="border-bottom gap-2 d-flex align-items-center kesana"
                    style="cursor: pointer;">
                    <p class="m-0">Lihat series</p><i class="material-icons text-light">arrow_forward</i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Modal Series -->
<div id="modal-series" onclick="closemodalseries()" class="d-none justify-content-center align-items-center"
    style="z-index:2; position:fixed; top:0; left:0; width:100%; height:100svh; background-color:rgba(0,0,0,0.8);">
    <div style="border-radius:4px;" class="p-5 gap-2 show-block-ke-hide">
        <div class="gap-2 Mseries ">
            <a href="/product?koleksi=sorely"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Sorely</p>
            </a>
            <a href="/product?koleksi=cabana"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Cabana</p>
            </a>
            <a href="/product?koleksi=orca" class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Orca</p>
            </a>
            <a href="/product?koleksi=water-case"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Water Case</p>
            </a>
            <a href="/product?koleksi=plint-base"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Plint Base</p>
            </a>
            <a href="/product?koleksi=cutout"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Cutout</p>
            </a>
            <a href="/product?koleksi=industrial"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Industrial</p>
            </a>
            <a href="/product?koleksi=metal-frame"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Metal Frame</p>
            </a>
            <a href="/product?koleksi=cody" class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">COdy</p>
            </a>
            <a href="/product?koleksi=socoplate"
                class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
                <p class="m-0 fw-bold">Socoplate</p>
            </a>
        </div>
    </div>
    <div class="hide-ke-show-block" style="width: 70vw;">
        <a href="/product?koleksi=sorely" style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Sorely</p>
        </a>
        <a href="/product?koleksi=cabana" style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Cabana</p>
        </a>
        <a href="/product?koleksi=cody" style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Cody</p>
        </a>
        <a href="/product?koleksi=water-case"
            style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Water Case</p>
        </a>
        <a href="/product?koleksi=plint-base"
            style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">1
            <p class="m-0 fw-bold">Plint Base</p>
        </a>
        <a href="/product?koleksi=cutout" style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Cutout</p>
        </a>
        <a href="/product?koleksi=industrial"
            style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Industrial</p>
        </a>
        <a href="/product?koleksi=metal-frame"
            style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Metal Frame</p>
        </a>
        <a href="/product?koleksi=cody" style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Cody</p>
        </a>
        <a href="/product?koleksi=socoplate" style="border-bottom: 1px solid grey; text-decoration: none; color: white;"
            class="p-4 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Socoplate</p>
        </a>
    </div>
</div>



<script>
const modalseriesELM = document.getElementById('modal-series');

function closemodalseries() {
    modalseriesELM.classList.remove('d-flex');
    modalseriesELM.classList.add('d-none');
    document.body.style.overflow = 'auto';
}

function modalseries() {
    modalseriesELM.classList.add('d-flex');
    modalseriesELM.classList.remove('d-none');
    document.body.style.overflow = 'hidden';

}
</script>

<!-- Bagian Windows -->
<div class="container show-flex-ke-hide gap-3 my-5" style="width:100%; height:60svh;">
    <a href="/product?ruang=tamu" style="flex: 1; text-decoration:none; color:black;" class="d-flex flex-column">
        <img src="./img/foto/livingroom1 comp.png" alt="" class="w-100 mb-3"
            style="flex: 1; width: 100%; object-fit: cover;">
        <div class="d-flex gap-4 justify-content-center mb-2" style="height: fit-content">
            <h5 style="font-weight: 500;" class="m-0">L</h5>
            <h5 style="font-weight: 500;" class="m-0">I</h5>
            <h5 style="font-weight: 500;" class="m-0">V</h5>
            <h5 style="font-weight: 500;" class="m-0">I</h5>
            <h5 style="font-weight: 500;" class="m-0">N</h5>
            <h5 style="font-weight: 500;" class="m-0">G</h5>
        </div>
        <h5 class="text-center fw-bold" style="font-size: 13px; height: fit-content">ROOM</h5>
    </a>
    <a href="/product?ruang=tidur" style="flex: 1; text-decoration:none; color:black;" class="d-flex flex-column">
        <img src="./img/foto/bedroom cody comp.png" alt="" class="w-100 mb-3"
            style="flex: 1; width: 100%; object-fit: cover;">
        <div class="d-flex gap-4 justify-content-center mb-2" style="height: fit-content">
            <h5 style="font-weight: 500;" class="m-0">B</h5>
            <h5 style="font-weight: 500;" class="m-0">E</h5>
            <h5 style="font-weight: 500;" class="m-0">D</h5>
        </div>
        <h5 class="text-center fw-bold" style="font-size: 13px; height: fit-content">ROOM</h5>
    </a>
    <a href="/product?ruang=keluarga" style="flex: 1; text-decoration:none; color:black;" class="d-flex flex-column">
        <img src="./img/foto/ruangtamu1 comp.png" alt="" class="w-100 mb-3"
            style="flex: 1; width: 100%; object-fit: cover;">
        <div class="d-flex gap-4 justify-content-center mb-2" style="height: fit-content">
            <h5 style="font-weight: 500;" class="m-0">L</h5>
            <h5 style="font-weight: 500;" class="m-0">O</h5>
            <h5 style="font-weight: 500;" class="m-0">U</h5>
            <h5 style="font-weight: 500;" class="m-0">N</h5>
            <h5 style="font-weight: 500;" class="m-0">G</h5>
            <h5 style="font-weight: 500;" class="m-0">E</h5>
        </div>
        <h5 class="text-center fw-bold" style="font-size: 13px; height: fit-content">ROOM</h5>
    </a>
</div>

<!-- Bagian HP -->
<style>
#scroll-set {
    width: 100%;
    overflow: scroll;
    scroll-snap-type: x mandatory;
}

#scroll-set::-webkit-scrollbar {
    display: none;
}
</style>
<div class="hide-ke-show-block" id="scroll-set">
    <div class="d-flex gap-1 px-5 pt-5 pb-4" style="width: fit-content;">
        <a href="/product?ruang=tamu" class="bg-primary"
            style="width: 80vw; text-decoration:none; color:black; scroll-snap-align: center; display: block; height: 100px;">
            <img src="<?= base_url('/img/foto/livingroom1 comp.png') ?>" alt=""
                style=" height: auto; width: 100%; border-radius:4px;" class="mb-2">
            <div class="d-flex gap-4 justify-content-center mb-2">
                <h5 style="font-weight: 500;" class="m-0">L</h5>
                <h5 style="font-weight: 500;" class="m-0">I</h5>
                <h5 style="font-weight: 500;" class="m-0">V</h5>
                <h5 style="font-weight: 500;" class="m-0">I</h5>
                <h5 style="font-weight: 500;" class="m-0">N</h5>
                <h5 style="font-weight: 500;" class="m-0">G</h5>
            </div>
            <h5 class="text-center text-secondary fw-bold" style="font-size: 13px">ROOM</h5>
        </a>
        <a href="/product?ruang=tidur"
            style="width: 80vw; text-decoration:none; color:black; scroll-snap-align: center; display: block;">
            <img src="<?= base_url('/img/foto/bedroom cody comp.png') ?>" alt=""
                style=" height: auto; width: 100%; border-radius:4px;" class="mb-2">
            <div class="d-flex gap-4 justify-content-center mb-2">
                <h5 style="font-weight: 500;" class="m-0">B</h5>
                <h5 style="font-weight: 500;" class="m-0">E</h5>
                <h5 style="font-weight: 500;" class="m-0">D</h5>
            </div>
            <h5 class="text-center text-secondary fw-bold" style="font-size: 13px">ROOM</h5>
        </a>
        <a href="/product?ruang=keluarga"
            style="width: 80vw; text-decoration:none; color:black; scroll-snap-align: center; display: block;">
            <img src="<?= base_url('/img/foto/ruangtamu1 comp.png') ?>" alt=""
                style=" height: auto; width: 100%; border-radius:4px;" class="mb-2">
            <div class="d-flex gap-4 justify-content-center mb-2">
                <h5 style="font-weight: 500;" class="m-0">L</h5>
                <h5 style="font-weight: 500;" class="m-0">O</h5>
                <h5 style="font-weight: 500;" class="m-0">U</h5>
                <h5 style="font-weight: 500;" class="m-0">N</h5>
                <h5 style="font-weight: 500;" class="m-0">G</h5>
                <h5 style="font-weight: 500;" class="m-0">E</h5>
            </div>
            <h5 class="text-center text-secondary fw-bold" style="font-size: 13px">ROOM</h5>
        </a>
    </div>
</div>

<!-- Bagian Windows Scroll -->
<style>
.carousel-indicators button {
    width: 12px;
    height: 12px;
    background-color: #fff;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .carousel-inner img {
        height: 40vh;
        object-fit: cover;
    }

    .carousel-indicators {
        bottom: 10px;
    }

    .findout {
        font-size: 14px;
        padding: 8px 16px;
    }

    .p-5 {
        padding: 2rem !important;
    }
}
</style>
<div id="scrollDuluAnjay" class="carousel slide show-flex-ke-hide" data-bs-interval="2000" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#scrollDuluAnjay" data-bs-slide-to="0" class="active" aria-current="true"
            aria-label="Geser 1"></button>
        <button type="button" data-bs-target="#scrollDuluAnjay" data-bs-slide-to="1" aria-label="Geser 2"></button>
        <button type="button" data-bs-target="#scrollDuluAnjay" data-bs-slide-to="2" aria-label="Geser 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div style="position:relative;">
                <div class="d-flex flex-column w-100 h-100 justify-content-center align-items-center"
                    style="position:absolute;">
                    <a href="/product" class="findout">Find Out More</a>
                </div>
                <img src="<?= base_url('/img/foto/find out more watercase comp.png') ?>" class="d-block w-100"
                    style="object-fit: cover; object-position: top;" alt="Geser 1">
            </div>
        </div>
        <div class="carousel-item">
            <div style="position:relative;">
                <div class="d-flex flex-column w-100 h-100 justify-content-center align-items-center"
                    style="position:absolute;">
                    <a href="https://ilenafurniture.net/" class="findout">Inventori Collection</a>
                </div>
                <img src="<?= base_url('/img/foto/find out more watercase comp.png') ?>" class="d-block w-100"
                    style=" object-fit: cover; object-position: top;" alt="Geser 2">
            </div>
        </div>
        <div class="carousel-item">
            <div style="position:relative;">
                <div class="d-flex flex-column w-100 h-100 justify-content-center align-items-center"
                    style="position:absolute;">
                    <a href="/product" class="findout">Find Out More</a>
                </div>
                <img src="<?= base_url('/img/foto/find out more watercase comp.png') ?>" class="d-block w-100"
                    style=" object-fit: cover; object-position: top;" alt="Geser 3">
            </div>
        </div>
    </div>
</div>
<!-- End Bagian Windows Scroll -->






<?= $this->endSection(); ?>