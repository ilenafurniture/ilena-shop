<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div>
    <img style="width: 100%; height: 50svh; object-fit: cover;" src="../img/foto/gambar-hero2.webp" alt="">
    <div class="pb-4">
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
        <div class="container show-block-ke-hide">
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
    </div>
</div>
<!-- END Bagian HP -->
<hr>
<div class="container pt-4 pb-5">
    <h1 class="teks-sedang mb-4 text-center">Our Clients</h1>
    <div class="d-flex gap-5 align-items-center justify-content-center" style="flex-wrap: wrap;">
        <img src="<?= base_url('../img/logo/thelandofnod.webp'); ?>" alt="" width=" 150px">
        <img src="<?= base_url('../img/logo/crateandbarrel.webp'); ?>" alt="" width="150px">
        <img src="<?= base_url('../img/logo/westelm.webp'); ?>" alt="" width="150px">
        <img src="<?= base_url('../img/logo/williamssonoma.webp'); ?>" alt="" width="150px">
    </div>
</div>
<?= $this->endSection(); ?>