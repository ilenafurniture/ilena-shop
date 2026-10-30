<?php
$headerMode = in_array($headerMode ?? '', ['auth', 'account', 'full'], true) ? $headerMode : 'full';
?>
<div class="ilena-utility-bar" aria-label="Lunarea Furniture">
    <div class="ilena-utility-bar__inner">
        <a href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer" aria-label="Kunjungi Lunarea Furniture">
            <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" alt="Lunarea Furniture" width="150" height="30" decoding="async">
        </a>
    </div>
</div>
<nav class="nav show-block-ke-hide ilena-topbar">
    <div class="container py-3 ilena-topbar__container">
        <div class="d-flex w-100 justify-content-between align-items-center ilena-topbar__inner ilena-topbar__inner--<?= esc($headerMode, 'attr'); ?>">
            <div class="navbar-brand-pair">
                <a class="navbar-brand-ilena" href="/" aria-label="Ilena Furniture - Beranda">
                    <img src="<?= base_url('/img/Logo Putih Ilena 1.png'); ?>" alt="Ilena Furniture" width="150" height="30" decoding="async">
                </a>
            </div>
            <?php if ($headerMode === 'full'): ?>
                <div class="ilena-topbar__main-nav" role="navigation" aria-label="Menu utama">
                    <a href="/product">Furniture Collection</a>
                    <a href="https://ilenafurniture.net/" target="_blank" rel="noopener noreferrer">Custom Interior</a>
                    <a href="/partner">Partner Store</a>
                    <a href="/article">Article</a>
                    <a href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20saya%20ingin%20menghubungi%20tim%20Ilena" target="_blank" rel="noopener noreferrer">Contact Us</a>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-end ilena-topbar__actions">
                <button type="button" class="ilena-header-action ilena-search-toggle" aria-label="Buka pencarian" aria-expanded="false" aria-controls="ilena-desktop-search" data-search-toggle>
                    <i class="material-icons" aria-hidden="true">search</i>
                </button>
                <?php if (session()->get('isLogin') && in_array((string) session()->get('role'), ['1', '5'], true)) { ?>
                    <?php if ((string) session()->get('role') === '1') { ?>
                        <a href="/admin/product" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light">Admin</p>
                        </a>
                        <form action="/logout" method="post" class="m-0"><input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>"><button type="submit" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></button></form>
                    <?php } else { ?>
                        <a href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light">Workspace</p>
                        </a>
                        <form action="/logout" method="post" class="m-0"><input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>"><button type="submit" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></button></form>
                    <?php } ?>
                <?php } else { ?>
                    <a href="/wishlist" class="ilena-header-action" aria-label="Wishlist"><i class="material-icons" aria-hidden="true">favorite_border</i><span class="ilena-sr-only">Wishlist</span></a>
                    <a href="/cart" class="ilena-header-action" aria-label="Keranjang"><i class="material-icons" aria-hidden="true">shopping_bag</i><span class="ilena-sr-only">Keranjang</span></a>
                    <a href="<?= session()->get('isLogin') ? '/account' : '/login'; ?>" class="ilena-header-action" aria-label="<?= session()->get('isLogin') ? 'Akun saya' : 'Masuk'; ?>"><i class="material-icons" aria-hidden="true">person_outline</i><span class="ilena-sr-only"><?= session()->get('isLogin') ? 'Akun saya' : 'Masuk'; ?></span></a>
                <?php } ?>
            </div>
        </div>
        <div class="ilena-search-popover" id="ilena-desktop-search" data-search-popover hidden>
            <form action="/find" method="get" class="ilena-global-search" data-global-search role="search">
                <label class="ilena-sr-only" for="global-search-desktop">Cari produk atau koleksi</label>
                <input id="global-search-desktop" placeholder="Cari produk atau koleksi" class="input" name="q"
                    type="search" autocomplete="off" autocapitalize="off" aria-autocomplete="list" aria-expanded="false" aria-controls="global-search-desktop-listbox">
                <div class="ilena-search-suggestions" id="global-search-desktop-listbox" role="listbox" hidden></div>
            </form>
        </div>
    </div>
