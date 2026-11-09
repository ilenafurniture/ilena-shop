<?php

namespace App\Services\Auth;

/** Shared customer password policy for registration, account changes, and resets. */
final class PasswordPolicy
{
    public static function isStrong(string $password): bool
    {
        $length = mb_strlen($password, 'UTF-8');
        if ($length < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
            return false;
        }

        $normalized = mb_strtolower($password, 'UTF-8');
        $compact = preg_replace('/[^a-z0-9]/', '', $normalized) ?? $normalized;
        $common = [
            'password', 'password1', 'password123', 'qwerty123', '12345678',
            'ilena1234', 'admin123', 'letmein123',
        ];
        if (in_array($normalized, $common, true) || in_array($compact, $common, true)) {
            return false;
        }

        $classes = 0;
        foreach (['/[a-z]/', '/[A-Z]/', '/[0-9]/', '/[^a-zA-Z0-9]/'] as $pattern) {
            if (preg_match($pattern, $password) === 1) $classes++;
        }

        // Shorter passwords need variety; longer passphrases may use two character types.
        return $classes >= 3 || ($length >= 16 && $classes >= 2);
    }
}
