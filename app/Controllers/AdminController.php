<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->session = \Config\Services::session();
    }
    public function listProduct()
    {
        $product = $this->barangModel->getBarang();
        $data = [
            'title' => 'Produk Kami',
            'produk' => $product
        ];
        return view('admin/all', $data);
    }
    public function customer()
    {
    }
    public function addProduct()
    {
        $data = [
            'title' => 'Tambah Produk'
        ];
        return view('admin/add', $data);
    }
    public function actionAddProduct()
    {
        
        $data = [
            'title' => 'Tambah Produk'
        ];
        return view('admin/add', $data);
    }
    public function editProduct($id_product)
    {
        $product = $this->barangModel->getBarang($id_product);
        // $product['pencarian'] = json_decode($product['pencarian'],true);  
        $product['deskripsi'] = json_decode($product['deskripsi'],true);  
        $product['varian'] = json_decode($product['varian'],true);  
        $data = [
            'title' => 'Tambah Produk',
            'produk' => $product
        ];
        return view('admin/editproduct', $data);
    }
    public function activeProduct($id_product)
    {
        $product = $this->barangModel->getBarang($id_product);
        $this->barangModel->where(['id' => $id_product])->set(['status' => !$product['status']])->update();
        //lanjut nanti
    }
    public function order()
    {
        $pesanan = $this->pemesananModel->getPemesanan();
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $pesanan[$ind_p]['alamat'] = json_decode($p['alamat'], true);
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
        }
        $data = [
            'title' => 'Pesanan',
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan)
        ];
        return view('admin/order', $data);
    }
}