<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\GambarArtikelModel;
use App\Models\ArtikelModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\KelurahanModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\PemesananGudangModel;
use App\Models\KartuStokModel;
use App\Models\ProvinsiModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\VoucherModel;
use App\Models\GambarHeaderModel;
use App\Models\VoucherUsageModel;
use App\Services\ShippingService;


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
    protected $voucherUsageModel;
    protected $session;
    protected $apikey_img_ilena;

    protected $provinsiModel;
    protected $kabupatenModel;
    protected $kecamatanModel;
    protected $kelurahanModel;
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
        $this->voucherUsageModel = new VoucherUsageModel();
        $this->session = \Config\Services::session();
        $this->apikey_img_ilena = env('APIKEY_IMG_ILENA', 'DefaultValue');
        $this->provinsiModel = new ProvinsiModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->kecamatanModel = new KecamatanModel();
        $this->kelurahanModel = new KelurahanModel();
    }
    public function getNavbarData()
    {
        $jenis = $this->barangModel->query(
            "SELECT 
                subkategori AS jenis, 
                GROUP_CONCAT(DISTINCT kategori ORDER BY kategori ASC) AS koleksi
            FROM barang
            GROUP BY subkategori;"
        )->getResultArray();

        $hasil = [];
        foreach ($jenis as $j) {
            $arrKoleksi = [];
            $koleksi = explode(',', $j['koleksi']);

            foreach ($koleksi as $k) {
                $produk = $this->barangModel
                    ->select('id')
                    ->select('nama')
                    ->select('deskripsi')
                    ->where(['subkategori' => $j['jenis'], 'kategori' => $k])->first();
                if (!$produk) continue;
                $deskripsi = json_decode($produk['deskripsi'], true) ?? [];
                $deskripsi['perawatan'] = '';
                $rawText = $deskripsi['deskripsi'] ?? '';
                $text = strip_tags($rawText);
                $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                $text = trim($text);
                $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);
                $deskripsi['deskripsi'] = $text;
                $itemKoleksi = [
                    'id' => $produk['id'],
                    'nama' => $produk['nama'],
                    'deskripsi' => $deskripsi,
                    'koleksi' => $k
                ];
                array_push($arrKoleksi, $itemKoleksi);
            }

            if (str_contains($j['jenis'], 'dresser')) {
                $hasil['dresser'][$j['jenis']] = $arrKoleksi;
            } else if (str_contains($j['jenis'], 'bed')) {
                $hasil['bed'][$j['jenis']] = $arrKoleksi;
            } else {
                $hasil[$j['jenis']] = $arrKoleksi;
            }
        }
        return $hasil;
    }
    public function index()
    {
        $sliders = $this->gambarHeaderModel->findAll();
        $produk  = $this->barangModel->orderBy('pengunjung', 'desc')->findAll(4, 0);
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Home',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'metaKeyword' => 'ilena Furniture, Toko Furniture, Sorely Ilena Semarang, Cabana Ilena Semarang, Orca Ilena Semarang, Plint Base Ilena Semarang, Cutout Ilena Semarang, Living Room Ilena Semarang, Bed Room Ilena Semarang, Lounge Room Ilena Semarang',
            'produk' => $produk,
            'wishlist' => $wishlist,
            'sliders' => $sliders,
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
        $koleksi = $this->koleksiModel->findAll();
        $jenis = $this->jenisModel->findAll();
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Cari Produk',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'koleksiJenis' => [
                'koleksi' => $koleksi,
                'jenis' => $jenis,
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

    // BAGIAN VOUCHER
    private function getEligibleVoucher($email, $subtotal) {
    $today = date('Y-m-d');
    $voucherList = $this->voucherModel
        ->where('aktif', 1)
        ->where("(mulai IS NULL OR mulai <= '$today')")
        ->where("(berakhir IS NULL OR berakhir >= '$today')")
        ->findAll();

    foreach ($voucherList as $v) {
        if ((int)$v['auto_apply'] !== 1) continue;

        // target check
        if ($v['target'] === 'baru') {
        $user = $this->pembeliModel->where('email', $email)->first();
        if (!$user || (int)$user['welcome_used'] === 1) continue;
        }

        // cek usage limit
        $globalCount = $this->voucherUsageModel
            ->where('kode_voucher', $v['kode'])->countAllResults();
        if ($v['max_pakai'] > 0 && $globalCount >= $v['max_pakai']) continue;

        // cek usage per user
        $perUserCount = $this->voucherUsageModel
            ->where(['kode_voucher' => $v['kode'], 'email' => $email])
            ->countAllResults();
        if ($v['sekali_pakai_per_user'] == 1 && $perUserCount > 0) continue;

        // cek minimal belanja
        if ($subtotal < (int)$v['minimal_belanja']) continue;

        return $v;
    }

    return null;
    }

    private function applyVoucherToItems(array &$items, int &$total, array $voucher) {
    if (!$voucher) return;
    $potongan = 0;
    if ($voucher['tipe'] === 'persen') {
        $potongan = floor($total * ($voucher['nilai'] / 100));
    } else {
        $potongan = (int)$voucher['nilai'];
    }

    $items[] = [
        'id' => strtoupper($voucher['kode']),
        'name' => $voucher['nama'] ?: "Voucher ".$voucher['kode'],
        'quantity' => 1,
        'price' => -$potongan
    ];

    $total -= $potongan;
    if ($total < 0) $total = 0;
    }


    // END BAGIAN VOUCHER

    public function product($nama = false, $ind_nama = false)
    {
        $wishlist = $this->session->get('wishlist');
        $koleksi  = $this->koleksiModel->findAll();
        $jenis    = $this->jenisModel->findAll();
        $nama     = str_replace('-', ' ', $nama);

        if (!isset($wishlist)) {
            $wishlist = [];
        }

        if ($nama) {
            $productsemua = $this->barangModel->where(['nama' => $nama])->findAll();
            $product      = $productsemua[$ind_nama];
            $product['deskripsi'] = json_decode($product['deskripsi'] ?? '{}', true) ?? [];
            $product['varian']    = json_decode($product['varian'] ?? '[]', true) ?? [];

            $produkSejenis = $this->barangModel
                ->where(['subkategori' => $product['subkategori']])
                ->where('id !=', $product['id'])
                ->orderBy('pengunjung', 'desc')
                ->findAll(8, 0);

            $seluruhBarangFilter = [];
            $seluruhNama =  [];
            foreach ($produkSejenis as $s) {
                if (!in_array($s['nama'], $seluruhNama)) {
                    array_push($seluruhBarangFilter, $s);
                    array_push($seluruhNama, $s['nama']);
                }
            }

            $data = [
                'title'         => ucwords($product['nama']),
                'navbar'        => $this->getNavbarData(),
                'apikey_img_ilena' => $this->apikey_img_ilena,
                'produk'        => $product,
                'wishlist'      => $wishlist,
                'produkSejenis' => $seluruhBarangFilter,
                'produkSemua'   => $productsemua,
                'indexNama'     => $ind_nama,
                'metaDeskripsi' => $product['nama'] . ' ilena futniture Ilena Semarang',
                'metaKeyword'   => $product['kategori'] . ' Ilena Semarang'
            ];

            // menambah pengunjung
            $this->barangModel->where(['id' => $product['id']])
                            ->set(['pengunjung' => (int)$product['pengunjung'] + 1])
                            ->update();

            return view('pages/product', $data);
        } else {
            // ====== TAMBAHAN KECIL: dukung /product?jenis=diskon (atau bundling) ======
            $jenisParam = strtolower(trim($this->request->getGet('jenis') ?? ''));

            // ambil data default seperti sistem lama
            $product = $this->barangModel->getBarangNama();

            if ($jenisParam === 'diskon') {
                // semua produk dengan diskon > 0
                $product = array_values(array_filter($product, function ($row) {
                    return (float)($row['diskon'] ?? 0) > 0;
                }));
                $titlePage = 'Produk Diskon';
            } elseif ($jenisParam === 'bundling') {
                // (opsional) produk yang namanya mengandung "bundling" dan sedang diskon
                $product = array_values(array_filter($product, function ($row) {
                    return (float)($row['diskon'] ?? 0) > 0
                        && stripos($row['nama'] ?? '', 'bundling') !== false;
                }));
                $titlePage = 'Bundling Sedang Diskon';
            } else {
                $titlePage = 'Produk Kami'; // default persis seperti sistem lama
            }
            // ====== END TAMBAHAN ======

            $data = [
                'title' => $titlePage,
                'navbar' => $this->getNavbarData(),
                'apikey_img_ilena' => $this->apikey_img_ilena,
                'produk' => $product,
                'koleksiJenis' => [
                    'koleksi' => $koleksi,
                    'jenis'   => $jenis,
                ],
                'jenis' => $jenis,
                'wishlist' => $wishlist,
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
        // Pastikan user terverifikasi
        if (session()->get('active') === '0') {
            return redirect()->to('/verify');
        }

        // Ambil keranjang dari session (fallback ke array kosong)
        $keranjang = session()->get('keranjang') ?? [];

        // Validasi dan sinkronisasi stok
        $invalidIndex = [];
        $insufficientIndex = [];
        foreach ($keranjang as $i => $item) {
            $produk = $this->barangModel->getBarang($item['id_barang']);
            if (! $produk) {
                $invalidIndex[] = $i;
                continue;
            }
            $varianList = json_decode($produk['varian'], true);
            $foundVariant = false;
            foreach ($varianList as $v) {
                if (strtolower($v['nama']) === strtolower($item['varian'])) {
                    $foundVariant = true;
                    if ($v['stok'] < $item['jumlah']) {
                        if ($v['stok'] === 0) {
                            $invalidIndex[] = $i;
                        } else {
                            $insufficientIndex[] = ['index' => $i, 'stok' => $v['stok']];
                        }
                    }
                    break;
                }
            }
            if (! $foundVariant) {
                $invalidIndex[] = $i;
            }
        }

        // Hapus produk yang tidak valid
        if (! empty($invalidIndex)) {
            foreach ($invalidIndex as $idx) {
                unset($keranjang[$idx]);
            }
            $keranjang = array_values($keranjang);
            session()->set('keranjang', $keranjang);
            $this->syncCartToUser($keranjang);
            return redirect()->to('/cart');
        }

        // Perbaiki jumlah jika stok kurang
        if (! empty($insufficientIndex)) {
            foreach ($insufficientIndex as $info) {
                $keranjang[$info['index']]['jumlah'] = $info['stok'];
            }
            session()->set('keranjang', $keranjang);
            $this->syncCartToUser($keranjang);
            return redirect()->to('/cart');
        }

        // Hitung total harga dan pasang detail gambar
        $hargaTotal = 0;
        foreach ($keranjang as $i => $item) {
            $produk = $this->barangModel->getBarang($item['id_barang']);
            $varians = json_decode($produk['varian'], true);
            foreach ($varians as $v) {
                if ($v['nama'] === $item['varian']) {
                    $keranjang[$i]['src_gambar'] = "/img/barang/1000/{$item['id_barang']}-" . explode(',', $v['urutan_gambar'])[0] . '.webp';
                    break;
                }
            }
            $keranjang[$i]['detail'] = $produk;
            $linePrice = $produk['harga'] * $item['jumlah'] * (100 - $produk['diskon']) / 100;
            $hargaTotal += $linePrice;
        }

        // Siapkan data untuk view
        return view('pages/cart', [
            'title'       => 'Keranjang',
            'navbar'      => $this->getNavbarData(),
            'keranjang'   => $keranjang,
            'hargaTotal'  => $hargaTotal,
        ]);
    }

    public function addCart($idbarang, $varian, $jumlah)
    {
        $keranjang = session()->get('keranjang') ?? [];
        $found = false;
        foreach ($keranjang as &$item) {
            if ($item['id_barang'] == $idbarang && $item['varian'] == $varian) {
                $item['jumlah'] += (int) $jumlah;
                $found = true;
                break;
            }
        }
        if (! $found) {
            $keranjang[] = [
                'id_barang' => $idbarang,
                'varian'    => $varian,
                'jumlah'    => (int) $jumlah,
            ];
        }
        session()->set('keranjang', $keranjang);
        $this->syncCartToUser($keranjang);
        return redirect()->to('/cart');
    }

    public function reduceCart($idx)
    {
        $keranjang = session()->get('keranjang') ?? [];
        if (isset($keranjang[$idx])) {
            $keranjang[$idx]['jumlah']--;
            if ($keranjang[$idx]['jumlah'] <= 0) {
                unset($keranjang[$idx]);
            }
        }
        $keranjang = array_values($keranjang);
        session()->set('keranjang', $keranjang);
        $this->syncCartToUser($keranjang);
        return redirect()->to('/cart');
    }

    public function deleteCart($idx)
    {
        $keranjang = session()->get('keranjang') ?? [];
        if (isset($keranjang[$idx])) {
            unset($keranjang[$idx]);
        }
        $keranjang = array_values($keranjang);
        session()->set('keranjang', $keranjang);
        $this->syncCartToUser($keranjang);
        return redirect()->to('/cart');
    }

    private function syncCartToUser(array $cart)
    {
        if ($email = session()->get('email')) {
            $this->pembeliModel->where('email', $email)
                                 ->set(['keranjang' => json_encode($cart)])
                                 ->update();
        }
    }

    public function getKota($id_prov)
    {
        
        $kota = $this->kabupatenModel->getKabupatenByProvinsi($id_prov);
        return $this->response->setJSON($kota, false);
    }
    public function getKec($id_kota)
    {
       
        $kec = $this->kecamatanModel->getKecamatanByKabupaten($id_kota);
        return $this->response->setJSON($kec, false);
    }
    public function getKode($kec)
    {
        
        $kode = $this->kelurahanModel->getKelurahanByKecamatan($kec);
        if (!$kode) {
            return $this->response->setJSON(['error' => 'Kode pos tidak ditemukan'], false);
        }
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
        $provinsi = $this->provinsiModel->getProvinsi();

        $cartCheck = $this->validateCheckoutCart($keranjang);
        if (!$cartCheck['ok']) {
            session()->setFlashdata('msg', $cartCheck['msg']);
            return redirect()->to('/cart');
        }
        $keranjang = $cartCheck['cart'];
        $hargaTotal = $cartCheck['subtotal'];

        $alamat = $this->session->get('alamat');
        if (!isset($alamat)) {
            $alamat = [];
        }

        $data = [
            'title' => 'Alamat',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'provinsi' => $provinsi,
            'keranjang' => $keranjang,
            'hargaTotal' => $hargaTotal,
            'hargaKeseluruhan' => $hargaTotal,
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
        // dd($this->request->getVar());
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
        if (!is_array($alamat) || !array_key_exists($ind_add, $alamat)) {
            return redirect()->to('/address');
        }

        $alamatselected = $alamat[$ind_add];
        $keranjang = $this->session->get('keranjang');
        if (!isset($keranjang)) {
            return redirect()->to('/product');
        }
        $cartCheck = $this->validateCheckoutCart($keranjang);
        if (!$cartCheck['ok']) {
            session()->setFlashdata('msg', $cartCheck['msg']);
            return redirect()->to('/cart');
        }
        $keranjang = $cartCheck['cart'];
        $hargaTotal = $cartCheck['subtotal'];

        $shippingService = new ShippingService();
        $kurir = $shippingService->rates($alamatselected, $keranjang);

        if (empty($kurir)) {
            session()->setFlashdata('msg', 'Pilihan kurir belum tersedia untuk alamat ini. Silakan cek alamat atau hubungi admin.');
            return redirect()->to('/address');
        }

        $data = [
            'title' => 'Pengiriman',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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

    public function isTimeInRange($startTime, $endTime)
    {
        $currentTime = date("H:i", strtotime(("+7 hours")));
        if ($currentTime >= $startTime && $currentTime <= $endTime) {
            return true;
        } else {
            return false;
        }
    }

    private function validateCheckoutCart(array $keranjang): array
    {
        if (empty($keranjang)) {
            return ['ok' => false, 'msg' => 'Keranjangmu kosong.', 'cart' => [], 'subtotal' => 0, 'flashSale' => 0];
        }

        $cart = [];
        $subtotal = 0;
        $hargaTotalBundling = 0;
        $flashSale = 0;

        foreach ($keranjang as $item) {
            $produk = $this->barangModel->getBarang($item['id_barang'] ?? null);
            if (!$produk) {
                return ['ok' => false, 'msg' => 'Ada produk di keranjang yang sudah tidak tersedia.', 'cart' => [], 'subtotal' => 0, 'flashSale' => 0];
            }

            $varianList = json_decode($produk['varian'] ?? '[]', true);
            if (!is_array($varianList)) {
                return ['ok' => false, 'msg' => 'Data varian produk tidak valid.', 'cart' => [], 'subtotal' => 0, 'flashSale' => 0];
            }

            $jumlah = (int)($item['jumlah'] ?? 0);
            if ($jumlah <= 0) {
                return ['ok' => false, 'msg' => 'Jumlah produk di keranjang tidak valid.', 'cart' => [], 'subtotal' => 0, 'flashSale' => 0];
            }

            $varianTerpilih = null;
            foreach ($varianList as $v) {
                if (strtolower((string)($v['nama'] ?? '')) === strtolower((string)($item['varian'] ?? ''))) {
                    $varianTerpilih = $v;
                    break;
                }
            }

            if (!$varianTerpilih) {
                return ['ok' => false, 'msg' => 'Ada varian produk yang sudah tidak tersedia.', 'cart' => [], 'subtotal' => 0, 'flashSale' => 0];
            }

            if ((int)($varianTerpilih['stok'] ?? 0) < $jumlah) {
                return [
                    'ok' => false,
                    'msg' => 'Stok ' . $produk['nama'] . ' varian ' . $item['varian'] . ' tidak mencukupi.',
                    'cart' => [],
                    'subtotal' => 0,
                    'flashSale' => 0
                ];
            }

            $urutan = explode(',', $varianTerpilih['urutan_gambar'] ?? '');
            $item['src_gambar'] = "/img/barang/1000/{$item['id_barang']}-" . trim($urutan[0] ?? '1') . '.webp';
            $item['detail'] = $produk;
            $cart[] = $item;

            $harga = (float)($produk['harga'] ?? 0);
            $diskon = (float)($produk['diskon'] ?? 0);
            $lineTotal = round(((100 - $diskon) / 100) * $harga) * $jumlah;
            $subtotal += $lineTotal;

            if (str_contains(strtolower($produk['nama'] ?? ''), 'bundling')) {
                $hargaTotalBundling += $lineTotal;
                foreach (["03:00@07:00"] as $range) {
                    [$start, $end] = explode("@", $range);
                    if ($this->isTimeInRange($start, $end)) {
                        $flashSale = $hargaTotalBundling * 15 / 100;
                    }
                }
            }
        }

        return ['ok' => true, 'msg' => '', 'cart' => $cart, 'subtotal' => $subtotal, 'flashSale' => $flashSale];
    }

    private function paymentFees(): array
    {
        return [
            'bca'=>['type'=>'flat','value'=>4000,'taxable'=>true],
            'bri'=>['type'=>'flat','value'=>4000,'taxable'=>true],
            'bni'=>['type'=>'flat','value'=>4000,'taxable'=>true],
            'mandiri'=>['type'=>'flat','value'=>4000,'taxable'=>true],
            'permata'=>['type'=>'flat','value'=>4000,'taxable'=>true],
            'cimb'=>['type'=>'flat','value'=>4000,'taxable'=>true],
            'gopay'=>['type'=>'percent','value'=>2.0],
            'shopeepay'=>['type'=>'percent','value'=>2.0],
            'qris'=>['type'=>'percent','value'=>0.7],
            'card'=>['type'=>'percent','value'=>2.9,'flat_add'=>2000],
        ];
    }

    private function calculateAdminFee(string $method, float $baseAmount): int
    {
        $fees = $this->paymentFees();
        if (!isset($fees[$method])) {
            return 0;
        }

        $rule = $fees[$method];
        $fee = 0;
        if ($rule['type'] === 'flat') {
            $fee = (float)$rule['value'];
            if (!empty($rule['taxable'])) {
                $fee += $fee * 0.11;
            }
        } else {
            $fee = ((float)$rule['value'] / 100) * $baseAmount;
        }
        if (isset($rule['flat_add'])) {
            $fee += (float)$rule['flat_add'];
        }

        return (int)ceil($fee);
    }

    public function payment($index_kurir)
    {
        if (session()->get('active') == '0') return redirect()->to('/verify');

        $keranjang = $this->session->get('keranjang');
        $alamatselected = $this->session->get('alamatTerpilih');
        $kurir = $this->session->get('kurir');

        if (!is_array($alamatselected) || empty($alamatselected)) return redirect()->to('/address');
        if (!is_array($kurir) || !array_key_exists($index_kurir, $kurir)) return redirect()->to('/address');
        if (!is_array($keranjang) || empty($keranjang)) {
            session()->setFlashdata('msg','Keranjangmu kosong.');
            return redirect()->to('/cart');
        }

        $cartCheck = $this->validateCheckoutCart($keranjang);
        if (!$cartCheck['ok']) {
            session()->setFlashdata('msg', $cartCheck['msg']);
            return redirect()->to('/cart');
        }
        $keranjang = $cartCheck['cart'];
        $hargaTotal = (float)$cartCheck['subtotal'];
        $flashSale = (float)$cartCheck['flashSale'];
        $kurirTerpilih = $kurir[$index_kurir];
        $hargaOngkir = (float)($kurirTerpilih['harga'] ?? 0);

        // ====== legacy voucher untuk email uji (tetap, tidak auto-apply) ======
        $voucher      = [];
        $emailUjiCoba = ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','tipaun0605@gmail.com'];
        if (session()->get('isLogin') && in_array($alamatselected['email_pemesan'], $emailUjiCoba, true)) {
            $voucherMemberBaru = $this->voucherModel->where(['id'=>1])->first();
            if ($voucherMemberBaru) {
                $listEmail = json_decode($voucherMemberBaru['list_email'] ?? '[]', true);
                if (!is_array($listEmail)) $listEmail = [];
                if (!in_array($alamatselected['email_pemesan'], $listEmail, true)) {
                    $voucher[] = $voucherMemberBaru;
                }
            }
        }

        // ====== voucher hasil klaim (redeem) — ditampilkan sebagai pilihan, bukan auto apply ======
        $claimedIds = (array)(session()->get('voucher_claimed') ?? []);
        if (!empty($claimedIds)) {
            $claimedRows = $this->voucherModel->whereIn('id', array_map('intval', $claimedIds))->findAll();
            foreach ($claimedRows as $cr) {
                $tipe  = $cr['tipe']  ?? ($cr['satuan']  ?? 'persen');
                $nilai = $cr['nilai'] ?? ($cr['nominal'] ?? 0);
                $voucher[] = [
                    'id'          => (int)$cr['id'],
                    'kode'        => $cr['kode'],
                    'nama'        => $cr['nama'],
                    'deskripsi'   => $cr['deskripsi'] ?? '',
                    'tipe'        => $tipe,
                    'nilai'       => (float)$nilai,
                    'nominal'     => $nilai,
                    'satuan'      => ($tipe === 'persen') ? 'persen' : 'rupiah',
                    'target'      => $cr['target'] ?? 'semua',
                    'auto_apply'  => $cr['auto_apply'] ?? 0,
                    'aktif'       => $cr['aktif'] ?? 1,
                    'minimal_belanja' => (int)($cr['minimal_belanja'] ?? 0),
                    'mulai'       => $cr['mulai'] ?? null,
                    'berakhir'    => $cr['berakhir'] ?? null,
                ];
            }
            // dedupe by id
            $voucher = array_values(array_reduce($voucher, function($acc,$v){ $acc[$v['id']]=$v; return $acc; }, []));
        }

        // ====== Rekomendasi voucher eligible (pakai helper yang sama!) ======
        $email    = $alamatselected['email_pemesan'];
        $subtotal = $hargaTotal;

        try {
            $autoRows = $this->voucherModel->where(['aktif'=>1, 'auto_apply'=>1])->findAll();
        } catch (\Throwable $th) {
            $autoRows = [];
        }

        foreach ($autoRows as $row) {
            $elig = $this->checkVoucherEligibility($row, $email, $subtotal);
            if (!$elig['ok']) continue;

            $vRecord = [
                'id'            => (int)$row['id'],
                'kode'          => $row['kode'],
                'nama'          => $row['nama'],
                'deskripsi'     => $row['deskripsi'] ?? '',
                'tipe'          => $row['tipe'] ?? ($row['satuan'] ?? 'persen'),
                'nilai'         => (float)($row['nilai'] ?? ($row['nominal'] ?? 0)),
                'nominal'       => ($row['nilai'] ?? ($row['nominal'] ?? 0)),
                'satuan'        => (($row['tipe'] ?? ($row['satuan'] ?? 'persen')) === 'persen') ? 'persen' : 'rupiah',
                'target'        => $row['target'] ?? 'semua',
                'auto_apply'    => (int)($row['auto_apply'] ?? 0),
                'aktif'         => (int)($row['aktif'] ?? 1),
                'minimal_belanja'=> (int)($row['minimal_belanja'] ?? 0),
                'mulai'         => $row['mulai'] ?? null,
                'berakhir'      => $row['berakhir'] ?? null,
                'recommended'   => true,
                'estimated_cut' => (int)$elig['cut'],
            ];

            // hindari duplikat (jika sudah ada dari claimed/legacy)
            $exists = false;
            foreach ($voucher as $vx) {
                if ((int)($vx['id'] ?? 0) === (int)$vRecord['id']) { $exists = true; break; }
            }
            if (!$exists) $voucher[] = $vRecord;
        }

        // ====== Diskon voucher yg DIPILIH user (bukan auto-apply) ======
        $diskonVoucher = 0; $voucherSelected = false;
        if (session()->get('voucher')) {
            $vd = $this->voucherModel->find((int)session()->get('voucher'));
            if ($vd) {
                $elig = $this->checkVoucherEligibility($vd, $email, $hargaTotal);
                if (!empty($elig['ok'])) {
                    $diskonVoucher = (int)($elig['cut'] ?? 0);
                    $voucherSelected = $vd + ['rupiah' => $diskonVoucher];
                } else {
                    session()->remove('voucher');
                    session()->setFlashdata('msg', $elig['msg'] ?? 'Voucher tidak memenuhi syarat.');
                }
            }
        }

        // ====== (Opsional) auto pilih yg terbesar — TAPI TIDAK auto-apply.
        //      Kita hanya "merekomendasikan" di UI (kamu sudah punya UI pilih voucher).
        //      Jadi bagian ini sengaja tidak override pilihan user. ======

        $method  = session()->get('payment_method') ?? 'bca';
        session()->set('payment_method', $method);

        $baseForFee = max(0, $hargaTotal - $diskonVoucher - $flashSale + $hargaOngkir);
        $biayaAdmin  = $this->calculateAdminFee($method, $baseForFee);
        $grossAmount = max(0, (int) round($baseForFee + $biayaAdmin));

        $data = [
            'title'            => 'Pembayaran',
            'navbar'           => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'hargaTotal'       => $hargaTotal,
            'user' => [
                'email'  => $alamatselected['email_pemesan'],
                'nama'   => $alamatselected['nama_penerima'],
                'no_hp'  => $alamatselected['nohp_penerima'],
                'alamat' => $alamatselected['alamat_lengkap'],
            ],
            'keranjang' => $keranjang,
            'dataMidJson' => base64_encode(json_encode([
                'code'      => ':ilenafur',
                'email'     => $alamatselected['email_pemesan'],
                'nama'      => $alamatselected['nama_penerima'],
                'nohp'      => $alamatselected['nohp_penerima'],
                'alamat'    => $alamatselected['alamat_lengkap'],
                'keranjang' => $this->session->get('keranjang'),
                'kurir'     => $kurirTerpilih,
                'voucher'   => $voucherSelected ? ['d'=>(int)$diskonVoucher,'id'=>$voucherSelected['id']] : false
            ])),
            'indexAddress'      => $index_kurir,
            'voucher'           => ['list'=>$voucher, 'selected'=>$voucherSelected],
            'emailUji'          => in_array($alamatselected['email_pemesan'], $emailUjiCoba, true),
            'msg'               => session()->getFlashdata('msg'),
            'hargaOngkir'       => $hargaOngkir,
            'kurir'             => $kurirTerpilih,
            'flashSale'         => $flashSale,
            'biayaAdmin'        => $biayaAdmin,
            'paymentMethod'     => $method,
            'listPaymentMethod' => ['bca','bri','bni','mandiri','permata','cimb','gopay','shopeepay','qris'],
            'grossAmount'       => $grossAmount,
        ];

        $this->session->set(['alamatTerpilih' => $alamatselected]);
        $this->session->set(['kurirTerpilih' => $kurirTerpilih]);
        return view('pages/payment', $data);
    }



    public function paymentMethod($method, $ind_add)
    {
        $method = strtolower(trim((string)$method));
        if (!array_key_exists($method, $this->paymentFees())) {
            session()->setFlashdata('msg', 'Metode pembayaran tidak valid.');
            return redirect()->to('/payment/' . $ind_add);
        }

        session()->set('payment_method', $method);
        return redirect()->to('/payment/' . $ind_add);
    }

    public function useVoucher($data)
    {
        [$ind_voucher, $ind_address] = explode('-', $data);
        session()->set('voucher', $ind_voucher);
        return redirect()->to('/payment/' . $ind_address);
    }

    public function cancelVoucher($data)
    {
        [$ind_voucher, $ind_address] = explode('-', $data);
        session()->remove('voucher');
        return redirect()->to('/payment/' . $ind_address);
    }

    private function validateVoucherByCode(string $code, string $email, float $subtotal): array
    {
        $code = strtoupper(trim($code));
        if ($code === '') return ['ok'=>false,'msg'=>'Kode voucher kosong.'];

        $row = $this->voucherModel->where(['kode' => $code])->first();
        if (!$row) return ['ok'=>false,'msg'=>'Voucher tidak ditemukan.'];

        if (empty($row['aktif'])) {
            return ['ok'=>false,'msg'=>'Voucher sedang nonaktif.'];
        }

        $today = date('Y-m-d');
        if (!empty($row['mulai']) && $today < substr($row['mulai'],0,10)) {
            return ['ok'=>false,'msg'=>'Voucher belum mulai berlaku.'];
        }
        if (!empty($row['berakhir']) && $today > substr($row['berakhir'],0,10)) {
            return ['ok'=>false,'msg'=>'Voucher sudah berakhir.'];
        }

        $minBelanja = (float)($row['minimal_belanja'] ?? 0);
        if ($subtotal < $minBelanja) {
            return ['ok'=>false,'msg'=>'Belanja belum mencapai minimal voucher.'];
        }

        // Limit global
        $maxPakai = (int)($row['max_pakai'] ?? 0);
        if ($maxPakai > 0) {
            $usedCount = $this->voucherUsageModel
                ->where('kode_voucher', $row['kode'])
                ->countAllResults();
            if ($usedCount >= $maxPakai) {
                return ['ok'=>false,'msg'=>'Voucher sudah mencapai limit pemakaian.'];
            }
        }

        // Sekali per user?
        $onceUser = !empty($row['sekali_pakai_per_user']);
        if ($onceUser) {
            $already = $this->voucherUsageModel
                ->where(['kode_voucher'=>$row['kode'], 'email'=>$email])
                ->countAllResults();
            if ($already > 0) {
                return ['ok'=>false,'msg'=>'Voucher ini hanya bisa dipakai sekali per pengguna.'];
            }
        }

        // Target user
        $target = strtolower($row['target'] ?? 'semua');
        if ($target === 'baru') {
            $isFirst = $this->isFirstOrderByEmail($email);
            if (!$isFirst) return ['ok'=>false,'msg'=>'Voucher khusus member baru.'];
        } elseif ($target === 'lama') {
            $isFirst = $this->isFirstOrderByEmail($email);
            if ($isFirst) return ['ok'=>false,'msg'=>'Voucher khusus member lama.'];
        } elseif ($target === 'spesifik') {
            $list = json_decode($row['list_email'] ?? '[]', true);
            if (is_array($list) && !in_array($email, $list, true)) {
                return ['ok'=>false,'msg'=>'Voucher ini khusus pengguna tertentu.'];
            }
        }

        // Hitung diskon
        $tipe  = $row['tipe']  ?? ($row['satuan']  ?? 'persen');
        $nilai = (float)($row['nilai'] ?? ($row['nominal'] ?? 0));
        $rupiah = 0;
        if ($tipe === 'persen') $rupiah = (int) floor(($nilai / 100.0) * $subtotal);
        else $rupiah = (int) $nilai;
        $rupiah = max(0, min($rupiah, (int)$subtotal));

        return ['ok'=>true, 'msg'=>'Voucher berhasil diterapkan.', 'voucher'=>$row, 'rupiah'=>$rupiah];
    }

    /**
     * Hook sederhana untuk deteksi first order.
     * Ganti dengan query ke Order/Pemesanan model kamu agar akurat.
     */
    private function isFirstOrderByEmail(string $email): bool
    {
        try {
            if (property_exists($this, 'pemesananModel') && $this->pemesananModel) {
                $cnt = $this->pemesananModel->where('email', $email)->countAllResults();
                return $cnt === 0;
            }
        } catch (\Throwable $th) {}
        return true; // fallback: anggap baru
    }

    /**
     * POST /redeemvoucher/{ind_add}
     * Simpan voucher ke session (kompat dengan flow lama).
     */
    public function redeemVoucher($ind_add)
    {
        log_message('debug', '[REDEEM] method='.$this->request->getMethod().' ind_add='.$ind_add);

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/payment/'.$ind_add);
        }

        // Terima `code` atau `kode` (kompat semua view)
        $rawCode = (string)($this->request->getPost('code') ?? $this->request->getPost('kode') ?? '');
        $code = strtoupper(trim($rawCode));
        log_message('debug', '[REDEEM] input code="'.$rawCode.'" normalized="'.$code.'"');

        if ($code === '') {
            session()->setFlashdata('msg','Kode voucher kosong.');
            return redirect()->to('/payment/'.$ind_add);
        }

        // Cek alamat & keranjang
        $alamatTerpilih = $this->session->get('alamatTerpilih');
        if (!is_array($alamatTerpilih) || empty($alamatTerpilih)) {
            session()->setFlashdata('msg','Alamat tidak valid.');
            return redirect()->to('/payment/'.$ind_add);
        }
        $email = (string)($alamatTerpilih['email_pemesan'] ?? '');

        $keranjang = $this->session->get('keranjang');
        if (!is_array($keranjang) || empty($keranjang)) {
            session()->setFlashdata('msg','Keranjang kosong.');
            return redirect()->to('/payment/'.$ind_add);
        }

        // Hitung subtotal (setara payment())
        $subtotal = 0.0;
        foreach ($keranjang as $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            if (!$produk) continue;
            $harga  = (float)($produk['harga'] ?? 0);
            $diskon = (float)($produk['diskon'] ?? 0);
            $jumlah = (int)($k['jumlah'] ?? 0);
            $subtotal += $harga * $jumlah * (100 - $diskon) / 100;
        }
        $subtotal = max(0, $subtotal);
        log_message('debug', '[REDEEM] subtotal='.$subtotal.' email='.$email);

        // Ambil voucher by kode (case-insensitive aman di semua collation)
        $row = $this->voucherModel->where('LOWER(kode)', strtolower($code))->first();
        if (!$row) {
            session()->setFlashdata('msg','Kode voucher tidak ditemukan.');
            return redirect()->to('/payment/'.$ind_add);
        }
        log_message('debug', '[REDEEM] voucher found id='.$row['id'].' kode='.$row['kode']);

        // Validasi eligibility (satu sumber kebenaran)
        $elig = $this->checkVoucherEligibility($row, $email, $subtotal);
        if (empty($elig['ok'])) {
            // Pesan dari helper sudah spesifik (periode/min belanja/sekali user/dll)
            $reason = (string)($elig['msg'] ?? 'Voucher tidak memenuhi syarat.');
            session()->setFlashdata('msg', $reason);
            log_message('debug', '[REDEEM] not eligible: '.$reason);
            return redirect()->to('/payment/'.$ind_add);
        }

        // CLAIM BERHASIL → tambahkan ke daftar pilihan (bukan auto-apply)
        $claimed = (array)(session()->get('voucher_claimed') ?? []);
        $id = (int)$row['id'];
        if (!in_array($id, $claimed, true)) {
            $claimed[] = $id;
            session()->set('voucher_claimed', $claimed);
        }

        // Jangan pilih otomatis—biarkan user memilih
        $pot = (int)($elig['cut'] ?? 0);
        $msg = 'Voucher "'.$row['kode'].'" berhasil diklaim.';
        session()->setFlashdata('msg', $msg);

        return redirect()->to('/payment/'.$ind_add);
    }


    private function checkVoucherEligibility(array $row, string $email, float $subtotal): array
    {
        $tipe  = $row['tipe']  ?? ($row['satuan'] ?? 'persen');
        $nilai = (float)($row['nilai'] ?? ($row['nominal'] ?? 0));
        $today = date('Y-m-d');

        // 1) Aktif
        if (empty($row['aktif'])) {
            return ['ok'=>false,'msg'=>'Voucher sedang nonaktif.'];
        }

        // 2) Periode
        $mulai = !empty($row['mulai']) ? substr($row['mulai'],0,10) : null;
        $akhir = !empty($row['berakhir']) ? substr($row['berakhir'],0,10) : null;
        if ($mulai && $today < $mulai) {
            return ['ok'=>false,'msg'=>'Voucher belum mulai berlaku (mulai '.$mulai.').'];
        }
        if ($akhir && $today > $akhir) {
            return ['ok'=>false,'msg'=>'Voucher sudah berakhir (berlaku sampai '.$akhir.').'];
        }

        // 3) Minimal belanja
        $minBelanja = (float)($row['minimal_belanja'] ?? 0);
        if ($subtotal < $minBelanja) {
            return [
                'ok'=>false,
                'msg'=>'Belanja belum mencapai minimal voucher: Rp '.number_format($minBelanja,0,',','.').
                    '. Subtotal kamu: Rp '.number_format($subtotal,0,',','.')
            ];
        }

        // 4) Limit global
        try {
            $maxPakai = (int)($row['max_pakai'] ?? 0);
            if ($maxPakai > 0 && isset($this->voucherUsageModel)) {
                $usedCount = $this->voucherUsageModel
                    ->where('kode_voucher', $row['kode'])
                    ->countAllResults();
                if ($usedCount >= $maxPakai) {
                    return ['ok'=>false,'msg'=>'Voucher sudah mencapai limit pemakaian.'];
                }
            }
        } catch (\Throwable $th) {}

        // 5) Sekali per user
        if (!empty($row['sekali_pakai_per_user'])) {
            try {
                if (isset($this->voucherUsageModel)) {
                    $already = $this->voucherUsageModel
                        ->where(['kode_voucher'=>$row['kode'], 'email'=>$email])
                        ->countAllResults();
                    if ($already > 0) {
                        return ['ok'=>false,'msg'=>'Voucher ini hanya dapat digunakan sekali per pengguna.'];
                    }
                }
            } catch (\Throwable $th) {}

            // fallback list_email (kalau dipakai tracking)
            $listEmail = json_decode($row['list_email'] ?? '[]', true);
            if (is_array($listEmail) && in_array($email, $listEmail, true)) {
                return ['ok'=>false,'msg'=>'Voucher ini sudah pernah kamu pakai.'];
            }
        }

        // 6) Target
        $target = strtolower($row['target'] ?? 'semua');
        if ($target === 'baru' && !$this->isFirstOrderByEmail($email)) {
            return ['ok'=>false,'msg'=>'Voucher khusus member baru.'];
        }
        if ($target === 'lama' && $this->isFirstOrderByEmail($email)) {
            return ['ok'=>false,'msg'=>'Voucher khusus member lama.'];
        }
        if ($target === 'spesifik') {
            $wl = json_decode($row['list_email'] ?? '[]', true);
            if (is_array($wl) && !in_array($email, $wl, true)) {
                return ['ok'=>false,'msg'=>'Voucher ini khusus pengguna tertentu.'];
            }
        }

        // 7) Estimasi potongan
        $rupiah = ($tipe === 'persen')
            ? (int) floor(($nilai / 100.0) * $subtotal)
            : (int) $nilai;
        $rupiah = max(0, min($rupiah, (int)$subtotal));

        return ['ok'=>true,'msg'=>'Voucher valid.','cut'=>$rupiah];
    }


    public function removeVoucher($ind_add)
    {
        $this->session->remove('voucher');
        return redirect()->to('/payment/'.$ind_add);
    }

    private function midtransTestEmails(): array
    {
        return ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','adityaanugrah494@gmail.com','tipaun0605@gmail.com'];
    }

    private function midtransServerKey(string $email = ''): string
    {
        if (in_array($email, $this->midtransTestEmails(), true)) {
            return 'SB-Mid-server-3M67g25LgovNPlwdS4WfiMsh';
        }

        return (string)env('MIDTRANS_PRODUCTION_KEY', 'DefaultValue');
    }

    private function midtransStatusToOrderStatus(string $transactionStatus, string $fraudStatus = 'accept'): string
    {
        if ($fraudStatus !== '' && $fraudStatus !== 'accept') {
            return 'Forbidden';
        }

        $map = [
            'settlement' => 'Proses',
            'capture' => 'Proses',
            'pending' => 'Menunggu Pembayaran',
            'expire' => 'Kadaluarsa',
            'deny' => 'Ditolak',
            'failure' => 'Gagal',
            'refund' => 'Refund',
            'partial_refund' => 'Partial Refund',
            'cancel' => 'Dibatalkan',
        ];

        return $map[$transactionStatus] ?? 'No Status';
    }

    private function isChargeableProductItem(array $item): bool
    {
        $name = strtolower((string)($item['name'] ?? ''));
        $id = strtolower((string)($item['id'] ?? ''));
        return !in_array($name, ['voucher', 'flash sale', 'biaya admin', 'biaya ongkir'], true)
            && !in_array($id, ['voucher', 'flash sale', 'biaya admin', 'biaya ongkir'], true);
    }

    private function itemVariantName(array $item): string
    {
        $name = (string)($item['name'] ?? '');
        if (preg_match('/\(([^()]*)\)\s*$/', $name, $m)) {
            return trim($m[1]);
        }

        return '';
    }

    private function markVoucherUsed(array $order): void
    {
        $items = json_decode($order['items'] ?? '[]', true);
        if (!is_array($items)) return;

        $email = (string)($order['email'] ?? '');
        foreach ($items as $item) {
            if (strtolower((string)($item['name'] ?? '')) !== 'voucher') continue;

            $kode = (string)($item['id'] ?? '');
            if ($kode === '' || $kode === 'Voucher') continue;

            try {
                $exists = $this->voucherUsageModel
                    ->where(['kode_voucher' => $kode, 'email' => $email])
                    ->countAllResults();
                if ($exists <= 0) {
                    $this->voucherUsageModel->insert([
                        'kode_voucher' => $kode,
                        'email' => $email,
                        'used_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $vRec = $this->voucherModel->where('kode', $kode)->first();
                if ($vRec) {
                    $listEmail = json_decode($vRec['list_email'] ?? '[]', true);
                    if (!is_array($listEmail)) $listEmail = [];
                    if (!in_array($email, $listEmail, true)) {
                        $listEmail[] = $email;
                        $this->voucherModel->update($vRec['id'], ['list_email' => json_encode($listEmail)]);
                    }
                }
            } catch (\Throwable $th) {
                log_message('error', 'Gagal mencatat voucher order ' . ($order['id_midtrans'] ?? '') . ': ' . $th->getMessage());
            }
        }
    }

    private function processPaidOrder(array $order): void
    {
        $idPesanan = (string)($order['id_midtrans'] ?? '');
        if ($idPesanan === '') return;

        $alreadyQueued = $this->pemesananGudangModel->where('id_pesanan', $idPesanan)->countAllResults();
        if ($alreadyQueued > 0) {
            return;
        }

        $items = json_decode($order['items'] ?? '[]', true);
        $dataMid = json_decode($order['data_mid'] ?? '[]', true);
        if (!is_array($items)) return;

        $tanggal = $dataMid['transaction_time'] ?? date('Y-m-d H:i:s');
        foreach ($items as $item) {
            if (!$this->isChargeableProductItem($item)) continue;

            $barang = $this->barangModel->getBarang($item['id'] ?? null);
            if (!$barang) continue;

            $variantName = $this->itemVariantName($item);
            $qty = max(0, (int)($item['quantity'] ?? 0));
            if ($variantName === '' || $qty <= 0) continue;

            $varians = json_decode($barang['varian'] ?? '[]', true);
            if (!is_array($varians)) continue;

            $saldo = 0;
            foreach ($varians as $idx => $v) {
                if (strtolower((string)($v['nama'] ?? '')) === strtolower($variantName)) {
                    $varians[$idx]['stok'] = max(0, (int)($v['stok'] ?? 0) - $qty);
                    $saldo = $varians[$idx]['stok'];
                    break;
                }
            }
            $this->barangModel->update($barang['id'], ['varian' => json_encode($varians)]);

            for ($x = 1; $x <= $qty; $x++) {
                $this->pemesananGudangModel->insert([
                    'id_pesanan' => $idPesanan,
                    'tanggal' => $tanggal,
                    'nama' => $item['name'],
                    'id_barang' => $item['id'],
                    'packed' => false,
                    'printed' => false,
                ]);
            }

            $tanggalNoStrip = date("YmdHis", strtotime($tanggal));
            $this->kartuStokModel->insert([
                'id_barang' => $item['id'],
                'tanggal' => $tanggal,
                'keterangan' => $tanggalNoStrip . "-" . $item['id'] . "-" . strtoupper($variantName) . "-" . $idPesanan,
                'debit' => 0,
                'kredit' => $qty,
                'saldo' => $saldo,
                'pending' => true,
                'id_pesanan' => $idPesanan,
                'varian' => strtoupper($variantName),
            ]);
        }

        $this->markVoucherUsed($order);
    }

    private function restorePaidOrderStock(array $order): void
    {
        $items = json_decode($order['items'] ?? '[]', true);
        if (!is_array($items)) return;

        foreach ($items as $item) {
            if (!$this->isChargeableProductItem($item)) continue;

            $barang = $this->barangModel->getBarang($item['id'] ?? null);
            if (!$barang) continue;

            $variantName = $this->itemVariantName($item);
            $qty = max(0, (int)($item['quantity'] ?? 0));
            if ($variantName === '' || $qty <= 0) continue;

            $varians = json_decode($barang['varian'] ?? '[]', true);
            if (!is_array($varians)) continue;

            foreach ($varians as $idx => $v) {
                if (strtolower((string)($v['nama'] ?? '')) === strtolower($variantName)) {
                    $varians[$idx]['stok'] = (int)($v['stok'] ?? 0) + $qty;
                    break;
                }
            }
            $this->barangModel->update($barang['id'], ['varian' => json_encode($varians)]);
        }
    }

    // ========================================================================
    // ======================  actionPayCore() — FULL  ========================
    // ========================================================================

    public function actionPayCore($token)
    {
        $deCodeToken = base64_decode($token);
        $parts = explode(':', $deCodeToken);
        if (count($parts) < 2) {
            session()->setFlashdata('msg', 'Token tidak valid');
            return redirect()->to('/address');
        }

        [$stamp, $index_kurir] = $parts;
        if (!is_numeric($stamp) || (time() - (int)$stamp) > 600) {
            session()->setFlashdata('msg', 'Token kadaluarsa, silakan ulangi pembayaran');
            return redirect()->to('/payment/' . $index_kurir);
        }

        $pembayaran = (string)session()->get('payment_method');
        $alamatselected = session()->get('alamatTerpilih');
        $kurir = session()->get('kurir');
        if (!is_array($alamatselected) || empty($alamatselected)) {
            session()->setFlashdata('msg','Alamat tidak ditemukan, silakan ulangi.');
            return redirect()->to('/address');
        }
        if (!is_array($kurir) || !array_key_exists($index_kurir, $kurir)) {
            session()->setFlashdata('msg','Kurir tidak ditemukan, silakan pilih ulang.');
            return redirect()->to('/address');
        }

        if ($pembayaran === 'card' && !$this->validate(['tokencc'=>'required'])) {
            session()->setFlashdata('msg','Data kartu belum lengkap');
            return redirect()->to('/payment/' . $index_kurir)->withInput();
        }

        $email = $alamatselected['email_pemesan'];
        $nama = $alamatselected['nama_penerima'];
        $nohp = $alamatselected['nohp_penerima'];
        $alamat = $alamatselected['alamat_lengkap'];
        $kurirTerpilih = $kurir[$index_kurir];
        $emailUjiCoba = $this->midtransTestEmails();

        $cartCheck = $this->validateCheckoutCart((array)(session()->get('keranjang') ?? []));
        if (!$cartCheck['ok']) {
            session()->setFlashdata('msg', $cartCheck['msg']);
            return redirect()->to('/cart');
        }

        $subtotal = (float)$cartCheck['subtotal'];
        $flashSale = (float)$cartCheck['flashSale'];
        $itemDetails = [];
        foreach ($cartCheck['cart'] as $element) {
            $produk = $element['detail'];
            $harga = (float)$produk['harga'];
            $diskon = (float)$produk['diskon'];
            $jumlah = (int)$element['jumlah'];
            $hasil = (int)round(((100 - $diskon) / 100) * $harga);
            $desc = json_decode($produk['deskripsi'] ?? '[]', true);
            $dim = $desc['dimensi']['asli']['panjang'] ?? '';
            $varLbl = ucfirst($element['varian'] ?? '');

            $itemDetails[] = [
                'id' => $produk['id'],
                'price' => $hasil,
                'quantity' => $jumlah,
                'name' => $produk['nama'] . " " . $dim . " (" . $varLbl . ")",
            ];
        }

        $diskonVoucher = 0;
        $voucher = false;
        if (session()->get('voucher')) {
            $vd = $this->voucherModel->find((int)session()->get('voucher'));
            if ($vd) {
                $elig = $this->checkVoucherEligibility($vd, $email, $subtotal);
                if (empty($elig['ok'])) {
                    session()->setFlashdata('msg', $elig['msg'] ?? 'Voucher tidak memenuhi syarat.');
                    return redirect()->to('/payment/' . $index_kurir);
                }
                $diskonVoucher = (int)($elig['cut'] ?? 0);
                $voucher = ['d' => $diskonVoucher, 'id' => $vd['id'], 'kode' => $vd['kode']];
            }
        }

        $total = $subtotal;
        if ($voucher) {
            $itemDetails[] = ['id'=>$voucher['kode'],'price'=>-$voucher['d'],'quantity'=>1,'name'=>'Voucher'];
            $total -= $voucher['d'];
        }
        if ($flashSale > 0) {
            $itemDetails[] = ['id'=>'Flash Sale','price'=>-$flashSale,'quantity'=>1,'name'=>'Flash Sale'];
            $total -= $flashSale;
        }

        $hargaOngkir = (int)($kurirTerpilih['harga'] ?? 0);
        $itemDetails[] = ['id'=>'Biaya Ongkir','price'=>$hargaOngkir,'quantity'=>1,'name'=>'Biaya Ongkir'];
        $total += $hargaOngkir;

        $biayaAdmin = $this->calculateAdminFee($pembayaran, $total);
        $itemDetails[] = ['id'=>'Biaya Admin','price'=>$biayaAdmin,'quantity'=>1,'name'=>'Biaya Admin'];
        $total += $biayaAdmin;

        $last = $this->pemesananModel->orderBy('id','desc')->first();
        $idAsli = "IL".sprintf("%08d", $last ? ((int)$last['id']+1) : 1);
        $idFix = in_array($email, $emailUjiCoba, true) ? ("IL".rand()) : $idAsli;

        $customField = json_encode(['e'=>$email,'n'=>$nama,'h'=>$nohp,'a'=>$alamat,'i'=>session()->get('keranjang'),'k'=>$kurirTerpilih,'v'=>$voucher]);
        $arrPostField = [
            "transaction_details" => ["order_id"=>$idFix, "gross_amount"=>$total],
            "customer_details" => ["email"=>$email,"phone"=>$nohp,"first_name"=>$nama],
            "item_details" => $itemDetails,
            "custom_field1" => substr($customField, 0, 255),
            "custom_field2" => substr($customField, 255, 255),
            "custom_field3" => substr($customField, 510, 255),
        ];

        switch ($pembayaran) {
            case 'bca': case 'bri': case 'bni': case 'cimb':
                $arrPostField["payment_type"] = "bank_transfer";
                $arrPostField["bank_transfer"] = ["bank"=>$pembayaran];
                $arrPostField['custom_expiry'] = ["expiry_duration"=>60,"unit"=>"minute"];
                break;
            case 'permata':
                $arrPostField["payment_type"] = "permata";
                $arrPostField['custom_expiry'] = ["expiry_duration"=>60,"unit"=>"minute"];
                break;
            case 'mandiri':
                $arrPostField["payment_type"] = "echannel";
                $arrPostField["echannel"] = ["bill_info1"=>"Payment:","bill_info2"=>"Online purchase"];
                $arrPostField['custom_expiry'] = ["expiry_duration"=>60,"unit"=>"minute"];
                break;
            case 'qris':
                $arrPostField["payment_type"] = "qris";
                $arrPostField["qris"] = ["acquirer"=>"gopay"];
                $arrPostField['custom_expiry'] = ["expiry_duration"=>15,"unit"=>"minute"];
                break;
            case 'gopay':
                $arrPostField["payment_type"] = "gopay";
                $arrPostField["gopay"] = ["enable_callback"=>true,"callback_url"=>"https://ilenafurniture.com/order/".$idFix];
                $arrPostField['custom_expiry'] = ["expiry_duration"=>15,"unit"=>"minute"];
                break;
            case 'shopeepay':
                $arrPostField["payment_type"] = "shopeepay";
                $arrPostField["shopeepay"] = ["callback_url"=>"https://ilenafurniture.com/order/".$idFix];
                $arrPostField['custom_expiry'] = ["expiry_duration"=>15,"unit"=>"minute"];
                break;
            case 'card':
                $arrPostField["payment_type"] = "credit_card";
                $arrPostField["credit_card"] = ["token_id"=>$this->request->getVar('tokencc')];
                break;
            default:
                return redirect()->to('/payment/' . $index_kurir);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => in_array($email, $emailUjiCoba, true) ? "https://api.sandbox.midtrans.com/v2/charge" : "https://api.midtrans.com/v2/charge",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arrPostField),
            CURLOPT_HTTPHEADER => ["Accept: application/json","Content-Type: application/json","Authorization: Basic ".base64_encode($this->midtransServerKey($email).":")],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) return "cURL Error #:" . $err;
        $hasilMidtrans = json_decode($response, true);
        if (substr($hasilMidtrans['status_code'] ?? '', 0, 1) !== '2') {
            session()->setFlashdata('msg', $hasilMidtrans['status_message'] ?? 'Transaksi gagal');
            return redirect()->to('/payment/' . $index_kurir);
        }

        $status = $this->midtransStatusToOrderStatus($hasilMidtrans['transaction_status'] ?? '', $hasilMidtrans['fraud_status'] ?? 'accept');
        $this->pemesananModel->insert([
            'nama' => $nama,
            'email'=> $email,
            'nohp' => $nohp,
            'alamat'=> $alamat,
            'resi' => 'Menunggu pengiriman',
            'items'=> json_encode($itemDetails),
            'kurir'=> json_encode($kurirTerpilih),
            'id_midtrans'=> $idFix,
            'status'=> $status,
            'data_mid'=> json_encode($hasilMidtrans),
        ]);

        $trx = $this->pemesananModel->where('id_midtrans', $idFix)->first();
        if ($status === 'Proses' && $trx) {
            $this->processPaidOrder($trx);
        }

        session()->remove(['voucher', 'voucher_claimed', 'keranjang', 'kurir', 'kurirTerpilih', 'alamatTerpilih', 'hargaKeseluruhan']);
        $this->syncCartToUser([]);

        return redirect()->to('/orderdetail/' . rawurlencode(strtolower($status)) . '?idorder=' . $idFix);
    }

    public function updateTransaction()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        if (!is_array($body)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid JSON']);
        }

        $orderId = (string)($body['order_id'] ?? '');
        $transactionStatus = (string)($body['transaction_status'] ?? '');
        $statusCode = (string)($body['status_code'] ?? '');
        $grossAmount = (string)($body['gross_amount'] ?? '');
        $signature = (string)($body['signature_key'] ?? '');
        if ($orderId === '' || $transactionStatus === '') {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Missing transaction data']);
        }

        $order = $this->pemesananModel->getPemesanan($orderId);
        if (!$order) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Order not found']);
        }

        $serverKey = $this->midtransServerKey((string)($order['email'] ?? ''));
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signature === '' || !hash_equals($expectedSignature, $signature)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Invalid signature']);
        }

        $oldStatus = (string)($order['status'] ?? '');
        $newStatus = $this->midtransStatusToOrderStatus($transactionStatus, (string)($body['fraud_status'] ?? 'accept'));

        $dataMid = json_decode($order['data_mid'] ?? '[]', true);
        if (!is_array($dataMid)) $dataMid = [];
        $dataMid = array_merge($dataMid, $body);

        $this->pemesananModel->where('id_midtrans', $orderId)->set([
            'status' => $newStatus,
            'data_mid' => json_encode($dataMid),
        ])->update();

        $updatedOrder = $this->pemesananModel->getPemesanan($orderId);
        if ($newStatus === 'Proses' && $oldStatus !== 'Proses' && $updatedOrder) {
            $this->processPaidOrder($updatedOrder);
        }

        if (in_array($newStatus, ['Kadaluarsa', 'Ditolak', 'Gagal', 'Dibatalkan'], true) && $oldStatus === 'Proses' && $updatedOrder) {
            $this->restorePaidOrderStock($updatedOrder);
        }

        return $this->response->setJSON(['success' => true]);
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
                        'navbar' => $this->getNavbarData(),
                        'apikey_img_ilena' => $this->apikey_img_ilena,
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
                        'navbar' => $this->getNavbarData(),
                        'apikey_img_ilena' => $this->apikey_img_ilena,
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
                        'navbar' => $this->getNavbarData(),
                        'apikey_img_ilena' => $this->apikey_img_ilena,
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
                        'navbar' => $this->getNavbarData(),
                        'apikey_img_ilena' => $this->apikey_img_ilena,
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
                'navbar' => $this->getNavbarData(),
                'apikey_img_ilena' => $this->apikey_img_ilena,
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
        $status = rawurldecode($status);
        $idOrder = (string)($this->request->getGet('idorder') ?? '');
        if ($idOrder !== '') {
            $order = $this->pemesananModel->getPemesanan($idOrder);
            $pemesanan = $order ? [$order] : [];
            $pemesananAll = $pemesanan;
            if ($order) {
                $status = $order['status'];
            }
        } else {
            $email = session()->get('email');
            $query = $this->pemesananModel->where('status', $status);
            if ($email) {
                $query = $query->where('email', $email);
            }
            $pemesanan = $email ? $query->findAll() : [];
            $pemesananAll = $email ? $this->pemesananModel->where('email', $email)->findAll() : [];
        }
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'pemesananAll' => $pemesananAll,
            'bulan' => $bulan,
            'carapembayaran' => $carapembayaran,
            'status' => $status,
            'statusSelain' => $statusSelain,
        ];
        switch (strtolower($status)) {
            case 'kadaluarsa':
                if (isset($pemesanan[0]['data_mid']['expiry_time'])) {
                    $data['expiry_time'] = $pemesanan[0]['data_mid']['expiry_time'];
                } else {
                    $data['expiry_time'] = 'Waktu Kadaluarsa Tidak Tersedia';
                }
                return view('pages/detailOrderKadaluarsa', $data);
                break;

            case 'dibatalkan':
                return view('pages/detailOrderBatal', $data);
                break;

            case 'menunggu pembayaran':
                return view('pages/detailOrderMenunggu', $data);
                break;

            case 'proses':
                return view('pages/detailOrderProses', $data);
                break;

            default:
                return redirect()->to('/order');
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
            if ($i['id'] != 'Voucher' && $i['id'] != 'Biaya Admin' && $i['id'] != 'Flash Sale') {
                $barangCur = $this->barangModel->getBarang($i['id']);
                $arr['items'][$ind_i]['collection'] = $barangCur['kategori'];
                $arr['items'][$ind_i]['detail'] = $barangCur;
            }
        }

        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $data = [
            'title' => 'Print Preview',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'transaksi' => $arr,
            'transaksiJson' => json_encode($arr),
            'bulan' => $bulan
        ];
        return view('pages/invoice', $data);
    }

    public function wishlist()
    {
        $wishlist = $this->session->get('wishlist');
        $produk   = [];

        if (!is_array($wishlist)) {
            $wishlist = [];
        }

        // Bersihkan ID wishlist yg tidak ditemukan
        $ketemuProdukNull = [];
        foreach ($wishlist as $index => $w) {
            $produkCek = $this->barangModel->getBarang($w);
            if (!$produkCek) {
                $ketemuProdukNull[] = $index;
            }
        }

        if (!empty($ketemuProdukNull)) {
            foreach ($ketemuProdukNull as $k) {
                unset($wishlist[$k]);
            }
            $wishlistBaru = array_values($wishlist);
            $this->session->set(['wishlist' => $wishlistBaru]);

            $email = session()->get('email');
            if ($email) {
                $this->pembeliModel
                    ->where('email', $email)
                    ->set(['wishlist' => json_encode($wishlistBaru)])
                    ->update();
            }

            return redirect()->to('/wishlist');
        }

        foreach ($wishlist as $w) {
            $barang = $this->barangModel->getBarang($w);
            if ($barang) {
                $produk[] = $barang;
            }
        }

        $data = [
            'title'            => 'Favorite',
            'navbar'           => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produk'           => $produk,
            'wishlist'         => $wishlist,
        ];

        // (Opsional) Balas JSON bila AJAX minta data wishlist
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'count'    => count($wishlist),
                'produk'   => $produk,
                'wishlist' => array_values($wishlist),
            ]);
        }

        return view('pages/wishlist', $data);
    }

    public function addWishlist($id_barang)
    {
        $id_barang = (string) $id_barang;

        $wishlist = $this->session->get('wishlist');
        if (!is_array($wishlist)) {
            $wishlist = [];
        }

        // tambah kalau belum ada
        if ($id_barang !== '' && !in_array($id_barang, $wishlist, true)) {
            $wishlist[] = $id_barang;
        }

        $this->session->set(['wishlist' => $wishlist]);

        $email = session()->get('email');
        if ($email) {
            $this->pembeliModel
                ->where('email', $email)
                ->set(['wishlist' => json_encode($wishlist)])
                ->update();
        }

        // Jika AJAX: kembalikan JSON (tidak redirect)
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'action'   => 'add',
                'id'       => $id_barang,
                'count'    => count($wishlist),
                'wishlist' => array_values($wishlist),
            ]);
        }

        // Non-AJAX: tetap seperti sistem lama (redirect ke product)
        $barang = $this->barangModel->getBarang($id_barang);
        if ($barang && !empty($barang['nama'])) {
            return redirect()->to('/product/' . str_replace(' ', '-', $barang['nama']));
        }
        // Fallback aman jika barang sudah tidak ada
        return redirect()->to('/wishlist');
    }

    public function delWishlist($id_barang)
    {
        $id_barang = (string) $id_barang;

        $wishlist = $this->session->get('wishlist');
        if (!is_array($wishlist)) {
            $wishlist = [];
        }

        $key = array_search($id_barang, $wishlist, true);
        if ($key !== false) {
            unset($wishlist[$key]);
            $wishlist = array_values($wishlist);
        }

        $this->session->set(['wishlist' => $wishlist]);

        $email = session()->get('email');
        if ($email) {
            $this->pembeliModel
                ->where('email', $email)
                ->set(['wishlist' => json_encode($wishlist)])
                ->update();
        }

        // Jika AJAX: balas JSON agar tidak dinavigasi SPA (hindari error dom.js)
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'action'   => 'del',
                'id'       => $id_barang,
                'count'    => count($wishlist),
                'wishlist' => array_values($wishlist),
            ]);
        }

        // Non-AJAX (tetap jalur lama): redirect ke halaman produk terkait
        $barang = $this->barangModel->getBarang($id_barang);
        if ($barang && !empty($barang['nama'])) {
            return redirect()->to('/product/' . str_replace(' ', '-', $barang['nama']));
        }
        // Fallback aman
        return redirect()->to('/wishlist');
    }

    public function wishlistToCart()
    {
        $wishlist  = $this->session->get('wishlist');
        $keranjang = $this->session->get('keranjang');

        if (!is_array($wishlist))  $wishlist  = [];
        if (!is_array($keranjang)) $keranjang = [];

        foreach ($wishlist as $id_barang) {
            $produknya = $this->barangModel->getBarang($id_barang);
            if (!$produknya) {
                continue; // skip barang yg sudah tidak ada
            }

            // amankan varian
            $varianArr = json_decode($produknya['varian'] ?? '[]', true) ?: [];
            $varian    = $varianArr[0]['nama'] ?? 'default';

            $ketemu = false;
            foreach ($keranjang as $index => $k) {
                if (
                    isset($k['id_barang'], $k['varian'])
                    && (string)$k['id_barang'] === (string)$id_barang
                    && (string)$k['varian'] === (string)$varian
                ) {
                    $keranjang[$index]['jumlah'] = (int)($keranjang[$index]['jumlah'] ?? 0) + 1;
                    $ketemu = true;
                    break;
                }
            }

            if (!$ketemu) {
                $keranjang[] = [
                    'id_barang' => $id_barang,
                    'varian'    => $varian,
                    'jumlah'    => 1,
                ];
            }
        }

        $this->session->set(['keranjang' => $keranjang]);

        $email = session()->get('email');
        if ($email) {
            $this->pembeliModel
                ->where('email', $email)
                ->set(['keranjang' => json_encode($keranjang)])
                ->update();
        }

        return redirect()->to('/cart');
    }


    public function actionSearchArticle()
    {
        $cari = $this->request->getVar('cari');
        return redirect()->to('/article/find/' . str_replace(' ', '-', $cari));
    }
    public function findArticle($cari)
    {
        $kataKunci = explode('-', $cari);
        $this->artikelModel->groupStart();
        foreach ($kataKunci as $kata) {
            $this->artikelModel->orLike('judul', $kata, 'both');
        }
        $this->artikelModel->groupEnd();
        $relevan = $this->artikelModel->findAll();

        function slugify($text)
        {
            $text = strtolower($text);
            $text = preg_replace('/[^a-z0-9\s]/', '', $text); // Hilangkan tanda baca
            $text = preg_replace('/\s+/', '-', $text); // Ubah spasi jadi dash
            return trim($text, '-');
        }

        usort($relevan, function ($a, $b) use ($cari) {
            similar_text(slugify($a['judul']), $cari, $percentA);
            similar_text(slugify($b['judul']), $cari, $percentB);
            return $percentB <=> $percentA; // Urutkan dari yang paling mirip ke paling rendah
        });

        $idsRelevan = array_column($relevan, 'id');
        $tambahan = [];
        if (!empty($idsRelevan)) {
            $tambahan = $this->artikelModel
                ->whereNotIn('id', $idsRelevan)
                ->findAll();
        } else {
            $tambahan = $this->artikelModel->findAll();
        }

        // Gabungkan dua hasil
        $artikel = array_merge($relevan, $tambahan);
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $artikelPopuler = $this->artikelModel->orderBy('pengunjung', 'desc')->limit(3, 0)->findAll();
        $artikel3Baru = [];
        foreach ($artikel as $ind_a => $a) {
            $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
            $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
            if ($ind_a < 3) {
                array_push($artikel3Baru, [
                    'judul' => $a['judul'],
                    'path' => $a['path'],
                    'kategori' => $a['kategori'],
                    'deskripsi' => $a['deskripsi'],
                    'id' => $a['id'],
                    'header' => $a['header'],
                ]);
            }
        }

        $data = [
            'title' => 'Artikel',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => array_values(array_filter($artikel, function ($value, $key) {
                return $key >= 2;
            }, ARRAY_FILTER_USE_BOTH)),
            'artikel3BaruJson' => json_encode($artikel3Baru),
            'artikelPopuler' => $artikelPopuler,
            'bulan' => $bulan,
            'cari' => str_replace('-', ' ', $cari)
        ];

        // $data = [
        //     'title' => 'Artikel',
        //     'navbar' => $this->getNavbarData(),
        //     'apikey_img_ilena' => $this->apikey_img_ilena,
        //     'artikel' => $artikel,
        //     'find' => str_replace('-', ' ', $cari),
        //     'bulan' => $bulan
        // ];
        return view('pages/artikelAll', $data);
    }

    public function login()
    {
        $data = [
            'title' => 'Akun',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
                'nama' => 'Admin Ilena',
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
        $provinsi = $this->provinsiModel->findAll();

        $alamat = $this->session->get('alamat');
        if (!isset($alamat)) {
            $alamat = [];
        }

        $data = [
            'title' => 'Akun Saya',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'alamat' => $alamat,
            'alamatJson' => json_encode($alamat),
            'email' => session()->get('email'),
            'nama' => session()->get('nama'),
            'nohp' => session()->get('nohp'),
            'provinsi' => $provinsi,
            'msgSandi' => session()->get('msg-sandi') ? session()->get('msg-sandi') : false,
            'msg' => session()->getFlashdata('msg')
        ];
        return view('pages/account', $data);
    }
    public function visiMisi()
    {
        $data = [
            'title' => 'Visi dan Misi',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
        ];
        return view('pages/visiMisi', $data);
    }
    public function faq()
    {
        $data = [
            'title' => 'FAQ',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
            // ++ pengunjung
            $this->artikelModel->where(['id' => $artikel['id']])
                ->set(['pengunjung' => $artikel['pengunjung'] + 1])
                ->update();

            // normalisasi field
            $artikel['kategori'] = explode(",", $artikel['kategori']);
            $artikel['waktu'] = date("d", strtotime($artikel['waktu'])) . " " .
                $bulan[date("m", strtotime($artikel['waktu'])) - 1] . " " .
                date("Y", strtotime($artikel['waktu']));

            /*
            |----------------------------------------------------------
            | Pagination untuk Artikel Terkait (perubahan minimal)
            | Param halaman khusus: rpage (agar tidak bentrok param lain)
            |----------------------------------------------------------
            */
            $perTerkait = 6; // jumlah item terkait per halaman (sesuaikan)
            $rpage      = max(1, (int)($this->request->getVar('rpage') ?? 1));
            $roffset    = ($rpage - 1) * $perTerkait;

            // Hitung total artikel terkait
            $totalTerkait = $this->artikelModel
                ->like('kategori', $artikel['kategori'][0], 'both')
                ->countAllResults();

            // Ambil artikel terkait sesuai halaman
            $artikelTerkait = $this->artikelModel
                ->like('kategori', $artikel['kategori'][0], 'both')
                ->orderBy('waktu', 'DESC')
                ->findAll($perTerkait, $roffset);

            // Normalisasi artikel terkait
            foreach ($artikelTerkait as $ind_a => $a) {
                $artikelTerkait[$ind_a]['kategori'] = explode(",", $a['kategori']);
                $artikelTerkait[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " .
                    $bulan[date("m", strtotime($a['waktu'])) - 1] . " " .
                    date("Y", strtotime($a['waktu']));
            }

            // Pager sederhana untuk view
            $pagerTerkait = [
                'total'    => $totalTerkait,
                'perPage'  => $perTerkait,
                'current'  => $rpage,
                'lastPage' => (int)ceil(($totalTerkait ?: 0) / $perTerkait),
            ];

            // Produk terkait (tanpa perubahan)
            $produkTerkait = $this->barangModel
                ->where(['subkategori' => $artikel['kategori'][0]])
                ->orderBy('pengunjung', 'desc')
                ->findAll(10, 0);

            $data = [
                'title'             => 'Artikel ' . $artikel['judul'],
                'navbar'            => $this->getNavbarData(),
                'apikey_img_ilena'  => $this->apikey_img_ilena,
                'artikel'           => $artikel,
                'artikelTerkait'    => $artikelTerkait,
                'pagerTerkait'      => $pagerTerkait, // <-- kirim ke view
                'produkTerkait'     => $produkTerkait,
                'komen'             => json_decode($artikel['komen'], true),
                'komenJson'         => $artikel['komen'],
                'metaDeskripsi'     => $artikel['deskripsi'],
                'metaKeyword'       => $artikel['keywords'],
                'wishlist'          => $wishlist,
                'bulan'             => $bulan
            ];
            return view('pages/artikel', $data);
        } else {
            // Halaman daftar artikel (tanpa perubahan)
            $artikelPopuler = $this->artikelModel->orderBy('pengunjung', 'desc')->limit(5, 0)->findAll();
            $artikel3Baru = $this->artikelModel
                ->select('judul')->select('path')->select('kategori')->select('deskripsi')->select('id')->select('header')
                ->orderBy('id', 'asc')->limit(3, 0)->findAll();

            // Catatan: pastikan $artikel terdefinisi jika memang diperlukan di sini.
            // (Di kode asli, foreach ($artikel as ...) dipakai, pastikan Anda sudah menyiapkan $artikel sebelumnya.)
            foreach ($artikel as $ind_a => $a) {
                $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
                $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " .
                    $bulan[date("m", strtotime($a['waktu'])) - 1] . " " .
                    date("Y", strtotime($a['waktu']));
            }

            $data = [
                'title'            => 'Artikel',
                'navbar'           => $this->getNavbarData(),
                'apikey_img_ilena' => $this->apikey_img_ilena,
                'artikel'          => array_values(array_filter($artikel, function ($value, $key) {
                    return $key >= 2;
                }, ARRAY_FILTER_USE_BOTH)),
                'artikel3BaruJson' => json_encode($artikel3Baru),
                'artikelPopuler'   => $artikelPopuler,
                'bulan'            => $bulan
            ];
            return view('pages/artikelAll', $data);
        }
    }

    public function articleCategory($category)
    {
        $category = str_replace('-', ' ', $category);
        $category = str_replace('@', '&', $category);
        $artikel = $this->artikelModel->like('kategori', $category, 'both')->findAll();
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        if (!$artikel) return redirect()->to('article');
        $artikelPopuler = $this->artikelModel->orderBy('pengunjung', 'desc')->limit(5, 0)->findAll();
        $artikel3Baru = $this->artikelModel
            ->select('judul')->select('path')->select('kategori')->select('deskripsi')->select('id')->select('header')
            ->orderBy('id', 'asc')->limit(3, 0)->findAll();
        foreach ($artikel as $ind_a => $a) {
            $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
            $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
        }
        $data = [
            'title' => 'Artikel | ' . ucwords($category),
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => array_values(array_filter($artikel, function ($value, $key) {
                return $key >= 2;
            }, ARRAY_FILTER_USE_BOTH)),
            'artikel3BaruJson' => json_encode($artikel3Baru),
            'artikelPopuler' => $artikelPopuler,
            'bulan' => $bulan,
            'category' => $category,
        ];
        return view('pages/artikelAll', $data);
    }

    public function addLikeArticle($id_artikel)
    {
        $artikelCurr = $this->artikelModel->getArtikel($id_artikel);
        if (!$artikelCurr) {
            return redirect()->to('/article');
        }

        $this->artikelModel->where(['id' => $id_artikel])->set(['suka' => $artikelCurr['suka'] + 1])->update();
        return redirect()->to('/article/' . ($artikelCurr['path'] ?? urlencode($artikelCurr['judul'])));
    }

    public function addShareArticle($id_artikel)
    {
        $artikelCurr = $this->artikelModel->getArtikel($id_artikel);
        if (!$artikelCurr) {
            return redirect()->to('/article');
        }

        $bagikan = (int)($artikelCurr['bagikan'] ?? 0);
        $this->artikelModel->where(['id' => $id_artikel])->set(['bagikan' => $bagikan + 1])->update();
        return redirect()->to('/article/' . ($artikelCurr['path'] ?? urlencode($artikelCurr['judul'])));
    }

    public function tentang()
    {
        $data = [
            'title' => 'Tentang Kami',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
        ];
        return view('pages/tentang', $data);
    }
    public function partner()
    {
        $data = [
            'title' => 'Mitra Kami',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
        ];
        return view('pages/mitra', $data);
    }
    public function iklan()
    {
        $series = [
            [
                'nama'      => 'Cody Series',
                'gaya'      => 'Japandi · Hangat & Estetik',
                'deskripsi' => 'Perpaduan kayu solid dan garis bersih ala Japandi. Cocok untuk ruang tamu yang ingin tampil hangat tanpa kesan ramai.',
                'image'     => 'https://img.ilenafurniture.com/image/1748318610868.jpg/?apikey=' . $this->apikey_img_ilena,
                'link'      => base_url('/product/coffee-table-ilena-cody'),
            ],
            [
                'nama'      => 'Plint Base Series',
                'gaya'      => 'Modern · Elegan & Premium',
                'deskripsi' => 'Desain minimalis dengan finishing premium. Pilihan tepat untuk rumah modern yang mengutamakan estetika & ketahanan.',
                'image'     => 'https://img.ilenafurniture.com/image/1748320128043.jpg/?apikey=' . $this->apikey_img_ilena,
                'link'      => base_url('/product/bufet-tv-ilena-plint-base'),
            ],
            [
                'nama'      => 'Cabana Series',
                'gaya'      => 'Timeless · Simply & Tahan Lama',
                'deskripsi' => 'Bentuk simpel dengan storage maksimal. Series yang akan tetap relevan walau tren interior berganti.',
                'image'     => 'https://img.ilenafurniture.com/image/1742973475864.png/?apikey=' . $this->apikey_img_ilena,
                'link'      => base_url('/product/bufet-tv-ilena-cabana'),
            ],
        ];

        $data = [
            'title'             => 'Promo Furniture Sepaket — Gratis Ongkir',
            'navbar'            => $this->getNavbarData(),
            'apikey_img_ilena'  => $this->apikey_img_ilena,
            'metaTitle'         => 'Promo Furniture Sepaket Ilena — Gratis Ongkir',
            'metaDeskripsi'     => 'Desain interior rumah baru jadi mudah! Pilih furniture sepaket Ilena: estetik, premium, dan saat ini gratis ongkir ke seluruh Indonesia.',
            'metaKeyword'       => 'promo furniture, furniture sepaket, gratis ongkir, ilena furniture, jasa interior rumah baru',
            'metaImage'         => 'https://img.ilenafurniture.com/image/1748320128043.jpg/?apikey=' . $this->apikey_img_ilena,
            'series'            => $series,
            'msg_active'        => false,
        ];
        return view('pages/iklan', $data);
    }
    public function contact()
    {
        $data = [
            'title' => 'Kontak Kami',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
        ];
        return view('pages/contact', $data);
    }
    public function syarat()
    {
        $data = [
            'title' => 'Syarat & Ketentuan',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
        ];
        return view('pages/syarat', $data);
    }
    public function kebijakan()
    {
        $data = [
            'title' => 'Kebijakan Privasi',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
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


    public function notFound()
    {
        $data = [
            'title' => 'Halaman Tidak Ditemukan',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
        ];
        return view('layout/notFound', $data);
    }
}
