<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
/* ====== Light minimalist tokens ====== */
:root {
    --bg: #fafafa;
    --txt: #0f172a;
    --muted: #64748b;
    --accent: #e11d48;
    /* merah */
    --accent2: #f97316;
    /* oranye */
    --border: #e5e7eb;
}

/* Halaman tipis, tanpa card */
.container .konten {
    background:
        radial-gradient(900px 520px at 110% -8%, rgba(225, 29, 72, .05), transparent 60%),
        radial-gradient(900px 520px at -10% -8%, rgba(249, 115, 22, .05), transparent 60%);
    border-radius: 18px;
    padding: 14px 14px 26px;
}

/* Breadcrumb */
.breadcrumb a {
    color: #334155;
    text-decoration: none;
}

.breadcrumb .breadcrumb-item.active {
    color: #64748b;
}

/* ===== Hero — flat, tanpa kartu */
.hero {
    text-align: center;
    padding: 22px 8px 18px;
}

.hero h1 {
    margin: 0;
    font-weight: 900;
    letter-spacing: -.4px;
    color: var(--txt);
}

.hero p {
    margin: 8px 0 0;
    color: var(--muted);
}

.hero .underline {
    position: relative;
    display: inline-block;
}

.hero .underline::after {
    content: "";
    position: absolute;
    left: 10%;
    right: 10%;
    bottom: -8px;
    height: 4px;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    opacity: .25;
}

/* ===== Section spacing + pemisah halus */
.section {
    padding: 14px 6px;
    border-bottom: 1px solid var(--border);
}

.section:last-of-type {
    border-bottom: 0;
}

/* Paragraf pembuka */
.lead {
    margin: 0;
    line-height: 1.85;
    color: #111827;
}

/* ===== Blok media (gambar + teks) — tanpa card, grid saja */
.media {
    display: grid;
    grid-template-columns: 1.05fr 1fr;
    gap: 24px;
    align-items: center;
}

.media img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 14px;
}

.media h2 {
    margin: 0 0 10px;
    font-weight: 800;
    letter-spacing: -.2px;
    color: var(--txt);
}

.media p {
    margin: 0;
    color: #111827;
    text-align: justify;
}

/* Versi berpasangan kedua (teks dulu) */
.media.reverse {
    grid-template-columns: 1fr 1.05fr;
}

/* ===== Mobile block (class-mu tetap) */
.hide-ke-show-block img {
    border-radius: 14px;
}

/* ===== Clients — baris datar */
.clients {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
    align-items: center;
    justify-items: center;
}

.client-logo {
    max-width: 160px;
    width: 100%;
    filter: grayscale(1) contrast(1.05) brightness(.95);
    transition: .2s;
}

.client-logo:hover {
    filter: none;
    transform: translateY(-1px);
}

/* ===== Kontak — flat dua kolom */
.contact {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 22px;
}

.contact .map {
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}

.contact iframe {
    width: 100%;
    height: 100%;
    min-height: 280px;
    border: 0;
}

.contact h5 {
    margin: 0 0 8px;
    font-weight: 800;
    color: var(--txt);
}

.contact p {
    margin: 0;
    color: #111827;
}

.contact .contact-link {
    color: #0f172a;
    text-decoration: none;
}

.contact .contact-link:hover {
    text-decoration: underline;
}

.contact .faq-link {
    color: var(--accent);
    text-decoration: none;
}

.contact .faq-link:hover {
    text-decoration: underline;
}

