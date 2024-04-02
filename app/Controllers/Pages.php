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
    public function index()
    {
        $produk  = $this->barangModel->getBarang();
        $data = [
            'title' => 'Home',
            'produk' => $produk,
        ];
        return view('pages/home', $data);
    }
    public function product($id = false)
    {
        if ($id) {
            $product = $this->barangModel->getBarang($id);
            $product['deskripsi'] = json_decode($product['deskripsi'], true);
            $product['varian'] = json_decode($product['varian'], true);
            $data = [
                'title' => 'produk',
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
    public function cart()
    {
        // $strukturKeranjang = [
        //     [
        //         'id_barang' => '001',
        //         'varian' => 'winge',
        //         'jumlah' => 1
        //     ],
        //     [
        //         'id_barang' => '002',
        //         'varian' => 'winge',
        //         'jumlah' => 1
        //     ],
        //     [
        //         'id_barang' => '003',
        //         'varian' => 'winge',
        //         'jumlah' => 1
        //     ],
        // ];
        $keranjang = $this->session->get('keranjang');
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if($v['nama'] == $k['varian'] ) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/".$k['id_barang']."/".explode(',', $v['urutan_gambar'])[0];
                    
                }
            }
            $keranjang[$index]['detail'] = $produk;
        }
        
        $data = [
            'title' => 'Keranjang',
            'keranjang' => $keranjang,
        ];
        return view('pages/cart', $data);
    }
    public function addCart($idbarang, $varian, $jumlah)
    {
        $keranjang = $this->session->get('keranjang');
        if(!isset($keranjang)){
            $keranjang = [];
        }
        $ketemu = false;
        foreach ($keranjang as $index => $k) {
            if($k['id_barang'] == $idbarang && $k['varian'] == $varian) {
                $keranjang[$index]['jumlah']++;
                $ketemu = true; 
            }
        }
        if(!$ketemu){
            array_push($keranjang, [
                'id_barang' => $idbarang,
                'varian' => $varian,
                'jumlah' => $jumlah
            ]);
        }
        $this->session->set([
            'keranjang' => $keranjang
        ]);
        return redirect()->to('/cart');
    }

    public function address()
    {
        //Dapatkan data provinsi
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $provinsi = json_decode($response, true);
        $data = [
            'title' => 'Alamat',
            'provinsi' => $provinsi["rajaongkir"]["results"],
        ];
        return view('pages/address', $data);
    }
    public function shipping()
    {
        $data = [
            'title' => 'Pengiriman',
        ];
        return view('pages/shipping', $data);
    }
    public function payment()
    {
        $data = [
            'title' => 'Pembayaran',
        ];
        return view('pages/payment', $data);
    }
    
    public  function progressPay()
    {
        $data = [
            'title' => 'Peroses Pembayaran',
        ];
        return view('pages/progresspay', $data);
        
    }
    public function wishlist()
    {
        $data = [
            'title' => 'Menu Favorite',
        ];
        return view('pages/wishlist', $data);

    }
}