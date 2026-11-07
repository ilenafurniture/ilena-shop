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

    public static function isAutoCompletionDue(string $status, mixed $shippedAt, int $now, bool $hasOpenCase): bool
    {
        if ($status !== 'Dikirim' || $hasOpenCase || ! is_numeric($shippedAt)) {
            return false;
        }

        $shippedAt = (int) $shippedAt;
        return $shippedAt > 0 && $now >= $shippedAt + (6 * 86400);
    }

    /** Convert a manually verified legacy shipment date into a conservative timestamp. */
    public static function legacyShipmentTimestamp(string $date, int $now): ?int
    {
        $timezone = new \DateTimeZone('Asia/Jakarta');
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date, $timezone);
        $errors = \DateTimeImmutable::getLastErrors();
        if (! $parsed || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $parsed->format('Y-m-d') !== $date
            || $parsed > (new \DateTimeImmutable('@' . $now))->setTimezone($timezone)->setTime(0, 0)
        ) {
            return null;
        }

        // The exact dispatch time is unknown; count from the end of that date
        // so an imported date can never complete the order prematurely.
        return $parsed->setTime(23, 59, 59)->getTimestamp();
    }

    public static function isCustomerRole(string $role): bool
    {
        return in_array($role, ['0', '4'], true);
    }
}
