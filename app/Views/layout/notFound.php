<?php $isEnglish = (bool) ($isEnglish ?? false); ?>
<!doctype html>
<html lang="<?= $isEnglish ? 'en' : 'id'; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <meta name="theme-color" content="#fbf9f6">
    <title><?= $isEnglish ? 'Page not found | Ilena Furniture' : 'Halaman tidak ditemukan | Ilena Furniture'; ?></title>
    <meta name="description" content="<?= $isEnglish ? 'This Ilena Furniture page could not be found.' : 'Halaman Ilena Furniture ini tidak ditemukan.'; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --paper: #fbf9f6;
            --ink: #292622;
            --muted: #726b64;
            --line: #e9e3dc;
            --red: #e84a49;
            --wood: #8b684c;
            --display: "Cormorant Garamond", "Iowan Old Style", "Palatino Linotype", Georgia, serif;
            --body: "Manrope", -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { min-width: 320px; min-height: 100vh; margin: 0; background: var(--paper); color: var(--ink); font-family: var(--body); -webkit-font-smoothing: antialiased; }
        a { color: inherit; }
        .error-page { display: flex; min-height: 100svh; flex-direction: column; padding: 0 clamp(22px, 6vw, 88px); }
        .site-head { display: flex; width: min(1264px, 100%); min-height: 88px; align-items: center; justify-content: space-between; gap: 24px; margin: 0 auto; border-bottom: 1px solid var(--line); }
        .brand { display: inline-flex; align-items: center; text-decoration: none; }
        .brand img { display: block; width: 150px; height: 31px; object-fit: contain; object-position: left center; }
        .locale { display: flex; align-items: center; gap: 10px; color: var(--muted); font-size: 12px; letter-spacing: .08em; }
        .locale a { text-decoration: none; transition: color .16s ease; }
        .locale a:hover, .locale a[aria-current="page"] { color: var(--red); }
        .locale a[aria-current="page"] { font-weight: 700; }
        .locale span { color: #b7aea5; }
        main { display: grid; width: min(1040px, 100%); flex: 1; grid-template-columns: .9fr 1.1fr; align-items: center; gap: clamp(44px, 9vw, 132px); margin: 0 auto; padding: clamp(56px, 10vh, 112px) 0; }
        .visual { position: relative; display: flex; min-height: 310px; align-items: center; justify-content: center; border-right: 1px solid var(--line); }
        .visual::before { position: absolute; top: 50%; left: 50%; width: min(78%, 310px); aspect-ratio: 1; border: 1px solid #e5d9cc; border-radius: 50%; content: ""; transform: translate(-50%, -50%); }
        .error-number { position: relative; z-index: 1; margin: -26px 0 0 -17px; color: var(--red); font-family: var(--display); font-size: clamp(128px, 18vw, 220px); font-weight: 500; letter-spacing: -.085em; line-height: .78; white-space: nowrap; }
        .furniture-mark { position: absolute; z-index: 2; right: 5%; bottom: 12%; width: clamp(108px, 13vw, 150px); height: auto; overflow: visible; }
        .furniture-mark path, .furniture-mark line { fill: none; stroke: var(--wood); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2.1; }
        .furniture-mark .accent { stroke: var(--red); }
        .visual-caption { position: absolute; bottom: 1px; left: 0; color: var(--muted); font-size: 10px; letter-spacing: .13em; text-transform: uppercase; }
        .message { max-width: 500px; }
        .eyebrow { display: flex; align-items: center; gap: 10px; margin: 0 0 17px; color: var(--wood); font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .eyebrow::before { width: 24px; height: 1px; background: var(--red); content: ""; }
        h1 { margin: 0; font-family: var(--display); font-size: clamp(46px, 6vw, 76px); font-weight: 500; letter-spacing: -.035em; line-height: .98; }
        .description { max-width: 430px; margin: 20px 0 0; color: var(--muted); font-size: 14px; line-height: 1.85; }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 24px; margin-top: 31px; }
        .primary-link { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; gap: 13px; padding: 0 20px; border: 1px solid var(--red); background: var(--red); color: #fff; font-size: 12px; font-weight: 700; letter-spacing: .045em; text-decoration: none; transition: background-color .18s ease, border-color .18s ease; }
        .primary-link:hover { border-color: #d63d3c; background: #d63d3c; }
        .primary-link svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.7; }
        .secondary-link { color: var(--ink); font-size: 12px; font-weight: 700; text-decoration-color: #cbb9a9; text-underline-offset: 5px; transition: color .16s ease; }
        .secondary-link:hover { color: var(--red); }
        .brand-line { display: flex; width: min(1264px, 100%); align-items: center; justify-content: space-between; gap: 18px; margin: 0 auto; border-top: 1px solid var(--line); padding: 17px 0 22px; color: var(--muted); font-size: 11px; letter-spacing: .025em; }
        .brand-line strong { color: var(--wood); font-family: var(--display); font-size: 18px; font-weight: 500; letter-spacing: 0; }
        a:focus-visible { outline: 3px solid rgba(232, 74, 73, .45); outline-offset: 4px; }
        @media (max-width: 700px) {
            .error-page { padding-inline: 22px; }
            .site-head { min-height: 70px; }
            .brand img { width: 132px; height: 28px; }
            main { grid-template-columns: 1fr; gap: 42px; padding: 50px 0 62px; }
            .visual { min-height: 210px; justify-content: flex-start; border-right: 0; border-bottom: 1px solid var(--line); padding-bottom: 26px; }
            .visual::before { left: 43%; width: min(62vw, 230px); }
            .error-number { margin: -8px 0 0 6px; font-size: clamp(132px, 43vw, 190px); }
            .furniture-mark { right: 9%; bottom: 19%; width: 112px; }
            .visual-caption { bottom: 0; }
            h1 { max-width: 440px; font-size: clamp(48px, 13vw, 68px); }
            .description { font-size: 13px; }
            .brand-line { align-items: flex-end; padding-bottom: 14px; }
            .brand-line strong { max-width: 180px; font-size: 16px; text-align: right; }
        }
        @media (max-width: 380px) { .actions { align-items: flex-start; flex-direction: column; gap: 19px; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition: none !important; } }
    </style>
</head>
<body>
    <div class="error-page">
        <header class="site-head">
            <a class="brand" href="<?= $isEnglish ? '/en' : '/'; ?>" aria-label="Ilena Furniture — <?= $isEnglish ? 'Home' : 'Beranda'; ?>">
                <img src="/img/Logo Hitam Ilena.png" width="150" height="31" alt="Ilena Furniture">
            </a>
            <nav class="locale" aria-label="<?= $isEnglish ? 'Language' : 'Bahasa'; ?>">
                <a href="/" lang="id" hreflang="id"<?= $isEnglish ? '' : ' aria-current="page"'; ?>>ID</a>
                <span aria-hidden="true">/</span>
                <a href="/en" lang="en" hreflang="en"<?= $isEnglish ? ' aria-current="page"' : ''; ?>>EN</a>
            </nav>
        </header>
        <main>
            <div class="visual" aria-label="404">
                <div class="error-number" aria-hidden="true">404</div>
                <svg class="furniture-mark" viewBox="0 0 150 128" role="img" aria-label="<?= $isEnglish ? 'Line drawing of a chair' : 'Ilustrasi kursi'; ?>">
                    <path d="M39 54V28c0-7 5-12 12-12h48c7 0 12 5 12 12v26M32 56h86c7 0 12 5 12 12v21H20V68c0-7 5-12 12-12Z" />
                    <path d="M29 91v23m91-23v23M34 70h82" />
                    <path class="accent" d="M49 42h52M43 55v14m69-14v14" />
                    <line x1="11" y1="114" x2="139" y2="114" />
                </svg>
                <span class="visual-caption"><?= $isEnglish ? 'A little detour' : 'Sejenak tersesat'; ?></span>
            </div>
            <section class="message" aria-labelledby="not-found-title">
                <p class="eyebrow"><?= $isEnglish ? 'Page not found' : 'Halaman tidak ditemukan'; ?></p>
                <h1 id="not-found-title"><?= $isEnglish ? 'This page isn’t here.' : 'Halaman ini tidak ada di sini.'; ?></h1>
                <p class="description"><?= $isEnglish ? 'The link may have changed or the page may no longer be available. Find your way back to the pieces made for your home.' : 'Tautan mungkin sudah berubah atau halaman ini tidak lagi tersedia. Mari kembali menemukan furniture yang cocok untuk rumah Anda.'; ?></p>
                <div class="actions">
                    <a class="primary-link" href="<?= $isEnglish ? '/en' : '/'; ?>">
                        <?= $isEnglish ? 'Back to home' : 'Kembali ke beranda'; ?>
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M10 4l6 6-6 6" /></svg>
                    </a>
                    <a class="secondary-link" href="<?= $isEnglish ? '/en/product' : '/product'; ?>"><?= $isEnglish ? 'Explore the collection' : 'Jelajahi koleksi'; ?></a>
                </div>
            </section>
        </main>
        <footer class="brand-line">
            <span><?= $isEnglish ? 'Ilena Furniture' : 'Ilena Furniture'; ?></span>
            <strong>Mulai dari rumah, tercipta senyum ramah.</strong>
        </footer>
    </div>
</body>
</html>
