<div class="header-hp w-100 hide-ke-show-block">
    <h1 class="teks-sedang text-center pt-1">Gudang Ilena</h1>
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


<div class="menu-hp d-flex align-content-center justify-content-center">
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/gudang/listorder">
            <i class="material-icons">people</i>
            <p class="m-0 ">Pesanan</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-nav <?= $title == 'Pesanan Selesai' ? 'active' : ''; ?>" href="/gudang/listorderafter">
            <i class="material-icons">local_shipping</i>
            <p class="m-0">Pesanan Selesai</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-nav <?= $title == 'Mutasi' ? 'active' : ''; ?>" href="/gudang/mutasi">
            <i class="material-icons">library_books</i>
            <p class="m-0">Mutasi</p>
        </a>
    </div>

    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-nav" href="/logout">
            <i class="material-icons">exit_to_app</i>
            <p class="m-0 teks-menu">Keluar</p>
        </a>
    </div>
</div>