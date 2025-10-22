<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
/* ===================== THEME & RESETS ===================== */
:root {
    --merah: #b31217;
    --merah-600: #a50e12;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --slate-300: #d1d5db;
    --slate-700: #334155;
    --slate-800: #1f2937;
    --ring: #fde2e2;
}

* {
    box-sizing: border-box
}

html,
body {
    scroll-behavior: smooth
}

body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale
}

/* Scrollbar */
*::-webkit-scrollbar {
    width: 8px;
    height: 8px
}

*::-webkit-scrollbar-thumb {
    background: #d9d9d9;
    border-radius: 20px
}

*::-webkit-scrollbar-thumb:hover {
    background: #c7c7c7
}

*::-webkit-scrollbar-track {
    background: transparent
}

/* ===================== NOTIF TOAST ===================== */
.notif {
    position: fixed;
    bottom: 50px;
    right: 0;
    padding: .6em 2em;
    color: #e84a49;
    border-radius: 10px;
    font-weight: 600;
    letter-spacing: -.2px;
    font-size: 13.5px;
    background: #fff8f8;
    border: 1px solid #ffd0d0;
    box-shadow: 0 10px 30px rgba(184, 27, 29, .15);
    transition: .5s;
    transform: translateX(100%);
}

.notif.show {
    right: 50px;
    transform: translateX(0%)
}

/* ===================== PRODUK LIST ===================== */
.item-produk {
    border-radius: 12px;
    border: 1px solid var(--slate-100);
    background: #fff;
    padding: 10px 12px;
    transition: transform .18s, box-shadow .18s, border-color .18s;
}

.item-produk:hover {
    transform: translateY(-1px);
    border-color: var(--slate-200);
    box-shadow: 0 12px 28px rgba(2, 8, 23, .06)
}

.item-produk img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid var(--slate-200)
}

.item-produk .item-varian {
    cursor: pointer;
    outline: 1px solid var(--slate-300);
    border: 1px solid #fff;
    border-radius: 999px;
    width: 18px;
    height: 18px;
    transition: transform .1s, outline-color .2s;
}

.item-produk .item-varian:hover {
    outline: 1px solid var(--merah)
}

.item-produk .item-varian:active {
    transform: scale(.92)
}

/* ===================== ITEM KERANJANG ===================== */
.item-keranjang-admin {
    border: 1px dashed var(--slate-200);
    background: linear-gradient(#fff, #fff) padding-box, repeating-linear-gradient(90deg, #f3f4f6 0 8px, transparent 8px 16px) border-box;
    border-radius: 12px;
    padding: 8px;
}

.item-keranjang-admin img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid var(--slate-200)
}

.item-keranjang-admin .counter {
    display: flex;
    align-items: center;
    gap: 8px
}

.item-keranjang-admin .counter .action {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 26px;
    height: 26px;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #ffd2d2;
    color: var(--merah);
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    box-shadow: 0 6px 16px rgba(225, 29, 72, .06);
}

.item-keranjang-admin .counter .action:hover {
    background: #fff2f2
}

.item-keranjang-admin .counter .action:active {
    transform: translateY(1px) scale(.98)
}

.item-keranjang-admin .counter .angka {
    width: 34px;
    text-align: center;
    font-weight: 700;
    letter-spacing: .2px
}

/* ===================== FORM ELEMENTS ===================== */
input {
    font-size: 13px
}

.form-control,
.form-select {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    transition: border-color .15s, box-shadow .15s, background .15s;
    background: #fff;
    font-weight: 500;
}

.form-control:focus,
.form-select:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none
}

/* Helper chip */
#container-react small,
.mb-1>small,
.d-flex.gap-1>small,
.d-flex.gap-2>small,
.d-flex.flex-column>small {
    position: relative;
    display: block;
    margin: 6px 0;
    padding: 8px 12px 8px 36px;
    font-size: 12px;
    line-height: 1.35;
    letter-spacing: -.2px;
    background: #fff8e1;
    color: #8a6d3b;
    border: 1px solid #f2e6bd;
    border-radius: 10px;
    box-shadow: 0 1px 0 rgba(0, 0, 0, .02) inset;
    transition: background .2s, border-color .2s, transform .2s, opacity .2s, box-shadow .2s;
    font-weight: 500;
}

#container-react small::before,
.mb-1>small::before,
.d-flex.gap-1>small::before,
.d-flex.gap-2>small::before,
.d-flex.flex-column>small::before {
    content: "notifications_active";
    font-family: "Material Icons";
    font-size: 18px;
    line-height: 1;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: .9;
}

#container-react small[data-urgent] {
    background: #ffe8e8;
    border-color: #ffc9c9;
    color: #8b2a2b
}

#container-react small[data-urgent]::before {
    content: "priority_high"
}

#container-react small[data-type="info"] {
    background: #e8f4ff;
    border-color: #cfe7ff;
    color: #1e4e79
}

#container-react small[data-type="info"]::before {
    content: "info"
}

.mb-1:focus-within>small,
.d-flex.gap-1:focus-within>small,
.d-flex.gap-2:focus-within>small,
.d-flex.flex-column:focus-within>small {
    position: absolute;
    right: 0;
    top: -10px;
    background: #111;
    color: #fff;
    border-color: #222;
    box-shadow: 0 12px 30px rgba(0, 0, 0, .25);
    z-index: 3;
    opacity: .98;
    max-width: clamp(220px, 45vw, 420px);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    pointer-events: none
}

.mb-1:focus-within>small::before,
.d-flex.gap-1:focus-within>small::before,
.d-flex.gap-2:focus-within>small::before,
.d-flex.flex-column:focus-within>small::before {
    content: "tips_and_updates";
    opacity: 1
}

/* ===================== PREVIEW MODAL ===================== */
.preview-overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, .45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px)
}

.preview-card {
    width: min(980px, 94vw);
    max-height: 90vh;
    overflow: hidden;
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    box-shadow: 0 24px 72px rgba(0, 0, 0, .35), 0 0 0 1px rgba(255, 255, 255, .8) inset;
    display: flex;
    flex-direction: column
}

.preview-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: linear-gradient(180deg, #fff, #fafafa)
}

.preview-title {
    margin: 0;
    font-size: 18px;
    letter-spacing: -.2px;
    font-weight: 700;
    color: #0f172a
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #111827
}

.badge.warn {
    color: #b42318;
    background: #feeaea;
    border-color: #ffd3cf
}

.badge.ok {
    color: #065f46;
    background: #e7f8ef;
    border-color: #c9f0dc
}

