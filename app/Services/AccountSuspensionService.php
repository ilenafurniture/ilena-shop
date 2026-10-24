<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class AccountSuspensionService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function isSuspended(string $email): bool
    {
        $state = $this->state($email);
        return $state !== null;
    }

    /** Return active suspension details; expired suspensions are cleared lazily. */
    public function state(string $email): ?array
    {
        $email = strtolower(trim($email));
        if ($email === '' || !$this->db->tableExists('user') || !$this->hasSuspensionSchema()) {
            return null;
        }

        $row = $this->db->table('user')
            ->select('suspended_until, suspension_reason, suspended_by')
            ->where('email', $email)
            ->get(1)
            ->getRowArray();

        if (!$row || empty($row['suspended_until'])) {
            return null;
        }

        try {
            $until = new \DateTimeImmutable((string)$row['suspended_until'], new \DateTimeZone('UTC'));
        } catch (\Throwable $exception) {
            $until = null;
        }
        if ($until === null || $until->getTimestamp() <= time()) {
            $this->clear($email);
            return null;
        }

        return [
            'until' => $until->format('Y-m-d H:i:s'),
            'reason' => (string)($row['suspension_reason'] ?? ''),
            'by' => (string)($row['suspended_by'] ?? ''),
        ];
    }

    public function suspendCustomer(string $email, int $days, string $reason, string $adminEmail): bool
    {
        $email = strtolower(trim($email));
        $adminEmail = strtolower(trim($adminEmail));
        if ($days < 1 || $days > 365 || $email === '' || !$this->hasSuspensionSchema()) {
            return false;
        }

        $customer = $this->db->table('user')
            ->select('email, role')
            ->where('email', $email)
            ->whereIn('role', ['0', '4'])
            ->get(1)
            ->getRowArray();
        if (!$customer) {
            return false;
        }

        $until = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->modify('+' . $days . ' days')
            ->format('Y-m-d H:i:s');

        return $this->db->table('user')->where('email', $email)->update([
            'suspended_until' => $until,
            'suspension_reason' => mb_substr(trim($reason), 0, 500),
            'suspended_by' => $adminEmail,
        ]);
    }

    public function reinstateCustomer(string $email): bool
    {
        $email = strtolower(trim($email));
        if ($email === '' || !$this->hasSuspensionSchema()) {
            return false;
        }

        return $this->db->table('user')
            ->where('email', $email)
            ->whereIn('role', ['0', '4'])
            ->update([
                'suspended_until' => null,
                'suspension_reason' => null,
                'suspended_by' => null,
            ]);
    }

    /** Ends a suspended customer's session on the next authenticated request. */
    public function enforceSession(): ?array
    {
        if (!in_array((string)session()->get('role'), ['0', '4'], true)) {
            return null;
        }

        $state = $this->state((string)session()->get('email'));
        if ($state !== null) {
            session()->destroy();
        }

        return $state;
    }

    private function hasSuspensionSchema(): bool
    {
        foreach (['suspended_until', 'suspension_reason', 'suspended_by'] as $field) {
            if (!$this->db->fieldExists($field, 'user')) {
                return false;
            }
        }

        return true;
    }

    private function clear(string $email): void
    {
        $this->db->table('user')->where('email', $email)->update([
            'suspended_until' => null,
            'suspension_reason' => null,
            'suspended_by' => null,
        ]);
    }
}
