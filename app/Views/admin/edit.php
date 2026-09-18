<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<?php $imageVersion = !empty($produk['tgl_update']) ? strtotime((string) $produk['tgl_update']) : time(); ?>

<style>
:root {
    --bg: #0f1115;
    --card: #161a22;
    --muted: #8b95a7;
    --text: #e7e8ea;
    --line: #222838;
    --brand: #6aa9ff;
    --danger: #ff6b6b;
}

body {
    background: var(--bg);
    color: var(--text);
}

.page {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.title {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: .2px;
    margin-bottom: 1.25rem;
}

.grid {
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 1.25rem;
}

@media (max-width: 980px) {
    .grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 16px;
}

.card+.card {
    margin-top: 12px;
}

.section-title {
    font-size: .95rem;
    font-weight: 600;
    color: #cfd6e4;
    margin-bottom: .75rem;
    letter-spacing: .3px;
}

.row {
    display: flex;
    gap: 12px;
    align-items: center;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 12px;
}

.label {
    font-size: .85rem;
    color: var(--muted);
}

.inp,
.sel,
.ta {
    width: 100%;
    background: #0d1016;
    color: var(--text);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: .65rem .8rem;
    outline: none;
}

.inp:focus,
.sel:focus,
.ta:focus {
    border-color: #2c3650;
    box-shadow: 0 0 0 3px rgba(106, 169, 255, .15);
}

.ta {
    min-height: 110px;
    resize: vertical;
}

.input-group {
    display: flex;
}

.input-group span {
    display: inline-flex;
    align-items: center;
    padding: 0 .6rem;
    border: 1px solid var(--line);
    border-left: 0;
    border-radius: 0 10px 10px 0;
    background: #0d1016;
    color: var(--muted);
}

.btn {
    appearance: none;
    border: 1px solid var(--line);
    background: #0d1016;
    color: #dfe7ff;
    padding: .6rem .9rem;
    border-radius: 10px;
    cursor: pointer;
    transition: .15s ease;
}

.btn:hover {
    border-color: #2a3550;
    transform: translateY(-1px);
}

.btn.primary {
    background: linear-gradient(180deg, #6aa9ff, #4f8be0);
    border-color: #4f8be0;
    color: #0b1220;
    font-weight: 600;
}

.btn.ghost {
    background: transparent;
}

.btn.danger {
    background: transparent;
    color: var(--danger);
    border-color: #402326;
}

.muted {
    color: var(--muted);
    font-size: .85rem;
}

.hr {
    height: 1px;
    background: var(--line);
    margin: 10px 0 14px;
    border: 0;
}

.switches {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: .45rem .7rem;
    border-radius: 999px;
    background: #0d1016;
    border: 1px solid var(--line);
}

.img-preview {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid var(--line);
    background: #0d1016;
}

.uploader {
    display: flex;
    gap: 10px;
    align-items: center;
}

.uploader input[type=file] {
    display: none;
}

.uploader label {
    cursor: pointer;
}

.stack {
    display: grid;
    gap: 10px;
}

.list-images {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.thumb {
    width: 88px;
    height: 88px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--line);
    position: relative;
    background: #0d1016;
}

.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumb .x {
    position: absolute;
    top: 4px;
    right: 6px;
    background: #ff6b6b;
    color: #0b0d14;
    border-radius: 6px;
    font-size: .75rem;
    padding: 2px 6px;
    cursor: pointer;
}

.two {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

@media (max-width:700px) {
    .two {
        grid-template-columns: 1fr;
    }
}

.footer-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    position: sticky;
    bottom: 0;
    padding-top: 10px;
    margin-top: 10px;
    background: linear-gradient(180deg, transparent, rgba(15, 17, 21, .6) 30%, var(--bg) 70%);
}

.hint {
    font-size: .8rem;
    color: #9aa6bd;
}
</style>

<div class="page">
    <div class="title">Anjayy Produk</div>

    <form method="post" id="product-edit-form"
        action="/admin/editproduct/<?= $produk['id']; ?><?= isset($_GET['sblm']) ? '/' . $_GET['sblm'] : ''; ?>"
        enctype="multipart/form-data">
        <?= csrf_field(); ?>

        <div class="grid">
            <!-- LEFT -->
            <div class="stack">
                <div class="card">
                    <div class="section-title">Informasi Utama</div>
                    <div class="field">
                        <label class="label">Nama Produk</label>
                        <input class="inp" type="text" name="nama" required value="<?= $produk['nama']; ?>">
                    </div>

                    <div class="two">
                        <div class="field">
                            <label class="label">Harga</label>
                            <input class="inp" type="number" name="harga" required value="<?= $produk['harga']; ?>">
                        </div>
                        <div class="field">
                            <label class="label">Diskon</label>
                            <div class="input-group">
                                <input class="inp" type="number" name="diskon" step="any" required
                                    value="<?= $produk['diskon']; ?>">
                                <span>%</span>
                            </div>
                            <div class="hint">Kosongkan atau 0 jika tidak ada diskon.</div>
                        </div>
                    </div>

                    <div class="two">
                        <div class="field">
                            <label class="label">Koleksi</label>
                            <select class="sel" name="kategori" onchange="generateId(event)">
                                <?php foreach ($koleksi as $k) { ?>
                                <option value="<?= $k['id']; ?>"
                                    <?= $k['nama'] == $produk['kategori'] ? 'selected' : ''; ?>>
                                    <?= $k['nama']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="field">
                            <label class="label">Jenis</label>
                            <select class="sel" name="subkategori" onchange="generateId(event)">
                                <?php foreach ($jenis as $k) { ?>
                                <option value="<?= $k['id']; ?>"
                                    <?= $k['nama'] == $produk['subkategori'] ? 'selected' : ''; ?>>
                                    <?= $k['nama']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <input type="text" name="id" value="<?= $produk['id']; ?>" hidden>

                    <div class="two">
                        <div class="field">
                            <label class="label">Link Shopee</label>
                            <input class="inp" type="text" name="shopee" value="<?= $produk['shopee']; ?>">
                        </div>
                        <div class="field">
                            <label class="label">Link Tokopedia</label>
                            <input class="inp" type="text" name="tokped" value="<?= $produk['tokped']; ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Link Tiktok</label>
                        <input class="inp" type="text" name="tiktok" value="<?= $produk['tiktok']; ?>">
                    </div>
                </div>

                <div class="card">
                    <div class="section-title">Deskripsi & Spesifikasi</div>
                    <div class="field">
                        <label class="label">Deskripsi</label>
                        <textarea class="ta" name="deskripsi_teks"
                            required><?= $produk['deskripsi']['deskripsi']; ?></textarea>
                    </div>
                    <div class="field">
                        <label class="label">Spesifikasi / Perawatan</label>
                        <textarea class="ta" name="perawatan"
                            required><?= $produk['deskripsi']['perawatan']; ?></textarea>
                    </div>

                    <div class="hr"></div>
                    <div class="section-title">Ruangan</div>
                    <div class="switches">
                        <label class="chip">
                            <input type="checkbox" name="ruang_tamu" <?= $produk['ruang_tamu'] ? 'checked' : ''; ?>>
                            Ruang Tamu
                        </label>
                        <label class="chip">
                            <input type="checkbox" name="ruang_keluarga"
                                <?= $produk['ruang_keluarga'] ? 'checked' : ''; ?>> Ruang Keluarga
                        </label>
                        <label class="chip">
                            <input type="checkbox" name="ruang_tidur" <?= $produk['ruang_tidur'] ? 'checked' : ''; ?>>
                            Ruang Tidur
                        </label>
                    </div>

                    <div class="hr"></div>
                    <div class="two">
                        <div>
                            <div class="section-title">Dimensi Paket</div>
                            <div class="two">
                                <div class="field"><label class="label">Panjang (cm)</label><input class="inp"
                                        name="panjang-paket"
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['panjang']; ?>"></div>
                                <div class="field"><label class="label">Lebar (cm)</label><input class="inp"
                                        name="lebar-paket"
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['lebar']; ?>"></div>
                            </div>
                            <div class="two">
                                <div class="field"><label class="label">Tinggi (cm)</label><input class="inp"
                                        name="tinggi-paket"
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['tinggi']; ?>"></div>
                                <div class="field"><label class="label">Berat (kg)</label><input class="inp"
                                        type="number" step="any" name="berat-paket"
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['berat']; ?>"></div>
                            </div>
                        </div>
                        <div>
                            <div class="section-title">Dimensi Asli</div>
                            <div class="two">
                                <div class="field"><label class="label">Panjang (cm)</label><input class="inp"
                                        name="panjang-asli"
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['panjang']; ?>"></div>
                                <div class="field"><label class="label">Lebar (cm)</label><input class="inp"
                                        name="lebar-asli"
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['lebar']; ?>"></div>
                            </div>
                            <div class="two">
                                <div class="field"><label class="label">Tinggi (cm)</label><input class="inp"
                                        name="tinggi-asli"
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['tinggi']; ?>"></div>
                                <div class="field"><label class="label">Berat (kg)</label><input class="inp"
                                        type="number" step="any" name="berat-asli"
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['berat']; ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden JSON fields untuk backend -->
                <input type="hidden" name="deskripsi" id="deskripsi-json">
                <input type="hidden" name="varian" id="varian-json">

                <div class="footer-actions">
                    <button class="btn ghost" type="button" onclick="history.back()">Batal</button>
                    <button class="btn primary" type="submit">Simpan Perubahan</button>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="stack">
                <div class="card">
                    <div class="section-title">Gambar Hover</div>
                    <div class="hint" style="margin-bottom:10px;">
                        Maksimal 4MB per foto. Format: JPG, PNG, WebP, atau AVIF. Sistem akan menyimpan ulang ke WebP.
                    </div>
                    <img id="imghover-preview" class="img-preview" src="/viewpichover/<?= $produk['id']; ?>?proxy=1&v=<?= $imageVersion; ?>" alt="">
                    <div class="uploader" style="margin-top:10px;">
                        <label class="btn">Pilih File</label>
                        <input name="gambar_hover" type="file" accept="image/jpeg,image/png,image/webp,image/avif" onchange="uploadFileGambarHover(event)">
                        <span class="muted">Maks. 4MB</span>
                    </div>
                </div>

                <div class="card">
                    <div class="section-title">Varian</div>
                    <div id="container-varian" class="stack">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                        <div class="card" style="padding:12px;">
                            <div class="section-title">Varian #<?= $ind_v + 1; ?></div>
                            <div class="list-images" id="container-gambar<?= $ind_v + 1; ?>">
                                <!-- preview lama -->
                                <?php foreach (explode(",", $v['urutan_gambar']) as $ind_u => $u) { ?>
                                <div class="thumb"
                                    onclick="hapusSubvarian('<?= $ind_v + 1; ?>','<?= $ind_u + 1; ?>',event)">
                                    <div class="x">hapus</div>
                                    <img src="/product-cover/<?= $produk['id'] ?>?slot=<?= urlencode(trim((string) $u)); ?>&proxy=1&v=<?= $imageVersion; ?>" alt="">
                                </div>
                                <?php } ?>
                            </div>

                            <div id="container-input-gambar<?= $ind_v + 1; ?>" class="row"
                                style="flex-wrap:wrap; margin-top:8px;">
                                <?php foreach (explode(",", $v['urutan_gambar']) as $ind_urutanGambar => $urutanGambar) { ?>
                                <label class="btn">
                                    + Tambah
                                    <input type="file"
                                        id="input-gambar-<?= $ind_v + 1; ?>-<?= $ind_urutanGambar + 1; ?>"
                                        name="gambar-<?= $ind_v + 1; ?>-<?= $ind_urutanGambar + 1; ?>"
                                        data-slot="<?= esc(trim((string) $urutanGambar), 'attr'); ?>"
                                        accept="image/jpeg,image/png,image/webp,image/avif"
                                        onchange="uploadFile(event)">
                                </label>
                                <?php } ?>
                                <label class="btn">
                                    + Tambah
                                    <input type="file"
                                        id="input-gambar-<?= $ind_v + 1; ?>-<?= count(explode(",", $v['urutan_gambar'])) + 1; ?>"
                                        name="gambar-<?= $ind_v + 1; ?>-<?= count(explode(",", $v['urutan_gambar'])) + 1; ?>"
                                        accept="image/jpeg,image/png,image/webp,image/avif"
                                        onchange="uploadFile(event)">
                                </label>
                            </div>

                            <div class="two" style="margin-top:10px;">
                                <div class="field">
                                    <label class="label">Nama</label>
                                    <input class="inp" type="text" name="nama-var<?= $ind_v + 1; ?>"
                                        value="<?= $v['nama']; ?>" required>
                                </div>
                                <div class="field">
                                    <label class="label">Kode Warna</label>
                                    <input class="inp" type="text" name="kode-var<?= $ind_v + 1; ?>"
                                        value="<?= $v['kode']; ?>" required>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Stok</label>
                                <input class="inp" type="text" name="stok-var<?= $ind_v + 1; ?>"
                                    value="<?= $v['stok']; ?>" required>
                            </div>

                            <div class="row" style="justify-content:flex-end;">
                                <button type="button" class="btn danger" onclick="deleteVarian(event)">Hapus
                                    Varian</button>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="row" style="justify-content:flex-end; margin-top:8px;">
                        <button class="btn" type="button" onclick="addVarian()">+ Tambah Varian</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- tracker varian (tetap seperti versi kamu) -->
        <input type="text" name="hitung-varian" style="display:none;" value="<?= $hitungVarian; ?>">
    </form>
</div>

<script>
let counterJmlVarian = <?= count($produk['varian']); ?>;
const hitungVarianInputElm = document.querySelector('input[name="hitung-varian"]');
const MAX_UPLOAD_MB = 4;
const MAX_PRODUCT_IMAGES = 25;
const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

function validasiFileGambar(file) {
    if (!file) return true;
    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
        alert('Format foto harus JPG, PNG, WebP, atau AVIF.');
        return false;
    }
    if (file.size > MAX_UPLOAD_MB * 1024 * 1024) {
        alert(`Ukuran foto maksimal ${MAX_UPLOAD_MB}MB per file. Kompres dulu fotonya atau pilih file yang lebih kecil.`);
        return false;
    }
    return true;
}

function buatElementDariHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild;
}

function uploadFileGambarHover(event) {
    const imgHoverPreviewElm = document.getElementById('imghover-preview');
    const file = event.target.files?. [0];
    if (!file) return;
    if (!validasiFileGambar(file)) {
        event.target.value = '';
        return;
    }
    imgHoverPreviewElm.src = URL.createObjectURL(file);
}

function uploadFile(event) {
    const varianNum = event.target.id.split("-")[2];
    const subVarianNum = event.target.id.split("-")[3];

    // tambahkan slot input berikutnya
    const inputBaru = `
      <label class="btn">
        + Tambah
        <input type="file" id="input-gambar-${varianNum}-${Number(subVarianNum)+1}"
               name="gambar-${varianNum}-${Number(subVarianNum)+1}" accept="image/jpeg,image/png,image/webp,image/avif" onchange="uploadFile(event)">
      </label>`;
    const containerInputGambar = document.getElementById("container-input-gambar" + varianNum);
    containerInputGambar.append(buatElementDariHTML(inputBaru));

    // tampilkan preview
    const file = event.target.files?. [0];
    if (!file) return;
    if (!validasiFileGambar(file)) {
        event.target.value = '';
        return;
    }
    const blobUrl = URL.createObjectURL(file);
    const existingSlot = event.target.dataset.slot || '';
    if (existingSlot) {
        const oldThumb = document.querySelector(`#container-gambar${varianNum} .thumb:nth-child(${Number(subVarianNum)}) img`);
        if (oldThumb) {
            oldThumb.src = blobUrl;
            return;
        }
    }
    const itemGambar = `
      <div class="thumb" >
        <div class="x">hapus</div>
        <img src="${blobUrl}" alt="">
      </div>`;
    const itemGambarElm = buatElementDariHTML(itemGambar);
    itemGambarElm.querySelector('.x').addEventListener('click', () => {
        document.getElementById("container-gambar" + varianNum).removeChild(itemGambarElm);
        containerInputGambar.removeChild(event.target.parentNode);
    });
    document.getElementById("container-gambar" + varianNum).append(itemGambarElm);
}

function addVarian() {
    const containerVarian = document.getElementById("container-varian");
    const no = ++counterJmlVarian;

    const html = `
      <div class="card" style="padding:12px;">
        <div class="section-title">Varian #${no}</div>

        <div class="list-images" id="container-gambar${no}"></div>
        <div id="container-input-gambar${no}" class="row" style="flex-wrap:wrap; margin-top:8px;">
          <label class="btn">
            + Tambah
            <input type="file" id="input-gambar-${no}-1" name="gambar-${no}-1" accept="image/jpeg,image/png,image/webp,image/avif" onchange="uploadFile(event)">
          </label>
        </div>

        <div class="two" style="margin-top:10px;">
          <div class="field">
            <label class="label">Nama</label>
            <input class="inp" type="text" name="nama-var${no}" required>
          </div>
          <div class="field">
            <label class="label">Kode Warna</label>
            <input class="inp" type="text" name="kode-var${no}" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Stok</label>
          <input class="inp" type="text" name="stok-var${no}" required>
        </div>

        <div class="row" style="justify-content:flex-end;">
          <button type="button" class="btn danger" onclick="deleteVarian(event)">Hapus Varian</button>
        </div>
      </div>`;
    containerVarian.append(buatElementDariHTML(html));

    // tracker
    if (hitungVarianInputElm.value === '') hitungVarianInputElm.value = String(no);
    else hitungVarianInputElm.value += "," + String(no);
}

function deleteVarian(event) {
    const card = event.target.closest('.card');
    const containerVarian = document.getElementById("container-varian");
    const urutanVarianKe = card.querySelector('[id^="container-gambar"]').id.replace('container-gambar', '');
    containerVarian.removeChild(card);

    // update tracker
    let varianArr = (hitungVarianInputElm.value || '').split(",").filter(Boolean);
    const idx = varianArr.indexOf(String(urutanVarianKe));
    if (idx >= 0) varianArr.splice(idx, 1);
    hitungVarianInputElm.value = varianArr.join(",");
}

function generateId() {
    /* dibiarkan sesuai kode asli */
}

function hapusSubvarian(varianNum, indexGambar, e) {
    const containerGambar = document.getElementById("container-gambar" + varianNum);
    const containerInputGambar = document.getElementById("container-input-gambar" + varianNum);
    const parentNode = document.getElementById('input-gambar-' + varianNum + '-' + indexGambar).parentNode;
    containerGambar.removeChild(e.target.parentNode);
    containerInputGambar.removeChild(parentNode);
}
</script>

<!-- Susun JSON & rename file inputs sebelum submit (supaya cocok dengan backend) -->
<script>
(function() {
    const form = document.querySelector('form');
    const deskripsiHidden = document.getElementById('deskripsi-json');
    const varianHidden = document.getElementById('varian-json');

    form.addEventListener('submit', function(event) {
        const selectedFiles = Array.from(form.querySelectorAll('input[type="file"]'))
            .flatMap(input => Array.from(input.files || []));
        const productFiles = Array.from(form.querySelectorAll('#container-varian input[type="file"]'))
            .flatMap(input => Array.from(input.files || []));
        if (productFiles.length > MAX_PRODUCT_IMAGES) {
            event.preventDefault();
            alert(`Maksimal ${MAX_PRODUCT_IMAGES} foto produk dalam sekali simpan.`);
            return;
        }
        for (const file of selectedFiles) {
            if (!validasiFileGambar(file)) {
                event.preventDefault();
                return;
            }
        }

        // DESKRIPSI JSON
        const deskripsiText = document.querySelector('textarea[name="deskripsi_teks"]').value || '';
        const perawatanText = document.querySelector('textarea[name="perawatan"]').value || '';
        const deskripsiJSON = {
            deskripsi: deskripsiText,
            perawatan: perawatanText,
            dimensi: {
                paket: {
                    panjang: document.querySelector('input[name="panjang-paket"]').value,
                    lebar: document.querySelector('input[name="lebar-paket"]').value,
                    tinggi: document.querySelector('input[name="tinggi-paket"]').value,
                    berat: document.querySelector('input[name="berat-paket"]').value
                },
                asli: {
                    panjang: document.querySelector('input[name="panjang-asli"]').value,
                    lebar: document.querySelector('input[name="lebar-asli"]').value,
                    tinggi: document.querySelector('input[name="tinggi-asli"]').value,
                    berat: document.querySelector('input[name="berat-asli"]').value
                }
            }
        };
        deskripsiHidden.value = JSON.stringify(deskripsiJSON);

        // VARIAN JSON
        const varianElems = Array.from(document.querySelectorAll('#container-varian > .card'));
        const renameTargets = [];
        const usedSlots = new Set();
        form.querySelectorAll('#container-varian input[type="file"][data-slot]').forEach(inp => {
            const slot = Number(inp.dataset.slot || 0);
            if (slot > 0) usedSlots.add(slot);
        });
        let nextImageSlot = Math.max(0, ...Array.from(usedSlots)) + 1;
        const allocateNewSlot = () => {
            while (usedSlots.has(nextImageSlot)) nextImageSlot++;
            usedSlots.add(nextImageSlot);
            return nextImageSlot++;
        };

        const varianArr = varianElems.map((card, idx) => {
            const no = idx + 1;
            const nama = card.querySelector(`input[name="nama-var${no}"]`)?.value || '';
            const kode = card.querySelector(`input[name="kode-var${no}"]`)?.value || '';
            const stok = card.querySelector(`input[name="stok-var${no}"]`)?.value || '0';

            const inputs = Array.from(card.querySelectorAll(
                `#container-input-gambar${no} input[type="file"]`));
            const urutan = [];
            inputs.forEach(inp => {
                let slot = Number(inp.dataset.slot || 0);
                const hasFile = inp.files && inp.files.length > 0;
                if (slot > 0) {
                    urutan.push(String(slot));
                    if (hasFile) {
                        renameTargets.push([inp, slot - 1]);
                    }
                    return;
                }

                if (inp.files && inp.files.length > 0) {
                    slot = allocateNewSlot();
                    inp.dataset.slot = String(slot);
                    urutan.push(String(slot));
                    renameTargets.push([inp, slot - 1]); // name => gambar_<0based>
                }
            });

            return {
                id: String(no),
                nama,
                kode,
                stok,
                urutan_gambar: urutan.join(',')
            };
        });

        varianHidden.value = JSON.stringify(varianArr);

        // RENAME file inputs sesuai pola backend
        renameTargets.forEach(([inp, zeroIdx]) => inp.setAttribute('name', `gambar_${zeroIdx}`));
        // gambar_hover dipertahankan namanya
    });
})();
</script>

<?= $this->endSection(); ?>
