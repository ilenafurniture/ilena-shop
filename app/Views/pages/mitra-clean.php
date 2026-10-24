<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php
$activePartners = is_array($partners ?? null) ? $partners : [];
$partnerQuery = (string) ($partnerQuery ?? '');
$partnerTotal = (int) ($partnerTotal ?? count($activePartners));
$partnerPage = (int) ($partnerPage ?? 1);
$partnerPages = (int) ($partnerPages ?? 1);
$partnerPerPage = (int) ($partnerPerPage ?? 10);
$partnerFirst = $partnerTotal === 0 ? 0 : (($partnerPage - 1) * $partnerPerPage) + 1;
$partnerLast = min($partnerPage * $partnerPerPage, $partnerTotal);
?>
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
            <p class="ilena-partners__count">Menampilkan <strong><?= $partnerFirst; ?>–<?= $partnerLast; ?></strong> dari <strong><?= $partnerTotal; ?></strong> partner</p>
        </div>

        <form class="ilena-partners__search" action="/partner" method="get" role="search">
            <label class="sr-only" for="partner-search">Cari partner berdasarkan nama, kota, atau alamat</label>
            <span aria-hidden="true">⌕</span>
            <input id="partner-search" name="q" type="search" value="<?= esc($partnerQuery, 'attr'); ?>" placeholder="Cari nama, kota, atau alamat" autocomplete="off">
            <button type="submit">Cari</button>
        </form>

        <?php if ($activePartners !== []): ?>
            <ul class="ilena-partners__list" id="partner-list">
                <?php foreach ($activePartners as $partner): ?>
                    <?php
                    $partnerName = trim((string) ($partner['name'] ?? ''));
                    $partnerCity = trim((string) ($partner['city'] ?? ''));
                    $partnerAddress = trim((string) ($partner['address'] ?? ''));
                    $partnerImage = trim((string) ($partner['img'] ?? ''));
                    $partnerHref = trim((string) ($partner['href'] ?? ''));
                    ?>
                    <li class="ilena-partners__item">
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
            <?php if ($partnerPages > 1): ?>
                <nav class="ilena-partners__pagination" aria-label="Halaman daftar partner">
                    <?php if ($partnerPage > 1): ?><a href="<?= site_url('partner') . '?' . http_build_query(array_filter(['q' => $partnerQuery, 'page' => $partnerPage - 1], static fn ($value) => $value !== '')); ?>" rel="prev">← Sebelumnya</a><?php endif; ?>
                    <span>Halaman <?= $partnerPage; ?> dari <?= $partnerPages; ?></span>
                    <?php if ($partnerPage < $partnerPages): ?><a href="<?= site_url('partner') . '?' . http_build_query(array_filter(['q' => $partnerQuery, 'page' => $partnerPage + 1], static fn ($value) => $value !== '')); ?>" rel="next">Berikutnya →</a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <p class="ilena-partners__empty"><?= $partnerQuery !== '' ? 'Tidak ada partner yang cocok dengan pencarian tersebut.' : 'Informasi partner sedang diperbarui.'; ?> Silakan hubungi <a href="mailto:cs@ilenafurniture.com">Customer Service Ilena</a> untuk bantuan.</p>
        <?php endif; ?>
    </section>
</main>
<?= $this->endSection(); ?>
