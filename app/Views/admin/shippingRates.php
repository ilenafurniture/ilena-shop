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
.apm-rates__guide { margin:22px 0; overflow-x:auto; border-top:1px solid #dfe1e2; border-bottom:1px solid #dfe1e2; }
.apm-rates__guide table { width:100%; min-width:760px; border-collapse:collapse; font-size:13px; }
.apm-rates__guide caption { padding:12px 0; color:#292c2e; font-size:15px; font-weight:700; text-align:left; }
.apm-rates__guide th, .apm-rates__guide td { padding:10px 12px; border-top:1px solid #e7e8e9; text-align:left; vertical-align:top; }
.apm-rates__guide th { color:#383d40; background:#f5f4f1; font-weight:700; }
.apm-rates__guide td:first-child { min-width:205px; color:#6f2425; font-weight:650; }
.apm-rates__guide td:nth-child(2) { min-width:285px; }
.apm-rates__tip { padding:12px 14px; border-left:3px solid #8e2427; background:#f7f4f0; color:#393633 !important; }
.apm-rates__upload form { max-width:760px; padding-top:16px; border-top:1px solid #e4e6e7; }
.apm-rates__upload input[type=file]:focus-visible, .apm-rates__button:focus-visible { outline:3px solid #8e2427; outline-offset:3px; }
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
        <p>Setiap baris CSV mewakili satu tujuan tarif. Untuk memperbarui daftar yang sedang dipakai, unduh tarif aktif, ubah data yang diperlukan, lalu unggah kembali seluruh file.</p>
        <div class="apm-rates__actions mb-3">
            <a class="apm-rates__button" href="/admin/shipping-rates/export"><span class="material-icons" aria-hidden="true">download</span>Unduh data aktif untuk diperbarui</a>
            <a class="apm-rates__button" href="/admin/shipping-rates/template"><span class="material-icons" aria-hidden="true">description</span>Unduh template kosong</a>
        </div>
        <div class="apm-rates__guide" role="region" aria-label="Panduan kolom CSV tarif APM" tabindex="0">
            <table>
                <caption>Arti kolom pada file CSV</caption>
                <thead><tr><th scope="col">Nama kolom</th><th scope="col">Yang perlu diisi</th><th scope="col">Contoh</th></tr></thead>
                <tbody>
                    <tr><td>ID Tarif</td><td>Biarkan ID dari file tetap unik. Jangan mengubah atau menyalin ID yang sama.</td><td>APM-001</td></tr>
                    <tr><td>Regional APM</td><td>Nama cakupan regional pada daftar APM.</td><td>Jawa Barat / Banten / DKI</td></tr>
                    <tr><td>Area APM</td><td>Area/cakupan tarif yang tercantum oleh APM.</td><td>JAWA BARAT</td></tr>
                    <tr><td>Tujuan (Kabupaten/Kota)</td><td>Nama tujuan persis seperti pada data tarif APM.</td><td>Bandung</td></tr>
                    <tr><td>Tarif per kg (Rp)</td><td>Angka rupiah saja, tanpa “Rp”, titik, atau pemisah ribuan.</td><td>3500</td></tr>
                    <tr><td>Minimum berat tagih (kg)</td><td>Berat minimum yang dikenai tarif. Isi angka bulat.</td><td>30</td></tr>
                    <tr><td>Estimasi min (hari) / Estimasi maks (hari)</td><td>Rentang estimasi hari. Nilai minimum tidak boleh melebihi maksimum.</td><td>2 / 3</td></tr>
                    <tr><td>Jadwal layanan</td><td>Jadwal khusus bila tercantum; boleh dikosongkan jika tidak ada.</td><td>Senin-Jumat</td></tr>
                    <tr><td>Status data APM</td><td>Catatan sumber/anomali dari data APM. Jangan menghapus tanda anomali yang masih perlu dicek.</td><td>DARI SCAN</td></tr>
                </tbody>
            </table>
        </div>
        <p class="apm-rates__tip"><strong>Perhatian:</strong> unggahan mengganti seluruh dataset tarif aktif, bukan hanya baris yang diedit. File harus berformat CSV; pemisah koma maupun titik koma diterima. Dataset lama otomatis disimpan untuk pemulihan. Tarif ambigu atau bertanda anomali tetap dialihkan ke CS, bukan dijumlahkan.</p>
        <ol class="apm-rates__steps">
            <li>Gunakan satu ID unik untuk setiap baris dan jangan mengubah ID tarif yang sudah ada.</li>
            <li>Isi tarif dan berat dengan angka bulat tanpa simbol mata uang atau titik pemisah.</li>
            <li>Aplikasi menghitung tarif memakai berat aktual produk atau minimum berat pada tarif, mana yang lebih besar. Berat volume tidak dihitung otomatis.</li>
        </ol>
        <form action="/admin/shipping-rates/import" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <label for="apm-rates-file">Pilih file CSV lengkap (maksimal 1 MB)</label>
            <input id="apm-rates-file" name="rates_file" type="file" accept=".csv,text/csv" required>
            <button class="apm-rates__button apm-rates__button--primary mt-3" type="submit"><span class="material-icons" aria-hidden="true">upload</span>Validasi lalu terapkan semua tarif</button>
        </form>
        <p class="small mt-3 mb-0">Perubahan tarif tidak mengubah pengaturan Gratis Ongkir. Promo tetap menghitung batas wilayah, minimum belanja, serta batas subsidi sesuai konfigurasi admin.</p>
    </section>
</main>
<?= $this->endSection(); ?>
