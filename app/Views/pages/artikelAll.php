<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php $isEnglish = service('request')->getLocale() === 'en'; ?>

<style>
/* ====== HERO ====== */
.header {
    height: 90svh;
    width: 100%;
    min-height: 633px;
    max-height: 760px;
    color: #fff
}

.header .container-header-img {
    width: 100%;
    height: 90svh;
    min-height: 633px;
    max-height: 760px;
    position: relative;
    z-index: 1;
    overflow: hidden
}

.header .container-header-img img {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    scale: 1;
    transition: 2s
}

.header .container-header-img img.active {
    opacity: 1;
    transition: 2s;
    scale: 1.1
}

.header .container-header-content {
    background-image: linear-gradient(to right, rgba(0, 0, 0, .7), transparent);
    width: 100%;
    height: 90svh;
    min-height: 633px;
    max-height: 760px;
    position: absolute;
    z-index: 2;
    display: flex;
    justify-content: center;
    align-items: center
}

.header .container-header-content .deskripsi {
    max-height: 90px
}

.header .container-header-content .deskripsi::-webkit-scrollbar {
    display: none
}

@media (max-width:600px) {

    .header,
    .header .container-header-img {
        height: 90svh
    }

    .header .container-header-content {
        height: 90svh;
        flex-direction: column
    }
}

.item-slider-header-img {
    background-color: rgba(255, 255, 255, .5);
    height: 10px;
    width: 10px;
    border-radius: 20px;
    transition: .5s
}

.item-slider-header-img.active {
    width: 20px;
    background-color: #fff;
    transition: .5s
}

.material-icons {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 22px
}

/* ====== KATEGORI SCROLLER ====== */
.container-kategori-artikel {
    display: flex;
    gap: 20px;
    justify-content: start;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    padding-bottom: 1em
}

.container-kategori-artikel::-webkit-scrollbar {
    display: none
}

.item-kategori-artikel {
    background-position: center;
    background-size: cover;
    text-decoration: none;
    width: calc((100% / 3) - 25px);
    max-width: 300px;
    border-radius: 10px;
    aspect-ratio: 3/4;
    flex: 0 0 auto;
    scroll-snap-align: start
}

.item-kategori-artikel>div {
    display: flex;
    border-radius: 10px;
    flex-direction: column;
    justify-content: center;
    color: #fff;
    padding: 3em;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .3);
    transition: .5s
}

.item-kategori-artikel:hover>div {
    background-color: rgba(0, 0, 0, .8);
    transition: .5s
}

.item-kategori-artikel .judul {
    margin: 0;
    font-size: 20px;
    font-weight: 700
}

.item-kategori-artikel i {
    position: relative;
    transform: translateX(-20px);
    transition: .5s;
    opacity: 0
}

.item-kategori-artikel:hover i {
    transform: translateX(0);
    transition: .5s;
    opacity: 1
}

.item-kategori-artikel .desc {
    max-height: 0;
    transition: .5s;
    overflow: hidden
}

.item-kategori-artikel:hover .desc {
    max-height: 100px;
    transition: .5s
}

@media (max-width:700px) {
    .item-kategori-artikel {
        width: calc(100% - 25px);
        aspect-ratio: 3/1
    }
}

/* ====== LINK "SELENGKAPNYA" ====== */
.btn-selengkapnya {
    display: block;
    text-decoration: none;
    font-size: 14px;
    color: #000;
    width: fit-content
}

.btn-selengkapnya>div {
    gap: 1em;
    transition: .4s
}

.btn-selengkapnya:hover>div {
    gap: 2em;
    transition: .4s
}

.btn-selengkapnya::after {
    content: '';
    display: block;
    width: 0;
    height: 2px;
    background-color: #000;
    transition: .2s
}

.btn-selengkapnya:hover::after {
    width: 100%;
    transition: .2s
}

.btn-selengkapnya p {
    margin: 0
}

