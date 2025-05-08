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
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
        width: 80px;
    }

    .item-toko .nama {
        font-size: 10px;
    }

    .item-toko .alamat {
        font-size: 8px;
        font-style: normal;
        line-height: 1;
    }

    #map {
        height: 200px;
    }
}

@media (max-width: 480px) {
    .item-toko .nama {
        font-size: 8px;
    }

    .item-toko .alamat {
        font-size: 6px;
        font-style: normal;
    }

    .container-toko {
        padding-left: 5px;
        padding-right: 5px;
    }

    .item-toko img {
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
        width: 70px;
    }

    #map {
        height: 200px;
    }
}
</style>
<div class="container">
    <div class="konten mx-auto">

        <!-- <img style="width: 100%; height: 50vh; object-fit: cover;" src="../img/foto/gambar-hero2.webp" alt="Hero Image"> -->
        <!-- Lokasi Toko -->
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Tentang Kami
                </li>
            </ol>
        </nav>

        <hr>
        <h2 class="teks-sedang mb-4 mt-4 text-center">Mitra Kami</h2>
        <hr class="my-3">
        <div id="map" style="flex: 1; height: 400px; border-radius: 8px;" class="mb-3"></div>
        <div class="container-toko">
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/KxVrTxNAJv8ub8YJA"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Jempol Baru Furniture</h3>
                    <p class="alamat m-0">Jl. R. M. Said No.4, RW.6, Keprabon, Kec. Banjarsari, Kota Surakarta
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742449816363.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Jempol Baru Furniture" class="client-logo">
                </div>
            </div>

            <div class="item-toko">
                <div class="p-4">
                    <a href="https://maps.app.goo.gl/cBQGbHzKCgyoTAM29"
                        style="flex: 1; text-decoration: none; color: black;">
                        <h3 class="nama">Sumber Abadi Furniture</h3>
                        <p class="alamat m-0">Jl. Magelang No.km7, Mlati Beningan, Sendangadi,
                            Kec. Mlati, Kabupaten Sleman, Daerah Istimewa Yogyakarta</p>
                    </a>
                </div>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742449853266.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Sumber Abadi Furniture" class="client-logo">
                </div>
            </div>

            <div class="item-toko">
                <a href="https://maps.app.goo.gl/2f6uDgjkd9SSwRnC8"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">SURI Meubel Furniture</h3>
                    <p class="alamat m-0">2C79+R2C, Jl. MH Thamrin, Sekayu, Kec. Semarang Tengah, Kota Semarang,
                        Jawa
                        Tengah</p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742449882169.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="SURI Meubel Furniture" class="client-logo">
                </div>
            </div>

            <div class="item-toko">
                <a href="https://maps.app.goo.gl/EJYp9pEmPZ3a7XASA"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Home Gallery Furniture</h3>
                    <p class="alamat m-0">Jl. Puncak Permai Utara I A No.5, Babatan, Kec. Wiyung, Surabaya, Jawa
                        Timur
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742449923158.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Home Gallery Furniture" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/BPDJUK8MyRBFrJGW8"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Pari Anom Jaya Furniture</h3>
                    <p class="alamat m-0">Jl. Bunguran No.45 Lantai 4, Bongkaran, Kec. Pabean Cantikan, Surabaya
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742449972596.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Pari Anom Jaya Furniture" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/BkiBFdCA4Rop3qHS7"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Puri Mabel & Interior</h3>
                    <p class="alamat m-0">Jl. Puri Anjasmoro Blk. H5 No.57, Tawangsari, Semarang, Jawa Tengah
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742450004899.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Puri Mabel & Interior" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Victoria Furnicenter</h3>
                    <p class="alamat m-0">Jl. Raya Menganti Karangan No.578, Babatan, Kec. Wiyung, Surabaya,
                        Jawa
                        Timur
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742450030786.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Victoria Furnicenter" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Tunggal Jaya Furniture</h3>
                    <p class="alamat m-0">Jl. Raya Menganti Karangan No.578, Babatan, Kec. Wiyung, Surabaya,
                        Jawa
                        Timur
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://img.ilenafurniture.com/image/1742450030786.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Tunggal Jaya Furniture" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Cipta Bangun Jaya Furniture</h3>
                    <p class="alamat m-0">Jl. Jend. Sudirman No.189, Tidar Sel., Kec. Magelang Sel., Kota Magelang, Jawa
                        Tengah
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://lh3.googleusercontent.com/p/AF1QipMWUmj6bwLhOk3oi1hkWM-kq86aSIa5cQ8IAIFW=w427-h240-k-no"
                        alt="Cipta Bangun Jaya Furniture" class="client-logo">
                </div>
            </div>
        </div>
        <hr>
        <h1 class="teks-sedang mb-4 mt-4 text-center">Cerita Kami</h1>
        <hr class="my-3">
        <p class="text-justify" style="line-height: 1.8;">
            Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan CV Catur Bhakti Mandiri yang telah
            berdiri sejak 30 tahun.
            Ilena menandai dimulainya bisnis ritel dan interior. Dengan melebarnya industri yang didorong oleh
            kebutuhan konsumen,
            kami melakukan berbagai inovasi, keberlanjutan serta keinginan untuk terus konsisten berada di dekat
            hati konsumen
            dengan furniture berkualitas.
        </p>

        <!-- Bagian Windows -->
        <div class="container show-block-ke-hide">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img class="w-100 h-auto rounded-3 shadow-lg"
                        src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Urban Design" style="object-fit: cover;">
                </div>
                <div class="col-md-6">
                    <h2 class="mb-5" style="font-size: 2.5rem;">Crafted to Urban Design</h2>
                    <p class="text-justify">
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang
                        yang
                        menjadi indah dalam kenangan.
                        Keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat urban yang cocok
                        untuk
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
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang,
                        Jawa
                        Tengah.
                        Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi
                        terhadap
                        keseimbangan kebutuhan konsumen
                        dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu
                        yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
                <div class="col-md-6">
                    <img class="w-100 h-auto rounded-3 shadow-lg"
                        src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Company Profile" style="object-fit: cover; filter: grayscale(1);">
                </div>
            </div>
        </div>
        <!-- END Bagian Windows -->

        <!-- Bagian HP (Mobile view) -->
        <div class="container hide-ke-show-block">
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <img class="w-100 h-auto rounded-3 shadow-lg"
                        src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Urban Design Mobile" style="object-fit: cover;">
                </div>
                <div class="col-12">
                    <h2 class="mb-3" style="font-size: 2.5rem;">Crafted to Urban Design</h2>
                    <p class="text-justify">
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang
                        yang
                        menjadi indah dalam kenangan.
                        Keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat urban yang cocok
                        untuk
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
                        src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?=  $apikey_img_ilena ?>"
                        alt="Company Profile Mobile" style="object-fit: cover; filter: grayscale(1);">
                </div>
                <div class="col-12">
                    <h3 class="mb-3" style="font-size: 2.5rem;">Profil Perusahaan</h3>
                    <p class="text-justify">
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang,
                        Jawa
                        Tengah.
                        Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi
                        terhadap
                        keseimbangan kebutuhan konsumen
                        dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu
                        yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
            </div>
        </div>


        <!-- Bagian Klien -->

        <hr>
        <h2 class="teks-sedang mb-4 mt-4 text-center">Our Clients</h2>
        <hr class="my-3">
        <div class="d-flex gap-4 align-items-center justify-content-center flex-wrap">
            <img src="https://img.ilenafurniture.com/image/1742450163897.webp/?apikey=<?=  $apikey_img_ilena ?>"
                alt="The Land of Nod" width="150px" class="client-logo">
            <img src="https://img.ilenafurniture.com/image/1742450190985.webp/?apikey=<?=  $apikey_img_ilena ?>"
                alt="Crate and Barrel" width="150px" class="client-logo">
            <img src="https://img.ilenafurniture.com/image/1742450222588.webp/?apikey=<?=  $apikey_img_ilena ?>"
                alt="West Elm" width="150px" class="client-logo">
            <img src="https://img.ilenafurniture.com/image/1742450249426.webp/?apikey=<?=  $apikey_img_ilena ?>"
                alt="Williams Sonoma" width="150px" class="client-logo">
        </div>

        <hr>
        <h1 class="teks-sedang mb-4 mt-4 text-center">Kontak Kami</h1>
        <hr class="my-3">
        <div class="py-4 gap-4 baris-ke-kolom">
            <div style="flex:1;">
                <!-- <h5><strong>Lokasi Kami</strong></h5> -->
                <div style="border-radius:1em; overflow:hidden; height:100%;" class="mb-2">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.687866383162!2d110.32868959999999!3d-7.0459182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7061e23055c8ed%3A0xa875b119e04372d4!2sCV.Catur%20Bhakti%20Mandiri!5e0!3m2!1sen!2sid!4v1723450895314!5m2!1sen!2sid"
                        style="border:0; width:100%; height:100%;  " allowfullscreen="" loading="lazy" zoom="80"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <p class="fw-normal" style="font-size:12px; color:red;"><strong>Lokasi:</strong> Jalan Lingkar Taman
                    Industri,
                    Jatibarang,
                    Mijen,
                    Kota
                    Semarang, Jawa Tengah</p>
            </div>
            <div style="flex:1;">
                <h5><strong>Customer Service</strong></h5>
                <p class="my-2" style="font-size:14px;">Silakan ajukan pertanyaan Anda dengan menghubungi layanan
                    customer service
                    kami di bawah ini:
                </p>
                <div class="d-flex gap-2 mb-4">
                    <div>
                        <p style="color:black; font-size: 14px; padding:0;" class="m-0">Email</p>
                        <p style="color:black; font-size: 14px; padding:0;" class="m-0">No. WhatsApp</p>
                    </div>
                    <div>
                        <a class="d-block" style="color:black; font-size: 14px; padding:0; text-decoration:none;"
                            href="mailto:cs@ilenafurniture.com">: cs@ilenafurniture.com</a>
                        <a class="d-block" style="color:black; font-size: 14px; padding:0; text-decoration:none;"
                            href="https://wa.me/+628112938158">: 08112938158</a>
                    </div>
                </div>

                <h5><strong>Temukan Jawaban Cepat</strong></h5>

                <div>
                    <a style="color:black; font-size: 14px;" href="<?= base_url('/faq#faqkedua') ?>"><strong>Apa metode
                            pembayaran yang
                            bisa
                            digunakan?</strong></a>
                </div>
                <div>
                    <a style="color:black; font-size: 14px;" href="<?= base_url('/faq#faqpertama') ?>"><strong>Bagaimana
                            cara melakukan
                            pemesanan & pembelian di website
                            Ilena?</strong></a>
                </div>
                <div>
                    <a href="<?= base_url('/faq') ?>" style="color:red; text-decoration:none;">Lihat Semua FAQ</a>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- <script>
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
    maxZoom: 30
}).addTo(map);

