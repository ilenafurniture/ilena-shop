<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="ilena-editorial ilena-about" aria-labelledby="about-title">
    <nav class="ilena-editorial__breadcrumb" aria-label="Breadcrumb">
        <a href="<?= esc(ilena_store_path('/'), 'attr'); ?>"><?= lang('Store.home'); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?= lang('Store.breadcrumbAbout'); ?></span>
    </nav>

    <header class="ilena-editorial__hero">
        <div class="ilena-about__hero-copy">
            <p class="ilena-editorial__eyebrow"><?= lang('Store.storyOfIlena'); ?></p>
            <h1 id="about-title"><?= lang('Store.aboutHeroTitle'); ?></h1>
            <p class="ilena-editorial__intro"><?= lang('Store.aboutHeroIntro'); ?></p>
            <div class="ilena-about__vision">
                <svg class="ilena-about__vision-mark" viewBox="0 0 96 56" aria-hidden="true" focusable="false">
                    <path class="vision-mark-stroke" pathLength="1" d="M5 27 23 10l18 17M10 23v25h26V23M19 48V34h9v14" />
                    <path class="vision-mark-stroke vision-mark-connect" pathLength="1" d="M42 31h13" />
                    <circle class="vision-mark-stroke vision-mark-face" cx="73" cy="29" r="17" pathLength="1" />
                    <path class="vision-mark-stroke vision-mark-smile" pathLength="1" d="M65 32q8 9 16 0" />
                    <circle class="vision-mark-dot" cx="49" cy="31" r="2.2" />
                </svg>
                <p lang="id">Mulai dari rumah,<br><em>tercipta senyum ramah.</em></p>
            </div>
            <div class="ilena-about__hero-actions">
                <a class="ilena-about__action-primary" href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= lang('Store.aboutHeroShopCta'); ?></a>
                <a class="ilena-about__action-secondary" href="<?= esc(ilena_store_path('/visimisi'), 'attr'); ?>"><?= lang('Store.aboutVisionValuesCta'); ?></a>
            </div>
        </div>
        <figure class="ilena-about__hero-visual ilena-about__image">
            <img src="https://img.ilenafurniture.com/image/1742450062282.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="<?= esc(lang('Store.aboutHeroImageAlt'), 'attr'); ?>" width="960" height="640" fetchpriority="high" decoding="async">
            <svg class="ilena-about-motion-art" viewBox="0 0 520 320" aria-hidden="true" focusable="false">
                <circle class="about-motion-orbit-line" cx="260" cy="164" r="148" />
                <path class="about-motion-path" pathLength="1" d="M91 190v-48c0-19 15-34 34-34h270c19 0 34 15 34 34v48M70 183h380v53H70zM91 236l-9 28m347-28 9 28" />
                <path class="about-motion-path about-motion-detail" pathLength="1" d="M111 183v-37c0-11 9-20 20-20h112v57m22-57h112c11 0 20 9 20 20v37m-152-57v57m-115 20h304" />
                <path class="about-motion-path about-motion-measure" pathLength="1" d="M91 78h338m-338-8v16m338-16v16" />
                <g class="about-motion-orbit-marker"><circle cx="260" cy="16" r="4" /><circle cx="260" cy="16" r="8" /></g>
            </svg>
            <figcaption class="ilena-about__hero-caption"><?= lang('Store.aboutCaption'); ?></figcaption>
        </figure>
    </header>

    <section class="ilena-about__story" aria-labelledby="about-story-title">
        <div class="ilena-about__copy">
            <p class="ilena-editorial__eyebrow"><?= lang('Store.designedForDailyLife'); ?></p>
            <h2 id="about-story-title"><?= lang('Store.modernLifeTitle'); ?></h2>
            <p><?= lang('Store.aboutHistoryOne'); ?></p>
            <p><?= lang('Store.aboutHistoryTwo'); ?></p>
        </div>
        <div class="ilena-about__timeline" aria-labelledby="about-timeline-title">
            <p class="ilena-editorial__eyebrow"><?= lang('Store.aboutTimelineEyebrow'); ?></p>
            <h2 id="about-timeline-title"><?= lang('Store.aboutTimelineTitle'); ?></h2>
            <ol>
                <li><span class="ilena-about__timeline-value"><?= lang('Store.aboutManufacturingValue'); ?></span><div><h3><?= lang('Store.aboutManufacturingMilestoneTitle'); ?></h3><p><?= lang('Store.aboutManufacturingMilestoneBody'); ?></p></div></li>
                <li><span class="ilena-about__timeline-value"><?= lang('Store.aboutBrandYear'); ?></span><div><h3><?= lang('Store.aboutBrandMilestoneTitle'); ?></h3><p><?= lang('Store.aboutBrandMilestoneBody'); ?></p></div></li>
            </ol>
        </div>
    </section>

    <section class="ilena-about__profile" aria-labelledby="about-profile-title">
        <div>
            <p class="ilena-editorial__eyebrow"><?= lang('Store.manufacturingExperience'); ?></p>
            <h2 id="about-profile-title"><?= lang('Store.experiencedGrowing'); ?></h2>
            <p><?= lang('Store.manufacturingDescription'); ?></p>
            <a class="ilena-editorial__text-link" href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= lang('Store.exploreCollection'); ?> <span aria-hidden="true">→</span></a>
        </div>
        <figure class="ilena-about__profile-image">
            <img src="https://img.ilenafurniture.com/image/1742450090380.webp/?apikey=<?= esc($apikey_img_ilena, 'attr'); ?>" alt="<?= esc(lang('Store.aboutProfileImageAlt'), 'attr'); ?>" width="960" height="640" loading="lazy" decoding="async">
        </figure>
    </section>

    <section class="ilena-about__guides" aria-labelledby="about-guides-title">
        <div class="ilena-editorial__section-heading">
            <p class="ilena-editorial__eyebrow"><?= lang('Store.aboutGuidesEyebrow'); ?></p>
            <h2 id="about-guides-title"><?= lang('Store.aboutGuidesTitle'); ?></h2>
        </div>
        <div class="ilena-about__guide-list">
            <article><h3><?= lang('Store.aboutProductGuideTitle'); ?></h3><p><?= lang('Store.aboutProductGuideBody'); ?></p><a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= lang('Store.exploreCollection'); ?> <span aria-hidden="true">↗</span></a></article>
            <article><h3><?= lang('Store.aboutDeliveryGuideTitle'); ?></h3><p><?= lang('Store.aboutDeliveryGuideBody'); ?></p><a href="<?= esc(ilena_store_path('/faq'), 'attr'); ?>"><?= lang('Store.readFaq'); ?> <span aria-hidden="true">↗</span></a></article>
            <article><h3><?= lang('Store.aboutPartnerGuideTitle'); ?></h3><p><?= lang('Store.aboutPartnerGuideBody'); ?></p><a href="<?= esc(ilena_store_path('/partner'), 'attr'); ?>"><?= lang('Store.aboutPartnerCta'); ?> <span aria-hidden="true">↗</span></a></article>
        </div>
    </section>

    <?php $aboutTestimonials = is_array($aboutTestimonials ?? null) ? $aboutTestimonials : []; if ($aboutTestimonials !== []): ?>
    <section class="ilena-about__reviews" aria-labelledby="about-reviews-title">
        <div class="ilena-editorial__section-heading">
            <p class="ilena-editorial__eyebrow"><?= lang('Store.aboutReviewsEyebrow'); ?></p>
            <h2 id="about-reviews-title"><?= lang('Store.aboutReviewsTitle'); ?></h2>
        </div>
        <div class="ilena-about__review-list">
            <?php foreach ($aboutTestimonials as $testimonial): ?>
            <blockquote>
                <?php if (!empty($testimonial['rating'])): $testimonialRating = min(5, max(1, (int) $testimonial['rating'])); ?><span class="ilena-about__review-rating" role="img" aria-label="<?= esc(sprintf(lang('Store.starsOutOfFive'), $testimonialRating), 'attr'); ?>"><?= str_repeat('★', $testimonialRating); ?></span><?php endif; ?>
                <p><?= esc((string) ($testimonial['body'] ?? '')); ?></p>
                <cite><?= lang('Store.aboutVerifiedBuyer'); ?></cite>
            </blockquote>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="ilena-about__partner-cta" aria-labelledby="about-partner-title">
        <div>
            <p class="ilena-editorial__eyebrow"><?= lang('Store.partnerStore'); ?></p>
            <h2 id="about-partner-title"><?= lang('Store.aboutPartnerTitle'); ?></h2>
            <p><?= lang('Store.aboutPartnerBody'); ?></p>
        </div>
        <a href="<?= esc(ilena_store_path('/partner'), 'attr'); ?>"><?= lang('Store.aboutPartnerCta'); ?> <span aria-hidden="true">↗</span></a>
    </section>

    <section class="ilena-about__contact" aria-labelledby="about-contact-title">
        <div>
            <p class="ilena-editorial__eyebrow"><?= lang('Store.readyToHelp'); ?></p>
            <h2 id="about-contact-title"><?= lang('Store.letsConnect'); ?></h2>
            <p><?= lang('Store.contactAboutBody'); ?></p>
            <div class="ilena-about__contact-actions">
                <a class="ilena-about__contact-primary" href="https://wa.me/628112938158" target="_blank" rel="noopener noreferrer"><?= lang('Store.contactWhatsApp'); ?> <span aria-hidden="true">↗</span></a>
                <a class="ilena-about__contact-secondary" href="mailto:cs@ilenafurniture.com">cs@ilenafurniture.com</a>
            </div>
        </div>
        <address>
            <strong><?= lang('Store.aboutAddressTitle'); ?></strong>
            <span>CV Catur Bhakti Mandiri</span>
            <span>Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa Tengah</span>
            <a href="https://www.google.com/maps/search/?api=1&query=CV+Catur+Bhakti+Mandiri+Semarang" target="_blank" rel="noopener noreferrer"><?= lang('Store.openGoogleMaps'); ?> <span aria-hidden="true">↗</span></a>
        </address>
    </section>
</main>
<script>
(() => {
    const visual = document.querySelector('.ilena-about__image');
    if (!visual || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    visual.classList.add('ilena-about-motion-ready');
    const observer = new IntersectionObserver((entries, activeObserver) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                visual.classList.add('ilena-about-motion-active');
                visual.classList.add('ilena-about-motion-visible');
            } else {
                visual.classList.remove('ilena-about-motion-active');
            }
        });
    }, { threshold: 0.18 });
    observer.observe(visual);
})();
</script>
<?= $this->endSection(); ?>
