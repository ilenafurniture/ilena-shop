<?php

use CodeIgniter\Test\CIUnitTestCase;

final class SeoLocalizationTest extends CIUnitTestCase
{
    public function testSharedMetadataUsesActiveLanguageAndLocalizedAlternates(): void
    {
        $template = file_get_contents(APPPATH . 'Views/layout/template.php');

        $this->assertStringContainsString('Explore Ilena Furniture collections, product information, and services', $template);
        $this->assertStringContainsString('metaAlternateUrls[\'en\']', $template);
        $this->assertStringContainsString('twitter:image:alt', $template);
        $this->assertStringContainsString('produk[\'deskripsi\'][\'deskripsi_en\']', $template);
        $this->assertStringContainsString('ilena_product_display_name($produk', $template);
    }

    public function testSitemapHasLocalizedPublicUrlsAndLegacyFileIsRewrittenToController(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Pages.php');
        $rewrite = file_get_contents(FCPATH . '.htaccess');

        $this->assertStringContainsString('xmlns:xhtml="http://www.w3.org/1999/xhtml"', $controller);
        $this->assertStringContainsString('hreflang="x-default"', $controller);
        $this->assertStringContainsString("ilena_public_sitemap_v3", $controller);
        $this->assertStringContainsString('RewriteRule ^sitemap\\.xml$ index.php/sitemap.xml', $rewrite);
    }

    public function testHomepageAndArticleTemplatesHaveOnePrimaryHeading(): void
    {
        foreach (['home.php', 'artikel.php', 'artikelAll.php'] as $view) {
            $html = file_get_contents(APPPATH . 'Views/pages/' . $view);
            preg_match_all('/<h1\\b/i', $html, $matches);
            $this->assertCount(1, $matches[0], $view . ' should expose one primary H1 heading.');
        }
    }

    public function testProductCollectionPagesHaveLocalizedMetadataAndAlternates(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Pages.php');

        $this->assertStringContainsString("'industrial' => ['Industrial'", $controller);
        $this->assertStringContainsString("'cody' => ['Cody'", $controller);
        $this->assertStringContainsString("'orca' => ['Orca'", $controller);
        $this->assertStringContainsString("'metaAlternateUrls' => [", $controller);
        $this->assertStringNotContainsString("'keywords' => ['Cody Ilena", $controller);
    }

    public function testSitemapToleratesMissingOptionalArticleTranslationColumns(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Pages.php');

        $this->assertStringContainsString('db_connect()->fieldExists($localizedColumn, \'artikel\')', $controller);
        $this->assertStringContainsString('$this->artikelModel->select($articleColumns)->findAll()', $controller);
    }

    public function testCollectionLandingProvidesSharedCatalogData(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Pages.php');
        $start = strpos($controller, 'public function productCategory(');
        $end = strpos($controller, 'public function cart(', $start);
        $method = substr($controller, $start, $end - $start);

        $this->assertStringContainsString("'koleksiJenis' => [", $method);
        $this->assertStringContainsString('\'jenis\' => $jenis', $method);
    }

    public function testSingleCollectionFilterGetsSpecificTitleWhileRemainingNonIndexable(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Pages.php');
        $layout = file_get_contents(APPPATH . 'Views/layout/template.php');

        $this->assertStringContainsString('$singleCollectionName . \' Furniture Collection\'', $controller);
        $this->assertStringContainsString('\'Furniture \' . $singleCollectionName', $controller);
        $this->assertStringContainsString('$localeNeutralPath === \'product\' && $requestQuery !== []', $layout);
    }

    public function testToastTemplatesDoNotShipDemoPlaceholderCopy(): void
    {
        foreach ([APPPATH . 'Views/layout/template.php', APPPATH . 'Views/admin/template.php'] as $templatePath) {
            $template = file_get_contents($templatePath);
            $this->assertStringNotContainsString('Hello, world! This is a toast message.', $template);
            $this->assertStringContainsString('aria-live="polite" aria-atomic="true"', $template);
        }
    }
}
