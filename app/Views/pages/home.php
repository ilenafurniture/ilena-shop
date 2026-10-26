<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php
$homeAssets = is_array($homeAssets ?? null) ? $homeAssets : [];
$homeImage = static function (string $file, int $width = 1200) use ($homeAssets): string {
    $file = basename($file);
    $version = substr(hash('sha256', $file), 0, 12);
    $width = in_array($width, [640, 900, 1200, 1600], true) ? $width : 1200;
    if (!empty($homeAssets[$file]) && is_string($homeAssets[$file])) {
        return $homeAssets[$file];
    }
    return base_url('home-image/' . rawurlencode($file) . '?v=' . $version . '&w=' . $width);
};
?>
<?php if ($msg_active) { ?>
<div id="modal-voucher" class="ilena-home-popup ilena-home-popup--active" role="presentation">
    <div class="ilena-home-popup__backdrop" onclick="closeModalVoucher()">
        <section class="ilena-home-popup__dialog ilena-home-popup__dialog--voucher" role="dialog" aria-modal="true" aria-labelledby="voucher-popup-title" onclick="event.stopPropagation()">
            <button type="button" class="ilena-home-popup__close" onclick="closeModalVoucher()" aria-label="Tutup promo">&times;</button>
            <span class="ilena-home-popup__eyebrow">ILENA MEMBER BENEFIT</span>
            <h2 id="voucher-popup-title">Selamat datang di ruang yang lebih istimewa.</h2>
            <p>Klaim voucher diskon 5% Anda dan temukan furniture yang terasa true to you.</p>
            <small>*S&amp;K diskon berlaku 1 bulan sejak menjadi member.</small>
            <a href="/product" class="ilena-home-popup__primary">Jelajahi koleksi</a>
            <button type="button" class="ilena-home-popup__dismiss" onclick="closeModalVoucher()">Nanti saja</button>
        </section>
    </div>
</div>
<script>
function closeModalVoucher() {
    const voucherPopup = document.getElementById('modal-voucher');
    if (!voucherPopup) return;
    voucherPopup.classList.add('d-none');
    voucherPopup.classList.remove('ilena-home-popup--active');
}
</script>
<?php } ?>



<div id="login-modal" class="d-none" aria-hidden="true">
    <div class="ilena-home-popup__backdrop" onclick="closeLoginModel(event)">
        <section class="ilena-home-popup__dialog ilena-home-popup__dialog--membership" role="dialog" aria-modal="true" aria-labelledby="membership-popup-title">
            <button type="button" class="ilena-home-popup__close" onclick="closeLoginModel()" aria-label="Tutup promo">&times;</button>
            <div class="ilena-home-popup__image">
                <img loading="lazy" decoding="async" src="<?= $homeImage('1742444827619.webp') ?>" alt="Inspirasi interior Ilena Furniture">
                <span>Modern &amp; Stylish<br>Furniture <em>True to you</em></span>
            </div>
            <div class="ilena-home-popup__content">
                <span class="ilena-home-popup__eyebrow">ILENA MEMBERSHIP</span>
                <h2 id="membership-popup-title">Ruang pilihanmu, dengan keuntungan khusus.</h2>
                <p>Nikmati diskon 5% untuk pembelian pertama dan inspirasi furniture pilihan Ilena.</p>
                <a href="/register" class="ilena-home-popup__primary">Gabung membership</a>
                <p class="ilena-home-popup__login">Sudah punya akun? <a href="/login">Masuk</a></p>
                <button type="button" class="ilena-home-popup__dismiss" onclick="closeLoginModel()">Nanti saja</button>
            </div>
        </section>
    </div>
</div>

<div id="second-ad" class="d-none">
    <div style="position: fixed; background-color: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100vw; height: 100svh; z-index: 100;"
        class="show-flex-ke-hide justify-content-center align-items-center">
        <div style="width: fit-content; height: fit-content; overflow: hidden; position:relative;">
            <div style="position: absolute;"
                class="p-3 w-100 h-100 d-flex flex-column justify-content-between align-items-center">
                <div class="d-flex justify-content-end w-100 py-1 px-3">
                    <p class="m-0 d-block" style="cursor: pointer; font-size:18px; font-weight:bold; color:black;"
                        onclick="closeSecondAd()">X</p>
                </div>
                <div class="d-flex flex-column align-items-center position-relative w-100" style="height: 100%;">

                    <a href="/product?jenis=bundling" class="btn-popup text-center mt-2">
                        Lihat Produk
                    </a>
                </div>
            </div>
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445131798.webp') ?>"
                style="width: 400px; height: 400px; object-fit: cover; border-radius: 10px;" alt="">
        </div>
    </div>
</div>

