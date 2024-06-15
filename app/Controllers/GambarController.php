<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;

class GambarController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
    }

    public function tampilGambarBarang($idBarang)
    {
        $gambar = $this->barangModel->getBarangAdmin($idBarang)['gambar'];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambar;
    }

    public function tampilGambarVarian($idBarang, $urutan)
    {
        $gambar = $this->gambarBarangModel->getGambar($idBarang);
        $gambarSelected = $gambar['gambar' . $urutan];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambarSelected;
    }
}
