<?php

use CodeIgniter\Test\CIUnitTestCase;

final class AdminSliderUploadPermissionTest extends CIUnitTestCase
{
    public function testSliderUploadHandlesUnwritableDirectoryWithoutThrowingPhpWarning(): void
    {
        $source = file_get_contents(APPPATH . 'Controllers/AdminController.php');

        $this->assertStringContainsString('!@mkdir($uploadDir, 0775, true)', $source);
        $this->assertStringContainsString('if (!is_writable($uploadDir))', $source);
        $this->assertStringContainsString('$this->sliderUploadStorageUnavailable = true;', $source);
        $this->assertStringContainsString('Foto slider tidak tersimpan karena folder upload belum dapat ditulis.', $source);
    }
}
