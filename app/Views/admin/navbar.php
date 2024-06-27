<div class="admin-nav show-block-ke-hide">
    <div style="padding: 2em;">
        <h1 class="teks-sedang mb-4">Admin Ilena</h1>
        <div class="mt-2 d-flex justify-content-between py-1">
            <p class="m-0">
                Email
            </p>
            <p class="fw-bold m-0">
                <?= session()->get("nama"); ?>
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
        <a class="item-nav <?= $title == 'Produk Kami' ? 'active' : ''; ?>" href="/admin/product">
            <i class="material-icons">people</i>
            <p class="m-0">Produk</p>
        </a>
        <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/admin/order">
            <i class="material-icons">shopping_cart</i>
            <p class="m-0">Pesanan</p>
        </a>
        <a class="item-nav <?= $title == 'Pengajuan Print Ulang' ? 'active' : ''; ?>" href="/admin/reprint">
            <i class="material-icons">assignment</i>
            <p class="m-0">Pengajuan Print</p>
        </a>
        <a class="item-nav <?= $title == 'Konfirmasi Marketplace' ? 'active' : ''; ?>" href="/admin/marketplace">
            <i class="material-icons">assignment_turned_in</i>
            <p class="m-0">Konfirmasi Marketplace</p>
        </a>
        <a class="item-nav" href="/logout">
            <i class="material-icons">exit_to_app</i>
            <p class="m-0">Keluar</p>
        </a>
    </div>
</div>



<div class="hide-ke-show-block">
    <div class="header-hp w-100">
        <h1 class="teks-sedang text-center pt-1">Admin Ilena</h1>
        <div class="mt-2">
            <p class="m-0">
                Nama
            </p>
            <p class="fw-bold m-0">
                <?= session()->get("nama"); ?>
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


    <div class="menu-hp w-100 d-flex align-content-center justify-content-center">
        <div style="flex:1;" class="d-flex justify-content-center align-content-center">
            <a class="item-nav <?= $title == 'Produk Kami' ? 'active' : ''; ?>" href="/admin/product">
                <i class="material-icons">people</i>
                <p class="m-0">Produk</p>
            </a>
        </div>
        <div style="flex:1;" class="d-flex justify-content-center align-content-center">
            <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/admin/order">
                <i class="material-icons">shopping_cart</i>
                <p class="m-0">Pesanan</p>
            </a>
        </div>
        <div style="flex:1;" class="d-flex justify-content-center align-content-center">
            <a class="item-nav <?= $title == 'Pengajuan Print Ulang' ? 'active' : ''; ?>" href="/admin/reprint">
                <i class="material-icons">assignment</i>
                <p class="m-0">Pengajuan Print</p>
            </a>
        </div>
        <div style="flex:1;" class="d-flex justify-content-center align-content-center">
            <a class="item-nav <?= $title == 'Konfirmasi Marketplace' ? 'active' : ''; ?>" href="/admin/marketplace">
                <i class="material-icons">assignment_turned_in</i>
                <p class="m-0">Konfirmasi Marketplace</p>
            </a>
        </div>
        <div style="flex:1;" class="d-flex justify-content-center align-content-center">
            <a class="item-nav" href="/logout">
                <i class="material-icons">exit_to_app</i>
                <p class="m-0">Keluar</p>
            </a>
        </div>
    </div>