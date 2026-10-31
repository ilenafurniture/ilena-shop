<?php
$lowerTitle = strtolower($title ?? '');
$currentPath = trim(service('uri')->getPath(), '/');
$can = $canPermission;
$isActivePrefix = static function (array $prefixes) use ($currentPath): bool {
    foreach ($prefixes as $prefix) {
        $prefix = trim($prefix, '/');
        if ($currentPath === $prefix || str_starts_with($currentPath, $prefix . '/')) {
            return true;
        }
    }
    return false;
};

$websiteOrderTodoCount = (int) ($websiteOrderTodoCount ?? 0);

$mobileItems = [
    ['show' => $can('dashboard'), 'label' => 'Home', 'icon' => 'space_dashboard', 'href' => '/admin', 'active' => $currentPath === 'admin' || $currentPath === 'admin/'],
    ['show' => $can('products'), 'label' => 'Produk', 'icon' => 'inventory_2', 'href' => '/admin/product', 'active' => $isActivePrefix(['admin/product']) || ($title ?? '') === 'Produk Kami'],
    ['show' => $can('products'), 'label' => 'Foto Preview', 'icon' => 'collections', 'href' => '/admin/menu-previews', 'active' => $isActivePrefix(['admin/menu-previews'])],
    ['show' => $can('orders_online'), 'label' => 'Pesanan', 'icon' => 'shopping_bag', 'href' => '/admin/order/online', 'active' => $isActivePrefix(['admin/order/online']), 'badge' => $websiteOrderTodoCount],
    ['show' => $can('vouchers'), 'label' => 'Voucher', 'icon' => 'confirmation_number', 'href' => '/admin/voucher', 'active' => $isActivePrefix(['admin/voucher']) || str_contains($lowerTitle, 'voucher')],
    ['show' => $can('content'), 'label' => 'Konten', 'icon' => 'dashboard_customize', 'href' => '/admin/homelayout', 'active' => $isActivePrefix(['admin/homelayout', 'admin/article', 'admin/partners'])],
];
$mobileItems = array_slice(array_values(array_filter($mobileItems, static fn($item) => $item['show'])), 0, 5);
$mobileQuickItems = array_slice($mobileItems, 0, 4);
$mobileMenuGroups = [
    'Utama' => [['show' => $can('dashboard'), 'label' => 'Dashboard', 'icon' => 'space_dashboard', 'href' => '/admin', 'active' => $currentPath === 'admin']],
    'Produk' => [
        ['show' => $can('products'), 'label' => 'Produk', 'icon' => 'inventory_2', 'href' => '/admin/product', 'active' => $isActivePrefix(['admin/product']) || ($title ?? '') === 'Produk Kami'],
        ['show' => $can('products'), 'label' => 'Foto Preview Menu', 'icon' => 'collections', 'href' => '/admin/menu-previews', 'active' => $isActivePrefix(['admin/menu-previews'])],
        ['show' => $can('products'), 'label' => 'Perbaikan File Gambar', 'icon' => 'image_search', 'href' => '/admin/changepic', 'active' => $isActivePrefix(['admin/changepic'])],
        ['show' => $can('vouchers'), 'label' => 'Voucher', 'icon' => 'confirmation_number', 'href' => '/admin/voucher', 'active' => $isActivePrefix(['admin/voucher']) || str_contains($lowerTitle, 'voucher')],
    ],
    'Pesanan' => [
        ['show' => $can('orders_online'), 'label' => 'Pesanan Online', 'icon' => 'shopping_bag', 'href' => '/admin/order/online', 'active' => $isActivePrefix(['admin/order/online']), 'badge' => $websiteOrderTodoCount],
        ['show' => $can('mutasi'), 'label' => 'Mutasi Stok', 'icon' => 'swap_horiz', 'href' => '/admin/mutasi', 'active' => $isActivePrefix(['admin/mutasi', 'admin/mutasiconfirm', 'admin/labelbarang'])],
    ],
    'Konten & toko' => [
        ['show' => $can('content'), 'label' => 'Artikel', 'icon' => 'article', 'href' => '/admin/article', 'active' => $isActivePrefix(['admin/article'])],
        ['show' => $can('content'), 'label' => 'Home Layout', 'icon' => 'dashboard_customize', 'href' => '/admin/homelayout', 'active' => $isActivePrefix(['admin/homelayout'])],
        ['show' => $can('content'), 'label' => 'Partner', 'icon' => 'storefront', 'href' => '/admin/partners', 'active' => $isActivePrefix(['admin/partners'])],
        ['show' => $can('shipping'), 'label' => 'Gratis Ongkir', 'icon' => 'local_shipping', 'href' => '/admin/free-shipping', 'active' => $isActivePrefix(['admin/free-shipping'])],
        ['show' => $can('marketplace'), 'label' => 'Workspace Marketplace', 'icon' => 'storefront', 'href' => '/market/product', 'active' => $isActivePrefix(['market'])],
    ],
    'Tools' => [
        ['show' => $can('analytics'), 'label' => 'Analytics', 'icon' => 'insights', 'href' => '/analytics', 'active' => $isActivePrefix(['analytics'])],
        ['show' => $can('activity_log'), 'label' => 'Log Aktivitas', 'icon' => 'history', 'href' => '/admin/activity-log', 'active' => $isActivePrefix(['admin/activity-log'])],
        ['show' => $can('customers'), 'label' => 'Akun Pelanggan', 'icon' => 'manage_accounts', 'href' => '/admin/customers', 'active' => $isActivePrefix(['admin/customers'])],
        ['show' => $can('meta_capi'), 'label' => 'Meta CAPI', 'icon' => 'track_changes', 'href' => '/admin/meta-capi', 'active' => $isActivePrefix(['admin/meta-capi'])],
        ['show' => $can('spam_cleanup'), 'label' => 'Cleanup Spam Akun', 'icon' => 'person_remove', 'href' => '/admin/spam-cleanup', 'active' => $isActivePrefix(['admin/spam-cleanup'])],
        ['show' => (string) session()->get('role') === '1', 'label' => 'Role & Akses', 'icon' => 'admin_panel_settings', 'href' => '/admin/rbac', 'active' => $isActivePrefix(['admin/rbac'])],
    ],
];
?>

