<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php
$isEnglish = (bool) ($isEnglish ?? false);
$money = static fn (int $amount): string => 'Rp ' . number_format($amount, 0, ',', '.');
?>
<main class="ilena-bundles" aria-labelledby="bundle-title">
    <header class="ilena-bundles__intro">
        <p><?= $isEnglish ? 'ILENA FURNITURE SETS' : 'PAKET FURNITURE ILENA'; ?></p>
        <h1 id="bundle-title"><?= esc(lang('Store.bundlePageTitle')); ?></h1>
        <span><?= $isEnglish ? 'Pieces chosen to work beautifully together, with each item and saving shown clearly.' : 'Furniture yang dipilih agar serasi, dengan rincian setiap produk dan penghematan yang jelas.'; ?></span>
    </header>

    <?php if (session()->getFlashdata('msg')): ?>
        <p class="ilena-bundles__notice" role="status"><?= esc(session()->getFlashdata('msg')); ?></p>
    <?php endif; ?>

    <?php if ($offers === []): ?>
        <section class="ilena-bundles__empty" role="status">
            <h2><?= $isEnglish ? 'No sets available right now' : 'Belum ada paket yang tersedia'; ?></h2>
            <p><?= $isEnglish ? 'Browse the collection and check back for new furniture sets.' : 'Jelajahi koleksi furniture kami dan kembali lagi untuk melihat paket terbaru.'; ?></p>
            <a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= esc(lang('Store.productDetails')); ?></a>
        </section>
    <?php else: ?>
        <div class="ilena-bundles__list">
            <?php foreach ($offers as $offer): ?>
                <?php
                $bundleName = $isEnglish && trim((string) ($offer['name_en'] ?? '')) !== '' ? $offer['name_en'] : $offer['name'];
                $bundleDescription = $isEnglish && trim((string) ($offer['description_en'] ?? '')) !== '' ? $offer['description_en'] : $offer['description'];
                $availableSets = (int) ($offer['available_sets'] ?? 0);
                ?>
                <article class="ilena-bundle">
                    <div class="ilena-bundle__heading">
                        <div>
                            <p><?= $isEnglish ? 'A considered combination' : 'Kombinasi pilihan'; ?></p>
                            <h2><?= esc($bundleName); ?></h2>
                            <?php if (trim((string) $bundleDescription) !== ''): ?><div><?= esc($bundleDescription); ?></div><?php endif; ?>
                        </div>
                        <span class="ilena-bundle__availability <?= $availableSets < 1 ? 'is-empty' : ''; ?>">
                            <?= $availableSets > 0 ? ($isEnglish ? $availableSets . ' sets available' : $availableSets . ' paket tersedia') : esc(lang('Store.bundleSoldOut')); ?>
                        </span>
                    </div>

                    <ul class="ilena-bundle__components">
                        <?php foreach ($offer['components'] as $component): ?>
                            <?php
                            $product = (array) ($component['product'] ?? []);
                            $variantRows = json_decode((string) ($product['varian'] ?? '[]'), true) ?: [];
                            $imageSlot = '1';
                            foreach ($variantRows as $variantRow) {
                                if (is_array($variantRow) && strcasecmp(trim((string) ($variantRow['nama'] ?? '')), (string) $component['variant_name']) === 0) {
                                    $imageSlot = trim((string) (explode(',', (string) ($variantRow['urutan_gambar'] ?? '1'))[0] ?? '1')) ?: '1';
                                    break;
                                }
                            }
                            $imageUrl = base_url('viewvar/' . rawurlencode((string) $component['product_id']) . '/' . rawurlencode($imageSlot));
                            $componentName = $isEnglish ? ilena_product_display_name($product) : ilena_product_display_name($product);
                            ?>
                            <li class="ilena-bundle__component">
                                <img src="<?= esc($imageUrl, 'attr'); ?>" width="76" height="76" loading="lazy" decoding="async" alt="<?= esc($componentName, 'attr'); ?>">
                                <div>
                                    <a href="<?= esc(ilena_store_path('/product/' . url_title((string) ($product['nama'] ?? ''), '-', true)), 'attr'); ?>"><?= esc($componentName); ?></a>
                                    <span><?= esc(ilena_product_variant_display_name($product, (string) $component['variant_name'])); ?> · <?= (int) $component['quantity']; ?> <?= $isEnglish ? 'pc' : 'pcs'; ?></span>
                                </div>
                                <strong><?= esc($money((int) $component['line_total_after'])); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <footer class="ilena-bundle__footer">
                        <div class="ilena-bundle__totals">
                            <span><?= $isEnglish ? 'Set total' : 'Total paket'; ?></span>
                            <s><?= esc($money((int) $offer['total_before'])); ?></s>
                            <strong><?= esc($money((int) $offer['total_after'])); ?></strong>
                            <small><?= esc(lang('Store.bundleDiscount')); ?> <?= esc($money((int) $offer['discount'])); ?></small>
                        </div>
                        <?php if ($availableSets > 0): ?>
                            <form method="post" action="<?= esc(ilena_store_path('/bundle/add/' . rawurlencode((string) $offer['slug'])), 'attr'); ?>">
                                <?= csrf_field(); ?>
                                <button type="submit"><?= esc(lang('Store.bundleAddToCart')); ?></button>
                            </form>
                        <?php else: ?>
                            <button type="button" disabled><?= esc(lang('Store.bundleSoldOut')); ?></button>
                        <?php endif; ?>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<style>
