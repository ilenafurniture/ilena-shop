<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\KeranjangMarketModel;

class MarketplaceController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $keranjangMarketModel;
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
        $this->keranjangMarketModel = new KeranjangMarketModel();
        $this->session = \Config\Services::session();
    }
    public function product()
    {
        $product = $this->barangModel->getBarang();
        $data = [
            'title' => 'Semua Produk',
            'produk' => $product
        ];
        return view('market/product', $data);
    }

    public function cart()
    {
        $cart = $this->keranjangMarketModel->getKeranjang();
        $data = [
            'title' => 'Keranjang Produk',
            'cart' => $cart
        ];
        return view('market/cart', $data);
    }
}