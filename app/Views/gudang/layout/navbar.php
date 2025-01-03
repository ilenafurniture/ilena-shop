<div class="admin-nav show-block-ke-hide">
    <div style="padding: 2em;">
        <h1 class="teks-sedang mb-4">Gudang Ilena</h1>
        <div class="mt-2 d-flex justify-content-between py-1">
            <p class="m-0">
                Nama
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
        <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/gudang/listorder">
            <i class="material-icons">people</i>
            <p class="m-0">Pesanan</p>
        </a>
        <a class="item-nav <?= $title == 'Pesanan Selesai' ? 'active' : ''; ?>" href="/gudang/listorderafter">
            <i class="material-icons">local_shipping</i>
            <p class="m-0">Pesanan Selesai</p>
        </a>
        <a class="item-nav <?= $title == 'Mutasi' ? 'active' : ''; ?>" href="/gudang/mutasi">
            <i class="material-icons">library_books</i>
            <p class="m-0">Mutasi</p>
        </a>
        <a class="item-nav " href="/logout">
            <i class="material-icons">exit_to_app</i>
            <p class="m-0">Keluar</p>
        </a>
    </div>
</div>