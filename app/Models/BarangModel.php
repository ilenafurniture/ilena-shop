<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $allowedFields = [
        'id',
        'nama',
        'pencarian',
        'gambar',
        'harga',
        'deskripsi',
        'kategori',
        'subkategori',
        'diskon',
        'varian',
        'shopee',
        'tokped',
        'tiktok',
    ];

    public function getBarang($id = false)
    {
        if ($id == false) {
            return $this->orderBy('nama', 'asc')->findAll();
        }
        return $this->where(['id' => $id])->first();
    }
    public function getBarangLimit()
    {
        return $this->orderBy('nama', 'asc')->findAll(10, 0);
    }
    public function getBarangBaru()
    {
        return $this->orderBy('id', 'desc')->findAll(10, 0);
    }
    public function getBarangPage($page)
    {
        // $hitungPag = floor($page / 20);
        $hitungPag = 20 * ($page - 1);
        if ($page > 1) {
            return $this->orderBy('nama', 'asc')->findAll(20, $hitungPag);
        } else {
            return $this->orderBy('nama', 'asc')->findAll(20, 0);
        }
    }
}