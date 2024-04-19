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
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Home',
            'produk' => $produk,
            'wishlist' => $wishlist
        ];
        return view('pages/home', $data);
    }

    public function find()
    {
        $data = [
            'title' => 'find',
        ];
    }
    public function product($id = false)
    {
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        if ($id) {
            $product = $this->barangModel->getBarang($id);
            $product['deskripsi'] = json_decode($product['deskripsi'], true);
            $product['varian'] = json_decode($product['varian'], true);
            $data = [
                'title' => 'produk',
                'produk' => $product,
                'wishlist' => $wishlist
            ];
            return view('pages/product', $data);
        } else {
            $product = $this->barangModel->getBarang();
            $data = [
                'title' => 'Produk Kami',
                'produk' => $product,
                'wishlist' => $wishlist
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
        $hargaTotal = 0;
        $keranjang = $this->session->get('keranjang');
        if (!isset($keranjang)) {
            $keranjang = [];
        }
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                }
            }
            $keranjang[$index]['detail'] = $produk;
            $hargaTotal += $produk['harga'] * $k['jumlah'] * (100 - $produk['diskon']) / 100;
        }

        $data = [
            'title' => 'Keranjang',
            'keranjang' => $keranjang,
            'hargaTotal' => $hargaTotal,
            'hargaKeseluruhan' => $hargaTotal + 5000
        ];
        return view('pages/cart', $data);
    }
    public function addCart($idbarang, $varian, $jumlah)
    {
        $keranjang = $this->session->get('keranjang');
        if (!isset($keranjang)) {
            $keranjang = [];
        }
        $ketemu = false;
        foreach ($keranjang as $index => $k) {
            if ($k['id_barang'] == $idbarang && $k['varian'] == $varian) {
                $keranjang[$index]['jumlah'] += $jumlah;
                $ketemu = true;
            }
        }
        if (!$ketemu) {
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

    public function reduceCart($index_cart)
    {
        $keranjang = $this->session->get('keranjang');
        $keranjang[$index_cart]['jumlah'] -= 1;
        if ($keranjang[$index_cart]['jumlah'] == 0) {
            unset($keranjang[$index_cart]);
            $keranjangBaru = array_values($keranjang);
            $this->session->set(['keranjang' => $keranjangBaru]);
            return redirect()->to('/cart');
        }
        $this->session->set(['keranjang' => $keranjang]);
        return redirect()->to('/cart');
    }

    public function deleteCart($index_cart)
    {
        $keranjang = $this->session->get('keranjang');
        unset($keranjang[$index_cart]);

        $keranjangBaru = array_values($keranjang);
        $this->session->set(['keranjang' => $keranjangBaru]);
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

        $hargaTotal = 0;
        $keranjang = $this->session->get('keranjang');
        if (!isset($keranjang)) {
            return redirect()->to('/product');
        }
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                }
            }
            $keranjang[$index]['detail'] = $produk;
            $hargaTotal += $produk['harga'] * $k['jumlah'] * (100 - $produk['diskon']) / 100;
        }

        $alamat = $this->session->get('alamat');
        if (!isset($alamat)) {
            $alamat = [];
        }
        // $alamat = [
        //     [
        //         'email_pemesan' => 'galihsuks@gmail.com',
        //         'nama_penerima' => 'Sukma Mukti',
        //         'nohp_penerima' => '0812345678',
        //         'prov' => 'Jawa Tengah',
        //         'kab' => 'Klaten',
        //         'kec' => 'Klaten Utara',
        //         'kel' => 'Jonggrangan',
        //         'alamat_tambahan' => 'Jl.Manahan No.123',
        //         'alamat_lengkap' => 'Jl.Manahan No.123 Jonggrangan, Klaten Utara, Klaten, Jawa Tengah'
        //     ],
        //     [
        //         'email_pemesan' => 'emaoildqwd@gmail.com',
        //         'nama_penerima' => 'Hidayatullah',
        //         'nohp_penerima' => '081445353',
        //         'prov' => 'Jawa Timur',
        //         'kab' => 'Surabaya',
        //         'kec' => 'Sukolilo',
        //         'kel' => 'Keputih',
        //         'alamat_tambahan' => 'Jl.Keputih Gang III',
        //         'alamat_lengkap' => 'Jl.Keputih Gang III Keputih, Sukolilo, Surabaya, Jawa Timur'
        //     ]
        // ];

        $data = [
            'title' => 'Alamat',
            'provinsi' => $provinsi["rajaongkir"]["results"],
            'keranjang' => $keranjang,
            'hargaTotal' => $hargaTotal,
            'hargaKeseluruhan' => $hargaTotal + 5000,
            'alamat' => $alamat
        ];
        return view('pages/address', $data);
    }
    public function addAddress()
    {
        $emailPem = $this->request->getVar('emailPem');
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');
        $provinsi = explode("-", $this->request->getVar('provinsi'));
        $kota = explode("-", $this->request->getVar('kota'));
        $kecamatan = explode("-", $this->request->getVar('kecamatan'));
        $kodepos = explode("-", $this->request->getVar('kodepos'));
        $alamatAdd = $this->request->getVar('alamat_add');

        $alamat = $this->session->get('alamat');
        if (!isset($alamat)) {
            $alamat = [];
        }
        array_push($alamat, [
            'email_pemesan' => $emailPem,
            'nama_penerima' => $nama,
            'nohp_penerima' => $nohp,
            'prov_id' => $provinsi[0],
            'prov' => $provinsi[1],
            'kab_id' => $kota[0],
            'kab' => $kota[1],
            'kec_id' => $kecamatan[0],
            'kec' => $kecamatan[1],
            'desa' => $kodepos[0],
            'kodepos' => $kodepos[1],
            'alamat_tambahan' => $alamatAdd,
            'alamat_lengkap' => $alamatAdd . " " . $kodepos[0] . ", " . $kecamatan[1] . ", " . $kota[1] . ", " . $provinsi[1] . " " . $kodepos[1]
        ]);
        $this->session->set(['alamat' => $alamat]);
        return redirect()->to('/address');
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
        $wishlist = $this->session->get('wishlist');
        $produk = [];
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        foreach ($wishlist as $w) {
            array_push($produk, $this->barangModel->getBarang($w));
        }
        $data = [
            'title' => 'Menu Favorite',
            'produk' => $produk,
            'wishlist' => $wishlist
        ];
        return view('pages/wishlist', $data);
    }
    public function addWishlist($id_barang)
    {
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        array_push($wishlist, $id_barang);
        $this->session->set(['wishlist' => $wishlist]);
        return redirect()->to('/wishlist');
    }
    public function delWishlist($id_barang)
    {
        $wishlist = session()->get('wishlist');
        if (($key = array_search($id_barang, $wishlist)) !== false) {
            unset($wishlist[$key]);
        }
        session()->set(['wishlist' => $wishlist]);
        return redirect()->to('/wishlist');
    }
    public function wishlistToCart()
    {
        $wishlist = $this->session->get('wishlist');
        $keranjang = $this->session->get('keranjang');
        if (!isset($keranjang)) {
            $keranjang = [];
        }
        foreach ($wishlist as $id_barang) {
            $produknya = $this->barangModel->getBarang($id_barang);
            $varian = json_decode($produknya['varian'], true)[0]['nama'];
            // $ketemu = false;
            // foreach ($keranjang as $index => $element) {
            //     if ($element['id'] == $id_barang && $element['varian'] == $varian) {
            //         $keranjang[$index]['jumlah'] += 1;
            //         $ketemu = true;
            //     }
            // }
            // if (!$ketemu) {
            //     $keranjangBaru = array(
            //         'id' => $id_barang,
            //         'jumlah' => 1,
            //         'varian' => $varian,
            //         'index_gambar' => 0
            //     );
            //     array_push($keranjang, $keranjangBaru);
            // }

            $ketemu = false;
            foreach ($keranjang as $index => $k) {
                if ($k['id_barang'] == $id_barang && $k['varian'] == $varian) {
                    $keranjang[$index]['jumlah']++;
                    $ketemu = true;
                }
            }
            if (!$ketemu) {
                array_push($keranjang, [
                    'id_barang' => $id_barang,
                    'varian' => $varian,
                    'jumlah' => 1
                ]);
            }
        }
        $this->session->set(['keranjang' => $keranjang]);
        return redirect()->to('/cart');
    }
}
