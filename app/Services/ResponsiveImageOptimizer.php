<?php

namespace App\Services;

/**
 * Produces bounded, cacheable WebP renditions for storefront imagery.
 */
class ResponsiveImageOptimizer
{
    public function toWebp(string $source, int $maxWidth, int $quality = 78): ?string
    {
        if ($source === '' || $maxWidth < 1 || !function_exists('imagewebp')) {
            return null;
        }

        $info = @getimagesizefromstring($source);
        if (!is_array($info) || empty($info[0]) || empty($info[1])) {
            return null;
        }

        $sourceWidth = (int) $info[0];
        $sourceHeight = (int) $info[1];
        if ($sourceWidth < 1 || $sourceHeight < 1 || ($sourceWidth * $sourceHeight) > 40000000) {
            return null;
        }

        $scale = min(1, $maxWidth / $sourceWidth);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $sourceImage = @imagecreatefromstring($source);
        if ($sourceImage === false) {
            return null;
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($targetImage === false) {
            imagedestroy($sourceImage);
            return null;
        }

        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        ob_start();
        $saved = imagewebp($targetImage, null, max(45, min(90, $quality)));
        $optimized = ob_get_clean();
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $saved && is_string($optimized) && $optimized !== '' ? $optimized : null;
    }
}
