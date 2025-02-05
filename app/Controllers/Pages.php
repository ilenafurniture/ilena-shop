<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\GambarArtikelModel;
use App\Models\ArtikelModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\PemesananGudangModel;
use App\Models\KartuStokModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\VoucherModel;
use App\Models\GambarHeaderModel;

class Pages extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $gambarHeaderModel;
    protected $gambarBarang3000Model;
    protected $gambarArtikelModel;
    protected $artikelModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $kartuStokModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $voucherModel;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->gambarHeaderModel = new GambarHeaderModel();
        $this->gambarBarang3000Model = new GambarBarang3000Model();
        $this->gambarArtikelModel = new GambarArtikelModel();
        $this->artikelModel = new ArtikelModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananGudangModel = new PemesananGudangModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->voucherModel = new VoucherModel();
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        $produk  = $this->barangModel->orderBy('pengunjung', 'desc')->findAll(4, 0);
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Home',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'metaKeyword' => 'ilena Furniture, Toko Furniture, Sorely Ilena Semarang, Cabana Ilena Semarang, Orca Ilena Semarang, Plint Base Ilena Semarang, Cutout Ilena Semarang, Living Room Ilena Semarang, Bed Room Ilena Semarang, Lounge Room Ilena Semarang',
            'produk' => $produk,
            'wishlist' => $wishlist,
            'msg_active' => session()->getFlashdata('msg_active') ? session()->getFlashdata('msg_active') : false,
            'isLogin' => session()->get('isLogin') ? true : false,
        ];
        return view('pages/home', $data);
    }
    public function indexGalih()
    {
        $produk  = $this->barangModel->orderBy('pengunjung', 'desc')->findAll(4, 0);
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Home',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'produk' => $produk,
            'wishlist' => $wishlist,
            'msg_active' => session()->getFlashdata('msg_active') ? session()->getFlashdata('msg_active') : false,
            'isLogin' => session()->get('isLogin') ? true : false
        ];
        return view('pages/homeGalih', $data);
    }

    public function actionFind()
    {
        $cari = str_replace(" ", "-", $this->request->getVar('cari'));
        return redirect()->to('/find/' . $cari);
    }
    public function find($teks)
    {
        $cari = str_replace("-", " ", $teks);
        $produk = $this->barangModel->like('nama', $cari, 'both')->where(['active' => '1'])->findAll();
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Cari Produk',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'produk' => $produk,
            'wishlist' => $wishlist,
            'find' => $cari
        ];
        return view('pages/all', $data);
    }

    public function fixId()
    {
        $seluruhBarang = $this->barangModel->findAll();
        foreach ($seluruhBarang as $sb) {
            $koleksi = $this->koleksiModel->where(['nama' => $sb['kategori']])->first();
            $jenis = $this->jenisModel->where(['nama' => $sb['subkategori']])->first();
            $idBaru = '1' . sprintf("%02d", $koleksi['id']) . sprintf("%03d", $jenis['id']) . substr($sb['id'], -2);
            $this->barangModel->where(['id' => $sb['id']])->set([
                'id' => $idBaru
            ])->update();
            $this->gambarBarangModel->where(['id' => $sb['id']])->set([
                'id' => $idBaru
            ])->update();
            $this->gambarBarang3000Model->where(['id' => $sb['id']])->set([
                'id' => $idBaru
            ])->update();
        }
        return $this->response->setJSON(['Sucess' => 'OK'], false);
    }
    public function fixSet()
    {
        $seluruhBarang = $this->barangModel->findAll();
        $ruangTamu = false;
        $ruangKeluarga = false;
        $ruangTidur = false;
        foreach ($seluruhBarang as $barang) {
            switch ($barang['subkategori']) {
                case 'armoir':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
                case 'bookshelf':
                    $ruangTamu = true;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'coffee table':
                    $ruangTamu = true;
                    $ruangKeluarga = true;
                    $ruangTidur = false;
                    break;
                case 'console table':
                    $ruangTamu = true;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
                case 'credenza':
                    $ruangTamu = false;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'dresser 3 drawer':
                    $ruangTamu = false;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'dresser 4 drawer':
                    $ruangTamu = false;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'dresser 5 drawer':
                    $ruangTamu = false;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'dresser 6 drawer':
                    $ruangTamu = false;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'dresser tall cabinet':
                    $ruangTamu = false;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'dressing table':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = false;
                    break;
                case 'king bed':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
                case 'queen bed':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
                case 'side table':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
                case 'meja nakas':
                    $ruangTamu = true;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'bufet tv':
                    $ruangTamu = true;
                    $ruangKeluarga = true;
                    $ruangTidur = true;
                    break;
                case 'wardrobe':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
                case 'single bed':
                    $ruangTamu = false;
                    $ruangKeluarga = false;
                    $ruangTidur = true;
                    break;
            }
            $this->barangModel->where(['id' => $barang['id']])->set([
                'ruang_tamu' => $ruangTamu,
                'ruang_keluarga' => $ruangKeluarga,
                'ruang_tidur' => $ruangTidur
            ])->update();
        }
        return $this->response->setJSON(['Sucess' => 'OK'], false);
    }
    public function fixNama()
    {
        $seluruhBarang = $this->barangModel->findAll();
        foreach ($seluruhBarang as $sb) {
            $this->barangModel->where([
                'id' => $sb['id']
            ])->set([
                'nama' => $sb['subkategori'] . ' ' . $sb['kategori']
            ])->update();
        }
        return $this->response->setJSON(['Sucess' => 'OK'], false);
    }

    public function gantiJenis($nama_lama, $nama_baru)
    {
        $this->jenisModel->where(['nama' => $nama_lama])->set(['nama' => $nama_baru])->update();
        $seluruhBarang = $this->barangModel->findAll();
        foreach ($seluruhBarang as $sb) {
            if (strtolower($sb['subkategori']) == $nama_lama) {
                $this->barangModel->where([
                    'id' => $sb['id']
                ])->set([
                    'subkategori' => $nama_baru
                ])->update();
            }
        }
        return $this->response->setJSON(['Sucess' => 'OK'], false);
    }

    public function product($nama = false, $ind_nama = false)
    {
        $wishlist = $this->session->get('wishlist');
        $koleksi = $this->koleksiModel->findAll();
        $jenis = $this->jenisModel->findAll();
        $nama = str_replace('-', ' ', $nama);
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        if ($nama) {
            $productsemua = $this->barangModel->where(['nama' => $nama])->findAll();
            $product = $productsemua[$ind_nama];
            // dd($product);
            $product['deskripsi'] = json_decode($product['deskripsi'], true);
            $product['varian'] = json_decode($product['varian'], true);
            $produkSejenis = $this->barangModel->where(['subkategori' => $product['subkategori']])->where('id !=', $product['id'])->orderBy('pengunjung', 'desc')->findAll(8, 0);
            $seluruhBarangFilter = [];
            $seluruhNama =  [];
            foreach ($produkSejenis as $s) {
                if (!in_array($s['nama'], $seluruhNama)) {
                    array_push($seluruhBarangFilter, $s);
                    array_push($seluruhNama, $s['nama']);
                }
            }
            $data = [
                'title' => ucwords($product['nama']),
                'navbar' => [
                    'koleksi' => $this->koleksiModel->findAll(),
                    'jenis' => $this->jenisModel->findAll(),
                ],
                'produk'        => $product,
                'wishlist'      => $wishlist,
                'koleksi'       => $koleksi,
                'jenis'         => $jenis,
                'produkSejenis' => $seluruhBarangFilter,
                'produkSemua'   => $productsemua,
                'indexNama'     => $ind_nama,
                'metaDeskripsi' => $product['nama'] . ' ' . 'ilena futniture' . ' ' . 'Ilena Semarang',
                'metaKeyword'   => $product['kategori'] . ' ' . 'Ilena Semarang'
            ];

            //menambah pengunjung
            $this->barangModel->where(['id' => $product['id']])->set(['pengunjung' => (int)$product['pengunjung'] + 1])->update();

            return view('pages/product', $data);
        } else {
            $product = $this->barangModel->getBarangNama();
            $data = [
                'title' => 'Produk Kami',
                'navbar' => [
                    'koleksi' => $this->koleksiModel->findAll(),
                    'jenis' => $this->jenisModel->findAll(),
                ],
                'produk' => $product,
                'wishlist' => $wishlist,
                'koleksi' => $koleksi,
                'jenis' => $jenis,
            ];
            return view('pages/all', $data);
        }
    }
    public function productCategory($kategori)
    {
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $koleksi = $this->koleksiModel->findAll();
        $jenis = $this->jenisModel->findAll();
        $product = $this->barangModel->orderBy('nama', 'asc')->where(['active' => '1', 'kategori' => str_replace('-', ' ', $kategori)])->findAll();

        $seluruhBarangFilter = [];
        $seluruhNama =  [];
        foreach ($product as $s) {
            if (!in_array($s['nama'], $seluruhNama)) {
                array_push($seluruhBarangFilter, $s);
                array_push($seluruhNama, $s['nama']);
            }
        }

        $meta = [
            'cabana' => [
                'deskripsi' => 'Temukan furniture rumah tangga modern berkualitas di Cabana Ilena Semarang',
                'keywords' => ['Cabana Ilena', 'Cabana Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'sorely' => [
                'deskripsi' => 'Sempurnakan rumah dengan furniture modern ala sorely Ilena Semarang',
                'keywords' => ['Sorely Ilena', 'Sorely Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'orca' => [
                'deskripsi' => 'Buat interior lebih sempurna dengan furniture elegan dari Orca ilena Semarang',
                'keywords' => ['Orca Ilena,Orca Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'water-case' => [
                'deskripsi' => 'Pilih furniture terbaik untuk hunian dengan beli Water case Ilena Semarang',
                'keywords' => ['Water Case Ilena,Water Case Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'plint-base' => [
                'deskripsi' => 'Beli sekarang furniture model terbaru ala plint base Ilena Semarang',
                'keywords' => ['Plint Base Ilena,Plint Base Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'cutout' => [
                'deskripsi' => 'Pastikan furniture rumah selalu keren dan berkualitas dengan beli CutOut Ilena Semarang',
                'keywords' => ['Cutout Ilena,Cutout Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'industrial' => [
                'deskripsi' => 'Pastikan furniture rumah selalu keren dan berkualitas dengan beli Industrial Ilena Semarang',
                'keywords' => ['Industrial Ilena,Industrial Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'metal-frame' => [
                'deskripsi' => 'Pastikan furniture rumah selalu keren dan berkualitas dengan beli Metal Frame Ilena Semarang',
                'keywords' => ['Metal Frame Ilena,Metal Frame Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'socoplate' => [
                'deskripsi' => 'Pastikan furniture rumah selalu keren dan berkualitas dengan beli Socoplate Ilena Semarang',
                'keywords' => ['Socoplate Ilena,Socoplate Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
            'cody' => [
                'deskripsi' => 'Pastikan furniture rumah selalu keren dan berkualitas dengan beli Cody Ilena Semarang',
                'keywords' => ['Cody Ilena,Cody Ilena Semarang,Living Room Ilena', 'Living Room', 'Ilena Semarang', 'Bed Room Ilena', 'Bed Room Ilena Semarang', 'Lounge Room Ilena', 'Lounge Room Ilena Semarang'],
            ],
        ];

        $data = [
            'title' => 'Produk Kami',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'produk' => $seluruhBarangFilter,
            'wishlist' => $wishlist,
            'koleksi' => $koleksi,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'metaDeskripsi' => $meta[$kategori]['deskripsi'],
            'metaKeyword' => implode(',', $meta[$kategori]['keywords'])
        ];
        return view('pages/all', $data);
    }
    public function cart()
    {
        if (session()->get('active') == '0') return redirect()->to('/verify');
        $hargaTotal = 0;
        $keranjang = $this->session->get('keranjang');
        if (!isset($keranjang)) {
            $keranjang = [];
        }
        $ketemuProdukNull = [];
        $ketemuProdukKurang = [];
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            if (!$produk) {
                array_push($ketemuProdukNull, $index);
            } else {
                $varianProduk = json_decode($produk['varian'], true);
                $cekVarianExist = false;
                foreach ($varianProduk as $vp) {
                    if (strtolower($vp['nama']) == strtolower($k['varian'])) {
                        if ($vp['stok'] < $k['jumlah']) {
                            if ($vp['stok'] == 0) {
                                array_push($ketemuProdukNull, $index);
                            } else {
                                array_push($ketemuProdukKurang, $index . '-' . $vp['stok']);
                            }
                        }
                        $cekVarianExist = true;
                    }
                }
                if (!$cekVarianExist) {
                    array_push($ketemuProdukNull, $index);
                }
            }
        }
        if (count($ketemuProdukNull) > 0) {
            foreach ($ketemuProdukNull as $k) {
                unset($keranjang[$k]);
                $keranjangBaru = array_values($keranjang);
                $this->session->set(['keranjang' => $keranjangBaru]);
                $email = session()->get('email');
                if ($email)
                    $this->pembeliModel->where('email', $email)->set(['keranjang' => json_encode($keranjangBaru)])->update();
            }
            return redirect()->to('/cart');
        }
        if (count($ketemuProdukKurang) > 0) {
            foreach ($ketemuProdukKurang as $k) {
                $index = explode("-", $k)[0];
                $stokCur = explode("-", $k)[1];
                $keranjang[$index]['jumlah'] = $stokCur;
                $this->session->set(['keranjang' => $keranjang]);
                $email = session()->get('email');
                if ($email)
                    $this->pembeliModel->where('email', $email)->set(['keranjang' => json_encode($keranjang)])->update();
            }
            return redirect()->to('/cart');
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
            $keranjang = $keranjangBaru;
            // $this->session->set(['keranjang' => $keranjangBaru]);
            // return redirect()->to('/cart');
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
    public function getKode($kec)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dakotacargo.co.id/api/api_glb_M_kodepos.asp?key=15f6a51696a8b034f9ce366a6dc22138&id=11022019000001&aKec=" . rawurlencode($kec),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $kode = json_decode($response, true);
        return $this->response->setJSON($kode, false);
    }

    public function address()
    {
        if (session()->get('active') == '0') return redirect()->to('/verify');
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'provinsi' => $provinsi["rajaongkir"]["results"],
            'keranjang' => $keranjang,
            'hargaTotal' => $hargaTotal,
            'hargaKeseluruhan' => $hargaTotal + 5000,
            'alamat' => $alamat,
            'alamatJson' => json_encode($alamat),
            'email' => session()->get('email') ? session()->get('email') : '',
            'nama' => session()->get('nama') ? session()->get('nama') : '',
            'nohp' => session()->get('nohp') ? session()->get('nohp') : '',
            'msg' => session()->getFlashdata('msg')
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
        $email = session()->get('email');
        if ($email) {
            if ($email != $emailPem) {
                session()->setFlashdata('msg', 'Email yang dimasukan harus sesuai dengan email akun member Anda');
                return redirect()->to($checkPage == 'address' ? '/address' : '/account');
            }
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

        if ($email) $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamat)])->update();
        return redirect()->to($checkPage == 'address' ? '/address' : '/account');
    }
    public function deleteAddress($ind_add, $pathname)
    {
        $alamat = $this->session->get('alamat');
        unset($alamat[$ind_add]);
        $alamatBaru = array_values($alamat);
        $this->session->set(['alamat' => $alamatBaru]);

        $email = session()->get('email');
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamatBaru)])->update();
        return redirect()->to($pathname);
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
        $pathnameUrl = $this->request->getVar('url');

        $alamat = $this->session->get('alamat');
        $email = session()->get('email');
        if ($email) {
            if ($email != $emailPem) {
                session()->setFlashdata('msg', 'Email yang dimasukan harus sesuai dengan email akun member Anda');
                return redirect()->to($pathnameUrl);
            }
        }
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

        if ($email)
            $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamat)])->update();
        return redirect()->to($pathnameUrl);
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
            $beratVolume = ceil((float)$dimensiPaket['panjang'] / 10 * (float)$dimensiPaket['lebar'] / 10 * (float)$dimensiPaket['tinggi'] / 10 / 3500); //kg
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
    public function paymentlama($index_kurir)
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'hargaTotal' => $hargaTotal,
            'hargaOngkir' => $kurir[$index_kurir]['harga'],
            'hargaKeseluruhan' => ($hargaTotal + 5000 + $kurir[$index_kurir]['harga']),
            'indKurir' => $index_kurir,
            'user' => [
                'email' => $alamatTerpilih['email_pemesan'],
                'nama' => $alamatTerpilih['nama_penerima'],
                'no_hp' => $alamatTerpilih['nohp_penerima'],
                'alamat' => $alamatTerpilih['alamat_lengkap'],

            ],
            'keranjang' => $keranjang,
            'kurir' => $kurir[$index_kurir],
            'dataMidJson' => base64_encode(json_encode([
                'code' => ':ilenafur',
                'email' => $alamatTerpilih['email_pemesan'],
                'nama' => $alamatTerpilih['nama_penerima'],
                'nohp' => $alamatTerpilih['nohp_penerima'],
                'alamat' => $alamatTerpilih['alamat_lengkap'],
                'keranjang' => $this->session->get('keranjang'),
                'kurir' => $kurir[$index_kurir],
            ]))
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
    public function payment($ind_add)
    {
        if (session()->get('active') == '0') return redirect()->to('/verify');
        $hargaTotal = 0;
        $keranjang = $this->session->get('keranjang');
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

        //voucher
        $voucher = [];
        $emailUjiCoba = ['galihsuks123@gmail.com', 'ilenafurniture@gmail.com', 'galih8.4.2001@gmail.com'];
        if (session()->get('isLogin') && in_array($alamatselected['email_pemesan'], $emailUjiCoba)) {
            //voucher member baru
            $voucherMemberBaru = $this->voucherModel->where(['id' => 1])->first();
            if (!in_array($alamatselected['email_pemesan'], json_decode($voucherMemberBaru['list_email'], true))) {
                array_push($voucher, $voucherMemberBaru);
            }
        }
        $diskonVoucher = 0; //satuannya rupiah
        $voucherSelected = false;
        if (session()->get('voucher')) {
            $voucherDetail = $this->voucherModel->where(['id' => session()->get('voucher')])->first();
            if ($voucherDetail['satuan'] == 'persen') {
                $diskonVoucher = round($voucherDetail['nominal'] / 100 * $hargaTotal);
            }
            $voucherSelected = $voucherDetail;
            $voucherSelected['rupiah'] = $diskonVoucher;
        }

        $data = [
            'title' => 'Pembayaran',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'hargaTotal' => $hargaTotal,
            'user' => [
                'email' => $alamatselected['email_pemesan'],
                'nama' => $alamatselected['nama_penerima'],
                'no_hp' => $alamatselected['nohp_penerima'],
                'alamat' => $alamatselected['alamat_lengkap'],

            ],
            'keranjang' => $keranjang,
            'dataMidJson' => base64_encode(json_encode([
                'code' => ':ilenafur',
                'email' => $alamatselected['email_pemesan'],
                'nama' => $alamatselected['nama_penerima'],
                'nohp' => $alamatselected['nohp_penerima'],
                'alamat' => $alamatselected['alamat_lengkap'],
                'keranjang' => $this->session->get('keranjang'),
                'voucher' => $voucherSelected ? [
                    'd' => $diskonVoucher,
                    'id' => $voucherSelected['id']
                ] : false
                // 'kurir' => $kurir[$index_kurir],
            ])),
            'indexAddress' => $ind_add,
            'voucher' => [
                'list' => $voucher,
                'selected' => $voucherSelected,
            ],
            'emailUji' => in_array($alamatselected['email_pemesan'], $emailUjiCoba),
            'msg' => session()->getFlashdata('msg')
        ];

        $this->session->set(['alamatTerpilih' => $alamatselected]);
        return view('pages/payment', $data);
    }
    public function useVoucher($data)
    {
        $ind_voucher = explode('-', $data)[0];
        $ind_address = explode('-', $data)[1];
        session()->set('voucher', $ind_voucher);
        return redirect()->to('/payment/' . $ind_address);
    }
    public function cancelVoucher($data)
    {
        $ind_voucher = explode('-', $data)[0];
        $ind_address = explode('-', $data)[1];
        session()->remove('voucher');
        return redirect()->to('/payment/' . $ind_address);
    }
    public function actionPaySnap()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode(base64_decode(json_decode($bodyJson, true)['content']), true);
        // return $this->response->setJSON($body, false);
        if ($body['code'] != ':ilenafur') {
            return $this->response->setJSON([
                'token' => 'gagal'
            ], false);
        }
        $email = $body['email'];
        $nama = $body['nama'];
        $nohp = $body['nohp'];
        $alamatLengkap = $body['alamat'];
        $keranjang = $body['keranjang'];
        $voucher = $body['voucher'];
        // $kurir = $body['kurir'];

        $subtotal = 0;
        $itemDetails = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id_barang']);
                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * (int)$element['jumlah'];

                $item = array(
                    'id' => $produknya["id"],
                    'price' => $hasil,
                    'quantity' => (int)$element['jumlah'],
                    'name' => substr($produknya["nama"] . " (" . ucfirst($element['varian']) . ")", 0, 50),
                );
                array_push($itemDetails, $item);
            }
        }
        $total = $subtotal + 5000;

        if ($voucher) {
            $item = array(
                'id' => 'Voucher',
                'price' => -$voucher['d'],
                'quantity' => 1,
                'name' => 'Voucher',
            );
            array_push($itemDetails, $item);
            $total -= $voucher['d'];
        }
        $biayaadmin = array(
            'id' => 'Biaya Admin',
            'price' => 5000,
            'quantity' => 1,
            'name' => 'Biaya Admin',
        );
        array_push($itemDetails, $biayaadmin);

        $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "IL" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "IL" . rand();
        $customField = json_encode([
            'e' => $email,
            'n' => $nama,
            'h' => $nohp,
            'a' => $alamatLengkap,
            'i' => $keranjang,
            'v' => $voucher
        ]);

        $emailUjiCoba = ['galihsuks123@gmail.com', 'ilenafurniture@gmail.com', 'galih8.4.2001@gmail.com','adityaanugrah494@gmail.com'];
        $arrPostField = [
            "transaction_details" => [
                "order_id" => $randomId,
                "gross_amount" => $total,
            ],
            'customer_details' => array(
                'email' => $email,
                'first_name' => $nama,
                'phone' => $nohp,
                'billing_address' => array(
                    'email' => $email,
                    'first_name' => $nama,
                    'phone' => $nohp,
                    'address' => $alamatLengkap,
                ),
                'shipping_address' => array(
                    'email' => $email,
                    'first_name' => $nama,
                    'phone' => $nohp,
                    'address' => $alamatLengkap,
                )
            ),
            'callbacks' => array(
                'finish' => "https://ilenafurniture.com/order/" . $randomId,
            ),
            'item_details' => $itemDetails,
            "custom_field1" => substr($customField, 0, 255),
            "custom_field2" => substr($customField, 255, 255),
            "custom_field3" => substr($customField, 510, 255),
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://app.midtrans.com/snap/v1/transactions",
            CURLOPT_URL => "https://app.sandbox.midtrans.com/snap/v1/transactions",
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
        $hasilMidtrans = json_decode($response, true);
        return $this->response->setJSON($hasilMidtrans, false);
    }
    public function actionPayCore($ind_add)
    {
        $pembayaran = $this->request->getVar('pembayaran');
        $alamatselected = session()->get('alamatTerpilih');
        $email = $alamatselected['email_pemesan'];
        $nama = $alamatselected['nama_penerima'];
        $nohp = $alamatselected['nohp_penerima'];
        $alamatLengkap = $alamatselected['alamat_lengkap'];
        $keranjang = session()->get('keranjang');

        if ($pembayaran == 'card') {
            if (!$this->validate([
                'tokencc' => ['rules' => 'required'],
            ])) {
                $validation = \Config\Services::validation();
                session()->setFlashdata('msg', 'Terdapat data yang masih kosong');
                return redirect()->to('/payment/' . $ind_add)->withInput();
            }
        }
        $tokencc = $this->request->getVar('tokencc');
        $emailUjiCoba = ['galihsuks123@gmail.com', 'ilenafurniture@gmail.com', 'galih8.4.2001@gmail.com','adityaanugrah494@gmail.com'];

        $subtotal = 0;
        $itemDetails = [];
        if (!empty($keranjang)) {
            foreach ($keranjang as $ind => $element) {
                $produknya = $this->barangModel->getBarang($element['id_barang']);
                $persen = (100 - $produknya['diskon']) / 100;
                $hasil = round($persen * $produknya['harga']);
                $subtotal += $hasil * (int)$element['jumlah'];
                $deskripsinya = json_decode($produknya['deskripsi'], true);

                $item = array(
                    'id' => $produknya["id"],
                    'price' => $hasil,
                    'quantity' => (int)$element['jumlah'],
                    'name' => $produknya["nama"] . " " . $deskripsinya['dimensi']['asli']['panjang'] . " (" . ucfirst($element['varian']) . ")", //tambvahin ukuran
                    // 'name' => substr($produknya["nama"] . " (" . ucfirst($element['varian']) . ")", 0, 50), //tambvahin ukuran
                );
                array_push($itemDetails, $item);
            }
        }

        $diskonVoucher = 0; //satuannya rupiah
        $voucherSelected = false;
        if (session()->get('voucher')) {
            $voucherDetail = $this->voucherModel->where(['id' => session()->get('voucher')])->first();
            if ($voucherDetail['satuan'] == 'persen') {
                $diskonVoucher = round($voucherDetail['nominal'] / 100 * $subtotal);
            }
            $voucherSelected = $voucherDetail;
            $voucherSelected['rupiah'] = $diskonVoucher;
        }

        $voucher = $voucherSelected ? [
            'd' => $diskonVoucher,
            'id' => $voucherSelected['id']
        ] : false;
        // $kurir = $body['kurir'];

        $total = $subtotal + 5000;
        if ($voucher) {
            $item = array(
                'id' => 'Voucher',
                'price' => -$voucher['d'],
                'quantity' => 1,
                'name' => 'Voucher',
            );
            array_push($itemDetails, $item);
            $total -= $voucher['d'];
        }
        $biayaadmin = array(
            'id' => 'Biaya Admin',
            'price' => 5000,
            'quantity' => 1,
            'name' => 'Biaya Admin',
        );
        array_push($itemDetails, $biayaadmin);

        $midtrans_production_key = env('MIDTRANS_PRODUCTION_KEY', 'DefaultValue');
        if (in_array($email, $emailUjiCoba))
            $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");
        else
            $auth = base64_encode($midtrans_production_key . ":");
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idAsli = "IL" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "IL" . rand();
        $idFix = in_array($email, $emailUjiCoba) ? $randomId : $idAsli;
        $customField = json_encode([
            'e' => $email,
            'n' => $nama,
            'h' => $nohp,
            'a' => $alamatLengkap,
            'i' => $keranjang,
            'v' => $voucher
        ]);

        $arrPostField = [
            "transaction_details" => [
                "order_id" => $idFix,
                "gross_amount" => $total,
            ],
            'customer_details' => array(
                'email' => $email,
                'phone' => $nohp,
                'first_name' => $nama,
            ),
            'item_details' => $itemDetails,
            "custom_field1" => substr($customField, 0, 255),
            "custom_field2" => substr($customField, 255, 255),
            "custom_field3" => substr($customField, 510, 255),
        ];
        switch ($pembayaran) {
            case 'bca':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bca"];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 60,
                    "unit" => "minute"
                ];
                break;
            case 'bri':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bri"];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 60,
                    "unit" => "minute"
                ];
                break;
            case 'bni':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "bni"];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 60,
                    "unit" => "minute"
                ];
                break;
            case 'cimb':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank" => "cimb"];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 60,
                    "unit" => "minute"
                ];
                break;
            case 'permata':
                $arrPostField["payment_type"] = "permata";
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 60,
                    "unit" => "minute"
                ];
                break;
            case 'mandiri':
                $arrPostField["payment_type"] = "echannel";
                $arrPostField["echannel"] = [
                    "bill_info1" => "Payment:",
                    "bill_info2" => "Online purchase"
                ];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 60,
                    "unit" => "minute"
                ];
                break;
            case 'qris':
                $arrPostField["payment_type"] = "qris";
                $arrPostField["qris"] = ["acquirer" => "gopay"];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 15,
                    "unit" => "minute"
                ];
                break;
            case 'gopay':
                $arrPostField["payment_type"] = "gopay";
                $arrPostField["gopay"] = [
                    "enable_callback" => true,
                    "callback_url" => "https://ilenafurniture.com/order/" . $arrPostField['transaction_details']['order_id']
                ];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 15,
                    "unit" => "minute"
                ];
                break;
            case 'shopeepay':
                $arrPostField["payment_type"] = "shopeepay";
                $arrPostField["shopeepay"] = ["callback_url" => "https://ilenafurniture.com/order/" . $arrPostField['transaction_details']['order_id']];
                $arrPostField['custom_expiry'] = [
                    "expiry_duration" => 15,
                    "unit" => "minute"
                ];
                break;
            case 'card':
                $arrPostField["payment_type"] = "credit_card";
                $arrPostField["credit_card"] = [
                    "token_id" => $tokencc
                ];
                break;
            default:
                return redirect()->to('/payment/' . $ind_add);
                break;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => in_array($email, $emailUjiCoba) ? "https://api.sandbox.midtrans.com/v2/charge" : "https://api.midtrans.com/v2/charge",
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
        $hasilMidtrans = json_decode($response, true);

        if (substr($hasilMidtrans['status_code'], 0, 1) != '2') {
            session()->setFlashdata('msg', $hasilMidtrans['status_message']);
            return redirect()->to('/payment/' . $ind_add);
        }

        //dari update transaction =============================
        switch ($hasilMidtrans['transaction_status']) {
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

        if ($voucher) {
            $voucherSelected = $this->voucherModel->where(['id' => $voucher['id']])->first();
            $voucherSelected_email = json_decode($voucherSelected['list_email'], true);
            array_push($voucherSelected_email, $email);
            $this->voucherModel->where(['id' => $voucher['id']])->set(['list_email' => json_encode($voucherSelected_email)])->update();
        }

        $this->pemesananModel->insert([
            'nama' => $nama,
            'email' => $email,
            'nohp' => $nohp,
            'alamat' => $alamatLengkap,
            'resi' => 'Menunggu pengiriman',
            'items' => json_encode($itemDetails),
            'kurir' => json_encode([]),
            'id_midtrans' => $idFix,
            'status' => $status,
            'data_mid' => json_encode($hasilMidtrans),
        ]);

        //pengurangan stok
        $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $idFix)->first();
        $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
        foreach ($dataTransaksiFulDariDatabase_items as $item) {
            if ($item['id'] != 'Biaya Admin' && $item['id'] != 'Voucher') {
                $barangCurr = $this->barangModel->where('id', $item['id'])->first();
                $varianBarangCurr = json_decode($barangCurr['varian'], true);
                foreach ($varianBarangCurr as $ind_v => $v) {
                    if ($v['nama'] == rtrim(explode("(", $item['name'])[1], ")")) {
                        $varianBarangCurr[$ind_v]['stok'] = (int)$v['stok'] - $item['quantity'];
                    }
                }
                $this->barangModel->where('id', $item['id'])->set([
                    'varian' => json_encode($varianBarangCurr)
                ])->update();
            }
        }
        return redirect()->to('/orderdetail/'. strtolower($status).'?idorder=' . $idFix);
    }
    public function actionPay($metode)
    {
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "IContoh" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "I" . rand();
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
            $varianArr = json_decode($produk['varian'], true);
            foreach ($varianArr as $ind_v => $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                    $varianArr[$ind_v]['stok'] = (int)$v['stok'] - 1;
                }
            }
            $keranjang[$index]['detail'] = $produk;
            $this->barangModel->where(['id' => $k['id_barang']])->set(['varian' => json_encode($varianArr)])->update();
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
                'packed' => false
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
        $arr = [
            'success' => true,
        ];
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $order_id = $body['order_id'];
        $fraud = $body['fraud_status'];
        if (isset($body['custom_field1'])) {
            $customField = json_decode($body['custom_field1'] . (isset($body['custom_field2']) ? $body['custom_field2'] : '') . (isset($body['custom_field3']) ? $body['custom_field3'] : ''), true);
        }
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
            if ($status == 'Kadaluarsa' || $status == 'Ditolak' || $status == 'Gagal' || $status == 'Dibatalkan') {
                $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $order_id)->first();
                $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
                foreach ($dataTransaksiFulDariDatabase_items as $item) {
                    $barangCurr = $this->barangModel->where('id', $item['id'])->first();
                    $varianBarangCurr = json_decode($barangCurr['varian'], true);
                    foreach ($varianBarangCurr as $ind_v => $v) {
                        if ($v['nama'] == rtrim(explode("(", $item['name'])[1], ")")) {
                            $varianBarangCurr[$ind_v]['stok'] = (int)$v['stok'] + $item['quantity'];
                        }
                    }
                    $this->barangModel->where('id', $item['id'])->set([
                        'varian' => json_encode($varianBarangCurr)
                    ])->update();
                }
            }

            //insert pesanan gudang
            $items_curr = json_decode($dataTransaksi_curr['items'], true);
            if ($status == 'Proses') {
                foreach ($items_curr as $i) {
                    if ($i['name'] != "Biaya Ongkir" && $i['name'] != "Biaya Admin") {
                        for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                            $this->pemesananGudangModel->insert([
                                'id_pesanan' => $order_id,
                                'tanggal' => $dataMid_curr['transaction_time'],
                                'nama' => $i['name'],
                                'id_barang' => $i['id'],
                                'packed' => false
                            ]);
                        }

                        $produknya = $this->barangModel->getBarang($i['id']);
                        $varian = json_decode($produknya['varian'], true);
                        $saldo = 0;
                        foreach ($varian as $ind_v => $v) {
                            if (strtolower($v['nama']) == strtolower(rtrim(explode("(", $i['name'])[1], ")"))) {
                                $saldo = (int)$v['stok'];
                            }
                        }
                        $tanggalNoStrip = date("YmdHis", strtotime($dataMid_curr['transaction_time']));
                        $this->kartuStokModel->insert([
                            'id_barang' => $i['id'],
                            'tanggal' => $dataMid_curr['transaction_time'],
                            'keterangan' => $tanggalNoStrip . "-" . $i['id'] . "-" . strtoupper(rtrim(explode("(", $i['name'])[1], ")")) . "-" . $dataTransaksi_curr['id_midtrans'],
                            'debit' => 0,
                            'kredit' => $i['quantity'],
                            'saldo' => $saldo,
                            'pending' => true,
                            'id_pesanan' => $dataTransaksi_curr['id_midtrans'],
                            'varian' => strtoupper(rtrim(explode("(", $i['name'])[1], ")"))
                        ]);
                    }
                }
            }
        }
        // else {
        //     $keranjang = $customField['i'];
        //     $itemDetails = [];
        //     foreach ($keranjang as $element) {
        //         $produknya = $this->barangModel->getBarang($element['id_barang']);
        //         $persen = (100 - $produknya['diskon']) / 100;
        //         $hasil = round($persen * $produknya['harga']);

        //         $item = array(
        //             'id' => $produknya["id"],
        //             'price' => $hasil,
        //             'quantity' => (int)$element['jumlah'],
        //             'name' => substr($produknya["nama"] . " (" . ucfirst($element['varian']) . ")", 0, 50),
        //         );
        //         array_push($itemDetails, $item);
        //     }
        //     $biayaadmin = array(
        //         'id' => 'Biaya Admin',
        //         'price' => 5000,
        //         'quantity' => 1,
        //         'name' => 'Biaya Admin',
        //     );
        //     array_push($itemDetails, $biayaadmin);
        //     if ($customField['v']) {
        //         $item = array(
        //             'id' => 'Voucher',
        //             'price' => -$customField['v']['d'],
        //             'quantity' => 1,
        //             'name' => 'Voucher',
        //         );
        //         array_push($itemDetails, $item);

        //         //masukin email customer ke tabel voucher
        //         $voucherSelected = $this->voucherModel->where(['id' => $customField['v']['id']])->first();
        //         $voucherSelected_email = json_decode($voucherSelected['list_email'], true);
        //         array_push($voucherSelected_email, $customField['e']);
        //         $this->voucherModel->where(['id' => $customField['v']['id']])->set(['list_email' => json_encode($voucherSelected_email)])->update();
        //     }

        //     // $biayaongkir = array(
        //     //     'id' => 'Biaya Ongkir',
        //     //     'price' => $customField['k']['harga'],
        //     //     'quantity' => 1,
        //     //     'name' => 'Biaya Ongkir',
        //     // );
        //     // array_push($itemDetails, $biayaongkir);

        //     $this->pemesananModel->insert([
        //         'nama' => $customField['n'],
        //         'email' => $customField['e'],
        //         'nohp' => $customField['h'],
        //         'alamat' => $customField['a'],
        //         'resi' => 'Menunggu pengiriman',
        //         'items' => json_encode($itemDetails),
        //         'kurir' => json_encode([]),
        //         'id_midtrans' => $order_id,
        //         'status' => $status,
        //         'data_mid' => json_encode($body),
        //     ]);

        //     //pengurangan stok
        //     $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $order_id)->first();
        //     $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
        //     foreach ($dataTransaksiFulDariDatabase_items as $item) {
        //         $barangCurr = $this->barangModel->where('id', $item['id'])->first();
        //         $varianBarangCurr = json_decode($barangCurr['varian'], true);
        //         foreach ($varianBarangCurr as $ind_v => $v) {
        //             if ($v['nama'] == rtrim(explode("(", $item['name'])[1], ")")) {
        //                 $varianBarangCurr[$ind_v]['stok'] = (int)$v['stok'] - $item['quantity'];
        //             }
        //         }
        //         $this->barangModel->where('id', $item['id'])->set([
        //             'varian' => json_encode($varianBarangCurr)
        //         ])->update();
        //     }
        // }
        return $this->response->setJSON($arr, false);
    }
    public function cancelOrder($id_midtrans)
    {
        $auth = base64_encode("SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh" . ":");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $id_midtrans . "/cancel",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
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
        return redirect()->to('/orderadmin');
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/cencelpay', $data);
    }
    public function orderLama($id_order = false)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_order);
        $carapembayaran = [
            'bca' => [
                [
                    'nama' => 'myBCA',
                    'isi' => '1. Login ke myBCA<br>
                                2. Pilih Transfer dan pilih Virtual Account<br>
                                3. Pilih Transfer ke tujuan baru<br>
                                4. Masukkan nomor Virtual Account dari e-commerce dan klik Lanjut<br>
                                5. Pilih rekening sumber dana (jika memiliki lebih dari satu), masukkan nominal dan klik Lanjut<br>
                                6. Cek detail transaksi, klik Lanjut<br>
                                7. Masukkan PIN dan transaksi berhasil'
                ],
                [
                    'nama' => 'BCA Mobile',
                    'isi' => '1. Login ke BCA mobile<br>
                                2. Pilih m-Transfer dan pilih BCA Virtual Account<br>
                                3. Masukkan nomor BCA Virtual Account dari e-commerce dan klik Send<br>
                                4. Masukkan nominal<br>
                                5. Cek detail transaksi, klik OK<br>
                                6. Masukkan PIN dan transaksi berhasil'
                ],
                [
                    'nama' => 'KlikBCA',
                    'isi' => '1. Login ke KlikBCA<br>
                                2. Pilih Transfer Dana dan pilih Transfer ke BCA Virtual Account<br>
                                3. Masukkan nomor BCA Virtual Account dari e-commerce dan klik Lanjutkan<br>
                                4. Masukkan nominal dan klik Lanjutkan<br>
                                5. Masukkan Respon KeyBCA Appli 1 dan klik Kirim<br>
                                6. Transaksi berhasil dilakukan'
                ],
                [
                    'nama' => 'ATM BCA',
                    'isi' => '1. Masukkan Kartu ATM dan PIN di ATM BCA<br>
                                2. Pilih Penarikan Tunai/Transaksi Lainnya<br>
                                3. Pilih Transaksi Lainnya<br>
                                4. Pilih Transfer<br>
                                5. Pilih menu Ke Rek BCA Virtual Account<br>
                                6. Masukkan nomor BCA Virtual Account dan klik Benar<br>
                                7. Cek detail transaksi dan pilih Ya<br>
                                8. Transaksi berhasil'
                ]
            ],
            'mandiri' => [
                [
                    'nama' => 'Livin by Mandiri',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih Ecommerce.<br>
                                3. Pilih Midtrans di bagian penyedia jasa.<br>
                                4. Masukkan nomor virtual account pada bagian kode bayar.<br>
                                5. Klik lanjutkan untuk konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'ATM Mandiri',
                    'isi' => '1. Pilih bayar/beli pada menu utama.<br>
                                2. Pilih lainnya.<br>
                                3. Pilih multi payment.<br>
                                4. Masukkan kode perusahaan Midtrans 70012.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'Mandiri Internet Banking',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih multi payment.<br>
                                3. Pilih dari rekening.<br>
                                4. Pilih Midtrans di bagian penyedia jasa.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
            ],
            'bni' => [
                [
                    'nama' => 'ATM BNI',
                    'isi' => '1. Pilih menu lain pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BNI.<br>
                                4. Masukkan nomor rekening pembayaran.<br>
                                5. Masukkan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.<br>
                                7. Internet Banking'
                ],
                [
                    'nama' => 'BNI Internet Banking',
                    'isi' => '1. Pilih transaksi, lalu info & administrasi transfer.<br>
                                2. Pilih atur rekening tujuan.<br>
                                3. Masukkan informasi rekening, lalu konfirmasi.<br>
                                4. Pilih transfer, lalu transfer ke rekening BNI.<br>
                                5. Masukkan detail pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BNI Mobile Banking',
                    'isi' => '1. Pilih transfer.<br>
                                2. Pilih virtual account billing.<br>
                                3. Pilih rekening debit yang akan digunakan.<br>
                                4. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'bri' => [
                [
                    'nama' => 'ATM BRI',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih lainnya.<br>
                                4. Pilih BRIVA.<br>
                                5. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'IB BRI',
                    'isi' => '1. Pilih pembayaran & pembelian.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BRImo',
                    'isi' => '1. Pilih pembayaran.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'bca' => [
                [
                    'nama' => 'ATM BCA',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BCA virtual account.<br>
                                4. Masukan Nomor BCA virtual account.<br>
                                5. Masukan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'Klik BCA',
                    'isi' => '1. Pilih Transfer Dana.<br>
                                2. Pilih Transfer ke BCA virtual account.<br>
                                3. Masukkan nomor BCA virtual account.<br>
                                4. Masukan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'M-BCA',
                    'isi' => '1. Pilih m-Transfer.<br>
                                2. Pilih BCA virtual account.<br>
                                3. Masukkan nomor BCA virtual account.<br>
                                4. Masukan jumlah yang akan dibayar, lalu konfirmasi<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'permata' => [
                [
                    'nama' => 'ATM Permata/ALTO',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih pembayaran lainnya.<br>
                                4. Pilih virtual account.<br>
                                5. Masukkan nomor virtual account Permata, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
            ],
            'cimb' => [
                [
                    'nama' => 'ATM CIMB Niaga',
                    'isi' => '1. Pilih pembayaran pada menu utama.<br>
                                2. Pilih virtual account.<br>
                                3. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                4. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Clicks',
                    'isi' => '1. Pilih pembayaran tagihan pada menu utama.<br>
                                2. Pilih mobile rekening virtual.<br>
                                3. Masukkan nomor virtual account, lalu klik lanjut untuk verifikasi detail.<br>
                                4. Pilih kirim OTP untuk lanjut.<br>
                                5. Masukkan OTP yang dikirimkan ke nomor HP Anda, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Mobile',
                    'isi' => '1. Pilih menu transfer.<br>
                                2. Pilih transfer to other CIMB Niaga account.<br>
                                3. Pilih sumber dana: CASA atau rekening ponsel.<br>
                                4. Masukkan nomor virtual account.<br>
                                5. Masukkan jumlah yang akan dibayar.<br>
                                6. Ikuti instruksi untuk menyelesaikan pembayaran.<br>
                                7. Pembayaran selesai.'
                ],
            ],
            'qris' => [
                [
                    'nama' => 'QRIS',
                    'isi' => '1. Buka aplikasi yang mendukung pembayaran dengan QRIS.<br>
                                2. Download atau pindai QRIS pada layar.<br>
                                3. Konfirmasi pembayaran pada aplikasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'gopay' => [
                [
                    'nama' => 'GoPay',
                    'isi' => '1. Klik Bayar sekarang.<br>
                                2. Aplikasi Gojek atau GoPay akan terbuka.<br>
                                3. Konfirmasi pembayaran di aplikasi Gojek atau GoPay.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'toko' => [
                [
                    'nama' => 'Pemesanan Gudang',
                    'isi' => '-'
                ],
            ],
            'market' => [
                [
                    'nama' => 'Pemesanan Marketplace',
                    'isi' => '-'
                ],
            ],
            'card' => 'Always Success'
        ];
        if ($id_order) {
            $pemesanan = $this->pemesananModel->getPemesanan($id_order);
            $dataMid = json_decode($pemesanan['data_mid'], true);
            $kurir = json_decode($pemesanan['kurir'], true);
            $items = json_decode($pemesanan['items'], true);
            foreach ($items as $ind_i => $i) {
                $produknya = $this->barangModel->getBarang($i['id']);
                $items[$ind_i]['name'] = '';
            }
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
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        case 'credit_card':
                            $va_number = '';
                            $bank = "card";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $waktuExpire = strtotime($dataMid['expiry_time']);
                    $waktuCurr = strtotime("+7 Hours");
                    $waktuSelisih = $waktuExpire - $waktuCurr;
                    $waktu = date("H:i:s", $waktuSelisih);

                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $data = [
                        'title' => 'Peroses Pembayaran',
                        'navbar' => [
                            'koleksi' => $this->koleksiModel->findAll(),
                            'jenis' => $this->jenisModel->findAll(),
                        ],
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'bank' => $bank,
                        'items' => $items,
                        'waktu' => $waktu,
                        'caraPembayaran' => $carapembayaran[$bank],
                        'waktuExpire' => date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire)
                    ];
                    return view('pages/progresspay', $data);
                    break;
                case 'Proses':
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
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        case 'toko':
                            $va_number = 'PEMBAYARAN TOKO';
                            $bank = "toko";
                            break;
                        case 'market':
                            $va_number = 'PEMBAYARAN MARKETPLACE';
                            $bank = "market";
                            break;
                        case 'credit_card':
                            $va_number = '';
                            $bank = "card";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $data = [
                        'title' => 'Pembayaran Sukes',
                        'navbar' => [
                            'koleksi' => $this->koleksiModel->findAll(),
                            'jenis' => $this->jenisModel->findAll(),
                        ],
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'kurir' => $kurir,
                        'items' => $items,
                        'bank' => $bank,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'caraPembayaran' => $carapembayaran[$bank],
                    ];
                    return view('pages/successpay', $data);
                    break;
                case 'Dikirim':
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
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        case 'toko':
                            $va_number = 'PEMBAYARAN TOKO';
                            $bank = "toko";
                            break;
                        case 'market':
                            $va_number = 'PEMBAYARAN MARKETPLACE';
                            $bank = "market";
                            break;
                        case 'credit_card':
                            $va_number = '';
                            $bank = "card";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $data = [
                        'title' => 'Pembayaran Sukes',
                        'navbar' => [
                            'koleksi' => $this->koleksiModel->findAll(),
                            'jenis' => $this->jenisModel->findAll(),
                        ],
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'kurir' => $kurir,
                        'items' => $items,
                        'bank' => $bank,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'caraPembayaran' => $carapembayaran[$bank],
                    ];
                    return view('pages/successpay', $data);
                    break;
                case 'Kadaluarsa':
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
                        case 'qris':
                            $va_number = 'https://api.midtrans.com/v2/qris/' . $dataMid['transaction_id'] . '/qr-code';
                            $bank = "qris";
                            break;
                        case 'credit_card':
                            $va_number = '';
                            $bank = "card";
                            break;
                        default:
                            $va_number = "";
                            break;
                    }

                    $waktuExpire = strtotime($dataMid['expiry_time']);
                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $data = [
                        'title' => 'Peroses Pembayaran',
                        'navbar' => [
                            'koleksi' => $this->koleksiModel->findAll(),
                            'jenis' => $this->jenisModel->findAll(),
                        ],
                        'pemesanan' => $pemesanan,
                        'dataMid' => $dataMid,
                        'va_number' => $va_number,
                        'biller_code' => $biller_code,
                        'bank' => $bank,
                        'items' => $items,
                        'caraPembayaran' => $carapembayaran[$bank],
                        'waktuExpire' => date("d", $waktuExpire) . " " . $bulan[(int)date("m", $waktuExpire) - 1] . " " . date("Y H:i:s", $waktuExpire)
                    ];
                    return view('pages/expirepay', $data);
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
                $pesanan[$ind_p]['data_mid'] = [
                    'transaction_time' => json_decode($p['data_mid'], true)['transaction_time'],
                    'gross_amount' => json_decode($p['data_mid'], true)['gross_amount'],
                ];
                $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
                $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
            }
            $data = [
                'title' => 'Pesanan',
                'navbar' => [
                    'koleksi' => $this->koleksiModel->findAll(),
                    'jenis' => $this->jenisModel->findAll(),
                ],
                'pesanan' => $pesanan,
                'email' => session()->get('email'),
                'nama' => session()->get('nama'),
                'nohp' => session()->get('nohp'),
                'pesananJson' => json_encode($pesanan),
                'msgSandi' => session()->get('msg-sandi') ? session()->get('msg-sandi') : false,
            ];
            return view('pages/order', $data);
        }
    }
    public function order($id_order = false)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_order);
        $carapembayaran = [
            'bca' => [
                [
                    'nama' => 'myBCA',
                    'isi' => '1. Login ke myBCA<br>
                                2. Pilih Transfer dan pilih Virtual Account<br>
                                3. Pilih Transfer ke tujuan baru<br>
                                4. Masukkan nomor Virtual Account dari e-commerce dan klik Lanjut<br>
                                5. Pilih rekening sumber dana (jika memiliki lebih dari satu), masukkan nominal dan klik Lanjut<br>
                                6. Cek detail transaksi, klik Lanjut<br>
                                7. Masukkan PIN dan transaksi berhasil'
                ],
                [
                    'nama' => 'BCA Mobile',
                    'isi' => '1. Login ke BCA mobile<br>
                                2. Pilih m-Transfer dan pilih BCA Virtual Account<br>
                                3. Masukkan nomor BCA Virtual Account dari e-commerce dan klik Send<br>
                                4. Masukkan nominal<br>
                                5. Cek detail transaksi, klik OK<br>
                                6. Masukkan PIN dan transaksi berhasil'
                ],
                [
                    'nama' => 'KlikBCA',
                    'isi' => '1. Login ke KlikBCA<br>
                                2. Pilih Transfer Dana dan pilih Transfer ke BCA Virtual Account<br>
                                3. Masukkan nomor BCA Virtual Account dari e-commerce dan klik Lanjutkan<br>
                                4. Masukkan nominal dan klik Lanjutkan<br>
                                5. Masukkan Respon KeyBCA Appli 1 dan klik Kirim<br>
                                6. Transaksi berhasil dilakukan'
                ],
                [
                    'nama' => 'ATM BCA',
                    'isi' => '1. Masukkan Kartu ATM dan PIN di ATM BCA<br>
                                2. Pilih Penarikan Tunai/Transaksi Lainnya<br>
                                3. Pilih Transaksi Lainnya<br>
                                4. Pilih Transfer<br>
                                5. Pilih menu Ke Rek BCA Virtual Account<br>
                                6. Masukkan nomor BCA Virtual Account dan klik Benar<br>
                                7. Cek detail transaksi dan pilih Ya<br>
                                8. Transaksi berhasil'
                ]
            ],
            'mandiri' => [
                [
                    'nama' => 'Livin by Mandiri',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih Ecommerce.<br>
                                3. Pilih Midtrans di bagian penyedia jasa.<br>
                                4. Masukkan nomor virtual account pada bagian kode bayar.<br>
                                5. Klik lanjutkan untuk konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'ATM Mandiri',
                    'isi' => '1. Pilih bayar/beli pada menu utama.<br>
                                2. Pilih lainnya.<br>
                                3. Pilih multi payment.<br>
                                4. Masukkan kode perusahaan Midtrans 70012.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'Mandiri Internet Banking',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih multi payment.<br>
                                3. Pilih dari rekening.<br>
                                4. Pilih Midtrans di bagian penyedia jasa.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
            ],
            'bni' => [
                [
                    'nama' => 'ATM BNI',
                    'isi' => '1. Pilih menu lain pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BNI.<br>
                                4. Masukkan nomor rekening pembayaran.<br>
                                5. Masukkan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.<br>
                                7. Internet Banking'
                ],
                [
                    'nama' => 'BNI Internet Banking',
                    'isi' => '1. Pilih transaksi, lalu info & administrasi transfer.<br>
                                2. Pilih atur rekening tujuan.<br>
                                3. Masukkan informasi rekening, lalu konfirmasi.<br>
                                4. Pilih transfer, lalu transfer ke rekening BNI.<br>
                                5. Masukkan detail pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BNI Mobile Banking',
                    'isi' => '1. Pilih transfer.<br>
                                2. Pilih virtual account billing.<br>
                                3. Pilih rekening debit yang akan digunakan.<br>
                                4. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'bri' => [
                [
                    'nama' => 'ATM BRI',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih lainnya.<br>
                                4. Pilih BRIVA.<br>
                                5. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'IB BRI',
                    'isi' => '1. Pilih pembayaran & pembelian.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BRImo',
                    'isi' => '1. Pilih pembayaran.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'bca' => [
                [
                    'nama' => 'ATM BCA',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BCA virtual account.<br>
                                4. Masukan Nomor BCA virtual account.<br>
                                5. Masukan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'Klik BCA',
                    'isi' => '1. Pilih Transfer Dana.<br>
                                2. Pilih Transfer ke BCA virtual account.<br>
                                3. Masukkan nomor BCA virtual account.<br>
                                4. Masukan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'M-BCA',
                    'isi' => '1. Pilih m-Transfer.<br>
                                2. Pilih BCA virtual account.<br>
                                3. Masukkan nomor BCA virtual account.<br>
                                4. Masukan jumlah yang akan dibayar, lalu konfirmasi<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'permata' => [
                [
                    'nama' => 'ATM Permata/ALTO',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih pembayaran lainnya.<br>
                                4. Pilih virtual account.<br>
                                5. Masukkan nomor virtual account Permata, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
            ],
            'cimb' => [
                [
                    'nama' => 'ATM CIMB Niaga',
                    'isi' => '1. Pilih pembayaran pada menu utama.<br>
                                2. Pilih virtual account.<br>
                                3. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                4. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Clicks',
                    'isi' => '1. Pilih pembayaran tagihan pada menu utama.<br>
                                2. Pilih mobile rekening virtual.<br>
                                3. Masukkan nomor virtual account, lalu klik lanjut untuk verifikasi detail.<br>
                                4. Pilih kirim OTP untuk lanjut.<br>
                                5. Masukkan OTP yang dikirimkan ke nomor HP Anda, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Mobile',
                    'isi' => '1. Pilih menu transfer.<br>
                                2. Pilih transfer to other CIMB Niaga account.<br>
                                3. Pilih sumber dana: CASA atau rekening ponsel.<br>
                                4. Masukkan nomor virtual account.<br>
                                5. Masukkan jumlah yang akan dibayar.<br>
                                6. Ikuti instruksi untuk menyelesaikan pembayaran.<br>
                                7. Pembayaran selesai.'
                ],
            ],
            'qris' => [
                [
                    'nama' => 'QRIS',
                    'isi' => '1. Buka aplikasi yang mendukung pembayaran dengan QRIS.<br>
                                2. Download atau pindai QRIS pada layar.<br>
                                3. Konfirmasi pembayaran pada aplikasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'gopay' => [
                [
                    'nama' => 'GoPay',
                    'isi' => '1. Klik Bayar sekarang.<br>
                                2. Aplikasi Gojek atau GoPay akan terbuka.<br>
                                3. Konfirmasi pembayaran di aplikasi Gojek atau GoPay.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'toko' => [
                [
                    'nama' => 'Pemesanan Gudang',
                    'isi' => '-'
                ],
            ],
            'market' => [
                [
                    'nama' => 'Pemesanan Marketplace',
                    'isi' => '-'
                ],
            ],
            'card' => 'Always Success'
        ];
        
        $email = session()->get('email');
        $pesanan = $this->pemesananModel->getPemesananCus($email);
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = [
                'transaction_time' => json_decode($p['data_mid'], true)['transaction_time'],
                'gross_amount' => json_decode($p['data_mid'], true)['gross_amount'],
            ];
            $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
        }
        $data = [
            'title' => 'Pesanan',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'pesanan' => $pesanan,
            'email' => session()->get('email'),
            'nama' => session()->get('nama'),
            'nohp' => session()->get('nohp'),
            'pesananJson' => json_encode($pesanan),
            'msgSandi' => session()->get('msg-sandi') ? session()->get('msg-sandi') : false,
        ];
        return view('pages/order', $data);
    }
    public function orderDetail($status)
    {
        $pemesanan = $this->pemesananModel->where('status', $status)->findAll();    
        $pemesananAll = $this->pemesananModel->findAll();    
        $carapembayaran = [
            'bca' => [
                [
                    'nama' => 'myBCA',
                    'isi' => '1. Login ke myBCA<br>
                                2. Pilih Transfer dan pilih Virtual Account<br>
                                3. Pilih Transfer ke tujuan baru<br>
                                4. Masukkan nomor Virtual Account dari e-commerce dan klik Lanjut<br>
                                5. Pilih rekening sumber dana (jika memiliki lebih dari satu), masukkan nominal dan klik Lanjut<br>
                                6. Cek detail transaksi, klik Lanjut<br>
                                7. Masukkan PIN dan transaksi berhasil'
                ],
                [
                    'nama' => 'BCA Mobile',
                    'isi' => '1. Login ke BCA mobile<br>
                                2. Pilih m-Transfer dan pilih BCA Virtual Account<br>
                                3. Masukkan nomor BCA Virtual Account dari e-commerce dan klik Send<br>
                                4. Masukkan nominal<br>
                                5. Cek detail transaksi, klik OK<br>
                                6. Masukkan PIN dan transaksi berhasil'
                ],
                [
                    'nama' => 'KlikBCA',
                    'isi' => '1. Login ke KlikBCA<br>
                                2. Pilih Transfer Dana dan pilih Transfer ke BCA Virtual Account<br>
                                3. Masukkan nomor BCA Virtual Account dari e-commerce dan klik Lanjutkan<br>
                                4. Masukkan nominal dan klik Lanjutkan<br>
                                5. Masukkan Respon KeyBCA Appli 1 dan klik Kirim<br>
                                6. Transaksi berhasil dilakukan'
                ],
                [
                    'nama' => 'ATM BCA',
                    'isi' => '1. Masukkan Kartu ATM dan PIN di ATM BCA<br>
                                2. Pilih Penarikan Tunai/Transaksi Lainnya<br>
                                3. Pilih Transaksi Lainnya<br>
                                4. Pilih Transfer<br>
                                5. Pilih menu Ke Rek BCA Virtual Account<br>
                                6. Masukkan nomor BCA Virtual Account dan klik Benar<br>
                                7. Cek detail transaksi dan pilih Ya<br>
                                8. Transaksi berhasil'
                ]
            ],
            'mandiri' => [
                [
                    'nama' => 'Livin by Mandiri',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih Ecommerce.<br>
                                3. Pilih Midtrans di bagian penyedia jasa.<br>
                                4. Masukkan nomor virtual account pada bagian kode bayar.<br>
                                5. Klik lanjutkan untuk konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'ATM Mandiri',
                    'isi' => '1. Pilih bayar/beli pada menu utama.<br>
                                2. Pilih lainnya.<br>
                                3. Pilih multi payment.<br>
                                4. Masukkan kode perusahaan Midtrans 70012.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'Mandiri Internet Banking',
                    'isi' => '1. Pilih bayar pada menu utama.<br>
                                2. Pilih multi payment.<br>
                                3. Pilih dari rekening.<br>
                                4. Pilih Midtrans di bagian penyedia jasa.<br>
                                5. Masukkan kode pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
            ],
            'bni' => [
                [
                    'nama' => 'ATM BNI',
                    'isi' => '1. Pilih menu lain pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BNI.<br>
                                4. Masukkan nomor rekening pembayaran.<br>
                                5. Masukkan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.<br>
                                7. Internet Banking'
                ],
                [
                    'nama' => 'BNI Internet Banking',
                    'isi' => '1. Pilih transaksi, lalu info & administrasi transfer.<br>
                                2. Pilih atur rekening tujuan.<br>
                                3. Masukkan informasi rekening, lalu konfirmasi.<br>
                                4. Pilih transfer, lalu transfer ke rekening BNI.<br>
                                5. Masukkan detail pembayaran, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BNI Mobile Banking',
                    'isi' => '1. Pilih transfer.<br>
                                2. Pilih virtual account billing.<br>
                                3. Pilih rekening debit yang akan digunakan.<br>
                                4. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'bri' => [
                [
                    'nama' => 'ATM BRI',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih lainnya.<br>
                                4. Pilih BRIVA.<br>
                                5. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'IB BRI',
                    'isi' => '1. Pilih pembayaran & pembelian.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'BRImo',
                    'isi' => '1. Pilih pembayaran.<br>
                                2. Pilih BRIVA.<br>
                                3. Masukkan nomor BRIVA, lalu konfirmasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'bca' => [
                [
                    'nama' => 'ATM BCA',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih transfer.<br>
                                3. Pilih ke rekening BCA virtual account.<br>
                                4. Masukan Nomor BCA virtual account.<br>
                                5. Masukan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'Klik BCA',
                    'isi' => '1. Pilih Transfer Dana.<br>
                                2. Pilih Transfer ke BCA virtual account.<br>
                                3. Masukkan nomor BCA virtual account.<br>
                                4. Masukan jumlah yang akan dibayar, lalu konfirmasi.<br>
                                5. Pembayaran berhasil.'
                ],
                [
                    'nama' => 'M-BCA',
                    'isi' => '1. Pilih m-Transfer.<br>
                                2. Pilih BCA virtual account.<br>
                                3. Masukkan nomor BCA virtual account.<br>
                                4. Masukan jumlah yang akan dibayar, lalu konfirmasi<br>
                                5. Pembayaran berhasil.'
                ],
            ],
            'permata' => [
                [
                    'nama' => 'ATM Permata/ALTO',
                    'isi' => '1. Pilih transaksi lainnya pada menu utama.<br>
                                2. Pilih pembayaran.<br>
                                3. Pilih pembayaran lainnya.<br>
                                4. Pilih virtual account.<br>
                                5. Masukkan nomor virtual account Permata, lalu konfirmasi.<br>
                                6. Pembayaran berhasil.'
                ],
            ],
            'cimb' => [
                [
                    'nama' => 'ATM CIMB Niaga',
                    'isi' => '1. Pilih pembayaran pada menu utama.<br>
                                2. Pilih virtual account.<br>
                                3. Masukkan nomor virtual account, lalu konfirmasi.<br>
                                4. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Clicks',
                    'isi' => '1. Pilih pembayaran tagihan pada menu utama.<br>
                                2. Pilih mobile rekening virtual.<br>
                                3. Masukkan nomor virtual account, lalu klik lanjut untuk verifikasi detail.<br>
                                4. Pilih kirim OTP untuk lanjut.<br>
                                5. Masukkan OTP yang dikirimkan ke nomor HP Anda, lalu konfirmasi.<br>
                                6. Pembayaran selesai.'
                ],
                [
                    'nama' => 'OCTO Mobile',
                    'isi' => '1. Pilih menu transfer.<br>
                                2. Pilih transfer to other CIMB Niaga account.<br>
                                3. Pilih sumber dana: CASA atau rekening ponsel.<br>
                                4. Masukkan nomor virtual account.<br>
                                5. Masukkan jumlah yang akan dibayar.<br>
                                6. Ikuti instruksi untuk menyelesaikan pembayaran.<br>
                                7. Pembayaran selesai.'
                ],
            ],
            'qris' => [
                [
                    'nama' => 'QRIS',
                    'isi' => '1. Buka aplikasi yang mendukung pembayaran dengan QRIS.<br>
                                2. Download atau pindai QRIS pada layar.<br>
                                3. Konfirmasi pembayaran pada aplikasi.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'gopay' => [
                [
                    'nama' => 'GoPay',
                    'isi' => '1. Klik Bayar sekarang.<br>
                                2. Aplikasi Gojek atau GoPay akan terbuka.<br>
                                3. Konfirmasi pembayaran di aplikasi Gojek atau GoPay.<br>
                                4. Pembayaran berhasil.'
                ],
            ],
            'toko' => [
                [
                    'nama' => 'Pemesanan Gudang',
                    'isi' => '-'
                ],
            ],
            'market' => [
                [
                    'nama' => 'Pemesanan Marketplace',
                    'isi' => '-'
                ],
            ],
            'card' => 'Always Success'
        ];
        // $status = "Proses";
        //     break;
        // case 'capture':
        //     $status = "Proses";
        //     break;
        // case 'pending':
        //     $status = "Menunggu Pembayaran";
        //     break;
        // case 'expire':
        //     $status = "Kadaluarsa";
        //     break;
        // case 'deny':
        //     $status = "Ditolak";
        //     break;
        // case 'failure':
        //     $status = "Gagal";
        //     break;
        // case 'refund':
        //     $status = "Refund";
        //     break;
        // case 'partial_refund':
        //     $status = "Partial Refund";
        //     break;
        // case 'cancel':
        //     $status = "Dibatalkan";
        //     break;
        // default:
        //     $status = "No Status";
        $statusAll = ['Proses', 'Menunggu Pembayaran', 'Kadaluarsa', 'Ditolak', 'Gagal', 'Refund', 'Partial Refund', 'Dibatalkan'];
        $statusSelain = array_filter($statusAll, function ($s) use ($status) {
            return strtolower($s) != strtolower($status);
        });
        foreach ($pemesanan as $ind_p => $p) {
            $pemesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            $pemesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
            $pemesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $items = $pemesanan[$ind_p]['items'];
            foreach ($items as $ind_i => $i) {
                $produknya = $this->barangModel->getBarang($i['id']);
                if ($produknya) {
                    $items[$ind_i]['name'] = $produknya['nama'];
                }
            }
        }
        foreach ($pemesananAll as $ind_p => $p) {
            $pemesananAll[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            $pemesananAll[$ind_p]['kurir'] = json_decode($p['kurir'], true);
            $pemesananAll[$ind_p]['items'] = json_decode($p['items'], true);
            $items = $pemesananAll[$ind_p]['items'];
            foreach ($items as $ind_i => $i) {
                $produknya = $this->barangModel->getBarang($i['id']);
                if ($produknya) {
                    $items[$ind_i]['name'] = $produknya['nama'];
                }
            }
        }
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [
            'title' => 'Peroses Pembayaran',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'pemesanan' => $pemesanan,
            'pemesananAll' => $pemesananAll,
            'bulan' => $bulan,
            'carapembayaran' => $carapembayaran,
            'status' => $status,
            'statusSelain' => $statusSelain,
        ];
        switch (strtolower($status)) {
            case 'menunggu pembayaran':
                return view('pages/detailOrderMenunggu', $data);
                break;
            case 'proses':
                return view('pages/detailOrderProses', $data);
                break;
            
            default:
                # code...
                break;
        }
    }
    public function invoice($id_mid)
    {
        $transaksi = $this->pemesananModel->getPemesanan($id_mid);
        // dd($transaksi);
        $arr = [
            'id' => $transaksi['id'],
            'nama' => $transaksi['nama'],
            'email' => $transaksi['email'],
            'nohp' => $transaksi['nohp'],
            'alamat' => $transaksi['alamat'],
            'resi' => $transaksi['resi'],
            'id_midtrans' => $transaksi['id_midtrans'],
            'items' => json_decode($transaksi['items'], true),
            'status' => $transaksi['status'],
            'kurir' => count(json_decode($transaksi['kurir'], true)) > 0 ? json_decode($transaksi['kurir'], true) : [
                'nama' => 'Menunggu pengiriman',
                'deskripsi' => 'Kosong',
            ],
            'data_mid' => json_decode($transaksi['data_mid'], true),
        ];
        foreach ($arr['items'] as $ind_i => $i) {
            if ($i['id'] != 'Voucher' && $i['id'] != 'Biaya Admin') {
                $arr['items'][$ind_i]['collection'] = $this->barangModel->getBarang($i['id'])['kategori'];
            }
        }
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $data = [
            'title' => 'Print Preview',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'transaksi' => $arr,
            'transaksiJson' => json_encode($arr),
            'bulan' => $bulan
        ];
        return view('pages/invoice', $data);
    }
    public function wishlist()
    {
        $wishlist = $this->session->get('wishlist');
        $produk = [];
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $ketemuProdukNull = [];
        foreach ($wishlist as $index => $w) {
            $produkCek = $this->barangModel->getBarang($w);
            if (!$produkCek) {
                array_push($ketemuProdukNull, $index);
            }
        }
        if (count($ketemuProdukNull) > 0) {
            foreach ($ketemuProdukNull as $k) {
                unset($wishlist[$k]);
                $wishlistBaru = array_values($wishlist);
                $this->session->set(['wishlist' => $wishlistBaru]);
                $email = session()->get('email');
                if ($email)
                    $this->pembeliModel->where('email', $email)->set(['wishlist' => json_encode($wishlistBaru)])->update();
            }
            return redirect()->to('/wishlist');
        }
        foreach ($wishlist as $w) {
            array_push($produk, $this->barangModel->getBarang($w));
        }
        $data = [
            'title' => 'Favorite',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
        $barang = $this->barangModel->getBarang($id_barang);
        return redirect()->to('/product/' . str_replace(' ', '-', $barang['nama']));
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
        $barang = $this->barangModel->getBarang($id_barang);
        return redirect()->to('/product/' . str_replace(' ', '-', $barang['nama']));
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

    public function actionSearchArticle()
    {
        $cari = $this->request->getVar('cari');
        return redirect()->to('/article/find/' . str_replace(' ', '-', $cari));
    }
    public function findArticle($cari)
    {
        $artikel = $this->artikelModel->like('judul', str_replace("-", " ", $cari), 'both')->orderBy('id', 'desc')->findAll();
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        foreach ($artikel as $ind_a => $a) {
            $artikel[$ind_a]['header'] = '/imgart/' . $a['id'];
            $artikel[$ind_a]['isi'] = json_decode($a['isi'], true);
            $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
            $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
        }
        $data = [
            'title' => 'Artikel',
            'artikel' => $artikel,
            'find' => str_replace('-', ' ', $cari)
        ];
        return view('pages/artikelAll', $data);
    }

    public function login()
    {
        $data = [
            'title' => 'Akun',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->set($ses_data);
            session()->setFlashdata('msg', "Email " . $email . " perlu diverifikasi");
            return redirect()->to('/verify');
        }
        if ($getUser['role'] == '0' || $getUser['role'] == '4') {
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
            return redirect()->to('/admin/product');
        } else if ($getUser['role'] == '2') {
            $nama = ucwords(str_replace("_", " ", substr($getUser['email'], 0, -4)));
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'isLogin' => true,
                'nama' => $nama
            ];
            session()->set($ses_data);
            return redirect()->to('/gudang/listorder');
        } else if ($getUser['role'] == '3') {
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->set($ses_data);
            return redirect()->to('/market/product');
        }
    }
    public function editSandi($path)
    {
        $sandi = $this->request->getVar('sandi');
        $sandiKonfirm = $this->request->getVar('sandiKonfirm');
        $email = session()->get('email');
        if ($sandi != $sandiKonfirm) {
            session()->setFlashdata('msg-sandi', 'Sandi yang terkonfirmasi tidak cocok');
            return redirect()->to('/' . $path);
        }
        $this->userModel->where(['email' => $email])->set([
            'sandi' => password_hash($sandi, PASSWORD_DEFAULT),
        ])->update();
        session()->setFlashdata('msg-sandi', 'Sandi Anda berhasil diubah');
        return redirect()->to('/' . $path);
    }
    public function register()
    {
        $data = [
            'title' => 'Membuat Akun',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
            'validasi-syarat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Anda belum menyetujui syarat dan ketentuan pendaftaran'
                ]
            ],
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-nama', $validation->getError('nama'));
            session()->setFlashdata('val-email', $validation->getError('email'));
            session()->setFlashdata('val-sandi', $validation->getError('sandi'));
            session()->setFlashdata('val-nohp', $validation->getError('nohp'));
            session()->setFlashdata('val-syarat', $validation->getError('validasi-syarat'));
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
            'email' => $this->request->getVar('email'),
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
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
        session()->setFlashdata('msg_active', true);
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
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
            'alamat' => $alamat,
            'alamatJson' => json_encode($alamat),
            'email' => session()->get('email'),
            'nama' => session()->get('nama'),
            'nohp' => session()->get('nohp'),
            'provinsi' => $provinsi["rajaongkir"]["results"],
            'msgSandi' => session()->get('msg-sandi') ? session()->get('msg-sandi') : false,
            'msg' => session()->getFlashdata('msg')
        ];
        return view('pages/account', $data);
    }
    public function visiMisi()
    {
        $data = [
            'title' => 'Visi dan Misi',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/visiMisi', $data);
    }
    public function faq()
    {
        $data = [
            'title' => 'FAQ',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/faq', $data);
    }

    public function article($judul_article = false)
    {
        $wishlist = session()->get('wishlist');
        if (!$wishlist) {
            $wishlist = [];
        }
        $artikel = $this->artikelModel->getArtikelJudul($judul_article);
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        if (!$artikel) return redirect()->to('article');
        if ($judul_article) {
            $artikel['header'] = '/imgart/' . $artikel['id'];
            $artikel['isi'] = json_decode($artikel['isi'], true);
            $artikel['kategori'] = explode(",", $artikel['kategori']);
            $artikel['waktu'] = date("d", strtotime($artikel['waktu'])) . " " . $bulan[date("m", strtotime($artikel['waktu'])) - 1] . " " . date("Y", strtotime($artikel['waktu']));

            $artikelTerkait = $this->artikelModel->like('kategori', $artikel['kategori'][0], 'both')->findAll();
            foreach ($artikelTerkait as $ind_a => $a) {
                $artikelTerkait[$ind_a]['header'] = '/imgart/' . $a['id'];
                $artikelTerkait[$ind_a]['isi'] = json_decode($a['isi'], true);
                $artikelTerkait[$ind_a]['kategori'] = explode(",", $a['kategori']);
                $artikelTerkait[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
            }
            $produkTerkait = $this->barangModel->where(['subkategori' => $artikel['kategori'][0]])->orderBy('pengunjung', 'desc')->findAll(10, 0);
            $data = [
                'title' => 'Artikel ' . $artikel['judul'],
                'artikel' => $artikel,
                'artikelTerkait' => $artikelTerkait,
                'produkTerkait' => $produkTerkait,
                'komen' => json_decode($artikel['komen'], true),
                'komenJson' => $artikel['komen'],
                'metaDeskripsi' => $artikel['judul'],
                'metaKeyword' => $artikel['keywords'],
                'wishlist' => $wishlist
            ];
            return view('pages/artikel', $data);
        } else {
            foreach ($artikel as $ind_a => $a) {
                $artikel[$ind_a]['header'] = '/imgart/' . $a['id'];
                $artikel[$ind_a]['isi'] = json_decode($a['isi'], true);
                $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
                $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
            }
            $data = [
                'title' => 'Artikel',
                'artikel' => $artikel
            ];
            return view('pages/artikelAll', $data);
        }
    }

    public function addLikeArticle($id_artikel)
    {
        $artikelCurr = $this->artikelModel->getArtikel($id_artikel);
        $this->artikelModel->where(['id' => $id_artikel])->set(['suka' => $artikelCurr['suka'] + 1])->update();
        return redirect()->to('/article/' . urlencode($artikelCurr['judul']));
    }

    public function tentang()
    {
        $data = [
            'title' => 'Tentang Kami',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/tentang', $data);
    }
    public function contact()
    {
        $data = [
            'title' => 'Kontak Kami',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/contact', $data);
    }
    public function syarat()
    {
        $data = [
            'title' => 'Syarat & Ketentuan',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/syarat', $data);
    }
    public function kebijakan()
    {
        $data = [
            'title' => 'Kebijakan Privasi',
            'navbar' => [
                'koleksi' => $this->koleksiModel->findAll(),
                'jenis' => $this->jenisModel->findAll(),
            ],
        ];
        return view('pages/kebijakan', $data);
    }

    public function gantinamakekecil($batchSize = 20)
    {
        try {
            // Hitung total produk
            $totalProduk = $this->barangModel->countAll();
            if ($totalProduk == 0) {
                return $this->response->setJSON(['pesan' => 'Tidak ada produk yang tersedia'], 404);
            }

            // Hitung jumlah produk yang sudah huruf kecil
            $produkLowercase = $this->barangModel
                ->where("LOWER(nama) = nama") // Cek apakah sudah huruf kecil
                ->countAllResults();

            // Hitung jumlah produk yang belum huruf kecil
            $produkBelumLowercase = $totalProduk - $produkLowercase;

            // Jika semua sudah huruf kecil
            if ($produkBelumLowercase == 0) {
                return $this->response->setJSON([
                    'pesan' => 'Semua produk sudah menjadi huruf kecil',
                    'total' => $totalProduk,
                    'sudah_lowercase' => $produkLowercase,
                    'belum_lowercase' => $produkBelumLowercase
                ], 200);
            }

            // Ambil offset dari request (default: 0)
            $offset = $this->request->getVar('offset') ?? 0;

            // Ambil batch produk yang belum huruf kecil
            $produk = $this->barangModel
                ->where("LOWER(nama) != nama") // Hanya produk yang belum huruf kecil
                ->findAll($batchSize, $offset);

            // Ubah nama produk menjadi huruf kecil
            foreach ($produk as $p) {
                $this->barangModel->where("id", $p["id"])->set([
                    "nama" => strtolower($p["nama"])
                ])->update();
            }

            // Hitung batch yang telah diproses
            $nextOffset = $offset + $batchSize;

            // Respon sukses
            return $this->response->setJSON([
                'pesan' => count($produk) . ' produk berhasil diubah',
                'next_offset' => $nextOffset,
                'total' => $totalProduk,
                'sudah_lowercase' => $produkLowercase + count($produk),
                'belum_lowercase' => $produkBelumLowercase - count($produk)
            ], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan dan kembalikan pesan error
            return $this->response->setJSON([
                'pesan' => 'Terjadi kesalahan saat mengubah data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}