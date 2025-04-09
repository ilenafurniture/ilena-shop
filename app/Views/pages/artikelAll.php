<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<style>
    .header {
        height: 90svh;
        width: 100%;
        min-height: 633px;
        max-height: 760px;
        background-color: aqua;
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
        gap: 10px;
        background-color: aqua;
        justify-content: start;
    }

    .item-kategori-artikel {
        background-position: center;
        background-size: cover;
        text-decoration: none;
        display: flex;
        align-items: center;
        color: white;
        width: calc(30%);
        justify-content: center;
        border-radius: 10px;
        aspect-ratio: 3 / 4;
    }

    .item-kategori-artikel p {
        margin: 0;
    }
</style>
<div id="container-react-artikel" class="tampilHp"></div>
<script type="text/babel">
    const { useState, useEffect, useRef } = React;
    const App = () => {
        const [img, setImg] = useState(['active', '', '']);
        const categories = [
            {
                teks: 'Olahraga',
                link: '/article/category/olahraga',
                gambar: 'https://images.unsplash.com/photo-1733509213080-db2aca1bc244?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            },
            {
                teks: 'Tips Trik',
                link: '/article/category/tips-trik',
                gambar: 'https://images.unsplash.com/photo-1739989934265-b46240484890?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            },
            {
                teks: 'Home',
                link: '/article/category/olahraga',
                gambar: 'https://images.unsplash.com/photo-1742238619061-c4470b8c0372?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            },
            {
                teks: 'News',
                link: '/article/category/olahraga',
                gambar: 'https://plus.unsplash.com/premium_photo-1681336549369-ee96c6ca07b5?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            },
        ];

        return (
            <>
                <div className="header">
                    <div className="container-header-content">
                        <div className="d-flex align-items-end container gap-5">
                            <div style={{ height: '100%', flex: '1' }}>
                                <div>
                                    <p>Tips & trik</p>
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
                            </div>
                            <div className="d-flex align-items-center gap-2">
                                <div className={`item-slider-header-img ${img[0]}`} onClick={()=>{setImg(img.map((e, ind)=>(ind == 0 ? 'active' : '')))}}></div>
                                <div className={`item-slider-header-img ${img[1]}`} onClick={()=>{setImg(img.map((e, ind)=>(ind == 1 ? 'active' : '')))}}></div>
                                <div className={`item-slider-header-img ${img[2]}`} onClick={()=>{setImg(img.map((e, ind)=>(ind == 2 ? 'active' : '')))}}></div>
                            </div>
                        </div>
                    </div>
                    <div className="container-header-img">
                        <img className={img[0]} src="https://images.unsplash.com/photo-1742827871492-72428a28106b?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="" />
                        <img className={img[1]} src="https://images.unsplash.com/photo-1742654230711-f938802dea76?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="" />
                        <img className={img[2]} src="https://images.unsplash.com/photo-1742646895349-93c71c08e693?q=80&w=1013&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="" />
                    </div>
                </div>
                <div className="container">
                    <div style={{ paddingBlock: '3em' }}>
                        <h1 className="teks-besar mb-3">Article categories</h1>
                            <div className="container-kategori-artikel">
                                {categories.map((c, ind_c) => (
                                    // <a key={ind_c} href={c.link} style={{backgroundImage: `url(${c.gambar})`}} className="item-kategori-artikel">
                                    //     <p>{c.teks}</p>
                                    //     <i className="material-icons">open_in_new</i>
                                    // </a>
                                    <div key={ind_c} className="item-kategori-artikel">

                                    </div>
                                ))}
                            </div>
                    </div>
                </div>
            </>
        );
    }
    ReactDOM.render(<App />, document.getElementById("container-react-artikel"));
</script>
<?= $this->endSection(); ?>