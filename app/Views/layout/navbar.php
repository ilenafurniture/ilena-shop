<?php
$headerMode = in_array($headerMode ?? '', ['auth', 'account', 'full'], true) ? $headerMode : 'full';
$activeLocale = ilena_current_store_locale();
$currentPath = trim(service('uri')->getPath(), '/');
$pathWithoutLocale = preg_replace('#^(?:id|en)(?:/|$)#', '', $currentPath);
$pathWithoutLocale = trim((string) $pathWithoutLocale, '/');
$queryString = service('request')->getUri()->getQuery();
$languageReturnUrl = '/' . $pathWithoutLocale . ($queryString !== '' ? '?' . $queryString : '');
$idLanguageUrl = '/language/id?' . http_build_query(['return' => $languageReturnUrl]);
$enLanguageUrl = '/language/en?' . http_build_query(['return' => $languageReturnUrl]);
?>
<div class="ilena-utility-bar" aria-label="Brand">
    <div class="ilena-utility-bar__inner">
        <div class="ilena-utility-bar__brands" aria-label="Ilena group brands">
            <a href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(lang('Store.visitLunarea'), 'attr'); ?>">
                <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" alt="Lunarea Furniture" width="100" height="21" decoding="async">
            </a>
            <span class="ilena-utility-bar__brand-divider" aria-hidden="true"></span>
            <a class="ilena-utility-bar__interior" href="https://ilenafurniture.net/" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(lang('Store.customInterior'), 'attr'); ?>">
                <img src="<?= base_url('img/logo/logoilenainterior-v2.png'); ?>" alt="Ilena Interior" width="110" height="29" decoding="async">
            </a>
        </div>
        <nav class="ilena-language-switch" aria-label="<?= esc(lang('Store.language'), 'attr'); ?>">
            <a href="<?= esc($idLanguageUrl, 'attr'); ?>" lang="id" hreflang="id"<?= $activeLocale === 'id' ? ' aria-current="page"' : ''; ?>>ID</a>
            <span aria-hidden="true">/</span>
            <a href="<?= esc($enLanguageUrl, 'attr'); ?>" lang="en" hreflang="en"<?= $activeLocale === 'en' ? ' aria-current="page"' : ''; ?>>EN</a>
        </nav>
    </div>
