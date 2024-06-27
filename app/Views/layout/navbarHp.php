<div class="navbar-hp-atas hide-ke-show-flex">
    <div style="flex:1;">
        <form action="" class="w-100">
            <input placeholder="Cari produk" style="text-transform: capitalize;" class="input w-100" name="text"
                type="text">
        </form>
    </div>
    <div style="width: fit-content">
        <div class="">
            <input id="n-logo" type="checkbox" class="d-none">
            <label for="n-logo">
                <img src="<?php echo base_url('/img/logo/N.png'); ?>" alt="logo ilena" height="30em">
            </label>
            <div class="expend-hp">
                <p class="mb-2">Kategori</p>
                <a class="w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Kursi</a>
                <a class="w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja</a>
            </div>
        </div>
    </div>
</div>

<div class="navbar-hp hide-ke-show-flex">
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Home' ? 'active' : ''; ?>" href="/">
            <i class="material-icons">home</i>
            <p class="m-0 ">Home</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Favorite' ? 'active' : ''; ?>" href="/wishlist">
            <i class="material-icons">bookmark_border</i>
            <p class="m-0">Tersimpan</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Keranjang' ? 'active' : ''; ?>" href="/cart">
            <i class="material-icons">shopping_cart</i>
            <p class="m-0">Keranjang</p>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Akun' ? 'active' : ''; ?>"
            href="<?= session()->get('isLogin') ? '/account': '/login' ?>">
            <i class="material-icons">person_outline</i>
            <p class="m-0"><?= session()->get('isLogin') ? 'Akun': 'Masuk' ?></p>
        </a>
    </div>

</div>