<?php if (!session()->get('isLogin')) { ?>
<script>
const loginModalElm = document.getElementById('login-modal');
const secondAdElm = document.getElementById('second-ad');
let opened = false;
window.addEventListener('scroll', () => {
    const voucherModal = document.getElementById('modal-voucher');
    if (!loginModalElm || window.sessionStorage.getItem('close-login-modal') || (voucherModal && !voucherModal.classList.contains('d-none'))) return;
    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = maxScroll > 0 ? (window.scrollY / maxScroll) * 100 : 0;
    if (scrollPercent > 50 && !opened) {
        loginModalElm.classList.remove('d-none');
        loginModalElm.setAttribute('aria-hidden', 'false');
        document.body.classList.add('ilena-popup-open');
        opened = true;
    }
}, { passive: true });

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && loginModalElm && !loginModalElm.classList.contains('d-none')) closeLoginModel();
});

function closeLoginModel(event) {
    if (event && event.target !== event.currentTarget) return;
    if (!loginModalElm) return;
    loginModalElm.classList.add('d-none');
    loginModalElm.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('ilena-popup-open');
    window.sessionStorage.setItem('close-login-modal', '1');
    opened = false;
}

function closeSecondAd() {
    secondAdElm.classList.add("d-none");
    window.sessionStorage.setItem('close-second-ad', true);
    opened = false
}
</script>
<?php } ?>

<!--Tampilan Home Baru -->
<!-- Tampilan SLide Promo -->
<style>
.btn-popup {
    width: 400px;
    position: absolute;
    bottom: -17px;
    left: 50%;
    transform: translateX(-50%);
    padding: 5px 10px;
    text-decoration: none;
    color: #b37c20;
    background-color: #24513c;
    border-radius: 0px 0px 8px 8px;
    font-weight: bold;
}

