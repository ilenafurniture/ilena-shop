<?php

namespace App\Services;

final class CacheRefreshWorker
{
    public function runOne(): bool
    {
        $queue = new CacheRefreshQueue();
        $job = $queue->claim();
        if (!$job) return false;

        try {
            (new PublicCacheRefreshRegistry())->refresh((string) $job['cache_name']);
            $queue->complete((int) $job['id']);
        } catch (\Throwable $error) {
            $queue->fail((int) $job['id'], get_class($error) . ': ' . $error->getMessage(), (int) $job['attempts']);
            log_message('error', 'Public cache refresh failed for {name}: {type}', [
                'name' => $job['cache_name'], 'type' => get_class($error),
            ]);
        }
        return true;
    }

    public function warmAll(): int
    {
        $registry = new PublicCacheRefreshRegistry();
        $warmed = 0;
        foreach (PublicCacheRefreshRegistry::NAMES as $name) {
            $registry->refresh($name);
            $warmed++;
        }
        return $warmed;
    }
}
