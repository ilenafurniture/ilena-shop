<?php

namespace App\Services;

/** Deterministic integer-rupiah allocation of a fixed bundle discount. */
final class BundlePricing
{
    /**
     * @param list<array{product_id:string,variant_name:string,quantity:int,unit_price:int}> $components
     * @return array{components:array,total_before:int,discount:int,total_after:int}
     */
    public static function calculate(array $components, float $discountPercent): array
    {
        if ($components === [] || count($components) > 20 || !is_finite($discountPercent) || $discountPercent < 0 || $discountPercent >= 100) {
            throw new \InvalidArgumentException('Bundle pricing input is invalid.');
        }

        $lines = [];
        $total = 0;
        foreach ($components as $component) {
            $id = $component['product_id'] ?? null;
            $variant = $component['variant_name'] ?? null;
            $quantity = $component['quantity'] ?? null;
            $unitPrice = $component['unit_price'] ?? null;
            if (!is_string($id) || $id === '' || !is_string($variant) || $variant === ''
                || !is_int($quantity) || $quantity < 1 || $quantity > 100
                || !is_int($unitPrice) || $unitPrice < 1
            ) {
                throw new \InvalidArgumentException('Bundle component is invalid.');
            }

            $lineTotal = $unitPrice * $quantity;
            if ($lineTotal > 2_000_000_000 || $total > 2_000_000_000 - $lineTotal) {
                throw new \InvalidArgumentException('Bundle total exceeds payment limits.');
            }
            $total += $lineTotal;
            $unitPriceAfter = max(1, (int) round($unitPrice * (100 - $discountPercent) / 100, 0, PHP_ROUND_HALF_UP));
            $lineTotalAfter = $unitPriceAfter * $quantity;
            $lines[] = [
                'product_id' => $id,
                'variant_name' => $variant,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_price_after' => $unitPriceAfter,
                'line_total_before' => $lineTotal,
                'discount' => $lineTotal - $lineTotalAfter,
                'line_total_after' => $lineTotalAfter,
            ];
        }

        $discount = $total - array_sum(array_column($lines, 'line_total_after'));

        return [
            'components' => $lines,
            'total_before' => $total,
            'discount' => $discount,
            'total_after' => $total - $discount,
        ];
    }
}
