<?php
$footerPath = '/' . strtolower(trim((string) service('uri')->getPath(), '/'));
$footerInternal = preg_match('~^/(admin|analytics|market|editarticle|changepic|gantilokasi|cobainput|gamwm)(/|$)~', $footerPath) === 1;
$footerAuth = in_array($footerPath, ['/login', '/register', '/verify'], true);
$footerCheckout = preg_match('~^/(cart|shipping|payment|progresspay|successpay|cencelpay|expirepay)(/|$)~', $footerPath) === 1;
$footerAccount = preg_match('~^/(account|address|order|orderdetail|invoice|wishlist)(/|$)~', $footerPath) === 1;
$paymentLogos = [
    ['bca.png', 'BCA'], ['bni.png', 'BNI'], ['bri.png', 'BRI'],
    ['mandiri.png', 'Mandiri'], ['permata.png', 'PermataBank'], ['cimb1.png', 'CIMB Niaga'],
    ['mastercard.png', 'Mastercard'], ['visa.png', 'Visa'], ['qris.png', 'QRIS'],
    ['shopeepay.png', 'ShopeePay'], ['gopay.png', 'GoPay'],
];
?>
<?php if (!$footerInternal && $footerAuth): ?>
<footer class="ilena-context-footer ilena-context-footer--auth" role="contentinfo">
    <a href="/" aria-label="Ilena Furniture — Beranda"><img src="<?= base_url('img/LogoIlenaWhite.png'); ?>" width="120" height="18" alt="Ilena Furniture"></a>
    <nav aria-label="Informasi legal"><a href="/syarat">Syarat &amp; ketentuan</a><a href="/kebijakan">Kebijakan privasi</a></nav>
    <small>© <?= date('Y'); ?> Ilena Furniture</small>
</footer>
<?php elseif (!$footerInternal && $footerCheckout): ?>
<footer class="ilena-context-footer ilena-context-footer--checkout" role="contentinfo">
    <span>Butuh bantuan? <a href="/faq">Kunjungi pusat bantuan</a></span>
    <span>Pembayaran aman melalui <a href="https://midtrans.com" target="_blank" rel="noopener noreferrer">Midtrans</a></span>
    <small>© <?= date('Y'); ?> Ilena Furniture</small>
</footer>
<?php elseif (!$footerInternal && $footerAccount): ?>
<footer class="ilena-context-footer ilena-context-footer--account" role="contentinfo">
    <nav aria-label="Tautan akun"><a href="/account">Profil</a><a href="/order">Pesanan</a><a href="/wishlist">Wishlist</a></nav>
    <a href="/faq">Butuh bantuan?</a>
    <small>© <?= date('Y'); ?> Ilena Furniture</small>
</footer>
<?php elseif (!$footerInternal): ?>
<footer class="ilena-store-footer" role="contentinfo">
    <div class="ilena-store-footer__main">
        <section class="ilena-store-footer__brand" aria-label="Ilena Furniture">
            <a href="/" aria-label="Ilena Furniture — Beranda"><img src="<?= base_url('img/LogoIlenaWhite.png'); ?>" width="140" height="22" alt="Ilena Furniture"></a>
            <p>Furniture untuk ruang yang terasa lebih personal.</p>
        </section>
        <nav class="ilena-store-footer__column" aria-label="Jelajahi Ilena">
            <h2>Jelajahi</h2>
            <a href="/tentang">Tentang kami</a><a href="/partner">Mitra dan lokasi</a>
            <a href="/article">Artikel</a><a href="/visimisi">Visi, misi &amp; nilai</a>
        </nav>
        <nav class="ilena-store-footer__column" aria-label="Bantuan dan informasi">
            <h2>Bantuan</h2>
            <a href="/faq">Pusat bantuan</a><a href="/order">Status pesanan</a>
            <a href="/syarat">Syarat &amp; ketentuan</a><a href="/kebijakan">Kebijakan privasi</a>
        </nav>
        <section class="ilena-store-footer__column">
            <h2>Ikuti kami</h2>
            <a href="https://www.instagram.com/ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">Instagram</a>
            <a href="https://www.tiktok.com/@ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">TikTok</a>
            <a href="https://youtube.com/@ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">YouTube</a>
            <a href="https://www.facebook.com/profile.php?id=61559533190687" target="_blank" rel="noopener noreferrer">Facebook</a>
        </section>
        <section class="ilena-store-footer__column ilena-store-footer__commerce">
            <h2>Belanja &amp; pembayaran</h2>
            <div class="ilena-store-footer__marketplaces">
                <a href="https://tokopedia.link/GNcLHtigJLb" target="_blank" rel="noopener noreferrer">Tokopedia</a>
                <a href="https://shopee.co.id/ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">Shopee</a>
                <a href="https://www.tiktok.com/@ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">TikTok Shop</a>
            </div>
            <div class="ilena-store-footer__payments" aria-label="Metode pembayaran">
                <?php foreach ($paymentLogos as [$image, $label]): ?>
                    <img src="<?= base_url('img/pembayaran/' . $image); ?>" alt="<?= esc($label, 'attr'); ?>" loading="lazy" decoding="async">
                <?php endforeach; ?>
            </div>
            <span class="ilena-store-footer__midtrans">Pembayaran diproses aman oleh <a href="https://midtrans.com" target="_blank" rel="noopener noreferrer">Midtrans</a></span>
        </section>
    </div>
    <div class="ilena-store-footer__sister">
        <span>Bagian dari</span>
        <details class="ilena-lunarea-popover">
            <summary class="ilena-store-footer__sister-brand" aria-label="Tentang Lunarea Furniture">
                <img src="<?= base_url('img/logo/N.png'); ?>" width="32" height="32" alt="">
                <span>Lunarea Furniture</span>
            </summary>
            <div class="ilena-lunarea-popover__panel">
                <strong>Lunarea Furniture</strong>
                <p>Temukan inspirasi dan koleksi furnitur dari Lunarea.</p>
                <a href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer">Kunjungi Lunarea <span aria-hidden="true">↗</span></a>
            </div>
        </details>
    </div>
    <div class="ilena-store-footer__bottom">
        <small>© <?= date('Y'); ?> Ilena Furniture. Hak cipta dilindungi.</small>
        <nav aria-label="Informasi legal"><a href="/syarat">Syarat &amp; ketentuan</a><a href="/kebijakan">Kebijakan privasi</a></nav>
    </div>
    <div class="ilena-store-footer__mobile-clear" aria-hidden="true"></div>
</footer>
<?php endif; ?>
