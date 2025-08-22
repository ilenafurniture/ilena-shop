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
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Tersedia Di
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
                    <img src="https://img.ilenafurniture.com/image/1742449816363.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742449853266.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742449882169.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742449923158.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742449972596.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742450004899.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742450030786.webp/?apikey=<?= $apikey_img_ilena ?>"
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
                    <img src="https://img.ilenafurniture.com/image/1742450030786.webp/?apikey=<?= $apikey_img_ilena ?>"
                        alt="Tunggal Jaya Furniture" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Cipta Bangun Jaya Furniture Magelang</h3>
                    <p class="alamat m-0">Jl. Jend. Sudirman No.189, Tidar Sel., Kec. Magelang Sel., Kota Magelang, Jawa
                        Tengah
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://lh3.googleusercontent.com/p/AF1QipMWUmj6bwLhOk3oi1hkWM-kq86aSIa5cQ8IAIFW=w427-h240-k-no"
                        alt="Cipta Bangun Jaya Furniture Magelang" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Cipta Bangun Jaya Furniture Jakarta</h3>
                    <p class="alamat m-0">Jl. Raya Ragunan No.51, RT.5/RW.4, Ps. Minggu, Kota Jakarta Selatan, Daerah
                        Khusus Ibukota Jakarta
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://lh3.googleusercontent.com/gps-cs-s/AC9h4npIeYybVsaWPUt8J9SFrLRy5WsNFexTWw-Rb2GMXlC2Yi91DkFi1opmxP210dhgxOpHqxdFomlQPgPdZR07yllFb7BXcw0n9VxnIKy56Ld__02l6lQUBJ2jtT5du7rGXr0BnJqveA=w408-h306-k-no"
                        alt="Cipta Bangun Jaya Furniture Jakarta" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Homj Furniture</h3>
                    <p class="alamat m-0">Jl. Jemursari III No.25, Jemur Wonosari, Kec. Wonocolo, Surabaya, Jawa Timur
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://lh3.googleusercontent.com/gps-cs-s/AC9h4noB7Su_X26RWhDD2QvcU6AwF_uD_xpB8gNz2ed6UFI3zUgJ9s5DjOGlNEKvhUX3Tr9c1jmOirs19hCpk-wLJEiGT4CYCDztq59Ndw9jV0Htb8MRXAG5PRbo9UXVL8dlz0iebt7P=w408-h544-k-no"
                        alt="Homj Furniture" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/oumo8h2XUy32KH168"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Kasur Indah</h3>
                    <p class="alamat m-0">Jl. Imam Bonjol No.403, Pemecutan Klod, Kec. Denpasar Bar., Kota Denpasar,
                        Bali
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://i.imgur.com/MwLYjVf.png" alt="Kasur Indah" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/MJ396VhceiHfGLBF6"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">DM Mebel Supeno</h3>
                    <p class="alamat m-0">Jl. Menteri Supeno No.73, Pandeyan, Kec. Umbulharjo, Kota Yogyakarta, Daerah
                        Istimewa Yogyakarta 55162
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://i.ibb.co.com/q3vG29Bf/Screenshot-2025-08-06-102008.png" alt="DM Mebel Supeno"
                        class="client-logo">
                </div>

            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/8a9karvqThnFXzX97"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Istana Meubel CIREBON</h3>
                    <p class="alamat m-0">Jl. Pekarungan No.12-14, Panjunan, Kec. Lemahwungkuk, Kota Cirebon, Jawa Barat
                        45112
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://i.ibb.co.com/Q7HV0b1v/Screenshot-2025-08-19-233904.png" alt="DM Mebel Supeno"
                        class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/5QZ8tTaSVTmTtxHQ7"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Living Home Houseware & Furniture</h3>
                    <p class="alamat m-0">Jl. KH. Wahid Hasyim No.88, Windusara, Karangklesem, Kec. Purwokerto Sel.,
                        Kabupaten Banyumas, Jawa Tengah 53144
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://i.ibb.co.com/TDFP36LD/Screenshot-2025-08-19-234119.png" alt="DM Mebel Supeno"
                        class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/cU4SfXLKjL9pV5uz8"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Vinetta Furniture Purwokerto</h3>
                    <p class="alamat m-0">Jalan Kombes Jl. Komisaris Bambang Suprapto No.99, Cigrobak, Purwokerto Lor,
                        Kec. Purwokerto Tim., Kabupaten Banyumas, Jawa Tengah 53114
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://i.ibb.co.com/v4VDntfc/Screenshot-2025-08-22-142911.png"
                        alt="Vinetta Furniture Purwokerto" class="client-logo">
                </div>
            </div>
            <div class="item-toko">
                <a href="https://maps.app.goo.gl/17uAqPADuswiuxeG6"
                    style="flex: 1; text-decoration: none; color: black;" class="p-4">
                    <h3 class="nama">Pusat Grosir & Eceran Meubel FURNI CENTER Purwokerto</h3>
                    <p class="alamat m-0">Jl. Pekarungan No.12-14, Panjunan, Kec. Lemahwungkuk, Kota Cirebon, Jawa Barat
                        45112
                    </p>
                </a>
                <div class="p-2">
                    <img src="https://i.ibb.co.com/JR7y1B3Y/Screenshot-2025-08-22-144249.png"
                        alt="Pusat Grosir & Eceran Meubel FURNI CENTER Purwokerto" class="client-logo">
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
            name: "Cipta Bangun Jaya Furniture Jakarta",
            coords: [-6.285266506836552, 106.84299483660007]
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
            name: "Cipta Bangun Jaya Furniture Magelang",
            coords: [-7.497190468107969, 110.22351816365261]
        },
        {
            name: "Homj Furniture",
            coords: [-7.321151681470964, 112.7441071560504]
        },
        {
            name: "Kasur Indah",
            coords: [-8.696614587200164, 115.18642372535126]
        },
        {
            name: "DM Mebel Supeno",
            coords: [-7.816169475560965, 110.38068786772793]
        },
        {
            name: "Istana Meubel CIREBON",
            coords: [-6.715091293405323, 108.56476548596918]
        },
        {
            name: "Living Home Houseware & Furniture",
            coords: [-7.446178589024145, 109.24378085767192]
        },
        {
            name: "Vinetta Furniture Purwokerto",
            coords: [-7.42304698938314, 109.25023176982587]
        },
        {
            name: "Pusat Grosir & Eceran Meubel FURNI CENTER Purwokerto",
            coords: [-7.438356338368611, 109.24430059470743]
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