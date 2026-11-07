<?php

namespace Tests\Unit;

use App\Services\RajaOngkirShippingRateService;
use CodeIgniter\Test\CIUnitTestCase;
use ReflectionMethod;

final class RajaOngkirShippingRateServiceTest extends CIUnitTestCase
{
    public function testDomesticCostRowsMapToCheckoutRatesAndKeepThreeCheapest(): void
    {
        $service = new RajaOngkirShippingRateService();
        $method = new ReflectionMethod($service, 'mapRates');
        $method->setAccessible(true);
        $rows = [];
        foreach ([90_000, 30_000, 60_000, 10_000, 20_000] as $index => $price) {
            $rows[] = [
                'name' => 'Courier ' . $index,
                'code' => 'courier' . $index,
                'service' => 'regular',
                'description' => 'Regular service',
                'cost' => $price,
                'etd' => '2 - 3 Days',
            ];
        }

        $rates = $method->invoke($service, $rows, 'Ogan Komering Ulu Timur');

        $this->assertCount(3, $rates);
        $this->assertSame([10_000, 20_000, 30_000], array_column($rates, 'harga'));
        $this->assertSame('rajaongkir', $rates[0]['provider']);
        $this->assertSame('courier3:regular', $rates[0]['service_code']);
        $this->assertSame('2 - 3', $rates[0]['estimasi']);
        $this->assertFalse($rates[0]['free_shipping_eligible']);
    }

    public function testCartWeightUsesPackageKilogramsAndReturnsProviderGrams(): void
    {
        $service = new RajaOngkirShippingRateService();
        $method = new ReflectionMethod($service, 'cartWeightGrams');
        $method->setAccessible(true);
        $cart = [[
            'jumlah' => 2,
            'detail' => ['deskripsi' => json_encode(['dimensi' => [
                'asli' => ['berat' => 30],
                'paket' => ['berat' => 32],
            ]])],
        ]];

        $this->assertSame(64_000, $method->invoke($service, $cart));
        $cart[0]['detail']['deskripsi'] = json_encode(['dimensi' => ['asli' => ['berat' => 30], 'paket' => ['berat' => 0]]]);
        $this->assertSame(60_000, $method->invoke($service, $cart), 'A zero package weight should fall back to a valid actual product weight.');
        $cart[0]['detail']['deskripsi'] = json_encode(['dimensi' => ['asli' => ['berat' => 0], 'paket' => ['berat' => 0]]]);
        $this->assertSame(0, $method->invoke($service, $cart));
    }
}
