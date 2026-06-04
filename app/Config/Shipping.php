<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Shipping extends BaseConfig
{
    public string $provider = 'biteship';
    public string $biteshipApiKey = '';
    public string $originPostalCode = '';
    public string $couriers = 'jne,jnt,sicepat,anteraja,wahana,tiki,lion,paxel';
    public int $connectTimeout = 5;
    public int $timeout = 12;

    public bool $manualFallbackEnabled = false;
    public int $manualFallbackDefaultPrice = 0;
    public int $manualFallbackPricePerKg = 0;
    public string $manualFallbackEstimate = '3 - 10';
    public string $manualFallbackCourierCode = 'manual';
    public string $manualFallbackCourierName = 'Pengiriman Manual';

    public function __construct()
    {
        $this->provider = strtolower((string) env('SHIPPING_PROVIDER', $this->provider));
        $this->biteshipApiKey = trim((string) env('BITESHIP_API_KEY', $this->biteshipApiKey));
        $this->originPostalCode = trim((string) env('SHIPPING_ORIGIN_POSTAL_CODE', $this->originPostalCode));
        $this->couriers = trim((string) env('BITESHIP_COURIERS', $this->couriers));
        $this->connectTimeout = (int) env('SHIPPING_CONNECT_TIMEOUT', $this->connectTimeout);
        $this->timeout = (int) env('SHIPPING_TIMEOUT', $this->timeout);

        $this->manualFallbackEnabled = filter_var(
            env('SHIPPING_MANUAL_FALLBACK_ENABLED', $this->manualFallbackEnabled),
            FILTER_VALIDATE_BOOLEAN
        );
        $this->manualFallbackDefaultPrice = (int) env('SHIPPING_MANUAL_DEFAULT_PRICE', $this->manualFallbackDefaultPrice);
        $this->manualFallbackPricePerKg = (int) env('SHIPPING_MANUAL_PRICE_PER_KG', $this->manualFallbackPricePerKg);
        $this->manualFallbackEstimate = trim((string) env('SHIPPING_MANUAL_ESTIMATE', $this->manualFallbackEstimate));
        $this->manualFallbackCourierCode = trim((string) env('SHIPPING_MANUAL_COURIER_CODE', $this->manualFallbackCourierCode));
        $this->manualFallbackCourierName = trim((string) env('SHIPPING_MANUAL_COURIER_NAME', $this->manualFallbackCourierName));
    }
}
