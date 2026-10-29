<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="ilena-editorial ilena-about" aria-labelledby="about-title">
    <nav class="ilena-editorial__breadcrumb" aria-label="Breadcrumb">
        <a href="/">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Tentang kami</span>
    </nav>

    <header class="ilena-editorial__hero">
        <div class="ilena-about__hero-copy">
            <p class="ilena-editorial__eyebrow">Cerita Ilena</p>
            <h1 id="about-title">Furniture untuk ruang<br>yang terasa seperti rumah.</h1>
            <p class="ilena-editorial__intro">Berangkat dari pengalaman panjang dalam manufaktur, Ilena hadir untuk membawa furniture yang fungsional, berkualitas, dan dekat dengan kehidupan sehari-hari.</p>
            <div class="ilena-about__vision" aria-label="Visi Ilena: Mulai dari rumah, tercipta senyum ramah.">
                <svg class="ilena-about__vision-mark" viewBox="0 0 96 56" aria-hidden="true" focusable="false">
                    <path class="vision-mark-stroke" pathLength="1" d="M5 27 23 10l18 17M10 23v25h26V23M19 48V34h9v14" />
                    <path class="vision-mark-stroke vision-mark-connect" pathLength="1" d="M42 31h13" />
                    <circle class="vision-mark-stroke vision-mark-face" cx="73" cy="29" r="17" pathLength="1" />
                    <path class="vision-mark-stroke vision-mark-smile" pathLength="1" d="M65 32q8 9 16 0" />
                    <circle class="vision-mark-dot" cx="49" cy="31" r="2.2" />
                </svg>
                <p>Mulai dari rumah,<br><em>tercipta senyum ramah.</em></p>
            </div>
        </div>
        <figure class="ilena-about__hero-visual ilena-about__image">
            <img src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="Furniture Ilena dalam ruang bergaya urban" width="960" height="640" fetchpriority="high">
            <svg class="ilena-about-motion-art" viewBox="0 0 520 320" aria-hidden="true" focusable="false">
                <circle class="about-motion-orbit-line" cx="260" cy="164" r="148" />
                <path class="about-motion-path" pathLength="1" d="M91 190v-48c0-19 15-34 34-34h270c19 0 34 15 34 34v48M70 183h380v53H70zM91 236l-9 28m347-28 9 28" />
                <path class="about-motion-path about-motion-detail" pathLength="1" d="M111 183v-37c0-11 9-20 20-20h112v57m22-57h112c11 0 20 9 20 20v37m-152-57v57m-115 20h304" />
                <path class="about-motion-path about-motion-measure" pathLength="1" d="M91 78h338m-338-8v16m338-16v16" />
                <g class="about-motion-orbit-marker"><circle cx="260" cy="16" r="4" /><circle cx="260" cy="16" r="8" /></g>
            </svg>
            <figcaption class="ilena-about__hero-caption">Ruang yang nyaman dimulai dari rumah.</figcaption>
        </figure>
    </header>

    <section class="ilena-about__story" aria-labelledby="about-story-title">
        <div class="ilena-about__copy">
            <p class="ilena-editorial__eyebrow">Dirancang untuk keseharian</p>
            <h2 id="about-story-title">Dirancang untuk kehidupan modern.</h2>
            <p>Ilena lahir pada 2024 di bawah naungan CV Catur Bhakti Mandiri, perusahaan dengan pengalaman sekitar 30 tahun di bidang furniture. Kami mengembangkan bisnis ritel dan interior dengan memahami kebutuhan ruang dan kebiasaan masyarakat urban.</p>
            <p>Setiap ruang punya ceritanya sendiri. Kami ingin membantu Anda membentuk ruang yang nyaman, fungsional, dan mencerminkan siapa Anda.</p>
        </div>
        <aside class="ilena-about__experience" aria-label="Sekitar 30 tahun pengalaman manufaktur">
            <span aria-hidden="true">30</span>
            <p><strong>Tahun pengalaman</strong><br>menjadi landasan Ilena dalam memahami furniture.</p>
        </aside>
    </section>

    <section class="ilena-about__profile" aria-labelledby="about-profile-title">
        <div>
            <p class="ilena-editorial__eyebrow">Pengalaman manufaktur</p>
            <h2 id="about-profile-title">Berpengalaman. Bertumbuh dengan konsisten.</h2>
            <p>Berbasis di Semarang, CV Catur Bhakti Mandiri memproduksi furniture untuk kebutuhan rumah tangga, perkantoran, dan perhotelan. Pengalaman tersebut menjadi landasan Ilena dalam menjaga kualitas produk dan menghadirkan pilihan yang relevan untuk rumah modern.</p>
            <a class="ilena-editorial__text-link" href="/product">Jelajahi koleksi <span aria-hidden="true">→</span></a>
        </div>
        <figure class="ilena-about__profile-image">
            <img src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="Proses dan produk furniture Ilena" width="960" height="640" loading="lazy" decoding="async">
        </figure>
    </section>

    <section class="ilena-about__clients" aria-labelledby="about-clients-title">
        <div class="ilena-editorial__section-heading">
            <p class="ilena-editorial__eyebrow">Kemitraan pilihan</p>
            <h2 id="about-clients-title">Dipercaya oleh</h2>
        </div>
        <ul class="ilena-about__client-logos" aria-label="Daftar klien Ilena">
            <li>The Land of Nod</li>
            <li>Crate &amp; Barrel</li>
            <li>West Elm</li>
            <li>Williams Sonoma</li>
        </ul>
    </section>

    <section class="ilena-about__contact" aria-labelledby="about-contact-title">
        <div>
            <p class="ilena-editorial__eyebrow">Kami siap membantu</p>
            <h2 id="about-contact-title">Mari terhubung.</h2>
            <p>Hubungi tim Ilena untuk pertanyaan seputar produk, pemesanan, atau kerja sama.</p>
            <div class="ilena-about__contact-actions">
                <a class="ilena-about__contact-primary" href="https://wa.me/628112938158" target="_blank" rel="noopener noreferrer">Hubungi melalui WhatsApp <span aria-hidden="true">↗</span></a>
                <a class="ilena-about__contact-secondary" href="mailto:cs@ilenafurniture.com">cs@ilenafurniture.com</a>
            </div>
        </div>
        <address>
            <strong>Ilena Furniture</strong>
            <span>Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa Tengah</span>
            <a href="https://www.google.com/maps/search/?api=1&query=CV+Catur+Bhakti+Mandiri+Semarang" target="_blank" rel="noopener noreferrer">Buka lokasi di Google Maps <span aria-hidden="true">↗</span></a>
        </address>
    </section>
</main>
<script>
(() => {
    const visual = document.querySelector('.ilena-about__image');
    if (!visual || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    visual.classList.add('ilena-about-motion-ready');
    const observer = new IntersectionObserver((entries, activeObserver) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                visual.classList.add('ilena-about-motion-active');
                visual.classList.add('ilena-about-motion-visible');
            } else {
                visual.classList.remove('ilena-about-motion-active');
            }
        });
    }, { threshold: 0.18 });
    observer.observe(visual);
})();
</script>
<?= $this->endSection(); ?>
