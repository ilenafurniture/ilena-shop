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
    ];

    public function getUser($email = false)
    {
        return $this->where(['email' => $email])->first();
    }

    public function findByEmailIdentity(string $email): ?array
    {
        return $this->where('LOWER(TRIM(email))', strtolower(trim($email)))->first();
    }
}
