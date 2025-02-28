<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div>
    <img style="width: 100%; height: 50vh; object-fit: cover;" src="../img/foto/gambar-hero2.webp" alt="Hero Image">

    <div class="pb-4">
        <div class="container py-4">
            <h1 class="teks-besar mb-3">Cerita Kami</h1>
            <p style="text-align: justify; line-height: 1.6;">
                Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan CV Catur Bhakti Mandiri yang telah
                berdiri sejak 30 tahun. Ilena menandai dimulainya bisnis ritel dan interior. Dengan melebarnya industri
                yang didorong oleh kebutuhan konsumen, kami melakukan berbagai inovasi, keberlanjutan serta keinginan
                untuk terus konsisten berada di dekat hati konsumen dengan furniture berkualitas.
            </p>
        </div>

        <!-- Bagian Windows -->
        <div class="container show-block-ke-hide">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;"
                        src="<?= base_url('/img/foto/tt.jpg') ?>" alt="Urban Design">
                </div>
                <div class="col-md-6">
                    <h2 class="teks-besar mb-3">Crafted to Urban Design</h2>
                    <p style="text-align: justify;">
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang yang
                        menjadi indah dalam kenangan. Keberhasilan Ilena merupakan usaha menghadirkan furniture khas
                        masyarakat urban yang cocok untuk segala suasana. Kami percaya bahwa setiap ruang kosong
                        memiliki cerita yang diukir indah oleh individu dan relasinya sebagai bentuk representasi
                        tersendiri. Bersama Ilena wujudkan keindahan interior ruang impian.
                    </p>
                </div>
            </div>
        </div>

        <div class="container show-block-ke-hide mt-5">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="teks-besar mb-3">Profil Perusahaan</h2>
                    <p style="text-align: justify;">
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang, Jawa
                        Tengah. Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi
                        terhadap keseimbangan kebutuhan konsumen dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
                <div class="col-md-6">
                    <img style="width: 100%; height: auto; object-fit: cover; filter: grayscale(1); border-radius: 8px;"
                        src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="Company Profile">
                </div>
            </div>
        </div>

        <!-- END Bagian Windows -->

        <!-- Bagian HP (Mobile view) -->
        <div class="container hide-ke-show-block">
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <img style="width: 100%; object-fit: cover; border-radius: 8px;"
                        src="<?= base_url('/img/foto/tt.jpg') ?>" alt="Urban Design Mobile">
                </div>
                <div class="col-12">
                    <h2 class="teks-besar mb-3">Crafted to Urban Design</h2>
                    <p style="text-align: justify;">
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang yang
                        menjadi indah dalam kenangan. Keberhasilan Ilena merupakan usaha menghadirkan furniture khas
                        masyarakat urban yang cocok untuk segala suasana. Kami percaya bahwa setiap ruang kosong
                        memiliki cerita yang diukir indah oleh individu dan relasinya sebagai bentuk representasi
                        tersendiri. Bersama Ilena wujudkan keindahan interior ruang impian.
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <img style="width: 100%; object-fit: cover; filter: grayscale(1); border-radius: 8px;"
                        src="<?= base_url('/img/foto/Tentangperusahaan.JPG') ?>" alt="Company Profile Mobile">
                </div>
                <div class="col-12">
                    <h3 class="teks-besar mb-3">Profil Perusahaan</h3>
                    <p style="text-align: justify;">
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang, Jawa
                        Tengah. Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi
                        terhadap keseimbangan kebutuhan konsumen dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
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

<!-- Lokasi Toko -->
<div id="mitrakami" class="container pt-4 pb-5" style="background-color: rgb(230, 230, 230); border-radius: 10px;">
    <h2 class="teks-sedang mb-4 text-center">Mitra Kami</h2>
    <div id="map" style="height: 500px; width: 100%; border-radius: 10px;"></div>
</div>

