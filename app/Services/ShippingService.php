<?php

namespace App\Services;

/** Checkout tariff facade: local APM first, optional Biteship live rates second. */
final class ShippingService
{
    private string $lastError = '';

    public function rates(array $address, array $cart): array
    {
        $apm = new ApmShippingRateService();
        $apmRates = $apm->rates($address, $cart);
        $this->lastError = $apm->lastError();
        $biteship = new BiteshipShippingRateService();
        $biteshipRates = $biteship->rates($address, $cart);
        $rates = self::combineRates($apmRates, $biteshipRates);
        if ($rates !== []) {
            $this->lastError = '';
        } else {
            $this->lastError = $apm->lastError() !== '' ? $apm->lastError() : $biteship->lastError();
        }
        return $rates;
    }

    /** APM always ranks first and owns free-shipping eligibility when available. */
    public static function combineRates(array $apmRates, array $biteshipRates): array
    {
        usort($biteshipRates, static function (array $a, array $b): int {
            $priceOrder = ((int) ($a['harga'] ?? PHP_INT_MAX)) <=> ((int) ($b['harga'] ?? PHP_INT_MAX));
            if ($priceOrder !== 0) {
                return $priceOrder;
            }
            $nameOrder = strcasecmp((string) ($a['nama'] ?? ''), (string) ($b['nama'] ?? ''));
            return $nameOrder !== 0
                ? $nameOrder
                : strcmp((string) ($a['service_code'] ?? ''), (string) ($b['service_code'] ?? ''));
        });
        $biteshipRates = array_slice($biteshipRates, 0, 3);

        if ($apmRates !== []) {
            foreach ($apmRates as &$rate) {
                $rate['free_shipping_eligible'] = true;
            }
            unset($rate);
            foreach ($biteshipRates as &$rate) {
                $rate['free_shipping_eligible'] = false;
            }
            unset($rate);
        } else {
            foreach ($biteshipRates as &$rate) {
                $rate['free_shipping_eligible'] = true;
            }
            unset($rate);
        }
        $rates = array_merge($apmRates, $biteshipRates);
        usort($rates, static function (array $a, array $b): int {
            $providerPriority = ['apm' => 0, 'biteship' => 1];
            $priority = ($providerPriority[$a['provider'] ?? ''] ?? 9) <=> ($providerPriority[$b['provider'] ?? ''] ?? 9);
            return $priority !== 0 ? $priority : ((int) $a['harga'] <=> (int) $b['harga']);
        });
        return $rates;
    }

    public function lastError(): string
    {
        return $this->lastError;
    }

    /** Rechecks a live Biteship choice before payment so stale prices cannot be charged. */
    public function revalidateRate(array $selected, array $address, array $cart): ?array
    {
        if (($selected['provider'] ?? '') !== 'biteship') {
            return $selected;
        }
        $serviceCode = (string) ($selected['service_code'] ?? '');
        $selectedPrice = (int) ($selected['harga'] ?? -1);
        if ($serviceCode === '' || $selectedPrice < 0) {
            return null;
        }
        $apm = new ApmShippingRateService();
        $biteship = new BiteshipShippingRateService();
        $rates = self::combineRates($apm->rates($address, $cart), $biteship->rates($address, $cart, true));
        foreach ($rates as $rate) {
            if (($rate['provider'] ?? '') === 'biteship'
                && (string) ($rate['service_code'] ?? '') === $serviceCode
                && (int) ($rate['harga'] ?? -2) === $selectedPrice
            ) {
                return $rate;
            }
        }
        return null;
    }
}