.preview-body {
    padding: 14px 20px;
    overflow: auto;
    flex: 1;
    background: linear-gradient(#fff, #fff), radial-gradient(1200px 600px at 50% -40%, #fafafa 10%, transparent 70%) no-repeat
}

.preview-grid {
    display: grid;
    grid-template-columns: 1.3fr .7fr;
    gap: 16px
}

@media (max-width:860px) {
    .preview-grid {
        grid-template-columns: 1fr
    }
}

.preview-section {
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    background: #fff;
    overflow: hidden
}

.section-head {
    padding: 10px 14px;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-100);
    font-weight: 600;
    letter-spacing: -.2px;
    display: flex;
    align-items: center;
    gap: 8px
}

.section-body {
    padding: 12px 14px
}

.dl {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 8px 12px;
    font-size: 13px;
    line-height: 1.35
}

.dl b {
    color: #0f172a;
    font-weight: 600
}

.dl .mono {
    color: #334155;
    font-variant-numeric: tabular-nums
}

.items-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px
}

.items-table thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-100);
    padding: 10px 12px;
    text-align: left
}

.items-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle
}

.cell-right {
    text-align: right
}

.cell-center {
    text-align: center
}

.cell-thumb {
    width: 52px
}

.thumb {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid var(--slate-200)
}

.summary-card {
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    background: #fff;
    overflow: hidden
}

.summary-body {
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    gap: 10px
}

.row-sum {
    display: flex;
    justify-content: space-between;
    align-items: center
}

.row-sum .label {
    color: #334155
}

.row-sum .value {
    font-weight: 700;
    font-variant-numeric: tabular-nums
}

.divider {
    height: 1px;
    background: var(--slate-100);
    margin: 6px 0
}

.total-line {
    padding-top: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center
}

.total-line .ttl-label {
    font-weight: 700;
    letter-spacing: -.2px
}

.total-line .ttl-val {
    font-weight: 800;
    font-size: 18px;
    font-variant-numeric: tabular-nums
}

.preview-footer {
    padding: 12px 20px;
    border-top: 1px solid var(--slate-100);
    background: #fff;
    position: sticky;
    bottom: 0;
    display: flex;
    gap: 10px;
    justify-content: space-between;
    align-items: center
}

/* ===================== BUTTONS ===================== */
.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .7em 1em;
    border-radius: 10px;
    font-weight: 600
}

.btn-ghost:hover {
    background: #e5e7eb
}

.btn-primary {
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    border: 0;
    padding: .8em 1.2em;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 10px 28px rgba(165, 14, 18, .25);
    transition: transform .08s, filter .08s, box-shadow .18s;
}

.btn-primary:hover {
    filter: brightness(.97)
}

.btn-primary:active {
    transform: translateY(1px);
    box-shadow: 0 6px 16px rgba(165, 14, 18, .22)
}

.btn-copy {
    border: 1px dashed var(--slate-300);
    background: #f9fafb;
    padding: .6em .9em;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600
}

.btn-copy:hover {
    background: #f3f4f6
}

.btn-close {
    border: 0;
    background: #f3f4f6;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .15s, transform .1s
}

.btn-close:hover {
    background: #e5e7eb
}

.btn-close:active {
    transform: translateY(1px)
}

.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 700;
    letter-spacing: .1px;
    padding: .9em 1.1em;
    border-radius: 12px;
    box-shadow: 0 14px 36px rgba(179, 18, 23, .28);
    transition: transform .08s, filter .08s, box-shadow .18s, opacity .2s;
}

.btn-default-merah:hover {
    filter: brightness(.98)
}

.btn-default-merah:active {
    transform: translateY(1px);
    box-shadow: 0 10px 22px rgba(179, 18, 23, .24)
}

.btn-default-merah.disabled {
    opacity: .55;
    pointer-events: none
}

/* ===================== PAGINATION ===================== */
.container-pag {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 6px
}

.container-pag .item-pag {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid var(--slate-200);
    cursor: pointer;
    user-select: none;
    background: #fff;
    transition: background .15s, color .15s, border-color .15s, transform .08s;
    box-shadow: 0 6px 16px rgba(2, 8, 23, .04)
}

.container-pag .item-pag:hover {
    border-color: #ffd0d0;
    background: #fff6f6
}

.container-pag .item-pag.active {
    background: var(--merah);
    color: #fff;
    border-color: var(--merah);
    box-shadow: 0 10px 22px rgba(179, 18, 23, .25)
}

.container-pag .item-pag:active {
    transform: translateY(1px)
}

/* ===================== KONTEN ATAS ===================== */
h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    letter-spacing: -.2px
}

h1.teks-sedang::after {
    content: "";
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(179, 18, 23, .25), transparent);
    border-radius: 999px
}

/* ===================== MEDIA ===================== */
@media (prefers-reduced-motion:reduce) {

    .notif,
    .item-produk,
    .btn-primary,
    .btn-default-merah,
    .item-keranjang-admin .action {
        transition: none
    }
}

@media (max-width:576px) {

    #container-react small,
    .mb-1>small,
    .d-flex.gap-1>small,
    .d-flex.gap-2>small,
    .d-flex.flex-column>small {
        font-size: 11.5px;
        padding-left: 34px
    }

    #container-react small::before,
    .mb-1>small::before,
    .d-flex.gap-1>small::before,
    .d-flex.gap-2>small::before,
    .d-flex.flex-column>small::before {
        font-size: 16px;
        left: 9px
    }

    .mb-1:focus-within>small,
    .d-flex.gap-1:focus-within>small,
    .d-flex.gap-2:focus-within>small,
    .d-flex.flex-column:focus-within>small {
        top: -6px;
        right: 0;
        max-width: 85%
    }
}

/* ===================== KOMPAT ===================== */
.item-produk img {
    width: 50px;
    border-radius: 10px
}

.item-produk .item-varian {
    border-radius: 2em
}

.thumb {
    border: 1px solid #e5e7eb
}

.note {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 12px;
    color: #334155;
    background: #f8fafc;
    border: 1px dashed #e5e7eb;
    border-radius: 10px;
    padding: 8px 10px
}
</style>

<div style="padding: 2em;" class="h-100 d-flex flex-column">
    <h1 class="teks-sedang mb-4">Buat Pesanan</h1>
    <div id="container-react" style="flex:1;" class="d-flex gap-3"></div>
</div>

<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<script>
// Aman: langsung objek JS, tanpa JSON.parse('...') yang rawan single-quote
console.log(<?= $produkJson ?>);
</script>

<!-- JSX & fitur modern (LOGIKA TIDAK DIUBAH) -->
<script type="text/babel" data-presets="env,react">
    const { useState, useEffect } = React;
