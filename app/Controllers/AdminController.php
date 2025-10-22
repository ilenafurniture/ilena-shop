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

class AdminController extends BaseController
{
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
        
    }

    // ==== Helper untuk tipe offline (SJ / SP / NF) ====
    protected function normalizeJenis($jenis): string
    {
        $j = strtolower(trim((string)$jenis));
        if ($j === 'sj' || $j === 'sale') return 'sale';
        if ($j === 'nf' || $j === 'nonfaktur' || $j === 'non-faktur') return 'nf';
        // fallback ke SP (surat pesanan / selain sale)
        return 'sp';
    }

    protected function prefixByJenis($jenis): string
    {
        switch ($this->normalizeJenis($jenis)) {
            case 'sale': return 'SJ';
            case 'nf'  : return 'NF';
            default    : return 'SP';
        }
    }

    protected function defaultStatusByJenis($jenis, int $downPayment): string
    {
        $nj = $this->normalizeJenis($jenis);
        if ($nj === 'sale') return $downPayment > 0 ? 'DP' : 'pending';
        if ($nj === 'nf')   return $downPayment > 0 ? 'DP' : 'pending'; // ← samakan dengan Sale
        return 'success'; // sp
    }


    protected function shouldDeductStockNow($jenis, int $downPayment): bool
    {
        $nj = $this->normalizeJenis($jenis);
        if ($nj === 'sale') return $downPayment == 0;
        if ($nj === 'nf')   return $downPayment == 0; // ← tadinya selalu true; ubah seperti Sale
        return false; // sp
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
            'pemesanan' => $pemesanan,
            'tanggal' => date("d", $tsPemesanan) . " " . $bulan[(int)date("m", $tsPemesanan) - 1] . " " . date("Y", $tsPemesanan),
            'items' => $items
        ];
        return view('gudang/suratJalan', $data);
    }

    public function suratInvoice($id_pesanan)
    {
        $pemesanan = $this->pemesananOfflineModel->getPemesanan($id_pesanan);
        if (!$pemesanan['tanggal_inv']) {
            $seg = ($pemesanan['jenis'] === 'nf') ? 'nf' : 'sale';
            return redirect()->to('/admin/order/offline/' . $seg);
        }

        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->select('barang.varian as barang_varian')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan])
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
                    'jumlah' => $counterJumlah[count($itemsFiltered)]
                ]));
                array_push($filter, $i['id_barang'] . '-' . $i['varian']);
            }
        }
        foreach ($itemsFiltered as $ind_i => $i) {
            $varian = json_decode($i['barang_varian'], true);
            $varianSelected = array_values(array_filter($varian, function ($v) use ($i) {
                return $v['nama'] == $i['varian'];
            }))[0];
            $itemsFiltered[$ind_i]['id_baru'] = $i['id_barang'] . $varianSelected['id'];
            // dd($varianSelected);
        }
        $data = [
            'title' => 'Surat Invoice',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'items' => $itemsFiltered,
        ];
        return view('admin/suratInvoice', $data);
    }
    public function suratInvoiceDP($id_pesanan)
    {
        $pemesanan = $this->pemesananOfflineModel->getPemesanan($id_pesanan);

        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan])
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
                    'jumlah' => $counterJumlah[count($itemsFiltered)]
                ]));
                array_push($filter, $i['id_barang'] . '-' . $i['varian']);
            }
        }
        $data = [
            'title' => 'Surat Invoice DP',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'items' => $itemsFiltered,
        ];
        return view('admin/suratInvoice', $data);
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

        $data = [
            'title' => 'Surat Jalan Offline',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'items' => $itemsFiltered,
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

    public function orderOffline($jenis)
    {
        $prefix  = $this->prefixByJenis($jenis); // SJ / SP / NF
        $pesanan = $this->pemesananOfflineModel
            ->like('id_pesanan', $prefix, 'after')
            ->findAll();

        $provinsi = $this->provinsiModel->findAll();

        $data = [
            'title'         => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pesanan'       => $pesanan,
            'pesananJson'   => json_encode($pesanan),
            'jenis'         => $this->normalizeJenis($jenis),  // 'sale' | 'sp' | 'nf'
            'msg'           => session()->getFlashdata('msg'),
            'provinsi'      => $provinsi
        ];
        return view('admin/orderOffline', $data);
    }

    public function orderOfflineUpdate()
    {
        $req = $this->request;
        $id  = trim((string)$req->getPost('id_pesanan'));
        $jenis = trim((string)$req->getPost('jenis')); // biar redirect balik ke tab yg sama

        if ($id === '') {
            return redirect()->back()->with('msg', 'ID pesanan tidak valid.');
        }

        // normalisasi DP (boleh kosong)
        $dpRaw = $req->getPost('down_payment');
        $dp    = null;
        if ($dpRaw !== null && $dpRaw !== '') {
            // hapus pemisah ribuan
            $dp = (int)preg_replace('/[^\d\-]/', '', (string)$dpRaw);
        }

        // normalisasi tanggal ke format DB (YYYY-mm-dd HH:ii:ss)
        $tgl = $req->getPost('tanggal'); // bisa dari datetime-local atau date
        if ($tgl) {
            // format dari input datetime-local biasanya "YYYY-mm-ddTHH:ii"
            $tgl = str_replace('T', ' ', $tgl);
            // pastikan komponennya lengkap (tambahkan :00 kalau perlu)
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/', $tgl)) {
                $tgl .= ':00';
            }
        }

        $payload = array_filter([
            'tanggal'            => $tgl ?: null,
            'nama'               => $req->getPost('nama') ?: null,
            'nohp'               => $req->getPost('nohp') ?: null,
            'alamat_pengiriman'  => $req->getPost('alamat_pengiriman') ?: null,
            'keterangan'         => $req->getPost('keterangan') ?: null,
            'npwp'               => $req->getPost('npwp') ?: null,
            'down_payment'       => $dp,
        ], function($v){ return $v !== null; });

        // cek exist
        $order = $this->pemesananOfflineModel->where('id_pesanan', $id)->first();
        if (!$order) {
            return redirect()->back()->with('msg', 'Order tidak ditemukan.');
        }

        // update aman
        $this->pemesananOfflineModel->where('id_pesanan', $id)->set($payload)->update();

        return redirect()->to('/admin/order/offline/'.($jenis ?: 'sale'))->with('msg', 'Data berhasil diperbarui.');
    }

    public function getItemsOffline($id_pesanan)
    {
        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan])
            ->findAll();
        foreach ($items as $ind_i => $i) {
            $items[$ind_i]['dimensi'] = json_decode($i['deskripsi'], true)['dimensi']['asli'];
            $items[$ind_i]['deskripsi'] = '';
        }
        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'items' => $items,
        ], false);
    }
    public function orderOfflineAdd()
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

        $data = [
            'title' => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produkJson' => json_encode($produk),
            'provinsi' => $provinsi
        ];
        return view('admin/orderOfflineAdd', $data);
    }

    public function generateAlamat($alamat)
    {
        $detail = $alamat['detail'];
        $provinsi = $alamat['provinsi'];
        $kabupaten = $alamat['kabupaten'];
        $kecamatan = $alamat['kecamatan'];
        $kelurahan = $alamat['kelurahan'];
        $kodepos = $alamat['kodepos'];
        return $detail . ", " . $kelurahan . ", " . $kecamatan . ", " . $kabupaten . ", " . $provinsi . " " . $kodepos;
    }
    public function actionAddOrderOffline()
    {
        $body = $this->request->getBody();
        $body = json_decode($body, true);

        // --- harden default supaya tidak Notice/Warning ---
        $body['downPayment']          = (int)($body['downPayment']          ?? 0);
        $body['potonganHargaSatuan']  = (int)($body['potonganHargaSatuan']  ?? 0);
        $body['jenis']                = $body['jenis'] ?? 'sp'; // default-kan ke SP kalau tidak dikirim
        $body['npwp']                 = isset($body['npwp']) ? ($body['npwp'] == '-' ? null : $body['npwp']) : null;

        if ($body['downPayment'] > $body['totalAkhir']) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'pesan' => 'Down Payment melebihi total akhir',
            ]);
        }
        if ($body['potonganHargaSatuan'] > 25) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'pesan' => 'Potongan harga satuan melebihi 25%',
            ]);
        }

        $alamatPengiriman = [
            'provinsi' => $body['provinsi'],
            'kabupaten' => $body['kabupaten'],
            'kecamatan' => $body['kecamatan'],
            'kelurahan' => $body['kelurahan'],
            'kodepos' => $body['kodepos'],
            'detail' => $body['detail'],
        ];
        $alamatTagihan = [
            'provinsi' => $body['provinsiTagihan'] ?? null,
            'kabupaten' => $body['kabupatenTagihan'] ?? null,
            'kecamatan' => $body['kecamatanTagihan'] ?? null,
            'kelurahan' => $body['kelurahanTagihan'] ?? null,
            'kodepos' => $body['kodeposTagihan'] ?? null,
            'detail' => $body['detailTagihan'] ?? null,
        ];
        $totalAkhir = $body['totalAkhir'];

        // --- gunakan mapping helper untuk tentukan prefix/id/status ---
        $normJenis = $this->normalizeJenis($body['jenis']);  // 'sale' | 'sp' | 'nf'
        $KODE_AWAL = $this->prefixByJenis($normJenis);       // 'SJ' | 'SP' | 'NF'

        $dataTerbaru = $this->pemesananOfflineModel
            ->like('id_pesanan', $KODE_AWAL, 'after')
            ->orderBy('id', 'desc')->first();
        $idFix = $KODE_AWAL . (sprintf("%08d", $dataTerbaru ? ((int)substr($dataTerbaru['id_pesanan'], 2) + 1) : 1));

        $tanggalNoStrip = date('Ymd', strtotime($body['tanggal']));

        // --- loop items ---
        foreach ($body['items'] as $item) {
            $produkCur = $this->barangModel->getBarang($item['id']);
            $varian    = json_decode($produkCur['varian'], true);
            $saldo     = 0;
            $varianBaru = $varian;

            foreach ($varian as $ind => $v) {
                if ($v['nama'] == $item['varian']) {
                    $saldo = (int)$v['stok'];
                    // potong stok sekarang jika perlu (sale tanpa DP / NF)
                    if ($this->shouldDeductStockNow($normJenis, $body['downPayment'])) {
                        $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - $item['jumlah']);
                    }
                }
            }

            $saldoAkhir = $saldo - $item['jumlah'];
            if ($this->shouldDeductStockNow($normJenis, $body['downPayment']) && $saldoAkhir < 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk produk ' . $item['varian'] . ' pada barang ' . $produkCur['nama'],
                ], false);
            }

            // update stok + kartu_stok HANYA jika harus potong sekarang
            if ($this->shouldDeductStockNow($normJenis, $body['downPayment'])) {
                $this->barangModel->where('id', $item['id'])->set([
                    'varian' => json_encode($varianBaru)
                ])->update();

                $this->kartuStokModel->insert([
                    'id_barang' => $item['id'],
                    'tanggal'   => $body['tanggal'],
                    'keterangan'=> $tanggalNoStrip . "-" . $item['id'] . "-" . str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $idFix,
                    'debit'     => 0,
                    'kredit'    => $item['jumlah'],
                    'saldo'     => $saldoAkhir,
                    'pending'   => false,
                    'id_pesanan'=> $idFix,
                    'varian'    => $item['varian'],
                ]);
            }

            // simpan detail items (tetap disimpan untuk semua jenis)
            for ($i = 0; $i < $item['jumlah']; $i++) {
                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan'    => $idFix,
                    'id_barang'     => $item['id'],
                    'harga'         => $item['harga'],
                    'varian'        => $item['varian'],
                    'special_price' => $body['potonganHargaSatuan'],
                    'id_return'     => ''
                ]);
            }
        }

        // --- tentukan tanggal_inv sesuai jenis ---
        // sale: pakai aturan lama (hanya kalau ada NPWP dan tanpa DP)
        // sp & nf: tidak ada faktur -> null
        $tanggal_inv = null;
        if ($normJenis === 'sale' || $normJenis === 'nf') {
            $tanggal_inv = $body['npwp'] ? ($body['downPayment'] > 0 ? null : $body['tanggal']) : null;
        }

        $data = [
            'nama'               => $body['nama'],
            'nohp'               => $body['nohp'],
            'alamat_pengiriman'  => $this->generateAlamat($alamatPengiriman),
            'alamat_tagihan'     => $body['npwp'] ? $this->generateAlamat($alamatTagihan) : null,
            'npwp'               => $body['npwp'] ? $body['npwp'] : null,
            'nama_npwp'          => $body['npwp'] ? $body['nama_npwp'] : null,
            'tanggal'            => $body['tanggal'],
            'tanggal_inv'        => $tanggal_inv,
            'id_pesanan'         => $idFix,
            'status'             => $this->defaultStatusByJenis($normJenis, $body['downPayment']),
            'jenis'              => $normJenis, // simpan 'sale' | 'sp' | 'nf'
            'total_akhir'        => $totalAkhir,
            'keterangan'         => $body['keterangan'],
            'po'                 => $body['po'] ? $body['po'] : null,
            'down_payment'       => (($normJenis === 'sale' || $normJenis === 'nf') && $body['downPayment'] > 0) ? $body['downPayment'] : null,
        ];
        $this->pemesananOfflineModel->insert($data);

        return $this->response->setStatusCode(200)->setJSON([
            'success'    => true,
            'id_pesanan' => $idFix,
        ], false);
    }

    public function actionBuatDP()
    {
        $tanggal   = $this->request->getVar('tanggal');
        $idPesanan = $this->request->getVar('id_pesanan');
        $npwp      = $this->request->getVar('npwp');
        $nama_npwp = $this->request->getVar('nama_npwp');

        $pesananDP = $this->pemesananOfflineModel->getPemesanan($idPesanan);
        if (!$pesananDP) {
            return redirect()->back()->with('msg', 'Pesanan sumber tidak ditemukan.');
        }

        // Jenis asli pesanan: 'sale' atau 'nf' (SP tidak relevan untuk DP)
        $jenisAsal = $pesananDP['jenis'] ?: 'sale';
        $prefix    = $this->prefixByJenis($jenisAsal); // SALE->SJ, NF->NF

        // Ambil items sumber
        $itemsDP = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $idPesanan])
            ->findAll();

        // Generate ID baru mengikuti prefix jenis asal
        $dataTerbaru = $this->pemesananOfflineModel
            ->like('id_pesanan', $prefix, 'after')
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = $dataTerbaru ? ((int)substr($dataTerbaru['id_pesanan'], 2) + 1) : 1;
        $idFix   = $prefix . sprintf('%08d', $nextNum);

        $tanggalNoStrip = date('Ymd', strtotime($tanggal));

        // Buat entri pesanan DP (mirror dari logika SALE, tapi pakai $prefix & $jenisAsal)
        $data = [
            'nama'              => $pesananDP['nama'],
            'nohp'              => $pesananDP['nohp'],
            'alamat_pengiriman' => $pesananDP['alamat_pengiriman'],
            'alamat_tagihan'    => $pesananDP['alamat_pengiriman'], // mengikuti kode lama
            'nama_npwp'         => $npwp ? $nama_npwp : null,
            'npwp'              => $npwp ?: null,
            'tanggal'           => $tanggal,
            // Faktur saat DP dibuat? mengikuti aturan SALE/NF: faktur kalau ada NPWP & TANPA DP
            'tanggal_inv'       => $npwp ? $tanggal : null,
            'id_pesanan'        => $idFix,
            'status'            => 'pending',   // DP invoice masih menunggu pelunasan
            'jenis'             => $jenisAsal,  // penting: pertahankan 'nf' jika asal NF
            'total_akhir'       => $pesananDP['total_akhir'],
            'keterangan'        => $pesananDP['keterangan'],
            'po'                => $pesananDP['po'],
            // Jadikan DP di SJ/NF asal ter‐offset (negatif di invoice DP)
            'down_payment'      => (int)$pesananDP['down_payment'] * -1,
        ];
        $this->pemesananOfflineModel->insert($data);

        // Tandai pesanan asal menjadi DP paid
        $this->pemesananOfflineModel
            ->where(['id_pesanan' => $idPesanan])
            ->set(['status' => 'DP paid'])
            ->update();

        // Kelompokkan items untuk pemotongan stok & duplikasi item
        $filter         = [];
        $itemsFiltered  = [];
        $counterJumlah  = [];

        foreach ($itemsDP as $i) {
            if (!in_array($i['id_barang'].'-'.$i['varian'], $filter)) {
                $filter[] = $i['id_barang'].'-'.$i['varian'];
                $counterJumlah[] = 1;
            } else {
                $counterJumlah[count($counterJumlah) - 1] += 1;
            }
        }

        $filter = [];
        foreach ($itemsDP as $i) {
            if (!in_array($i['id_barang'].'-'.$i['varian'], $filter)) {
                $itemsFiltered[] = array_merge($i, [
                    'dimensi' => json_decode($i['deskripsi'], true)['dimensi']['asli'],
                    'jumlah'  => $counterJumlah[count($itemsFiltered)]
                ]);
                $filter[] = $i['id_barang'].'-'.$i['varian'];
            }
        }

        // Potong stok saat buat invoice DP (sesuai perilaku SALE/NF dengan DP)
        foreach ($itemsFiltered as $i) {
            $produkCur = $this->barangModel->getBarang($i['id_barang']);
            $varian    = json_decode($produkCur['varian'], true);
            $saldo     = 0; $varianBaru = $varian;

            foreach ($varian as $ind => $v) {
                if ($v['nama'] == $i['varian']) {
                    $saldo = (int)$v['stok'];
                    $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - $i['jumlah']);
                }
            }
            $saldoAkhir = $saldo - $i['jumlah'];

            // update stok barang
            $this->barangModel->where('id', $i['id_barang'])->set([
                'varian' => json_encode($varianBaru)
            ])->update();

            // kartu stok
            $this->kartuStokModel->insert([
                'id_barang' => $i['id_barang'],
                'tanggal'   => $tanggal,
                'keterangan'=> $tanggalNoStrip . "-" . $i['id_barang'] . "-" . str_replace(' ', '-', strtoupper($i['varian'])) . "-" . $idFix,
                'debit'     => 0,
                'kredit'    => $i['jumlah'],
                'saldo'     => $saldoAkhir,
                'pending'   => false,
                'id_pesanan'=> $idFix,
                'varian'    => $i['varian'],
            ]);

            // duplikasi item ke invoice DP (tautkan id_return ke pesanan asal)
            for ($in = 0; $in < (int)$i['jumlah']; $in++) {
                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan'    => $idFix,
                    'id_barang'     => $i['id_barang'],
                    'harga'         => $i['harga'],
                    'varian'        => $i['varian'],
                    'special_price' => $i['special_price'],
                    'id_return'     => $idPesanan,
                ]);
            }
        }

        // Kembali ke listing sesuai jenis asal
        $segmentJenis = $jenisAsal === 'nf' ? 'nf' : 'sale';
        return redirect()->to('/admin/order/offline/' . $segmentJenis);
    }

    public function actionKoreksiSP()
    {
        $body = $this->request->getVar();
        $isiBody = [
            'index_items_selected' => $body['index_items_selected'],
            'id_pesanan' => $body['id_pesanan'],
            'tanggal' => $body['tanggal'],
            'provinsiTagihan' => $body['provinsi'],
            'kabupatenTagihan' => $body['kota'],
            'kecamatanTagihan' => $body['kecamatan'],
            'kodeposTagihan' => $body['kodepos'],
            'detailTagihan' => $body['detail'],
            'alamatTagihan' => $body['alamatTagihan'],
            'npwp' => $body['npwp'],
            'keterangan' => $body['keterangan'],
        ];

        $id_pesanan_SP = $body['id_pesanan'];
        $sp_current = $this->pemesananOfflineModel->getPemesanan($id_pesanan_SP);
        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan_SP])
            ->findAll();
        $alamatTagihan = $body['provinsi'] ? [
            'provinsi' => explode('-', $body['provinsi'])[1],
            'kabupaten' => explode('-', $body['kota'])[1],
            'kecamatan' => explode('-', $body['kecamatan'])[1],
            'kelurahan' => explode('-', $body['kodepos'])[0],
            'kodepos' => explode('-', $body['kodepos'])[1],
            'detail' => $body['detail'],
        ] : [];

        //generate id
        $dataTerbaru = $this->pemesananOfflineModel->like('id_pesanan', 'SJ', 'after')->orderBy('id', 'desc')->first();
        $dataTerbaruSK = $this->pemesananOfflineModel->like('id_pesanan', 'SK', 'after')->orderBy('id', 'desc')->first();
        $idSJ = 'SJ' . (sprintf("%08d", $dataTerbaru ? ((int)substr($dataTerbaru['id_pesanan'], 2) + 1) : 1));
        $idSK = 'SK' . (sprintf("%08d", $dataTerbaruSK ? ((int)substr($dataTerbaruSK['id_pesanan'], 2) + 1) : 1));
        $tanggalNoStrip = date('Ymd', strtotime($body['tanggal']));

        $indexItems = explode(',', $body['index_items_selected']);
        $totalAkhir = 0;
        foreach ($items as $ind_i => $item) {
            if ($indexItems[$ind_i] == '1') {
                $produkCur = $this->barangModel->getBarang($item['id_barang']);
                $varian = json_decode($produkCur['varian'], true);
                $saldo = 0;
                $varianBaru = $varian;
                foreach ($varian as $ind => $v) {
                    if ($v['nama'] == $item['varian']) {
                        $saldo = (int)$v['stok'];
                        $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - 1);
                    }
                }
                $this->pemesananOfflineItemModel->where(['id' => $item['id']])->set(['id_return' => $idSK])->update();
                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan' => $idSJ,
                    'id_barang' => $item['id_barang'],
                    'harga' => $item['harga'],
                    'varian' => $item['varian'],
                    'id_return' => $idSK
                ]);
                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan' => $idSK,
                    'id_barang' => $item['id_barang'],
                    'harga' => $item['harga'],
                    'varian' => $item['varian'],
                    'id_return' => ''
                ]);
                $this->kartuStokModel->insert([
                    'id_barang' => $item['id_barang'],
                    'tanggal' => $body['tanggal'],
                    'keterangan' => $tanggalNoStrip . "-" . $item['id_barang'] . "-" . str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $idSK,
                    'debit' => 1,
                    'kredit' => 0,
                    'saldo' => $saldo + 1,
                    'pending' => false,
                    'id_pesanan' => $idSK,
                    'varian' => $item['varian'],
                ]);
                $this->kartuStokModel->insert([
                    'id_barang' => $item['id_barang'],
                    'tanggal' => $body['tanggal'],
                    'keterangan' => $tanggalNoStrip . "-" . $item['id_barang'] . "-" . str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $idSJ,
                    'debit' => 0,
                    'kredit' => 1,
                    'saldo' => $saldo,
                    'pending' => false,
                    'id_pesanan' => $idSJ,
                    'varian' => $item['varian'],
                ]);
                $totalAkhir += (int)$item['harga'];
            }
        }

        $diskonVal = isset($body['diskon']) && $body['diskon'] !== '' ? (float)$body['diskon'] : 0;
        $diskon_koreksi = ($diskonVal / 100) * $totalAkhir;
        $dataSJ = [
            'nama' => $sp_current['nama'],
            'nohp' => $sp_current['nohp'],
            'alamat_pengiriman' => $sp_current['alamat_pengiriman'],
            'alamat_tagihan' => $body['npwp'] ? (isset($body['checkAlamat']) ? $body['alamatTagihan'] : $this->generateAlamat($alamatTagihan)) : null,
            'npwp' => $body['npwp'] ? $body['npwp'] : null,
            'nama_npwp' => $body['npwp'] ? $body['nama_npwp'] : null,
            'tanggal' => $body['tanggal'],
            'tanggal_inv' => $body['npwp'] ? $body['tanggal'] : null,
            'id_pesanan' => $idSJ,
            'status' => 'pending',
            'jenis' => 'sale',
            'total_akhir' => $totalAkhir - $diskon_koreksi,
            'keterangan' => $body['keterangan'],
            'po' => $sp_current['po'],
        ];
        $dataSK = [
            'nama' => $sp_current['nama'],
            'nohp' => $sp_current['nohp'],
            'alamat_pengiriman' => $sp_current['alamat_pengiriman'],
            // 'alamat_tagihan' => isset($body['checkAlamat']) ? $body['alamatTagihan'] : $this->generateAlamat($alamatTagihan),
            'tanggal' => $body['tanggal'],
            'id_pesanan' => $idSK,
            'status' => 'success',
            'jenis' => 'sale',
            'total_akhir' => $totalAkhir,
            'keterangan' => $body['keterangan'],
            'po' => $sp_current['po'],
        ];
        $this->pemesananOfflineModel->insert($dataSJ);
        $this->pemesananOfflineModel->insert($dataSK);
        return redirect()->to('/admin/order/offline/sale');
    }
    public function actionBuatInvoice()
    {
        $tanggal = $this->request->getVar('tanggal');
        $npwp = $this->request->getVar('npwp');
        $nama_npwp = $this->request->getVar('nama_npwp');
        $alamat = $this->request->getVar('alamat');
        $idPesanan = $this->request->getVar('id_pesanan');

        $this->pemesananOfflineModel->where(['id_pesanan' => $idPesanan])->set([
            'npwp' => $npwp,
            'nama_npwp' => $nama_npwp,
            'tanggal_inv' => $tanggal,
            'alamat_tagihan' => $alamat
        ])->update();
        session()->setFlashdata('msg', 'Invoice ' . $idPesanan . ' berhasil dibuat');
        $row = $this->pemesananOfflineModel->where('id_pesanan', $idPesanan)->select('jenis')->first();
        $seg = ($row && $row['jenis'] === 'nf') ? 'nf' : 'sale';
        return redirect()->to('/admin/order/offline/' . $seg);

    }
    public function benerinSurat()
    {
        $pemesanan = $this->pemesananOfflineModel->findAll();
        foreach ($pemesanan as $p) {
            $this->pemesananOfflineModel->where(['id' => $p['id']])->set(['tanggal_inv' => $p['tanggal']])->update();
        }
        dd('done');
    }

    public function actionAccOrderOffline($id_pesanan)
    {
        $this->pemesananOfflineModel->where(['id_pesanan' => $id_pesanan])->set(['status' => 'success'])->update();
        session()->setFlashdata('msg', 'Invoice ' . $id_pesanan . ' sudah lunas');
        $jenis = $this->pemesananOfflineModel->where('id_pesanan', $id_pesanan)->select('jenis')->first()['jenis'] ?? 'sale';
        return redirect()->to('/admin/order/offline/' . ($jenis === 'nf' ? 'nf' : 'sale'));

    }


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

}