</div>
<nav class="nav show-block-ke-hide ilena-topbar">
    <div class="container py-3 ilena-topbar__container">
        <div class="d-flex w-100 justify-content-between align-items-center ilena-topbar__inner ilena-topbar__inner--<?= esc($headerMode, 'attr'); ?>">
            <div class="navbar-brand-pair">
                <a class="navbar-brand-ilena" href="<?= esc($activeLocale === 'en' ? '/en' : '/', 'attr'); ?>" aria-label="Ilena Furniture — <?= esc(lang('Store.home'), 'attr'); ?>">
                    <img src="<?= base_url('/img/Logo Putih Ilena 1.png'); ?>" alt="Ilena Furniture" width="150" height="30" decoding="async">
                </a>
            </div>
            <?php if ($headerMode === 'full'): ?>
                <div class="ilena-topbar__main-nav" role="navigation" aria-label="Menu utama">
                    <a href="<?= esc($activeLocale === 'en' ? '/en/product' : '/product', 'attr'); ?>"><?= esc(lang('Store.collection')); ?></a>
                    <a href="<?= esc($activeLocale === 'en' ? '/en/partner' : '/partner', 'attr'); ?>"><?= esc(lang('Store.partnerStore')); ?></a>
                    <a href="<?= esc($activeLocale === 'en' ? '/en/article' : '/article', 'attr'); ?>"><?= esc(lang('Store.article')); ?></a>
                    <a href="https://wa.me/628112938158?text=<?= rawurlencode(lang('Store.whatsAppContactMessage')); ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('Store.contactUs')); ?></a>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-end ilena-topbar__actions">
                <button type="button" class="ilena-header-action ilena-search-toggle" aria-label="<?= esc(lang('Store.openSearch'), 'attr'); ?>" aria-expanded="false" aria-controls="ilena-desktop-search" data-search-toggle>
                    <i class="material-icons" aria-hidden="true">search</i>
                </button>
                <?php if (session()->get('isLogin') && in_array((string) session()->get('role'), ['1', '5'], true)) { ?>
                    <?php if ((string) session()->get('role') === '1') { ?>
                        <a href="/admin/product" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light">Admin</p>
                        </a>
                        <form action="<?= esc(ilena_store_path('/logout'), 'attr'); ?>" method="post" class="m-0"><input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>"><button type="submit" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></button></form>
                    <?php } else { ?>
                        <a href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light"><?= esc(lang('Store.workspace')); ?></p>
                        </a>
                        <form action="<?= esc(ilena_store_path('/logout'), 'attr'); ?>" method="post" class="m-0"><input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>"><button type="submit" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></button></form>
                    <?php } ?>
                <?php } else { ?>
                    <a href="<?= esc(ilena_store_path('/wishlist'), 'attr'); ?>" class="ilena-header-action" aria-label="<?= esc(lang('Store.favorites'), 'attr'); ?>"><i class="material-icons" aria-hidden="true">favorite_border</i><span class="ilena-sr-only"><?= esc(lang('Store.favorites')); ?></span></a>
                    <a href="<?= esc(ilena_store_path('/cart'), 'attr'); ?>" class="ilena-header-action" aria-label="<?= esc(lang('Store.cartTitle'), 'attr'); ?>"><i class="material-icons" aria-hidden="true">shopping_bag</i><span class="ilena-sr-only"><?= esc(lang('Store.cartTitle')); ?></span></a>
                    <a href="<?= esc(ilena_store_path(session()->get('isLogin') ? '/account' : '/login'), 'attr'); ?>" class="ilena-header-action" aria-label="<?= esc(session()->get('isLogin') ? lang('Store.myAccount') : lang('Store.signIn'), 'attr'); ?>"><i class="material-icons" aria-hidden="true">person_outline</i><span class="ilena-sr-only"><?= esc(session()->get('isLogin') ? lang('Store.myAccount') : lang('Store.signIn')); ?></span></a>
                <?php } ?>
            </div>
        </div>
        <div class="ilena-search-overlay" id="ilena-desktop-search" data-search-popover hidden>
            <section class="ilena-search-dialog" data-search-dialog role="dialog" aria-modal="true" aria-labelledby="ilena-search-title">
                <div class="ilena-search-dialog__heading">
                    <div>
                        <p class="ilena-search-dialog__eyebrow">Ilena Furniture</p>
                        <h2 id="ilena-search-title"><?= esc(lang('Store.searchTitle')); ?></h2>
                    </div>
                    <button type="button" class="ilena-search-close" data-search-close aria-label="<?= esc(lang('Store.dismiss'), 'attr'); ?>">×</button>
                </div>
                <form action="<?= esc($activeLocale === 'en' ? '/en/find' : '/find', 'attr'); ?>" method="get" class="ilena-global-search" data-global-search role="search">
                    <label class="ilena-sr-only" for="global-search-desktop"><?= esc(lang('Store.searchLabel')); ?></label>
                    <span class="material-icons ilena-search-dialog__icon" aria-hidden="true">search</span>
                    <input id="global-search-desktop" placeholder="<?= esc(lang('Store.searchPlaceholder'), 'attr'); ?>" class="input" name="q"
                        type="search" autocomplete="off" autocapitalize="off" aria-autocomplete="list" aria-expanded="false" aria-controls="global-search-desktop-listbox">
                    <div class="ilena-search-suggestions" id="global-search-desktop-listbox" role="listbox" hidden></div>
                </form>
                <p class="ilena-search-dialog__hint"><?= esc(lang('Store.searchHint')); ?></p>
            </section>
        </div>
    </div>
