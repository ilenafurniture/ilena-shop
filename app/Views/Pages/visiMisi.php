<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
    .teks-visi {
        cursor: default;
        text-align: center;
    }

    .teks-visi h2 {
        display: inline;
        font-size: 30px;
        letter-spacing: -1px;
        margin: 0;
        /* color: grey; */
        transition: 0.5s;
    }

    /* .teks-visi h2::after {
        content: '';
        display: block;
        width: 100px;
        height: 20px;
        background-color: var(--merah);
        position: ;
    } */

    .teks-visi h5 {
        font-size: 20px;
        margin: 0;
        color: grey;
        letter-spacing: -1px;
        display: inline;
        transition: 0.5s;
    }

    .teks-visi:hover h2 {
        font-size: 50px;
        letter-spacing: -3px;
        color: var(--merah);
        font-weight: 600;
        font-size: 48px;
        transition: 0.5s;
    }

    .teks-visi:hover h5 {
        color: black;
        transition: 0.5s;
    }

    .logo-rumah {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        /* background-color: plum; */
    }

    @keyframes nlogo {
        to {
            scale: 0.5;
        }
    }

    .logo-rumah #nlogo {
        scale: 1;
        width: 300px;
        aspect-ratio: 1 / 1;
        animation: nlogo linear forwards;
        animation-timeline: view();
        animation-range: 400px 600px;
    }

    @keyframes atap {
        to {
            opacity: 1;
            margin-bottom: -120px;
            /* scale: 1; */
        }
    }

    .logo-rumah #atap {
        /* scale: 0.6; */
        opacity: 0;
        height: 100px;
        margin-bottom: -100px;
        animation: atap linear forwards;
        animation-timeline: view();
        animation-range: 500px 600px;
    }

    @keyframes tangan {
        to {
            opacity: 1;
            margin-top: -180px;
            scale: 1;
        }
    }

    .logo-rumah #tangan {
        height: 200px;
        margin-top: -200px;
        opacity: 0;
        scale: 0.5;
        animation: tangan linear forwards;
        animation-timeline: view();
        animation-range: 350px 500px;

    }

    .logo-rumah #kiri {
        height: 100%;
        opacity: 1;
    }

    .logo-rumah #kanan {
        height: 100%;
        opacity: 1;
    }

    @keyframes teksramah {
        to {
            gap: 1em;
            max-height: 20svh;
            margin-top: 4em;
        }
    }

    .container-teks-ramah {
        margin-top: 0;
        max-height: 0;
        overflow: hidden;
        display: flex;
        justify-content: center;
        gap: 0.5em;
        animation: teksramah linear forwards;
        animation-timeline: view();
        animation-range: 200px 300px;
    }


    .container-teks-ramah .item-teks-ramah {
        cursor: default;
        overflow: hidden;
        transition: 1s;
        /* scale: 0.7; */
        /* background-color: aqua; */
        transition: 1s;
    }

    .container-teks-ramah .item-teks-ramah h1 {
        font-weight: 700;
        display: block;
        letter-spacing: -3px;
        color: grey;
        transition: 1s;
    }

    .container-teks-ramah .item-teks-ramah h1.huruf-ramah-add {
        overflow: hidden;
        font-size: 60px;
        line-height: 46px;
        max-width: 0;
    }

    .container-teks-ramah .item-teks-ramah h1.huruf-ramah {
        font-size: 100px;
        line-height: 75px;
        max-width: 300px;
    }

    .container-teks-ramah .item-teks-ramah p {
        display: block;
        max-height: 0;
        overflow: hidden;
        transition: 1s;
        max-width: 0;
        text-wrap: nowrap;
        margin-left: 0;
    }

    /* .container-teks-ramah .item-teks-ramah:hover {
        scale: 0.8s;
        transition: 1s;
        } */

    .container-teks-ramah .item-teks-ramah:hover h1 {
        color: var(--merah);
        transition: 1s;
    }

    .container-teks-ramah .item-teks-ramah:hover h1.huruf-ramah-add {
        max-width: 100vw;
        transition: 1s;
    }

    .container-teks-ramah .item-teks-ramah:hover p {
        max-height: 200px;
        transition: 1s;
        max-width: 300px;
        margin-left: 1em;
    }

    @keyframes containercoorporate {
        to {
            height: 750px;
        }
    }

    .container-coorporate {
        height: 300px;
        overflow: hidden;
        /* background-color: aqua; */
        animation: containercoorporate linear forwards;
        animation-timeline: view();
        animation-range: 300px 70svh;
        display: flex;
        /* flex-column justify-content-center align-items-center  */
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    @media (max-width: 600px) {
        .container-coorporate {
            display: none;
        }
    }
</style>
<!-- <div style="z-index: 99; position: fixed; top: 300px; left: 0; width: 100vw; height: 2px; background-color: red"></div>
<div style="z-index: 99; position: fixed; top: 500px; left: 0; width: 100vw; height: 2px; background-color: blue"></div> -->
<div class="container d-flex justify-content-center">
    <div class="konten">
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Visi dan Misi
                </li>
            </ol>
        </nav>
        <h1 class="text-center teks-besar">Visi</h1>
        <hr class="my-4">
        <!-- <h5 class="mb-4 text-center" style="color: var(--hijau)">Ilena Furniture</h5> -->
        <!-- <div class="teks-visi">
            <h5>Begin at</h5>
            <h2>home</h2>
            <h5>, a friendly smile</h5>
            <h2>blossoms</h2>
        </div> -->
        <div class="teks-visi mb-4 show-block-ke-hide">
            <h5>Mulai dari </h5>
            <h2>rumah</h2>
            <h5>, Tercipta senyum</h5>
            <h2>ramah</h2>
        </div>
        <div class="teks-visi mb-4 hide-ke-show-block">
            <h5>Mulai dari </h5>
            <h2>rumah</h2><br>
            <h5>Tercipta senyum</h5>
            <h2>ramah</h2>
        </div>
        <!-- <p class="text-center mt-2 teks-sedang">Mulai dari <b>Rumah</b>, Tercipta senyum <b>ramah</b></p> -->
        <!-- <hr class="my-4"> -->
        <p style="margin-bottom: 4em;">Ilena hadir di tengah-tengah masyarakat Indonesia dengan mengusung visi: mulai dari rumah, tercipta senyum ramah. Satu kalimat ini menjadi representasi bahwa Ilena dengan tangan terbuka akan selalu ada bagi masyarakat Indonesia melalui produk unggulan yang jadi solusi tepat melengkapi interior jadi lebih sempurna.</p>
        <h1 class="text-center teks-besar">Our Culture</h1>
        <hr>
        <p>Kami berkomitmen untuk terus berkembang dan berinovasi dalam menghasilkan produk unggulan dengan penuh <b style="color: var(--merah); font-weight:700;">RAMAH</b>, sebagai pilar utama yang tersampaikan pada rangkaian berikut ini:</p>
        <div class="container-coorporate" style="margin-bottom: 4em;">
            <div class="logo-rumah">
                <img src="<?= base_url('img/Ramah Ilena/atap.png'); ?>" alt="" id="atap">
                <img src="<?= base_url('img/Ramah Ilena/N.png'); ?>" alt="" id="nlogo">
                <div class="d-flex justify-content-center" id="tangan">
                    <img src="<?= base_url('img/Ramah Ilena/tangan kiri.png'); ?>" alt="" id="kiri">
                    <img src="<?= base_url('img/Ramah Ilena/tangan kanan.png'); ?>" alt="" id="kanan">
                </div>
            </div>
            <div class="container-teks-ramah">
                <div class="item-teks-ramah">
                    <div class="d-flex align-items-end">
                        <h1 class="huruf-ramah">R</h1>
                        <h1 class="huruf-ramah-add">AGAM</h1>
                    </div>
                    <p>Toleransi terhadap segala bentuk<br>
                        perbedaan dan menjadikannya sebagai<br>
                        semangat dalam mencapai tujuan<br>
                        bersama</p>
                </div>
                <div class="item-teks-ramah">
                    <div class="d-flex align-items-end">
                        <h1 class="huruf-ramah">A</h1>
                        <h1 class="huruf-ramah-add">MANAH</h1>
                    </div>
                    <p>Menjaga kualitas produk dan<br>
                        pelayanan terhadap konsumen<br>
                        secara maksimal</p>
                </div>
                <div class="item-teks-ramah">
                    <div class="d-flex align-items-end">
                        <h1 class="huruf-ramah">M</h1>
                        <h1 class="huruf-ramah-add">ODERN</h1>
                    </div>
                    <p>Tertuju pada modernisasi<br>
                        dengan menciptakan efisiensi<br>
                        terhadap produk</p>
                </div>
                <div class="item-teks-ramah">
                    <div class="d-flex align-items-end">
                        <h1 class="huruf-ramah">A</h1>
                        <h1 class="huruf-ramah-add">DAPTIF</h1>
                    </div>
                    <p>Terbuka dengan perkembangan<br>
                        zaman dan teknologi yang mempengaruhi<br>
                        perubahan kebutuhan serta<br>
                        keinginan manusia</p>
                </div>
                <div class="item-teks-ramah">
                    <div class="d-flex align-items-end">
                        <h1 class="huruf-ramah">H</h1>
                        <h1 class="huruf-ramah-add">ARMONIS</h1>
                    </div>
                    <p>Menciptakan relasi yang<br>
                        sehat antara karyawan dan perusahaan<br>
                        sebagai satu kesatuan yang<br>
                        tidak bisa terpisahkan</p>
                </div>
            </div>
        </div>
        <div class="hide-ke-show-block" style="margin-bottom: 4em;">
            <div class="d-flex justify-content-center w-100 my-3">
                <img src="<?= base_url('img/Ramah Ilena/Ramah Ilena Full.png'); ?>" alt="" style="width: 80%;">
            </div>
            <h1 class="teks-sedang"><b style="color: var(--merah)">R</b>agam</h1>
            <p class="mb-3">Toleransi terhadap segala bentuk
                perbedaan dan menjadikannya sebagai
                semangat dalam mencapai tujuan
                bersama</p>
            <h1 class="teks-sedang"><b style="color: var(--merah)">A</b>manah</h1>
            <p class="mb-3 ">Menjaga kualitas produk dan pelayanan terhadap konsumen secara maksimal</p>
            <h1 class="teks-sedang"><b style="color: var(--merah)">M</b>odern</h1>
            <p class="mb-3 ">Tertuju pada modernisasi dengan menciptakan efisiensi terhadap produk</p>
            <h1 class="teks-sedang"><b style="color: var(--merah)">A</b>daptif</h1>
            <p class="mb-3 ">Terbuka dengan perkembangan zaman dan teknologi yang mempengaruhi perubahan kebutuhan serta keinginan manusia</p>
            <h1 class="teks-sedang"><b style="color: var(--merah)">H</b>armonis</h1>
            <p>Menciptakan relasi yang sehat antara karyawan dan perusahaan sebagai satu kesatuan yang tidak bisa terpisahkan</p>
        </div>
        <h1 class="text-center teks-besar">Misi</h1>
        <hr>
        <div class="baris-ke-kolom">
            <div style="flex: 1;">
                <h1 class="teks-sedang mb-2">Membangun & Menjaga Kepercayaan Konsumen</h1>
                <p>Menjadikan kepuasan konsumen sebagai prioritas melalui pelayanan prima dan produk unggulan</p>
            </div>
            <div style="flex: 1;">
                <h1 class="teks-sedang mb-2">Mendukung Keseimbangan Ekosistem Lingkungan</h1>
                <p>Menjaga lingkungan dan kelanjutan ekosistem hutan dengan hanya menggunakan material bahan yang jelas asal-usulnya</p>
            </div>
            <div style="flex: 1;">
                <h1 class="teks-sedang mb-2">Memiliki Satu Tujuan Bersama</h1>
                <p>Membangun tim yang solid dan bersinergi sesuai dengan nilai perusahaan untuk terus berkembang dan mencapai satu tujuan bersama</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>