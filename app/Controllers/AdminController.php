<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\ArtikelModel;
use App\Models\GambarArtikelModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\KelurahanModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\PemesananGudangModel;
use App\Models\ProvinsiModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\AjukanPrintModel;
use App\Models\KartuStokModel;
use App\Models\GambarHeaderModel;
use App\Models\PemesananOfflineModel;
use App\Models\PemesananOfflineItemModel;
use App\Models\VoucherModel;
use App\Models\VoucherUsageModel;
use App\Models\ProjectInteriorModel;
use App\Models\ProjectInteriorPaymentModel;
use App\Models\SuratJalanModel;
use App\Models\SuratJalanItemModel;
use App\Models\ProjectInteriorItemModel;

// Import Traits for modular organization
use App\Controllers\Admin\Traits\ProductTrait;
use App\Controllers\Admin\Traits\ArticleTrait;
use App\Controllers\Admin\Traits\VoucherTrait;
use App\Controllers\Admin\Traits\MutasiTrait;
use App\Controllers\Admin\Traits\OrderOnlineTrait;
use App\Controllers\Admin\Traits\OrderOfflineTrait;
use App\Controllers\Admin\Traits\SuratJalanTrait;
use App\Controllers\Admin\Traits\HelperTrait;
use App\Controllers\Admin\Traits\ProjectInteriorTrait;

class AdminController extends BaseController
{
    // Use Traits for code organization
    use ProductTrait;
    use ArticleTrait;
    use VoucherTrait;
    use MutasiTrait;
    use OrderOnlineTrait;
    use OrderOfflineTrait;
    use SuratJalanTrait;
    use HelperTrait;
    use ProjectInteriorTrait;


