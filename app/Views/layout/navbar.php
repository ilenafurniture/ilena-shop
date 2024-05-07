<nav class="nav" style="background-color: black;">
    <div class="container py-3">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <div style="width: calc(100% / 3)">
                <form action="">
                    <input placeholder="Cari produk" style="text-transform: capitalize;" class="input" name="text" type="text">
                </form>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-center">
                <a href="/">
                    <img src="<?php echo base_url('/img/logoilenawhite.png'); ?>" alt="logo ilena" height="30em">
                </a>
            </div>
            <div style="width: calc(100% / 3)" class="d-flex justify-content-end">
                <?php if (session()->get('isLogin')) { ?>
                    <?php if (session()->get('role') == '0') { ?>
                        <a href="/wishlist" class="btn"><i class="material-icons text-light">bookmark_border</i></a>
                        <a href="/cart" class="btn"><i class="material-icons text-light">shopping_cart</i></a>
                        <a href="/account" class="btn"><i class="material-icons text-light">person_outline</i></a>
                    <?php } else { ?>
                        <a href="/admin" class="btn"><i class="material-icons text-light">people</i></a>
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
<div class="d-flex justify-content-center w-100" style="background-color:whitesmoke; position: sticky; top:-1px; z-index: 99;">
    <div class="d-flex align-items-center py-2" style="width:300px">
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Kursi</a>
            <div class="child-list-nav">
                <div class="container py-3 d-flex">
                    <div style="flex: 1;">
                        <p class="m-0">Jelajahi Kursi</p>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Kursi
                            Belajar</a>
                        <a class="text-dark w-100 d-block" style="text-decoration: none; font-weight:500;" href="#">Kursi
                            TV</a>
                    </div>
                    <div style="width: 233px">
                        <a href="#" class="container-produk-nav">
                            <img class="mb-3" src="https://images.unsplash.com/photo-1467043153537-a4fba2cd39ef?q=80&w=1919&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            <h5>New! Modern, Organic Stripes</h5>
                            <p>Kursi Sofa Luar Angkasa High Class KS001</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-nav">
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Rak</a>
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
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Lemari</a>
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
            <a class="text-dark text-center w-100 d-block" style="text-decoration: none;">Meja</a>
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