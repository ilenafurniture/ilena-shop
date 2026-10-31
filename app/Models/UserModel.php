<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $allowedFields = [
        'email',
        'sandi',
        'role',
        'otp',
        'active',
        'waktu_otp',
        'suspended_until',
        'suspension_reason',
        'suspended_by',
        'auth_version',
    ];

    public function getUser($email = false)
    {
        return $this->where(['email' => $email])->first();
    }

    public function findByEmailIdentity(string $email): ?array
    {
        $email = strtolower(trim($email));
        $db = $this->db;
        $user = $db->table('user')->where('LOWER(TRIM(email))', $email)->get()->getRowArray();
        if ($user) return $user;
        if (! $db->tableExists('customer_email_aliases')) return null;
        $alias = $db->table('customer_email_aliases')->select('customer_email')->where('alias_email', $email)->get()->getRowArray();
        return empty($alias['customer_email']) ? null : $db->table('user')
            ->where('LOWER(TRIM(email))', strtolower((string) $alias['customer_email']))->get()->getRowArray();
    }
}
