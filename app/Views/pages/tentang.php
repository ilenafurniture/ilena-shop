<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
.container-toko::-webkit-scrollbar {
    display: none;
}

.container-toko {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: 15px;
    padding-bottom: 10px;
    height: 100px;
    width: 100%;
}

.item-toko {
    display: flex;
    flex-direction: row;
    background-color: white;
    border-radius: 12px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 5px;
    transition: transform 0.3s ease-in-out;
}

.item-toko:hover {
    transform: translateY(-5px);
}

.item-toko img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.item-toko .nama {
    font-size: 1.2rem;
    font-weight: bold;
    letter-spacing: -0.5px;
    margin-bottom: 5px;
}

.item-toko .alamat {
    font-size: 12px;
    color: #666;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .container-toko {
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 10px;
        height: 100px;
    }

    .item-toko {
        width: 100%;
    }

    .item-toko img {
        height: 100%;
        border-radius: 12px;
        object-fit: cover;
        width: 100%;
    }

    .item-toko .nama {
        font-size: 14px;
    }

    .item-toko .alamat {
        font-size: 11px;
        font-style: normal;
        line-height: 1.2;
    }

    #map {
        height: 300px;
    }
}

@media (max-width: 480px) {
    .item-toko .nama {
        font-size: 11px;
    }

    .item-toko .alamat {
        font-size: 8px;
        font-style: normal;
        line-height: 1.2;
    }

    .container-toko {
        padding-left: 5px;
        padding-right: 5px;
    }
}
</style>
<div>
    <!-- <img style="width: 100%; height: 50vh; object-fit: cover;" src="../img/foto/gambar-hero2.webp" alt="Hero Image"> -->
    <!-- Lokasi Toko -->

    <div id="mitrakami" class="container pt-4 pb-5" style="background-color: rgb(230, 230, 230); border-radius: 10px;">
        <h2 class="teks-sedang mb-4 text-center">Mitra Kami</h2>
        <div id="map" style="flex: 1; height: 400px; border-radius: 8px;" class="mb-3"></div>
        <div class="container-toko">
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/KxVrTxNAJv8ub8YJA"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Jempol Baru Furniture</h3>
                    <p class="alamat m-0">Jl. R. M. Said No.4, RW.6, Keprabon, Kec. Banjarsari, Kota Surakarta</p>
                </a>
                <div class="p-2">
                    <img src="<?= base_url('/img/fototoko/tokojempol.png') ?>" style="width: max-content;"
                        alt="Jempol Baru Furniture" class="client-logo">
                </div>
            </div>

            <div class="item-toko">
                <div class="p-4">
                    <a href="https://maps.app.goo.gl/cBQGbHzKCgyoTAM29"
                        style="flex: 1; text-decoration: none; color: black;">
                        <h3 class="nama">Sumber Abadi Furniture</h3>
                        <p class="alamat m-0">Jl. Magelang No.km7, Mlati Beningan, Sendangadi, Kec. Mlati, Kabupaten
                            Sleman,
                            Daerah Istimewa Yogyakarta</p>
                    </a>
                </div>
                <div class="p-2">
                    <img src="<?= base_url('/img/fototoko/tokosumberabadi.png') ?>" style="width: max-content;"
                        alt="Sumber Abadi Furniture" class="client-logo">
                </div>
            </div>

            <div class="item-toko">
                <a href="https://maps.app.goo.gl/2f6uDgjkd9SSwRnC8"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">SURI Meubel Furniture</h3>
                    <p class="alamat m-0">2C79+R2C, Jl. MH Thamrin, Sekayu, Kec. Semarang Tengah, Kota Semarang, Jawa
                        Tengah</p>
                </a>
                <div class="p-2">
                    <img src="<?= base_url('/img/fototoko/tokosuri.png') ?>" style="width: max-content;"
                        alt="SURI Meubel Furniture" class="client-logo">
                </div>
            </div>

            <div class="item-toko">
                <a href="https://maps.app.goo.gl/EJYp9pEmPZ3a7XASA"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Home Gallery Furniture</h3>
                    <p class="alamat m-0">Jl. Puncak Permai Utara I A No.5, Babatan, Kec. Wiyung, Surabaya, Jawa Timur
                    </p>
                </a>
                <div class="p-2">
                    <img src="<?= base_url('/img/fototoko/tokohome.png') ?>" alt="Toko Kayu Jaya" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/BPDJUK8MyRBFrJGW8"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Pari Anom Jaya Furniture</h3>
                    <p class="alamat m-0">Jl. Puncak Permai Utara I A No.5, Babatan, Kec. Wiyung, Surabaya, Jawa Timur
                    </p>
                </a>
                <div class="p-2">
                    <img src="<?= base_url('/img/fototoko/tokoparianom.png') ?>" style="width: max-content;"
                        alt="Toko Kayu Jaya" class="client-logo">
                </div>
            </div>
        </div>
    </div>




    <div class="pb-4">
        <div class="container py-4">
            <h1 class="teks-sedang mb-4 text-center">Cerita Kami</h1>
            <p class="text-justify" style="line-height: 1.8;">
                Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan CV Catur Bhakti Mandiri yang telah
                berdiri sejak 30 tahun.
                Ilena menandai dimulainya bisnis ritel dan interior. Dengan melebarnya industri yang didorong oleh
                kebutuhan konsumen,
                kami melakukan berbagai inovasi, keberlanjutan serta keinginan untuk terus konsisten berada di dekat
                hati konsumen
                dengan furniture berkualitas.
            </p>
        </div>

        <!-- Bagian Windows -->
        <div class="container show-block-ke-hide">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img class="w-100 h-auto rounded-3 shadow-lg" src="<?= base_url('/img/foto/tt.jpg') ?>"
                        alt="Urban Design" style="object-fit: cover;">
                </div>
                <div class="col-md-6">
                    <h2 class="mb-5" style="font-size: 2.5rem;">Crafted to Urban Design</h2>
                    <p class="text-justify">
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang yang
                        menjadi indah dalam kenangan.
                        Keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat urban yang cocok untuk
                        segala suasana.
                        Kami percaya bahwa setiap ruang kosong memiliki cerita yang diukir indah oleh individu dan
                        relasinya sebagai bentuk representasi tersendiri.
                        Bersama Ilena, wujudkan keindahan interior ruang impian Anda.
                    </p>
                </div>
            </div>
        </div>

        <div class="container show-block-ke-hide mt-5">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-5" style="font-size: 2.5rem;">Profil Perusahaan</h2>
                    <p class="text-justify">
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang, Jawa
                        Tengah.
                        Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi terhadap
                        keseimbangan kebutuhan konsumen
                        dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu
                        yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
                <div class="col-md-6">
                    <img class="w-100 h-auto rounded-3 shadow-lg"
                        src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="Company Profile"
                        style="object-fit: cover; filter: grayscale(1);">
                </div>
            </div>
        </div>
        <!-- END Bagian Windows -->

        <!-- Bagian HP (Mobile view) -->
        <div class="container hide-ke-show-block">
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <img class="w-100 h-auto rounded-3 shadow-lg" src="<?= base_url('/img/foto/tt.jpg') ?>"
                        alt="Urban Design Mobile" style="object-fit: cover;">
                </div>
                <div class="col-12">
                    <h2 class="mb-3" style="font-size: 2.5rem;">Crafted to Urban Design</h2>
                    <p class="text-justify">
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang yang
                        menjadi indah dalam kenangan.
                        Keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat urban yang cocok untuk
                        segala suasana.
                        Kami percaya bahwa setiap ruang kosong memiliki cerita yang diukir indah oleh individu dan
                        relasinya sebagai bentuk representasi tersendiri.
                        Bersama Ilena, wujudkan keindahan interior ruang impian Anda.
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <img class="w-100 h-auto rounded-3 shadow-lg"
                        src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="Company Profile Mobile"
                        style="object-fit: cover; filter: grayscale(1);">
                </div>
                <div class="col-12">
                    <h3 class="mb-3" style="font-size: 2.5rem;">Profil Perusahaan</h3>
                    <p class="text-justify">
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang, Jawa
                        Tengah.
                        Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi terhadap
                        keseimbangan kebutuhan konsumen
                        dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu
                        yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
            </div>
        </div>
    </div>


    <hr>

    <!-- Bagian Klien -->
    <div class="container pt-4 pb-5">
        <h2 class="teks-sedang mb-4 text-center">Our Clients</h2>
        <div class="d-flex gap-4 align-items-center justify-content-center flex-wrap">
            <img src="<?= base_url('../img/logo/thelandofnod.webp'); ?>" alt="The Land of Nod" width="150px"
                class="client-logo">
            <img src="<?= base_url('../img/logo/crateandbarrel.webp'); ?>" alt="Crate and Barrel" width="150px"
                class="client-logo">
            <img src="<?= base_url('../img/logo/westelm.webp'); ?>" alt="West Elm" width="150px" class="client-logo">
            <img src="<?= base_url('../img/logo/williamssonoma.webp'); ?>" alt="Williams Sonoma" width="150px"
                class="client-logo">
        </div>
    </div>

    <hr>



    <script>
    // Map Initialization
    var map = L.map('map', {
        center: [-7.614529, 110.712246],
        zoom: 6.5,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        touchZoom: false
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 25
    }).addTo(map);

    var customIcon = L.icon({
        iconUrl: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e84a49" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
            <path d="M21 10c0 7.941-9 13-9 13S3 17.941 3 10a9 9 0 1 1 18 0z"></path>
            <circle cx="12" cy="10" r="3"></circle>
        </svg>
    `),
        iconSize: [18, 18],
        iconAnchor: [10, 10],
        popupAnchor: [0, 0]
    });

    var stores = [{
        name: "Jempol Baru Furniture",
        coords: [-7.565618587313341, 110.82431800071647]
    }, {
        name: "Sumber Abadi Furniture",
        coords: [-7.743324302431505, 110.3624866828005]
    }, {
        name: "Home Gallery Furniture",
        coords: [-7.2886288976515985, 112.67261790823825]
    }, {
        name: "Pari Anom Jaya Furniture",
        coords: [-7.264405907300976, 112.79562668327448]
    }, {
        name: "Suri Mebel Semarang",
        coords: [-6.985425332274277, 110.41746260495303]
    }];

    stores.forEach(store => {
        var marker = L.marker(store.coords, {
            icon: customIcon
        }).addTo(map);
        marker.bindPopup(
            `<b>${store.name}</b>
         `
        );
        marker.on('mouseover', function() {
            marker.openPopup();
        });
        marker.on('mouseout', function() {
            marker.closePopup();
        });
    });
    </script>

    <?= $this->endSection(); ?>