.slider {
    overflow: hidden;
    width: 100%;
    height: auto;
    aspect-ratio: 1400 / 400;
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
    text-decoration: none;
    color: white;
    flex-shrink: 0;
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

.slides a picture {
    display: block;
    width: 100%;
    height: 100%;
}

.slides a img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slider .material-icons {
    font-size: 40px;
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
    width: 10px;
    height: 10px;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s;
    border: 1px solid #844709;
}

.dot.active {
    background-color: #844709;
}

.panah {
    position: absolute;
    top: 50%;
    /* transform: translateY(-50%); */
    display: flex;
    justify-content: space-between;
    gap: 10px;
    width: 100%;
    height: 0px;
    background-color: aqua;
    /* padding: 10px; */
}

@media (max-width: 700px) {
    .slider {
        aspect-ratio: 1080 / 1200;
    }

}
</style>

<div class="slider">
    <div class="slides">
        <?php foreach ($sliders as $ind_s => $slider): ?>
        <?php
            $sliderId = preg_replace('/[^0-9]/', '', (string) ($slider['id'] ?? ''));
            if ($sliderId === '') {
                continue;
            }
            $desktopVersion = substr(md5((string) ($slider['foto'] ?? '')), 0, 12);
            $mobileVersion = substr(md5((string) ($slider['foto_hp'] ?? '')), 0, 12);
            $desktopSrc = '/imgheader/' . $sliderId . '?v=' . $desktopVersion . '&format=webp';
            $mobileSrc = '/imgheaderhp/' . $sliderId . '?v=' . $mobileVersion . '&format=webp';
            $isFirstSlide = $ind_s === 0;
        ?>
        <a <?= !empty($slider['url']) ? 'href="' . esc($slider['url'], 'attr') . '"' : ''; ?>
            class="<?= $isFirstSlide ? 'active' : 'kanan'; ?>">
            <picture>
                <source media="(max-width: 700px)" srcset="<?= esc($mobileSrc, 'attr'); ?>">
                <img src="<?= esc($desktopSrc, 'attr'); ?>" alt="<?= esc('Inspirasi furniture Ilena' . ($ind_s > 0 ? ' — promosi ' . ($ind_s + 1) : ''), 'attr'); ?>" decoding="async"<?= $isFirstSlide ? ' fetchpriority="high"' : ' loading="lazy"'; ?>>
            </picture>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="dots">
        <?php foreach ($sliders as $key => $slider): ?>
        <div class="dot <?= $key === 0 ? 'active' : ''; ?>"></div>
        <?php endforeach; ?>
    </div>
</div>

<!-- <div class="slider">
    <div class="slides">
        <a href="/product?koleksi=industrial&jenis=coffee-table" class="active">
            <img class="ls" src="imgheader/1" alt="Slide 1">
            <img class="pt" src="imgheaderhp/1" alt="Slide 1">
        </a>
        <a href="/register" class="kanan">
            <img class="ls" src="imgheader/2" alt="Slide 2">
            <img class="pt" src="imgheaderhp/2" alt="Slide 2">
        </a>
        <a href="/product" class="kanan">
            <img class="ls" src="imgheader/3" alt="Slide 3">
            <img class="pt" src="imgheaderhp/3" alt="Slide 3">
        </a>
        <a class="kanan">
            <img class="ls" src="imgheader/4" alt="Slide 4">
            <img class="pt" src="imgheaderhp/4" alt="Slide 4">
        </a>
    </div>
    <div class="dots">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
    <div class="panah">
        <button class="btn-teks-aja item-panah"><i class="material-icons">chevron_left</i></button>
        <button class="btn-teks-aja item-panah"><i class="material-icons">chevron_right</i></button>
    </div>
</div> -->

<!-- End Tampilan Side Promo -->
<!-- bagian windows -->
<div class="p-5 ilena-home-hero">
    <div class="show-flex-ke-hide p-5 flex-column align-items-center justify-content-center"
        data-lazy-bg="<?= esc($homeImage('1742972542166.png'), 'attr'); ?>" style="background-size:cover;background-position:center;background-color:#f4f1ec; width:100%; height:80svh;">
        <p class="ilena-home-kicker">RUANG YANG MENCERITAKAN DIRI ANDA</p>
        <h1 class="teks-besar mb-2 text-center text-light">Modern & Stylish<br>Furniture</h1>
        <p class="text-handwrite py-1 px-2 text-light"
            style="font-size:30px; transform: rotate(-10deg) translate(100px, -20px);">True to you</p>
        <a href="/product" class="d-flex align-items-center gap-2 kesana">
            <p class="m-0 fw-bold">Telusuri</p><i class="material-icons text-light">arrow_forward</i>
        </a>
    </div>
    <!-- bagian HP -->
    <div class="hide-ke-show-flex px-5 flex-column justify-content-center"
        style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('<?= $homeImage('1742972542166.png', 900) ?>'); background-size: cover; width:100%; height:60svh;">
        <p class="ilena-home-kicker">RUANG YANG MENCERITAKAN DIRI ANDA</p>
        <h1 class="teks-besar text-light" style="font-size:34px;">Modern & Stylish<br>Furniture</h1>
        <p class="text-handwrite py-1 px-2 text-light"
            style="font-size:20px; transform: rotate(-10deg) translate(50px, -15px);">True to you</p>
        <a href="/product" class="btn-underline text-light" style="transform: translateY(100px);">
            <p class="m-0 fw-bold">Telusuri</p><i class="material-icons text-light">arrow_forward</i>
        </a>
    </div>
</div>

<!-- Bagian Windows -->
<style>
.kontenLN {
    position: relative;
    flex: 2;
    background: url('<?= $homeImage('1748239864605.jpg') ?>');
    background-size: cover;
    background-position: center;
    cursor: pointer;
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
    cursor: pointer;
    border-radius: 100px;
    padding: 0.5em 0em;
    box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.5);
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

/* Mobile */
.kontenLNMobile {
    position: relative;
    flex: 2;
    background: url('<?= $homeImage('1742446101353.webp') ?>');
    background-size: cover;
    background-position: center;
    cursor: pointer;
    transition-delay: 0.4s;
    height: 100%;
}

@media (max-width: 767px) {
    .teks-sedang {
        font-size: 30px;
    }

    .kontenLNMobile {
        /* height: 50vh; */
    }

    .overlayBed,
    .overlayMeja {
        position: absolute;
        opacity: 0;
        max-width: 0;
        padding: 0.5em 0.5em;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1rem;
        transition: 1s;
        border-radius: 50px;
    }

    .overlayBed.show,
    .overlayMeja.show {
        opacity: 1;
        height: 60px;
        max-width: 200px;
        padding: 0.5em 2em;
        transition: 1s;
    }

    .overlayBed p:first-child,
    .overlayMeja p:first-child {
        font-size: 14px;
        margin: 0;
    }

    .overlayBed a,
    .overlayMeja a {
        text-decoration: none;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
    }

    .overlayBed a i,
    .overlayMeja a i {
        font-size: 16px;
        margin-left: 0.5rem;
    }

    .overlayBed {
        top: 150px;
        left: 60px;
    }

    .overlayMeja {
        top: auto;
        right: 16px;
        bottom: 18px;
        left: 16px;
        width: fit-content;
        max-width: calc(100% - 32px);
    }
}
</style>
<div class="show-flex-ke-hide align-items-stretch ilena-home-mosaic" style="width:100%; height:80svh;">
    <a class="d-flex align-items-center ps-5 zoom-wrapper" href="<?= base_url('/product/bufet-tv-ilena-plint-base') ?>"
        style="text-decoration:none; flex:1; position: relative;">
        <img loading="lazy" decoding="async" src="<?= $homeImage('1748320128043.jpg') ?>"
            alt="Background Image"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
        <div>
            <p class="text-light">Make you feel</p>
            <h1 class="teks-besar text-light mb-5">Elegant</h1>
            <div style="width:120px; height:2px; background-color:white;"></div>
        </div>
    </a>
    <div style="flex:1;" class=" d-flex flex-column">
        <a class="d-flex align-items-center ps-5 zoom-wrapper"
            href="<?= base_url('/product/coffee-table-ilena-cody') ?>"
            style="text-decoration:none; flex: 1; position: relative;">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1748318610868.jpg') ?>"
                alt="Background Image"
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
            <div>
                <h1 class="teks-besar text-light mb-3">Luxury</h1>
                <div class="d-flex gap-3">
                    <div style="width:20px; height:20px; background-color:white;"></div>
                    <div style="width:20px; height:20px; border: 2px solid white;"></div>
                </div>
            </div>
        </a>
        <a class="d-flex" style="text-decoration:none; flex: 1" href="<?= base_url('product/bufet-tv-ilena-cabana') ?>">
            <div class="d-flex align-items-center ps-5 zoom-wrapper" style="flex:1; position: relative;">
                <img loading="lazy" decoding="async" src="<?= $homeImage('1742973475864.png') ?>"
                    alt="Background Image"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
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


<!-- <div class="show-flex-ke-hide align-items-stretch" style="width:100%; height:80svh;">
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
</div> -->




<!-- Bagian HP -->
<div class="hide-ke-show-flex flex-column pt-2 gap-2 ilena-home-mosaic-mobile" style="width:100%; height:80svh;">
    <a href="<?= base_url('/product/bufet-tv-ilena-plint-base') ?>" class="d-flex align-items-center px-5"
        data-lazy-bg="<?= esc($homeImage('1748320128043.jpg'), 'attr'); ?>" style="text-decoration:none; flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
        <div>
            <p class="text-light ms-2 m-0" style="font-size:12px;">Make you feel</p>
            <h1 class="text-light ms-2" style="font-size:28px;">Elegant</h1>
            <div style="width:60px; height:1px; background-color:white;" class="m-2"></div>
        </div>
    </a>
    <a class="d-flex align-items-center px-5" href="<?= base_url('/product/coffee-table-ilena-cody') ?>"
        data-lazy-bg="<?= esc($homeImage('1748318610868.jpg'), 'attr'); ?>" style="text-decoration:none; flex: 1; background-size:cover;background-position:center;background-color:#f4f1ec;">
        <div>
            <h1 class="text-light ms-2" style="font-size:28px;">Luxury</h1>
            <div class="d-flex gap-1 ms-2">
                <div style="width:15px; height:15px; background-color:white;"></div>
                <div style="width:15px; height:15px; border: 1px solid white;"></div>
            </div>
        </div>
    </a>
    <a href="<?= base_url('product/bufet-tv-ilena-cabana') ?>" class="d-flex align-items-center px-5"
        data-lazy-bg="<?= esc($homeImage('1742973475864.png'), 'attr'); ?>" style="text-decoration:none; flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
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
                <a href="<?= base_url('/product?koleksi=industrial') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Industrial</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Menjadi diri sendiri dengan berkreasi sesuai kata hati masihkah terasa sulit?
                    Mari mulai
                    dengan berbenah ruang yang merefleksikan karakter diri. Industrial Series hadir untuk
                    menjadi teman
                    yang tepat untuk berdiri tegak tanpa takut menjadi beda, unik, menawan dengan
                    elegan.<Br>Mari mulai
                    dari sekarang, bersama Ilena.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1742448909605.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1742448940409.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1748241007946.jpg'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; height:100%; background-repeat: no-repeat;">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=sorely') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Sorely</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan logam tebal.
                    Dibuat
                    dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan kenyamanan dan
                    ketenangan
                    bagi setiap penghuninya. Kami berbagi semangat kami dengan nama Ilena.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449015853.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449044439.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1742449070724.png'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=watercase') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Water Case</h1>
                </a>
                <h1 style=" font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Secara spesial dirancang untuk memberikan kehangatan dan kenyamanan pada hunian
                    rumah.
                    water case series hadir dengan menunjukkan kesederhanaan sebuah desain yang memiliki fungsi
                    sesuai
                    dengan kebutuhan dan memperindah rumah dengan sentuhan interior yang bersahaja dalam balutan
                    gaya
                    modern klasik ala Ilena Furniture.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449142002.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449164348.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1742449189905.png'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=plintbase') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Plint Base</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Ilena memaknai minimalis sebagai mahakarya indah yang dibalut dalam
                    kesederhanaan.
                    Dengan
                    kepraktisan fungsinya, plint base menjawab kebutuhan furniture secara menyeluruh dan relevan
                    hingga
                    dalam waktu berdekade lamanya. Inilah Classic modern yang Anda butuhkan</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449272365.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449295384.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1742449317998.png'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=    ') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Cut Out</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Kami percaya bahwa sebuah ruangan didesain dengan sepenuh hati akan memberikan
                    energi
                    positif bagi setiap penghuninya. Bekal inilah yang membuat Ilena terus melakukan inovasi
                    untuk
                    menghadirkan furniture terbaik bagi Anda. Cut Out hadir dengan series dalam balutan gaya
                    minimalis
                    dengan mengadopsi budaya Jepang yang terkenal mengutamakan fungsi dan kepraktisan. Desainnya
                    yang
                    sederhana dengan sedikit aksen memudahkan Anda untuk merawat dan menempatkan dalam segala
                    konsep
                    ruang menjadi lebih sempurna dengan Cut Out series dari Ilena.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449351540.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449375221.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1742449396827.png'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>

    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=orca') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Orca</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Merancang dengan sepenuh hati furniture bertemakan modern dengan sentuhan warna
                    basic
                    yang menjadi aksen menonjol jadi ciri khas dari series Orca. Hadir dengan menonjolkan
                    teksture khas
                    kayu yang unik dipadukan dengan finishing satin yang solid. Desain ini dipersembahkan untuk
                    Anda
                    yang menyukai perabotan kayu dengan sentuhan modern masa kini.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449442663.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1742449462506.png'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1742449486165.png'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>

    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=cody') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Cody</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Menghadirkan karakter Japandi yang hangat melalui garis desain bersih,
                    material kayu yang natural, dan proporsi yang ringan. Series Cody cocok untuk Anda
                    yang ingin menciptakan ruang modern, nyaman, dan tetap terasa homey untuk aktivitas
                    sehari-hari.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1748244053696.jpg'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1748244032628.jpg'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1748243821780.jpg'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>

    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=metalframe') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Metal Frame</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Dirancang untuk menghadirkan kesan modern industrial yang kokoh tanpa
                    mengurangi sisi elegan pada ruangan. Perpaduan rangka metal dan material kayu
                    memberi tampilan tegas, tahan lama, serta mudah dipadukan dengan berbagai konsep
                    interior masa kini.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    data-lazy-bg="<?= esc($homeImage('1748244560652.jpg'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
                <div
                    data-lazy-bg="<?= esc($homeImage('1748244523907.jpg'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:center;background-color:#f4f1ec;">
                </div>
            </div>
        </div>
        <div class="w-100"
            data-lazy-bg="<?= esc($homeImage('1748244474986.jpg'), 'attr'); ?>" style=" flex:1; background-size:cover;background-position:center;background-color:#f4f1ec; ">
        </div>
    </div>

    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=socoplate') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Socoplate</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Series Socoplate menonjolkan desain modern minimalis dengan aksen logam
                    sebagai highlight yang memperkuat karakter furniture. Sentuhan detailnya membuat
                    ruangan terlihat lebih rapi, berkelas, dan fungsional untuk kebutuhan rumah
                    modern.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/socoplate/'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/socoplate/'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/socoplate/'); background-size: cover; background-position:center; ">
        </div>
    </div>
    <div class="scroll-home-item">
        <div class="container d-flex py-5 gap-5" style="flex: 1">
            <div class="pt-4" style="width:20px; height: 20px; opacity: 0;"></div>
            <div style="flex:6;">
                <a href="<?= base_url('/product?koleksi=cabana') ?>" style="text-decoration:none; color:black;">
                    <h1 class="teks-besar m-0">Cabana</h1>
                </a>
                <h1 style="font-size: 20px; letter-spacing: 2em;" class="mb-3">SERIES</h1>
                <p class="m-0">Cabana hadir dengan bentuk simpel, storage maksimal, dan tampilan timeless
                    yang mudah menyatu dengan berbagai gaya interior. Pilihan tepat untuk Anda yang
                    membutuhkan furniture praktis, elegan, serta tetap relevan digunakan dalam jangka
                    panjang.</p>
            </div>
            <div class="d-flex gap-4" style="flex:4;">
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/cabana/'); background-size: cover; background-position:center;">
                </div>
                <div
                    style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/cabana/'); background-size: cover; background-position:center;">
                </div>
            </div>
        </div>
        <div class="w-100"
            style=" flex:1; background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('./img/series/cabana/'); background-size: cover; background-position:center; ">
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
        <input id="scrollhome7" type="radio" name="scrollHome">
        <label for="scrollhome7"></label>
        <input id="scrollhome8" type="radio" name="scrollHome">
        <label for="scrollhome8"></label>
        <input id="scrollhome9" type="radio" name="scrollHome">
        <label for="scrollhome9"></label>
        <input id="scrollhome10" type="radio" name="scrollHome">
        <label for="scrollhome10"></label>
    </div>