<div class="header-hp admin-mobile-header w-100 hide-ke-show-block">
    <div class="admin-mobile-brand">
        <div class="admin-mobile-mark">IL</div>
        <div>
            <strong class="admin-mobile-brand-title">Admin Ilena</strong>
            <p><?= esc(session()->get('nama') ?: session()->get('email')); ?></p>
        </div>
    </div>
    <form action="/logout" method="post" class="admin-mobile-logout-form">
        <?= csrf_field(); ?>
        <button type="submit" class="admin-mobile-logout" aria-label="Keluar dari akun admin" title="Keluar">
            <i class="material-icons" aria-hidden="true">logout</i>
            <span>Keluar</span>
        </button>
    </form>
</div>

<nav class="navbar-hp admin-mobile-nav hide-ke-show-flex" aria-label="Menu admin mobile">
    <?php foreach ($mobileQuickItems as $item): ?>
    <a class="item-navhp admin-mobile-item position-relative <?= $item['active'] ? 'active' : ''; ?>" href="<?= esc($item['href']); ?>" <?= $item['active'] ? 'aria-current="page"' : ''; ?>>
        <i class="material-icons"><?= esc($item['icon']); ?></i>
        <span><?= esc($item['label']); ?></span>
        <?php if (($item['badge'] ?? 0) > 0): ?>
        <span class="order-alert-badge-hp" title="<?= esc($item['badge']); ?> pesanan website perlu diurus">
            <?= esc($item['badge'] > 99 ? '99+' : $item['badge']); ?>
        </span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
    <button type="button" class="item-navhp admin-mobile-item admin-mobile-more" id="adminMobileMenuOpen" aria-haspopup="dialog" aria-controls="adminMobileMenuDrawer" aria-expanded="false">
        <i class="material-icons" aria-hidden="true">grid_view</i><span>Menu</span>
    </button>
</nav>

