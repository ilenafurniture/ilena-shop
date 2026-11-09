<?php $isEnglish = (bool) ($isEnglish ?? false); ?>
<!doctype html>
<html lang="<?= $isEnglish ? 'en' : 'id'; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <meta name="theme-color" content="#f8f6f2">
    <title><?= $isEnglish ? 'Page not found | Ilena Furniture' : 'Halaman tidak ditemukan | Ilena Furniture'; ?></title>
    <meta name="description" content="<?= $isEnglish ? 'This Ilena Furniture page could not be found.' : 'Halaman Ilena Furniture ini tidak ditemukan.'; ?>">
    <style>
        :root{color-scheme:light;--ink:#211f1d;--muted:#706a64;--red:#a62c2b;--paper:#f8f6f2;--line:#e7e1d9}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;background:var(--paper);color:var(--ink);font-family:Arial,Helvetica,sans-serif}
        a{color:inherit}
        .page{min-height:100vh;display:flex;flex-direction:column;padding:clamp(20px,5vw,64px)}
        .top{display:flex;align-items:center;justify-content:space-between;gap:20px;width:min(1120px,100%);margin:0 auto}
        .brand{display:inline-flex;align-items:center;text-decoration:none}
        .brand img{display:block;width:clamp(132px,17vw,178px);height:auto;max-height:42px;object-fit:contain;object-position:left center}
        .language{display:flex;align-items:center;gap:9px;color:var(--muted);font-size:.82rem}
        .language a{text-decoration:none}
        .language a[aria-current=page]{color:var(--red);font-weight:700}
        main{display:grid;grid-template-columns:minmax(230px,.8fr) minmax(320px,1.2fr);align-items:center;gap:clamp(36px,8vw,112px);width:min(960px,100%);margin:auto;padding:72px 0}
        .code{position:relative;color:var(--red);font-family:Georgia,'Times New Roman',serif;font-size:clamp(8rem,22vw,17rem);font-weight:700;line-height:.8;letter-spacing:-.1em}
        .code::after{content:'';position:absolute;left:9px;right:4px;bottom:-23px;height:2px;background:var(--red);transform:scaleX(.72);transform-origin:left center}
        .copy{max-width:490px}
        .eyebrow{margin:0 0 14px;color:var(--red);font-size:.76rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase}
        h1{max-width:470px;margin:0;font-family:Georgia,'Times New Roman',serif;font-size:clamp(2.2rem,5vw,4rem);font-weight:500;line-height:1.08;letter-spacing:-.045em}
        .description{max-width:420px;margin:18px 0 0;color:var(--muted);font-size:1rem;line-height:1.7}
        .actions{display:flex;flex-wrap:wrap;gap:11px;margin-top:28px}
        .button{display:inline-flex;min-height:48px;align-items:center;justify-content:center;padding:0 18px;border:1px solid var(--red);border-radius:5px;background:var(--red);color:#fff;font-size:.92rem;font-weight:700;text-decoration:none;transition:background-color .16s ease,border-color .16s ease}
        .button:hover{border-color:#822120;background:#822120}
        .button--secondary{background:transparent;color:var(--ink);border-color:#bdb5ac}
        .button--secondary:hover{background:#fff;color:var(--ink);border-color:#8d847b}
        .button:focus-visible,.language a:focus-visible,.brand:focus-visible{outline:3px solid #d69a7b;outline-offset:4px}
        footer{width:min(1120px,100%);margin:0 auto;border-top:1px solid var(--line);padding-top:16px;color:var(--muted);font-family:Georgia,'Times New Roman',serif;font-size:.96rem}
        @media(max-width:680px){.page{padding:20px 22px 18px}.top{align-items:flex-start}.brand img{width:132px}.language{padding-top:7px}main{grid-template-columns:1fr;gap:38px;padding:16vh 0 13vh}.code{font-size:clamp(7rem,35vw,11rem)}.code::after{bottom:-15px;max-width:250px}h1{font-size:clamp(2.2rem,10vw,3.1rem)}.description{font-size:.96rem}.actions{display:grid;grid-template-columns:1fr}.button{width:100%}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{scroll-behavior:auto!important;transition:none!important}}
    </style>
</head>
<body>
    <div class="page">
        <header class="top">
            <a class="brand" href="<?= $isEnglish ? '/en' : '/'; ?>" aria-label="Ilena Furniture — <?= $isEnglish ? 'Home' : 'Beranda'; ?>">
                <img src="/img/Logo Hitam Ilena.png" width="174" height="36" alt="Ilena Furniture">
            </a>
            <nav class="language" aria-label="<?= $isEnglish ? 'Language' : 'Bahasa'; ?>">
                <a href="/" lang="id" hreflang="id"<?= $isEnglish ? '' : ' aria-current="page"'; ?>>ID</a>
                <span aria-hidden="true">/</span>
                <a href="/en" lang="en" hreflang="en"<?= $isEnglish ? ' aria-current="page"' : ''; ?>>EN</a>
            </nav>
        </header>
        <main>
            <div class="code" aria-hidden="true">404</div>
            <section class="copy" aria-labelledby="not-found-title">
                <p class="eyebrow"><?= $isEnglish ? 'Page not found' : 'Halaman tidak ditemukan'; ?></p>
                <h1 id="not-found-title"><?= $isEnglish ? 'We couldn’t find this page.' : 'Ruang ini belum kami temukan.'; ?></h1>
                <p class="description"><?= $isEnglish ? 'The address may have changed or the page may no longer be available. Let’s help you find your way back.' : 'Tautannya mungkin berubah atau halaman ini sudah tidak tersedia. Mari kembali dan temukan yang kakak cari.'; ?></p>
                <div class="actions">
                    <a class="button" href="<?= $isEnglish ? '/en' : '/'; ?>"><?= $isEnglish ? 'Back to home' : 'Kembali ke beranda'; ?></a>
                    <a class="button button--secondary" href="<?= $isEnglish ? '/en/product' : '/product'; ?>"><?= $isEnglish ? 'Browse furniture' : 'Jelajahi furniture'; ?></a>
                </div>
            </section>
        </main>
        <footer>Mulai dari rumah, tercipta senyum ramah.</footer>
    </div>
</body>
</html>
