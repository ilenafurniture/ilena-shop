<?php

use App\Services\AdminRbacService;
use CodeIgniter\Test\CIUnitTestCase;

final class AdminRbacRolePolicyTest extends CIUnitTestCase
{
    public function testOfflinePrefixesAndAdministrativeAliasesAreMapped(): void
    {
        $rbac=new AdminRbacService();
        $this->assertSame('orders_offline',$rbac->routePermission('admin/order/offline/sale'));
        $this->assertSame('project_interior',$rbac->routePermission('admin/order/offline/interior'));
        $this->assertSame('content',$rbac->routePermission('editarticle/1'));
        $this->assertSame('products',$rbac->routePermission('changepic/1'));
        $this->assertSame('products',$rbac->routePermission('gantilokasi/1'));
        $this->assertSame('products', $rbac->routePermission('admin/changepic'));
        $this->assertSame('products', $rbac->routePermission('admin/menu-previews'));
        $this->assertSame('products', $rbac->routePermission('admin/menu-previews/save'));
        $this->assertSame('orders_online',$rbac->routePermission('cancelorder/1'));
    }

    public function testAllPreviouslyHiddenOperationalModulesHaveExplicitPermissions(): void
    {
        $rbac = new AdminRbacService();
        $this->assertSame('orders_offline', $rbac->routePermission('admin/order/offline/sale'));
        $this->assertSame('project_interior', $rbac->routePermission('admin/project-interior'));
        $this->assertSame('mutasi', $rbac->routePermission('admin/mutasi'));
        $this->assertSame('spam_cleanup', $rbac->routePermission('admin/spam-cleanup'));
        $this->assertSame('marketplace', $rbac->routePermission('market/product'));
        $this->assertArrayNotHasKey('rbac', $rbac->permissions());
    }

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

    public function testSharedShipmentPermissionIsResolvedBySourceWithoutTextColumnJoin(): void
    {
        $db = \Config\Database::connect();
        foreach (['admin_rbac_user_roles', 'admin_rbac_roles', 'surat_jalan', 'project_interior'] as $table) {
            if ($db->tableExists($table)) {
                $db->query('DROP TABLE ' . $db->protectIdentifiers($table));
            }
        }
        $prefix = $db->getPrefix();
        $db->query('CREATE TABLE ' . $prefix . 'surat_jalan (id INTEGER PRIMARY KEY, id_pesanan TEXT)');
        $db->query('CREATE TABLE ' . $prefix . 'project_interior (id INTEGER PRIMARY KEY, kode_sj TEXT)');
        $db->table('surat_jalan')->insertBatch([
            ['id' => 10, 'id_pesanan' => 'OFFLINE-10'],
            ['id' => 20, 'id_pesanan' => 'INTERIOR-20'],
        ]);
        $db->table('project_interior')->insert(['id' => 1, 'kode_sj' => 'INTERIOR-20']);

        $rbac = new AdminRbacService();
        $this->assertTrue($rbac->saveRole(null, 'Offline Staff', ['orders_offline']));
        $this->assertTrue($rbac->assignRole('offline@example.test', 1));
        $this->assertTrue($rbac->saveRole(null, 'Interior Staff', ['project_interior']));
        $this->assertTrue($rbac->assignRole('interior@example.test', 2));
        session()->set('role', '5');

        $this->assertTrue($rbac->canAccessPath('offline@example.test', 'admin/surat-jalan/offline/10/edit'));
        $this->assertFalse($rbac->canAccessPath('offline@example.test', 'admin/surat-jalan/offline/20/edit'));
        $this->assertTrue($rbac->canAccessPath('interior@example.test', 'admin/surat-jalan/offline/20/edit'));
        $this->assertFalse($rbac->canAccessPath('interior@example.test', 'admin/surat-jalan/offline/10/edit'));
    }

    public function testPrivilegedSessionMustMatchCurrentActiveDatabaseAccount(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('user')) {
            $db->query('DROP TABLE ' . $db->protectIdentifiers('user'));
        }
        $db->query('CREATE TABLE ' . $db->getPrefix() . 'user (email TEXT PRIMARY KEY, role TEXT, active TEXT)');
        $db->table('user')->insertBatch([
            ['email' => 'staff@example.test', 'role' => '5', 'active' => '1'],
            ['email' => 'disabled@example.test', 'role' => '5', 'active' => '0'],
            ['email' => 'demoted@example.test', 'role' => '0', 'active' => '1'],
        ]);

        $rbac = new AdminRbacService();
        $this->assertTrue($rbac->sessionAccountMatchesDatabase('STAFF@example.test', '5'));
        $this->assertFalse($rbac->sessionAccountMatchesDatabase('disabled@example.test', '5'));
        $this->assertFalse($rbac->sessionAccountMatchesDatabase('demoted@example.test', '5'));
        $this->assertFalse($rbac->sessionAccountMatchesDatabase('missing@example.test', '5'));
        $this->assertFalse($rbac->sessionAccountMatchesDatabase('staff@example.test', '1'));
    }
}
