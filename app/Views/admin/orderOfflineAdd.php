<!-- app/Views/admin/orderOfflineAdd.php -->
<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --merah: #b31217;
    --merah-600: #a50e12;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --slate-300: #d1d5db;
    --slate-700: #334155;
    --slate-800: #1f2937;
    --ring: rgba(255, 180, 180, .35);
    --ok: #16a34a;
    --warn: #b91c1c;
    --info: #2563eb;
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

h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    letter-spacing: -.2px
}

h1.teks-sedang::after {
    content: "";
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(179, 18, 23, .25), transparent);
    border-radius: 999px
}

/* Cards */
.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
    box-shadow: 0 10px 26px rgba(2, 8, 23, .06);
}

.card-head {
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    letter-spacing: -.2px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 14px;
}

/* Step */
.step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
}

.step .num {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-weight: 900;
    font-size: 12px;
}

.step .txt {
    line-height: 1.2
}

.step .txt b {
    font-weight: 900
}

.muted {
    color: #64748b;
    font-size: 12px
}

/* Form */
.form-control,
.form-select,
input {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    background: #fff;
    font-weight: 600;
    font-size: 13px;
    transition: border-color .15s, box-shadow .15s, background .15s;
}

.form-control:focus,
.form-select:focus,
input:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none;
}

/* Helper chip */
.helper {
    position: relative;
    display: block;
    margin: 8px 0 0;
    padding: 8px 12px 8px 36px;
    font-size: 12px;
    line-height: 1.35;
    letter-spacing: -.2px;
    background: #e8f4ff;
    color: #1e4e79;
    border: 1px solid #cfe7ff;
    border-radius: 10px;
}

.helper:before {
    content: "info";
    font-family: "Material Icons";
    font-size: 18px;
    line-height: 1;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: .9;
}

.helper.warn {
    background: #ffe8e8;
    border-color: #ffc9c9;
    color: #8b2a2b
}

.helper.warn:before {
    content: "priority_high"
}

.helper.info {
    background: #e8f4ff;
    border-color: #cfe7ff;
    color: #1e4e79
}

.helper.info:before {
    content: "info"
}

.helper.ok {
    background: #ecfdf3;
    border-color: #4ade80;
    color: #166534
}

.helper.ok:before {
    content: "check_circle"
}

/* Notif */
.notif {
    position: fixed;
    bottom: 50px;
    right: 0;
    padding: .6em 2em;
    color: #e84a49;
    border-radius: 10px;
    font-weight: 700;
    letter-spacing: -.2px;
    font-size: 13.5px;
    background: #fff8f8;
    border: 1px solid #ffd0d0;
    box-shadow: 0 10px 30px rgba(184, 27, 29, .15);
    transition: .5s;
    transform: translateX(100%);
    z-index: 99999;
}

.notif.show {
    right: 50px;
    transform: translateX(0%)
}

/* Keranjang cards */
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

