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
        max-height: 760px;
        min-height: 633px;
        position: absolute;
        z-index: 2;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    @media (max-width: 600px) {

        .header,
        .header .container-header-img {
            height: 70svh;
        }

        .header .container-header-content>div {
            height: 70svh;
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
</style>
<div id="container-react-artikel"></div>
<script type="text/babel">
    const { useState, useEffect, useRef } = React;
    const artikel3Baru = JSON.parse('<?= $artikel3BaruJson; ?>');
    console.log(artikel3Baru)
    const App = () => {
        const [img, setImg] = useState(['active', '', '']);
        const [imgSelected, setImgSelected] = useState(null);
        const categories = [
            {
                teks: 'Olahraga',
                link: '/article/category/olahraga',
                gambar: 'https://images.unsplash.com/photo-1733509213080-db2aca1bc244?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, ex soluta aut aliquam vitae at '   
            },
            {
                teks: 'Tips Trik',
                link: '/article/category/tips-trik',
                gambar: 'https://images.unsplash.com/photo-1739989934265-b46240484890?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, ex soluta aut aliquam vitae at '   
            },
            {
                teks: 'Home',
                link: '/article/category/olahraga',
                gambar: 'https://images.unsplash.com/photo-1742238619061-c4470b8c0372?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, ex soluta aut aliquam vitae at '   
            },
            {
                teks: 'News',
                link: '/article/category/olahraga',
                gambar: 'https://plus.unsplash.com/premium_photo-1681336549369-ee96c6ca07b5?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, ex soluta aut aliquam vitae at '   
            },
            {
                teks: 'Home',
                link: '/article/category/olahraga',
                gambar: 'https://images.unsplash.com/photo-1742238619061-c4470b8c0372?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, ex soluta aut aliquam vitae at '   
            },
            {
                teks: 'News',
                link: '/article/category/olahraga',
                gambar: 'https://plus.unsplash.com/premium_photo-1681336549369-ee96c6ca07b5?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                deskripsi: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, ex soluta aut aliquam vitae at '   
            },
        ];

        useEffect(() => {
            const interval = setInterval(() => {
                setImg((prev) => {
                    const indx = prev.indexOf("active") + 1 >= prev.length ? 0 : prev.indexOf("active") + 1;
                    return prev.map((p, ind_p) => (ind_p == indx ? "active" : ""));
                })
            }, 5000)
            return () => clearInterval(interval)
        }, [])

        useEffect(() => {
            const indexOn = img.indexOf('active')
            const imgCur = {...artikel3Baru[indexOn]};
            setImgSelected({
                judul: imgCur.judul,
            })
        }, [img])
        
        useEffect(() => {
            console.log('img selected')
            console.log(imgSelected)
        }, [imgSelected])

        return (
            <>
                <div className="header">
                    <div className="container-header-content">
                        <div className="d-flex align-items-end container gap-5">
                            <div style={{ height: '100%', flex: '1' }}>
                                {imgSelected &&
                                    <div>
                                        <p>{imgSelected}</p>
                                        <h5 className="teks-sedang">INTO THE</h5>
                                        <h1 className="teks-besar">WILD THE BIGGEST</h1>
                                        <h1 className="teks-besar mb-3">RARE MOMENTs</h1>
                                        <div style={{maxWidth: '500px', borderLeft: '3px solid white'}} className="ps-4">
                                            <p className="mb-4" style={{ color: 'rgb(219, 219, 219)'}}>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor provident deserunt soluta corporis distinctio quia culpa maiores, esse quibusdam perspiciatis.</p>
                                        </div>
                                        <div className="d-flex gap-2">
                                            <a href="" className="btn-lonjong-putih"><i className="material-icons">bookmark_border</i></a>
                                            <a href="" className="btn-lonjong-putih-outline">Baca selengkapnya</a>
                                        </div>
                                    </div>
                                }
                            </div>
                            <div className="d-flex align-items-center gap-2">
                                <div className={`item-slider-header-img ${img[0]}`} onClick={()=>{setImg(img.map((e, ind)=>(ind == 0 ? 'active' : '')))}}></div>
                                <div className={`item-slider-header-img ${img[1]}`} onClick={()=>{setImg(img.map((e, ind)=>(ind == 1 ? 'active' : '')))}}></div>
                                <div className={`item-slider-header-img ${img[2]}`} onClick={()=>{setImg(img.map((e, ind)=>(ind == 2 ? 'active' : '')))}}></div>
                            </div>
                        </div>
                    </div>
                    <div className="container-header-img">
                        {artikel3Baru.map((a, ind_a) => (
                            <img key={ind_a} className={img[ind_a]} src={a.header} alt="" />
                        ))}
                    </div>
                </div>
                <div className="container">
                    <div className="py-5">
                        <h1 className="teks-besar mb-5 show-block-ke-hide">Article categories</h1>
                        <h1 className="teks-besar mb-3 hide-ke-show-block">Article<br />categories</h1>
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
    <div class="py-4">
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
                            <span class="text-secondary" style="font-size: small;"><?= date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu'])); ?></span>
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
    <div class="py-4">
        <div class="baris-ke-kolom">
            <div style="flex: 1;">
                <img src="" alt="">
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>