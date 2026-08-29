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

    public function candidates(int $limit = 500): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('user')
            ->select('email, role, active, waktu_otp')
            ->orderBy('email', 'asc')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $email = (string)($row['email'] ?? '');
            if (!$this->isSuspiciousEmail($email)) {
                continue;
            }

            $out[] = [
                'email' => $email,
                'role' => $row['role'] ?? '',
                'active' => $row['active'] ?? '',
                'waktu_otp' => $row['waktu_otp'] ?? '',
                'reason' => $this->reason($email),
            ];
        }

        return $out;
    }

    public function deleteEmails(array $emails): int
    {
        $emails = array_values(array_unique(array_filter(array_map('strval', $emails))));
        if (empty($emails)) return 0;

        $safeToDelete = [];
        foreach ($emails as $email) {
            if ($this->isSuspiciousEmail($email)) {
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