.item-keranjang-admin {
    border: 1px dashed var(--slate-200);
    background: linear-gradient(#fff, #fff) padding-box,
        repeating-linear-gradient(90deg, #f3f4f6 0 8px, transparent 8px 16px) border-box;
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

.counter {
    display: flex;
    align-items: center;
    gap: 8px
}

.counter .action {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 26px;
    height: 26px;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #ffd2d2;
    color: var(--merah);
    font-weight: 900;
    cursor: pointer;
    transition: all .15s;
    box-shadow: 0 6px 16px rgba(225, 29, 72, .06);
}

.counter .action:hover {
    background: #fff2f2
}

.counter .action:active {
    transform: translateY(1px) scale(.98)
}

.counter .angka {
    width: 34px;
    text-align: center;
    font-weight: 900;
    letter-spacing: .2px
}

/* Buttons */
.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .7em 1em;
    border-radius: 10px;
    font-weight: 800
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
    font-weight: 900;
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

.btn-primary-secondary {
    background: #e5e7eb;
    color: #111827;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .15)
}

.btn-primary-secondary:hover {
    background: #d4d4d8
}

.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 900;
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

/* Preview modal */
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
    font-weight: 900;
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
    font-weight: 900;
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
    font-weight: 900
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
    font-weight: 900;
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
    font-weight: 900;
    letter-spacing: -.2px
}

.total-line .ttl-val {
    font-weight: 900;
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

.btn-copy {
    border: 1px dashed var(--slate-300);
    background: #f9fafb;
    padding: .6em .9em;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 900
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

/* Sticky bottom action for mobile */
.sticky-action {
    position: sticky;
    bottom: 0;
    padding: 12px 0 0;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1) 40%);
}

@media (max-width: 992px) {
    .layout-grid {
        flex-direction: column;
    }
}
</style>

<div style="padding:2em;" class="h-100 d-flex flex-column">
    <h1 class="teks-sedang mb-3">Buat Pesanan Offline</h1>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="step">
                <div class="num">1</div>
                <div class="txt"><b>Pilih Item</b><br><span class="muted">Tambah produk ke keranjang dulu.</span></div>
            </div>
            <div class="step mt-2">
                <div class="num">2</div>
                <div class="txt"><b>Isi Penerima & Dokumen</b><br><span class="muted">Nama, No HP, tanggal, jenis
                        (Sale/NF/Display).</span></div>
            </div>
            <div class="step mt-2">
                <div class="num">3</div>
                <div class="txt"><b>Isi Alamat</b><br><span class="muted">Alamat pengiriman wajib. Alamat tagihan wajib
                        untuk Sale/NF.</span></div>
            </div>
            <span class="helper info">Shortcut aman: isi keranjang → isi penerima → pilih jenis → isi alamat →
                preview.</span>
        </div>

        <div class="col-lg-4">
            <div class="card-soft">
                <div class="card-head">
                    <div class="card-title">
                        <!-- INI HTML biasa, boleh pakai style string -->
                        <i class="material-icons" style="font-size:18px;color:#64748b;">shopping_cart</i>
                        Ringkasan
                    </div>
                </div>
                <div class="card-body">
                    <div id="ringkasan-top" class="muted">Memuat ringkasan...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="container-react" style="flex:1;" class="d-flex"></div>
</div>

<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<script>
console.log(<?= $produkJson ?>);
</script>

<script type="text/babel" data-presets="env,react">
    const { useState, useEffect, useMemo } = React;

const produkSemua = <?= $produkJson ?>;

function nowLocalDatetimeValue(){
  const d = new Date();
  const pad = (n)=>String(n).padStart(2,'0');
  return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
const clamp = (n,min,max)=>Math.min(max, Math.max(min, Number(n||0)));
const toNum = (x)=>Number(x||0);

const StepBadge = ({ n }) => (
  <span style={{
    display: 'inline-grid',
    placeItems: 'center',
    width: 28,
    height: 28,
    borderRadius: 999,
    background: '#fee2e2',
    border: '1px solid #fecaca',
    color: '#b91c1c',
    fontWeight: 900
  }}>{n}</span>
);

const App = () => {
  const [listProduk, setListProduk] = useState(produkSemua);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageNumbers, setPageNumbers] = useState([]);
  const [currentItems, setCurrentItems] = useState([]);
  const [cari, setCari] = useState('');
  const [modalKeranjang, setModalKeranjang] = useState(false);
  const [notif, setNotif] = useState({ show:false, teks:'' });

  const [formData, setFormData] = useState({
    provinsi:'', kabupaten:'', kecamatan:'', kelurahan:'', kodepos:'', detail:'',
    provinsiTagihan:'', kabupatenTagihan:'', kecamatanTagihan:'', kelurahanTagihan:'', kodeposTagihan:'', detailTagihan:'',
    totalAkhir:0, jenis:'sale', tanggal: nowLocalDatetimeValue(),
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
  const [potongan, setPotongan] = useState({ nominal:0, satuan:'persen' });
  const [alamatTagihanSama, setAlamatTagihanSama] = useState(false);

  /* PREVIEW */
  const [previewOpen, setPreviewOpen] = useState(false);
  const [previewPayload, setPreviewPayload] = useState(null);
  const formatRupiah = (n)=>`Rp ${Number(n||0).toLocaleString('id-ID')}`;
  const isInvoiceLike = (j)=> j==='sale' || j==='nf';

  const subtotalBarang = useMemo(()=>{
    return (formData.items||[]).reduce((acc,it)=>acc + (toNum(it.harga)*toNum(it.jumlah)), 0);
  },[formData.items]);

  const potonganFormNominal = useMemo(()=>{
    const sub = subtotalBarang;
    if(potongan.satuan==='rupiah') return toNum(potongan.nominal);
    return (toNum(potongan.nominal)/100)*sub;
  },[potongan, subtotalBarang]);

  const totalAkhirComputed = useMemo(()=>{
    const sub = subtotalBarang;
    const pot = potonganFormNominal;
    return Math.max(0, Math.ceil(sub - pot));
  },[subtotalBarang, potonganFormNominal]);

  const sisaBayar = useMemo(()=>{
    return Math.max(0, toNum(formData.totalAkhir) - toNum(formData.downPayment));
  },[formData.totalAkhir, formData.downPayment]);

  useEffect(()=>{ setFormData(prev=>({...prev,totalAkhir: totalAkhirComputed})); },[totalAkhirComputed]);

  useEffect(()=>{
    const fixed = clamp(potonganHargaSatuan, 0, 25);
    if(fixed !== potonganHargaSatuan) setPotonganHargaSatuan(fixed);
  },[potonganHargaSatuan]);

  useEffect(()=>{
    const fixed = clamp(formData.downPayment, 0, toNum(formData.totalAkhir));
    if(fixed !== formData.downPayment) setFormData(prev=>({...prev, downPayment: fixed}));
  },[formData.downPayment, formData.totalAkhir]);

  /* ===== ringkasan di kanan atas (HTML biasa via innerHTML aman) ===== */
  useEffect(()=>{
    const el = document.getElementById('ringkasan-top');
    if(!el) return;
    el.innerHTML = `
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>Item</span><b>${(formData.items||[]).length}</b></div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>Subtotal</span><b>${formatRupiah(subtotalBarang)}</b></div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>Total Akhir</span><b style="color:#b31217;">${formatRupiah(formData.totalAkhir)}</b></div>
      <div style="display:flex;justify-content:space-between;"><span>Sisa Bayar</span><b>${formatRupiah(sisaBayar)}</b></div>
      <div class="helper" style="margin-top:10px;">Klik <b>Preview & Buat</b> kalau sudah siap.</div>
    `;
  },[formData.items, subtotalBarang, formData.totalAkhir, sisaBayar]);

  /* ========= DEPENDENCY ADDRESS (kirim) ========= */
  useEffect(()=>{
    if(formData.provinsi){
      const idprov = formData.provinsi.split("-")[0];
      (async ()=>{
        try{
          const r = await fetch("/getkota/"+idprov);
          const kota = await r.json();
          setKabupaten(Array.isArray(kota)?kota:(kota?.label||kota?.results||[]));
        }catch{ setKabupaten([]); }
      })();
    }
    setFormData(prev=>({...prev,kabupaten:'',kecamatan:'',kelurahan:'',kodepos:''}));
    setKecamatan([]); setKelurahan([]);
  },[formData.provinsi]);

  useEffect(()=>{
    if(formData.kabupaten){
      const idkab = formData.kabupaten.split("-")[0];
      (async ()=>{
        try{
          const r = await fetch("/getkec/"+idkab);
          const kota = await r.json();
          setKecamatan(Array.isArray(kota)?kota:(kota?.label||kota?.results||[]));
        }catch{ setKecamatan([]); }
      })();
    }
    setFormData(prev=>({...prev,kecamatan:'',kelurahan:'',kodepos:''}));
    setKelurahan([]);
  },[formData.kabupaten]);

  useEffect(()=>{
    if(formData.kecamatan){
      const idkec = formData.kecamatan.split("-")[0];
      (async ()=>{
        try{
          const r = await fetch("/getkode/"+idkec);
          const hasil = await r.json();
          setKelurahan(Array.isArray(hasil)?hasil:(hasil?.label||hasil?.results||[]));
        }catch{ setKelurahan([]); }
      })();
    }
    setFormData(prev=>({...prev,kelurahan:'',kodepos:''}));
  },[formData.kecamatan]);

  useEffect(()=>{
    if(formData.kelurahan){
      const parts = formData.kelurahan.split('-');
      setFormData(prev=>({...prev, kodepos: parts[1] || ''}));
    }
  },[formData.kelurahan]);

  /* ========= DEPENDENCY ADDRESS (tagihan) ========= */
  useEffect(()=>{
    if(formData.provinsiTagihan){
      const idprov = formData.provinsiTagihan.split("-")[0];
      (async ()=>{
        try{
          const r = await fetch("/getkota/"+idprov);
          const kota = await r.json();
          setKabupatenTagihan(Array.isArray(kota)?kota:(kota?.label||kota?.results||[]));
        }catch{ setKabupatenTagihan([]); }
      })();
    }
    setFormData(prev=>({...prev,kabupatenTagihan:'',kecamatanTagihan:'',kelurahanTagihan:'',kodeposTagihan:''}));
    setKecamatanTagihan([]); setKelurahanTagihan([]);
  },[formData.provinsiTagihan]);

  useEffect(()=>{
    if(formData.kabupatenTagihan){
      const idkab = formData.kabupatenTagihan.split("-")[0];
      (async ()=>{
        try{
          const r = await fetch("/getkec/"+idkab);
          const kota = await r.json();
          setKecamatanTagihan(Array.isArray(kota)?kota:(kota?.label||kota?.results||[]));
        }catch{ setKecamatanTagihan([]); }
      })();
    }
    setFormData(prev=>({...prev,kecamatanTagihan:'',kelurahanTagihan:'',kodeposTagihan:''}));
    setKelurahanTagihan([]);
  },[formData.kabupatenTagihan]);

  useEffect(()=>{
    if(formData.kecamatanTagihan){
      const idkec = formData.kecamatanTagihan.split("-")[0];
      (async ()=>{
        try{
          const r = await fetch("/getkode/"+idkec);
          const hasil = await r.json();
          setKelurahanTagihan(Array.isArray(hasil)?hasil:(hasil?.label||hasil?.results||[]));
        }catch{ setKelurahanTagihan([]); }
      })();
    }
    setFormData(prev=>({...prev,kelurahanTagihan:'',kodeposTagihan:''}));
  },[formData.kecamatanTagihan]);

  useEffect(()=>{
    if(formData.kelurahanTagihan){
      const parts = formData.kelurahanTagihan.split('-');
      setFormData(prev=>({...prev, kodeposTagihan: parts[1] || ''}));
    }
  },[formData.kelurahanTagihan]);

  useEffect(()=>{
    if(!alamatTagihanSama) return;
    setFormData(prev => ({
      ...prev,
      provinsiTagihan: prev.provinsi,
      kabupatenTagihan: prev.kabupaten,
      kecamatanTagihan: prev.kecamatan,
      kelurahanTagihan: prev.kelurahan,
      kodeposTagihan: prev.kodepos,
      detailTagihan: prev.detail,
    }));
  }, [alamatTagihanSama, formData.provinsi, formData.kabupaten, formData.kecamatan, formData.kelurahan, formData.kodepos, formData.detail]);

  /* ========= Pagination & filter produk ========= */
  useEffect(()=>{ setCurrentPage(1); },[pageNumbers.length]);
  useEffect(()=>{ setPageNumbers(Array.from({length: Math.ceil(listProduk.length/20)},(_,i)=>i+1)); },[listProduk]);
  useEffect(()=>{
    const indexOfLast = currentPage*20;
    const indexOfFirst = indexOfLast-20;
    setCurrentItems(listProduk.slice(indexOfFirst,indexOfLast));
  },[currentPage,listProduk]);
  useEffect(()=>{
    const q = (cari||'').toLowerCase();
    setListProduk([...produkSemua].filter(p=>(p.nama||'').toLowerCase().includes(q)));
  },[cari]);

  /* ========= Validasi form ========= */
  useEffect(()=>{
    const {provinsi,kabupaten,kecamatan,kelurahan,kodepos,detail,jenis,tanggal,items,nama,nohp,
      provinsiTagihan,kabupatenTagihan,kecamatanTagihan,kelurahanTagihan,kodeposTagihan,detailTagihan} = formData;

    const isFormValidKhusus = ()=>{
      if(isInvoiceLike(jenis) && !alamatTagihanSama){
        return [provinsiTagihan,kabupatenTagihan,kecamatanTagihan,kelurahanTagihan,kodeposTagihan,detailTagihan].every(v=>v && v!=='');
      }
      return true;
    };

    const isFormValid = ()=>{
      const filled = [provinsi,kabupaten,kecamatan,kelurahan,kodepos,detail,jenis,tanggal,nama,nohp].every(v=>v && v!=='');
      const totalValid = Number(formData.totalAkhir) > 0;
      const itemsValid = (items||[]).length > 0;
      return filled && totalValid && itemsValid;
    };

    setCanSave(isFormValid() && isFormValidKhusus());
  },[formData, alamatTagihanSama]);

  /* ========= Keranjang ========= */
  const showToast = (teks, ms=2500)=>{
    setNotif({ show:true, teks });
    setTimeout(()=>setNotif({ show:false, teks:'' }), ms);
  };

  const handlePilih = (produk,varian)=>{
    const stok = Number(varian.stok);
    const exist = formData.items.find(k=>k.id==produk.id && k.varian==varian.nama);
    if(stok===0){ showToast('Stok habis'); return; }

    if(exist){
      setFormData(prev=>({...prev,
        items: prev.items.map(item=>{
          if(item.id==produk.id && item.varian==varian.nama){
            const next = item.jumlah+1;
            return { ...item, jumlah: next>stok ? item.jumlah : next };
          }
          return item;
        })
      }));
    }else{
      setFormData(prev=>({...prev,
        items:[...prev.items,{
          id:produk.id, nama:produk.nama, jumlah:1, varian:varian.nama,
          harga: produk.harga * (toNum(potonganHargaSatuan)>0 ? (100-toNum(potonganHargaSatuan)) : 75) / 100,
          detail:{produk,varian}
        }]
      }));
    }
  };

  useEffect(()=>{
    setFormData(prev=>({...prev,
      items: prev.items.map(item=>({
        ...item,
        harga: item.detail.produk.harga * (toNum(potonganHargaSatuan)>0 ? (100-toNum(potonganHargaSatuan)) : 75) / 100
      }))
    }));
  },[potonganHargaSatuan]);

  const handleCount = (index,max,tambah)=>{
    setFormData(prev=>({...prev,
      items: prev.items
        .map((item,ind_i)=>{
          if(index!==ind_i) return item;
          const next = item.jumlah + tambah;
          return { ...item, jumlah: next>max ? item.jumlah : next };
        })
        .filter(item=>item.jumlah>0)
    }));
  };

  /* ========= Build payload ========= */
  const buildPayload = ()=>{
  
    const specialPriceActive = Number(potongan.nominal) > 0 ? 1 : 0;
    const fAkhir = {
      ...formData,

      specialPriceActive: specialPriceActive,
      specialPriceNominal : Number(potongan.nominal || 0),
      specialPriceSatuan : potongan.satuan,

      provinsi: formData.provinsi.split("-")[1],
      kabupaten: formData.kabupaten.split("-")[1],
      kecamatan: formData.kecamatan.split("-")[1],
      kelurahan: formData.kelurahan.split("-")[0],

      provinsiTagihan: alamatTagihanSama ? (formData.provinsi.split("-")[1]) : (formData.provinsiTagihan ? formData.provinsiTagihan.split("-")[1] : null),
      kabupatenTagihan: alamatTagihanSama ? (formData.kabupaten.split("-")[1]) : (formData.kabupatenTagihan ? formData.kabupatenTagihan.split("-")[1] : null),
      kecamatanTagihan: alamatTagihanSama ? (formData.kecamatan.split("-")[1]) : (formData.kecamatanTagihan ? formData.kecamatanTagihan.split("-")[1] : null),
      kelurahanTagihan: alamatTagihanSama ? (formData.kelurahan.split("-")[0]) : (formData.kelurahanTagihan ? formData.kelurahanTagihan.split("-")[0] : null),
      kodeposTagihan: alamatTagihanSama ? formData.kodepos : formData.kodeposTagihan,
      detailTagihan: alamatTagihanSama ? formData.detail : formData.detailTagihan,

      items: formData.items.map(it=>({ id:it.id, nama:it.nama, varian:it.varian, jumlah:it.jumlah, harga:it.harga })),
      potonganHargaSatuan: toNum(potonganHargaSatuan||0),
    };

    const fAkhir1 = {
      ...fAkhir,
      provinsiTagihan: fAkhir.jenis=='display' ? null : fAkhir.provinsiTagihan,
      kabupatenTagihan: fAkhir.jenis=='display' ? null : fAkhir.kabupatenTagihan,
      kecamatanTagihan: fAkhir.jenis=='display' ? null : fAkhir.kecamatanTagihan,
      kelurahanTagihan: fAkhir.jenis=='display' ? null : fAkhir.kelurahanTagihan,
      kodeposTagihan: fAkhir.jenis=='display' ? null : fAkhir.kodeposTagihan,
      detailTagihan: fAkhir.jenis=='display' ? null : fAkhir.detailTagihan,
      npwp: fAkhir.jenis=='display' ? null : fAkhir.npwp,
      nama_npwp: fAkhir.jenis=='display' ? null : fAkhir.nama_npwp,
    };

    return fAkhir1;
  };

  /* ========= Preview ========= */
  const openPreview = ()=>{
    const payload = buildPayload();
    setPreviewPayload(payload);
    setPreviewOpen(true);
  };
  const closePreview = ()=>setPreviewOpen(false);

  const invalidFinancial =
    toNum(formData.downPayment) > toNum(formData.totalAkhir) ||
    toNum(potonganHargaSatuan) > 25;

  const handleSubmit = (payloadFromPreview=null, isDraft=false)=>{
    const basePayload = payloadFromPreview ?? buildPayload();
    const formDataAkhir1 = { ...basePayload, isDraft: isDraft ? 1 : 0 };

    (async ()=>{
      try{
        const response = await fetch(`/admin/order-offline/add`,{
          method:"POST",
          headers:{ "Content-Type":"application/json" },
          body: JSON.stringify(formDataAkhir1),
        });
        const responseJson = await response.json().catch(()=>({}));

        if(response.status==200){
          window.alert(isDraft ? 'Draft pesanan tersimpan' : 'Berhasil menambahkan pesanan');
          const seg = formData.jenis === 'display' ? 'sp' : formData.jenis;
          window.location.href = `/admin/order/offline/${seg}`;
        } else {
          window.alert(responseJson.pesan || 'Gagal menyimpan pesanan');
        }
      }catch{
        window.alert('Gagal menyimpan (network error)');
      }
    })();
  };

  useEffect(()=>{
    const onKey = (e)=>{ if(e.key==='Escape') setPreviewOpen(false); };
    window.addEventListener('keydown', onKey);
    return ()=>window.removeEventListener('keydown', onKey);
  },[]);

  const copyPayload = ()=>{
    try{
      const p = buildPayload();
      navigator.clipboard.writeText(JSON.stringify(p,null,2));
      showToast('Payload tersalin', 2000);
    }catch{
      showToast('Gagal menyalin', 2000);
    }
  };

  const jenisInfo = (j)=>{
    if(j==='sale') return 'SALE: dengan faktur & pajak; butuh alamat tagihan + NPWP (jika ada).';
    if(j==='nf') return 'NF: non faktur; tetap butuh alamat tagihan.';
    return 'DISPLAY: SP display; tanpa tagihan & NPWP.';
  };

  const getThumbByItem = (it)=>{
    const found = formData.items.find(x => x.id === it.id && x.varian === it.varian);
    return found?.detail?.produk?.gambar || '';
  };

  return (
    <>
      {/* ===== Modal Keranjang ===== */}
      {modalKeranjang && (
        <div className="d-flex justify-content-center align-items-center"
          style={{position:'fixed',top:0,right:0,width:'100vw',height:'100svh',backgroundColor:'rgba(0,0,0,0.5)',zIndex:9}}>
          <div className="bg-white p-4 d-flex flex-column" style={{width:'80%',height:'80%',borderRadius:10}}>
            <div className="d-flex justify-content-end">
              <button className="btn-close" onClick={()=>setModalKeranjang(false)}>
                <i className="material-icons">close</i>
              </button>
            </div>
            <h3 className="text-center" style={{fontWeight:900,letterSpacing:'-.2px'}}>Pilih Produk</h3>
            <div className="helper info" style={{marginTop:0}}>Klik warna varian untuk menambahkan barang ke keranjang.</div>
            <hr />
            <div className="d-flex gap-2" style={{flex:1}}>
              <div style={{flex:1}} className="d-flex flex-column">
                <input type="text" onChange={(e)=>setCari(e.target.value)} className="form-control mb-3" placeholder="Cari barang..." />

                <div className="w-100 mb-4" style={{flex:1,position:'relative'}}>
                  <div className="w-100 h-100 pe-2" style={{position:'absolute',overflow:'auto'}}>
                    <div className="d-flex flex-column gap-2">
                      {currentItems.map((i,ind_i)=>(
                        <div key={ind_i} className="item-produk">
                          <div className="d-flex gap-2 align-items-center">
                            <img src={i.gambar} alt={i.nama}/>
                            <div style={{flex:1}}>
                              <p className="m-0" style={{fontWeight:900,fontSize:'13.5px',textWrap:'nowrap'}}>{i.nama}</p>
                              <p className="m-0" style={{fontSize:12,color:'#475569'}}>{i.dimensi?.panjang} x {i.dimensi?.lebar} x {i.dimensi?.tinggi}</p>
                            </div>
                            <div className="d-flex gap-2">
                              {(i.varian||[]).map((v,ind_v)=>(
                                <div className="item-varian"
                                  style={{backgroundColor:v.kode}}
                                  key={ind_v}
                                  title={`${v.nama} • stok ${v.stok}`}
                                  onClick={()=>handlePilih(i,v)}
                                />
                              ))}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>

                <div className="d-flex align-items-center justify-content-center gap-2">
                  <button className="btn-ghost" onClick={()=>setCurrentPage(prev=>Math.max(prev-1,1))}>
                    <i className="material-icons" style={{fontSize:18,verticalAlign:'-3px'}}>chevron_left</i>
                  </button>
                  <div className="muted">{currentPage} / {pageNumbers.length || 1}</div>
                  <button className="btn-ghost" onClick={()=>setCurrentPage(prev=>Math.min(prev+1,pageNumbers.length))}>
                    <i className="material-icons" style={{fontSize:18,verticalAlign:'-3px'}}>chevron_right</i>
                  </button>
                </div>
              </div>

              <div style={{flex:1,position:'relative'}}>
                <div className="w-100 h-100" style={{position:'absolute',overflow:'auto'}}>
                  <div className="d-flex flex-column gap-2">
                    {formData.items.length>0 ? (
                      formData.items.map((i,ind_i)=>(
                        <div key={ind_i} className="item-keranjang-admin w-100">
                          <div className="d-flex gap-2 align-items-center w-100">
                            <img src={i.detail?.produk?.gambar} alt={i.nama}/>
                            <div style={{flex:1}}>
                              <p className="m-0" style={{fontWeight:900,fontSize:'13.5px',textWrap:'nowrap'}}>{i.nama}</p>
                              <p className="m-0" style={{fontSize:12,color:'#475569'}}><b>{i.detail?.produk?.dimensi?.panjang}</b> | {i.varian}</p>
                            </div>
                            <div className="counter">
                              <div className="action" onClick={()=>handleCount(ind_i, Number(i.detail?.varian?.stok||0), -1)}>-</div>
                              <div className="angka">{i.jumlah}</div>
                              <div className="action" onClick={()=>handleCount(ind_i, Number(i.detail?.varian?.stok||0), 1)}>+</div>
                            </div>
                          </div>
                        </div>
                      ))
                    ) : (
                      <div className="text-secondary text-center"><i>Keranjang kosong</i></div>
                    )}
                  </div>
                </div>
              </div>
            </div>

            <div className="sticky-action">
              <button className="btn-default-merah w-100" onClick={()=>setModalKeranjang(false)}>
                Selesai Pilih Item
              </button>
            </div>
          </div>
        </div>
      )}

      <div className={`notif ${notif.show ? 'show' : ''}`}>{notif.teks}</div>

      {/* ===== Preview Modal ===== */}
      {previewOpen && previewPayload && (
        <div className="preview-overlay" role="dialog" aria-modal="true" aria-labelledby="prevTitle"
          onClick={(e)=>{ if(e.target.classList.contains('preview-overlay')) closePreview(); }}>
          <div className="preview-card" role="document">
            <div className="preview-header">
              <h3 id="prevTitle" className="preview-title">Preview Pesanan</h3>
              <div style={{display:'flex', gap:8, alignItems:'center'}}>
                <span className={`badge ${toNum(potonganHargaSatuan)>25 ? 'warn':'ok'}`}>
                  <i className="material-icons" style={{fontSize:16}}>local_offer</i>
                  Potongan Satuan: {toNum(potonganHargaSatuan)}%
                </span>
                {toNum(formData.downPayment) > toNum(formData.totalAkhir) && (
                  <span className="badge warn">
                    <i className="material-icons" style={{fontSize:16}}>warning</i>
                    DP melebihi total
                  </span>
                )}
              </div>
            </div>

            <div className="preview-body">
              <div className="preview-grid">
                <div className="preview-section">
                  <div className="section-head">
                    <i className="material-icons" style={{fontSize:18}}>receipt_long</i> Rincian Pesanan
                  </div>
                  <div className="section-body">
                    <div className="dl" style={{marginBottom:12}}>
                      <b>Jenis</b><div>{previewPayload.jenis ? String(previewPayload.jenis).toUpperCase() : ''}</div>
                      <b>Tanggal</b><div className="mono">{formData.tanggal || '-'}</div>
                      <b>Nama</b><div>{previewPayload.nama || '-'}</div>
                      <b>No HP</b><div className="mono">{previewPayload.nohp || '-'}</div>
                      <b>Nama NPWP</b><div>{previewPayload.nama_npwp || '-'}</div>
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

                    <div className="preview-section" style={{border:0, marginTop:4}}>
                      <div className="section-head" style={{borderRadius:'10px 10px 0 0'}}>
                        <i className="material-icons" style={{fontSize:18}}>inventory_2</i> Item
                      </div>
                      <div className="section-body" style={{padding:0}}>
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
                                  <img className="thumb" src={getThumbByItem(it)} alt={it.nama}/>
                                </td>
                                <td>{Number(potongan.nominal) > 0 && (<b style={{color:'#b31217'}}>[SPESIAL PRICE]</b>)}{it.nama}</td>
                                <td className="mono">{it.varian}</td>
                                <td className="cell-center">{it.jumlah}</td>
                                <td className="cell-right mono">{formatRupiah(it.harga)}</td>
                                <td className="cell-right mono">{formatRupiah(toNum(it.harga) * toNum(it.jumlah))}</td>
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <div className="helper info" style={{marginTop:12}}>
                      Periksa <b>Qty</b>, <b>Harga</b>, dan <b>Alamat</b>. Kalau sudah benar, lanjut simpan.
                    </div>
                  </div>
                </div>

                <div className="summary-card">
                  <div className="section-head">
                    <i className="material-icons" style={{fontSize:18}}>payments</i> Ringkasan
                  </div>
                  <div className="summary-body">
                    <div className="row-sum"><span className="label">Subtotal Barang</span><span className="value">{formatRupiah(subtotalBarang)}</span></div>
                    <div className="row-sum"><span className="label">Potongan (form)</span><span className="value">{potongan.satuan==='rupiah' ? formatRupiah(potongan.nominal) : `${potongan.nominal}%`}</span></div>
                    <div className="row-sum"><span className="label">Potongan Harga Satuan</span><span className="value">{toNum(potonganHargaSatuan)}%</span></div>
                    <div className="row-sum"><span className="label">Down Payment</span><span className="value">{formatRupiah(formData.downPayment)}</span></div>

                    <div className="divider"></div>

                    <div className="total-line">
                      <div className="ttl-label">Total Akhir</div>
                      <div className="ttl-val">{formatRupiah(formData.totalAkhir)}</div>
                    </div>
                    <div className="row-sum"><span className="label">Sisa Bayar</span><span className="value">{formatRupiah(sisaBayar)}</span></div>

                    {toNum(potonganHargaSatuan)>25 && (
                      <div className="helper warn" style={{marginTop:8}}>
                        Potongan harga satuan <b>maksimal 25%</b>.
                      </div>
                    )}
                    {toNum(formData.downPayment)>toNum(formData.totalAkhir) && (
                      <div className="helper warn" style={{marginTop:8}}>
                        Down Payment tidak boleh melebihi Total Akhir.
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
                  className="btn-primary btn-primary-secondary"
                  onClick={()=>{ if(!invalidFinancial){ closePreview(); handleSubmit(previewPayload, true); } }}
                  disabled={invalidFinancial}
                >
                  Simpan Draft
                </button>

                <button
                  type="button"
                  className="btn-primary"
                  onClick={()=>{ if(!invalidFinancial){ closePreview(); handleSubmit(previewPayload, false); } }}
                  disabled={invalidFinancial}
                >
                  Simpan &amp; Finalisasi
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ===== MAIN LAYOUT ===== */}
      <div className="d-flex gap-3 w-100 layout-grid">

        {/* LEFT */}
        <div className="d-flex flex-column gap-3" style={{flex:1}}>

          {/* 1) Keranjang */}
          <div className="card-soft">
            <div className="card-head">
              <div className="card-title">
                <StepBadge n={1} />
                Keranjang (Wajib)
              </div>
              <button className="btn-ghost" onClick={()=>setModalKeranjang(true)}>
                <i className="material-icons" style={{fontSize:18,verticalAlign:'-3px'}}>
                  {formData.items.length>0 ? 'edit' : 'add_shopping_cart'}
                </i>
                {formData.items.length>0 ? 'Edit Item' : 'Tambah Item'}
              </button>
            </div>
            <div className="card-body">
              {formData.items.length>0 ? (
                <div className="d-flex flex-column gap-2" style={{maxHeight:280, overflow:'auto'}}>
                  {formData.items.map((i,ind_i)=>(
                    <div key={ind_i} className="item-keranjang-admin w-100">
                      <div className="d-flex gap-2 align-items-center w-100">
                        <img src={i.detail?.produk?.gambar} alt={i.nama}/>
                        <div style={{flex:1}}>
                          <p className="m-0" style={{fontWeight:900,fontSize:'13.5px',textWrap:'nowrap'}}>{i.nama}</p>
                          <p className="m-0" style={{fontSize:12,color:'#475569'}}><b>{i.detail?.produk?.dimensi?.panjang}</b> | {i.varian}</p>
                          <p className="m-0" style={{fontSize:12,color:'#64748b'}}>Harga: <b>{formatRupiah(i.harga)}</b></p>
                        </div>
                        <div className="counter">
                          <div className="action" onClick={()=>handleCount(ind_i, Number(i.detail?.varian?.stok||0), -1)}>-</div>
                          <div className="angka">{i.jumlah}</div>
                          <div className="action" onClick={()=>handleCount(ind_i, Number(i.detail?.varian?.stok||0), 1)}>+</div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="helper warn" style={{marginTop:0}}>
                  Keranjang masih kosong. Klik <b>Tambah Item</b> dulu ya 🙏
                </div>
              )}

              <div className="d-flex justify-content-between mt-3">
                <div className="muted">Subtotal: <b>{formatRupiah(subtotalBarang)}</b></div>
                <div className="muted">
                  Total Akhir: <b style={{color:'#b31217'}}>{formatRupiah(formData.totalAkhir)}</b>
                </div>
              </div>
            </div>
          </div>

          {/* 2) Penerima & Dokumen */}
          <div className="card-soft">
            <div className="card-head">
              <div className="card-title">
                <StepBadge n={2} />
                Penerima & Dokumen (Wajib)
              </div>
            </div>
            <div className="card-body">
              <div className="row g-2">
                <div className="col-md-4">
                  <input type="text" className="form-control" placeholder="Nama penerima *" value={formData.nama}
                    onChange={(e)=>setFormData({...formData, nama:e.target.value})}/>
                </div>
                <div className="col-md-4">
                  <input type="text" className="form-control" placeholder="No HP penerima *" value={formData.nohp}
                    onChange={(e)=>setFormData({...formData, nohp:e.target.value})}/>
                </div>
                <div className="col-md-4">
                  <input type="datetime-local" className="form-control" value={formData.tanggal}
                    onChange={(e)=>setFormData({...formData, tanggal:e.target.value})}/>
                </div>
              </div>

              <div className="row g-2 mt-2">
                <div className="col-md-6">
                  <select value={formData.jenis} onChange={(e)=>setFormData({...formData, jenis:e.target.value})} className="form-select">
                    <option value="sale">Sale</option>
                    <option value="nf">NF (Non Faktur)</option>
                    <option value="display">Display</option>
                  </select>
                  <span className="helper info">{jenisInfo(formData.jenis)}</span>
                </div>

                <div className="col-md-6">
                  <div className="row g-2">
                    <div className="col-6">
                      <input type="text" className="form-control" placeholder="PO (opsional)" value={formData.po}
                        onChange={(e)=>setFormData({...formData, po:e.target.value})}/>
                    </div>
                    <div className="col-6">
                      <input type="text" className="form-control" placeholder="Keterangan (opsional)" value={formData.keterangan}
                        onChange={(e)=>setFormData({...formData, keterangan:e.target.value})}/>
                    </div>
                  </div>

                  {isInvoiceLike(formData.jenis) && (
                    <div className="row g-2 mt-2">
                      <div className="col-6">
                        <input type="text" className="form-control" placeholder="Nama NPWP (opsional)" value={formData.nama_npwp}
                          onChange={(e)=>setFormData({...formData, nama_npwp:e.target.value})}/>
                      </div>
                      <div className="col-6">
                        <input type="text" className="form-control" placeholder="No NPWP (opsional)" value={formData.npwp}
                          onChange={(e)=>setFormData({...formData, npwp:e.target.value})}/>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>

        </div>

        {/* RIGHT */}
        <div className="d-flex flex-column gap-3" style={{flex:1}}>

          {/* 3) Alamat Pengiriman */}
          <div className="card-soft">
            <div className="card-head">
              <div className="card-title">
                <StepBadge n={3} />
                Alamat Pengiriman (Wajib)
              </div>
            </div>
            <div className="card-body">
              <div className="row g-2">
                <div className="col-md-6">
                  <select className="form-select" value={formData.provinsi} onChange={(e)=>setFormData({...formData, provinsi:e.target.value})}>
                    <option value="">-- Pilih provinsi --</option>
                    <?php foreach ($provinsi as $p) { ?>
                      <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div className="col-md-6">
                  <select className="form-select" value={formData.kabupaten} onChange={(e)=>setFormData({...formData, kabupaten:e.target.value})}>
                    <option value="">-- Pilih kabupaten --</option>
                    {kabupaten.map((k,ind_k)=>(
                      <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="row g-2 mt-2">
                <div className="col-md-6">
                  <select className="form-select" value={formData.kecamatan} onChange={(e)=>setFormData({...formData, kecamatan:e.target.value})}>
                    <option value="">-- Pilih kecamatan --</option>
                    {kecamatan.map((k,ind_k)=>(
                      <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                    ))}
                  </select>
                </div>
                <div className="col-md-6">
                  <select className="form-select" value={formData.kelurahan} onChange={(e)=>setFormData({...formData, kelurahan:e.target.value})}>
                    <option value="">-- Pilih kelurahan --</option>
                    {kelurahan.map((k,ind_k)=>(
                      <option key={ind_k} value={`${k.label}-${k.kodepos}`}>{k.label}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="row g-2 mt-2">
                <div className="col-md-4">
                  <input type="text" className="form-control" placeholder="Kodepos *" value={formData.kodepos}
                    onChange={(e)=>setFormData({...formData, kodepos:e.target.value})}/>
                </div>
                <div className="col-md-8">
                  <input type="text" className="form-control" placeholder="Jalan, no rumah, RT/RW *" value={formData.detail}
                    onChange={(e)=>setFormData({...formData, detail:e.target.value})}/>
                </div>
              </div>
            </div>
          </div>

          {/* 4) Alamat Tagihan */}
          {isInvoiceLike(formData.jenis) && (
            <div className="card-soft">
              <div className="card-head">
                <div className="card-title">
                  <StepBadge n={4} />
                  Alamat Tagihan (Wajib untuk Sale/NF)
                </div>
              </div>
              <div className="card-body">
                <label className="d-flex gap-2 align-items-center mb-2" style={{fontWeight:800}}>
                  <input type="checkbox" onChange={(e)=>setAlamatTagihanSama(e.target.checked)} checked={alamatTagihanSama}/>
                  Samakan dengan alamat pengiriman
                </label>

                {!alamatTagihanSama && (
                  <>
                    <div className="row g-2">
                      <div className="col-md-6">
                        <select className="form-select" value={formData.provinsiTagihan} onChange={(e)=>setFormData({...formData, provinsiTagihan:e.target.value})}>
                          <option value="">-- Pilih provinsi --</option>
                          <?php foreach ($provinsi as $p) { ?>
                            <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div className="col-md-6">
                        <select className="form-select" value={formData.kabupatenTagihan} onChange={(e)=>setFormData({...formData, kabupatenTagihan:e.target.value})}>
                          <option value="">-- Pilih kabupaten --</option>
                          {kabupatenTagihan.map((k,ind_k)=>(
                            <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                          ))}
                        </select>
                      </div>
                    </div>

                    <div className="row g-2 mt-2">
                      <div className="col-md-6">
                        <select className="form-select" value={formData.kecamatanTagihan} onChange={(e)=>setFormData({...formData, kecamatanTagihan:e.target.value})}>
                          <option value="">-- Pilih kecamatan --</option>
                          {kecamatanTagihan.map((k,ind_k)=>(
                            <option key={ind_k} value={`${k.id}-${k.label}`}>{k.label}</option>
                          ))}
                        </select>
                      </div>
                      <div className="col-md-6">
                        <select className="form-select" value={formData.kelurahanTagihan} onChange={(e)=>setFormData({...formData, kelurahanTagihan:e.target.value})}>
                          <option value="">-- Pilih kelurahan --</option>
                          {kelurahanTagihan.map((k,ind_k)=>(
                            <option key={ind_k} value={`${k.label}-${k.kodepos}`}>{k.label}</option>
                          ))}
                        </select>
                      </div>
                    </div>

                    <div className="row g-2 mt-2">
                      <div className="col-md-4">
                        <input type="text" className="form-control" placeholder="Kodepos *" value={formData.kodeposTagihan}
                          onChange={(e)=>setFormData({...formData, kodeposTagihan:e.target.value})}/>
                      </div>
                      <div className="col-md-8">
                        <input type="text" className="form-control" placeholder="Jalan, no rumah, RT/RW *" value={formData.detailTagihan}
                          onChange={(e)=>setFormData({...formData, detailTagihan:e.target.value})}/>
                      </div>
                    </div>
                  </>
                )}

                <span className="helper info">Alamat tagihan dipakai untuk dokumen penagihan (Sale/NF).</span>
              </div>
            </div>
          )}

          {/* 5) Diskon & DP */}
          {isInvoiceLike(formData.jenis) && (
            <div className="card-soft">
              <div className="card-head">
                <div className="card-title">
                  <StepBadge n={5} />
                  Potongan & Down Payment (Opsional)
                </div>
              </div>
              <div className="card-body">
                <div className="row g-2">
                  <div className="col-md-6">
                    <input type="number" value={potongan.nominal}
                      onChange={(e)=>setPotongan({...potongan, nominal:Number(e.target.value)})}
                      className="form-control" placeholder="Potongan (angka)"/>
                  </div>
                  <div className="col-md-6">
                    <select className="form-select" value={potongan.satuan}
                      onChange={(e)=>setPotongan({...potongan, satuan:e.target.value})}>
                      <option value="rupiah">Rupiah</option>
                      <option value="persen">Persen</option>
                    </select>
                  </div>
                </div>

                <div className="row g-2 mt-2">
                  <div className="col-md-6">
                    <input type="number" value={formData.downPayment}
                      onChange={(e)=>setFormData({...formData, downPayment:Number(e.target.value)})}
                      className="form-control" placeholder="Down Payment"/>
                    <span className="helper info">DP otomatis dibatasi 0 sampai Total Akhir.</span>
                  </div>
                  <div className="col-md-6">
                    <div className="input-group">
                      <input type="number" value={potonganHargaSatuan}
                        onChange={(e)=>setPotonganHargaSatuan(Number(e.target.value))}
                        className="form-control"/>
                      <span className="input-group-text">%</span>
                    </div>
                    <span className="helper warn">Potongan harga satuan maksimal <b>25%</b>.</span>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Action */}
          <div className="sticky-action">
            <button
              type="button"
              onClick={()=>{ if(canSave) openPreview(); }}
              className={`btn-default-merah w-100 ${canSave ? '' : 'disabled'}`}
              title={canSave ? 'Lihat preview pesanan' : 'Lengkapi data & item terlebih dahulu'}
            >
              Preview &amp; Buat
            </button>
            {!canSave && (
              <div className="muted" style={{textAlign:'center', marginTop:8}}>
                Syarat minimal: <b>ada item</b>, <b>nama+nohp</b>, <b>alamat kirim lengkap</b>{isInvoiceLike(formData.jenis) ? <>, <b>alamat tagihan</b></> : ''}.
              </div>
            )}
          </div>

        </div>
      </div>
    </>
  );
};

ReactDOM.render(<App />, document.getElementById("container-react"));
</script>

<?= $this->endSection(); ?>