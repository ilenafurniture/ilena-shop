<?php

namespace App\Models;

use CodeIgniter\Model;

class KartuStokModel extends Model
{
    protected $table = 'kartu_stok';
    protected $allowedFields = [
        'id_barang',
        'tanggal',
        'keterangan',
        'debit',
        'kredit',
        'saldo'
    ];

    public function getKartu($id_barang, $keterangan = false)
    {
        if (!$keterangan) {
            return $this->where(['id_barang' => $id_barang])->findAll();
        }
        return $this->where([
            'id_barang' => $id_barang,
            'keterangan' => $keterangan
        ])->first();
    }
}