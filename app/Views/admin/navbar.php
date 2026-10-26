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
            ['show' => $can('products'), 'label' => 'Foto Preview Menu', 'icon' => 'collections', 'href' => '/admin/menu-previews', 'active' => $isActivePrefix(['admin/menu-previews'])],
            ['show' => $can('vouchers'), 'label' => 'Voucher', 'icon' => 'confirmation_number', 'href' => '/admin/voucher', 'active' => $isActivePrefix(['admin/voucher']) || str_contains($lowerTitle, 'voucher')],
        ],
        'Pesanan' => [
            ['show' => $can('orders_online'), 'label' => 'Pesanan Online', 'icon' => 'shopping_bag', 'href' => '/admin/order/online', 'active' => $isActivePrefix(['admin/order/online']), 'badge' => $websiteOrderTodoCount],
        ],
        'Konten' => [
            ['show' => $can('content'), 'label' => 'Artikel', 'icon' => 'article', 'href' => '/admin/article', 'active' => $isActivePrefix(['admin/article']) || str_contains($lowerTitle, 'artikel')],
            ['show' => $can('content'), 'label' => 'Home Layout', 'icon' => 'dashboard_customize', 'href' => '/admin/homelayout', 'active' => $isActivePrefix(['admin/homelayout']) || ($title ?? '') === 'Home Layout'],
            ['show' => $can('content'), 'label' => 'Partner', 'icon' => 'storefront', 'href' => '/admin/partners', 'active' => $isActivePrefix(['admin/partners']) || ($title ?? '') === 'Partner Ilena'],
            ['show' => $can('shipping'), 'label' => 'Gratis Ongkir', 'icon' => 'local_shipping', 'href' => '/admin/free-shipping', 'active' => $isActivePrefix(['admin/free-shipping']) || ($title ?? '') === 'Gratis Ongkir'],
        ],
        'Tools' => [
            ['show' => $can('analytics'), 'label' => 'Analytics', 'icon' => 'insights', 'href' => '/analytics', 'active' => $isActivePrefix(['analytics']) || ($title ?? '') === 'Insights Analytics'],
            ['show' => $can('activity_log'), 'label' => 'Log Aktivitas', 'icon' => 'history', 'href' => '/admin/activity-log', 'active' => $isActivePrefix(['admin/activity-log']) || ($title ?? '') === 'Log Aktivitas Admin'],
            ['show' => $can('customers'), 'label' => 'Akun Pelanggan', 'icon' => 'manage_accounts', 'href' => '/admin/customers', 'active' => $isActivePrefix(['admin/customers']) || ($title ?? '') === 'Akun Pelanggan'],
            ['show' => $can('meta_capi'), 'label' => 'Meta CAPI', 'icon' => 'track_changes', 'href' => '/admin/meta-capi', 'active' => $isActivePrefix(['admin/meta-capi']) || ($title ?? '') === 'Meta CAPI'],
            ['show' => $can('rbac'), 'label' => 'Role & Akses', 'icon' => 'admin_panel_settings', 'href' => '/admin/rbac', 'active' => $isActivePrefix(['admin/rbac']) || ($title ?? '') === 'Role & Akses Admin'],
        ],
    ];
?>

<aside class="admin-nav admin-nav-clean show-block-ke-hide" aria-label="Menu admin Ilena">
    <div class="admin-brand-card">
        <div class="admin-brand-mark" aria-hidden="true">I</div>
        <div class="admin-brand-text">
            <span class="admin-brand-kicker">ILENA FURNITURE</span>
            <h1>Administration</h1>
        </div>
    </div>
    <div class="admin-user-chip">
        <span class="admin-user-avatar" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr((string) (session()->get('nama') ?: session()->get('email')), 0, 1, 'UTF-8'), 'UTF-8')); ?></span>
        <span class="admin-user-details"><strong><?= esc(session()->get('nama') ?: 'Admin'); ?></strong><small><?= esc(session()->get('email')); ?></small></span>
        <span class="admin-user-status" title="Sesi aktif" aria-label="Sesi aktif"></span>
    </div>

    <nav class="admin-menu-list">
        <?php foreach ($navGroups as $groupLabel => $items): ?>
            <?php $visibleItems = array_values(array_filter($items, static fn($item) => $item['show'])); ?>
            <?php if (count($visibleItems) > 0): ?>
                <section class="admin-menu-section">
                    <div class="admin-menu-title"><span><?= esc($groupLabel); ?></span><span class="admin-menu-rule" aria-hidden="true"></span></div>
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
        <form action="/logout" method="post" class="m-0">
            <?= csrf_field(); ?>
            <button type="submit" class="item-nav admin-menu-item admin-logout border-0 w-100 text-start">
                <span class="admin-menu-icon"><i class="material-icons">logout</i></span>
                <span class="admin-menu-label">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<style>
