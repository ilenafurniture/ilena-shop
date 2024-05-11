<div class="admin-nav">
    <div style="padding: 2em;">
        <h1 class="teks-sedang mb-4">Admin Ilena</h1>
        <div class="mt-2 d-flex justify-content-between py-1">
            <p class="m-0">
                Email
            </p>
            <p class="fw-bold m-0">
                adminilena@gmail.com
            </p>
        </div>
        <div class="d-flex justify-content-between py-1">
            <p class="m-0">
                Sandi
            </p>
            <a href="" class="btn-teks-aja">Ganti Sandi</a>
        </div>
        <span class="garis my-2"></span>
    </div>
    <div>
        <a class="item-nav <?= $title == 'Produk Kami' ? 'active' : ''; ?>" href="/listproduct">
            <i class="material-icons">people</i>
            <p class="m-0">Produk</p>
        </a>
        <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/orderadmin">
            <i class="material-icons">shopping_cart</i>
            <p class="m-0">Pesanan</p>
        </a>
        <a class="item-nav" href="/logout">
            <i class="material-icons">exit_to_app</i>
            <p class="m-0">Keluar</p>
        </a>
    </div>
</div>