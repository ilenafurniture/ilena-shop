<?php
$headerMode = in_array($headerMode ?? '', ['auth', 'checkout', 'account'], true) ? $headerMode : 'auth';
$headerPath = '/' . trim(strtolower((string) service('uri')->getPath()), '/');
?>
<header class="ilena-compact-header ilena-compact-header--<?= esc($headerMode, 'attr'); ?>">
    <a class="ilena-compact-header__logo" href="/" aria-label="Ilena Furniture — Beranda">
        <img src="<?= base_url('img/Logo Putih Ilena 1.png'); ?>" width="150" height="30" alt="Ilena Furniture">
    </a>
    <details class="ilena-lunarea-popover ilena-compact-header__lunarea">
        <summary aria-label="Tentang Lunarea Furniture">
            <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" width="150" height="30" alt="Lunarea Furniture">
        </summary>
        <div class="ilena-lunarea-popover__panel">
            <strong>Lunarea Furniture</strong>
            <p>Temukan inspirasi dan koleksi furnitur dari Lunarea.</p>
            <a href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer">Kunjungi Lunarea <span aria-hidden="true">↗</span></a>
        </div>
    </details>
    <nav class="ilena-compact-header__actions" aria-label="Navigasi halaman">
        <?php if ($headerMode === 'auth'): ?>
            <a class="ilena-compact-header__link" href="/product">Kembali ke toko</a>
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
            <?php if (session()->get('isLogin')): ?>
                <a class="ilena-compact-header__link" href="/account">Akun saya</a>
            <?php else: ?>
                <a class="ilena-compact-header__link" href="/login">Masuk</a>
            <?php endif; ?>
        <?php else: ?>
            <span class="ilena-compact-header__welcome">Halo, <?= esc(session()->get('nama') ?: 'Pelanggan'); ?></span>
            <a class="ilena-compact-header__link" href="/order">Pesanan</a>
            <a class="ilena-compact-header__link" href="/account">Akun</a>
            <a class="ilena-compact-header__shop" href="/product">Lanjut belanja</a>
            <?php if (session()->get('isLogin')): ?>
                <form class="ilena-compact-header__logout" action="/logout" method="post">
                    <?= csrf_field(); ?>
                    <button class="ilena-compact-header__link" type="submit">Keluar</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
</header>
