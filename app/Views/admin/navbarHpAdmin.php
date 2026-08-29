<?php
$rbacService = new \App\Services\AdminRbacService();
$can = static fn(string $permission): bool => $rbacService->hasPermission(session()->get('email'), $permission);
?>

<div class="header-hp w-100 hide-ke-show-block">
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

<div class="navbar-hp hide-ke-show-flex">
    <?php if ($can('products')): ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Produk Kami' ? 'active' : ''; ?>" href="/admin/product">
            <i class="material-icons">people</i>
            <!-- <p class="m-0">Produk</p> -->
        </a>
    </div>
    <?php endif; ?>
    <?php if ($can('orders_online')): ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/admin/order">
            <i class="material-icons">shopping_cart</i>
            <!-- <p class="m-0">Pesanan</p> -->
        </a>
    </div>
    <?php endif; ?>
    <?php if ($can('orders_online')): ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Pengajuan Print Ulang' ? 'active' : ''; ?>" href="/admin/reprint">
            <i class="material-icons">assignment</i>
            <!-- <p class="m-0">Pengajuan Print</p> -->
        </a>
    </div>
    <?php endif; ?>
    <?php if ($can('orders_online')): ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Konfirmasi Marketplace' ? 'active' : ''; ?>" href="/admin/marketplace">
            <i class="material-icons">assignment_turned_in</i>
            <!-- <p class="m-0">Konfirmasi Marketplace</p> -->
        </a>
    </div>
    <?php endif; ?>
    <?php if ($can('rbac')): ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Role & Akses Admin' ? 'active' : ''; ?>" href="/admin/rbac">
            <i class="material-icons">admin_panel_settings</i>
        </a>
    </div>
    <?php endif; ?>
    <?php if ($can('meta_capi')): ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $title == 'Meta CAPI' ? 'active' : ''; ?>" href="/admin/meta-capi">
            <i class="material-icons">track_changes</i>
            <!-- <p class="m-0">Meta CAPI</p> -->
        </a>
    </div>
    <?php endif; ?>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp" href="/logout">
            <i class="material-icons">exit_to_app</i>
            <!-- <p class="m-0">Keluar</p> -->
        </a>
    </div>
</div>
