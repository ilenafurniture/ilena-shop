<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div>
    <div>
        <div class="img-teks">
            <h1 class="teks-besar">Sofa Tamu</h1>
            <h5>Ruang Tamu Impian Menjadi Kenyataan</h5>
            <div class="d-flex gap-4 anak-img-teks">
                <a href="#">Selengkapnya ></a>
                <a href="#">Beli Sekarang ></a>
            </div>
        </div>
        <img class="img-besar" src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Gambar Hero">
    </div>

    <div class="container my-5">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="d-flex gap-3">
                        <a href="https://google.com" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1675485470862-9af548e93466?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                        <a href="" class="img-kategori">
                            <img src="https://plus.unsplash.com/premium_photo-1676968003017-ae30ca56309d?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                        <a href="" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-flex gap-3">
                        <a href="" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1437419764061-2473afe69fc2?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                        <a href="" class="img-kategori">
                            <img src="https://plus.unsplash.com/premium_photo-1667355489924-0ce0b2bd9961?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                        <a href="" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1533090161767-e6ffed986c88?q=80&w=2069&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-flex gap-3">
                        <a href="" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1511389026070-a14ae610a1be?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                        <a href="" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1573104049264-5324ea0027d5?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                        <a href="" class="img-kategori">
                            <img src="https://images.unsplash.com/photo-1493934558415-9d19f0b2b4d2?q=80&w=2054&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="container d-flex justify-content-between mb-5 align-items-end">
        <h1 class="teks-besar">Ilena<br>Furniture</h1>
        <h1 class="teks-sedang mb-1">Desain Pilihan<br>Indonesia.</h1>
    </div>

    <div class="container mb-4">
        <video class="video-besar" autoplay muted loop>
            <source src="img/iphone.mp4" type="video/mp4">
        </video>
    </div>

    <div class="container mb-5">
        <h1 class="teks-besar text-center">Produk Kami</h1>
    </div>

    <div class="container mb-5">
        <div class="container-card1">
            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span>50%</span>
                        <div class="d-flex flex-column gap-2">
                            <a class="card1-btn-img" href="/wishlist"><i class="material-icons">bookmark_border</i></a>
                            <a class="card1-btn-img" href="/cart"><i class="material-icons">shopping_cart</i></a>
                        </div>
                    </div>
                    <a href="/product/1">
                        <img src="img/contoh.webp" alt="">
                    </a>
                </div>
                <div class="container-varian mb-1">
                    <input id="varian-1-1" type="radio" name="varian">
                    <label for="varian-1-1"><span style="background-color: brown;"></span></label>
                    <input id="varian-1-2" type="radio" name="varian">
                    <label for="varian-1-2"><span style="background-color: aqua;"></span></label>
                    <input id="varian-1-3" type="radio" name="varian">
                    <label for="varian-1-3"><span style="background-color: grey;"></span></label>
                </div>
                <h5>Rak Serbaguna</h5>
                <div class="d-flex gap-2">
                    <p class="harga">Rp 50,000.000</p>
                    <p class="harga-diskon">Rp 100,000.000</p>
                </div>
            </div>

            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span>50%</span>
                        <div class="d-flex flex-column gap-2">
                            <a class="card1-btn-img" href="/wishlist"><i class="material-icons">bookmark_border</i></a>
                            <a class="card1-btn-img" href="/cart"><i class="material-icons">shopping_cart</i></a>
                        </div>
                    </div>
                    <a href="#"><img src="https://i.ibb.co/86L9vkV/DTV-706-MAHONI-DEPAN.webp" alt="DTV-706-MAHONI-DEPAN"></a>
                </div>
                <div class="container-varian mb-1">
                    <input id="varian-2-1" type="radio" name="varian">
                    <label for="varian-2-1"><span style="background-color: brown;"></span></label>
                    <input id="varian-2-2" type="radio" name="varian">
                    <label for="varian-2-2"><span style="background-color: aqua;"></span></label>
                    <input id="varian-2-3" type="radio" name="varian">
                    <label for="varian-2-3"><span style="background-color: grey;"></span></label>
                </div>
                <h5>Lemari TV</h5>
                <div class="d-flex gap-2">
                    <p class="harga">Rp 50,000.000</p>
                    <p class="harga-diskon">Rp 100,000.000</p>
                </div>
            </div>

            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span>50%</span>
                        <div class="d-flex flex-column gap-2">
                            <a class="card1-btn-img" href="/wishlist"><i class="material-icons">bookmark_border</i></a>
                            <a class="card1-btn-img" href="/cart"><i class="material-icons">shopping_cart</i></a>
                        </div>
                    </div>
                    <a href="#"><img src="https://i.ibb.co/VgDyQz7/ALA-859-WINGE-PUTIH.webp" alt="ALA-859-WINGE-PUTIH"></a>
                </div>
                <div class="container-varian mb-1">
                    <input id="varian-3-1" type="radio" name="varian">
                    <label for="varian-3-1"><span style="background-color: brown;"></span></label>
                    <input id="varian-3-2" type="radio" name="varian">
                    <label for="varian-3-2"><span style="background-color: aqua;"></span></label>
                    <input id="varian-3-3" type="radio" name="varian">
                    <label for="varian-3-3"><span style="background-color: grey;"></span></label>
                </div>
                <h5>Lemari Anak</h5>
                <div class="d-flex gap-2">
                    <p class="harga">Rp 50,000.000</p>
                    <p class="harga-diskon">Rp 100,000.000</p>
                </div>
            </div>

            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span>50%</span>
                        <div class="d-flex flex-column gap-2">
                            <a class="card1-btn-img" href="/wishlist"><i class="material-icons">bookmark_border</i></a>
                            <a class="card1-btn-img" href="/cart"><i class="material-icons">shopping_cart</i></a>
                        </div>
                    </div>
                    <a href="#"><img src="https://i.ibb.co/TmjCjsZ/RB-6180-P-DEPAN.webp" alt="RB-6180-P-DEPAN"></a>
                </div>
                <div class="container-varian mb-1">
                    <input id="varian-4-1" type="radio" name="varian">
                    <label for="varian-4-1"><span style="background-color: brown;"></span></label>
                    <input id="varian-4-2" type="radio" name="varian">
                    <label for="varian-4-2"><span style="background-color: aqua;"></span></label>
                    <input id="varian-4-3" type="radio" name="varian">
                    <label for="varian-4-3"><span style="background-color: grey;"></span></label>
                </div>
                <h5>Rak Serbaguna</h5>
                <div class="d-flex gap-2">
                    <p class="harga">Rp 50,000.000</p>
                    <p class="harga-diskon">Rp 100,000.000</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5 d-flex justify-content-center">
        <a href="/product" class="btn-lonjong">Lihat Semua Produk <i class="material-icons">arrow_forward</i></a>
    </div>

</div>

<?= $this->endSection(); ?>