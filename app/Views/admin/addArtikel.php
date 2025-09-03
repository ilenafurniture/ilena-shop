<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<style>
:root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1f2937;
    --brand: #b31217;
    --ok: #16a34a;
    --warn: #b45309;
    --danger: #b91c1c;
}

/* ===== Layout umum ===== */
.artikel {
    padding: 2rem;
}

.split {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
}

.split>form {
    flex: 1;
}

.split>.side {
    width: 360px;
    max-width: 40vw;
    position: sticky;
    top: 16px;
}

/* ===== Tabel form ===== */
.table-input td {
    vertical-align: top;
    padding: 8px 8px;
}

.table-input td:first-child {
    width: 160px;
    color: var(--slate-600);
    font-weight: 600;
}

.baris {
    display: flex;
    gap: .5rem;
    align-items: center;
}

.form-control,
.form-select {
    border-radius: 10px;
    border: 1px solid var(--slate-300);
}

.form-control:focus,
.form-select:focus {
    border-color: var(--slate-400);
    box-shadow: 0 0 0 3px rgba(148, 163, 184, .25);
}

/* ===== Header image preview ===== */
.header-preview {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 8px;
}

.header-preview .thumb {
    width: 84px;
    height: 84px;
    border-radius: 12px;
    border: 1px solid var(--slate-200);
    background: #fff center/cover no-repeat;
}

.header-preview .status {
    font-size: 12px;
    color: var(--slate-600);
}

.status.ok {
    color: var(--ok);
    font-weight: 700
}

.status.bad {
    color: var(--danger);
    font-weight: 700
}

/* ===== Tombol ===== */
.btn-default {
    background: #111827;
    color: #fff;
    border: 0;
    border-radius: 10px;
    padding: .75rem 1rem;
}

.btn-default-merah {
    background: linear-gradient(180deg, var(--brand), #a50e12);
    color: #fff;
    border: 0;
    border-radius: 10px;
    padding: .65rem 1rem;
    font-weight: 700;
}

.btn-default:hover,
.btn-default-merah:hover {
    filter: brightness(.98);
}

/* ===== Modal Galeri ===== */
#modal-galeri {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .45);
    backdrop-filter: blur(2px);
    z-index: 5;
}

.container-galeri {
    width: min(1100px, 92vw);
    height: min(92vh, 860px);
    background: #fff;
    border-radius: 16px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
}

