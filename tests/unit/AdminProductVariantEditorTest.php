<?php

use CodeIgniter\Test\CIUnitTestCase;

final class AdminProductVariantEditorTest extends CIUnitTestCase
{
    public function testEditSerializationUsesEachVisibleVariantCardAfterDeletion(): void
    {
        $view = file_get_contents(APPPATH . 'Views/admin/edit.php');

        $this->assertStringContainsString("card.querySelector('input[name^=\"nama-var\"]')", $view);
        $this->assertStringContainsString("card.querySelector('input[name^=\"nama-en-var\"]')", $view);
        $this->assertStringContainsString("card.querySelector('input[name^=\"kode-var\"]')", $view);
        $this->assertStringContainsString("card.querySelector('input[name^=\"stok-var\"]')", $view);
        $this->assertStringContainsString("card.querySelector('[id^=\"container-input-gambar\"]')", $view);
        $this->assertStringNotContainsString('input[name="nama-var${no}"]', $view);
        $this->assertStringNotContainsString('#container-input-gambar${no}', $view);
    }

    public function testEditExplainsTheExactRequirementsForDeletingAVariant(): void
    {
        $view = file_get_contents(APPPATH . 'Views/admin/edit.php');

        $this->assertStringContainsString('Penghapusan hanya berhasil jika stok varian 0 dan tidak ada checkout aktif yang sedang memakai varian tersebut.', $view);
        $this->assertStringContainsString('Kosongkan stok melalui menu Mutasi sebelum menghapus varian.', $view);
    }
}
