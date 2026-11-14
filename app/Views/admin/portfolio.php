<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<?php
$project = is_array($editProject ?? null) ? $editProject : [];
$gallery = json_decode((string) ($project['gallery_json'] ?? '[]'), true);
$galleryText = is_array($gallery) ? implode("\n", $gallery) : '';
$value = static fn (string $key, $fallback = '') => old($key) ?? ($project[$key] ?? $fallback);
?>
<style>
.portfolio-admin{--pa-ink:#211f1e;--pa-muted:#736d69;--pa-line:#e8e2dd;--pa-red:#922e2a;color:var(--pa-ink);display:grid;gap:22px}.portfolio-admin__head{display:flex;justify-content:space-between;align-items:end;gap:20px;border-bottom:1px solid var(--pa-line);padding-bottom:20px}.portfolio-admin__head h1{font-size:clamp(26px,3vw,36px);letter-spacing:-.04em;margin:0 0 6px;font-weight:750}.portfolio-admin__head p,.portfolio-admin__hint{color:var(--pa-muted);font-size:13px;margin:0;line-height:1.6}.portfolio-admin__public{color:var(--pa-red);font-size:13px;font-weight:700;text-decoration:none}.portfolio-admin__notice{padding:12px 15px;border-left:3px solid var(--pa-red);background:#f6f1ee;font-size:13px}.portfolio-admin__layout{display:grid;gap:26px}.portfolio-admin__editor{display:grid;grid-template-columns:minmax(0,.95fr) minmax(340px,1.05fr);gap:26px;align-items:start}.portfolio-admin__form{background:#fff;border:1px solid var(--pa-line);padding:22px;display:grid;gap:14px}.portfolio-admin__form h2,.portfolio-admin__list h2{font-size:17px;font-weight:750;margin:0}.portfolio-admin__form label{display:grid;gap:6px;font-size:12px;font-weight:700}.portfolio-admin__form input,.portfolio-admin__form textarea{width:100%;border:1px solid #d9d3ce;border-radius:8px;padding:10px 12px;background:#fff;color:var(--pa-ink);font:inherit;font-size:13px}.portfolio-admin__form textarea{min-height:76px;resize:vertical}.portfolio-admin__two{display:grid;grid-template-columns:1fr 1fr;gap:12px}.portfolio-admin__checks{display:flex;gap:18px;flex-wrap:wrap}.portfolio-admin__checks label{display:flex;align-items:center;gap:8px}.portfolio-admin__checks input{width:17px;height:17px;accent-color:var(--pa-red)}.portfolio-admin__submit{border:0;background:var(--pa-red);color:#fff;border-radius:8px;padding:12px 16px;font-weight:750;cursor:pointer}.portfolio-admin__cancel{color:var(--pa-muted);font-size:13px}.portfolio-admin__list{min-width:0}.portfolio-admin__list-head{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:12px}.portfolio-admin__row{display:grid;grid-template-columns:110px minmax(0,1fr) auto;gap:16px;align-items:center;border-top:1px solid var(--pa-line);padding:14px 0}.portfolio-admin__thumb{width:110px;aspect-ratio:1.35;object-fit:cover;background:#eeeae6}.portfolio-admin__thumb--empty{display:grid;place-items:center;color:#958b84;font:20px Georgia,serif}.portfolio-admin__row h3{font-size:14px;margin:0 0 5px;font-weight:750}.portfolio-admin__row p{font-size:12px;color:var(--pa-muted);margin:0;line-height:1.5}.portfolio-admin__status{font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:750;color:#5f655b;margin-top:6px}.portfolio-admin__status--off{color:#9a413c}.portfolio-admin__actions{display:flex;align-items:center;gap:8px}.portfolio-admin__actions a,.portfolio-admin__actions button{border:1px solid var(--pa-line);background:#fff;border-radius:7px;padding:8px 10px;color:var(--pa-ink);text-decoration:none;font-size:12px;cursor:pointer}.portfolio-admin__actions button{color:var(--pa-red)}.portfolio-admin__empty{padding:35px 12px;border-top:1px solid var(--pa-line);color:var(--pa-muted);font-size:13px}.portfolio-admin .pager{padding-top:14px}@media(max-width:1100px){.portfolio-admin__editor{grid-template-columns:minmax(0,1fr) minmax(300px,.9fr);gap:18px}}@media(max-width:900px){.portfolio-admin__editor{grid-template-columns:1fr}.portfolio-admin__preview{position:static;order:-1}.portfolio-admin__preview-frame{max-height:none}}@media(max-width:640px){.portfolio-admin__head{align-items:start;flex-direction:column}.portfolio-admin__form{padding:16px}.portfolio-admin__two{grid-template-columns:1fr}.portfolio-admin__row{grid-template-columns:76px minmax(0,1fr);gap:11px}.portfolio-admin__thumb{width:76px}.portfolio-admin__actions{grid-column:2;justify-content:flex-start}}
</style>
<style>
.portfolio-admin__visibility{display:flex;justify-content:space-between;align-items:center;gap:18px;border-bottom:1px solid #e8e2dd;padding:0 0 18px}.portfolio-admin__visibility-copy{display:grid;gap:5px}.portfolio-admin__visibility-copy strong{font-size:14px}.portfolio-admin__visibility-copy span{font-size:12px;color:#736d69;line-height:1.5}.portfolio-admin__visibility-control{display:flex;align-items:center;gap:12px;flex:none}.portfolio-admin__visibility-control label{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;cursor:pointer}.portfolio-admin__visibility-control input{width:18px;height:18px;accent-color:#922e2a}.portfolio-admin__visibility-control button{min-height:40px;border:0;border-radius:7px;background:#922e2a;color:#fff;padding:0 14px;font-weight:700}@media(max-width:640px){.portfolio-admin__visibility{align-items:flex-start;flex-direction:column}.portfolio-admin__visibility-control{flex-wrap:wrap}}
</style>
<main class="portfolio-admin">
    <header class="portfolio-admin__head"><div><h1>Portofolio Ilena</h1><p>Atur proyek yang tampil di header menu Portofolio. Konten publik dibaca dari satu cache dan diperbarui setelah disimpan.</p></div><a class="portfolio-admin__public" href="/portfolio" target="_blank" rel="noopener">Lihat halaman publik</a></header>
    <form class="portfolio-admin__visibility" action="/admin/portfolio/visibility" method="post">
        <?= csrf_field(); ?>
        <div class="portfolio-admin__visibility-copy"><strong>Status halaman Portofolio</strong><span><?= !empty($portfolioPageEnabled) ? 'Halaman aktif. Jika belum ada proyek aktif, pengunjung melihat intro dan animasi “Segera Hadir”.' : 'Pengunjung melihat desain “Segera Hadir” sampai halaman diaktifkan.'; ?></span></div>
        <div class="portfolio-admin__visibility-control"><label><input type="checkbox" name="page_enabled" value="1" <?= !empty($portfolioPageEnabled) ? 'checked' : ''; ?>>Aktifkan halaman</label><button type="submit">Simpan status</button></div>
    </form>
    <?php if ($message = session()->getFlashdata('success')): ?><div class="portfolio-admin__notice" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if ($message = session()->getFlashdata('error')): ?><div class="portfolio-admin__notice" role="alert"><?= esc($message); ?></div><?php endif; ?>
    <div class="portfolio-admin__layout">
        <div class="portfolio-admin__editor">
        <form class="portfolio-admin__form" action="/admin/portfolio" method="post" data-portfolio-editor>
            <?= csrf_field(); ?><input type="hidden" name="id" value="<?= esc((string) ($project['id'] ?? '')); ?>">
            <h2><?= $project ? 'Edit proyek' : 'Tambah proyek'; ?></h2>
            <p class="portfolio-admin__hint">Gunakan teks biasa. Foto dan video sebaiknya diunggah ke CDN/object storage ILENA, bukan disimpan di database atau diproses tiap pengunjung.</p>
            <div class="portfolio-admin__two"><label>Judul — Indonesia<input name="title_id" maxlength="180" value="<?= esc($value('title_id')); ?>" required></label><label>Title — English<input name="title_en" maxlength="180" value="<?= esc($value('title_en')); ?>"></label></div>
            <label>Slug URL<input name="slug" maxlength="180" value="<?= esc($value('slug')); ?>" placeholder="otomatis-dari-judul"><small class="portfolio-admin__hint">Dibuat otomatis dari judul jika kosong.</small></label>
            <div class="portfolio-admin__two"><label>Kategori — Indonesia<input name="category_id" maxlength="100" value="<?= esc($value('category_id')); ?>" placeholder="Interior rumah"></label><label>Category — English<input name="category_en" maxlength="100" value="<?= esc($value('category_en')); ?>" placeholder="Residential interior"></label></div>
            <div class="portfolio-admin__two"><label>Ringkasan — Indonesia<textarea name="summary_id" maxlength="500"><?= esc($value('summary_id')); ?></textarea></label><label>Summary — English<textarea name="summary_en" maxlength="500"><?= esc($value('summary_en')); ?></textarea></label></div>
            <div class="portfolio-admin__two"><label>Cerita proyek — Indonesia<textarea name="description_id" maxlength="10000"><?= esc($value('description_id')); ?></textarea></label><label>Project story — English<textarea name="description_en" maxlength="10000"><?= esc($value('description_en')); ?></textarea></label></div>
            <div class="portfolio-admin__two"><label>Lokasi<input name="location" maxlength="180" value="<?= esc($value('location')); ?>"></label><label>Tahun selesai<input type="number" name="completed_year" min="1900" max="<?= date('Y') + 5; ?>" value="<?= esc((string) $value('completed_year')); ?>"></label></div>
            <label>URL foto utama<input type="url" name="cover_image" value="<?= esc($value('cover_image')); ?>" placeholder="https://img.ilenafurniture.com/...webp"><small class="portfolio-admin__hint">HTTPS, format WebP/AVIF/JPG/PNG atau file lokal /uploads/portfolio/.</small></label>
            <label>URL foto galeri <span class="portfolio-admin__hint">Satu URL per baris, maksimal 12.</span><textarea name="gallery_urls" rows="4" placeholder="https://img.ilenafurniture.com/...webp"><?= esc($galleryText); ?></textarea></label>
            <label>URL video MP4/WebM<input type="url" name="video_url" value="<?= esc($value('video_url')); ?>" placeholder="https://cdn.ilenafurniture.com/...mp4"><small class="portfolio-admin__hint">Video memakai preload=none agar tidak diunduh sebelum diputar.</small></label>
            <label>Urutan tampil<input type="number" name="sort_order" min="0" value="<?= esc((string) $value('sort_order', '0')); ?>"></label>
            <div class="portfolio-admin__checks"><label><input type="checkbox" name="featured" value="1" <?= (string) $value('featured', '0') === '1' ? 'checked' : ''; ?>>Jadikan proyek unggulan</label><label><input type="checkbox" name="active" value="1" <?= (string) $value('active', '0') === '1' ? 'checked' : ''; ?>>Tampilkan di website</label></div>
            <button class="portfolio-admin__submit" type="submit"><?= $project ? 'Simpan perubahan' : 'Tambah proyek'; ?></button>
            <?php if ($project): ?><a class="portfolio-admin__cancel" href="/admin/portfolio">Batalkan edit</a><?php endif; ?>
        </form>
        <aside class="portfolio-admin__preview" aria-label="Pratinjau langsung portofolio">
            <div class="portfolio-admin__preview-head"><div><span class="portfolio-admin__preview-kicker">PRATINJAU LANGSUNG</span><p>Perubahan terlihat di sini, tanpa menyimpan atau mengirim data ke server.</p></div><div class="portfolio-admin__preview-locale" role="group" aria-label="Bahasa pratinjau"><button type="button" data-preview-locale="id" aria-pressed="true">ID</button><button type="button" data-preview-locale="en" aria-pressed="false">EN</button></div></div>
            <div class="portfolio-admin__preview-frame">
                <div class="portfolio-admin__preview-feature">
                    <figure class="portfolio-admin__preview-media"><img data-preview-cover alt="Pratinjau foto sampul proyek" hidden><video data-preview-video controls playsinline preload="none" hidden></video><div class="portfolio-admin__preview-placeholder" data-preview-cover-placeholder><span class="portfolio-admin__preview-drawing" aria-hidden="true"><i></i><b></b><em></em></span><small data-preview-media-label>Foto/video proyek akan tampil di sini</small></div><figcaption data-preview-category>FURNITURE & INTERIOR</figcaption></figure>
                    <div class="portfolio-admin__preview-copy"><span class="portfolio-admin__preview-category" data-preview-category-copy>KATEGORI PROYEK</span><h2 data-preview-title>Judul proyek Anda</h2><p data-preview-summary>Ringkasan proyek akan tampil di sini setelah Anda mengisinya.</p><dl><div><dt data-preview-location-label>LOKASI</dt><dd data-preview-location>Kota / area</dd></div><div><dt data-preview-year-label>TAHUN</dt><dd data-preview-year>—</dd></div></dl><details data-preview-story-wrap><summary data-preview-story-label>Cerita proyek</summary><p data-preview-story>Deskripsi proyek akan tampil di sini.</p></details></div>
                </div>
                <section class="portfolio-admin__preview-gallery"><div class="portfolio-admin__preview-gallery-head"><div><span class="portfolio-admin__preview-kicker" data-preview-gallery-kicker>GALERI PROYEK</span><h3 data-preview-gallery-title>Proyek lainnya</h3></div><span data-preview-gallery-count>0 FOTO</span></div><div class="portfolio-admin__preview-gallery-grid" data-preview-gallery-grid><div class="portfolio-admin__preview-gallery-empty" data-preview-gallery-empty>Tambahkan URL foto galeri untuk melihat pratinjau di sini.</div></div></section>
                <div class="portfolio-admin__preview-foot"><span></span><small>ILENA / PORTFOLIO</small><span></span></div>
            </div>
        </aside>
        </div>
        <section class="portfolio-admin__list" aria-labelledby="portfolio-list-title"><div class="portfolio-admin__list-head"><h2 id="portfolio-list-title">Daftar proyek</h2><span><?= count($projects ?? []); ?> di halaman ini</span></div>
            <?php if (empty($projects)): ?><p class="portfolio-admin__empty">Belum ada proyek. Tambahkan entri, lalu aktifkan saat konten foto dan informasinya siap.</p><?php endif; ?>
            <?php foreach (($projects ?? []) as $item): ?><article class="portfolio-admin__row">
                <?php if (!empty($item['cover_image'])): ?><img class="portfolio-admin__thumb" src="<?= esc($item['cover_image'], 'attr'); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><div class="portfolio-admin__thumb portfolio-admin__thumb--empty">ILENA</div><?php endif; ?>
                <div><h3><?= esc($item['title_id']); ?><?= !empty($item['featured']) ? ' · Unggulan' : ''; ?></h3><p><?= esc($item['category_id'] ?: 'Belum ada kategori'); ?><?= !empty($item['location']) ? ' · ' . esc($item['location']) : ''; ?></p><span class="portfolio-admin__status <?= empty($item['active']) ? 'portfolio-admin__status--off' : ''; ?>"><?= empty($item['active']) ? 'Disembunyikan' : 'Tampil'; ?></span></div>
                <div class="portfolio-admin__actions"><a href="/admin/portfolio?edit=<?= (int) $item['id']; ?>">Edit</a><form action="/admin/portfolio/delete/<?= (int) $item['id']; ?>" method="post" data-ilena-confirm="Hapus proyek <?= esc($item['title_id'], 'attr'); ?>?" data-ilena-confirm-title="Hapus proyek portofolio"><?= csrf_field(); ?><button type="submit">Hapus</button></form></div>
            </article><?php endforeach; ?>
            <?= isset($pager) ? $pager->links('portfolio') : ''; ?>
        </section>
    </div>
</main>
<style>
.portfolio-admin__preview{position:sticky;top:18px;min-width:0}.portfolio-admin__preview-head{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:12px}.portfolio-admin__preview-head p{font-size:11px;line-height:1.45;color:#625a54;margin:5px 0 0}.portfolio-admin__preview-kicker{font-size:9px;letter-spacing:.13em;font-weight:800;color:#922e2a}.portfolio-admin__preview-locale{display:flex;gap:3px;padding:3px;background:#eee9e4}.portfolio-admin__preview-locale button{border:0;background:transparent;color:#514943;min-width:34px;padding:6px 8px;font-size:10px;font-weight:750;cursor:pointer}.portfolio-admin__preview-locale button[aria-pressed="true"]{background:#fff;color:#922e2a;box-shadow:0 1px 3px #211f1e20}.portfolio-admin__preview-frame{max-height:calc(100vh - 130px);overflow:auto;background:#fff;border:1px solid var(--pa-line);padding:clamp(14px,2vw,24px);scrollbar-color:#bcb1a9 transparent}.portfolio-admin__preview-feature{display:grid;grid-template-columns:minmax(0,1.12fr) minmax(150px,.88fr);gap:17px;align-items:center}.portfolio-admin__preview-media{aspect-ratio:1.25;position:relative;margin:0;overflow:hidden;background:#eeeae6}.portfolio-admin__preview-media>img,.portfolio-admin__preview-media>video{display:block;width:100%;height:100%;object-fit:cover}.portfolio-admin__preview-media>img[hidden],.portfolio-admin__preview-media>video[hidden]{display:none}.portfolio-admin__preview-placeholder{position:absolute;inset:0;display:grid;place-items:center;align-content:center;gap:10px;background:#eeeae6;color:#625a54}.portfolio-admin__preview-placeholder[hidden]{display:none}.portfolio-admin__preview-placeholder small{font-size:9px;text-align:center;padding:0 10px}.portfolio-admin__preview-drawing{width:55%;height:42%;position:relative;border:1px solid #b9aea5;transform:perspective(220px) rotateY(-8deg)}.portfolio-admin__preview-drawing:before{content:"";position:absolute;left:12%;right:12%;bottom:22%;height:36%;border:1.5px solid #514842;border-radius:8px 8px 2px 2px}.portfolio-admin__preview-drawing:after{content:"";position:absolute;left:18%;right:18%;bottom:13%;height:7%;border-top:1px solid #514842;border-bottom:1px solid #514842}.portfolio-admin__preview-drawing i{position:absolute;left:50%;top:17%;height:38%;width:1px;background:#922e2a}.portfolio-admin__preview-drawing b,.portfolio-admin__preview-drawing em{position:absolute;top:8%;width:1px;height:28%;background:#9a8f86}.portfolio-admin__preview-drawing b{left:27%}.portfolio-admin__preview-drawing em{right:27%}.portfolio-admin__preview-media figcaption{position:absolute;bottom:8px;left:8px;background:#211f1ed9;color:#fff;padding:5px 7px;font-size:7px;letter-spacing:.09em}.portfolio-admin__preview-copy{min-width:0}.portfolio-admin__preview-category{font-size:8px;letter-spacing:.11em;font-weight:750;color:#922e2a}.portfolio-admin__preview-copy h2{font-size:clamp(20px,2vw,29px);line-height:1.1;letter-spacing:-.035em;font-weight:550;margin:8px 0}.portfolio-admin__preview-copy>p,.portfolio-admin__preview-copy details p{font-size:10px;line-height:1.55;color:#625a54;margin:0}.portfolio-admin__preview-copy dl{display:flex;gap:18px;margin:12px 0}.portfolio-admin__preview-copy dl div{min-width:0}.portfolio-admin__preview-copy dt{font-size:7px;letter-spacing:.08em;color:#625a54}.portfolio-admin__preview-copy dd{font-size:9px;margin:3px 0 0;overflow-wrap:anywhere}.portfolio-admin__preview-copy details{border-top:1px solid var(--pa-line);padding-top:7px}.portfolio-admin__preview-copy summary{font-size:9px;font-weight:700;cursor:pointer}.portfolio-admin__preview-copy details p{padding-top:6px}.portfolio-admin__preview-gallery{padding-top:18px;margin-top:18px;border-top:1px solid var(--pa-line)}.portfolio-admin__preview-gallery-head{display:flex;justify-content:space-between;align-items:end;gap:10px;margin-bottom:10px}.portfolio-admin__preview-gallery-head h3{font-size:16px;font-weight:550;margin:4px 0 0}.portfolio-admin__preview-gallery-head>span{font-size:7px;letter-spacing:.1em;color:#625a54}.portfolio-admin__preview-gallery-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px}.portfolio-admin__preview-gallery-grid img{width:100%;aspect-ratio:1.15;object-fit:cover;background:#eeeae6}.portfolio-admin__preview-gallery-empty{grid-column:1/-1;padding:22px 12px;border:1px dashed #b7aaa1;color:#625a54;text-align:center;font-size:9px}.portfolio-admin__preview-foot{display:flex;align-items:center;justify-content:center;gap:9px;padding-top:17px;color:#625a54}.portfolio-admin__preview-foot span{width:20px;height:1px;background:#c9c0b9}.portfolio-admin__preview-foot small{font-size:7px;letter-spacing:.13em}.portfolio-admin__preview :focus-visible{outline:2px solid #922e2a;outline-offset:3px}@media(max-width:900px){.portfolio-admin__preview{order:-1}.portfolio-admin__preview-frame{max-height:none}.portfolio-admin__preview-feature{grid-template-columns:minmax(0,1fr) minmax(160px,.8fr)}}@media(max-width:560px){.portfolio-admin__preview-feature{grid-template-columns:1fr}.portfolio-admin__preview-media{aspect-ratio:1.5}.portfolio-admin__preview-copy h2{font-size:25px}.portfolio-admin__preview-frame{padding:12px}.portfolio-admin__preview-gallery-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
</style>
<script>
(function () {
    var form = document.querySelector('[data-portfolio-editor]');
    if (!form) return;
    var fields = function (name) { return form.querySelector('[name="' + name + '"]'); };
    var preview = function (name) { return document.querySelector('[data-preview-' + name + ']'); };
    var locale = 'id';
    var lastGalleryKey = null;
    var safeMedia = function (raw, video) {
        var value = String(raw || '').trim();
        if (/^\/uploads\/portfolio\/(?!.*\.\.\/)[\w./-]+\.(?:webp|avif|jpe?g|png|mp4|webm)$/i.test(value)) return video ? (/\.(?:mp4|webm)$/i.test(value) ? value : '') : (/\.(?:webp|avif|jpe?g|png)$/i.test(value) ? value : '');
        try {
            var url = new URL(value);
            if (url.protocol !== 'https:') return '';
            return (video ? /\.(?:mp4|webm)$/i : /\.(?:webp|avif|jpe?g|png)$/i).test(url.pathname) ? value : '';
        } catch (e) { return ''; }
    };
    var text = function (target, value, fallback) { if (target) target.textContent = value || fallback; };
    var update = function () {
        var en = locale === 'en';
        var title = fields(en ? 'title_en' : 'title_id').value.trim() || fields('title_id').value.trim();
        var category = fields(en ? 'category_en' : 'category_id').value.trim() || fields('category_id').value.trim();
        var summary = fields(en ? 'summary_en' : 'summary_id').value.trim() || fields('summary_id').value.trim();
        var story = fields(en ? 'description_en' : 'description_id').value.trim() || fields('description_id').value.trim();
        text(preview('title'), title, en ? 'Your project title' : 'Judul proyek Anda');
        text(preview('category-copy'), category, en ? 'PROJECT CATEGORY' : 'KATEGORI PROYEK');
        text(preview('category'), category, en ? 'FURNITURE & INTERIOR' : 'FURNITURE & INTERIOR');
        text(preview('summary'), summary, en ? 'A short project introduction will appear here.' : 'Ringkasan proyek akan tampil di sini setelah Anda mengisinya.');
        text(preview('location'), fields('location').value.trim(), en ? 'City / area' : 'Kota / area');
        text(preview('year'), fields('completed_year').value.trim(), '—');
        text(preview('story'), story, en ? 'The project story will appear here.' : 'Deskripsi proyek akan tampil di sini.');
        text(preview('location-label'), en ? 'LOCATION' : 'LOKASI', 'LOKASI');
        text(preview('year-label'), en ? 'YEAR' : 'TAHUN', 'TAHUN');
        text(preview('story-label'), en ? 'Project story' : 'Cerita proyek', 'Cerita proyek');
        text(preview('gallery-kicker'), en ? 'PROJECT GALLERY' : 'GALERI PROYEK', 'GALERI PROYEK');
        text(preview('gallery-title'), en ? 'More from this project' : 'Galeri proyek', 'Galeri proyek');
        var mediaLabel = preview('media-label');
        var cover = safeMedia(fields('cover_image').value, false);
        var videoUrl = safeMedia(fields('video_url').value, true);
        var imageNode = preview('cover');
        var videoNode = preview('video');
        var placeholder = preview('cover-placeholder');
        imageNode.hidden = !cover || !!videoUrl;
        if (cover && imageNode.src !== new URL(cover, location.origin).href) imageNode.src = cover;
        videoNode.hidden = !videoUrl;
        if (videoUrl) {
            var source = videoNode.querySelector('source');
            if (!source) { source = document.createElement('source'); videoNode.appendChild(source); }
            if (source.src !== new URL(videoUrl, location.origin).href) { source.src = videoUrl; videoNode.load(); }
            videoNode.poster = cover || '';
        } else { videoNode.pause(); }
        placeholder.hidden = !!cover || !!videoUrl;
        if (mediaLabel) mediaLabel.textContent = en ? 'Project cover photo or video will appear here' : 'Foto/video proyek akan tampil di sini';
        var rawGallery = fields('gallery_urls').value.split(/\r?\n/).map(function (item) { return safeMedia(item, false); }).filter(Boolean).slice(0, 12);
        var galleryKey = rawGallery.join('\n');
        if (galleryKey !== lastGalleryKey) {
            lastGalleryKey = galleryKey;
            var grid = preview('gallery-grid');
            var empty = preview('gallery-empty');
            grid.querySelectorAll('img').forEach(function (node) { node.remove(); });
            empty.hidden = rawGallery.length > 0;
            if (!rawGallery.length) empty.textContent = en ? 'Add gallery image URLs to preview them here.' : 'Tambahkan URL foto galeri untuk melihat pratinjau di sini.';
            rawGallery.forEach(function (url, index) { var image = document.createElement('img'); image.src = url; image.alt = (title || (en ? 'Project' : 'Proyek')) + ' — ' + (en ? 'gallery image ' : 'foto galeri ') + (index + 1); image.loading = 'lazy'; image.decoding = 'async'; grid.appendChild(image); });
        } else {
            preview('gallery-grid').querySelectorAll('img').forEach(function (image, index) { image.alt = (title || (en ? 'Project' : 'Proyek')) + ' — ' + (en ? 'gallery image ' : 'foto galeri ') + (index + 1); });
        }
        text(preview('gallery-count'), rawGallery.length + (en ? ' PHOTOS' : ' FOTO'), en ? '0 PHOTOS' : '0 FOTO');
    };
    form.addEventListener('input', update);
    document.querySelectorAll('[data-preview-locale]').forEach(function (button) {
        button.addEventListener('click', function () {
            locale = button.getAttribute('data-preview-locale') === 'en' ? 'en' : 'id';
            document.querySelectorAll('[data-preview-locale]').forEach(function (item) { item.setAttribute('aria-pressed', item === button ? 'true' : 'false'); });
            update();
        });
    });
    update();
})();
</script>
<?= $this->endSection(); ?>
