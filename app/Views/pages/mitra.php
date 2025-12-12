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
    --brand-2: #f97316;
    --glass: rgba(255, 255, 255, .86);
    --shadow-1: 0 10px 24px rgba(2, 6, 23, .08);
    --shadow-2: 0 18px 46px rgba(2, 6, 23, .12);
    --gap: 16px;
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
    margin: 0;
    font-size: 1.25rem;
}

.section-sub {
    text-align: center;
    color: var(--muted);
    margin-top: 6px;
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

/* ===== Toolbar filter ===== */
.filterbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin: 10px 0 4px 0;
    padding: 10px;
    border: 1px solid var(--ring);
    border-radius: 12px;
    background: #fff;
    box-shadow: var(--shadow-1);
}

.filter-left {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.f-field {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 10px;
    border-radius: 12px;
    border: 1px solid var(--ring-2);
    background: #fff;
    box-shadow: 0 6px 16px rgba(2, 6, 23, .06);
}

.f-field i {
    color: var(--muted);
    font-size: 18px;
}

.f-field input {
    border: 0;
    outline: none;
    width: min(320px, 70vw);
    font-weight: 800;
    color: var(--txt);
    font-size: .95rem;
}

.f-field input::placeholder {
    color: #94a3b8;
    font-weight: 700;
}

.f-select {
    border: 1px solid var(--ring-2);
    border-radius: 12px;
    padding: 9px 10px;
    background: #fff;
    font-weight: 800;
    color: var(--txt);
    box-shadow: 0 6px 16px rgba(2, 6, 23, .06);
    font-size: .95rem;
}

.kpi-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 900;
    color: var(--txt);
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--ring);
    background: linear-gradient(180deg, #fff, #fbfdff);
    box-shadow: 0 6px 18px rgba(2, 6, 23, .06);
    white-space: nowrap;
}

.kpi-dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: linear-gradient(180deg, var(--brand), var(--brand-2));
    box-shadow: 0 0 0 3px rgba(225, 29, 72, .12);
}

/* ===== View Toolbar ===== */
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
    flex-wrap: wrap;
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
    font-weight: 800;
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
    flex-wrap: wrap;
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
    flex-wrap: wrap;
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

/* Card toko */
.item-toko {
    scroll-snap-align: start;
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    min-width: min(560px, 92vw);
    padding: 12px;
    border-radius: 16px;
    background:
        radial-gradient(450px 140px at 88% -30%, rgba(225, 29, 72, .10), transparent 60%),
        radial-gradient(450px 140px at 10% 130%, rgba(249, 115, 22, .10), transparent 60%),
        linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(255, 255, 255, .86));
    border: 1px solid var(--ring);
    box-shadow: var(--shadow-1);
    backdrop-filter: blur(5px);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    overflow: hidden;
    position: relative;
}

.item-toko:before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(225, 29, 72, .08), rgba(249, 115, 22, .06));
    opacity: 0;
    transition: .22s;
}

.item-toko:hover {
    transform: translateY(-3px);
    border-color: rgba(2, 6, 23, .18);
    box-shadow: var(--shadow-2);
}

.item-toko:hover:before {
    opacity: 1;
}

.item-toko .info {
    position: relative;
    z-index: 1;
    min-width: 0;
    padding: 6px 8px 6px 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.chips {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.chip-city {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .72rem;
    font-weight: 900;
    color: var(--txt);
    background: rgba(225, 29, 72, .08);
    border: 1px solid rgba(225, 29, 72, .14);
    padding: .28rem .62rem;
    border-radius: 999px;
}

.chip-city i {
    font-size: 16px;
    color: var(--brand);
}

.item-toko .nama {
    color: #0f172a;
    margin: 0;
    font-weight: 950;
    letter-spacing: -.3px;
    font-size: 1.08rem;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.item-toko .alamat {
    color: var(--muted);
    margin: 0;
    font-size: .88rem;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.mini-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.mini-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: .46rem .68rem;
    border-radius: 11px;
    border: 1px solid var(--ring-2);
    background: #fff;
    font-weight: 900;
    color: var(--txt);
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
    box-shadow: 0 6px 16px rgba(2, 6, 23, .06);
    user-select: none;
    font-size: .92rem;
}

.mini-btn i {
    font-size: 18px;
}

.mini-btn:hover {
    border-color: transparent;
    background: linear-gradient(90deg, var(--brand), var(--brand-2));
    color: #fff;
}

.badge-mini {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #fff;
    font-size: .7rem;
    font-weight: 800;
    background: linear-gradient(180deg, var(--brand), var(--brand-2));
    padding: .32rem .6rem;
    border-radius: 999px;
    box-shadow: 0 8px 24px rgba(225, 29, 72, .25);
}

.item-toko .thumb {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px 6px 6px 10px;
}

.item-toko img {
    width: 104px;
    height: 86px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid var(--ring);
    filter: saturate(.96) contrast(1.02);
    transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
    box-shadow: 0 10px 22px rgba(2, 6, 23, .14);
}

.item-toko:hover img {
    transform: translateY(-1px) scale(1.03);
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

/* ====== GRID & LIST ====== */
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
    background:
        radial-gradient(400px 120px at 90% -10%, rgba(225, 29, 72, .06), transparent 55%),
        linear-gradient(180deg, #fff, #fbfdff);
    border-radius: 16px;
    box-shadow: var(--shadow-1);
    transition: .2s;
    position: relative;
    overflow: hidden;
}

.grid-card:before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(225, 29, 72, .08), rgba(249, 115, 22, .06));
    opacity: 0;
    transition: .2s;
}

.grid-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-2);
    border-color: rgba(2, 6, 23, .16);
}

