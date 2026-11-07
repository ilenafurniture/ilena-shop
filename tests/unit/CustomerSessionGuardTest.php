<?php

namespace Tests\Unit;

use App\Services\Auth\CustomerSessionGuard;
use CodeIgniter\Test\CIUnitTestCase;

final class CustomerSessionGuardTest extends CIUnitTestCase
{
    public function testPendingVerificationBypassIsLimitedToVerificationEndpoints(): void
    {
        foreach (['/verify', '/actionverify', '/kirimotp', '/en/verify', '/id/actionverify'] as $path) {
            $this->assertTrue(CustomerSessionGuard::isPendingVerificationPath($path), $path);
        }

        foreach (['/', '/account', '/en/account', '/kirimotp/extra', '/fakeverify'] as $path) {
            $this->assertFalse(CustomerSessionGuard::isPendingVerificationPath($path), $path);
        }
    }
}
