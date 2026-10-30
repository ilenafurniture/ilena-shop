<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php $isEnglish = service('request')->getLocale() === 'en'; ?>

<style>
.ilena-iklan-page {
    --iklan-ink: #25221f;
    --iklan-muted: #716a63;
    --iklan-line: #e5ded6;
    --iklan-paper: #f5f2ed;
    --iklan-accent: #8e3935;
    --iklan-font-body: var(--ilena-font-body, "Manrope", -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif);
    --iklan-font-display: var(--ilena-font-display, "Cormorant Garamond", Georgia, serif);
    color: var(--iklan-ink);
    background: #fff;
    font-family: var(--iklan-font-body);
    font-size: 16px;
    font-weight: 400;
    line-height: 1.65;
}
.ilena-iklan-page :where(*, *::before, *::after) { box-sizing: border-box; }
.ilena-iklan-page .iklan-section { padding: clamp(64px, 8vw, 112px) clamp(18px, 4vw, 56px); }
.ilena-iklan-page .iklan-container { width: min(100%, 1320px); margin-inline: auto; }
.ilena-iklan-page .iklan-hero {
    position: relative; display: flex; min-height: min(82vh, 850px); min-height: min(82svh, 850px); align-items: center; padding: clamp(52px, 8vw, 110px) clamp(20px, 7vw, 100px);
    overflow: hidden; color: #fff; background: #38322d;
}
.ilena-iklan-page .iklan-hero::before {
    position: absolute; inset: 0; z-index: 1; content: "";
    background: linear-gradient(90deg, rgba(23,20,18,.78) 0%, rgba(23,20,18,.5) 48%, rgba(23,20,18,.12) 100%);
}
.ilena-iklan-page .iklan-hero-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center 55%; }
.ilena-iklan-page .iklan-hero > .iklan-container { position: relative; z-index: 2; }
.ilena-iklan-page .iklan-hero .iklan-container { max-width: 1320px; }
.ilena-iklan-page .iklan-badge {
    display: inline-flex; align-items: center; gap: 8px; margin: 0 0 20px; padding: 8px 11px;
    border: 1px solid rgba(255,255,255,.45); border-radius: 0; background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.94); font-size: 11px; font-weight: 600; letter-spacing: .07em; line-height: 1.4;
}
.ilena-iklan-page .iklan-h1 {
    max-width: 780px; margin: 0 0 20px; color: #fff; font-family: var(--iklan-font-display);
    font-size: clamp(50px, 6.4vw, 88px); font-weight: 500; letter-spacing: -.025em; line-height: 1.02;
}
.ilena-iklan-page .iklan-h1 { text-wrap: balance; }
.ilena-iklan-page .iklan-h2,
.ilena-iklan-page .benefit-title,
.ilena-iklan-page .series-name,
.ilena-iklan-page .testi-quote { text-wrap: balance; }
.ilena-iklan-page .iklan-sub,
.ilena-iklan-page .iklan-sub-dark,
.ilena-iklan-page .benefit-text,
.ilena-iklan-page .series-desc { text-wrap: pretty; }
.ilena-iklan-page .iklan-h1 .iklan-desktop-break { display: block; }
.ilena-iklan-page .iklan-h2 {
    margin: 0 0 16px; color: var(--iklan-ink); font-family: var(--iklan-font-display);
    font-size: clamp(32px, 3.8vw, 50px); font-weight: 500; letter-spacing: -.02em; line-height: 1.08;
}
.ilena-iklan-page .iklan-sub { max-width: 590px; margin: 0 0 30px; color: rgba(255,255,255,.9); font-size: clamp(15px, 1.25vw, 17px); line-height: 1.7; }
.ilena-iklan-page .iklan-sub-dark { max-width: 660px; margin: 0 auto 36px; color: var(--iklan-muted); font-size: 15px; line-height: 1.75; }
.ilena-iklan-page .iklan-btn-primary,
.ilena-iklan-page .iklan-btn-outline,
.ilena-iklan-page .iklan-btn-dark {
    display: inline-flex; min-height: 48px; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px;
    border: 1px solid var(--iklan-accent); border-radius: 0; min-height: 44px; font-size: 12px; font-weight: 650; letter-spacing: .025em; line-height: 1.4; text-decoration: none;
    transition: color .2s ease, background .2s ease, border-color .2s ease;
}
.ilena-iklan-page .iklan-btn-primary { background: var(--iklan-accent); color: #fff !important; }
.ilena-iklan-page .iklan-btn-primary:hover { border-color: #712c29; background: #712c29; }
.ilena-iklan-page .iklan-btn-outline { border-color: rgba(255,255,255,.78); background: transparent; color: #fff !important; }
.ilena-iklan-page .iklan-btn-outline:hover { background: #fff; color: var(--iklan-ink) !important; }
.ilena-iklan-page .iklan-btn-dark { border-color: var(--iklan-ink); background: var(--iklan-ink); color: #fff !important; }
.ilena-iklan-page .iklan-btn-dark:hover { background: #443c35; }
.ilena-iklan-page .iklan-stats { background: var(--iklan-paper); }
.ilena-iklan-page .stat-card { height: 100%; padding: 20px 18px; border: 0; border-top: 1px solid #cbb8a5; border-radius: 0; background: transparent; box-shadow: none; text-align: left; }
.ilena-iklan-page .stat-num { margin: 0 0 7px; color: var(--iklan-accent); font-family: var(--iklan-font-display); font-size: clamp(44px, 5vw, 62px); font-weight: 500; line-height: .95; }
.ilena-iklan-page .stat-label { color: var(--iklan-muted); font-size: 14px; line-height: 1.5; }
.ilena-iklan-page .benefit-card,
.ilena-iklan-page .value-card { height: 100%; padding: 22px 0 8px; border: 0; border-top: 1px solid var(--iklan-line); border-radius: 0; background: transparent; box-shadow: none; transition: border-color .2s ease; }
.ilena-iklan-page .benefit-card:hover { transform: none; border-color: var(--iklan-accent); box-shadow: none; }
.ilena-iklan-page .benefit-icon { display: inline-flex; width: 42px; height: 42px; align-items: center; justify-content: center; margin: 0 0 16px; border-radius: 0; background: var(--iklan-paper); color: var(--iklan-accent); }
.ilena-iklan-page .benefit-title,
.ilena-iklan-page .value-card h3 { margin: 0 0 9px; color: var(--iklan-ink); font-family: var(--iklan-font-display); font-size: 23px; font-weight: 500; letter-spacing: -.01em; line-height: 1.2; }
.ilena-iklan-page .benefit-text,
.ilena-iklan-page .value-card p { margin: 0; color: var(--iklan-muted); font-size: 15px; line-height: 1.7; }
.ilena-iklan-page .value-card { border-top: 1px solid var(--iklan-accent); }
.ilena-iklan-page .series-card { display: flex; height: 100%; flex-direction: column; overflow: hidden; border: 0; border-radius: 0; background: #fff; box-shadow: none; }
.ilena-iklan-page .series-img { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: cover; background: var(--iklan-paper); transition: transform .55s ease; }
.ilena-iklan-page .series-card:hover .series-img { transform: scale(1.025); }
.ilena-iklan-page .series-body { flex: 1; padding: 18px 0 0; }
.ilena-iklan-page .series-tag { margin-bottom: 7px; color: var(--iklan-accent); font-size: 12px; font-weight: 600; letter-spacing: .025em; line-height: 1.45; }
.ilena-iklan-page .series-name { margin-bottom: 8px; color: var(--iklan-ink); font-family: var(--iklan-font-display); font-size: 26px; font-weight: 500; line-height: 1.15; }
.ilena-iklan-page .series-desc { flex: 1; margin-bottom: 16px; color: var(--iklan-muted); font-size: 15px; line-height: 1.7; }
.ilena-iklan-page .series-link { display: inline-flex; min-height: 44px; align-items: center; gap: 7px; color: var(--iklan-accent); font-size: 12px; font-weight: 650; text-decoration: none; }
.ilena-iklan-page .series-link:hover { color: var(--iklan-ink); }
.ilena-iklan-page .testi-section { background: var(--iklan-paper); }
.ilena-iklan-page .testi-card { height: 100%; padding: 22px 0; border: 0; border-top: 1px solid #d8c8b8; border-radius: 0; background: transparent; box-shadow: none; }
.ilena-iklan-page .testi-stars { margin-bottom: 12px; color: var(--iklan-accent); font-size: 15px; letter-spacing: 2px; }
.ilena-iklan-page .testi-quote { margin: 0 0 18px; color: var(--iklan-ink); font-family: var(--iklan-font-display); font-size: 20px; font-style: normal; line-height: 1.5; }
.ilena-iklan-page .testi-name { margin: 0; color: var(--iklan-ink); font-size: 14px; font-weight: 650; line-height: 1.4; }
.ilena-iklan-page .testi-loc { margin: 3px 0 0; color: var(--iklan-muted); font-size: 13px; line-height: 1.45; }
.ilena-iklan-page .testi-avatar { display: inline-flex; width: 40px; height: 40px; align-items: center; justify-content: center; margin-right: 12px; border-radius: 50%; background: #e9e1d8; color: var(--iklan-accent); font-size: 14px; }
.ilena-iklan-page .verif-badge { display: inline-block; margin-top: 5px; padding: 0; background: transparent; color: #58705b; font-size: 12px; font-weight: 600; line-height: 1.4; }
.ilena-iklan-page .final-cta { background: #25221f; color: #fff; text-align: center; }
.ilena-iklan-page .final-cta .iklan-h2 { color: #fff; }
.ilena-iklan-page .cta-mini-benefit { display: inline-flex; align-items: center; gap: 6px; margin: 6px 10px; color: rgba(255,255,255,.82); font-size: 13px; line-height: 1.5; }
.ilena-iklan-page .cta-mini-benefit i { color: #c6a98d; font-size: 17px; }
.ilena-iklan-page a:focus-visible { outline: 2px solid #8e3935; outline-offset: 3px; }
.ilena-iklan-page :where(a, button) { touch-action: manipulation; }
.ilena-iklan-page .iklan-section-heading { max-width: 720px; margin: 0 auto 42px; text-align: center; }
.ilena-iklan-page .iklan-section-heading .iklan-sub-dark { margin-bottom: 0; }
.ilena-iklan-page .benefit-card, .ilena-iklan-page .value-card, .ilena-iklan-page .series-card, .ilena-iklan-page .testi-card { min-width: 0; }
.ilena-iklan-page .series-img { height: auto; }
.ilena-iklan-page.iklan-motion-ready [data-iklan-reveal] { opacity: 0; transform: translate3d(0, 22px, 0); transition: opacity 520ms ease, transform 520ms cubic-bezier(.2,.7,.2,1); transition-delay: var(--iklan-reveal-delay, 0ms); }
.ilena-iklan-page.iklan-motion-ready [data-iklan-reveal].is-visible { opacity: 1; transform: translate3d(0, 0, 0); }
.ilena-iklan-page .benefit-icon i { transform: translateY(5px); transition: transform 420ms cubic-bezier(.2,.7,.2,1); }
.ilena-iklan-page.iklan-motion-ready [data-iklan-reveal].is-visible .benefit-icon i { transform: translateY(0); }
.ilena-iklan-page .iklan-btn-primary, .ilena-iklan-page .iklan-btn-outline, .ilena-iklan-page .iklan-btn-dark { min-height: 48px; }
.ilena-iklan-page #series { scroll-margin-top: 88px; }
@media (max-width: 700px) {
    .ilena-iklan-page .iklan-section { padding: 54px 18px; }
    .ilena-iklan-page .iklan-hero { min-height: min(76vh, 720px); min-height: min(76svh, 720px); padding: 46px 20px; }
    .ilena-iklan-page .iklan-hero::before { background: linear-gradient(90deg, rgba(23,20,18,.76), rgba(23,20,18,.42)); }
    .ilena-iklan-page .iklan-hero-image { object-position: 58% center; }
    .ilena-iklan-page .iklan-h1 { max-width: 620px; font-size: clamp(40px, 10.2vw, 58px); line-height: 1.04; }
    .ilena-iklan-page .iklan-h1 .iklan-desktop-break { display: none; }
    .ilena-iklan-page .iklan-sub { max-width: 36rem; margin-bottom: 24px; font-size: 15px; line-height: 1.65; }
    .ilena-iklan-page .iklan-sub-dark { margin-bottom: 26px; font-size: 14px; line-height: 1.7; }
    .ilena-iklan-page .iklan-actions { display: grid !important; grid-template-columns: 1fr; gap: 10px !important; max-width: 360px; }
    .ilena-iklan-page .final-cta .iklan-actions { margin-inline: auto; }
    .ilena-iklan-page .iklan-btn-primary, .ilena-iklan-page .iklan-btn-outline { width: 100%; min-height: 48px; padding-inline: 14px; font-size: 12px; }
    .ilena-iklan-page .stat-card { padding: 16px 0; }
    .ilena-iklan-page .value-card, .ilena-iklan-page .benefit-card { padding-top: 18px; }
    .ilena-iklan-page .series-body { padding-top: 14px; }
    .ilena-iklan-page .series-name { font-size: 24px; }
    .ilena-iklan-page .testi-quote { font-size: 19px; }
    .ilena-iklan-page .testi-card { padding-block: 18px; }
    .ilena-iklan-page .iklan-section-heading { margin-bottom: 30px; }
    .ilena-iklan-page .cta-mini-benefit { margin-inline: 6px; font-size: 12px; }
}
@media (max-width: 380px) {
    .ilena-iklan-page .iklan-section { padding-inline: 15px; }
    .ilena-iklan-page .iklan-hero { padding-inline: 16px; }
    .ilena-iklan-page .iklan-h1 { font-size: clamp(38px, 10.5vw, 42px); }
    .ilena-iklan-page .iklan-badge { font-size: 10px; letter-spacing: .045em; }
}
@media (prefers-reduced-motion: reduce) {
    .ilena-iklan-page *, .ilena-iklan-page *::before, .ilena-iklan-page *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; }
    .ilena-iklan-page.iklan-motion-ready [data-iklan-reveal] { opacity: 1; transform: none; }
}
</style>

<main class="ilena-iklan-page" aria-label="<?= esc($isEnglish ? 'Ilena furniture promotion' : 'Promo furniture Ilena', 'attr'); ?>">
<!-- 1. HERO -->
<section class="iklan-hero">
    <img class="iklan-hero-image" src="<?= esc('https://img.ilenafurniture.com/image/1748320128043.jpg/?apikey=' . $apikey_img_ilena, 'attr'); ?>" alt="" width="1920" height="1200" fetchpriority="high" decoding="async">
    <div class="iklan-container w-100 px-4" data-iklan-reveal>
        <span class="iklan-badge"><?= $isEnglish ? lang('Store.limitedPromotion') : 'Promo terbatas · Gratis ongkir'; ?></span>
        <h1 class="iklan-h1"><?= $isEnglish ? 'Furnishing your new home' : 'Desain Interior Rumah Baru'; ?><span class="iklan-desktop-break"><br></span><?= $isEnglish ? 'Made easy & beautiful' : ' Jadi Mudah & Estetik'; ?></h1>
        <p class="iklan-sub"><?= $isEnglish ? lang('Store.bundleHeroBody') : 'Pilih furniture sepaket dari Ilena — modern, premium, dan saat ini '; ?><?php if (!$isEnglish): ?><b>gratis ongkir</b> ke seluruh Indonesia. Tinggal pasang, rumah langsung jadi.<?php endif; ?></p>
        <div class="d-flex gap-3 flex-wrap iklan-actions">
            <a href="#series" class="iklan-btn-primary"><?= $isEnglish ? lang('Store.viewSeries') : 'Lihat Koleksi Series'; ?></a>
            <a data-kbot-wa href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20mau%20tanya-tanya%20koleksinya" class="iklan-btn-outline" target="_blank" rel="noopener"><?= $isEnglish ? lang('Store.claimFreeShipping') : 'Klaim Gratis Ongkir'; ?></a>
        </div>
    </div>
</section>

<!-- 2. TRUST BADGES -->
<section class="iklan-section iklan-stats" aria-label="Keunggulan layanan">
    <div class="iklan-container">
        <div class="row g-3">
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="stat-card">
                    <div class="stat-num">10+</div>
                    <div class="stat-label"><?= $isEnglish ? lang('Store.collectionSeries') : 'Series Koleksi Premium'; ?></div>
                </div>
            </div>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="stat-card">
                    <div class="stat-num">100%</div>
                    <div class="stat-label"><?= $isEnglish ? lang('Store.freeShippingPromo') : 'Promo Gratis Ongkir'; ?></div>
                </div>
            </div>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="stat-card">
                    <div class="stat-num"><?= $isEnglish ? 'Easy' : 'Rapi'; ?></div>
                    <div class="stat-label"><?= $isEnglish ? lang('Store.shoppingTimeSaved') : 'Hemat Waktu Belanja'; ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. PROBLEM / SOLUTION -->
<section class="iklan-section" aria-labelledby="iklan-problem-title">
    <div class="iklan-container">
        <div class="iklan-section-heading" data-iklan-reveal>
            <h2 id="iklan-problem-title" class="iklan-h2"><?= $isEnglish ? lang('Store.renovationQuestion') : 'Renovasi Selesai, Bingung Cari Furniture?'; ?></h2>
            <p class="iklan-sub-dark"><?= $isEnglish ? lang('Store.bundleProblemBody') : 'Banding-bandingin di marketplace bikin pusing. Beda toko, beda style, ongkos pengiriman bikin budget bengkak. Ilena bantu beresin semuanya.'; ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="material-icons">schedule</i></div>
                    <h3 class="benefit-title"><?= $isEnglish ? lang('Store.saveTime') : 'Hemat Waktu'; ?></h3>
                    <p class="benefit-text"><?= $isEnglish ? lang('Store.saveTimeBody') : 'Nggak perlu lagi bandingin puluhan toko. Satu series, satu konsep, langsung jadi.'; ?></p>
                </div>
            </div>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="material-icons">verified</i></div>
                    <h3 class="benefit-title"><?= $isEnglish ? lang('Store.designThatFits') : 'Desain Pasti Cocok'; ?></h3>
                    <p class="benefit-text"><?= $isEnglish ? lang('Store.designThatFitsBody') : 'Setiap series dirancang satu kesatuan — warna, tekstur, dan gaya saling melengkapi.'; ?></p>
                </div>
            </div>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="material-icons">savings</i></div>
                    <h3 class="benefit-title"><?= $isEnglish ? lang('Store.shippingSavings') : 'Hemat Biaya Kirim'; ?></h3>
                    <p class="benefit-text"><?= $isEnglish ? lang('Store.shippingSavingsBody') : 'Promo gratis ongkir bantu kamu pangkas biaya pengiriman antar pulau yang biasanya bikin kaget.'; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. VALUE PROPOSITION -->
<section class="iklan-section" style="background:#fff;" aria-labelledby="iklan-value-title">
    <div class="iklan-container">
        <div class="iklan-section-heading" data-iklan-reveal>
            <h2 id="iklan-value-title" class="iklan-h2"><?= $isEnglish ? lang('Store.whyChooseIlena') : 'Kenapa Pilih Ilena?'; ?></h2>
            <p class="iklan-sub-dark"><?= $isEnglish ? lang('Store.whyChooseIlenaBody') : 'Bukan sekadar jual furniture, kami bantu kamu wujudkan rumah impian dengan tiga keunggulan utama.'; ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="value-card">
                    <h3><?= $isEnglish ? lang('Store.coordinatedSet') : 'Desain Sepaket'; ?></h3>
                    <p><?= $isEnglish ? lang('Store.coordinatedSetBody') : 'Beli satu series, dapat satu konsep ruangan utuh. Cocok untuk yang mau hasil rapi tanpa pusing match-matching.'; ?></p>
                </div>
            </div>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="value-card">
                    <h3><?= $isEnglish ? lang('Store.premiumQuality') : 'Kualitas Premium'; ?></h3>
                    <p><?= $isEnglish ? lang('Store.premiumQualityBody') : 'Material pilihan, finishing presisi, dirancang untuk tahan dipakai harian dengan estetika yang bertahan lama.'; ?></p>
                </div>
            </div>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="value-card">
                    <h3><?= $isEnglish ? lang('Store.freeDelivery') : 'Bebas Ongkir'; ?></h3>
                    <p><?= $isEnglish ? lang('Store.freeDeliveryBody') : 'Selama periode promo, gratis ongkir ke seluruh Indonesia. Harga yang kamu lihat, itu yang kamu bayar.'; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. PRODUCT SERIES SHOWCASE -->
<section id="series" class="iklan-section iklan-stats" aria-labelledby="iklan-series-title">
    <div class="iklan-container">
        <div class="iklan-section-heading" data-iklan-reveal>
            <h2 id="iklan-series-title" class="iklan-h2"><?= $isEnglish ? lang('Store.featuredCollections') : 'Series Pilihan untuk Rumahmu'; ?></h2>
            <p class="iklan-sub-dark"><?= $isEnglish ? lang('Store.featuredCollectionsBody') : 'Tiga series paling diminati customer Ilena. Klik untuk lihat detail produknya.'; ?></p>
        </div>
        <div class="row g-4 iklan-series-grid">
            <?php foreach ($series as $s): ?>
            <div class="col-md-4 col-12" data-iklan-reveal>
                <div class="series-card">
                    <img src="<?= esc($s['image']) ?>" alt="<?= esc($s['nama']) ?>" class="series-img" width="480" height="360" loading="lazy" decoding="async">
                    <div class="series-body">
                        <div class="series-tag"><?= esc($s['gaya']) ?></div>
                    <h3 class="series-name"><?= esc($s['nama']) ?></h3>
                        <p class="series-desc"><?= esc($isEnglish ? ($s['description_en'] ?? $s['deskripsi']) : $s['deskripsi']); ?></p>
                        <a href="<?= esc($s['link']) ?>" class="series-link"><?= $isEnglish ? lang('Store.checkDetails') : 'Cek Detail'; ?> <i class="material-icons" style="font-size:16px;">arrow_forward</i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>" class="iklan-btn-primary"><?= $isEnglish ? lang('Store.viewAllProducts') : 'Lihat Semua Produk'; ?></a>
        </div>
    </div>
</section>

<!-- 6. TESTIMONIALS -->
<section class="iklan-section testi-section" aria-labelledby="iklan-testimonial-title">
    <div class="iklan-container">
        <div class="iklan-section-heading" data-iklan-reveal>
            <h2 id="iklan-testimonial-title" class="iklan-h2"><?= $isEnglish ? lang('Store.customerStories') : 'Kata Mereka yang Sudah Pakai'; ?></h2>
            <p class="iklan-sub-dark"><?= $isEnglish ? lang('Store.customerStoriesBody') : 'Ratusan customer Ilena sudah merasakan kemudahan beli furniture sepaket.'; ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-12" data-iklan-reveal>
                <div class="testi-card">
                    <div class="testi-stars" role="img" aria-label="<?= esc($isEnglish ? 'Rated 5 out of 5' : 'Penilaian 5 dari 5', 'attr'); ?>">★★★★★</div>
                    <p class="testi-quote"><q><?= $isEnglish ? 'I was unsure which furniture to choose for our new home. We chose Ilena’s Cody collection and everything came together beautifully.' : 'Awalnya bingung mau cari furniture buat rumah baru. Pilih Cody Series Ilena, semuanya match. Tetangga sampai tanya jasa interiornya.'; ?></q></p>
                    <div class="d-flex align-items-center">
                        <div class="testi-avatar">R</div>
                        <div>
                            <p class="testi-name">Ratna Wijaya</p>
                            <p class="testi-loc">Jakarta Selatan</p>
                            <span class="verif-badge">✓ <?= $isEnglish ? lang('Store.verifiedBuyer') : 'Pembeli Terverifikasi'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12" data-iklan-reveal>
                <div class="testi-card">
                    <div class="testi-stars" role="img" aria-label="<?= esc($isEnglish ? 'Rated 5 out of 5' : 'Penilaian 5 dari 5', 'attr'); ?>">★★★★★</div>
                    <p class="testi-quote"><q><?= $isEnglish ? 'The quality exceeded my expectations. Delivery to Surabaya was covered by the offer, and the packaging was very neat.' : 'Kualitas di luar ekspektasi. Pengiriman ke Surabaya gratis ongkir, packing rapi banget. Recommended untuk yang lagi siap-siap pindahan.'; ?></q></p>
                    <div class="d-flex align-items-center">
                        <div class="testi-avatar">B</div>
                        <div>
                            <p class="testi-name">Bayu Pratama</p>
                            <p class="testi-loc">Surabaya</p>
                            <span class="verif-badge">✓ <?= $isEnglish ? lang('Store.verifiedBuyer') : 'Pembeli Terverifikasi'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. FINAL CTA -->
<section class="iklan-section final-cta" aria-labelledby="iklan-final-title">
    <div class="iklan-container">
        <span class="iklan-badge"><?= $isEnglish ? lang('Store.limitedStock') : 'Stok terbatas'; ?></span>
        <h2 id="iklan-final-title" class="iklan-h2" data-iklan-reveal><?= $isEnglish ? lang('Store.makeDreamHome') : 'Yuk Wujudkan Rumah Impianmu Sekarang'; ?></h2>
        <p class="iklan-sub mx-auto" style="color:#d4d4d4;" data-iklan-reveal><?= $isEnglish ? lang('Store.promotionTermsBody') : 'Promo gratis ongkir berlaku selama stok masih tersedia. Konsultasikan kebutuhanmu langsung ke tim kami via WhatsApp.'; ?></p>
        <div class="d-flex gap-3 flex-wrap justify-content-center mb-4 iklan-actions" data-iklan-reveal>
            <a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>" class="iklan-btn-primary"><?= $isEnglish ? lang('Store.shopNow') : 'Belanja Sekarang'; ?></a>
            <a data-kbot-wa href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20mau%20tanya-tanya%20koleksinya" class="iklan-btn-outline" target="_blank" rel="noopener"><?= $isEnglish ? lang('Store.chatWhatsApp') : 'Chat WhatsApp'; ?></a>
        </div>
        <div class="mt-3" data-iklan-reveal>
            <span class="cta-mini-benefit"><i class="material-icons">local_shipping</i> <?= $isEnglish ? lang('Store.freeDelivery') : 'Gratis Ongkir'; ?></span>
            <span class="cta-mini-benefit"><i class="material-icons">verified</i> <?= $isEnglish ? lang('Store.qualityWarranty') : 'Garansi Kualitas'; ?></span>
            <span class="cta-mini-benefit"><i class="material-icons">support_agent</i> <?= $isEnglish ? lang('Store.responsiveSupport') : 'CS Responsif'; ?></span>
            <span class="cta-mini-benefit"><i class="material-icons">credit_card</i> <?= $isEnglish ? lang('Store.securePayment') : 'Pembayaran Aman'; ?></span>
        </div>
    </div>
</section>

</main>
<script>
(() => {
    const page = document.querySelector('.ilena-iklan-page');
    if (!page) return;
    const targets = Array.from(page.querySelectorAll('[data-iklan-reveal]'));
    const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
    if (!targets.length || !('IntersectionObserver' in window) || reducedMotion) {
        targets.forEach((target) => target.classList.add('is-visible'));
        return;
    }

    try {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -36px 0px' });

        targets.forEach((target) => {
            const siblings = Array.from(target.parentElement?.children ?? []).filter((item) => item.hasAttribute('data-iklan-reveal'));
            target.style.setProperty('--iklan-reveal-delay', `${Math.min(Math.max(siblings.indexOf(target), 0) * 75, 225)}ms`);
        });
        page.classList.add('iklan-motion-ready');
        targets.forEach((target) => observer.observe(target));
    } catch {
        page.classList.remove('iklan-motion-ready');
        targets.forEach((target) => target.classList.add('is-visible'));
    }
})();
</script>
<?php
$iklanStructuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => (string) ($metaTitle ?? 'Promo Furniture Sepaket Ilena'),
    'description' => (string) ($metaDeskripsi ?? ''),
    'url' => (string) ($metaUrl ?? site_url('iklan')),
    'inLanguage' => service('request')->getLocale() === 'en' ? 'en-US' : 'id-ID',
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Ilena Furniture',
        'url' => rtrim(base_url(), '/') . '/',
    ],
    'primaryImageOfPage' => [
        '@type' => 'ImageObject',
        'url' => (string) ($metaImage ?? ''),
        'caption' => (string) ($metaImageAlt ?? 'Promo furniture Ilena Furniture'),
    ],
    'mainEntity' => [
        '@type' => 'ItemList',
        'itemListElement' => array_map(static function (array $item, int $index): array {
            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => (string) ($item['nama'] ?? ''),
                'url' => (string) ($item['link'] ?? ''),
            ];
        }, $series ?? [], array_keys($series ?? [])),
    ],
];
?>
<script type="application/ld+json"><?= json_encode($iklanStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<?= $this->endSection(); ?>