.grid-card:hover:before {
    opacity: 1;
}

.grid-card .g-info {
    position: relative;
    z-index: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.grid-card .g-name {
    margin: 0;
    font-weight: 900;
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

.grid-card .g-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .72rem;
    font-weight: 900;
    color: var(--txt);
    background: rgba(249, 115, 22, .08);
    border: 1px solid rgba(249, 115, 22, .14);
    padding: .24rem .55rem;
    border-radius: 999px;
    width: fit-content;
}

.grid-card .g-chip i {
    font-size: 16px;
    color: var(--brand-2);
}

.grid-card img {
    position: relative;
    z-index: 1;
    width: 112px;
    height: 92px;
    border-radius: 14px;
    object-fit: cover;
    border: 1px solid var(--ring);
    box-shadow: 0 10px 22px rgba(2, 6, 23, .12);
}

/* List */
.list-wrap {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.list-row {
    display: grid;
    grid-template-columns: 76px 1fr auto;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border: 1px solid var(--ring);
    border-radius: 14px;
    background:
        radial-gradient(300px 120px at 95% -20%, rgba(249, 115, 22, .08), transparent 60%),
        linear-gradient(180deg, #fff, #fbfdff);
    box-shadow: var(--shadow-1);
    transition: .2s;
}

.list-row:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-2);
    border-color: rgba(2, 6, 23, .16);
}

.list-row img {
    width: 76px;
    height: 64px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid var(--ring);
    box-shadow: 0 10px 20px rgba(2, 6, 23, .10);
}

.list-row .l-title {
    font-weight: 900;
    margin: 0;
    color: #0f172a;
    letter-spacing: -.2px;
}

.list-row .l-addr {
    margin: 4px 0 0 0;
    color: var(--muted);
    font-size: .9rem;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.list-row .l-meta {
    margin: 6px 0 0 0;
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.list-row .l-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .72rem;
    font-weight: 900;
    color: var(--txt);
    background: rgba(225, 29, 72, .08);
    border: 1px solid rgba(225, 29, 72, .14);
    padding: .24rem .55rem;
    border-radius: 999px;
}

.list-row .l-chip i {
    font-size: 16px;
    color: var(--brand);
}

.list-row .l-cta {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.list-row .l-cta a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 10px;
    border-radius: 12px;
    border: 1px solid var(--ring-2);
    text-decoration: none;
    color: #0f172a;
    font-weight: 900;
    box-shadow: 0 6px 16px rgba(2, 6, 23, .06);
    transition: .2s;
    white-space: nowrap;
}

.list-row .l-cta a:hover {
    border-color: transparent;
    background: linear-gradient(90deg, var(--brand), var(--brand-2));
    color: #fff;
}

/* ============ RESPONSIVE ============ */
@media (max-width:1200px) {
    .grid-wrap {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width:992px) {
    :root {
        --gap: 14px;
    }

    #map {
        height: 360px;
    }
}

/* HP / tablet kecil */
@media (max-width:768px) {

    /* kecilkan headline & sub */
    .section-title {
        font-size: 1.1rem;
        gap: 8px;
    }

    .section-sub {
        font-size: .85rem;
    }

    /* filterbar rapet & input lebih kecil */
    .filterbar {
        padding: 10px;
        gap: 10px;
    }

    .f-field {
        padding: 8px 10px;
        border-radius: 12px;
    }

    .f-field input {
        width: min(220px, 70vw);
        font-size: .9rem;
    }

    .f-select {
        font-size: .9rem;
        padding: 8px 10px;
    }

    /* tombol view lebih compact */
    .view-btn {
        padding: 7px 10px;
        font-size: .92rem;
    }

    .view-btn i {
        font-size: 17px;
    }

    /* rail: card mengecil */
    .item-toko {
        min-width: min(480px, 92vw);
        padding: 10px;
        border-radius: 14px;
    }

    .item-toko .nama {
        font-size: .98rem;
    }

    .item-toko .alamat {
        font-size: .82rem;
    }

    .mini-btn {
        font-size: .88rem;
        padding: .42rem .6rem;
        border-radius: 10px;
    }

    .item-toko img {
        width: 92px;
        height: 78px;
        border-radius: 12px;
    }

    #map {
        height: 300px;
    }

    .grid-card img {
        width: 98px;
        height: 82px;
    }
}

/* HP kecil banget */
@media (max-width:520px) {

    /* container lebih hemat */
    .container .konten {
        padding: 10px 10px 18px 10px;
        border-radius: 16px;
    }

    /* filterbar jadi stack full width */
    .filterbar {
        align-items: stretch;
    }

    .filter-left {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }

    .f-field,
    .f-select {
        width: 100%;
    }

    .f-field input {
        width: 100%;
    }

    /* KPI pindah ke kanan bawah/akhir (tetap rapi) */
    .kpi-pill {
        width: fit-content;
        align-self: flex-end;
    }

    /* view toolbar: tab wrap rapi */
    .view-tabs {
        width: 100%;
    }

    .view-meta {
        width: 100%;
        justify-content: flex-end;
        font-size: .85rem;
    }

    /* rail header: progress full width */
    .rail-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .rail-progress {
        width: 100%;
        max-width: 100%;
    }

    .toko-nav-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }

    /* rail card: lebih kecil + teks lebih kecil */
    .item-toko {
        min-width: 92vw;
        grid-template-columns: 1fr auto;
        padding: 10px;
    }

    .chip-city {
        font-size: .68rem;
        padding: .22rem .5rem;
    }

    .item-toko .nama {
        font-size: .95rem;
    }

    .item-toko .alamat {
        font-size: .78rem;
        -webkit-line-clamp: 2;
    }

    .mini-btn {
        font-size: .84rem;
        padding: .38rem .55rem;
    }

    .item-toko img {
        width: 82px;
        height: 68px;
    }

    /* map lebih pendek biar gak “full film” */
    #map {
        height: 240px;
        min-height: 220px;
    }

    /* grid jadi 1 kolom */
    .grid-wrap {
        grid-template-columns: 1fr;
    }

    /* list: jadi 2 kolom + CTA full width */
    .list-row {
        grid-template-columns: 72px 1fr;
        grid-auto-rows: auto;
        gap: 10px;
        padding: 10px;
    }

    .list-row img {
        width: 72px;
        height: 60px;
        border-radius: 12px;
    }

    .list-row .l-title {
        font-size: .95rem;
    }

    .list-row .l-addr {
        font-size: .82rem;
    }

    .list-row .l-cta {
        grid-column: 1 / -1;
        width: 100%;
        justify-content: stretch;
    }

    .list-row .l-cta a {
        width: 100%;
        justify-content: center;
        padding: 10px 12px;
        /* lebih enak dipencet */
    }
}

