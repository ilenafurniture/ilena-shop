<nav class="nav show-block-ke-hide" style="background-color: black;">
    <div class="container py-3">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <div style="width: calc(100% / 3)">
                <form action="/actionfind" method="post">
                    <input placeholder="Cari produk" style="text-transform: capitalize;" class="input" name="cari"
                        type="text">
                </form>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-center">
                <a href="/">
                    <img src="<?php echo base_url('/img/Logo Putih Ilena 1.png'); ?>" alt="logo ilena" height="30em">
                </a>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-end">
                <?php if (session()->get('isLogin')) { ?>
                <?php if (session()->get('role') == '0' || session()->get('role') == '4') { ?>
                <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                <a href="/account" class="btn"><i class="material-icons text-light">person_outline</i></a>
                <?php } else if (session()->get('role') == '1') { ?>
                <a href="/admin/product" class="btn d-flex align-items-center">
                    <i class="material-icons text-light">chevron_left</i>
                    <p class="m-0 text-light">Admin</p>
                </a>
                <a href="/logout" class="btn" style="padding-right: 0"><i
                        class="material-icons text-light">exit_to_app</i></a>
                <?php } else if (session()->get('role') == '2') { ?>
                <a href="/gudang/listorder" class="btn d-flex align-items-center">
                    <i class="material-icons text-light">chevron_left</i>
                    <p class="m-0 text-light">Gudang</p>
                </a>
                <a href="/logout" class="btn" style="padding-right: 0"><i
                        class="material-icons text-light">exit_to_app</i></a>
                <?php } else if (session()->get('role') == '3') { ?>
                <a href="/market/product" class="btn d-flex align-items-center">
                    <i class="material-icons text-light">chevron_left</i>
                    <p class="m-0 text-light">Marketplace</p>
                </a>
                <a href="/logout" class="btn" style="padding-right: 0"><i
                        class="material-icons text-light">exit_to_app</i></a>
                <?php } ?>
                <?php } else { ?>
                <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                <a href="/login" class="btn"><i class="material-icons text-light">person_outline</i></a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>
<div style="background-color: #474747;" class="py-1 show-block-ke-hide">
    <p class="m-0 text-center" style="color: white;">Lebih hemat dengan Free Ongkir hingga 100%</p>
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
}

@media (max-width: 1146px) {
    #container-react {
        justify-content: start;
        padding-inline: 2em;
        overflow-x: scroll;
    }
}
</style>
<div id="container-react">

</div>
<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<script type="text/babel">
    const { useState, useEffect } = React;
    const navbar = JSON.parse('<?= json_encode($navbar); ?>');
    console.log(navbar);
    const App = () => {
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
            console.log({
                category,
                products
            })
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

        return (
            <>
                <div className="w-100 show-flex-ke-hide container-navbar-list-nav">
                    <div className="d-flex align-items-center py-2 gap-5">
                        {Object.entries(navbar).map(([category, products]) => (
                        <div key={category} className="list-nav" onMouseOver={() => handleHoverJenis(category, products)} onMouseLeave={() => handleCloseHover('jenis')}>
                            <a
                                className="text-dark text-center w-100 d-block"
                                style={{ textDecoration: 'none' }}
                                href={`/product?jenis=${category.replace(' ', '-')}`}
                            >{category.charAt(0).toUpperCase() + category.slice(1)}
                            </a>
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
                                gridTemplateColumns: 'repeat(3, 1fr)',
                                rowGap: '1em',
                            }}
                        >
                        {jenisSelected.products && <>
                            {Array.isArray(jenisSelected.products)
                            ? jenisSelected.products.map((product, index) => (
                                <div key={product.id}>
                                    <p className="m-0" style={{ fontSize: '14px' }}>
                                        Jelajahi {product.koleksi.charAt(0).toUpperCase() + product.koleksi.slice(1)}
                                    </p>
                                    <div className="ms-2">
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
                                                href={`/product?koleksi=${product.koleksi.replace(' ', '-')}&jenis=${jenisSelected.category.replace(' ', '-')}`}
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
                                <div
                                    className={`d-flex gap-4 mb-3`}
                                >
                                    <div style={{ flex: 1 }}>
                                        <img
                                            style={{
                                            borderRadius: '6px',
                                            overflow: 'hidden',
                                            objectFit: 'cover',
                                            width: '100%',
                                            height: '100%',
                                            }}
                                            src={`/img/barang/300/${productSelected.id}.webp`}
                                            alt={`${productSelected.nama} ${productSelected.koleksi}`}
                                        />
                                    </div>
                                    <div style={{ flex: 1 }}>
                                        <h3 className="teks-sedang mb-3">
                                            {productSelected.koleksi.charAt(0).toUpperCase() + productSelected.koleksi.slice(1)}
                                        </h3>
                                        <p style={{ textAlign: 'justify' }} className="mb-2">{productSelected.deskripsi.deskripsi}</p>
                                        <a
                                            href={`/product/${productSelected.nama.toLowerCase().replace(' ', '-')}`}
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