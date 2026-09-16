<?php
$lowerTitle = strtolower($title ?? '');
$currentPath = trim(service('uri')->getPath(), '/');
$rbacService = new \App\Services\AdminRbacService();
$can = static fn(string $permission): bool => $rbacService->hasPermission(session()->get('email'), $permission);
$isActivePrefix = static function (array $prefixes) use ($currentPath): bool {
    foreach ($prefixes as $prefix) {
        $prefix = trim($prefix, '/');
        if ($currentPath === $prefix || str_starts_with($currentPath, $prefix . '/')) {
            return true;
        }
    }
    return false;
};

$websiteOrderTodoCount = 0;
if ($can('orders_online')) {
    $testEmails = ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','adityaanugrah494@gmail.com','tipaun0605@gmail.com','uuua5021@gmail.com'];
    $websiteOrderTodoCount = (new \App\Models\PemesananModel())
        ->whereIn('status', ['Proses', 'Menunggu Pembayaran'])
        ->like('id_midtrans', 'IL', 'after')
        ->whereNotIn('email', $testEmails)
        ->countAllResults();
}

$mobileItems = [
    ['show' => $can('products'), 'label' => 'Produk', 'icon' => 'inventory_2', 'href' => '/admin/product', 'active' => $isActivePrefix(['admin/product']) || ($title ?? '') === 'Produk Kami'],
    ['show' => $can('orders_online'), 'label' => 'Pesanan', 'icon' => 'shopping_bag', 'href' => '/admin/order/online', 'active' => $isActivePrefix(['admin/order/online']), 'badge' => $websiteOrderTodoCount],
    ['show' => $can('vouchers'), 'label' => 'Voucher', 'icon' => 'confirmation_number', 'href' => '/admin/voucher', 'active' => $isActivePrefix(['admin/voucher']) || str_contains($lowerTitle, 'voucher')],
    ['show' => $can('content'), 'label' => 'Konten', 'icon' => 'dashboard_customize', 'href' => '/admin/homelayout', 'active' => $isActivePrefix(['admin/homelayout', 'admin/article'])],
    ['show' => $can('rbac'), 'label' => 'Akses', 'icon' => 'admin_panel_settings', 'href' => '/admin/rbac', 'active' => $isActivePrefix(['admin/rbac']) || ($title ?? '') === 'Role & Akses Admin'],
    ['show' => true, 'label' => 'Keluar', 'icon' => 'logout', 'href' => '/logout', 'active' => false],
];
$mobileItems = array_slice(array_values(array_filter($mobileItems, static fn($item) => $item['show'])), 0, 5);
?>

<div class="header-hp admin-mobile-header w-100 hide-ke-show-block">
    <div class="admin-mobile-brand">
        <div class="admin-mobile-mark">IL</div>
        <div>
            <h1>Admin Ilena</h1>
            <p><?= esc(session()->get('nama') ?: session()->get('email')); ?></p>
        </div>
    </div>
</div>

<nav class="navbar-hp admin-mobile-nav hide-ke-show-flex" aria-label="Menu admin mobile">
    <?php foreach ($mobileItems as $item): ?>
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
</nav>

<style>
.admin-mobile-header {
    padding: 14px 16px 10px;
    background: #fff;
    border-bottom: 1px solid #eef2f7;
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
    background: #111827;
    color: #fff;
    font-weight: 900;
    letter-spacing: -.04em;
}
.admin-mobile-brand h1 {
    margin: 0;
    font-size: 16px;
    font-weight: 850;
    color: #111827;
    letter-spacing: -.03em;
}
.admin-mobile-brand p {
    margin: 2px 0 0;
    max-width: 260px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 12px;
    color: #64748b;
}

.navbar-hp.admin-mobile-nav {
    height: 72px;
    padding: 8px 10px 10px;
    background: rgba(255, 255, 255, .96);
    backdrop-filter: blur(14px);
    border-top: 1px solid #eef2f7;
    box-shadow: 0 -10px 28px rgba(15, 23, 42, .08);
    gap: 6px;
    justify-content: space-between;
}
.admin-mobile-nav .admin-mobile-item {
    min-width: 58px;
    min-height: 52px;
    flex: 1;
    border-radius: 16px;
    color: #64748b;
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
    color: #be123c;
    background: #fff1f2;
}
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
    border: 2px solid #fff;
}
</style>