/* super kecil (opsional, biar aman) */
@media (max-width:380px) {
    .section-title {
        font-size: 1.02rem;
    }

    .badge-mini {
        font-size: .66rem;
    }

    .view-btn {
        padding: 7px 9px;
        font-size: .88rem;
    }

    .item-toko img {
        width: 78px;
        height: 64px;
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

        <!-- Filterbar -->
        <div class="filterbar">
            <div class="filter-left">
                <div class="f-field" title="Cari nama/alamat/kota">
                    <i class="material-icons">search</i>
                    <input id="q" type="text" placeholder="Cari mitra… (contoh: Surabaya / Ragunan / Agrapana)">
                </div>

                <select id="city" class="f-select" title="Filter kota">
                    <option value="">Semua Kota</option>
                </select>

                <select id="sort" class="f-select" title="Urutkan">
                    <option value="name_asc">Nama A–Z</option>
                    <option value="name_desc">Nama Z–A</option>
                    <option value="city_asc">Kota A–Z</option>
                    <option value="city_desc">Kota Z–A</option>
                </select>
            </div>

            <div class="kpi-pill">
                <span class="kpi-dot"></span>
                <span id="kpiCount">0 Mitra</span>
            </div>
        </div>

        <!-- Toolbar View -->
        <div class="view-toolbar">
            <div class="view-tabs" role="tablist" aria-label="Jenis tampilan">
                <button id="btnViewRail" class="view-btn" type="button" aria-pressed="true">
                    <i class="material-icons">view_carousel</i>Rail
                </button>
                <button id="btnViewGrid" class="view-btn" type="button" aria-pressed="false">
                    <i class="material-icons">grid_view</i>Grid
                </button>
                <button id="btnViewList" class="view-btn" type="button" aria-pressed="false">
                    <i class="material-icons">view_list</i>List
                </button>
            </div>
            <div class="view-meta"><span id="total-mitra">0 Mitra</span></div>
        </div>

        <!-- VIEW: RAIL -->
        <div class="toko-rail view-pane active" id="pane-rail">
            <div class="rail-head">
                <div class="rail-meta">
                    <span class="count-dot"></span>
                    <span id="label-count">Memuat mitra…</span>
                </div>
                <div class="rail-actions">
                    <input id="railProgress" type="range" min="0" max="100" value="0" class="rail-progress"
                        aria-label="Scroll progress">
                    <button class="toko-nav-btn toko-prev" type="button" aria-label="Sebelumnya">
                        <i class="material-icons" style="font-size:18px">chevron_left</i>
                    </button>
                    <button class="toko-nav-btn toko-next" type="button" aria-label="Berikutnya">
                        <i class="material-icons" style="font-size:18px">chevron_right</i>
                    </button>
                </div>
            </div>

            <div class="container-toko" id="container-toko"></div>
        </div>

        <!-- VIEW: GRID -->
        <div class="view-pane" id="pane-grid">
            <div class="grid-wrap" id="gridWrap"></div>
        </div>

        <!-- VIEW: LIST -->
        <div class="view-pane" id="pane-list">
            <div class="list-wrap" id="listWrap"></div>
        </div>

    </div>
</div>

<script>
/**
 * ====== SATU SUMBER DATA (MASTER) ======
 * Semua view + marker map akan dibangun dari sini.
 */
const MITRA = [{
        name: "Sumber Abadi Furniture",
        city: "Yogyakarta",
        address: "Jl. Magelang No. Km7, SENDANGADI, Mlati, Sleman, DI Yogyakarta 55285",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Magelang No. Km7, Sendangadi, Mlati, Sleman, DI Yogyakarta 55285"),
        img: "https://img.ilenafurniture.com/image/1742449853266.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-7.743533, 110.362422]
    },
    {
        name: "Tunggal Jaya Furniture",
        city: "Malang",
        address: "Jl. Kauman Dalam No.6, Kauman, Kec. Klojen, Kota Malang, Jawa Timur 65119",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Kauman Dalam No.6, Kauman, Klojen, Kota Malang, Jawa Timur 65119"),
        img: "https://i.ibb.co.com/tpBL6KrY/Screenshot-2025-12-13-034900.png",
        coords: [-7.982988941336538, 112.62939908349134]
    },
    {
        name: "Jempol Baru Furniture",
        city: "Solo",
        address: "Jl. R. M. Said, Keprabon, Banjarsari, Surakarta (Solo), Jawa Tengah 57131",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "JL RM. SAID, Keprabon, Banjarsari, Surakarta (Solo), Jawa Tengah 57131"),
        img: "https://img.ilenafurniture.com/image/1742449816363.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-7.565692379905529, 110.82430179811045]
    },
    {
        name: "Home Gallery",
        city: "Surabaya",
        address: "Pakuwon Trade Center Lantai LG Stand Promo Indah Lontar No 2, Jl. Puncak Permai Utara I A No.5, Babatan, Wiyung, Surabaya, Jawa Timur 60123",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Pakuwon Trade Center Lantai LG, Jl. Puncak Permai Utara I A No.5, Babatan, Wiyung, Surabaya"),
        img: "https://img.ilenafurniture.com/image/1742449923158.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-7.288629, 112.672618]
    },
    {
        name: "Pari Anom Jaya",
        city: "Surabaya",
        address: "Pasar Atum, Jl. Bunguran No.45 Lantai 4, Bongkaran, Pabean Cantikan, Surabaya, Jawa Timur 60161",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Pasar Atum, Jl. Bunguran No.45 Lantai 4, Surabaya"),
        img: "https://img.ilenafurniture.com/image/1742449972596.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-7.241680, 112.744704]
    },
    {
        name: "Suri Meubel",
        city: "Semarang",
        address: "Jl. MH Thamrin, Sekayu, Kec Semarang Tengah",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. MH Thamrin, Sekayu, Semarang Tengah, Semarang"),
        img: "https://img.ilenafurniture.com/image/1742449882169.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-6.985425, 110.417463]
    },
    {
        name: "Victoria Furnicenter",
        city: "Surabaya",
        address: "Jl. Raya Menganti Karangan No.578, Babatan, Wiyung, Surabaya, Jawa Timur 60227",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Raya Menganti Karangan No.578, Babatan, Wiyung, Surabaya, Jawa Timur 60227"),
        img: "https://img.ilenafurniture.com/image/1742450030786.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-7.310520, 112.683035]
    },
    {
        name: "Puri Mebel & Interior",
        city: "Semarang",
        address: "Jl. Puri Anjasmoro Blk. H5 No.57, Tawangsari, Semarang, Jawa Tengah 50144",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Puri Anjasmoro Blk. H5 No.57, Tawangsari, Semarang, Jawa Tengah 50144"),
        img: "https://img.ilenafurniture.com/image/1742450004899.webp/?apikey=<?= $apikey_img_ilena ?>",
        coords: [-6.965809, 110.390439]
    },
    {
        name: "Toko Mebel Garuda Mas",
        city: "Magelang",
        address: "Jl. Jend. Sudirman No.189, Tidar Sel., Kec. Magelang Sel., Kota Magelang, Jawa Tengah 59214",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Jend. Sudirman No.189, Tidar Selatan, Magelang Selatan, Kota Magelang"),
        img: "https://i.ibb.co.com/JRhJFZSV/Screenshot-2025-09-15-143456.png",
        coords: [-7.497210712603127, 110.22352521039721]
    },
    {
        name: "Cipta Bangun Jaya Furniture",
        city: "Jakarta Selatan",
        address: "Jl. Raya Ragunan No.51, Ps. Minggu, Kota Jakarta Selatan, DKI Jakarta 12520",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Raya Ragunan No.51, Pasar Minggu, Jakarta Selatan 12520"),
        img: "https://i.ibb.co.com/4R1CNqBL/Screenshot-2025-12-13-035035.png",
        coords: [-6.285252355503124, 106.84299797668308]
    },
    {
        name: "Homj Furniture",
        city: "Surabaya",
        address: "Jl. Jemusari III No.25, Jemur Wonosari, Kec. Wonocolo, Surabaya, Jawa Timur 60237",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Jemusari III No. 25, Jemur Wonosari, Wonocolo, Surabaya, Jawa Timur 60237"),
        img: "https://i.ibb.co.com/93zBts4Q/Screenshot-2025-12-13-035158.png",
        coords: [-7.321175506922994, 112.74408773425009]
    },
    {
        name: "Kasur Indah",
        city: "Denpasar",
        address: "Jl. Imam Bonjol No.403, Pemecutan Klod, Denpasar Barat, Bali 80119",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Imam Bonjol No.403, Pemecutan Klod, Denpasar Barat, Bali 80119"),
        img: "https://i.imgur.com/MwLYjVf.png",
        coords: [-8.696615, 115.186424]
    },
    {
        name: "Seruma Space",
        city: "Yogyakarta",
        address: "Sokowaten, Tamanan, Kec. Banguntapan, Kabupaten Bantul, DI Yogyakarta 55191",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Sokowaten, Tamanan, Banguntapan, Bantul, DI Yogyakarta 55191"),
        img: "https://i.ibb.co.com/DfJyv5HL/Screenshot-2025-09-15-143349.png",
        coords: [-7.8347557775250065, 110.37898663945798]
    },
    {
        name: "Living Home Houseware & Furniture",
        city: "Purwokerto",
        address: "Jl. KH. Wahid Hasyim No.88, Windusara, Karangklesem, Purwokerto Selatan, Banyumas, Jawa Tengah 53144",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. KH. Wahid Hasyim No.88, Windusara, Karangklesem, Purwokerto Selatan, Banyumas 53144"),
        img: "https://i.ibb.co.com/TDFP36LD/Screenshot-2025-08-19-234119.png",
        coords: [-7.446179, 109.243781]
    },
    {
        name: "Vinetta Furniture",
        city: "Purwokerto",
        address: "Jl. Komisaris Bambang Suprapto No.99, Cigrobak, Purwokerto Lor, Purwokerto Timur, Banyumas, Jawa Tengah 53114",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Komisaris Bambang Suprapto No.99, Purwokerto, Banyumas 53114"),
        img: "https://i.ibb.co.com/v4VDntfc/Screenshot-2025-08-22-142911.png",
        coords: [-7.423047, 109.250232]
    },
    {
        name: "Furni Center",
        city: "Purwokerto",
        address: "Jl. S. Parman No.125, Windusara, Karangklesem, Purwokerto Selatan, Banyumas, Jawa Tengah 53144",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. S. Parman No.125, Karangklesem, Purwokerto Selatan, Banyumas 53144"),
        img: "https://i.ibb.co.com/JR7y1B3Y/Screenshot-2025-08-22-144249.png",
        coords: [-7.438356, 109.244301]
    },
    {
        name: "Meubel Lisa Jaya",
        city: "Bangkalan (Madura)",
        address: "Timur Jalan SMPN 2 Bangkalan, JL. K.H. Hasyim Asyari No.13, Demangan Barat, Bangkalan, Jawa Timur 69115",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "JL. K.H. Hasyim Asyari No.13, Demangan Barat, Bangkalan, Jawa Timur 69115"),
        img: "https://i.ibb.co.com/ch0bNWzW/Screenshot-2025-09-15-143632.png",
        coords: [-7.0282709675175585, 112.74647900288844]
    },
    {
        name: "Agrapana Furniture (Godean)",
        city: "Yogyakarta",
        address: "Jl. Sidomoyo No.89, Cokro Konteng, Sidoarum, Kec. Godean, Sleman, DI Yogyakarta 55264",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Sidomoyo No.89, Sidoarum, Godean, Sleman, DI Yogyakarta 55264"),
        img: "https://i.ibb.co.com/fdsqzwFK/Screenshot-2025-09-15-143959.png",
        coords: [-7.196062871164613, 107.8984975832736]
    },
    {
        name: "Elok Meubel",
        city: "Tegal",
        address: "Jl. Mayjend DI. Panjaitan No.68/70, Tegalsari, Tegal Barat, Kota Tegal, Jawa Tengah 52111",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Mayjend DI. Panjaitan No.68/70, Tegalsari, Tegal Barat, Kota Tegal 52111"),
        img: "https://i.ibb.co.com/spnfJ946/Screenshot-2025-09-15-144115.png",
        coords: [-6.859464477608871, 109.13568596532647]
    },
    {
        name: "Meubel Murah",
        city: "Jakarta Timur",
        address: "Jl. Raya Kalimalang No.37 1, Duren Sawit, Jakarta Timur, DKI Jakarta 13440",
        href: "https://maps.app.goo.gl/37sFgPYmeYhp6Hvj8?g_st=aw",
        img: "https://i.ibb.co.com/0yc93DRK/Screenshot-2025-09-22-092249.png",
        coords: [-6.247639456432581, 106.91847865337301]
    },
    {
        name: "Terus Jaya Abadi Furniture",
        city: "Kab. Ciamis",
        address: "Jl. Ampera 1 No.11, Ciamis, Kec. Ciamis, Kabupaten Ciamis, Jawa Barat 46211",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Ampera 1 No.11, Ciamis, Jawa Barat 46211"),
        img: "https://i.ibb.co.com/zhzGJ0NT/Screenshot-2025-12-13-035311.png",
        coords: [-7.325306216951325, 108.35359338015712]
    },
    {
        name: "Agrapana Furniture (Garut)",
        city: "Garut",
        address: "Jl. Proklamasi, Jayaraga, Kec. Tarogong Kidul, Kabupaten Garut, Jawa Barat 44151",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Proklamasi, Jayaraga, Tarogong Kidul, Garut, Jawa Barat 44151"),
        img: "https://i.ibb.co.com/Mx8y2Rn9/DSCF1260.webp",
        coords: [-7.196089263091836, 107.89847351344686]
    },
    {
        name: "Raja Mebel Pamekasan",
        city: "Pamekasan (Madura)",
        address: "Jl. Kabupaten, Sumur Putih, Bugih, Kec. Pamekasan, Kabupaten Pamekasan, Jawa Timur 69316",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Kabupaten, Sumur Putih, Bugih, Pamekasan, Jawa Timur 69316"),
        img: "https://i.ibb.co.com/BHGNqyR6/JK.webp",
        coords: [-7.157671151259155, 113.47468982883595]
    },
    {
        name: "Mebel Irian",
        city: "Pasuruan",
        address: "Jl. Kh. Wachid Hasyim No.123, Kebonsari, Kec. Panggungrejo, Kota Pasuruan, Jawa Timur 67114",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Kh. Wachid Hasyim No.123, Kebonsari, Panggungrejo, Kota Pasuruan, Jawa Timur 67114"),
        img: "https://i.ibb.co.com/gF6bCFw3/Screenshot-2025-12-13-035417.png",
        coords: [-7.644566586400122, 112.90469485825982]
    },
    {
        name: "Sumber Rejeki Furniture",
        city: "Tasikmalaya",
        address: "Jl. Perintis Kemerdekaan No.51, Sambongjaya, Kec. Mangkubumi, Kab. Tasikmalaya, Jawa Barat 46181",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Perintis Kemerdekaan No.51, Sambongjaya, Mangkubumi, Tasikmalaya, Jawa Barat 46181"),
        img: "https://i.ibb.co.com/zVrDthFH/Screenshot-2025-12-13-035528.png",
        coords: [-7.357534958851788, 108.21717233004006]
    },
    {
        name: "Suka Indah",
        city: "Bandung",
        address: "Jl. Jend. Sudirman No.494, Ciroyom, Kec. Babakan Ciparay, Kota Bandung, Jawa Barat 40221",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Jend. Sudirman No.494, Ciroyom, Babakan Ciparay, Kota Bandung, Jawa Barat 40221"),
        img: "https://i.ibb.co.com/29Y8gR8/Screenshot-2025-12-13-035656.png",
        coords: [-6.918514533963292, 107.58623480039284]
    },
    {
        name: "Makmur Furni-store",
        city: "Cilacap",
        address: "Jl. Tugu Barat No.25, Sampang Selatan, Sampang, Kec. Sampang, Kabupaten Cilacap, Jawa Tengah 53273",
        href: "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(
            "Jl. Tugu Barat No.25, Sampang Selatan, Sampang, Cilacap, Jawa Tengah 53273"),
        img: "https://i.ibb.co.com/0Rgz9Nqr/Screenshot-2025-12-13-035809.png",
        coords: [-7.563000989154064, 109.19923900428012]
    },
];

