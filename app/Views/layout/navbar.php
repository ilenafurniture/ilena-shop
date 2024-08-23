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
            <div class="child-list-nav" style="overflow: auto;">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Sorely</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=bookshelf" onmouseover="hoverListNav('sorely-bookshelf')">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=bufet-tv" onmouseover="hoverListNav('sorely-bufet')">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=coffee-table" onmouseover="hoverListNav('sorely-coffee')">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=console-table" onmouseover="hoverListNav('sorely-console')">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-3-drawer" onmouseover="hoverListNav('sorely-3drawer')">Dresser 3
                                Drawer</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=dresser-6-drawer" onmouseover="hoverListNav('sorely-tallcabinet')">Dresser Tall
                                Cabinet
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=king-bed" onmouseover="hoverListNav('sorely-king')">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=meja-nakas" onmouseover="hoverListNav('sorely-nakas')">Meja Nakas</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=queen-bed" onmouseover="hoverListNav('sorely-queen')">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=side-table" onmouseover="hoverListNav('sorely-side')">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=single-bed" onmouseover="hoverListNav('sorely-single')">Single Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=sorely&jenis=wardrobe" onmouseover="hoverListNav('sorely-wardrobe')">Wardrobe</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 sorely-penjelasan" id="sorely-bookshelf">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900201" alt="sorely boookshelf">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bookshelf</h3>
                                <p style="text-align: justify" class="mb-2">Rak buku ini dirancang dengan desain yang gaya modern & minimalis dengan 2 bagian 4 space rak ambalan terbuka dan 1 kabinet bawah dengan pintu tertutup yang bisa jadi tempat penyimpanan buku dan barang pajangan kesayangan. Rak ini cocok dijadikan salah satu item yang ditempatkan pada ruang tamu, ruang kerja, hingga kantor.
                                    Koleksi Ilena Furniture series Sorely didesain eksklusif dengan material kayu dan rangka besi berkualitas premium.</p>
                                <a href="<?= base_url('/product/bookshelf-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-bufet">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901601" alt="dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bufet TV</h3>
                                <p style="text-align: justify" class="mb-2">Credenza TV ini didesain secara khusus sebagai tempat TV dan perangkat lainnya. Dilengkapi juga dengan kabinet laci dengan handle mushroom yang nyaman digenggaman tangan dan 1 lubang kabel di bagian belakang. Furnitur ini membuat peralatan elektronik dan barang lainnya tertata lebih rapi dan menambah estetika di ruang keluarga jadi lebih eye catching.
                                    Koleksi Ilena Furniture series Sorely didesain eksklusif dengan material pembuatan berkualitas premium.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-coffee">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900301" alt="dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Coffee Table</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan memadukan 2 jenis material berbeda yang menghasilkan sebuah coffee table unik yang eye catching dengan daya tahan tinggi dari material kayu dan logam terbaik. Membuat suasana bersantai bersama jadi lebih nyaman dan menyenangkan. Cocok ditempatkan pada ruang tamu, ruang keluarga, teras hingga lobby kantor.
                                    Koleksi Ilena Furniture Series Sorely didesain eksklusif dengan material pembuatan berkualitas premium.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-console">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900401" alt="sorely-console">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Console Table</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan desain modern & minimalis yang stylish sebagai bagian dari furniture dekorasi ruangan. Perpaduan dari kayu dan besi yang menghasilkan furniture yang unik dan eye catching dengan daya tahan yang lebih baik.
                                    Koleksi Ilena Furniture series Sorely didesain eksklusif dengan material pembuatan berkualitas premium.</p>
                                <a href="<?= base_url('/product/console-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-3drawer">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10900601" alt="sorely-dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 Drawer</h3>
                                <p style="text-align: justify" class="mb-2">Lemari laci 3 tingkat berdesain stylish dan minimalis ini cocok digunakan sebagai tempat penyimpanan berbagai barang dan pakaian. Dengan 3 tingkat kabinet laci ini, bisa memungkinkan untuk lebih banyak menyimpan barang dengan rapi dan aman dalam satu tempat saja. Dilengkapi dengan handle mushroom hitam dan rangka 4 rangka kaki yang kokoh.
                                    Koleksi Ilena Furniture series Sorely didesain eksklusif dengan material pembuatan berkualitas premium.</p>
                                <a href="<?= base_url('/product/dresser-3-drawer-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-tallcabinet">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="" alt="sorely-dresser-tall-cabinet">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser Tall Cabinet</h3>
                                <p style="text-align: justify" class="mb-2"></p>
                                <a href="<?= base_url('/product') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-king">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901201" alt="sorely-king-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">King Bed</h3>
                                <p style="text-align: justify" class="mb-2">Tempat tidur ini dirancang dengan bed frame dari paduan kayu dan besi yang kuat namun terlihat unik dan estetik. Dilengkapi dengan headboard cukup tinggi dan dipan dari kayu sehingga mampu menopang beban berat tanpa perlu khawatir lagi. Tempat tidur king size ini sangat cocok untuk ditempatkan di kamar tidur utama.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-nakas">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901501" alt="sorely-meja-nakas">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Meja Nakas</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan desain modern yang minimalis yang stylish.Model meja ini terbuat dari paduan bahan kayu dan logam yang selaras membuat visualnya unik dan menawan. Cocok untuk melengkapi furniture di ruang tamu, ruang keluarga, hingga lobby.</p>
                                <a href="<?= base_url('/product/meja-nakas-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-queen">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901301" alt="sorely-queen-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Queen Bed</h3>
                                <p style="text-align: justify" class="mb-2">Tempat tidur ini dirancang dengan bed frame kayu solid dan besi yang kuat namun unik dan estetik. Dilengkapi dengan headboard cukup tinggi dan dipan dari kayu sehingga mampu menopang beban berat tanpa perlu khawatir.</p>
                                <a href="<?= base_url('/product/queen-bed-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-side">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901401" alt="sorely-side">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Side Table</h3>
                                <p style="text-align: justify" class="mb-2">Nakas/meja samping multifungsi dengan desain modern & minimalis yang stylish . Cocok digunakan sebagai tempat buku, hp, hingga lampu tidur. Side table ini bisa diletakkan pada samping kasur atau sudut ruangan lain sesuai kebutuhan. Dengan handle mushroom yang pas digenggam tangan dengan frame besi yang menambah ketahanan.</p>
                                <a href="<?= base_url('/product/side-table-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-single">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901801" alt="sorely-single-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Single Bed</h3>
                                <p style="text-align: justify" class="mb-2">Single bed Sorely memiliki desain unik dari paduan material kayu dan besi. Tetap mengedepankan kenyamanan, paduan 2 material ini membuat single bed lebih kokoh dan awet hingga berdekade lamanya.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 sorely-penjelasan" id="sorely-wardrobe">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10901701" alt="sorely-wardrobe">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Wardrobe</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan menyatukan 2 elemen material kayu solid dan besi, furniture lemari pakaian ini hadir dengan wujud desain unik dengan ambalan rak terbuka dan kabinet tertutup yang dijadikan sebagai tempat gantung pakaian serta 1 kabinet tertutup kecil di sisi bawah kiri yang bisa dijadikan tempat menyimpan keperluan lainnya.</p>
                                <a href="<?= base_url('/product/wardrobe-ilena-sorely') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <p class="text-secondary">Terinspirasi oleh keindahan perpaduan dua material: kayu hangat dan logam tebal. Dibuat dengan sungguh-sungguh untuk melengkapi interior estetis, menghadirkan kenyamanan dan ketenangan bagi setiap penghuninya. Kami berbagi semangat kami dengan nama Ilena.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=cabana">Cabana</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Cabana</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=bookshelf" onmouseover="hoverListNav('cabana-bookshelf')">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=bufet-tv" onmouseover="hoverListNav('cabana-bufet')">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=coffee-table" onmouseover="hoverListNav('cabana-coffee')">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=console-table" onmouseover="hoverListNav('cabana-console')">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=dresser-3-drawer" onmouseover="hoverListNav('cabana-3drawer')">Dresser 3
                                Drawer</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=dresser-5-drawer" onmouseover="hoverListNav('cabana-5drawer')">Dresser 5
                                Drawer</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=dresser-6-drawer" onmouseover="hoverListNav('cabana-6drawer')">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=king-bed" onmouseover="hoverListNav('cabana-king')">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=queen-bed" onmouseover="hoverListNav('cabana-queen')">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=side-table" onmouseover="hoverListNav('cabana-side')">Side Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=cabana&jenis=single-bed" onmouseover="hoverListNav('cabana-single')">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="d-flex gap-4 mb-3 cabana-penjelasan" id="cabana-bookshelf">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10100201" alt="cabana-bookshelf">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bookshelf</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan konstruksi bahan kayu solid yang kuat, rak buku dengan ukuran yang cukup besar ini bisa difungsikan juga sebagai partisi ruang untuk percantik interior Anda.</p>
                                <a href="<?= base_url('/product/bookshelf-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-bufet">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10101601" alt="cabana-bufet">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bufet TV</h3>
                                <p style="text-align: justify" class="mb-2">Koleksi Ilena Furniture series Cabana didesain eksklusif dengan material pembuatan berkualitas premium.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-coffee">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10100301" alt="cabana-coffee-table">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Coffee Table</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan model modern minimalis yang menawan dan praktis. Perpaduan warna coklat dan hitam yang harmonis membuat tampilan meja kopi ini lebih tegas dan elegan. furniture ini cocok ditempatkan pada ruang tamu, ruang keluarga, hingga lobby kantor yang memaksimalkan spot berkumpul jadi lebih nyaman dan menyenangkan.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-console">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10100401" alt="cabana-console-table">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Console Table</h3>
                                <p style="text-align: justify" class="mb-2">Sebuah meja konsol dengan model sederhana yang difungsikan untuk memaksimalkan tampilan ruang kosong di belakang sofa, atau dinding menjadi lebih menarik. Agar lebih sempurna, Anda bisa menambahkan aksesoris sesuai dengan preferensi pribadi.</p>
                                <a href="<?= base_url('/product/Console-Table-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-3drawer">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10100601" alt="cabana-dresser-3-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 3 Drawer</h3>
                                <p style="text-align: justify" class="mb-2">Dresser 3 tingkat makin banyak diminati dengan desain modern minimalis yang tetap stylish. Dengan desain yang multifungsi ini, Anda bisa menyimpan berbagai macam pakaian, dan barang-barang lain sesuai dengan kebutuhan. Dilengkapi dengan 3 laci tingkat vertikal yang dengan handle mushroom yang semakin mempertegas kesan elegan.</p>
                                <a href="<?= base_url('/product/dresser-3-drawer-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-5drawer">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10100801" alt="cabana-dresser-5-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 5 Drawer</h3>
                                <p style="text-align: justify" class="mb-2">Dresser 5 tingkat makin banyak diminati dengan desain modern minimalis yang tetap stylish. Dengan desain yang multifungsi ini, Anda bisa menyimpan berbagai macam pakaian, dan barang-barang lain sesuai dengan kebutuhan. Dilengkapi dengan 5 laci tingkat vertikal yang dengan handle mushroom warna hitam yang semakin mempertegas kesan elegan.</p>
                                <a href="<?= base_url('/product/dresser-5-drawer-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-6drawer">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10100901" alt="cabana-dresser-6-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Drawer</h3>
                                <p style="text-align: justify" class="mb-2">Dresser 6 tingkat makin banyak diminati dengan desain modern minimalis yang tetap stylish. Dengan desain yang multifungsi ini, Anda bisa menyimpan berbagai macam pakaian, dan barang-barang lain sesuai dengan kebutuhan. Dilengkapi dengan 6 laci tingkat vertikal yang dengan handle mushroom warna hitam yang semakin mempertegas kesan elegan.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-king">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10101201" alt="cabana-king-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">King Bed</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang secara khusus dengan gaya modern minimalis. Dengan paduan warna kontras yang selaras, menjadikan king bed ini lebih menarik dan unik.</p>
                                <a href="<?= base_url('/product/king-bed-ilena-water-case') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-queen">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10101301" alt="cabana-queen-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Queen Bed</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang secara khusus dengan gaya modern minimalis. Dengan paduan warna kontras yang selaras, menjadikan queen bed ini lebih menarik dan unik.</p>
                                <a href="<?= base_url('/product/Queen-Bed-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-side">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10101401" alt="cabana-side-table">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Side Table</h3>
                                <p style="text-align: justify" class="mb-2">Nakas/ meja samping multifungsi dengan desain modern & minimalis yang stylish . Cocok digunakan sebagai tempat buku, hp, hingga lampu tidur. Side table ini bisa diletakkan pada samping kasur atau sudut ruangan lain sesuai kebutuhan. Dengan handle mushroom yang pas digenggam tangan dengan frame besi yang menambah ketahanan.</p>
                                <a href="<?= base_url('/product/Side-Table-Ilena-Cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 cabana-penjelasan" id="cabana-single">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10101801" alt="cabana-single-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Single Bed</h3>
                                <p style="text-align: justify" class="mb-2">Tempat tidur single bed ini di desain dengan gaya modern klasik dengan menonjolkan ciri khas dari tekstur kayu jati. Ranjang tidur ini dirancang dengan model sederhana dengan mempertimbangkan kenyamanan. Cocok untuk mengisi ruang tidur tamu atau anak.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-cabana') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <p class="text-secondary">Modern ala Ilena hadir dengan berbagai model menawan yang akan membuat interior ruang jadi lebih sempurna. Dengan warna coklat serat kayu yang khas serta finishing satin yang menjadi highlight pada series Cabana. Material kayu masih jadi primadona dengan berbagai inovasi sentuhan modern yang membuat tampilan tidak lekang oleh waktu.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;"
                href="/product?koleksi=orca">Orca</a>
            <div class="child-list-nav">
                <div class="container d-flex align-items-stretch py-4">
                    <div style="flex: 1;">
                        <p class="m-0" style="font-size:14px;">Jelajahi Orca</p>
                        <div class="ms-2">
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=bookshelf" onmouseover="hoverListNav('orca-bookshelf')">Bookshelf</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=bufet-tv" onmouseover="hoverListNav('orca-bufet')">Bufet TV</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=coffee-table" onmouseover="hoverListNav('orca-coffee')">Coffee Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=console-table" onmouseover="hoverListNav('orca-console')">Console
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=dresser-6-drawer" onmouseover="hoverListNav('orca-6drawer')">Dresser 6
                                Drawer
                            </a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=king-bed" onmouseover="hoverListNav('orca-king')">King
                                Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=queen-bed" onmouseover="hoverListNav('orca-queen')">Queen Bed</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=side-table" onmouseover="hoverListNav('orca-side')">Side
                                Table</a>
                            <a class="w-100 d-block" style="text-decoration: none; font-weight:500; font-size:20px;"
                                href="/product?koleksi=orca&jenis=single-bed" onmouseover="hoverListNav('orca-single')">Single Bed</a>
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <div class="d-flex gap-4 mb-3 orca-penjelasan" id="orca-bookshelf">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10600201" alt="orca-bookshelf">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bookshelf</h3>
                                <p style="text-align: justify" class="mb-2">Rak buku dari series Orca hadir dengan desain modern minimalis yang stylish. Terbuat dari material kayu premium yang tentunya kuat dan kokoh untuk waktu yang lama. Aksen warna putih atau hitam mempertegas kesan elegan dan tegas dengan tetap mempertahankan suasana hangat dari elemen kayu.</p>
                                <a href="<?= base_url('/product/bookshelf-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-bufet">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10601601" alt="orca-bufet">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Bufet TV</h3>
                                <p style="text-align: justify" class="mb-2">Credenza TV dengan series Orca menawarkan furnitur dengan desain modern minimalis yang sangat praktis dan cocok untuk masyarakat urban. Terbuat dari kayu yang kokoh dengan warna yang hangat ditambah akses warna putih atau hitam membuat tampilan lebih tegas dan elegan.</p>
                                <a href="<?= base_url('/product/bufet-tv-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-coffee">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10600301" alt="orca-coffee">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Coffee Table</h3>
                                <p style="text-align: justify" class="mb-2">Dibuat dengan menggabungkan 2 warna yang menghasilkan sebuah meja kopi elegan dan unik. Warna hitam/putih jadi aksen simbol ketegasan yang membuat setiap mata terpanah untuk memandang ke arah meja ini. Cocok digunakan pada area berkumpul dalam ruangan.</p>
                                <a href="<?= base_url('/product/coffee-table-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-console">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10600401" alt="orca-console">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Console Table</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang secara khusus sebagai furniture tambahan yang melengkapi keindahan interior ruang. Meja konsol ini terbuat dari material kayu dengan gaya sederhana dan unik khas modern minimalis dari series Orca ala Ilena Furniture.</p>
                                <a href="<?= base_url('/product/console-table-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-6drawer">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10600901" alt="orca-dresser-6-drawer">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Dresser 6 Drawer</h3>
                                <p style="text-align: justify" class="mb-2">Dresser 6 laci dari Series Orca hadir dengan desain modern minimalis yang tetap stylish dan praktis. Warna kayu yang hangat dan dikombinasikan warna putih yang jadi highlight tampilan furnitur dresser ini. Terbuat dari bahan kayu premium yang pastinya kokoh dan kuat untuk waktu yang lama. Cocok untuk berbagai kebutuhan penyimpanan barang Anda.</p>
                                <a href="<?= base_url('/product/dresser-6-drawer-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-king">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10601201" alt="orca-king-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">King Bed</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dari bahan kayu, King Bed ini merupakan pilihan yang tepat untuk Anda yang menyukai model edgy dengan aksen yang on point. Warna kayu dan hitam/ putih pada ranjang tidur ini dihasilkan dari proses finishing sempurna yang tidak mudah pudar.</p>
                                <a href="<?= base_url('/product/King-Bed-Ilena-Orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-queen">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10601301" alt="orca-queen-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Queen Bed</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dari bahan kayu, Queen Bed ini merupakan pilihan yang tepat untuk Anda yang menyukai model edgy dengan aksen yang on point. Warna kayu dan hitam/ putih pada ranjang tidur ini dihasilkan dari proses finishing sempurna yang tidak mudah pudar.</p>
                                <a href="<?= base_url('/product/queen-bed-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-side">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10601401" alt="orca-side-table">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Side Table</h3>
                                <p style="text-align: justify" class="mb-2">Side table minimalis dengan paduan corak warna hitam/putih dan coklat kayu yang kontras memberikan sentuhan cantik & unik. Dengan desain minimalisnya, meja ini mampu menyempurnakan interior di ruang tamu, kamar tidur, kantor, dan sudut ruang lain jadi lebih lengkap dan elegan.</p>
                                <a href="<?= base_url('/product/side-table-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <div class="d-none gap-4 mb-3 orca-penjelasan" id="orca-single">
                            <div style="flex: 1;">
                                <img style="border-radius:6px; overflow: hidden; object-fit: cover; width: 100%; height: 100%;"
                                    src="/viewpic/10601801" alt="orca-single-bed">
                            </div>
                            <div style="flex: 1;">
                                <h3 class="teks-sedang mb-3">Single Bed</h3>
                                <p style="text-align: justify" class="mb-2">Dirancang dengan menonjolkan warna hitam/putih yang menjadi highlight dari rajang tidur single bed series Orca. Dengan tetap menggunakan kayu yang kokoh dan nyaman digunakan untuk waktu yang lama.</p>
                                <a href="<?= base_url('/product/single-bed-ilena-orca') ?>"
                                    style="display: inline; font-size: 10px; text-decoration: none; color: var(--merah);">lihat
                                    selengkapnya..</a>
                            </div>
                        </div>
                        <p class="text-secondary">Modern ala Ilena hadir dengan berbagai model menawan yang akan membuat interior ruang jadi lebih sempurna. Dengan warna coklat serat kayu yang khas serta finishing satin yang menjadi highlight pada series Cabana. Material kayu masih jadi primadona dengan berbagai inovasi sentuhan modern yang membuat tampilan tidak lekang oleh waktu.</p>
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