const produkSemua = <?= $produkJson ?>;

function waktuSkrg(){
  const date=new Date();
  const datePart=[date.getFullYear(),date.getMonth()+1,date.getDate()].map((n,i)=>n.toString().padStart(i===0?4:2,"0")).join("-");
  const timePart=[date.getHours(),date.getMinutes(),date.getSeconds()].map(n=>n.toString().padStart(2,"0")).join(":");
  return datePart+" "+timePart;
}

const App = () => {
  const [listProduk, setListProduk] = useState(<?= $produkJson ?>);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageNumbers, setPageNumbers] = useState([]);
  const [currentItems, setCurrentItems] = useState([]);
  const [cari, setCari] = useState('');
  const [modalKeranjang, setModalKeranjang] = useState(false);
  const [keranjang, setKeranjang] = useState([]); // (tidak dipakai, tetap dipertahankan)
  const [notif, setNotif] = useState({ show: false, teks: 'Stok tidak cukup' });
  const [totalHarga, setTotalHarga] = useState(0);
  const [formData, setFormData] = useState({
    provinsi:'',kabupaten:'',kecamatan:'',kelurahan:'',kodepos:'',detail:'',
    provinsiTagihan:'',kabupatenTagihan:'',kecamatanTagihan:'',kelurahanTagihan:'',kodeposTagihan:'',detailTagihan:'',
    totalAkhir:0, jenis:'sale', tanggal:'',
    items:[],
    nama:'', nama_npwp:'', nohp:'', npwp:'', keterangan:'', po:'', downPayment:0,
  });
  const [potonganHargaSatuan, setPotonganHargaSatuan] = useState(0);
  const [kabupaten, setKabupaten] = useState([]);
  const [kecamatan, setKecamatan] = useState([]);
  const [kelurahan, setKelurahan] = useState([]);
  const [kabupatenTagihan, setKabupatenTagihan] = useState([]);
  const [kecamatanTagihan, setKecamatanTagihan] = useState([]);
  const [kelurahanTagihan, setKelurahanTagihan] = useState([]);
  const [canSave, setCanSave] = useState(false);
  const [potongan, setPotongan] = useState({ nominal: 0, satuan: 'persen' });
  const [alamatTagihanSama, setAlamatTagihanSama] = useState(false);

  /* PREVIEW */
  const [previewOpen, setPreviewOpen] = useState(false);
  const [previewPayload, setPreviewPayload] = useState(null);
  const formatRupiah = (n) => `Rp ${Number(n||0).toLocaleString('id-ID')}`;

  /* === Helper: aturan "seperti Sale" juga untuk NF === */
  const isInvoiceLike = (j) => j === 'sale' || j === 'nf';

  /* Helpers untuk preview */
  const subtotalBarang = () => {
    return (formData.items || []).reduce((acc, it) => acc + (Number(it.harga||0) * Number(it.jumlah||0)), 0);
  };
  const potonganFormNominal = () => {
    const sub = subtotalBarang();
    if(potongan.satuan === 'rupiah') return Number(potongan.nominal||0);
    return (Number(potongan.nominal||0) / 100) * sub;
  };
  const sisaBayar = () => Math.max(0, Number(formData.totalAkhir||0) - Number(formData.downPayment||0));

  useEffect(()=>{
    if(formData.provinsi){
      const idprov=formData.provinsi.split("-")[0];
      async function fetchRajaOngkir(){ const r=await fetch("/getkota/"+idprov); const kota=await r.json(); setKabupaten(kota); }
      fetchRajaOngkir();
    }
    setFormData({...formData,kabupaten:'',kecamatan:'',kelurahan:'',kodepos:''});
    setKecamatan([]); setKelurahan([]);
  },[formData.provinsi]);

  useEffect(()=>{
    if(formData.kabupaten){
      const idkab=formData.kabupaten.split("-")[0];
      async function fetchRajaOngkir(){ const r=await fetch("/getkec/"+idkab); const kota=await r.json(); setKecamatan(kota); }
      fetchRajaOngkir();
    }
    setFormData({...formData,kecamatan:'',kelurahan:'',kodepos:''});
    setKelurahan([]);
  },[formData.kabupaten]);

  useEffect(()=>{
    if(formData.kecamatan){
      const idkec=formData.kecamatan.split("-")[0];
      async function fetchRajaOngkir(){ const r=await fetch("/getkode/"+idkec); const hasil=await r.json(); setKelurahan(hasil); }
      fetchRajaOngkir();
    }
    setFormData({...formData,kelurahan:'',kodepos:''});
  },[formData.kecamatan]);

  useEffect(()=>{ if(formData.kelurahan){ setFormData({...formData, kodepos: formData.kelurahan.split('-')[1]}); } },[formData.kelurahan]);

  /* TAGIHAN */
  useEffect(()=>{
    if(formData.provinsiTagihan){
      const idprov=formData.provinsiTagihan.split("-")[0];
      async function fetchRajaOngkir(){ const r=await fetch("/getkota/"+idprov); const kota=await r.json(); setKabupatenTagihan(kota); }
      fetchRajaOngkir();
    }
    setFormData({...formData,kabupatenTagihan:'',kecamatanTagihan:'',kelurahanTagihan:'',kodeposTagihan:''});
    setKecamatanTagihan([]); setKelurahanTagihan([]);
  },[formData.provinsiTagihan]);

  useEffect(()=>{
    if(formData.kabupatenTagihan){
      const idkab=formData.kabupatenTagihan.split("-")[0];
      async function fetchRajaOngkir(){ const r=await fetch("/getkec/"+idkab); const kota=await r.json(); setKecamatanTagihan(kota); }
      fetchRajaOngkir();
    }
    setFormData({...formData,kecamatanTagihan:'',kelurahanTagihan:'',kodeposTagihan:''});
    setKelurahanTagihan([]);
  },[formData.kabupatenTagihan]);

  useEffect(()=>{
    if(formData.kecamatanTagihan){
      const idkec=formData.kecamatanTagihan.split("-")[0];
      async function fetchRajaOngkir(){ const r=await fetch("/getkode/"+idkec); const hasil=await r.json(); setKelurahanTagihan(hasil); }
      fetchRajaOngkir();
    }
    setFormData({...formData,kelurahanTagihan:'',kodeposTagihan:''});
  },[formData.kecamatanTagihan]);

  useEffect(()=>{ if(formData.kelurahanTagihan){ setFormData({...formData, kodeposTagihan: formData.kelurahanTagihan.split('-')[1]}); } },[formData.kelurahanTagihan]);

  useEffect(()=>{ setCurrentPage(1); },[pageNumbers]);

  useEffect(()=>{ setPageNumbers(Array.from({length: Math.ceil(listProduk.length/20)},(_,i)=>i+1)); },[listProduk]);

  useEffect(()=>{
    const indexOfLast=currentPage*20;
    const indexOfFirst=indexOfLast-20;
    setCurrentItems(listProduk.slice(indexOfFirst,indexOfLast));
  },[currentPage,listProduk]);

  useEffect(()=>{
    const listBaru=[...produkSemua].filter(p=>p.nama.toLowerCase().includes(cari.toLowerCase()));
    setListProduk(listBaru);
  },[cari]);

  useEffect(()=>{
    const {provinsi,kabupaten,kecamatan,kelurahan,kodepos,detail,totalAkhir,jenis,tanggal,items,nama,nohp,
      provinsiTagihan,kabupatenTagihan,kecamatanTagihan,kelurahanTagihan,kodeposTagihan,detailTagihan}=formData;

    const isFormValidKhusus=()=>{
      if(isInvoiceLike(jenis) && !alamatTagihanSama){
        const ok=[provinsiTagihan,kabupatenTagihan,kecamatanTagihan,kelurahanTagihan,kodeposTagihan,detailTagihan].every(v=>v&&v!=='');
        return ok;
      } return true;
    }
    const isFormValid=()=>{
      const filled=[provinsi,kabupaten,kecamatan,kelurahan,kodepos,detail,jenis,tanggal,nama,nohp].every(v=>v&&v!=='');
      const totalValid=totalAkhir>0;
      const itemsValid=items.length>0;
      return filled && totalValid && itemsValid;
    };
    setCanSave(isFormValid(formData) && isFormValidKhusus(jenis));
  },[formData,alamatTagihanSama]);

  useEffect(()=>{
    if(formData.items.length>0){
      const hargaTotal=formData.items.map(k=>Number(k.harga)*Number(k.jumlah)).reduce((a,b)=>a+b,0);
      setFormData({...formData, totalAkhir: Math.ceil(hargaTotal - (potongan.satuan=='rupiah' ? potongan.nominal : potongan.nominal/100*hargaTotal))});
    } else {
      setFormData({...formData, totalAkhir: 0});
    }
  },[formData.items, potongan]);

  const handlePilih=(produk,varian)=>{
    const stok=Number(varian.stok);
    const exist=formData.items.find(k=>k.id==produk.id && k.varian==varian.nama);
    if(stok==0){
      setNotif({teks:'Stok habis',show:true});
      setTimeout(()=>setNotif({teks:'Stok habis',show:false}),3000);
      return;
    }
    if(exist){
      setFormData({
        ...formData,
        items: formData.items.map(item=>{
          if(item.id==produk.id && item.varian==varian.nama){
            return { ...item, jumlah: (item.jumlah+1)>stok ? item.jumlah : (item.jumlah+1) }
          } return item;
        })
      });
    } else {
      setFormData({
        ...formData,
        items:[...formData.items,{
          id:produk.id, nama:produk.nama, jumlah:1, varian:varian.nama,
          harga: produk.harga * (potonganHargaSatuan>0 ? (100-potonganHargaSatuan) : 75) / 100,
          detail:{produk,varian}
        }]
      });
    }
  };

  useEffect(()=>{
    setFormData({
      ...formData,
      items: formData.items.map(item=>({
        ...item,
        harga: item.detail.produk.harga * (potonganHargaSatuan>0 ? (100-potonganHargaSatuan) : 75) / 100
      }))
    });
  },[potonganHargaSatuan]);

  const handleCount=(index,max,tambah)=>{
    setFormData({
      ...formData,
      items: formData.items
        .map((item,ind_i)=> index==ind_i ? { ...item, jumlah: (item.jumlah+tambah)>max ? item.jumlah : (item.jumlah+tambah) } : item)
        .filter(item=>item.jumlah>0)
    });
  };

  /* Build payload (sesuai sistem) */
  const buildPayload=()=>{
    const fAkhir={
      ...formData,
      provinsi: formData.provinsi.split("-")[1],
      kabupaten: formData.kabupaten.split("-")[1],
      kecamatan: formData.kecamatan.split("-")[1],
      kelurahan: formData.kelurahan.split("-")[0],
      provinsiTagihan: alamatTagihanSama ? formData.provinsi.split("-")[1] : (formData.provinsiTagihan ? formData.provinsiTagihan.split("-")[1] : null),
      kabupatenTagihan: alamatTagihanSama ? formData.kabupaten.split("-")[1] : (formData.kabupatenTagihan ? formData.kabupatenTagihan.split("-")[1] : null),
      kecamatanTagihan: alamatTagihanSama ? formData.kecamatan.split("-")[1] : (formData.kecamatanTagihan ? formData.kecamatanTagihan.split("-")[1] : null),
      kelurahanTagihan: alamatTagihanSama ? formData.kelurahan.split("-")[0] : (formData.kelurahanTagihan ? formData.kelurahanTagihan.split("-")[0] : null),
      kodeposTagihan: alamatTagihanSama ? formData.kodepos : formData.kodeposTagihan,
      detailTagihan: alamatTagihanSama ? formData.detail : formData.detailTagihan,
      items: formData.items.map(it=>({ id:it.id, nama:it.nama, varian:it.varian, jumlah:it.jumlah, harga:it.harga })),
      potonganHargaSatuan
    };
    const fAkhir1={
      ...fAkhir,
      // display → kosongkan field faktur/npwp
      provinsiTagihan: fAkhir.jenis=='display' ? null : fAkhir.provinsiTagihan,
      kabupatenTagihan: fAkhir.jenis=='display' ? null : fAkhir.kabupatenTagihan,
      kecamatanTagihan: fAkhir.jenis=='display' ? null : fAkhir.kecamatanTagihan,
      kelurahanTagihan: fAkhir.jenis=='display' ? null : fAkhir.kelurahanTagihan,
      kodeposTagihan: fAkhir.jenis=='display' ? null : fAkhir.kodeposTagihan,
      detailTagihan: fAkhir.jenis=='display' ? null : fAkhir.detailTagihan,
      npwp: fAkhir.jenis=='display' ? null : fAkhir.npwp,
    };
    return fAkhir1;
  };

  const openPreview=()=>{ const payload=buildPayload(); setPreviewPayload(payload); setPreviewOpen(true); };
  const closePreview=()=>setPreviewOpen(false);

  const handleSubmit=(payloadFromPreview=null)=>{
    const formDataAkhir1 = payloadFromPreview ?? buildPayload();
    async function fetchSubmit(){
      const response = await fetch(`/admin/order-offline/add`,{
        method:"POST", headers:{ "Content-Type":"application/json" }, body: JSON.stringify(formDataAkhir1),
      });
      const responseJson = await response.json();
      if(response.status==200){
        window.alert('Berhasil menambahkan pesanan');
        const seg = formData.jenis === 'display' ? 'sp' : formData.jenis; // display → sp
        window.location.href = `/admin/order/offline/${seg}`;
      } else {
        window.alert(responseJson.pesan);
      }
    }
    fetchSubmit();
  };

  /* 🔒 Escape untuk tutup preview */
  useEffect(()=>{ const onKey = (e)=>{ if(e.key==='Escape') setPreviewOpen(false); }; window.addEventListener('keydown', onKey); return ()=>window.removeEventListener('keydown', onKey); },[]);

  const copyPayload = () => {
    try {
      const p = buildPayload();
      navigator.clipboard.writeText(JSON.stringify(p, null, 2));
      setNotif({show:true, teks:'Payload tersalin'});
      setTimeout(()=>setNotif({show:false, teks:''}), 2000);
    } catch {}
  };

  // Helper penjelasan jenis dokumen
  const jenisInfo = (j) => {
    if(j==='sale') return 'SALE: Pesanan dengan faktur & pajak; butuh data penagihan/NPWP.';
    if(j==='nf')   return 'NF (Non Faktur): Pesanan tanpa faktur pajak, tetap butuh alamat penagihan.';
    return 'DISPLAY: SP untuk kebutuhan display; tanpa tagihan & NPWP.';
  };

  return (
    <>
      {modalKeranjang && (
        <div className="d-flex justify-content-center align-items-center" style={{position:'fixed',top:0,right:0,width:'100vw',height:'100svh',backgroundColor:'rgba(0,0,0,0.5)',zIndex:9}}>
          <div className="bg-white p-4 d-flex flex-column" style={{width:'80%',height:'80%',borderRadius:'10px'}}>
            <div className="d-flex justify-content-end">
              <button className="btn-close" onClick={()=>{setModalKeranjang(false)}}><i className="material-icons">close</i></button>
            </div>
            <h3 className="text-center" style={{fontWeight:700,letterSpacing:'-.2px'}}>Keranjang</h3>
            <hr />
            <div className="d-flex gap-2" style={{flex:1}}>
              <div style={{flex:1}} className="d-flex flex-column">
                <div className="d-flex gap-2">
                  <div style={{flex:1}}>
                    <input type="text" onChange={(e)=>{setCari(e.target.value)}} className="form-control mb-3" placeholder="Cari barang" />
                    <small data-type="info">Klik warna varian untuk menambahkan barang ke keranjang.</small>
                  </div>
                </div>
                <div className="w-100 mb-4" style={{flex:1,position:'relative'}}>
                  <div className="w-100 h-100 pe-2" style={{position:'absolute',overflow:'auto'}}>
                    <div className="d-flex flex-column gap-2">
                      {currentItems.map((i,ind_i)=>(
                        <div key={ind_i} className="item-produk">
                          <div className="d-flex gap-2 align-items-center">
                            <img src={i.gambar} alt={i.nama}/>
                            <div style={{flex:1}}>
                              <p className="m-0" style={{fontWeight:700,fontSize:'13.5px',textWrap:'nowrap'}}>{i.nama}</p>
                              <p className="m-0" style={{fontSize:'12px',color:'#475569'}}>{i.dimensi.panjang} x {i.dimensi.lebar} x {i.dimensi.tinggi}</p>
                            </div>
                            <div className="d-flex gap-2">
                              {i.varian.map((v,ind_v)=>(
                                <div className="item-varian" style={{backgroundColor:v.kode}} key={ind_v} title={v.nama+' • stok '+v.stok} onClick={()=>{handlePilih(i,v)}}></div>
                              ))}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>

                <div className="container-pag">
                  <a onClick={()=>setCurrentPage(prev=>Math.max(prev-1,1))} className='item-pag'>
                    <i className="material-icons">chevron_left</i>
                  </a>
                  {pageNumbers.map(number=> (number>=currentPage-2 && number<=currentPage+2) && (
                    <a key={number} onClick={()=>setCurrentPage(number)} className={`item-pag ${currentPage===number?"active":""}`}>{number}</a>
                  ))}
                  <a onClick={()=>setCurrentPage(prev=>Math.min(prev+1,pageNumbers.length))} className="item-pag">
                    <i className="material-icons">chevron_right</i>
                  </a>
                </div>
              </div>

              <div style={{flex:1,position:'relative'}}>
                <div className="w-100 h-100" style={{position:'absolute',overflow:'auto'}}>
                  <div className="d-flex flex-column gap-2">
                    {formData.items.length>0 ? (
                      <>
                        {formData.items.map((i,ind_i)=>(
                          <div key={ind_i} className="item-keranjang-admin w-100">
                            <div className="d-flex gap-2 align-items-center w-100">
                              <img src={i.detail.produk.gambar} alt={i.nama}/>
                              <div style={{flex:1}}>
                                <p className="m-0" style={{fontWeight:700,fontSize:'13.5px',textWrap:'nowrap'}}>{i.nama}</p>
                                <p className="m-0" style={{fontSize:'12px',color:'#475569'}}><b>{i.detail.produk.dimensi.panjang}</b> | {i.varian}</p>
                              </div>
                              <div className="counter">
                                <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), -1)}}>-</div>
                                <div className="angka">{i.jumlah}</div>
                                <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), 1)}}>+</div>
                              </div>
                            </div>
                          </div>
                        ))}
                      </>
                    ) : (
                      <div className={"text-secondary text-center"}><i>Keranjang kosong</i></div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Notif */}
      <div className={`notif ${notif.show ? 'show' : ''}`}>{notif.teks}</div>

      {/* ===== PREVIEW MODAL ===== */}
      {previewOpen && previewPayload && (
      <div className="preview-overlay" role="dialog" aria-modal="true" aria-labelledby="prevTitle" onClick={(e)=>{ if(e.target.classList.contains('preview-overlay')) closePreview(); }}>
        <div className="preview-card" role="document">
          <div className="preview-header">
            <h3 id="prevTitle" className="preview-title">Preview Pesanan</h3>
            <div style={{display:'flex', gap:8, alignItems:'center'}}>
              <span className={`badge ${potonganHargaSatuan>25 ? 'warn':'ok'}`}>
                <i className="material-icons" style={{fontSize:16}}>local_offer</i>
                Potongan Satuan: {potonganHargaSatuan}%
              </span>
              {Number(formData.downPayment) > Number(formData.totalAkhir) && (
                <span className="badge warn">
                  <i className="material-icons" style={{fontSize:16}}>warning</i>
                  DP melebihi total
                </span>
              )}
            </div>
          </div>

          <div className="preview-body">
            <div className="preview-grid">
              {/* KIRI */}
              <div className="preview-section">
                <div className="section-head">
                  <i className="material-icons" style={{fontSize:18}}>receipt_long</i> Rincian Pesanan
                </div>
                <div className="section-body">
                  <div className="dl" style={{marginBottom:12}}>
                    <b>Jenis</b><div>{previewPayload.jenis ? previewPayload.jenis.toUpperCase() : ''}</div>
                    <b>Tanggal</b><div className="mono">{formData.tanggal || '-'}</div>
                    <b>Nama</b><div>{previewPayload.nama || '-'}</div>
                    <b>No HP</b><div className="mono">{previewPayload.nohp || '-'}</div>
                    <b>NPWP</b><div className="mono">{previewPayload.npwp || '-'}</div>
                    <b>PO</b><div className="mono">{previewPayload.po || '-'}</div>
                    <b>Keterangan</b><div>{previewPayload.keterangan || '-'}</div>
                    <b>Alamat Kirim</b>
                    <div>
                      {(formData.provinsi && formData.provinsi.split('-')[1]) || ''},
                      {' '}{(formData.kabupaten && formData.kabupaten.split('-')[1]) || ''},
                      {' '}{(formData.kecamatan && formData.kecamatan.split('-')[1]) || ''},
                      {' '}{(formData.kelurahan && formData.kelurahan.split('-')[0]) || ''} {formData.kodepos}
                      <br/>{formData.detail}
                    </div>
                    {isInvoiceLike(formData.jenis) && (
                      <>
                        <b>Alamat Tagihan</b>
                        <div>
                          {alamatTagihanSama ? <i>Disamakan dengan alamat kirim</i> : (
                            <>
                              {(formData.provinsiTagihan && formData.provinsiTagihan.split('-')[1]) || ''},
                              {' '}{(formData.kabupatenTagihan && formData.kabupatenTagihan.split('-')[1]) || ''},
                              {' '}{(formData.kecamatanTagihan && formData.kecamatanTagihan.split('-')[1]) || ''},
                              {' '}{(formData.kelurahanTagihan && formData.kelurahanTagihan.split('-')[0]) || ''} {formData.kodeposTagihan}
                              <br/>{formData.detailTagihan}
                            </>
                          )}
                        </div>
                      </>
                    )}
                  </div>

                  <div className="preview-section" style={{border:'0', marginTop:4}}>
                    <div className="section-head" style={{borderRadius:'10px 10px 0 0'}}>
                      <i className="material-icons" style={{fontSize:18}}>inventory_2</i> Item
                    </div>
                    <div className="section-body" style={{padding:'0'}}>
                      <table className="items-table">
                        <thead>
                          <tr>
                            <th className="cell-thumb"></th>
                            <th>Produk</th>
                            <th>Varian</th>
                            <th className="cell-center">Qty</th>
                            <th className="cell-right">Harga</th>
                            <th className="cell-right">Subtotal</th>
                          </tr>
                        </thead>
                        <tbody>
                          {previewPayload.items.map((it, idx) => (
                            <tr key={idx}>
                              <td className="cell-thumb">
                                <img className="thumb" src={(formData.items[idx] && formData.items[idx].detail && formData.items[idx].detail.produk && formData.items[idx].detail.produk.gambar) || ''} alt={it.nama}/>
                              </td>
                              <td>{it.nama}</td>
                              <td className="mono">{it.varian}</td>
                              <td className="cell-center">{it.jumlah}</td>
                              <td className="cell-right mono">{formatRupiah(it.harga)}</td>
                              <td className="cell-right mono">{formatRupiah(Number(it.harga) * Number(it.jumlah))}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div className="note" style={{marginTop:12}}>
                    <i className="material-icons" style={{fontSize:18}}>info</i>
                    <div>
                      Periksa kembali <b>Qty</b>, <b>Harga Satuan</b>, dan <b>Alamat</b>. Klik <b>Kembali</b> untuk mengubah sebelum membuat pesanan.
                    </div>
                  </div>
                </div>
              </div>

              {/* KANAN: Ringkasan */}
              <div className="summary-card">
                <div className="section-head">
                  <i className="material-icons" style={{fontSize:18}}>payments</i> Ringkasan
                </div>
                <div className="summary-body">
                  <div className="row-sum"><span className="label">Subtotal Barang</span><span className="value">{formatRupiah(subtotalBarang())}</span></div>
                  <div className="row-sum">
                    <span className="label">Potongan (form)</span>
                    <span className="value">{potongan.satuan === 'rupiah' ? formatRupiah(potongan.nominal) : `${potongan.nominal}%`}</span>
                  </div>
                  <div className="row-sum"><span className="label">Potongan Harga Satuan</span><span className="value">{potonganHargaSatuan}%</span></div>
                  <div className="row-sum"><span className="label">Down Payment</span><span className="value">{formatRupiah(formData.downPayment)}</span></div>

                  <div className="divider"></div>

                  <div className="total-line">
                    <div className="ttl-label">Total Akhir</div>
                    <div className="ttl-val">{formatRupiah(formData.totalAkhir)}</div>
                  </div>
                  <div className="row-sum">
                    <span className="label">Sisa Bayar</span>
                    <span className="value">{formatRupiah(sisaBayar())}</span>
                  </div>

                  {potonganHargaSatuan > 25 && (
                    <div className="note" style={{marginTop:8}}>
                      <i className="material-icons" style={{fontSize:18, color:'#b42318'}}>warning</i>
                      Potongan harga satuan <b>maksimal 25%</b>. Backend akan menolak jika melebihi.
                    </div>
                  )}
                  {Number(formData.downPayment) > Number(formData.totalAkhir) && (
                    <div className="note" style={{marginTop:8}}>
                      <i className="material-icons" style={{fontSize:18, color:'#b45309'}}>report</i>
                      Down Payment tidak boleh melebihi Total Akhir. Sesuaikan nominalnya.
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>

          <div className="preview-footer">
            <button type="button" className="btn-copy" onClick={copyPayload} title="Salin JSON payload (debug)">
              <i className="material-icons" style={{fontSize:16,verticalAlign:'-3px'}}>content_copy</i> Salin Data
            </button>
            <div style={{display:'flex', gap:10}}>
              <button type="button" className="btn-ghost" onClick={closePreview}>Kembali</button>
              <button
                type="button"
                className="btn-primary"
                onClick={()=>{ closePreview(); handleSubmit(previewPayload); }}
                disabled={Number(formData.downPayment) > Number(formData.totalAkhir) || potonganHargaSatuan > 25}
                title={(Number(formData.downPayment) > Number(formData.totalAkhir) || potonganHargaSatuan > 25) ? 'Perbaiki data terlebih dahulu' : ''}
              >
                Kirim / Buat
              </button>
            </div>
          </div>
        </div>
      </div>
      )}

      {/* ===== MAIN ===== */}
      <div className="d-flex gap-4 w-100" style={{height:'fit-content'}}>
        <div className="d-flex flex-column gap-4" style={{flex:1}}>
          <div style={{width:'100%',flex:1,position:'relative',minHeight:'400px'}} className="mb-1">
            <div style={{position:'absolute',border:'1px solid rgb(167, 8, 8)',padding:'1em',borderRadius:'5px'}} className="w-100 h-100 d-flex flex-column">
              <div className="d-flex gap-1 justify-content-between">
                <div className="d-flex flex-column">
                  <p className="m-0" style={{fontWeight:600,fontSize:'14px'}}>Keranjang kamu</p>
                  <p className="m-0" style={{fontSize:'13px',letterSpacing:'-.2px',color:'#475569'}}>{formData.items.length} Produk</p>
                </div>
                {formData.items.length>0 ?
                  <a onClick={()=>{setModalKeranjang(true)}} style={{cursor:'pointer',color:'rgb(167, 8, 8)',fontSize:'32px',display:'flex',alignItems:'center',justifyContent:'center',textDecoration:'none',lineHeight:'0px'}}><i className="material-icons">edit</i></a>
                  :
                  <a onClick={()=>{setModalKeranjang(true)}} style={{cursor:'pointer',color:'rgb(167, 8, 8)',fontSize:'32px',display:'flex',alignItems:'center',justifyContent:'center',textDecoration:'none',lineHeight:'0px'}}><i className="material-icons">add_shopping_cart</i></a>
                }
              </div>
              <hr/>
              <div className="d-flex flex-column gap-1 w-100" style={{overflow:'auto',flex:1}}>
                {formData.items.length>0 ? (
                  <>
                    {formData.items.map((i,ind_i)=>(
                      <div key={ind_i} className="item-keranjang-admin w-100">
                        <div className="d-flex gap-2 align-items-center w-100">
                          <img src={i.detail.produk.gambar} alt={i.nama}/>
                          <div style={{flex:1}}>
                            <p className="m-0" style={{fontWeight:700,fontSize:'13.5px',textWrap:'nowrap'}}>{i.nama}</p>
                            <p className="m-0" style={{fontSize:'12px',color:'#475569'}}><b>{i.detail.produk.dimensi.panjang}</b> | {i.varian}</p>
                          </div>
                          <div className="counter">
                            <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), -1)}}>-</div>
                            <div className="angka">{i.jumlah}</div>
                            <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), 1)}}>+</div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </>
                ) : (
                  <div className={"text-secondary text-center"}><i>Keranjang kosong</i></div>
                )}
              </div>
              <hr />
              <div className="d-flex justify-content-between">
                <div className="d-flex flex-column">
                  <p className="m-0" style={{fontSize:'13.5px',letterSpacing:'-.2px'}}><b>Potongan</b></p>
                  <p className="m-0" style={{fontSize:'13.5px',letterSpacing:'-.2px'}}><b>Total Biaya</b></p>
                </div>
                <div className="d-flex flex-column align-items-end">
                  <p className="m-0" style={{fontSize:'13.5px',letterSpacing:'-.2px'}}><b>{potongan.satuan=='rupiah' ? `Rp ${Number(potongan.nominal||0).toLocaleString('id-ID')}` : `${potongan.nominal}%`}</b></p>
                  <p className="m-0" style={{fontSize:'13.5px',letterSpacing:'-.2px'}}><b>Rp. {Number(formData.totalAkhir||0).toLocaleString('id-ID')}</b></p>
                </div>
              </div>
            </div>
          </div>

          <div className="d-flex flex-column gap-1">
            <p className="m-0" style={{letterSpacing:'-.2px',fontSize:'15px',fontWeight:600}}>Detail :</p>
            <div className="d-flex gap-1 mb-1">
              <input type="text w-50" className="form-control" placeholder="Nama penerima" value={formData.nama} onChange={(e)=>{setFormData({...formData, nama:e.target.value})}} />
              <input type="text w-50" className="form-control" placeholder="Nama NPWP" value={formData.nama_npwp} onChange={(e)=>{setFormData({...formData, nama_npwp:e.target.value})}} />
              <input type="text w-50" className="form-control" placeholder="Nohp penerima" value={formData.nohp} onChange={(e)=>{setFormData({...formData, nohp:e.target.value})}} />
            </div>
            <div className="d-flex gap-1 mb-1">
              {isInvoiceLike(formData.jenis) &&
                <input type="text" className="w-100 form-control" placeholder="No NPWP" value={formData.npwp} onChange={(e)=>{setFormData({...formData, npwp:e.target.value})}} />
              }
              <input type="datetime-local" placeholder="Tanggal" className="w-100 form-control" value={formData.tanggal} onChange={(e)=>{setFormData({...formData, tanggal:e.target.value})}} />
            </div>
            <div className="d-flex gap-1 mb-1">
              <input type="text w-50" className="form-control" placeholder="Keterangan (opt)" value={formData.keterangan} onChange={(e)=>{setFormData({...formData, keterangan:e.target.value})}} />
              <input type="text w-50" className="form-control" placeholder="PO (opt)" value={formData.po} onChange={(e)=>{setFormData({...formData, po:e.target.value})}} />
            </div>
            <select value={formData.jenis} onChange={(e)=>{setFormData({...formData, jenis:e.target.value})}} className="form-select mb-1">
              <option value="sale">Sale</option>
              <option value="nf">NF (Non Faktur)</option>
              <option value="display">Display</option>
            </select>
            <small data-type="info">{jenisInfo(formData.jenis)}</small>
          </div>
        </div>

        <div className="d-flex flex-column" style={{flex:1}}>
          {/* Pengiriman */}
          <div className={"d-flex flex-column gap-1"}>
            <p className="m-0" style={{letterSpacing:'-.2px',fontSize:'15px',fontWeight:600}}>Alamat Pengiriman</p>
            <div className="mb-1 d-flex gap-1 align-items-center">
              <select className="form-select" value={formData.provinsi} onChange={(e)=>{setFormData({...formData, provinsi:e.target.value})}}>
                <option value="">-- Pilih provinsi --</option>
                <?php foreach ($provinsi as $p) { ?>
                  <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                <?php } ?>
              </select>
              <select className="form-select" value={formData.kabupaten} onChange={(e)=>{setFormData({...formData, kabupaten:e.target.value})}}>
                <option value="">-- Pilih kabupaten --</option>
                {kabupaten.map((k,ind_k)=>(
                  <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                ))}
              </select>
            </div>
            <div className="mb-1 d-flex gap-1 align-items-center">
              <select className="form-select" value={formData.kecamatan} onChange={(e)=>{setFormData({...formData, kecamatan:e.target.value})}}>
                <option value="">-- Pilih kecamatan --</option>
                {kecamatan.map((k,ind_k)=>(
                  <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                ))}
              </select>
              <select className="form-select" value={formData.kelurahan} onChange={(e)=>{setFormData({...formData, kelurahan:e.target.value})}}>
                <option value="">-- Pilih kelurahan --</option>
                {kelurahan.map((k,ind_k)=>(
                  <option key={ind_k} value={`${k.label}-${k.kodepos}`}>{k.label}</option>
                ))}
              </select>
            </div>
            <div className="mb-1 d-flex gap-1 align-items-center">
              <input type="text" className="form-control" placeholder="Kodepos" value={formData.kodepos} onChange={(e)=>{setFormData({...formData, kodepos:e.target.value})}} />
            </div>
            <input type="text" className="form-control mb-1" placeholder="Jalan, NO.Rumah, RT/RW," value={formData.detail} onChange={(e)=>{setFormData({...formData, detail:e.target.value})}} />
          </div>

          {/* Tagihan */}
          {isInvoiceLike(formData.jenis) && (
            <>
              <div className={"d-flex flex-column gap-1 my-4"}>
                {!alamatTagihanSama && (
                  <>
                    <p className="m-0" style={{letterSpacing:'-.2px',fontSize:'15px',fontWeight:600}}>Alamat Tagihan</p>
                    <div className="mb-1 d-flex gap-1 align-items-center">
                      <select className="form-select" value={formData.provinsiTagihan} onChange={(e)=>{setFormData({...formData, provinsiTagihan:e.target.value})}}>
                        <option value="">-- Pilih provinsi --</option>
                        <?php foreach ($provinsi as $p) { ?>
                          <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                        <?php } ?>
                      </select>
                      <select className="form-select" value={formData.kabupatenTagihan} onChange={(e)=>{setFormData({...formData, kabupatenTagihan:e.target.value})}}>
                        <option value="">-- Pilih kabupaten --</option>
                        {kabupatenTagihan.map((k,ind_k)=>(
                          <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                        ))}
                      </select>
                    </div>
                    <div className="mb-1 d-flex gap-1 align-items-center">
                      <select className="form-select" value={formData.kecamatanTagihan} onChange={(e)=>{setFormData({...formData, kecamatanTagihan:e.target.value})}}>
                        <option value="">-- Pilih kecamatan --</option>
                        {kecamatanTagihan.map((k,ind_k)=>(
                          <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                        ))}
                      </select>
                      <select className="form-select" value={formData.kelurahanTagihan} onChange={(e)=>{setFormData({...formData, kelurahanTagihan:e.target.value})}}>
                        <option value="">-- Pilih kelurahan --</option>
                        {kelurahanTagihan.map((k,ind_k)=>(
                          <option key={ind_k} value={`${k.label}-${k.kodepos}`}>{k.label}</option>
                        ))}
                      </select>
                    </div>
                    <div className="mb-1 d-flex gap-1 align-items-center">
                      <input type="text" className="form-control" placeholder="Kodepos" value={formData.kodeposTagihan} onChange={(e)=>{setFormData({...formData, kodeposTagihan:e.target.value})}} />
                    </div>
                    <input type="text" className="form-control mb-1" placeholder="Jalan, NO.Rumah, RT/RW," value={formData.detailTagihan} onChange={(e)=>{setFormData({...formData, detailTagihan:e.target.value})}} />
                  </>
                )}
                <label className="d-flex gap-2 align-items-center">
                  <input type="checkbox" onChange={(e)=>setAlamatTagihanSama(e.target.checked)} checked={alamatTagihanSama}/>
                  <p className="m-0">Samakan alamat pengiriman</p>
                </label>
              </div>

              <hr />
              <p className="m-0" style={{letterSpacing:'-.2px',fontSize:'15px',fontWeight:600}}>Potongan</p>
              <div className="d-flex gap-1 mb-1">
                <div style={{flex:1}}>
                  <input type="number" value={potongan.nominal} onChange={(e)=>{setPotongan({...potongan, nominal:Number(e.target.value)})}} className="form-control mb-1" placeholder="Nominal"/>
                </div>
                <div style={{flex:1}}>
                  <select className="form-select" value={potongan.satuan} onChange={(e)=>{setPotongan({...potongan, satuan:e.target.value})}}>
                    <option value="rupiah">Rupiah</option>
                    <option value="persen">Persen</option>
                  </select>
                </div>
              </div>

              <hr />
              <p className="m-0" style={{letterSpacing:'-.2px',fontSize:'15px',fontWeight:600}}>Down Payment</p>
              <input type="number" value={formData.downPayment} onChange={(e)=>{setFormData({...formData, downPayment:e.target.value})}} className="form-control mb-1"/>
              <small data-type="info">DP tidak boleh melebihi total akhir.</small>

              <hr />
              <p className="m-0" style={{letterSpacing:'-.2px',fontSize:'15px',fontWeight:600}}>Potongan Harga Satuan</p>
              <div className="input-group">
                <input type="number" value={potonganHargaSatuan} onChange={(e)=>{setPotonganHargaSatuan(e.target.value)}} className="form-control mb-1"/>
                <span className="input-group-text">%</span>
              </div>
              <small data-urgent>Potongan harga satuan <b>maksimal 25%</b>. Sistem akan menolak bila lebih dari itu.</small>
            </>
          )}

          <button
            type="button"
            onClick={()=>{ if(canSave) openPreview(); }}
            className={`btn-default-merah w-100 ${canSave ? '' : 'disabled'}`}
            title={canSave ? 'Lihat preview pesanan' : 'Lengkapi data & item terlebih dahulu'}
          >
            Preview &amp; Buat
          </button>
        </div>
      </div>
    </>
  );
};

ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>