<?php
$headerMode = in_array($headerMode ?? '', ['auth', 'checkout', 'account'], true) ? $headerMode : 'auth';
$headerPath = '/' . trim(strtolower((string) service('uri')->getPath()), '/');
$headerLoggedIn = (bool) session()->get('isLogin');
$headerRole = (string) session()->get('role');
$headerIsStaff = $headerLoggedIn && in_array($headerRole, ['1', '5'], true);
$headerCartItems = session()->get('keranjang');
$headerCartCount = 0;
if (is_array($headerCartItems)) {
    foreach ($headerCartItems as $headerCartItem) {
        $headerCartCount += max(0, (int) ($headerCartItem['jumlah'] ?? 1));
    }
}
?>
<header class="ilena-compact-header ilena-compact-header--<?= esc($headerMode, 'attr'); ?>">
    <div class="ilena-compact-header__brands" aria-label="Ilena dan Lunarea">
        <a class="ilena-compact-header__logo" href="/" aria-label="Ilena Furniture — Beranda">
            <img src="<?= base_url('img/Logo Putih Ilena 1.png'); ?>" width="112" height="23" alt="Ilena Furniture">
        </a>
        <span class="ilena-compact-header__brand-divider" aria-hidden="true"></span>
        <details class="ilena-lunarea-popover ilena-compact-header__lunarea">
            <summary aria-label="Tentang Lunarea Furniture">
                <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" width="120" height="26" alt="Lunarea Furniture">
            </summary>
            <div class="ilena-lunarea-popover__panel">
                <strong>Lunarea Furniture</strong>
                <p>Temukan inspirasi dan koleksi furnitur dari Lunarea.</p>
                <a href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer">Kunjungi Lunarea <span aria-hidden="true">↗</span></a>
            </div>
        </details>
    </div>
    <nav class="ilena-compact-header__actions" aria-label="Navigasi halaman">
        <?php if ($headerMode === 'auth'): ?>
            <a class="ilena-compact-header__icon-link" href="/wishlist" aria-label="Wishlist" title="Wishlist"><i class="material-icons" aria-hidden="true">bookmark_border</i><span>Wishlist</span></a>
            <div class="ilena-header-popover ilena-header-popover--cart">
                <a class="ilena-compact-header__icon-link ilena-header-popover__trigger" href="/cart" aria-label="Keranjang" aria-haspopup="true"><i class="material-icons" aria-hidden="true">shopping_cart</i><span class="ilena-sr-only">Keranjang</span></a>
                <div class="ilena-header-popover__panel" role="status"><strong>Keranjang</strong><p><?= $headerCartCount > 0 ? esc($headerCartCount . ' barang siap dilihat di keranjang Anda.') : 'Keranjang Anda masih kosong.'; ?></p><a href="<?= $headerCartCount > 0 ? '/cart' : '/product'; ?>"><?= $headerCartCount > 0 ? 'Lihat keranjang' : 'Mulai belanja'; ?></a></div>
            </div>
            <a class="ilena-compact-header__icon-link" href="<?= $headerLoggedIn && !$headerIsStaff ? '/account' : '/login'; ?>" aria-label="<?= $headerLoggedIn && !$headerIsStaff ? 'Akun saya' : 'Masuk'; ?>" title="<?= $headerLoggedIn && !$headerIsStaff ? 'Akun saya' : 'Masuk'; ?>"><i class="material-icons" aria-hidden="true">person_outline</i><span><?= $headerLoggedIn && !$headerIsStaff ? 'Akun saya' : 'Masuk'; ?></span></a>
        <?php elseif ($headerMode === 'checkout'): ?>
            <span class="ilena-compact-header__secure">
                <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M5.5 8V5.75a4.5 4.5 0 0 1 9 0V8m-10 0h11v9h-11z"/><path d="M10 11v2"/></svg>
                Belanja aman
            </span>
            <?php if ($headerPath === '/cart'): ?>
                <a class="ilena-compact-header__link" href="/product">Lanjut belanja</a>
            <?php else: ?>
                <a class="ilena-compact-header__link" href="/cart">Kembali ke keranjang</a>
            <?php endif; ?>
            <?php if ($headerLoggedIn && !$headerIsStaff): ?>
                <a class="ilena-compact-header__link" href="/account">Akun saya</a>
            <?php elseif ($headerIsStaff): ?>
                <a class="ilena-compact-header__link" href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>">Workspace</a>
            <?php else: ?>
                <a class="ilena-compact-header__link" href="/login">Masuk</a>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($headerIsStaff): ?>
                <a class="ilena-compact-header__link" href="<?= esc(session()->get('admin_home') ?: '/admin'); ?>">Workspace</a>
                <form class="ilena-compact-header__logout" action="/logout" method="post">
                    <?= csrf_field(); ?>
                    <button class="ilena-compact-header__link" type="submit">Keluar</button>
                </form>
            <?php elseif ($headerLoggedIn): ?>
                <a class="ilena-compact-header__icon-link" href="/wishlist" aria-label="Wishlist" title="Wishlist"><i class="material-icons" aria-hidden="true">bookmark_border</i><span>Wishlist</span></a>
                <div class="ilena-header-popover ilena-header-popover--cart">
                    <a class="ilena-compact-header__icon-link ilena-header-popover__trigger" href="/cart" aria-label="Keranjang" aria-haspopup="true"><i class="material-icons" aria-hidden="true">shopping_cart</i><span class="ilena-sr-only">Keranjang</span></a>
                    <div class="ilena-header-popover__panel" role="status"><strong>Keranjang</strong><p><?= $headerCartCount > 0 ? esc($headerCartCount . ' barang siap dilihat di keranjang Anda.') : 'Keranjang Anda masih kosong.'; ?></p><a href="<?= $headerCartCount > 0 ? '/cart' : '/product'; ?>"><?= $headerCartCount > 0 ? 'Lihat keranjang' : 'Mulai belanja'; ?></a></div>
                </div>
                <details class="ilena-compact-header__account-menu">
                    <summary aria-label="Menu akun"><i class="material-icons" aria-hidden="true">person_outline</i><span>Akun</span></summary>
                    <div class="ilena-compact-header__account-panel">
                        <a href="/account">Profil</a>
                        <a href="/order">Pesanan</a>
                        <a href="/address">Alamat</a>
                        <form action="/logout" method="post"><?= csrf_field(); ?><button type="submit">Keluar</button></form>
                    </div>
                </details>
            <?php else: ?>
                <a class="ilena-compact-header__icon-link" href="/wishlist" aria-label="Wishlist" title="Wishlist"><i class="material-icons" aria-hidden="true">bookmark_border</i><span>Wishlist</span></a>
                <div class="ilena-header-popover ilena-header-popover--cart">
                    <a class="ilena-compact-header__icon-link ilena-header-popover__trigger" href="/cart" aria-label="Keranjang" aria-haspopup="true"><i class="material-icons" aria-hidden="true">shopping_cart</i><span class="ilena-sr-only">Keranjang</span></a>
                    <div class="ilena-header-popover__panel" role="status"><strong>Keranjang</strong><p><?= $headerCartCount > 0 ? esc($headerCartCount . ' barang siap dilihat di keranjang Anda.') : 'Keranjang Anda masih kosong.'; ?></p><a href="<?= $headerCartCount > 0 ? '/cart' : '/product'; ?>"><?= $headerCartCount > 0 ? 'Lihat keranjang' : 'Mulai belanja'; ?></a></div>
                </div>
                <a class="ilena-compact-header__shop" href="/login">Masuk</a>
                <a class="ilena-compact-header__link" href="/product">Belanja</a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
</header>
