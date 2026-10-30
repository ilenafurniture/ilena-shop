<?php

namespace Tests\Unit;

use App\Services\StorefrontLayoutPolicy;
use CodeIgniter\Test\CIUnitTestCase;

final class StorefrontLayoutPolicyTest extends CIUnitTestCase
{
    public function testCartKeepsStorefrontHeaderButUsesCompactFooter(): void
    {
        $this->assertSame('full', StorefrontLayoutPolicy::headerMode('/cart'));
        $this->assertSame('checkout', StorefrontLayoutPolicy::footerMode('/cart'));
        $this->assertSame('full', StorefrontLayoutPolicy::headerMode('/product'));
        $this->assertSame('store', StorefrontLayoutPolicy::footerMode('/'));
    }

    public function testCheckoutStepsKeepCompactShell(): void
    {
        foreach (['/shipping', '/shipping/123', '/payment', '/payment/method/123/visa', '/progresspay', '/successpay', '/cencelpay', '/expirepay'] as $path) {
            $this->assertSame('checkout', StorefrontLayoutPolicy::headerMode($path));
            $this->assertSame('checkout', StorefrontLayoutPolicy::footerMode($path));
        }
    }

    public function testAllStorefrontShellFamiliesStayInTheirOwnLayout(): void
    {
        foreach (['/', '/product', '/find', '/article', '/partner'] as $path) {
            $this->assertSame('full', StorefrontLayoutPolicy::headerMode($path), $path);
            $this->assertSame('store', StorefrontLayoutPolicy::footerMode($path), $path);
        }

        $this->assertSame('full', StorefrontLayoutPolicy::headerMode('/cart'), '/cart');
        $this->assertSame('checkout', StorefrontLayoutPolicy::footerMode('/cart'), '/cart');

        foreach (['/login', '/register', '/verify'] as $path) {
            $this->assertSame('auth', StorefrontLayoutPolicy::headerMode($path), $path);
            $this->assertSame('auth', StorefrontLayoutPolicy::footerMode($path), $path);
        }

        foreach (['/account', '/address', '/order', '/orderdetail/123', '/invoice/123', '/wishlist'] as $path) {
            $this->assertSame('account', StorefrontLayoutPolicy::headerMode($path), $path);
            $this->assertSame('account', StorefrontLayoutPolicy::footerMode($path), $path);
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

    public function testLocalizedPathsUseTheSameShellPolicy(): void
    {
        foreach (['/en/cart', '/id/cart'] as $path) {
            $this->assertSame('full', StorefrontLayoutPolicy::headerMode($path), $path);
            $this->assertSame('checkout', StorefrontLayoutPolicy::footerMode($path), $path);
        }

        foreach (['/en/login', '/id/register'] as $path) {
            $this->assertSame('auth', StorefrontLayoutPolicy::headerMode($path), $path);
            $this->assertSame('auth', StorefrontLayoutPolicy::footerMode($path), $path);
        }

        foreach (['/en/account', '/id/wishlist'] as $path) {
            $this->assertSame('account', StorefrontLayoutPolicy::headerMode($path), $path);
            $this->assertSame('account', StorefrontLayoutPolicy::footerMode($path), $path);
        }

        $this->assertSame('/', StorefrontLayoutPolicy::normalizePath('/en'));
        $this->assertSame('/partner', StorefrontLayoutPolicy::normalizePath('/en/partner/'));
    }
}
