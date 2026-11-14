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
        $this->assertSame('ilena_public_portfolio_v1', PortfolioService::CACHE_KEY);
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

    public function testEnabledEmptyPortfolioShowsBilingualLayoutPreviewInsteadOfBlankState(): void
    {
        $view = file_get_contents(APPPATH . 'Views/pages/portfolio.php');
        $this->assertStringContainsString('ilena-portfolio__preview', $view);
        $this->assertStringContainsString('PRATINJAU LAYOUT', $view);
        $this->assertStringContainsString('LAYOUT PREVIEW', $view);
        $this->assertStringContainsString('KONTEN PROYEK AKAN TAMPIL DI SINI', $view);
        $this->assertStringContainsString('PROJECT CONTENT WILL APPEAR HERE', $view);
        $this->assertStringContainsString('@media(max-width:760px){.ilena-portfolio__preview', $view);
        $this->assertStringContainsString('@media(prefers-reduced-motion:reduce){.ilena-portfolio__preview', $view);
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
    }
}
