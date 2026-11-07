<?php

namespace Tests\Unit;

use App\Services\FreeShippingService;
use CodeIgniter\Test\CIUnitTestCase;

final class FreeShippingServiceTest extends CIUnitTestCase
{
    public function testProvinceIdsTakePrecedenceOverFallbackNames(): void
    {
        $config = [
            'active' => true,
            'province_ids' => ['31', '32'],
            'province_names' => ['Jawa Timur'],
        ];

        $this->assertTrue(FreeShippingService::addressIsEligible(['prov_id' => '31', 'prov' => 'DKI Jakarta'], $config));
        $this->assertFalse(FreeShippingService::addressIsEligible(['prov_id' => '35', 'prov' => 'Jawa Timur'], $config));
    }

    public function testProvinceNamesAreUsedOnlyWhenNoProvinceIdsAreConfigured(): void
    {
        $config = ['active' => true, 'province_ids' => [], 'province_names' => ['Provinsi Jawa Barat', 'DI Yogyakarta']];

        $this->assertTrue(FreeShippingService::addressIsEligible(['prov_id' => '32', 'prov' => 'Jawa Barat'], $config));
        $this->assertTrue(FreeShippingService::addressIsEligible(['prov' => 'Daerah Istimewa Yogyakarta'], $config));
        $this->assertFalse(FreeShippingService::addressIsEligible(['prov' => 'Jawa Tengah'], $config));
    }

    public function testProvinceSelectionCoversAllCitiesAndSpecificCitySelectionIsNarrower(): void
    {
        $provinceRule = ['active' => true, 'province_ids' => ['32']];
        $cityRule = ['active' => true, 'kabupaten_ids' => ['3273']];

        $this->assertTrue(FreeShippingService::addressIsEligible(['prov_id' => '32', 'kab_id' => '3273'], $provinceRule));
        $this->assertTrue(FreeShippingService::addressIsEligible(['prov_id' => '32', 'kab_id' => '3274'], $provinceRule));
        $this->assertTrue(FreeShippingService::addressIsEligible(['prov_id' => '32', 'kab_id' => '3273'], $cityRule));
        $this->assertFalse(FreeShippingService::addressIsEligible(['prov_id' => '32', 'kab_id' => '3274'], $cityRule));
    }

    public function testConfiguredCityIdsPreventNameFallbackFromWideningCoverage(): void
    {
        $config = [
            'active' => true,
            'kabupaten_ids' => ['3273'],
            'province_names' => ['Jawa Barat'],
        ];

        $this->assertTrue(FreeShippingService::addressIsEligible(['prov_id' => '32', 'kab_id' => '3273', 'prov' => 'Jawa Barat'], $config));
        $this->assertFalse(FreeShippingService::addressIsEligible(['prov_id' => '32', 'kab_id' => '3274', 'prov' => 'Jawa Barat'], $config));
    }

    public function testMinimumOrderAndMaximumShippingRateAreEnforced(): void
    {
        $config = [
            'active' => true,
            'province_ids' => ['31'],
            'minimum_order_amount' => 1_000_000,
            'maximum_shipping_rate' => 100_000,
            'label' => 'Gratis ongkir pilihan wilayah',
        ];
        $address = ['prov_id' => '31', 'prov' => 'DKI Jakarta'];
        $rates = [
            ['nama' => 'Carrier A', 'harga' => 80_000],
            ['nama' => 'Carrier B', 'harga' => 125_000],
        ];

        $belowMinimum = FreeShippingService::applyConfigToRates($address, $rates, 999_999, $config);
        $this->assertSame($rates, $belowMinimum);

        $eligibleRates = FreeShippingService::applyConfigToRates($address, $rates, 1_000_000, $config);
        $this->assertSame(0, $eligibleRates[0]['harga']);
        $this->assertSame(80_000, $eligibleRates[0]['harga_asli']);
        $this->assertTrue($eligibleRates[0]['gratis_ongkir']);
        $this->assertSame(125_000, $eligibleRates[1]['harga']);
        $this->assertArrayNotHasKey('gratis_ongkir', $eligibleRates[1]);
    }

    public function testRepeatedEvaluationPreservesOriginalRateAndRestoresItWhenRuleStopsApplying(): void
    {
        $address = ['prov_id' => '31', 'prov' => 'DKI Jakarta'];
        $freeConfig = ['active' => true, 'province_ids' => ['31']];
        $paidConfig = ['active' => false, 'province_ids' => ['31']];
        $rates = [['nama' => 'Carrier A', 'harga' => 80_000]];

        $firstPass = FreeShippingService::applyConfigToRates($address, $rates, 80_000, $freeConfig);
        $secondPass = FreeShippingService::applyConfigToRates($address, $firstPass, 80_000, $freeConfig);
        $this->assertSame(80_000, $secondPass[0]['harga_asli']);
        $this->assertSame(0, $secondPass[0]['harga']);

        $restored = FreeShippingService::applyConfigToRates($address, $secondPass, 80_000, $paidConfig);
        $this->assertSame(80_000, $restored[0]['harga']);
        $this->assertArrayNotHasKey('gratis_ongkir', $restored[0]);
        $this->assertArrayNotHasKey('harga_asli', $restored[0]);
    }

    public function testApMIsPrimaryAndRajaOngkirIsOnlyUsedAsFallback(): void
    {
        $address = ['prov_id' => '31', 'prov' => 'DKI Jakarta'];
        $config = ['active' => true, 'province_ids' => ['31']];
        $rates = \App\Services\ShippingService::combineRates(
            [['provider' => 'apm', 'harga' => 80_000]],
            [['provider' => 'rajaongkir', 'harga' => 60_000]],
        );

        $this->assertSame(['apm'], array_column($rates, 'provider'));
        $this->assertTrue($rates[0]['free_shipping_eligible']);

        $withApm = FreeShippingService::applyConfigToRates($address, $rates, 1_000_000, $config);
        $this->assertSame(0, $withApm[0]['harga']);
        $this->assertTrue($withApm[0]['gratis_ongkir']);

        $fallbackRates = \App\Services\ShippingService::combineRates([], [['provider' => 'rajaongkir', 'harga' => 60_000]]);
        $this->assertTrue($fallbackRates[0]['free_shipping_eligible']);
        $fallback = FreeShippingService::applyConfigToRates($address, $fallbackRates, 1_000_000, $config);
        $this->assertSame(0, $fallback[0]['harga']);
        $this->assertTrue($fallback[0]['gratis_ongkir']);
    }

    public function testOnlyThreeCheapestRajaOngkirRatesAreIncluded(): void
    {
        $rajaRates = [
            ['provider' => 'rajaongkir', 'nama' => 'Courier E', 'service_code' => 'e:regular', 'harga' => 90_000],
            ['provider' => 'rajaongkir', 'nama' => 'Courier C', 'service_code' => 'c:regular', 'harga' => 30_000],
            ['provider' => 'rajaongkir', 'nama' => 'Courier D', 'service_code' => 'd:regular', 'harga' => 60_000],
            ['provider' => 'rajaongkir', 'nama' => 'Courier A', 'service_code' => 'a:regular', 'harga' => 10_000],
            ['provider' => 'rajaongkir', 'nama' => 'Courier B', 'service_code' => 'b:regular', 'harga' => 20_000],
        ];

        $rates = \App\Services\ShippingService::combineRates([], $rajaRates);

        $this->assertSame(['rajaongkir', 'rajaongkir', 'rajaongkir'], array_column($rates, 'provider'));
        $this->assertSame([10_000, 20_000, 30_000], array_column($rates, 'harga'));
    }
}
