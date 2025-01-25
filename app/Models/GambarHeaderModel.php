<?php

namespace App\Models;

use CodeIgniter\Model;

class GambarHeaderModel extends Model
{
    protected $table = 'gambar_header';
    protected $allowedFields = [
        'id',
        'foto',
        'foto_hp',
    ];

    public function getGambar($id)
    {
        return $this->where(['id' => $id])->first();
    }
}
