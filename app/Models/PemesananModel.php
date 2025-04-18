<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananModel extends Model
{
    protected $table = 'pemesanan';
    protected $allowedFields = [
        'nama',
        'email',
        'nohp',
        'alamat',
        'resi',
        'id_midtrans',
        'items',
        'status',
        'kurir',
        'data_mid',
        'id_marketplace',
        'status_print',
        'keterangan_suratjalan'
    ];

    public function getPemesanan($id_midtrans = false)
    {
        if (!$id_midtrans) {
            return $this->orderBy('id', 'desc')->findAll();
        }
        return $this->where(['id_midtrans' => $id_midtrans])->first();
    }
    public function getPemesananCus($emailCus)
    {
        return $this->where(['email' => $emailCus])->orderBy('id', 'desc')->findAll();
    }
    public function getPemesananPage($page)
    {
        // $hitungPag = floor($page / 20);
        $hitungPag = 20 * ($page - 1);
        if ($page > 1) {
            return $this->orderBy('id', 'desc')->findAll(20, $hitungPag);
        } else {
            return $this->orderBy('id', 'desc')->findAll(20, 0);
        }
    }

    public function getPemesananMarket($id_midtrans = false)
    {
        if (!$id_midtrans) {
            return $this->like('id_midtrans', 'MP', 'both')->orderBy('id', 'desc')->findAll();
        }
        return $this->where([
            'id_midtrans' => $id_midtrans,
        ])->first();
    }
}
