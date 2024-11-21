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
                                onmouseover="hoverListNav('bookshelf1-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=console-table"
                                onmouseover="hoverListNav('bookshelf1-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-6-drawer"
                                onmouseover="hoverListNav('bookshelf1-orca')">Orca</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=single-bed"
                                onmouseover="hoverListNav('bookshelf1-sorely')">Sorely</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 bookshelf-penjelasan" id="bookshelf1-cabana">
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
                        <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf1-industrial">
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
                        <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf1-orca">
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
                        <div class="d-none gap-4 mb-3 bookshelf-penjelasan" id="bookshelf1-sorely">
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
                                onmouseover="hoverListNav('coffeetable1-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-cutout')">Cutuot</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=metal-frame&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-metalframe')">Metal Frame</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-orca')">Orca</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=king-bed"
                                onmouseover="hoverListNav('bookshelf-plintbase')">Plint Base</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-socoplate')">Socoplate</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=coffee-table"
                                onmouseover="hoverListNav('coffeetable1-sorely')">Sorely</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-cabana">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-cody">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-cutout">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-industrial">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-metalframe">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-orca">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-plintbase">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-socoplate">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-sorely">
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
                        <div class="d-none gap-4 mb-3 coffeetable-penjelasan" id="coffeetable1-watercase">
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
                                onmouseover="hoverListNav('consoletable1-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-cutout')">Cutuot</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=metal-frame&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-metalframe')">Metal Frame</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-orca')">Orca</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-plintbase')">Plint Base</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-socoplate')">Socoplate</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=console-table"
                                onmouseover="hoverListNav('consoletable1-sorely')">Sorely</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 consoletable-penjelasan" id="consoletable1-cabana">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-cody">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10300301/1" alt="console table cutout">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-industrial">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10500401/1" alt="console table metal frame">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10600401/1" alt="coffee table orca">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10700401/1" alt="coffee table plint base">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10800401/1" alt="coffee table socoplate">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10900401/1" alt="coffee table sorely">
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
                        <div class="d-none gap-4 mb-3 consoletable-penjelasan" id="consoletable1-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11000401/1" alt="coffee table water case">
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

        <!-- Credenza -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=credenza">Credenza</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Credenza</p>
                        <div class="ms-2">

                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=console-table"
                                onmouseover="hoverListNav('credenza1-industrial')">Industrial</a>

                        </div>
                    </div>
                    <div style="flex:1;">

                        <div class="d-flex gap-4 mb-3 credenza-penjelasan" id="credenza1-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10400501/1" alt="credenza industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Credenza-Ilena-Industrial') ?>"
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
        <!-- End Credenza -->

        <!-- Dresser -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=console-table">Dresser</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;" class="d-flex">
                        <div style="flex: 0.5;">
                            <!-- Dreeser 3 -->
                            <p class="m-0" style="font-size:14px;">Jelajahi Dresser 3</p>
                            <div class="ms-2 mb-2">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cabana&jenis=dresser-3-drawer"
                                    onmouseover="hoverListNav('dresser3-cabana')">Cabana</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cutout&jenis=dresser-3-drawer"
                                    onmouseover="hoverListNav('dresser3-cutout')">Cutuot</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=socoplate&jenis=dresser-3-drawer"
                                    onmouseover="hoverListNav('dresser3-socoplate')">Socoplate</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=sorely&jenis=dresser-3-drawer"
                                    onmouseover="hoverListNav('dresser3-sorely')">Sorely</a>
                            </div>
                            <!-- End Dresser 3 -->

                            <!-- Drsser 4 -->
                            <p class="m-0" style="font-size:14px;">Jelajahi Dresser 4</p>
                            <div class="ms-2">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser4-cody')">Cody</a>
                            </div>
                            <!-- End Dresser 4 -->
                        </div>
                        <div style="flex: 0.5;">
                            <!-- Dresser 5 -->
                            <p class="m-0" style="font-size:14px;">Jelajahi Dresser 5</p>
                            <div class="mb-4">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cabana&jenis=dresser-5-drawer"
                                    onmouseover="hoverListNav('dresser5-cabana')">Cabana</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cutout&jenis=dresser-5-drawer"
                                    onmouseover="hoverListNav('dresser5-cutout')">Cutout</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=socoplate&jenis=dresser-5-drawer"
                                    onmouseover="hoverListNav('dresser5-socoplate')">Socoplate</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-sorely')">Sorely</a>
                            </div>
                            <!-- Enf Drasser 5 -->

                            <!-- Dresser 6 -->
                            <p class="m-0" style="font-size:14px;">Jelajahi Dresser 6</p>
                            <div class="mb-4">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-cabana')">Cabana</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-cutout')">Cutout</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-metalframe')">Metal Frame</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-orca')">Orca</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-plintbase')">Plint Base</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=dresser-4-drawer"
                                    onmouseover="hoverListNav('dresser6-watercase')">Water case</a>

                            </div>
                            <!-- End Dresser 6 -->
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <!-- Dresser 3 -->
                        <div class="d-flex gap-4 mb-3 dresser-penjelasan" id="dresser3-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10100601/1" alt="dresser3 cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Console-Table-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser3-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10300601/1" alt="dresser3 cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 CutOut</h3>
                                <p style="text-align: jusy" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/console-table-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser3-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10800601/1" alt="Dresser 3 socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Dresser-3-Drawer-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser3-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10900601/1" alt="Dresser 3  sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-3-drawer-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- End Dresser 3 -->

                        <!-- Dresser 4 -->
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser4-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10200701/1" alt="Dresser 4 cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 4 cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Dresser-4-Drawer-Ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- End Dresser 4 -->

                        <!-- Dresser 5 -->
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser5-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10100801/1" alt="dresser 5 cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 5 Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-5-drawer-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser5-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10300901/1" alt="dresser 5 cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 5 CutOut</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser5-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10800801/1" alt="dresser 5 socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 5 Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Dresser-5-Drawer-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- End Dresser 5 -->



                        <!-- Dresser 6 -->
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10100901/1" alt="dresser 5 Canaba">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 5 Canaba</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10300901/1" alt="dresser 6 cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 5 Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10500901/1" alt="dresser 6 Metal Frame">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Metal frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Dresser-6-Drawer-Ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10600901/1" alt="dresser 6 Orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10700901/1" alt="dresser 6 Plint Base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Dresser-6-drawer-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11000901/1" alt="dresser 6 Plint Base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Water case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 dresser-penjelasan" id="dresser6-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901001/1" alt="dresser 6 Sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/dresser-tall-cabinet-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu
                            hangat dan
                            logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis,
                            menghadirkan
                            kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan
                            nama
                            Ilena.</p>
                        <!-- End Dressr 6 -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Dresser -->

        <!-- Bed -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=king-bed+queen-bed">Bed</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;" class="d-flex">
                        <div style="flex: 0.5;">
                            <!-- King Bed -->
                            <p class="m-0" style="font-size:14px;">Jelajahi King Bed</p>
                            <div class="ms-2 mb-2">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cabana&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-cabana')">Cabana</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-cody')">Cody</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cutout&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-cutout')">Cutuot</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=socoplate&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-socoplate')">Socoplate</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=industrial&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-industrial')">Industrial</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=metal-frame&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-metalframe')">Metal Frame</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=orca&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-orca')">Orca</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=plint-base&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-plintbase')">Plint Base</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=sorely&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-sorely')">Sorely</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=water-case&jenis=king-bed"
                                    onmouseover="hoverListNav('king1-watercase')">Water Case</a>
                            </div>
                            <!-- End King Bed-->
                        </div>

                        <div style="flex: 0.5;">
                            <!-- Queen Bed -->
                            <p class="m-0" style="font-size:14px;">Jelajahi Queen Bed</p>
                            <div class="ms-2 mb-2">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cabana&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-cabana')">Cabana</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-cody')">Cody</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cutout&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-cutout')">Cutuot</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=socoplate&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-socoplate')">Socoplate</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=industrial&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-industrial')">Industrial</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=metal-frame&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-metalframe')">Metal Frame</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=orca&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-orca')">Orca</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=plint-base&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-plintbase')">Plint Base</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=sorely&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-sorely')">Sorely</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=water-case&jenis=queen-bed"
                                    onmouseover="hoverListNav('king2-watercase')">Water Case</a>
                            </div>
                            <!-- Queen Bed -->
                        </div>

                        <div style="flex: 0.5;">
                            <!-- Single Bed -->
                            <p class="m-0" style="font-size:14px;">Jelajahi Single Bed</p>
                            <div class="ms-2 mb-2">
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cabana&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-cabana')">Cabana</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cody&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-cody')">Cody</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=cutout&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-cutout')">Cutuot</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=socoplate&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-socoplate')">Socoplate</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=industrial&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-industrial')">Industrial</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=metal-frame&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-metalframe')">Metal Frame</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=orca&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-orca')">Orca</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=plint-base&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-plintbase')">Plint Base</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=sorely&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-sorely')">Sorely</a>
                                <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                    href="/product?koleksi=water-case&jenis=queen-bed"
                                    onmouseover="hoverListNav('king3-watercase')">Water Case</a>
                            </div>
                            <!-- Single Bed -->
                        </div>

                    </div>
                    <div style="flex: 1;">
                        <!-- Bed King -->
                        <div class="d-flex gap-4 mb-3 king-penjelasan" id="king1-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10101201/1" alt="king1 cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/King-Bed-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10201201/1" alt="king1 cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/King-Bed-Ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10301201/1" alt="king1 cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King CutOut</h3>
                                <p style="text-align: jusy" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10801201/1" alt="Bed King socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10401201/1" alt="Bed King industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/King-Bed-Ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10501201/1" alt="Bed King metal frame">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/King-Bed-Ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10601201/1" alt="Bed King Orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/King-Bed-Ilena-Orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10701201/1" alt="Bed King plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901201/1" alt="King sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king1-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11001201/1" alt="King water case">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed King Water Case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/king-Bed-Ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- End bed King -->

                        <!-- Bed queen -->
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10101301/1" alt="queen cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10201301/1" alt="Queen cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/QUEEN-BED-ILENA-CODY') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10301301/1" alt="Queen cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen CutOut</h3>
                                <p style="text-align: jusy" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/queen-bed-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10801301/1" alt="Bed Queen socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10401201/1" alt="Bed Queen industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10501201/1" alt="Bed Queen metal frame">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10601201/1" alt="Bed Queen Orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-Orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10701201/1" alt="Bed Queen plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901201/1" alt="Queen sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king2-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11001301/1" alt="Queen water case">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Queen Water Case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- End bed queen -->

                        <!-- Bed Single -->
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10101801/1" alt="king3 cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10201801/1" alt="king3 cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10301801/1" alt="king3 cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single CutOut</h3>
                                <p style="text-align: jusy" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10801801/1" alt="Bed Single socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10401801/1" alt="Bed Single industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10501801/1" alt="Bed Single metal frame">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10601201/1" alt="Bed Single Orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-Orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10701801/1" alt="Bed Single plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901801/1" alt="King sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 king-penjelasan" id="king3-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11001801/1" alt="King water case">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bed Single Water Case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis
                                    material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan
                                    daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai
                                    bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- End bed KSingle -->

                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu
                            hangat dan
                            logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis,
                            menghadirkan
                            kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan
                            nama
                            Ilena.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Bed -->

        <!-- Side Table -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=side-table">Side Table</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Side Table</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-cutout')">Cutout</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=metal-frame&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-metalframe')">Metal frame</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-orca')">Orca</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-plintbase')">Plint Base</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-socoplate')">Socoplate</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-sorely')">Sorely</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=side-table"
                                onmouseover="hoverListNav('sidetable1-watercase')">Water Case</a>

                        </div>
                    </div>
                    <div style="flex:1;">

                        <div class="d-flex gap-4 mb-3 sidetable-penjelasan" id="sidetable1-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10101401/1" alt="sidetable Cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10201401/1" alt="sidetable cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10301401/1" alt="sidetable cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cutout</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10401401/1" alt="sidetable industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10501401/1" alt="sidetable metalframe">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10601401/1" alt="sidetable orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10701401/1" alt="sidetable plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/side-table-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10801401/1" alt="sidetable socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901401/1" alt="sidetable socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/side-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sidetable-penjelasan" id="sidetable1-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11001401/1" alt="sidetable watercase">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Water case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/side-table-ilena-water-case') ?>"
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
        <!-- End Side Table -->

        <!-- Meja Nakas -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=meja-nakas">Meja Nakas</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Meja Nakas</p>
                        <div class="ms-2">

                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=console-table"
                                onmouseover="hoverListNav('mejanakas1-sorely')">Sorely</a>

                        </div>
                    </div>
                    <div style="flex:1;">

                        <div class="d-flex gap-4 mb-3 mejanakas-penjelasan" id="mejanakas1-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901501/1" alt="mejanakas sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Meja-Nakas-ilena-sorely') ?>"
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
        <!-- End Meja Nakas -->

        <!-- Bufet tv -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=bufet-tv">Bufet TV</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Bufet TV</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-cabana')">Cabana</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cutout&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-cutout')">Cutout</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=industrial&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-industrial')">Industrial</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=metal-frame&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-metalframe')">Metal frame</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-orca')">Orca</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=plint-base&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-plintbase')">Plint Base</a>
                            <!-- <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-socoplate')">Socoplate</a> -->
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-sorely')">Sorely</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=water-case&jenis=bufet-tv"
                                onmouseover="hoverListNav('bufet1-watercase')">Water Case</a>

                        </div>
                    </div>
                    <div style="flex:1;">

                        <div class="d-flex gap-4 mb-3 bufet-penjelasan" id="bufet1-cabana">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10101601/1" alt="bufetTV Cabana">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cabana</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10201601/1" alt="bufetTV cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-cutout">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10301601/1" alt="bufetTV cutout">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cutout</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-cutout') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-industrial">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10401601/1" alt="bufetTV industrial">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Industrial</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-Industrial') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-metalframe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10501601/1" alt="bufetTV metalframe">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Metal Frame</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-Metal-Frame') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-orca">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10601601/1" alt="bufetTV orca">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Orca</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-Orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-plintbase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10701601/1" alt="bufetTV plint base">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Plint Base</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-plint-base') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <!-- <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10801401/1" alt="bufetTV socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div> -->
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901601/1" alt="bufet sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 bufet-penjelasan" id="bufet1-watercase">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/11001601/1" alt="bufetTV watercase">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Water case</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-water-case') ?>"
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
        <!-- End Bufet tv -->

        <!-- Meja Wardrobe -->
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?jenis=wardrobe">Wardrobe</a>
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Wardrobe</p>
                        <div class="ms-2">

                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cody&jenis=wardrobe"
                                onmouseover="hoverListNav('wardrobe1-cody')">Cody</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=socoplate&jenis=wardrobe"
                                onmouseover="hoverListNav('wardrobe1-socoplate')">Socoplate</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=wardrobe"
                                onmouseover="hoverListNav('wardrobe1-sorely')">Sorely</a>

                        </div>
                    </div>
                    <div style="flex:1;">

                        <div class="d-flex gap-4 mb-3 wardrobe-penjelasan" id="wardrobe1-cody">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10201701/1" alt="wardrobe cody">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Cody</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Wardrobe-Ilena-Cody') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 wardrobe-penjelasan" id="wardrobe1-socoplate">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10801701/1" alt="wardrobe socoplate">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Socoplate</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/Wardrobe-Ilena-Socoplate') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 wardrobe-penjelasan" id="wardrobe1-sorely">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewvar/10901701/1" alt="wardrobe sorely">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Sorely</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material
                                    berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya
                                    tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama
                                    jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang
                                    keluarga, teras hingga lobby kantor.</p>
                                <a href="<?= base_url('/product/wardrobe-ilena-sorely') ?>"
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
        <!-- End Wardrobe -->

        <div class="bg-list-nav"></div>
    </div>
</div>
<script>
function hoverListNav(idElm) {
    const arrPenjelasanElm = document.querySelectorAll('.' + idElm.split('-')[0].slice(0, -1) + '-penjelasan');
    // console.log(arrPenjelasanElm)
    arrPenjelasanElm.forEach(penjelasanElm => {
        penjelasanElm.classList.add('d-none')
        penjelasanElm.classList.remove('d-flex')
    });
    // console.log(idElm)
    const itemElm = document.getElementById(idElm)
    itemElm.classList.remove('d-none')
    itemElm.classList.add('d-flex')
}
</script>