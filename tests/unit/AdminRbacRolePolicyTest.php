<?php

use App\Services\AdminRbacService;
use CodeIgniter\Test\CIUnitTestCase;

final class AdminRbacRolePolicyTest extends CIUnitTestCase
{
    public function testMarketplaceAndShopOrderAccessUseExplicitPermissions(): void
    {
        $rbac = new AdminRbacService();

        $this->assertSame('marketplace', $rbac->routePermission('market/product'));
        $this->assertSame('marketplace', $rbac->routePermission('market/cart'));
        $this->assertSame('orders_online', $rbac->routePermission('admin/ordertoko/0'));
        $this->assertSame('dashboard', $rbac->routePermission('admin'));
        $this->assertSame('content', $rbac->routePermission('admin/partners/12'));
        $this->assertSame('products', $rbac->routePermission('admin/product-bulk-edit'));
        $this->assertArrayHasKey('marketplace', $rbac->permissions());
        $this->assertNotContains('meta_capi', $rbac->operationalAdminPermissions());
        $this->assertNotContains('spam_cleanup', $rbac->operationalAdminPermissions());
        $this->assertNotContains('rbac', $rbac->operationalAdminPermissions());
    }

    public function testCustomStaffCannotFallThroughToUnmappedAdminRoutes(): void
    {
        $rbac = new AdminRbacService();
        session()->set('role', '5');
        $this->assertFalse($rbac->canAccessPath('staff@example.test', 'admin/unmapped-action'));
        $this->assertFalse($rbac->canAccessPath('staff@example.test', 'admin/rbac'));

        session()->set('role', '1');
        $this->assertTrue($rbac->canAccessPath('admin@example.test', 'admin/unmapped-action'));
        $this->assertTrue($rbac->canAccessPath('admin@example.test', 'admin/rbac'));
    }
}
