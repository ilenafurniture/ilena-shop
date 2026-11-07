<?php

namespace App\Services;

/** Checkout tariff facade: local APM first, RajaOngkir as live fallback. */
final class ShippingService
{
    private string $lastError = '';

    public function rates(array $address, array $cart): array
    {
        $apm = new ApmShippingRateService();
        $apmRates = $apm->rates($address, $cart);
        if ($apmRates !== []) {
            $this->lastError = '';
            return self::combineRates($apmRates, []);
        }

        $rajaOngkir = new RajaOngkirShippingRateService();
        $rajaRates = $rajaOngkir->rates($address, $cart);
        $this->lastError = $rajaRates !== [] ? '' : ($rajaOngkir->lastError() ?: $apm->lastError());
        return self::combineRates([], $rajaRates);
    }

    /** APM is authoritative when available; RajaOngkir is strictly a fallback. */
    public static function combineRates(array $apmRates, array $rajaRates): array
    {
        usort($rajaRates, static function (array $a, array $b): int {
            $priceOrder = ((int) ($a['harga'] ?? PHP_INT_MAX)) <=> ((int) ($b['harga'] ?? PHP_INT_MAX));
            if ($priceOrder !== 0) {
                return $priceOrder;
            }
            $nameOrder = strcasecmp((string) ($a['nama'] ?? ''), (string) ($b['nama'] ?? ''));
            return $nameOrder !== 0
                ? $nameOrder
                : strcmp((string) ($a['service_code'] ?? ''), (string) ($b['service_code'] ?? ''));
        });
        $rajaRates = array_slice($rajaRates, 0, 3);

        if ($apmRates !== []) {
            foreach ($apmRates as &$rate) {
                $rate['free_shipping_eligible'] = true;
            }
            unset($rate);
        } else {
            foreach ($rajaRates as &$rate) {
                $rate['free_shipping_eligible'] = true;
            }
            unset($rate);
        }
        return $apmRates !== [] ? $apmRates : $rajaRates;
    }

    public function lastError(): string
    {
        return $this->lastError;
    }

    /** Rechecks a live RajaOngkir choice before payment so stale prices cannot be charged. */
    public function revalidateRate(array $selected, array $address, array $cart): ?array
    {
        if (($selected['provider'] ?? '') !== 'rajaongkir') {
            return $selected;
        }
        $serviceCode = (string) ($selected['service_code'] ?? '');
        $selectedPrice = (int) ($selected['gratis_ongkir'] ?? false
            ? ($selected['harga_asli'] ?? -1)
            : ($selected['harga'] ?? -1));
        if ($serviceCode === '' || $selectedPrice < 0) {
            return null;
        }
        $apm = new ApmShippingRateService();
        $apmRates = $apm->rates($address, $cart);
        if ($apmRates !== []) {
            return null;
        }
        $rajaRates = (new RajaOngkirShippingRateService())->rates($address, $cart, true);
        $rates = self::combineRates([], $rajaRates);
        foreach ($rates as $rate) {
            if (($rate['provider'] ?? '') === 'rajaongkir'
                && (string) ($rate['service_code'] ?? '') === $serviceCode
                && (int) ($rate['harga'] ?? -2) === $selectedPrice
            ) {
                return $rate;
            }
        }
        return null;
    }
}
