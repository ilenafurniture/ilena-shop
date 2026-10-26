<?php

namespace App\Services;

/** Encodes JSON for an inline script element without allowing HTML/script termination. */
final class ScriptJson
{
    public static function encode($value): string
    {
        $json = json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE
        );

        if ($json === false) {
            throw new \RuntimeException('Inline JSON serialization failed.');
        }

        return $json;
    }
}