<div class="admin-mobile-drawer" id="adminMobileMenuDrawer" hidden>
    <button type="button" class="admin-mobile-drawer-scrim" data-admin-menu-close aria-label="Tutup menu"></button>
    <section class="admin-mobile-drawer-panel" role="dialog" aria-modal="true" aria-labelledby="adminMobileMenuTitle" tabindex="-1">
        <div class="admin-mobile-drawer-head">
            <div><span class="admin-mobile-drawer-kicker">ILENA ADMIN</span><h2 id="adminMobileMenuTitle">Menu navigasi</h2></div>
            <button type="button" class="admin-mobile-drawer-close" data-admin-menu-close aria-label="Tutup menu"><i class="material-icons" aria-hidden="true">close</i></button>
        </div>
        <nav class="admin-mobile-drawer-list" aria-label="Semua menu admin">
            <?php foreach ($mobileMenuGroups as $groupLabel => $items): ?>
                <?php $visibleItems = array_values(array_filter($items, static fn($item) => $item['show'])); ?>
                <?php if ($visibleItems): ?><section class="admin-mobile-drawer-group"><h3><?= esc($groupLabel); ?></h3>
                    <?php foreach ($visibleItems as $item): ?>
                        <a href="<?= esc($item['href']); ?>" class="admin-mobile-drawer-link <?= !empty($item['active']) ? 'active' : ''; ?>" <?= !empty($item['active']) ? 'aria-current="page"' : ''; ?>>
                            <i class="material-icons" aria-hidden="true"><?= esc($item['icon']); ?></i><span><?= esc($item['label']); ?></span>
                            <?php if (($item['badge'] ?? 0) > 0): ?><span class="admin-mobile-drawer-badge"><?= esc($item['badge'] > 99 ? '99+' : $item['badge']); ?></span><?php endif; ?>
                            <i class="material-icons admin-mobile-drawer-arrow" aria-hidden="true">chevron_right</i>
                        </a>
                    <?php endforeach; ?>
                </section><?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </section>
</div>

<script>
(() => {
    const openButton = document.getElementById('adminMobileMenuOpen');
    const drawer = document.getElementById('adminMobileMenuDrawer');
    const panel = drawer?.querySelector('.admin-mobile-drawer-panel');
    if (!openButton || !drawer || !panel) return;
    let previousFocus = null;
    const closeMenu = () => {
        drawer.hidden = true;
        openButton.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('admin-menu-is-open');
        previousFocus?.focus();
    };
    openButton.addEventListener('click', () => {
        previousFocus = document.activeElement;
        drawer.hidden = false;
        openButton.setAttribute('aria-expanded', 'true');
        document.body.classList.add('admin-menu-is-open');
        panel.focus();
    });
    drawer.querySelectorAll('[data-admin-menu-close]').forEach(button => button.addEventListener('click', closeMenu));
    drawer.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        drawer.hidden = true;
        openButton.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('admin-menu-is-open');
    }));
    document.addEventListener('keydown', event => {
        if (drawer.hidden) return;
        if (event.key === 'Escape') closeMenu();
        if (event.key === 'Tab') {
            const focusable = [...panel.querySelectorAll('a, button')];
            if (!focusable.length) return;
            if (event.shiftKey && document.activeElement === focusable[0]) { event.preventDefault(); focusable.at(-1).focus(); }
            else if (!event.shiftKey && document.activeElement === focusable.at(-1)) { event.preventDefault(); focusable[0].focus(); }
        }
    });
})();
</script>

