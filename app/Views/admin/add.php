<!-- app/Views/admin/add.php -->
<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
/* ====== Layout & Cards ====== */
.page-wrap {
    padding: 2em;
}

.form-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 24px;
}

@media (max-width: 992px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
    border: 1px solid rgba(0, 0, 0, .06);
}

.card-header {
    padding: 18px 20px;
    border-bottom: 1px solid rgba(0, 0, 0, .06);
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: .2px;
}

.card-body {
    padding: 18px 20px;
}

.section-title {
    margin: 18px 0 10px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #6b7280;
    font-weight: 700;
}

/* ====== Table-ish inputs ====== */
.table-input {
    width: 100%;
    border-collapse: collapse;
}

.table-input td {
    padding: 8px 0;
    vertical-align: top;
}

.table-input td:first-child {
    width: 36%;
    color: #374151;
    font-weight: 500;
    padding-right: 14px;
}

.form-control,
.form-select,
.input-group-text,
.btn-default,
.btn-default-merah,
.btn-teks-aja {
    border-radius: 12px !important;
}

.form-control,
.form-select {
    border: 1px solid #e5e7eb;
    transition: box-shadow .2s, border-color .2s;
}

.form-control:focus,
.form-select:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, .1);
}

.input-group {
    display: flex;
    align-items: stretch;
}

