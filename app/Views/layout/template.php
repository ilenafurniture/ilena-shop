<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale(), 'attr'); ?>" translate="no">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16806007994">
    </script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'AW-16806007994');
    </script>
    <!-- TikTok Pixel Code Start -->
    <meta name="facebook-domain-verification" content="7gteseil6x5vlinogoixcvrdb7zoci" />
    <!-- Meta Pixel Code -->
    <script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1030536602870149');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1030536602870149&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
    <!-- Google Tag Manager -->
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-K5BLN7VV');
    </script>
    <!-- End Google Tag Manager -->
    <script>
    ! function(w, d, t) {
        w.TiktokAnalyticsObject = t;
        var ttq = w[t] = w[t] || [];
        ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias",
            "group", "enableCookie", "disableCookie", "holdConsent", "revokeConsent", "grantConsent"
        ], ttq.setAndDefer = function(t, e) {
            t[e] = function() {
                t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
            }
        };
        for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
        ttq.instance = function(t) {
            for (
                var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
            return e
        }, ttq.load = function(e, n) {
            var r = "https://analytics.tiktok.com/i18n/pixel/events.js",
                o = n && n.partner;
            ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = r, ttq._t = ttq._t || {}, ttq._t[e] = +new Date,
                ttq._o = ttq._o || {}, ttq._o[e] = n || {};
            n = document.createElement("script");
            n.type = "text/javascript", n.async = !0, n.src = r + "?sdkid=" + e + "&lib=" + t;
            e = document.getElementsByTagName("script")[0];
            e.parentNode.insertBefore(n, e)
        };
        ttq.load('CR2OGUBC77U99EQB0MMG');
        ttq.page();
    }(window, document, 'ttq');
    </script>
    <!-- TikTok Pixel Code End -->
    <meta name="google-site-verification" content="fbuobh8O6hdzi1dFtDU48hqyMlHD0xvCfObbr1EgD0M" />
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-6Q18QTYQE0');
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Y4Z51NJ3QM"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-Y4Z51NJ3QM');
    </script>
    <!-- Google Tag Manager -->
    <!-- <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-P2Q6W4SH');
    </script> -->
    <!-- End Google Tag Manager -->

    <?php
    $isEnglishDocument = service('request')->getLocale() === 'en';
    $documentTitle = trim((string) ($metaTitle ?? ''));
    if ($documentTitle === '') {
        $documentTitle = trim((string) ($title ?? 'Ilena Furniture')) . ' | Ilena Furniture';
    }
    $documentDescription = trim((string) ($metaDeskripsi ?? ''));
    if ($documentDescription === '') {
        $descriptionSubject = trim((string) ($title ?? 'Ilena Furniture'));
        $documentDescription = $isEnglishDocument
            ? (($descriptionSubject !== '' ? $descriptionSubject . ' — ' : '') . 'Explore Ilena Furniture collections, product information, and services for a more comfortable home.')
            : (($descriptionSubject !== '' ? $descriptionSubject . ' — ' : '') . 'Jelajahi koleksi, informasi produk, dan layanan Ilena Furniture untuk rumah yang lebih nyaman.');
    }
    $canonicalPath = trim((string) service('uri')->getPath(), '/');
    $defaultCanonicalUrl = $canonicalPath === '' ? rtrim(base_url(), '/') . '/' : site_url($canonicalPath);
    $localeNeutralPath = trim(App\Services\StorefrontLayoutPolicy::normalizePath($canonicalPath), '/');
    $idAlternateUrl = $localeNeutralPath === '' ? rtrim(base_url(), '/') . '/' : site_url($localeNeutralPath);
    $enAlternateUrl = $localeNeutralPath === '' ? rtrim(base_url(), '/') . '/en' : site_url('en/' . $localeNeutralPath);
    $metaAlternateUrls = isset($metaAlternateUrls) && is_array($metaAlternateUrls)
        ? $metaAlternateUrls
        : ['id' => $idAlternateUrl, 'en' => $enAlternateUrl, 'x-default' => $idAlternateUrl];
    $alternateIdUrl = trim((string) ($metaAlternateUrls['id'] ?? $idAlternateUrl));
    $alternateEnUrl = trim((string) ($metaAlternateUrls['en'] ?? ''));
    $alternateDefaultUrl = trim((string) ($metaAlternateUrls['x-default'] ?? $alternateIdUrl));
    $documentUrl = trim((string) ($metaUrl ?? $defaultCanonicalUrl));
    if ($documentUrl === '') {
        $documentUrl = $defaultCanonicalUrl;
    }
    if (!preg_match('#^https?://#i', $documentUrl)) {
        $documentUrl = site_url(ltrim($documentUrl, '/'));
    }
    $documentImage = trim((string) ($metaImage ?? 'https://ilenafurniture.com/img/logo/N.png'));
    $documentImageAlt = trim((string) ($metaImageAlt ?? 'Ilena Furniture'));
    $siteBaseUrl = rtrim(base_url(), '/') . '/';
    $isHomePage = $localeNeutralPath === '';
    $documentRobots = trim((string) ($metaRobots ?? ''));
    if ($documentRobots === '') {
        $noindexPrefixes = ['admin', 'dev', 'staff', 'cart', 'wishlist', 'address', 'login', 'register', 'verify', 'account', 'order', 'orderdetail', 'invoice', 'shipping', 'payment', 'progresspay', 'successpay', 'cencelpay', 'expirepay', 'find', 'search'];
        $robotsPath = strtolower($localeNeutralPath);
        $documentRobots = 'index, follow';
        foreach ($noindexPrefixes as $prefix) {
            if ($robotsPath === $prefix || str_starts_with($robotsPath, $prefix . '/')) {
                $documentRobots = 'noindex, follow';
                break;
            }
        }
        $requestQuery = service('request')->getGet();
        if ($localeNeutralPath === 'product' && $requestQuery !== []) {
            $documentRobots = 'noindex, follow';
        }
        if ($localeNeutralPath === 'partner' && array_intersect(['q', 'city', 'page'], array_keys($requestQuery))) {
            $documentRobots = 'noindex, follow';
        }
    }
    ?>
    <?php if ($isHomePage) : ?>
    <?php
    $organizationId = $siteBaseUrl . '#organization';
    $siteIdentity = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'OnlineStore',
                '@id' => $organizationId,
                'name' => 'Ilena Furniture',
                'alternateName' => ['ILENA', 'I L E N A'],
                'legalName' => 'CV Catur Bhakti Mandiri',
                'url' => $siteBaseUrl,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => base_url('img/logo/N.png'),
                    'width' => 161,
                    'height' => 160,
                ],
                'image' => base_url('img/logo/N.png'),
                'description' => $isEnglishDocument
                    ? 'Ilena Furniture is an Indonesian furniture brand by CV Catur Bhakti Mandiri, creating furniture for homes, offices, and hospitality spaces.'
                    : 'Ilena Furniture adalah merek furniture modern dari CV Catur Bhakti Mandiri, produsen furniture berbahan kayu di Semarang, Jawa Tengah, untuk kebutuhan rumah, perkantoran, dan perhotelan.',
                'slogan' => 'Mulai dari rumah, tercipta senyum ramah.',
                'knowsAbout' => ['Furniture rumah', 'Furniture perkantoran', 'Furniture perhotelan', 'Desain interior'],
                'email' => 'cs@ilenafurniture.com',
                'telephone' => '+628112938158',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Jalan Lingkar Taman Industri, Jatibarang, Mijen',
                    'addressLocality' => 'Semarang',
                    'addressRegion' => 'Jawa Tengah',
                    'addressCountry' => 'ID',
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer service',
                    'email' => 'cs@ilenafurniture.com',
                    'telephone' => '+628112938158',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => $siteBaseUrl . '#website',
                'url' => $siteBaseUrl,
                'name' => 'Ilena Furniture',
                'alternateName' => 'ILENA',
                'inLanguage' => $isEnglishDocument ? 'en' : 'id-ID',
                'publisher' => ['@id' => $organizationId],
            ],
        ],
    ];
    ?>
    <script type="application/ld+json"><?= json_encode($siteIdentity, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
    <?php endif; ?>
    <?php if (isset($produk['nama'], $produk['id']) && is_array($produk)) : ?>
    <?php
    $schemaVariantRows = is_array($produk['varian'] ?? null) ? $produk['varian'] : [];
    $schemaStock = 0;
    foreach ($schemaVariantRows as $schemaVariant) {
        if (is_array($schemaVariant)) $schemaStock += max(0, (int) ($schemaVariant['stok'] ?? 0));
    }
    $schemaPrice = \App\Services\CatalogPricing::unitPrice($produk);
    $schemaProductName = function_exists('ilena_product_display_name')
        ? ilena_product_display_name($produk, $isEnglishDocument ? 'en' : 'id')
        : (string) $produk['nama'];
    $rawSchemaDescription = $isEnglishDocument && trim((string) ($produk['deskripsi']['deskripsi_en'] ?? '')) !== ''
        ? (string) $produk['deskripsi']['deskripsi_en']
        : (string) ($produk['deskripsi']['deskripsi'] ?? '');
    $schemaDescription = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($rawSchemaDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    $schemaProduct = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $schemaProductName,
        'image' => $documentImage,
        'sku' => (string) $produk['id'],
        'brand' => ['@type' => 'Brand', 'name' => 'Ilena Furniture'],
        'category' => function_exists('ilena_catalog_type_label')
            ? ilena_catalog_type_label((string) ($produk['kategori'] ?? 'Furniture'), $isEnglishDocument ? 'en' : 'id')
            : (string) ($produk['kategori'] ?? 'Furniture'),
    ];
    if ($schemaDescription !== '') $schemaProduct['description'] = $schemaDescription;
    if ($schemaPrice > 0) {
        $schemaProduct['offers'] = [
            '@type' => 'Offer',
            'url' => $documentUrl,
            'priceCurrency' => 'IDR',
            'price' => $schemaPrice,
            'itemCondition' => 'https://schema.org/NewCondition',
            'availability' => $schemaStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ];
    }
    ?>
    <script type="application/ld+json"><?= json_encode($schemaProduct, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
    <?php endif; ?>
    <meta property="og:title" content="<?= esc($documentTitle, 'attr'); ?>">
    <meta property="og:description" content="<?= esc($documentDescription, 'attr'); ?>">
    <meta property="og:image" content="<?= esc($documentImage, 'attr'); ?>">
    <meta property="og:image:alt" content="<?= esc($documentImageAlt, 'attr'); ?>">
    <meta property="og:url" content="<?= esc($documentUrl, 'attr'); ?>">
    <meta property="og:type" content="<?= isset($produk['id']) && is_array($produk) ? 'product' : 'website'; ?>">
    <meta property="og:locale" content="<?= $isEnglishDocument ? 'en_US' : 'id_ID'; ?>">
    <?php if ($alternateEnUrl !== '') : ?><meta property="og:locale:alternate" content="<?= $isEnglishDocument ? 'id_ID' : 'en_US'; ?>"><?php endif; ?>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#e84a49">
    <meta name="msapplication-navbutton-color" content="#e84a49">
    <meta name="apple-mobile-web-app-status-bar-style" content="#e84a49">
    <meta name="crome-mobile-web-app-status-bar-style" content="#e84a49">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ilena furniture">
    <meta name="author" content="Ilena Furniture">
    <meta name="title" content="<?= esc($documentTitle, 'attr'); ?>">
    <meta name="description" content="<?= esc($documentDescription, 'attr'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:site_name" content="Ilena Furniture">
    <meta name="robots" content="<?= esc($documentRobots, 'attr'); ?>">
    <link rel="canonical" href="<?= esc($documentUrl, 'attr'); ?>">
    <?php if ($alternateIdUrl !== '') : ?><link rel="alternate" hreflang="id" href="<?= esc($alternateIdUrl, 'attr'); ?>"><?php endif; ?>
    <?php if ($alternateEnUrl !== '') : ?><link rel="alternate" hreflang="en" href="<?= esc($alternateEnUrl, 'attr'); ?>"><?php endif; ?>
    <?php if ($alternateDefaultUrl !== '') : ?><link rel="alternate" hreflang="x-default" href="<?= esc($alternateDefaultUrl, 'attr'); ?>"><?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($documentTitle, 'attr'); ?>">
    <meta name="twitter:description" content="<?= esc($documentDescription, 'attr'); ?>">
    <meta name="twitter:image" content="<?= esc($documentImage, 'attr'); ?>">
    <meta name="twitter:image:alt" content="<?= esc($documentImageAlt, 'attr'); ?>">
    <title><?= esc($documentTitle); ?></title>
    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/icon?family=Material+Icons" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"></noscript>
    <?php if ($title == 'Pembayaran') { ?>
    <!-- <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-aGWfdxs2btRH4xSd" async></script> -->
    <script rel="preconnect" id="midtrans-script" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js"
        data-environment="<?= $emailUji ? 'sandbox' : 'production'; ?>"
        data-client-key="<?= $emailUji ? 'SB-Mid-client-aGWfdxs2btRH4xSd' : 'Mid-client-9sUvUz3XTM_xqGOp'; ?>"
        type="text/javascript" async>
    </script>
    <?php } ?>

    <link rel="stylesheet" href="<?= base_url('css/style.css?v=' . filemtime(FCPATH . 'css/style.css')); ?>">
    <link rel="stylesheet" href="<?= base_url('css/ilena-dialog.css?v=20261027'); ?>">
    <link rel="stylesheet"
        href="<?= base_url('css/ilena-storefront.css?v=' . filemtime(FCPATH . 'css/ilena-storefront.css')); ?>">
    <link rel="stylesheet" href="<?= base_url('css/ilena-commerce-extras.css?v=' . filemtime(FCPATH . 'css/ilena-commerce-extras.css')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
    // Public pages may be prefixed with /en. Resolve assets against the
    // locale-neutral route so both /partner and /en/partner share the shell.
    $layoutPath = App\Services\StorefrontLayoutPolicy::normalizePath((string) service('uri')->getPath());
    $editorialPagePath = $layoutPath;
    ?>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <?php if (in_array($editorialPagePath, ['/', '/tentang', '/partner', '/article', '/iklan'], true)) : ?>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet"></noscript>
    <?php endif; ?>
    <?php if (in_array($editorialPagePath, ['/tentang', '/partner'], true)) : ?>
    <link rel="stylesheet"
        href="<?= base_url('css/ilena-editorial-pages.css?v=' . filemtime(FCPATH . 'css/ilena-editorial-pages.css')); ?>">
    <?php endif; ?>
    <link rel="icon" href="<?= base_url('img/logo/N.png?v=' . date('d-m-Y')); ?>" type="image/png">
    <script>
    (() => {
        const fallbackImageUrl =
            <?= json_encode(base_url('img/ilena-image-placeholder.png'), JSON_UNESCAPED_SLASHES); ?>;
        document.addEventListener('error', (event) => {
            const image = event.target;
            if (!(image instanceof HTMLImageElement)) return;
            if (image.dataset.ilenaFallbackApplied === 'true') {
                image.hidden = true;
                return;
            }
            image.dataset.ilenaFallbackApplied = 'true';
            image.classList.add('ilena-image-fallback');
            image.src = fallbackImageUrl;
        }, true);
    })();
    </script>
</head>

<?php
$isInternalArea = App\Services\StorefrontLayoutPolicy::isInternal($layoutPath);
$layoutHeaderMode = App\Services\StorefrontLayoutPolicy::headerMode($layoutPath);
$needsReactRuntime = in_array($layoutHeaderMode, ['full', 'auth', 'account'], true) || $layoutPath === '/article';
$contentProvidesMain = in_array($layoutPath, ['/contact', '/iklan', '/partner', '/visimisi', '/tentang'], true)
    || str_starts_with($layoutPath, '/invoice/');
$layoutPageSegments = array_values(array_filter(explode('/', trim($layoutPath, '/'))));
$layoutPageSlug = preg_replace('/[^a-z0-9-]+/', '-', $layoutPageSegments[0] ?? 'home') ?: 'home';
?>

<body
    class="<?= $isInternalArea ? 'ilena-internal' : 'ilena-storefront ilena-header--' . $layoutHeaderMode . ' ilena-page--' . esc($layoutPageSlug, 'attr'); ?>">
    <?php if (!$isInternalArea): ?><a class="ilena-skip-link" href="#main-content">Lewati ke konten utama</a><?php endif; ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5BLN7VV" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-8L3XG70VSN"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    // end

    gtag('config', 'G-8L3XG70VSN');
    </script>

    <div class="toast start-50 translate-middle">
        <div class="toast-body">
            <p aria-live="polite" aria-atomic="true"></p>
            <div class="mt-2 pt-2 border-top d-flex gap-1">
                <form method="post">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Ok</button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm" onclick="hapusToast()">Batal</button>
            </div>
        </div>
    </div>

    <script defer src="<?= base_url('js/bootstrap.bundle.min.js'); ?>"></script>
    <?php $wawaImageVersion = (string) filemtime(FCPATH . 'js/ilena-support-chat.js'); ?>
    <div class="container-melayang d-flex gap-2 align-items-end <?= isset($geser_container_melayang) ? 'geser' : ''; ?>" data-ilena-chat-widget data-chat-locale="<?= esc(service('request')->getLocale(), 'attr'); ?>" data-wawa-image-base="<?= esc(base_url('img/fotowawa/'), 'attr'); ?>" data-wawa-image-version="<?= esc($wawaImageVersion, 'attr'); ?>" data-context-product="<?= isset($produk) && is_array($produk) && isset($produk['id']) ? esc((string)$produk['id'], 'attr') : ''; ?>">
        <section id="container-greeting-card" class="ilena-chat-greeting d-none" aria-label="<?= esc(lang('Store.chatAriaLabel'), 'attr'); ?>">
            <div class="ilena-chat-greeting__panel">
                <div class="ilena-chat-resize-handle" data-chat-resize role="group" tabindex="0" aria-label="<?= esc(lang('Store.chatResize'), 'attr'); ?>" title="<?= esc(lang('Store.chatResize'), 'attr'); ?>"></div>
                <header class="ilena-chat-greeting__heading">
                    <div class="ilena-chat-brand"><span class="ilena-chat-brand__mark ilena-chat-brand__mark--wawa" aria-hidden="true"><img data-wawa-avatar="header" alt="" width="360" height="360" loading="lazy" decoding="async"></span><span class="ilena-chat-brand__copy"><p>Kak Wawa</p><small><i aria-hidden="true"></i><span data-chat-status><?= esc(lang('Store.chatReady')); ?></span></small></span></div>
                    <button type="button" class="ilena-chat-sound" data-chat-sound aria-pressed="true" aria-label="<?= esc(lang('Store.chatMuteSound'), 'attr'); ?>" title="<?= esc(lang('Store.chatSoundOn'), 'attr'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5Zm4 4a4 4 0 0 1 0 6m3-9a8 8 0 0 1 0 12"/></svg></button>
                    <button type="button" aria-label="<?= esc(lang('Store.chatClose'), 'attr'); ?>" data-close-chat-greeting><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                </header>
                <div class="ilena-chat-transcript" data-chat-messages role="log" aria-live="polite" aria-relevant="additions" aria-atomic="false" aria-label="<?= esc(lang('Store.chatHistory'), 'attr'); ?>">
                    <div class="ilena-chat-welcome"><p class="ilena-chat-welcome__eyebrow"><?= esc(lang('Store.chatWelcomeEyebrow')); ?></p><h2><?= esc(lang('Store.chatWelcomeTitle')); ?></h2><p><?= esc(lang('Store.chatWelcomeBody')); ?></p><p class="ilena-chat-reply-time"><span aria-hidden="true">◷</span><?= esc(lang('Store.chatReplyTime')); ?></p><div class="ilena-chat-suggestions" aria-label="<?= esc(lang('Store.chatSuggestions'), 'attr'); ?>"><button type="button" data-chat-suggestion="<?= esc(lang('Store.chatSuggestionSofaValue'), 'attr'); ?>"><?= esc(lang('Store.chatSuggestionSofa')); ?></button><button type="button" data-chat-suggestion="<?= esc(lang('Store.chatSuggestionDiningValue'), 'attr'); ?>"><?= esc(lang('Store.chatSuggestionDining')); ?></button><button type="button" data-chat-suggestion="<?= esc(lang('Store.chatSuggestionBudgetValue'), 'attr'); ?>"><?= esc(lang('Store.chatSuggestionBudget')); ?></button></div></div>
                    <div class="ilena-chat-typing" data-chat-typing role="status" aria-live="polite" hidden><img class="ilena-chat-typing__avatar" data-wawa-avatar="typing" alt="" width="360" height="360" loading="lazy" decoding="async"><span aria-hidden="true"><i></i><i></i><i></i></span><b data-chat-typing-copy><?= esc(lang('Store.chatTyping')); ?></b></div>
                </div>
                <form class="ilena-chat-compose" data-chat-form>
                    <?= csrf_field(); ?>
                    <label class="visually-hidden" for="ilenaChatInput"><?= esc(lang('Store.chatMessageLabel')); ?></label>
                    <textarea id="ilenaChatInput" name="message" rows="1" maxlength="1200" placeholder="<?= esc(lang('Store.chatInputPlaceholder'), 'attr'); ?>" required></textarea>
                    <button type="submit" aria-label="<?= esc(lang('Store.chatSend'), 'attr'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12 20 4l-5 16-3-7-8-1Zm8 1 8-9"/></svg></button>
                </form>
                <form class="ilena-chat-contact" data-chat-contact-form hidden>
                    <div><strong><?= esc(lang('Store.chatHandoffTitle')); ?></strong><p><?= esc(lang('Store.chatHandoffBody')); ?></p></div>
                    <label><?= esc(lang('Store.chatNameLabel')); ?><input name="contactName" type="text" autocomplete="name" minlength="2" maxlength="100" required></label>
                    <label><?= esc(lang('Store.chatPhoneLabel')); ?><input name="contactPhone" type="tel" inputmode="tel" autocomplete="tel" minlength="8" maxlength="24" pattern="[+0-9 ().-]{8,24}" placeholder="<?= esc(lang('Store.chatPhonePlaceholder'), 'attr'); ?>" required></label>
                    <div class="ilena-chat-contact__actions"><button type="submit"><?= esc(lang('Store.chatHandoffSubmit')); ?></button><button type="button" data-chat-contact-cancel><?= esc(lang('Store.chatCancel')); ?></button></div>
                </form>
                <footer class="ilena-chat-footer"><a data-kbot-wa href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20saya%20butuh%20bantuan" target="_blank" rel="noopener noreferrer">WhatsApp</a></footer>
                <p class="ilena-chat-notice" data-chat-notice role="status" hidden></p>
            </div>
        </section>
        <button type="button" class="ilena-back-to-top" data-back-to-top aria-label="<?= esc(lang('Store.backToTop'), 'attr'); ?>" aria-hidden="true" tabindex="-1">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 19V5m-7 7 7-7 7 7"/></svg>
        </button>
        <div class="ilena-chat-dock d-flex flex-column gap-2">
            <div class="ilena-chat-greeting-teaser" data-chat-greeting role="group" aria-label="<?= esc(lang('Store.chatGreetingAria'), 'attr'); ?>" aria-live="polite" hidden>
                <div class="ilena-chat-greeting-teaser__bubble">
                    <button type="button" data-greeting-dismiss aria-label="<?= esc(lang('Store.chatGreetingDismiss'), 'attr'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                    <strong><?= esc(lang('Store.chatGreetingTitle')); ?></strong>
                    <p><?= esc(lang('Store.chatGreetingBody')); ?></p>
                    <button type="button" data-greeting-open aria-label="<?= esc(lang('Store.chatGreetingOpen'), 'attr'); ?>"><?= esc(lang('Store.chatGreetingButton')); ?></button>
                </div>
                <span class="ilena-chat-greeting-teaser__mark" aria-hidden="true"><img data-wawa-avatar="greeting" alt="" width="360" height="360" loading="lazy" decoding="async"></span>
            </div>
            <button type="button" class="btn-circle hitam ilena-floating-chat" id="ilenaAssistantLauncher" data-open-ilena-chat aria-label="<?= esc(lang('Store.chatLauncher'), 'attr'); ?>" aria-expanded="false" aria-controls="container-greeting-card">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 11.5a7.5 7.5 0 0 1-11.2 6.5L4 19l1-4.4A7.5 7.5 0 1 1 20 11.5Z"/><path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"/></svg>
            </button>
        </div>
    </div>
    <script defer src="<?= base_url('js/ilena-support-chat.js?v=' . filemtime(FCPATH . 'js/ilena-support-chat.js')); ?>"></script>

    <?php if (!$isInternalArea && $needsReactRuntime): ?>
    <script defer crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
    <script defer crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
    <script defer crossorigin src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <?php endif; ?>


    <?php
    $storeCartItems = session()->get('keranjang');
    $storeCartItems = is_array($storeCartItems) ? $storeCartItems : [];
    $storeCartQuantity = array_sum(array_map(static fn ($item): int => is_array($item) ? max(0, (int) ($item['jumlah'] ?? 0)) : 0, $storeCartItems));
    ?>
    <?php if (!$isInternalArea && in_array($layoutHeaderMode, ['full', 'auth', 'account'], true)): ?>
    <?= $this->include('layout/navbar', ['headerMode' => $layoutHeaderMode, 'cartQuantity' => $storeCartQuantity]); ?>
    <?= $this->include('layout/navbarHp', ['cartQuantity' => $storeCartQuantity]); ?>
    <?php elseif (!$isInternalArea): ?>
    <?= $this->include('layout/navbarCompact', ['headerMode' => $layoutHeaderMode, 'cartQuantity' => $storeCartQuantity]); ?>
    <?php endif; ?>
    <<?= $contentProvidesMain ? 'div' : 'main'; ?> id="main-content" tabindex="-1" style="flex: 1; position: relative; min-height: 500px;">
        <?= $this->renderSection('content'); ?>
    </<?= $contentProvidesMain ? 'div' : 'main'; ?>>
    <?= $this->include('layout/footer'); ?>
    <?php if (!$isInternalArea): ?>
    <div class="ilena-cart-feedback" data-ilena-cart-feedback data-network-message="<?= esc(lang('Store.cartAddNetworkFeedback'), 'attr'); ?>" role="status" aria-live="polite" aria-atomic="true" hidden></div>
    <script defer src="<?= base_url('js/ilena-cart-feedback.js?v=' . (is_file(FCPATH . 'js/ilena-cart-feedback.js') ? filemtime(FCPATH . 'js/ilena-cart-feedback.js') : '1')); ?>"></script>
    <script defer src="<?= base_url('js/ilena-commerce-extras.js?v=' . filemtime(FCPATH . 'js/ilena-commerce-extras.js')); ?>"></script>
    <?php endif; ?>
    <script>
    const toastElm = document.querySelector(".toast")
    const toastTeksElm = document.querySelector(".toast p")
    const toastOkElm = document.querySelector(`.toast form`)
    const toastCloseElm = document.querySelector(`.toast button[type="button"]`)

    function triggerToast(text, linkAction) {
        toastElm.classList.add("show")
        toastTeksElm.innerHTML = text
        toastOkElm.action = linkAction;
        if (!linkAction) {
            toastOkElm.classList.add('d-none');
            toastCloseElm.innerHTML = 'Ok';
        } else {
            toastOkElm.classList.remove('d-none');
            toastCloseElm.innerHTML = 'Batal';
        }
    }

    function hapusToast() {
        toastElm.classList.remove("show")
    }

    function copytext(teks) {
        navigator.clipboard.writeText(teks);
    }

    function pergiKeProduk(namaProduk) {
        window.location.href = '/product/' + namaProduk
    }
    </script>

    <!-- Tracking -->
    <script>
    (() => {
        // abaikan halaman admin atau bot
        const isAdmin = location.pathname.startsWith('/admin');
        if (isAdmin) return;
        // kamu juga bisa nambah pengecualian lain kalau perlu

        // catat start time
        const t0 = Date.now();

        // data dasar
        const payload = {
            path: location.pathname || '/',
            ref: document.referrer || '',
            ua: navigator.userAgent || '',
            w: screen.width,
            h: screen.height,
        };

        // fungsi kirim pakai Beacon (aman saat halaman ditutup)
        function sendBeacon(extra = {}) {
            const data = JSON.stringify({
                ...payload,
                ...extra
            });
            const url = '/track/hit';
            if (navigator.sendBeacon) {
                const blob = new Blob([data], {
                    type: 'application/json'
                });
                navigator.sendBeacon(url, blob);
            } else {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: data,
                    keepalive: true
                }).catch(() => {});
            }
        }

        // pageview awal (durasi=0)
        sendBeacon({
            durasi: 0,
            event: 'pv'
        });

        // heartbeat tiap 15 detik agar durasi “real”
        let timer = setInterval(() => {
            const dur = Math.round((Date.now() - t0) / 1000);
            sendBeacon({
                durasi: dur,
                event: 'hb'
            });
        }, 15000);

        // saat akan keluar halaman, kirim durasi final
        window.addEventListener('pagehide', () => {
            clearInterval(timer);
            const dur = Math.round((Date.now() - t0) / 1000);
            sendBeacon({
                durasi: dur,
                event: 'leave'
            });
        });
    })();
    </script>

    <script
        src="<?= base_url('js/ilena-smart-search.js?v=' . (is_file(FCPATH . 'js/ilena-smart-search.js') ? filemtime(FCPATH . 'js/ilena-smart-search.js') : time())); ?>"
        defer></script>
    <script src="<?= base_url('js/ilena-dialog.js?v=20261027'); ?>"></script>
    <script src="https://go.kreasibot.com/k.js" data-kreasibot="ilenafurniture" defer></script>
</body>

</html>