/* ===== Responsive */
@media (max-width: 992px) {

    .media,
    .media.reverse {
        grid-template-columns: 1fr;
    }

    .contact {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .clients {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .hero h1 {
        font-size: 1.75rem;
    }
}
</style>

<div class="container">
    <div class="konten mx-auto">
        <nav style="--bs-breadcrumb-divider:'>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
            </ol>
        </nav>

        <!-- Hero -->
        <div class="hero section">
            <h1 class="teks-sedang underline">Cerita Kami</h1>
            <p>Perjalanan Ilena: dari manufaktur kayu berpengalaman menuju ritel & interior modern.</p>
        </div>

        <!-- Paragraf pembuka -->
        <div class="section">
            <p class="lead">
                Cerita lahirnya Ilena bermula pada tahun 2024 di bawah naungan CV Catur Bhakti Mandiri yang telah
                berdiri sejak 30 tahun.
                Ilena menandai dimulainya bisnis ritel dan interior. Dengan melebarnya industri yang didorong oleh
                kebutuhan konsumen,
                kami melakukan berbagai inovasi, keberlanjutan serta keinginan untuk terus konsisten berada di dekat
                hati konsumen
                dengan furniture berkualitas.
            </p>
        </div>

        <!-- Desktop blocks (tetap pakai class-mu show-block-ke-hide) -->
        <div class="section show-block-ke-hide">
            <div class="media">
                <div>
                    <img src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?= $apikey_img_ilena ?>"
                        alt="Urban Design">
                </div>
                <div>
                    <h2>Crafted to Urban Design</h2>
                    <p>
                        Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang yang
                        menjadi indah dalam kenangan.
                        Keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat urban yang cocok untuk
                        segala suasana.
                        Kami percaya bahwa setiap ruang kosong memiliki cerita yang diukir indah oleh individu dan
                        relasinya sebagai bentuk representasi tersendiri.
                        Bersama Ilena, wujudkan keindahan interior ruang impian Anda.
                    </p>
                </div>
            </div>
        </div>

        <div class="section show-block-ke-hide">
            <div class="media reverse">
                <div>
                    <h2>Profil Perusahaan</h2>
                    <p>
                        CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang, Jawa
                        Tengah.
                        Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi terhadap
                        keseimbangan kebutuhan konsumen
                        dan kesediaan sumber daya selama puluhan tahun lamanya.
                        Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga,
                        perkantoran & perhotelan berbahan dasar kayu
                        yang bersumber dari hutan berkelanjutan.
                    </p>
                </div>
                <div>
                    <img src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?= $apikey_img_ilena ?>"
                        alt="Company Profile" style="filter:grayscale(1);">
                </div>
            </div>
        </div>

        <!-- Mobile blocks (tetap pakai class-mu hide-ke-show-block) -->
        <div class="section hide-ke-show-block">
            <div class="mb-4">
                <img src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?= $apikey_img_ilena ?>"
                    alt="Urban Design Mobile">
                <h2 class="mt-3" style="font-weight:800;">Crafted to Urban Design</h2>
                <p style="text-align:justify;">
                    Ilena hadir menjadi teman untuk menciptakan banyak kesan dan pesan dalam setiap sudut ruang yang
                    menjadi indah dalam kenangan.
                    Keberhasilan Ilena merupakan usaha menghadirkan furniture khas masyarakat urban yang cocok untuk
                    segala suasana.
                    Kami percaya bahwa setiap ruang kosong memiliki cerita yang diukir indah oleh individu dan relasinya
                    sebagai bentuk representasi tersendiri.
                    Bersama Ilena, wujudkan keindahan interior ruang impian Anda.
                </p>
            </div>

            <div>
                <img src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?= $apikey_img_ilena ?>"
                    alt="Company Profile Mobile" style="filter:grayscale(1);">
                <h3 class="mt-3" style="font-weight:800;">Profil Perusahaan</h3>
                <p style="text-align:justify;">
                    CV Catur Bhakti Mandiri merupakan produsen kayu ternama Indonesia yang berada di Semarang, Jawa
                    Tengah.
                    Selama 30 tahun lamanya berkomitmen untuk selalu memberikan kualitas dan terintegrasi terhadap
                    keseimbangan kebutuhan konsumen
                    dan kesediaan sumber daya selama puluhan tahun lamanya.
                    Produk kami terdiri dari beragam furniture untuk mewujudkan interior ruang rumah tangga, perkantoran
                    & perhotelan berbahan dasar kayu
                    yang bersumber dari hutan berkelanjutan.
                </p>
            </div>
        </div>

        <!-- Clients -->
        <div class="section">
            <h2 class="teks-sedang text-center mb-3" style="font-weight:800;">Our Clients</h2>
            <div class="clients">
                <img src="https://img.ilenafurniture.com/image/1742450163897.webp/?apikey=<?= $apikey_img_ilena ?>"
                    alt="The Land of Nod" class="client-logo">
                <img src="https://img.ilenafurniture.com/image/1742450190985.webp/?apikey=<?= $apikey_img_ilena ?>"
                    alt="Crate and Barrel" class="client-logo">
                <img src="https://img.ilenafurniture.com/image/1742450222588.webp/?apikey=<?= $apikey_img_ilena ?>"
                    alt="West Elm" class="client-logo">
                <img src="https://img.ilenafurniture.com/image/1742450249426.webp/?apikey=<?= $apikey_img_ilena ?>"
                    alt="Williams Sonoma" class="client-logo">
            </div>
        </div>

        <!-- Kontak -->
        <div class="section">
            <h1 class="teks-sedang text-center mb-3" style="font-weight:800;">Kontak Kami</h1>
            <div class="contact">
                <div class="map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.687866383162!2d110.32868959999999!3d-7.0459182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7061e23055c8ed%3A0xa875b119e04372d4!2sCV.Catur%20Bhakti%20Mandiri!5e0!3m2!1sen!2sid!4v1723450895314!5m2!1sen!2sid"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <p class="mt-2" style="font-size:12px; color:#e11d48;">
                        <strong>Lokasi:</strong> Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa
                        Tengah
                    </p>
                </div>

                <div>
                    <h5><strong>Customer Service</strong></h5>
                    <p class="my-2" style="font-size:14px;">
                        Silakan ajukan pertanyaan Anda dengan menghubungi layanan customer service kami di bawah ini:
                    </p>
                    <div class="d-flex gap-3 mb-3" style="align-items:baseline;">
                        <div>
                            <p class="m-0" style="font-size:14px; color:#0f172a;">Email</p>
                            <p class="m-0" style="font-size:14px; color:#0f172a;">No. WhatsApp</p>
                        </div>
                        <div>
                            <a class="contact-link d-block" href="mailto:cs@ilenafurniture.com">:
                                cs@ilenafurniture.com</a>
                            <a class="contact-link d-block" href="https://wa.me/+628112938158">: 08112938158</a>
                        </div>
                    </div>

                    <h5><strong>Temukan Jawaban Cepat</strong></h5>
                    <div><a class="contact-link" style="font-size:14px;"
                            href="<?= base_url('/faq#faqkedua') ?>"><strong>Apa metode pembayaran yang bisa
                                digunakan?</strong></a></div>
                    <div><a class="contact-link" style="font-size:14px;"
                            href="<?= base_url('/faq#faqpertama') ?>"><strong>Bagaimana cara melakukan pemesanan &
                                pembelian di website Ilena?</strong></a></div>
                    <div class="mt-2"><a href="<?= base_url('/faq') ?>" class="faq-link">Lihat Semua FAQ</a></div>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>