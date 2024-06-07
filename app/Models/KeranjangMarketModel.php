<?php

namespace App\Models;

use CodeIgniter\Model;

class KeranjangMarketModel extends Model
{
    protected $table = 'keranjang_market';
    protected $allowedFields = [
        'id_barang',
        'varian',
        'jumlah',
    ];

    public function getKeranjang($id = false)
    {
        if (!$id) {
            return $this->findAll();
        }
        return $this->where([
            'id' => $id
        ])->first();
    }
}