    protected $barangModel;
    protected $gambarBarangModel;
    protected $gambarHeaderModel;
    protected $gambarBarang3000Model;
    protected $artikelModel;
    protected $gambarArtikelModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $ajukanPrintModel;
    protected $kartuStokModel;
    protected $session;
    protected $apikey_img_ilena;
    protected $pemesananOfflineModel;
    protected $pemesananOfflineItemModel;
    protected $provinsiModel;
    protected $kabupatenModel;
    protected $kecamatanModel;
    protected $kelurahanModel;
    protected $voucherModel;
    protected $voucherUsageModel;
    protected $projectInteriorModel;
    protected $projectInteriorPaymentModel;
    protected $suratJalanModel;
    protected $suratJalanItemModel;
    protected $projectInteriorItemModel;





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
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->ajukanPrintModel = new AjukanPrintModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->pemesananOfflineModel = new PemesananOfflineModel();
        $this->pemesananOfflineItemModel = new PemesananOfflineItemModel();
        $this->session = \Config\Services::session();
        $this->apikey_img_ilena = env('APIKEY_IMG_ILENA', 'DefaultValue');
        $this->provinsiModel = new ProvinsiModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->kecamatanModel = new KecamatanModel();
        $this->kelurahanModel = new KelurahanModel();
        $this->voucherModel = new VoucherModel();
        $this->voucherUsageModel = new VoucherUsageModel();
        $this->projectInteriorModel = new ProjectInteriorModel();
        $this->projectInteriorPaymentModel = new ProjectInteriorPaymentModel();
        $this->suratJalanModel = new SuratJalanModel();
        $this->suratJalanItemModel = new SuratJalanItemModel();
        $this->projectInteriorItemModel = new ProjectInteriorItemModel();
        
    }
    
    public function listProduct()
    {
        $product = $this->barangModel->getBarangAdmin();
        $koleksi = $this->koleksiModel->findAll();
        foreach ($product as $index_p => $p) {
            $product[$index_p]['varian'] = json_decode($p['varian'], true);
            $product[$index_p]['allstok'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
                // $product[$index_p]['allstok'] = $product[$index_p]['allstok'] . $v['stok'];
                if ($ind_v == 0) $product[$index_p]['allstok'] .= $v['nama'] . ' : ' . $v['stok'];
                else $product[$index_p]['allstok'] .= "<br>" . $v['nama'] . ' : ' . $v['stok'];
            }
        }
        $data = [
            'title' => 'Produk Kami',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produk' => $product,
            'koleksi' => $koleksi
        ];
        return view('admin/all', $data);
    }
    public function listProductTable()
    {
        $product = $this->barangModel->orderBy('nama', 'asc')->findAll();
        foreach ($product as $index_p => $p) {
            $deskripsiArr = json_decode($p['deskripsi'], true);
            $deskripsi = str_replace('</p>', '', str_replace('<br>', '', str_replace('<p>', '', $deskripsiArr['deskripsi'])));
            $dimensi = 'Dimensi : P(' . $deskripsiArr['dimensi']['asli']['panjang'] . 'mm) x L(' . $deskripsiArr['dimensi']['asli']['lebar'] . 'mm) x T(' . $deskripsiArr['dimensi']['asli']['tinggi'] . 'mm) dengan berat ' . $deskripsiArr['dimensi']['asli']['berat'] . 'kg';
            $product[$index_p]['deskripsi_nonhtml'] = $deskripsi . ' ' . $dimensi;
            $product[$index_p]['gambar'] = 'https://ilenafurniture.com/viewpichover/' . $p['id'];
            $product[$index_p]['varian'] = json_decode($p['varian'], true);
            $product[$index_p]['stok_total'] = 0;
            $product[$index_p]['warna'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
                $product[$index_p]['stok_total'] += $v['stok'];
                $product[$index_p]['warna'] .= ($ind_v == 0 ? '' : '/') . ucwords(strtolower($v['nama']));
            }
        }
        $data = [
            'title' => 'Produk Kami ',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produk' => $product,
        ];
        return view('admin/allTable', $data);
    }
    // === FORM TAMBAH PRODUK ===
    public function addProduct()
    {
        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = [
            'title' => 'Tambah Produk',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'koleksi' => $koleksi,
            'koleksiJson' => json_encode($koleksi),
            'jenis' => $jenis,
            'jenisJson' => json_encode($jenis),
            'produk' => false,
            'val' => [
                'msg' => session()->getFlashdata('val-id'),
            ]
        ];
        return view('admin/add', $data);
    }

    // === ACTION TAMBAH PRODUK ===
    public function actionAddProduct()
    {
        if (!$this->validate([
            'id' => [
                'rules'  => 'required|is_unique[barang.id]',
                'errors' => [
                    'required'  => 'Id harus diisi',
                    'is_unique' => 'Id sudah terdaftar',
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-id', $validation->getError('id'));
            return redirect()->to('/admin/addproduct')->withInput();
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis   = $this->jenisModel->getJenis();

        $data               = $this->request->getVar();
        $data_gambar_mentah = $this->request->getFiles();

        if (isset($data_gambar_mentah['gambar_hover']) && $data_gambar_mentah['gambar_hover']->isValid()) {
            $fp = 'imgdum/barang/hover';
            $data_gambar_mentah['gambar_hover']->move($fp, $data['id'] . '.webp');
            if (file_exists('img/barang/hover/' . $data['id'] . '.webp')) {
                unlink('img/barang/hover/' . $data['id'] . '.webp');
            }
            \Config\Services::image()
                ->withFile($fp . '/' . $data['id'] . '.webp')
                ->resize(300, 300, true, 'height')
                ->save('img/barang/hover/' . $data['id'] . '.webp');
            @unlink($fp . '/' . $data['id'] . '.webp');
        }
        unset($data_gambar_mentah['gambar_hover']);

        foreach (json_decode($data['varian'], true) as $varian) {
            $tanggalNoStrip = date("YmdHis", strtotime("+7 Hours"));
            $this->kartuStokModel->insert([
                'id_barang'   => $data['id'],
                'tanggal'     => date("Y-m-d H:i:s", strtotime("+7 Hours")),
                'keterangan'  => $tanggalNoStrip . "-" . $data['id'] . "-" . strtoupper($varian['nama']) . "-ADDPRODUCT",
                'debit'       => $varian['stok'],
                'kredit'      => 0,
                'saldo'       => $varian['stok'],
                'pending'     => false,
                'id_pesanan'  => 'ADDPRODUCT',
                'varian'      => strtoupper($varian['nama'])
            ]);
        }

        $koleksiSelected = '';
        if (!empty($data['kategori'])) {
            $row = array_values(array_filter($koleksi, fn($v) => $v['id'] == $data['kategori']))[0] ?? null;
            $koleksiSelected = $row ? $row['nama'] : '';
        }

        $jenisSelected = '';
        if (!empty($data['subkategori'])) {
            $row = array_values(array_filter($jenis, fn($v) => $v['id'] == $data['subkategori']))[0] ?? null;
            $jenisSelected = $row ? $row['nama'] : '';
        }

        foreach ($data_gambar_mentah as $ind_g => $dG) {
            $urutan = (int)explode('_', $ind_g)[1];
            $dG->move('imgdum');

            if (file_exists('img/barang/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp')) {
                unlink('img/barang/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');
            }
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(3000, 3000, true, 'height')
                ->save('img/barang/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');

            if (file_exists('img/barang/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp')) {
                unlink('img/barang/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');
            }
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(1000, 1000, true, 'height')
                ->save('img/barang/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');

            if ($urutan <= 0) {
                if (file_exists('img/barang/300/' . $data['id'] . '.webp')) {
                    unlink('img/barang/300/' . $data['id'] . '.webp');
                }
                \Config\Services::image()
                    ->withFile('imgdum/' . $dG->getName())
                    ->resize(300, 300, true, 'height')
                    ->save('img/barang/300/' . $data['id'] . '.webp');
            }
            @unlink('imgdum/' . $dG->getName());
        }

        // ===== Jadwal diskon (baru, minimal patch) =====
        $pakaiSchedule = !empty($this->request->getVar('pakai_jadwal_diskon')) ? 1 : 0;
        $mulaiRaw      = (string) $this->request->getVar('diskon_mulai');    // ex: 2025-10-06T13:00
        $selesaiRaw    = (string) $this->request->getVar('diskon_selesai');  // ex: 2025-10-06T23:00

        $insertDataBarang = [
            'id'            => $data['id'],
            'nama'          => $data['nama'],
            'harga'         => $data['harga'],
            'pencarian'     => '',
            'rate'          => '0',
            'deskripsi'     => $data['deskripsi'],
            'kategori'      => $koleksiSelected,
            'subkategori'   => $jenisSelected,
            'diskon'        => $data['diskon'],
            'varian'        => $data['varian'],
            'shopee'        => $data['shopee'],
            'tokped'        => $data['tokped'],
            'tiktok'        => $data['tiktok'],
            'active'        => '1',
            'ruang_tamu'    => $data['ruang_tamu'],
            'ruang_keluarga'=> $data['ruang_keluarga'],
            'ruang_tidur'   => $data['ruang_tidur'],

            // simpan jadwal
            'pakai_jadwal_diskon' => $pakaiSchedule,
            'diskon_mulai'        => $pakaiSchedule && $mulaiRaw
                                        ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $mulaiRaw))) : null,
            'diskon_selesai'      => $pakaiSchedule && $selesaiRaw
                                        ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $selesaiRaw))) : null,
        ];

        $this->barangModel->insert($insertDataBarang);

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'dataYgDiInsertKeBarang' => $insertDataBarang,
                'pesan' => 'Berhasil menambahkan produk ' . $data['nama']
            ]);
    }


    // === FORM EDIT PRODUK ===
    public function editProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        $product['deskripsi'] = json_decode($product['deskripsi'] ?? '[]', true);
        $product['varian']    = json_decode($product['varian'] ?? '[]', true);

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis   = $this->jenisModel->getJenis();

        unset($product['gambar'], $product['tgl_update'], $product['active']);

        $product['ruang_tamu']     = ($product['ruang_tamu'] ?? '0') == '1';
        $product['ruang_keluarga'] = ($product['ruang_keluarga'] ?? '0') == '1';
        $product['ruang_tidur']    = ($product['ruang_tidur'] ?? '0') == '1';

        $katRow = $this->koleksiModel->where('nama', $product['kategori'] ?? '')->first();
        $jenRow = $this->jenisModel->where('nama', $product['subkategori'] ?? '')->first();
        $product['kategori']    = $katRow['id'] ?? '';
        $product['subkategori'] = $jenRow['id'] ?? '';

        // field jadwal (pastikan ada kolomnya di DB)
        $product['pakai_jadwal_diskon'] = (int)($product['pakai_jadwal_diskon'] ?? 0);
        $product['diskon_mulai']        = $product['diskon_mulai']   ?? null; // 'YYYY-MM-DD HH:MM:SS'
        $product['diskon_selesai']      = $product['diskon_selesai'] ?? null;

        $data = [
            'title'            => 'Tambah Produk',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'jenisJson'        => json_encode($jenis),
            'koleksiJson'      => json_encode($koleksi),
            'koleksi'          => $koleksi,
            'jenis'            => $jenis,
            'produk'           => $product,
            'produkJson'       => json_encode($product),
            'idProduct'        => $id_product,
            'val'              => ['msg' => session()->getFlashdata('val-id')],
        ];

        return view('admin/add', $data);
    }


    // === ACTION EDIT PRODUK ===
    public function actionEditProduct($id_product)
    {
        try {
            $barang = $this->barangModel->getBarangAdmin($id_product);
            if (!$barang) {
                return $this->response->setStatusCode(404)->setJSON(['pesan' => 'Produk tidak ditemukan']);
            }

            $data  = $this->request->getVar();
            $files = $this->request->getFiles();

            $ensureDir = function (string $dir) {
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                if (!is_writable($dir)) { @chmod($dir, 0775); }
            };
            $ensureDir('imgdum');
            $ensureDir('imgdum/barang/hover');
            $ensureDir('img/barang/300');
            $ensureDir('img/barang/1000');
            $ensureDir('img/barang/3000');
            $ensureDir('img/barang/hover');

            if (isset($files['gambar_hover']) && $files['gambar_hover'] && $files['gambar_hover']->isValid()) {
                try {
                    $tmpPath = 'imgdum/barang/hover';
                    $files['gambar_hover']->move($tmpPath, $id_product . '.webp');

                    @unlink('img/barang/hover/' . $id_product . '.webp');
                    \Config\Services::image()
                        ->withFile($tmpPath . '/' . $id_product . '.webp')
                        ->resize(300, 300, true, 'height')
                        ->save('img/barang/hover/' . $id_product . '.webp');

                    @unlink($tmpPath . '/' . $id_product . '.webp');
                } catch (\Throwable $e) {
                    log_message('error', 'EDIT hover gagal: {msg}', ['msg' => $e->getMessage()]);
                }
            }

            if (!empty($files)) {
                foreach ($files as $field => $file) {
                    if ($field === 'gambar_hover') continue;
                    if (strpos($field, 'gambar_') !== 0) continue;
                    if (!$file || !$file->isValid()) continue;

                    try {
                        $parts  = explode('_', $field);
                        $urutan = isset($parts[1]) ? (int)$parts[1] : 0;

                        $file->move('imgdum');

                        @unlink("img/barang/3000/{$id_product}-" . ($urutan + 1) . ".webp");
                        \Config\Services::image()
                            ->withFile('imgdum/' . $file->getName())
                            ->resize(3000, 3000, true, 'height')
                            ->save("img/barang/3000/{$id_product}-" . ($urutan + 1) . ".webp");

                        @unlink("img/barang/1000/{$id_product}-" . ($urutan + 1) . ".webp");
                        \Config\Services::image()
                            ->withFile('imgdum/' . $file->getName())
                            ->resize(1000, 1000, true, 'height')
                            ->save("img/barang/1000/{$id_product}-" . ($urutan + 1) . ".webp");

                        if ($urutan <= 0) {
                            @unlink("img/barang/300/{$id_product}.webp");
                            \Config\Services::image()
                                ->withFile('imgdum/' . $file->getName())
                                ->resize(300, 300, true, 'height')
                                ->save("img/barang/300/{$id_product}.webp");
                        }

                        @unlink('imgdum/' . $file->getName());
                    } catch (\Throwable $e) {
                        log_message('error', 'EDIT varian gambar gagal ({field}): {msg}', [
                            'field' => $field,
                            'msg'   => $e->getMessage()
                        ]);
                    }
                }
            }

            $kategoriNama    = $barang['kategori'];
            $subkategoriNama = $barang['subkategori'];

            if (!empty($data['kategori'])) {
                $koleksiRow = $this->koleksiModel->where('id', $data['kategori'])->first();
                if ($koleksiRow) $kategoriNama = $koleksiRow['nama'];
            }
            if (!empty($data['subkategori'])) {
                $jenisRow = $this->jenisModel->where('id', $data['subkategori'])->first();
                if ($jenisRow) $subkategoriNama = $jenisRow['nama'];
            }

            $deskripsi = $data['deskripsi'] ?? $barang['deskripsi'];
            if (is_array($deskripsi)) $deskripsi = json_encode($deskripsi);
            $varian = $data['varian'] ?? $barang['varian'];
            if (is_array($varian)) $varian = json_encode($varian);

            $boolTo01 = fn($v) => (is_bool($v) ? ($v ? '1' : '0') : (string)$v);

            $payload = [
                'nama'           => $data['nama']        ?? $barang['nama'],
                'harga'          => $data['harga']       ?? $barang['harga'],
                'pencarian'      => $barang['pencarian'] ?? '',
                'deskripsi'      => $deskripsi,
                'kategori'       => $kategoriNama,
                'subkategori'    => $subkategoriNama,
                'diskon'         => $data['diskon']      ?? $barang['diskon'],
                'varian'         => $varian,
                'shopee'         => $data['shopee']      ?? ($barang['shopee'] ?? ''),
                'tokped'         => $data['tokped']      ?? ($barang['tokped'] ?? ''),
                'tiktok'         => $data['tiktok']      ?? ($barang['tiktok'] ?? ''),
                'ruang_tamu'     => $boolTo01($data['ruang_tamu']     ?? ($barang['ruang_tamu'] ?? '0')),
                'ruang_keluarga' => $boolTo01($data['ruang_keluarga'] ?? ($barang['ruang_keluarga'] ?? '0')),
                'ruang_tidur'    => $boolTo01($data['ruang_tidur']    ?? ($barang['ruang_tidur'] ?? '0')),
            ];

            // ===== PATCH PENTING: simpan jadwal diskon =====
            if ($this->request->getVar('pakai_jadwal_diskon') !== null) {
                $pakaiSchedule = !empty($this->request->getVar('pakai_jadwal_diskon')) ? 1 : 0;
                $mulaiRaw      = (string) $this->request->getVar('diskon_mulai');    // 2025-10-06T13:00
                $selesaiRaw    = (string) $this->request->getVar('diskon_selesai');  // 2025-10-06T23:00

                $payload['pakai_jadwal_diskon'] = $pakaiSchedule;
                $payload['diskon_mulai']   = $pakaiSchedule && $mulaiRaw
                    ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $mulaiRaw))) : null;
                $payload['diskon_selesai'] = $pakaiSchedule && $selesaiRaw
                    ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $selesaiRaw))) : null;
            }
            // ===== END PATCH =====

            $this->barangModel->update($id_product, $payload);

            return $this->response->setStatusCode(200)->setJSON([
                'pesan'   => 'Berhasil mengubah produk.',
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'EDIT PRODUCT ERROR: {msg} at {file}:{line}', [
                'msg'  => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'pesan' => 'Gagal mengubah produk.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function actionEditProductOld($pathname = false)
    {
        $idBarang = $this->request->getVar('id');
        if (!$idBarang) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'pesan' => 'ID barang tidak dikirim']);
        }

        $barangCur = $this->barangModel->getBarangAdmin($idBarang);
        if (!$barangCur) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'pesan' => 'Barang tidak ditemukan']);
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = $this->request->getVar();
        $files = $this->request->getFiles();

        $data['deskripsi'] = json_decode($data['deskripsi'] ?? '', true) ?? [];
        $data['varian'] = json_decode($data['varian'] ?? '', true) ?? [];

        if (!is_array($data['deskripsi']) || !is_array($data['varian'])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'pesan' => 'Format JSON tidak valid']);
        }

        if (isset($files['gambar_hover']) && $files['gambar_hover']->isValid()) {
            $tmpPath = 'imgdum/barang/hover';
            $files['gambar_hover']->move($tmpPath, $idBarang . '.webp');
            $finalPath = 'img/barang/hover/' . $idBarang . '.webp';
            if (file_exists($finalPath)) unlink($finalPath);
            \Config\Services::image()
                ->withFile($tmpPath . '/' . $idBarang . '.webp')
                ->resize(300, 300, true, 'height')
                ->save($finalPath);
            unlink($tmpPath . '/' . $idBarang . '.webp');
        }

        $gambarPaths = [];
        foreach ($files as $key => $file) {
            if (strpos($key, 'gambar_') === 0 && $file->isValid()) {
                $urutan = (int) explode('_', $key)[1];
                $file->move('imgdum');
                $filename = $file->getName();

                foreach ([3000, 1000, 300] as $size) {
                    $savePath = "img/barang/{$size}/{$idBarang}-" . ($urutan + 1) . '.webp';
                    if (file_exists($savePath)) unlink($savePath);
                    \Config\Services::image()
                        ->withFile('imgdum/' . $filename)
                        ->resize($size, $size, true, 'height')
                        ->save($savePath);
                }

                unlink('imgdum/' . $filename);
                $gambarPaths[$key] = "{$idBarang}-" . ($urutan + 1) . '.webp';
            }
        }

        foreach ($gambarPaths as $key => $filename) {
            $this->gambarBarangModel->where(['id' => $idBarang])->set([$key => $filename])->update();
            $this->gambarBarang3000Model->where(['id' => $idBarang])->set([$key => $filename])->update();
        }

        $koleksiSelected = array_values(array_filter($koleksi, fn($k) => $k['id'] == $data['kategori']))[0]['nama'] ?? '';
        $jenisSelected = array_values(array_filter($jenis, fn($j) => $j['id'] == $data['subkategori']))[0]['nama'] ?? '';

        $dataUpdate = [
            'nama' => $data['nama'] ?? null,
            'harga' => $data['harga'] ?? null,
            'deskripsi' => json_encode($data['deskripsi']),
            'kategori' => $koleksiSelected,
            'subkategori' => $jenisSelected,
            'diskon' => $data['diskon'] ?? '0',
            'varian' => json_encode($data['varian']),
            'shopee' => $data['shopee'] ?? '',
            'tokped' => $data['tokped'] ?? '',
            'tiktok' => $data['tiktok'] ?? '',
            'gambar' => $gambarPaths['gambar_0'] ?? $barangCur['gambar'],
            'ruang_tamu' => $data['ruang_tamu'] ?? '0',
            'ruang_keluarga' => $data['ruang_keluarga'] ?? '0',
            'ruang_tidur' => $data['ruang_tidur'] ?? '0',
        ];

        $dataUpdate = array_filter($dataUpdate, fn($val) => !is_null($val));

        if (empty($dataUpdate)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'pesan' => 'Tidak ada data untuk diperbarui']);
        }


        $this->barangModel->where(['id' => $idBarang])->set($dataUpdate)->update();

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON(['status' => 'ok', 'pesan' => 'Produk berhasil diupdate']);
        }

        return redirect()->to($pathname ? str_replace('@', '/', $pathname) : 'admin/product');
    }



    public function mutasi($data = false)
    {
        $product = $this->barangModel->getBarang();
        if (!$data) {
            $id_barang = $product[0]['id'];
            $varian = json_decode($product[0]['varian'], true)[0]['nama'];
        } else {
            $id_barang = explode('-', $data)[0];
            $varian = explode('-', $data)[1];
        }
        foreach ($product as $ind_p => $p) {
            $product[$ind_p]['varian'] = json_decode($p['varian'], true);
            $product[$ind_p]['deskripsi'] = json_decode($p['deskripsi'], true);
        }
        $mutasi = $this->kartuStokModel->where(['id_barang' => $id_barang, 'varian' => $varian])->findAll();
        // $pemesananBelumPrint = $this->pemesananModel->where('status_print !=', 'sudah print')->findAll();

        // $mutasiDanPemesanan = $mutasi;
        // foreach ($pemesananBelumPrint as $p) {
        //     foreach (json_decode($pemesananBelumPrint['items'], true) as $i) {
        //         if($i['id'] == $id_barang) {
        //             $itemMP = [
        //                 'id_barang' => $id_barang,
        //                 'tanggal' => json_decode($pemesananBelumPrint['data_mid'], true)['transaction_time'],
        //                 'keterangan' => 'Masih menunggu surat jalan di print',
        //                 'debit' => 0,
        //                 'kredit' => $i['quantity'],
        //                 'saldo' => 'Menyesuaikan'
        //             ];
        //             array_push($mutasiDanPemesanan, )
        //         }
        //     }
        // }
        $msg = session()->getFlashdata('msg');
        $data = [
            'title' => 'Mutasi',
            'mutasi' => $mutasi,
            'product' => $product,
            'idBarang' => $id_barang,
            'data' => $data,
            'msg' => $msg ? $msg : false
        ];
        return view('admin/mutasi', $data);
    }
    public function actionAddMutasi()
    {
        $tanggal = $this->request->getVar('tanggal');
        $barang = explode("-", $this->request->getVar('barang'));
        $jenis = $this->request->getVar('jenis');
        $alasan = $this->request->getVar('alasan');
        $nominal = (int)$this->request->getVar('nominal');

        //cek apakah ada pesanan yg masuh menunggu pembayaran
        $pesanan = $this->pemesananModel->where(['status' => 'Menunggu Pembayaran'])->findAll();
        if (count($pesanan) > 0) {
            session()->setFlashdata('msg', 'Ada pesanan dengan status Menunggu Pembayaran sehingga belum bisa melakukan perubahan data stok');
            return redirect()->to('/admin/mutasi/' . $barang[0] . '-' . $barang[1]);
        }

        $d = strtotime($tanggal);
        $keterangan = date("Ymd", $d) . date("His") . "-" . $barang[0] . "-" . strtoupper($barang[1]) . "-MANUALLY";

        $produk = $this->barangModel->getBarang($barang[0]);
        $saldoSkrg = (int)json_decode($produk['varian'], true)[(int)$barang[2]]['stok'];
        $debit = 0;
        $kredit = 0;
        if ($jenis == 'debit') {
            $debit = $nominal;
        } else if ($jenis == 'kredit') {
            $kredit = $nominal;
        }
        $nominal *= ($jenis == 'debit' ? 1 : -1);
        $saldo = $saldoSkrg + $nominal;
        if ($saldo < 0) {
            session()->setFlashdata('msg', 'Saldo tinggal ' . $saldoSkrg . ' jadi tidak cukup');
            return redirect()->to('/admin/mutasi/' . $barang[0] . '-' . $barang[1]);
        }
        $varianBaru = json_decode($produk['varian'], true);
        $varianBaru[(int)$barang[2]]['stok'] = (string)$saldo;
        $this->barangModel->where('id', $barang[0])->set([
            'varian' => json_encode($varianBaru)
        ])->update();
        $this->kartuStokModel->insert([
            'id_barang' => $barang[0],
            'tanggal' => $tanggal,
            'keterangan' => $keterangan,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldo,
            'pending' => false,
            'id_pesanan' => 'MANUALLY',
            'alasan' => $alasan,
            'varian' => $varianBaru[(int)$barang[2]]['nama'],
        ]);
        return redirect()->to('/admin/mutasi/' . $barang[0] . '-' . $barang[1]);
    }

    public function mutasiConfirm()
    {
        $mutasi = $this->kartuStokModel->where('alasan !=', '')->findAll();
        foreach ($mutasi as $ind_m => $m) {
            $produk  = $this->barangModel->where(['id' => $m['id_barang']])->first();
            $mutasi[$ind_m]['detail'] = $produk;
            if ($produk) {
                foreach (json_decode($produk['varian'], true) as $v) {
                    if (strtoupper($v['nama']) == strtoupper($m['varian'])) {
                        $mutasi[$ind_m]['stok'] = $v['stok'];
                    }
                }
            } else {
                $mutasi[$ind_m]['stok'] = 0;
            }
        }
        $data = [
            'title' => 'Konfirm Mutasi',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'mutasi' => $mutasi,
            'msg' => session()->getFlashdata('msg')
        ];
        return view('admin/mutasiConfirm', $data);
    }
    public function accMutasi($id)
    {
        $getMutasi  = $this->kartuStokModel->where(['id' => $id])->first();
        $produk = $this->barangModel->getBarang($getMutasi['id_barang']);
        $varianCurr = json_decode($produk['varian'], true);
        $saldoFix = 0;
        foreach ($varianCurr as $ind_v => $v) {
            if (strtoupper($v['nama']) == strtoupper($getMutasi['varian'])) {
                $saldoSkrg = $v['stok'];
                $saldoFix = $saldoSkrg + ($getMutasi['debit'] ? $getMutasi['debit'] : -$getMutasi['kredit']);
                $varianCurr[$ind_v]['stok'] = $saldoFix;
            }
        }
        if ($saldoFix < 0) {
            session()->setFlashdata('msg', 'Saldo melebihi batas minimal');
            return redirect()->to('/admin/mutasiconfirm');
        }
        $this->kartuStokModel->where(['id' => $id])->set([
            'saldo' => $saldoFix,
            'alasan' => '',
            'pending' => false
        ])->update();
        $this->barangModel->where(['id' => $getMutasi['id_barang']])->set(['varian' => json_encode($varianCurr)])->update();
        return redirect()->to('admin/mutasiconfirm');
    }
    public function denyMutasi($id)
    {
        $this->kartuStokModel->where(['id' => $id])->delete();
        return redirect()->to('admin/mutasiconfirm');
    }

    public function gantiUkuran($koleksi, $jenis)
    {
        // $barangLama = $this->barangModel->findAll(10, 0);
        $barangLama = $this->barangModel->where(['kategori' => $koleksi, 'subkategori' => $jenis])->findAll();
        if (count($barangLama) <= 0) {
            return $this->response->setJSON(['message' => 'barang nggk nemu'], false);
        }
        function file_get_contents_curl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        foreach ($barangLama as $b) {
            $varian = json_decode($b['varian'], true);
            $insertGambarBarang = [];
            $insertGambarBarang3000 = [];
            $insertGambarBarang300 = '';
            foreach ($varian as $v) {
                $urutanGambar = explode(",", $v['urutan_gambar']);
                foreach ($urutanGambar as $u) {
                    $dataGambar = file_get_contents_curl(
                        'https://ilenafurniture.com/viewvar3000/' . $b['id'] . '/' . $u
                    );
                    $fp = 'imgdum/' . $b['id'] . '-' . $u . '.webp';
                    file_put_contents($fp, $dataGambar);

                    \Config\Services::image()
                        ->withFile($fp)
                        ->resize(3000, 3000, true, 'height')->save('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    $insertGambarBarang3000['gambar' . $u] = file_get_contents('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    unlink('imgdum/1' . $b['id'] . '-' . $u . '.webp');

                    \Config\Services::image()
                        ->withFile($fp)
                        ->resize(1000, 1000, true, 'height')->save('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    $insertGambarBarang['gambar' . $u] = file_get_contents('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    unlink('imgdum/1' . $b['id'] . '-' . $u . '.webp');

                    unlink($fp);
                }
            }

            $dataGambar = file_get_contents_curl(
                'https://ilenafurniture.com/viewpic/' . $b['id']
            );
            $fp = 'imgdum/' . $b['id'] . '.webp';
            file_put_contents($fp, $dataGambar);

            \Config\Services::image()
                ->withFile($fp)
                ->resize(300, 300, true, 'height')->save('imgdum/1' . $b['id']  . '.webp');
            $insertGambarBarang300 = file_get_contents('imgdum/1' . $b['id']  . '.webp');

            unlink($fp);
            unlink('imgdum/1' . $b['id']  . '.webp');

            $this->barangModel->where(['id' => $b['id']])->set(['gambar' => $insertGambarBarang300])->update();
            $this->gambarBarangModel->where(['id' => $b['id']])->set($insertGambarBarang)->update();
            $this->gambarBarang3000Model->where(['id' => $b['id']])->set($insertGambarBarang3000)->update();
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
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
        $gambar3000 = $this->gambarBarang3000Model->where('id', $id_product)->delete();
        return redirect()->to('admin/product');
    }
    public function order()
    {
        $pesanan = $this->pemesananModel->getPemesanan();
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field1']))
                $pesanan[$ind_p]['data_mid']['custom_field1'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field1']);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field2']))
                $pesanan[$ind_p]['data_mid']['custom_field2'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field2']);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field3']))
                $pesanan[$ind_p]['data_mid']['custom_field3'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field3']);
            $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
        }
        $data = [
            'title' => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan)
        ];
        return view('admin/order', $data);
    }
    public function orderAdd()
    {
        $produk = $this->barangModel->findAll();
        // dd($produk);
        foreach ($produk as $ind_p => $p) {
            $produk[$ind_p]['gambar'] = base_url('img/barang/300/' . $p['id'] . '.webp?v=' . strtotime($p['tgl_update']));
            $produk[$ind_p]['gambar_hover'] = '';
            $produk[$ind_p]['dimensi'] =  json_decode($p['deskripsi'], true)['dimensi']['asli'];
            $produk[$ind_p]['deskripsi'] =  '';
            $produk[$ind_p]['varian'] =  json_decode($p['varian'], true);
            $produk[$ind_p]['shopee'] = '';
            $produk[$ind_p]['tokped'] = '';
            $produk[$ind_p]['tiktok'] = '';
            $produk[$ind_p]['nama'] = ucwords($p['nama']);
        }

        //Dapatkan data provinsi
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
        //     CURLOPT_SSL_VERIFYHOST => 0,
        //     CURLOPT_SSL_VERIFYPEER => 0,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        //     CURLOPT_HTTPHEADER => array(
        //         "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
        //     ),
        // ));
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);
        // if ($err) {
        //     return "cURL Error #:" . $err;
        // }
        $provinsi = $this->provinsiModel->findAll();
        // dd($provinsi);

        $data = [
            'title' => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produkJson' => json_encode($produk),
            'provinsi' => $provinsi
        ];
        return view('admin/orderAdd', $data);
    }
    public function actionOrderAdd()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $keranjang = $body['keranjang'];
        $items = [];
        
        $hargaTotal = $body['hargaTotal'];
        $waktu = $body['waktu'] ? str_replace("T", " ", $body['waktu']) : date("Y-m-d H:i:s", strtotime(('+7 Hours')));
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "IL" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1)) . '';
        $randomId = "IL" . rand();
        foreach ($keranjang as $k) {
            array_push($items, [
                'id' => $k['id'],
                'name' => $k['name'],
                'quantity' => $k['quantity'],
                'price' => $k['price'],
            ]);

            //kartu stok ditambahkan
            $varian = $k['detail']['varian'];
            $saldo = (int)$varian['stok'];
            $tanggalNoStrip = date("YmdHis", strtotime("+7 hours"));
            $this->kartuStokModel->insert([
                'id_barang' => $k['id'],
                'tanggal' => $waktu,
                'keterangan' => $tanggalNoStrip . "-" . $k['id'] . "-" . strtoupper($varian['nama']) . "-" . $idFix,
                'debit' => 0,
                'kredit' => $k['quantity'],
                'saldo' => $saldo,
                'pending' => true,
                'id_pesanan' => $idFix,
                'varian' => strtoupper($varian['nama'])
            ]);
        }
        $data = [
            'data_mid'          => json_encode([
                'transaction_time' => $waktu,
                'order_id'         => $idFix,
                'gross_amount'     => $hargaTotal,
                'payment_type'     => 'admin'
            ]),
            'id_midtrans'       => $idFix,
            'email'             => $body['email'],
            'nohp'              => $body['nohp'],
            'alamat'            => $body['alamatLengkap'],
            'nama'              => $body['nama'],
            'kurir'             => json_encode([
                'nama' => 'Menyesuaikan',
                'deskripsi' => 'Menyesuaikan',
                'harga' => 'Menyesuaikan',
                'estimasi' => 'Menyesuaikan'
            ]),
            'resi'              => 'Menunggu pengiriman',
            'status'            => isset($body['stokTetap']) ? ($body['stokTetap'] ? 'Menunggu Pembayaran' : 'Proses') : 'Proses',
            'id_marketplace'    => $body['idMarketplace'],
            'items'             => json_encode($items),
            'status_print'      => 'siap',
            'keterangan_suratjalan' => $body['keteranganSJ']
        ];
        $this->pemesananModel->insert($data);
        return $this->response->setStatusCode(200)->setJSON(['success' => true], false);
    }
    public function actionEditResi()
    {
        $nama = $this->request->getVar('nama');
        $deskripsi = $this->request->getVar('deskripsi');
        $resi = $this->request->getVar('resi');
        $idMid = $this->request->getVar('idMid');
        $this->pemesananModel->where(['id_midtrans' => $idMid])->set([
            'kurir' => json_encode([
                'nama' => $nama,
                'deskripsi' => $deskripsi,
            ]),
            'resi' => $resi,
            'status' => 'Dikirim'
        ])->update();
        return redirect()->to('/admin/order/online');
    }

    public function reprint()
    {
        $ajukanPrint = $this->ajukanPrintModel->findAll();
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($ajukanPrint as $ind_a => $a) {
            $d = strtotime($a['tanggal']);
            $ajukanPrint[$ind_a]['tanggal'] = date("d", $d) . " " . $bulan[(int)date("m", $d) - 1] . " " . date("Y", $d);
        }
        $data = [
            'title' => 'Pengajuan Print Ulang',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'ajukan' => $ajukanPrint
        ];
        return view('admin/reprint', $data);
    }
    public function accReprint($id_midtrans)
    {
        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'siap'])->update();
        $this->ajukanPrintModel->where(['id_midtrans' => $id_midtrans])->delete();
        $this->kartuStokModel->where(['id_pesanan' => $id_midtrans])->set(['pending' => true])->update();
        return redirect()->to('/admin/reprint');
    }
    public function marketplace()
    {
        $pemesanan = $this->pemesananModel->getPemesananMarket();
        $pemesananBelumKonfirmasi = [];
        foreach ($pemesanan as $p) {
            $pemesananGudang = $this->pemesananGudangModel->where([
                'id_pesanan' => $p['id_midtrans'],
            ])->first();
            if (!$pemesananGudang) {
                $items = json_decode($p['items'], true);
                $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $data_mid = json_decode($p['data_mid'], true);
                $data_mid['transaction_time'] = date('d', strtotime($data_mid['transaction_time'])) . ' ' . $bulan[date('m', strtotime($data_mid['transaction_time'])) - 1] . ' ' . date('Y', strtotime($data_mid['transaction_time']));
                $kurir = json_decode($p['kurir'], true);

                $pemesanan_curr = $p;
                $pemesanan_curr['items'] = $items;
                $pemesanan_curr['data_mid'] = $data_mid;
                $pemesanan_curr['kurir'] = $kurir;

                array_push($pemesananBelumKonfirmasi, $pemesanan_curr);
            }
        }

        $data = [
            'title' => 'Konfirmasi Marketplace',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'val' => [
                'msg' => session()->getFlashdata('msg')
            ],
            'pemesanan' => $pemesananBelumKonfirmasi,
            'pemesananJson' => json_encode($pemesananBelumKonfirmasi),
        ];
        return view('admin/marketplace', $data);
    }

    public function confirmMarketplace($id)
    {
        $pemesanan = $this->pemesananModel->where([
            'id' => $id,
        ])->first();
        $items_curr = json_decode($pemesanan['items'], true);
        foreach ($items_curr as $i) {
            if ($i['name'] != "Voucher" && $i['name'] != "Biaya Admin") {
                for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                    $this->pemesananGudangModel->insert([
                        'id_pesanan' => $pemesanan['id_midtrans'],
                        'tanggal' => json_decode($pemesanan['data_mid'], true)['transaction_time'],
                        'nama' => $i['name'],
                        'id_barang' => $i['id'],
                        'packed' => false
                    ]);
                }
            }
        }
        return redirect()->to('/admin/marketplace');
    }

    public function orderToko($ind_add)
    {
        $email = session()->get('email');
        $nama = session()->get('nama');
        $nohp = session()->get('nohp');
        $keranjang = session()->get('keranjang');
        $alamat = session()->get('alamat')[$ind_add];

        $hargaTotal = 0;
        $items = [];
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                }
            }
            $keranjang[$index]['detail'] = $produk;
            $hargaTotal += $produk['harga'] * $k['jumlah'] * (100 - $produk['diskon']) / 100;

            $item = [
                'id' => $k['id_barang'],
                'price' => $produk['harga'],
                'quantity' => $k['jumlah'],
                'name' => $produk['nama'] . " " . json_decode($produk['deskripsi'], true)['dimensi']['asli']['panjang'] . ' (' . $k['varian'] . ')',
            ];
            array_push($items, $item);
        }

        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "TOKO" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "TOKO" . rand();
        $tanggal = date('Y-m-d H:i:s', strtotime('+7 hours'));
        $data = [
            'data_mid'          => json_encode([
                'transaction_time'  => $tanggal,
                'order_id'          => $idFix,
                'gross_amount'      => $hargaTotal,
                'payment_type'      => 'toko'
            ]),
            'id_midtrans'       => $idFix,
            'email'             => $email,
            'kurir'             => json_encode([]),
            'resi'              => 'Menunggu pengiriman',
            'harga'             => $hargaTotal,
            'nama'              => $nama,
            'nohp'              => $nohp,
            'alamat'            => $alamat['alamat_lengkap'],
            'status'            => 'Proses',
            'id_marketplace'    => '',
            'items'             => json_encode($items),
        ];
        $this->pemesananModel->insert($data);

        //insert pesanan gudang
        foreach ($items as $i) {
            if ($i['name'] != "Biaya Ongkir" && $i['name'] != "Biaya Admin") {
                for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                    $this->pemesananGudangModel->insert([
                        'id_pesanan' => $idFix,
                        'tanggal' => $tanggal,
                        'nama' => $i['name'],
                        'id_barang' => $i['id'],
                        'packed' => false
                    ]);
                }
            }
        }

        //pengurangan stok
        $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $idFix)->first();
        $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
        foreach ($dataTransaksiFulDariDatabase_items as $item) {
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

        return redirect()->to('/order');
    }

    public function labelBarang($id)
    {
        $pemesanan = $this->pemesananModel->where(['id_midtrans' => $id])->first();
        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $data = [
            'title' => 'Label Barang',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan
        ];
        return view('admin/labelBarang', $data);
    }

    // ARTIKEL
    public function article()
    {
        $artikel = $this->artikelModel->orderBy('waktu', 'desc')->findAll();
        $data = [
            'title' => 'Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => $artikel,
            'msg' => session()->getFlashdata('msg')
        ];
        return view('admin/artikel', $data);
    }
    public function articleCategory($kategori)
    {
        $artikel = $this->artikelModel->getArtikelKategori(str_replace("-", " ", $kategori));
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        if (!$artikel) return redirect()->to('article');
        foreach ($artikel as $ind_a => $a) {
            $artikel[$ind_a]['header'] = '/imgart/' . $a['id'];
            $artikel[$ind_a]['isi'] = json_decode($a['isi'], true);
            $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
            $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
        }
        $data = [
            'title' => 'Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => $artikel
        ];
        return view('pages/artikelAll', $data);
    }
    // END ARTIKEL

    // LANJUTAN ARTIKEL
    public function addArticle()
    {
        $galeri = $this->gambarArtikelModel->findAll();
        $tinymce_key = env('TINYMCE_KEY', 'DefaultValue');
        $data = [
            'title' => 'Tambah Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'galeri' => $galeri,
            'tinyMCE' => $tinymce_key
        ];
        return view('admin/addArtikel', $data);
    }
    public function actionAddGaleriArticle()
    {
        $gambar = $this->request->getFile('file');
        $id = date("YmdHis", strtotime("+7 hours"));
        if ($gambar->isValid()) {
            $fp = 'img/artikel';
            $gambar->move($fp, $id . '.webp');
        }
        $this->gambarArtikelModel->insert([
            'id' => $id,
            'url' => 'img/artikel/' . $id . '.webp'
        ]);
        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'url' => 'img/artikel/' . $id . '.webp'
        ], false);
    }
    public function actionAddArticle()
    {
        $judul = $this->request->getVar('judul');
        $path = str_replace(",", "", $judul);
        $path = str_replace(".", "", $path);
        $path = str_replace("& ", "", $path);
        $path = str_replace("?", "", $path);
        $path = str_replace("!", "", $path);
        $path = str_replace(":", "", $path);
        $path = str_replace(" ", "-", $path);
        $path = strtolower($path);
        $this->artikelModel->insert([
            'id' => "A" . date("YmdHis", strtotime("+7 Hours")),
            'judul' => $judul,
            'path' => $path,
            'penulis' => $this->request->getVar('penulis'),
            'keywords' => $this->request->getVar('keywords'),
            'kategori' => strtolower($this->request->getVar('kategori')),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'waktu' => $this->request->getVar('waktu'),
            'isi' => $this->request->getVar('isi'),
            'header' => $this->request->getVar('header'),
            'suka' => 0,
            'bagikan' => 0,
            'komen' => json_encode([]),
        ]);
        session()->setFlashdata('msg', 'Artikel berhasil ditambahkan');
        return redirect()->to('/admin/article');
    }

    public function deleteArticle($id)
    {
        $this->artikelModel->where(['id' => $id])->delete();
        session()->setFlashdata('msg', 'Artikel berhasil dihapus');
        return redirect()->to('/admin/article');
    }
    public function editArticle($id)
    {
        $artikel = $this->artikelModel->where(['id' => $id])->first();
        $galeri = $this->gambarArtikelModel->findAll();
        $tinymce_key = env('TINYMCE_KEY', 'DefaultValue');
        $data = [
            'title' => 'Edit Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => $artikel,
            'galeri' => $galeri,
            'tinyMCE' => $tinymce_key
        ];
        return view('admin/editArtikel', $data);
    }
    public function actionEditArticle($id)
    {
        $judul = $this->request->getVar('judul');
        $path = str_replace(",", "", $judul);
        $path = str_replace(".", "", $path);
        $path = str_replace("& ", "", $path);
        $path = str_replace("?", "", $path);
        $path = str_replace("!", "", $path);
        $path = str_replace(":", "", $path);
        $path = str_replace(" ", "-", $path);
        $path = strtolower($path);
        $this->artikelModel->where(['id' => $id])->set([
            'judul' => $judul,
            'path' => $path,
            'penulis' => $this->request->getVar('penulis'),
            'keywords' => $this->request->getVar('keywords'),
            'kategori' => strtolower($this->request->getVar('kategori')),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'waktu' => $this->request->getVar('waktu'),
            'isi' => $this->request->getVar('isi'),
            'header' => $this->request->getVar('header'),
        ])->update();
        session()->setFlashdata('msg', 'Artikel berhasil diedit');
        return redirect()->to('/admin/article');
    }

    public function homeLayout()
    {
        $gambarHeader = [
            'url1' => $this->gambarHeaderModel->where(['id' => 1])->first()['url'],
            'url2' => $this->gambarHeaderModel->where(['id' => 2])->first()['url'],
            'url3' => $this->gambarHeaderModel->where(['id' => 3])->first()['url'],
            'url4' => $this->gambarHeaderModel->where(['id' => 4])->first()['url'],
        ];
        $data = [
            'title' => 'Home Layout',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'msg' => session()->getFlashdata('msg'),
            'gambarHeader' => $gambarHeader
        ];
        return view('admin/homeLayout', $data);
    }

    public function actionHomeLayout()
    {
        $image1 = $this->request->getFile('image1');
        $image1hp = $this->request->getFile('image1-hp');
        $url1 = $this->request->getVar('url1');

        $image2 = $this->request->getFile('image2');
        $image2hp = $this->request->getFile('image2-hp');
        $url2 = $this->request->getVar('url2');

        $image3 = $this->request->getFile('image3');
        $image3hp = $this->request->getFile('image3-hp');
        $url3 = $this->request->getVar('url3');

        $image4 = $this->request->getFile('image4');
        $image4hp = $this->request->getFile('image4-hp');
        $url4 = $this->request->getVar('url4');

        $dataUpdate = [];
        if ($image1->isValid()) $dataUpdate['foto'] = file_get_contents($image1);
        if ($image1hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image1hp);
        $dataUpdate['url'] = $url1 ? $url1 : null;
        $this->gambarHeaderModel->where(['id' => 1])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($image2->isValid()) $dataUpdate['foto'] = file_get_contents($image2);
        if ($image2hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image2hp);
        $dataUpdate['url'] = $url2 ? $url2 : null;
        $this->gambarHeaderModel->where(['id' => 2])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($image3->isValid()) $dataUpdate['foto'] = file_get_contents($image3);
        if ($image3hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image3hp);
        $dataUpdate['url'] = $url3 ? $url3 : null;
        $this->gambarHeaderModel->where(['id' => 3])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($image4->isValid()) $dataUpdate['foto'] = file_get_contents($image4);
        if ($image4hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image4hp);
        $dataUpdate['url'] = $url4 ? $url4 : null;
        $this->gambarHeaderModel->where(['id' => 4])->set($dataUpdate)->update();

        session()->setFlashdata('msg', 'Home layout telah diperbarui');
        return redirect()->to('/admin/homelayout');
    }

    public function suratJalan($id_midtrans)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        $d = strtotime("+7 hours");
        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'sudah print'])->update();
        $items = [];
        if ($pemesanan['status_print'] == 'siap') {
            foreach (json_decode($pemesanan['items'], true) as $p) {
                if ($p['name'] != 'Biaya Ongkir' && $p['name'] != 'Biaya Admin' && $p['name'] != 'Voucher' && strtolower($p['name']) != 'flash sale' && !str_contains(strtolower($p['name']), 'potongan')) {
                    $produknya = $this->barangModel->getBarang($p['id']);
                    $varian = json_decode($produknya['varian'], true);
                    $saldo = 0;
                    foreach ($varian as $ind_v => $v) {
                        if (strtolower($v['nama']) == strtolower(rtrim(explode("(", $p['name'])[1], ")"))) {
                            $saldo = (int)$v['stok'];
                        }
                    }
                    // $this->kartuStokModel->insert([
                    //     'id_barang' => $p['id'],
                    //     'tanggal' => $tanggal,
                    //     'keterangan' => $tanggalNoStrip . "-" . $p['id'] . "-" . strtoupper(rtrim(explode("(", $p['name'])[1], ")")) . "-" . $pemesanan['id_midtrans'],
                    //     'debit' => 0,
                    //     'kredit' => $p['quantity'],
                    //     'saldo' => (int)$saldo - (int)$p['quantity'],
                    // ]);
                    $this->kartuStokModel
                    ->where([
                        'id_barang' => $p['id'],
                        'id_pesanan'=> $pemesanan['id_midtrans'],   // tambah ini
                    ])
                    ->set([
                        'pending' => false,
                        'saldo'   => (int)$saldo - (int)$p['quantity'],
                    ])->update();
                    array_push($items, $p);
                }
            }
        }

        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $pemesanan['kurir'] = json_decode($pemesanan['kurir'], true);
        $pemesanan['data_mid'] = json_decode($pemesanan['data_mid'], true);
        foreach ($pemesanan['items'] as $ind_i => $item) {
            if ($item['name'] != 'Voucher' && $item['name'] != 'Biaya Admin' && $item['name'] != 'Biaya Ongkir' && $item['name'] != 'Flash Sale') {
                $varianItem = rtrim(explode("(", $item['name'])[1], ")");
                $produkCurr = $this->barangModel->getBarang($item['id']);
                $dimensi = json_decode($produkCurr['deskripsi'], true)['dimensi']['asli'];
                $pemesanan['items'][$ind_i]['name'] = $produkCurr['nama'] . ", " . $dimensi['panjang'] . "x" . $dimensi['lebar'] . "x" . $dimensi['tinggi'] . "<br>" . $varianItem;
                array_push($items, $item);
            }
        }
        $bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $tsPemesanan = strtotime($pemesanan['data_mid']['transaction_time']);
        $data = [
            'title' => 'Surat Jalan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan'           => $pemesanan,
            'tanggal' => date("d", $tsPemesanan) . " " . $bulan[(int)date("m", $tsPemesanan) - 1] . " " . date("Y", $tsPemesanan),
            'items' => $items
        ];
        return view('gudang/suratJalan', $data);
    }

    public function suratInvoice($id_pesanan)
    {
        $pemesanan = $this->pemesananOfflineModel->getPemesanan($id_pesanan);

        // kalau belum ada tanggal invoice, balik ke list
        if (empty($pemesanan['tanggal_inv'])) {
            $seg = ($pemesanan['jenis'] === 'nf') ? 'nf' : 'sale';
            return redirect()->to('/admin/order/offline/' . $seg);
        }

        // ambil semua item (per pcs)
        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->select('barang.varian as barang_varian')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan])
            ->findAll();

        /**
         * =====================================================
         * GROUP ITEM:
         * - qty dihitung dari jumlah baris
         * - special_price = MAX (kalau ada 1 aja → SPECIAL PRICE)
         * =====================================================
         */
        $groupMap = [];

        foreach ($items as $i) {
            $key = $i['id_barang'] . '-' . $i['varian'];

            if (!isset($groupMap[$key])) {
                $dimensi = ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'];
                $desc = json_decode($i['deskripsi'] ?? '', true);
                if (isset($desc['dimensi']['asli'])) {
                    $dimensi = $desc['dimensi']['asli'];
                }

                $groupMap[$key] = [
                    'id_barang'     => $i['id_barang'],
                    'varian'        => $i['varian'],
                    'nama'          => $i['nama'],
                    'barang_varian' => $i['barang_varian'],
                    'harga'         => (int)$i['harga'],
                    'dimensi'       => $dimensi,
                    'jumlah'        => 0,
                    'special_price' => (int)($i['special_price'] ?? 0),
                ];
            }

            // qty = jumlah baris
            $groupMap[$key]['jumlah']++;

            // ambil special_price TERBESAR
            $sp = (int)($i['special_price'] ?? 0);
            if ($sp > $groupMap[$key]['special_price']) {
                $groupMap[$key]['special_price'] = $sp;
            }
        }

        $itemsFiltered = array_values($groupMap);

        // generate id_baru (kode barang + id varian)
        foreach ($itemsFiltered as $idx => $i) {
            $varian = json_decode($i['barang_varian'] ?? '[]', true);
            $found  = array_values(array_filter($varian, function ($v) use ($i) {
                return ($v['nama'] ?? '') === $i['varian'];
            }));

            $idVar = $found[0]['id'] ?? '';
            $itemsFiltered[$idx]['id_baru'] = $i['id_barang'] . $idVar;
        }

        return view('admin/suratInvoice', [
            'title'              => 'Surat Invoice',
            'apikey_img_ilena'   => $this->apikey_img_ilena,
            'pemesanan'          => $pemesanan,
            'items'              => $itemsFiltered,
        ]);
    }

    public function suratInvoiceDP($id_pesanan)
    {
        $pemesanan = $this->pemesananOfflineModel->getPemesanan($id_pesanan);

        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->select('barang.varian as barang_varian')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan])
            ->findAll();

        // === GROUP ITEM (SAMA PERSIS DENGAN suratInvoice) ===
        $groupMap = [];

        foreach ($items as $i) {
            $key = $i['id_barang'] . '-' . $i['varian'];

            if (!isset($groupMap[$key])) {
                $dimensi = ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'];
                $desc = json_decode($i['deskripsi'] ?? '', true);
                if (isset($desc['dimensi']['asli'])) {
                    $dimensi = $desc['dimensi']['asli'];
                }

                $groupMap[$key] = [
                    'id_barang'     => $i['id_barang'],
                    'varian'        => $i['varian'],
                    'nama'          => $i['nama'],
                    'barang_varian' => $i['barang_varian'],
                    'harga'         => (int)$i['harga'],
                    'dimensi'       => $dimensi,
                    'jumlah'        => 0,
                    'special_price' => (int)($i['special_price'] ?? 0),
                ];
            }

            $groupMap[$key]['jumlah']++;

            $sp = (int)($i['special_price'] ?? 0);
            if ($sp > $groupMap[$key]['special_price']) {
                $groupMap[$key]['special_price'] = $sp;
            }
        }

        $itemsFiltered = array_values($groupMap);

        foreach ($itemsFiltered as $idx => $i) {
            $varian = json_decode($i['barang_varian'] ?? '[]', true);
            $found  = array_values(array_filter($varian, function ($v) use ($i) {
                return ($v['nama'] ?? '') === $i['varian'];
            }));

            $idVar = $found[0]['id'] ?? '';
            $itemsFiltered[$idx]['id_baru'] = $i['id_barang'] . $idVar;
        }

        return view('admin/suratInvoice', [
            'title'              => 'Surat Invoice DP',
            'apikey_img_ilena'   => $this->apikey_img_ilena,
            'pemesanan'          => $pemesanan,
            'items'              => $itemsFiltered,
        ]);
    }


    public function suratOffline($sjOffline)
    {
        $pemesanan = $this->pemesananOfflineModel->getPemesanan($sjOffline);
        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->select('barang.varian as barang_varian') // <-- TAMBAH INI
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $sjOffline])
            ->findAll();
        $filter = [];
        $itemsFiltered = [];
        $counterJumlah = [];
        foreach ($items as $i) {
            if (!in_array($i['id_barang'] . '-' . $i['varian'], $filter)) {
                array_push($filter, $i['id_barang'] . '-' . $i['varian']);
                array_push($counterJumlah, 1);
            } else {
                $counterJumlah[count($counterJumlah) - 1] += 1;
            }
        }
        $filter = [];
        foreach ($items as $i) {
            if (!in_array($i['id_barang'] . '-' . $i['varian'], $filter)) {
                array_push($itemsFiltered, array_merge($i, [
                    'dimensi' => json_decode($i['deskripsi'], true)['dimensi']['asli'],
                    'jumlah'  => $counterJumlah[count($itemsFiltered)]
                ]));
                array_push($filter, $i['id_barang'] . '-' . $i['varian']);
            }
        }
        foreach ($itemsFiltered as $ind_i => $i) {
            $listVar = json_decode($i['barang_varian'] ?? '[]', true);
            $match   = null;
            foreach ($listVar as $v) {
                if (($v['nama'] ?? '') === ($i['varian'] ?? '')) { $match = $v; break; }
            }
            $itemsFiltered[$ind_i]['id_baru'] = isset($match['id'])
                ? ($i['id_barang'] . $match['id'])
                : $i['id_barang']; // fallback kalau struktur varian tak punya 'id'
            // optional rapikan:
            // unset($itemsFiltered[$ind_i]['barang_varian']);
        }
        // ===== Ambil SJ terakhir untuk pesanan ini (agar nomor tidak DRAFT) =====
        $sj = $this->suratJalanModel
            ->where('id_pesanan', $sjOffline)
            ->orderBy('id', 'DESC')
            ->first();

        // Kalau belum ada SJ sama sekali, buatkan 1 SJ otomatis
        if (!$sj) {
            $nowDb = date('Y-m-d H:i:s', strtotime('+7 hours'));
            $jenisDb = $this->normalizeJenis($pemesanan['jenis'] ?? 'sp');
            
            // Detect SP from id_pesanan prefix (e.g., 'SP00000002')
            $isSP = ($jenisDb === 'sp') || (stripos($sjOffline, 'SP') === 0);

            // Use appropriate number generator based on order type
            if ($jenisDb === 'nf') {
                $noSj = $this->generateSjNumberGlobalNF($nowDb);
            } elseif ($isSP) {
                $noSj = $this->generateSpNumberGlobal($nowDb);
            } else {
                $noSj = $this->generateSjNumberGlobal($nowDb);
            }

            $this->suratJalanModel->insert([
                'id_pesanan'   => $sjOffline,
                'no_sj'        => $noSj,
                'tanggal'      => $nowDb,
                'status'       => 'final',
                'finalized_at' => $nowDb,
            ]);

            $sj = $this->suratJalanModel
                ->where('id_pesanan', $sjOffline)
                ->orderBy('id', 'DESC')
                ->first();
        }


        $data = [
            'title' => 'Surat Jalan Offline',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'items' => $itemsFiltered,
            'sj' => $sj,
        ];
        return view('admin/suratOffline', $data);
    }

    public function suratKoreksi($id_Koreksi)
    {
        $pemesanan = $this->pemesananOfflineModel->getPemesanan($id_Koreksi);
        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_Koreksi])
            ->findAll();

        $grouped = [];
        foreach ($items as $item) {
            $key = $item['id_barang'] . '|' . $item['varian'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id_pesanan' => $item['id_pesanan'], // ambil yang pertama ditemukan
                    'id_barang' => $item['id_barang'],
                    'harga' => $item['harga'],
                    'id_return' => $item['id_return'],
                    'nama' => $item['nama'],
                    'dimensi' => json_decode($item['deskripsi'], true)['dimensi']['asli'],
                    'varian' => $item['varian'],
                    'jumlah' => 0,
                ];
            }

            $grouped[$key]['jumlah']++;
        }

        $itemsFilter = array_values($grouped);

        $data = [
            'title' => 'Surat Koreksi',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'items' => $itemsFilter,
            'id_koreksi' => $id_Koreksi,
        ];
        return view('admin/suratKoreksi', $data);
    }


    public function changePic()
    {
        $barangLama = $this->barangModel
            ->select('id')
            ->select('nama')
            ->select('deskripsi')
            ->findAll();
        foreach ($barangLama as $ind_b => $b) {
            $barangLama[$ind_b]['dimensi'] = json_decode($b['deskripsi'], true)['dimensi']['asli'];
            $barangLama[$ind_b]['deskripsi'] = '';
        }
        $data = [
            'title' => 'Ganti Gambar',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'barang' => $barangLama,
            'barangJson' => json_encode($barangLama)
        ];
        return view('admin/gantiGambar', $data);
    }
    public function seoCheck($id)
    {
        $artikel = $this->artikelModel->getArtikel($id);

        if (!$artikel) {
            return 'Artikel tidak ditemukan';
        }

        $seoTitleMeta = checkTitleMeta($artikel['judul'], $artikel['deskripsi']);
        $seoKeywordDensity = checkKeywordDensity($artikel['isi'], $artikel['keywords']);
        $seoImages = checkImageAltTags($artikel['isi']);
        $seoLinks = checkInternalExternalLinks($artikel['isi'], base_url());
        $seoHeadings = checkHeadingStructure($artikel['isi']);
        $seoReadability = readabilityScore($artikel['isi']);

        return view('seo_check', [
            'artikel' => $artikel,
            'seoTitleMeta' => $seoTitleMeta,
            'seoKeywordDensity' => $seoKeywordDensity,
            'seoImages' => $seoImages,
            'seoLinks' => $seoLinks,
            'seoHeadings' => $seoHeadings,
            'seoReadability' => $seoReadability,
        ]);
    }

    // ========== VOUCHER ==========
    public function voucher()
    {
        $q = $this->request->getGet('q');
        $builder = $this->voucherModel->orderBy('id','DESC');
        if ($q) {
            $builder->groupStart()
                ->like('kode', $q)
                ->orLike('nama', $q)
                ->groupEnd();
        }
        $data = [
            'title'   => 'Kelola Voucher',
            'voucher' => $builder->findAll(),
        ];
        return view('admin/voucher', $data);
    }

    public function actionAddVoucher()
    {
        // normalisasi nilai & tipe
        $tipe  = $this->request->getPost('tipe') ?: 'persen';
        $nilai = (int)($this->request->getPost('nilai') ?? 0);

        $data = [
            'kode'   => strtoupper((string)$this->request->getPost('kode')),
            'nama'   => (string)$this->request->getPost('nama'),
            'deskripsi' => (string)$this->request->getPost('deskripsi'),
            'tipe'   => $tipe,
            'nilai'  => $nilai,
            // fallback lama (optional): simpan juga nominal/satuan agar baris lama & baru konsisten
            'satuan'  => ($tipe === 'persen') ? 'persen' : 'rupiah',
            'nominal' => $nilai,

            'minimal_belanja' => (int)($this->request->getPost('minimal_belanja') ?: 0),
            'mulai'           => $this->request->getPost('mulai') ?: null,
            'berakhir'        => $this->request->getPost('berakhir') ?: null,
            'auto_apply'      => $this->request->getPost('auto_apply') ? 1 : 0,
            'aktif'           => $this->request->getPost('aktif') ? 1 : 0,
            'target'          => $this->request->getPost('target') ?: 'semua',
            'max_pakai'       => (int)($this->request->getPost('max_pakai') ?: 0),
            'sekali_pakai_per_user' => $this->request->getPost('sekali_pakai_per_user') ? 1 : 0,
            // list_email biarkan null/[] di awal
        ];

        $this->voucherModel->insert($data);
        return redirect()->to('/admin/voucher')->with('msg', 'Voucher berhasil ditambahkan!');
    }

    public function deleteVoucher($id)
    {
        $this->voucherModel->delete((int)$id);
        return redirect()->to('/admin/voucher')->with('msg', 'Voucher berhasil dihapus!');
    }

    // Edit dari modal (POST /admin/voucher/edit/{id})
    public function editVoucher($id)
    {
        $row = $this->voucherModel->find((int)$id);
        if (!$row) {
            return redirect()->to('/admin/voucher')->with('msg', 'Voucher tidak ditemukan.');
        }

        $tipe  = $this->request->getPost('tipe') ?: ($row['tipe'] ?? 'persen');
        $nilai = (int)($this->request->getPost('nilai') ?? ($row['nilai'] ?? 0));

        $payload = [
            'kode'   => strtoupper((string)$this->request->getPost('kode')),
            'nama'   => (string)$this->request->getPost('nama'),
            'deskripsi' => (string)$this->request->getPost('deskripsi'),
            'tipe'   => $tipe,
            'nilai'  => $nilai,
            'satuan'  => ($tipe === 'persen') ? 'persen' : 'rupiah', // fallback lama
            'nominal' => $nilai,                                    // fallback lama

            'minimal_belanja' => (int)($this->request->getPost('minimal_belanja') ?? 0),
            'mulai'           => $this->request->getPost('mulai') ?: null,
            'berakhir'        => $this->request->getPost('berakhir') ?: null,
            'target'          => $this->request->getPost('target') ?: 'semua',
            'auto_apply'      => $this->request->getPost('auto_apply') ? 1 : 0,
            'sekali_pakai_per_user' => $this->request->getPost('sekali_pakai_per_user') ? 1 : 0,
        ];

        // bersihkan field yang semuanya null agar tidak kena "There is no data to update"
        $payload = array_filter($payload, static function($v){ return $v !== null; });

        if ($payload === []) {
            return redirect()->to('/admin/voucher')->with('msg', 'Tidak ada perubahan.');
        }

        $this->voucherModel->update((int)$id, $payload);
        return redirect()->to('/admin/voucher')->with('msg', 'Voucher berhasil diperbarui!');
    }

    // Toggle via AJAX (POST /admin/voucher/toggle/{id})
    public function toggleVoucher($id)
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success'=>false,'message'=>'Method not allowed']);
        }

        $row = $this->voucherModel->find((int)$id);
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'Voucher tidak ditemukan']);
        }

        $aktifReq = $this->request->getPost('aktif');
        $new = ($aktifReq === null) ? (int)!((int)($row['aktif'] ?? 0))
                                    : ((int)$aktifReq ? 1 : 0);

        if ((int)$row['aktif'] === $new) {
            return $this->response->setJSON(['success'=>true,'message'=>'Tidak ada perubahan','data'=>['aktif'=>$new]]);
        }

        $ok = $this->voucherModel->update((int)$id, ['aktif' => $new]);
        return $this->response->setJSON([
            'success' => (bool)$ok,
            'message' => $ok ? 'Berhasil mengubah status.' : 'Gagal mengubah status.',
            'data'    => ['aktif' => $new]
        ]);
    }

    public function voucherUsage()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('voucher_usage vu')
            ->select('vu.id, vu.kode_voucher, vu.email, vu.used_at, v.nama as nama_voucher')
            ->join('voucher v', 'v.kode = vu.kode_voucher', 'left')
            ->orderBy('vu.id', 'DESC');

        $q = $this->request->getGet('q');
        if ($q) {
            $builder->groupStart()
                ->like('vu.kode_voucher', $q)
                ->orLike('vu.email', $q)
                ->orLike('v.nama', $q)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();

        return view('admin/voucher_usage', [
            'title' => 'Pemakaian Voucher',
            'rows'  => $rows,
            'q'     => $q
        ]);
    }

    public function deleteVoucherUsage($id)
    {
        $this->voucherUsageModel->delete((int)$id);
        return redirect()->to('/admin/voucher/usage')->with('msg','Log penggunaan dihapus.');
    }


    


    // NOTE: orderOffline method is now defined in OrderOfflineTrait
    // NOTE: Duplicate methods removed (orderOfflineUpdate, getItemsOffline)

    // NOTE: Duplicate methods removed (orderOfflineAdd, generateAlamat)

    // NOTE: Duplicate method removed (actionAddOrderOffline)

    // NOTE: Duplicate methods removed (actionBuatDP, actionKoreksiSP)

    // NOTE: Duplicate methods removed (actionBuatInvoice, benerinSurat, actionAccOrderOffline, orderOfflineFinalize)

    // =========================================================
    // ======================== INTERIOR ========================
    // (sama seperti code kamu terakhir)
    // =========================================================

    // NOTE: Duplicate methods removed (projectInteriorAdd, actionProjectInteriorAdd, projectInteriorDetail, projectInteriorSjDraft)



    public function projectInteriorPaymentInvoice(string $kodeProject, int $paymentId)
    {
        $project = $this->projectInteriorModel
            ->where('kode_project', $kodeProject)
            ->first();

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Project interior dengan kode {$kodeProject} tidak ditemukan."
            );
        }

        $payment = $this->projectInteriorPaymentModel
            ->where('project_id', $project['id'])
            ->where('id', $paymentId)
            ->first();

        if (!$payment) {
            return redirect()
                ->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Pembayaran tidak ditemukan untuk project ini.');
        }

        $historyBefore = $this->projectInteriorPaymentModel
            ->where('project_id', $project['id'])
            ->where('id <', $payment['id'])
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();
        $offline = $db->table('pemesanan_offline')
            ->where('id_pesanan', $project['kode_sj'])
            ->whereIn('jenis', ['sale', 'nf'])
            ->get()->getRowArray();

