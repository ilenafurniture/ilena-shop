<?php

namespace Tests\Unit;

use App\Services\HomeAssetManager;
use CodeIgniter\HTTP\Files\UploadedFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class HomeAssetManagerTest extends TestCase
{
    public function testCatalogContainsOnlyKnownHomeSlots(): void
    {
        $catalog = (new HomeAssetManager())->catalog();

        self::assertCount(42, $catalog);
        self::assertArrayHasKey('1742972542166.png', $catalog);
        self::assertArrayHasKey('1748320128043.jpg', $catalog);
    }

    public function testUploadRejectsUnknownSlotBeforeReadingFile(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $manager = new HomeAssetManager();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Slot gambar Home tidak dikenal.');
        $manager->upload('unexpected.webp', $file);
    }
}
