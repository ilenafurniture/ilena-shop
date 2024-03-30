<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<div class="container d-flex konten gap-5">
    <div style="width: 200px;">
        <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Kategori
        </div>
        <div class="collapse py-2" id="collapseExample">
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="kategori-1" class="inp-cbx" />
                <label for="kategori-1" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Meja</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="kategori-2" class="inp-cbx" />
                <label for="kategori-2" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Lemari</p>
                </label>
            </div>
        </div>

        <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="false" aria-controls="collapseExample1">
            Varian
        </div>
        <div class="collapse py-2" id="collapseExample1">
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="kategori-3" class="inp-cbx" />
                <label for="kategori-3" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                        </svg></span>
                    <p>Wingge</p>
                </label>
            </div>
            <div class="checkbox-wrapper-46">
                <input type="checkbox" id="kategori-4" class="inp-cbx" />
                <label for="kategori-4" class="cbx"><span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg></span>
                    <p>Mahoni</p>
                </label>
            </div>
        </div>
        <a href="" class="mt-2 btn-lonjong">Terapkan</a>
    </div>
    <div class="flex-grow-1">
        <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Produk Kami</a></li>
                <li class="breadcrumb-item"><a href="/">Meja</a></li>
                <li class="breadcrumb-item">Meja TV</li>
                </li>
            </ol>
        </nav>
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
                    <a href="#"><img src="https://i.ibb.co/86L9vkV/DTV-706-MAHONI-DEPAN.webp" alt="DTV-706-MAHONI-DEPAN" border="0"></a>
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
</div>


<?= $this->endSection(); ?>