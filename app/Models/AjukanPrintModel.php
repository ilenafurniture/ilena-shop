<?php

namespace App\Models;

use CodeIgniter\Model;

class AjukanPrintModel extends Model
{
    protected $table = 'ajukan_print';
    protected $allowedFields = [
        'id_midtrans',
        'tanggal',
        'atas_nama',
        'kendala',
    ];
}
