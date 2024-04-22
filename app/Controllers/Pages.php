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

    public function getKota($id_prov)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/city?province=" . $id_prov,
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
        $kota = json_decode($response, true);
        return $this->response->setJSON($kota, false);
    }
    public function getKec($id_kota)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=" . $id_kota,
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
        $kec = json_decode($response, true);
        return $this->response->setJSON($kec, false);
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
    public function deleteAddress($ind_add)
    {
        $alamat = $this->session->get('alamat');
        unset($alamat[$ind_add]);
        $alamatBaru = array_values($alamat);
        $this->session->set(['alamat' => $alamatBaru]);
        return redirect()->to('/address');
    }
    public function shipping($ind_add)
    {
        $alamat = $this->session->get('alamat');
        if (!isset($alamat)) return redirect()->to('/address');
        $alamatselected = $alamat[$ind_add]; 
        $beratAkhir = 0;
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
            $beratAkhir += json_decode($produk['deskripsi'] , true)['dimensi']['paket']['berat'];
            
        }
        // dd($alamatselected);
        $kurir = [];
        $listKurir = ['jne','pos','tiki','wahana','sicepat','jnt','ninja','lion','anteraja'];
        foreach ($listKurir as $l) {
            $curl_jne = curl_init();
            curl_setopt_array($curl_jne, array(
                CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "origin=5504&originType=subdistrict&destination=" . $alamatselected['kec_id'] . "&destinationType=subdistrict&weight=" . $beratAkhir * 1000 . "&courier=".$l,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
                ),
            ));
            $response = curl_exec($curl_jne);
            $err = curl_error($curl_jne);
            curl_close($curl_jne);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $jne = json_decode($response, true);
            if(isset($jne)){
                foreach ($jne['rajaongkir']['results'][0]['costs'] as $j) {
                    $item_kurir = [ 
                        'nama' => $jne['rajaongkir']['results'][0]['code'],
                        'deskripsi' => $j['description'],
                        'harga' => $j['cost'][0]['value'],
                        'estimasi' => $j['cost'][0]['etd'],
                    ];
                    array_push($kurir, $item_kurir);
                }
            }
        }

        $curl_dakota = curl_init();
        $data_dakota = [
            'prov' => $alamatselected['prov'],
            'kab' => $alamatselected['kab'],
            'kec' => $alamatselected['kec'],
        ];
        curl_setopt_array($curl_dakota, array(
            CURLOPT_URL => "https://api.jasminefurniture.co.id/dakota",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data_dakota),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl_dakota);
        $err = curl_error($curl_dakota);
        curl_close($curl_dakota);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $dakota = json_decode($response, true);
        foreach ($dakota['data'] as $deskripsi => $value_dakota) {
            if ($deskripsi != 'UNIT') {
                $item_kurir = [ 
                    'nama' => 'dakota',
                    'deskripsi' => ucwords($deskripsi),
                    'harga' => $beratAkhir > (int)$value_dakota[0]['minkg'] ? (int)$value_dakota[0]['kgnext'] * $beratAkhir : (int)$value_dakota[0]['pokok'],
                    'estimasi' => $value_dakota[0]['LT'],
                ];
                array_push($kurir, $item_kurir);
            }
        }

        $data = [
            'title' => 'Pengiriman',
            'alamat' => $alamat[$ind_add],
            'keranjang' => $keranjang,
            'hargaTotal' => $hargaTotal,
            'hargaKeseluruhan' => $hargaTotal + 5000 + $kurir[0]['harga'],
            'kurir' => $kurir,
        ];

        $this->session->set(['kurir'=> $kurir]);  
        return view('pages/shipping', $data);
    }
    
    public function payment($index_kurir)
    {
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

        $kurir = $this->session->get('kurir');
        $data = [
            'title' => 'Pembayaran',
            'hargaTotal' => $hargaTotal,
            'hargaOngkir' => $kurir[$index_kurir]['harga'],
            'hargaKeseluruhan' => $hargaTotal + 5000 + $kurir[$index_kurir]['harga'],
        ];

        $this->session->set(['hargaKeseluruhan' => $hargaTotal + 5000 + $kurir[$index_kurir]['harga']]);
        return view('pages/payment', $data);
    }
    public function actionPay($metode)
    {
        // lanjut Besok
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