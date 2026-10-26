<?php

namespace App\Services;

/** Shared server-side email policy for checkout contact details. */
final class CheckoutEmailPolicy
{
    public static function normalize(mixed $value): ?string
    {
        if (!is_string($value)) return null;

        $email = strtolower(trim($value));
        if ($email === '' || strlen($email) > 191 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        if ((new SpamAccountCleanupService())->isDisposableEmail($email)) return null;
        return $email;
    }
}