function safeText(s) {
    return (s ?? "").toString();
}

function norm(s) {
    return safeText(s).toLowerCase().trim();
}

let state = {
    q: "",
    city: "",
    sort: "name_asc"
};
let viewItems = [...MITRA];

function uniqueCities(items) {
    const set = new Set(items.map(x => safeText(x.city)).filter(Boolean));
    return Array.from(set).sort((a, b) => a.localeCompare(b, 'id'));
}

function applyFilters() {
    let items = [...MITRA];
    const q = norm(state.q);
    if (q) {
        items = items.filter(m => (`${m.name} ${m.city} ${m.address}`.toLowerCase()).includes(q));
    }
    if (state.city) {
        items = items.filter(m => safeText(m.city) === state.city);
    }

    const byName = (a, b) => safeText(a.name).localeCompare(safeText(b.name), 'id');
    const byCity = (a, b) => safeText(a.city).localeCompare(safeText(b.city), 'id');

    switch (state.sort) {
        case "name_desc":
            items.sort((a, b) => byName(b, a));
            break;
        case "city_asc":
            items.sort((a, b) => byCity(a, b) || byName(a, b));
            break;
        case "city_desc":
            items.sort((a, b) => byCity(b, a) || byName(a, b));
            break;
        default:
            items.sort((a, b) => byName(a, b));
            break;
    }
    return items;
}

