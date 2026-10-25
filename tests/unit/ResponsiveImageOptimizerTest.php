<?php

namespace Tests\Unit;

use App\Services\ResponsiveImageOptimizer;
use PHPUnit\Framework\TestCase;

class ResponsiveImageOptimizerTest extends TestCase
{
    public function testItCreatesBoundedWebpFromAnImage(): void
    {
        if (!function_exists('imagewebp')) {
            self::markTestSkipped('GD WebP support is not available.');
        }

        $sourceImage = imagecreatetruecolor(800, 400);
        $colors = [
            imagecolorallocate($sourceImage, 139, 104, 76),
            imagecolorallocate($sourceImage, 244, 240, 235),
            imagecolorallocate($sourceImage, 41, 38, 34),
        ];
        imagefilledrectangle($sourceImage, 0, 0, 799, 399, $colors[0]);
        imagefilledrectangle($sourceImage, 160, 80, 639, 319, $colors[1]);
        imagefilledrectangle($sourceImage, 300, 130, 499, 269, $colors[2]);
        ob_start();
        imagejpeg($sourceImage, null, 92);
        $source = (string) ob_get_clean();
        imagedestroy($sourceImage);

        $optimized = (new ResponsiveImageOptimizer())->toWebp($source, 400, 78);
        self::assertIsString($optimized);
        $info = getimagesizefromstring($optimized);
        self::assertIsArray($info);
        self::assertSame('image/webp', $info['mime']);
        self::assertSame(400, $info[0]);
        self::assertSame(200, $info[1]);
        self::assertLessThan(strlen($source), strlen($optimized));
    }

    public function testItRejectsInvalidImageBytes(): void
    {
        self::assertNull((new ResponsiveImageOptimizer())->toWebp('not-an-image', 800));
    }
}
