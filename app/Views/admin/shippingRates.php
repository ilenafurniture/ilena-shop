<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<?php $summary = $summary ?? ['rows' => 0, 'updated_at' => null, 'source' => 'APM', 'customized' => false]; ?>
<style>
.apm-rates { max-width: 1120px; margin: 0 auto; color: #202124; }
.apm-rates__head { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; margin-bottom:26px; }
.apm-rates h1 { margin:0 0 6px; font-size:clamp(24px,3vw,32px); letter-spacing:-.04em; font-weight:750; }
.apm-rates p { color:#687078; line-height:1.6; }
.apm-rates__actions { display:flex; flex-wrap:wrap; gap:10px; }
.apm-rates__button { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:42px; padding:9px 14px; border:1px solid #d7dadd; border-radius:8px; color:#25292d; background:#fff; text-decoration:none; font-weight:650; }
.apm-rates__button--primary { background:#272727; color:#fff; border-color:#272727; }
.apm-rates__notice { padding:13px 15px; margin:0 0 18px; border-left:3px solid #a3262d; background:#fff5f4; color:#542326; }
.apm-rates__notice--success { border-color:#54745d; background:#f2f8f3; color:#304b37; }
.apm-rates__summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; padding:18px 0; margin:0 0 24px; border-top:1px solid #e4e6e7; border-bottom:1px solid #e4e6e7; }
.apm-rates__summary strong { display:block; font-size:20px; }
.apm-rates__summary span { color:#727980; font-size:13px; }
.apm-rates__upload { max-width:760px; }
.apm-rates__upload label { display:block; margin:14px 0 7px; font-weight:650; }
.apm-rates__upload input[type=file] { width:100%; padding:12px; border:1px solid #d7dadd; border-radius:8px; background:#fff; }
.apm-rates__steps { padding-left:20px; color:#555e65; line-height:1.8; }
@media(max-width:700px) { .apm-rates__head { flex-direction:column; } .apm-rates__summary { grid-template-columns:1fr; gap:12px; } .apm-rates__actions { width:100%; } .apm-rates__button { flex:1; } }
</style>

<main class="apm-rates">
    <header class="apm-rates__head">
        <div>
            <h1>Tarif Pengiriman APM</h1>
            <p class="mb-0">Tarif checkout dibaca dari satu daftar APM lokal. Pengaturan Gratis Ongkir tetap terpisah dan tetap dapat diubah di halaman khususnya.</p>
        </div>
        <div class="apm-rates__actions">
            <a class="apm-rates__button" href="/admin/free-shipping"><span class="material-icons" aria-hidden="true">local_offer</span>Pengaturan Gratis Ongkir</a>
            <a class="apm-rates__button" href="/admin/shipping-rates/export"><span class="material-icons" aria-hidden="true">download</span>Unduh Tarif Aktif</a>
        </div>
    </header>

    <?php if (!empty($error)): ?><div class="apm-rates__notice" role="alert"><?= esc($error); ?></div><?php endif; ?>
    <?php if (!empty($msg)): ?><div class="apm-rates__notice apm-rates__notice--success" role="status"><?= esc($msg); ?></div><?php endif; ?>

    <section class="apm-rates__summary" aria-label="Status tarif aktif">
        <div><strong><?= number_format((int) $summary['rows'], 0, ',', '.'); ?></strong><span>tujuan dalam dataset aktif</span></div>
        <div><strong><?= !empty($summary['customized']) ? 'Sudah diperbarui' : 'Dataset awal'; ?></strong><span><?= esc((string) $summary['source']); ?></span></div>
        <div><strong><?= esc((string) ($summary['updated_at'] ?: 'Belum ada impor admin')); ?></strong><span>pembaruan terakhir</span></div>
    </section>

    <?php if (!empty($summary['has_previous'])): ?>
        <form action="/admin/shipping-rates/restore" method="post" class="mb-4" data-ilena-confirm="Pulihkan versi tarif sebelumnya? Tarif aktif saat ini akan tersimpan sebagai versi pemulihan berikutnya." data-ilena-confirm-title="Pulihkan tarif APM" data-ilena-confirm-text="Ya, pulihkan">
            <?= csrf_field(); ?>
            <button class="apm-rates__button" type="submit"><span class="material-icons" aria-hidden="true">history</span>Pulihkan versi sebelumnya</button>
        </form>
    <?php endif; ?>

    <section class="apm-rates__upload">
        <h2 class="h5 fw-bold">Perbarui tarif dari APM</h2>
        <p>Unduh daftar aktif, perbarui nominal dan ketentuan dari informasi APM terbaru, lalu unggah kembali sebagai satu file CSV lengkap. Dataset lama hanya diganti setelah seluruh file lolos validasi.</p>
        <ol class="apm-rates__steps">
            <li>Unduh <a href="/admin/shipping-rates/export">tarif aktif</a> untuk mengedit daftar yang sedang dipakai, atau <a href="/admin/shipping-rates/template">template CSV</a> untuk menyiapkan dataset baru.</li>
            <li>Pertahankan ID unik setiap baris. Jangan menggandakan ID. Nama tujuan yang sama boleh ada jika memang wilayahnya berbeda.</li>
            <li>Isi biaya per kg, minimum berat, dan estimasi hari. Aplikasi memakai berat aktual produk dan minimum pada tarif; daftar APM ini tidak menetapkan rumus berat volume otomatis.</li>
            <li>Untuk tujuan ganda yang masih ambigu atau ditandai anomali, checkout meminta pelanggan menghubungi CS—tarifnya tidak dijumlahkan.</li>
            <li>Unggah CSV lengkap. Jika valid, tarif aktif diperbarui; salinan sebelumnya disimpan sebagai pemulihan.</li>
        </ol>
        <form action="/admin/shipping-rates/import" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <label for="apm-rates-file">File CSV tarif lengkap (maksimal 1 MB)</label>
            <input id="apm-rates-file" name="rates_file" type="file" accept=".csv,text/csv" required>
            <button class="apm-rates__button apm-rates__button--primary mt-3" type="submit"><span class="material-icons" aria-hidden="true">upload</span>Validasi dan Terapkan Tarif</button>
        </form>
        <p class="small mt-3 mb-0">Perubahan tarif tidak mengubah pengaturan Gratis Ongkir. Promo tetap menghitung batas wilayah, minimum belanja, serta batas subsidi sesuai konfigurasi admin.</p>
    </section>
</main>
<?= $this->endSection(); ?>
