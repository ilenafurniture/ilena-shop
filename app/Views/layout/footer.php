<?php
$footerPath = App\Services\StorefrontLayoutPolicy::normalizePath((string) service('uri')->getPath());
$footerInternal = App\Services\StorefrontLayoutPolicy::isInternal($footerPath);
$footerMode = App\Services\StorefrontLayoutPolicy::footerMode($footerPath);
$paymentLogos = [
    ['bca.webp', 'BCA', false], ['bni.webp', 'BNI', false], ['bri.webp', 'BRI', false],
    ['mandiri.webp', 'Mandiri', false], ['permatabank.webp', 'PermataBank', false], ['cimb1.png', 'CIMB Niaga', true],
    ['mastercard.webp', 'Mastercard', false], ['visa.webp', 'Visa', false], ['qris.webp', 'QRIS', false],
    ['shopeepay.webp', 'ShopeePay', false], ['gopay.webp', 'GoPay', false],
];
?>
<?php if (!$footerInternal && $footerMode === 'auth'): ?>
<footer class="ilena-context-footer ilena-context-footer--auth" role="contentinfo">
    <a href="<?= esc(ilena_store_path('/'), 'attr'); ?>" aria-label="Ilena Furniture — Beranda"><img src="<?= base_url('img/LogoIlenaWhite.png'); ?>" width="120" height="18" alt="Ilena Furniture"></a>
    <nav aria-label="<?= esc(lang('Store.footerLegal'), 'attr'); ?>"><a href="<?= esc(ilena_store_path('/syarat'), 'attr'); ?>"><?= esc(lang('Store.footerTerms')); ?></a><a href="<?= esc(ilena_store_path('/kebijakan'), 'attr'); ?>"><?= esc(lang('Store.footerPrivacy')); ?></a></nav>
    <small>© <?= date('Y'); ?> Ilena Furniture</small>
</footer>
<?php elseif (!$footerInternal && $footerMode === 'checkout'): ?>
<footer class="ilena-context-footer ilena-context-footer--checkout" role="contentinfo">
    <span><?= esc(lang('Store.footerNeedHelp')); ?> <a href="<?= esc(ilena_store_path('/faq'), 'attr'); ?>"><?= esc(lang('Store.footerVisitHelp')); ?></a></span>
    <span><?= esc(lang('Store.footerSafePayment')); ?> <a href="https://midtrans.com" target="_blank" rel="noopener noreferrer">Midtrans</a></span>
    <small>© <?= date('Y'); ?> Ilena Furniture</small>
</footer>
<?php elseif (!$footerInternal && $footerMode === 'account'): ?>
<footer class="ilena-context-footer ilena-context-footer--account" role="contentinfo">
    <nav aria-label="<?= esc(lang('Store.footerHelp'), 'attr'); ?>"><a href="<?= esc(ilena_store_path('/faq'), 'attr'); ?>"><?= esc(lang('Store.footerHelpCenter')); ?></a><a href="<?= esc(ilena_store_path('/syarat'), 'attr'); ?>"><?= esc(lang('Store.footerTerms')); ?></a><a href="<?= esc(ilena_store_path('/kebijakan'), 'attr'); ?>"><?= esc(lang('Store.footerPrivacy')); ?></a></nav>
    <a href="mailto:cs@ilenafurniture.com"><?= esc(lang('Store.footerContact')); ?></a>
    <small>© <?= date('Y'); ?> Ilena Furniture</small>
