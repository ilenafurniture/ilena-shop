<?php

namespace App\Models;

use CodeIgniter\Model;

class KoleksiModel extends Model
{
    protected $table = 'koleksi';
    protected $allowedFields = [
        'id',
        'nama',
    ];

    public function getKoleksi($id = false)
    {
        if (!$id) {
            return $this->findAll();
        }
        return $this->where(['id' => $id])->first();
    }
}
