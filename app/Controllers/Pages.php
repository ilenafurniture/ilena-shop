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
        if ($id) {
            $product = $this->barangModel->getBarang($id);
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
        $data = [
            'title' => 'Keranjang',
        ];
        return view('pages/cart', $data);
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
}