var customIcon = L.icon({
    iconUrl: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e84a49" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
            <path d="M21 10c0 7.941-9 13-9 13S3 17.941 3 10a9 9 0 1 1 18 0z"></path>
            <circle cx="12" cy="10" r="3"></circle>
        </svg>
    `),
    iconSize: [14, 14],
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
}, {
    name: "Puri Mabel & Interior",
    coords: [-6.9658085878072935, 110.39043865842983]
}, {
    name: "Victoria Furnicenter",
    coords: [-7.310519605654715, 112.68303473759022]
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
</script> -->

<script>
var map = L.map('map').setView([-7.614529, 110.712246], 6.5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var locations = [{
        name: "Jempol Baru Furniture",
        coords: [-7.565618587313341, 110.82431800071647]
    },
    {
        name: "Sumber Abadi Furniture",
        coords: [-7.743532734851712, 110.36242217704519]
    },
    {
        name: "Home Gallery Furniture",
        coords: [-7.2886288976515985, 112.67261790823825]
    },
    {
        name: "Pari Anom Jaya Furniture",
        coords: [-7.241679877880819, 112.74470435104482]
    },
    {
        name: "Suri Mebel Semarang",
        coords: [-6.985425332274277, 110.41746260495303]
    },
    {
        name: "Puri Mabel & Interior",
        coords: [-6.9658085878072935, 110.39043865842983]
    },
    {
        name: "Victoria Furnicenter",
        coords: [-7.310519605654715, 112.68303473759022]
    },
    {
        name: "Tunggal Jaya Furniture",
        coords: [-7.98299983310868, 112.62939860029017]
    },
    {
        name: "Cipta Bangun Jaya Furniture",
        coords: [-7.497190468107969, 110.22351816365261]
    }
];

locations.forEach(function(store) {
    var marker = L.circleMarker([store.coords[0], store.coords[1]], {
        color: '#FF4D4D',
        fillColor: '#FF4D4D',
        fillOpacity: 0.2,
        stroke: true,
        strokeOpacity: 0.2,
        weight: 0.2,
        radius: 10
    }).addTo(map);

    var popupContent = "<b>" + store.name + "</b>";
    marker.bindPopup(popupContent);

    var circle = L.circle([store.coords[0], store.coords[1]], {
        color: '#FF4D4D',
        fillColor: '#FF4D4D',
        fillOpacity: 0.2,
        radius: 50
    }).addTo(map);
    marker.on('mouseover', function() {
        marker.openPopup();
    });
    marker.on('mouseout', function() {
        marker.closePopup();
    });
    marker.on('click', function() {
        var lat = store.coords[0];
        var lng = store.coords[1];
        var googleMapsUrl = "https://www.google.com/maps?q=" + lat + "," + lng;
        window.open(googleMapsUrl, "_blank");
    });

    circle.on('click', function() {
        var lat = store.coords[0];
        var lng = store.coords[1];
        var googleMapsUrl = "https://www.google.com/maps?q=" + lat + "," + lng;
        window.open(googleMapsUrl, "_blank");
    });
});
map.on('click', function(e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;
    var googleMapsUrl = "https://www.google.com/maps?q=" + lat + "," + lng;
    window.open(googleMapsUrl, "_blank");
});
</script>

<?= $this->endSection(); ?>