<?php

namespace App\Services;

use Closure;
use Config\Database;

/** Shared MySQL single-flight lock plus stale-while-refresh for public cache data. */
final class CoordinatedPublicCache
{
    public function remember(string $cacheKey, string $refreshName, int $freshTtl, int $staleTtl, Closure $producer, bool $force = false): mixed
    {
        $entry = cache()->get($cacheKey);
        $now = time();
        if (!$force && $this->valid($entry) && $now < $entry['fresh_until']) return $entry['value'];

        if (!$force && $this->valid($entry) && $now < $entry['stale_until']) {
            (new CacheRefreshQueue())->enqueue($refreshName);
            return $entry['value'];
        }

        $db = Database::connect();
        $lockName = $this->lockName($cacheKey);
        $locked = false;
        try {
            if ($db->DBDriver === 'MySQLi') {
                try {
                    $lock = $db->query('SELECT GET_LOCK(?, 10) AS acquired', [$lockName])->getRowArray();
                    $locked = (int) ($lock['acquired'] ?? 0) === 1;
                } catch (\Throwable $lockError) {
                    // Cache coordination is an optimization; a lock failure must not
                    // make a public page unavailable. The DB cache still serves stale
                    // data where available, otherwise this request rebuilds it.
                    log_message('warning', 'Public cache lock unavailable for {key}: {type}', [
                        'key' => $cacheKey, 'type' => get_class($lockError),
                    ]);
                }
            }

            if (!$locked && !$force) {
                $latest = cache()->get($cacheKey);
                if ($this->valid($latest) && time() < $latest['stale_until']) return $latest['value'];
            }

            $latest = cache()->get($cacheKey);
            if (!$force && $this->valid($latest) && time() < $latest['fresh_until']) return $latest['value'];

            $value = $producer();
            $fresh = CacheTtl::jitter($freshTtl);
            $envelope = [
                '__ilena_public_cache' => 1,
                'value' => $value,
                'fresh_until' => time() + $fresh,
                'stale_until' => time() + $fresh + max(0, $staleTtl),
            ];
            cache()->save($cacheKey, $envelope, $fresh + max(0, $staleTtl));
            return $value;
        } finally {
            if ($locked && $db->DBDriver === 'MySQLi') {
                try { $db->query('SELECT RELEASE_LOCK(?)', [$lockName]); } catch (\Throwable $ignored) {}
            }
        }
    }

    /** Keep a usable stale value while scheduling one deduplicated background refresh. */
    public function invalidate(string $cacheKey, string $refreshName): void
    {
        $entry = cache()->get($cacheKey);
        if ($this->valid($entry) && time() < $entry['stale_until']) {
            $entry['fresh_until'] = 0;
            $remaining = max(1, (int) $entry['stale_until'] - time());
            cache()->save($cacheKey, $entry, $remaining);
        } else {
            cache()->delete($cacheKey);
        }

        (new CacheRefreshQueue())->enqueue($refreshName);
    }

    /** Drop a cache entry when an editor action must be visible on the next request. */
    public function invalidateImmediately(string $cacheKey): void
    {
        $db = Database::connect();
        $locked = false;
        $lockName = $this->lockName($cacheKey);
        try {
            if ($db->DBDriver === 'MySQLi') {
                try {
                    $lock = $db->query('SELECT GET_LOCK(?, 5) AS acquired', [$lockName])->getRowArray();
                    $locked = (int) ($lock['acquired'] ?? 0) === 1;
                } catch (\Throwable $lockError) {
                    log_message('warning', 'Immediate public cache invalidation lock unavailable for {key}: {type}', [
                        'key' => $cacheKey, 'type' => get_class($lockError),
                    ]);
                }
            }
            cache()->delete($cacheKey);
        } finally {
            if ($locked && $db->DBDriver === 'MySQLi') {
                try { $db->query('SELECT RELEASE_LOCK(?)', [$lockName]); } catch (\Throwable $ignored) {}
            }
        }
    }

    private function lockName(string $cacheKey): string
    {
        return 'ilena:cache:' . substr(hash('sha256', $cacheKey), 0, 48);
    }

    private function valid($entry): bool
    {
        return is_array($entry)
            && ($entry['__ilena_public_cache'] ?? null) === 1
            && array_key_exists('value', $entry)
            && is_numeric($entry['fresh_until'] ?? null)
            && is_numeric($entry['stale_until'] ?? null);
    }
}