</nav>
<script>
(() => {
    const toggle = document.querySelector('[data-search-toggle]');
    const panel = document.querySelector('[data-search-popover]');
    const input = document.getElementById('global-search-desktop');
    if (!toggle || !panel || !input) return;

    const closeSearch = () => {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Buka pencarian');
    };
    toggle.addEventListener('click', () => {
        const opening = panel.hidden;
        panel.hidden = !opening;
        toggle.setAttribute('aria-expanded', String(opening));
        toggle.setAttribute('aria-label', opening ? 'Tutup pencarian' : 'Buka pencarian');
        if (opening) window.requestAnimationFrame(() => input.focus());
    });
    document.addEventListener('pointerdown', (event) => {
        if (!panel.hidden && !panel.contains(event.target) && !toggle.contains(event.target)) closeSearch();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) {
            closeSearch();
            toggle.focus();
        }
    });
})();
</script>
<?php if ($headerMode === 'full'): ?>
<div class="ilena-shipping-bar show-block-ke-hide" role="note" aria-label="Promo pengiriman">
    <div class="container ilena-shipping-bar__inner">
        <i class="material-icons" aria-hidden="true">local_shipping</i>
        <p class="m-0">Lebih hemat dengan <strong>Free Ongkir hingga 100%</strong></p>
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
<script type="text/babel">
    const { useState, useEffect, useRef } = React;
    const navbar = JSON.parse('<?= json_encode($navbar ?? []); ?>');
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
                    setEnterNavbar({...enterNavbar, jenis: false});
                    break;
                case 'kategori':
                    setEnterNavbar({...enterNavbar, kategori: false});
                    break;
                default:
                    break;
            }
        }
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
                                href={`/product?jenis=${category.replace(' ', '-')}`}
                            >{category.charAt(0).toUpperCase() + category.slice(1)}
                            </a>
                            :
                            <a
                                className="text-center w-100 d-block"
                                style={{ textDecoration: 'none', color: jenisSelected.category == category ? 'var(--merah)' : 'black' }}
                                href={`/product?jenis=${Object.entries(products).reduce((prev, [index, isi]) => {
                                    return `${prev}${prev == '' ? '' : '+'}${index.split(' ').join('-')}`;
                                }, "")}`}
                            >{category.charAt(0).toUpperCase() + category.slice(1)}
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
                                            href={`/product?koleksi=${product.koleksi.replace(' ', '-')}&jenis=${jenisSelected.category.replace(' ', '-')}`}
                                            onMouseOver={() => handleHover(product)}
                                        >
                                            {product.koleksi.charAt(0).toUpperCase() + product.koleksi.slice(1)}
                                        </a>
                                    </div>
                                </div>
                            ))
                            : Object.entries(jenisSelected.products).map(([subCategory, subProducts]) => (
                                <div key={subCategory}>
                                    <p className="m-0" style={{ fontSize: '14px' }}>
                                        Jelajahi {subCategory.charAt(0).toUpperCase() + subCategory.slice(1)}
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
                                                href={`/product?koleksi=${product.koleksi.replace(' ', '-')}&jenis=${subCategory.split(' ').join('-')}`}
                                                onMouseOver={() => handleHover(product)}
                                            >
                                                {product.koleksi.charAt(0).toUpperCase() + product.koleksi.slice(1)}
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
                                            {productSelected.koleksi.charAt(0).toUpperCase() + productSelected.koleksi.slice(1)}
                                        </h3>
                                        <p style={{ textAlign: 'justify' }} className="mb-2">{productSelected.deskripsi.deskripsi}</p>
                                        <a
                                            href={`/product/${productSelected.nama.toLowerCase().split(' ').join('-')}`}
                                            style={{
                                                display: 'inline',
                                                fontSize: '10px',
                                                textDecoration: 'none',
                                                color: 'var(--merah)',
                                            }}
                                        >
                                            lihat selengkapnya..
                                        </a>
                                    </div>
                                </div>
                                <p className="text-secondary">
                                    Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan nama Ilena.
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
