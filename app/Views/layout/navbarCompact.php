<?php
$headerMode = in_array($headerMode ?? '', ['auth', 'checkout', 'account'], true) ? $headerMode : 'auth';
$headerPathValue = trim((string) service('uri')->getPath(), '/');
$headerPathValue = (string) preg_replace('#^(?:en|id)(?:/|$)#i', '', $headerPathValue, 1);
$headerPath = '/' . trim(strtolower($headerPathValue), '/');
$headerLoggedIn = (bool) session()->get('isLogin');
$headerRole = (string) session()->get('role');
$headerIsStaff = $headerLoggedIn && in_array($headerRole, ['1', '5'], true);
?>
<header class="ilena-compact-header ilena-compact-header--<?= esc($headerMode, 'attr'); ?>">
    <div class="ilena-compact-header__brands" aria-label="Ilena Furniture">
        <a class="ilena-compact-header__logo" href="<?= esc(ilena_store_path('/'), 'attr'); ?>" aria-label="Ilena Furniture — <?= esc(lang('Store.home'), 'attr'); ?>">
            <img src="<?= base_url('img/Logo Putih Ilena 1.png'); ?>" width="112" height="23" alt="Ilena Furniture">
        </a>
        <span class="ilena-compact-header__brand-divider" aria-hidden="true"></span>
        <a class="ilena-compact-header__lunarea" href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(lang('Store.visitLunarea'), 'attr'); ?>">
            <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" width="120" height="26" alt="Lunarea Furniture">
            <span class="ilena-brand-popover" aria-hidden="true"><strong>Lunarea Furniture</strong><small><?= esc(lang('Store.lunareaPopover')); ?></small></span>
        </a>
        <a class="ilena-compact-header__interior" href="https://ilenafurniture.net/" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(lang('Store.customInterior'), 'attr'); ?>">
            <img src="<?= base_url('img/logo/logoilenainterior-v2.png'); ?>" width="92" height="24" alt="Ilena Interior">
            <span class="ilena-brand-popover" aria-hidden="true"><strong>Ilena Interior</strong><small><?= esc(lang('Store.interiorPopover')); ?></small></span>
        </a>
    </div>
    <nav class="ilena-compact-header__actions" aria-label="Navigasi halaman">
        <?php if ($headerMode === 'checkout'): ?>
            <span class="ilena-compact-header__secure"><svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M5.5 8V5.75a4.5 4.5 0 0 1 9 0V8m-10 0h11v9h-11z"/><path d="M10 11v2"/></svg><?= esc(lang('Store.secureShopping')); ?></span>
            <a class="ilena-compact-header__link" href="<?= esc(ilena_store_path($headerPath === '/cart' ? '/product' : '/cart'), 'attr'); ?>"><?= esc($headerPath === '/cart' ? lang('Store.continueShopping') : lang('Store.backToCart')); ?></a>
            <?php if ($headerLoggedIn && !$headerIsStaff): ?><a class="ilena-compact-header__link" href="<?= esc(ilena_store_path('/account'), 'attr'); ?>"><?= esc(lang('Store.myAccount')); ?></a><?php elseif ($headerIsStaff): ?><a class="ilena-compact-header__link" href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>"><?= esc(lang('Store.workspace')); ?></a><?php else: ?><a class="ilena-compact-header__link" href="<?= esc(ilena_store_path('/login'), 'attr'); ?>"><?= esc(lang('Store.signIn')); ?></a><?php endif; ?>
        <?php elseif ($headerIsStaff): ?>
            <a class="ilena-compact-header__link" href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>"><?= esc(lang('Store.workspace')); ?></a>
            <form class="ilena-compact-header__logout" action="<?= esc(ilena_store_path('/logout'), 'attr'); ?>" method="post"><?= csrf_field(); ?><button class="ilena-compact-header__link" type="submit"><?= esc(lang('Store.logout')); ?></button></form>
        <?php else: ?>
            <a class="ilena-compact-header__icon-link" href="<?= esc(ilena_store_path('/wishlist'), 'attr'); ?>" aria-label="<?= esc(lang('Store.favorites'), 'attr'); ?>"><i class="material-icons" aria-hidden="true">favorite_border</i><span class="ilena-sr-only"><?= esc(lang('Store.favorites')); ?></span></a>
            <a class="ilena-compact-header__icon-link" href="<?= esc(ilena_store_path('/cart'), 'attr'); ?>" aria-label="<?= esc(lang('Store.cartTitle'), 'attr'); ?>"><i class="material-icons" aria-hidden="true">shopping_bag</i><span class="ilena-sr-only"><?= esc(lang('Store.cartTitle')); ?></span></a>
            <a class="ilena-compact-header__icon-link" href="<?= esc(ilena_store_path($headerLoggedIn ? '/account' : '/login'), 'attr'); ?>" aria-label="<?= esc($headerLoggedIn ? lang('Store.myAccount') : lang('Store.signIn'), 'attr'); ?>"><i class="material-icons" aria-hidden="true">person_outline</i><span class="ilena-sr-only"><?= esc($headerLoggedIn ? lang('Store.myAccount') : lang('Store.signIn')); ?></span></a>
            <?php if (!$headerLoggedIn): ?><a class="ilena-compact-header__link" href="<?= esc(ilena_store_path('/register'), 'attr'); ?>"><?= esc(lang('Store.registerNow')); ?></a><?php endif; ?>
        <?php endif; ?>
    </nav>
</header>