function buildRail(items) {
    const rail = document.getElementById("container-toko");
    rail.innerHTML = "";

    items.forEach((m) => {
        const div = document.createElement("div");
        div.className = "item-toko";
        div.innerHTML = `
      <div class="info">
        <div class="chips">
          <span class="chip-city"><i class="material-icons">location_on</i>${safeText(m.city)}</span>
        </div>
        <a href="${m.href}" style="text-decoration:none;" target="_blank" rel="noopener">
          <h3 class="nama">${safeText(m.name)}</h3>
          <p class="alamat m-0">${safeText(m.address)}</p>
        </a>
        <div class="mini-actions">
          <span class="mini-btn" data-open="${encodeURIComponent(m.href)}">
            <i class="material-icons">map</i> Maps
          </span>
          ${m.coords ? `
            <span class="mini-btn" data-fly="${m.coords[0]},${m.coords[1]}">
              <i class="material-icons">near_me</i> Fokus
            </span>
          ` : ``}
        </div>
      </div>
      <div class="thumb">
        <img loading="lazy" decoding="async" src="${m.img}" alt="${safeText(m.name)}">
      </div>
    `;
        rail.appendChild(div);
    });

    rail.querySelectorAll("[data-open]").forEach(el => {
        el.addEventListener("click", (e) => {
            e.preventDefault();
            const url = decodeURIComponent(el.getAttribute("data-open"));
            window.open(url, "_blank");
        });
    });

    rail.querySelectorAll("[data-fly]").forEach(el => {
        el.addEventListener("click", (e) => {
            e.preventDefault();
            const [lat, lng] = el.getAttribute("data-fly").split(",").map(Number);
            if (window.__ILENAMAP && Number.isFinite(lat) && Number.isFinite(lng)) {
                window.__ILENAMAP.setView([lat, lng], 14, {
                    animate: true
                });
            }
        });
    });
}

