<?php
namespace App\Services;

/** One scheduled-discount rule for all server-side sales channels. */
final class CatalogPricing
{
    public static function discount(array $row, ?\DateTimeImmutable $now = null): float
    {
        $discount = max(0, min(100, (float)($row['diskon_configured'] ?? $row['diskon'] ?? 0)));
        if ($discount === 0.0 || empty($row['pakai_jadwal_diskon'])) return $discount;
        if (empty($row['diskon_mulai']) || empty($row['diskon_selesai'])) return 0;
        try {
            $now ??= new \DateTimeImmutable('now');
            $start = new \DateTimeImmutable((string)$row['diskon_mulai']);
            $end = new \DateTimeImmutable((string)$row['diskon_selesai']);
            return $start <= $end && $now >= $start && $now <= $end ? $discount : 0;
        } catch (\Throwable $e) { return 0; }
    }

    public static function unitPrice(array $row): int
    {
        return max(0, (int)round(max(0,(float)($row['harga'] ?? 0)) * (100-self::discount($row))/100));
    }

    public static function forSale(array $row): array
    {
        $row['diskon_configured'] = $row['diskon_configured'] ?? $row['diskon'] ?? 0;
        $row['diskon'] = self::discount($row);
        $row['harga_akhir'] = self::unitPrice($row);
        return $row;
    }
}
