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
}