function buildGridList(items) {
    const gridWrap = document.getElementById("gridWrap");
    const listWrap = document.getElementById("listWrap");
    gridWrap.innerHTML = "";
    listWrap.innerHTML = "";

    items.forEach((m) => {
        const g = document.createElement("a");
        g.className = "grid-card";
        g.href = m.href;
        g.target = "_blank";
        g.rel = "noopener";
        g.style.textDecoration = "none";
        g.innerHTML = `
      <div class="g-info">
        <span class="g-chip"><i class="material-icons">location_on</i>${safeText(m.city)}</span>
        <p class="g-name">${safeText(m.name)}</p>
        <p class="g-addr">${safeText(m.address)}</p>
      </div>
      <img loading="lazy" decoding="async" src="${m.img}" alt="${safeText(m.name)}">
    `;
        gridWrap.appendChild(g);

        const l = document.createElement("div");
        l.className = "list-row";
        l.innerHTML = `
      <img loading="lazy" decoding="async" src="${m.img}" alt="${safeText(m.name)}">
      <div>
        <p class="l-title m-0">${safeText(m.name)}</p>
        <div class="l-meta">
          <span class="l-chip"><i class="material-icons">location_on</i>${safeText(m.city)}</span>
          ${m.coords ? `<span class="l-chip" style="background:rgba(249,115,22,.08);border-color:rgba(249,115,22,.14)"><i class="material-icons" style="color:var(--brand-2)">my_location</i>Map</span>` : ``}
        </div>
        <p class="l-addr">${safeText(m.address)}</p>
      </div>
      <div class="l-cta">
        ${m.coords ? `<a href="javascript:void(0)" data-fly="${m.coords[0]},${m.coords[1]}"><i class="material-icons" style="font-size:18px">near_me</i> Fokus</a>` : ``}
        <a href="${m.href}" target="_blank" rel="noopener">
          <i class="material-icons" style="font-size:18px">map</i> Lihat Peta
        </a>
      </div>
    `;
        listWrap.appendChild(l);
    });

    listWrap.querySelectorAll("[data-fly]").forEach(el => {
        el.addEventListener("click", () => {
            const [lat, lng] = el.getAttribute("data-fly").split(",").map(Number);
            if (window.__ILENAMAP && Number.isFinite(lat) && Number.isFinite(lng)) {
                window.__ILENAMAP.setView([lat, lng], 14, {
                    animate: true
                });
            }
        });
    });
}

