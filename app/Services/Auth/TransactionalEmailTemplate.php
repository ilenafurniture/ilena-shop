<?php

namespace App\Services\Auth;

/** Lightweight, dependency-free templates for customer account emails. */
final class TransactionalEmailTemplate
{
    public static function html(string $title, string $intro, string $contentHtml, ?string $actionLabel = null, ?string $actionUrl = null, string $footer = 'Email ini terkait keamanan akun Ilena Anda. Jika Anda tidak meminta tindakan ini, abaikan email ini atau hubungi'): string
    {
        $button = '';
        if ($actionLabel !== null && $actionUrl !== null && filter_var($actionUrl, FILTER_VALIDATE_URL)) {
            $button = '<tr><td style="padding:20px 0 8px"><a href="' . esc($actionUrl, 'attr') . '" style="display:inline-block;background:#8f2430;color:#fff;text-decoration:none;padding:12px 22px;border-radius:4px;font:600 14px Arial,sans-serif">' . esc($actionLabel) . '</a></td></tr>';
        }

        return '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
            . '<body style="margin:0;background:#f6f4f1;color:#242220;font-family:Arial,Helvetica,sans-serif">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4f1;padding:28px 12px"><tr><td align="center">'
            . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#fff;border:1px solid #e9e4df">'
            . '<tr><td style="padding:22px 28px;border-bottom:2px solid #8f2430;font-size:17px;font-weight:700;letter-spacing:.04em;color:#262322">ILENA FURNITURE</td></tr>'
            . '<tr><td style="padding:28px 28px 10px"><h1 style="margin:0 0 14px;font-size:22px;line-height:1.35;color:#262322">' . esc($title) . '</h1>'
            . '<p style="margin:0;font-size:15px;line-height:1.7;color:#504b47">' . esc($intro) . '</p></td></tr>'
            . '<tr><td style="padding:8px 28px 26px;font-size:14px;line-height:1.7;color:#504b47">' . $contentHtml . '</td></tr>'
            . $button
            . '<tr><td style="padding:18px 28px;border-top:1px solid #eee9e4;font-size:12px;line-height:1.6;color:#77716c">' . esc($footer) . ' <a href="mailto:info@ilenafurniture.com" style="color:#8f2430">' . (str_starts_with($footer, 'This email') ? 'Ilena support' : 'tim Ilena') . '</a>.</td></tr>'
            . '</table></td></tr></table></body></html>';
    }

    public static function plain(string $title, string $intro, string $details, ?string $actionLabel = null, ?string $actionUrl = null, string $footer = 'Email terkait keamanan akun Ilena. Jika Anda tidak meminta tindakan ini, abaikan email ini atau hubungi info@ilenafurniture.com.'): string
    {
        $text = "ILENA FURNITURE\n\n{$title}\n\n{$intro}";
        if ($details !== '') $text .= "\n\n{$details}";
        if ($actionLabel !== null && $actionUrl !== null && filter_var($actionUrl, FILTER_VALIDATE_URL)) $text .= "\n\n{$actionLabel}: {$actionUrl}";
        return $text . "\n\n{$footer} info@ilenafurniture.com.";
    }
}
