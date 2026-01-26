<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectInteriorModel extends Model
{
    protected $table      = 'project_interior';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'kode_project',
        'nama_project',

        // link dokumen utama (offline)
        'kode_sj',   // SJ######## atau NF########
        'kode_sp',   // kalau masih dipakai, boleh tetap ada / null

        // pembayaran & status
        'total_dp',
        'total_bayar',
        'status',

        // info tambahan
        'no_po',
        'catatan',

        // pajak & total
        'ppn_mode',      // non|ppn10|ppn11
        'ppn_rate',      // 0|10|11
        'subtotal_dpp',
        'total_ppn',
        'grand_total',
    ];
}