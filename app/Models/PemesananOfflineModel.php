<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananOfflineModel extends Model
{
    protected $table = 'pemesanan_offline';
    protected $allowedFields = [
        'nama',
        'nohp',
        'alamat_pengiriman',
        'alamat_tagihan',
        'npwp',
        'tanggal',
        'id_pesanan',
        'status',
        'total_akhir',
        'jenis',
        'keterangan',
        'po',
    ];

    public function getPemesanan($id_pesanan)
    {
        return $this->where(['id_pesanan' => $id_pesanan])->first();
    }
}
