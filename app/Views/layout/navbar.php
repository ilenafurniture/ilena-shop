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
    <!-- <p class="m-0 text-center" style="color: #844709;">Lebih hemat dengan Free Ongkir hingga 100%</p> -->
    <p class="m-0 text-center" style="color: white;">Lebih hemat dengan Free Ongkir hingga 100%</p>
</div>
<div class="justify-content-center w-100 show-flex-ke-hide"
    style="background-color: whitesmoke; position: sticky; top:-1px; z-index: 99;">
    <div class="d-flex align-items-center py-2 gap-5">

        <!-- Bookshelf -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=bookshelf">Bookshelf</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Bookshelf</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=bookshelf"
                                onmouseover="hoverListNav('bookshelf-cabana')">Cabana</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=bufet-tv"
                                onmouseover="hoverListNav('bookshelf-cody')">Cody</a> -->
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=coffee-table"
                                onmouseover="hoverListNav('bookshelf-cutout')">Cutuot</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=console-table"
                                onmouseover="hoverListNav('bookshelf-industrial')">Industrial</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-3-drawer"
                                onmouseover="hoverListNav('bookshelf-metalframe')">Metal Frame</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-6-drawer"
                                onmouseover="hoverListNav('bookshelf-orca')">Orca</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=king-bed"
                                onmouseover="hoverListNav('bookshelf-plintbase')">Plint Base</a> -->
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=queen-bed"
                                onmouseover="hoverListNav('bookshelf-socoplate')">Socoplate</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=single-bed"
                                onmouseover="hoverListNav('bookshlef-sorely')">Sorely</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10100201/1" alt="bookshelf cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Rak buku ini dirancang dengan desain yang
                                    gaya modern & minimalis dengan 2 bagian 4 space rak ambalan terbuka dan 1 kabinet
                                    bawah dengan pintu tertutup yang bisa jadi tempat penyimpanan buku dan barang
                                    pajangan kesayangan. Rak ini cocok dijadikan salah satu item yang ditempatkan pada
                                    ruang tamu, ruang kerja, hingga kantor.</p>
                                <a href="<?= base_url('/product/Bookshelf-Ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901601" alt="bookshelf cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cody</h3>
                                <p style="text-align: justify" class="mb-2">Credenza TV ini didesain secara khusus
                                    sebagai tempat TV dan perangkat lainnya. Dilengkapi juga dengan kabinet laci dengan
                                    handle mushroom yang nyaman digenggaman tangan dan 1 lubang kabel di bagian
                                    belakang. Furnitur ini membuat peralatan elektronik dan barang lainnya tertata lebih
                                    rapi dan menambah estetika di ruang keluarga jadi lebih eye catching.
                                    Koleksi Ilena Furniture series Sorely didesain eksklusif dengan material pembuatan
                                    berkualitas premium.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->
                        <!-- <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900301" alt="dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Coffee Table</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.
                                    Koleksi Ilena Furniture Series Sorely didesain eksklusif dengan material pembuatan
                                    berkualitas premium.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->
                        <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10400201/1" alt="bookshelf industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Rak buku ini dirancang dengan desain yang
                                    gaya modern & minimalis dengan 2 bagian 4 space rak ambalan terbuka dan 1 kabinet
                                    bawah dengan pintu tertutup yang bisa jadi tempat penyimpanan buku dan barang
                                    pajangan kesayangan. Rak ini cocok dijadikan salah satu item yang ditempatkan pada
                                    ruang tamu, ruang kerja, hingga kantor.</p>
                                <a href="<?= base_url('/product/Bookshelf-Ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900601" alt="sorely-dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 Drawer</h3>
                                <p style="text-align: justify" class="mb-2">Lemari laci 3 tingkat berdesain stylish dan
                                    minimalis ini cocok digunakan sebagai tempat penyimpanan berbagai barang dan
                                    pakaian. Dengan 3 tingkat kabinet laci ini, bisa memungkinkan untuk lebih banyak
                                    menyimpan barang dengan rapi dan aman dalam satu tempat saja. Dilengkapi dengan
                                    handle mushroom hitam dan rangka 4 rangka kaki yang kokoh.
                                    Koleksi Ilena Furniture series Sorely didesain eksklusif dengan material pembuatan
                                    berkualitas premium.</p>
                                <a href="<?= base_url('/product/dresser-3-drawer-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->
                        <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10600201/1" alt="bookshelf orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Orca</h3>
                                <p style="text-align: justify" class="mb-2">Rak buku ini dirancang dengan desain yang
                                    gaya modern & minimalis dengan 2 bagian 4 space rak ambalan terbuka dan 1 kabinet
                                    bawah dengan pintu tertutup yang bisa jadi tempat penyimpanan buku dan barang
                                    pajangan kesayangan. Rak ini cocok dijadikan salah satu item yang ditempatkan pada
                                    ruang tamu, ruang kerja, hingga kantor.</p>
                                <a href="<?= base_url('/product/bookshelf-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901201" alt="sorely-king-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">King Bed</h3>
                                <p style="text-align: justify" class="mb-2">Tempat tidur ini dirancang dengan bed frame
                                    dari paduan kayu dan besi yang kuat namun terlihat unik dan estetik. Dilengkapi
                                    dengan headboard cukup tinggi dan dipan dari kayu sehingga mampu menopang beban
                                    berat tanpa perlu khawatir lagi. Tempat tidur king size ini sangat cocok untuk
                                    ditempatkan di kamar tidur utama.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->
                        <!-- <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901501" alt="sorely-meja-nakas">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Meja Nakas</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan desain modern yang
                                    minimalis yang stylish.Model meja ini terbuat dari paduan bahan kayu dan logam yang
                                    selaras membuat visualnya unik dan menawan. Cocok untuk melengkapi furniture di
                                    ruang tamu, ruang keluarga, hingga lobby.</p>
                                <a href="<?= base_url('/product/meja-nakas-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->
                        <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10900201/1" alt="sorely-queen-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Rak buku ini dirancang dengan desain yang
                                    gaya modern & minimalis dengan 2 bagian 4 space rak ambalan terbuka dan 1 kabinet
                                    bawah dengan pintu tertutup yang bisa jadi tempat penyimpanan buku dan barang
                                    pajangan kesayangan. Rak ini cocok dijadikan salah satu item yang ditempatkan pada
                                    ruang tamu, ruang kerja, hingga kantor.</p>
                                <a href="<?= base_url('/product/bookshelf-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901401" alt="sorely-side">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Side Table</h3>
                                <p style="text-align: justify" class="mb-2">Nakas/meja samping multifungsi dengan desain
                                    modern & minimalis yang stylish . Cocok digunakan sebagai tempat buku, hp, hingga
                                    lampu tidur. Side table ini bisa diletakkan pada samping kasur atau sudut ruangan
                                    lain sesuai kebutuhan. Dengan handle mushroom yang pas digenggam tangan dengan frame
                                    besi yang menambah ketahanan.</p>
                                <a href="<?= base_url('/product/side-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->

                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan
                            logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan
                            kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan nama
                            Ilena.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Bookshelf -->

        <!-- Coffee Table -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=coffee-table">Coffee Table</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Coffee Table</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-cutout')">Cutuot</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=metal-frame&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-metalframe')">Metal Frame</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-orca')">Orca</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=king-bed"
                                onmouseover="hoverListNav('bookshelf-plintbase')">Plint Base</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-socoplate')">Socoplate</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable-sorely')">Sorely</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10100301/1" alt="coffee table cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10200301/1" alt="coffee table cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Coffee-Table-Ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900301" alt="dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">CutOut</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10400301/1" alt="coffee table industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Coffee-Table-Ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10500301/1" alt="sorely-dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Coffee-Table-Ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10600301/1" alt="coffee table orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10700301/1" alt="coffee table plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10800301/1" alt="coffee table socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Coffee-Table-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10900301/1" alt="coffee table sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11000301/1" alt="coffee table water case">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Water Case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>

                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan
                            logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan
                            kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan nama
                            Ilena.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Coffee Table -->

        <!-- Console Table -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=console-table">Console Table</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Console Table</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-cutout')">Cutuot</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=metal-frame&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-metalframe')">Metal Frame</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-orca')">Orca</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=king-bed"
                                onmouseover="hoverListNav('bookshelf-plintbase')">Plint Base</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-socoplate')">Socoplate</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=console-table"
                                onmouseover="hoverListNav('consoletable-sorely')">Sorely</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 consoletable-penjelasan" id="consoletable-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10100401/1" alt="console table cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Console-Table-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10200401/1" alt="console table cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Console-Table-Ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10300401/1" alt="console table cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">CutOut</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/console-table-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10400401/1" alt="console table industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Console-Table-Ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10500301/1" alt="console table metal frame">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Coffee-Table-Ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10600301/1" alt="coffee table orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10700301/1" alt="coffee table plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10800301/1" alt="coffee table socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Coffee-Table-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10900301/1" alt="coffee table sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11000301/1" alt="coffee table water case">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Water Case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>

                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan
                            logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan
                            kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan nama
                            Ilena.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Console Table -->

        <div class="bg-list-nav"></div>
    </div>
</div>
<script>
function hoverListNav(idElm) {
    console.log('.' + idElm.split('-')[0] + '-penjelasan')
    const arrPenjelasanElm = document.querySelectorAll('.' + idElm.split('-')[0] + '-penjelasan');
    console.log(arrPenjelasanElm)
    arrPenjelasanElm.forEach(penjelasanElm => {
        penjelasanElm.classList.add('d-none')
        penjelasanElm.classList.remove('d-flex')
    });
    console.log(idElm)
    const itemElm = document.getElementById(idElm)
    itemElm.classList.remove('d-none')
    itemElm.classList.add('d-flex')
}
</script>