<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="ilena-editorial ilena-about" aria-labelledby="about-title">
    <nav class="ilena-editorial__breadcrumb" aria-label="Breadcrumb">
        <a href="/">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Tentang kami</span>
    </nav>

    <header class="ilena-editorial__hero">
        <p class="ilena-editorial__eyebrow">Cerita Ilena</p>
        <h1 id="about-title">Furniture untuk ruang<br>yang terasa seperti rumah.</h1>
        <p class="ilena-editorial__intro">Berangkat dari pengalaman panjang dalam manufaktur, Ilena hadir untuk membawa furniture yang fungsional, berkualitas, dan dekat dengan kehidupan sehari-hari.</p>
    </header>

    <section class="ilena-about__story" aria-labelledby="about-story-title">
        <div class="ilena-about__image">
            <img src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="Furniture Ilena dalam ruang bergaya urban" width="960" height="640" fetchpriority="high">
        </div>
        <div class="ilena-about__copy">
            <p class="ilena-editorial__eyebrow">Dirancang untuk keseharian</p>
            <h2 id="about-story-title">Crafted for modern living.</h2>
            <p>Ilena lahir pada 2024 di bawah naungan CV Catur Bhakti Mandiri, perusahaan dengan pengalaman sekitar 30 tahun di bidang furniture. Kami mengembangkan bisnis ritel dan interior dengan memahami kebutuhan ruang dan kebiasaan masyarakat urban.</p>
            <p>Setiap ruang punya ceritanya sendiri. Kami ingin membantu Anda membentuk ruang yang nyaman, fungsional, dan mencerminkan siapa Anda.</p>
        </div>
    </section>

    <section class="ilena-about__profile" aria-labelledby="about-profile-title">
        <div>
            <p class="ilena-editorial__eyebrow">Pengalaman manufaktur</p>
            <h2 id="about-profile-title">Berpengalaman. Bertumbuh dengan konsisten.</h2>
            <p>Berbasis di Semarang, CV Catur Bhakti Mandiri memproduksi furniture untuk kebutuhan rumah tangga, perkantoran, dan perhotelan. Pengalaman tersebut menjadi landasan Ilena dalam menjaga kualitas produk dan menghadirkan pilihan yang relevan untuk rumah modern.</p>
            <a class="ilena-editorial__text-link" href="/product">Jelajahi koleksi <span aria-hidden="true">→</span></a>
        </div>
        <img src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="Proses dan produk furniture Ilena" width="960" height="640" loading="lazy" decoding="async">
    </section>

    <section class="ilena-about__clients" aria-labelledby="about-clients-title">
        <div class="ilena-editorial__section-heading">
            <p class="ilena-editorial__eyebrow">Dipercaya oleh</p>
            <h2 id="about-clients-title">Our clients</h2>
        </div>
        <div class="ilena-about__client-logos">
            <img src="https://img.ilenafurniture.com/image/1742450163897.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="The Land of Nod" width="160" height="64" loading="lazy" decoding="async">
            <img src="https://img.ilenafurniture.com/image/1742450190985.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="Crate and Barrel" width="160" height="64" loading="lazy" decoding="async">
            <img src="https://img.ilenafurniture.com/image/1742450222588.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="West Elm" width="160" height="64" loading="lazy" decoding="async">
            <img src="https://img.ilenafurniture.com/image/1742450249426.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="Williams Sonoma" width="160" height="64" loading="lazy" decoding="async">
        </div>
    </section>

    <section class="ilena-about__contact" aria-labelledby="about-contact-title">
        <div>
            <p class="ilena-editorial__eyebrow">Kami siap membantu</p>
            <h2 id="about-contact-title">Mari terhubung.</h2>
            <p>Hubungi tim Ilena untuk pertanyaan seputar produk, pemesanan, atau kerja sama.</p>
            <a href="mailto:cs@ilenafurniture.com">cs@ilenafurniture.com</a>
            <a href="https://wa.me/628112938158" target="_blank" rel="noopener noreferrer">WhatsApp 0811-2938-158</a>
        </div>
        <address>
            <strong>Ilena Furniture</strong>
            <span>Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa Tengah</span>
            <a href="https://www.google.com/maps/search/?api=1&query=CV+Catur+Bhakti+Mandiri+Semarang" target="_blank" rel="noopener noreferrer">Buka lokasi di Google Maps <span aria-hidden="true">↗</span></a>
        </address>
    </section>
</main>
<?= $this->endSection(); ?>
