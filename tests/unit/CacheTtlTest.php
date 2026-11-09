<?php

namespace Tests\Unit;

use App\Services\CacheTtl;
use CodeIgniter\Test\CIUnitTestCase;

final class CacheTtlTest extends CIUnitTestCase
{
    public function testJitterNeverExtendsConfiguredTtl(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $ttl = CacheTtl::jitter(1000);
            $this->assertGreaterThanOrEqual(900, $ttl);
            $this->assertLessThanOrEqual(1000, $ttl);
        }
    }

    public function testJitterKeepsTinyTtlsPositiveAndCapsSpread(): void
    {
        $this->assertSame(1, CacheTtl::jitter(1, 0.25));
        $this->assertSame(1, CacheTtl::jitter(0, 0.25));

        for ($i = 0; $i < 100; $i++) {
            $ttl = CacheTtl::jitter(1000, 1.0);
            $this->assertGreaterThanOrEqual(750, $ttl);
            $this->assertLessThanOrEqual(1000, $ttl);
        }
    }
}
