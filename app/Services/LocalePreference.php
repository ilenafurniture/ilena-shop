<?php

namespace App\Services;

final class LocalePreference
{
    public static function choose(?string $savedLocale, ?string $countryCode): string
    {
        $savedLocale = strtolower(trim((string) $savedLocale));
        if (in_array($savedLocale, ['id', 'en'], true)) {
            return $savedLocale;
        }

        $countryCode = strtoupper(trim((string) $countryCode));
        if (preg_match('/^[A-Z]{2}$/', $countryCode) === 1
            && ! in_array($countryCode, ['ID', 'XX'], true)
        ) {
            return 'en';
        }

        return 'id';
    }

    /** Convert a same-site path to the requested storefront locale. */
    public static function localizePath(string $returnTo, string $locale): string
    {
        $locale = $locale === 'en' ? 'en' : 'id';
        $parts = parse_url(trim($returnTo));
        if (! is_array($parts) || isset($parts['scheme']) || isset($parts['host'])) {
            $parts = ['path' => '/'];
        }
        $path = is_array($parts) ? (string) ($parts['path'] ?? '/') : '/';

        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')
            || str_contains($path, '\\') || preg_match('/[\x00-\x1F\x7F]/', $path) === 1
        ) {
            $path = '/';
        }
        if (preg_match('#^/language(?:/|$)#i', $path) === 1) {
            $path = '/';
        }

        $path = (string) preg_replace('#^/en(?=/|$)#i', '', $path);
        $path = $path === '' ? '/' : $path;
        if ($locale === 'en') {
            $path = '/en' . ($path === '/' ? '' : $path);
        }
        $query = is_array($parts) && isset($parts['query']) && $parts['query'] !== ''
            ? '?' . $parts['query']
            : '';

        return $path . $query;
    }
}