if (!$offline) {
            return redirect()
                ->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Data pemesanan offline untuk dokumen utama tidak ditemukan.');
        }

        // hitung nomor display
        $rawId      = (string)($offline['id_pesanan'] ?? $project['kode_sj']);
        $jenisLower = strtolower(trim($offline['jenis'] ?? ''));
        $nomorDisplayBase = '';

        if (preg_match('/^NF(\d+)$/i', $rawId, $m)) {
            $digits = $m[1];
            $last5  = substr($digits, -5);
            $nomorDisplayBase = 'NF' . str_pad($last5, 5, '0', STR_PAD_LEFT);
        } else {
            $nomorBase = substr($rawId, 5);

            if ($jenisLower === 'nf') {
                if (preg_match('/(\d+)/', $nomorBase, $m2)) {
                    $digits2 = $m2[1];
                    $last5   = substr($digits2, -5);
                    $nomorDisplayBase = 'NF' . str_pad($last5, 5, '0', STR_PAD_LEFT);
                } else {
                    $nomorDisplayBase = 'NF' . $nomorBase;
                }
            } else {
                $nomorDisplayBase = $nomorBase;
            }
        }

        $totalPaidUntilThis = 0;
        foreach ($historyBefore as $h) $totalPaidUntilThis += (int)($h['nominal'] ?? 0);
        $totalPaidUntilThis += (int)($payment['nominal'] ?? 0);

        $nilaiKontrakTotal = (int)($project['nilai_kontrak'] ?? 0);
        $nominalPembayaran = (int)($payment['nominal'] ?? 0);

        $pemesanan = [
            'id_pesanan'        => $project['kode_sj'],
            'jenis'             => $offline['jenis'] ?? 'sale',
            'status'            => 'payment',
            'down_payment'      => 0,
            'total_akhir'       => $nilaiKontrakTotal,
            'tanggal'           => $payment['tanggal'] ?? date('Y-m-d'),
            'tanggal_inv'       => $payment['tanggal'] ?? date('Y-m-d'),
            'nama_npwp'         => $offline['nama_npwp'] ?: ($offline['nama'] ?? ($project['nama_project'] ?? '-')),
            'nohp'              => $offline['nohp'] ?? '',
            'alamat_tagihan'    => $offline['alamat_tagihan'] ?: ($offline['alamat_pengiriman'] ?? ''),
            'alamat_pengiriman' => $offline['alamat_pengiriman'] ?? '',
            'npwp'              => $offline['npwp'] ?? null,
            'po'                => $offline['po'] ?? null,
            'keterangan'        => trim((($offline['keterangan'] ?? '') ? $offline['keterangan'] . ' | ' : '') .
                'Pembayaran: ' . strtoupper($payment['jenis']) . ' ' . number_format($nominalPembayaran, 0, ',', '.')),
        ];

        $items = [];
        $payment['no_invoice'] = $nomorDisplayBase;

        return view('admin/suratInvoice', [
            'title'               => 'Invoice Pembayaran Project Interior',
            'pemesanan'           => $pemesanan,
            'items'               => $items,
            'project'             => $project,
            'is_payment_invoice'  => true,
            'payment'             => $payment,
            'history_before'      => $historyBefore,
            'total_paid_until'    => $totalPaidUntilThis,
        ]);
    }

    // NOTE: Duplicate method removed (actionProjectInteriorAddPayment)
