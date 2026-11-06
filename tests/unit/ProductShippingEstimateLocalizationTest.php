<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

final class ProductShippingEstimateLocalizationTest extends CIUnitTestCase
{
    public function testShippingEstimateErrorKeysExistInBothLocalesAndMatchControllerReferences(): void
    {
        $keys = [
            'productShippingEstimateInvalid',
            'productShippingEstimateUnavailable',
            'productShippingStockChanged',
        ];
        $indonesian = require APPPATH . 'Language/id/Store.php';
        $english = require APPPATH . 'Language/en/Store.php';
        $controller = (string) file_get_contents(APPPATH . 'Controllers/Pages.php');

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $indonesian);
            $this->assertArrayHasKey($key, $english);
            $this->assertStringContainsString("lang('Store.{$key}')", $controller);
        }
    }
}
