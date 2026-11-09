<?php

namespace Tests\unit;

use App\Services\BundlePricing;
use PHPUnit\Framework\TestCase;

final class BundlePricingTest extends TestCase
{
    public function testDiscountIsAllocatedPerUnitAndTotalsReconcileExactly(): void
    {
        $result = BundlePricing::calculate([
            ['product_id' => 'P1', 'variant_name' => 'Natural', 'quantity' => 2, 'unit_price' => 100001],
            ['product_id' => 'P2', 'variant_name' => 'Black', 'quantity' => 1, 'unit_price' => 50000],
        ], 15.5);

        self::assertSame(250002, $result['total_before']);
        self::assertSame(211252, $result['total_after']);
        self::assertSame(38750, $result['discount']);
        self::assertSame($result['total_before'] - $result['discount'], $result['total_after']);
        self::assertSame(84501, $result['components'][0]['unit_price_after']);
    }

    public function testRejectsMalformedOrFreeBundleDiscounts(): void
    {
        $component = [['product_id' => 'P1', 'variant_name' => 'Natural', 'quantity' => 1, 'unit_price' => 100]];
        foreach ([-0.01, 100, INF] as $discount) {
            try {
                BundlePricing::calculate($component, $discount);
                self::fail('Invalid bundle discount was accepted.');
            } catch (\InvalidArgumentException) {
                self::assertTrue(true);
            }
        }
    }

    public function testRejectsComponentQuantityAndPriceOverflow(): void
    {
        foreach ([
            ['product_id' => 'P1', 'variant_name' => 'Natural', 'quantity' => 0, 'unit_price' => 100],
            ['product_id' => 'P1', 'variant_name' => 'Natural', 'quantity' => 101, 'unit_price' => 100],
            ['product_id' => 'P1', 'variant_name' => 'Natural', 'quantity' => 100, 'unit_price' => 21000000],
        ] as $component) {
            try {
                BundlePricing::calculate([$component], 10);
                self::fail('Invalid bundle component was accepted.');
            } catch (\InvalidArgumentException) {
                self::assertTrue(true);
            }
        }
    }
}
