<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectInteriorModel extends Model
{
    protected $table            = 'project_interior'; 
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields    = [
        'kode_project',
        'kode_sp',
        'kode_sj',
        'kode_barang',
        'nama_project',
        'status',          // draft / dp / termin / lunas
        'nilai_kontrak',
        'total_bayar',

        // opsional, kalau ada di tabelmu:
        'nama_customer',
        'nama_client',
        'nohp',
        'alamat_tagihan',
        'alamat_pengiriman',
        'alamat',
        'npwp',
        'no_po',
        
        'keterangan',
        'tanggal_invoice',
        'tanggal_sj',
    ];
}