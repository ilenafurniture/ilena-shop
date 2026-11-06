<?php

namespace App\Services;

/** Checkout tariff facade. APM tariffs are local; no live quote API is called. */
final class ShippingService
{
    private string $lastError = '';

    public function rates(array $address, array $cart): array
    {
        $apm = new ApmShippingRateService();
        $rates = $apm->rates($address, $cart);
        $this->lastError = $apm->lastError();
        usort($rates, static fn (array $a, array $b): int => ((int) $a['harga']) <=> ((int) $b['harga']));
        return $rates;
    }

    public function lastError(): string
    {
        return $this->lastError;
    }
}