/* ====== SEARCH OVERLAY ====== */
input[name="cari"] {
    width: 100%;
    border: none;
    border-bottom: 1px solid #fff;
    background: none;
    color: #fff
}

input[name="cari"]:focus {
    border: none;
    outline: none;
    border-bottom: 1px solid #fff
}

input[name="cari"]+button {
    background: none;
    cursor: pointer;
    outline: none;
    border: none;
    color: #fff
}

/* ====== HERO 3 ARTIKEL ====== */
.artikel-list-besar {
    background-position: center;
    background-size: 100%;
    border-radius: 15px;
    min-width: 425px;
    cursor: pointer;
    transition: .5s;
    position: relative;
    text-decoration: none;
    display: block
}

.artikel-list-besar:hover {
    background-size: 110%;
    transition: .5s
}

.artikel-list-besar>div {
    border-radius: 15px;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    padding: 2em;
    background-image: linear-gradient(to top, rgba(0, 0, 0, .7), transparent);
    z-index: 3;
    position: relative
}

.artikel-list-besar::before {
    content: '';
    display: block;
    border-radius: 15px;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, 0);
    transition: .5s;
    position: absolute;
    z-index: 2
}

.artikel-list-besar:hover::before {
    background-color: rgba(0, 0, 0, .6);
    transition: .5s
}

.artikel-list-besar .judul {
    font-weight: 600;
    color: #fff;
    font-size: 20px;
    margin: 0;
    max-width: 500px
}

.artikel-list-besar .kategori {
    background: #fff;
    display: block;
    padding: .3em 1em;
    font-weight: 700;
    border-radius: 2em;
    font-size: 14px;
    color: #000
}

.artikel-list-besar .tanggal {
    background: rgba(255, 255, 255, .3);
    display: block;
    padding: .3em 1em;
    font-weight: 700;
    border-radius: 2em;
    font-size: 14px;
    color: #fff;
    width: fit-content
}

.artikel-list-kecil {
    background-position: center;
    background-size: 100%;
    border-radius: 15px;
    cursor: pointer;
    transition: .5s;
    position: relative;
    text-decoration: none
}

.artikel-list-kecil:hover {
    background-size: 110%;
    transition: .5s
}

.artikel-list-kecil>div {
    border-radius: 15px;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    padding: 2em;
    background-image: linear-gradient(to top, rgba(0, 0, 0, .7), transparent);
    z-index: 3;
    position: relative
}

.artikel-list-kecil::before {
    content: '';
    display: block;
    border-radius: 15px;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, 0);
    transition: .5s;
    position: absolute;
    z-index: 2
}

.artikel-list-kecil:hover::before {
    background-color: rgba(0, 0, 0, .6);
    transition: .5s
}

.artikel-list-kecil .judul {
    font-weight: 600;
    color: #fff;
    font-size: 20px;
    margin: 0
}

.artikel-list-kecil .kategori {
    background: #fff;
    display: block;
    padding: .3em 1em;
    font-weight: 700;
    border-radius: 2em;
    font-size: 14px;
    color: #000
}

.artikel-list-kecil .tanggal {
    background: rgba(255, 255, 255, .3);
    display: block;
    padding: .3em 1em;
    font-weight: 700;
    border-radius: 2em;
    font-size: 14px;
    color: #fff;
    width: fit-content
}

@media (max-width:700px) {

    .artikel-list-kecil,
    .artikel-list-besar {
        min-width: 0
    }

    .artikel-list-besar .judul,
    .artikel-list-kecil .judul {
        font-size: 14px;
        margin-bottom: 10px
    }

    .artikel-list-besar .kategori,
    .artikel-list-kecil .kategori {
        font-size: 10px;
        margin-bottom: 10px
    }

    .artikel-list-besar .tanggal,
    .artikel-list-kecil .tanggal {
        font-size: 10px
    }
}

/* ====== GRID ARTIKEL BAWAH ====== */
.container-artikel-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr)
}

