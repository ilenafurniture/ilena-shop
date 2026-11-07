<?php

namespace App\Services\Auth;

/** Lightweight, dependency-free templates for customer account emails. */
final class TransactionalEmailTemplate
{
    public static function verificationCode(string $code, string $locale = 'id'): string
    {
        $english = strtolower($locale) === 'en';
        $label = $english ? 'YOUR VERIFICATION CODE' : 'KODE VERIFIKASI ANDA';
        $hint = $english
            ? 'Select the code to copy it. On mobile, tap and hold the code.'
            : 'Pilih kode untuk menyalinnya. Di ponsel, tekan dan tahan kode.';

        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;background:#f8f5f1;border:1px solid #e8dfd7;border-left:4px solid #8f2430">'
            . '<tr><td align="center" style="padding:20px 16px 8px;font:700 11px Arial,sans-serif;letter-spacing:.12em;color:#625b55">' . $label . '</td></tr>'
            . '<tr><td align="center" style="padding:4px 16px 8px"><span style="display:inline-block;font:700 34px/1.3 Arial,Helvetica,sans-serif;letter-spacing:.24em;color:#8f2430;user-select:all;-webkit-user-select:all">' . esc($code) . '</span></td></tr>'
            . '<tr><td align="center" style="padding:0 16px 18px;font:12px/1.5 Arial,sans-serif;color:#625b55">' . $hint . '</td></tr>'
            . '</table>';
    }

    public static function html(string $title, string $intro, string $contentHtml, ?string $actionLabel = null, ?string $actionUrl = null, ?string $footer = null, string $locale = 'id'): string
    {
        $english = strtolower($locale) === 'en';
        $footer ??= $english
            ? 'This email concerns the security of your Ilena account. If you did not request this action, ignore this email or contact'
            : 'Email ini terkait keamanan akun Ilena Anda. Jika Anda tidak meminta tindakan ini, abaikan email ini atau hubungi';
        $supportLabel = $english ? 'Ilena support' : 'tim Ilena';
        $button = '';
        if ($actionLabel !== null && $actionUrl !== null && filter_var($actionUrl, FILTER_VALIDATE_URL)) {
            $button = '<tr><td align="center" style="padding:8px 28px 24px"><a href="' . esc($actionUrl, 'attr') . '" style="display:inline-block;background:#8f2430;color:#fff;text-decoration:none;padding:13px 24px;border-radius:4px;font:600 14px Arial,sans-serif">' . esc($actionLabel) . '</a></td></tr>';
        }

        return '<!doctype html><html lang="' . ($english ? 'en' : 'id') . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
            . '<body style="margin:0;background:#f6f4f1;color:#242220;font-family:Arial,Helvetica,sans-serif">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4f1;padding:28px 12px"><tr><td align="center">'
            . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#fff;border:1px solid #e9e4df">'
            . '<tr><td style="padding:20px 28px;border-bottom:2px solid #8f2430;font-size:16px;font-weight:700;letter-spacing:.08em;color:#262322">ILENA FURNITURE</td></tr>'
            . '<tr><td style="padding:28px 28px 10px"><h1 style="margin:0 0 14px;font-size:22px;line-height:1.35;color:#262322">' . esc($title) . '</h1>'
            . '<p style="margin:0;font-size:15px;line-height:1.7;color:#504b47">' . esc($intro) . '</p></td></tr>'
            . '<tr><td style="padding:8px 28px 18px;font-size:14px;line-height:1.7;color:#504b47">' . $contentHtml . '</td></tr>'
            . $button
            . '<tr><td style="padding:18px 28px;border-top:1px solid #eee9e4;font-size:12px;line-height:1.6;color:#625b55">' . esc($footer) . ' <a href="mailto:info@ilenafurniture.com" style="color:#8f2430">' . $supportLabel . '</a>.</td></tr>'
            . '</table></td></tr></table></body></html>';
    }

    public static function plain(string $title, string $intro, string $details, ?string $actionLabel = null, ?string $actionUrl = null, ?string $footer = null, string $locale = 'id'): string
    {
        $english = strtolower($locale) === 'en';
        $footer ??= $english
            ? 'This email concerns your Ilena account security. If you did not request this action, ignore it or contact'
            : 'Email terkait keamanan akun Ilena. Jika Anda tidak meminta tindakan ini, abaikan email ini atau hubungi';
        $text = "ILENA FURNITURE\n\n{$title}\n\n{$intro}";
        if ($details !== '') $text .= "\n\n{$details}";
        if ($actionLabel !== null && $actionUrl !== null && filter_var($actionUrl, FILTER_VALIDATE_URL)) $text .= "\n\n{$actionLabel}: {$actionUrl}";
        return $text . "\n\n{$footer} info@ilenafurniture.com.";
    }
}