<script>
// Map Initialization
var map = L.map('map', {
    center: [-7.614529, 110.712246],
    zoom: 7,
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
    coords: [-7.565517457000553, 110.82418385423263],
    link: "https://maps.app.goo.gl/J7anuqnuommmVAta9",
    image: "https://streetviewpixels-pa.googleapis.com/v1/thumbnail?panoid=GhMtvLKGPqypqcpi8sT4pQ&cb_client=search.gws-prod.gps&w=408&h=240&yaw=53.174248&pitch=0&thumbfov=100"
}, {
    name: "Sumber Abadi Furniture",
    coords: [-7.743342634217805, 110.36247403971207],
    link: "https://maps.app.goo.gl/DcA9BUHheDdQD6Gc7",
    image: "https://lh3.googleusercontent.com/gps-proxy/ALd4DhGShmpToVMb7TTupR8Vi1tkd8bzWn5XfuPzOzw5NTGIy_-t1Ju5aSRxPTpFW_QJKRAiWQmJpElLFyTRFyEbD2xO77PYFF3PfOaLOn-E_ksf0r3L4c9PxjtlRSMBp1A5O1J_K_d7BdEfMyAkMo0gJs2xHWievt7lFeu-neRzcNyZFh47c3e2eStMdtkJ9M8V0S3_G10=w408-h240-k-no"
}, {
    name: "Home Gallery Furniture",
    coords: [-7.2886288976515985, 112.67261790823825],
    link: "https://maps.app.goo.gl/p5UwACRF6S8hMdrg7",
    image: "https://lh5.googleusercontent.com/p/AF1QipOoBRnUjEsbwm2qQ7dGex2s30m_zSb3uX-pZ8Jf=w408-h335-k-no"
}, {
    name: "Pari Anom Jaya Furniture",
    coords: [-7.264405907300976, 112.79562668327448],
    link: "https://maps.app.goo.gl/c7FX6VVayuNHE5Sp8",
    image: "https://www.google.com/maps/@-7.2418678,112.744882,3a,75y,90t/data=!3m8!1e2!3m6!1s3d2tRXH8lIQAAAQYa2kNyg!2e0!3e3!6shttps:%2F%2Flh3.googleusercontent.com%2Fgps-proxy%2FALd4DhG-v_-Vzkr3WL1B0PebVNqXDTX7iU9628Qpfxj4p8Xk83F_6zJ6vCX7V7zoqIrIbI5hb9-Sg8oHZKnpkAUJj9IlpCbvpA6e-J2M2GeFdw8U4TsNPQW8W0BfHBsXyj9BfvlYVDbbvAuKgT-HfigyCr7MBgChUkTrCAoyzNIWv0H7gMXFY9BhnekwvK54sS1xikRkATQ%3Dw114-h86-k-no!7i2133!8i1600?entry=ttu&g_ep=EgoyMDI1MDIxOS4xIKXMDSoASAFQAw%3D%3D"
}, {
    name: "Suri Mebel Semarang",
    coords: [-6.985425332274277, 110.41746260495303],
    link: "https://maps.app.goo.gl/bbT2yeHvj1jPttWUA",
    image: "https://streetviewpixels-pa.googleapis.com/v1/thumbnail?panoid=NPKi7fw1xW7VgdaqnCNLFw&cb_client=search.gws-prod.gps&w=408&h=240&yaw=270.7453&pitch=0&thumbfov=100"
}];

stores.forEach(store => {
    var marker = L.marker(store.coords, {
        icon: customIcon
    }).addTo(map);
    marker.bindPopup(
        `<b>${store.name}</b><br>
         <img src="${store.image}" alt="${store.name}" style="width:100%; max-width:100px; border-radius:4px; margin:5px 0;"><br>
         <a href="${store.link}" target="_blank" style="text-align: center;">Buka di Google Maps</a>`
    );
    marker.on('mouseover', function() {
        marker.openPopup();
    });
    marker.on('mouseout', function() {
        marker.closePopup();
    });
    marker.on('click', function() {
        window.open(store.link, "_blank");
    });
});
</script>

<?= $this->endSection(); ?>