@media (max-width:995px) {
    .container-artikel-list {
        grid-template-columns: repeat(2, 1fr)
    }
}

.container-artikel-list .artikel-list {
    background-position: center;
    background-size: 100%;
    border-radius: 15px;
    cursor: pointer;
    transition: .5s;
    position: relative;
    aspect-ratio: 1/1;
    text-decoration: none
}

.container-artikel-list .artikel-list:hover {
    background-size: 110%;
    transition: .5s
}

.container-artikel-list .artikel-list>div {
    border-radius: 15px;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    padding: 2em;
    background-image: linear-gradient(to top, rgba(0, 0, 0, .7), transparent);
    z-index: 3;
    position: relative
}

.container-artikel-list .artikel-list::before {
    content: '';
    display: block;
    border-radius: 15px;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, 0);
    transition: .5s;
    position: absolute;
    z-index: 2
}

.container-artikel-list .artikel-list:hover::before {
    background-color: rgba(0, 0, 0, .6);
    transition: .5s
}

.container-artikel-list .artikel-list .judul {
    font-weight: 600;
    color: #fff;
    font-size: 20px;
    margin: 0
}

.container-artikel-list .artikel-list .kategori {
    background: #fff;
    display: block;
    padding: .3em 1em;
    font-weight: 700;
    border-radius: 2em;
    font-size: 14px;
    color: #000
}

.container-artikel-list .artikel-list .tanggal {
    background: rgba(255, 255, 255, .3);
    display: block;
    padding: .3em 1em;
    font-weight: 700;
    border-radius: 2em;
    font-size: 14px;
    color: #fff;
    width: fit-content
}

/* ====== LIST HP ====== */
.container-artikel-list-hp {
    display: none;
    flex-direction: column
}

.container-artikel-list-hp .artikel-list {
    display: flex;
    width: 100%;
    text-decoration: none;
    color: #000
}

.container-artikel-list-hp .artikel-list .image {
    width: 100px
}

.container-artikel-list-hp .artikel-list img {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 10px
}

.container-artikel-list-hp .artikel-list .tanggal {
    display: flex;
    align-items: start;
    gap: 6px
}

.container-artikel-list-hp .artikel-list .tanggal span {
    font-size: 10px
}

.container-artikel-list-hp .artikel-list .judul {
    font-size: 12px
}

@media (max-width:700px) {
    .container-artikel-list {
        display: none
    }

    .container-artikel-list .artikel-list .judul {
        font-size: 14px
    }

    .container-artikel-list .artikel-list .kategori,
    .container-artikel-list .artikel-list .tanggal {
        font-size: 10px
    }

    .container-artikel-list-hp {
        display: flex
    }
}

/* ====== PAGER (tema: pill, pakai var(--merah)) ====== */
.rel-pager {
    display: flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    margin: 10px 0 22px
}

.rel-pager button {
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #111827;
    padding: 8px 12px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px
}

.rel-pager button:hover:not([disabled]) {
    background: #f8fafb
}

.rel-pager button[disabled] {
    opacity: .5;
    cursor: not-allowed
}

.rel-pager .is-active {
    background: var(--merah);
    color: #fff;
    border-color: var(--merah)
}

.rel-pager .material-icons {
    font-size: 18px;
    line-height: 1
}

/* ===== FIX: badge kategori memanjang (ikut stretch) ===== */
.artikel-list-besar .kategori,
.artikel-list-kecil .kategori,
.container-artikel-list .artikel-list .kategori {
    display: inline-flex !important;
    align-items: center;
    height: auto !important;
    line-height: 1.2;
    align-self: flex-start;
    padding: .35em 1em
}

.artikel-list-besar .d-flex>.kategori,
.artikel-list-kecil .d-flex>.kategori,
.container-artikel-list .artikel-list .d-flex>.kategori {
    align-self: flex-start !important
}

/* util */
.hide-ke-show-inline {
    display: none
}

