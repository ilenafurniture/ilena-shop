<div class="header-hp w-100 hide-ke-show-block">
    <h1 class="teks-sedang text-center pt-1">Market Ilena</h1>
    <div class="mt-2 d-flex justify-content-between">
        <p class="m-0">
            Email
        </p>
        <p class="fw-bold m-0">
            <?= session()->get("email"); ?>
        </p>
    </div>
    <div class="d-flex mb-2">
        <div style="flex:4;">
            <p class="m-0">
                Sandi
            </p>
        </div>
        <div style="flex:1;">
            <a href="" class="btn-teks-aja">Ganti Sandi</a>
        </div>
    </div>
</div>




<div class="navbar-hp hide-ke-show-flex">
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Semua Produk' ? 'active' : ''; ?>" href="/market/product">
            <i class="material-icons">people</i>
            <p class="m-0">Pesanan</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Keranjang Produk' ? 'active' : ''; ?>" href="/market/cart">
            <i class="material-icons">shopping_cart</i>
            <p class="m-0">Keranjang</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp" href="/logout">
            <i class="material-icons">exit_to_app</i>
            <p class="m-0 teks-menu">Keluar</p>
        </a>
    </div>

</div>