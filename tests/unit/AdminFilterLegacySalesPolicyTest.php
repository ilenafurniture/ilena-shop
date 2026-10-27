<?php

use App\Filters\AdminFilter;
use CodeIgniter\Test\CIUnitTestCase;

final class AdminFilterLegacySalesPolicyTest extends CIUnitTestCase
{
    public function testLegacyOfflineAndInteriorRoutesAreRecognizedIncludingActions(): void
    {
        $filter = new AdminFilter();
        $method = new ReflectionMethod(AdminFilter::class, 'isWebsiteLocalSalesPath');
        $method->setAccessible(true);

        foreach ([
            '/admin/order/offline/sale',
            'admin/order-offline/add',
            '/admin/project-interior/ABC123/payment',
            '/admin/surat-jalan/offline/12/finalize',
            '/admin/invoice-offline/ORDER-1',
        ] as $path) {
            $this->assertTrue($method->invoke($filter, $path), $path . ' should be a legacy local-sales route');
        }
    }

    public function testOnlineOrderAndOtherAdminRoutesRemainAvailable(): void
    {
        $filter = new AdminFilter();
        $method = new ReflectionMethod(AdminFilter::class, 'isWebsiteLocalSalesPath');
        $method->setAccessible(true);

        foreach ([
            '/admin/order/online',
            '/admin/product',
            '/admin/voucher',
            '/admin/customers',
        ] as $path) {
            $this->assertFalse($method->invoke($filter, $path), $path . ' should remain outside the disabled modules');
        }
    }
}
