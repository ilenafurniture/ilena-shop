<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanItemModel extends Model
{
    protected $table            = 'surat_jalan_item';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'surat_jalan_id',
        'id_barang',
        'varian',
        'qty',
    ];
    protected $useTimestamps    = false;
}