</nav>
<script>
(() => {
    const toggle = document.querySelector('[data-search-toggle]');
    const panel = document.querySelector('[data-search-popover]');
    const dialog = panel?.querySelector('[data-search-dialog]');
    const input = document.getElementById('global-search-desktop');
    const closeButton = panel?.querySelector('[data-search-close]');
    if (!toggle || !panel || !dialog || !input || !closeButton) return;

    const closeSearch = () => {
        panel.hidden = true;
        document.body.classList.remove('ilena-search-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Buka pencarian');
    };
    toggle.addEventListener('click', () => {
        const opening = panel.hidden;
        panel.hidden = !opening;
        document.body.classList.toggle('ilena-search-open', opening);
        toggle.setAttribute('aria-expanded', String(opening));
        toggle.setAttribute('aria-label', opening ? <?= json_encode(lang('Store.dismiss'), JSON_UNESCAPED_UNICODE) ?> : <?= json_encode(lang('Store.openSearch'), JSON_UNESCAPED_UNICODE) ?>);
        if (opening) window.requestAnimationFrame(() => input.focus());
    });
    closeButton.addEventListener('click', () => {
        closeSearch();
        toggle.focus();
    });
    panel.addEventListener('pointerdown', (event) => {
        if (event.target === panel) {
            closeSearch();
            toggle.focus();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (panel.hidden) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeSearch();
            toggle.focus();
        } else if (event.key === 'Tab') {
            const focusable = [...dialog.querySelectorAll('button:not([disabled]), input:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])')];
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });
})();
</script>
<?php if ($headerMode === 'full'): ?>
<div class="ilena-shipping-bar show-block-ke-hide" role="note" aria-label="<?= esc(lang('Store.shippingPromoBar'), 'attr'); ?>">
    <div class="container ilena-shipping-bar__inner">
        <i class="material-icons" aria-hidden="true">local_shipping</i>
        <p class="m-0"><?= esc(lang('Store.shippingPromoBar')); ?></p>
    </div>
</div>
<style>
    #container-react {
        position: sticky;
        top: -1px;
        z-index: 99;
    }

    .container-navbar-list-nav {
        background-color: whitesmoke;
        position: sticky;
        top: -1px;
        z-index: 99;
        justify-content: center;
        overflow-x: auto;
    }

    .container-navbar-list-nav::-webkit-scrollbar {
        display: none;
    }

    @media (max-width: 1343px) {
        .container-navbar-list-nav {
            justify-content: start;
            padding-inline: 2em;
            /* overflow-x: scroll; */
        }
    }

    @media (max-width: 600px) {
        .tampilHp {
            display: none;
        }
    }

    .overlay-navbar {
        height: 40px;
        width: 200px;
        background-image: linear-gradient(to left, whitesmoke, transparent);
        margin-bottom: -40px;
        display: flex;
        z-index: 120;
        align-items: center;
        justify-content: end;
        padding-right: 20px;
    }

    .overlay-navbar i {
        cursor: pointer;
        transition: 0.2s;
    }

    .overlay-navbar i:hover {
        font-weight: bold;
        color: var(--merah);
        transition: 0.2s;
    }
</style>
<div id="container-react" class="tampilHp"></div>
<?php
    $navbarLocale = service('request')->getLocale() === 'en' ? 'en' : 'id';
    $navbarLabelMap = [];
    $rememberNavbarLabel = static function ($label) use (&$navbarLabelMap, $navbarLocale): void {
        if (!is_string($label) || trim($label) === '') return;
        $navbarLabelMap[$label] = $navbarLocale === 'en'
            ? ilena_catalog_type_label($label, 'en')
            : ucfirst($label);
    };
    foreach (($navbar ?? []) as $categoryLabel => $categoryProducts) {
        $rememberNavbarLabel($categoryLabel);
        if (!is_array($categoryProducts)) continue;
        foreach ($categoryProducts as $subCategoryLabel => $productsOrProduct) {
            if (is_string($subCategoryLabel)) $rememberNavbarLabel($subCategoryLabel);
            $menuProducts = is_array($productsOrProduct) ? $productsOrProduct : [];
            if (isset($menuProducts['koleksi'])) $menuProducts = [$menuProducts];
            foreach ($menuProducts as $menuProduct) {
                if (is_array($menuProduct)) $rememberNavbarLabel($menuProduct['koleksi'] ?? null);
            }
        }
    }