public function projectInteriorCreateInvoice(string $kodeProject)
    {
        $project = $this->projectInteriorModel
            ->where('kode_project', $kodeProject)
            ->first();

        if (!$project) {
            throw PageNotFoundException::forPageNotFound(
                "Project interior dengan kode {$kodeProject} tidak ditemukan."
            );
        }

        if (($project['status'] ?? '') !== 'lunas') {
            return redirect()
                ->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Project belum LUNAS, invoice belum bisa dibuat.');
        }

        // data offline reserved (untuk alamat/npwp/dll)
        $db = \Config\Database::connect();
        $offline = $db->table('pemesanan_offline')
            ->where('id_pesanan', $project['kode_sj'])
            ->whereIn('jenis', ['sale', 'nf'])
            ->get()->getRowArray();

if (!$offline) {
            return redirect()
                ->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Data pemesanan offline (reserved) untuk dokumen utama tidak ditemukan.');
        }

        $projectId = (int)($project['id'] ?? 0);

        // ambil item multi
        $piItems = $this->projectInteriorItemModel
            ->where('project_id', $projectId)
            ->orderBy('id', 'ASC')
            ->findAll();

        // nilai kontrak = grand_total (incl PPN)
        $nilaiKontrakTotal = (int)($project['nilai_kontrak'] ?? 0);
        if ($nilaiKontrakTotal <= 0) {
            $nilaiKontrakTotal = (int)($project['grand_total'] ?? 0);
        }

        $subtotalDpp = (int)($project['subtotal_dpp'] ?? 0);
        $totalPpn    = (int)($project['total_ppn'] ?? 0);

        // fallback hitung kalau field kosong
        if ($subtotalDpp <= 0 && is_array($piItems)) {
            $subtotalDpp = 0;
            foreach ($piItems as $it) $subtotalDpp += (int)($it['subtotal'] ?? ((int)($it['harga_satuan'] ?? 0) * (int)($it['qty'] ?? 0)));
        }
        if ($totalPpn < 0) $totalPpn = 0;

        // tanggal invoice harus mengikuti tanggal SJ TERAKHIR yang sudah PRINTED/FINAL
        $tanggalSjLast = null;
        $sjLast = $this->suratJalanModel
            ->where('id_pesanan', (string)$project['kode_sj'])
            ->where('status', 'printed')
            ->orderBy('tanggal', 'DESC')
            ->first();

        if ($sjLast && !empty($sjLast['tanggal'])) {
            $tanggalSjLast = $sjLast['tanggal'];
        }

        $tanggalSj  = $tanggalSjLast ?? ($offline['tanggal'] ?? date('Y-m-d'));
        $tanggalInv = $tanggalSjLast ?? ($offline['tanggal_inv'] ?? $tanggalSj);

        // build items untuk suratInvoice view
        $items = [];
        foreach (($piItems ?? []) as $it) {
            $qty = (int)($it['qty'] ?? 0);
            if ($qty <= 0) continue;

            $items[] = [
                'id_baru'       => strtoupper((string)($it['kode_barang'] ?? $project['kode_project'])),
                'nama'          => (string)($it['nama_barang'] ?? 'PEKERJAAN INTERIOR'),
                'varian'        => $project['nama_project'] ?? '',
                'dimensi'       => ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'],
                'jumlah'        => $qty,
                'harga'         => (int)($it['harga_satuan'] ?? 0), // DPP per unit
                'special_price' => 0,
            ];
        }

        // payment breakdown
        $payments = $this->projectInteriorPaymentModel
            ->where('project_id', $projectId)
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        $sumDp = 0; $sumTermin = 0; $sumPelunasan = 0;
        foreach ($payments as $p) {
            $nominal = (int) ($p['nominal'] ?? 0);
            switch ($p['jenis']) {
                case 'dp': $sumDp += $nominal; break;
                case 'termin': $sumTermin += $nominal; break;
                case 'pelunasan': $sumPelunasan += $nominal; break;
            }
        }
        $totalBayar  = $sumDp + $sumTermin + $sumPelunasan;
        $sisaTagihan = max(0, $nilaiKontrakTotal - $totalBayar);

        $pemesanan = [
            'id_pesanan'        => $project['kode_sj'],
            'jenis'             => $offline['jenis'] ?? 'sale',
            'status'            => 'success',
            'down_payment'      => 0,
            'total_akhir'       => $nilaiKontrakTotal,
            'tanggal'           => $tanggalSj,
            'tanggal_inv'       => $tanggalInv,
            'nama_npwp'         => $offline['nama_npwp'] ?: ($offline['nama'] ?? ($project['nama_project'] ?? '-')),
            'nohp'              => $offline['nohp'] ?? '',
            'alamat_tagihan'    => $offline['alamat_tagihan'] ?: ($offline['alamat_pengiriman'] ?? ''),
            'alamat_pengiriman' => $offline['alamat_pengiriman'] ?? '',
            'npwp'              => $offline['npwp'] ?? null,
            'po'                => $offline['po'] ?? null,
            'keterangan'        => $offline['keterangan'] ?? null,
        ];

        
        // ===== SUMMARY INVOICE (INTERIOR): gabungkan semua nomor SJ dalam project ini =====
        $summary_sj_numbers = '';
        try {
            $sjKey = (string)($project['kode_sj'] ?? '');
            if ($sjKey !== '') {
                $sjRows = $this->suratJalanModel
                    ->select('no_sj')
                    ->where('id_pesanan', $sjKey)
                    ->where('status', 'final')
                    ->where('no_sj IS NOT NULL', null, false)
                    ->orderBy('id', 'ASC')
                    ->findAll();

                $nums = [];
                foreach ($sjRows as $r) {
                    $no = (string)($r['no_sj'] ?? '');
                    if ($no === '') continue;
                    // contoh: NF0003/SJ/CBM/1/2026 atau 00003/SJ/CBM/1/2026
                    if (preg_match('/^(?:NF)?([^\/]+)/i', $no, $mm)) {
                        $base = trim((string)($mm[1] ?? ''));
                        // pastikan hanya angka (tanpa NF)
                        if (preg_match('/(\d+)/', $base, $m2)) {
                            $nums[] = $m2[1];
                        }
                    }
                }
                if (!empty($nums)) {
                    // urutkan secara numerik tanpa mengubah padding asli secara brutal
                    // (kita simpan string apa adanya, tapi sort by integer value)
                    usort($nums, function($a, $b) {
                        return ((int)$a) <=> ((int)$b);
                    });
                    // pertahankan padding: kembalikan ke string asli (kalau ada leading zero sudah ikut di $a)
                    $summary_sj_numbers = implode(',', $nums);
                }
            }
        } catch (\Throwable $e) {
            // diamkan, biar invoice tetap bisa dicetak
            $summary_sj_numbers = '';
        }

        return view('admin/suratInvoice', [
            'title'               => 'Invoice Project Interior',
            'pemesanan'           => $pemesanan,
            'items'               => $items,
            'is_project_interior' => true,
            'nilai_kontrak'       => $nilaiKontrakTotal,
            'dpp'                 => $subtotalDpp,
            'ppn_11'              => $totalPpn,
            'dp_total'            => $sumDp,
            'termin_total'        => $sumTermin,
            'pelunasan_total'     => $sumPelunasan,
            'total_bayar'         => $totalBayar,
            'sisa_tagihan'        => $sisaTagihan,
            'payments'            => $payments,
            'summary_sj_numbers'  => $summary_sj_numbers,
        ]);
    }



    // NOTE: Duplicate methods removed (projectInteriorSuratJalan)

    // NOTE: Duplicate methods removed (projectInteriorList)

    // =========================================================
    // ============ SURAT JALAN OFFLINE (shared) =================
    // =========================================================
