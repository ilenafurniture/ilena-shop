<?php

namespace App\Services\Auth;

final class CustomerEmailIdentity
{
    public function canonical(string $email): ?string
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) return null;
        $db = db_connect();
        if (! $db->tableExists('customer_email_aliases')) return $email;
        $row = $db->table('customer_email_aliases')->select('customer_email')->where('alias_email', $email)->get()->getRowArray();
        return is_array($row) && ! empty($row['customer_email']) ? strtolower((string) $row['customer_email']) : $email;
    }

    /** @return list<string> Canonical address and retained historical aliases. */
    public function allFor(string $email): array
    {
        $canonical = $this->canonical($email);
        if ($canonical === null) return [];
        $emails = [$canonical];
        $db = db_connect();
        if ($db->tableExists('customer_email_aliases')) {
            $rows = $db->table('customer_email_aliases')->select('alias_email')->where('customer_email', $canonical)->get()->getResultArray();
            foreach ($rows as $row) {
                $alias = strtolower(trim((string) ($row['alias_email'] ?? '')));
                if ($alias !== '' && ! in_array($alias, $emails, true)) $emails[] = $alias;
            }
        }
        $requested = strtolower(trim($email));
        if (filter_var($requested, FILTER_VALIDATE_EMAIL) && ! in_array($requested, $emails, true)) $emails[] = $requested;
        return $emails;
    }
}
