<?php

namespace App\Services;

class SpamAccountCleanupService
{
    public function spamPatterns(): array
    {
        return [
            '/*',
            '*/',
            '--',
            '#',
            'select',
            'union',
            'updatexml',
            'extractvalue',
            'procedure',
            'analyse',
            'benchmark',
            'sleep(',
            'row(',
            'information_schema',
            '@@',
            "'",
            '"',
            '<',
            '>',
            '(',
            ')',
        ];
    }

    public function disposableDomains(): array
    {
        return [
            '10minutemail.com',
            '10minutemail.net',
            '10minemail.com',
            '1secmail.com',
            '20minutemail.com',
            '33mail.com',
            'anonaddy.com',
            'anonaddy.me',
            'burnermail.io',
            'disposablemail.com',
            'dispostable.com',
            'duck.com',
            'emailfake.com',
            'emailondeck.com',
            'emailtemporanea.net',
            'fakeinbox.com',
            'getairmail.com',
            'getnada.com',
            'guerrillamail.com',
            'guerrillamailblock.com',
            'guerrillamail.net',
            'guerrillamail.org',
            'inboxkitten.com',
            'mail.tm',
            'mail.gw',
            'mailcatch.com',
            'maildrop.cc',
            'mailinator.com',
            'mailnesia.com',
            'mintemail.com',
            'mohmal.com',
            'mytemp.email',
            'mozmail.com',
            'privaterelay.appleid.com',
            'relay.firefox.com',
            'simplelogin.com',
            'simplelogin.co',
            'simplelogin.io',
            'sharklasers.com',
            'spam4.me',
            'temp-mail.io',
            'temp-mail.org',
            'tempmail.com',
            'tempmail.net',
            'tempmail.plus',
            'tempmail.ninja',
            'tempmailo.com',
            'throwaway.email',
            'throwawaymail.com',
            'trashmail.com',
            'yopmail.com',
        ];
    }

    public function isDisposableEmail(string $email): bool
    {
        $email = strtolower(trim($email));
        $domain = substr(strrchr($email, '@') ?: '', 1);
        if ($domain === '') return false;

        foreach ($this->disposableDomains() as $blocked) {
            if ($domain === $blocked || str_ends_with($domain, '.' . $blocked)) return true;
        }

        if ((new \App\Services\Auth\DisposableEmailDomainList())->isListed($domain)) return true;

        foreach (['tempmail', 'temp-mail', 'mailinator', 'guerrillamail', 'yopmail', 'maildrop', 'throwaway', 'trashmail'] as $keyword) {
            if (str_contains($domain, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function isSuspiciousEmail(string $email): bool
    {
        $email = trim($email);
        $lower = strtolower($email);

        if ($email === '') return true;
        if (strlen($email) > 191) return true;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
        if ($this->isDisposableEmail($email)) return true;

        foreach ($this->spamPatterns() as $pattern) {
            if (str_contains($lower, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }

    public function isInactiveExpiredAccount(array $row, int $olderThanDays = 7): bool
    {
        $active = (string)($row['active'] ?? '');
        $role = (string)($row['role'] ?? '');
        $otpExpiresAt = (int)($row['waktu_otp'] ?? 0);
        $cutoff = time() - (max(0, $olderThanDays) * 86400);

        return $role === '0'
            && $active === '0'
            && $otpExpiresAt > 0
            && $otpExpiresAt < $cutoff;
    }

    public function candidates(int $limit = 500, int $inactiveOlderThanDays = 7): array
    {
        $db = \Config\Database::connect();
        // Only list accounts that can safely be purged automatically. A suspicious
        // email address alone is never enough to delete an active/customer account.
        $cutoff = time() - (max(7, $inactiveOlderThanDays) * 86400);
        $userRows = $db->table('user')
            ->select('email, role, active, waktu_otp')
            ->where('role', '0')
            ->where('active', '0')
            ->where('waktu_otp >', 0)
            ->where('waktu_otp <', $cutoff)
            ->orderBy('email', 'asc')
            ->limit(max(1, min(1000, $limit)))
            ->get()
            ->getResultArray();

        $out = [];
        $seen = [];
        foreach ($userRows as $row) {
            $email = (string)($row['email'] ?? '');
            $isInactiveExpired = $this->isInactiveExpiredAccount($row, $inactiveOlderThanDays);
            if (!$isInactiveExpired || !$this->isSafeToDelete($db, $email)) {
                continue;
            }

            $out[] = [
                'email' => $email,
                'source' => 'user + pembeli',
                'role' => $row['role'] ?? '',
                'active' => $row['active'] ?? '',
                'waktu_otp' => $row['waktu_otp'] ?? '',
                'reason' => 'Akun pelanggan belum aktif, OTP kedaluwarsa lebih dari ' . max(7, $inactiveOlderThanDays) . ' hari, dan tidak memiliki pesanan/role admin',
            ];
            $seen[strtolower($email)] = true;
        }

        return array_slice($out, 0, max(1, min(1000, $limit)));
    }

    public function deleteEmails(array $emails): int
    {
        $emails = array_values(array_unique(array_filter(array_map('strval', $emails))));
        if (empty($emails)) return 0;

        $db = \Config\Database::connect();
        $deleted = 0;
        foreach ($emails as $email) {
            $db->transBegin();
            try {
                $row = $db->table('user')->select('email, role, active, waktu_otp')->where('email', $email)->get()->getRowArray();
                if (!$row || !$this->isInactiveExpiredAccount($row, 7) || !$this->isSafeToDelete($db, $email)) {
                    $db->transRollback();
                    continue;
                }
                $db->table('pembeli')->where('email', $email)->delete();
                $db->table('user')->where('email', $email)->where('role', '0')->where('active', '0')->delete();
                if ($db->transStatus()) {
                    $db->transCommit();
                    $deleted++;
                } else {
                    $db->transRollback();
                }
            } catch (\Throwable $e) {
                $db->transRollback();
                log_message('error', 'Spam cleanup skipped candidate: {message}', ['message' => $e->getMessage()]);
            }
        }
        return $deleted;
    }

    private function isSafeToDelete($db, string $email): bool
    {
        foreach (['pemesanan', 'pemesanan_online', 'orders'] as $table) {
            if (!$db->tableExists($table)) continue;
            $fields = $db->getFieldNames($table);
            if (in_array('email', $fields, true)
                && $db->table($table)->where('email', $email)->countAllResults() > 0) return false;
        }
        if ($db->tableExists('admin_rbac_user_roles')
            && $db->table('admin_rbac_user_roles')->where('email', $email)->countAllResults() > 0) return false;
        return true;
    }

    private function reason(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid / payload injection';
        }

        $lower = strtolower($email);
        if ($this->isDisposableEmail($email)) {
            return 'Domain email tempmail/disposable';
        }

        foreach ($this->spamPatterns() as $pattern) {
            if (str_contains($lower, strtolower($pattern))) {
                return 'Mengandung pola mencurigakan: ' . $pattern;
            }
        }

        return 'Mencurigakan';
    }
}
