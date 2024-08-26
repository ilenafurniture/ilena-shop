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

<div id="login-modal">
    <div style="position: fixed; background-color: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100vw; height: 100svh; z-index: 99;"
        class="show-flex-ke-hide justify-content-center align-items-center">
        <div style="width: fit-content; height: fit-content;  overflow: hidden; position:relative;">
            <div style="position: absolute;"
                class="w-100 h-100 d-flex flex-column justify-content-between align-items-center">
                <div class="d-flex justify-content-end w-100 py-1 px-3">
                    <p class="m-0 d-block" style="cursor: pointer; font-size:18px; font-weight:bold; color:white;"
                        onclick="closeLoginModel()">X</p>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <a href="/register" class="btn-lonjong putih mb-2">Daftar Sekarang</a>
                    <p class="text-center" style="color:#fcf7da;">Sudah punya akun? <a href="/login"
                            class="btn-teks-aja" style="display: inline; color: white">Login akun</a></p>
                </div>
            </div>
            <img src="<?= base_url('/img/foto/diskon.webp') ?>" style="width: 400px; height: 400px; object-fit: cover"
                alt="">
        </div>
    </div>
    <div style="position: fixed; background-color: white; bottom: 0; left: 0; width: 100%; height: fit-content; z-index: 99;"
        class="hide-ke-show-flex flex-column justify-content-center align-items-center p-4">
        <div class="d-flex justify-content-end w-100" style="position: relative; margin-bottom: -10px;">
            <button class="btn-teks-aja" onclick="closeModalVoucher()">X</button>
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
<div style="background-color: #f5f5f5;">
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= base_url('/img/foto/gambar-hero.webp') ?>" alt="Gambar Hero" class="d-block w-100" style="
                height: 60svh;
                object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="<?= base_url('/img/foto/gambar-hero2 edit.webp') ?>" alt="Gambar Hero" class="d-block w-100"
                    style="
                
                height: 60svh;
                object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="Gambar Hero" class="d-block w-100"
                    style="
                height: 60svh;
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

    <div class="container baris-ke-kolom py-5">
        <div style="flex: 1">
            <img src="<?= base_url('/img/foto/ly1.webp') ?>" alt="" class="w-100"
                style="height: 100%; object-fit: cover; border-radius:4px;">
        </div>
        <div style="flex: 1" class="d-flex flex-column">
            <div style="flex:1;" class="px-2">
                <h1 class="teks-besar">About Ilena</h1>
                <p>With decades of experience in the exceptional
                    wooden furniture industry
                </p>
                <p>selama berdekade lamanya telah berkecimpung di
                    bidang furniture kayu berkualitas
                </p>
            </div>
            <div class="py-4 px-2"
                style="flex:1; background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('./img/foto/gambar-hero2 edit.webp'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; border-radius:4px; width:100%; height:100%;">
                <div class="ps-4">
                    <h1 class=" teks-sedang" style="color:white;">Crafted to urban Design</h1>
                    <p style="color:white;">Creating memorable impressions and messages
                        in every corner of your space, turning it into a
                        beautiful memory. Our success comes from
                        providing furniture that perfectly fits urban
                        lifestyles and is suitable for any setting.</p>
                    <p style="color:white;">Menciptakan banyak kesan dan pesan dalam
                        setiap sudut ruang yang menjadi indah dalam
                        kenangan. Keberhasilan Kami merupakan usaha
                        menghadirkan furniture khas masyarakat urban
                        yang cocok untuk segala suasana.</p>
                </div>
            </div>
        </div>
    </div>
    <div>
        <img src="<?=base_url('/img/foto/layer2.webp') ?>" alt="" class="w-100" style="object-fit:cover; height:60svh;">
    </div>
    <div class="container py-5">
        <h1 class="teks-besar d-flex justify-content-center py-4">Unification Process</h1>
        <div class="d-flex" style="display: flex; flex-wrap: wrap;">
            <div class="d-flex" style="flex: 1;">
                <img src="<?=base_url('') ?>/img/foto/layer2.webp" alt="" class="w-100"
                    style="object-fit: cover; height: 60svh; width: 100%; border-radius:4px;">
            </div>
            <div style="flex: 1;" class="d-flex flex-column px-4">
                <p style="text-align: justify;">Our production facilities are spread across Central Java,
                    Indonesia. The main site is located in Gedung Pani, covering
                    an area of 9,000 m². Additionally, we have another facility in
                    the BSB area, also spanning 9,000 m². To further enhance
                    our production capacity, we've expanded to a 12,000 m²
                    area in Kaliwungu. This strategic distribution is supported by
                    the trust of local residents, who join us in creating premium
                    furniture. With a team of 300 dedicated employees, we are
                    committed to bringing our shared vision to life at Ilena.</p>

                <p style="text-align: justify;">Lokasi produksi kami tersebar di Jawa Tengah, Indonesia.
                    Tempat produksi utama berada di Gedung Pani, Jawa
                    tengah dengan luas area 9000 m². Kemudian ditunjang juga
                    dengan tempat produksi lain di kawasan BSB seluas 9000
                    m². Untuk memaksimalkan produksi, kami merambah ke
                    daerah lain tepatnya di Kaliwungu dengan area seluas
                    12.000 m². Persebaran lokasi produksi ini juga dibarengi
                    dengan kepercayaan masyarakat sekitar kepada kami untuk
                    bergerak bersama menghasilkan furniture unggulan.
                    Sebanyak 300 karyawan telah bersama kami mewujudkan
                    mimpi bersama Ilena.</p>
            </div>
        </div>

    </div>
    <div>
        <img src="<?=base_url('/img/foto/l1.webp') ?>" alt="" class="w-100"
            style="height: 60svh; object-fit: cover; border-radius:4px;">
    </div>
    <div class="container baris-ke-kolom align-items-stretch py-5" style="display: flex; flex-wrap: wrap;">
        <div class="limapuluh-ke-seratus d-flex flex-column" style="flex: 1; padding-right: 10px;">
            <h1 class="teks-besar-home">Vision</h1>
            <p style="margin-bottom: 20px;">Begin at home, a friendly smile blossoms<br>Mulai dari Rumah, Tercipta
                senyum ramah</p>
            <div class="w-100" style="flex: 1; position: relative; overflow: hidden;">
                <img src="<?=base_url('') ?>/img/foto/ly1.webp" alt=""
                    style="object-fit: cover; position: absolute; width: 100%; height: 100%;">
            </div>
        </div>
        <div class="limapuluh-ke-seratus" style="flex: 1; padding-left: 10px;">
            <img src="<?=base_url('/img/foto/gambar-hero2 edit.webp') ?>" alt=""
                style="object-fit: cover; width:100%; border-radius:4px;">
            <h1 class="teks-besar-home pt-2">Mission</h1>
            <p style="font-weight: bold;">Developing & sustaining customer loyalty</p>
            <p>Committing to customer satisfaction with exceptional service and premium products</p>
            <p>Menjadikan kepuasan konsumen sebagai prioritas melalui pelayanan prima dan produk unggulan</p>

            <p style="font-weight: bold;">Supporting the balance of environmental ecosystems</p>
            <p>Preserving the environment and supporting forest ecosystem sustainability by using materials with fully
                traceable origins.</p>
            <p>Menjaga lingkungan dan kelanjutan ekosistem hutan dengan hanya menggunakan material bahan yang jelas
                asal-usulnya.</p>

            <p style="font-weight: bold;">United by a common purpose</p>
            <p>Developing a unified and collaborative team that reflects company values to foster growth and achieve a
                common goal.</p>
            <p>Membangun tim yang solid dan bersinergi sesuai dengan nilai perusahaan untuk terus berkembang dan
                mencapai satu tujuan bersama.</p>
        </div>
    </div>

    <div
        style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('/img/foto/lbg.webp'); background-size: cover; background-position: center; color: white; padding: 50px;">
        <div class="container">
            <h1 class="teks-besar" style="font-size: 2.5rem; font-weight: bold;">Corporate Values</h1>
            <p>We are dedicated to continuous growth and innovation in creating exceptional products,
                with a core focus on being 'sustainable', as detailed in the following pillars:</p>
            <ul style="padding-left: 0;">
                <li><strong>Diversity</strong><br>Embracing all forms of differences and leveraging them as
                    inspiration to achieve common goals.<br>Toleransi terhadap segala bentuk perbedaan dan menjadikannya
                    sebagai
                    semangat dalam mencapai tujuan bersama.</li>
                <li><strong>Trustworthiness</strong><br>Maintaining the highest standards in both product quality and
                    customer service.<br>Menjaga kualitas produk dan pelayanan terhadap konsumen secara maksimal.</li>
                <li><strong>Modernity</strong><br>Pursuing modernization to enhance product efficiency.<br>Terus pada
                    modernisasi dengan menciptakan efisiensi terhadap produk.</li>
                <li><strong>Adaptability</strong><br>Integrating developments in science and technology that impact the
                    company's needs.<br>Terlibat dalam perkembangan zaman dan teknologi yang mempengaruhi perubahan
                    kebutuhan serta keinginan manusia.</li>
                <li><strong>Harmony</strong><br>Fostering a healthy and cohesive relationship between employees and the
                    company as an integral whole.<br>Menciptakan relasi yang sehat antara karyawan dan perusahaan
                    sebagai satu kesatuan yang tidak bisa terpisahkan.</li>
            </ul>
        </div>
    </div>


    <div class="py-5" style="padding: 50px 0;">
        <h1 class="teks-besar-home text-center" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 30px;">
            Clients
        </h1>
        <div class="text-center">
            <img src="./img/logo/crateandbarrel.webp" alt="Crate & Barrel" style="max-width: 200px; margin: 10px;">
            <img src="./img/logo/thelandofnod.webp" alt="The Land of Nod" style="max-width: 200px; margin: 10px;">
            <img src="./img/logo/westelm.webp" alt="West Elm" style="max-width: 200px; margin: 10px;">
            <img src="./img/logo/williamssonoma.webp" alt="Williams Sonoma" style="max-width: 200px; margin: 10px;">
        </div>
    </div>
    <hr>
    <div class="gap-2">
        <div class="container py-3">
            <div class="flex-column">
                <div class="d-flex px-5 gap-4">
                    <div style="flex:1;">
                        <img src="<?=base_url('/img/foto/ly1.webp') ?>" alt=""
                            style="object-fit: cover; width: 100%; height: 60svh; border-radius:4px;">
                    </div>
                    <div style="flex:1;">
                        <h1 class="teks-besar-home mb-3" style="font-size: 2rem; font-weight: bold;">About Us</h1>
                        <p style="text-align: justify;"> The story of Ilena began in 2024 under the umbrella of CV Catur
                            Bhakti Mandiri, a company established 30 years ago. Ilena marks the
                            company's entry into the retail and interior design business. As the
                            industry expands to meet consumer demands, we are committed to
                            innovation, sustainability, and maintaining a close connection with our
                            customers through high-quality furniture.
                        </p>
                        <p style="text-align: justify;">Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan
                            CV
                            Catur Bhakti Mandiri yang telah berdiri sejak 30 tahun. Ilena menandai
                            dimulainya bisnis ritel dan interior. Dengan melebarnya industri yang
                            didorong oleh kebutuhan konsumen, kami melakukan berbagai
                            inovasi, keberlanjutan serta keinginan untuk terus konsisten berada di
                            dekat hati konsumen dengan furniture berkualitas.</p>

                    </div>

                </div>
            </div>
        </div>

        <div class="container py-3">
            <div class="flex-column">
                <div class="d-flex px-5 gap-4">
                    <div style="flex:1;">
                        <h1 class="teks-besar-home mb-3" style="font-size: 2rem; font-weight: bold;">Company Profile
                        </h1>
                        <p style="text-align: justify;"> CV Catur Bhakti Mandiri is a leading wood manufacturer based in
                            Semarang,
                            Central Java, Indonesia. With 30 years of experience, we are committed to
                            delivering outstanding products while balancing consumer needs with
                            sustainable resource practices. Our extensive collection of wood-based
                            furniture is crafted for residential, office, and hospitality settings, all sourced
                            from certified sustainable forests
                            #Find Out More
                        </p>
                        <p style="text-align: justify;">CV Catur Bhakti Mandiri merupakan produsen kayu ternama
                            Indonesia
                            yang
                            berada di Semarang, Jawa Tengah. Selama 30 tahun lamanya berkomitmen
                            untuk selalu memberikan kualitas dan terintegrasi terhadap keseimbangan
                            kebutuhan konsumen dan kesediaan sumber daya selama puluhan tahun
                            lamanya. Produk kami terdiri dari beragam furniture untuk mewujudkan
                            interior ruang rumah tangga, perkantoran & perhotelan berbahan dasar kayu
                            yang telah bersertifikasi serta bersumber dari hutan berkelanjutan.
                            #Find Out More</p>

                    </div>
                    <div style="flex:1;">
                        <img src="<?=base_url('/img/foto/ly1.webp') ?>" alt=""
                            style="object-fit: cover; width: 100%; height: 60svh; border-radius:4px;">
                    </div>

                </div>
            </div>
        </div>
    </div>
    <hr>

    <div style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),url('/img/foto/home1.webp'); height: 80svh; background-size: cover; background-position: center;"
        class="text-light d-flex justify-content-center align-items-center w-100">
        <a href="/product" class="m-0"
            style="font-size: 1.5rem;  text-decoration: underline; color:white; ">#FindOutMore</a>
    </div>





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
                    <p class="m-0">Dibuat dari bahan ramah lingkungan yang tidak berbahaya bagi kelangsungan
                        manusia,
                        bumi, dan lingkungan</p>
                </div>
            </div>
            <div style="flex: 1" class="d-flex gap-4 align-items-center">
                <div style="width: 70px;" class="d-flex justify-content-center">
                    <img src="../img/seluruh indo oren1.png" alt="" style="width: 50px;">
                </div>
                <div style="flex:1;">
                    <p class="fw-bold mb-1" style="font-size: 20px;">Bebas kirim seluruh Indonesia</p>
                    <p class="m-0">Bekerjasama dengan mitra ekspedisi yang telah menjangkau pengiriman aman &
                        terpercaya
                        ke seluruh Indonesia</p>
                </div>
            </div>
        </div>
        <!-- </div> -->
        <!-- <img class="img-besar" style="height: 300px;" src=".<?=base_url('') ?>/img/foto/gambar-hero2 edit.webp" alt="Gambar Hero"> -->
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