function updateCounts(items) {
    const label = document.getElementById("label-count");
    const totalLabel = document.getElementById("total-mitra");
    const kpi = document.getElementById("kpiCount");
    const count = items.length;

    if (label) label.textContent = count ? `${count} Mitra Ditampilkan` : "0 Mitra";
    if (totalLabel) totalLabel.textContent = count ? `${count} Mitra` : "0 Mitra";
    if (kpi) kpi.textContent = count ? `${count} Mitra` : "0 Mitra";
}

function renderMap(items) {
    if (!window.__ILENAMAP) return;

    if (window.__ILENA_LAYERS) {
        window.__ILENA_LAYERS.forEach(x => window.__ILENAMAP.removeLayer(x));
    }
    window.__ILENA_LAYERS = [];

    items.forEach(function(store) {
        if (!store.coords || !Array.isArray(store.coords)) return;

        var brand = getComputedStyle(document.documentElement).getPropertyValue('--brand').trim() || '#e11d48';
        var brand2 = getComputedStyle(document.documentElement).getPropertyValue('--brand-2').trim() ||
            '#f97316';

        var marker = L.circleMarker(store.coords, {
            color: brand,
            fillColor: brand,
            fillOpacity: 0.18,
            stroke: true,
            weight: 0.8,
            radius: 10
        }).addTo(window.__ILENAMAP);

        var circle = L.circle(store.coords, {
            color: brand2,
            fillColor: brand2,
            fillOpacity: 0.14,
            radius: 70
        }).addTo(window.__ILENAMAP);

        const pop = `
      <div style="min-width:220px;font-family:system-ui">
        <div style="font-weight:900;color:#0f172a">${safeText(store.name)}</div>
        <div style="margin-top:4px;color:#64748b;font-weight:800;font-size:12px">${safeText(store.city)}</div>
        <div style="margin-top:8px;color:#475569;font-weight:700;font-size:12px;line-height:1.25">${safeText(store.address)}</div>
        <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
          <a href="${store.href}" target="_blank" rel="noopener"
             style="text-decoration:none;font-weight:900;padding:.45rem .7rem;border-radius:10px;border:1px solid rgba(15,23,42,.14);color:#0f172a;">
             Open Maps
          </a>
          <a href="https://www.google.com/maps?q=${store.coords[0]},${store.coords[1]}" target="_blank" rel="noopener"
             style="text-decoration:none;font-weight:900;padding:.45rem .7rem;border-radius:10px;border:0;color:#fff;background:linear-gradient(90deg,var(--brand),var(--brand-2));">
             Navigate
          </a>
        </div>
      </div>
    `;
        marker.bindPopup(pop);

        marker.on('mouseover', () => marker.openPopup());
        marker.on('mouseout', () => marker.closePopup());

        function go() {
            window.open("https://www.google.com/maps?q=" + store.coords[0] + "," + store.coords[1], "_blank");
        }
        marker.on('click', go);
        circle.on('click', go);

        window.__ILENA_LAYERS.push(marker, circle);
    });
}

