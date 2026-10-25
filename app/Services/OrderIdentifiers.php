<?php
namespace App\Services;
final class OrderIdentifiers
{
    // Existing pemesanan.id_midtrans and downstream references are VARCHAR(20).
    public static function web(): string { return 'IL'.strtoupper(bin2hex(random_bytes(9))); }
    public static function market(): string { return 'IL'.strtoupper(bin2hex(random_bytes(8))).'MP'; }
    public static function shop(): string { return 'TOKO'.strtoupper(bin2hex(random_bytes(8))); }
}
