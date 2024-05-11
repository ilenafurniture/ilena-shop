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
        $product = $this->barangModel->getBarangAdmin();
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
        $d = strtotime("+7 Hours");
        $tanggal = "B" . date("YmdHis", $d);
        $data = $this->request->getVar();
        $data_gambar_mentah = $this->request->getFiles();
        $data_gambar = [];
        foreach ($data_gambar_mentah as $key => $g) {
            if ($g->isValid()) $data_gambar[$key] = $g;
        }
        $jumlahVarian = explode(",", $this->request->getVar('hitung-varian'));

        $insertGambarBarang = [
            'id' => $tanggal
        ];
        $varianData = [];
        $counterGambar = 0;
        foreach ($jumlahVarian as $j) {
            $urutanGambar = [];
            foreach ($data_gambar as $ind_g => $g) {
                if (explode("-", $ind_g)[1] == $j) {
                    $counterGambar++;
                    array_push($urutanGambar, $counterGambar);
                }
            }
            $itemVarian = [
                'nama' => $data['nama-var' . $j],
                'kode' => $data['kode-var' . $j],
                'stok' => $data['stok-var' . $j],
                'urutan_gambar' => implode(",", $urutanGambar),
            ];
            array_push($varianData, $itemVarian);
        }
        $insertDataBarang = [
            'id' => $tanggal,
            'nama' => $data['nama'],
            'harga' => $data['harga'],
            'pencarian' => '',
            'rate' => '0',
            'deskripsi' => json_encode([
                'deskripsi' => $data['deskripsi'],
                'dimensi' => [
                    'asli' => [
                        'panjang' => $data['panjang-asli'],
                        'lebar' => $data['lebar-asli'],
                        'tinggi' => $data['tinggi-asli'],
                        'berat' => $data['berat-asli'],
                    ],
                    'paket' => [
                        'panjang' => $data['panjang-paket'],
                        'lebar' => $data['lebar-paket'],
                        'tinggi' => $data['tinggi-paket'],
                        'berat' => $data['berat-paket'],
                    ]
                ],
                'perawatan' => $data['perawatan']
            ]),
            'kategori' => $data['kategori'],
            'subkategori' => $data['subkategori'],
            'diskon' => $data['diskon'],
            'varian' => json_encode($varianData),
            'shopee' => $data['shopee'],
            'tokped' => $data['tokped'],
            'tiktok' => '',
            'active' => '1',
        ];
        $index_data_gambar = array_flip(array_keys($data_gambar));
        foreach ($data_gambar as $key_dg => $dG) {
            $insertGambarBarang['gambar' . ((int)$index_data_gambar[$key_dg] + 1)] = file_get_contents($dG);
        }
        $this->barangModel->insert($insertDataBarang);
        $this->gambarBarangModel->insert($insertGambarBarang);
        return redirect()->to('/listproduct');
    }
    public function editProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        // $product['pencarian'] = json_decode($product['pencarian'],true);  
        $product['deskripsi'] = json_decode($product['deskripsi'], true);
        $product['varian'] = json_decode($product['varian'], true);
        $data = [
            'title' => 'Tambah Produk',
            'produk' => $product
        ];
        return view('admin/edit', $data);
    }
    public function activeProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        $this->barangModel->where(['id' => $id_product])->set(['active' => $product['active'] == '0' ? '1' : '0'])->update();
        $arr = [
            'pesan' => 'Ok',
        ];
        return $this->response->setJSON($arr, false);
    }
    public function deleteProduct($id_product)
    {
        $produk = $this->barangModel->where('id', $id_product)->delete();
        $gambar = $this->gambarBarangModel->where('id', $id_product)->delete();
        return redirect()->to('/listproduct');
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
