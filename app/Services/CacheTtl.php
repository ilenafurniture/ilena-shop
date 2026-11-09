<?php

namespace App\Services;

/** Downward-only TTL jitter spreads expirations without exceeding freshness bounds. */
final class CacheTtl
{
    public static function jitter(int $ttl, float $fraction = 0.10): int
    {
        $ttl = max(1, $ttl);
        $fraction = max(0.0, min(0.25, $fraction));
        $spread = (int) floor($ttl * $fraction);
        return max(1, $ttl - ($spread > 0 ? random_int(0, $spread) : 0));
    }
}
