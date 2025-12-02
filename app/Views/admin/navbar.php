<?php
    // Biar gampang dipakai di banyak tempat
    $lowerTitle = strtolower($title ?? '');

    // Section "Pesanan" dianggap aktif kalau:
    // - title persis "Pesanan" (online/offline lama)
    // - atau ada teks "project interior" di title
    $isPesananSection =
        $lowerTitle === 'pesanan' ||
        str_contains($lowerTitle, 'project interior');
?>

<div class="admin-nav show-block-ke-hide">
    <div style="padding: 2em;">
        <h1 class="teks-sedang mb-4">Admin Ilena</h1>
        <div class="mt-2 d-flex justify-content-between py-1">
            <p class="m-0">Email</p>
            <p class="fw-bold m-0"><?= session()->get("email"); ?></p>
        </div>
        <!-- <div class="d-flex justify-content-between py-1">
            <p class="m-0">Sandi</p>
            <a href="" class="btn-teks-aja">Ganti Sandi</a>
        </div> -->
        <span class="garis my-2"></span>
    </div>

    <div>
        <!-- ===== Section Produk ===== -->
        <div class="nav-separator">
            <span class="line"></span><span class="label">Produk</span><span class="line"></span>
        </div>
        <a class="item-nav <?= $title == 'Produk Kami' ? 'active' : ''; ?>" href="/admin/product">
            <i class="material-icons">people</i>
            <p class="m-0">Produk</p>
        </a>
        <a class="item-nav <?= str_contains($lowerTitle, 'voucher') ? 'active' : ''; ?>" href="/admin/voucher">
            <i class="material-icons">confirmation_number</i>
            <p class="m-0">Voucher</p>
        </a>

        <!-- ===== Section Pesanan ===== -->
        <div class="nav-separator">
            <span class="line"></span><span class="label">Pesanan</span><span class="line"></span>
        </div>

        <!-- Parent toggle -->
        <div class="item-nav <?= $isPesananSection ? 'active' : ''; ?>">
            <label for="navbar-admin-pesanan">
                <i class="material-icons">shopping_cart</i>
                <p class="m-0" style="flex: 1">Pesanan</p>
                <i class="material-icons arrow">arrow_right</i>
            </label>
        </div>

        <!-- Checkbox untuk expand/collapse; "checked" kalau sedang di halaman pesanan / project interior -->
        <input type="checkbox" id="navbar-admin-pesanan" <?= $isPesananSection ? 'checked' : ''; ?>>

        <div class="item-nav-expand">
            <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/admin/order/online">
                <i class="material-icons">language</i>
                <p class="m-0">Online</p>
            </a>
            <a class="item-nav <?= $title == 'Pesanan' ? 'active' : ''; ?>" href="/admin/order/offline/sale">
                <i class="material-icons">store</i>
                <p class="m-0">Offline</p>
            </a>
            <a class="item-nav <?= str_contains($lowerTitle, 'project interior') ? 'active' : ''; ?>"
                href="/admin/project-interior">
                <i class="material-icons">home</i>
                <p class="m-0">Interior</p>
            </a>
        </div>

        <!-- ===== Section Konten ===== -->
        <div class="nav-separator">
            <span class="line"></span><span class="label">Konten</span><span class="line"></span>
        </div>
        <a class="item-nav <?= str_contains($lowerTitle, 'artikel') ? 'active' : ''; ?>" href="/admin/article">
            <i class="material-icons">book</i>
            <p class="m-0">Artikel</p>
        </a>

        <a class="item-nav <?= $title == 'Home Layout' ? 'active' : ''; ?>" href="/admin/homelayout">
            <i class="material-icons">brush</i>
            <p class="m-0">Home Layout</p>
        </a>

        <!-- ===== Section Analytics ===== -->
        <div class="nav-separator">
            <span class="line"></span><span class="label">Analytics &amp; Tools</span><span class="line"></span>
        </div>
        <a class="item-nav <?= $title == 'Insights Analytics' ? 'active' : ''; ?>" href="/analytics">
            <i class="material-icons">insights</i>
            <p class="m-0">Analytics</p>
        </a>

        <!-- ===== Section Sistem ===== -->
        <div class="nav-separator">
            <span class="line"></span><span class="label">Sistem</span><span class="line"></span>
        </div>
        <a class="item-nav" href="/logout">
            <i class="material-icons">exit_to_app</i>
            <p class="m-0">Keluar</p>
        </a>
    </div>
</div>

<style>
.nav-separator {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 2em 6px 2em;
    color: #6b7280;
    user-select: none;
}

.nav-separator .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 700;
}

.nav-separator .line {
    height: 1px;
    background: #e5e7eb;
    flex: 1;
}

.nav-separator+.item-nav {
    margin-top: 4px;
}
</style>