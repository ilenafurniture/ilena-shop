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
        'gambar_hover',
        'harga',
        'deskripsi',
        'kategori',
        'subkategori',
        'varian',
        'diskon',
        'shopee',
        'tokped',
        'tiktok',
        'active',
        'pengunjung',
        'ruang_tamu',
        'ruang_tidur',
        'ruang_keluarga',
    ];

    public function getBarang($id = false)
    {
        if ($id == false) {
            return $this->orderBy('nama', 'asc')->where(['active' => '1'])->findAll();
        }
        return $this->where(['id' => $id, 'active' => '1'])->first();
    }
    public function getBarangNama($nama = false)
    {
        if ($nama == false) {
            $seluruhBrang = $this->orderBy('nama', 'asc')->where(['active' => '1'])->findAll();
            $seluruhBarangFilter = [];
            $seluruhNama =  [];
            foreach ($seluruhBrang as $s) {
                if (!in_array($s['nama'], $seluruhNama)) {
                    array_push($seluruhBarangFilter, $s);
                    array_push($seluruhNama, $s['nama']);
                }
            }
            return $seluruhBarangFilter;
        }
        return $this->where(['id' => $nama, 'active' => '1'])->first();
    }
    public function getBarangAdmin($id = false)
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