<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
/* ===== Tokens Light (tetap) ===== */
:root {
    --bg: #f7fafc;
    --panel: #ffffff;
    --panel-2: #f9fafb;
    --ring: rgba(15, 23, 42, .08);
    --ring-2: rgba(15, 23, 42, .14);
    --txt: #0f172a;
    --muted: #64748b;
    --brand: #e11d48;
    /* merah */
    --brand-2: #f97316;
    /* oranye */
    --glass: rgba(255, 255, 255, .86);
    --shadow-1: 0 10px 24px rgba(2, 6, 23, .08);
    --shadow-2: 0 18px 46px rgba(2, 6, 23, .12);
    --gap: 16px;
    --card-h: 110px;
}

/* halaman */
.container .konten {
    background:
        radial-gradient(900px 500px at 110% -5%, rgba(226, 232, 240, .55), transparent 60%),
        radial-gradient(1000px 600px at -10% -10%, rgba(241, 245, 249, .7), transparent 60%);
    border-radius: 18px;
    padding: 12px 12px 22px 12px;
}

/* section title */
.section-hero {
    position: relative;
    margin-top: .5rem;
    padding: 14px 16px;
    border-radius: 14px;
    background:
        radial-gradient(1000px 500px at 110% -10%, rgba(225, 29, 72, .08), transparent 60%),
        radial-gradient(900px 600px at -10% -10%, rgba(249, 115, 22, .08), transparent 55%),
        linear-gradient(180deg, #ffffff, #fbfdff 60%);
    box-shadow: var(--shadow-1);
    border: 1px solid var(--ring);
    color: var(--txt);
}

.section-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 900;
    letter-spacing: -.5px;
    color: var(--txt);
}

.section-sub {
    text-align: center;
    color: var(--muted);
    margin-top: 4px;
    font-size: .95rem;
}

/* Map panel */
.map-wrap {
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--ring);
    background: linear-gradient(180deg, #ffffff, #fbfdff);
    box-shadow: var(--shadow-1);
}

#map {
    height: 420px;
    min-height: 300px;
}

/* ===== View Toolbar (baru) ===== */
.view-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin: 10px 0 4px 0;
    padding: 8px;
    border: 1px solid var(--ring);
    border-radius: 12px;
    background: #fff;
    box-shadow: var(--shadow-1);
}

.view-tabs {
    display: flex;
    gap: 8px;
    align-items: center;
}

.view-btn {
    appearance: none;
    border: 1px solid var(--ring);
    background: #fff;
    color: #0f172a;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
    box-shadow: 0 4px 10px rgba(2, 6, 23, .06);
}

.view-btn i {
    font-size: 18px;
}

.view-btn[aria-pressed="true"] {
    border-color: transparent;
    background: linear-gradient(180deg, #fff, #fff) padding-box,
        linear-gradient(90deg, var(--brand), var(--brand-2)) border-box;
    border: 2px solid transparent;
    box-shadow: 0 10px 22px rgba(225, 29, 72, .18);
}

.view-meta {
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Rail header */
.toko-rail {
    position: relative;
    margin-top: 12px;
}

.rail-head {
    display: flex;
    align-items: center;
    gap: 12px;
    justify-content: space-between;
    margin-bottom: 10px;
}

.rail-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--muted);
    font-size: .9rem;
}

.count-dot {
    display: inline-flex;
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: linear-gradient(180deg, var(--brand), var(--brand-2));
    box-shadow: 0 0 0 3px rgba(225, 29, 72, .12);
}

/* progress */
.rail-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rail-progress {
    appearance: none;
    width: 160px;
    height: 8px;
    border-radius: 999px;
    background: #e5e7eb;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid var(--ring);
}

.rail-progress::-webkit-slider-thumb {
    appearance: none;
    width: 0;
    height: 0;
}

.rail-progress::-webkit-slider-runnable-track {
    background: linear-gradient(90deg, var(--brand), var(--brand-2)) 0/var(--fill, 0%) 100% no-repeat, #e5e7eb;
}

