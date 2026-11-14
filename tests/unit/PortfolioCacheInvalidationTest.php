<?php

namespace Tests\Unit;

use App\Services\PortfolioService;
use CodeIgniter\Test\CIUnitTestCase;

final class PortfolioCacheInvalidationTest extends CIUnitTestCase
{
    public function testPortfolioUpdatesRemoveStaleCachedVisibilityImmediately(): void
    {
        $cache = cache();
        $key = PortfolioService::CACHE_KEY;
        $staleEnvelope = [
            '__ilena_public_cache' => 1,
            'value' => ['enabled' => false, 'projects' => []],
            'fresh_until' => time() - 1,
            'stale_until' => time() + 1200,
        ];

        try {
            $cache->save($key, $staleEnvelope, 1200);
            (new PortfolioService())->invalidate();
            $this->assertNull($cache->get($key), 'A just-changed portfolio setting must not serve the stale page state.');
        } finally {
            $cache->delete($key);
        }
    }
}
