<?php
$headerMode = in_array($headerMode ?? '', ['auth', 'checkout', 'account'], true) ? $headerMode : 'auth';
$headerPath = '/' . trim(strtolower((string) service('uri')->getPath()), '/');
$headerLoggedIn = (bool) session()->get('isLogin');
$headerRole = (string) session()->get('role');
$headerIsStaff = $headerLoggedIn && in_array($headerRole, ['1', '5'], true);
?>
<header class="ilena-compact-header ilena-compact-header--<?= esc($headerMode, 'attr'); ?>">
    <div class="ilena-compact-header__brands" aria-label="Ilena dan Lunarea">
        <a class="ilena-compact-header__logo" href="/" aria-label="Ilena Furniture — Beranda">
            <img src="<?= base_url('img/Logo Putih Ilena 1.png'); ?>" width="112" height="23" alt="Ilena Furniture">
        </a>
        <span class="ilena-compact-header__brand-divider" aria-hidden="true"></span>
        <a class="ilena-compact-header__lunarea" href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer" aria-label="Kunjungi Lunarea Furniture">
            <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" width="120" height="26" alt="Lunarea Furniture">
        </a>
    </div>
    <nav class="ilena-compact-header__actions" aria-label="Navigasi halaman">
        <?php if ($headerMode === 'checkout'): ?>
            <span class="ilena-compact-header__secure"><svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M5.5 8V5.75a4.5 4.5 0 0 1 9 0V8m-10 0h11v9h-11z"/><path d="M10 11v2"/></svg>Belanja aman</span>
            <a class="ilena-compact-header__link" href="<?= $headerPath === '/cart' ? '/product' : '/cart'; ?>"><?= $headerPath === '/cart' ? 'Lanjut belanja' : 'Kembali ke keranjang'; ?></a>
            <?php if ($headerLoggedIn && !$headerIsStaff): ?><a class="ilena-compact-header__link" href="/account">Akun saya</a><?php elseif ($headerIsStaff): ?><a class="ilena-compact-header__link" href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>">Workspace</a><?php else: ?><a class="ilena-compact-header__link" href="/login">Masuk</a><?php endif; ?>
        <?php elseif ($headerIsStaff): ?>
            <a class="ilena-compact-header__link" href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>">Workspace</a>
            <form class="ilena-compact-header__logout" action="/logout" method="post"><?= csrf_field(); ?><button class="ilena-compact-header__link" type="submit">Keluar</button></form>
        <?php else: ?>
            <a class="ilena-compact-header__icon-link" href="/wishlist" aria-label="Wishlist"><i class="material-icons" aria-hidden="true">favorite_border</i><span class="ilena-sr-only">Wishlist</span></a>
            <a class="ilena-compact-header__icon-link" href="/cart" aria-label="Keranjang"><i class="material-icons" aria-hidden="true">shopping_bag</i><span class="ilena-sr-only">Keranjang</span></a>
            <a class="ilena-compact-header__icon-link" href="<?= $headerLoggedIn ? '/account' : '/login'; ?>" aria-label="<?= $headerLoggedIn ? 'Akun saya' : 'Masuk'; ?>"><i class="material-icons" aria-hidden="true">person_outline</i><span class="ilena-sr-only"><?= $headerLoggedIn ? 'Akun saya' : 'Masuk'; ?></span></a>
            <?php if (!$headerLoggedIn): ?><a class="ilena-compact-header__link" href="/register">Daftar</a><?php endif; ?>
        <?php endif; ?>
    </nav>
</header>