</div>

<script>
const scrollHomeElm = document.querySelector('.scroll-home');
const counterSlideElm = document.getElementById('counter-slide');
const numCounterSlideElm = document.querySelectorAll('#counter-slide p');
const inputScrollHomeElm = document.querySelectorAll('input[name="scrollHome"]');
if (scrollHomeElm && inputScrollHomeElm.length) {
    const slideWidth = () => scrollHomeElm.querySelector('.scroll-home-item')?.getBoundingClientRect().width || scrollHomeElm.clientWidth;
    let scrollTick = false;
    const syncHomeGallery = () => {
        const maxIndex = inputScrollHomeElm.length - 1;
        const index = Math.max(0, Math.min(maxIndex, Math.round(scrollHomeElm.scrollLeft / slideWidth())));
        inputScrollHomeElm.forEach((input, inputIndex) => { input.checked = inputIndex === index; });
        if (numCounterSlideElm.length >= 2) {
            numCounterSlideElm[0].textContent = String(index + 1).padStart(2, '0');
            numCounterSlideElm[1].textContent = String(inputScrollHomeElm.length).padStart(2, '0');
        }
        if (counterSlideElm) {
            counterSlideElm.style.opacity = '1';
            counterSlideElm.style.transform = 'none';
        }
        scrollTick = false;
    };
    scrollHomeElm.addEventListener('scroll', () => {
        if (!scrollTick) {
            window.requestAnimationFrame(syncHomeGallery);
            scrollTick = true;
        }
    }, { passive: true });
    inputScrollHomeElm.forEach((input, index) => {
        input.setAttribute('aria-label', 'Tampilkan koleksi ' + (index + 1));
        input.addEventListener('change', () => {
            if (input.checked) scrollHomeElm.scrollTo({ left: slideWidth() * index, behavior: 'smooth' });
        });
    });
    syncHomeGallery();
}
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
        <!-- <div class="overlayBed">
            <a href="/product?koleksi=cabana&jenis=king-bed+queen-bed+single-bed"
                class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Bed Cabana</p>
                    <p class="m-0 fw-regular">Cabana</p>
                </div>
                <i class="material-icons">arrow_forward</i>
            </a>
        </div> -->
        <!-- <div class="overlayMeja">
            <a href="/product?koleksi=cabana&jenis=side-table" class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Side Table</p>
                    <p class="m-0 fw-regular">Cabana</p>
                </div>
                <i class="material-icons">arrow_forward</i>
            </a>
        </div> -->
        <div class="overlayMeja" id="overlayMeja-desktop">
            <a href="/product?koleksi=cody&jenis=queen-bed" class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Bed Cody</p>
                    <p class="m-0 fw-regular">Cody</p>
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
<div class="hide-ke-show-flex align-items-stretch"
    style="width:100%; height:40svh; position:relative; min-height: 414px;">
    <div class="kontenLNMobile">
        <!-- <div class="overlayBed" id="overlayBed">
            <a href="/product?koleksi=cabana&jenis=king-bed+queen-bed+single-bed"
                class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Bed Cabana</p>
                    <p class="m-0 fw-regular">Cabana</p>
                </div>
                <i class="material-icons">arrow_forward</i>
            </a>
        </div> -->
        <div class="overlayMeja" id="overlayMeja-mobile">
            <a href="/product?koleksi=cody&jenis=queen-bed" class="d-flex gap-3 align-items-center">
                <div>
                    <p class="m-0 fw-bold">Bed Cody</p>
                    <p class="m-0 fw-regular">Cody</p>
                </div>
                <i class="material-icons">arrow_forward</i>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.kontenLN, .kontenLNMobile').forEach((section) => {
        const revealLink = section.querySelector('.overlayMeja');
        if (!revealLink) return;
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                revealLink.classList.add('show');
                currentObserver.unobserve(entry.target);
            });
        }, { threshold: 0.35 });
        observer.observe(section);
    });
});
</script>

