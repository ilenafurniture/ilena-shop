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
    <a style="flex:1;" class="item-navhp <?= $title == "Home"? 'active':'' ?>" href="/">
        <i class="material-icons">home</i>
        <p class="m-0">Beranda</p>
    </a>
    <a class="item-navhp <?= $title == "Favorite"? 'active':'' ?>" href="/wishlist" style="flex:1;">
        <i class="material-icons">bookmark_border</i>
        <p class="m-0">Tersimpan</p>
    </a>
    <a class="item-navhp <?= $title == "Keranjang"? 'active':'' ?>" style="flex:1;" href="/cart">
        <i class="material-icons">shopping_cart</i>
        <p class="m-0">Keranjang</p>
    </a>
    <a class="item-navhp <?= $title == "Akun"? 'active':'' ?>" style="flex:1;" href="/account">
        <i class="material-icons">person</i>
        <p class="m-0">Akun</p>
    </a>
</div>