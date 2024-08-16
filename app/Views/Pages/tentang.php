<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div>
    <img style="width: 100%; height: 50svh; object-fit: cover;" src="../img/foto/gambar-hero2.webp" alt="">
    <div class="container py-4">
        <h1 class="teks-besar mb-3">Cerita Kami</h1>
        <p style="text-align: justify;">Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan CV Catur Bhakti
            Mandiri yang telah berdiri
            sejak 30 tahun. Ilena menandai dimulainya bisnis ritel dan interior. Dengan melebarnya industri yang
            didorong oleh kebutuhan konsumen, kami melakukan berbagai inovasi, keberlanjutan serta keinginan untuk terus
            konsisten berada di dekat hati konsumen dengan furniture berkualitas.</p>
    </div>
    <!-- <img style="width: 100%; height: 80svh; object-fit: cover;"
        src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=2069&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
        alt=""> -->
    <div class="container py-4 mt-2">
        <div class="baris-ke-kolom">
            <div style="flex: 1;">
                <img style="width: 100%; aspect-ratio: 16/9; object-fit:cover; background-position: bottom; border-radius:8px;"
                    src="<?= base_url('/img/foto/tt.jpg') ?>" alt="">
            </div>
            <div style="flex: 1;">
                <h1 class="teks-besar mb-3">Crafted to Urban Design</h1>
                <!-- <p style="color: grey;" class="mb-3">ILENA FURNITURE</p> -->
                <p style="text-align: justify;">Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam
                    setiap sudut ruang yang menjadi
                    indah dalam kenangan. keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat
                    urban
                    yang cocok untuk segala suasana. kami percaya bahwa setiap ruang kosong memiliki cerita yang diukir
                    indah oleh individu dan relasinya sebagai bentuk representasi tersendiri. bersama Ilena wujudkan
                    keindahan interior ruang impian.
                </p>
            </div>
        </div>
    </div>
    <div class="container py-4 mt-2">
        <div class="baris-ke-kolom-reverse">
            <div style="flex: 1;">
                <h1 class="teks-besar mb-3">Profil Perusahaan</h1>
                <p style="text-align: justify;">CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang
                    berada di Semarang, Jawa Tengah.
                    Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi terhadap
                    keseimbangan kebutuhan konsumen dan kesediaan sumber daya selama puluhan tahun lamanya. Produk kami
                    terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga, perkantoran &
                    perhotelan
                    berbahan dasar kayu yang bersumber dari hutan berkelanjutan.
                </p>
            </div>
            <div style="flex: 1;">
                <img style="width: 100%; aspect-ratio: 16/9; object-fit:cover; border-radius:8px;"
                    src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="">
            </div>
        </div>
    </div>

    <!-- <div style="width: 100%; height: 80svh; position: absolute; background-color: rgba(0,0,0,0.7)"
        class="d-flex flex-column justify-content-center align-items-center">
        <h1 class="teks-sedang mb-3 text-center text-light">#FindOutMore</h1>
        <a href="/product" class="btn btn-outline-light">Ilena Furniture</a>
    </div>
    <img style="width: 100%; height: 80svh; object-fit: cover; object-position: bottom;"
        src="https://images.unsplash.com/photo-1617325247661-675ab4b64ae2?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
        alt=""> -->

    <div style="flex: 1;" class="p-5 show-block-ke-hide">
        <hr>
        <h1 class="text-center mb-0 p-1" style="font-size:42px; font-weight: 600;">Our Partners</h1>
        <div class="d-flex">
            <div class="wrapper">
                <a class="item item1" style="text-decoration:none; color:black;">
                    <img src="../img/foto/Tentangperusahaan.JPG" alt="">
                    <p class="m-1 align-content-lg-start fw-bold" style="font-size:14px;">Toko Berkah</p>
                    <p class="m-1  fw-medium" style="font-size:14px;">Kab Semarang</p>
                </a>
                <a class="item item2" style="text-decoration:none; color:black;">
                    <img src="https://i.ibb.co.com/Vxrr12p/nopic.webp" alt="">
                    <p class="m-1 fw-bold" style="font-size:14px;">Toko Serbaguna</p>
                    <p class="m-1  fw-medium" style="font-size:14px;">Semarang</p>
                </a>
                <a class="item item3" style="text-decoration:none; color:black;">
                    <img src="../img/foto/gambar-hero2.webp" alt="">
                    <p class="m-1 fw-bold" style="font-size:14px;">Toko Melati</p>
                    <p class="m-1  fw-medium" style="font-size:14px;">Semarang</p>
                </a>
                <a class="item item4" style="text-decoration:none; color:black;">
                    <img src="https://i.ibb.co.com/YQHq3D2/Whats-App-Image-2024-08-06-at-12-09-23-1782181c.jpg" alt="">
                    <p class="m-1 fw-bold" style="font-size:14px;">PT. Catur Bhakti Mandiri</p>
                    <p class="m-1  fw-medium" style="font-size:14px;">Kendal</p>
                </a>
                <a class="item item5" style="text-decoration:none; color:black;">
                    <img src="https://surveyikm.fosan.id/foto/1678940177292-image.jpg" alt="">
                    <p class="m-1 fw-bold" style="font-size:14px;">PT.Tri Cahya Purnama</p>
                    <p class="m-1  fw-medium" style="font-size:14px;">Semarang</p>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>