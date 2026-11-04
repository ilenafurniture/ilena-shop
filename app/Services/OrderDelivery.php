<?php

namespace App\Services;

/** Small, deterministic rules for the final hand-off in a web order. */
final class OrderDelivery
{
    /**
     * Keep the checkout quote metadata when the admin only adds tracking details.
     * A newly selected carrier/service must not inherit a stale quote.
     *
     * @param array<string, mixed> $previous
     * @return array<string, mixed>
     */
    public static function courierSnapshot(array $previous, string $carrier, string $service): array
    {
        $sameSelection = strcasecmp(trim((string) ($previous['nama'] ?? '')), trim($carrier)) === 0
            && strcasecmp(trim((string) ($previous['deskripsi'] ?? '')), trim($service)) === 0;
        $snapshot = $sameSelection ? $previous : [];
        $snapshot['nama'] = trim($carrier);
        $snapshot['deskripsi'] = trim($service);

        return $snapshot;
    }

    public static function canBuyerConfirmReceipt(string $status): bool
    {
        return $status === 'Dikirim';
    }

    public static function isCustomerRole(string $role): bool
    {
        return in_array($role, ['0', '4'], true);
    }
}
