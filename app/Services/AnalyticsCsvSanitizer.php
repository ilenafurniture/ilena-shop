<?php

namespace App\Services;

/** Neutralizes spreadsheet formula cells in user-controlled analytics text. */
final class AnalyticsCsvSanitizer
{
    public static function text(string $value): string
    {
        // Formula parsers may ignore leading whitespace/control characters.
        if (preg_match('/^[\x00-\x20\x7F\x{FEFF}]*[=+\-@]/u', $value) === 1) {
            return "'" . $value;
        }

        return $value;
    }
}
