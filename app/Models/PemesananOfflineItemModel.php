<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananOfflineItemModel extends Model
{
    protected $table = 'pemesanan_offline_item';
    protected $allowedFields = [
        'id_pesanan',
        'id_barang',
        'harga',
        'special_price', // satuannya persen
        'varian',
        'id_return'
    ];
}