<!-- Bagian Windows -->
<div class="show-flex-ke-hide align-items-stretch" style="width:100%; height:40svh; min-height: 373px;">
    <a href="<?= base_url('product/bufet-tv-ilena-cabana') ?>" data-lazy-bg="<?= esc($homeImage('1742449659675.webp'), 'attr'); ?>" style="flex:1; background-size:cover;background-position:top;background-color:#f4f1ec;">
    </a>
    <div style="flex:1; background-color:#7B441c; position:relative;">
        <div class="d-flex p-5 h-100 w-100 text-light flex-column" style="flex:1; position:absolute;">
            <div style="flex:1;" class="h-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="mb-3 teks-sedang fw-bold">Inaugural<br>Season</h3>
                    <p>Ilena hadir pertama kali dengan memperkenalkan 10 series terbaik. Terinspirasi dari berbagai
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
    <div style="flex:1; background-color:white;" class="d-flex flex-column justify-content-center align-items-center">
        <p class="teks-sedang">EST</p>
        <h1 class=" mb-2 teks-besar" style="font-weight:600px; font-size:100px; color:  #7B441c;">2024</h1>
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
                    <p>Ilena hadir pertama kali dengan memperkenalkan 10 series terbaik. Terinspirasi dari berbagai
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

<style>
.modalseriesbox {
    text-decoration: none;
    background-color: rgba(0, 0, 0, 0);
    color: white;
    border: 1px solid white;
    transition: 0.4s;
    flex: 1;
}

