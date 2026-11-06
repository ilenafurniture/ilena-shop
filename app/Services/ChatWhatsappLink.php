<?php

namespace App\Services;

/** Builds a user-initiated WhatsApp handoff link without sending chat data automatically. */
final class ChatWhatsappLink
{
    private const PHONE = '628112938158';

    public static function forQuestion(string $question, string $locale = 'id'): string
    {
        $question = trim((string) preg_replace('/\s+/u', ' ', $question));
        $question = mb_substr($question, 0, 400);
        $english = strtolower(trim($locale)) === 'en';
        $message = $question === ''
            ? ($english ? 'Hello Ilena, I need help with a product or service.' : 'Halo Ilena, saya membutuhkan bantuan seputar produk atau layanan.')
            : ($english ? 'Hello Ilena, I have a question: ' : 'Halo Ilena, saya ingin bertanya: ') . $question;

        return 'https://wa.me/' . self::PHONE . '?text=' . rawurlencode($message);
    }

    /** Build a user-clicked handoff link with a prefilled, structured order message. */
    public static function forPrefilledMessage(string $message): string
    {
        $message = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $message);
        $message = trim(mb_substr($message, 0, 2400, 'UTF-8'));

        return 'https://wa.me/' . self::PHONE . '?text=' . rawurlencode($message);
    }
}