.ilena-bundles{--bundle-ink:#25211d;--bundle-muted:#6d655e;--bundle-line:#e8e1da;max-width:1180px;margin:0 auto;padding:clamp(28px,5vw,64px) 20px 80px;color:var(--bundle-ink)}
.ilena-bundles__intro{max-width:720px;margin:0 auto 34px;text-align:center}.ilena-bundles__intro>p,.ilena-bundle__heading>div>p{margin:0 0 10px;color:#9c2930;font-size:.72rem;font-weight:750;letter-spacing:.16em}.ilena-bundles__intro h1{margin:0 0 12px;font-size:clamp(1.8rem,4vw,3rem);line-height:1.08;letter-spacing:-.045em}.ilena-bundles__intro>span{color:var(--bundle-muted);line-height:1.7}
.ilena-bundles__list{display:grid;gap:22px}.ilena-bundle{border:1px solid var(--bundle-line);background:#fffdfa}.ilena-bundle__heading{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;padding:24px 26px 18px;border-bottom:1px solid var(--bundle-line)}.ilena-bundle__heading h2{margin:0 0 7px;font-size:clamp(1.25rem,2vw,1.65rem);letter-spacing:-.025em}.ilena-bundle__heading>div>div{max-width:700px;color:var(--bundle-muted);font-size:.94rem;line-height:1.6}.ilena-bundle__availability{flex:none;padding:7px 10px;background:#f7f2ed;color:#4e4035;font-size:.77rem;font-weight:700}.ilena-bundle__availability.is-empty{color:#67615b}.ilena-bundle__components{list-style:none;margin:0;padding:0 26px}.ilena-bundle__component{display:grid;grid-template-columns:76px minmax(0,1fr) auto;align-items:center;gap:16px;padding:16px 0;border-bottom:1px solid #eee9e4}.ilena-bundle__component img{width:76px;height:76px;object-fit:cover;background:#f5f1ec}.ilena-bundle__component div{display:grid;gap:5px}.ilena-bundle__component a{color:var(--bundle-ink);font-weight:700;text-decoration:none}.ilena-bundle__component span{color:var(--bundle-muted);font-size:.85rem}.ilena-bundle__component strong{font-size:.95rem;font-variant-numeric:tabular-nums}.ilena-bundle__footer{display:flex;justify-content:space-between;align-items:center;gap:18px;padding:20px 26px}.ilena-bundle__totals{display:grid;grid-template-columns:auto auto;align-items:center;gap:4px 12px}.ilena-bundle__totals span{font-size:.82rem;color:var(--bundle-muted)}.ilena-bundle__totals s{color:var(--bundle-muted);font-size:.82rem}.ilena-bundle__totals strong{grid-column:1/-1;font-size:1.45rem;letter-spacing:-.03em}.ilena-bundle__totals small{grid-column:1/-1;color:#8f1d22;font-weight:650}.ilena-bundle__footer button,.ilena-bundles__empty a{min-height:46px;padding:0 20px;border:0;background:#981f26;color:#fff;font:inherit;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.ilena-bundle__footer button:disabled{background:#8b8580;cursor:not-allowed}.ilena-bundle__footer button:focus-visible,.ilena-bundles a:focus-visible{outline:3px solid #292421;outline-offset:3px}.ilena-bundles__empty{text-align:center;padding:48px 20px;border-top:1px solid var(--bundle-line);border-bottom:1px solid var(--bundle-line)}.ilena-bundles__empty p{color:var(--bundle-muted)}.ilena-bundles__notice{padding:12px 16px;border-left:3px solid #981f26;background:#f8f4f0}
@media(max-width:640px){.ilena-bundles{padding:24px 14px 48px}.ilena-bundle__heading{padding:18px 16px;display:block}.ilena-bundle__availability{display:inline-block;margin-top:12px}.ilena-bundle__components{padding:0 16px}.ilena-bundle__component{grid-template-columns:60px minmax(0,1fr);gap:12px}.ilena-bundle__component img{width:60px;height:60px}.ilena-bundle__component strong{grid-column:2}.ilena-bundle__footer{padding:18px 16px;align-items:stretch;flex-direction:column}.ilena-bundle__footer form,.ilena-bundle__footer button{width:100%}}
</style>
<?= $this->endSection(); ?>