public function projectInteriorCreateSuratJalan(string $kodeProject)
{
    $kodeProject = trim($kodeProject);
    if ($kodeProject === '') {
        return redirect()->back()->with('msg', 'Kode project tidak valid.');
    }

    $req = $this->request;

    // 1) Ambil project
    $project = $this->projectInteriorModel->where('kode_project', $kodeProject)->first();
    if (!$project) {
        return redirect()->back()->with('msg', 'Project interior tidak ditemukan.');
    }

    // 2) DP gate (wajib DP dulu) — kompatibel jika tabel payment pakai kolom berbeda
    $dpTotal = 0;
    try {
        $dpTotal = (int)($this->projectInteriorPaymentModel
            ->selectSum('nominal', 'total')
            ->where('kode_project', $kodeProject)
            ->whereIn('jenis', ['dp','DP','down payment','uang muka','deposit'])
            ->get()->getRowArray()['total'] ?? 0);
    } catch (\Throwable $e) {
        // fallback: beberapa schema pakai project_id
        try {
            $dpTotal = (int)($this->projectInteriorPaymentModel
                ->selectSum('nominal', 'total')
                ->where('project_id', (int)($project['id'] ?? 0))
                ->whereIn('jenis', ['dp','DP','down payment','uang muka','deposit'])
                ->get()->getRowArray()['total'] ?? 0);
        } catch (\Throwable $e2) {
            // kalau dua-duanya gagal, biarkan 0 (akan tertahan DP gate)
            $dpTotal = 0;
        }
    }

    if ($dpTotal <= 0) {
        return redirect()->to(site_url('admin/project-interior/'.$kodeProject))
            ->with('msg', 'Wajib DP dulu sebelum membuat Surat Jalan (SJ).');
    }

    // 3) Ambil item yang dipilih dari form (checkbox)
    $itemKeys = $req->getPost('item_keys');
    if (!is_array($itemKeys)) $itemKeys = [];
    $itemKeys = array_values(array_filter(array_map('trim', $itemKeys)));

    if (count($itemKeys) <= 0) {
        return redirect()->to(site_url('admin/project-interior/'.$kodeProject))
            ->with('msg', 'Pilih minimal 1 item yang akan dibuatkan SJ.');
    }

    $db = \Config\Database::connect();

    try {
        $db->transBegin();

        // 4) Tentukan SJ ke- (boleh banyak SJ per project)
        $last = $this->suratJalanModel
            ->selectMax('pengiriman_ke', 'maxk')
            ->where('project_id', (int)($project['id'] ?? 0))
            ->get()->getRowArray();

        $pengirimanKe = (int)($last['maxk'] ?? 0) + 1;

        // 5) Buat header SJ (draft)
        $insertOk = $this->suratJalanModel->insert([
            'project_id'    => (int)($project['id'] ?? 0),
            'id_pesanan'    => $kodeProject, // key project (PI0000...)
            'pengiriman_ke' => $pengirimanKe,
            'status'        => 'draft',
            'tanggal'       => date('Y-m-d H:i:s', strtotime('+7 hours')),
        ], true);

        if (!$insertOk) {
            throw new \RuntimeException('Gagal membuat header Surat Jalan.');
        }

        $sjId = (int)$this->suratJalanModel->getInsertID();
        if ($sjId <= 0) {
            throw new \RuntimeException('Gagal mendapatkan ID Surat Jalan.');
        }

        // 6) Ambil item project (kompatibel kalau kolom relasi beda)
        try {
            $projectItems = $this->projectInteriorItemModel
                ->where('kode_project', $kodeProject)
                ->findAll();
        } catch (\Throwable $e) {
            $projectItems = $this->projectInteriorItemModel
                ->where('project_id', (int)($project['id'] ?? 0))
                ->findAll();
        }

        // Map key => row (key = kode_barang||varian)
        $map = [];
        foreach (($projectItems ?? []) as $pi) {
            $k = (string)($pi['kode_barang'] ?? $pi['id_barang'] ?? '');
            $v = (string)($pi['varian'] ?? '');
            $map[$k.'||'.$v] = $pi;
        }

        // 7) Insert hanya item yang dipilih (qty awal 0, nanti diisi di edit SJ)
        $inserted = 0;
        foreach ($itemKeys as $key) {
            if (!isset($map[$key])) continue;

            $pi = $map[$key];

            $this->suratJalanItemModel->insert([
                'surat_jalan_id' => $sjId,
                // NOTE: schema kamu kemungkinan id_barang int (FK barang).
                // untuk interior kamu sebelumnya pakai kode_barang, jadi kita fallback.
                'id_barang'      => $pi['id_barang'] ?? $pi['kode_barang'] ?? null,
                'varian'         => $pi['varian'] ?? '',
                'qty'            => 0,
            ]);

            $inserted++;
        }

        if ($inserted <= 0) {
            throw new \RuntimeException('Tidak ada item yang valid untuk dibuatkan SJ (cek mapping item_keys).');
        }

        $db->transCommit();

        // arahkan ke edit SJ (offline edit) agar isi qty lalu finalize
        return redirect()->to(site_url('admin/surat-jalan/offline/' . $sjId . '/edit'))
            ->with('msg', 'SJ draft berhasil dibuat. Isi qty per item lalu Simpan/Finalize.');
    } catch (\Throwable $e) {
        if ($db->transStatus()) $db->transRollback();

        return redirect()->to(site_url('admin/project-interior/'.$kodeProject))
            ->with('msg', 'Gagal membuat SJ: ' . $e->getMessage());
    }
}





    public function createSuratJalanOffline(string $idPesanan)
    {
        $order = $this->pemesananOfflineModel->where('id_pesanan', $idPesanan)->first();
        if (!$order) {
            return redirect()->back()->with('msg', 'Pesanan tidak ditemukan.');
        }

        $lastKe = $this->suratJalanModel->getLastSjKe($idPesanan);
        $sjKe   = $lastKe + 1;

        $now = date('Y-m-d H:i:s', strtotime('+7 hours'));
        $normJenis = $this->normalizeJenis($order['jenis'] ?? 'sale');
        $noSj = ($normJenis === 'nf') ? $this->generateSjNumberGlobalNF($now) : $this->generateSjNumberGlobal($now);
        $this->suratJalanModel->insert([
            'id_pesanan'   => $idPesanan,
            'no_sj'        => $noSj,
            'sj_ke'        => $sjKe,
            'tanggal'      => $now,
            'status'       => 'final',
            'finalized_at' => $now,
        ]);
        $sjId = (int)$this->suratJalanModel->getInsertID();

        $ordered    = $this->groupOrderItems($idPesanan);
        $shippedMap = $this->shippedQtyMap($idPesanan);

        foreach ($ordered as $o) {
            $key = $o['id_barang'] . '||' . $o['varian'];
            $already = (int)($shippedMap[$key] ?? 0);
            $remain  = max(0, (int)$o['qty'] - $already);
            if ($remain <= 0) continue;

            $this->suratJalanItemModel->insert([
                'surat_jalan_id' => $sjId,
                'id_barang'      => (int)$o['id_barang'],
                'varian'         => (string)$o['varian'],
                'qty'            => $remain,
            ]);
        }

        return redirect()->to('/admin/surat-jalan/offline/' . $sjId . '/edit')
            ->with('msg', 'SJ ke-' . $sjKe . ' dibuat (DRAFT). Cek qty lalu finalize untuk nomor SJ.');
    }

    public function suratJalanOffline(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        $pemesanan = $this->pemesananOfflineModel
            ->where('id_pesanan', $sj['id_pesanan'])
            ->first();
        if (!$pemesanan) return redirect()->back()->with('msg', 'Pesanan sumber SJ tidak ditemukan.');

        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.*')
            ->select('barang.nama as barang_nama, barang.deskripsi as barang_deskripsi')
            ->join('barang', 'barang.id = surat_jalan_item.id_barang', 'left')
            ->where('surat_jalan_item.surat_jalan_id', $suratJalanId)
            ->orderBy('surat_jalan_item.id', 'ASC')
            ->findAll();

        $items = [];
        foreach ($rows as $r) {
            $dim = ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'];

            if (!empty($r['dimensi_json'])) {
                $dj = json_decode($r['dimensi_json'], true);
                if (is_array($dj)) $dim = array_merge($dim, $dj);
            } else {
                $d = json_decode($r['barang_deskripsi'] ?? '', true);
                if (is_array($d) && isset($d['dimensi']['asli'])) {
                    $dim = $d['dimensi']['asli'];
                }
            }

            $kode = !empty($r['kode_barang']) ? $r['kode_barang'] : (string)($r['id_barang'] ?? '');
            $nama = !empty($r['nama_barang']) ? $r['nama_barang'] : (string)($r['barang_nama'] ?? '-');

            $items[] = [
                'id_baru' => (string)$kode,
                'nama'    => (string)$nama,
                'varian'  => (string)($r['varian'] ?? ''),
                'dimensi' => $dim,
                'jumlah'  => (int)($r['qty'] ?? 0),
            ];
        }

        return view('admin/suratOffline', [
            'title'     => 'Surat Jalan',
            'pemesanan' => $pemesanan,
            'items'     => $items,
            'sj'        => $sj,
        ]);
    }

    public function suratJalanOfflineEdit(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        $pemesanan = $this->pemesananOfflineModel->where('id_pesanan', $sj['id_pesanan'])->first();
        if (!$pemesanan) return redirect()->back()->with('msg', 'Pesanan sumber tidak ditemukan.');

        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.*')
            ->select('barang.nama as barang_nama, barang.deskripsi as barang_deskripsi')
            ->join('barang', 'barang.id = surat_jalan_item.id_barang', 'left')
            ->where('surat_jalan_item.surat_jalan_id', $suratJalanId)
            ->orderBy('surat_jalan_item.id', 'ASC')
            ->findAll();

        $isInteriorSj = false;
        foreach ($rows as $r) {
            if (empty($r['id_barang'])) { $isInteriorSj = true; break; }
        }

        // OFFLINE: data ordered untuk dropdown/cek sisa
        $ordered = [];
        if (!$isInteriorSj) {
            $ordered = $this->groupOrderItems($sj['id_pesanan']);
        }

        // shipped qty untuk validasi edit:
        // - OFFLINE: by id_barang||varian
        // - INTERIOR: by I||kode_barang (khusus item interior)
        $shippedMap = $this->shippedQtyMapExceptSj($sj['id_pesanan'], $suratJalanId);

        return view('admin/suratJalanEdit', [
            'title'        => 'Edit Surat Jalan',
            'sj'           => $sj,
            'pemesanan'    => $pemesanan,
            'rows'         => $rows,
            'ordered'      => $ordered,
            'shippedMap'   => $shippedMap,
            'isInteriorSj' => $isInteriorSj,
            'msg'          => session()->getFlashdata('msg'),
        ]);
    }


    public function suratJalanOfflineEditSave(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        // status SJ: draft/final/printed/void (final/printed = terkunci)
        if (in_array(($sj['status'] ?? ''), ['final','printed','void'], true)) {
            return redirect()->back()->with('msg', 'SJ sudah final/printed, tidak bisa diedit.');
        }

        $req = $this->request;

        // update tanggal jika ada
        $tanggal = $req->getPost('tanggal');
        if ($tanggal) {
            $tanggal = str_replace('T', ' ', $tanggal);
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/', $tanggal)) $tanggal .= ':00';
            $this->suratJalanModel->where('id', $suratJalanId)->set(['tanggal' => $tanggal])->update();
        }

        $itemIds = $req->getPost('item_id');
        $qtys    = $req->getPost('qty');

        if (!is_array($itemIds) || !is_array($qtys) || count($itemIds) !== count($qtys)) {
            return redirect()->back()->with('msg', 'Payload item tidak valid.');
        }

        // shipped qty (exclude sj yg sedang diedit) -> hanya printed
        $shippedMap = $this->shippedQtyMapExceptSj($sj['id_pesanan'], $suratJalanId);

        // rows existing
        $rows = $this->suratJalanItemModel->where('surat_jalan_id', $suratJalanId)->findAll();
        $byId = [];
        foreach ($rows as $r) $byId[(int)$r['id']] = $r;

        // detect interior
        $isInteriorSj = false;
        foreach ($rows as $r) { if (empty($r['id_barang'])) { $isInteriorSj = true; break; } }

        // ===== orderedMap (limit total boleh dikirim) =====
        // OFFLINE: by id_barang||varian
        // INTERIOR: by I||kode_barang (ambil dari project_interior_item.qty)
        $orderedMap = [];

        if (!$isInteriorSj) {
            $ordered = $this->groupOrderItems($sj['id_pesanan']);
            foreach ($ordered as $o) {
                $key = (int)$o['id_barang'].'||'.(string)$o['varian'];
                $orderedMap[$key] = (int)$o['qty'];
            }
        } else {
            // cari project interior berdasarkan kode_sj = id_pesanan
            $db = \Config\Database::connect();
            $proj = $db->table('project_interior')
                ->where('kode_sj', $sj['id_pesanan'])
                ->get()->getRowArray();

            if (!$proj) {
                return redirect()->back()->with('msg', 'Project interior tidak ditemukan untuk SJ ini.');
            }

            $projectId = (int)($proj['id'] ?? 0);
            if ($projectId <= 0) {
                return redirect()->back()->with('msg', 'Project ID interior tidak valid.');
            }

            // ambil item project multi
            $items = $db->table('project_interior_item')
                ->where('project_id', $projectId)
                ->get()->getResultArray();

            foreach ($items as $it) {
                $kode = trim((string)($it['kode_barang'] ?? ''));
                if ($kode === '') continue;
                $key = 'I||' . $kode;
                $orderedMap[$key] = (int)($it['qty'] ?? 0);
            }
        }

        // ===== total qty baru per key (untuk validasi) =====
        $newTotals = [];
        for ($i=0; $i<count($itemIds); $i++) {
            $id  = (int)$itemIds[$i];
            $qty = (int)$qtys[$i];
            if (!isset($byId[$id])) continue;
            if ($qty < 0) $qty = 0;

            $r = $byId[$id];

            if (!$isInteriorSj) {
                $key = (int)$r['id_barang'].'||'.(string)$r['varian'];
            } else {
                $kode = trim((string)($r['kode_barang'] ?? ''));
                $key  = 'I||' . $kode;
            }

            $newTotals[$key] = ($newTotals[$key] ?? 0) + $qty;
        }

        // ===== validasi: shipped(except current) + newTotals <= orderedMap =====
        foreach ($newTotals as $key => $qtyNew) {
            $already = (int)($shippedMap[$key] ?? 0);
            $allowed = (int)($orderedMap[$key] ?? 0);

            if (($already + $qtyNew) > $allowed) {
                $remain = max(0, $allowed - $already);
                return redirect()->back()->with('msg', 'Qty melebihi sisa untuk item ini. Sisa yang boleh dikirim: ' . $remain);
            }
        }

        // ===== save =====
        for ($i=0; $i<count($itemIds); $i++) {
            $id  = (int)$itemIds[$i];
            $qty = (int)$qtys[$i];
            if ($qty < 0) $qty = 0;
            $this->suratJalanItemModel->where('id', $id)->set(['qty' => $qty])->update();
        }

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
            ->with('msg', 'SJ berhasil disimpan.');
    }




    public function suratJalanOfflineItemAdd(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (($sj['status'] ?? '') === 'final') {
            return redirect()->back()->with('msg', 'SJ sudah final, tidak bisa ditambah item.');
        }

        $req = $this->request;

        $mode = $req->getPost('mode'); // offline|interior
        $qty  = (int)$req->getPost('qty');
        if ($qty <= 0) $qty = 1;

        if ($mode === 'interior') {
            $kodeBarang = trim((string)$req->getPost('kode_barang'));
            $namaBarang = trim((string)$req->getPost('nama_barang'));
            $varian     = trim((string)$req->getPost('varian'));

            if ($kodeBarang === '' || $namaBarang === '') {
                return redirect()->back()->with('msg', 'Kode barang & nama barang wajib diisi.');
            }

            $this->suratJalanItemModel->insert([
                'surat_jalan_id' => $suratJalanId,
                'id_barang'      => null,
                'kode_barang'    => $kodeBarang,
                'nama_barang'    => $namaBarang,
                'varian'         => $varian,
                'qty'            => $qty,
                'dimensi_json'   => json_encode(['panjang'=>'-','lebar'=>'-','tinggi'=>'-']),
            ]);

            return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
                ->with('msg', 'Item interior ditambahkan.');
        }

        $idBarang = (int)$req->getPost('id_barang');
        $varian   = trim((string)$req->getPost('varian'));

        if ($idBarang <= 0 || $varian === '') {
            return redirect()->back()->with('msg', 'Barang & varian wajib dipilih.');
        }

        $this->suratJalanItemModel->insert([
            'surat_jalan_id' => $suratJalanId,
            'id_barang'      => $idBarang,
            'kode_barang'    => null,
            'nama_barang'    => null,
            'varian'         => $varian,
            'qty'            => $qty,
            'dimensi_json'   => null,
        ]);

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
            ->with('msg', 'Item offline ditambahkan.');
    }

    public function suratJalanOfflineItemDelete(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (($sj['status'] ?? '') === 'final') {
            return redirect()->back()->with('msg', 'SJ sudah final, tidak bisa hapus item.');
        }

        $itemId = (int)$this->request->getPost('item_id');
        if ($itemId <= 0) return redirect()->back()->with('msg', 'Item tidak valid.');

        $this->suratJalanItemModel->where('id', $itemId)->where('surat_jalan_id', $suratJalanId)->delete();

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
            ->with('msg', 'Item dihapus.');
    }

    public function suratJalanOfflineFinalize(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (($sj['status'] ?? '') === 'final') {
            return redirect()->back()->with('msg', 'SJ sudah final.');
        }

        $sum = (int)($this->suratJalanItemModel
            ->selectSum('qty','total')
            ->where('surat_jalan_id', $suratJalanId)
            ->get()->getRowArray()['total'] ?? 0);

        if ($sum <= 0) {
            return redirect()->back()->with('msg', 'Tidak ada qty untuk diprint. Isi qty dulu.');
        }

        $tanggalDb = $sj['tanggal'] ?? date('Y-m-d H:i:s', strtotime('+7 hours'));

        $noSj = $sj['no_sj'] ?? null;
        if (!$noSj) {
            $noSj = $this->generateSjNumberGlobal($tanggalDb);
        }

        $this->suratJalanModel->where('id', $suratJalanId)->set([
            'no_sj'        => $noSj,
            'status'       => 'final',
            'finalized_at' => date('Y-m-d H:i:s', strtotime('+7 hours')),
        ])->update();

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId)
            ->with('msg', 'SJ di-finalize. Nomor: '.$noSj.' (siap print)');
    }

    // =========================================================
    // ========================= HELPERS ========================
    // =========================================================

    private function generateSjNumberGlobal(string $tanggalDb): string
    {
        $ts   = strtotime($tanggalDb ?: 'now');
        $mm   = (int)date('m', $ts);
        $yyyy = date('Y', $ts);

        $last = $this->suratJalanModel
            ->select('no_sj')
            ->where('no_sj IS NOT NULL', null, false)
            ->like('no_sj', '/SJ/CBM/' . $mm . '/' . $yyyy, 'both')
            ->orderBy('id', 'DESC')
            ->first();

        $next = 1;
        if ($last && !empty($last['no_sj'])) {
            $parts = explode('/', $last['no_sj']); // [00001, SJ, CBM, 12, 2025]
            $num   = (int)($parts[0] ?? 0);
            $next  = $num + 1;
        }

        $urut = str_pad((string)$next, 5, '0', STR_PAD_LEFT);
        return $urut . '/SJ/CBM/' . $mm . '/' . $yyyy;
    }


    /**
     * Nomor SJ global khusus NON FAKTUR (NF)
     * Format: NF0001/SJ/CBM/mm/yyyy
     */
    private function generateSjNumberGlobalNF(string $tanggalDb): string
    {
        $ts   = strtotime($tanggalDb ?: 'now');
        $mm   = (int)date('m', $ts);
        $yyyy = date('Y', $ts);

        $last = $this->suratJalanModel
            ->select('no_sj')
            ->where('no_sj IS NOT NULL', null, false)
            ->like('no_sj', 'NF%/SJ/CBM/' . $mm . '/' . $yyyy, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $next = 1;
        if ($last && !empty($last['no_sj'])) {
            // contoh: NF0007/SJ/CBM/01/2026
            if (preg_match('/NF(\d+)\//', $last['no_sj'], $mmatch)) {
                $next = ((int)$mmatch[1]) + 1;
            }
        }

        $urut = str_pad((string)$next, 4, '0', STR_PAD_LEFT);
        return 'NF' . $urut . '/SJ/CBM/' . $mm . '/' . $yyyy;
    }

    /**
     * Nomor SJ global sederhana untuk INTERIOR: SJ000001, SJ000002, ...
     * Disimpan di kolom surat_jalan.no_sj.
     * Tidak mengganggu format SJ offline lama (00001/SJ/CBM/...).
     */
    private function generateSjCodeSimple(): string
    {
        $db = \Config\Database::connect();
        $row = $db->table('surat_jalan')
            ->select('no_sj')
            ->like('no_sj', 'SJ', 'after')
            ->orderBy('id', 'DESC')
            ->get(1)->getRowArray();

        $last = (string)($row['no_sj'] ?? '');
        $n = 0;
        if (preg_match('/^SJ(\d{1,})$/', $last, $m)) {
            $n = (int)$m[1];
        } else {
            // fallback cari angka terakhir dari no_sj yang cocok
            if (preg_match('/SJ(\d+)/', $last, $m2)) $n = (int)$m2[1];
        }
        $next = $n + 1;
        return 'SJ' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }


    private function groupOrderItems(string $idPesanan): array
    {
        $rows = $this->pemesananOfflineItemModel
            ->select('id_barang, varian, COUNT(*) as qty')
            ->where('id_pesanan', $idPesanan)
            ->groupBy('id_barang, varian')
            ->findAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_barang' => (int)$r['id_barang'],
                'varian'    => (string)$r['varian'],
                'qty'       => (int)$r['qty'],
            ];
        }
        return $out;
    }

    private function shippedQtyMap(string $idPesanan): array
    {
        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.id_barang, surat_jalan_item.varian, SUM(surat_jalan_item.qty) as shipped_qty, surat_jalan_item.kode_barang')
            ->join('surat_jalan', 'surat_jalan.id = surat_jalan_item.surat_jalan_id')
            ->where('surat_jalan.id_pesanan', $idPesanan)
            ->groupBy('surat_jalan_item.id_barang, surat_jalan_item.varian, surat_jalan_item.kode_barang')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $isInterior = empty($r['id_barang']);
            $key = $isInterior ? '__INTERIOR__' : ((int)$r['id_barang'] . '||' . (string)$r['varian']);
            $map[$key] = (int)($r['shipped_qty'] ?? 0);
        }
        return $map;
    }

    private function generateNextOfflineCode(string $prefix): string
    {
        $prefix = strtoupper(trim($prefix));
        if ($prefix === '') return '';

        $last = $this->pemesananOfflineModel
            ->like('id_pesanan', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $nextNum = 1;
        if ($last && !empty($last['id_pesanan'])) {
            $digits = preg_replace('/\D+/', '', substr($last['id_pesanan'], strlen($prefix)));
            if ($digits !== '') $nextNum = ((int)$digits) + 1;
        }

        return $prefix . sprintf('%08d', $nextNum);
    }
private function interiorShippedQtyMap(string $idPesanan): array
    {
        if ($idPesanan === '') return [];

        // hanya hitung dari SJ yang sudah PRINTED (anggap FINAL)
        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.kode_barang, surat_jalan_item.qty')
            ->join('surat_jalan sj', 'sj.id = surat_jalan_item.surat_jalan_id')
            ->where('sj.id_pesanan', $idPesanan)
            ->where('sj.status', 'printed')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $kode = trim((string)($r['kode_barang'] ?? ''));
            if ($kode === '') continue;
            $key = 'I||' . $kode;
            $map[$key] = (int)($map[$key] ?? 0) + (int)($r['qty'] ?? 0);
        }
        return $map;
    }



    private function shippedQtyMapExceptSj(string $idPesanan, int $excludeSjId): array
    {
        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.id_barang, surat_jalan_item.varian, surat_jalan_item.qty, surat_jalan_item.kode_barang')
            ->join('surat_jalan sj', 'sj.id = surat_jalan_item.surat_jalan_id')
            ->where('sj.id_pesanan', $idPesanan)
            ->where('sj.id !=', $excludeSjId)
            ->where('sj.status', 'printed') // dianggap "FINAL"
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $isInterior = empty($r['id_barang']);

            if ($isInterior) {
                $kode = trim((string)($r['kode_barang'] ?? ''));
                if ($kode === '') continue;
                $key = 'I||' . $kode; // PER ITEM INTERIOR
            } else {
                $key = ((int)$r['id_barang']).'||'.((string)$r['varian']);
            }

            $map[$key] = (int)($map[$key] ?? 0) + (int)($r['qty'] ?? 0);
        }
        return $map;
    }



    // ==== Helper untuk tipe offline (SJ / DISPLAY / NF) ====
    protected function normalizeJenis($jenis): string
    {
        $j = strtolower(trim((string)$jenis));
        // faktur
        if ($j === 'sj' || $j === 'sale' || $j === 'faktur') return 'sale';
        // non faktur
        if ($j === 'nf' || $j === 'nonfaktur' || $j === 'non-faktur') return 'nf';
        // display (dulunya dipakai 'sp' di sistem lama)
        if ($j === 'display' || $j === 'sp') return 'display';
        return 'display';
    }

    /**
     * Prefix untuk id_pesanan offline.
     * - FAKTUR  : SJ........
     * - NONFAKTUR: NF........
     * - DISPLAY : SP........ (tetap SP agar kompatibel dengan data lama)
     */
    protected function prefixByJenis($jenis): string
    {
        switch ($this->normalizeJenis($jenis)) {
            case 'sale':    return 'SJ';
            case 'nf'  :    return 'NF';
            case 'display': return 'SP';
            default    :    return 'SP';
        }
    }

    protected function defaultStatusByJenis($jenis, int $downPayment): string
    {
        $nj = $this->normalizeJenis($jenis);
        if ($nj === 'sale') return $downPayment > 0 ? 'DP' : 'pending';
        if ($nj === 'nf')   return $downPayment > 0 ? 'DP' : 'pending';
        if ($nj === 'display') return 'success';
        return 'success';
    }

    protected function shouldDeductStockNow($jenis, int $downPayment): bool
    {
        $nj = $this->normalizeJenis($jenis);
        if ($nj === 'sale') return $downPayment == 0;
        if ($nj === 'nf')   return $downPayment == 0;
        // DISPLAY tidak potong stok saat create
        return false;
    }

    private function displayNoNf(string $idPesanan): string
    {
        // NF00000033 -> NF0033 (4 digit)
        if (preg_match('/^NF(\d+)$/i', $idPesanan, $m)) {
            $digits = $m[1];
            $last4  = substr($digits, -3);
            return 'NF' . str_pad($last4, 4, '0', STR_PAD_LEFT);
        }
        return $idPesanan;
    }
    private function ppnRateFromMode(string $mode): int
    {
        switch ($mode) {
            case 'ppn11':
                return 11;
            case 'ppn10':
                return 10;
            case 'non':
            default:
                return 0;
        }
    }



}