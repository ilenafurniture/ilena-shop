<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php
$isEnglish = (bool) ($isEnglish ?? false);
$projects = is_array($portfolioProjects ?? null) ? $portfolioProjects : [];
$pageEnabled = !empty($portfolioPageEnabled);
$featuredIndex = null;
foreach ($projects as $index => $project) {
    if (!empty($project['featured'])) { $featuredIndex = $index; break; }
}
if ($featuredIndex === null && $projects) $featuredIndex = 0;
$featured = $featuredIndex !== null ? $projects[$featuredIndex] : null;
$mediaAlt = static fn (array $project, string $fallback): string => trim((string) ($project['title_id'] ?? '')) ?: $fallback;
?>
<main class="ilena-portfolio" aria-labelledby="portfolio-title">
    <nav class="ilena-portfolio__crumb" aria-label="<?= $isEnglish ? 'Breadcrumb' : 'Navigasi halaman'; ?>">
        <a href="<?= esc(ilena_store_path('/'), 'attr'); ?>"><?= $isEnglish ? 'Home' : 'Beranda'; ?></a>
        <span aria-hidden="true">/</span><span aria-current="page"><?= $isEnglish ? 'Portfolio' : 'Portofolio'; ?></span>
    </nav>

    <?php if (!$pageEnabled || !$projects): ?>
        <section class="ilena-portfolio__coming" aria-labelledby="portfolio-title">
            <div class="ilena-portfolio__coming-copy" data-portfolio-reveal>
                <p class="ilena-portfolio__eyebrow"><?= $isEnglish ? 'A NEW PERSPECTIVE IS TAKING SHAPE' : 'SEBUAH PERSPEKTIF BARU SEDANG DISIAPKAN'; ?></p>
                <h1 id="portfolio-title" data-portfolio-text-reveal><?= $isEnglish ? 'Spaces made to feel like yours.' : 'Ruang yang terasa benar-benar milik Anda.'; ?></h1>
                <p><?= $isEnglish ? 'We are carefully curating Ilena’s furniture and interior stories. The portfolio will be here soon.' : 'Kami sedang menyiapkan cerita furniture dan interior Ilena dengan saksama. Portofolio pilihan akan segera hadir di sini.'; ?></p>
                <span class="ilena-portfolio__coming-label"><i aria-hidden="true"></i><?= $isEnglish ? 'COMING SOON' : 'SEGERA HADIR'; ?></span>
                <a class="ilena-portfolio__coming-link" href="<?= esc(ilena_store_path('/contact'), 'attr'); ?>"><?= $isEnglish ? 'Talk with Ilena' : 'Hubungi Ilena'; ?></a>
            </div>
            <div class="ilena-portfolio__coming-art" aria-hidden="true" data-portfolio-reveal="media">
                <span class="ilena-portfolio__art-caption">ILENA / PORTFOLIO</span>
                <svg viewBox="0 0 560 390" role="presentation" focusable="false">
                    <path class="portfolio-art-room" d="M74 326V93l164-58 247 78v213" />
                    <path class="portfolio-art-floor" d="M54 326h466M74 93l247 78 164-58M321 171v155" />
                    <path class="portfolio-art-furniture" d="M139 285v-60c0-16 13-29 29-29h110c16 0 29 13 29 29v60m-168-25h168m-150 25v31m132-31v31M175 195v-39h93v39m-80-39v-29m67 29v-29m-67 0h67" />
                    <path class="portfolio-art-detail" d="M362 326v-81h69v81m-69-54h69m-56-27v-31h43v31m-22 0v31" />
                    <path class="portfolio-art-sweep" pathLength="1" d="M86 343c80 17 172 22 263 13 64-7 111-22 140-43" />
                    <circle class="portfolio-art-marker" cx="0" cy="0" r="6" />
                </svg>
                <span class="ilena-portfolio__art-index">01 <i></i> 03</span>
            </div>
        </section>
    <?php else: ?>

    <header class="ilena-portfolio__intro">
        <div class="ilena-portfolio__intro-copy" data-portfolio-reveal>
            <p class="ilena-portfolio__eyebrow"><?= $isEnglish ? 'ILENA FURNITURE · SELECTED WORKS' : 'ILENA FURNITURE · PILIHAN KARYA'; ?></p>
            <h1 id="portfolio-title" data-portfolio-text-reveal><?= $isEnglish ? 'Spaces made to feel like yours.' : 'Ruang yang terasa benar-benar milik Anda.'; ?></h1>
            <p><?= $isEnglish ? 'A considered collection of furniture and interior projects, shaped around the way people live.' : 'Pilihan proyek furniture dan interior yang dirancang dengan perhatian pada cara setiap orang menjalani ruangnya.'; ?></p>
            <?php if ($projects): ?><a class="ilena-portfolio__jump" href="#portfolio-projects"><?= $isEnglish ? 'Explore selected projects' : 'Jelajahi proyek pilihan'; ?><span aria-hidden="true">↓</span></a><?php endif; ?>
        </div>
        <div class="ilena-portfolio__intro-mark" aria-hidden="true" data-portfolio-reveal>
            <span class="ilena-portfolio__mark-letter">I</span>
            <span class="ilena-portfolio__mark-caption">FURNITURE<br>& INTERIOR</span>
            <span class="ilena-portfolio__mark-rule"></span>
        </div>
    </header>

    <?php if ($featured): ?>
        <?php
        $featuredTitle = $isEnglish ? ((string) ($featured['title_en'] ?? '') ?: $featured['title_id']) : $featured['title_id'];
        $featuredCategory = $isEnglish ? ((string) ($featured['category_en'] ?? '') ?: $featured['category_id']) : $featured['category_id'];
        $featuredSummary = $isEnglish ? ((string) ($featured['summary_en'] ?? '') ?: $featured['summary_id']) : $featured['summary_id'];
        ?>
        <section class="ilena-portfolio__featured" id="portfolio-projects" aria-label="<?= $isEnglish ? 'Featured project' : 'Proyek unggulan'; ?>">
            <figure class="ilena-portfolio__featured-image" data-portfolio-reveal="media">
                <?php if (!empty($featured['cover_image'])): ?>
                    <img src="<?= esc($featured['cover_image'], 'attr'); ?>" alt="<?= esc($featuredTitle ?: $mediaAlt($featured, $isEnglish ? 'Featured Ilena project' : 'Proyek pilihan Ilena'), 'attr'); ?>" width="1600" height="1000" fetchpriority="high" decoding="async">
                <?php else: ?><div class="ilena-portfolio__media-empty" aria-hidden="true"><span>ILENA</span><small><?= $isEnglish ? 'Project photography will appear here' : 'Foto proyek akan ditampilkan di sini'; ?></small></div><?php endif; ?>
                <?php if (!empty($featured['video_url'])): ?><video class="ilena-portfolio__video" controls preload="none" playsinline poster="<?= esc($featured['cover_image'] ?? '', 'attr'); ?>"><source src="<?= esc($featured['video_url'], 'attr'); ?>"><span><?= $isEnglish ? 'Your browser does not support video.' : 'Browser Anda tidak mendukung video.'; ?></span></video><?php endif; ?>
                <figcaption><?= $isEnglish ? 'SELECTED PROJECT' : 'PROYEK PILIHAN'; ?></figcaption>
            </figure>
            <div class="ilena-portfolio__featured-copy" data-portfolio-reveal>
                <p class="ilena-portfolio__eyebrow"><?= esc($featuredCategory ?: ($isEnglish ? 'Furniture & Interior' : 'Furniture & Interior')); ?></p>
                <h2><?= esc($featuredTitle); ?></h2>
                <?php if ($featuredSummary !== ''): ?><p class="ilena-portfolio__summary"><?= esc($featuredSummary); ?></p><?php endif; ?>
                <dl class="ilena-portfolio__facts">
                    <?php if (!empty($featured['location'])): ?><div><dt><?= $isEnglish ? 'Location' : 'Lokasi'; ?></dt><dd><?= esc($featured['location']); ?></dd></div><?php endif; ?>
                    <?php if (!empty($featured['completed_year'])): ?><div><dt><?= $isEnglish ? 'Completed' : 'Tahun'; ?></dt><dd><?= esc((string) $featured['completed_year']); ?></dd></div><?php endif; ?>
                </dl>
                <?php if (!empty($featured['description_id']) || !empty($featured['description_en'])): ?><details class="ilena-portfolio__details"><summary><?= $isEnglish ? 'Project story' : 'Cerita proyek'; ?></summary><p><?= esc($isEnglish ? ((string) ($featured['description_en'] ?? '') ?: (string) $featured['description_id']) : (string) $featured['description_id']); ?></p></details><?php endif; ?>
                <?php if (!empty($featured['gallery'])): ?><details class="ilena-portfolio__gallery"><summary><?= $isEnglish ? 'View project photos' : 'Lihat foto proyek'; ?> (<?= count($featured['gallery']); ?>)</summary><div><?php foreach ($featured['gallery'] as $photo): ?><img src="<?= esc($photo, 'attr'); ?>" alt="<?= esc($featuredTitle . ' — ' . ($isEnglish ? 'project detail' : 'detail proyek'), 'attr'); ?>" loading="lazy" decoding="async"><?php endforeach; ?></div></details><?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (count($projects) > ($featuredIndex !== null ? 1 : 0)): ?>
        <section class="ilena-portfolio__work" aria-labelledby="portfolio-work-title">
            <div class="ilena-portfolio__section-head" data-portfolio-reveal><div><p class="ilena-portfolio__eyebrow"><?= $isEnglish ? 'MORE FROM ILENA' : 'KARYA LAINNYA'; ?></p><h2 id="portfolio-work-title"><?= $isEnglish ? 'Made for the way you live.' : 'Dirancang untuk keseharian Anda.'; ?></h2></div><span><?= sprintf('%02d', count($projects)); ?> <?= $isEnglish ? 'PROJECTS' : 'PROYEK'; ?></span></div>
            <div class="ilena-portfolio__grid">
                <?php foreach ($projects as $index => $project): if ($index === $featuredIndex) continue;
                    $cardTitle = $isEnglish ? ((string) ($project['title_en'] ?? '') ?: $project['title_id']) : $project['title_id'];
                    $cardCategory = $isEnglish ? ((string) ($project['category_en'] ?? '') ?: $project['category_id']) : $project['category_id'];
                    $cardSummary = $isEnglish ? ((string) ($project['summary_en'] ?? '') ?: $project['summary_id']) : $project['summary_id'];
                ?>
                    <article class="ilena-portfolio__project" data-portfolio-reveal>
                        <figure class="ilena-portfolio__project-media" data-portfolio-reveal="media">
                            <?php if (!empty($project['cover_image'])): ?><img src="<?= esc($project['cover_image'], 'attr'); ?>" alt="<?= esc($mediaAlt($project, $isEnglish ? 'Ilena furniture and interior project' : 'Proyek furniture dan interior Ilena'), 'attr'); ?>" width="900" height="700" loading="lazy" decoding="async">
                            <?php else: ?><div class="ilena-portfolio__media-empty" aria-hidden="true"><span>ILENA</span><small><?= $isEnglish ? 'Photography coming soon' : 'Foto akan ditambahkan'; ?></small></div><?php endif; ?>
                            <?php if (!empty($project['video_url'])): ?><video controls preload="none" playsinline poster="<?= esc($project['cover_image'] ?? '', 'attr'); ?>"><source src="<?= esc($project['video_url'], 'attr'); ?>"></video><?php endif; ?>
                        </figure>
                        <div class="ilena-portfolio__project-copy"><p class="ilena-portfolio__eyebrow"><?= esc($cardCategory); ?></p><h3><?= esc($cardTitle); ?></h3><?php if ($cardSummary !== ''): ?><p><?= esc($cardSummary); ?></p><?php endif; ?><div class="ilena-portfolio__project-meta"><?php if (!empty($project['location'])): ?><span><?= esc($project['location']); ?></span><?php endif; ?><?php if (!empty($project['completed_year'])): ?><span><?= esc((string) $project['completed_year']); ?></span><?php endif; ?></div><?php if (!empty($project['gallery'])): ?><details class="ilena-portfolio__gallery"><summary><?= $isEnglish ? 'View project photos' : 'Lihat foto proyek'; ?> (<?= count($project['gallery']); ?>)</summary><div><?php foreach ($project['gallery'] as $photo): ?><img src="<?= esc($photo, 'attr'); ?>" alt="<?= esc($cardTitle . ' — ' . ($isEnglish ? 'project detail' : 'detail proyek'), 'attr'); ?>" loading="lazy" decoding="async"><?php endforeach; ?></div></details><?php endif; ?><?php if (!empty($project['description_id']) || !empty($project['description_en'])): ?><details class="ilena-portfolio__details"><summary><?= $isEnglish ? 'Project story' : 'Cerita proyek'; ?></summary><p><?= esc($isEnglish ? ((string) ($project['description_en'] ?? '') ?: (string) $project['description_id']) : (string) $project['description_id']); ?></p></details><?php endif; ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($projects): ?>
        <aside class="ilena-portfolio__closing" data-portfolio-reveal><p class="ilena-portfolio__eyebrow"><?= $isEnglish ? 'YOUR NEXT SPACE' : 'RUANG BERIKUTNYA'; ?></p><h2><?= $isEnglish ? 'Every room has a story still to shape.' : 'Setiap ruang punya cerita yang bisa diwujudkan.'; ?></h2><a href="<?= esc(ilena_store_path('/contact'), 'attr'); ?>"><?= $isEnglish ? 'Talk with Ilena' : 'Diskusikan bersama Ilena'; ?></a></aside>
    <?php endif; ?>
    <?php endif; ?>