.galeri-toolbar {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.galeri {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
}

@media (max-width:1600px) {
    .galeri {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (max-width:1300px) {
    .galeri {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width:960px) {
    .galeri {
        grid-template-columns: repeat(3, 1fr);
    }
}

.galeri .item-galeri {
    aspect-ratio: 1/1;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid var(--slate-200);
    cursor: pointer;
    position: relative;
    background: #f8fafc;
    transition: border .15s, transform .08s;
}

.galeri .item-galeri:hover {
    border-color: var(--brand);
    transform: translateY(-1px);
}

.galeri .item-galeri img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.copied-badge {
    position: absolute;
    right: 8px;
    top: 8px;
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 999px;
    background: #ecfeff;
    color: #075985;
    border: 1px solid #a5f3fc;
    display: none;
}

/* ===== Dropzone upload ===== */
.dropzone {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    border: 1.5px dashed var(--slate-300);
    border-radius: 12px;
    padding: 10px 12px;
    transition: all .15s;
    background: #fff;
}

.dropzone.dragover {
    border-color: var(--brand);
    background: #fff5f5;
}

.dz-ico {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #f3f4f6;
    display: grid;
    place-items: center;
    color: #111827;
}

.upload-meta {
    font-size: 12px;
    color: var(--slate-600)
}

.progress-line {
    height: 6px;
    width: 100%;
    background: var(--slate-100);
    border-radius: 999px;
    overflow: hidden;
}

.progress-line>span {
    display: block;
    height: 100%;
    background: linear-gradient(90deg, var(--brand), #e11d48);
    width: 0%;
}

/* ===== SEO panel ===== */
.seo-card {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: 14px;
    padding: 14px;
    box-shadow: 0 10px 26px rgba(0, 0, 0, .04);
}

.seo-card h2 {
    margin-top: 0;
    font-size: 18px;
}

.seo-row {
    margin: .35rem 0;
}

.seo-row strong {
    color: #0f172a
}

.seo-info {
    font-weight: 600;
}

.text-success {
    color: var(--ok) !important;
}

.text-danger {
    color: var(--danger) !important;
}
</style>

<script src="https://cdn.tiny.cloud/1/<?= $tinyMCE; ?>/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- MODAL GALERI -->
<div id="modal-galeri" class="d-none justify-content-center align-items-center">
    <div class="container-galeri">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin:0 0 2px 0;">Galeri</h2>
                <p class="m-0 text-secondary">Klik gambar untuk menyalin URL gambar</p>
            </div>
            <button onclick="closeModalGaleri()" class="btn btn-outline-dark">Tutup</button>
        </div>
        <hr class="my-2">
        <div class="galeri-toolbar">
            <div id="dropzone" class="dropzone">
                <div class="dz-ico"><i class="material-icons">file_upload</i></div>
                <div style="flex:1">
                    <div class="d-flex gap-2 flex-wrap">
                        <input type="file" class="form-control" id="input-add-galeri" style="max-width:260px;">
                        <button onclick="submitAddGaleri()" class="btn-default-merah">Upload</button>
                    </div>
                    <div class="upload-meta mt-1">Drag & drop file ke area ini atau pilih file di atas.</div>
                    <div class="progress-line mt-2" style="display:none;"><span id="upload-progress"></span></div>
                </div>
            </div>
        </div>
        <hr class="my-2">
        <div style="flex:1; overflow:auto;" class="pe-1">
            <div class="galeri gap-1" id="galeri-grid">
                <?php foreach ($galeri as $g) { ?>
                <div class="item-galeri" onclick="copytext('<?= base_url($g['url']); ?>', 'Url telah di salin', this)">
                    <span class="copied-badge">Copied</span>
                    <img loading="lazy" src="<?= base_url($g['url']); ?>" alt="">
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- FORM + SEO -->
<div class="artikel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-3">Tambah Artikel</h1>
        <button type="button" class="btn-default-merah" onclick="openModalGaleri()">Galeri</button>
    </div>

    <div class="split">
        <form method="post" action="/admin/addarticle">
            <div class="container seo-card">
                <?= csrf_field(); ?>
                <table class="table-input w-100">
                    <tbody>
                        <tr>
                            <td>Judul</td>
                            <td>
                                <div class="baris">
                                    <input type="text" class="form-control" name="judul" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Penulis</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="penulis" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>
                                <div class="baris">
                                    <select name="kategori" class="form-select">
                                        <option selected value="edukasi">Edukasi</option>
                                        <option value="tips & trik">Tips & trick</option>
                                        <option value="fun fact">Fun Fact</option>
                                        <option value="rekomendasi">Rekomendasi</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Ubah</td>
                            <td>
                                <div class="baris"><input type="datetime-local" class="form-control" name="waktu"
                                        required></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Gambar Header</td>
                            <td>
                                <div class="baris" style="flex:1; flex-direction:column; align-items:flex-start;">
                                    <input type="text" class="form-control" name="header" required
                                        placeholder="Masukan URL">
                                    <div class="header-preview">
                                        <div id="header-thumb" class="thumb"></div>
                                        <div id="header-status" class="status">Preview akan muncul di sini</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Keywords</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="keywords" required
                                        placeholder="Pisahkan dengan koma"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Deskripsi</td>
                            <td>
                                <div class="baris" style="width:100%;">
                                    <textarea name="deskripsi" class="form-control" required rows="4"
                                        style="width:100%;"></textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="container mt-3 seo-card">
                <h5 class="mb-2" style="font-weight:800; letter-spacing:-.2px;">Isi Artikel</h5>
                <textarea name="isi" id="content"></textarea>
            </div>

            <button type="submit" class="btn-default mt-3 w-100">Buat</button>
        </form>

        <div class="side">
            <div class="seo-card">
                <h2>SEO Check</h2>
                <div class="seo-row"><strong>Panjang Title:</strong> <span class="text-danger seo-info">0 karakter
                        (Kurang Optimal)</span></div>
                <div class="seo-row"><strong>Panjang Meta Description:</strong> <span class="text-danger seo-info">0
                        karakter (Kurang Optimal)</span></div>
                <div class="seo-row"><strong>Keyword Density:</strong> <span class="text-danger seo-info">(Kurang
                        Optimal)</span></div>
                <div id="seo-container-keywords" class="seo-row" style="margin-left:.25rem;">
                    <span class="text-danger mini">Masukkan keywords untuk melihat rincian</span>
                </div>
                <div class="seo-row"><strong>Gambar dengan ALT:</strong> <span class="text-danger seo-info">0 dari 0
                        gambar (Kurang Optimal)</span></div>
                <div class="seo-row"><strong>Jumlah Link:</strong> <span class="text-danger seo-info">Internal: 0,
                        External: 0 (Kurang Optimal)</span></div>
                <div class="seo-row"><strong>Struktur Heading:</strong> <span class="text-danger seo-info">(Kurang
                        Optimal)</span></div>
                <div class="seo-row"><strong>Readability Score:</strong> <span class="text-danger seo-info">0 (Sulit
                        Dibaca)</span></div>
            </div>
        </div>
    </div>
</div>

<script>
/* ===== Refs ===== */
const inputJudulElm = document.querySelector('input[name="judul"]');
const inputDeskripsiElm = document.querySelector('textarea[name="deskripsi"]');
const inputKeywordsElm = document.querySelector('input[name="keywords"]');
const inputHeaderElm = document.querySelector('input[name="header"]');
const headerThumbElm = document.getElementById('header-thumb');
const headerStatusElm = document.getElementById('header-status');

const modalGaleriElm = document.getElementById('modal-galeri');
const galeriGridElm = document.getElementById('galeri-grid');

let contentArtikel = '';
const [seoTitleElm, seoDescriptionElm, seoKeywordsElm, seoGambarElm, seoLinkElm, seoHeadingElm, seoReadibilityElm] =
document.querySelectorAll('.seo-info');
const seoContainerKeywordsElm = document.getElementById('seo-container-keywords');

/* ===== Helpers (toast fallback untuk copytext) ===== */
function fallbackToast(msg) {
    if (typeof callNotif === 'function') {
        callNotif(msg);
        return;
    }
    // fallback minimal
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText =
        'position:fixed;left:50%;top:20px;transform:translateX(-50%);background:#111827;color:#fff;padding:8px 12px;border-radius:10px;z-index:99999;box-shadow:0 8px 24px rgba(0,0,0,.25);font-size:13px';
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 1600);
}

function showCopiedBadge(el) {
    const badge = el.querySelector('.copied-badge');
    if (!badge) return;
    badge.style.display = 'inline-flex';
    badge.style.opacity = '1';
    setTimeout(() => {
        badge.style.opacity = '0';
        setTimeout(() => badge.style.display = 'none', 200);
    }, 900);
}

/* ===== Upload galeri ===== */
function submitAddGaleri() {
    const galeriElm = galeriGridElm;
    const inputFile = document.getElementById('input-add-galeri');
    if (inputFile.files.length === 0) {
        alert('Pilih file terlebih dahulu!');
        return;
    }
    const dzProgressWrap = document.querySelector('.progress-line');
    const dzBar = document.getElementById('upload-progress');
    dzProgressWrap.style.display = '';

    const formData = new FormData();
    formData.append('file', inputFile.files[0]);
    fetch('/admin/addgaleriarticle', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const base_url = window.location.origin;
            galeriElm.innerHTML = galeriElm.innerHTML + `
          <div class="item-galeri" onclick="copytext('${base_url}/${data.url}', 'Url telah di salin', this)">
            <span class="copied-badge">Copied</span>
            <img loading="lazy" src="${base_url}/${data.url}" alt="">
          </div>`;
            inputFile.value = "";
            dzBar.style.width = '0%';
            dzProgressWrap.style.display = 'none';
            fallbackToast('Galeri ditambahkan');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan, coba lagi nanti!');
            dzBar.style.width = '0%';
            dzProgressWrap.style.display = 'none';
        });
}

// Drag & drop area
(function() {
    const dz = document.getElementById('dropzone');
    const inputFile = document.getElementById('input-add-galeri');
    const dzBar = document.getElementById('upload-progress');
    const dzProgressWrap = document.querySelector('.progress-line');

    if (!dz) return;

    ['dragenter', 'dragover'].forEach(ev => dz.addEventListener(ev, e => {
        e.preventDefault();
        e.stopPropagation();
        dz.classList.add('dragover');
    }));
    ['dragleave', 'drop'].forEach(ev => dz.addEventListener(ev, e => {
        e.preventDefault();
        e.stopPropagation();
        dz.classList.remove('dragover');
    }));

    dz.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (!files || !files[0]) return;
        inputFile.files = files;
        // auto upload
        dzProgressWrap.style.display = '';
        dzBar.style.width = '35%';
        submitAddGaleri();
    });
})();

/* ===== Header URL live preview ===== */
function updateHeaderPreview(url) {
    if (!url) {
        headerThumbElm.style.backgroundImage = 'none';
        headerStatusElm.textContent = 'Preview akan muncul di sini';
        headerStatusElm.className = 'status';
        return;
    }
    // cek valid url sederhana
    const ok = /^(https?:)?\/\//i.test(url);
    if (!ok) {
        headerThumbElm.style.backgroundImage = 'none';
        headerStatusElm.textContent = 'URL tidak valid';
        headerStatusElm.className = 'status bad';
        return;
    }
    const img = new Image();
    img.onload = function() {
        headerThumbElm.style.backgroundImage = `url('${url.replace(/'/g,"\\'")}')`;
        headerStatusElm.textContent = `${img.width}×${img.height} • OK`;
        headerStatusElm.className = 'status ok';
    }
    img.onerror = function() {
        headerThumbElm.style.backgroundImage = 'none';
        headerStatusElm.textContent = 'Gagal memuat gambar';
        headerStatusElm.className = 'status bad';
    }
    img.src = url;
}

inputHeaderElm.addEventListener('input', e => updateHeaderPreview(e.target.value));

/* ===== Modal Galeri open/close ===== */
function openModalGaleri() {
    modalGaleriElm.classList.remove('d-none');
    modalGaleriElm.classList.add('d-flex');
}

function closeModalGaleri() {
    modalGaleriElm.classList.add('d-none');
    modalGaleriElm.classList.remove('d-flex');
}
// klik overlay untuk tutup
modalGaleriElm.addEventListener('click', (e) => {
    if (e.target === modalGaleriElm) closeModalGaleri();
});

/* ===== SEO Helpers ===== */
function checkTitleMeta(title, description) {
    const titleLength = title.length;
    const descLength = description.length;
    const data = {
        title_length: titleLength,
        description_length: descLength,
        title_status: (titleLength < 50) ? 'Kurang Optimal' : ((titleLength > 60) ? 'Berlebihan' : 'Optimal'),
        description_status: (descLength < 120) ? 'Kurang Optimal' : ((descLength > 160) ? 'Berlebihan' : 'Optimal'),
    }
    if (data.title_status == 'Optimal') {
        seoTitleElm.classList.remove('text-danger');
        seoTitleElm.classList.add('text-success')
    } else {
        seoTitleElm.classList.add('text-danger');
        seoTitleElm.classList.remove('text-success')
    }
    if (data.description_status == 'Optimal') {
        seoDescriptionElm.classList.remove('text-danger');
        seoDescriptionElm.classList.add('text-success')
    } else {
        seoDescriptionElm.classList.add('text-danger');
        seoDescriptionElm.classList.remove('text-success')
    }
    seoTitleElm.innerHTML = `${data.title_length} karakter (${data.title_status})`;
    seoDescriptionElm.innerHTML = `${data.description_length} karakter (${data.description_status})`
}

function checkKeywordDensity(content, keywords) {
    const textContent = content.replace(/<[^>]*>/g, '');
    const totalWords = textContent.trim() ? textContent.split(/\s+/).length : 0;
    const keywordArray = keywords.toLowerCase().split(',').map(k => k.trim()).filter(Boolean);

    let keywordCounts = {};
    let keywordPercentages = {};
    let totalKeywordCount = 0;

    keywordArray.forEach(keyword => {
        const regex = new RegExp(`\\b${keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`, 'gi');
        const count = (textContent.match(regex) || []).length;
        keywordCounts[keyword] = count;
        totalKeywordCount += count;
    });

    Object.keys(keywordCounts).forEach(keyword => {
        keywordPercentages[keyword] = (totalWords > 0) ? ((keywordCounts[keyword] / totalWords) * 100).toFixed(
            2) : "0.00";
    });

    const totalDensity = (totalWords > 0) ? (totalKeywordCount / totalWords) * 100 : 0;
    const data = {
        total_words: totalWords,
        keyword_counts: keywordCounts,
        keyword_percentages: keywordPercentages,
        total_density: totalDensity.toFixed(2),
        status: (totalDensity < 1) ? 'Kurang Optimal' : ((totalDensity > 3) ? 'Berlebihan' : 'Optimal'),
    };

    seoContainerKeywordsElm.innerHTML = '';
    if (keywordArray.length === 0) {
        seoContainerKeywordsElm.innerHTML =
            '<span class="text-danger mini">Masukkan keywords untuk melihat rincian</span>';
    } else {
        Object.entries(data.keyword_counts).forEach(([keyword, value]) => {
            seoContainerKeywordsElm.innerHTML += `
          <div class="mb-1">- <strong>${keyword}:</strong> <span class="${(Number(data.keyword_percentages[keyword])>=1 && Number(data.keyword_percentages[keyword])<=3)?'text-success':'text-danger'}">${Number(data.keyword_percentages[keyword])}%</span></div>
        `;
        });
    }

    if (data.status == 'Optimal') {
        seoKeywordsElm.classList.remove('text-danger');
        seoKeywordsElm.classList.add('text-success')
    } else {
        seoKeywordsElm.classList.add('text-danger');
        seoKeywordsElm.classList.remove('text-success')
    }
    seoKeywordsElm.innerHTML = `${data.total_density}% (${data.status})`
}

function checkImageAltTags(content) {
    const imgTags = content.match(/<img[^>]*>/g) || [];
    const imagesWithAlt = content.match(/<img[^>]+alt=["'][^"']+["']/g) || [];
    const data = {
        total_images: imgTags.length,
        images_with_alt: imagesWithAlt.length,
        status: (imgTags.length === 0) ? 'Kurang Optimal' : ((imagesWithAlt.length === imgTags.length) ? 'Optimal' :
            'Berlebihan'),
    };
    if (data.status == 'Optimal') {
        seoGambarElm.classList.remove('text-danger');
        seoGambarElm.classList.add('text-success')
    } else {
        seoGambarElm.classList.add('text-danger');
        seoGambarElm.classList.remove('text-success')
    }
    seoGambarElm.innerHTML = `${data.images_with_alt} dari ${data.total_images} gambar (${data.status})`
}

function checkInternalExternalLinks(content, baseUrl) {
    const links = content.match(/<a[^>]+href=["'](.*?)["']/g) || [];
    let internalLinks = 0;
    let externalLinks = 0;

    links.forEach(link => {
        const urlMatch = link.match(/href=["'](.*?)["']/);
        if (urlMatch) {
            const url = urlMatch[1];
            if (url.startsWith(baseUrl) || url.startsWith('/')) internalLinks++;
            else externalLinks++;
        }
    });

    const data = {
        total_links: links.length,
        internal_links: internalLinks,
        external_links: externalLinks,
        status: (links.length === 0) ? 'Kurang Optimal' : ((internalLinks > 0 && externalLinks > 0) ? 'Optimal' :
            'Berlebihan'),
    };
    if (data.status == 'Optimal') {
        seoLinkElm.classList.remove('text-danger');
        seoLinkElm.classList.add('text-success')
    } else {
        seoLinkElm.classList.add('text-danger');
        seoLinkElm.classList.remove('text-success')
    }
    seoLinkElm.innerHTML = `Internal: ${data.internal_links}, External: ${data.external_links} (${data.status})`
}

function checkHeadingStructure(content) {
    const headingTags = content.match(/<h[1-6][^>]*>(.*?)<\/h[1-6]>/g) || [];
    let headingCounts = {};
    headingTags.forEach(tag => {
        const level = tag.match(/<h([1-6])/)[1];
        headingCounts[level] = (headingCounts[level] || 0) + 1;
    });
    const data = {
        headings: headingCounts,
        status: ((headingCounts['1'] || 0) === 0) ? 'Kurang Optimal' : (((headingCounts['1'] || 0) > 1) ?
            'Berlebihan' : 'Optimal'),
    };
    if (data.status == 'Optimal') {
        seoHeadingElm.classList.remove('text-danger');
        seoHeadingElm.classList.add('text-success')
    } else {
        seoHeadingElm.classList.add('text-danger');
        seoHeadingElm.classList.remove('text-success')
    }
    seoHeadingElm.innerHTML = `(${data.status})`
}

function checkReadabilityScore(content) {
    const textContent = content.replace(/<[^>]*>/g, '').trim();
    const sentencesArr = textContent.split(/[.!?]+/).filter(s => s.trim().length > 0);
    const sentences = sentencesArr.length || 1;
    const words = textContent ? textContent.split(/\s+/).length : 0;
    const syllables = (textContent.match(/[aeiouy]{1,2}/gi) || []).length;

    const fleschScore = 206.835 - (1.015 * (words / sentences)) - (84.6 * (syllables / Math.max(1, words)));

    const data = {
        score: (isFinite(fleschScore) ? fleschScore : 0).toFixed(2),
        status: (fleschScore >= 60) ? 'Mudah Dibaca' : 'Sulit Dibaca',
    };
    if (data.status == 'Mudah Dibaca') {
        seoReadibilityElm.classList.remove('text-danger');
        seoReadibilityElm.classList.add('text-success')
    } else {
        seoReadibilityElm.classList.add('text-danger');
        seoReadibilityElm.classList.remove('text-success')
    }
    seoReadibilityElm.innerHTML = `${data.score} (${data.status})`
}

/* ===== Event listeners ===== */
inputJudulElm.addEventListener('input', (e) => {
    checkTitleMeta(e.target.value, inputDeskripsiElm.value);
});
inputDeskripsiElm.addEventListener('input', (e) => {
    checkTitleMeta(inputJudulElm.value, e.target.value);
});
inputKeywordsElm.addEventListener('input', (e) => {
    checkKeywordDensity(contentArtikel, e.target.value);
});

/* ===== TinyMCE ===== */
tinymce.init({
    selector: "#content",
    setup: function(editor) {
        editor.on('input', function() {
            contentArtikel = editor.getContent()
            checkKeywordDensity(contentArtikel, inputKeywordsElm.value);
            checkImageAltTags(contentArtikel);
            checkInternalExternalLinks(contentArtikel, "<?= base_url(); ?>");
            checkHeadingStructure(contentArtikel);
            checkReadabilityScore(contentArtikel);
        });
    },
    convert_urls: true,
    relative_urls: false,
    remove_script_host: false,
    plugins: ["image", "link", "lists", "media", "table"],
    menubar: "edit insert format table",
    toolbar: "undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table",
});

/* ===== copytext enhancement (badge) ===== */
// Jika sudah ada copytext global, kita hanya menambahkan badge handler via argumen el
window.copytext = window.copytext || function(txt, msg, el) {
    navigator.clipboard.writeText(txt).then(() => {
        showCopiedBadge(el || document.body);
        fallbackToast(msg || 'Disalin');
    });
};
</script>

<?= $this->endSection(); ?>