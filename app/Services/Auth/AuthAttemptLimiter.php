<?php

namespace App\Services\Auth;

/** Shared IP + identity throttle for public login, signup, and OTP resend. */
final class AuthAttemptLimiter
{
    private $check;

    public function __construct(?callable $check = null)
    {
        $this->check = $check ?? static fn(string $key, int $limit, int $seconds): bool =>
            service('throttler')->check($key, $limit, $seconds);
    }

    public function allow(string $scope, string $ip, string $identity, int $ipLimit, int $identityLimit, int $window): bool
    {
        if (!preg_match('/^[a-z_]{2,32}$/', $scope)) return false;
        $ipKey = 'ilena-auth-' . $scope . '-ip-' . hash('sha256', trim($ip));
        $identity = mb_strtolower(trim($identity));
        $identityKey = $identity === '' ? null : 'ilena-auth-' . $scope . '-identity-' . hash('sha256', $identity);

        $cacheDir = WRITEPATH . 'cache';
        if (!is_dir($cacheDir) && !@mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) return false;
        $lock = @fopen($cacheDir . DIRECTORY_SEPARATOR . 'ilena-auth-attempts.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) {
            if (is_resource($lock)) fclose($lock);
            return false;
        }

        try {
            $ipAllowed = ($this->check)($ipKey, max(1, $ipLimit), max(1, $window));
            $identityAllowed = $identityKey === null || ($this->check)($identityKey, max(1, $identityLimit), max(1, $window));
            return $ipAllowed && $identityAllowed;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
