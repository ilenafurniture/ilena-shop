<?php

namespace App\Services\Payments;

/** Resolve the stable identity used by per-customer voucher limits. */
final class VoucherIdentity
{
    public static function resolve(
        string $contactEmail,
        bool $isLoggedIn,
        string $role,
        string $active,
        string $accountEmail
    ): string {
        $candidate = $isLoggedIn && in_array($role, ['0', '4'], true) && $active === '1'
            ? $accountEmail
            : $contactEmail;

        return strtolower(trim($candidate));
    }
}
