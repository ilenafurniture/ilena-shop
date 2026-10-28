<?php

namespace App\Services;

final class ChatSupportSchedule
{
    public const HOURS = '08.00–16.00 WIB';

    public static function availability(?\DateTimeImmutable $now = null): array
    {
        $timezone = new \DateTimeZone('Asia/Jakarta');
        $now = ($now ?? new \DateTimeImmutable('now', $timezone))->setTimezone($timezone);
        $minute = ((int) $now->format('G') * 60) + (int) $now->format('i');

        return ['open' => $minute >= 480 && $minute < 960, 'hours' => self::HOURS];
    }

    public static function afterHoursReply(string $whatsappUrl): array
    {
        return [
            'body' => 'Terima kasih, pesan Anda sudah tersimpan. Tim Ilena sedang di luar jam layanan (' . self::HOURS . ') dan akan menindaklanjuti saat layanan dibuka kembali. Jika ingin bantuan sekarang, silakan hubungi kami melalui WhatsApp.',
            'whatsappUrl' => $whatsappUrl,
        ];
    }
}
