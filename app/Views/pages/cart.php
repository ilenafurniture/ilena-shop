<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container d-flex justify-content-center align-items-center ilena-cart-page <?= empty($keranjang) ? 'is-empty' : ''; ?>">
    <?php if ($cartMessage = session()->getFlashdata('msg')): ?>
        <p class="pemberitahuan" role="alert"><?= esc($cartMessage); ?></p>
    <?php endif; ?>
    <div class="konten baris-ke-kolom">
        <div style="flex:1;">
            <div class="mb-4">
                <h1 class="teks-sedang"><?= esc(lang('Store.cartTitle')); ?></h1>
                <p style="color: grey;"><?= count($keranjang) ?> <?= esc(lang('Store.itemCount')); ?></p>
            </div>
            <?php if (empty($keranjang)): ?>
                <div class="ilena-cart-empty">
                    <svg class="ilena-cart-empty__illustration" viewBox="0 0 144 96" role="img" aria-label="<?= esc(lang('Store.emptyCartTitle'), 'attr'); ?>" focusable="false"><path d="M24 55V42a12 12 0 0 1 24 0v13m48 0V42a12 12 0 0 1 24 0v13M20 55h104v19H20zM29 74v9m86-9v9M48 55V42a24 24 0 0 1 48 0v13" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M49 24h46" stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity=".45"/></svg>
                    <p class="ilena-cart-empty__eyebrow"><?= esc(lang('Store.emptyCartEyebrow')); ?></p>
                    <h2><?= esc(lang('Store.emptyCartTitle')); ?></h2>
                    <p><?= esc(lang('Store.emptyCartBody')); ?></p>
                    <a class="btn-default-merah" href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= esc(lang('Store.exploreCollection')); ?></a>
                </div>
            <?php endif; ?>
            <div class="container-keranjang show-block-ke-hide">
                <?php foreach ($keranjang as $index => $k) { $cartProduct = (array) ($k['detail'] ?? []); $cartProductName = ilena_product_display_name($cartProduct); $cartProductName = $cartProductName !== '' ? $cartProductName : lang('Store.productFallback'); $cartCategory = ilena_catalog_type_label((string) ($cartProduct['kategori'] ?? '')); $cartVariant = ilena_product_variant_display_name($cartProduct, (string) ($k['varian'] ?? '')); ?>
                <div class="item-keranjang">
                    <a href="<?= esc(ilena_store_path('/product/' . str_replace(' ', '-', (string) $k['detail']['nama'])), 'attr'); ?>" style="display:block;">
                        <img src="<?= esc($k['src_gambar'] ?? '', 'attr'); ?>" alt="<?= esc($cartProductName, 'attr'); ?>">
                    </a>
                    <div style="flex: 1;" class="d-flex flex-column">
                        <a href="<?= esc(ilena_store_path('/product/' . str_replace(' ', '-', (string) $k['detail']['nama'])), 'attr'); ?>"
                            style="text-decoration:none; color: black;">
                            <p class="m-0"><?= esc($cartCategory); ?></p>
                            <h1 class="nama-barang"><?= esc($cartProductName); ?></h1>
                            <p class="my-2"><?= esc(lang('Store.variantLabel')); ?>: <?= esc($cartVariant); ?></p>
                        </a>
                        <div class="d-flex align-items-end flex-grow-1">
                            <div class="d-flex align-items-center gap-4">
                                <div class="number-control">
                                    <!-- <a style="text-decoration: none; color: black;" href="/reducecart/<?= $index ?>">
                                        <div class="number-left"></div>
                                    </a> -->
                                    <form action="<?= esc(ilena_store_path('/reducecart/' . $index), 'attr'); ?>" method="post">
                                        <?= csrf_field(); ?>
                                        <button type="submit">
                                            <div class="number-left"></div>
                                        </button>
                                    </form>
                                    <input type="number" name="number" class="number-quantity" disabled
                                        value="<?= $k['jumlah'] ?>">
                                    <form action="<?= esc(ilena_store_path('/addcart/' . $k['id_barang'] . '/' . rawurlencode($k['varian']) . '/1'), 'attr'); ?>" method="post">
                                        <?= csrf_field(); ?>
                                        <button type="submit">
                                            <div class="number-right"></div>
                                        </button>
                                    </form>
                                </div>
                                <form action="<?= esc(ilena_store_path('/deletecart/' . $index), 'attr'); ?>" method="post" class="m-0">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn-teks-aja m-0"><?= esc(lang('Store.delete')); ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div style="width: 150px;">
                        <p class="m-0 text-end"><?= esc(lang('Store.unitPrice')); ?></p>
                        <p style="font-weight:bold; font-size: 18px; letter-spacing: -1px; text-align: right;"
                            class="harga">Rp
                            <?= number_format($k['detail']['harga'] * (100 - $k['detail']['diskon']) / 100, 0, ',', '.'); ?>
                        </p>
                        <?php if ($k['detail']['diskon'] > 0) { ?>
                        <p class="harga-diskon text-end">
                            Rp <?= number_format($k['detail']['harga'], 0, ',', '.'); ?>
                        </p>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="hide-ke-show-block">
                <?php foreach ($keranjang as $index => $k) { $cartProduct = (array) ($k['detail'] ?? []); $cartProductName = ilena_product_display_name($cartProduct); $cartProductName = $cartProductName !== '' ? $cartProductName : lang('Store.productFallback'); $cartCategory = ilena_catalog_type_label((string) ($cartProduct['kategori'] ?? '')); $cartVariant = ilena_product_variant_display_name($cartProduct, (string) ($k['varian'] ?? '')); ?>
                <div class="item-keranjang-hp">
                    <a href="<?= esc(ilena_store_path('/product/' . str_replace(' ', '-', (string) $k['detail']['nama'])), 'attr'); ?>" style="display:block;">
                        <img src="<?= esc($k['src_gambar'] ?? '', 'attr'); ?>" alt="<?= esc($cartProductName, 'attr'); ?>">
                    </a>
                    <div style="flex: 1;" class="d-flex flex-column justify-content-between">
                        <a href="<?= esc(ilena_store_path('/product/' . str_replace(' ', '-', (string) $k['detail']['nama'])), 'attr'); ?>"
                            style="text-decoration:none; color: black;" class="d-flex flex-column h-100">
                            <div style="flex: 1">
                                <p class="m-0" style="letter-spacing: -1px;"><?= esc($cartCategory); ?>
                                </p>
                                <h1 class="nama-barang"><?= esc($cartProductName); ?></h1>
                            </div>
                            <p class="harga-hp">
                                Rp
                                <?= number_format($k['detail']['harga'] * (100 - $k['detail']['diskon']) / 100, 0, ',', '.'); ?>
                            </p>
                            <p class="m-0" style="font-size: 10px;"><?= esc(lang('Store.variantLabel')); ?>: <?= esc($cartVariant); ?></p>
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-2 align-items-end justify-content-between">
                        <form action="<?= esc(ilena_store_path('/deletecart/' . $index), 'attr'); ?>" method="post">
                            <?= csrf_field(); ?>
                    <button type="submit" class="btn-teks-aja m-0"><?= esc(lang('Store.delete')); ?></button>
                        </form>
                        <div class="number-control-hp">
                            <form action="<?= esc(ilena_store_path('/reducecart/' . $index), 'attr'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit">
                                    <div class="number-left"></div>
                                </button>
                            </form>
                            <div class="number-quantity-hp">
                                <?= $k['jumlah'] ?>
                            </div>
                            <form action="<?= esc(ilena_store_path('/addcart/' . $k['id_barang'] . '/' . rawurlencode($k['varian']) . '/1'), 'attr'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit">
                                    <div class="number-right"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

        </div>
        <div class="tigapuluh-ke-seratus">
            <div class="card p-4 ilena-cart-summary">
                <h4 style="letter-spacing: -1px"><?= esc(lang('Store.orderSummary')); ?></h4>
                <div class="mt-2 d-flex justify-content-between py-1">
                    <p class="m-0">
                        <?= esc(lang('Store.price')); ?>
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                    </p>
                </div>
                <!-- <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        Biaya Admin
                    </p>
                    <p class="fw-bold m-0">
                        Rp 5,000
                    </p>
                </div> -->
                <span class="garis my-2"></span>
                <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        <?= esc(lang('Store.total')); ?>
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                    </p>
                </div>
                <a <?= count($keranjang) > 0 ? 'href="' . esc(ilena_store_path('/address'), 'attr') . '"' : '' ?>
                    class="btn-default-merah <?= count($keranjang) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center"><?= esc(lang('Store.checkout')); ?></a>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>
