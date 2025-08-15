<?php

namespace App\Controllers;

use App\Models\BarangModel;

class FixingController extends BaseController
{
    protected $barangModel;
 
    public function __construct()
    {
        $this->barangModel = new BarangModel();
    }

    public function fixIdBarang()
{
    $barangAll = $this->barangModel->findAll();
    $checking = [];

    foreach ($barangAll as $barang) {
        $varian = []; // inisialisasi awal
        try {
            $decoded = json_decode($barang['varian'] ?? '[]', true);
            $varian = is_array($decoded) ? $decoded : [];

            $adaHitam = array_reduce($varian, function($acc, $v) {
                return $acc || (isset($v['nama']) && strtolower($v['nama']) === 'hitam');
            }, false);

            foreach ($varian as $ind_v => $v) {
                $nama = strtolower($v['nama'] ?? '');
                if (count($varian) > 1) {
                    if ($adaHitam) {
                        $id_varian = $nama === 'hitam' ? '01' : '02';
                    } else {
                        $id_varian = str_pad((string) ($ind_v + 1), 2, '0', STR_PAD_LEFT);
                    }
                } else {
                    $id_varian = '01';
                }
                $varian[$ind_v]['id'] = $id_varian;
            }

            $this->barangModel->where('id', $barang['id'])
                ->set(['varian' => json_encode($varian, JSON_UNESCAPED_UNICODE)])
                ->update();

            $checking[] = [
                'id' => $barang['id'],
                'nama' => $barang['nama'],
                'varian' => $varian,
                'status' => 'success',
            ];
        } catch (\Throwable $th) {
            $checking[] = [
                'id' => $barang['id'],
                'nama' => $barang['nama'],
                'varian' => $varian,
                'status' => 'error',
                'error' => $th->getMessage(),
            ];
        }
    }

    return $this->response->setJSON($checking);
}

    
}