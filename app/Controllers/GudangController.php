<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;

class GudangController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->session = \Config\Services::session();
    }

    public function listOrder()
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
        ];
        return view('gudang/listOrder', $data);
    }

    public function scanOrder()
    {
        $data = [
            'title' => 'Scan'
        ];
        return view('gudang/scanOrder', $data);
    }
    public function actionScan()
    {
        $id_barang = $this->request->getVar('id_produk');
        $produk = $this->barangModel->getBarang($id_barang);
        $produk['varian'] = json_decode($produk['varian'], true);
        $data = [
            'title' => 'Pilih Varian',
            'produk' => $produk
        ];
        if (count($produk['varian']) > 1) {
            return view('gudang/pilihVarian', $data);
        } else {
            echo 'ini harunya langsung ngurang karena hanya punya 1 varian yaitu varian ' . $produk['varian'][0]['nama'];
        }
    }
}
