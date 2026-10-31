<?php
$activeLocale = ilena_current_store_locale();
$currentPath = trim(service('uri')->getPath(), '/');
$pathWithoutLocale = preg_replace('#^(?:id|en)(?:/|$)#', '', $currentPath);
$pathWithoutLocale = trim((string) $pathWithoutLocale, '/');
$queryString = service('request')->getUri()->getQuery();
$languageReturnUrl = '/' . $pathWithoutLocale . ($queryString !== '' ? '?' . $queryString : '');
$idLanguageUrl = '/language/id?' . http_build_query(['return' => $languageReturnUrl]);
$enLanguageUrl = '/language/en?' . http_build_query(['return' => $languageReturnUrl]);
?>
<div class="navbar-hp-atas hide-ke-show-flex">
    <a href="<?= esc($activeLocale === 'en' ? '/en' : '/', 'attr'); ?>" aria-label="Ilena Furniture — <?= esc(lang('Store.home'), 'attr'); ?>">
        <img src="<?= base_url('/img/logo/N.png'); ?>" alt="Ilena Furniture" width="36" height="36" decoding="async">
    </a>
    <div class="ilena-mobile-sister-brands" aria-label="Ilena group brands">
        <a class="ilena-mobile-lunarea-link" href="https://lunareafurniture.com" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(lang('Store.visitLunarea'), 'attr'); ?>">
            <img src="<?= base_url('img/logolunareanavbar.webp'); ?>" width="112" height="24" alt="Lunarea Furniture">
        </a>
        <a class="ilena-mobile-interior-link" href="https://ilenafurniture.net/" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(lang('Store.customInterior'), 'attr'); ?>">
            <img src="<?= base_url('img/logo/logoilenainterior-v2.png'); ?>" width="110" height="29" alt="Ilena Interior">
        </a>
    </div>
    <div class="ilena-mobile-search-slot">
        <form action="<?= esc($activeLocale === 'en' ? '/en/find' : '/find', 'attr'); ?>" method="get" class="w-100 ilena-global-search" data-global-search role="search">
            <label class="ilena-sr-only" for="global-search-mobile"><?= esc(lang('Store.searchLabel')); ?></label>
            <input id="global-search-mobile" placeholder="<?= esc(lang('Store.searchPlaceholder'), 'attr'); ?>" class="input w-100" name="q"
                type="search" autocomplete="off" autocapitalize="off" aria-autocomplete="list" aria-expanded="false" aria-controls="global-search-mobile-listbox">
            <div class="ilena-search-suggestions" id="global-search-mobile-listbox" role="listbox" hidden></div>
        </form>
    </div>
    <div style="width: fit-content">
        <input id="n-logo" type="checkbox" class="ilena-mobile-menu-checkbox" aria-controls="mobile-menu-drawer">
        <label for="n-logo" class="ilena-mobile-menu-button" aria-label="<?= esc(lang('Store.menuToggle'), 'attr'); ?>">
            <div class="menu-icon">
                <i class="material-icons" aria-hidden="true">menu</i>
            </div>
            <div class="close-icon">
                <i class="material-icons" aria-hidden="true">close</i>
            </div>
        </label>
        <div class="expend-hp" id="mobile-menu-drawer" role="navigation" aria-label="<?= esc(lang('Store.collectionMenu'), 'attr'); ?>">
            <nav class="ilena-mobile-primary-links" aria-label="<?= esc(lang('Store.primaryNavigation'), 'attr'); ?>">
                <a href="<?= esc($activeLocale === 'en' ? '/en/product' : '/product', 'attr'); ?>"><?= esc(lang('Store.collection')); ?></a>
                <a href="<?= esc($activeLocale === 'en' ? '/en/partner' : '/partner', 'attr'); ?>"><?= esc(lang('Store.partnerStore')); ?></a>
                <a href="<?= esc($activeLocale === 'en' ? '/en/article' : '/article', 'attr'); ?>"><?= esc(lang('Store.article')); ?></a>
                <a href="https://wa.me/628112938158?text=<?= rawurlencode(lang('Store.whatsAppContactMessage')); ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('Store.contactUs')); ?></a>
            </nav>
            <nav class="ilena-mobile-language-switch" aria-label="<?= esc(lang('Store.language'), 'attr'); ?>">
                <a href="<?= esc($idLanguageUrl, 'attr'); ?>" lang="id" hreflang="id"<?= $activeLocale === 'id' ? ' aria-current="page"' : ''; ?>>Indonesia</a>
                <a href="<?= esc($enLanguageUrl, 'attr'); ?>" lang="en" hreflang="en"<?= $activeLocale === 'en' ? ' aria-current="page"' : ''; ?>>English</a>
            </nav>
            <p class="mb-2" style="color:white; font-size: 10px"><?= esc(lang('Store.collection')); ?></p>
            <div>
                <input type="radio" name="collection" id="collection1" class="item-collection">
                <label for="collection1" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=bookshelf'), 'attr'); ?>">Bookshelf</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=bookshelf'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=bookshelf'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=bookshelf'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=bookshelf'), 'attr'); ?>">Sorely</a>
                </div>
            </div>
            <div>
                <input type="radio" name="collection" id="collection2" class="item-collection">
                <label for="collection2" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=coffee-table'), 'attr'); ?>">Coffee Table</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=coffee-table'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=coffee-table'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=coffee-table'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=coffee-table'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=coffee-table'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=coffee-table'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=coffee-table'), 'attr'); ?>">Socoplate
                    </a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=coffee-table'), 'attr'); ?>">Sorely</a>
                </div>
            </div>
            <div>
                <input type="radio" name="collection" id="collection3" class="item-collection">
                <label for="collection3" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=console-table'), 'attr'); ?>">Console Table</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=console-table'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=console-table'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=coffee-table'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=console-table'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=console-table'), 'attr'); ?>">Metal Frame
                    </a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=console-table'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=console-table'), 'attr'); ?>">Plint Base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=console-table'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=console-table'), 'attr'); ?>">Sorely</a>
                </div>
            </div>
            <div>
                <input type="radio" name="collection" id="collection4" class="item-collection">
                <label for="collection4" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=credenza'), 'attr'); ?>">Credenza</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=credenza'), 'attr'); ?>">Industrial</a>
                </div>
            </div>
            <div>
                <input type="radio" name="collection" id="collection5" class="item-collection">
                <label for="collection5" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=dresser-3-drawer'), 'attr'); ?>">Dresser 3</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=dresser-3-drawer'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=dresser-3-drawer'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=dresser-3-drawer'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=dresser-3-drawer'), 'attr'); ?>">Sorely</a>
                </div>
            </div>
            <div>
                <input type="radio" name="collection" id="collection6" class="item-collection">
                <label for="collection6" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=dresser-4-drawer'), 'attr'); ?>">Dresser 4</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=dresser-4-drawer'), 'attr'); ?>">Cody</a>
                </div>
            </div>
            <div>
                <input type="radio" name="collection" id="collection7" class="item-collection">
                <label for="collection7" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=dresser-5-drawer'), 'attr'); ?>">Dresser 5</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=dresser-5-drawer'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=dresser-5-drawer'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=dresser-5-drawer'), 'attr'); ?>">Socoplate</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection8" class="item-collection">
                <label for="collection8" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=dresser-6-drawer'), 'attr'); ?>">Dresser 6</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=dresser-6-drawer'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=dresser-6-drawer'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=dresser-6-drawer'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=dresser-6-drawer'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=dresser-6-drawer'), 'attr'); ?>">Plint Base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=water-case&jenis=dresser-6-drawer'), 'attr'); ?>">Water Case</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection9" class="item-collection">
                <label for="collection9" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=king-bed'), 'attr'); ?>">King Bed</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=king-bed'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=king-bed'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=king-bed'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=king-bed'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=king-bed'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=king-bed'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=king-bed'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=king-bed'), 'attr'); ?>">Plint base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=king-bed'), 'attr'); ?>">Sorely</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=water-case&jenis=king-bed'), 'attr'); ?>">Water Case</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection10" class="item-collection">
                <label for="collection10" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=queen-bed'), 'attr'); ?>">Queen Bed</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=queen-bed'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=queen-bed'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=queen-bed'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=queen-bed'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=queen-bed'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=queen-bed'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=queen-bed'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=queen-bed'), 'attr'); ?>">Plint base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=queen-bed'), 'attr'); ?>">Sorely</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=water-case&jenis=queen-bed'), 'attr'); ?>">Water Case</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection11" class="item-collection">
                <label for="collection11" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=single-bed'), 'attr'); ?>">Single Bed</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=single-bed'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=single-bed'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=single-bed'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=single-bed'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=single-bed'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=single-bed'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=single-bed'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=single-bed'), 'attr'); ?>">Plint base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=single-bed'), 'attr'); ?>">Sorely</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=water-case&jenis=single-bed'), 'attr'); ?>">Water Case</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection12" class="item-collection">
                <label for="collection12" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=side-table'), 'attr'); ?>">Side Table</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=side-table'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=side-table'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=side-table'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=side-table'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=side-table'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=side-table'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=side-table'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=side-table'), 'attr'); ?>">Plint base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=side-table'), 'attr'); ?>">Sorely</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=water-case&jenis=side-table'), 'attr'); ?>">Water Case</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection13" class="item-collection">
                <label for="collection13" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=meja-nakas'), 'attr'); ?>"><?= esc(ilena_catalog_type_label('Meja Nakas')); ?></a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=meja-nakas'), 'attr'); ?>">Sorely</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection14" class="item-collection">
                <label for="collection14" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=bufet-tv'), 'attr'); ?>"><?= esc(ilena_catalog_type_label('Bufet TV')); ?></a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cabana&jenis=bufet-tv'), 'attr'); ?>">Cabana</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=bufet-tv'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cutout&jenis=bufet-tv'), 'attr'); ?>">Cutout</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=bufet-tv'), 'attr'); ?>">Industrial</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=metal-frame&jenis=bufet-tv'), 'attr'); ?>">Metal Frame</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=orca&jenis=bufet-tv'), 'attr'); ?>">Orca</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=plint-base&jenis=bufet-tv'), 'attr'); ?>">Plint base</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=bufet-tv'), 'attr'); ?>">Sorely</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=water-case&jenis=bufet-tv'), 'attr'); ?>">Water Case</a>
                </div>
            </div>

            <div>
                <input type="radio" name="collection" id="collection15" class="item-collection">
                <label for="collection15" class="collectionjdl">
                    <a class="m-0 fw-bold" href="<?= esc(ilena_store_path('/product?jenis=wardrobe'), 'attr'); ?>">Wardrobe</a>
                    <i class="material-icons">arrow_forward_ios</i>
                </label>
                <div class="container-collection">
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=cody&jenis=wardrobe'), 'attr'); ?>">Cody</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=socoplate&jenis=wardrobe'), 'attr'); ?>">Socoplate</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=sorely&jenis=wardrobe'), 'attr'); ?>">Sorely</a>
                    <a class="w-100 d-block" style="text-decoration: none; font-size:16px;"
                        href="<?= esc(ilena_store_path('/product?koleksi=industrial&jenis=wardrobe'), 'attr'); ?>">Industrial</a>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="navbar-hp hide-ke-show-flex">
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $pathWithoutLocale === '' ? 'active' : ''; ?>" href="<?= esc(ilena_store_path('/'), 'attr'); ?>" aria-label="<?= esc(lang('Store.home'), 'attr'); ?>" title="<?= esc(lang('Store.home'), 'attr'); ?>" <?= $pathWithoutLocale === '' ? 'aria-current="page"' : ''; ?>>
            <i class="material-icons" aria-hidden="true">home</i><span><?= esc(lang('Store.home')); ?></span>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $pathWithoutLocale === 'wishlist' ? 'active' : ''; ?>" href="<?= esc(ilena_store_path('/wishlist'), 'attr'); ?>" aria-label="<?= esc(lang('Store.favorites'), 'attr'); ?>" title="<?= esc(lang('Store.favorites'), 'attr'); ?>" <?= $pathWithoutLocale === 'wishlist' ? 'aria-current="page"' : ''; ?>>
            <i class="material-icons" aria-hidden="true">favorite_border</i><span><?= esc(lang('Store.favorites')); ?></span>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= $pathWithoutLocale === 'cart' ? 'active' : ''; ?>" href="<?= esc(ilena_store_path('/cart'), 'attr'); ?>" aria-label="<?= esc(lang('Store.cartTitle'), 'attr'); ?>" title="<?= esc(lang('Store.cartTitle'), 'attr'); ?>" <?= $pathWithoutLocale === 'cart' ? 'aria-current="page"' : ''; ?>>
            <i class="material-icons" aria-hidden="true">shopping_bag</i><span><?= esc(lang('Store.cartTitle')); ?></span>
        </a>
    </div>
    <div style="flex:1;" class="d-flex justify-content-center align-content-center">
        <a class="item-navhp <?= in_array($pathWithoutLocale, ['account', 'login'], true) ? 'active' : ''; ?>"
            href="<?= esc(ilena_store_path(session()->get('isLogin') ? '/account' : '/login'), 'attr'); ?>" aria-label="<?= esc(session()->get('isLogin') ? lang('Store.myAccount') : lang('Store.signIn'), 'attr'); ?>" title="<?= esc(session()->get('isLogin') ? lang('Store.myAccount') : lang('Store.signIn'), 'attr'); ?>" <?= in_array($pathWithoutLocale, ['account', 'login'], true) ? 'aria-current="page"' : ''; ?>>
            <i class="material-icons" aria-hidden="true">person_outline</i><span><?= esc(session()->get('isLogin') ? lang('Store.accountTitle') : lang('Store.signIn')); ?></span>
        </a>
    </div>

</div>