/* tombol prev/next */
.toko-nav-btn {
    border: 1px solid var(--ring-2);
    background: linear-gradient(180deg, #ffffff, #f8fafc);
    color: #0f172a;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    box-shadow: var(--shadow-1);
    cursor: pointer;
    transition: .2s;
}

.toko-nav-btn:hover {
    transform: translateY(-2px);
    border-color: rgba(2, 6, 23, .22);
}

/* Rail container */
.container-toko {
    display: flex;
    gap: var(--gap);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x mandatory;
    scroll-padding-left: 6px;
    padding: 8px 4px 14px 4px;
}

.container-toko::-webkit-scrollbar {
    height: 8px;
}

.container-toko::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--brand), var(--brand-2));
    border-radius: 999px;
}

/* Card toko (sumber) */
.item-toko {
    scroll-snap-align: start;
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    min-width: min(560px, 92vw);
    height: var(--card-h);
    padding: 10px;
    border-radius: 14px;
    background: linear-gradient(180deg, rgba(255, 255, 255, .9), rgba(255, 255, 255, .86)), radial-gradient(400px 120px at 80% -20%, rgba(225, 29, 72, .06), transparent 55%), var(--glass);
    border: 1px solid var(--ring);
    box-shadow: var(--shadow-1);
    backdrop-filter: blur(5px);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.item-toko:hover {
    transform: translateY(-3px);
    border-color: rgba(2, 6, 23, .18);
    box-shadow: var(--shadow-2);
}

.item-toko .info {
    min-width: 0;
    padding: 4px 8px 4px 10px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.item-toko .nama {
    color: #0f172a;
    margin: 0;
    font-weight: 900;
    letter-spacing: -.3px;
    font-size: 1.05rem;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.item-toko .alamat {
    color: var(--muted);
    margin: 0;
    font-size: .84rem;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.badge-mini {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    background: linear-gradient(180deg, var(--brand), var(--brand-2));
    padding: .32rem .6rem;
    border-radius: 999px;
    box-shadow: 0 8px 24px rgba(225, 29, 72, .25);
}

.item-toko .thumb {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px 6px 6px 10px;
}

.item-toko img {
    width: 96px;
    height: 82px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid var(--ring);
    filter: saturate(.96) contrast(1.02);
    transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
    box-shadow: 0 8px 18px rgba(2, 6, 23, .12);
}

.item-toko:hover img {
    transform: translateY(-1px) scale(1.01);
    filter: saturate(1) contrast(1.04);
}

/* Breadcrumb */
.breadcrumb a {
    color: #334155;
    text-decoration: none;
}

.breadcrumb .breadcrumb-item.active {
    color: #64748b;
}

/* ====== GRID & LIST (baru) ====== */
.view-pane {
    display: none;
}

.view-pane.active {
    display: block;
}

/* Grid */
.grid-wrap {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.grid-card {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    padding: 12px;
    border: 1px solid var(--ring);
    background: #fff;
    border-radius: 14px;
    box-shadow: var(--shadow-1);
    transition: .2s;
}

.grid-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-2);
}

.grid-card .g-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.grid-card .g-name {
    margin: 0;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -.2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.grid-card .g-addr {
    margin: 0;
    color: var(--muted);
    font-size: .9rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.grid-card img {
    width: 110px;
    height: 90px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid var(--ring);
}

/* List */
.list-wrap {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.list-row {
    display: grid;
    grid-template-columns: 72px 1fr auto;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border: 1px solid var(--ring);
    border-radius: 12px;
    background: #fff;
    box-shadow: var(--shadow-1);
}

.list-row img {
    width: 72px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid var(--ring);
}

.list-row .l-title {
    font-weight: 800;
    margin: 0;
    color: #0f172a;
    letter-spacing: -.2px;
}

.list-row .l-addr {
    margin: 2px 0 0 0;
    color: var(--muted);
    font-size: .9rem;
}

.list-row .l-cta a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 10px;
    border-radius: 10px;
    border: 1px solid var(--ring);
    text-decoration: none;
    color: #0f172a;
}

.list-row .l-cta a:hover {
    border-color: transparent;
    background: linear-gradient(90deg, var(--brand), var(--brand-2));
    color: #fff;
}

/* Responsive */
@media (max-width: 1200px) {
    .grid-wrap {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 992px) {
    :root {
        --card-h: 100px;
        --gap: 14px;
    }

    #map {
        height: 360px;
    }
}

@media (max-width: 768px) {
    :root {
        --card-h: 92px;
        --gap: 12px;
    }

    .item-toko {
        min-width: min(480px, 92vw);
    }

    .item-toko .nama {
        font-size: 1rem;
    }

    .item-toko .alamat {
        font-size: .8rem;
    }

    .item-toko img {
        width: 86px;
        height: 74px;
    }

    #map {
        height: 300px;
    }

    .grid-card img {
        width: 96px;
        height: 80px;
    }
}

@media (max-width: 520px) {
    :root {
        --card-h: 86px;
    }

    .item-toko {
        min-width: 88vw;
    }

    .item-toko img {
        width: 78px;
        height: 66px;
    }

    #map {
        height: 260px;
    }

    .grid-wrap {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container">
    <div class="konten mx-auto">
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tersedia Di</li>
            </ol>
        </nav>

        <div class="section-hero">
            <h2 class="section-title">
                <span class="badge-mini"><i class="material-icons" style="font-size:16px">location_on</i> Mitra</span>
                <span>•</span>
                <span>Mitra Kami</span>
            </h2>
            <p class="section-sub">Temukan produk Ilena di jaringan partner tepercaya kami di berbagai kota.</p>
        </div>

        <div class="map-wrap my-3">
            <div id="map"></div>
        </div>

        <!-- ===== Toolbar View ===== -->
        <div class="view-toolbar">
            <div class="view-tabs" role="tablist" aria-label="Jenis tampilan">
                <button id="btnViewRail" class="view-btn" type="button" aria-pressed="true"><i
                        class="material-icons">view_carousel</i>Rail</button>
                <button id="btnViewGrid" class="view-btn" type="button" aria-pressed="false"><i
                        class="material-icons">grid_view</i>Grid</button>
                <button id="btnViewList" class="view-btn" type="button" aria-pressed="false"><i
                        class="material-icons">view_list</i>List</button>
            </div>
            <div class="view-meta"><span id="total-mitra">0 Mitra</span></div>
        </div>

        <!-- ====== VIEW: RAIL ====== -->
        <div class="toko-rail view-pane active" id="pane-rail">
            <div class="rail-head">
                <div class="rail-meta">
                    <span class="count-dot"></span>
                    <span id="label-count">Memuat mitra…</span>
                </div>
                <div class="rail-actions">
                    <input id="railProgress" type="range" min="0" max="100" value="0" class="rail-progress"
                        aria-label="Scroll progress">
                    <button class="toko-nav-btn toko-prev" type="button" aria-label="Sebelumnya"><i
                            class="material-icons" style="font-size:18px">chevron_left</i></button>
                    <button class="toko-nav-btn toko-next" type="button" aria-label="Berikutnya"><i
                            class="material-icons" style="font-size:18px">chevron_right</i></button>
                </div>
            </div>

            <div class="container-toko" id="container-toko">
                <!-- ==== DAFTAR MITRA (ASLI + TAMBAHAN) ==== -->

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/LDNymAWjfyFijkZT7" class="info" style="text-decoration:none;">
                        <h3 class="nama">Jempol Baru Furniture</h3>
                        <p class="alamat m-0">Jl. R. M. Said No.4, RW.6, Keprabon, Kec. Banjarsari, Kota Surakarta</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742449816363.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="Jempol Baru Furniture">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/cBQGbHzKCgyoTAM29" class="info" style="text-decoration:none;">
                        <h3 class="nama">Sumber Abadi Furniture</h3>
                        <p class="alamat m-0">Jl. Magelang No.km7, Mlati Beningan, Sendangadi, Sleman, DIY</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742449853266.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="Sumber Abadi Furniture">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/2f6uDgjkd9SSwRnC8" class="info" style="text-decoration:none;">
                        <h3 class="nama">Suri Meubel (Semarang)</h3>
                        <p class="alamat m-0">Jl. MH Thamrin, Sekayu, Kec. Semarang Tengah, Kota Semarang</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742449882169.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="SURI Meubel Furniture">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/EJYp9pEmPZ3a7XASA" class="info" style="text-decoration:none;">
                        <h3 class="nama">Home Gallery Furniture</h3>
                        <p class="alamat m-0">Jl. Puncak Permai Utara I A No.5, Babatan, Wiyung, Surabaya</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742449923158.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="Home Gallery Furniture">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/BPDJUK8MyRBFrJGW8" class="info" style="text-decoration:none;">
                        <h3 class="nama">Pari Anom Jaya Furniture</h3>
                        <p class="alamat m-0">Pasar Atum, Jl. Bunguran No.45 Lantai 4, Bongkaran, Pabean Cantikan,
                            Surabaya</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742449972596.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="Pari Anom Jaya Furniture">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/BkiBFdCA4Rop3qHS7" class="info" style="text-decoration:none;">
                        <h3 class="nama">Puri Mebel & Interior (Semarang)</h3>
                        <p class="alamat m-0">Jl. Puri Anjasmoro Blk. H5 No.57, Tawangsari, Semarang</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742450004899.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="Puri Mebel & Interior">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5" class="info" style="text-decoration:none;">
                        <h3 class="nama">Victoria Furnicenter</h3>
                        <p class="alamat m-0">Jl. Raya Menganti Karangan No.578, Babatan, Wiyung, Surabaya</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://img.ilenafurniture.com/image/1742450030786.webp/?apikey=<?= $apikey_img_ilena ?>"
                            alt="Victoria Furnicenter">
                    </div>
                </div>

                <!-- KOREKSI: Tunggal Jaya (Malang) -->
                <div class="item-toko">
                    <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Kauman+Dalam+No.6,+Kauman,+Klojen,+Malang+65119"
                        class="info" style="text-decoration:none;">
                        <h3 class="nama">Tunggal Jaya Furniture</h3>
                        <p class="alamat m-0">Jl. Kauman Dalam No.6, Kauman, Kec. Klojen, Kota Malang 65119</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async" src="/img/mitra/tunggal-jaya-malang.jpg"
                            alt="Tunggal Jaya Furniture">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5" class="info" style="text-decoration:none;">
                        <h3 class="nama">Cipta Bangun Jaya Furniture Magelang</h3>
                        <p class="alamat m-0">Jl. Jend. Sudirman No.189, Magelang Selatan</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://lh3.googleusercontent.com/p/AF1QipMWUmj6bwLhOk3oi1hkWM-kq86aSIa5cQ8IAIFW=w427-h240-k-no"
                            alt="CBJ Magelang">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/5ZXJpa5BAFadgwAr5" class="info" style="text-decoration:none;">
                        <h3 class="nama">Cipta Bangun Jaya Furniture Jakarta</h3>
                        <p class="alamat m-0">Jl. Raya Ragunan No.51, Pasar Minggu, Jakarta Selatan</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://lh3.googleusercontent.com/gps-cs-s/AC9h4npIeYybVsaWPUt8J9SFrLRy5WsNFexTWw-Rb2GMXlC2Yi91DkFi1opmxP210dhgxOpHqxdFomlQPgPdZR07yllFb7BXcw0n9VxnIKy56Ld__02l6lQUBJ2jtT5du7rGXr0BnJqveA=w408-h306-k-no"
                            alt="CBJ Jakarta">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/oumo8h2XUy32KH168" class="info" style="text-decoration:none;">
                        <h3 class="nama">Kasur Indah</h3>
                        <p class="alamat m-0">Jl. Imam Bonjol No.403, Denpasar</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async" src="https://i.imgur.com/MwLYjVf.png" alt="Kasur Indah">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/MJ396VhceiHfGLBF6" class="info" style="text-decoration:none;">
                        <h3 class="nama">DM Mebel Supeno</h3>
                        <p class="alamat m-0">Jl. Menteri Supeno No.73, Umbulharjo, Yogyakarta</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/q3vG29Bf/Screenshot-2025-08-06-102008.png" alt="DM Mebel Supeno">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/8a9karvqThnFXzX97" class="info" style="text-decoration:none;">
                        <h3 class="nama">Istana Meubel (Cirebon)</h3>
                        <p class="alamat m-0">Jl. Pekarungan No.12-14, Lemahwungkuk, Cirebon</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/Q7HV0b1v/Screenshot-2025-08-19-233904.png"
                            alt="Istana Meubel Cirebon">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/5QZ8tTaSVTmTtxHQ7" class="info" style="text-decoration:none;">
                        <h3 class="nama">Living Home Houseware & Furniture</h3>
                        <p class="alamat m-0">Jl. KH. Wahid Hasyim No.88, Purwokerto</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/TDFP36LD/Screenshot-2025-08-19-234119.png" alt="Living Home">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/cU4SfXLKjL9pV5uz8" class="info" style="text-decoration:none;">
                        <h3 class="nama">Vinetta Furniture Purwokerto</h3>
                        <p class="alamat m-0">Jl. Komisaris Bambang Suprapto No.99, Purwokerto</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/v4VDntfc/Screenshot-2025-08-22-142911.png"
                            alt="Vinetta Purwokerto">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/17uAqPADuswiuxeG6" class="info" style="text-decoration:none;">
                        <h3 class="nama">FURNI CENTER Purwokerto</h3>
                        <p class="alamat m-0">Jl. S. Parman No.125, Karangklesem, Purwokerto Selatan</p>
                    </a>
                    <div class="thumb">
                        <img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/JR7y1B3Y/Screenshot-2025-08-22-144249.png" alt="Furni Center">
                    </div>
                </div>

                <!-- ===== TAMBAHAN BARU DARI DATA KAMU ===== -->

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/24PJYPuvbjqPbmUD6" class="info" style="text-decoration:none;">
                        <h3 class="nama">Seruma Space</h3>
                        <p class="alamat m-0">Sokowaten, Tamanan, Banguntapan, Bantul, DI Yogyakarta 55191</p>
                    </a>
                    <div class="thumb"><img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/DfJyv5HL/Screenshot-2025-09-15-143349.png" alt="Seruma Space">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/HCJvNpPmpdARbHaGA" class="info" style="text-decoration:none;">
                        <h3 class="nama">Toko Mebel Garuda Mas</h3>
                        <p class="alamat m-0">Jl. Jend. Sudirman No.189, Tidar Sel., Magelang Selatan, Magelang 59214
                        </p>
                    </a>
                    <div class="thumb"><img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/JRhJFZSV/Screenshot-2025-09-15-143456.png"
                            alt="Toko Mebel Garuda Mas"></div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/UHUoe8DMdio4mfoi9" class="info" style="text-decoration:none;">
                        <h3 class="nama">Meubel Lisa Jaya</h3>
                        <p class="alamat m-0">Timur Jl. SMPN 2, JL. K.H. Hasyim Asyari No.13, Demangan Barat, Bangkalan
                            69115</p>
                    </a>
                    <div class="thumb"><img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/ch0bNWzW/Screenshot-2025-09-15-143632.png" alt="Meubel Lisa Jaya">
                    </div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/qcX5EL6n4PW72Xq57" class="info" style="text-decoration:none;">
                        <h3 class="nama">Agrapana Furniture</h3>
                        <p class="alamat m-0">Jl. Sidomoyo No.89, Sidoarum, Godean, Sleman, DI Yogyakarta 55264</p>
                    </a>
                    <div class="thumb"><img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/fdsqzwFK/Screenshot-2025-09-15-143959.png"
                            alt="Agrapana Furniture"></div>
                </div>

                <div class="item-toko">
                    <a href="https://maps.app.goo.gl/YEY22dZeYKcbnXoD9" class="info" style="text-decoration:none;">
                        <h3 class="nama">Elok Meubel</h3>
                        <p class="alamat m-0">Jl. Mayjend DI. Panjaitan No.68 / 70, Tegalsari, Kec. Tegal Barat, Tegal
                            52111</p>
                    </a>
                    <div class="thumb"><img loading="lazy" decoding="async"
                            src="https://i.ibb.co.com/spnfJ946/Screenshot-2025-09-15-144115.png" alt="Elok Meubel">
                    </div>
                </div>

            </div>
        </div>

        <!-- ====== VIEW: GRID (baru) ====== -->
        <div class="view-pane" id="pane-grid">
            <div class="grid-wrap" id="gridWrap"></div>
        </div>

        <!-- ====== VIEW: LIST (baru) ====== -->
        <div class="view-pane" id="pane-list">
            <div class="list-wrap" id="listWrap"></div>
        </div>

    </div>
</div>

<!-- Scroll helpers (rail tetap) -->
<script>
(function() {
    const rail = document.getElementById('container-toko');
    const prev = document.querySelector('.toko-prev');
    const next = document.querySelector('.toko-next');
    const prog = document.getElementById('railProgress');
    const label = document.getElementById('label-count');
    const totalLabel = document.getElementById('total-mitra');
    const count = rail ? rail.querySelectorAll('.item-toko').length : 0;
    if (label) label.textContent = count ? `${count} Mitra Terdaftar` : '0 Mitra';
    if (totalLabel) totalLabel.textContent = count ? `${count} Mitra` : '0 Mitra';

    if (!rail || !prev || !next || !prog) return;
    const step = () => Math.min(rail.clientWidth * 0.8, 520);
    prev.addEventListener('click', () => rail.scrollBy({
        left: -step(),
        behavior: 'smooth'
    }));
    next.addEventListener('click', () => rail.scrollBy({
        left: step(),
        behavior: 'smooth'
    }));

    const updateProg = () => {
        const max = rail.scrollWidth - rail.clientWidth;
        const val = max > 0 ? (rail.scrollLeft / max) * 100 : 0;
        prog.value = val;
        prog.style.setProperty('--fill', val + '%');
    };
    rail.addEventListener('scroll', updateProg);
    window.addEventListener('resize', updateProg);
    prog.addEventListener('input', (e) => {
        const max = rail.scrollWidth - rail.clientWidth;
        rail.scrollTo({
            left: max * (e.target.value / 100),
            behavior: 'smooth'
        });
    });
    updateProg();

    // blink kecil saat jumlah bertambah
    totalLabel && totalLabel.animate(
        [{
            transform: "scale(1)"
        }, {
            transform: "scale(1.06)"
        }, {
            transform: "scale(1)"
        }], {
            duration: 360
        }
    );
})();
</script>

<!-- View toggle + builder Grid/List (BARU) -->
<script>
(function() {
    const btnRail = document.getElementById('btnViewRail');
    const btnGrid = document.getElementById('btnViewGrid');
    const btnList = document.getElementById('btnViewList');

    const paneRail = document.getElementById('pane-rail');
    const paneGrid = document.getElementById('pane-grid');
    const paneList = document.getElementById('pane-list');

    const rail = document.getElementById('container-toko');
    const gridWrap = document.getElementById('gridWrap');
    const listWrap = document.getElementById('listWrap');

    function setPressed(target) {
        [btnRail, btnGrid, btnList].forEach(b => b.setAttribute('aria-pressed', 'false'));
        target.setAttribute('aria-pressed', 'true');
    }

    function showPane(p) {
        [paneRail, paneGrid, paneList].forEach(x => x.classList.remove('active'));
        p.classList.add('active');
    }

    // Builder dari item rail -> grid/list (sekali saja)
    let built = false;

    function buildAltViews() {
        if (built) return;
        const items = rail.querySelectorAll('.item-toko');
        items.forEach((it) => {
            const name = it.querySelector('.nama')?.textContent?.trim() || '';
            const addr = it.querySelector('.alamat')?.textContent?.trim() || '';
            const href = it.querySelector('a.info')?.getAttribute('href') || '#';
            const imgSrc = it.querySelector('img')?.getAttribute('src') || '';

            // Grid card
            const g = document.createElement('a');
            g.className = 'grid-card';
            g.href = href;
            g.style.textDecoration = 'none';
            g.innerHTML = `
        <div class="g-info">
          <p class="g-name">${name}</p>
          <p class="g-addr">${addr}</p>
        </div>
        <img loading="lazy" decoding="async" src="${imgSrc}" alt="${name}">
      `;
            gridWrap.appendChild(g);

            // List row
            const l = document.createElement('div');
            l.className = 'list-row';
            l.innerHTML = `
        <img loading="lazy" decoding="async" src="${imgSrc}" alt="${name}">
        <div>
          <p class="l-title m-0">${name}</p>
          <p class="l-addr">${addr}</p>
        </div>
        <div class="l-cta">
          <a href="${href}" target="_blank" rel="noopener">
            <i class="material-icons" style="font-size:18px">map</i> Lihat Peta
          </a>
        </div>
      `;
            listWrap.appendChild(l);
        });
        built = true;
    }

    btnRail.addEventListener('click', () => {
        setPressed(btnRail);
        showPane(paneRail);
    });
    btnGrid.addEventListener('click', () => {
        setPressed(btnGrid);
        buildAltViews();
        showPane(paneGrid);
    });
    btnList.addEventListener('click', () => {
        setPressed(btnList);
        buildAltViews();
        showPane(paneList);
    });
})();
</script>

<!-- Map (Leaflet) -->
<script>
var map = L.map('map').setView([-7.614529, 110.712246], 6.5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var locations = [{
        name: "Jempol Baru Furniture (Solo)",
        coords: [-7.565686761626352, 110.82430295467728]
    },
    {
        name: "Cipta Bangun Jaya Furniture (Jakarta)",
        coords: [-6.285267, 106.842995]
    },
    {
        name: "Sumber Abadi Furniture (Yogyakarta)",
        coords: [-7.743533, 110.362422]
    },
    {
        name: "Home Gallery Furniture (Surabaya)",
        coords: [-7.288629, 112.672618]
    },
    {
        name: "Pari Anom Jaya (Surabaya)",
        coords: [-7.241680, 112.744704]
    },
    {
        name: "Suri Meubel (Semarang)",
        coords: [-6.985425, 110.417463]
    },
    {
        name: "Puri Mebel & Interior (Semarang)",
        coords: [-6.965809, 110.390439]
    },
    {
        name: "Victoria Furnicenter (Surabaya)",
        coords: [-7.310520, 112.683035]
    },
    {
        name: "Tunggal Jaya Furniture (Malang)",
        coords: [-7.982, 112.630]
    },
    {
        name: "Cipta Bangun Jaya Furniture (Magelang)",
        coords: [-7.497190, 110.223518]
    },
    {
        name: "Homj Furniture (Surabaya)",
        coords: [-7.321152, 112.744107]
    },
    {
        name: "Kasur Indah (Denpasar)",
        coords: [-8.696615, 115.186424]
    },
    {
        name: "DM Mebel Supeno (Yogyakarta)",
        coords: [-7.816169, 110.380688]
    },
    {
        name: "Istana Meubel (Cirebon)",
        coords: [-6.715091, 108.564765]
    },
    {
        name: "Living Home (Purwokerto)",
        coords: [-7.446179, 109.243781]
    },
    {
        name: "Vinetta Furniture (Purwokerto)",
        coords: [-7.423047, 109.250232]
    },
    {
        name: "FURNI CENTER (Purwokerto)",
        coords: [-7.438356, 109.244301]
    }
];

// === TAMBAHAN MARKER BARU ===
locations.push({
    name: "Seruma Space (Banguntapan, Bantul)",
    coords: [-7.8347557775250065, 110.37898663945798]
}, {
    name: "Toko Mebel Garuda Mas (Magelang)",
    coords: [-7.497210712603127, 110.22352521039721]
}, {
    name: "Meubel Lisa Jaya (Bangkalan)",
    coords: [-7.0282709675175585, 112.74647900288844]
}, {
    name: "Agrapana Furniture (Godean, Sleman)",
    coords: [-6.859468184151993, 109.13567611808162]
}, {
    name: "Elok Meubel (Tegal)",
    coords: [-6.859464477608871, 109.13568596532647]
});

locations.forEach(function(store) {
    var marker = L.circleMarker(store.coords, {
        color: '#e11d48',
        fillColor: '#e11d48',
        fillOpacity: 0.18,
        stroke: true,
        weight: 0.6,
        radius: 10
    }).addTo(map);
    var circle = L.circle(store.coords, {
        color: '#f97316',
        fillColor: '#f97316',
        fillOpacity: 0.15,
        radius: 60
    }).addTo(map);
    marker.bindPopup("<b>" + store.name + "</b>");
    marker.on('mouseover', () => marker.openPopup());
    marker.on('mouseout', () => marker.closePopup());

    function go() {
        window.open("https://www.google.com/maps?q=" + store.coords[0] + "," + store.coords[1], "_blank");
    }
    marker.on('click', go);
    circle.on('click', go);
});
map.on('click', function(e) {
    window.open("https://www.google.com/maps?q=" + e.latlng.lat + "," + e.latlng.lng, "_blank");
});
</script>

<?= $this->endSection(); ?>