.modalseriesbox p {
    font-size: 20px;
    font-weight: 500;
    letter-spacing: 7px;
    text-transform: uppercase;
    text-wrap: nowrap;
    position: relative;
    transform: translateX(7px);
}

.modalseriesbox:hover {
    text-decoration: none;
    background-color: white;
    color: black;
    transition: 0.4s;
    /* border: 1px solid ; */
    /* background-color: white; */
}

.Mseries {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
}
</style>
<!-- Bagian Modal Series -->
<div id="modal-series" onclick="closemodalseries()" class="d-none justify-content-center align-items-center"
    style="z-index:2; position:fixed; top:0; left:0; width:100%; height:100svh; background-color:rgba(0,0,0,0.8);">
    <div style="border-radius:4px;" class="p-5 gap-2 show-flex-ke-hide flex-wrap justify-content-center">
        <a href="/product?koleksi=sorely" class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
            <p class="m-0 fw-bold">Sorely</p>
        </a>
        <a href="/product?koleksi=cabana" class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
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
        <a href="/product?koleksi=cutout" class="modalseriesbox p-5 d-flex justify-content-center align-items-center">
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
        <!-- <div class="gap-2 Mseries ">
        </div> -->
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

<style>
.zoom-wrapper {
    overflow: hidden;
    height: 100%;
    cursor: pointer;
}

