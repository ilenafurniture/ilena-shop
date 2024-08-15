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
                    <img src="<?php echo base_url('/img/logoilenawhite.png'); ?>" alt="logo ilena" height="30em">
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
<div style="background-color: rgb(211, 211, 211);" class="py-1 show-block-ke-hide">
    <!-- <p class="m-0 text-center" style="color: #844709;">Lebih hemat dengan Free Ongkir hingga 100%</p> -->
    <p class="m-0 text-center" style="color: black;">Lebih hemat dengan Free Ongkir hingga 100%</p>
</div>
<div class="justify-content-center w-100 show-flex-ke-hide"
    style="background-color:whitesmoke; position: sticky; top:-1px; z-index: 99;">
    <div class="d-flex align-items-center py-2 gap-5">
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=sorely">Sorely</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-start">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Sorely</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=bookshelf">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=bufet-tv">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=coffee-table">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=console-table">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-3-drawer">Dresser 3
                                Drawer</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-6-drawer">Dresser Tall
                                Cabinet
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=king-bed">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=meja-nakas">Meja Nakas</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=queen-bed">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=side-table">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=single-bed">Single Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=wardrobe">Wardrobe</a>
                        </div>
                    </div>
                    <div style="flex:1;" class="d-flex gap-4 mt-3">
                        <!-- <div class=""> -->
                        <div style="flex: 1;">
                            <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                src="https://i.ibb.co.com/ZSNH3wC/DSC04063.webp" alt="dresser-3-drawer">
                        </div>
                        <div style="flex: 1;">
                            <h3 class="teks-sedang mb-3">Lemari Laci<br>3 Tingkat</h3>
                            <p style="text-align: justify" class="mb-2">Lemari laci 3 tingkat berdesain stylish dan
                                minimalis
                                ini cocok
                                digunakan sebagai
                                tempat penyimpanan berbagai barang dan pakaian. Dengan 3 tingkat kabinet laci ini,
                                bisa memungkinkan untuk lebih banyak menyimpan barang dengan rapi dan aman dalam
                                satu tempat saja. Dilengkapi dengan handle mushroom hitam dan rangka 4 rangka kaki
                                yang kokoh.
                            </p>
                            <p style="text-align: justify;">Kayu solid
                                mahoni,engineered wood,
                                rangka besi, dilapisi
                                vinir jati
                                dengan finishing
                                white or black wash</p>
                            <a href="<?= base_url('/product/dresser-3-drawer-ilena-sorely') ?>"
                                style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                selengkapnya..</a>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=cabana">Cabana</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-start">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Cabana</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=bookshelf">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=bufet-tv">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=coffee-table">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=console-table">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=dresser-3-drawer">Dresser 3
                                Drawer</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=dresser-5-drawer">Dresser 5
                                Drawer</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=dresser-6-drawer">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=king-bed">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=queen-bed">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=side-table">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=single-bed">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex:1;" class="d-flex gap-4 mt-3">
                        <div style="flex: 1;">
                            <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                src="https://ilenafurniture.com/viewvar/10101401/1" alt="dresser-3-drawer">
                        </div>
                        <div style="flex: 1;">
                            <h3 class="teks-sedang mb-3">Side Table</h3>
                            <p style="text-align: justify" class="mb-2">Nakas/ meja samping multifungsi dengan
                                desain
                                modern & minimalis yang stylish . Cocok digunakan sebagai tempat buku, hp, hingga
                                lampu
                                tidur. Side table ini bisa diletakkan pada samping kasur atau sudut ruangan lain
                                sesuai
                                kebutuhan. Dengan handle mushroom yang pas digenggam tangan dengan frame besi yang
                                menambah ketahanan.
                            </p>
                            <p style="text-align: justify" class="mb-2">Koleksi Ilena Furniture series Sorely didesain
                                eksklusif dengan material pembuatan
                                berkualitas premium</p>
                            <p style="text-align: justify;">Kayu solid mahoni,engineered wood, rangka besi, dilapisi
                                vinir jati dengan finishing white or black wash.</p>
                            <a href="<?= base_url('/product/Side-Table-Ilena-Cabana') ?>"
                                style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                selengkapnya..</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=orca">Orca</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-start">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Orca</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=bookshelf">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=bufet-tv">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=coffee-table">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=console-table">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=dresser-6-drawer">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=king-bed">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=queen-bed">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=side-table">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=single-bed">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex:1;" class="d-flex gap-4 mt-3">
                        <div style="flex: 1;">
                            <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                src="https://ilenafurniture.com/viewvar/10601401/4" alt="dresser-3-drawer">
                        </div>
                        <div style="flex: 1;">
                            <h3 class="teks-sedang mb-3">Side Table</h3>
                            <p style="text-align: justify" class="mb-2">Side table minimalis dengan paduan corak warna
                                hitam/putih dan coklat kayu yang kontras memberikan sentuhan cantik & unik. Dengan
                                desain minimalisnya, meja ini mampu menyempurnakan interior di ruang tamu, kamar tidur,
                                kantor, dan sudut ruang lain jadi lebih lengkap dan elegan.
                            </p>
                            <p style="text-align: justify" class="mb-2">Koleksi Ilena Furniture series Orca didesain
                                eksklusif dengan material pembuatan berkualitas premium.</p>
                            <p style="text-align: justify;">Kayu solid mahoni, engineered wood, dilapisi vinir jati
                                dengan finishing satin white & black wash.</p>
                            <a href="<?= base_url('/product/side-table-ilena-orca') ?>"
                                style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                selengkapnya..</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=water-case">Water Case</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-start">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Water Case</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=bookshelf">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=bufet-tv">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=coffee-table">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=console-table">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=dresser-6-drawer">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=king-bed">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=queen-bed">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=side-table">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=single-bed">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex:1;" class="d-flex gap-4 mt-3">
                        <div style="flex: 1;">
                            <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                src="https://ilenafurniture.com/viewvar/11000301/4" alt="dresser-3-drawer">
                        </div>
                        <div style="flex: 1;">
                            <h3 class="teks-sedang mb-3">Coffee Table</h3>
                            <p style="text-align: justify" class="mb-2">Dibuat dengan memadukan unsur modern dan
                                minimalis yang stylish untuk menyempurnakan keindahan interior ruang. Menempatkan coffee
                                table ini di area ruang tamu, ruang keluarga, hingga kantor akan menambah kesan elegan
                                dan hangat dalam ruangan.
                                Koleksi Ilena Furniture series Water Case didesain eksklusif dengan material pembuatan
                                berkualitas premium.
                            </p>
                            <p style="text-align: justify;">Kayu solid mahoni,engineered wood, dilapisi vinir jati
                                dengan finishing whitewash.</p>
                            <a href="<?= base_url('/product/coffee-table-ilena-water-case') ?>"
                                style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                selengkapnya..</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=plint-base">Plint Base</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-start">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Plint Base</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=bufet-tv">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=coffee-table">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=console-table">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=dresser-6-drawer">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=king-bed">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=queen-bed">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=side-table">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=single-bed">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex:1;" class="d-flex gap-4 mt-3">
                        <div style="flex: 1;">
                            <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                src="https://ilenafurniture.com/viewvar/10700301/3" alt="dresser-3-drawer">
                        </div>
                        <div style="flex: 1;">
                            <h3 class="teks-sedang mb-3">Coffee Table</h3>
                            <p style="text-align: justify" class="mb-2">Dirancang dengan model modern minimalis dari
                                kayu mahoni dengan finishing satin yang artistik. Meja ini sangat cocok ditempatkan pada
                                ruang tamu atau kantor yang memberikan sentuhan sempurna pada interior ruang. Koleksi
                                Ilena Furniture series Plint base didesain eksklusif dengan material pembuatan
                                berkualitas premium.
                            </p>
                            <p style="text-align: justify;">Kayu solid mahoni, engineered wood, finishing vinir jati
                                white wash</p>
                            <a href="<?= base_url('/product/coffee-table-ilena-plint-base') ?>"
                                style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                selengkapnya..</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=cutout">CutOut</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-start">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi CutOut</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=bufet-tv">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=coffee-table">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=console-table">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=dresser-3-drawer">Dresser 3
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=dresser-5-drawer">Dresser 5
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=dresser-6-drawer">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=king-bed">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=queen-bed">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=side-table">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=single-bed">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex:1;" class="d-flex gap-4 mt-3">
                        <div style="flex: 1;">
                            <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                src="https://ilenafurniture.com/viewvar/10301401/4" alt="dresser-3-drawer">
                        </div>
                        <div style="flex: 1;">
                            <h3 class="teks-sedang mb-3">Coffee Table</h3>
                            <p style="text-align: justify" class="mb-2">Side table minimalis ini merupakan furniture
                                tambahan untuk ruang tamu, kamar tidur, dan kantor yang akan melengkapi interior rumah
                                jadi lebih aesthetic.
                                Koleksi Ilena Furniture series Cutout didesain eksklusif dengan material pembuatan
                                berkualitas premium.
                            </p>
                            <p style="text-align: justify;">Kayu solid mahoni, engineered wood, dilapisi vinir jati
                                dengan finishing whitewash</p>
                            <a href="<?= base_url('/product/side-table-ilena-cutout') ?>"
                                style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                selengkapnya..</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-list-nav"></div>
    </div>
</div>