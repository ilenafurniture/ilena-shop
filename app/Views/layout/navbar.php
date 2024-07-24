<nav class="nav show-block-ke-hide" style="background-color: black;">
    <div class="container py-3">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <div style="width: calc(100% / 3)">
                <form action="/actionfind" method="post">
                    <input placeholder="Cari produk" style="text-transform: capitalize;" class="input" name="cari" type="text">
                </form>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-center">
                <a href="/">
                    <img src="<?php echo base_url('/img/logoilenawhite.png'); ?>" alt="logo ilena" height="30em">
                </a>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-end">
                <?php if (session()->get('isLogin')) { ?>
                    <?php if (session()->get('role') == '0' || session()->get('role') == '4') { ?>
                        <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                        <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                        <a href="/account" class="btn"><i class="material-icons text-light">person_outline</i></a>
                    <?php } else if (session()->get('role') == '1') { ?>
                        <a href="/admin/product" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light">Admin</p>
                        </a>
                        <a href="/logout" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></a>
                    <?php } else if (session()->get('role') == '2') { ?>
                        <a href="/gudang/listorder" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light">Gudang</p>
                        </a>
                        <a href="/logout" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></a>
                    <?php } else if (session()->get('role') == '3') { ?>
                        <a href="/market/product" class="btn d-flex align-items-center">
                            <i class="material-icons text-light">chevron_left</i>
                            <p class="m-0 text-light">Marketplace</p>
                        </a>
                        <a href="/logout" class="btn" style="padding-right: 0"><i class="material-icons text-light">exit_to_app</i></a>
                    <?php } ?>
                <?php } else { ?>
                    <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                    <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                    <a href="/login" class="btn"><i class="material-icons text-light">person_outline</i></a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>
<div class="justify-content-center w-100 show-flex-ke-hide" style="background-color:whitesmoke; position: sticky; top:-1px; z-index: 99;">
    <div class="d-flex align-items-center py-2 gap-5">
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Sorely</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1;">
                        <p class="m-0">Jelajahi Sorely</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Dresser Tall Cabinet</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Dresser 3 Drawer</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Bookshelf</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Media TV</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Side Table</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Stool</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Coffee Table</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Console Table</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Wardrobe</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Queen Bed</a>
                    </div>
                    <div style="width: 233px">
                        <!-- <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1467043153537-a4fba2cd39ef?q=80&w=1919&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Kursi Sofa Luar Angkasa High Class KS001</p>
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Cabana</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1">
                        <p class="m-0">Jelajahi Rak</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Rak
                            Belajar</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Rak
                            TV</a>
                    </div>
                    <div style="width: 233px">
                        <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1551232864-3f0890e580d9?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Rak Belajar Luar Angkasa High Class RS001</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Orca</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1">
                        <p class="m-0">Jelajahi Lemari</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Lemari
                            Belajar</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Lemari
                            TV</a>
                    </div>
                    <div style="width: 233px">
                        <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1595515106864-077d30192c56?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Lemari Sofa Luar Angkasa High Class LS001</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Water Case</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1">
                        <p class="m-0">Jelajahi Meja</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja
                            Belajar</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja
                            TV</a>
                    </div>
                    <div style="width: 233px">
                        <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Meja Sofa Luar Angkasa High Class KS001</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Plint Base</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1">
                        <p class="m-0">Jelajahi Meja</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja
                            Belajar</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja
                            TV</a>
                    </div>
                    <div style="width: 233px">
                        <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Meja Sofa Luar Angkasa High Class KS001</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Cut Out</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1">
                        <p class="m-0">Jelajahi Meja</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja
                            Belajar</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Meja
                            TV</a>
                    </div>
                    <div style="width: 233px">
                        <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Meja Sofa Luar Angkasa High Class KS001</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-list-nav"></div>
    </div>
</div>