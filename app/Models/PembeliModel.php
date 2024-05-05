<?php

namespace App\Models;

use CodeIgniter\Model;

class PembeliModel extends Model
{
    protected $table = 'pembeli';
    protected $allowedFields = [
        'email',
        'nama',
        'nohp',
        'alamat',
        'wishlist',
        'keranjang',
    ];

    public function getPembeli($email = false)
    {
        return $this->where(['email' => $email])->first();
    }
}
