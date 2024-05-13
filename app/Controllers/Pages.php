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
        $this->session->set(['keranjang' => $keranjang]);
        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['keranjang' => json_encode($keranjang)])->update();
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
        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['keranjang' => json_encode($keranjang)])->update();
        return redirect()->to('/cart');
    }

    public function deleteCart($index_cart)
    {
        $keranjang = $this->session->get('keranjang');
        unset($keranjang[$index_cart]);

        $keranjangBaru = array_values($keranjang);
        $this->session->set(['keranjang' => $keranjangBaru]);
        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['keranjang' => json_encode($keranjangBaru)])->update();
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
        $keranjang = session()->get('keranjang');
        if (!isset($keranjang)) {
            return redirect()->to('/cart');
        } else {
            if (count($keranjang) <= 0) {
                return redirect()->to('/cart');
            }
        }

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
            'alamat' => $alamat,
            'email' => session()->get('email') ? session()->get('email') : '',
            'nama' => session()->get('nama') ? session()->get('nama') : '',
            'nohp' => session()->get('nohp') ? session()->get('nohp') : '',
        ];
        return view('pages/address', $data);
    }
    public function addAddress()
    {
        $checkPage = $this->request->getVar('checkpage');
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

        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamat)])->update();
        return redirect()->to($checkPage == 'address' ? '/address' : '/account');
    }
    public function deleteAddress($ind_add)
    {
        $alamat = $this->session->get('alamat');
        unset($alamat[$ind_add]);
        $alamatBaru = array_values($alamat);
        $this->session->set(['alamat' => $alamatBaru]);

        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamatBaru)])->update();
        return redirect()->to('/account');
    }
    public function editAddress($ind_add)
    {
        $emailPem = $this->request->getVar('emailPem');
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');
        $provinsi = explode("-", $this->request->getVar('provinsiEdit'));
        $kota = explode("-", $this->request->getVar('kotaEdit'));
        $kecamatan = explode("-", $this->request->getVar('kecamatanEdit'));
        $kodepos = explode("-", $this->request->getVar('kodeposEdit'));
        $alamatAdd = $this->request->getVar('alamat_add');

        $alamat = $this->session->get('alamat');
        $alamat[$ind_add] = [
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
        ];
        $this->session->set(['alamat' => $alamat]);

        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamat)])->update();
        return redirect()->to('/account');
    }
    public function shipping($ind_add)
    {
        $alamat = $this->session->get('alamat');
        if (!array_key_exists($ind_add, $alamat)) {
            return redirect()->to('/address');
        }
        if (!isset($alamat)) {
            return redirect()->to('/address');
        } else {
            if (count($alamat) == 0) {
                return redirect()->to('/address');
            }
        }

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
            $dimensiPaket = json_decode($produk['deskripsi'], true)['dimensi']['paket'];
            $beratVolume = ceil((float)$dimensiPaket['panjang'] * (float)$dimensiPaket['lebar'] * (float)$dimensiPaket['tinggi'] / 3500); //kg
            $beratAsli = (float)$dimensiPaket['berat'];
            $beratAkhir += ($beratVolume > $beratAsli ? $beratVolume : $beratAsli) * $k['jumlah'];
        }

        $kurir = [];
        $curl_kurir = curl_init();
        curl_setopt_array($curl_kurir, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=5504&originType=subdistrict&destination=" . $alamatselected['kec_id'] . "&destinationType=subdistrict&weight=" . $beratAkhir * 1000 . "&courier=jne:jnt:wahana:sentral",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl_kurir);
        $err = curl_error($curl_kurir);
        curl_close($curl_kurir);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $rajaOngkirCost = json_decode($response, true);
        if (isset($rajaOngkirCost)) {
            foreach ($rajaOngkirCost['rajaongkir']['results'] as $k) {
                foreach ($k['costs'] as $c) {
                    $item_kurir = [
                        'nama' => $k['code'],
                        'deskripsi' => $c['description'],
                        'harga' => $c['cost'][0]['value'],
                        'estimasi' => $c['cost'][0]['etd'],
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

        $this->session->set(['kurir' => $kurir]);
        $this->session->set(['alamatTerpilih' => $alamatselected]);
        return view('pages/shipping', $data);
    }
    public function payment($index_kurir)
    {
        $hargaTotal = 0;
        $keranjang = $this->session->get('keranjang');
        $kurir = $this->session->get('kurir');
        $alamatTerpilih = session()->get('alamatTerpilih');
        if (!isset($alamatTerpilih)) {
            return redirect()->to('/address');
        } else {
            if (count($alamatTerpilih) <= 0) {
                return redirect()->to('/address');
            }
        }
        if (!isset($kurir)) {
            return redirect()->to('/address');
        } else {
            if (count($kurir) <= 0) {
                return redirect()->to('/address');
            }
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
            'title' => 'Pembayaran',
            'hargaTotal' => $hargaTotal,
            'hargaOngkir' => $kurir[$index_kurir]['harga'],
            'hargaKeseluruhan' => ($hargaTotal + 5000 + $kurir[$index_kurir]['harga']),
            'indKurir' => $index_kurir
        ];

        $this->session->set([
            'hargaKeseluruhan' => [
                'hargaBarang' => $hargaTotal,
                'hargaKurir' => $kurir[$index_kurir]['harga']
            ]
        ]);
        $this->session->set(['kurirTerpilih' => $kurir[$index_kurir]]);
        return view('pages/payment', $data);
    }
    public function actionPay($metode)
    {
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "IContoh" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = rand();
        $alamatselected = $this->session->get('alamatTerpilih');
        $kurirselected = $this->session->get('kurirTerpilih');
        if (!isset($alamatselected) || !isset($kurirselected)) {
            return redirect()->to('/address');
        } else {
            if (count($alamatselected) <= 0 || count($kurirselected) <= 0) {
                return redirect()->to('/address');
            }
        }

        $keranjang = $this->session->get('keranjang');
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                }
            }
            $keranjang[$index]['detail'] = $produk;
        }

        $itemDetails = [];
        foreach ($keranjang as $element) {
            $produknya = $element['detail'];
            array_push($produk, $produknya);
            $persen = (100 - $produknya['diskon']) / 100;
            $hasil = round($persen * $produknya['harga']);
            $item = array(
                'id' => $produknya["id"],
                'price' => $hasil,
                'quantity' => $element['jumlah'],
                'name' => $produknya["nama"] . " (" . ucfirst($element['varian']) . ")",
            );
            array_push($itemDetails, $item);
        }
        $item = array(
            'id' => 'Biaya Ongkir',
            'price' => $this->session->get('hargaKeseluruhan')['hargaKurir'],
            'quantity' => 1,
            'name' => 'Biaya Ongkir',
        );
        $biayaadmin = array(
            'id' => 'Biaya Admin',
            'price' => 5000,
            'quantity' => 1,
            'name' => 'Biaya Admin',
        );
        array_push($itemDetails, $item);
        array_push($itemDetails, $biayaadmin);

        $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");

        $arrPostField = [
            "transaction_details" => [
                "order_id" => $randomId,
                "gross_amount" => $this->session->get('hargaKeseluruhan')['hargaKurir'] + 5000 + $this->session->get('hargaKeseluruhan')['hargaBarang']
                // "gross_amount" => $this->session->get('hargaKeseluruhan')
            ],
            'customer_details' => array(
                'email' => $alamatselected['email_pemesan'],
                'first_name' => $alamatselected['nama_penerima'],
                'phone' => $alamatselected['nohp_penerima'],
                'billing_address' => array(
                    'email' => $alamatselected['email_pemesan'],
                    'first_name' => $alamatselected['nama_penerima'],
                    'phone' => $alamatselected['nohp_penerima'],
                    'address' => $alamatselected['alamat_lengkap'],
                ),
                'shipping_address' => array(
                    'email' => $alamatselected['email_pemesan'],
                    'first_name' => $alamatselected['nama_penerima'],
                    'phone' => $alamatselected['nohp_penerima'],
                    'address' => $alamatselected['alamat_lengkap'],
                )
            ),
            'item_details' => $itemDetails
        ];
        switch ($metode) {
            case 'bca':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bca"];
                break;
            case 'bri':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bri"];
                break;
            case 'bni':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bni"];
                break;
            case 'cimb':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "cimb"];
                break;
            case 'permata':
                $arrPostField["payment_type"] = "permata";
                break;
            case 'mandiri':
                $arrPostField["payment_type"] = "echannel";
                $arrPostField["echannel"] = [
                    "bill_info1" => "Payment:",
                    "bill_info2" => "Online purchase"
                ];
                break;
            default:
                return redirect()->to('/address');
                break;
        }
        // dd($arrPostField);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/charge",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arrPostField),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $auth,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasil = json_decode($response, true);

        // dd($hasil);

        // $this->pemesananModel->set([
        //     'nama' => $alamatselected['nama_penerima'],
        //     'email' => $alamatselected['email_pemesan'],
        //     'nohp' => $alamatselected['nohp_penerima'],
        //     'alamat' => json_encode($alamatselected),
        //     'resi' => "Menunggu pengiriman " . strtoupper($kurirselected['nama']),
        //     'items' => json_encode($itemDetails),
        //     'kurir' => $kurirselected['nama'],
        //     'data_mid' => $response
        // ])->update();

        if ($hasil['fraud_status'] == "accept") {
            switch ($hasil['transaction_status']) {
                case 'settlement':
                    $status = "Proses";
                    break;
                case 'capture':
                    $status = "Proses";
                    break;
                case 'pending':
                    $status = "Menunggu Pembayaran";
                    break;
                case 'expire':
                    $status = "Kadaluarsa";
                    break;
                case 'deny':
                    $status = "Ditolak";
                    break;
                case 'failure':
                    $status = "Gagal";
                    break;
                case 'refund':
                    $status = "Refund";
                    break;
                case 'partial_refund':
                    $status = "Partial Refund";
                    break;
                case 'cancel':
                    $status = "Dibatalkan";
                    break;
                default:
                    $status = "No Status";
                    break;
            }
        } else {
            $status = 'Forbidden';
        }
        $this->pemesananModel->insert([
            'nama' => $alamatselected['nama_penerima'],
            'email' => $alamatselected['email_pemesan'],
            'nohp' => $alamatselected['nohp_penerima'],
            'alamat' => json_encode($alamatselected),
            'resi' => "Menunggu pengiriman " . strtoupper($kurirselected['nama']),
            'items' => json_encode($itemDetails),
            'kurir' => json_encode($kurirselected),
            'data_mid' => $response,
            'id_midtrans' => $hasil['order_id'],
            'status' => $status,
        ]);
        return redirect()->to('/order/' . $hasil['order_id']);
    }
    public function updateTransaction()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $order_id = $body['order_id'];
        $fraud = $body['fraud_status'];
        if ($fraud == "accept") {
            switch ($body['transaction_status']) {
                case 'settlement':
                    $status = "Proses";
                    break;
                case 'capture':
                    $status = "Proses";
                    break;
                case 'pending':
                    $status = "Menunggu Pembayaran";
                    break;
                case 'expire':
                    $status = "Kadaluarsa";
                    break;
                case 'deny':
                    $status = "Ditolak";
                    break;
                case 'failure':
                    $status = "Gagal";
                    break;
                case 'refund':
                    $status = "Refund";
                    break;
                case 'partial_refund':
                    $status = "Partial Refund";
                    break;
                case 'cancel':
                    $status = "Dibatalkan";
                    break;
                default:
                    $status = "No Status";
                    break;
            }
        } else {
            $status = 'Forbidden';
        }

        $dataTransaksi_curr = $this->pemesananModel->getPemesanan($order_id);
        if (isset($dataTransaksi_curr)) {
            $dataMid_curr = json_decode($dataTransaksi_curr['data_mid'], true);
            $dataMid_curr['transaction_status'] = $body['transaction_status'];
            $this->pemesananModel->where('id_midtrans', $order_id)->set([
                'status' => $status,
                'data_mid' => json_encode($dataMid_curr),
            ])->update();

            //reset jumlah produk
            if ($status == 'Kadaluarsa' || $status == 'Ditolak' || $status == 'Gagal') {
                $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $order_id)->first();
                $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
                foreach ($dataTransaksiFulDariDatabase_items as $item) {
                    $barangCurr = $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->first();
                    $this->barangModel->where('nama', rtrim(explode("(", $item['name'])[0]))->set([
                        'stok' => $barangCurr['stok'] + $item['quantity']
                    ])->update();
                }
            }
        } else {
            $this->pemesananModel->insert([
                'nama' => '',
                'email' => '',
                'nohp' => '',
                'alamat' => json_encode([]),
                'resi' => '',
                'items' => json_encode([]),
                'kurir' => '',
                'id_midtrans' => $order_id,
                'status' => $status,
            ]);
        }
        $arr = [
            'success' => true,
        ];
        return $this->response->setJSON($arr, false);
    }
    public function progressPay($id_midtrans)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        $dataMid = json_decode($pemesanan['data_mid'], true);
        $biller_code = "";
        $bank = "";
        switch ($dataMid['payment_type']) {
            case 'bank_transfer':
                if (isset($dataMid['permata_va_number'])) {
                    $va_number = $dataMid['permata_va_number'];
                    $bank = "permata";
                } else {
                    $va_number = $dataMid['va_numbers'][0]['va_number'];
                    $bank = $dataMid['va_numbers'][0]['bank'];
                }
                break;
            case 'echannel':
                $va_number = $dataMid['bill_key'];
                $biller_code = $dataMid['biller_code'];
                $bank = "mandiri";
                break;
        }

        $waktuExpire = strtotime($dataMid['expiry_time']);
        $waktuCurr = strtotime("+7 Hours");
        $waktuSelisih = $waktuExpire - $waktuCurr;
        $waktu = date("H:i:s", $waktuSelisih);

        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [
            'title' => 'Peroses Pembayaran',
            'pemesanan' => $pemesanan,
            'dataMid' => $dataMid,
            'va_number' => $va_number,
            'biller_code' => $biller_code,
            'bank' => $bank,
            'waktu' => $waktu,
            'waktuExpire' => date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire)
        ];
        return view('pages/progresspay', $data);
    }
    public function successPay($id_midtrans)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        $dataMid = json_decode($pemesanan['data_mid'], true);
        $kurir = json_decode($pemesanan['kurir'], true);
        $items = json_decode($pemesanan['items'], true);
        $bank = "";
        switch ($dataMid['payment_type']) {
            case 'bank_transfer':
                if (isset($dataMid['permata_va_number'])) {
                    $bank = "permata";
                } else {
                    $bank = $dataMid['va_numbers'][0]['bank'];
                }
                break;
            case 'echannel':
                $bank = "mandiri";
                break;
        }
        $data = [
            'title' => 'Pembayaran Sukes',
            'pemesanan' => $pemesanan,
            'dataMid' => $dataMid,
            'kurir' => $kurir,
            'items' => $items,
            'bank' => $bank,
        ];
        return view('pages/successpay', $data);
    }
    public function cencelPay()
    {
        $data = [
            'title' => 'Pembayaran batal',
        ];
        return view('pages/cencelpay', $data);
    }
    public function order($id_order = false)
    {
        if ($id_order) {
            $pemesanan = $this->pemesananModel->getPemesanan($id_order);
            $dataMid = json_decode($pemesanan['data_mid'], true);
            $kurir = json_decode($pemesanan['kurir'], true);
            $items = json_decode($pemesanan['items'], true);
            switch ($pemesanan['status']) {
                case 'Menunggu Pembayaran':
                    $biller_code = "";
                    $bank = "";
                    switch ($dataMid['payment_type']) {
                        case 'bank_transfer':
                            if (isset($dataMid['permata_va_number'])) {
                                $va_number = $dataMid['permata_va_number'];
                                $bank = "permata";
                            } else {
                                $va_number = $dataMid['va_numbers'][0]['va_number'];
                                $bank = $dataMid['va_numbers'][0]['bank'];
                            }
                            break;
                        case 'echannel':
                            $va_number = $dataMid['bill_key'];
                            $biller_code = $dataMid['biller_code'];
                            $bank = "mandiri";
                            break;
                    }

                    $waktuExpire = strtotime($dataMid['expiry_time']);
                    $waktuCurr = strtotime("+7 Hours");
                    $waktuSelisih = $waktuExpire - $waktuCurr;
                    $waktu = date("H:i:s", $waktuSelisih);

                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $data = [
                        'title' => 'Peroses Pembayaran',
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'bank' => $bank,
                        'waktu' => $waktu,
                        'waktuExpire' => date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire)
                    ];
                    return view('pages/progresspay', $data);
                    break;
                case 'Proses':
                    $bank = "";
                    switch ($dataMid['payment_type']) {
                        case 'bank_transfer':
                            if (isset($dataMid['permata_va_number'])) {
                                $bank = "permata";
                            } else {
                                $bank = $dataMid['va_numbers'][0]['bank'];
                            }
                            break;
                        case 'echannel':
                            $bank = "mandiri";
                            break;
                    }
                    $data = [
                        'title' => 'Pembayaran Sukes',
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'kurir' => $kurir,
                        'items' => $items,
                        'bank' => $bank,
                    ];
                    return view('pages/successpay', $data);
                    break;
                case 'Kadaluarsa':
                    $status = "Kadaluarsa";
                    break;
                case 'Ditolak':
                    $status = "Ditolak";
                    break;
                case 'Gagal':
                    $status = "Gagal";
                    break;
                case 'Refund':
                    $status = "Refund";
                    break;
                case 'Partial Refund':
                    $status = "Partial Refund";
                    break;
                case 'Dibatalkan':
                    $status = "Dibatalkan";
                    break;
            }
        } else {
            $email = session()->get('email');
            $pesanan = $this->pemesananModel->getPemesananCus($email);
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
            return view('pages/order', $data);
        }
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

        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['wishlist' => json_encode($wishlist)])->update();
        return redirect()->to('/wishlist');
    }
    public function delWishlist($id_barang)
    {
        $wishlist = session()->get('wishlist');
        if (($key = array_search($id_barang, $wishlist)) !== false) {
            unset($wishlist[$key]);
        }
        session()->set(['wishlist' => $wishlist]);
        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['wishlist' => json_encode($wishlist)])->update();
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
        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['keranjang' => json_encode($keranjang)])->update();
        return redirect()->to('/cart');
    }

    public function login()
    {
        $data = [
            'title' => 'Masuk Akun',
            'val' => [
                'msg' => session()->getFlashdata('msg'),
                'val_email' => session()->getFlashdata('val-email'),
                'val_sandi' => session()->getFlashdata('val-sandi'),
                'isiEmail' => session()->getFlashdata('isiEmail'),
            ]
        ];
        return view('pages/login', $data);
    }
    public function actionLogin()
    {
        if (!$this->validate([
            'email' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Email harus diisi'
                ]
            ],
            'sandi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Sandi harus diisi'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-email', $validation->getError('email'));
            session()->setFlashdata('val-sandi', $validation->getError('sandi'));
            return redirect()->to('/login')->withInput();
        }

        $email = $this->request->getVar('email');
        $sandi = $this->request->getVar('sandi');
        $getUser = $this->userModel->getUser($email);
        if (!$getUser) {
            session()->setFlashdata('msg', 'Email tidak terdaftar');
            return redirect()->to('/login');
        }
        $authSandi = password_verify($sandi, $getUser['sandi']);
        if (!$authSandi) {
            session()->setFlashdata('msg', 'Sandi salah');
            return redirect()->to('/login');
        }

        $ses_data = ['alamat', 'wishlist', 'keranjang'];
        session()->remove($ses_data);
        if ($getUser['active'] == '0') {
            $ses_data = [
                'email' => $getUser['email'],
                'active' => '0',
                'isLogin' => true
            ];
            session()->set($ses_data);
            session()->setFlashdata('msg', "Email " . $email . " perlu diverifikasi");
            return redirect()->to('/verify');
        }
        if ($getUser['role'] == '0') {
            $getPembeli = $this->pembeliModel->getPembeli($email);
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'nama' => $getPembeli['nama'],
                'alamat' => json_decode($getPembeli['alamat'], true),
                'nohp' => $getPembeli['nohp'],
                'wishlist' => json_decode($getPembeli['wishlist'], true),
                'keranjang' => json_decode($getPembeli['keranjang'], true),
                'transaksi' => json_decode($getPembeli['transaksi'], true),
                'isLogin' => true
            ];
            session()->set($ses_data);
            return redirect()->to(site_url('/'));
        } else if ($getUser['role'] == '1') {
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->set($ses_data);
            return redirect()->to('/listproduct');
        } else if ($getUser['role'] == '2') {
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->set($ses_data);
            return redirect()->to('/g/order');
        }
    }
    public function register()
    {
        $data = [
            'title' => 'Membuat Akun',
            'val' => [
                'val_nama' => session()->getFlashdata('val-nama'),
                'val_email' => session()->getFlashdata('val-email'),
                'val_sandi' => session()->getFlashdata('val-sandi'),
                'val_nohp' => session()->getFlashdata('val-nohp'),
                'msg' => session()->getFlashdata('msg'),
                // 'val_alamat' => session()->getFlashdata('val-alamat'),
            ]
        ];
        return view('pages/register', $data);
    }
    public function actionRegister()
    {
        if (!$this->validate([
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama lengkap harus diisi',
                ]
            ],
            'email' => [
                'rules' => 'required|is_unique[user.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'is_unique' => 'Email sudah terdaftar',
                ]
            ],
            'sandi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Sandi harus diisi'
                ]
            ],
            'nohp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor handphone harus diisi'
                ]
            ],
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-nama', $validation->getError('nama'));
            session()->setFlashdata('val-email', $validation->getError('email'));
            session()->setFlashdata('val-sandi', $validation->getError('sandi'));
            session()->setFlashdata('val-nohp', $validation->getError('nohp'));
            return redirect()->to('/register')->withInput();
        }

        $otp_number = rand(100000, 999999);
        $waktu_otp = time() + 300;
        $d = strtotime("+425 Minutes");
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

        $email = \Config\Services::email();
        $email->setFrom('no-reply@ilenafurniture.com', 'Ilena Furniture');
        $email->setTo($this->request->getVar('email'));
        $email->setSubject('ILENA Store - Verifikasi OTP');
        $email->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
        $email->send();

        $this->userModel->insert([
            'email' => $this->request->getVar('email'),
            'sandi' => password_hash($this->request->getVar('sandi'), PASSWORD_DEFAULT),
            'role' => '0',
            'otp' => $otp_number,
            'active' => '0',
            'waktu_otp' => $waktu_otp
        ]);
        $this->pembeliModel->insert([
            'nama' => $this->request->getVar('nama'),
            'email_user' => $this->request->getVar('email'),
            'nohp' => $this->request->getVar('nohp'),
            'alamat' => json_encode([]),
            'wishlist' => json_encode([]),
            'keranjang' => json_encode([])
        ]);

        $emailUser = $this->request->getVar('email');
        $getUser = $this->userModel->getUser($emailUser);
        $ses_data = ['alamat', 'wishlist', 'keranjang'];
        session()->remove($ses_data);
        $ses_data = [
            'email' => $getUser['email'],
            'active' => '0',
            'isLogin' => true
        ];
        session()->set($ses_data);
        session()->setFlashdata('msg', "OTP telah dikirim ke email " . $emailUser . " dan berlaku hingga " . $waktu_otp_tanggal);
        return redirect()->to('/verify');
    }
    public function verify()
    {
        $data = [
            'title' => 'Verifikasi',
            'val' => [
                'msg' => session()->getFlashdata('msg'),
                'val_verify' => session()->getFlashdata('val_verify')
            ]
        ];
        return view('pages/verify', $data);
    }
    public function actionVerify()
    {
        $otp = $this->request->getVar("otp");
        $email = session()->get("email");
        $getUser = $this->userModel->getUser($email);
        if ($otp != $getUser['otp']) {
            session()->setFlashdata('val_verify', "OTP salah");
            return redirect()->to("/verify");
        }
        $waktu_otp = time();
        if ($waktu_otp > (int)$getUser['waktu_otp']) {
            $otp_number = rand(100000, 999999);
            $waktu_otp = time() + 300;
            $d = strtotime("+425 Minutes");
            $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
            $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

            $sendemail = \Config\Services::email();
            $sendemail->setFrom('no-reply@ilenafurniture.com', 'Ilena Furniture');
            $sendemail->setTo($email);
            $sendemail->setSubject('ILENA Store - Verifikasi OTP');
            $sendemail->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
            $sendemail->send();

            $this->userModel->where('email', $email)->set([
                'otp' => $otp_number,
                'waktu_otp' => $waktu_otp
            ])->update();
            session()->setFlashdata('msg', "OTP telah diperbarui dan sudah dikirim kembali ke email " . $email);
            return redirect()->to("/verify");
        }

        $getPembeli = $this->pembeliModel->getPembeli($email);
        $ses_data = [
            'active' => '1',
            'role' => $getUser['role'],
            'nama' => $getPembeli['nama'],
            'alamat' => json_decode($getPembeli['alamat'], true),
            'nohp' => $getPembeli['nohp'],
            'wishlist' => json_decode($getPembeli['wishlist'], true),
            'keranjang' => json_decode($getPembeli['keranjang'], true)
        ];
        $this->userModel->where('email', $email)->set([
            'active' => '1',
            'otp' => '0',
            'waktu_otp' => '0'
        ])->update();
        session()->set($ses_data);
        return redirect()->to(site_url('/'));
    }
    public function kirimOTP()
    {
        $emailUser = session()->get('email');
        $otp_number = rand(100000, 999999);
        $waktu_otp = time() + 300;
        $d = strtotime("+425 Minutes");
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

        $email = \Config\Services::email();
        $email->setFrom('no-reply@ilenafurniture.com', 'Ilena Furniture');
        $email->setTo($emailUser);
        $email->setSubject('ILENA Store - Verifikasi OTP');
        $email->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
        $email->send();

        $this->userModel->where('email', $emailUser)->set([
            'otp' => $otp_number,
            'waktu_otp' => $waktu_otp
        ])->update();

        session()->setFlashdata('msg', "OTP telah dikirim ke email " . $emailUser . " dan berlaku hingga " . $waktu_otp_tanggal);
        return redirect()->to('/verify');
    }
    public function actionLogout()
    {
        // $ses_data = ['email', 'role', 'alamat', 'wishlist', 'keranjang', 'isLogin', 'active', 'transaksi', 'nama', 'nohp'];
        session()->destroy();
        session()->setFlashdata('msg', 'Kamu telah keluar');
        return redirect()->to('/login');
    }
    public function account()
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

        $alamat = $this->session->get('alamat');
        if (!isset($alamat)) {
            $alamat = [];
        }

        $data = [
            'title' => 'Akun Saya',
            'alamat' => $alamat,
            'alamatJson' => json_encode($alamat),
            'email' => session()->get('email'),
            'nama' => session()->get('nama'),
            'nohp' => session()->get('nohp'),
            'provinsi' => $provinsi["rajaongkir"]["results"],
        ];
        return view('pages/account', $data);
    }
    public function faq()
    {
        $data = [
            'title' => 'FAQ'
        ];
        return view('pages/faq', $data);
    }
    public function tentang()
    {
        $data = [
            'title' => 'Tentang Kami'
        ];
        return view('pages/tentang', $data);
    }
    public function syarat()
    {
        $data = [
            'title' => 'Syarat & Ketentuan'
        ];
        return view('pages/syarat', $data);
    }
    public function kebijakan()
    {
        $data = [
            'title' => 'Kebijakan Privasi'
        ];
        return view('pages/kebijakan', $data);
    }
}