</main>
<style>
.ilena-portfolio{--pf-ink:#211f1e;--pf-muted:#736d69;--pf-line:#ded9d4;--pf-red:#922e2a;color:var(--pf-ink);width:min(1240px,calc(100% - 48px));margin:0 auto;padding:26px 0 96px}.ilena-portfolio__crumb{display:flex;gap:10px;color:var(--pf-muted);font-size:12px;margin:0 0 40px}.ilena-portfolio__crumb a{color:inherit;text-decoration:none}.ilena-portfolio__crumb a:hover{color:var(--pf-red)}.ilena-portfolio__intro{display:grid;grid-template-columns:minmax(0,1fr) 240px;gap:48px;align-items:end;padding:0 0 clamp(52px,8vw,112px);border-bottom:1px solid var(--pf-line)}.ilena-portfolio__intro-copy{max-width:820px}.ilena-portfolio__eyebrow{font-size:10px;letter-spacing:.16em;font-weight:700;color:var(--pf-red);margin:0 0 16px}.ilena-portfolio h1,.ilena-portfolio h2,.ilena-portfolio h3{font-family:inherit;letter-spacing:-.045em}.ilena-portfolio h1{font-size:clamp(42px,7vw,88px);line-height:.99;font-weight:500;max-width:850px;margin:0}.ilena-portfolio__intro-copy>p:not(.ilena-portfolio__eyebrow){font-size:clamp(15px,1.5vw,19px);line-height:1.7;color:var(--pf-muted);max-width:620px;margin:24px 0}.ilena-portfolio__jump,.ilena-portfolio__empty>a,.ilena-portfolio__closing>a{color:var(--pf-ink);font-weight:700;font-size:13px;text-decoration:none;border-bottom:1px solid var(--pf-red);padding:0 0 8px;display:inline-flex;gap:20px}.ilena-portfolio__intro-mark{height:220px;border-left:1px solid var(--pf-line);display:flex;flex-direction:column;align-items:flex-end;justify-content:flex-end;position:relative}.ilena-portfolio__mark-letter{font-family:Georgia,serif;font-size:clamp(120px,17vw,210px);line-height:.72;color:#eeeae6;font-weight:400}.ilena-portfolio__mark-caption{position:absolute;top:18px;right:0;color:var(--pf-muted);font-size:9px;line-height:1.6;letter-spacing:.14em}.ilena-portfolio__mark-rule{position:absolute;bottom:0;left:24px;height:1px;width:52px;background:var(--pf-red)}.ilena-portfolio__featured{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(260px,.75fr);gap:clamp(28px,6vw,86px);align-items:center;padding:clamp(48px,8vw,104px) 0;border-bottom:1px solid var(--pf-line)}.ilena-portfolio__featured-image,.ilena-portfolio__project-media{margin:0;position:relative;background:#eeeae6;overflow:hidden;aspect-ratio:1.48}.ilena-portfolio__featured-image>img,.ilena-portfolio__project-media>img{width:100%;height:100%;object-fit:cover;display:block}.ilena-portfolio__featured-image figcaption{position:absolute;bottom:14px;left:16px;color:#fff;background:rgba(20,19,18,.68);padding:7px 10px;font-size:9px;letter-spacing:.15em}.ilena-portfolio__video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;background:#171514}.ilena-portfolio__featured-copy h2{font-size:clamp(32px,4vw,54px);line-height:1.04;font-weight:500;margin:0 0 18px}.ilena-portfolio__summary{color:var(--pf-muted);line-height:1.75;font-size:15px}.ilena-portfolio__facts{display:flex;gap:32px;margin:28px 0 0}.ilena-portfolio__facts div{display:grid;gap:5px}.ilena-portfolio__facts dt{font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--pf-muted)}.ilena-portfolio__facts dd{font-size:13px;margin:0;font-weight:650}.ilena-portfolio__empty{margin:50px 0 0;padding:clamp(28px,6vw,68px);background:#f4f1ee;display:grid;grid-template-columns:1fr minmax(250px,.65fr);gap:40px;align-items:end}.ilena-portfolio__empty-index{font:500 clamp(30px,5vw,62px)/1 Georgia,serif;color:#8b827b}.ilena-portfolio__empty-index span{display:block;margin-top:12px;font-family:inherit;font-size:9px;font-weight:700;line-height:1.5;letter-spacing:.14em;color:var(--pf-red)}.ilena-portfolio__empty>p{font-size:17px;line-height:1.7;color:var(--pf-muted);margin:0}.ilena-portfolio__empty>a{grid-column:2;justify-self:start}.ilena-portfolio__work{padding:clamp(56px,8vw,104px) 0}.ilena-portfolio__section-head{display:flex;justify-content:space-between;align-items:end;gap:20px;margin-bottom:30px}.ilena-portfolio__section-head h2{font-size:clamp(30px,4vw,48px);font-weight:500;margin:0}.ilena-portfolio__section-head>span{font-size:10px;letter-spacing:.12em;color:var(--pf-muted)}.ilena-portfolio__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:clamp(24px,4vw,58px) 28px}.ilena-portfolio__project:nth-child(3n+2){margin-top:56px}.ilena-portfolio__project-media{aspect-ratio:1.34}.ilena-portfolio__project-copy{padding-top:20px}.ilena-portfolio__project-copy h3{font-size:clamp(22px,2.4vw,30px);font-weight:550;margin:0 0 10px}.ilena-portfolio__project-copy>p:not(.ilena-portfolio__eyebrow){color:var(--pf-muted);font-size:14px;line-height:1.7;margin:0;max-width:620px}.ilena-portfolio__project-meta{display:flex;gap:18px;color:var(--pf-muted);font-size:12px;margin-top:17px}.ilena-portfolio__details,.ilena-portfolio__gallery{margin-top:18px;border-top:1px solid var(--pf-line);padding-top:12px}.ilena-portfolio__details summary,.ilena-portfolio__gallery summary{font-size:12px;font-weight:700;cursor:pointer;list-style:none}.ilena-portfolio__details summary::-webkit-details-marker,.ilena-portfolio__gallery summary::-webkit-details-marker{display:none}.ilena-portfolio__details summary:after,.ilena-portfolio__gallery summary:after{content:'+';float:right;color:var(--pf-red);font-size:17px;line-height:12px}.ilena-portfolio__details[open] summary:after,.ilena-portfolio__gallery[open] summary:after{content:'−'}.ilena-portfolio__details p{font-size:13px;line-height:1.75;color:var(--pf-muted);white-space:pre-line;margin:12px 0 0}.ilena-portfolio__gallery>div{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:12px}.ilena-portfolio__gallery img{width:100%;aspect-ratio:1;object-fit:cover}.ilena-portfolio__media-empty{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;background:linear-gradient(135deg,#ebe7e2,#f6f4f1);color:#8d8580}.ilena-portfolio__media-empty span{font:500 38px Georgia,serif;letter-spacing:.14em}.ilena-portfolio__media-empty small{font-size:11px}.ilena-portfolio__closing{margin-top:40px;padding:clamp(28px,5vw,58px);background:#f4f1ee}.ilena-portfolio__closing h2{font-size:clamp(28px,4vw,48px);line-height:1.1;font-weight:500;max-width:650px;margin:0 0 24px}.ilena-portfolio :focus-visible{outline:2px solid var(--pf-red);outline-offset:4px}
@media(max-width:760px){.ilena-portfolio{width:min(100% - 32px,600px);padding:20px 0 64px}.ilena-portfolio__crumb{margin-bottom:28px}.ilena-portfolio__intro{grid-template-columns:1fr;gap:22px;padding-bottom:44px}.ilena-portfolio__intro-mark{height:82px;border-left:0;border-bottom:1px solid var(--pf-line);align-items:flex-start;justify-content:flex-end}.ilena-portfolio__mark-letter{font-size:76px}.ilena-portfolio__mark-caption{top:auto;bottom:12px;left:86px;right:auto}.ilena-portfolio__mark-rule{left:auto;right:0;bottom:-1px}.ilena-portfolio__featured{grid-template-columns:1fr;gap:25px;padding:42px 0}.ilena-portfolio__featured-image{aspect-ratio:1.2}.ilena-portfolio__empty{grid-template-columns:1fr;gap:22px;margin-top:32px;padding:26px 22px}.ilena-portfolio__empty>a{grid-column:1}.ilena-portfolio__grid{grid-template-columns:1fr;gap:34px}.ilena-portfolio__project:nth-child(3n+2){margin-top:0}.ilena-portfolio__section-head{align-items:start}.ilena-portfolio__section-head>span{padding-top:3px}.ilena-portfolio__facts{gap:24px}.ilena-portfolio__work{padding:54px 0}.ilena-portfolio__gallery>div{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(prefers-reduced-motion:reduce){.ilena-portfolio *{scroll-behavior:auto!important;animation:none!important;transition:none!important}}
</style>
<style>
.ilena-portfolio__coming{min-height:min(690px,calc(100svh - 210px));display:grid;grid-template-columns:minmax(0,.92fr) minmax(400px,1.08fr);align-items:center;gap:clamp(24px,5vw,76px);padding:clamp(26px,5vw,64px) 0 36px}.ilena-portfolio__coming-copy{max-width:560px}.ilena-portfolio__coming-copy h1{font-size:clamp(38px,5.4vw,70px);line-height:1.03;font-weight:500}.ilena-portfolio__coming-copy>p:not(.ilena-portfolio__eyebrow){font-size:15px;line-height:1.8;color:var(--pf-muted);max-width:470px;margin:22px 0}.ilena-portfolio__coming-label{display:flex;align-items:center;gap:10px;margin:28px 0 26px;font-size:10px;letter-spacing:.16em;font-weight:750;color:var(--pf-red)}.ilena-portfolio__coming-label i{width:7px;height:7px;border-radius:50%;background:var(--pf-red);animation:portfolio-status 2.4s ease-in-out infinite}.ilena-portfolio__coming-link{display:inline-flex;color:var(--pf-ink);font-weight:700;font-size:13px;text-decoration:none;border-bottom:1px solid var(--pf-red);padding-bottom:8px}.ilena-portfolio__coming-art{position:relative;min-width:0;background:#f3f0ec;overflow:hidden;aspect-ratio:1.4;display:grid;place-items:center}.ilena-portfolio__coming-art:before{content:"";position:absolute;inset:9% 8%;border:1px solid rgba(86,72,62,.13)}.ilena-portfolio__coming-art svg{width:94%;height:auto;overflow:visible}.ilena-portfolio__art-caption,.ilena-portfolio__art-index{position:absolute;z-index:1;font-size:9px;letter-spacing:.15em;color:#746b64}.ilena-portfolio__art-caption{top:7%;left:8%}.ilena-portfolio__art-index{right:8%;bottom:7%;display:flex;align-items:center;gap:10px}.ilena-portfolio__art-index i{width:36px;height:1px;background:var(--pf-red)}.portfolio-art-room,.portfolio-art-floor,.portfolio-art-furniture,.portfolio-art-detail,.portfolio-art-sweep{fill:none;stroke:#332d29;stroke-width:1.35;stroke-linecap:round;stroke-linejoin:round;vector-effect:non-scaling-stroke}.portfolio-art-room{stroke:#857a72;stroke-width:1}.portfolio-art-furniture{stroke:#292522;stroke-width:1.7;stroke-dasharray:1;stroke-dashoffset:1;animation:portfolio-draw 2.8s .25s ease forwards}.portfolio-art-detail{stroke:#8f302b;stroke-width:1.5;stroke-dasharray:1;stroke-dashoffset:1;animation:portfolio-draw 2s 1.3s ease forwards}.portfolio-art-sweep{stroke:#a7443e;stroke-width:1.4;stroke-dasharray:1;stroke-dashoffset:1;animation:portfolio-draw 1.7s 2.1s ease forwards}.portfolio-art-marker{fill:#a33a34;animation:portfolio-marker 6s 3.2s ease-in-out infinite;transform-box:fill-box;transform-origin:center}.ilena-portfolio__coming :focus-visible{outline:2px solid var(--pf-red);outline-offset:4px}@keyframes portfolio-draw{to{stroke-dashoffset:0}}@keyframes portfolio-marker{0%{transform:translate(88px,343px);opacity:0}10%{opacity:1}45%{transform:translate(324px,356px);opacity:1}82%{transform:translate(494px,313px);opacity:1}100%{transform:translate(494px,313px);opacity:0}}@keyframes portfolio-status{0%,100%{box-shadow:0 0 0 0 rgba(146,46,42,.24)}50%{box-shadow:0 0 0 5px rgba(146,46,42,0)}}
@media(max-width:760px){.ilena-portfolio__coming{grid-template-columns:1fr;min-height:0;padding:24px 0 10px;gap:30px}.ilena-portfolio__coming-copy h1{font-size:clamp(37px,10vw,58px)}.ilena-portfolio__coming-art{aspect-ratio:1.12}.ilena-portfolio__art-caption,.ilena-portfolio__art-index{font-size:8px}}
@media(prefers-reduced-motion:reduce){.ilena-portfolio__coming *{animation:none!important;transition:none!important}}
</style>
<style>
.ilena-portfolio .ilena-portfolio__jump,.ilena-portfolio .ilena-portfolio__coming-link,.ilena-portfolio .ilena-portfolio__closing>a{transition:color .22s ease,transform .22s ease,border-color .22s ease}.ilena-portfolio .ilena-portfolio__jump:hover,.ilena-portfolio .ilena-portfolio__coming-link:hover,.ilena-portfolio .ilena-portfolio__closing>a:hover{color:var(--pf-red);transform:translateY(-2px);border-color:var(--pf-red)}.ilena-portfolio__featured-image>img,.ilena-portfolio__project-media>img{transition:transform .75s cubic-bezier(.2,.7,.2,1)}.ilena-portfolio__project{transition:transform .35s cubic-bezier(.2,.7,.2,1)}.ilena-portfolio__featured-image,.ilena-portfolio__project-media{overflow:hidden}.ilena-portfolio__details[open]>p,.ilena-portfolio__gallery[open]>div{animation:portfolio-details-in .28s ease both}
@media(hover:hover) and (pointer:fine){.ilena-portfolio__featured-image:hover>img,.ilena-portfolio__project-media:hover>img{transform:scale(1.035)}.ilena-portfolio__project:hover,.ilena-portfolio__project:focus-within{transform:translateY(-4px)}}
.portfolio-motion-ready [data-portfolio-reveal],.portfolio-motion-ready [data-portfolio-text-reveal]{opacity:0;transform:translate3d(0,14px,0);transition:opacity .62s ease var(--pf-reveal-delay,0ms),transform .62s cubic-bezier(.2,.7,.2,1) var(--pf-reveal-delay,0ms)}.portfolio-motion-ready [data-portfolio-reveal].is-visible,.portfolio-motion-ready [data-portfolio-text-reveal].is-visible{opacity:1;transform:none}.portfolio-motion-ready [data-portfolio-reveal="media"]{clip-path:inset(0 4% 0 4%);transition:opacity .65s ease var(--pf-reveal-delay,0ms),transform .65s ease var(--pf-reveal-delay,0ms),clip-path .8s cubic-bezier(.2,.7,.2,1) var(--pf-reveal-delay,0ms)}.portfolio-motion-ready [data-portfolio-reveal="media"].is-visible{clip-path:inset(0)}.portfolio-motion-ready [data-portfolio-text-reveal]{clip-path:inset(0 0 100%);transition:opacity .55s ease var(--pf-reveal-delay,0ms),transform .55s ease var(--pf-reveal-delay,0ms),clip-path .72s cubic-bezier(.2,.7,.2,1) calc(var(--pf-reveal-delay,0ms) + 100ms)}.portfolio-motion-ready [data-portfolio-text-reveal].is-visible{clip-path:inset(0)}
@media(hover:hover) and (pointer:fine){.portfolio-motion-ready .ilena-portfolio__project.is-visible:hover,.portfolio-motion-ready .ilena-portfolio__project.is-visible:focus-within{transform:translateY(-4px)}}
.portfolio-motion-ready .ilena-portfolio__coming-art:not(.is-visible) .portfolio-art-furniture,.portfolio-motion-ready .ilena-portfolio__coming-art:not(.is-visible) .portfolio-art-detail,.portfolio-motion-ready .ilena-portfolio__coming-art:not(.is-visible) .portfolio-art-sweep,.portfolio-motion-ready .ilena-portfolio__coming-art:not(.is-visible) .portfolio-art-marker{animation-play-state:paused}
@keyframes portfolio-details-in{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:none}}
@media(prefers-reduced-motion:reduce){.ilena-portfolio *{scroll-behavior:auto!important;animation:none!important;transition:none!important;transform:none!important;clip-path:none!important}}
</style>
<script>
(function () {
    var page = document.querySelector('.ilena-portfolio');
    if (!page) return;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;
    document.documentElement.style.scrollBehavior = 'smooth';
    var items = Array.prototype.slice.call(page.querySelectorAll('[data-portfolio-reveal], [data-portfolio-text-reveal]'));
    if (!items.length || !('IntersectionObserver' in window)) return;
    items.forEach(function (item, index) {
        item.style.setProperty('--pf-reveal-delay', Math.min((index % 4) * 65, 195) + 'ms');
    });
    page.classList.add('portfolio-motion-ready');
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
    items.forEach(function (item) { observer.observe(item); });
})();
</script>
<?= $this->endSection(); ?>