?>
<script type="text/babel">
    const { useState, useEffect, useRef } = React;
    const navbar = <?= json_encode($navbar ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const navbarLabelMap = <?= json_encode($navbarLabelMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const localePrefix = <?= json_encode($navbarLocale === 'en' ? '/en' : ''); ?>;
    const displayNavbarLabel = (label) => navbarLabelMap[label] || String(label || '').replace(/\b\w/g, letter => letter.toUpperCase());
    const App = () => {
        const containerNavbarListNav = useRef();
        const [jenisSelected , setJenisSelected] = useState({
            category: null,
            products: null
        });
        const [productSelected , setProductSelected] = useState(null);
        const handleHover = (product) => {
            setProductSelected(product);
        };
        const [enterNavbar, setEnterNavbar] = useState({
            jenis: false,
            kategori: false,
        });
        const [scrollPosition, setScrollPosition] = useState(0);
        const [isAtEnd, setIsAtEnd] = useState(false);

        const handleCloseHover = (type) => {
            switch (type) {
                case 'jenis':
                    setEnterNavbar((current) => ({ ...current, jenis: false }));
                    break;
                case 'kategori':
                    setEnterNavbar((current) => ({ ...current, kategori: false }));
                    break;
                default:
                    break;
            }
        }
        useEffect(() => {
            const interactionRoot = document.getElementById('container-react');
            if (!interactionRoot) return undefined;

            const closeWhenPointerLeavesNavigation = (event) => {
                if (event.relatedTarget instanceof Node && interactionRoot.contains(event.relatedTarget)) return;
                setEnterNavbar({ jenis: false, kategori: false });
            };

            interactionRoot.addEventListener('mouseleave', closeWhenPointerLeavesNavigation);
            return () => interactionRoot.removeEventListener('mouseleave', closeWhenPointerLeavesNavigation);
        }, []);
        useEffect(()=>{
            if(!enterNavbar.jenis && !enterNavbar.kategori){
                setProductSelected(null);
                setJenisSelected({
                    category: null,
                    products: null
                });
            }
        }, [enterNavbar])
        const handleHoverJenis = (category, products) =>{
            // console.log({
            //     category,
            //     products
            // })
            // console.log(Array.isArray(products) ? products[0] : products[Object.keys(products)[0]][0])
            if(category == null) setProductSelected(null);
            else setProductSelected(Array.isArray(products) ? products[0] : products[Object.keys(products)[0]][0]);
            setJenisSelected({
                category,
                products
            });
            setEnterNavbar({
                jenis: true,
                kategori: true
            });
        }

        const handleScrollClick = () => {
            if(containerNavbarListNav) {
                // console.log(containerNavbarListNav.current)
                containerNavbarListNav.current.scrollLeft += 200;
            }
        }

        // Fungsi untuk menangani event scroll
        const handleScroll = (event) => {
            const { scrollLeft, scrollWidth, clientWidth } = event.target;
            // Mengupdate posisi scroll
            setScrollPosition(scrollLeft);
            // console.log(`${scrollWidth} - ${scrollLeft} = ${scrollWidth - scrollLeft}`)
            // console.log(`clientWidth: ${clientWidth - 20}`)
            // Mengecek apakah sudah di bagian kanan (end) dari scroll
            if (scrollWidth - scrollLeft - 20 < clientWidth) {
                setIsAtEnd(true);
            } else {
                setIsAtEnd(false);
            }
        };
        
        useEffect(()=>{
            console.log(jenisSelected)
        }, [jenisSelected])

        return (
            <>
                {!isAtEnd &&
                <div className="d-flex justify-content-end">
                    <div className="overlay-navbar"><i className="material-icons" onClick={() => {handleScrollClick()}}>arrow_forward_ios</i></div>
                </div>}
                <div className="w-100 show-flex-ke-hide container-navbar-list-nav" ref={containerNavbarListNav} onScroll={handleScroll}>
                    <div className="d-flex align-items-center py-2 gap-5">
                        {Object.entries(navbar).map(([category, products]) => (
                        <div key={category} className="list-nav" onMouseOver={() => handleHoverJenis(category, products)} onMouseLeave={() => handleCloseHover('jenis')}>
                            {Array.isArray(products) ?
                            <a
                                className="text-center w-100 d-block"
                                style={{ textDecoration: 'none', color: jenisSelected.category == category ? 'var(--merah)' : 'black' }}
                                href={`${localePrefix}/product?jenis=${encodeURIComponent(category.replace(/ /g, '-'))}`}
                            >{displayNavbarLabel(category)}
                            </a>
                            :
                            <a
                                className="text-center w-100 d-block"
                                style={{ textDecoration: 'none', color: jenisSelected.category == category ? 'var(--merah)' : 'black' }}
                                href={`${localePrefix}/product?jenis=${encodeURIComponent(Object.entries(products).reduce((prev, [index, isi]) => {
                                    return `${prev}${prev == '' ? '' : '+'}${index.split(' ').join('-')}`;
                                }, ""))}`}
                            >{displayNavbarLabel(category)}
                            </a>
                            }
                        </div>
                        ))}
                    </div>
                </div>
                <div className={"child-list-nav " + (jenisSelected.category ? 'show' : '')} onMouseLeave={() => handleCloseHover('kategori')}>
                    <div className="container d-flex align-items-start py-4">
                        <div
                            style={{
                                flex: 1,
                                display: 'grid',
                                gridTemplateColumns: `repeat(${jenisSelected.products && Array.isArray(jenisSelected.products) ? '2' : '3'}, 1fr)`,
                                rowGap: jenisSelected.products && Array.isArray(jenisSelected.products) ? '0em' : '1em',
                            }}
                        >
                        {jenisSelected.products && <>
                            {Array.isArray(jenisSelected.products)
                            ? jenisSelected.products.map((product, index) => (
                                <div key={product.id}>
                                    <div className="ms-1">
                                        <a
                                            className="w-100 d-block"
                                            style={{
                                                textDecoration: 'none',
                                                fontWeight: 500,
                                                fontSize: '20px',
                                                color: product.id == productSelected.id ? 'var(--merah)' : 'black',
                                            }}
                                            href={`${localePrefix}/product?koleksi=${encodeURIComponent(product.koleksi.replace(/ /g, '-'))}&jenis=${encodeURIComponent(jenisSelected.category.replace(/ /g, '-'))}`}
                                            onMouseOver={() => handleHover(product)}
                                        >
                                            {displayNavbarLabel(product.koleksi)}
                                        </a>
                                    </div>
                                </div>
                            ))
                            : Object.entries(jenisSelected.products).map(([subCategory, subProducts]) => (
                                <div key={subCategory}>
                                    <p className="m-0" style={{ fontSize: '14px' }}>
                                        {<?= json_encode(lang('Store.navExplorePrefix')); ?>} {displayNavbarLabel(subCategory)}
                                    </p>
                                    <div className="ms-2">
                                        {subProducts.map((product) => (
                                            <a
                                                key={product.id}
                                                className="w-100 d-block"
                                                style={{
                                                    textDecoration: 'none',
                                                    fontWeight: 500,
                                                    fontSize: '20px',
                                                    color: product.id == productSelected.id ? 'var(--merah)' : 'black',
                                                }}
                                                href={`${localePrefix}/product?koleksi=${encodeURIComponent(product.koleksi.replace(/ /g, '-'))}&jenis=${encodeURIComponent(subCategory.split(' ').join('-'))}`}
                                                onMouseOver={() => handleHover(product)}
                                            >
                                                {displayNavbarLabel(product.koleksi)}
                                            </a>
                                        ))}
                                    </div>
                                </div>
                            ))} </>
                        }
                        </div>
                        {productSelected && 
                            <div style={{ flex: 1 }}>
                                <div className={`d-flex gap-4 mb-3 ${productSelected.preview_image ? 'has-preview-image' : 'text-only'}`}>
                                    {productSelected.preview_image && <div style={{ flex: 1 }}>
                                        <img
                                            style={{
                                            borderRadius: '6px',
                                            overflow: 'hidden',
                                            objectFit: 'cover',
                                            width: '100%',
                                            height: '100%',
                                            }}
                                            src={productSelected.preview_image || `/product-cover/${productSelected.id}?slot=${productSelected.cover_slot || '1'}&size=300`}
                                            onError={(event) => {
                                                const image = event.currentTarget;
                                                if (image.dataset.previewFallbackTried) return;
                                                image.dataset.previewFallbackTried = 'true';
                                                image.src = `/product-cover/${encodeURIComponent(productSelected.id)}?slot=${encodeURIComponent(productSelected.cover_slot || '1')}&size=300`;
                                            }}
                                            loading="lazy" decoding="async" alt={`${productSelected.nama} ${productSelected.koleksi}`}
                                        />
                                    </div>}
                                    <div style={{ flex: productSelected.preview_image ? 1 : 2 }}>
                                        <h3 className="teks-sedang mb-3">
                                            {displayNavbarLabel(productSelected.koleksi)}
                                        </h3>
                                        <p style={{ textAlign: 'justify' }} className="mb-2">{<?= json_encode($navbarLocale === 'en' ? 'deskripsi_en' : 'deskripsi'); ?> && productSelected.deskripsi[<?= json_encode($navbarLocale === 'en' ? 'deskripsi_en' : 'deskripsi'); ?>] || <?= json_encode(lang('Store.navProductDescriptionFallback')); ?>}</p>
                                        <a
                                            href={`${localePrefix}/product/${encodeURIComponent(productSelected.nama.toLowerCase().split(' ').join('-'))}`}
                                            style={{
                                                display: 'inline',
                                                fontSize: '10px',
                                                textDecoration: 'none',
                                                color: 'var(--merah)',
                                            }}
                                        >
                                            {<?= json_encode(lang('Store.navViewDetails')); ?>}
                                        </a>
                                    </div>
                                </div>
                                <p className="text-secondary">
                                    {<?= json_encode(lang('Store.navBrandStory')); ?>}
                                </p>
                            </div>
                        }
                    </div>
                </div>
            </>
        );
    }
    ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?php endif; ?>
