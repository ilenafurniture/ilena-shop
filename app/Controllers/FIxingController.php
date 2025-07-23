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
            try {
                $varian = json_decode($barang['varian'], true);
                $adaHitam = count(array_filter($varian, function($v) {
                    return strtolower($v['nama']) == 'hitam';
                })) > 0;
                foreach ($varian as $ind_v => $v) {
                    if(count($varian) > 1) {
                        if($adaHitam) {
                            $id_varian = strtolower($v['nama']) == 'hitam' ? '01' : '02';
                        } else {
                            $id_varian = '0'. ($ind_v + 1);
                        }
                    } else {
                        $id_varian = '01';
                    }
                    $varian[$ind_v]['id'] = $id_varian;
                }
                $this->barangModel->where('id', $barang['id'])
                    ->set(['varian' => json_encode($varian)])
                    ->update();
                $checking[] = [
                    'id' => $barang['id'],
                    'nama' => $barang['nama'],
                    'varian' => $varian,
                    'status' => 'success'
                ];
            } catch (\Throwable $th) {
                //throw $th;
                $checking[] = [
                    'id' => $barang['id'],
                    'nama' => $barang['nama'],
                    'varian' => $varian,
                    'status' => 'error'
                ];
            }
        }
        dd($checking);
    }

    
}