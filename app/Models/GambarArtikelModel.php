<?php

namespace App\Models;

use CodeIgniter\Model;

class GambarArtikelModel extends Model
{
    protected $table = 'gambar_artikel';
    protected $allowedFields = [
        'id',
        'url',
    ];
}
