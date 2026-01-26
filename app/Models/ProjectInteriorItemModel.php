<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectInteriorItemModel extends Model
{
    protected $table      = 'project_interior_item';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'project_id',
        'kode_barang',
        'nama_barang',
        'harga_satuan',
        'qty',
        'subtotal',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = false;
}