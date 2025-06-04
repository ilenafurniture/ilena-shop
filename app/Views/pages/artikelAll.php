<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<style>
    .header {
        height: 90svh;
        width: 100%;
        min-height: 633px;
        max-height: 760px;
        color: white;
    }

    .header .container-header-img {
        width: 100%;
        height: 90svh;
        min-height: 633px;
        max-height: 760px;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .header .container-header-img img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        scale: 1;
        transition: 2s;
    }

    .header .container-header-img img.active {
        opacity: 1;
        transition: 2s;
        scale: 1.1;
    }

    .header .container-header-content {
        background-image: linear-gradient(to right, rgba(0, 0, 0, 0.7), transparent);
        width: 100%;
        height: 90svh;
        min-height: 633px;
        max-height: 760px;
        position: absolute;
        z-index: 2;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .header .container-header-content .deskripsi {
        max-height: 90px;
    }

    .header .container-header-content .deskripsi::-webkit-scrollbar {
        display: none;
    }

    @media (max-width: 600px) {

        .header,
        .header .container-header-img {
            height: 90svh;
        }

        .header .container-header-content {
            height: 90svh;
            flex-direction: column;
        }
    }

    .item-slider-header-img {
        background-color: rgba(255, 255, 255, 0.5);
        height: 10px;
        width: 10px;
        border-radius: 20px;
        transition: 0.5s;
    }

    .item-slider-header-img.active {
        width: 20px;
        background-color: white;
        transition: 0.5s;
    }

    .material-icons {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 22px;
    }

    .container-kategori-artikel {
        display: flex;
        gap: 20px;
        justify-content: start;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 1em;
    }

    .container-kategori-artikel::-webkit-scrollbar {
        display: none;
        /* display: block;
        width: 100px; */
    }

    .item-kategori-artikel {
        background-position: center;
        background-size: cover;
        text-decoration: none;
        width: calc((100% / 3) - 25px);
        max-width: 300px;
        border-radius: 10px;
        aspect-ratio: 3 / 4;
        flex: 0 0 auto;
        scroll-snap-align: start;
    }

    .item-kategori-artikel>div {
        display: flex;
        border-radius: 10px;
        flex-direction: column;
        justify-content: center;
        color: white;
        padding: 3em;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.3);
        transition: 0.5s;
    }

    .item-kategori-artikel:hover>div {
        background-color: rgba(0, 0, 0, 0.8);
        transition: 0.5s;
    }

    .item-kategori-artikel .judul {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
    }

    .item-kategori-artikel i {
        position: relative;
        transform: translateX(-20px);
        transition: 0.5s;
        opacity: 0;
    }

    .item-kategori-artikel:hover i {
        transform: translateX(0px);
        transition: 0.5s;
        opacity: 1;
    }

    .item-kategori-artikel .desc {
        max-height: 0;
        transition: 0.5s;
        overflow: hidden;
    }

    .item-kategori-artikel:hover .desc {
        max-height: 100px;
        transition: 0.5s;
    }

    @media (max-width: 700px) {
        .item-kategori-artikel {
            width: calc((100%) - 25px);
            aspect-ratio: 3 / 1;
        }
    }

    .btn-selengkapnya {
        display: block;
        text-decoration: none;
        font-size: 14px;
        color: black;
        width: fit-content;
    }

    .btn-selengkapnya>div {
        gap: 1em;
        transition: 0.4s;
    }

    .btn-selengkapnya:hover>div {
        gap: 2em;
        transition: 0.4s;
    }

    .btn-selengkapnya::after {
        content: '';
        display: block;
        width: 0%;
        height: 2px;
        background-color: black;
        transition: 0.2s;
    }

    .btn-selengkapnya:hover::after {
        width: 100%;
        transition: 0.2s;
    }

    .btn-selengkapnya p {
        margin: 0;
    }

    input[name="cari"] {
        width: 100%;
        border: none;
        border-bottom: 1px solid white;
        background: none;
        color: white;
    }

    input[name="cari"]:focus {
        border: none;
        outline: none;
        border-bottom: 1px solid white;
    }

    input[name="cari"]+button {
        background: none;
        cursor: pointer;
        outline: none;
        border: none;
        color: white;
    }
