<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
.iklan-section {
    padding: 64px 16px;
}
.iklan-container {
    max-width: 1140px;
    margin: 0 auto;
}
.iklan-hero {
    position: relative;
    min-height: 90svh;
    display: flex;
    align-items: center;
    color: white;
    overflow: hidden;
}
.iklan-hero::before {
    content: "";
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.65), rgba(0,0,0,0.35)),
        url('https://img.ilenafurniture.com/image/1748320128043.jpg/?apikey=<?= $apikey_img_ilena ?>');
    background-size: cover;
    background-position: center;
    z-index: 0;
}
.iklan-hero > * { position: relative; z-index: 1; }
.iklan-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 999px;
    background-color: rgba(232,74,73,0.9);
    color: white;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 18px;
}
.iklan-h1 {
    font-size: clamp(32px, 5vw, 56px);
    font-weight: 700;
    line-height: 1.15;
    margin-bottom: 18px;
}
.iklan-h2 {
    font-size: clamp(26px, 3.5vw, 38px);
    font-weight: 700;
    line-height: 1.25;
    margin-bottom: 12px;
    color: #1a1a1a;
}
.iklan-sub {
    font-size: clamp(15px, 1.6vw, 18px);
    color: #e4e4e4;
    margin-bottom: 28px;
    max-width: 620px;
}
.iklan-sub-dark {
    font-size: 16px;
    color: #6b6b6b;
    max-width: 720px;
    margin: 0 auto 40px;
    text-align: center;
}
.iklan-btn-primary {
    display: inline-block;
    padding: 14px 28px;
    background-color: var(--merah);
    color: white !important;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    border: 2px solid var(--merah);
}
.iklan-btn-primary:hover {
    background-color: #c93b3a;
    border-color: #c93b3a;
    transform: translateY(-1px);
}
.iklan-btn-outline {
    display: inline-block;
    padding: 14px 28px;
    background-color: transparent;
    color: white !important;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid white;
    transition: all .2s;
}
.iklan-btn-outline:hover {
    background-color: white;
    color: #1a1a1a !important;
}
.iklan-btn-dark {
    display: inline-block;
    padding: 14px 28px;
    background-color: #1a1a1a;
    color: white !important;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid #1a1a1a;
}
.iklan-btn-dark:hover { background-color: #000; }

.iklan-stats {
    background-color: #fafafa;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 28px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    height: 100%;
}
.stat-num {
    font-size: 44px;
    font-weight: 800;
    color: var(--merah);
    line-height: 1;
    margin-bottom: 8px;
}
.stat-label {
    font-size: 14px;
    color: #555;
    font-weight: 500;
}

.benefit-card {
    background: white;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 28px;
    height: 100%;
    transition: all .2s;
}
.benefit-card:hover {
    border-color: var(--merah);
    box-shadow: 0 6px 24px rgba(232,74,73,0.08);
    transform: translateY(-2px);
}
.benefit-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background-color: #fdefef;
    color: var(--merah);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}
.benefit-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #1a1a1a;
}
.benefit-text {
    font-size: 14px;
    color: #6b6b6b;
    margin: 0;
    line-height: 1.6;
}

