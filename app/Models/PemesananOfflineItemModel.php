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
        'jumlah',
        'varian',
        'id_return' // ini ID SP maupun SJ nya, pokonya ini unutk mengkoneksikannya
    ];
}