</style>
<div style="position: absolute; z-index: 4;" class="w-100 pt-4">
    <div class="container">
        <form action="/actionsearcharticle" method="post">
            <div class="d-flex align-items-center gap-3">
                <input type="text" name="cari" class="py-1" value="<?= isset($cari) ? $cari : ''; ?>">
                <button type="submit">
                    <i class="material-icons">search</i>
                </button>
            </div>
        </form>
    </div>
</div>
<div id="container-react-artikel"></div>
<script type="text/babel">
    function judulSplitter(judul) {
        const arrJudul = judul.split(" ");
        const judul1 = `${arrJudul[0]} ${arrJudul[1]}`;
        arrJudul.splice(0, 2);
        const panjangBagi2Up = Math.ceil(arrJudul.length / 2);
        const judul2 = arrJudul
            .filter((a, ind_a) => ind_a < panjangBagi2Up)
            .join(" ");
        const judul3 = arrJudul
            .filter((a, ind_a) => ind_a >= panjangBagi2Up)
            .join(" ");
        return { judul1, judul2, judul3 };
    }
    function titleCase(str) {
        var splitStr = str.toLowerCase().split(' ');
        for (var i = 0; i < splitStr.length; i++) {
            splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
        }
        return splitStr.join(' '); 
    }

    const { useState, useEffect, useRef } = React;
    const artikel3Baru = JSON.parse('<?= $artikel3BaruJson; ?>');
    const inisialisasiImgSelected = artikel3Baru[0]
    const dataCategoryFromBackend = '<?= isset($category) ? ucwords($category) : ''; ?>';
    console.log('Data category')
    console.log(dataCategoryFromBackend)
    const App = () => {
        const intervalRef = useRef(null);
        const [img, setImg] = useState(['active', '', '']);
        const [imgSelected, setImgSelected] = useState({
                ...judulSplitter(inisialisasiImgSelected.judul),
                deskripsi: inisialisasiImgSelected.deskripsi,  
                id: inisialisasiImgSelected.id,  
                kategori: titleCase(inisialisasiImgSelected.kategori),  
                path: inisialisasiImgSelected.path,  
                header: inisialisasiImgSelected.header,
            });
        const innerWidth = useRef(window.innerWidth);
        const categories = [
            {
                teks: 'Tips & Trik',
                link: '/article/category/tips-@-trik',
                gambar: 'https://cdn.pixabay.com/photo/2020/03/05/17/35/tips-4905013_1280.jpg',
                deskripsi: 'Lebih banyak tahu berbagai life hack yang mempermudah hidup' 
            },
            {
                teks: 'Edukasi',
                link: '/article/category/edukasi',
                gambar: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1122&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Beragam informasi seputar furniture & properti terbaru'   
            },
            {
                teks: 'Fun Fact',
                link: '/article/category/fun-fact',
                gambar: 'https://images.unsplash.com/photo-1676276375656-c4864c632a08?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Ketahui serba-serbi fakta menarik yang menginspirasi'   
            },
            {
                teks: 'Rekomendasi',
                link: '/article/category/rekomendasi',
                gambar: 'https://images.unsplash.com/photo-1624269305543-efbc8d89f1d3?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Pentingnya mendapatkan yang tepat sesuai kebutuhan'   
            },
        ];

        useEffect(() => {
            intervalRef.current = setInterval(() => {
                setImg((prev) => {
                    const indx = prev.indexOf("active") + 1 >= prev.length ? 0 : prev.indexOf("active") + 1;
                    return prev.map((p, ind_p) => (ind_p == indx ? "active" : ""));
                })
            }, 5000)
            return () => clearInterval(intervalRef.current)
        }, [])

        useEffect(() => {
            const indexOn = img.indexOf('active')
            const imgCur = {...artikel3Baru[indexOn]};
            setImgSelected({
                ...judulSplitter(imgCur.judul),
                deskripsi: imgCur.deskripsi,  
                id: imgCur.id,
                kategori: titleCase(imgCur.kategori),  
                path: imgCur.path,  
                header: imgCur.header,
            })
        }, [img])

        return (
            <>
                {!dataCategoryFromBackend ?
                    <div className="header">
                        <div className="container-header-content">
                            <div className="d-flex align-items-end container gap-5">
                                <div style={{ height: '100%', flex: '1' }}>
                                    <div>
                                        <p>{imgSelected.kategori}</p>
                                        <h5 className="teks-sedang">{imgSelected.judul1}</h5>
                                        <h1 className="teks-besar mb-3">{imgSelected.judul2} {imgSelected.judul3}</h1>
                                        <div style={{maxWidth: '500px', borderLeft: '3px solid white', overflowY: 'auto'}} className="ps-4 mb-3 deskripsi">
                                            <p className="m-0" style={{ color: 'rgb(219, 219, 219)'}}>{imgSelected.deskripsi}</p>
                                        </div>
                                        <div className="d-flex gap-2">
                                            <a href={`/article/${imgSelected.path}`} className="btn-lonjong-putih-outline">Baca selengkapnya</a>
                                        </div>
                                    </div>
                                </div>
                                <div className="d-flex align-items-center gap-2">
                                    <div className={`item-slider-header-img ${img[0]}`} onClick={()=>{
                                        setImg(img.map((e, ind)=>(ind == 0 ? 'active' : '')));
                                        clearInterval(intervalRef.current);
                                    }}></div>
                                    <div className={`item-slider-header-img ${img[1]}`} onClick={()=>{
                                        setImg(img.map((e, ind)=>(ind == 1 ? 'active' : '')));
                                        clearInterval(intervalRef.current);
                                    }}></div>
                                    <div className={`item-slider-header-img ${img[2]}`} onClick={()=>{
                                        setImg(img.map((e, ind)=>(ind == 2 ? 'active' : '')));
                                        clearInterval(intervalRef.current);
                                    }}></div>
                                </div>
                            </div>
                        </div>
                        <div className="container-header-img">
                            {artikel3Baru.map((a, ind_a) => (
                                <img key={ind_a} className={img[ind_a]} src={a.header} alt="" />
                            ))}
                        </div>
                    </div>
                :
                    <div style={{ height: '100svh', maxHeight: '400px', width: '100%', backgroundImage: `url(${categories.find((c) => c.teks == dataCategoryFromBackend).gambar})` }}>
                        <div style={{ backgroundColor: 'rgba(0,0,0,0.3)', height: '100%', width: '100%' }} className="d-flex justify-content-center align-items-center flex-column">
                            <h1 className="teks-besar mb-3 text-white">{categories.find((c) => c.teks == dataCategoryFromBackend).teks}</h1>
                            <p className="text-center text-white">{categories.find((c) => c.teks == dataCategoryFromBackend).deskripsi}</p>
                        </div>
                    </div>
                }
                <div className="container">
                    <div className={innerWidth.current < 700 ? 'py-4' : 'py-5'}>
                        <h1 className="teks-besar mb-5 show-block-ke-hide">Categories</h1>
                        <h1 className="teks-besar mb-3 hide-ke-show-block">Categories</h1>
                        <div className="container-kategori-artikel">
                            {categories.map((c, ind_c) => (
                                <a key={ind_c} href={c.link} style={{backgroundImage: `url(${c.gambar})`}} className="item-kategori-artikel">
                                    <div>
                                        <div className="d-flex justify-content-between">
                                            <p className="judul">{c.teks}</p>
                                            <i className="material-icons" style={{width: 'fit-content'}}>open_in_new</i>
                                        </div>
                                        <div className="desc">
                                            <p>{c.deskripsi}</p>
                                        </div>
                                    </div>
                                </a>
                            ))}
                        </div>
                    </div>
                </div>
            </>
        );
    }
    ReactDOM.render(<App />, document.getElementById("container-react-artikel"));
