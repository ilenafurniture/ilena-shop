<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<main class="ilena-vision-page">
    <div class="ilena-vision-shell">
        <nav class="ilena-vision-breadcrumb" aria-label="breadcrumb">
            <a href="<?= esc(ilena_store_path('/'), 'attr'); ?>"><?= lang('Store.home'); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?= lang('Store.visionValues'); ?></span>
        </nav>

        <section class="ilena-vision-hero" aria-labelledby="ilena-vision-title">
            <div class="ilena-vision-hero-copy">
                <p class="ilena-vision-eyebrow" data-reveal>ILENA FURNITURE <span></span> <?= lang('Store.ourValues'); ?></p>
                <h1 id="ilena-vision-title" data-reveal>Mulai dari <em>rumah,</em><br>tercipta senyum ramah.</h1>
            </div>
                <figure class="ilena-vision-motion-art" data-reveal aria-label="<?= esc(lang('Store.visionMotionTitle'), 'attr'); ?>">
                <svg viewBox="0 0 520 440" role="img" aria-labelledby="ilena-motion-title" focusable="false">
                    <title id="ilena-motion-title"><?= lang('Store.visionMotionTitle'); ?></title>
                    <circle class="vision-art-halo" cx="260" cy="220" r="174" />
                    <circle class="vision-art-orbit" cx="260" cy="220" r="145" />
                    <path class="vision-art-house" d="M92 215 260 76l168 139M124 204v153h272V204M202 357v-92c0-30 26-55 58-55s58 25 58 55v92" />
                    <path class="vision-art-room" d="M151 357v-58c0-13 10-23 23-23h172c13 0 23 10 23 23v58M164 319h192M181 276v-35c0-11 9-20 20-20h118c11 0 20 9 20 20v35M202 357v-37M318 357v-37" />
                    <path class="vision-art-plant" d="M387 357v-72m0 24c-25-3-36-17-36-34 22 1 36 12 36 34Zm0-8c1-24 14-39 34-42 1 22-11 38-34 42Zm0 37c-18-3-26-14-26-28 16 1 26 10 26 28Zm0-8c1-19 12-30 28-32 0 18-10 29-28 32Z" />
                    <path class="vision-art-ground" d="M80 357h360" />
                    <circle class="vision-art-satellite" cx="260" cy="75" r="5" />
                    <circle class="vision-art-satellite vision-art-satellite--small" cx="405" cy="220" r="3">
                        <animateMotion dur="22s" repeatCount="indefinite" path="M0 0 A145 145 0 1 0 -290 0 A145 145 0 1 0 0 0" />
                    </circle>
                </svg>
                <span class="ilena-vision-motion-caption"><i></i> <?= lang('Store.visionMotionCaption'); ?></span>
            </figure>
            <div class="ilena-vision-hero-foot" data-reveal>
                <span class="ilena-vision-index">01 <i></i> <?= lang('Store.vision'); ?></span>
                <p><?= lang('Store.visionParagraph'); ?></p>
            </div>
        </section>

        <section class="ilena-values-section" aria-labelledby="ilena-values-title">
            <div class="ilena-vision-section-heading" data-reveal>
                <span class="ilena-vision-index">02 <i></i> <?= lang('Store.ourCulture'); ?></span>
                <div>
                    <h2 id="ilena-values-title"><?= lang('Store.ramahCultureTitle'); ?></h2>
                    <p><?= lang('Store.ramahCultureBody'); ?></p>
                </div>
            </div>

            <div class="ilena-values-layout">
                <figure class="ilena-values-art" data-reveal>
                    <img src="<?= base_url('img/Ramah Ilena/Ramah Ilena Full.png'); ?>" alt="<?= esc(lang('Store.valuesImageAlt'), 'attr'); ?>" loading="lazy" decoding="async">
                    <figcaption><?= lang('Store.valuesCaption'); ?></figcaption>
                </figure>
                <ol class="ilena-values-list">
                    <li data-reveal><span class="ilena-value-initial">R</span><div><h3>Ragam</h3><p><?= lang('Store.valueRagam'); ?></p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">A</span><div><h3>Amanah</h3><p><?= lang('Store.valueAmanah'); ?></p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">M</span><div><h3>Modern</h3><p><?= lang('Store.valueModern'); ?></p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">A</span><div><h3>Adaptif</h3><p><?= lang('Store.valueAdaptif'); ?></p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                    <li data-reveal><span class="ilena-value-initial">H</span><div><h3>Harmonis</h3><p><?= lang('Store.valueHarmonis'); ?></p></div><span class="ilena-value-line" aria-hidden="true"></span></li>
                </ol>
            </div>
        </section>

        <section class="ilena-mission-section" aria-labelledby="ilena-mission-title">
            <div class="ilena-vision-section-heading" data-reveal>
                <span class="ilena-vision-index">03 <i></i> <?= lang('Store.ourDirection'); ?></span>
                <div>
                    <h2 id="ilena-mission-title"><?= lang('Store.missionTitle'); ?></h2>
                    <p><?= lang('Store.missionIntro'); ?></p>
                </div>
            </div>
            <ol class="ilena-mission-list">
                <li data-reveal><span class="ilena-mission-mark" aria-hidden="true">01</span><div><h3><?= lang('Store.missionTrustTitle'); ?></h3><p><?= lang('Store.missionTrustBody'); ?></p></div></li>
                <li data-reveal><span class="ilena-mission-mark" aria-hidden="true">02</span><div><h3><?= lang('Store.missionEnvironmentTitle'); ?></h3><p><?= lang('Store.missionEnvironmentBody'); ?></p></div></li>
                <li data-reveal><span class="ilena-mission-mark" aria-hidden="true">03</span><div><h3><?= lang('Store.missionPurposeTitle'); ?></h3><p><?= lang('Store.missionPurposeBody'); ?></p></div></li>
            </ol>
        </section>
    </div>
</main>
<script>
(() => {
    const page = document.querySelector('.ilena-vision-page');
    const items = page?.querySelectorAll('[data-reveal]');
    if (!page || !items?.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    page.classList.add('ilena-motion-ready');
    const observer = new IntersectionObserver((entries, activeObserver) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            activeObserver.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -32px 0px' });
    items.forEach((item) => observer.observe(item));
})();
</script>
<?= $this->endSection(); ?>
