<!DOCTYPE html>
<html lang="en" translate="no">

<head>
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
    <!-- TikTok Pixel Code Start -->
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
    })(window, document, 'script', 'dataLayer', 'GTM-P2Q6W4SH');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6Q18QTYQE0"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-6Q18QTYQE0');
    </script>

    <?php if (isset($produk)) {
        if (isset($produk['nama'])) { ?>
    <script type="application/ld+json">
    {
        "@context": "https://ilenafurniture.com/",
        "@type": "produk",
        "name": "<?= $produk['nama'] ?>",
        "image": "https://ilenafurniture.com/viewpic/<?= $produk['id'] ?>",
        "description": "<?= $produk['deskripsi']['deskripsi'] ?>",
        "sku": "<?= $produk['id']; ?>",
        "offers": {
            "@type": "Offer",
            "priceCurrency": "IDR",
            "price": "<?= $produk['harga'] ?>",
            "itemCondition": "New",
            "availability": "in_stok"
        }
    }
    </script>
    <?php }
    } ?>


    <meta charset="UTF-8">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ilena furniture">
    <meta name="author" content="Ilena Furniture">
    <meta name="title" content="<?= $title ?> | I L E N A">
    <meta data-rh="true" name="title" content="<?= $title ?> | I L E N A">
    <meta name="description"
        content="<?= isset($metaDeskripsi) ? $metaDeskripsi : 'Toko Online furniture Semarang meliputi produk buffet tv, bed, lemari pakaian, coffee table, meja konsol, rak buku, dresser, meja nakas, dll. '; ?>">
    <meta name="keywords"
        content="<?= isset($metaKeyword) ? $metaKeyword : 'Sorely Ilena Semarang,Cabana Ilena Semarang,Orca Ilena Semarang,Plint Base Ilena Semarang,Cutout Ilena Semarang,Living Room Ilena Semarang,Bed Room Ilena Semarang,Lounge Room Ilena Semarang'; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta data-rh="true" property="og:title" content="Ilena Furniture">
    <meta data-rh="true" property="og:site_name" content="Ilena Furniture">
    <meta data-rh="true" property="og:url" content="https://ilenafurniture.com/">
    <meta data-rh="true" property="og:image" content="https://ilenafurniture.com/logo-icon.png">
    <meta data-rh="true" property="product:price:currency" content="Rp">
    <meta data-rh="true" property="product:price:amount" content="0">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://ilenafurniture.com/product">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $title ?> | I L E N A">
    <meta name="twitter:description"
        content="<?= isset($metaDeskripsi) ? $metaDeskripsi : 'Toko Online furniture Semarang meliputi produk buffet tv, bed, lemari pakaian, coffee table, meja konsol, rak buku, dresser, meja nakas, dll. '; ?>">
    <meta name="twitter:image" content="https://ilenafurniture.com/logo-icon.png">
    <title><?= $title; ?> | I L E N A</title>
    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php if ($title == 'Pembayaran') { ?>
    <!-- <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-aGWfdxs2btRH4xSd" async></script> -->
    <script rel="preconnect" id="midtrans-script" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js"
        data-environment="<?= $emailUji ? 'sandbox' : 'production'; ?>"
        data-client-key="<?= $emailUji ? 'SB-Mid-client-aGWfdxs2btRH4xSd' : 'Mid-client-9sUvUz3XTM_xqGOp'; ?>"
        type="text/javascript" async>
    </script>
    <?php } ?>
    <link rel="stylesheet" href="<?= base_url('css/style.css?v=1.44'); ?>">
    <link rel="icon" href="<?= base_url('logo-icon.png?v=2.0'); ?>" type="image/png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
</head>

<body>
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
            <p>Hello, world! This is a toast message.</p>
            <div class="mt-2 pt-2 border-top">
                <a type="button" class="btn btn-danger btn-sm">Ok</a>
                <button type="button" class="btn btn-secondary btn-sm" onclick="hapusToast()">Batal</button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('js/bootstrap.bundle.min.js'); ?>"></script>
    <div
        class="container-melayang d-flex gap-2 align-items-end <?= isset($geser_container_melayang) ? 'geser' : ''; ?>">
        <div id="container-greeting-card" class="d-none">
            <div style="background-color: white; border-radius: 1em; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);"
                class="p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <p class="m-0">Hai!</p>
                    <p class="m-0" style="cursor: pointer;" onclick="closeGreetingCard()">x</p>
                </div>
                <p class="m-0">Kalau kamu butuh bantuan,<br>hubungi kami via WhatsApp ya!</p>
            </div>
            <div style="height: 30px;"></div>
        </div>
        <div class="d-flex flex-column gap-2">
            <!--<a class="btn-circle" href="/form"><i class="material-icons">insert_comment</i></a>-->
            <a class="btn-circle hitam" id="btn-wa"
                href="https://api.whatsapp.com/send?phone=628112938158&text=Hi%2C%20CS%20Ilena%21%0D%0ASaya%20tertarik%20untuk%20beli%3A%0D%0ANama%20produk%3A%0D%0AVarian%20%3A%0D%0AJumlah%20%3A%0D%0ABantu%20saya%20untuk%20melakukan%20proses%20checkout%20nya%21">
                <i class="material-icons text-light">phone</i>
            </a>
        </div>
    </div>
    <?= $this->include('layout/navbar'); ?>
    <?= $this->include('layout/navbarHp'); ?>
    <div style="flex: 1">
        <?= $this->renderSection('content'); ?>
    </div>
    <?= $this->include('layout/footer'); ?>
    <script>
    const toastElm = document.querySelector(".toast")
    const toastTeksElm = document.querySelector(".toast p")
    const toastOkElm = document.querySelector(".toast a")
    const toastCloseElm = document.querySelector(".toast button")

    function triggerToast(text, linkAction) {
        console.log(linkAction, text);
        toastElm.classList.add("show")
        toastTeksElm.innerHTML = text
        toastOkElm.href = linkAction
        if (!linkAction) {
            toastOkElm.classList.add('d-none');
            toastCloseElm.innerHTML = 'Ok';
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
</body>

</html>