</script>
<div class="container">
    <hr>
    <div class="ubah-padding">
        <h3 class="teks-sedang mb-3">Popular now</h3>
        <div class="d-flex baris-ke-kolom">
            <?php foreach ($artikelPopuler as $ind_a => $a) { ?>
                <?php if ($ind_a < 3) { ?>
                    <?php if ($ind_a != 0) { ?>
                        <div class="bg-secondary show-block-ke-hide" style="width: 1px; opacity: 0.2"></div>
                    <?php } ?>
                    <div style="flex: 1;">
                        <div class="mb-2">
                            <span style="font-size: small; font-width: "><?= ucwords($a['kategori']); ?></span>
                            <span class="text-secondary" style="font-size: small;">•</span>
                            <span class="text-secondary"
                                style="font-size: small;"><?= date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu'])); ?></span>
                        </div>
                        <p class="fw-bold mb-1"><?= $a['judul']; ?></p>
                        <a href="/article/<?= $a['path']; ?>" class="btn-selengkapnya">
                            <div class="d-flex align-items-center">
                                <p>Baca selengkapnya</p>
                                <i class="material-icons">arrow_forward</i>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <hr>
    <style>
        .artikel-list-besar {
            background-position: center;
            background-size: 100%;
            border-radius: 15px;
            min-width: 425px;
            cursor: pointer;
            transition: 0.5s;
            position: relative;
            text-decoration: none;
            display: block;
        }

        .artikel-list-besar:hover {
            background-size: 110%;
            transition: 0.5s;
        }

        .artikel-list-besar>div {
            border-radius: 15px;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 2em;
            background-image: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            z-index: 3;
            position: relative;
        }

        .artikel-list-besar::before {
            content: '';
            display: block;
            border-radius: 15px;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0);
            transition: 0.5s;
            position: absolute;
            z-index: 2;
        }

        .artikel-list-besar:hover::before {
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            transition: 0.5s;
        }


        .artikel-list-besar .judul {
            font-weight: 600;
            color: white;
            font-size: 20px;
            margin: 0;
            max-width: 500px;
        }

        .artikel-list-besar .kategori {
            background-color: white;
            height: fit-content;
            display: block;
            padding: 0.3em 1em;
            font-weight: bold;
            border-radius: 2em;
            font-size: 14px;
            color: black;
        }

        .artikel-list-besar .tanggal {
            background-color: rgba(255, 255, 255, 0.3);
            height: fit-content;
            padding: 0.3em 1em;
            font-weight: bold;
            border-radius: 2em;
            font-size: 14px;
            color: white;
            width: fit-content;
        }



        .artikel-list-kecil {
            background-position: center;
            background-size: 100%;
            border-radius: 15px;
            /* min-width: 425px; */
            cursor: pointer;
            transition: 0.5s;
            position: relative;
            text-decoration: none;
        }

        .artikel-list-kecil:hover {
            background-size: 110%;
            transition: 0.5s;
        }

        .artikel-list-kecil>div {
            border-radius: 15px;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 2em;
            background-image: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            z-index: 3;
            position: relative;
        }

        .artikel-list-kecil::before {
            content: '';
            display: block;
            border-radius: 15px;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0);
            transition: 0.5s;
            position: absolute;
            z-index: 2;
        }

        .artikel-list-kecil:hover::before {
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            transition: 0.5s;
        }


        .artikel-list-kecil .judul {
            font-weight: 600;
            color: white;
            font-size: 20px;
            margin: 0;
        }

        .artikel-list-kecil .kategori {
            background-color: white;
            height: fit-content;
            display: block;
            padding: 0.3em 1em;
            font-weight: bold;
            border-radius: 2em;
            font-size: 14px;
            color: black;
        }

        .artikel-list-kecil .tanggal {
            background-color: rgba(255, 255, 255, 0.3);
            height: fit-content;
            padding: 0.3em 1em;
            font-weight: bold;
            border-radius: 2em;
            font-size: 14px;
            color: white;
            width: fit-content;
        }

        @media (max-width: 700px) {

            .artikel-list-kecil,
            .artikel-list-besar {
                min-width: 0px;
            }

            .artikel-list-besar .judul,
            .artikel-list-kecil .judul {
                font-size: 14px;
                margin-bottom: 10px;
            }

            .artikel-list-besar .kategori,
            .artikel-list-kecil .kategori {
                font-size: 10px;
                margin-bottom: 10px;
            }

            .artikel-list-besar .tanggal,
            .artikel-list-kecil .tanggal {
                font-size: 10px;
            }
        }
    </style>
    <div class="ubah-padding">
        <div class="baris-ke-kolom ubah-gap">
            <div style="flex: 2;">
                <a href="/article/<?= $artikel[0]['path']; ?>"
                    style="height: 100%; background-image: url(<?= $artikel[0]['header']; ?>);"
                    class="w-full artikel-list-besar">
                    <div>
                        <div style="flex: 1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= ucwords($artikel[0]['kategori'][0]); ?></snap>
                        </div>
                        <p class="tanggal show-block-ke-hide"><?= $artikel[0]['waktu']; ?></p>
                        <p class="judul"><?= $artikel[0]['judul']; ?></p>
                        <p class="tanggal hide-ke-show-block m-0"><?= $artikel[0]['waktu']; ?></p>
                    </div>
                </a>
            </div>
            <div style="flex: 1;" class="d-flex flex-column ubah-gap">
                <a href="/article/<?= $artikel[1]['path']; ?>"
                    style="height: 50%; background-image: url(<?= $artikel[1]['header']; ?>);"
                    class="w-full artikel-list-kecil">
                    <div>
                        <div style="flex: 1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= ucwords($artikel[1]['kategori'][0]); ?></snap>
                        </div>
                        <p class="tanggal show-block-ke-hide"><?= $artikel[1]['waktu']; ?></p>
                        <p class="judul"><?= $artikel[1]['judul']; ?></p>
                        <p class="tanggal hide-ke-show-block m-0"><?= $artikel[1]['waktu']; ?></p>
                    </div>
                </a>
                <a href="/article/<?= $artikel[2]['path']; ?>"
                    style="height: 50%; background-image: url(<?= $artikel[2]['header']; ?>);"
                    class="w-full artikel-list-kecil">
                    <div>
                        <div style="flex: 1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= ucwords($artikel[2]['kategori'][0]); ?></snap>
                        </div>
                        <p class="tanggal show-block-ke-hide"><?= $artikel[2]['waktu']; ?></p>
                        <p class="judul"><?= $artikel[2]['judul']; ?></p>
                        <p class="tanggal hide-ke-show-block m-0"><?= $artikel[2]['waktu']; ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <style>
        .container-artikel-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 995px) {
            .container-artikel-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .container-artikel-list .artikel-list {
            background-position: center;
            background-size: 100%;
            border-radius: 15px;
            /* min-width: 425px; */
            cursor: pointer;
            transition: 0.5s;
            position: relative;
            aspect-ratio: 1 / 1;
            text-decoration: none;
        }

        .container-artikel-list .artikel-list:hover {
            background-size: 110%;
            transition: 0.5s;
        }

        .container-artikel-list .artikel-list>div {
            border-radius: 15px;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 2em;
            background-image: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            z-index: 3;
            position: relative;
        }

        .container-artikel-list .artikel-list::before {
            content: '';
            display: block;
            border-radius: 15px;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0);
            transition: 0.5s;
            position: absolute;
            z-index: 2;
        }

        .container-artikel-list .artikel-list:hover::before {
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            transition: 0.5s;
        }


        .container-artikel-list .artikel-list .judul {
            font-weight: 600;
            color: white;
            font-size: 20px;
            margin: 0;
        }

        .container-artikel-list .artikel-list .kategori {
            background-color: white;
            height: fit-content;
            display: block;
            padding: 0.3em 1em;
            font-weight: bold;
            border-radius: 2em;
            font-size: 14px;
            color: black;
        }

        .container-artikel-list .artikel-list .tanggal {
            background-color: rgba(255, 255, 255, 0.3);
            height: fit-content;
            display: block;
            padding: 0.3em 1em;
            font-weight: bold;
            border-radius: 2em;
            font-size: 14px;
            color: white;
            width: fit-content;
        }

        .container-artikel-list-hp {
            display: none;
            flex-direction: column;
        }

        .container-artikel-list-hp .artikel-list {
            display: flex;
            width: 100%;
            text-decoration: none;
            color: black;
        }

        .container-artikel-list-hp .artikel-list .image {
            width: 100px;
        }

        .container-artikel-list-hp .artikel-list img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 10px;
        }

        .container-artikel-list-hp .artikel-list .tanggal {
            display: flex;
            align-items: start;
            gap: 6px;
        }

        .container-artikel-list-hp .artikel-list .tanggal span {
            font-size: 10px;
        }

        .container-artikel-list-hp .artikel-list .judul {
            font-size: 12px;
        }

        @media (max-width: 700px) {
            .container-artikel-list {
                display: none;
            }

            .container-artikel-list .artikel-list .judul {
                font-size: 14px;
            }

            .container-artikel-list .artikel-list .kategori,
            .container-artikel-list .artikel-list .tanggal {
                font-size: 10px;
            }

            .container-artikel-list-hp {
                display: flex;
            }
        }
    </style>
    <div class="container-artikel-list ubah-gap mb-5">
        <?php foreach ($artikel as $ind_a => $a) { ?>
            <?php if ($ind_a > 2) { ?>
                <a href="/article/<?= $a['path']; ?>" class="artikel-list" style="background-image: url(<?= $a['header']; ?>);">
                    <div>
                        <div style="flex: 1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= ucwords($a['kategori'][0]); ?></snap>
                        </div>
                        <p class="tanggal"><?= $a['waktu']; ?></p>
                        <p class="judul"><?= $a['judul']; ?></p>
                    </div>
                </a>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="container-artikel-list-hp gap-3 mb-5">
        <?php foreach ($artikel as $ind_a => $a) { ?>
            <?php if ($ind_a > 2) { ?>
                <a href="/article/<?= $a['path']; ?>" class="artikel-list gap-3">
                    <div class="image">
                        <img src="<?= $a['header']; ?>" alt="">
                    </div>
                    <div style="flex: 1;">
                        <div class="mb-1 tanggal">
                            <span><?= ucwords($a['kategori'][0]); ?></span>
                            <span class="text-secondary">•</span>
                            <span
                                class="text-secondary"><?= date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu'])); ?></span>
                        </div>
                        <p class="m-0 fw-bold judul"><?= $a['judul']; ?></p>
                    </div>
                </a>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<script>
    const ubahPaddingElm = document.querySelectorAll('.ubah-padding')
    const innerWidthClient = window.innerWidth;
    ubahPaddingElm.forEach((e) => {
        e.classList.add(innerWidthClient > 700 ? 'py-4' : 'py-3')
    })
    const ubahGapElm = document.querySelectorAll('.ubah-gap')
    ubahGapElm.forEach((e) => {
        e.classList.add(innerWidthClient > 700 ? 'gap-4' : 'gap-3')
    })
</script>
<?= $this->endSection(); ?>