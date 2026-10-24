<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php $activePartners = is_array($partners ?? null) ? $partners : []; ?>
<main class="ilena-editorial ilena-partners" aria-labelledby="partners-title">
    <nav class="ilena-editorial__breadcrumb" aria-label="Breadcrumb">
        <a href="/">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Mitra kami</span>
    </nav>

    <header class="ilena-editorial__hero ilena-partners__hero">
        <p class="ilena-editorial__eyebrow">Jaringan resmi Ilena</p>
        <h1 id="partners-title">Lebih dekat<br>dengan Ilena.</h1>
        <p class="ilena-editorial__intro">Temukan partner dan toko yang menyediakan produk Ilena. Cari berdasarkan nama, kota, atau alamat.</p>
        <a class="ilena-editorial__text-link" href="#partner-directory">Lihat daftar partner <span aria-hidden="true">↓</span></a>
    </header>

    <section id="partner-directory" class="ilena-partners__directory" aria-labelledby="partner-directory-title">
        <div class="ilena-editorial__section-heading">
            <div>
                <p class="ilena-editorial__eyebrow">Temukan lokasi</p>
                <h2 id="partner-directory-title">Partner Ilena</h2>
            </div>
            <p class="ilena-partners__count"><strong id="partner-count"><?= count($activePartners); ?></strong> lokasi</p>
        </div>

        <label class="ilena-partners__search" for="partner-search">
            <span aria-hidden="true">⌕</span>
            <input id="partner-search" type="search" placeholder="Cari nama, kota, atau alamat" autocomplete="off">
        </label>

        <?php if ($activePartners !== []): ?>
            <ul class="ilena-partners__list" id="partner-list">
                <?php foreach ($activePartners as $partner): ?>
                    <?php
                    $partnerName = trim((string) ($partner['name'] ?? ''));
                    $partnerCity = trim((string) ($partner['city'] ?? ''));
                    $partnerAddress = trim((string) ($partner['address'] ?? ''));
                    $partnerImage = trim((string) ($partner['img'] ?? ''));
                    $partnerHref = trim((string) ($partner['href'] ?? ''));
                    $partnerSearchText = strtolower($partnerName . ' ' . $partnerCity . ' ' . $partnerAddress);
                    ?>
                    <li class="ilena-partners__item" data-search="<?= esc($partnerSearchText, 'attr'); ?>">
                        <?php if ($partnerImage !== ''): ?>
                            <img class="ilena-partners__image" src="<?= esc($partnerImage, 'attr'); ?>" alt="" width="112" height="84" loading="lazy" decoding="async">
                        <?php endif; ?>
                        <div class="ilena-partners__details">
                            <?php if ($partnerCity !== ''): ?><p class="ilena-editorial__eyebrow"><?= esc($partnerCity); ?></p><?php endif; ?>
                            <h3><?= esc($partnerName !== '' ? $partnerName : 'Partner Ilena'); ?></h3>
                            <?php if ($partnerAddress !== ''): ?><address><?= esc($partnerAddress); ?></address><?php endif; ?>
                        </div>
                        <?php if ($partnerHref !== ''): ?>
                            <a class="ilena-partners__map-link" href="<?= esc($partnerHref, 'attr'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Lihat <?= esc($partnerName, 'attr'); ?> di peta">Lihat peta <span aria-hidden="true">↗</span></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="ilena-partners__empty" id="partner-empty" hidden>Tidak ada partner yang cocok. Coba kata kunci lain.</p>
        <?php else: ?>
            <p class="ilena-partners__empty">Informasi partner sedang diperbarui. Silakan hubungi <a href="mailto:cs@ilenafurniture.com">Customer Service Ilena</a> untuk bantuan.</p>
        <?php endif; ?>
    </section>
</main>
<?php if ($activePartners !== []): ?>
<script>
(() => {
    const input = document.getElementById('partner-search');
    const list = document.getElementById('partner-list');
    if (!input || !list) return;
    const items = Array.from(list.querySelectorAll('[data-search]'));
    const count = document.getElementById('partner-count');
    const empty = document.getElementById('partner-empty');
    input.addEventListener('input', () => {
        const query = input.value.trim().toLocaleLowerCase('id');
        let visible = 0;
        items.forEach((item) => {
            const match = item.dataset.search.includes(query);
            item.hidden = !match;
            if (match) visible += 1;
        });
        count.textContent = String(visible);
        empty.hidden = visible > 0;
    });
})();
</script>
<?php endif; ?>
<?= $this->endSection(); ?>
