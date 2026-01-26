<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanItemModel extends Model
{
    protected $table            = 'surat_jalan_item';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'surat_jalan_id',
        'id_barang',      // nullable untuk item interior
        'kode_barang',    // khusus interior (opsional)
        'nama_barang',    // khusus interior (opsional)
        'varian',
        'qty',
        'dimensi_json',   // khusus interior (opsional)
    ];
    protected $useTimestamps    = false;
}