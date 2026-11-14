<?php

use App\Services\AdminRbacService;
use App\Services\PortfolioService;
use App\Services\StorefrontLayoutPolicy;
use PHPUnit\Framework\TestCase;

final class PortfolioFeatureWiringTest extends TestCase
{
    public function testPortfolioRoutesUseContentPermissionAndPublicEditorialShell(): void
    {
        $rbac = new AdminRbacService();
        $this->assertSame('content', $rbac->routePermission('admin/portfolio'));
        $this->assertSame('content', $rbac->routePermission('admin/portfolio/delete/12'));
        $this->assertSame('content', $rbac->routePermission('admin/portfolio/visibility'));
        $this->assertSame('/portfolio', StorefrontLayoutPolicy::normalizePath('/en/portfolio'));
        $this->assertSame('full', StorefrontLayoutPolicy::headerMode('/portfolio'));
    }

    public function testPortfolioUsesAStableSharedCacheIdentity(): void
    {
        $this->assertSame('ilena_public_portfolio_v2', PortfolioService::CACHE_KEY);
        $this->assertSame('portfolio', PortfolioService::REFRESH_NAME);
        $this->assertSame('portfolio_page_enabled', PortfolioService::PAGE_SETTING_KEY);
        $this->assertContains(PortfolioService::REFRESH_NAME, \App\Services\PublicCacheRefreshRegistry::NAMES);
    }

    public function testPortfolioIsLinkedInBothStorefrontHeadersAndLocales(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');
        $desktop = file_get_contents(APPPATH . 'Views/layout/navbar.php');
        $mobile = file_get_contents(APPPATH . 'Views/layout/navbarHp.php');
        $this->assertStringContainsString('$routes->get(\'/portfolio\', \'Pages::portfolio\'', $routes);
        $this->assertStringContainsString('$routes->get(\'portfolio\', \'Pages::portfolio\'', $routes);
        $this->assertStringContainsString("'/en/portfolio' : '/portfolio'", $desktop);
        $this->assertStringContainsString("'/en/portfolio' : '/portfolio'", $mobile);
    }

    public function testEmptyPortfolioShowsBilingualAnimatedComingSoonDesign(): void
    {
        $view = file_get_contents(APPPATH . 'Views/pages/portfolio.php');
        $this->assertStringContainsString('if (!$pageEnabled)', $view);
        $this->assertStringContainsString('if (!$projects)', $view);
        $this->assertStringContainsString('COMING SOON', $view);
        $this->assertStringContainsString('SEGERA HADIR', $view);
        $this->assertStringContainsString('portfolio-empty-title', $view);
        $this->assertStringContainsString('Halaman Portofolio sudah aktif.', $view);
        $this->assertStringContainsString('class="portfolio-art-furniture"', $view);
        $this->assertStringContainsString('portfolio-art-furniture{stroke:', $view);
        $this->assertStringContainsString('@media(prefers-reduced-motion:reduce){.ilena-portfolio__coming', $view);
        $this->assertStringNotContainsString('ilena-portfolio__preview-grid', $view);
    }

    public function testPortfolioMotionUsesNativeBrowserFeaturesAndHonorsReducedMotion(): void
    {
        $view = file_get_contents(APPPATH . 'Views/pages/portfolio.php');
        $this->assertStringContainsString('new IntersectionObserver', $view);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $view);
        $this->assertStringContainsString('data-portfolio-reveal="media"', $view);
        $this->assertStringContainsString('portfolio-motion-ready', $view);
        $this->assertStringContainsString('transition:transform .75s', $view);
        $this->assertStringNotContainsString('<script src="https://', $view);
    }

    public function testAdminPortfolioEditorHasLocalLivePreviewAndLanguageSwitcher(): void
    {
        $view = file_get_contents(APPPATH . 'Views/admin/portfolio.php');
        $this->assertStringContainsString('data-portfolio-editor', $view);
        $this->assertStringContainsString('portfolio-admin__preview-frame', $view);
        $this->assertStringContainsString('data-preview-locale="id"', $view);
        $this->assertStringContainsString('data-preview-locale="en"', $view);
        $this->assertStringContainsString('data-preview-cover', $view);
        $this->assertStringContainsString('data-preview-gallery-grid', $view);
        $this->assertStringContainsString("form.addEventListener('input', update)", $view);
        $this->assertStringContainsString('new URL(value)', $view);
        $this->assertStringContainsString('Jika belum ada proyek aktif', $view);
    }
}