.input-group .form-control {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.input-group .input-group-text {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-left: 0;
    display: inline-flex;
    align-items: center;
    padding: 0 12px;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

/* ====== Buttons ====== */
.btn-row {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-default {
    background: #111827;
    color: #fff;
    border: none;
    padding: 10px 16px;
    font-weight: 600;
    transition: transform .05s ease, opacity .2s ease;
}

.btn-default:hover {
    opacity: .92;
}

.btn-default:active {
    transform: translateY(1px);
}

.btn-default[disabled] {
    opacity: .6;
    cursor: not-allowed;
}

.btn-default-merah {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 8px 12px;
    font-weight: 600;
}

.btn-teks-aja {
    padding: 6px 8px;
    border: none;
    background: transparent;
    color: #ef4444;
    font-weight: 600;
}

.page-title {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: .3px;
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ====== Images & Variants ====== */
.container-gambar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.item-gambar {
    position: relative;
    width: 96px;
    height: 96px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    cursor: pointer;
    transition: transform .15s;
}

.item-gambar:hover {
    transform: translateY(-2px);
}

.item-gambar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.item-gambar p {
    position: absolute;
    top: 6px;
    right: 6px;
    margin: 0;
    background: rgba(17, 24, 39, .85);
    color: #fff;
    font-weight: 700;
    border-radius: 10px;
    padding: 2px 8px;
    font-size: 12px;
    opacity: 0;
    transition: opacity .2s;
}

.item-gambar:hover p {
    opacity: 1;
}

.add-thumb {
    width: 96px;
    height: 96px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px dashed #cbd5e1;
    color: #64748b;
    background: #f8fafc;
    font-weight: 700;
    cursor: pointer;
    transition: border-color .2s, color .2s, transform .15s;
}

.add-thumb:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-2px);
}

.preview-hover {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}

/* ====== Chips ====== */
.chk-row {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.chk {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #fff;
    cursor: pointer;
    user-select: none;
}

.chk input {
    accent-color: #111827;
}

/* ====== Sticky bottom actions ====== */
.bottom-bar {
    position: sticky;
    bottom: 0;
    margin-top: 18px;
    padding: 12px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #fff 30%);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* ====== Loading Overlay ====== */
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, .45);
    backdrop-filter: blur(2px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-overlay.show {
    display: flex;
}

.loading-card {
    background: #111827;
    color: #fff;
    padding: 16px 18px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 220px;
    justify-content: center;
}

.spinner {
    width: 18px;
    height: 18px;
    border: 3px solid rgba(255, 255, 255, .25);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Badge status */
.badge {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 12px;
    border: 1px solid transparent;
}

.badge-aktif {
    background: rgba(16, 185, 129, .12);
    color: #065f46;
    border-color: rgba(16, 185, 129, .35);
}

.badge-non {
    background: rgba(107, 114, 128, .12);
    color: #374151;
    border-color: rgba(107, 114, 128, .35);
}
</style>

<div class="page-wrap">
    <h1 class="page-title"><?= isset($idProduct) ? 'Edit' : 'Tambah' ?> Produk</h1>
    <div id="container-react"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/babel">
    const { useState, useEffect, useRef } = React;

// ==== Data dari PHP (pastikan admin/template sudah load React + Babel UMD) ====
const koleksi = <?= $koleksiJson ?>;
const jenis   = <?= $jenisJson ?>;
const idProduct = '<?= isset($idProduct) ? $idProduct : '' ?>';
const produk   = <?= isset($produkJson) ? $produkJson : 'null' ?>;

// Prefix URL gambar (hanya dipakai saat edit)
const URL_1000_PREFIX = "<?= base_url('img/barang/1000/' . ($produk ? $produk['id'] : '') . '-') ?>";
const URL_3000_PREFIX = "<?= base_url('img/barang/3000/' . ($produk ? $produk['id'] : '') . '-') ?>";
const URL_HOVER       = "<?= base_url('img/barang/hover/' . ($produk ? $produk['id'] : '') . '.webp') ?>";

// === Helper: format waktu untuk input[type=datetime-local] ===
function toInputDateTime(val) {
  if (!val) return '';
  const v = (val + '').trim().replace(' ', 'T');
  const [d, t=''] = v.split('T');
  const [hh='00', mm='00'] = t.split(':');
  return `${d}T${hh.padStart(2,'0')}:${mm.padStart(2,'0')}`;
}

// === Helper: fetch URL -> File (supaya gambar lama ikut terkirim saat edit) ===
async function urlToFile(url, filename, mimeType) {
  const res = await fetch(url);
  const buffer = await res.arrayBuffer();
  return new File([buffer], filename, { type: mimeType });
}

const App = () => {
  const firstRender = useRef(true);
  const [formData, setFormData] = useState({
    id: idProduct || '',
    nama: '',
    harga: '',
    deskripsi: {
      deskripsi: '',
      dimensi: {
        asli:  { panjang:"", lebar:"", tinggi:"", berat:"" },
        paket: { panjang:"", lebar:"", tinggi:"", berat:"" }
      },
      perawatan: ''
    },
    kategori: '',
    subkategori: '',
    diskon: '',
    // Jadwal diskon
    pakai_jadwal_diskon: false,
    diskon_mulai: '',
    diskon_selesai: '',
    varian: [],
    shopee: '',
    tokped: '',
    tiktok: '',
    ruang_tamu: false,
    ruang_keluarga: false,
    ruang_tidur: false
  });
  const [eror, setEror] = useState('');
  const [hoverSrc, setHoverSrc] = useState(null);
  const [hoverFile, setHoverFile] = useState(null);
  const [gambarSrc, setGambarSrc] = useState([]);   // preview base64 per varian
  const [gambarFile, setGambarFile] = useState([]); // File[] per varian
  const [loading, setLoading] = useState(false);
  const idStr = useRef("1-00-000-XX");

  // Loading overlay
  useEffect(() => {
    const id = "global-loading-overlay";
    let overlay = document.getElementById(id);
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = id;
      overlay.className = 'loading-overlay';
      overlay.innerHTML = `
        <div class="loading-card">
          <div class="spinner"></div>
          <div>Sedang menyimpan...</div>
        </div>`;
      document.body.appendChild(overlay);
    }
    overlay.classList.toggle('show', loading);
    document.body.style.cursor = loading ? 'wait' : '';
  }, [loading]);

  // ADD: generate ID berdasarkan kategori/jenis
  useEffect(() => {
    if (idProduct) return;
    let arr = idStr.current.split("-");
    arr[1] = (formData.kategori ?? "").toString().padStart(2, '0');
    idStr.current = arr.join("-");
    setFormData(prev => ({ ...prev, id: arr.join("") }));
  }, [formData.kategori]);

  useEffect(() => {
    if (idProduct) return;
    let arr = idStr.current.split("-");
    arr[2] = (formData.subkategori ?? "").toString().padStart(3, '0');
    idStr.current = arr.join("-");
    setFormData(prev => ({ ...prev, id: arr.join("") }));
  }, [formData.subkategori]);

  // EDIT: preload data & gambar
  useEffect(() => {
    if (!firstRender.current) return;
    firstRender.current = false;

    if (idProduct && produk) {
      // Prefill form
      setFormData(prev => ({
        ...prev,
        id: idProduct,
        nama: produk.nama ?? '',
        harga: produk.harga ?? '',
        deskripsi: produk.deskripsi ?? prev.deskripsi,
        kategori: produk.kategori ?? '',
        subkategori: produk.subkategori ?? '',
        diskon: produk.diskon ?? '',
        // Jadwal
        pakai_jadwal_diskon:
          (produk.pakai_jadwal_diskon === 1 || produk.pakai_jadwal_diskon === '1') ||
          (!!produk.diskon_mulai && !!produk.diskon_selesai),
        diskon_mulai: toInputDateTime(produk.diskon_mulai),
        diskon_selesai: toInputDateTime(produk.diskon_selesai),
        varian: produk.varian ?? [],
        shopee: produk.shopee ?? '',
        tokped: produk.tokped ?? '',
        tiktok: produk.tiktok ?? '',
        ruang_tamu: (produk.ruang_tamu === '1' || produk.ruang_tamu === 1 || produk.ruang_tamu === true),
        ruang_keluarga: (produk.ruang_keluarga === '1' || produk.ruang_keluarga === 1 || produk.ruang_keluarga === true),
        ruang_tidur: (produk.ruang_tidur === '1' || produk.ruang_tidur === 1 || produk.ruang_tidur === true),
      }));

      // Hover preview
      setHoverSrc(URL_HOVER);

      // Preview 1000px untuk tiap varian
      const src = (produk.varian || []).map(v =>
        (v.urutan_gambar || '')
          .split(',')
          .filter(Boolean)
          .map(n => `${URL_1000_PREFIX}${n}.webp`)
      );
      setGambarSrc(src);

      // Konversi 3000px -> File supaya ikut terkirim saat submit
      (async () => {
        const filesPerVarian = [];
        for (let i = 0; i < (produk.varian || []).length; i++) {
          const v = produk.varian[i];
          const nums = (v.urutan_gambar || '').split(',').filter(Boolean);
          const fileList = [];
          for (let j = 0; j < nums.length; j++) {
            const n = nums[j];
            const imageUrl = `${URL_3000_PREFIX}${n}.webp`;
            const f = await urlToFile(imageUrl, `gambar-${i}-${j}.webp`, 'image/webp');
            fileList.push(f);
          }
          filesPerVarian.push(fileList);
        }
        setGambarFile(filesPerVarian);
      })();
    }
  }, []);

  // Set urutan_gambar otomatis saat gambarSrc berubah
  useEffect(() => {
    setFormData(prev => ({
      ...prev,
      varian: (prev.varian || []).map((v, ind_v) => {
        const arr = gambarSrc[ind_v] || [];
        if (ind_v === 0) {
          return { ...v, urutan_gambar: arr.map((_, idx) => idx + 1).join(',') };
        }
        // lanjutkan penomoran setelah varian sebelumnya
        const prevStr = prev.varian[ind_v - 1]?.urutan_gambar || '';
        const last = parseInt(prevStr.split(',').pop() || '0');
        return { ...v, urutan_gambar: arr.map((_, idx) => last + idx + 1).join(',') };
      })
    }));
  }, [gambarSrc]);

  const handleSubmit = () => {
    // Validasi
    if (!formData.nama || !formData.harga) { setEror("Nama dan harga produk wajib diisi."); return; }
    if ((formData.varian || []).length === 0) { setEror("Minimal 1 varian harus ditambahkan."); return; }
    if (!idProduct) {
      for (let i = 0; i < formData.varian.length; i++) {
        if (!gambarFile[i] || gambarFile[i].length === 0) { setEror(`Varian ke-${i + 1} belum memiliki gambar.`); return; }
      }
    }
    if (formData.pakai_jadwal_diskon) {
      if (!formData.diskon_mulai || !formData.diskon_selesai) { setEror("Mohon isi tanggal mulai & selesai jadwal diskon."); return; }
      if (new Date(formData.diskon_mulai) >= new Date(formData.diskon_selesai)) { setEror("Waktu mulai harus lebih awal dari waktu selesai."); return; }
    }

    const form = new FormData();
    form.append("id", formData.id);
    form.append("nama", formData.nama);
    form.append("harga", formData.harga);
    form.append("diskon", formData.diskon || "0");
    form.append("kategori", formData.kategori);
    form.append("subkategori", formData.subkategori);
    form.append("shopee", formData.shopee || "");
    form.append("tokped", formData.tokped || "");
    form.append("tiktok", formData.tiktok || "");
    form.append("ruang_tamu", formData.ruang_tamu ? "1" : "0");
    form.append("ruang_keluarga", formData.ruang_keluarga ? "1" : "0");
    form.append("ruang_tidur", formData.ruang_tidur ? "1" : "0");
    form.append("deskripsi", JSON.stringify(formData.deskripsi || {}));
    form.append("varian", JSON.stringify(formData.varian || []));

    if (hoverFile) form.append("gambar_hover", hoverFile);

    // Flatten semua gambar varian
    const flat = (gambarFile || []).reduce((p, c) => p.concat(c), []);
    if (!idProduct && flat.length === 0) { setEror("Minimal upload satu gambar untuk varian pertama."); return; }
    flat.forEach((file, idx) => { if (file instanceof File) form.append(`gambar_${idx}`, file); });

    // Jadwal Diskon
    form.append("pakai_jadwal_diskon", formData.pakai_jadwal_diskon ? "1" : "0");
    form.append("diskon_mulai", formData.diskon_mulai || "");
    form.append("diskon_selesai", formData.diskon_selesai || "");

    // CSRF
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    form.append(csrfName, csrfHash);

    setLoading(true);
    setEror("");

    (async () => {
      try {
        const url = `<?= rtrim(base_url(), '/'); ?>${idProduct ? `/admin/editproduct/${idProduct}` : `/admin/product`}`;
        const response = await fetch(url, { method: "POST", headers: { Accept: "application/json" }, body: form });
        const rawText = await response.clone().text();
        let result; try { result = JSON.parse(rawText); } catch { result = { pesan: rawText }; }
        setLoading(false);

        if (!response.ok) {
          setEror(result?.pesan || `Error ${response.status}. ${rawText?.slice(0, 500)}`);
          console.error("Response error:", { status: response.status, result });
          return;
        }

        Swal.fire({
          title: "Berhasil!",
          text: `Produk berhasil ${idProduct ? "diubah" : "ditambahkan"}.`,
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => { window.location.href = `<?= base_url('admin/product'); ?>`; });
      } catch (err) {
        console.error("Gagal mengirim data:", err);
        setLoading(false);
        setEror("Gagal menghubungi server.");
      }
    })();
  };

  // Badge status jadwal diskon
  const renderBadgeJadwal = () => {
    if (!formData.pakai_jadwal_diskon || !formData.diskon_mulai || !formData.diskon_selesai) return null;
    const now = new Date();
    const start = new Date(formData.diskon_mulai);
    const end = new Date(formData.diskon_selesai);
    const aktif = now >= start && now <= end;
    return (
      <span className={`badge ${aktif ? 'badge-aktif' : 'badge-non'}`}>
        {aktif ? 'Aktif sekarang' : 'Belum aktif / di luar jadwal'}
      </span>
    );
  };

  return (
    <>
      <div className="form-grid" aria-busy={loading}>
        {/* Kiri */}
        <div className="card">
          <div className="card-header"><h2>Data Produk</h2></div>
          <div className="card-body">
            <table className="table-input w-100">
              <tbody>
                <tr>
                  <td>Nama Produk</td>
                  <td>
                    <input type="text" className="form-control"
                      value={formData.nama}
                      onChange={e => setFormData({ ...formData, nama: e.target.value })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>Harga Produk</td>
                  <td>
                    <input type="number" className="form-control"
                      value={formData.harga}
                      onChange={e => setFormData({ ...formData, harga: e.target.value })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>Diskon Produk</td>
                  <td>
                    <div className="input-group">
                      <input type="number" className="form-control" step="any"
                        value={formData.diskon}
                        onChange={e => setFormData({ ...formData, diskon: e.target.value })}
                        required />
                      <span className="input-group-text">%</span>
                    </div>
                  </td>
                </tr>

                {/* Jadwal Diskon */}
                <tr>
                  <td>Jadwalkan Diskon</td>
                  <td>
                    <label className="chk">
                      <input
                        type="checkbox"
                        checked={formData.pakai_jadwal_diskon}
                        onChange={(e) => setFormData({ ...formData, pakai_jadwal_diskon: e.target.checked })}
                      />
                      Aktifkan jadwal
                    </label>
                    {formData.pakai_jadwal_diskon && (
                      <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:'8px', marginTop:'8px' }}>
                        <div>
                          <small className="section-title" style={{ margin:'0 0 6px' }}>Mulai</small>
                          <input
                            type="datetime-local"
                            className="form-control"
                            value={formData.diskon_mulai}
                            onChange={(e) => setFormData({ ...formData, diskon_mulai: e.target.value })}
                          />
                        </div>
                        <div>
                          <small className="section-title" style={{ margin:'0 0 6px' }}>Selesai</small>
                          <input
                            type="datetime-local"
                            className="form-control"
                            value={formData.diskon_selesai}
                            onChange={(e) => setFormData({ ...formData, diskon_selesai: e.target.value })}
                          />
                        </div>
                        <div style={{ gridColumn:'1 / -1' }}>{renderBadgeJadwal()}</div>
                      </div>
                    )}
                  </td>
                </tr>

                {!idProduct && (
                  <>
                    <tr>
                      <td>Koleksi</td>
                      <td>
                        <select className="form-select" value={formData.kategori}
                          onChange={e => setFormData({ ...formData, kategori: e.target.value })} required>
                          <option value="">-- Pilih koleksi --</option>
                          {koleksi.map((k, i) => <option key={i} value={k.id}>{k.nama}</option>)}
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td>Jenis</td>
                      <td>
                        <select className="form-select" value={formData.subkategori}
                          onChange={e => setFormData({ ...formData, subkategori: e.target.value })} required>
                          <option value="">-- Pilih jenis --</option>
                          {jenis.map((j, i) => <option key={i} value={j.id}>{j.nama}</option>)}
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td>ID Produk</td>
                      <td>
                        <input type="text" className="form-control"
                          value={formData.id}
                          onChange={e => setFormData({ ...formData, id: e.target.value })}
                          required />
                      </td>
                    </tr>
                  </>
                )}

                <tr>
                  <td>Link Shopee</td>
                  <td><input type="text" className="form-control"
                    value={formData.shopee} onChange={e => setFormData({ ...formData, shopee: e.target.value })} /></td>
                </tr>
                <tr>
                  <td>Link Tokopedia</td>
                  <td><input type="text" className="form-control"
                    value={formData.tokped} onChange={e => setFormData({ ...formData, tokped: e.target.value })} /></td>
                </tr>
                <tr>
                  <td>Link Tiktok</td>
                  <td><input type="text" className="form-control"
                    value={formData.tiktok} onChange={e => setFormData({ ...formData, tiktok: e.target.value })} /></td>
                </tr>

                <tr><td colSpan="2" className="section-title">Deskripsi</td></tr>
                <tr>
                  <td>Deskripsi</td>
                  <td>
                    <textarea className="form-control" rows="3"
                      value={formData.deskripsi.deskripsi}
                      onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, deskripsi: e.target.value } })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>Spesifikasi</td>
                  <td>
                    <textarea className="form-control" rows="3"
                      value={formData.deskripsi.perawatan}
                      onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, perawatan: e.target.value } })}
                      required />
                  </td>
                </tr>

                <tr><td colSpan="2" className="section-title">Bentuk Paket</td></tr>
                <tr>
                  <td>Panjang (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.paket.panjang}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, panjang: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Lebar (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.paket.lebar}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, lebar: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Tinggi (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.paket.tinggi}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, tinggi: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Berat (kg)</td>
                  <td><input type="number" step="any" className="form-control"
                    value={formData.deskripsi.dimensi.paket.berat}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, berat: e.target.value } } }
                    })} required /></td>
                </tr>

                <tr><td colSpan="2" className="section-title">Bentuk Asli</td></tr>
                <tr>
                  <td>Panjang (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.asli.panjang}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, panjang: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Lebar (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.asli.lebar}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, lebar: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Tinggi (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.asli.tinggi}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, tinggi: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Berat (kg)</td>
                  <td><input type="number" step="any" className="form-control"
                    value={formData.deskripsi.dimensi.asli.berat}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, berat: e.target.value } } }
                    })} required /></td>
                </tr>

                <tr><td colSpan="2" className="section-title">Ruangan</td></tr>
                <tr>
                  <td>Tag Ruangan</td>
                  <td>
                    <div className="chk-row">
                      <label className="chk"><input type="checkbox" checked={formData.ruang_tamu}
                        onChange={e => setFormData({ ...formData, ruang_tamu: e.target.checked })} />Ruang Tamu</label>
                      <label className="chk"><input type="checkbox" checked={formData.ruang_keluarga}
                        onChange={e => setFormData({ ...formData, ruang_keluarga: e.target.checked })} />Ruang Keluarga</label>
                      <label className="chk"><input type="checkbox" checked={formData.ruang_tidur}
                        onChange={e => setFormData({ ...formData, ruang_tidur: e.target.checked })} />Ruang Tidur</label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div className="bottom-bar">
              <div className="btn-row">
                <button className="btn-default" disabled={loading} onClick={handleSubmit}>
                  <span style={{display:'inline-flex',alignItems:'center',gap:'8px'}}>
                    {loading && <span className="spinner" style={{borderColor:'rgba(255,255,255,.35)', borderTopColor:'#fff'}}></span>}
                    {loading ? 'Menyimpan...' : 'Simpan'}
                  </span>
                </button>
              </div>
            </div>
            {eror && <p style={{ color: '#ef4444', marginTop: '8px', fontWeight: 600 }}>{eror}</p>}
          </div>
        </div>

        {/* Kanan */}
        <div className="card">
          <div className="card-header"><h2>Media & Varian</h2></div>
          <div className="card-body">
            <div className="section-title">Gambar Hover</div>
            <img className="preview-hover" src={hoverSrc || "/img/nopic.jpg"} alt="preview hover" />
            <div style={{ margin: '10px 0 20px' }}>
              <input
                onChange={(e) => {
                  const file = e.target.files[0];
                  if (file) {
                    setHoverFile(file);
                    const reader = new FileReader();
                    reader.onload = () => setHoverSrc(reader.result);
                    reader.readAsDataURL(file);
                  } else { setHoverFile(null); }
                }}
                name="gambar_hover" type="file" className="form-control" />
            </div>

            <div className="section-title">Varian</div>
            <div id="container-varian" style={{ display:'flex', flexDirection:'column', gap:'16px' }}>
              {(formData.varian || []).map((v, ind_v) => (
                <div key={ind_v} className="card" style={{ borderRadius:'12px' }}>
                  <div className="card-body">
                    <div className="container-gambar">
                      {gambarSrc.length > 0 && (gambarSrc[ind_v] || []).map((g, ind_g) => (
                        <div className="item-gambar" key={ind_g} title="Klik untuk hapus"
                          onClick={() => {
                            setGambarSrc(gambarSrc.map((gg, i) => i === ind_v ? gg.filter((_, j) => j !== ind_g) : gg));
                            setGambarFile(gambarFile.map((gg, i) => i === ind_v ? gg.filter((_, j) => j !== ind_g) : gg));
                          }}>
                          <p>✕</p>
                          <img src={g || "/img/nopic.jpg"} alt="" />
                        </div>
                      ))}
                      <div>
                        <input type="file" id={`file-${ind_v}`} style={{ display:'none' }}
                          onChange={(e) => {
                            const file = e.target.files[0];
                            if (file) {
                              const reader = new FileReader();
                              reader.onload = () => {
                                const newSrc  = [...gambarSrc];
                                const newFile = [...gambarFile];
                                if (!newSrc[ind_v])  newSrc[ind_v]  = [];
                                if (!newFile[ind_v]) newFile[ind_v] = [];
                                newSrc[ind_v].push(reader.result);
                                newFile[ind_v].push(file);
                                setGambarSrc(newSrc);
                                setGambarFile(newFile);
                              };
                              reader.readAsDataURL(file);
                            }
                          }} />
                        <label htmlFor={`file-${ind_v}`} className="add-thumb">+</label>
                      </div>
                    </div>

                    <table className="table-input w-100" style={{ marginTop:'12px' }}>
                      <tbody>
                        <tr>
                          <td>Nama</td>
                          <td>
                            <input className="form-control" value={v.nama}
                              onChange={e => setFormData({
                                ...formData,
                                varian: formData.varian.map((vv, i) => i === ind_v ? { ...vv, nama: e.target.value } : vv)
                              })} required />
                          </td>
                        </tr>
                        <tr>
                          <td>Kode Warna</td>
                          <td>
                            <input className="form-control" value={v.kode}
                              onChange={e => setFormData({
                                ...formData,
                                varian: formData.varian.map((vv, i) => i === ind_v ? { ...vv, kode: e.target.value } : vv)
                              })} required />
                          </td>
                        </tr>
                        <tr>
                          <td>Stok</td>
                          <td>
                            <input className="form-control" value={v.stok}
                              onChange={e => setFormData({
                                ...formData,
                                varian: formData.varian.map((vv, i) => i === ind_v ? { ...vv, stok: e.target.value } : vv)
                              })} required />
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <div className="btn-row" style={{ marginTop:'8px' }}>
                      <button type="button" className="btn-teks-aja"
                        onClick={() => {
                          setFormData({ ...formData, varian: formData.varian.filter((_, i) => i !== ind_v) });
                          setGambarFile(gambarFile.filter((_, i) => i !== ind_v));
                          setGambarSrc(gambarSrc.filter((_, i) => i !== ind_v));
                        }}>
                        Hapus Varian
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            <button className="btn-default-merah" type="button" style={{ marginTop:'12px' }}
              onClick={() => {
                setFormData({
                  ...formData,
                  varian: [...(formData.varian || []), { nama: "", kode: "", stok: "", urutan_gambar: "" }]
                });
                setGambarFile([...(gambarFile || []), []]);
                setGambarSrc([...(gambarSrc || []), []]);
              }}>
              Tambah Varian
            </button>
          </div>
        </div>
      </div>

      <input type="text" name="hitung-varian" style={{ display: 'none' }} defaultValue={1} />
    </>
  );
};

ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>