.value-card {
    background: #fafafa;
    border-radius: 14px;
    padding: 32px 24px;
    height: 100%;
    border-top: 4px solid var(--merah);
}
.value-card h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #1a1a1a;
}
.value-card p { color: #555; font-size: 14px; line-height: 1.65; margin: 0; }

.series-card {
    background: white;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all .25s;
}
.series-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.10);
}
.series-img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
}
.series-body {
    padding: 22px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.series-tag {
    font-size: 12px;
    color: var(--merah);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.series-name {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
}
.series-desc {
    font-size: 14px;
    color: #6b6b6b;
    line-height: 1.6;
    flex: 1;
    margin-bottom: 18px;
}
.series-link {
    color: var(--merah);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.series-link:hover { color: #c93b3a; }

.testi-section { background-color: #fafafa; }
.testi-card {
    background: white;
    border-radius: 14px;
    padding: 28px;
    height: 100%;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.testi-stars { color: #f5b40a; margin-bottom: 14px; font-size: 18px; letter-spacing: 2px; }
.testi-quote {
    font-size: 16px;
    color: #2a2a2a;
    line-height: 1.65;
    margin-bottom: 18px;
    font-style: italic;
}
.testi-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; }
.testi-loc { font-size: 13px; color: #888; margin: 0; }
.testi-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: var(--merah); color: white;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 18px;
    margin-right: 12px;
}
.verif-badge {
    display: inline-block;
    background: #1ece3623;
    color: #1ba32a;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 999px;
    margin-top: 4px;
}

.final-cta {
    background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
    color: white;
    text-align: center;
}
.final-cta .iklan-h2 { color: white; }
.cta-mini-benefit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #d4d4d4;
    font-size: 13px;
    margin: 6px 12px;
}
.cta-mini-benefit i { color: var(--merah); font-size: 18px; }

@media (max-width: 768px) {
    .iklan-section { padding: 48px 16px; }
    .iklan-hero { min-height: 80svh; }
}
</style>

<!-- 1. HERO -->
<section class="iklan-hero">
    <div class="iklan-container w-100 px-4">
        <span class="iklan-badge">PROMO TERBATAS · GRATIS ONGKIR</span>
        <h1 class="iklan-h1">Desain Interior Rumah Baru<br>Jadi Mudah & Estetik</h1>
        <p class="iklan-sub">Pilih furniture sepaket dari Ilena — modern, premium, dan saat ini <b>gratis ongkir</b> ke seluruh Indonesia. Tinggal pasang, rumah langsung jadi.</p>
        <div class="d-flex gap-3 flex-wrap">
            <a href="#series" class="iklan-btn-primary">Lihat Koleksi Series</a>
            <a href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20mau%20tanya-tanya%20koleksinya" class="iklan-btn-outline" target="_blank" rel="noopener">Klaim Gratis Ongkir</a>
        </div>
    </div>
</section>

<!-- 2. TRUST BADGES -->
<section class="iklan-section iklan-stats">
    <div class="iklan-container">
        <div class="row g-3">
            <div class="col-md-4 col-12">
                <div class="stat-card">
                    <div class="stat-num">10+</div>
                    <div class="stat-label">Series Koleksi Premium</div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="stat-card">
                    <div class="stat-num">100%</div>
                    <div class="stat-label">Promo Gratis Ongkir</div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="stat-card">
                    <div class="stat-num">Rapi</div>
                    <div class="stat-label">Hemat Waktu Belanja</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. PROBLEM / SOLUTION -->
<section class="iklan-section">
    <div class="iklan-container">
        <div class="text-center mb-5">
            <h2 class="iklan-h2">Renovasi Selesai, Bingung Cari Furniture?</h2>
            <p class="iklan-sub-dark">Banding-bandingin di marketplace bikin pusing. Beda toko, beda style, ongkos pengiriman bikin budget bengkak. Ilena bantu beresin semuanya.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-12">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="material-icons">schedule</i></div>
                    <h3 class="benefit-title">Hemat Waktu</h3>
                    <p class="benefit-text">Nggak perlu lagi bandingin puluhan toko. Satu series, satu konsep, langsung jadi.</p>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="material-icons">verified</i></div>
                    <h3 class="benefit-title">Desain Pasti Cocok</h3>
                    <p class="benefit-text">Setiap series dirancang satu kesatuan — warna, tekstur, dan gaya saling melengkapi.</p>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="material-icons">savings</i></div>
                    <h3 class="benefit-title">Hemat Biaya Kirim</h3>
                    <p class="benefit-text">Promo gratis ongkir bantu kamu pangkas biaya pengiriman antar pulau yang biasanya bikin kaget.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. VALUE PROPOSITION -->
<section class="iklan-section" style="background:#fff;">
    <div class="iklan-container">
        <div class="text-center mb-5">
            <h2 class="iklan-h2">Kenapa Pilih Ilena?</h2>
            <p class="iklan-sub-dark">Bukan sekadar jual furniture, kami bantu kamu wujudkan rumah impian dengan tiga keunggulan utama.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-12">
                <div class="value-card">
                    <h3>Desain Sepaket</h3>
                    <p>Beli satu series, dapat satu konsep ruangan utuh. Cocok untuk yang mau hasil rapi tanpa pusing match-matching.</p>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="value-card">
                    <h3>Kualitas Premium</h3>
                    <p>Material pilihan, finishing presisi, dirancang untuk tahan dipakai harian dengan estetika yang bertahan lama.</p>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="value-card">
                    <h3>Bebas Ongkir</h3>
                    <p>Selama periode promo, gratis ongkir ke seluruh Indonesia. Harga yang kamu lihat, itu yang kamu bayar.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. PRODUCT SERIES SHOWCASE -->
<section id="series" class="iklan-section iklan-stats">
    <div class="iklan-container">
        <div class="text-center mb-5">
            <h2 class="iklan-h2">Series Pilihan untuk Rumahmu</h2>
            <p class="iklan-sub-dark">Tiga series paling diminati customer Ilena. Klik untuk lihat detail produknya.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($series as $s): ?>
            <div class="col-md-4 col-12">
                <div class="series-card">
                    <img src="<?= esc($s['image']) ?>" alt="<?= esc($s['nama']) ?>" class="series-img" loading="lazy">
                    <div class="series-body">
                        <div class="series-tag"><?= esc($s['gaya']) ?></div>
                        <div class="series-name"><?= esc($s['nama']) ?></div>
                        <p class="series-desc"><?= esc($s['deskripsi']) ?></p>
                        <a href="<?= esc($s['link']) ?>" class="series-link">Cek Detail <i class="material-icons" style="font-size:16px;">arrow_forward</i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= base_url('/product') ?>" class="iklan-btn-primary">Lihat Semua Produk</a>
        </div>
    </div>
</section>

<!-- 6. TESTIMONIALS -->
<section class="iklan-section testi-section">
    <div class="iklan-container">
        <div class="text-center mb-5">
            <h2 class="iklan-h2">Kata Mereka yang Sudah Pakai</h2>
            <p class="iklan-sub-dark">Ratusan customer Ilena sudah merasakan kemudahan beli furniture sepaket.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-12">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-quote">"Awalnya bingung mau cari furniture buat rumah baru. Pilih Cody Series Ilena, semuanya match. Tetangga sampai tanya jasa interiornya."</p>
                    <div class="d-flex align-items-center">
                        <div class="testi-avatar">R</div>
                        <div>
                            <p class="testi-name">Ratna Wijaya</p>
                            <p class="testi-loc">Jakarta Selatan</p>
                            <span class="verif-badge">✓ Pembeli Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-quote">"Kualitas di luar ekspektasi. Pengiriman ke Surabaya gratis ongkir, packing rapi banget. Recommended untuk yang lagi siap-siap pindahan."</p>
                    <div class="d-flex align-items-center">
                        <div class="testi-avatar">B</div>
                        <div>
                            <p class="testi-name">Bayu Pratama</p>
                            <p class="testi-loc">Surabaya</p>
                            <span class="verif-badge">✓ Pembeli Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. FINAL CTA -->
<section class="iklan-section final-cta">
    <div class="iklan-container">
        <span class="iklan-badge">⏰ STOK TERBATAS</span>
        <h2 class="iklan-h2">Yuk Wujudkan Rumah Impianmu Sekarang</h2>
        <p class="iklan-sub mx-auto" style="color:#d4d4d4;">Promo gratis ongkir berlaku selama stok masih tersedia. Konsultasikan kebutuhanmu langsung ke tim kami via WhatsApp.</p>
        <div class="d-flex gap-3 flex-wrap justify-content-center mb-4">
            <a href="<?= base_url('/product') ?>" class="iklan-btn-primary">Belanja Sekarang</a>
            <a href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20mau%20tanya-tanya%20koleksinya" class="iklan-btn-outline" target="_blank" rel="noopener">Chat WhatsApp</a>
        </div>
        <div class="mt-3">
            <span class="cta-mini-benefit"><i class="material-icons">local_shipping</i> Gratis Ongkir</span>
            <span class="cta-mini-benefit"><i class="material-icons">verified</i> Garansi Kualitas</span>
            <span class="cta-mini-benefit"><i class="material-icons">support_agent</i> CS Responsif</span>
            <span class="cta-mini-benefit"><i class="material-icons">credit_card</i> Pembayaran Aman</span>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>
