<?php

namespace App\Services;

/** Central routing policy for the public shell so headers and footers stay aligned. */
final class StorefrontLayoutPolicy
{
    public static function normalizePath(string $path): string
    {
        return '/' . strtolower(trim($path, '/'));
    }

    public static function isInternal(string $path): bool
    {
        return preg_match('~^/(admin|analytics|market|editarticle|changepic|gantilokasi|cobainput|gamwm)(/|$)~', self::normalizePath($path)) === 1;
    }

    public static function headerMode(string $path): string
    {
        $path = self::normalizePath($path);
        if (in_array($path, ['/login', '/register', '/verify'], true)) {
            return 'auth';
        }
        // Cart remains part of the storefront; only active checkout/payment steps use the compact shell.
        if (preg_match('~^/(shipping|payment|progresspay|successpay|cencelpay|expirepay)(/|$)~', $path) === 1) {
            return 'checkout';
        }
        if (preg_match('~^/(account|address|order|orderdetail|invoice|wishlist)(/|$)~', $path) === 1) {
            return 'account';
        }
        return 'full';
    }

    public static function footerMode(string $path): string
    {
        $path = self::normalizePath($path);
        if (in_array($path, ['/login', '/register', '/verify'], true)) {
            return 'auth';
        }
        // Keep cart conversion pages focused: use support/payment assurance, not the full discovery footer.
        if (preg_match('~^/(cart|shipping|payment|progresspay|successpay|cencelpay|expirepay)(/|$)~', $path) === 1) {
            return 'checkout';
        }
        if (preg_match('~^/(account|address|order|orderdetail|invoice|wishlist)(/|$)~', $path) === 1) {
            return 'account';
        }
        return 'store';
    }
}
