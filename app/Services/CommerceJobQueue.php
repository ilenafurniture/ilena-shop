<?php

namespace App\Services;

use Config\Database;
use RuntimeException;

/** Database-backed durable queue with unique job keys and atomic worker claims. */
final class CommerceJobQueue
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function enqueue(string $key, string $type, string $orderId, array $payload): int
    {
        if ($key === '' || $type === '' || $orderId === '') throw new RuntimeException('Commerce job identity is incomplete.');
        $json = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $now = gmdate('Y-m-d H:i:s');
        $sql = 'INSERT INTO commerce_jobs (job_key,job_type,order_id,payload,status,attempts,available_at,created_at,updated_at) '
            . 'VALUES (?,?,?,?,\'queued\',0,?,?,?)';
        if ($this->db->DBDriver === 'MySQLi') $sql .= ' ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)';
        elseif ($this->db->DBDriver === 'SQLite3') $sql .= ' ON CONFLICT(job_key) DO NOTHING';
        $this->db->query($sql, [$key, $type, $orderId, $json, $now, $now, $now]);
        $row = $this->db->table('commerce_jobs')->select('id')->where('job_key', $key)->get()->getRowArray();
        $id = (int) ($row['id'] ?? 0);
        if ($id < 1) throw new RuntimeException('Commerce job could not be persisted.');
        return $id;
    }

    /** Claim one job under a transaction; competing workers cannot claim the same row. */
    public function claim(int $leaseSeconds = 120): ?array
    {
        $now = gmdate('Y-m-d H:i:s');
        $stale = gmdate('Y-m-d H:i:s', time() - max(30, $leaseSeconds));
        $this->db->transBegin();
        try {
            $staleJobs = $this->db->table('commerce_jobs')->where('status', 'processing')->where('locked_at <', $stale)->get()->getResultArray();
            foreach ($staleJobs as $staleJob) {
                $payload = json_decode((string) ($staleJob['payload'] ?? ''), true) ?: [];
                $safeRetry = !empty($payload['idempotency_key']) && (int) $staleJob['attempts'] < 5;
                $this->db->table('commerce_jobs')->where('id', (int) $staleJob['id'])->where('status', 'processing')->update([
                    'status' => $safeRetry ? 'queued' : 'uncertain', 'locked_at' => null,
                    'available_at' => $now, 'updated_at' => $now,
                    'last_error' => 'Worker lease expired; reconcile gateway status before any non-idempotent retry.',
                ]);
                if (!$safeRetry && ($staleJob['job_type'] ?? '') === 'midtrans.charge') {
                    $this->setCheckoutState((string) $staleJob['order_id'], 'uncertain');
                }
            }
            $sql = "SELECT * FROM commerce_jobs WHERE status='queued' AND available_at <= ? ORDER BY id ASC LIMIT 1";
            if ($this->db->DBDriver !== 'SQLite3') $sql .= ' FOR UPDATE';
            $job = $this->db->query($sql, [$now])->getRowArray();
            if (!$job) {
                $this->db->transCommit();
                return null;
            }
            $this->db->table('commerce_jobs')->where('id', (int) $job['id'])->where('status', 'queued')->update([
                'status' => 'processing', 'attempts' => (int) $job['attempts'] + 1,
                'locked_at' => $now, 'updated_at' => $now,
            ]);
            if ($this->db->affectedRows() !== 1) throw new RuntimeException('Commerce job claim lost a race.');
            $this->db->transCommit();
            $job['attempts'] = (int) $job['attempts'] + 1;
            return $job;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function complete(int $id): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->table('commerce_jobs')->where('id', $id)->where('status', 'processing')->update([
            'status' => 'completed', 'payload' => null, 'locked_at' => null,
            'completed_at' => $now, 'updated_at' => $now, 'last_error' => null,
        ]);
    }

    public function setCheckoutState(string $orderId, string $state): void
    {
        if (!in_array($state, ['queued', 'payment_ready', 'rejected', 'uncertain', 'failed'], true)) return;
        $order = $this->db->table('pemesanan')->where('id_midtrans', $orderId)->get()->getRowArray();
        if (!$order) return;
        $mid = json_decode((string) ($order['data_mid'] ?? '{}'), true) ?: [];
        unset($mid['_app_charge_in_flight'], $mid['_app_charge_started_at']);
        $mid['_app_checkout_queue_status'] = $state;
        $mid['_app_checkout_queue_updated_at'] = gmdate('Y-m-d H:i:s');
        $this->db->table('pemesanan')->where('id_midtrans', $orderId)->update(['data_mid' => json_encode($mid, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
    }

    /** A known-safe retry; uncertain provider outcomes must be parked, never blindly charged twice. */
    public function retryOrPark(array $job, string $reason, bool $safeToRetry): void
    {
        $attempts = (int) ($job['attempts'] ?? 1);
        $retry = $safeToRetry && $attempts < 5;
        $delay = min(300, 5 * (2 ** max(0, $attempts - 1)));
        $this->db->table('commerce_jobs')->where('id', (int) $job['id'])->where('status', 'processing')->update([
            'status' => $retry ? 'queued' : ($safeToRetry ? 'failed' : 'uncertain'),
            'available_at' => gmdate('Y-m-d H:i:s', time() + $delay), 'locked_at' => null,
            'last_error' => mb_substr($reason, 0, 1000), 'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);
        if (($job['job_type'] ?? '') === 'midtrans.charge') {
            $this->setCheckoutState((string) ($job['order_id'] ?? ''), $retry ? 'queued' : ($safeToRetry ? 'failed' : 'uncertain'));
        }
    }
}
