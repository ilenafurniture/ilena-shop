<?php

namespace App\Services;

/** Central policy for orders belonging to ILENA's internal test accounts. */
final class TestOrderAccess
{
    private const TEST_ACCOUNT_EMAILS = [
        'galihsuks123@gmail.com',
        'ilenafurniture@gmail.com',
        'galih8.4.2001@gmail.com',
        'adityaanugrah494@gmail.com',
        'tipaun0605@gmail.com',
        'uuua5021@gmail.com',
    ];

    public static function emails(): array
    {
        return self::TEST_ACCOUNT_EMAILS;
    }

    public static function isTestOrder(array $order): bool
    {
        $email = strtolower(trim((string) ($order['email'] ?? '')));
        return $email !== '' && in_array($email, self::TEST_ACCOUNT_EMAILS, true);
    }

    public static function isReadOnlyForRole(array $order, string $role): bool
    {
        return $role !== '1' && self::isTestOrder($order);
    }
}
