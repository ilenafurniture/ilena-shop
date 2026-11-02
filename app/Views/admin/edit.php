<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<?php
$imageVersion = !empty($produk['tgl_update']) ? strtotime((string) $produk['tgl_update']) : time();
$variantColorPresets = [
    'Natural' => '#C6A477', 'Oak' => '#D0B17D', 'Teak' => '#A66B45', 'Walnut' => '#70513D',
    'Beige' => '#C9B99A', 'Putih' => '#F4F2ED', 'Abu-abu' => '#85827D', 'Hitam' => '#292929',
];
?>

<style>
.page {
    --bg: #f7f5f1;
    --muted: #77736c;
    --text: #26251f;
    --line: #e9e5dc;
    --brand: #75664f;
    --danger: #a5463d;
    padding: 1.25rem clamp(14px, 2.4vw, 34px) 2.5rem;
    max-width: 1200px;
    margin: 0 auto;
    color: var(--text);
    font-family: Inter, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    font-size: 14px;
    line-height: 1.55;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
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

.edit-section {
    min-width: 0;
    padding: 20px 0;
}

.edit-section+.edit-section {
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
    color: var(--muted);
}

.edit-page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 8px 2px 20px;
}
.edit-page-eyebrow {
    margin: 0 0 5px;
    color: #8c795a;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
}
.edit-page-head .title {
    margin: 0;
    color: #27251f;
    font-size: clamp(23px, 2.4vw, 29px);
    font-weight: 620;
    letter-spacing: -.035em;
}
.edit-page-head p {
    margin: 6px 0 0;
    color: #77736c;
    font-size: 13.5px;
}
.edit-back-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 40px;
    padding: 0 14px;
    border: 1px solid #e4ded2;
    border-radius: 9px;
    color: #514a3d;
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background .18s ease, border-color .18s ease, transform .18s ease;
}
.edit-back-link:hover { color: #332d23; background: #f6f2ea; border-color: #cfc3ae; transform: translateY(-1px); }
.page .edit-section {
    background: transparent;
    border: 0;
    border-radius: 0;
    box-shadow: none;
}
.page .section-title { color: #343127; font-size: 14px; font-weight: 620; letter-spacing: -.01em; }
.page .label { color: #656157; font-size: 12.5px; font-weight: 560; }
.page .inp, .page .sel, .page .ta {
    border-color: #e5e1d8;
    border-radius: 8px;
    background: #fff;
    color: #29271f;
    font-size: 13.5px;
    transition: border-color .16s ease, box-shadow .16s ease;
}
.page .inp:focus, .page .sel:focus, .page .ta:focus {
    border-color: #9b896a;
    box-shadow: 0 0 0 3px rgba(155, 137, 106, .14);
}
.page .input-group span { border-color: #e5e1d8; background: #f8f6f2; color: #77736c; }
.page .btn { border-color: #e3ded4; border-radius: 8px; background: #fff; color: #514a3d; font-size: 13px; font-weight: 600; }
.page .btn:hover { border-color: #cbbda5; background: #f8f5ef; }
.page .btn.primary { border-color: #66583f; background: #66583f; color: #fff; }
.page .btn.primary:hover { border-color: #51452f; background: #51452f; }
.page .btn.danger { color: #a5463d; border-color: #ead5d1; background: #fff; }
.page .chip { border-color: #e9e5dc; background: #faf9f6; color: #4f4b42; font-size: 12px; }
.page .thumb { border-color: #e6e1d7; background: #f6f3ed; }
.page .thumb .x { border: 1px solid rgba(255,255,255,.55); background: #a5463d; color: #fff; }
.page .img-preview { border-color: #e7e1d6; background: #f5f2ec; }
.page .footer-actions {
    z-index: 3;
    margin: 16px -2px -2px;
    padding: 14px 2px 4px;
    background: linear-gradient(180deg, rgba(247,245,241,0), #f7f5f1 34%);
}
/* Cardless form: sections are separated with fine rules and whitespace. */
.page {
    max-width: 1320px;
    padding-top: 1.1rem;
}
.edit-page-head {
    padding: 8px 0 19px;
    border-bottom: 1px solid #ded9cf;
}
.page .grid { display: flex; flex-direction: column; gap: 0; }
.page .grid > .stack { display: contents; }
.page .stack { gap: 0; }
.page > form > .grid > .stack > .edit-section {
    padding: 20px 0;
    border: 0;
    border-bottom: 1px solid #e3ded4;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}
.page > form > .grid > .stack > .edit-section:first-child { padding-top: 8px; }
.page .section-title {
    margin-bottom: 16px;
    color: #71644f;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .11em;
    line-height: 1.4;
    text-transform: uppercase;
}
.page > form > .grid > .stack > .edit-section { counter-increment: edit-section; }
.page > form > .grid > .stack > .edit-section > .section-title:first-child::before {
    content: counter(edit-section, decimal-leading-zero);
    display: inline-block;
    margin-right: 9px;
    color: #a18b68;
    font-variant-numeric: tabular-nums;
    letter-spacing: .02em;
}
.page .field { gap: 5px; margin-bottom: 14px; }
.page .label { color: #575247; font-size: 13px; font-weight: 600; }
.page .inp, .page .sel, .page .ta { min-height: 44px; padding: .68rem .8rem; }
.page .ta { min-height: 124px; }
.page .hint { font-size: 12px; line-height: 1.5; }
.page details { margin: 7px 0 14px; border-top: 1px solid #e3ded4; }
.page details summary {
    padding: 11px 0; color: #514a3d; cursor: pointer; font-size: 13px; font-weight: 600;
    list-style: none; display: flex; align-items: center; justify-content: space-between;
}
.page details summary::-webkit-details-marker { display: none; }
.page details summary::after { content: '+'; color: #8b684c; font-size: 18px; font-weight: 400; }
.page details[open] summary::after { content: '−'; }
.page .edit-section > .section-title:not(:first-child) { margin-top: 17px; }
.page #container-varian { border-top: 1px solid #e3ded4; }
.page .variant-section { border-bottom: 1px solid #e3ded4 !important; }
.page .variant-section .section-title { color: #514a3d; letter-spacing: .04em; }
.page .footer-actions { position: sticky; bottom: 0; z-index: 5; }
.page #container-varian > .variant-section {
    margin: 0;
    padding: 18px 0 !important;
    border: 0;
    border-top: 1px solid #e3ded4;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}
.page .variant-section .section-title {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 12px;
    color: #39352c;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
}
.page #container-varian > .variant-section:first-child .section-title { margin-top: 0; }
.page .variant-section .list-images { min-height: 0; }
.page .variant-section .thumb { cursor: pointer; }
.page .variant-section .uploader { flex-wrap: wrap; }
.page #container-varian > .variant-section:first-child { border-top: 0; padding-top: 2px !important; }
.page .footer-actions {
    position: sticky;
    bottom: 0;
    margin: 0;
    padding: 14px 0 5px;
    border-top: 1px solid #ded9cf;
    background: rgba(247, 245, 241, .96);
    backdrop-filter: blur(8px);
}
.page .img-preview { max-width: 330px; }
.page .list-images { gap: 8px; }
.page .thumb { width: 92px; height: 92px; border: 1px solid #e2ddd3; border-radius: 8px; background: #eeeae1; overflow:hidden; }
.page .thumb img { display:block; width:100%; height:100%; object-fit:cover; }
.page .thumb .x { top:auto; right:5px; bottom:5px; z-index:2; display:grid; place-items:center; width:30px; height:30px; padding:0; border:1px solid rgba(255,255,255,.72); border-radius:7px; background:rgba(255,255,255,.95); color:#9e3831; cursor:pointer; }
.page .thumb .x:hover { background:#fff5f3; }
.page .thumb .thumb-index { position:absolute; top:5px; left:5px; z-index:2; display:grid; place-items:center; min-width:24px; height:24px; padding:0 5px; border:1px solid rgba(255,255,255,.72); border-radius:7px; background:rgba(35,32,27,.72); color:#fff; font-size:11px; font-weight:700; }
.page .thumb .thumb-drag { position:absolute; top:5px; right:5px; z-index:2; display:grid; place-items:center; width:26px; height:26px; border:1px solid rgba(255,255,255,.72); border-radius:7px; background:rgba(35,32,27,.72); color:#fff; pointer-events:none; }
.page .thumb .thumb-drag .material-icons { font-size:17px; }
.page .thumb { cursor:grab; transition:opacity .16s ease, transform .16s ease, box-shadow .16s ease; }
.page .thumb:active { cursor:grabbing; }
.page .thumb.is-dragging { opacity:.42; }
.page .thumb.is-drop-target { transform:translateY(-2px); box-shadow:0 0 0 2px rgba(162,142,107,.55); }
.page .thumb .material-icons { font-size:17px; }
.page .variant-section .list-images { gap:9px; }
.page #container-input-gambar .btn { min-height:36px; padding:6px 10px; border-radius:7px; font-size:12px; }
.page #container-input-gambar > label:not(.image-upload-more) { width:36px; min-width:36px; height:36px; min-height:36px; padding:0; overflow:hidden; justify-content:center; font-size:0; }
.page #container-input-gambar > label:not(.image-upload-more) .material-icons { font-size:17px; }
.page #container-input-gambar input[type="file"] { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); }
.page #container-input-gambar .image-upload-more { display:inline-flex; align-items:center; justify-content:center; gap:5px; }
.page #container-input-gambar .image-upload-more .material-icons { font-size:17px; }
.page .chip { border: 0; border-bottom: 1px solid #e3ded4; border-radius: 0; background: transparent; }
@media (max-width: 700px) {
    .edit-page-head { align-items: flex-start; }
    .edit-page-head p { max-width: 34ch; }
    .edit-back-link { flex: 0 0 auto; min-height: 38px; padding: 0 10px; }
    .page > form > .grid > .stack > .edit-section { padding: 17px 0; }
    .page .img-preview { max-width: 100%; }
}
.product-save-progress {
    display: none;
    margin: 18px 0 0;
    padding: 14px 0;
    border-top: 1px solid var(--line);
    color: var(--text);
}
.product-save-progress.is-visible { display: block; }
.product-save-progress__label { margin: 0 0 8px; font-weight: 600; }
.product-save-progress__detail { margin: 0; color: var(--muted); font-size: .9rem; }
.product-save-progress__track { height: 4px; margin-top: 12px; overflow: hidden; background: #e9e5dc; }
.product-save-progress__bar { width: 0; height: 100%; background: var(--brand); transition: width .18s ease; }
.product-save-progress.is-processing .product-save-progress__bar { width: 34%; animation: product-save-indeterminate 1.1s ease-in-out infinite alternate; }
.product-save-progress.is-error { color: #9b3932; }
@keyframes product-save-indeterminate { from { transform: translateX(-100%); } to { transform: translateX(295%); } }
@media (prefers-reduced-motion: reduce) { .product-save-progress__bar { transition: none; } .product-save-progress.is-processing .product-save-progress__bar { animation: none; width: 100%; } }
.variant-color-editor { display:grid; gap:8px; }
.variant-color-presets { display:flex; flex-wrap:wrap; gap:6px; }
.variant-color-choice { display:inline-flex; align-items:center; gap:6px; min-height:34px; padding:4px 8px; border:1px solid #e5e1d8; border-radius:18px; background:#fff; color:#454039; font:inherit; font-size:11px; cursor:pointer; }
.variant-color-choice:hover,.variant-color-choice.is-selected { border-color:#a8252d; background:#fbf5f4; }
.variant-color-choice__dot,.variant-color-swatch { width:20px; height:20px; flex:none; border:1px solid rgba(35,31,27,.2); border-radius:50%; }
.variant-color-custom { display:inline-flex; position:relative; align-items:center; gap:7px; min-height:34px; padding:4px 8px; border:1px dashed #c9c2b6; border-radius:18px; color:#625a50; font-size:11px; cursor:pointer; }
.variant-color-custom input { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }
.variant-color-current { display:flex; align-items:center; gap:7px; color:var(--muted); font-size:11px; }
@media (min-width:1100px) {
    .page { max-width: 1480px; }
    .grid { grid-template-columns: minmax(0,1.15fr) minmax(420px,.85fr); gap: 36px; }
    .page #container-varian > .variant-section {
        display: grid; grid-template-columns: 180px minmax(0,1fr); column-gap: 24px; align-items: start;
    }
    .page #container-varian > .variant-section > .section-title { grid-column: 1 / -1; margin: 4px 0 0; }
    .page #container-varian > .variant-section > .list-images,
    .page #container-varian > .variant-section > #container-input-gambar { grid-column: 1; }
    .page #container-varian > .variant-section > .two,
    .page #container-varian > .variant-section > .field,
    .page #container-varian > .variant-section > .row { grid-column: 2; }
    .page #container-varian > .variant-section > .two { display:grid; grid-template-columns: minmax(220px,.8fr) minmax(420px,1.2fr); align-items:start; gap:18px; }
    .variant-color-presets { gap:8px; }
}
</style>

<div class="page">
    <header class="edit-page-head">
        <div>
            <div class="edit-page-eyebrow">Katalog · Pengelolaan</div>
            <h1 class="title">Edit Produk</h1>
            <p>Atur detail produk, harga, varian, dan foto.</p>
        </div>
        <a class="edit-back-link" href="/admin/product"><i class="material-icons" aria-hidden="true">arrow_back</i><span>Kembali ke katalog</span></a>
    </header>

    <form method="post" id="product-edit-form"
        action="/admin/editproduct/<?= $produk['id']; ?><?= isset($_GET['sblm']) ? '/' . $_GET['sblm'] : ''; ?>"
        enctype="multipart/form-data">
        <?= csrf_field(); ?>

        <div class="grid">
            <!-- LEFT -->
            <div class="stack">
                <div class="edit-section">
                    <div class="section-title">Detail Produk</div>
                    <div class="field">
                        <label class="label" for="produk-nama">Nama produk</label>
                        <input class="inp" id="produk-nama" type="text" name="nama" required value="<?= esc($produk['nama'], 'attr'); ?>">
                    </div>

                    <div class="two">
                        <div class="field">
                            <label class="label" for="produk-harga">Harga (Rp)</label>
                            <input class="inp" id="produk-harga" type="number" min="0" step="1" name="harga" required value="<?= esc($produk['harga'], 'attr'); ?>">
                        </div>
                        <div class="field">
                            <label class="label" for="produk-diskon">Diskon</label>
                            <div class="input-group">
                                <input class="inp" id="produk-diskon" type="number" name="diskon" min="0" max="100" step="any" required
                                    value="<?= esc($produk['diskon'], 'attr'); ?>">
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

                    <details>
                        <summary>Link marketplace <span class="hint">Opsional</span></summary>
                        <div class="two">
                            <div class="field"><label class="label">Shopee</label><input class="inp" type="text" name="shopee" value="<?= esc($produk['shopee'] ?? '', 'attr'); ?>"></div>
                            <div class="field"><label class="label">Tokopedia</label><input class="inp" type="text" name="tokped" value="<?= esc($produk['tokped'] ?? '', 'attr'); ?>"></div>
                        </div>
                        <div class="field"><label class="label">TikTok Shop</label><input class="inp" type="text" name="tiktok" value="<?= esc($produk['tiktok'] ?? '', 'attr'); ?>"></div>
                    </details>
                </div>

                <div class="edit-section">
                    <div class="section-title">Deskripsi & Ukuran</div>
                    <div class="field">
                        <label class="label">Deskripsi</label>
                        <textarea class="ta" name="deskripsi_teks"
                            required><?= $produk['deskripsi']['deskripsi']; ?></textarea>
                    </div>
                    <div class="field">
                        <label class="label">Nama produk Inggris <span class="muted">(opsional)</span></label>
                        <input class="inp" type="text" name="nama_en" maxlength="180" value="<?= esc($produk['deskripsi']['nama_en'] ?? '', 'attr'); ?>">
                    </div>
                    <div class="field">
                        <label class="label">Spesifikasi / Perawatan</label>
                        <textarea class="ta" name="perawatan"
                            required><?= $produk['deskripsi']['perawatan']; ?></textarea>
                    </div>
                    <div class="field">
                        <label class="label">Deskripsi Inggris <span class="muted">(opsional)</span></label>
                        <textarea class="ta" name="deskripsi_en" maxlength="3000"><?= esc($produk['deskripsi']['deskripsi_en'] ?? ''); ?></textarea>
                    </div>
                    <div class="field">
                        <label class="label">Perawatan / spesifikasi Inggris <span class="muted">(opsional)</span></label>
                        <textarea class="ta" name="perawatan_en" maxlength="3000"><?= esc($produk['deskripsi']['perawatan_en'] ?? ''); ?></textarea>
                    </div>

                    <div class="section-title">Penempatan</div>
                    <div class="switches" aria-label="Ruangan produk">
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

                    <details>
                    <summary>Dimensi &amp; berat <span class="hint">Opsional</span></summary>
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
                    </details>
                </div>

                <!-- Hidden JSON fields untuk backend -->
                <input type="hidden" name="deskripsi" id="deskripsi-json">
                <input type="hidden" name="varian" id="varian-json">

            </div>

            <!-- RIGHT -->
            <div class="stack">
                <div class="edit-section">
                    <div class="section-title">Foto Alternatif</div>
                    <div class="hint" style="margin-bottom:10px;">
                        WebP atau AVIF · Maks. 4 MB per foto · Disimpan sebagai WebP.
                    </div>
                    <img id="imghover-preview" class="img-preview" src="/viewpichover/<?= $produk['id']; ?>?proxy=1&v=<?= $imageVersion; ?>" alt="">
                    <div class="uploader" style="margin-top:10px;">
                        <label class="btn">Pilih File</label>
                        <input name="gambar_hover" type="file" accept="image/webp,image/avif" onchange="uploadFileGambarHover(event)">
                        <span class="muted">Maks. 4MB</span>
                    </div>
                </div>

                <div class="edit-section">
                    <div class="section-title">Varian</div>
                    <div id="container-varian" class="stack">
                        <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                        <div class="variant-section">
                            <div class="section-title">Varian #<?= $ind_v + 1; ?></div>
                            <div class="list-images" id="container-gambar<?= $ind_v + 1; ?>">
                                <!-- preview lama -->
                                <?php foreach (explode(",", $v['urutan_gambar']) as $ind_u => $u) { ?>
                                <div class="thumb" draggable="true" ondragstart="startEditImageDrag(event)" ondragend="endEditImageDrag(event)" ondragover="event.preventDefault()" ondrop="dropEditImage(event,'<?= $ind_v + 1; ?>')" data-file-id="input-gambar-<?= $ind_v + 1; ?>-<?= $ind_u + 1; ?>" title="Seret untuk mengubah urutan">
                                    <img src="/product-cover/<?= $produk['id'] ?>?slot=<?= urlencode(trim((string) $u)); ?>&proxy=1&v=<?= $imageVersion; ?>" alt="">
                                    <span class="thumb-index" aria-label="Foto ke-<?= $ind_u + 1; ?>"><?= $ind_u + 1; ?></span>
                                    <span class="thumb-drag" aria-hidden="true"><i class="material-icons">drag_indicator</i></span>
                                    <button class="x" type="button" onclick="hapusSubvarian('<?= $ind_v + 1; ?>','<?= $ind_u + 1; ?>',event)" title="Hapus foto <?= $ind_u + 1; ?>" aria-label="Hapus foto <?= $ind_u + 1; ?>"><i class="material-icons" aria-hidden="true">delete_outline</i></button>
                                </div>
                                <?php } ?>
                            </div>

                            <div id="container-input-gambar<?= $ind_v + 1; ?>" class="row"
                                style="flex-wrap:wrap; margin-top:8px;">
                                <?php foreach (explode(",", $v['urutan_gambar']) as $ind_urutanGambar => $urutanGambar) { ?>
                                <label class="btn" title="Pilih foto untuk mengganti foto <?= $ind_urutanGambar + 1; ?>">
                                    <i class="material-icons" aria-hidden="true">edit</i> Ganti
                                    <input type="file"
                                        id="input-gambar-<?= $ind_v + 1; ?>-<?= $ind_urutanGambar + 1; ?>"
                                        name="gambar-<?= $ind_v + 1; ?>-<?= $ind_urutanGambar + 1; ?>"
                                        data-slot="<?= esc(trim((string) $urutanGambar), 'attr'); ?>"
                                        accept="image/webp,image/avif"
                                        onchange="uploadFile(event)">
                                </label>
                                <?php } ?>
                                <label class="btn image-upload-more" title="Tambah foto">
                                    <i class="material-icons" aria-hidden="true">add_photo_alternate</i> Tambah foto
                                    <input type="file"
                                        id="input-gambar-<?= $ind_v + 1; ?>-<?= count(explode(",", $v['urutan_gambar'])) + 1; ?>"
                                        name="gambar-<?= $ind_v + 1; ?>-<?= count(explode(",", $v['urutan_gambar'])) + 1; ?>"
                                        accept="image/webp,image/avif"
                                        onchange="uploadFile(event)">
                                </label>
                            </div>

                            <div class="two" style="margin-top:10px;">
                                <div class="field">
                                    <label class="label">Nama</label>
                                    <input class="inp" type="text" name="nama-var<?= $ind_v + 1; ?>"
                                        value="<?= esc($v['nama'] ?? '', 'attr'); ?>" required>
                                </div>
                                <div class="field">
                                    <label class="label">Warna varian</label>
                                    <div class="variant-color-editor">
                                        <input type="hidden" name="kode-var<?= $ind_v + 1; ?>" value="<?= esc($v['kode'] ?? '', 'attr'); ?>" required>
                                        <div class="variant-color-presets" role="group" aria-label="Pilih warna varian">
                                            <?php foreach ($variantColorPresets as $colorName => $colorValue): ?>
                                            <button type="button" class="variant-color-choice<?= strtolower((string) ($v['kode'] ?? '')) === strtolower($colorValue) ? ' is-selected' : ''; ?>" data-variant-color="<?= esc($colorValue, 'attr'); ?>" aria-pressed="<?= strtolower((string) ($v['kode'] ?? '')) === strtolower($colorValue) ? 'true' : 'false'; ?>">
                                                <span class="variant-color-choice__dot" style="background-color:<?= esc($colorValue, 'attr'); ?>"></span><?= esc($colorName); ?>
                                            </button>
                                            <?php endforeach; ?>
                                            <label class="variant-color-custom"><span class="variant-color-swatch">＋</span><span>Warna lain</span><input type="color" aria-label="Pilih warna khusus"></label>
                                        </div>
                                        <div class="variant-color-current"><span class="variant-color-swatch" data-current-color></span><span data-current-color-label>Warna tersimpan</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="nama-en-var<?= $ind_v + 1; ?>">Nama varian Inggris <span class="muted">(opsional)</span></label>
                                <input class="inp" type="text" id="nama-en-var<?= $ind_v + 1; ?>" name="nama-en-var<?= $ind_v + 1; ?>"
                                    maxlength="100" value="<?= esc($v['nama_en'] ?? '', 'attr'); ?>" placeholder="Contoh: Natural Oak">
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

        <div class="footer-actions">
            <a class="btn ghost" href="/admin/product">Batal</a>
            <button class="btn primary" type="submit">Simpan perubahan</button>
        </div>
        <div id="product-save-progress" class="product-save-progress" role="status" aria-live="polite">
            <p class="product-save-progress__label" id="product-save-progress-label">Menyimpan perubahan…</p>
            <p class="product-save-progress__detail" id="product-save-progress-detail"></p>
            <div class="product-save-progress__track" aria-hidden="true"><div class="product-save-progress__bar" id="product-save-progress-bar"></div></div>
        </div>

        <!-- tracker varian (tetap seperti versi kamu) -->
        <input type="text" name="hitung-varian" style="display:none;" value="<?= $hitungVarian; ?>">
    </form>
</div>

<script>
let counterJmlVarian = <?= count($produk['varian']); ?>;
const hitungVarianInputElm = document.querySelector('input[name="hitung-varian"]');
const variantColorPresetMap = <?= json_encode($variantColorPresets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
function syncVariantColorEditor(editor, color) {
    const input = editor.querySelector('input[type="hidden"][name^="kode-var"]');
    const normalized = String(color || '').trim();
    if (input) input.value = normalized;
    editor.querySelectorAll('[data-variant-color]').forEach(button => {
        const selected = button.dataset.variantColor.toLowerCase() === normalized.toLowerCase();
        button.classList.toggle('is-selected', selected);
        button.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });
    const dot = editor.querySelector('[data-current-color]');
    if (dot && normalized) dot.style.backgroundColor = normalized;
    const label = editor.querySelector('[data-current-color-label]');
    if (label && normalized) label.textContent = 'Warna varian dipilih';
}
document.addEventListener('click', event => {
    const choice = event.target.closest('[data-variant-color]');
    if (!choice) return;
    const editor = choice.closest('.variant-color-editor');
    if (editor) syncVariantColorEditor(editor, choice.dataset.variantColor);
});
document.addEventListener('input', event => {
    if (!event.target.matches('.variant-color-custom input[type="color"]')) return;
    const editor = event.target.closest('.variant-color-editor');
    if (editor) syncVariantColorEditor(editor, event.target.value);
});
document.querySelectorAll('.variant-color-editor').forEach(editor => {
    const value = editor.querySelector('input[type="hidden"][name^="kode-var"]')?.value || '';
    syncVariantColorEditor(editor, value);
});
const MAX_UPLOAD_MB = 4;
const MAX_PRODUCT_IMAGES = 25;
const ALLOWED_IMAGE_TYPES = ['image/webp', 'image/avif'];

function validasiFileGambar(file) {
    if (!file) return true;
    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
        alert('Format foto hanya WebP atau AVIF.');
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
            <label class="btn image-upload-more" title="Tambah foto">
              <i class="material-icons" aria-hidden="true">add_photo_alternate</i> Tambah foto
        <input type="file" id="input-gambar-${varianNum}-${Number(subVarianNum)+1}"
               name="gambar-${varianNum}-${Number(subVarianNum)+1}" accept="image/webp,image/avif" onchange="uploadFile(event)">
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
        const oldThumb = Array.from(document.getElementById("container-gambar" + varianNum)?.querySelectorAll('.thumb') || [])
            .find(thumb => thumb.dataset.fileId === event.target.id);
        if (oldThumb?.querySelector('img')) {
            oldThumb.querySelector('img').src = blobUrl;
            return;
        }
    }
    const itemGambar = `
      <div class="thumb" draggable="true" ondragstart="startEditImageDrag(event)" ondragend="endEditImageDrag(event)" ondragover="event.preventDefault()" ondrop="dropEditImage(event,'${varianNum}')" data-file-id="${event.target.id}" title="Seret untuk mengubah urutan">
        <span class="thumb-index">${Number(subVarianNum)}</span>
        <span class="thumb-drag" aria-hidden="true"><i class="material-icons">drag_indicator</i></span>
        <button class="x" type="button" title="Hapus foto" aria-label="Hapus foto"><i class="material-icons" aria-hidden="true">delete_outline</i></button>
        <img src="${blobUrl}" alt="">
      </div>`;
    const itemGambarElm = buatElementDariHTML(itemGambar);
    itemGambarElm.querySelector('.x').addEventListener('click', (deleteEvent) => {
        document.getElementById("container-gambar" + varianNum).removeChild(itemGambarElm);
        containerInputGambar.removeChild(event.target.parentNode);
        refreshEditThumbNumbers(varianNum);
    });
    document.getElementById("container-gambar" + varianNum).append(itemGambarElm);
}

function startEditImageDrag(event) {
    const thumb = event.target.closest('.thumb');
    if (!thumb || !event.dataTransfer) return;
    thumb.classList.add('is-dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', thumb.dataset.fileId || '');
}

function endEditImageDrag(event) {
    event.target.closest('.thumb')?.classList.remove('is-dragging');
    document.querySelectorAll('.thumb.is-drop-target').forEach(thumb => thumb.classList.remove('is-drop-target'));
}

function refreshEditThumbNumbers(varianNum) {
    document.getElementById('container-gambar' + varianNum)?.querySelectorAll('.thumb-index').forEach((badge, index) => {
        badge.textContent = String(index + 1);
    });
}

function dropEditImage(event, varianNum) {
    event.preventDefault();
    const targetThumb = event.target.closest('.thumb');
    const container = document.getElementById('container-gambar' + varianNum);
    const inputContainer = document.getElementById('container-input-gambar' + varianNum);
    const sourceId = event.dataTransfer?.getData('text/plain');
    const sourceThumb = Array.from(container?.querySelectorAll('.thumb') || []).find(thumb => thumb.dataset.fileId === sourceId);
    if (!targetThumb || !sourceThumb || sourceThumb === targetThumb) return;
    const thumbs = Array.from(container.querySelectorAll('.thumb'));
    const sourceBeforeTarget = thumbs.indexOf(sourceThumb) < thumbs.indexOf(targetThumb);
    container.insertBefore(sourceThumb, sourceBeforeTarget ? targetThumb.nextSibling : targetThumb);

    const orderedInputs = Array.from(container.querySelectorAll('.thumb'))
        .map(thumb => document.getElementById(thumb.dataset.fileId)?.parentElement)
        .filter(Boolean);
    const inputSet = new Set(orderedInputs);
    const remaining = Array.from(inputContainer.children).filter(child => !inputSet.has(child));
    inputContainer.replaceChildren(...orderedInputs, ...remaining);
    refreshEditThumbNumbers(varianNum);
}

function addVarian() {
    const containerVarian = document.getElementById("container-varian");
    const no = ++counterJmlVarian;

    const html = `
      <div class="variant-section">
        <div class="section-title">Varian #${no}</div>

        <div class="list-images" id="container-gambar${no}"></div>
        <div id="container-input-gambar${no}" class="row" style="flex-wrap:wrap; margin-top:8px;">
          <label class="btn">
            + Tambah
            <input type="file" id="input-gambar-${no}-1" name="gambar-${no}-1" accept="image/webp,image/avif" onchange="uploadFile(event)">
          </label>
        </div>

        <div class="two" style="margin-top:10px;">
          <div class="field">
            <label class="label">Nama</label>
            <input class="inp" type="text" name="nama-var${no}" required>
          </div>
          <div class="field">
            <label class="label">Warna varian</label>
            <div class="variant-color-editor">
              <input type="hidden" name="kode-var${no}" required>
              <div class="variant-color-presets" role="group" aria-label="Pilih warna varian">
                ${Object.entries(variantColorPresetMap).map(([name, color]) => `<button type="button" class="variant-color-choice" data-variant-color="${color}" aria-pressed="false"><span class="variant-color-choice__dot" style="background-color:${color}"></span>${name}</button>`).join('')}
                <label class="variant-color-custom"><span class="variant-color-swatch">＋</span><span>Warna lain</span><input type="color" aria-label="Pilih warna khusus"></label>
              </div>
              <div class="variant-color-current"><span class="variant-color-swatch" data-current-color></span><span data-current-color-label>Pilih warna untuk varian ini</span></div>
            </div>
          </div>
        </div>
        <div class="field">
          <label class="label" for="nama-en-var${no}">Nama varian Inggris <span class="muted">(opsional)</span></label>
          <input class="inp" type="text" id="nama-en-var${no}" name="nama-en-var${no}" maxlength="100" placeholder="Contoh: Natural Oak">
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
    const variantSection = event.target.closest('.variant-section');
    const containerVarian = document.getElementById("container-varian");
    const urutanVarianKe = variantSection.querySelector('[id^="container-gambar"]').id.replace('container-gambar', '');
    containerVarian.removeChild(variantSection);

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
    const thumb = e.target.closest('.thumb');
    const input = document.getElementById(thumb?.dataset.fileId || ('input-gambar-' + varianNum + '-' + indexGambar));
    if (thumb) containerGambar.removeChild(thumb);
    if (input?.parentNode && containerInputGambar.contains(input.parentNode)) containerInputGambar.removeChild(input.parentNode);
    refreshEditThumbNumbers(varianNum);
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
        const deskripsiEnText = document.querySelector('textarea[name="deskripsi_en"]').value || '';
        const perawatanEnText = document.querySelector('textarea[name="perawatan_en"]').value || '';
        const namaEnText = document.querySelector('input[name="nama_en"]').value || '';
        const deskripsiJSON = {
            deskripsi: deskripsiText,
            perawatan: perawatanText,
            nama_en: namaEnText.trim().slice(0, 180),
            deskripsi_en: deskripsiEnText,
            perawatan_en: perawatanEnText,
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
        const varianElems = Array.from(document.querySelectorAll('#container-varian > .variant-section'));
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
            const namaEn = card.querySelector(`input[name="nama-en-var${no}"]`)?.value || '';
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
                nama_en: namaEn.trim().slice(0, 100),
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

<script>
(function () {
    const form = document.getElementById('product-edit-form');
    const progress = document.getElementById('product-save-progress');
    const label = document.getElementById('product-save-progress-label');
    const detail = document.getElementById('product-save-progress-detail');
    const bar = document.getElementById('product-save-progress-bar');
    const submitButton = form?.querySelector('button[type="submit"]');
    if (!form || !progress || !submitButton) return;

    form.addEventListener('submit', function (event) {
        if (event.defaultPrevented) return;
        event.preventDefault();
        if (submitButton.disabled) return;

        const payload = new FormData(form);
        const xhr = new XMLHttpRequest();
        submitButton.disabled = true;
        progress.classList.add('is-visible');
        progress.classList.remove('is-error', 'is-processing');
        label.textContent = 'Mengunggah perubahan…';
        detail.textContent = 'Foto dikirim dengan aman; proses resize berjalan setelah upload selesai.';
        bar.style.width = '0%';

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.upload.addEventListener('progress', function (uploadEvent) {
            if (!uploadEvent.lengthComputable) {
                detail.textContent = 'Foto sedang dikirim…';
                return;
            }
            const percent = Math.min(100, Math.round((uploadEvent.loaded / uploadEvent.total) * 100));
            bar.style.width = percent + '%';
            detail.textContent = `Upload foto ${percent}%`;
            if (percent >= 100) {
                progress.classList.add('is-processing');
                label.textContent = 'Foto diterima, sedang diproses…';
                detail.textContent = 'Mohon tunggu, sistem sedang menyimpan dan menyiapkan foto produk.';
            }
        });
        xhr.addEventListener('load', function () {
            let result = {};
            try { result = JSON.parse(xhr.responseText || '{}'); } catch (_) { /* response non-JSON ditangani di bawah */ }
            if (xhr.status >= 200 && xhr.status < 300 && result.pesan) {
                label.textContent = 'Produk berhasil diperbarui';
                detail.textContent = result.pesan;
                bar.style.width = '100%';
                window.location.assign('/admin/product');
                return;
            }
            progress.classList.remove('is-processing');
            progress.classList.add('is-error');
            label.textContent = 'Perubahan belum tersimpan';
            detail.textContent = result.pesan || `Server merespons dengan status ${xhr.status || 'tidak diketahui'}. Silakan coba lagi.`;
            submitButton.disabled = false;
        });
        xhr.addEventListener('error', function () {
            progress.classList.remove('is-processing');
            progress.classList.add('is-error');
            label.textContent = 'Koneksi terputus';
            detail.textContent = 'Periksa koneksi lalu coba simpan kembali.';
            submitButton.disabled = false;
        });
        xhr.addEventListener('timeout', function () {
            progress.classList.remove('is-processing');
            progress.classList.add('is-error');
            label.textContent = 'Proses melewati batas waktu';
            detail.textContent = 'Coba simpan kembali. Jika berulang, catat waktu kejadian untuk tim teknis.';
            submitButton.disabled = false;
        });
        xhr.timeout = 180000;
        xhr.send(payload);
    });
})();
</script>

<?= $this->endSection(); ?>
