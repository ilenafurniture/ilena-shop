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
            '20minutemail.com',
            '33mail.com',
            'anonaddy.com',
            'burnermail.io',
            'dispostable.com',
            'emailondeck.com',
            'fakeinbox.com',
            'getnada.com',
            'guerrillamail.com',
            'guerrillamail.net',
            'guerrillamail.org',
            'maildrop.cc',
            'mailinator.com',
            'mohmal.com',
            'sharklasers.com',
            'temp-mail.org',
            'tempmail.com',
            'tempmail.net',
            'tempmailo.com',
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

        if (in_array($domain, $this->disposableDomains(), true)) {
            return true;
        }

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
        $userRows = $db->table('user')
            ->select('email, role, active, waktu_otp')
            ->orderBy('email', 'asc')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $out = [];
        $seen = [];
        foreach ($userRows as $row) {
            $email = (string)($row['email'] ?? '');
            $isSuspicious = $this->isSuspiciousEmail($email);
            $isInactiveExpired = $this->isInactiveExpiredAccount($row, $inactiveOlderThanDays);

            if (!$isSuspicious && !$isInactiveExpired) {
                continue;
            }

            $out[] = [
                'email' => $email,
                'source' => 'user + pembeli',
                'role' => $row['role'] ?? '',
                'active' => $row['active'] ?? '',
                'waktu_otp' => $row['waktu_otp'] ?? '',
                'reason' => $isSuspicious
                    ? $this->reason($email)
                    : 'Akun belum aktif dan OTP kadaluarsa lebih dari ' . $inactiveOlderThanDays . ' hari',
            ];
            $seen[strtolower($email)] = true;
        }

        $pembeliRows = $db->table('pembeli p')
            ->select('p.email, u.email AS user_email')
            ->join('user u', 'u.email = p.email', 'left')
            ->orderBy('p.email', 'asc')
            ->limit($limit)
            ->get()
            ->getResultArray();

        foreach ($pembeliRows as $row) {
            $email = (string)($row['email'] ?? '');
            $emailKey = strtolower($email);
            if (isset($seen[$emailKey])) {
                continue;
            }

            $isSuspicious = $this->isSuspiciousEmail($email);
            $isOrphan = empty($row['user_email']);

            if (!$isSuspicious && !$isOrphan) {
                continue;
            }

            $out[] = [
                'email' => $email,
                'source' => $isOrphan ? 'pembeli tanpa user' : 'pembeli',
                'role' => '-',
                'active' => '-',
                'waktu_otp' => '-',
                'reason' => $isSuspicious ? $this->reason($email) : 'Data pembeli tidak punya pasangan akun user',
            ];
            $seen[$emailKey] = true;
        }

        return $out;
    }

    public function deleteEmails(array $emails): int
    {
        $emails = array_values(array_unique(array_filter(array_map('strval', $emails))));
        if (empty($emails)) return 0;

        $safeToDelete = [];
        foreach ($emails as $email) {
            $db = \Config\Database::connect();
            $row = $db
                ->table('user')
                ->select('email, role, active, waktu_otp')
                ->where('email', $email)
                ->get()
                ->getRowArray();
            $hasPembeli = $db->table('pembeli')->where('email', $email)->countAllResults() > 0;

            if ($this->isSuspiciousEmail($email)
                || ($row && $this->isInactiveExpiredAccount($row, 1))
                || (!$row && $hasPembeli)
            ) {
                $safeToDelete[] = $email;
            }
        }
        if (empty($safeToDelete)) return 0;

        $db = \Config\Database::connect();
        $db->transStart();
        $db->table('pembeli')->whereIn('email', $safeToDelete)->delete();
        $db->table('user')->whereIn('email', $safeToDelete)->delete();
        $db->transComplete();

        return $db->transStatus() ? count($safeToDelete) : 0;
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
