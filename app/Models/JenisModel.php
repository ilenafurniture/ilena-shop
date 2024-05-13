<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisModel extends Model
{
    protected $table = 'jenis';
    protected $allowedFields = [
        'id',
        'nama',
    ];

    public function getJenis($id = false)
    {
        if (!$id) {
            return $this->findAll();
        }
        return $this->where(['id' => $id])->first();
    }
}
