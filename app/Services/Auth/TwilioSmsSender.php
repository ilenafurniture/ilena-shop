<?php

namespace App\Services\Auth;

final class TwilioSmsSender
{
    public function send(string $phoneE164, string $message): bool
    {
        $sid = trim((string) env('ILENA_TWILIO_ACCOUNT_SID'));
        $token = trim((string) env('ILENA_TWILIO_AUTH_TOKEN'));
        $from = trim((string) env('ILENA_TWILIO_FROM'));
        if ($sid === '' || $token === '' || $from === '') return false;
        if (! preg_match('/^AC[a-f0-9]{32}$/i', $sid) || ! preg_match('/^\+[1-9][0-9]{7,14}$/', $from)) return false;

        try {
            $response = service('curlrequest', [
                'timeout' => 8,
                'connect_timeout' => 4,
                'verify' => true,
            ])->post('https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($sid) . '/Messages.json', [
                'auth' => [$sid, $token],
                'form_params' => ['To' => $phoneE164, 'From' => $from, 'Body' => $message],
            ]);
            $payload = json_decode((string) $response->getBody(), true);
            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300
                && is_array($payload) && ! empty($payload['sid']);
        } catch (\Throwable $error) {
            log_message('warning', 'Phone verification SMS delivery failed: {type}', ['type' => get_class($error)]);
            return false;
        }
    }
}
