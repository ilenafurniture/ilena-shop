<?php

namespace Tests\Unit;

use App\Services\R2ProductImageService;
use CodeIgniter\Test\CIUnitTestCase;

final class R2ProductImageRedirectResponseTest extends CIUnitTestCase
{
    public function testRedirectHasNoHtmlBodyAndOnePublicCachePolicy(): void
    {
        $oldServerValue = $_SERVER['R2_PUBLIC_URL'] ?? null;
        $oldEnvValue = $_ENV['R2_PUBLIC_URL'] ?? null;
        $_SERVER['R2_PUBLIC_URL'] = 'https://cdn.example.test';
        $_ENV['R2_PUBLIC_URL'] = 'https://cdn.example.test';

        try {
            $response = (new R2ProductImageService())->redirectResponse(
                'products/300/example.webp',
                '1234567890',
            );

            $this->assertSame(302, $response->getStatusCode());
            $this->assertSame(
                'https://cdn.example.test/products/300/example.webp?v=1234567890',
                $response->getHeaderLine('Location'),
            );
            $this->assertSame('public, max-age=300', $response->getHeaderLine('Cache-Control'));
            $this->assertSame('', $response->getBody());
        } finally {
            if ($oldServerValue === null) {
                unset($_SERVER['R2_PUBLIC_URL']);
            } else {
                $_SERVER['R2_PUBLIC_URL'] = $oldServerValue;
            }

            if ($oldEnvValue === null) {
                unset($_ENV['R2_PUBLIC_URL']);
            } else {
                $_ENV['R2_PUBLIC_URL'] = $oldEnvValue;
            }
        }
    }
}
