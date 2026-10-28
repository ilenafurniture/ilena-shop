<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<main class="ilena-vision-page">
    <div class="ilena-vision-shell">
        <nav class="ilena-vision-breadcrumb" aria-label="breadcrumb">
            <a href="/">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Visi, misi &amp; nilai</span>
        </nav>

        <section class="ilena-vision-hero" aria-labelledby="ilena-vision-title">
            <p class="ilena-vision-eyebrow" data-reveal>ILENA FURNITURE <span></span> NILAI KAMI</p>
            <h1 id="ilena-vision-title" data-reveal>Mulai dari <em>rumah,</em><br>tercipta senyum ramah.</h1>
            <div class="ilena-vision-hero-foot" data-reveal>
                <span class="ilena-vision-index">01 <i></i> VISI</span>
                <p>Ilena hadir di tengah-tengah masyarakat Indonesia dengan mengusung visi: mulai dari rumah, tercipta senyum ramah. Satu kalimat ini menjadi representasi bahwa Ilena dengan tangan terbuka akan selalu ada bagi masyarakat Indonesia melalui produk unggulan yang jadi solusi tepat melengkapi interior jadi lebih sempurna.</p>
            </div>
            <span class="ilena-vision-orbit ilena-vision-orbit--one" aria-hidden="true"></span>
            <span class="ilena-vision-orbit ilena-vision-orbit--two" aria-hidden="true"></span>
        </section>

        <section class="ilena-values-section" aria-labelledby="ilena-values-title">
            <div class="ilena-vision-section-heading" data-reveal>
                <span class="ilena-vision-index">02 <i></i> BUDAYA</span>
                <div>
                    <h2 id="ilena-values-title">Nilai budaya <span>RAMAH</span></h2>
                    <p>Kami berkomitmen untuk terus berkembang dan berinovasi dalam menghasilkan produk unggulan dengan penuh RAMAH, sebagai pilar utama yang tersampaikan pada rangkaian berikut ini:</p>
                </div>
            </div>

            <div class="ilena-values-layout">
                <figure class="ilena-values-art" data-reveal>
                    <img src="<?= base_url('img/Ramah Ilena/Ramah Ilena Full.png'); ?>" alt="Ilustrasi rumah dan nilai budaya RAMAH Ilena" loading="lazy" decoding="async">
                    <figcaption>Nilai yang menumbuhkan rasa nyaman, mulai dari rumah.</figcaption>
                </figure>
                <ol class="ilena-values-list">
                    <li data-reveal><span class="ilena-value-initial">R</span><div><h3>Ragam</h3><p>Toleransi terhadap segala bentuk perbedaan dan menjadikannya sebagai semangat dalam mencapai tujuan bersama.</p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">A</span><div><h3>Amanah</h3><p>Menjaga kualitas produk dan pelayanan terhadap konsumen secara maksimal.</p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">M</span><div><h3>Modern</h3><p>Tertuju pada modernisasi dengan menciptakan efisiensi terhadap produk.</p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">A</span><div><h3>Adaptif</h3><p>Terbuka dengan perkembangan zaman dan teknologi yang mempengaruhi perubahan kebutuhan serta keinginan manusia.</p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">H</span><div><h3>Harmonis</h3><p>Menciptakan relasi yang sehat antara karyawan dan perusahaan sebagai satu kesatuan yang tidak bisa terpisahkan.</p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                </ol>
            </div>
        </section>

        <section class="ilena-mission-section" aria-labelledby="ilena-mission-title">
            <div class="ilena-vision-section-heading" data-reveal>
                <span class="ilena-vision-index">03 <i></i> ARAH KAMI</span>
                <div>
                    <h2 id="ilena-mission-title">Misi <span>yang kami jaga.</span></h2>
                    <p>Tiga komitmen yang menjadi arah dalam melayani dan bertumbuh bersama.</p>
                </div>
            </div>
            <ol class="ilena-mission-list">
                <li data-reveal><span class="ilena-mission-mark" aria-hidden="true">01</span><div><h3>Membangun &amp; Menjaga Kepercayaan Konsumen</h3><p>Menjadikan kepuasan konsumen sebagai prioritas melalui pelayanan prima dan produk unggulan.</p></div></li>
                    <li data-reveal><span class="ilena-mission-mark" aria-hidden="true">02</span><div><h3>Mendukung Keseimbangan Ekosistem Lingkungan</h3><p>Menjaga lingkungan dan kelanjutan ekosistem hutan dengan hanya menggunakan material bahan yang jelas asal-usulnya.</p></div></li>
                <li data-reveal><span class="ilena-mission-mark" aria-hidden="true">03</span><div><h3>Memiliki Satu Tujuan Bersama</h3><p>Membangun tim yang solid dan bersinergi sesuai dengan nilai perusahaan untuk terus berkembang dan mencapai satu tujuan bersama.</p></div></li>
            </ol>
        </section>
    </div>
</main>
<script>
(() => {
    const page = document.querySelector('.ilena-vision-page');
    const items = page?.querySelectorAll('[data-reveal]');
    if (!page || !items?.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    page.classList.add('ilena-motion-ready');
    const observer = new IntersectionObserver((entries, activeObserver) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            activeObserver.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -32px 0px' });
    items.forEach((item) => observer.observe(item));
})();
</script>
<?= $this->endSection(); ?>
