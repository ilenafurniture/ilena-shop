<?php

namespace App\Services;

use InvalidArgumentException;

/** Validates and normalizes the minimum contact details required for staff handoff. */
final class ChatContactDetails
{
    /** @return array{name:string,phone:string} */
    public static function normalize(string $name, string $phone): array
    {
        $name = trim((string) preg_replace('/\s+/u', ' ', strip_tags($name)));
        if (mb_strlen($name) < 2 || mb_strlen($name) > 100
            || preg_match('/^[\p{L}\p{M}][\p{L}\p{M}\p{N} .’\'-]*$/u', $name) !== 1
        ) {
            throw new InvalidArgumentException('Masukkan nama yang valid (2–100 karakter).');
        }

        $phone = trim($phone);
        if (preg_match('/^\+?[0-9\s().-]+$/', $phone) !== 1) {
            throw new InvalidArgumentException('Masukkan nomor WhatsApp yang valid.');
        }
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($digits) < 8 || strlen($digits) > 15) {
            throw new InvalidArgumentException('Nomor WhatsApp harus berisi 8–15 digit.');
        }

        if (str_starts_with($phone, '+')) {
            $phone = '+' . $digits;
        } elseif (str_starts_with($digits, '62')) {
            $phone = '+' . $digits;
        } elseif (str_starts_with($digits, '0')) {
            $phone = '+62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $phone = '+62' . $digits;
        } else {
            throw new InvalidArgumentException('Gunakan nomor Indonesia atau awali nomor internasional dengan tanda +.');
        }

        return ['name' => $name, 'phone' => $phone];
    }
}