function renderAll() {
    viewItems = applyFilters();
    buildRail(viewItems);
    buildGridList(viewItems);
    updateCounts(viewItems);
    renderMap(viewItems);
}

/* init filters */
(function() {
    const citySel = document.getElementById("city");
    uniqueCities(MITRA).forEach(c => {
        const opt = document.createElement("option");
        opt.value = c;
        opt.textContent = c;
        citySel.appendChild(opt);
    });

    document.getElementById("q").addEventListener("input", (e) => {
        state.q = e.target.value || "";
        renderAll();
    });
    document.getElementById("city").addEventListener("change", (e) => {
        state.city = e.target.value || "";
        renderAll();
    });
    document.getElementById("sort").addEventListener("change", (e) => {
        state.sort = e.target.value || "name_asc";
        renderAll();
    });
})();
</script>

<!-- Scroll helpers (rail) -->
<script>
(function() {
    const rail = document.getElementById('container-toko');
    const prev = document.querySelector('.toko-prev');
    const next = document.querySelector('.toko-next');
    const prog = document.getElementById('railProgress');
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
})();
</script>

<!-- View toggle -->
<script>
(function() {
    const btnRail = document.getElementById('btnViewRail');
    const btnGrid = document.getElementById('btnViewGrid');
    const btnList = document.getElementById('btnViewList');

    const paneRail = document.getElementById('pane-rail');
    const paneGrid = document.getElementById('pane-grid');
    const paneList = document.getElementById('pane-list');

    function setPressed(target) {
        [btnRail, btnGrid, btnList].forEach(b => b.setAttribute('aria-pressed', 'false'));
        target.setAttribute('aria-pressed', 'true');
    }

    function showPane(p) {
        [paneRail, paneGrid, paneList].forEach(x => x.classList.remove('active'));
        p.classList.add('active');
    }

    btnRail.addEventListener('click', () => {
        setPressed(btnRail);
        showPane(paneRail);
    });
    btnGrid.addEventListener('click', () => {
        setPressed(btnGrid);
        showPane(paneGrid);
    });
    btnList.addEventListener('click', () => {
        setPressed(btnList);
        showPane(paneList);
    });
})();
</script>

<!-- Map (Leaflet) -->
<script>
window.__ILENAMAP = L.map('map').setView([-7.614529, 110.712246], 6.5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(window.__ILENAMAP);

window.__ILENAMAP.on('click', function(e) {
    window.open("https://www.google.com/maps?q=" + e.latlng.lat + "," + e.latlng.lng, "_blank");
});

renderAll();
</script>

<?= $this->endSection(); ?>