</footer>
<?php elseif (!$footerInternal): ?>
<footer class="ilena-store-footer" role="contentinfo">
    <div class="ilena-store-footer__main">
        <section class="ilena-store-footer__brand" aria-label="Ilena Furniture">
            <a href="<?= esc(ilena_store_path('/'), 'attr'); ?>" aria-label="Ilena Furniture — Beranda"><img src="<?= base_url('img/LogoIlenaWhite.png'); ?>" width="140" height="22" alt="Ilena Furniture"></a>
            <div class="ilena-footer-vision" aria-label="Mulai dari rumah, tercipta senyum ramah.">
                <svg class="ilena-footer-vision__mark" viewBox="0 0 96 56" aria-hidden="true" focusable="false">
                    <path class="footer-vision-stroke" pathLength="1" d="M5 27 23 10l18 17M10 23v25h26V23M19 48V34h9v14" />
                    <path class="footer-vision-stroke footer-vision-connect" pathLength="1" d="M42 31h13" />
                    <circle class="footer-vision-stroke footer-vision-face" cx="73" cy="29" r="17" pathLength="1" />
                    <path class="footer-vision-stroke footer-vision-smile" pathLength="1" d="M65 32q8 9 16 0" />
                    <circle class="footer-vision-dot" cx="49" cy="31" r="2.2" />
                </svg>
                <p><?= esc(lang('Store.footerSlogan')); ?></p>
            </div>
        </section>
        <nav class="ilena-store-footer__column" aria-label="Jelajahi Ilena">
            <h2><?= esc(lang('Store.footerExplore')); ?></h2>
            <a href="<?= esc(ilena_store_path('/tentang'), 'attr'); ?>"><?= esc(lang('Store.footerAbout')); ?></a><a href="<?= esc(ilena_store_path('/partner'), 'attr'); ?>"><?= esc(lang('Store.footerPartners')); ?></a>
            <a href="<?= esc(ilena_store_path('/article'), 'attr'); ?>"><?= esc(lang('Store.footerArticles')); ?></a><a href="<?= esc(ilena_store_path('/visimisi'), 'attr'); ?>"><?= esc(lang('Store.footerValues')); ?></a>
        </nav>
        <nav class="ilena-store-footer__column" aria-label="Bantuan dan informasi">
            <h2><?= esc(lang('Store.footerHelp')); ?></h2>
            <a href="<?= esc(ilena_store_path('/faq'), 'attr'); ?>"><?= esc(lang('Store.footerHelpCenter')); ?></a><a href="<?= esc(ilena_store_path('/order'), 'attr'); ?>"><?= esc(lang('Store.footerOrderStatus')); ?></a>
            <a href="<?= esc(ilena_store_path('/syarat'), 'attr'); ?>"><?= esc(lang('Store.footerTerms')); ?></a><a href="<?= esc(ilena_store_path('/kebijakan'), 'attr'); ?>"><?= esc(lang('Store.footerPrivacy')); ?></a>
        </nav>
        <section class="ilena-store-footer__column">
            <h2><?= esc(lang('Store.footerFollow')); ?></h2>
            <a href="https://www.instagram.com/ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">Instagram</a>
            <a href="https://www.tiktok.com/@ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">TikTok</a>
            <a href="https://youtube.com/@ilenafurnitureofficial" target="_blank" rel="noopener noreferrer">YouTube</a>
            <a href="https://www.facebook.com/profile.php?id=61559533190687" target="_blank" rel="noopener noreferrer">Facebook</a>
        </section>
        <section class="ilena-store-footer__column ilena-store-footer__commerce">
            <h2><?= esc(lang('Store.footerShopPayment')); ?></h2>
            <div class="ilena-store-footer__marketplaces">
                <a href="https://tokopedia.link/GNcLHtigJLb" target="_blank" rel="noopener noreferrer" aria-label="Belanja Ilena di Tokopedia">
                    <img src="<?= base_url('img/logo/tokopedia.png'); ?>" alt="Tokopedia" width="181" height="50" loading="lazy" decoding="async">
                </a>
                <a href="https://shopee.co.id/ilenafurnitureofficial" target="_blank" rel="noopener noreferrer" aria-label="Belanja Ilena di Shopee">
                    <img src="<?= base_url('img/logo/shopee.png'); ?>" alt="Shopee" width="156" height="50" loading="lazy" decoding="async">
                </a>
                <a href="https://www.tiktok.com/@ilenafurnitureofficial" target="_blank" rel="noopener noreferrer" aria-label="Belanja Ilena di TikTok Shop">
                    <img src="<?= base_url('img/logo/tiktokshop.svg'); ?>" alt="TikTok Shop" width="156" height="50" loading="lazy" decoding="async">
                </a>
            </div>
            <div class="ilena-store-footer__payments" aria-label="Metode pembayaran">
                <?php foreach ($paymentLogos as [$image, $label, $whiteMark]): ?>
                    <img class="<?= $whiteMark ? 'ilena-store-footer__payment--white-mark' : ''; ?>" src="<?= base_url('img/pembayaran/' . $image); ?>" alt="<?= esc($label, 'attr'); ?>" loading="lazy" decoding="async">
                <?php endforeach; ?>
            </div>
            <span class="ilena-store-footer__midtrans"><?= esc(lang('Store.footerSecurePayment')); ?> <a href="https://midtrans.com" target="_blank" rel="noopener noreferrer">Midtrans</a></span>
        </section>
    </div>
    <div class="ilena-store-footer__sister">
        <span><?= esc(lang('Store.footerPartOf')); ?></span>
        <a class="ilena-store-footer__sister-brand" href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer" aria-label="Kunjungi Lunarea Furniture">
            <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" width="150" height="30" alt="Lunarea Furniture" loading="lazy" decoding="async">
        </a>
    </div>
    <div class="ilena-store-footer__bottom">
        <small>© <?= date('Y'); ?> Ilena Furniture. <?= esc(lang('Store.footerCopyright')); ?></small>
        <nav aria-label="<?= esc(lang('Store.footerLegal'), 'attr'); ?>"><a href="<?= esc(ilena_store_path('/syarat'), 'attr'); ?>"><?= esc(lang('Store.footerTerms')); ?></a><a href="<?= esc(ilena_store_path('/kebijakan'), 'attr'); ?>"><?= esc(lang('Store.footerPrivacy')); ?></a></nav>
    </div>
    <div class="ilena-store-footer__mobile-clear" aria-hidden="true"></div>
</footer>
<?php endif; ?>
<?php if (!$footerInternal && $footerMode === 'full'): ?>
<script>
(() => {
    const vision = document.querySelector('.ilena-footer-vision');
    if (!vision || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    vision.classList.add('ilena-footer-vision--ready');
    const observer = new IntersectionObserver((entries, activeObserver) => {
        if (!entries.some((entry) => entry.isIntersecting)) return;
        vision.classList.add('ilena-footer-vision--visible');
        activeObserver.disconnect();
    }, { threshold: 0.5 });
    observer.observe(vision);
})();
</script>
<?php endif; ?>
