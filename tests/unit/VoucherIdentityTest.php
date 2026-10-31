<?php

namespace Tests\Unit;

use App\Services\Payments\VoucherIdentity;
use CodeIgniter\Test\CIUnitTestCase;

final class VoucherIdentityTest extends CIUnitTestCase
{
    public function testSameVerifiedCustomerAlwaysUsesAccountEmailForVoucherLimit(): void
    {
        self::assertSame('member@example.com', VoucherIdentity::resolve(
            'different-contact@example.com', true, '0', '1', ' Member@Example.com '
        ));
        self::assertSame('member@example.com', VoucherIdentity::resolve(
            'different-contact@example.com', true, '4', '1', ' MEMBER@example.com '
        ));
    }

    public function testGuestAndUnverifiedAccountUseNormalizedCheckoutEmail(): void
    {
        self::assertSame('guest@example.com', VoucherIdentity::resolve(
            ' Guest@Example.com ', false, '0', '1', 'member@example.com'
        ));
        self::assertSame('guest@example.com', VoucherIdentity::resolve(
            ' Guest@Example.com ', true, '0', '0', 'member@example.com'
        ));
    }

    public function testNonCustomerRolesNeverUseTheirSessionEmailForVoucherIdentity(): void
    {
        self::assertSame('buyer@example.com', VoucherIdentity::resolve(
            'Buyer@Example.com', true, '5', '1', 'admin@example.com'
        ));
    }
}
