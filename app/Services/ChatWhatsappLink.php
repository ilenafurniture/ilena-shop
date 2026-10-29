<?php

namespace App\Services;

/** Builds a user-initiated WhatsApp handoff link without sending chat data automatically. */
final class ChatWhatsappLink
{
    private const PHONE = '628112938158';

    public static function forQuestion(string $question): string
    {
        $question = trim((string) preg_replace('/\s+/u', ' ', $question));
        $question = mb_substr($question, 0, 400);
        $message = $question === ''
            ? 'Halo Ilena, saya membutuhkan bantuan seputar produk atau layanan.'
            : 'Halo Ilena, saya ingin bertanya: ' . $question;

        return 'https://wa.me/' . self::PHONE . '?text=' . rawurlencode($message);
    }
}
