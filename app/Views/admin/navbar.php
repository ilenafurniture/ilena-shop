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

    $navGroups = [
        'Utama' => [
            ['show' => true, 'label' => 'Dashboard', 'icon' => 'space_dashboard', 'href' => '/admin', 'active' => $currentPath === 'admin' || $currentPath === 'admin/'],
        ],
        'Produk' => [
            ['show' => $can('products'), 'label' => 'Produk', 'icon' => 'inventory_2', 'href' => '/admin/product', 'active' => $isActivePrefix(['admin/product']) || ($title ?? '') === 'Produk Kami'],
            ['show' => $can('vouchers'), 'label' => 'Voucher', 'icon' => 'confirmation_number', 'href' => '/admin/voucher', 'active' => $isActivePrefix(['admin/voucher']) || str_contains($lowerTitle, 'voucher')],
        ],
        'Pesanan' => [
            ['show' => $can('orders_online'), 'label' => 'Pesanan Online', 'icon' => 'shopping_bag', 'href' => '/admin/order/online', 'active' => $isActivePrefix(['admin/order/online']), 'badge' => $websiteOrderTodoCount],
        ],
        'Konten' => [
            ['show' => $can('content'), 'label' => 'Artikel', 'icon' => 'article', 'href' => '/admin/article', 'active' => $isActivePrefix(['admin/article']) || str_contains($lowerTitle, 'artikel')],
            ['show' => $can('content'), 'label' => 'Home Layout', 'icon' => 'dashboard_customize', 'href' => '/admin/homelayout', 'active' => $isActivePrefix(['admin/homelayout']) || ($title ?? '') === 'Home Layout'],
            ['show' => $can('shipping'), 'label' => 'Gratis Ongkir', 'icon' => 'local_shipping', 'href' => '/admin/free-shipping', 'active' => $isActivePrefix(['admin/free-shipping']) || ($title ?? '') === 'Gratis Ongkir'],
        ],
        'Tools' => [
            ['show' => $can('analytics'), 'label' => 'Analytics', 'icon' => 'insights', 'href' => '/analytics', 'active' => $isActivePrefix(['analytics']) || ($title ?? '') === 'Insights Analytics'],
            ['show' => $can('activity_log'), 'label' => 'Log Aktivitas', 'icon' => 'history', 'href' => '/admin/activity-log', 'active' => $isActivePrefix(['admin/activity-log']) || ($title ?? '') === 'Log Aktivitas Admin'],
            ['show' => $can('meta_capi'), 'label' => 'Meta CAPI', 'icon' => 'track_changes', 'href' => '/admin/meta-capi', 'active' => $isActivePrefix(['admin/meta-capi']) || ($title ?? '') === 'Meta CAPI'],
            ['show' => $can('rbac'), 'label' => 'Role & Akses', 'icon' => 'admin_panel_settings', 'href' => '/admin/rbac', 'active' => $isActivePrefix(['admin/rbac']) || ($title ?? '') === 'Role & Akses Admin'],
        ],
    ];
?>

<aside class="admin-nav admin-nav-clean show-block-ke-hide" aria-label="Menu admin Ilena">
    <div class="admin-brand-card">
        <div class="admin-brand-mark">IL</div>
        <div class="admin-brand-text">
            <h1>Admin Ilena</h1>
            <p><?= esc(session()->get('email')); ?></p>
        </div>
    </div>

    <nav class="admin-menu-list">
        <?php foreach ($navGroups as $groupLabel => $items): ?>
            <?php $visibleItems = array_values(array_filter($items, static fn($item) => $item['show'])); ?>
            <?php if (count($visibleItems) > 0): ?>
                <section class="admin-menu-section">
                    <div class="admin-menu-title"><?= esc($groupLabel); ?></div>
                    <?php foreach ($visibleItems as $item): ?>
                        <a class="item-nav admin-menu-item <?= $item['active'] ? 'active' : ''; ?>" href="<?= esc($item['href']); ?>" <?= $item['active'] ? 'aria-current="page"' : ''; ?>>
                            <span class="admin-menu-icon"><i class="material-icons"><?= esc($item['icon']); ?></i></span>
                            <span class="admin-menu-label"><?= esc($item['label']); ?></span>
                            <?php if (($item['badge'] ?? 0) > 0): ?>
                                <span class="order-alert-badge" title="<?= esc($item['badge']); ?> pesanan website perlu diurus">
                                    <?= esc($item['badge'] > 99 ? '99+' : $item['badge']); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <div class="admin-nav-footer">
        <a class="item-nav admin-menu-item admin-logout" href="/logout">
            <span class="admin-menu-icon"><i class="material-icons">logout</i></span>
            <span class="admin-menu-label">Keluar</span>
        </a>
    </div>