<style>
.admin-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px 10px;
    background: #1c1b1a;
    border-bottom: 1px solid rgba(244,237,226,.12);
}
.admin-mobile-brand {
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-mobile-mark {
    width: 38px;
    height: 38px;
    border-radius: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(145deg,#3c3429,#26231e);
    border: 1px solid rgba(208,90,84,.5);
    color: #e4c99f;
    font-weight: 900;
    letter-spacing: -.04em;
}
.admin-mobile-brand h1 {
    margin: 0;
    font-size: 16px;
    font-weight: 850;
    color: #f5f0e8;
    letter-spacing: -.03em;
}
.admin-mobile-brand p {
    margin: 2px 0 0;
    max-width: 260px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 12px;
    color: #aaa196;
}
.admin-mobile-logout-form { flex: 0 0 auto; margin: 0; }
.admin-mobile-logout {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 44px;
    min-height: 44px;
    padding: 0 10px;
    border: 1px solid rgba(244,237,226,.18);
    border-radius: 10px;
    background: rgba(255,255,255,.055);
    color: #e3d7c5;
    font-size: 12px;
    font-weight: 700;
}
.admin-mobile-logout:hover,
.admin-mobile-logout:focus-visible { border-color: #d05a54; background: rgba(141,46,43,.26); }
.admin-mobile-logout:focus-visible { outline: 2px solid #f09a95; outline-offset: 2px; }
.admin-mobile-logout .material-icons { font-size: 18px; }

.navbar-hp.admin-mobile-nav {
    height: 72px;
    padding: 8px 10px 10px;
    background: #1c1b1a;
    border-top: 1px solid rgba(244,237,226,.13);
    box-shadow: 0 -8px 25px rgba(18,15,12,.16);
    gap: 6px;
    justify-content: space-between;
}
.admin-mobile-nav .admin-mobile-item {
    min-width: 58px;
    min-height: 52px;
    flex: 1;
    border-radius: 16px;
    color: #aaa196;
    gap: 2px;
    transition: background .18s ease, color .18s ease;
}
.admin-mobile-nav .admin-mobile-item .material-icons {
    font-size: 22px;
    padding: 0;
}
.admin-mobile-nav .admin-mobile-item span:not(.order-alert-badge-hp) {
    font-size: 10.5px;
    line-height: 1;
    font-weight: 750;
}
.admin-mobile-nav .admin-mobile-item.active {
    color: #fff;
    background: rgba(141,46,43,.3);
}
.admin-mobile-more { border:0; background:transparent; font:inherit; cursor:pointer; }
.admin-mobile-more:focus-visible { outline:2px solid #f09a95; outline-offset:-2px; }
.order-alert-badge-hp {
    position: absolute;
    top: 2px;
    right: 8px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 999px;
    background: #dc2626;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 900;
    line-height: 1;
    border: 2px solid #1c1b1a;
}
.admin-mobile-drawer[hidden] { display:none!important; }
.admin-mobile-drawer { position:fixed; inset:0; z-index:1300; display:flex; align-items:flex-end; justify-content:center; }
.admin-mobile-drawer-scrim { position:absolute; inset:0; width:100%; height:100%; padding:0; border:0; background:rgba(13,11,9,.62); }
.admin-mobile-drawer-panel { position:relative; z-index:1; width:min(100%,560px); max-height:min(82svh,760px); display:flex; flex-direction:column; padding:20px 18px max(20px,env(safe-area-inset-bottom)); border:1px solid rgba(208,90,84,.25); border-bottom:0; border-radius:16px 16px 0 0; color:#f7f5f3; background:#1c1b1a; box-shadow:0 -20px 60px rgba(0,0,0,.25); outline:none; }
.admin-mobile-drawer-head { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:14px; border-bottom:1px solid rgba(244,237,226,.12); }
.admin-mobile-drawer-kicker { color:#e7837e; font-size:9px; font-weight:800; letter-spacing:.18em; }
.admin-mobile-drawer-head h2 { margin:4px 0 0; color:#f5f0e8; font:500 21px/1.2 Georgia,serif; }
.admin-mobile-drawer-close { width:44px; height:44px; display:grid; place-items:center; border:1px solid rgba(244,237,226,.14); border-radius:11px; color:#eadcc7; background:rgba(255,255,255,.05); }
.admin-mobile-drawer-list { overflow-y:auto; overscroll-behavior:contain; padding-top:14px; }
.admin-mobile-drawer-group { display:grid; gap:4px; margin-bottom:16px; }
.admin-mobile-drawer-group h3 { margin:0 0 5px; padding:0 4px; color:#a89d8e; font-size:10px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
.admin-mobile-drawer-link { min-height:46px; display:flex; align-items:center; gap:12px; padding:7px 10px; border:1px solid transparent; border-radius:9px; color:#d0c6b8; text-decoration:none; font-size:13px; font-weight:600; }
.admin-mobile-drawer-link > .material-icons:first-child { color:#b9a889; font-size:19px; }
.admin-mobile-drawer-link span { flex:1; }
.admin-mobile-drawer-link.active { color:#fff; background:rgba(141,46,43,.3); border-color:rgba(208,90,84,.3); }
.admin-mobile-drawer-arrow { color:#80776b; font-size:18px; }
.admin-mobile-drawer-badge { flex:0 0 auto!important; padding:3px 7px; border-radius:99px; color:#fff; background:#8d2e2b; font-size:10px; }
.admin-mobile-brand-title { display:block; color:#f7f5f3; font:500 17px/1.15 Georgia,serif; }
.admin-mobile-drawer :is(button,a):focus-visible { outline:2px solid #f09a95; outline-offset:2px; }
body.admin-menu-is-open { overflow:hidden; }
@media(prefers-reduced-motion:reduce) { .admin-mobile-nav *, .admin-mobile-drawer * { transition:none!important; } }
</style>
