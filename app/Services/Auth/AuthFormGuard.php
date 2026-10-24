<?php

namespace App\Services\Auth;

final class AuthFormGuard
{
    public function issue(string $form): string
    {
        $key = $this->key($form);
        $nonce = bin2hex(random_bytes(32));
        session()->set($key, ['nonce' => $nonce, 'issued_at' => time()]);
        return $nonce;
    }

    /** Consume a one-use server-backed form nonce and reject filled honeypots. */
    public function verify(string $form, $nonce, array $honeypots = [], int $minimumAgeSeconds = 0, int $maximumAgeSeconds = 3600): bool
    {
        $key = $this->key($form);
        $state = session()->get($key);
        session()->remove($key);

        foreach ($honeypots as $value) {
            if (is_string($value) && trim($value) !== '') return false;
            if (is_array($value) || is_object($value)) return false;
        }

        if (!is_array($state) || !is_string($nonce) || !is_string($state['nonce'] ?? null)
            || !hash_equals($state['nonce'], $nonce)) return false;

        $issuedAt = (int)($state['issued_at'] ?? 0);
        $age = time() - $issuedAt;
        return $issuedAt > 0 && $age >= $minimumAgeSeconds && $age <= $maximumAgeSeconds;
    }

    private function key(string $form): string
    {
        if (!in_array($form, ['login', 'register'], true)) throw new \InvalidArgumentException('Unknown authentication form.');
        return 'ilena_auth_form_' . $form;
    }
}
