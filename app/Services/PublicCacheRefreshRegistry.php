<?php

namespace App\Services;

use App\Controllers\Pages;
use App\Models\GambarHeaderModel;

/** Explicit allow-list for cache refresh jobs; requests cannot select arbitrary callables. */
final class PublicCacheRefreshRegistry
{
    public const NAMES = ['navbar', 'sitemap', 'home_slider', 'catalog_search'];

    public function refresh(string $name): void
    {
        switch ($name) {
            case 'navbar':
                (new Pages())->getNavbarData(true);
                return;
            case 'sitemap':
                (new Pages())->refreshPublicSitemapCache();
                return;
            case 'home_slider':
                (new GambarHeaderModel())->getPublicHomeSlides(true);
                return;
            case 'catalog_search':
                (new Pages())->refreshSmartCatalogSearchCache();
                return;
            default:
                throw new \InvalidArgumentException('Unknown public cache refresh name.');
        }
    }
}
