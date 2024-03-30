<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten">
    <div class="baris-ke-kolom">
        <div class="limapuluh-ke-seratus">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Produk Kami</a></li>
                    <li class="breadcrumb-item"><a href="/">Meja</a></li>
                    <li class="breadcrumb-item">Meja TV</li>
                    </li>
                </ol>
            </nav>
            <h1 class="teks-besar mb-2">Meja Makan</h1>
            <div class="d-flex gap-2 mb-3">
                <p class="harga">Rp 50,000.000</p>
                <p class="harga-diskon">Rp 100,000.000</p>
            </div>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eveniet ipsa laborum iste ratione quod, modi
                assumenda esse totam fuga quisquam.</p>
            <div class="container-varian mb-3">
                <input id="varian-1-1" type="radio" name="varian">
                <label for="varian-1-1"><span style="background-color: brown;"></span></label>
                <input id="varian-1-2" type="radio" name="varian">
                <label for="varian-1-2"><span style="background-color: aqua;"></span></label>
                <input id="varian-1-3" type="radio" name="varian">
                <label for="varian-1-3"><span style="background-color: grey;"></span></label>
            </div>
            <div class="d-flex gap-3">
                <div class="number-control">
                    <div class="number-left"></div>
                    <input type="number" name="number" class="number-quantity" value="1">
                    <div class="number-right"></div>
                </div>
                <button class="btn-default-merah">Keranjang</button>
            </div>
            <?php if ($produk['tokped'] || $produk['shopee'] || $produk['tiktok']) { ?>
                <div class="mt-4">
                    <p class="mb-2">
                        Produk ini juga tersedia di
                    </p>
                    <div>
                        <?php if ($produk['tokped']) { ?>
                            <a href="<?= $produk['tokped']; ?>" title="Tokopedia" target="blank"><img src="/img/logo/tokopedia.png" class="marketplace"></a>
                        <?php } ?>
                        <?php if ($produk['shopee']) { ?>
                            <a href="<?= $produk['shopee']; ?>" title="Shopee" target="blank"><img src="/img/logo/shopee.png" class="marketplace"></a>
                        <?php } ?>
                        <?php if ($produk['tiktok']) { ?>
                            <a href="<?= $produk['tiktok']; ?>" title="Tiktok" target="blank"><img src="/img/logo/tiktokshop.svg" class="marketplace"></a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <a class="btn-teks-aja my-3" href="/wishlist"><i class="material-icons">bookmark_border</i> Tambah ke
                wishlist</a>

            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse1" aria-expanded="false" aria-controls="flush-collapse1">
                            DIMENSI
                        </button>
                    </h2>
                    <div id="flush-collapse1" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <p class="mb-0 fw-bold">Bagaimana cara mengetahui barang ready?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Ketersediaan stok bisa langsung dengan cara mengecek pada kolom kuantitas di
                                        spesifikasi produk. Apabila telah berhasil melakukan checkout, dapat
                                        dipastikan ketersediaan produk tersebut untuk Anda.
                                    </p>
                                </li>
                            </ul>
                            <p class="mb-0 mt-1 fw-bold">Bagaimana cara melihat promosi terupdate?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Promo produk yang lagi diadakan selalu tersedia dan dapat dilihat pada
                                        website Kami. Selain itu, promosi juga selalu Kami update di akun sosial
                                        media dan juga katalog yang Kami beri di WhatsApp serta email saat Anda
                                        berlangganan dengan layanan email dan WhatsApp Kami.
                                    </p>
                                </li>
                            </ul>
                            <p class="mb-0 mt-1 fw-bold">Darimana pengiriman produk Jasmine Furniture?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Pengiriman produk Jasmine Furniture berasal dari Kota Semarang, Jawa Tengah
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                            PERAWATAN
                        </button>
                    </h2>
                    <div id="flush-collapse2" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <p class="mb-0 fw-bold">Bagaimana cara mengetahui barang ready?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Ketersediaan stok bisa langsung dengan cara mengecek pada kolom kuantitas di
                                        spesifikasi produk. Apabila telah berhasil melakukan checkout, dapat
                                        dipastikan ketersediaan produk tersebut untuk Anda.
                                    </p>
                                </li>
                            </ul>
                            <p class="mb-0 mt-1 fw-bold">Bagaimana cara melihat promosi terupdate?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Promo produk yang lagi diadakan selalu tersedia dan dapat dilihat pada
                                        website Kami. Selain itu, promosi juga selalu Kami update di akun sosial
                                        media dan juga katalog yang Kami beri di WhatsApp serta email saat Anda
                                        berlangganan dengan layanan email dan WhatsApp Kami.
                                    </p>
                                </li>
                            </ul>
                            <p class="mb-0 mt-1 fw-bold">Darimana pengiriman produk Jasmine Furniture?</p>
                            <ul>
                                <li>
                                    <p class="mb-0">
                                        Pengiriman produk Jasmine Furniture berasal dari Kota Semarang, Jawa Tengah
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="limapuluh-ke-seratus">
            <div class="d-flex justify-content-end">
                <div class="d-flex align-items-start flex-column gap-2">
                    <div class="d-flex align-items-end gap-2">
                        <p class="m-0 fw-bold" style="font-size:24px; line-height: 24px">
                            01
                        </p>
                        <p class="m-0">
                            / 05
                        </p>
                    </div>
                    <div>
                        <button style="background-color:white; border: 0;">
                            < </button>
                                <button class="ms-5" style="background-color:white; border: 0;">></button>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <img class="img-detail-prev" src="/img/contoh.webp">
            </div>
            <div class="container-img-detail-select mb-3 mt-3">
                <input id="gambar-1-1" type="radio" name="gambar">
                <label class="img-detail-select" for="gambar-1-1"><img class="img-detail-prev" src="/img/contoh.webp"></label>
                <input id="gambar-1-2" type="radio" name="gambar">
                <label class="img-detail-select" for="gambar-1-2">
                    <img class="img-detail-prev" src="/img/contoh.webp">
                </label>
                <input id="gambar-1-3" type="radio" name="gambar">
                <label class="img-detail-select" for="gambar-1-3"><img class="img-detail-prev" src="/img/contoh.webp"></label>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>