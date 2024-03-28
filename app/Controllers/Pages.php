<?php

namespace App\Controllers;
use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;

class Pages extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Home',
        ];
        return view('pages/home', $data);
    }
    public function product($id = false)
    {
        if($id){
            $product = $this->barangModel->getBarang($id);
            $data = [
                'title' => $product['nama'],
                'produk' => $product
            ];
            return view('pages/product', $data);
        } else {
            $product = $this->barangModel->getBarang();
            $data = [
                'title' => 'Produk Kami',
            ];
            return view('pages/all', $data);
        }
    }
}