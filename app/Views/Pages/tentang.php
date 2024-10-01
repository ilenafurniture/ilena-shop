<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<img style="width: 100%; height: 30svh; object-fit: cover;" src="../img/foto/gambar-hero2.webp" alt="">
<div class="container py-4">
    <h1 class="teks-besar mb-3">Cerita Kami</h1>
    <p style="text-align: justify;">Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan
        CV Catur
        Bhakti
        Mandiri yang telah berdiri
        sejak 30 tahun. Ilena menandai dimulainya bisnis ritel dan interior. Dengan melebarnya industri yang
        didorong oleh kebutuhan konsumen, kami melakukan berbagai inovasi, keberlanjutan serta keinginan untuk
        terus
        konsisten berada di dekat hati konsumen dengan furniture berkualitas.</p>
</div>

<!-- Bagian Windows -->
<div class="container show-block-ke-hide">
    <div class="d-flex align-items-center">
        <div style="flex: 1;">
            <img style="width: 100%; aspect-ratio: 16/9; object-fit:cover; background-position: bottom; "
                src="<?= base_url('/img/foto/tt.jpg') ?>" alt="">
        </div>
        <div style="flex: 1;">
            <div style="padding-left: 3em;">
                <h1 class="teks-besar mb-3">Crafted to Urban Design</h1>
                <p style="text-align: justify;">Ilena hadir menjadi teman untuk menciptakan banyak
                    kesan dan
                    pesan
                    dalam
                    setiap sudut ruang yang menjadi
                    indah dalam kenangan. keberhasilan Ilena merupakan usaha menghadirkan furniture khas
                    masyarakat
                    urban
                    yang cocok untuk segala suasana. kami percaya bahwa setiap ruang kosong memiliki cerita yang
                    diukir
                    indah oleh individu dan relasinya sebagai bentuk representasi tersendiri. bersama Ilena
                    wujudkan
                    keindahan interior ruang impian.
                </p>
            </div>
        </div>
    </div>
</div>
<div class="container show-block-ke-hide mb-5">
    <div class="d-flex align-items-center">
        <div style="flex: 1;">
            <div style="padding-right: 3em;">
                <h1 class="teks-besar mb-3">Profil Perusahaan</h1>
                <p style="text-align: justify; ">CV Catur Bhakti Mandiri merupakan produsen kayu
                    ternama
                    Indonesia
                    yang
                    berada di Semarang, Jawa Tengah.
                    Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi
                    terhadap
                    keseimbangan kebutuhan konsumen dan kesediaan sumber daya selama puluhan tahun lamanya.
                    Produk
                    kami
                    terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga, perkantoran &
                    perhotelan
                    berbahan dasar kayu yang bersumber dari hutan berkelanjutan.
                </p>
            </div>
        </div>
        <div style="flex: 1;">
            <img style="width: 100%; aspect-ratio: 16/9; object-fit:cover; filter: grayscale(1)"
                src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="">
        </div>
    </div>
</div>

<!-- ENd Bagian Windows -->

<!-- bagian HP -->
<div class="container hide-ke-show-block">
    <div>
        <div style="flex: 1;" class="mb-2">
            <img style="width: 100%; aspect-ratio: 16/9; object-fit:cover; background-position: bottom; border-radius:4px; "
                src="<?= base_url('/img/foto/tt.jpg') ?>" alt="">
        </div>
        <div style="flex: 1;">
            <h1 class="teks-besar mb-3">Crafted to Urban Design</h1>
            <p style="text-align: justify;">Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan
                dalam
                setiap sudut ruang yang menjadi
                indah dalam kenangan. keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat
                urban
                yang cocok untuk segala suasana. kami percaya bahwa setiap ruang kosong memiliki cerita yang
                diukir
                indah oleh individu dan relasinya sebagai bentuk representasi tersendiri. bersama Ilena wujudkan
                keindahan interior ruang impian.
            </p>
        </div>
    </div>
    <div>
        <div style="flex: 1;" class="mb-2">
            <img style="width: 100%; aspect-ratio: 16/9; object-fit:cover; filter: grayscale(1); border-radius:4px;"
                src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="">
        </div>
        <div style="flex: 1;">
            <h3 class="teks-besar mb-3">Profil Perusahaan</h3>
            <p style="text-align: justify;">CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia
                yang
                berada di Semarang, Jawa Tengah.
                Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi terhadap
                keseimbangan kebutuhan konsumen dan kesediaan sumber daya selama puluhan tahun lamanya. Produk
                kami
                terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga, perkantoran &
                perhotelan
                berbahan dasar kayu yang bersumber dari hutan berkelanjutadiv
            </p>
        </div>
    </div>
</div>
<!-- END Bagian HP -->



<!-- <div style="flex: 1; background-image: url('/img/foto/l1.webp'); background-size: cover;background-color: rgba(0,0,0, 0.5);
  background-repeat:no-repeat; background-blend-mode: color;" class=" py-5">
    <h1 class="text-center mb-0" style="font-size:42px; font-weight: 600; color:white;">Our Partners</h1>
    <div class="d-flex">
        <div class="wrapper">
            <a class="item item1" style="text-decoration:none; color:black;">
                <img src="../img/foto/Tentangperusahaan.JPG" alt="">
                <p class="m-1 align-content-lg-start fw-bold" style="font-size:14px; color:white;">Toko Berkah
                </p>
                <p class="m-1  fw-medium" style="font-size:14px; color:white;">Kab Semarang</p>
            </a>
            <a class="item item2" style="text-decoration:none; color:black;">
                <img src="https://i.ibb.co.com/Vxrr12p/nopic.webp" alt="">
                <p class="m-1 fw-bold" style="font-size:14px; color:white;">Toko Serbaguna</p>
                <p class="m-1  fw-medium" style="font-size:14px; color:white;">Semarang</p>
            </a>
            <a class="item item3" style="text-decoration:none; color:black;">
                <img src="../img/foto/gambar-hero2.webp" alt="">
                <p class="m-1 fw-bold" style="font-size:14px; color:white;">Toko Melati</p>
                <p class="m-1  fw-medium" style="font-size:14px; color:white;">Semarang</p>
            </a>
            <a class="item item4" style="text-decoration:none; color:black;">
                <img src="https://i.ibb.co.com/YQHq3D2/Whats-App-Image-2024-08-06-at-12-09-23-1782181c.jpg" alt="">
                <p class="m-1 fw-bold" style="font-size:14px; color:white;">PT. Catur Bhakti Mandiri</p>
                <p class="m-1  fw-medium" style="font-size:14px; color:white;">Kendal</p>
            </a>
            <a class="item item5" style="text-decoration:none; color:black;">
                <img src="https://surveyikm.fosan.id/foto/1678940177292-image.jpg" alt="">
                <p class="m-1 fw-bold" style="font-size:14px; color:white;">PT.Tri Cahya Purnama</p>
                <p class="m-1  fw-medium" style="font-size:14px; color:white;">Semarang</p>
            </a>
        </div>
    </div>
</div> -->

<hr>
<div class="container pt-4 pb-5">
    <h1 class="teks-besar mb-4 text-center">Our Clients</h1>
    <div class="d-flex gap-4 align-items-center justify-content-center" style="flex-wrap: wrap;">
        <img src="img/clients/crate&barel logo.png" alt="" width="100px">
        <img src="img/clients/the land of nod logo.png" alt="" width="100px">
        <img src="img/clients/westalm logo.png" alt="" width="100px">
        <img src="img/clients/williamsonoma logo.png" alt="" width="100px">
    </div>
</div>
<?= $this->endSection(); ?>