<?php

namespace App\Services\Auth;

use Config\AuthCaptcha;

final class RecaptchaVerifier
{
    public function __construct(private ?AuthCaptcha $config = null, private $client = null)
    {
        $this->config ??= config(AuthCaptcha::class);
    }

    /** Returns a user-facing error or null. Tokens and provider response bodies are never logged. */
    public function error($token): ?string
    {
        if (!$this->config->enabled) return null;
        $hosts = array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', $this->config->allowedHostnames)))));
        if (trim($this->config->siteKey) === '' || trim($this->config->secretKey) === '' || $hosts === []) {
            log_message('error', 'Ilena authentication CAPTCHA is enabled but configuration is incomplete.');
            return lang('Store.authCaptchaUnavailable');
        }
        if (!is_string($token) || trim($token) === '' || strlen($token) > 8192) {
            return lang('Store.authCaptchaRequired');
        }

        try {
            $client = $this->client ?? \Config\Services::curlrequest([], null, null, false);
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => ['secret' => $this->config->secretKey, 'response' => $token],
                'connect_timeout' => 3,
                'timeout' => 8,
                'verify' => true,
                'http_errors' => false,
                'allow_redirects' => false,
            ]);
            if ($response->getStatusCode() !== 200) throw new \RuntimeException('CAPTCHA provider returned a non-200 response.');
            $body = json_decode((string)$response->getBody(), true);
            if (!is_array($body)) throw new \RuntimeException('CAPTCHA provider returned invalid JSON.');

            $hostname = is_string($body['hostname'] ?? null) ? strtolower($body['hostname']) : '';
            $issuedAt = is_string($body['challenge_ts'] ?? null) ? strtotime($body['challenge_ts']) : false;
            if (($body['success'] ?? false) !== true || !empty($body['error-codes'])
                || !in_array($hostname, $hosts, true) || $issuedAt === false
                || $issuedAt < time() - 120 || $issuedAt > time() + 30) {
                return lang('Store.authCaptchaRejected');
            }

            return null;
        } catch (\Throwable $error) {
            log_message('error', 'Ilena authentication CAPTCHA verification failed: {type}', ['type' => get_class($error)]);
            return lang('Store.authCaptchaInterrupted');
        }
    }
}