</aside>

<style>
.admin-nav-clean {
    width: 292px;
    min-width: 292px;
    height: 100%;
    background: #ffffff;
    border-right: 1px solid #eef2f7;
    color: #0f172a;
    overflow-y: auto;
    padding: 18px 14px;
    box-shadow: 8px 0 28px rgba(15, 23, 42, .04);
}

.admin-brand-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #eef2f7;
    background: linear-gradient(180deg, #fff, #fafafa);
    border-radius: 18px;
    margin-bottom: 18px;
}

.admin-brand-mark {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #111827;
    color: #fff;
    font-weight: 800;
    letter-spacing: -.04em;
    flex-shrink: 0;
}

.admin-brand-text { min-width: 0; }
.admin-brand-text h1 {
    margin: 0;
    font-size: 16px;
    line-height: 1.2;
    font-weight: 800;
    letter-spacing: -.03em;
    color: #111827;
}
.admin-brand-text p {
    margin: 3px 0 0;
    color: #64748b;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 190px;
}

.admin-menu-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding-bottom: 14px;
}
.admin-menu-section { display: grid; gap: 5px; }
.admin-menu-title {
    padding: 0 10px 4px;
    font-size: 11px;
    line-height: 1;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: #94a3b8;
    font-weight: 800;
}

.admin-nav-clean .item-nav.admin-menu-item,
.admin-nav-clean .item-nav.admin-menu-item:not(:has(label)) {
    min-height: 44px;
    padding: 9px 10px;
    border-radius: 14px;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: -.01em;
    border: 1px solid transparent;
    transition: background .18s ease, color .18s ease, border-color .18s ease, transform .18s ease;
}
.admin-nav-clean .item-nav.admin-menu-item:hover {
    background: #f8fafc;
    border-color: #eef2f7;
    color: #111827;
    transform: translateX(2px);
}
.admin-nav-clean .item-nav.admin-menu-item.active {
    background: #fff1f2;
    border-color: #fecdd3;
    color: #be123c;
    box-shadow: inset 3px 0 0 #e11d48;
}
.admin-menu-icon {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #64748b;
    flex-shrink: 0;
}
.admin-menu-icon .material-icons { font-size: 19px; }
.admin-menu-item.active .admin-menu-icon {
    background: #ffe4e6;
    color: #be123c;
}
.admin-menu-label { flex: 1; min-width: 0; }

.admin-nav-footer {
    position: sticky;
    bottom: 0;
    padding-top: 12px;
    background: linear-gradient(180deg, rgba(255,255,255,0), #fff 28%);
}
.admin-nav-clean .admin-logout {
    color: #ef4444 !important;
    background: #fff;
    border-color: #fee2e2 !important;
}
.admin-nav-clean .admin-logout .admin-menu-icon {
    background: #fef2f2;
    color: #ef4444;
}

.order-alert-badge {
    min-width: 22px;
    height: 22px;
    padding: 0 7px;
    border-radius: 999px;
    background: #dc2626;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 900;
    line-height: 1;
    box-shadow: 0 8px 18px rgba(220, 38, 38, .25);
}
</style>