.zoom-wrapper img {
    object-fit: cover;
    height: 100%;
    transition: transform 0.5s;
}

.zoom-wrapper:hover img {
    transform: scale(1.1);
}

#ruangan {
    height: 60svh;
}

@media(orientation: portrait) {
    #ruangan {
        max-height: 470px;
    }
}
</style>

<!-- Bagian Windows -->
<div id="ruangan" class="container show-flex-ke-hide gap-3 my-5 ilena-home-room-grid" style="width:100%;">
    <a href="/product?ruang=tamu" style="flex: 1; text-decoration:none; color:black;" class="d-flex flex-column">
        <div class="zoom-wrapper mb-2">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445399641.webp') ?>" alt=""
                style="flex: 1; width: 100%; object-fit: cover;">
        </div>
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
        <div class="zoom-wrapper mb-2">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445431983.webp') ?>" alt=""
                style="flex: 1; width: 100%; object-fit: cover;">
        </div>
        <div class="d-flex gap-4 justify-content-center mb-2" style="height: fit-content">
            <h5 style="font-weight: 500;" class="m-0">B</h5>
            <h5 style="font-weight: 500;" class="m-0">E</h5>
            <h5 style="font-weight: 500;" class="m-0">D</h5>
        </div>
        <h5 class="text-center fw-bold" style="font-size: 13px; height: fit-content">ROOM</h5>
    </a>
    <a href="/product?ruang=keluarga" style="flex: 1; text-decoration:none; color:black;" class="d-flex flex-column">
        <div class="zoom-wrapper mb-2">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445475511.webp') ?>" alt=""
                style="flex: 1; width: 100%; object-fit: cover;">
        </div>
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

<div class="hide-ke-show-block ilena-home-room-scroll" id="scroll-set">
    <div class="d-flex gap-1 px-5 pt-5 pb-4" style="width: fit-content;">
        <a href="/product?ruang=tamu" class="ilena-home-room-card"
            style="width: 80vw; text-decoration:none; color:black; scroll-snap-align: center; display: block;">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445542692.webp') ?>" alt=""
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
        <a href="/product?ruang=tidur" class="ilena-home-room-card"
            style="width: 80vw; text-decoration:none; color:black; scroll-snap-align: center; display: block;">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445603554.webp') ?>" alt=""
                style=" height: auto; width: 100%; border-radius:4px;" class="mb-2">
            <div class="d-flex gap-4 justify-content-center mb-2">
                <h5 style="font-weight: 500;" class="m-0">B</h5>
                <h5 style="font-weight: 500;" class="m-0">E</h5>
                <h5 style="font-weight: 500;" class="m-0">D</h5>
            </div>
            <h5 class="text-center text-secondary fw-bold" style="font-size: 13px">ROOM</h5>
        </a>
        <a href="/product?ruang=keluarga" class="ilena-home-room-card"
            style="width: 80vw; text-decoration:none; color:black; scroll-snap-align: center; display: block;">
            <img loading="lazy" decoding="async" src="<?= $homeImage('1742445633826.webp') ?>" alt=""
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