@media (max-width:700px) {
    .hide-ke-show-inline {
        display: inline
    }
}
</style>

<!-- ===== Search overlay (tetap) ===== -->
<div style="position: absolute; z-index: 4;" class="w-100 pt-4">
    <div class="container">
        <form action="<?= esc(ilena_store_path('/actionsearcharticle'), 'attr'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="d-flex align-items-center gap-3">
                <input type="text" name="cari" class="py-1" value="<?= isset($cari) ? $cari : ''; ?>">
                <button type="submit"><i class="material-icons">search</i></button>
            </div>
        </form>
    </div>
</div>

<!-- ===== React header ===== -->
<div id="container-react-artikel"></div>
<script type="text/babel">
    function judulSplitter(j){const a=j.split(" ");const j1=`${a[0]} ${a[1]}`;a.splice(0,2);const half=Math.ceil(a.length/2);const j2=a.filter((_,i)=>i<half).join(" ");const j3=a.filter((_,i)=>i>=half).join(" ");return{judul1:j1,judul2:j2,judul3:j3}}
function titleCase(s){const t=s.toLowerCase().split(' ');for(let i=0;i<t.length;i++){t[i]=t[i].charAt(0).toUpperCase()+t[i].substring(1)}return t.join(' ')}
const {useState,useEffect,useRef}=React;
const artikel3Baru=<?= json_encode($artikel3Baru, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const localePrefix=<?= json_encode($isEnglish ? '/en' : '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const inis=artikel3Baru[0];
const dataCategoryFromBackend='<?= isset($category) ? esc(ilena_article_category_label((string) $category), 'js') : ''; ?>';
const App=()=> {
  const intervalRef=useRef(null);
  const [img,setImg]=useState(['active','','']);
  const [imgSelected,setImgSelected]=useState({...judulSplitter(inis.judul),deskripsi:inis.deskripsi,id:inis.id,kategori:inis.kategori_label||titleCase(inis.kategori),path:inis.path,header:inis.header});
  const innerWidth=useRef(window.innerWidth);
  const categories=[
    {teks:<?= json_encode($isEnglish ? 'Tips & ideas' : 'Tips & Trik'); ?>,link:localePrefix+'/article/category/tips-@-trik',gambar:'https://cdn.pixabay.com/photo/2020/03/05/17/35/tips-4905013_1280.jpg',deskripsi:<?= json_encode($isEnglish ? 'Practical ideas to make everyday living easier' : 'Lebih banyak tahu berbagai life hack yang mempermudah hidup'); ?>},
    {teks:<?= json_encode($isEnglish ? 'Guides' : 'Edukasi'); ?>,link:localePrefix+'/article/category/edukasi',gambar:'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1122&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',deskripsi:<?= json_encode($isEnglish ? 'Updates and insights on furniture and interiors' : 'Beragam informasi seputar furniture & properti terbaru'); ?>},
    {teks:'Fun Fact',link:localePrefix+'/article/category/fun-fact',gambar:'https://images.unsplash.com/photo-1676276375656-c4864c632a08?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',deskripsi:<?= json_encode($isEnglish ? 'Interesting facts and inspiration for your home' : 'Ketahui serba-serbi fakta menarik yang menginspirasi'); ?>},
    {teks:<?= json_encode($isEnglish ? 'Recommendations' : 'Rekomendasi'); ?>,link:localePrefix+'/article/category/rekomendasi',gambar:'https://images.unsplash.com/photo-1624269305543-efbc8d89f1d3?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',deskripsi:<?= json_encode($isEnglish ? 'Find the right choices for your needs' : 'Pentingnya mendapatkan yang tepat sesuai kebutuhan'); ?>},
  ];
  useEffect(()=>{intervalRef.current=setInterval(()=>{setImg(p=>{const i=p.indexOf('active')+1>=p.length?0:p.indexOf('active')+1;return p.map((x,ix)=>ix===i?'active':'')})},5000);return()=>clearInterval(intervalRef.current)},[]);
  useEffect(()=>{const idx=img.indexOf('active');const cur={...artikel3Baru[idx]};setImgSelected({...judulSplitter(cur.judul),deskripsi:cur.deskripsi,id:cur.id,kategori:cur.kategori_label||titleCase(cur.kategori),path:cur.path,header:cur.header})},[img]);

  return(
    <>
      {!dataCategoryFromBackend?
        <div className="header">
          <div className="container-header-content">
            <div className="d-flex align-items-end container gap-5">
              <div style={{height:'100%',flex:'1'}}>
                <div>
                  <p>{imgSelected.kategori}</p>
                  <h5 className="teks-sedang">{imgSelected.judul1}</h5>
                  <h1 className="teks-besar mb-3">{imgSelected.judul2} {imgSelected.judul3}</h1>
                  <div style={{maxWidth:'500px',borderLeft:'3px solid white',overflowY:'auto'}} className="ps-4 mb-3 deskripsi">
                    <p className="m-0" style={{color:'rgb(219, 219, 219)'}}>{imgSelected.deskripsi}</p>
                  </div>
                  <div className="d-flex gap-2">
                    <a href={`${localePrefix}/article/${imgSelected.path}`} className="btn-lonjong-putih-outline"><?= $isEnglish ? 'Read more' : 'Baca selengkapnya'; ?></a>
                  </div>
                </div>
              </div>
              <div className="d-flex align-items-center gap-2">
                <div className={`item-slider-header-img ${img[0]}`} onClick={()=>{setImg(img.map((_,i)=>i===0?'active':''));clearInterval(intervalRef.current);}}></div>
                <div className={`item-slider-header-img ${img[1]}`} onClick={()=>{setImg(img.map((_,i)=>i===1?'active':''));clearInterval(intervalRef.current);}}></div>
                <div className={`item-slider-header-img ${img[2]}`} onClick={()=>{setImg(img.map((_,i)=>i===2?'active':''));clearInterval(intervalRef.current);}}></div>
              </div>
            </div>
          </div>
          <div className="container-header-img">
            {artikel3Baru.map((a,i)=>(<img key={i} className={img[i]} src={a.header} alt="" loading={i===0?'eager':'lazy'} decoding="async" />))}
          </div>
        </div>
      :
        <div style="height:100svh;max-height:400px;width:100%;background-size:cover;background-position:center;<?= isset($category_bg) ? 'background-image:url('.$category_bg.')' : '' ?>"></div>
      }
      <div className="container">
        <div className={innerWidth.current<700?'py-4':'py-5'}>
          <h1 className="teks-besar mb-5 show-block-ke-hide"><?= $isEnglish ? 'Categories' : 'Kategori'; ?></h1>
          <h1 className="teks-besar mb-3 hide-ke-show-block"><?= $isEnglish ? 'Categories' : 'Kategori'; ?></h1>
          <div className="container-kategori-artikel">
            {[
              {teks:<?= json_encode($isEnglish ? 'Tips & ideas' : 'Tips & Trik'); ?>,link:localePrefix+'/article/category/tips-@-trik',gambar:'https://cdn.pixabay.com/photo/2020/03/05/17/35/tips-4905013_1280.jpg',deskripsi:<?= json_encode($isEnglish ? 'Practical ideas to make everyday living easier' : 'Lebih banyak tahu berbagai life hack yang mempermudah hidup'); ?>},
              {teks:<?= json_encode($isEnglish ? 'Guides' : 'Edukasi'); ?>,link:localePrefix+'/article/category/edukasi',gambar:'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1122&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',deskripsi:<?= json_encode($isEnglish ? 'Updates and insights on furniture and interiors' : 'Beragam informasi seputar furniture & properti terbaru'); ?>},
              {teks:'Fun Fact',link:localePrefix+'/article/category/fun-fact',gambar:'https://images.unsplash.com/photo-1676276375656-c4864c632a08?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',deskripsi:<?= json_encode($isEnglish ? 'Interesting facts and inspiration for your home' : 'Ketahui serba-serbi fakta menarik yang menginspirasi'); ?>},
              {teks:<?= json_encode($isEnglish ? 'Recommendations' : 'Rekomendasi'); ?>,link:localePrefix+'/article/category/rekomendasi',gambar:'https://images.unsplash.com/photo-1624269305543-efbc8d89f1d3?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',deskripsi:<?= json_encode($isEnglish ? 'Find the right choices for your needs' : 'Pentingnya mendapatkan yang tepat sesuai kebutuhan'); ?>}
            ].map((c,i)=>(
              <a key={i} href={c.link} className="item-kategori-artikel">
                <img className="item-kategori-artikel__image" src={c.gambar} alt="" loading="lazy" decoding="async" />
                <div>
                  <div className="d-flex justify-content-between">
                    <p className="judul">{c.teks}</p>
                    <i className="material-icons" style={{width:'fit-content'}}>open_in_new</i>
                  </div>
                  <div className="desc"><p>{c.deskripsi}</p></div>
                </div>
              </a>
            ))}
          </div>
        </div>
      </div>
    </>
  )
}
ReactDOM.render(<App />, document.getElementById("container-react-artikel"));
</script>

<div class="container">
    <hr>
    <div class="ubah-padding">
        <h3 class="teks-sedang mb-3"><?= $isEnglish ? 'Popular now' : 'Populer saat ini'; ?></h3>
        <div class="d-flex baris-ke-kolom">
            <?php foreach ($artikelPopuler as $ind_a => $a) { if ($ind_a < 3) { ?>
            <?php if ($ind_a != 0) { ?><div class="bg-secondary show-block-ke-hide" style="width:1px;opacity:.2"></div>
            <?php } ?>
            <div style="flex:1;">
                <div class="mb-2">
                    <span style="font-size:small;"><?= esc(ilena_article_category_label((string) $a['kategori'])); ?></span>
                    <span class="text-secondary" style="font-size:small;">•</span>
                    <span class="text-secondary" style="font-size:small;">
                        <?= date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu'])); ?>
                    </span>
                </div>
                <p class="fw-bold mb-1"><?= esc($a['judul'] ?? ''); ?></p>
                <a href="<?= esc(ilena_store_path('/article/' . $a['path']), 'attr'); ?>" class="btn-selengkapnya">
                    <div class="d-flex align-items-center">
                        <p><?= $isEnglish ? 'Read more' : 'Baca selengkapnya'; ?></p><i class="material-icons">arrow_forward</i>
                    </div>
                </a>
            </div>
            <?php } } ?>
        </div>
    </div>
    <hr>

    <!-- ===== Hero 3 artikel ===== -->
    <div class="ubah-padding">
        <div class="baris-ke-kolom ubah-gap">
            <div style="flex:2;">
                <a href="<?= esc(ilena_store_path('/article/' . $artikel[0]['path']), 'attr'); ?>"
                    style="height:100%;"
                    class="w-full artikel-list-besar">
                    <img class="article-card-cover" src="<?= esc($artikel[0]['header'], 'attr'); ?>" alt="" loading="lazy" decoding="async" width="1200" height="800">
                    <div>
                        <div style="flex:1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= esc(ilena_article_category_label((string) $artikel[0]['kategori'][0])); ?></snap>
                        </div>
                        <p class="tanggal show-block-ke-hide"><?= $artikel[0]['waktu']; ?></p>
                        <p class="judul"><?= esc($artikel[0]['judul'] ?? ''); ?></p>
                        <p class="tanggal hide-ke-show-block m-0"><?= $artikel[0]['waktu']; ?></p>
                    </div>
                </a>
            </div>
            <div style="flex:1;" class="d-flex flex-column ubah-gap">
                <a href="<?= esc(ilena_store_path('/article/' . $artikel[1]['path']), 'attr'); ?>"
                    style="height:50%;"
                    class="w-full artikel-list-kecil">
                    <img class="article-card-cover" src="<?= esc($artikel[1]['header'], 'attr'); ?>" alt="" loading="lazy" decoding="async" width="900" height="600">
                    <div>
                        <div style="flex:1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= esc(ilena_article_category_label((string) $artikel[1]['kategori'][0])); ?></snap>
                        </div>
                        <p class="tanggal show-block-ke-hide"><?= $artikel[1]['waktu']; ?></p>
                        <p class="judul"><?= esc($artikel[1]['judul'] ?? ''); ?></p>
                        <p class="tanggal hide-ke-show-block m-0"><?= $artikel[1]['waktu']; ?></p>
                    </div>
                </a>
                <a href="<?= esc(ilena_store_path('/article/' . $artikel[2]['path']), 'attr'); ?>"
                    style="height:50%;"
                    class="w-full artikel-list-kecil">
                    <img class="article-card-cover" src="<?= esc($artikel[2]['header'], 'attr'); ?>" alt="" loading="lazy" decoding="async" width="900" height="600">
                    <div>
                        <div style="flex:1;" class="d-flex justify-content-end">
                            <snap class="kategori"><?= esc(ilena_article_category_label((string) $artikel[2]['kategori'][0])); ?></snap>
                        </div>
                        <p class="tanggal show-block-ke-hide"><?= $artikel[2]['waktu']; ?></p>
                        <p class="judul"><?= esc($artikel[2]['judul'] ?? ''); ?></p>
                        <p class="tanggal hide-ke-show-block m-0"><?= $artikel[2]['waktu']; ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- ======= PAGINATION (ATAS) ======= -->
    <nav id="home-pager-desktop-top" class="rel-pager"></nav>
    <nav id="home-pager-mobile-top" class="rel-pager"></nav>

    <!-- ===== Grid artikel bawah (Desktop) ===== -->
    <div id="home-articles-desktop" class="container-artikel-list ubah-gap mb-3">
        <?php foreach ($artikel as $ind_a => $a) { if ($ind_a > 2) { ?>
        <a href="<?= esc(ilena_store_path('/article/' . $a['path']), 'attr'); ?>" class="artikel-list">
            <img class="article-card-cover" src="<?= esc($a['header'], 'attr'); ?>" alt="" loading="lazy" decoding="async" width="900" height="700">
            <div>
                <div style="flex:1;" class="d-flex justify-content-end">
                    <snap class="kategori"><?= esc(ilena_article_category_label((string) $a['kategori'][0])); ?></snap>
                </div>
                <p class="tanggal"><?= $a['waktu']; ?></p>
                <p class="judul"><?= esc($a['judul'] ?? ''); ?></p>
            </div>
        </a>
        <?php } } ?>
    </div>

    <!-- ===== Grid artikel bawah (Mobile) ===== -->


    <!-- ======= PAGINATION (BAWAH) ======= -->
    <nav id="home-pager-desktop" class="rel-pager"></nav>
    <nav id="home-pager-mobile" class="rel-pager"></nav>
</div>

<script>
const ubahPaddingElm = document.querySelectorAll('.ubah-padding');
const innerWidthClient = window.innerWidth;
ubahPaddingElm.forEach(e => e.classList.add(innerWidthClient > 700 ? 'py-4' : 'py-3'));
const ubahGapElm = document.querySelectorAll('.ubah-gap');
ubahGapElm.forEach(e => e.classList.add(innerWidthClient > 700 ? 'gap-4' : 'gap-3'));
</script>

<!-- ===== Pagination client-side (tanpa ubah sistem backend) ===== -->
<script>
(function() {
    function setupPagination({
        containerSel,
        itemSel,
        pagerSel,
        perPage = 9,
        hashKey = 'page'
    }) {
        const container = document.querySelector(containerSel);
        const pager = document.querySelector(pagerSel);
        if (!container || !pager) return;

        const items = Array.from(container.querySelectorAll(itemSel));
        const total = items.length;
        if (total === 0) {
            pager.innerHTML = '';
            return;
        }

        const lastPage = Math.ceil(total / perPage);

        function readHash() {
            const m = (location.hash || '').match(new RegExp(`${hashKey}=(\\d+)`));
            const p = m ? parseInt(m[1], 10) : 1;
            return (isNaN(p) || p < 1 || p > lastPage) ? 1 : p;
        }

        function writeHash(page) {
            const raw = (location.hash || '').replace(/^#/, '');
            const parts = raw ? raw.split('&').filter(Boolean) : [];
            const others = parts.filter(s => !s.startsWith(`${hashKey}=`));
            others.push(`${hashKey}=${page}`);
            location.hash = '#' + others.join('&');
        }

        let current = readHash();

        function renderPage(page) {
            current = Math.max(1, Math.min(lastPage, page));
            const start = (current - 1) * perPage;
            const end = start + perPage;
            items.forEach((el, ix) => {
                el.style.display = (ix >= start && ix < end) ? '' : 'none';
            });
            renderPager();
            writeHash(current);
        }

        function renderPager() {
            if (lastPage <= 1) {
                pager.innerHTML = '';
                pager.style.display = 'none';
                return;
            }
            pager.style.display = '';
            let html = '';
            html += `<button type="button" ${current===1?'disabled':''} data-act="prev" aria-label="Sebelumnya">
               <span class="material-icons">arrow_back_ios</span><span class="hide-ke-show-inline">Prev</span>
             </button>`;

            const windowSize = 7;
            let start = Math.max(1, current - Math.floor(windowSize / 2));
            let end = Math.min(lastPage, start + windowSize - 1);
            if (end - start + 1 < windowSize) start = Math.max(1, end - windowSize + 1);

            for (let i = start; i <= end; i++) {
                html +=
                    `<button type="button" class="${i===current?'is-active':''}" data-page="${i}" aria-label="Halaman ${i}">${i}</button>`;
            }

            html += `<button type="button" ${current===lastPage?'disabled':''} data-act="next" aria-label="Berikutnya">
               <span class="hide-ke-show-inline">Next</span><span class="material-icons">arrow_forward_ios</span>
             </button>`;

            pager.innerHTML = html;

            pager.querySelectorAll('[data-page]').forEach(btn => {
                btn.onclick = () => renderPage(parseInt(btn.getAttribute('data-page'), 10));
            });
            const prev = pager.querySelector('[data-act="prev"]');
            const next = pager.querySelector('[data-act="next"]');
            if (prev) prev.onclick = () => renderPage(current - 1);
            if (next) next.onclick = () => renderPage(current + 1);
        }

        renderPage(current);
        window.addEventListener('hashchange', () => {
            const p = readHash();
            if (p !== current) renderPage(p);
        });
    }

    const isMobile = window.matchMedia('(max-width: 700px)').matches;

    /* Desktop grid (atas & bawah sinkron lewat hash #page=) */

    setupPagination({
        containerSel: '#home-articles-desktop',
        itemSel: '.artikel-list',
        pagerSel: '#home-pager-desktop',
        perPage: isMobile ? 6 : 9,
        hashKey: 'page'
    });

    /* Mobile list (atas & bawah sinkron lewat hash #pagem=) */
    setupPagination({
        containerSel: '#home-articles-mobile',
        itemSel: '.artikel-list',
        pagerSel: '#home-pager-mobile-top',
        perPage: 6,
        hashKey: 'pagem'
    });
    setupPagination({
        containerSel: '#home-articles-mobile',
        itemSel: '.artikel-list',
        pagerSel: '#home-pager-mobile',
        perPage: 6,
        hashKey: 'pagem'
    });
})();
</script>
<!-- ===== /Pagination ===== -->

<?= $this->endSection(); ?>
