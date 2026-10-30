<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php $isEnglish = service('request')->getLocale() === 'en'; ?>
<main class="ilena-contact-page" aria-labelledby="ilena-contact-title">
    <nav class="ilena-contact-page__breadcrumb" aria-label="Breadcrumb">
        <a href="<?= esc(ilena_store_path('/'), 'attr'); ?>"><?= $isEnglish ? lang('Store.contactBreadcrumbHome') : 'Beranda'; ?></a><span aria-hidden="true">/</span><span aria-current="page"><?= $isEnglish ? lang('Store.contactPage') : 'Kontak'; ?></span>
    </nav>

    <header class="ilena-contact-page__intro">
        <p class="ilena-contact-page__eyebrow"><?= $isEnglish ? lang('Store.contactService') : 'Layanan Ilena'; ?></p>
        <h1 id="ilena-contact-title"><?= $isEnglish ? lang('Store.contactHeading') : 'Mari terhubung.'; ?></h1>
        <p><?= $isEnglish ? lang('Store.contactIntro') : 'Ada yang ingin ditanyakan tentang produk, pesanan, atau kerja sama? Tim kami siap membantu.'; ?></p>
    </header>

    <section class="ilena-contact-page__body" aria-label="Informasi kontak Ilena">
        <div class="ilena-contact-page__channels">
            <a class="ilena-contact-page__channel" href="https://wa.me/628112938158?text=Halo%20Ilena%2C%20saya%20ingin%20bertanya." target="_blank" rel="noopener noreferrer">
                <span class="ilena-contact-page__channel-label">WhatsApp</span>
                <span class="ilena-contact-page__channel-value">0811-2938-158</span>
                <span class="ilena-contact-page__channel-action"><?= $isEnglish ? lang('Store.startConversation') : 'Mulai percakapan'; ?> <span aria-hidden="true">↗</span></span>
            </a>
            <a class="ilena-contact-page__channel" href="mailto:cs@ilenafurniture.com">
                <span class="ilena-contact-page__channel-label">Email</span>
                <span class="ilena-contact-page__channel-value">cs@ilenafurniture.com</span>
                <span class="ilena-contact-page__channel-action"><?= $isEnglish ? lang('Store.sendEmail') : 'Kirim email'; ?> <span aria-hidden="true">↗</span></span>
            </a>
        </div>

        <aside class="ilena-contact-page__location">
            <p class="ilena-contact-page__eyebrow"><?= $isEnglish ? lang('Store.location') : 'Lokasi'; ?></p>
            <h2>Ilena Furniture</h2>
            <address>Jalan Lingkar Taman Industri, Jatibarang, Mijen,<br>Kota Semarang, Jawa Tengah</address>
            <a href="https://www.google.com/maps/search/?api=1&amp;query=CV+Catur+Bhakti+Mandiri+Semarang" target="_blank" rel="noopener noreferrer"><?= $isEnglish ? lang('Store.viewGoogleMaps') : 'Lihat di Google Maps'; ?> <span aria-hidden="true">↗</span></a>
        </aside>
    </section>

    <footer class="ilena-contact-page__footer">
        <p><?= $isEnglish ? lang('Store.contactFooterQuestion') : 'Butuh informasi seputar produk atau layanan?'; ?></p>
        <a href="<?= esc(ilena_store_path('/faq'), 'attr'); ?>"><?= $isEnglish ? lang('Store.viewFaq') : 'Lihat pertanyaan yang sering diajukan'; ?> <span aria-hidden="true">→</span></a>
    </footer>
</main>
<?= $this->endSection(); ?>