<div class="slider">
    <div class="slides">
        <a href="/product" class="active">
            <div style="position: absolute; z-index: 5; top: 0; left: 0; width: 100%; height: 100%"
                class="d-flex justify-content-center align-items-center flex-column container-find-out">
                <p class="find-out-text">Find Out More</p>
                <div class="d-flex flex-column align-items-center">
                    <div class="popup-text">See All Products</div>
                    <i class="material-icons"
                        style="font-size: 50px; transform: translateY(-22px); color: black;">arrow_drop_down</i>
                </div>
            </div>
            <img loading="lazy" decoding="async" style="position: absolute; z-index: 4; top: 0; left: 0; width: 100%; height: 100%" class="d-block"
                src="<?= $homeImage('1742445684882.webp') ?>"
                alt="Slide 2">
        </a>
        <a href="https://ilenafurniture.net/" class="kanan">
            <img loading="lazy" decoding="async" class="ls"
                src="<?= $homeImage('1742445731467.png') ?>"
                alt="Slide 2">
            <img loading="lazy" decoding="async" class="pt"
                src="<?= $homeImage('1742445771003.png') ?>"
                alt="Slide 2">
        </a>
    </div>
    <div class="dots">
        <div class="dot active"></div>
        <div class="dot"></div>
    </div>
</div>
<style>
.container-find-out {
    background-color: rgba(0, 0, 0, 0);
    transition: 0.3s;
    pointer-events: none;
}

.container-find-out:hover {
    background-color: rgba(0, 0, 0, 0.6);
    transition: 0.3s;
}

.find-out-text {
    pointer-events: auto;
    position: absolute;
    z-index: 2;
    transition: transform 0.3s ease;
    font-size: 24px;
    font-weight: 500;
    letter-spacing: -1px;
    position: relative;
    transition: 0.7s;
    letter-spacing: normal;
}

.find-out-text:hover {
    /* font-size: 27px; */
    transition: 0.7s;
    letter-spacing: 2px;
}

.popup-text {
    background-color: black;
    color: white;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 10px 20px;
}

.find-out-text+.d-flex {
    z-index: 1;
    position: absolute;
    transform: translateY(-30px);
    max-height: 0px;
    overflow: hidden;
    transition: 0.3s;
    opacity: 0;
}

.find-out-text:hover+.d-flex {
    transition: 0.6s;
    max-height: 100px;
    opacity: 1;
}
</style>



<!-- <style>
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
</div> -->
<!-- End Bagian Windows Scroll -->

<script>
const sliders = document.querySelectorAll('.slider');
sliders.forEach((slider) => {
    const track = slider.querySelector('.slides');
    const slides = Array.from(track?.children ?? []);
    const dots = Array.from(slider.querySelectorAll('.dots .dot'));
    if (!track || slides.length < 2) return;

    let activeIndex = 0;
    const syncDots = () => {
        activeIndex = Math.max(0, Math.min(slides.length - 1, Math.round(track.scrollLeft / track.clientWidth)));
        dots.forEach((dot, index) => dot.classList.toggle('active', index === activeIndex));
    };
    const goTo = (index) => {
        activeIndex = (index + slides.length) % slides.length;
        track.scrollTo({
            left: activeIndex * track.clientWidth,
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
        });
        dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === activeIndex));
    };

    track.addEventListener('scroll', syncDots, { passive: true });
    dots.forEach((dot, index) => dot.addEventListener('click', () => goTo(index)));
    window.setInterval(() => goTo(activeIndex + 1), 7000);
});
</script>
<script>
(() => {
    const backgrounds = document.querySelectorAll('[data-lazy-bg]');
    if (!backgrounds.length) return;
    const loadBackground = (element) => {
        const url = element.getAttribute('data-lazy-bg');
        if (!url) return;
        element.style.backgroundImage = `url("${url.replace(/["\\]/g, '')}")`;
        element.removeAttribute('data-lazy-bg');
    };
    if (!('IntersectionObserver' in window)) {
        backgrounds.forEach(loadBackground);
        return;
    }
    const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            loadBackground(entry.target);
            currentObserver.unobserve(entry.target);
        });
    }, { rootMargin: '100px 0px' });
    backgrounds.forEach((element) => observer.observe(element));
})();
</script>
<?= $this->endSection(); ?>
