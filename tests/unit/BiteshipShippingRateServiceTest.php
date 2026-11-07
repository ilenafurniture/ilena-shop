<?php

namespace Tests\Unit;

use App\Services\BiteshipShippingRateService;
use CodeIgniter\Test\CIUnitTestCase;
use ReflectionMethod;

final class BiteshipShippingRateServiceTest extends CIUnitTestCase
{
    public function testBiteshipPricingResponseMapsToCheckoutRate(): void
    {
        $service = new BiteshipShippingRateService();
        $method = new ReflectionMethod($service, 'mapPricing');
        $method->setAccessible(true);

        $rates = $method->invoke($service, [[
            'courier_name' => 'JNE',
            'courier_code' => 'jne',
            'courier_service_name' => 'Reguler',
            'courier_service_code' => 'reg',
            'price' => 42000,
            'duration' => '2 - 3 days',
        ]], 'Ogan Komering Ulu Timur');

        $this->assertCount(1, $rates);
        $this->assertSame('biteship', $rates[0]['provider']);
        $this->assertSame('jne:reg', $rates[0]['service_code']);
        $this->assertSame(42000, $rates[0]['harga']);
        $this->assertFalse($rates[0]['free_shipping_eligible']);
    }

    public function testBiteshipCartItemsRequirePositivePackageWeight(): void
    {
        $service = new BiteshipShippingRateService();
        $method = new ReflectionMethod($service, 'buildItems');
        $method->setAccessible(true);

        $cart = [[
            'jumlah' => 2,
            'detail' => [
                'nama' => 'Lemari',
                'harga' => 1500000,
                'deskripsi' => json_encode(['dimensi' => [
                    'asli' => ['panjang' => 120, 'lebar' => 45, 'tinggi' => 180, 'berat' => 30],
                    'paket' => ['panjang' => 125, 'lebar' => 50, 'tinggi' => 185, 'berat' => 32],
                ]]),
            ],
        ]];

        $items = $method->invoke($service, $cart);
        $this->assertCount(1, $items);
        $this->assertSame(32000, $items[0]['weight']);
        $this->assertSame(2, $items[0]['quantity']);
        $this->assertSame(125, $items[0]['length']);

        $cart[0]['detail']['deskripsi'] = json_encode(['dimensi' => [
            'asli' => ['berat' => 0],
            'paket' => ['berat' => 0],
        ]]);
        $this->assertSame([], $method->invoke($service, $cart));
    }
}
