<?php

namespace App\Services;

use Config\Database;

/** Small DB-backed queue so stale-page requests never perform refresh work. */
final class CacheRefreshQueue
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function enqueue(string $cacheName): void
    {
        $now = gmdate('Y-m-d H:i:s');
        try {
            if ($this->db->DBDriver === 'MySQLi') {
                $this->db->query(
                    "INSERT INTO cache_refresh_jobs (cache_name,status,attempts,available_at,created_at,updated_at) VALUES (?, 'queued',0,?,?,?) ON DUPLICATE KEY UPDATE attempts=IF(status IN ('completed','failed'),0,attempts), available_at=IF(status IN ('completed','failed'),VALUES(available_at),available_at), updated_at=IF(status IN ('completed','failed'),VALUES(updated_at),updated_at), last_error=IF(status IN ('completed','failed'),NULL,last_error), status=IF(status IN ('completed','failed'), 'queued', status)",
                    [$cacheName, $now, $now, $now]
                );
                return;
            }

            $existing = $this->db->table('cache_refresh_jobs')->where('cache_name', $cacheName)->get()->getRowArray();
            if (!$existing) {
                $this->db->table('cache_refresh_jobs')->insert([
                    'cache_name' => $cacheName, 'status' => 'queued', 'attempts' => 0,
                    'available_at' => $now, 'created_at' => $now, 'updated_at' => $now,
                ]);
            } elseif (in_array($existing['status'], ['completed', 'failed'], true)) {
                $this->db->table('cache_refresh_jobs')->where('id', (int) $existing['id'])->update([
                    'status' => 'queued', 'attempts' => 0, 'available_at' => $now,
                    'locked_at' => null, 'last_error' => null, 'updated_at' => $now,
                ]);
            }
        } catch (\Throwable $error) {
            // Keep stale responses available even during a migration or queue outage.
            log_message('debug', 'Cache refresh enqueue skipped for {name}: {type}', [
                'name' => $cacheName, 'type' => get_class($error),
            ]);
        }
    }

    public function claim(int $leaseSeconds = 180): ?array
    {
        $now = gmdate('Y-m-d H:i:s');
        $stale = gmdate('Y-m-d H:i:s', time() - max(60, $leaseSeconds));
        $this->db->transBegin();
        try {
            $this->db->table('cache_refresh_jobs')->where('status', 'processing')->where('locked_at <', $stale)
                ->update(['status' => 'queued', 'locked_at' => null, 'available_at' => $now, 'updated_at' => $now]);
            $sql = "SELECT * FROM cache_refresh_jobs WHERE status='queued' AND available_at <= ? ORDER BY id ASC LIMIT 1";
            if ($this->db->DBDriver !== 'SQLite3') $sql .= ' FOR UPDATE';
            $job = $this->db->query($sql, [$now])->getRowArray();
            if (!$job) {
                $this->db->transCommit();
                return null;
            }
            $this->db->table('cache_refresh_jobs')->where('id', (int) $job['id'])->where('status', 'queued')->update([
                'status' => 'processing', 'attempts' => (int) $job['attempts'] + 1,
                'locked_at' => $now, 'updated_at' => $now,
            ]);
            if ($this->db->affectedRows() !== 1) throw new \RuntimeException('Cache refresh job claim lost a race.');
            $this->db->transCommit();
            $job['attempts'] = (int) $job['attempts'] + 1;
            return $job;
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    public function complete(int $id): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->table('cache_refresh_jobs')->where('id', $id)->where('status', 'processing')->update([
            'status' => 'completed', 'locked_at' => null, 'last_error' => null, 'updated_at' => $now,
        ]);
    }

    public function fail(int $id, string $error, int $attempts): void
    {
        $retry = $attempts < 5;
        $delay = min(300, 5 * (2 ** max(0, $attempts - 1)));
        $this->db->table('cache_refresh_jobs')->where('id', $id)->where('status', 'processing')->update([
            'status' => $retry ? 'queued' : 'failed', 'locked_at' => null,
            'available_at' => gmdate('Y-m-d H:i:s', time() + $delay),
            'last_error' => mb_substr($error, 0, 500), 'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }
}