.admin-nav-clean {
    --nav-bg: #211f1b;
    --nav-panel: #292620;
    --nav-border: rgba(244, 237, 226, .11);
    --nav-text: #f4efe7;
    --nav-muted: #b7aea2;
    --nav-accent: #c9a779;
    width: 264px; min-width: 264px; height: 100%; padding: 20px 14px 14px;
    display: flex; flex-direction: column; overflow-y: auto; color: var(--nav-text);
    background: var(--nav-bg); border-right: 1px solid rgba(0,0,0,.15);
    scrollbar-width: thin; scrollbar-color: #61594d transparent;
}
.admin-brand-card { display:flex; align-items:center; gap:11px; padding:4px 7px 18px; border:0; border-bottom:1px solid var(--nav-border); border-radius:0; margin:0 0 13px; background:none; }
.admin-brand-mark { width:39px; height:39px; display:grid; place-items:center; flex:0 0 auto; border:1px solid rgba(201,167,121,.6); border-radius:11px; background:linear-gradient(145deg,#3c3429,#26231e); color:#e4c99f; font-family:Georgia,serif; font-size:25px; line-height:1; }
.admin-brand-text { min-width:0; }
.admin-brand-kicker { display:block; margin-bottom:4px; color:#c9a779; font-size:9px; font-weight:800; letter-spacing:.19em; }
.admin-brand-text h1 { margin:0; color:#f5f0e8; font-family:Georgia,serif; font-size:17px; line-height:1.15; font-weight:500; letter-spacing:.01em; }
.admin-user-chip { display:flex; align-items:center; gap:10px; padding:10px 9px; margin-bottom:17px; border:1px solid var(--nav-border); border-radius:10px; background:rgba(255,255,255,.035); }
.admin-user-avatar { width:32px; height:32px; display:grid; place-items:center; flex:0 0 auto; border-radius:50%; background:#493f31; color:#e8d4b4; font-size:13px; font-weight:800; }
.admin-user-details { min-width:0; flex:1; display:grid; gap:2px; }
.admin-user-details strong { overflow:hidden; color:#f3eee7; font-size:12px; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
.admin-user-details small { overflow:hidden; color:#aaa196; font-size:10px; text-overflow:ellipsis; white-space:nowrap; }
.admin-user-status { width:7px; height:7px; border-radius:50%; background:#8eb596; box-shadow:0 0 0 3px rgba(142,181,150,.12); }
.admin-menu-list { display:flex; flex:1; flex-direction:column; gap:17px; padding-bottom:16px; }
.admin-menu-section { display:grid; gap:4px; }
.admin-menu-title { display:flex; align-items:center; gap:9px; padding:0 9px 5px; color:#968e82; font-size:9px; line-height:1; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
.admin-menu-rule { height:1px; flex:1; background:var(--nav-border); }
.admin-nav-clean .item-nav.admin-menu-item { position:relative; display:flex; min-height:43px; align-items:center; gap:10px; padding:6px 9px; border:1px solid transparent; border-radius:8px; color:var(--nav-muted); font-size:12px; font-weight:600; letter-spacing:.005em; text-decoration:none; transition:color .16s ease, background .16s ease, border-color .16s ease; }
.admin-nav-clean .item-nav.admin-menu-item:hover { color:#fffaf1; background:rgba(255,255,255,.055); border-color:rgba(255,255,255,.06); }
.admin-nav-clean .item-nav.admin-menu-item.active { color:#f0d9b7; background:rgba(201,167,121,.13); border-color:rgba(201,167,121,.2); box-shadow:inset 2px 0 #c9a779; }
.admin-menu-icon { width:27px; height:27px; display:grid; place-items:center; flex:0 0 auto; border-radius:7px; color:#b9afa2; background:rgba(255,255,255,.045); }
.admin-menu-icon .material-icons { font-size:17px; }
.admin-menu-item.active .admin-menu-icon { color:#e0c59d; background:rgba(201,167,121,.12); }
.admin-menu-label { min-width:0; flex:1; }
.admin-nav-footer { position:sticky; bottom:0; padding-top:10px; border-top:1px solid var(--nav-border); background:var(--nav-bg); }
.admin-nav-clean .admin-logout { min-height:42px!important; color:#d5aaa1!important; background:transparent!important; border:1px solid transparent!important; }
.admin-nav-clean .admin-logout:hover { color:#f5c4b8!important; background:rgba(193,105,86,.12)!important; }
.admin-nav-clean .admin-logout .admin-menu-icon { color:#d5aaa1; background:rgba(193,105,86,.1); }
.order-alert-badge { min-width:21px; height:21px; display:inline-flex; align-items:center; justify-content:center; padding:0 6px; border:1px solid rgba(255,255,255,.12); border-radius:999px; color:#fff4e9; background:#a75442; font-size:10px; font-weight:800; line-height:1; }
.admin-nav-clean :is(a,button):focus-visible { outline:2px solid #e0c59d; outline-offset:2px; }
@media(max-width:992px) { .admin-nav-clean { display:none; } }
@media(prefers-reduced-motion:reduce) { .admin-nav-clean * { transition:none!important; } }
</style>
