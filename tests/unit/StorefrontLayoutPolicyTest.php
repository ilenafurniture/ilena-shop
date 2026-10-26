<?php

namespace Tests\Unit;

use App\Services\StorefrontLayoutPolicy;
use CodeIgniter\Test\CIUnitTestCase;

final class StorefrontLayoutPolicyTest extends CIUnitTestCase
{
    public function testCartUsesSameStorefrontShellAsProductAndHome(): void
    {
        $this->assertSame('full', StorefrontLayoutPolicy::headerMode('/cart'));
        $this->assertSame('store', StorefrontLayoutPolicy::footerMode('/cart'));
        $this->assertSame('full', StorefrontLayoutPolicy::headerMode('/product'));
        $this->assertSame('store', StorefrontLayoutPolicy::footerMode('/'));
    }

    public function testCheckoutStepsKeepCompactShell(): void
    {
        foreach (['/shipping', '/payment', '/successpay'] as $path) {
            $this->assertSame('checkout', StorefrontLayoutPolicy::headerMode($path));
            $this->assertSame('checkout', StorefrontLayoutPolicy::footerMode($path));
        }
    }

    public function testPathNormalizationAndOtherShellsRemainConsistent(): void
    {
        $this->assertSame('account', StorefrontLayoutPolicy::headerMode('/Account/'));
        $this->assertSame('account', StorefrontLayoutPolicy::footerMode('/account'));
        $this->assertSame('auth', StorefrontLayoutPolicy::headerMode('/login'));
        $this->assertSame('auth', StorefrontLayoutPolicy::footerMode('/register'));
        $this->assertTrue(StorefrontLayoutPolicy::isInternal('/admin/product'));
        $this->assertFalse(StorefrontLayoutPolicy::isInternal('/cart'));
    }
}
