<?php
namespace App\Services;

/** One scheduled-discount rule for all server-side sales channels. */
final class CatalogPricing
{
    private const BUSINESS_TIMEZONE = 'Asia/Jakarta';

    public static function discount(array $row, ?\DateTimeImmutable $now = null): float
    {
        $discount = max(0, min(100, (float)($row['diskon_configured'] ?? $row['diskon'] ?? 0)));
        if ($discount === 0.0 || empty($row['pakai_jadwal_diskon'])) return $discount;
        return self::scheduleStatus($row, $now) === 'active' ? $discount : 0;
    }

    public static function scheduleStatus(array $row, ?\DateTimeImmutable $now = null): string
    {
        if (empty($row['pakai_jadwal_diskon']) || (float) ($row['diskon_configured'] ?? $row['diskon'] ?? 0) <= 0) {
            return 'none';
        }
        if (empty($row['diskon_mulai']) || empty($row['diskon_selesai'])) return 'invalid';

        try {
            $timezone = new \DateTimeZone(self::BUSINESS_TIMEZONE);
            $now = ($now ?? new \DateTimeImmutable('now', $timezone))->setTimezone($timezone);
            // Admin datetime-local and stored DATETIME values represent
            // Indonesian wall-clock time, independent of PHP's host timezone.
            $start = new \DateTimeImmutable((string) $row['diskon_mulai'], $timezone);
            $end = new \DateTimeImmutable((string) $row['diskon_selesai'], $timezone);
            if ($start > $end) return 'invalid';
            if ($now < $start) return 'upcoming';
            if ($now > $end) return 'ended';
            return 'active';
        } catch (\Throwable) {
            return 'invalid';
        }
    }

    public static function unitPrice(array $row, ?\DateTimeImmutable $now = null): int
    {
        // BundleCartService sets this only after validating the saved bundle
        // composition against the live catalog and campaign definition.
        if (isset($row['_validated_bundle_unit_price']) && is_numeric($row['_validated_bundle_unit_price'])) {
            return max(0, (int) $row['_validated_bundle_unit_price']);
        }
        return max(0, (int)round(max(0,(float)($row['harga'] ?? 0)) * (100-self::discount($row, $now))/100));
    }

    public static function forSale(array $row, ?\DateTimeImmutable $now = null): array
    {
        $row['diskon_configured'] = $row['diskon_configured'] ?? $row['diskon'] ?? 0;
        $row['diskon'] = self::discount($row, $now);
        $row['harga_akhir'] = self::unitPrice($row, $now);
        return $row;
    }
}
