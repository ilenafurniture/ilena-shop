<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananGudangModel extends Model
{
    protected $table = 'pemesanan_gudang';
    protected $allowedFields = [
        'id_pesanan',
        'tanggal',
        'nama',
        'id_barang',
        'jumlah',
        'packed'
    ];
    public function getPemesananGudang($nama_barang = false)
    {
        if (!$nama_barang) {
            return $this->findAll();
        }
        return $this->where([
            'nama' => $nama_barang,
            'packed' => false
        ])->first();
    }
}
