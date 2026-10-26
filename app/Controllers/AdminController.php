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
use App\Models\PartnerModel;
use App\Services\AuditLogService;
use App\Services\FreeShippingService;
use App\Services\MetaCapiSettingsService;
use App\Services\AdminRbacService;
use App\Services\SpamAccountCleanupService;
use App\Services\R2ProductImageService;
use App\Services\HomeAssetManager;

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
    private const PRODUCT_IMAGE_MAX_BYTES = 4 * 1024 * 1024;
    private const PRODUCT_IMAGE_MAX_MB = 4;
    private const PRODUCT_IMAGE_MAX_FILES = 25;
    private const PRODUCT_IMAGE_ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

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
    protected $partnerModel;
    protected R2ProductImageService $r2ProductImages;
    protected HomeAssetManager $homeAssetManager;





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
        $this->partnerModel = new PartnerModel();
        $this->r2ProductImages = new R2ProductImageService();
        $this->homeAssetManager = new HomeAssetManager($this->r2ProductImages);
        
    }

    public function dashboard()
    {
        $testEmails = ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','adityaanugrah494@gmail.com','tipaun0605@gmail.com','uuua5021@gmail.com'];
        $websiteOrderBase = static function () use ($testEmails) {
            return (new PemesananModel())
                ->like('id_midtrans', 'IL', 'after')
                ->whereNotIn('email', $testEmails);
        };

        $allProducts = $this->barangModel->findAll();
        $activeProducts = array_values(array_filter($allProducts, static fn($item) => !empty($item['active'])));
        $lowStockProducts = [];
        foreach ($allProducts as $product) {
            $variants = json_decode($product['varian'] ?? '[]', true) ?: [];
            $totalStock = 0;
            foreach ($variants as $variant) {
                $totalStock += (int)($variant['stok'] ?? 0);
            }
            if ($totalStock <= 3) {
                $lowStockProducts[] = [
                    'id' => $product['id'] ?? '',
                    'nama' => $product['nama'] ?? '-',
                    'stok' => $totalStock,
                ];
            }
        }

        $recentOrders = $websiteOrderBase()
            ->orderBy('id', 'desc')
            ->findAll(6);

        $paidOrders = $websiteOrderBase()
            ->whereIn('status', ['Proses', 'Dikirim', 'Selesai'])
            ->findAll();
        $revenue = 0;
        foreach ($paidOrders as $order) {
            $mid = json_decode($order['data_mid'] ?? '{}', true) ?: [];
            $revenue += (int) round((float)($mid['gross_amount'] ?? 0));
        }

        $stats = [
            'totalProducts' => count($allProducts),
            'activeProducts' => count($activeProducts),
            'lowStock' => count($lowStockProducts),
            'websiteOrdersTodo' => $websiteOrderBase()->whereIn('status', ['Proses', 'Menunggu Pembayaran'])->countAllResults(),
            'waitingPayment' => $websiteOrderBase()->where('status', 'Menunggu Pembayaran')->countAllResults(),
            'paidOrders' => count($paidOrders),
            'revenue' => $revenue,
            'articles' => $this->artikelModel->countAllResults(),
            'activeVouchers' => (new VoucherModel())->where('aktif', 1)->countAllResults(),
        ];

        return view('admin/dashboard', [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => array_slice($lowStockProducts, 0, 6),
        ]);
    }

    private function ensurePartnerTable(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('partners')) {
            return;
        }

        $forge = \Config\Database::forge();
        $forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 160],
            'city' => ['type' => 'VARCHAR', 'constraint' => 120],
            'address' => ['type' => 'TEXT'],
            'maps_url' => ['type' => 'TEXT', 'null' => true],
            'image_url' => ['type' => 'TEXT', 'null' => true],
            'lat' => ['type' => 'DECIMAL', 'constraint' => '12,8', 'null' => true],
            'lng' => ['type' => 'DECIMAL', 'constraint' => '12,8', 'null' => true],
            'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('partners', true);
    }

    public function partners()
    {
        $this->ensurePartnerTable();
        if ($this->partnerModel->countAllResults() === 0) {
            $this->seedPartnersFromLegacyView();
        }

        return view('admin/partners', [
            'title' => 'Partner Ilena',
            'partners' => $this->partnerModel
                ->orderBy('sort_order', 'ASC')
                ->orderBy('name', 'ASC')
                ->findAll(),
            'editPartner' => ($id = $this->request->getGet('edit')) ? $this->partnerModel->find($id) : null,
        ]);
    }

    private function seedPartnersFromLegacyView(): void
    {
        $viewPath = APPPATH . 'Views/pages/mitra.php';
        if (!is_file($viewPath)) {
            return;
        }
        $source = file_get_contents($viewPath);
        if (!preg_match('/const\s+LEGACY_MITRA\s*=\s*\[(.*?)\];\s*const\s+MITRA/s', $source, $match)) {
            return;
        }

        preg_match_all('/\{\s*(.*?)\n\s*\}/s', $match[1], $objects);
        $now = date('Y-m-d H:i:s');
        $rows = [];
        $readString = static function (string $object, string $field): string {
            return preg_match('/\b' . preg_quote($field, '/') . '\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $object, $m)
                ? stripcslashes($m[1])
                : '';
        };

        foreach ($objects[1] as $index => $object) {
            $name = trim($readString($object, 'name'));
            $city = trim($readString($object, 'city'));
            $address = trim($readString($object, 'address'));
            if ($name === '' || $city === '' || $address === '') {
                continue;
            }

            $img = trim($readString($object, 'img'));
            $img = str_replace('<?= $apikey_img_ilena ?>', $this->apikey_img_ilena, $img);
            $lat = null;
            $lng = null;
            if (preg_match('/coords\s*:\s*\[\s*([-0-9.]+)\s*,\s*([-0-9.]+)\s*\]/', $object, $coord)) {
                $lat = (float) $coord[1];
                $lng = (float) $coord[2];
            }

            $rows[] = [
                'name' => $name,
                'city' => $city,
                'address' => $address,
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address),
                'image_url' => $img,
                'lat' => $lat,
                'lng' => $lng,
                'active' => 1,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            $this->partnerModel->insertBatch($rows);
        }
    }

    public function actionPartner($id = null)
    {
        $this->ensurePartnerTable();
        $data = $this->request->getPost();
        $now = date('Y-m-d H:i:s');
        $payload = [
            'name' => trim((string) ($data['name'] ?? '')),
            'city' => trim((string) ($data['city'] ?? '')),
            'address' => trim((string) ($data['address'] ?? '')),
            'maps_url' => trim((string) ($data['maps_url'] ?? '')),
            'image_url' => trim((string) ($data['image_url'] ?? '')),
            'lat' => $data['lat'] !== '' ? (float) $data['lat'] : null,
            'lng' => $data['lng'] !== '' ? (float) $data['lng'] : null,
            'active' => !empty($data['active']) ? 1 : 0,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'updated_at' => $now,
        ];

        if ($payload['name'] === '' || $payload['city'] === '' || $payload['address'] === '') {
            return redirect()->back()->with('error', 'Nama, kota, dan alamat wajib diisi.')->withInput();
        }
        if ($payload['maps_url'] === '') {
            $payload['maps_url'] = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($payload['address']);
        }
        if (!preg_match('#^https?://#i', $payload['maps_url'])) {
            $payload['maps_url'] = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($payload['address']);
        }
        if ($payload['image_url'] !== '' && !preg_match('#^https?://#i', $payload['image_url'])) {
            $payload['image_url'] = '';
        }

        if ($id) {
            $this->partnerModel->update($id, $payload);
            return redirect()->to('/admin/partners')->with('success', 'Partner berhasil diperbarui.');
        }

        $payload['created_at'] = $now;
        $this->partnerModel->insert($payload);
        return redirect()->to('/admin/partners')->with('success', 'Partner berhasil ditambahkan.');
    }

    public function deletePartner($id)
    {
        $this->ensurePartnerTable();
        $this->partnerModel->delete($id);
        return redirect()->to('/admin/partners')->with('success', 'Partner berhasil dihapus.');
    }
    
    public function listProduct()
    {
        $product = $this->barangModel->getBarangAdmin();
        $koleksi = $this->koleksiModel->findAll();
        foreach ($product as $index_p => $p) {
            $decodedVarian = json_decode($p['varian'] ?? '[]', true);
            $product[$index_p]['varian'] = is_array($decodedVarian) ? $decodedVarian : [];
            $product[$index_p]['allstok'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
                if (!is_array($v)) continue;
                $namaVarian = $v['nama'] ?? 'Varian';
                $stokVarian = $v['stok'] ?? 0;
                if ($ind_v == 0) $product[$index_p]['allstok'] .= $namaVarian . ' : ' . $stokVarian;
                else $product[$index_p]['allstok'] .= "\n" . $namaVarian . ' : ' . $stokVarian;
            }
            if ($product[$index_p]['allstok'] === '') {
                $product[$index_p]['allstok'] = '-';
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

    private function normalizeBulkProductIds($rawIds): array
    {
        if (is_string($rawIds)) {
            $rawIds = explode(',', $rawIds);
        }
        if (!is_array($rawIds)) {
            return [];
        }

        $ids = [];
        foreach ($rawIds as $id) {
            $id = trim((string) $id);
            if ($id !== '' && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
        }

        return array_slice($ids, 0, 20);
    }

    public function bulkEditProduct()
    {
        $ids = $this->normalizeBulkProductIds($this->request->getGet('ids'));
        if (empty($ids)) {
            return redirect()->to('/admin/product')->with('error', 'Pilih produk dulu sebelum edit massal.');
        }

        $products = $this->barangModel->whereIn('id', $ids)->findAll();
        $byId = [];
        foreach ($products as $product) {
            $byId[$product['id']] = $product;
        }
        $orderedProducts = [];
        foreach ($ids as $id) {
            if (isset($byId[$id])) {
                $orderedProducts[] = $byId[$id];
            }
        }

        if (empty($orderedProducts)) {
            return redirect()->to('/admin/product')->with('error', 'Produk yang dipilih tidak ditemukan.');
        }

        return view('admin/bulkEdit', [
            'title' => 'Edit Massal Produk',
            'products' => $orderedProducts,
            'ids' => array_column($orderedProducts, 'id'),
            'koleksi' => $this->koleksiModel->getKoleksi(),
            'jenis' => $this->jenisModel->getJenis(),
        ]);
    }

    public function actionBulkEditProduct()
    {
        $ids = $this->normalizeBulkProductIds($this->request->getPost('ids'));
        if (empty($ids)) {
            return redirect()->to('/admin/product')->with('error', 'Pilih produk dulu sebelum edit massal.');
        }

        $payload = [];
        $post = $this->request->getPost();

        if (!empty($post['update_harga'])) {
            $payload['harga'] = max(0, (int) preg_replace('/\D+/', '', (string) ($post['harga'] ?? '0')));
        }
        if (!empty($post['update_diskon'])) {
            $payload['diskon'] = min(100, max(0, (int) ($post['diskon'] ?? 0)));
        }
        if (!empty($post['update_active'])) {
            $payload['active'] = ($post['active'] ?? '0') === '1' ? '1' : '0';
        }
        if (!empty($post['update_kategori'])) {
            $row = $this->koleksiModel->where('id', $post['kategori'] ?? '')->first();
            if (!$row) {
                return redirect()->back()->with('error', 'Koleksi yang dipilih tidak valid.')->withInput();
            }
            $payload['kategori'] = $row['nama'];
        }
        if (!empty($post['update_subkategori'])) {
            $row = $this->jenisModel->where('id', $post['subkategori'] ?? '')->first();
            if (!$row) {
                return redirect()->back()->with('error', 'Jenis produk yang dipilih tidak valid.')->withInput();
            }
            $payload['subkategori'] = $row['nama'];
        }
        if (!empty($post['update_ruangan'])) {
            $payload['ruang_tamu'] = !empty($post['ruang_tamu']) ? '1' : '0';
            $payload['ruang_keluarga'] = !empty($post['ruang_keluarga']) ? '1' : '0';
            $payload['ruang_tidur'] = !empty($post['ruang_tidur']) ? '1' : '0';
        }

        if (empty($payload)) {
            return redirect()->back()->with('error', 'Centang minimal satu field yang mau diubah.')->withInput();
        }

        $payload['tgl_update'] = date('Y-m-d H:i:s');
        try {
            $atomic = new \App\Services\AtomicOperation(db_connect(), 'ilena-bulk-product-edit');
            $products = $this->barangModel->whereIn('id', $ids)->findAll();
            if (count($products) !== count($ids)) {
                throw new \RuntimeException('Sebagian produk yang dipilih sudah tidak tersedia; tidak ada perubahan yang disimpan.');
            }
            foreach ($products as $product) {
                if (!$this->barangModel->update($product['id'], $payload)) {
                    throw new \RuntimeException('Pembaruan produk ' . $product['id'] . ' gagal; seluruh perubahan dibatalkan.');
                }
            }
            $atomic->commit();
        } catch (\Throwable $e) {
            log_message('error', 'Bulk product update failed: {error}', ['error' => $e->getMessage()]);
            return redirect()->to('/admin/product')->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/product')->with('success', count($products) . ' produk berhasil diperbarui.');
    }
    public function listProductTable()
    {
        $product = $this->barangModel->orderBy('nama', 'asc')->findAll();
        foreach ($product as $index_p => $p) {
            $deskripsiArr = json_decode($p['deskripsi'] ?? '', true);
            $deskripsiArr = is_array($deskripsiArr) ? $deskripsiArr : [];
            $deskripsi = strip_tags((string) ($deskripsiArr['deskripsi'] ?? ''));
            $dimensiAsli = $deskripsiArr['dimensi']['asli'] ?? [];
            $dimensi = 'Dimensi : P(' . ($dimensiAsli['panjang'] ?? '-') . 'mm) x L(' . ($dimensiAsli['lebar'] ?? '-') . 'mm) x T(' . ($dimensiAsli['tinggi'] ?? '-') . 'mm) dengan berat ' . ($dimensiAsli['berat'] ?? '-') . 'kg';
            $product[$index_p]['deskripsi_nonhtml'] = $deskripsi . ' ' . $dimensi;
            $product[$index_p]['gambar'] = 'https://ilenafurniture.com/viewpichover/' . $p['id'];
            $decodedVariants = json_decode($p['varian'] ?? '[]', true);
            $product[$index_p]['varian'] = is_array($decodedVariants) ? $decodedVariants : [];
            $product[$index_p]['stok_total'] = 0;
            $product[$index_p]['warna'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
                if (!is_array($v)) continue;
                $product[$index_p]['stok_total'] += (int) ($v['stok'] ?? 0);
                $product[$index_p]['warna'] .= ($product[$index_p]['warna'] === '' ? '' : '/') . ucwords(strtolower((string) ($v['nama'] ?? 'Varian')));
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
    private function validateProductImageUploads(array $files)
    {
        $productImageCount = 0;
        $hoverImageCount = 0;
        foreach ($files as $field => $file) {
            if (!$file || !str_starts_with((string)$field, 'gambar_')) {
                continue;
            }

            if (method_exists($file, 'getError') && $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($field !== 'gambar_hover' && !preg_match('/^gambar_[0-9]{1,4}$/', (string) $field)) {
                return 'Urutan file gambar produk tidak valid.';
            }

            if ($field === 'gambar_hover') {
                $hoverImageCount++;
            } else {
                $productImageCount++;
            }

            if ($productImageCount > self::PRODUCT_IMAGE_MAX_FILES || $hoverImageCount > 1) {
                return 'Maksimal ' . self::PRODUCT_IMAGE_MAX_FILES . ' foto produk + 1 foto hover dalam sekali simpan.';
            }

            if (!$file->isValid()) {
                $error = method_exists($file, 'getErrorString') ? $file->getErrorString() : 'file tidak valid';
                return 'Upload gambar gagal pada ' . $field . ': ' . $error;
            }

            if ($file->getSize() > self::PRODUCT_IMAGE_MAX_BYTES) {
                return 'Ukuran gambar ' . $field . ' terlalu besar. Maksimal ' . self::PRODUCT_IMAGE_MAX_MB . 'MB per foto.';
            }

            $mime = $file->getMimeType();
            if (!in_array($mime, self::PRODUCT_IMAGE_ALLOWED_MIMES, true)) {
                return 'Format gambar ' . $field . ' tidak didukung. Gunakan JPG, PNG, WebP, atau AVIF.';
            }
        }

        return null;
    }

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

        $data               = $this->request->getVar();
        if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', (string) ($data['id'] ?? ''))) {
            return $this->response->setStatusCode(422)->setJSON(['pesan' => 'ID produk hanya boleh berisi huruf, angka, garis bawah, atau tanda hubung (maksimal 64 karakter).']);
        }
        $data_gambar_mentah = $this->request->getFiles();

        if ($error = \App\Services\ProductInputPolicy::validate($data)) {
            return $this->response->setStatusCode(422)->setJSON(['pesan' => $error]);
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis   = $this->jenisModel->getJenis();
        $categoryRow = $this->koleksiModel->where('id', $data['kategori'] ?? '')->first();
        $subcategoryRow = $this->jenisModel->where('id', $data['subkategori'] ?? '')->first();
        if (!$categoryRow || !$subcategoryRow) {
            return $this->response->setStatusCode(422)->setJSON(['pesan' => 'Koleksi dan jenis produk wajib dipilih dari daftar yang tersedia.']);
        }

        if ($uploadError = $this->validateProductImageUploads($data_gambar_mentah)) {
            return $this->response->setStatusCode(413)->setJSON(['pesan' => $uploadError]);
        }

        $this->ensureProductStorageReady();
        $this->ensureBarangScheduleColumns();

        $variants = json_decode($data['varian'] ?? '[]', true);
        if (!is_array($variants)) {
            return $this->response->setStatusCode(400)->setJSON([
                'pesan' => 'Data varian tidak valid'
            ]);
        }
        $toPublicPath = static function (string $relativePath): string {
            return rtrim(FCPATH, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
        };

        if (isset($data_gambar_mentah['gambar_hover']) && $data_gambar_mentah['gambar_hover']->isValid()) {
            $fp = 'imgdum/barang/hover';
            $data_gambar_mentah['gambar_hover']->move($fp, $data['id'] . '.webp');
            $hoverDest = $toPublicPath('uploads/product-images/hover/' . $data['id'] . '.webp');
            if (file_exists($hoverDest)) {
                unlink($hoverDest);
            }
            \Config\Services::image()
                ->withFile($fp . '/' . $data['id'] . '.webp')
                ->resize(300, 300, true, 'height')
                ->save($hoverDest);
            $this->uploadProductImageToR2($hoverDest, 'uploads/product-images/hover/' . $data['id'] . '.webp');
            @unlink($fp . '/' . $data['id'] . '.webp');
        }
        unset($data_gambar_mentah['gambar_hover']);

        $koleksiSelected = $categoryRow['nama'];
        $jenisSelected = $subcategoryRow['nama'];

        foreach ($data_gambar_mentah as $ind_g => $dG) {
            if ($ind_g === 'gambar_hover') continue;
            if (!str_starts_with((string)$ind_g, 'gambar_')) continue;
            if (!$dG || !$dG->isValid()) continue;

            $parts = explode('_', (string)$ind_g);
            $urutan = (int)($parts[1] ?? 0);
            $dG->move('imgdum');

            $dest3000 = $toPublicPath('uploads/product-images/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');
            $dest1000 = $toPublicPath('uploads/product-images/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');
            $dest300 = $toPublicPath('uploads/product-images/300/' . $data['id'] . '.webp');

            if (file_exists($dest3000)) {
                unlink($dest3000);
            }
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(3000, 3000, true, 'height')
                ->save($dest3000);
            $this->uploadProductImageToR2($dest3000, 'uploads/product-images/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');

            if (file_exists($dest1000)) {
                unlink($dest1000);
            }
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(1000, 1000, true, 'height')
                ->save($dest1000);
            $this->uploadProductImageToR2($dest1000, 'uploads/product-images/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');

            if ($urutan <= 0) {
                if (file_exists($dest300)) {
                    unlink($dest300);
                }
                \Config\Services::image()
                    ->withFile('imgdum/' . $dG->getName())
                    ->resize(300, 300, true, 'height')
                    ->save($dest300);
                $this->uploadProductImageToR2($dest300, 'uploads/product-images/300/' . $data['id'] . '.webp');
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
            'tgl_update'    => date('Y-m-d H:i:s', strtotime('+7 hours')),
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

        try {
            $stockTransaction = new \App\Services\AtomicOperation(
                db_connect(),
                'ilena-product-' . sha1((string) $data['id'])
            );
            if (!$this->barangModel->insert($insertDataBarang)) {
                throw new \RuntimeException('Data produk gagal disimpan.');
            }
            foreach ($variants as $variant) {
                $timestamp = date('Y-m-d H:i:s', strtotime('+7 hours'));
                $ledger = [
                    'id_barang' => $data['id'],
                    'tanggal' => $timestamp,
                    'keterangan' => date('YmdHis', strtotime($timestamp)) . '-' . $data['id'] . '-' . strtoupper($variant['nama']) . '-ADDPRODUCT',
                    'debit' => (int) $variant['stok'],
                    'kredit' => 0,
                    'saldo' => (int) $variant['stok'],
                    'pending' => false,
                    'id_pesanan' => 'ADDPRODUCT',
                    'varian' => strtoupper($variant['nama']),
                ];
                if (!$this->kartuStokModel->insert($ledger)) {
                    throw new \RuntimeException('Saldo awal varian gagal dicatat.');
                }
            }
            $stockTransaction->commit();
        } catch (\Throwable $e) {
            log_message('error', 'ADD PRODUCT TRANSACTION ERROR: {msg}', ['msg' => $e->getMessage()]);
            return $this->response->setStatusCode(500)->setJSON(['pesan' => 'Produk dan saldo awal gagal disimpan; perubahan data dibatalkan.']);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'dataYgDiInsertKeBarang' => $insertDataBarang,
                'pesan' => 'Berhasil menambahkan produk ' . $data['nama']
            ]);
    }


    private function ensureProductStorageReady(): void
    {
        foreach ([
            'imgdum',
            'imgdum/barang',
            'imgdum/barang/hover',
            'uploads/product-images',
            'uploads/product-images/300',
            'uploads/product-images/1000',
            'uploads/product-images/3000',
            'uploads/product-images/hover',
            'img/barang',
            'img/barang/300',
            'img/barang/1000',
            'img/barang/3000',
            'img/barang/hover',
        ] as $dir) {
            $paths = [$dir];
            if (str_starts_with($dir, 'uploads/') || str_starts_with($dir, 'img/')) {
                $paths[] = rtrim(FCPATH, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR
                    . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR);
            }
            foreach (array_unique($paths) as $path) {
                if (!is_dir($path)) {
                    @mkdir($path, 0775, true);
                }
                if (is_dir($path) && !is_writable($path)) {
                    @chmod($path, 0775);
                }
            }
        }
    }

    private function uploadProductImageToR2(string $localPath, string $relativePath): void
    {
        if (!$this->r2ProductImages->enabled() || !is_file($localPath)) {
            return;
        }

        $key = preg_replace('#^uploads/product-images/#', 'products/', ltrim(str_replace('\\', '/', $relativePath), '/'));
        $ok = $this->r2ProductImages->uploadFile($localPath, $key, 'image/webp');
        if (!$ok) {
            log_message('error', 'Upload gambar produk ke R2 gagal: {key}', ['key' => $key]);
        }
    }

    private function deleteLocalProductImages(string $productId): void
    {
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $productId);
        if ($safeId === '') {
            return;
        }

        $paths = [
            "uploads/product-images/300/{$safeId}.webp",
            "uploads/product-images/hover/{$safeId}.webp",
            "img/barang/300/{$safeId}.webp",
            "img/barang/hover/{$safeId}.webp",
        ];

        foreach ([
            'uploads/product-images/1000', 'uploads/product-images/3000',
            'img/barang/1000', 'img/barang/3000',
        ] as $imageDir) {
            $absoluteDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imageDir);
            foreach (glob($absoluteDir . DIRECTORY_SEPARATOR . $safeId . '-*.webp') ?: [] as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        foreach ($paths as $relativePath) {
            $fullPath = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    private function ensureBarangScheduleColumns(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('barang')) {
                return;
            }

            $forge = \Config\Database::forge();
            $fields = [];
            if (!$db->fieldExists('pakai_jadwal_diskon', 'barang')) {
                $fields['pakai_jadwal_diskon'] = [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'null' => false,
                ];
            }
            if (!$db->fieldExists('diskon_mulai', 'barang')) {
                $fields['diskon_mulai'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }
            if (!$db->fieldExists('diskon_selesai', 'barang')) {
                $fields['diskon_selesai'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }

            if (!empty($fields)) {
                $forge->addColumn('barang', $fields);
            }
        } catch (\Throwable $th) {
            log_message('error', 'Gagal memastikan kolom jadwal diskon barang: {err}', [
                'err' => $th->getMessage(),
            ]);
        }
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

        // Keep tgl_update in the form payload: the editor uses it as a stable cache key for previews.
        unset($product['gambar'], $product['active']);

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
            'title'            => 'Edit Produk',
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
            if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', (string) $id_product)) {
                return $this->response->setStatusCode(422)->setJSON(['pesan' => 'ID produk tidak valid.']);
            }

            $data  = $this->request->getVar();
            $files = $this->request->getFiles();

            if ($error = \App\Services\ProductInputPolicy::validate($data)) {
                return $this->response->setStatusCode(422)->setJSON(['pesan' => $error]);
            }
            if (isset($data['id']) && (string) $data['id'] !== (string) $id_product) {
                return $this->response->setStatusCode(422)->setJSON(['pesan' => 'ID produk tidak boleh diubah dari formulir edit.']);
            }
            $koleksiRow = $this->koleksiModel->where('id', $data['kategori'] ?? '')->first();
            $jenisRow = $this->jenisModel->where('id', $data['subkategori'] ?? '')->first();
            if (!$koleksiRow || !$jenisRow) {
                return $this->response->setStatusCode(422)->setJSON(['pesan' => 'Koleksi dan jenis produk wajib dipilih dari daftar yang tersedia.']);
            }

            if ($uploadError = $this->validateProductImageUploads($files)) {
                return $this->response->setStatusCode(413)->setJSON(['pesan' => $uploadError]);
            }

            $publicPath = function (string $path): string {
                return rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
            };
            $ensureDir = function (string $dir) use ($publicPath) {
                $path = $publicPath($dir);
                if (!is_dir($path)) { @mkdir($path, 0775, true); }
                if (!is_writable($path)) { @chmod($path, 0775); }
                if ((!is_dir($path) || !is_writable($path)) && !$this->r2ProductImages->enabled()) {
                    throw new \RuntimeException('Folder gambar belum writable: ' . $dir);
                }
            };
            $processUploadedImage = function ($file, array $outputs) use ($publicPath): void {
                $tmpDir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache';
                if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
                $tmpName = uniqid('product-upload-', true) . '.' . ($file->guessExtension() ?: $file->getExtension() ?: 'img');
                $file->move($tmpDir, $tmpName, true);
                $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;

                foreach ($outputs as $output) {
                    [$destination, $width, $height] = $output;
                    $destFile = $publicPath($destination);
                    $destDir = dirname($destFile);
                    if (!is_dir($destDir)) { @mkdir($destDir, 0775, true); }
                    if (!is_writable($destDir)) { @chmod($destDir, 0775); }
                    if (!is_writable($destDir) && !$this->r2ProductImages->enabled()) {
                        throw new \RuntimeException('Folder tujuan gambar belum writable: ' . $destination);
                    }

                    $stagingFile = $tmpDir . DIRECTORY_SEPARATOR . 'resize-' . uniqid('', true) . '.webp';
                    \Config\Services::image()
                        ->withFile($tmpFile)
                        ->resize($width, $height, true, 'height')
                        ->save($stagingFile);

                    if (!is_file($stagingFile) || filesize($stagingFile) <= 0) {
                        @unlink($stagingFile);
                        throw new \RuntimeException('File hasil resize kosong: ' . $destination);
                    }

                    if (is_writable($destDir)) {
                        @unlink($destFile);
                        if (!@copy($stagingFile, $destFile)) {
                            if (!$this->r2ProductImages->enabled()) {
                                @unlink($stagingFile);
                                throw new \RuntimeException('File hasil resize tidak tersimpan: ' . $destination);
                            }
                        } else {
                            @touch($destFile);
                        }
                    }
                    $this->uploadProductImageToR2(is_file($destFile) ? $destFile : $stagingFile, $destination);
                    @unlink($stagingFile);
                }

                @unlink($tmpFile);
            };
            // Upload admin disimpan di uploads/product-images supaya tidak ketimpa Git/deploy.
            $ensureDir('uploads/product-images/1000');
            $ensureDir('uploads/product-images/3000');
            $ensureDir('uploads/product-images/300');
            $ensureDir('uploads/product-images/hover');

            if (isset($files['gambar_hover']) && $files['gambar_hover'] && $files['gambar_hover']->isValid()) {
                $processUploadedImage($files['gambar_hover'], [
                    ["uploads/product-images/hover/{$id_product}.webp", 300, 300],
                ]);
            }

            if (!empty($files)) {
                foreach ($files as $field => $file) {
                    if ($field === 'gambar_hover') continue;
                    if (strpos($field, 'gambar_') !== 0) continue;
                    if (!$file || !$file->isValid()) continue;

                    $parts  = explode('_', $field);
                    $urutan = isset($parts[1]) ? (int)$parts[1] : 0;
                    $slot = $urutan + 1;
                    $processUploadedImage($file, [
                        ["uploads/product-images/3000/{$id_product}-{$slot}.webp", 3000, 3000],
                        ["uploads/product-images/1000/{$id_product}-{$slot}.webp", 1000, 1000],
                    ]);
                }
            }

            $kategoriNama = $koleksiRow['nama'];
            $subkategoriNama = $jenisRow['nama'];

            $deskripsi = $data['deskripsi'] ?? $barang['deskripsi'];
            if (is_array($deskripsi)) $deskripsi = json_encode($deskripsi);
            $varian = $data['varian'] ?? $barang['varian'];
            if (is_array($varian)) $varian = json_encode($varian);

            // Cover/thumbnail katalog mengikuti foto pertama dari urutan varian pertama.
            // Dibuat non-fatal: kalau folder 300 belum writable, data produk & gambar utama tetap tersimpan.
            try {
                $varianArrForThumb = json_decode((string)$varian, true) ?: [];
                $firstImageSlot = null;
                if (!empty($varianArrForThumb[0]['urutan_gambar'])) {
                    $slots = array_values(array_filter(array_map('trim', explode(',', (string)$varianArrForThumb[0]['urutan_gambar']))));
                    $firstImageSlot = isset($slots[0]) ? (int)$slots[0] : null;
                }
                if ($firstImageSlot) {
                    $toPublicPath = function (string $relativePath): string {
                        return rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
                    };
                    $thumbSource1000 = $toPublicPath("uploads/product-images/1000/{$id_product}-{$firstImageSlot}.webp");
                    $thumbSource3000 = $toPublicPath("uploads/product-images/3000/{$id_product}-{$firstImageSlot}.webp");
                    $legacyThumbSource1000 = $toPublicPath("img/barang/1000/{$id_product}-{$firstImageSlot}.webp");
                    $legacyThumbSource3000 = $toPublicPath("img/barang/3000/{$id_product}-{$firstImageSlot}.webp");
                    $thumbDest = $toPublicPath("uploads/product-images/300/{$id_product}.webp");
                    $thumbDir = dirname($thumbDest);
                    if (!is_dir($thumbDir)) { @mkdir($thumbDir, 0775, true); }
                    if (!is_writable($thumbDir)) { @chmod($thumbDir, 0775); }
                    $thumbSource = is_file($thumbSource1000) ? $thumbSource1000 : (is_file($thumbSource3000) ? $thumbSource3000 : (is_file($legacyThumbSource1000) ? $legacyThumbSource1000 : (is_file($legacyThumbSource3000) ? $legacyThumbSource3000 : null)));
                    if ($thumbSource && (is_writable($thumbDir) || $this->r2ProductImages->enabled())) {
                        $tmpDir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache';
                        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
                        $thumbTmp = $tmpDir . DIRECTORY_SEPARATOR . 'thumb-' . $id_product . '-' . uniqid('', true) . '.webp';
                        \Config\Services::image()
                            ->withFile($thumbSource)
                            ->resize(300, 300, true, 'height')
                            ->save($thumbTmp);
                        if (is_file($thumbTmp) && filesize($thumbTmp) > 0) {
                            if (is_writable($thumbDir)) {
                                @unlink($thumbDest);
                                if (!@copy($thumbTmp, $thumbDest) && !$this->r2ProductImages->enabled()) {
                                    throw new \RuntimeException('Thumbnail cover tidak tersalin ke ' . $thumbDest);
                                }
                                if (is_file($thumbDest)) {
                                    @touch($thumbDest);
                                }
                            }
                            $this->uploadProductImageToR2(is_file($thumbDest) ? $thumbDest : $thumbTmp, "uploads/product-images/300/{$id_product}.webp");
                            @unlink($thumbTmp);
                        } else {
                            @unlink($thumbTmp);
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'EDIT thumbnail cover gagal: {msg}', ['msg' => $e->getMessage()]);
            }

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
                'tgl_update'     => date('Y-m-d H:i:s', strtotime('+7 hours')),
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

            $stockTransaction=new \App\Services\AtomicOperation(db_connect(),'ilena-product-'.sha1((string)$id_product));
            $current=db_connect()->query('SELECT varian FROM barang WHERE id = ? FOR UPDATE',[$id_product])->getRowArray();
            if (!$current) throw new \RuntimeException('Produk sudah tidak tersedia.');
            $payload['varian']=json_encode(\App\Services\ProductStockPolicy::rebase(json_decode($current['varian'],true)?:[],json_decode($payload['varian'],true)?:[]));
            if (!$this->barangModel->update($id_product, $payload)) throw new \RuntimeException('Data produk gagal disimpan.');
            $stockTransaction->commit();

            return $this->response->setStatusCode(200)->setJSON([
                'pesan'   => 'Berhasil mengubah produk. Saldo stok mengikuti transaksi terbaru; perubahan stok melalui Mutasi.',
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
            $varian = explode('-', $data, 2)[1];
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
        try {
            $db=db_connect(); $atomic=new \App\Services\AtomicOperation($db,'ilena-stock-mutation');
            $parts=explode('-',(string)$this->request->getPost('barang'));
            $id=array_shift($parts); $index=array_pop($parts); $name=implode('-',$parts);
            $kind=(string)$this->request->getPost('jenis'); $qty=filter_var($this->request->getPost('nominal'),FILTER_VALIDATE_INT);
            if (!ctype_digit((string)$index)||!in_array($kind,['debit','kredit'],true)||$qty===false||$qty<=0) throw new \RuntimeException('Jenis/jumlah/varian mutasi tidak valid.');
            $product=$this->barangModel->getBarangForUpdate($id); $variants=json_decode($product['varian']??'[]',true); $index=(int)$index;
            if (!$product||!isset($variants[$index])||strcasecmp((string)$variants[$index]['nama'],$name)!==0) throw new \RuntimeException('Produk/varian tidak ditemukan.');
            $balance=(int)$variants[$index]['stok']+($kind==='debit'?$qty:-$qty);
            \App\Services\InventoryAvailability::assertRemaining($db,(string)$id,$name,$balance);
            $date=(string)$this->request->getPost('tanggal');
            if (strtotime($date)===false) throw new \RuntimeException('Tanggal tidak valid.');
            $date=date('Y-m-d H:i:s',strtotime($date)); $variants[$index]['stok']=$balance;
            if (!$this->barangModel->where('id',$id)->set(['varian'=>json_encode($variants)])->update()) throw new \RuntimeException('Stok gagal disimpan.');
            if (!$this->kartuStokModel->insert(['id_barang'=>$id,'tanggal'=>$date,'keterangan'=>date('YmdHis',strtotime($date)).'-'.$id.'-'.strtoupper($name).'-MANUALLY',
                'debit'=>$kind==='debit'?$qty:0,'kredit'=>$kind==='kredit'?$qty:0,'saldo'=>$balance,'pending'=>false,'id_pesanan'=>'MANUALLY',
                'alasan'=>mb_substr((string)$this->request->getPost('alasan'),0,255),'varian'=>$name])) throw new \RuntimeException('Kartu stok gagal disimpan.');
            $atomic->commit(); return redirect()->to('/admin/mutasi/'.$id.'-'.rawurlencode($name));
        } catch (\Throwable $e) { log_message('error','Stock mutation failed: {error}',['error'=>$e->getMessage()]); return redirect()->back()->with('msg','Mutasi dibatalkan: '.$e->getMessage()); }
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
        $db = \Config\Database::connect();
        $db->transBegin();

        $getMutasi = $this->kartuStokModel->where(['id' => $id])->first();
        if (!$getMutasi || !filter_var($getMutasi['pending'] ?? false, FILTER_VALIDATE_BOOLEAN) || trim((string)($getMutasi['alasan']??''))==='') {
            $db->transRollback();
            return redirect()->to('/admin/mutasiconfirm')->with('msg', 'Mutasi sudah diproses atau tidak ditemukan.');
        }

        // Claim the pending ledger row atomically. A repeated/double request
        // cannot apply the same stock delta a second time.
        $this->kartuStokModel->where(['id' => $id, 'pending' => true])->set(['pending' => false])->update();
        if ($db->affectedRows() !== 1) {
            $db->transRollback();
            return redirect()->to('/admin/mutasiconfirm')->with('msg', 'Mutasi sudah diproses atau sedang diproses.');
        }

        // Serialize approvals for the same product so concurrent, different
        // ledger rows cannot overwrite each other's JSON stock update.
        $produk = $db->query(
            'SELECT * FROM barang WHERE id = ? FOR UPDATE',
            [$getMutasi['id_barang']]
        )->getRowArray();
        $varianCurr = json_decode($produk['varian'] ?? '[]', true);
        if (!$produk || !is_array($varianCurr)) {
            $db->transRollback();
            return redirect()->to('/admin/mutasiconfirm')->with('msg', 'Produk atau data varian tidak valid.');
        }

        $varianDitemukan = false;
        $saldoFix = 0;
        foreach ($varianCurr as $ind_v => $v) {
            if (strcasecmp((string)($v['nama'] ?? ''), (string)$getMutasi['varian']) === 0) {
                $saldoFix = (int)($v['stok'] ?? 0) + ((int)($getMutasi['debit'] ?? 0) - (int)($getMutasi['kredit'] ?? 0));
                $varianCurr[$ind_v]['stok'] = $saldoFix;
                $varianDitemukan = true;
                break;
            }
        }
        if (!$varianDitemukan || $saldoFix < 0) {
            $db->transRollback();
            return redirect()->to('/admin/mutasiconfirm')->with('msg', $varianDitemukan ? 'Saldo melebihi batas minimal.' : 'Varian produk tidak ditemukan.');
        }
        try { \App\Services\InventoryAvailability::assertRemaining($db,(string)$getMutasi['id_barang'],(string)$getMutasi['varian'],$saldoFix); }
        catch (\RuntimeException $e) { $db->transRollback(); return redirect()->back()->with('msg',$e->getMessage()); }

        $this->kartuStokModel->where(['id' => $id])->set([
            'saldo' => $saldoFix,
            'alasan' => '',
        ])->update();
        $this->barangModel->where(['id' => $getMutasi['id_barang']])->set(['varian' => json_encode($varianCurr)])->update();

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/admin/mutasiconfirm')->with('msg', 'Mutasi gagal disimpan; stok tidak berubah.');
        }
        $db->transCommit();
        return redirect()->to('admin/mutasiconfirm');
    }
    public function denyMutasi($id)
    {
        $this->kartuStokModel->where(['id' => $id, 'pending' => true])->where('alasan !=','')->delete();
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
        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON(['pesan' => 'Produk tidak ditemukan'], false);
        }

        $requestedStatus = $this->request->getPost('active');
        if (!in_array((string) $requestedStatus, ['0', '1'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['pesan' => 'Status produk tidak valid.']);
        }

        $newStatus = (string) $requestedStatus;
        if (!$this->barangModel->where(['id' => $id_product])->set([
            'active' => $newStatus,
            'tgl_update' => date('Y-m-d H:i:s'),
        ])->update()) {
            return $this->response->setStatusCode(500)->setJSON(['pesan' => 'Status produk gagal disimpan.']);
        }

        return $this->response->setJSON([
            'pesan' => 'Ok',
            'active' => $newStatus,
        ], false);
    }
    public function deleteProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        if (!$product) {
            return redirect()->to('/admin/product')->with('error', 'Produk tidak ditemukan.');
        }

        $variantData = json_decode($product['varian'] ?? '[]', true);
        $imageSlots = [];
        foreach (is_array($variantData) ? $variantData : [] as $variant) {
            foreach (explode(',', (string) ($variant['urutan_gambar'] ?? '')) as $slot) {
                $slot = (int) trim($slot);
                if ($slot > 0 && $slot <= 9999) $imageSlots[] = $slot;
            }
        }

        try {
            $deleteTransaction = new \App\Services\AtomicOperation(db_connect(), 'ilena-product-delete-' . sha1((string) $id_product));
            if (!$this->barangModel->where('id', $id_product)->delete()) {
                throw new \RuntimeException('Data produk tidak berhasil dihapus.');
            }
            if (!$this->gambarBarangModel->where('id', $id_product)->delete()
                || !$this->gambarBarang3000Model->where('id', $id_product)->delete()) {
                throw new \RuntimeException('Data indeks gambar gagal dihapus; penghapusan dibatalkan.');
            }
            $deleteTransaction->commit();
        } catch (\Throwable $e) {
            log_message('error', 'Product delete failed: {error}', ['error' => $e->getMessage()]);
            return redirect()->to('/admin/product')->with('error', 'Produk belum dihapus. ' . $e->getMessage());
        }
        $this->r2ProductImages->deleteProductImagesForSlots((string) $id_product, $imageSlots);
        $this->deleteLocalProductImages((string) $id_product);
        return redirect()->to('/admin/product')->with('success', 'Produk berhasil dihapus.');
    }
    public function order()
    {
        $pesanan = $this->pemesananModel
            ->whereNotIn('email', ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','adityaanugrah494@gmail.com','tipaun0605@gmail.com','uuua5021@gmail.com'])
            ->orderBy('id', 'desc')
            ->findAll();
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
        $db=db_connect();
        try {
            $atomic=new \App\Services\AtomicOperation($db);
            $body=json_decode($this->request->getBody(),true);
            if (!is_array($body)||empty($body['keranjang'])||!is_array($body['keranjang'])) throw new \RuntimeException('Rincian pesanan tidak valid.');
            $receipt=\App\Services\SessionOperation::lookup('manual_order',$body['operation_token']??null);
            if ($receipt!==null) return $this->response->setJSON($receipt);
            $rows=$body['keranjang']; usort($rows,static fn($a,$b)=>(string)($a['id']??'')<=>(string)($b['id']??''));
            $items=[]; $total=0;
            foreach ($rows as $line) {
                if (!is_array($line)||!is_scalar($line['id']??null)) throw new \RuntimeException('Produk tidak valid.');
                $product=$this->barangModel->getBarangForUpdate($line['id']);
                $qty=filter_var($line['quantity']??null,FILTER_VALIDATE_INT);
                $variant=(string)($line['variant']??$line['detail']['varian']['nama']??'');
                if ($variant==='' && preg_match('/\(([^()]*)\)\s*$/',(string)($line['name']??''),$m)) $variant=$m[1];
                $found=false; foreach (json_decode($product['varian']??'[]',true)??[] as $v) if (strcasecmp((string)$v['nama'],$variant)===0) $found=true;
                if (!$product||!$found||$qty===false||$qty<1||$qty>10000) throw new \RuntimeException('Produk/varian/qty tidak valid.');
                // Manual sales may use an operator-entered price, but totals and stock
                // are always calculated/validated on the server.
                $price=$line['price']??\App\Services\CatalogPricing::unitPrice($product);
                if (!is_numeric($price)||(float)$price<0||(float)$price>2147483647) throw new \RuntimeException('Harga item tidak valid.');
                $price=(int)round((float)$price); $total+=$price*$qty;
                $items[]=['id'=>$product['id'],'variant'=>$variant,'name'=>$product['nama'].' ('.$variant.')','quantity'=>$qty,'price'=>$price];
            }
            $id=\App\Services\OrderIdentifiers::web();
            $timestamp=!empty($body['waktu']) ? str_replace('T',' ',(string)$body['waktu']) : (new \DateTimeImmutable('now',new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
            if (strtotime($timestamp)===false) throw new \RuntimeException('Tanggal tidak valid.');
            $waiting=!empty($body['stokTetap']);
            $order=['id_midtrans'=>$id,'nama'=>(string)($body['nama']??''),'email'=>(string)($body['email']??''),'nohp'=>(string)($body['nohp']??''),'alamat'=>(string)($body['alamatLengkap']??''),
                'items'=>json_encode($items),'data_mid'=>json_encode(['transaction_time'=>$timestamp,'order_id'=>$id,'gross_amount'=>$total,'payment_type'=>'admin','_app_inventory_effects_applied'=>false]),
                'kurir'=>json_encode(['nama'=>'Menyesuaikan','deskripsi'=>'Menyesuaikan','harga'=>0,'estimasi'=>'']),'resi'=>'Menunggu pengiriman','status'=>$waiting?'Menunggu Pembayaran':'Proses',
                'id_marketplace'=>(string)($body['idMarketplace']??''),'status_print'=>'siap','keterangan_suratjalan'=>(string)($body['keteranganSJ']??'')];
            if (!$this->pemesananModel->insert($order)) throw new \RuntimeException('Pesanan gagal disimpan.');
            if (!$waiting) {
                (new \App\Services\InventoryLedgerService($db))->apply($order);
                $mid=json_decode($order['data_mid'],true); $mid['_app_inventory_effects_applied']=true;
                $this->pemesananModel->where('id_midtrans',$id)->set(['data_mid'=>json_encode($mid)])->update();
                foreach ($items as $item) for($i=0;$i<$item['quantity'];$i++) $this->pemesananGudangModel->insert(['id_pesanan'=>$id,'tanggal'=>$timestamp,'nama'=>$item['name'],'id_barang'=>$item['id'],'packed'=>false,'printed'=>false]);
            }
            $atomic->commit();
            $receipt=['success'=>true,'order_id'=>$id];
            \App\Services\SessionOperation::complete('manual_order',$body['operation_token'],$receipt);
            return $this->response->setJSON($receipt);
        } catch (\Throwable $e) { log_message('error','Manual order failed: {error}',['error'=>$e->getMessage()]); return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>$e->getMessage()]); }
    }
    public function actionEditResi()
    {
        $db=db_connect(); $db->transBegin();
        $nama = $this->request->getPost('nama');
        $deskripsi = $this->request->getPost('deskripsi');
        $resi = $this->request->getPost('resi');
        $idMid = $this->request->getPost('idMid');
        $order=$db->query('SELECT status, data_mid FROM pemesanan WHERE id_midtrans = ? FOR UPDATE',[$idMid])->getRowArray();
        $mid=json_decode($order['data_mid']??'{}',true)?:[];
        if (!$order || !in_array($order['status'],['Proses','Dikirim'],true) || ($mid['_app_payment_effects_applied']??null)===false || trim((string)$resi)==='') {
            $db->transRollback();
            return redirect()->to('/admin/order/online')->with('msg','Resi hanya dapat diisi setelah pembayaran dan alokasi stok terkonfirmasi.');
        }
        $this->pemesananModel->where(['id_midtrans' => $idMid])->set([
            'kurir' => json_encode([
                'nama' => $nama,
                'deskripsi' => $deskripsi,
            ]),
            'resi' => $resi,
            'status' => 'Dikirim'
        ])->update();
        if (!$db->transStatus()) { $db->transRollback(); return redirect()->back()->with('msg','Resi gagal disimpan.'); }
        $db->transCommit();
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

    public function denyReprint($id_midtrans)
    {
        $this->ajukanPrintModel->where(['id_midtrans' => $id_midtrans])->delete();
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

    public function editMarketplace($id)
    {
        return redirect()
            ->to('/admin/marketplace')
            ->with('msg', 'Form edit marketplace belum tersedia. Gunakan detail order atau konfirmasi marketplace.');
    }

    public function confirmMarketplace($id)
    {
        $db = \Config\Database::connect();
        $atomic=new \App\Services\AtomicOperation($db,'ilena-confirm-'.sha1((string)$id));
        $db->transBegin();
        $pemesanan = $db->query('SELECT * FROM pemesanan WHERE id = ? FOR UPDATE', [$id])->getRowArray();
        if (!$pemesanan) {
            $db->transRollback();
            return redirect()->to('/admin/marketplace')->with('msg', 'Pesanan tidak ditemukan.');
        }

        $existingQueueRows = $this->pemesananGudangModel
            ->where('id_pesanan', $pemesanan['id_midtrans'])
            ->countAllResults();
        if ($existingQueueRows > 0) {
            $db->transRollback();
            return redirect()->to('/admin/marketplace')->with('msg', 'Pesanan sudah masuk antrean gudang.');
        }

        $items_curr = json_decode($pemesanan['items'] ?? '[]', true);
        $dataMidtrans = json_decode($pemesanan['data_mid'] ?? '[]', true);
        if (!is_array($items_curr) || !is_array($dataMidtrans) || empty($dataMidtrans['transaction_time'])) {
            $db->transRollback();
            return redirect()->to('/admin/marketplace')->with('msg', 'Data item atau waktu transaksi tidak valid.');
        }

        if (!in_array($dataMidtrans['payment_type'] ?? '', ['market','admin'], true)
            || !in_array($pemesanan['status'], ['Proses','Menunggu Pembayaran'], true)) {
            $db->transRollback();
            return redirect()->back()->with('msg','Pesanan ini bukan konfirmasi manual/marketplace.');
        }
        try {
            if (empty($dataMidtrans['_app_inventory_effects_applied'])) {
                (new \App\Services\InventoryLedgerService($db))->apply($pemesanan);
                $dataMidtrans['_app_inventory_effects_applied']=true;
                $this->pemesananModel->where('id',$pemesanan['id'])->set(['status'=>'Proses','data_mid'=>json_encode($dataMidtrans)])->update();
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error','Manual order stock confirmation failed: {error}',['error'=>$e->getMessage()]);
            return redirect()->back()->with('msg','Konfirmasi gagal: '.$e->getMessage());
        }

        foreach ($items_curr as $item) {
            if (($item['name'] ?? '') === 'Voucher' || ($item['name'] ?? '') === 'Biaya Admin') {
                continue;
            }
            $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
            if ($quantity === false || $quantity < 1 || empty($item['id']) || empty($item['name'])) {
                $db->transRollback();
                return redirect()->to('/admin/marketplace')->with('msg', 'Rincian produk pesanan tidak valid.');
            }
            for ($x = 0; $x < $quantity; $x++) {
                $this->pemesananGudangModel->insert([
                    'id_pesanan' => $pemesanan['id_midtrans'],
                    'tanggal' => $dataMidtrans['transaction_time'],
                    'nama' => $item['name'],
                    'id_barang' => $item['id'],
                    'packed' => false,
                ]);
            }
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/admin/marketplace')->with('msg', 'Antrean gudang gagal disimpan.');
        }
        $db->transCommit();
        $atomic->commit();
        return redirect()->to('/admin/marketplace');
    }

    public function orderToko($ind_add)
    {
        $sessionToken = (string)session()->get('shop_order_token');
        $requestToken = (string)$this->request->getPost('shop_order_token');
        if ($sessionToken === '' || $requestToken === '' || !hash_equals($sessionToken, $requestToken)) {
            return redirect()->to('/cart')->with('msg', 'Permintaan pemesanan sudah digunakan atau kedaluwarsa. Silakan ulangi dari pembayaran.');
        }

        $cart = session()->get('keranjang');
        $address = session()->get('alamatTerpilih');
        if (!is_array($cart) || $cart === [] || !is_array($address) || empty($address['alamat_lengkap'])) {
            return redirect()->to('/cart')->with('msg', 'Keranjang atau alamat pemesanan tidak valid.');
        }

        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $productIds = array_values(array_unique(array_map(static fn($row) => (int)($row['id_barang'] ?? 0), $cart)));
            sort($productIds, SORT_NUMERIC);
            $lockedProducts = [];
            foreach ($productIds as $productId) {
                if ($productId <= 0) throw new \RuntimeException('Produk pada keranjang tidak valid.');
                $product = $db->query('SELECT * FROM barang WHERE id = ? FOR UPDATE', [$productId])->getRowArray();
                if (!$product || (string)($product['active'] ?? '0') !== '1') {
                    throw new \RuntimeException('Ada produk yang sudah tidak tersedia.');
                }
                $variants = json_decode((string)($product['varian'] ?? ''), true);
                if (!is_array($variants)) throw new \RuntimeException('Data varian produk tidak valid.');
                $lockedProducts[$productId] = ['row' => $product, 'variants' => $variants];
            }

            $items = [];
            $stockUpdates = [];
            $total = 0;
            foreach ($cart as $cartRow) {
                $productId = (int)($cartRow['id_barang'] ?? 0);
                $quantity = filter_var($cartRow['jumlah'] ?? null, FILTER_VALIDATE_INT);
                $variantName = trim((string)($cartRow['varian'] ?? ''));
                if ($quantity === false || $quantity < 1 || $quantity > 99 || $variantName === '' || !isset($lockedProducts[$productId])) {
                    throw new \RuntimeException('Rincian produk atau jumlah pesanan tidak valid.');
                }

                $product = $lockedProducts[$productId]['row'];
                $variantIndex = null;
                foreach ($lockedProducts[$productId]['variants'] as $index => $variant) {
                    if (strcasecmp((string)($variant['nama'] ?? ''), $variantName) === 0) {
                        $variantIndex = $index;
                        break;
                    }
                }
                if ($variantIndex === null) throw new \RuntimeException('Varian produk sudah tidak tersedia.');

                $stock = (int)($lockedProducts[$productId]['variants'][$variantIndex]['stok'] ?? 0);
                if ($stock < $quantity) throw new \RuntimeException('Stok tidak cukup untuk ' . ($product['nama'] ?? 'produk') . ' (' . $variantName . ').');
                \App\Services\InventoryAvailability::assertRemaining($db,(string)$productId,(string)$variantName,$stock-$quantity);
                $lockedProducts[$productId]['variants'][$variantIndex]['stok'] = $stock - $quantity;

                $basePrice = max(0, (int)($product['harga'] ?? 0));
                $discount = max(0, min(100, (int)($product['diskon'] ?? 0)));
                $unitPrice = \App\Services\CatalogPricing::unitPrice($product);
                $description = json_decode((string)($product['deskripsi'] ?? ''), true);
                $length = $description['dimensi']['asli']['panjang'] ?? '';
                $itemName = trim((string)$product['nama'] . ' ' . $length . ' (' . $variantName . ')');
                $items[] = ['id' => $productId, 'price' => $unitPrice, 'quantity' => $quantity, 'name' => $itemName];
                $total += $unitPrice * $quantity;
            }

            foreach ($lockedProducts as $productId => $productState) {
                $this->barangModel->where('id', $productId)->set([
                    'varian' => json_encode($productState['variants'], JSON_UNESCAPED_UNICODE),
                ])->update();
            }

            $orderId = \App\Services\OrderIdentifiers::shop();
            $timestamp = date('Y-m-d H:i:s', strtotime('+7 hours'));
            $order = [
                'data_mid' => json_encode([
                    'transaction_time' => $timestamp,
                    'order_id' => $orderId,
                    'gross_amount' => $total,
                    'payment_type' => 'toko',
                ], JSON_UNESCAPED_UNICODE),
                'id_midtrans' => $orderId,
                'email' => (string)($address['email_pemesan'] ?? session()->get('email')),
                'kurir' => json_encode([]),
                'resi' => 'Menunggu pengiriman',
                'status_print' => 'siap',
                'keterangan_suratjalan' => '',
                'nama' => (string)($address['nama_penerima'] ?? session()->get('nama')),
                'nohp' => (string)($address['nohp_penerima'] ?? session()->get('nohp')),
                'alamat' => (string)$address['alamat_lengkap'],
                'status' => 'Proses',
                'id_marketplace' => '',
                'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
            ];
            if (!$this->pemesananModel->insert($order)) throw new \RuntimeException('Pesanan gagal disimpan.');

            foreach ($items as $item) {
                for ($unit = 0; $unit < (int)$item['quantity']; $unit++) {
                    if (!$this->pemesananGudangModel->insert([
                        'id_pesanan' => $orderId,
                        'tanggal' => $timestamp,
                        'nama' => $item['name'],
                        'id_barang' => $item['id'],
                        'packed' => false,
                    ])) throw new \RuntimeException('Antrean pesanan gagal disimpan.');
                }
            }

            if ($db->transStatus() === false) throw new \RuntimeException('Transaksi pemesanan gagal.');
            $db->transCommit();
            session()->remove('shop_order_token');
            return redirect()->to('/order')->with('msg', 'Pesanan toko berhasil dibuat.');
        } catch (\Throwable $e) {
            if ($db->transStatus()) $db->transRollback();
            log_message('error', 'Order toko gagal: {message}', ['message' => $e->getMessage()]);
            $message = $e instanceof \RuntimeException
                ? $e->getMessage()
                : 'Pemesanan gagal diproses. Silakan coba kembali.';
            return redirect()->back()->with('msg', $message);
        }
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
            'gambarHeader' => $gambarHeader,
            'homeAssetCatalog' => $this->homeAssetManager->catalog(),
            'homeAssetUrls' => $this->homeAssetManager->publicUrls(),
            'homeAssetRecords' => $this->homeAssetManager->records(),
        ];
        return view('admin/homeLayout', $data);
    }

    private function simpanFileHeaderSlider($image, int $id, string $device): ?string
    {
        if (!$image || !$image->isValid()) {
            return null;
        }

        if ($image->getSize() < 1 || $image->getSize() > 8_388_608) {
            log_message('warning', 'Slider upload rejected by source size limit for slot {slot}.', ['slot' => $id]);
            return null;
        }

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
        ];
        $mime = $image->getMimeType();
        $dimensions = @getimagesize($image->getTempName());
        if (!isset($allowedMimes[$mime]) || !is_array($dimensions)
            || !isset($allowedMimes[$dimensions['mime'] ?? ''])) {
            return null;
        }
        $width = (int) $dimensions[0];
        $height = (int) $dimensions[1];
        if ($width < 1 || $height < 1 || $width > 6000 || $height > 6000 || ($width * $height) > 25_000_000) {
            log_message('warning', 'Slider upload rejected by dimension limit for slot {slot}.', ['slot' => $id]);
            return null;
        }

        $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'slider';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileStem = 'header-' . $id . '-' . $device . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4));
        $optimizedFilename = $fileStem . '.webp';
        $optimizedPath = $uploadDir . DIRECTORY_SEPARATOR . $optimizedFilename;
        $maxWidth = $device === 'mobile' ? 1200 : 1920;
        $sourceBytes = @file_get_contents($image->getTempName());
        if (is_string($sourceBytes) && $sourceBytes !== '') {
            $optimizer = new \App\Services\ResponsiveImageOptimizer();
            foreach ([82, 76, 70, 64] as $quality) {
                $optimizedBytes = $optimizer->toWebp($sourceBytes, $maxWidth, $quality);
                if (is_string($optimizedBytes) && strlen($optimizedBytes) <= 900_000
                    && @file_put_contents($optimizedPath, $optimizedBytes, LOCK_EX) !== false) {
                    return 'uploads/slider/' . $optimizedFilename;
                }
            }
        }

        log_message('warning', 'Slider upload rejected because WebP optimization failed for slot {slot}.', ['slot' => $id]);

        return null;
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
        if ($path = $this->simpanFileHeaderSlider($image1, 1, 'desktop')) $dataUpdate['foto'] = $path;
        if ($path = $this->simpanFileHeaderSlider($image1hp, 1, 'mobile')) $dataUpdate['foto_hp'] = $path;
        $dataUpdate['url'] = $url1 ? $url1 : null;
        $this->gambarHeaderModel->where(['id' => 1])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($path = $this->simpanFileHeaderSlider($image2, 2, 'desktop')) $dataUpdate['foto'] = $path;
        if ($path = $this->simpanFileHeaderSlider($image2hp, 2, 'mobile')) $dataUpdate['foto_hp'] = $path;
        $dataUpdate['url'] = $url2 ? $url2 : null;
        $this->gambarHeaderModel->where(['id' => 2])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($path = $this->simpanFileHeaderSlider($image3, 3, 'desktop')) $dataUpdate['foto'] = $path;
        if ($path = $this->simpanFileHeaderSlider($image3hp, 3, 'mobile')) $dataUpdate['foto_hp'] = $path;
        $dataUpdate['url'] = $url3 ? $url3 : null;
        $this->gambarHeaderModel->where(['id' => 3])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($path = $this->simpanFileHeaderSlider($image4, 4, 'desktop')) $dataUpdate['foto'] = $path;
        if ($path = $this->simpanFileHeaderSlider($image4hp, 4, 'mobile')) $dataUpdate['foto_hp'] = $path;
        $dataUpdate['url'] = $url4 ? $url4 : null;
        $this->gambarHeaderModel->where(['id' => 4])->set($dataUpdate)->update();

        $assetMessages = [];
        foreach ($this->homeAssetManager->catalog() as $filename => $label) {
            $field = 'home_asset_' . pathinfo($filename, PATHINFO_FILENAME);
            $file = $this->request->getFile($field);
            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            try {
                $this->homeAssetManager->upload($filename, $file);
                $assetMessages[] = $label . ' tersimpan sebagai WebP.';
            } catch (\Throwable $error) {
                $assetMessages[] = $label . ' gagal: ' . $error->getMessage();
            }
        }

        session()->setFlashdata('msg', $assetMessages
            ? 'Home layout diperbarui. ' . implode(' ', $assetMessages)
            : 'Home layout telah diperbarui.');
        return redirect()->to('/admin/homelayout');
    }

    public function suratJalan($id_midtrans)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        if (!$pemesanan) return redirect()->to('/admin/order/online')->with('msg', 'Pesanan tidak ditemukan.');
        $items = [];

        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $pemesanan['kurir'] = json_decode($pemesanan['kurir'], true);
        $pemesanan['data_mid'] = json_decode($pemesanan['data_mid'], true);
        foreach ($pemesanan['items'] as $ind_i => $item) {
            if ($item['name'] != 'Voucher' && $item['name'] != 'Biaya Admin' && $item['name'] != 'Biaya Ongkir' && $item['name'] != 'Flash Sale') {
                $varianItem = (string)($item['variant'] ?? '');
                if ($varianItem === '' && preg_match('/\(([^()]*)\)\s*$/',(string)$item['name'],$variantMatch)) $varianItem = $variantMatch[1];
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
            'orderId' => $id_midtrans,
            'tanggal' => date("d", $tsPemesanan) . " " . $bulan[(int)date("m", $tsPemesanan) - 1] . " " . date("Y", $tsPemesanan),
            'items' => $items
        ];
        return view('gudang/suratJalan', $data);
    }

    public function markSuratJalanPrinted($id_midtrans)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        $order = $db->query('SELECT id, status, data_mid, status_print FROM pemesanan WHERE id_midtrans = ? FOR UPDATE', [$id_midtrans])->getRowArray();
        if (!$order) {
            $db->transRollback();
            return redirect()->to('/admin/order/online')->with('msg', 'Pesanan tidak ditemukan.');
        }
        $mid=json_decode($order['data_mid']??'{}',true)?:[];
        if (!in_array($order['status'],['Proses','Dikirim','Selesai'],true) || ($mid['_app_payment_effects_applied']??null)===false || $this->pemesananGudangModel->where('id_pesanan',$id_midtrans)->countAllResults()===0) {
            $db->transRollback(); return redirect()->back()->with('msg','Pesanan belum terbayar/teralokasi; status cetak tidak diubah.');
        }
        if (($order['status_print'] ?? '') === 'sudah print') {
            $db->transCommit();
            return redirect()->to('/admin/order/online')->with('msg', 'Surat jalan sudah ditandai tercetak.');
        }

        $this->pemesananModel->where('id', $order['id'])->set(['status_print' => 'sudah print'])->update();
        $this->kartuStokModel->where(['id_pesanan' => $id_midtrans, 'pending' => true])->set(['pending' => false])->update();
        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/admin/order/online')->with('msg', 'Status cetak gagal diperbarui.');
        }
        $db->transCommit();
        return redirect()->to('/admin/order/online')->with('msg', 'Surat jalan ditandai sudah dicetak.');
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
            $deskripsi = json_decode($b['deskripsi'] ?? '[]', true) ?: [];
            $barangLama[$ind_b]['dimensi'] = $deskripsi['dimensi']['asli'] ?? ['panjang' => '-'];
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
            ->whereIn('status', ['final', 'printed'])
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
        try {
            $keys=$this->request->getPost('item_keys');
            if (!is_array($keys)) throw new \RuntimeException('Pilih minimal satu item.');
            $id=(new \App\Services\ShipmentService(db_connect()))->createInterior($kodeProject,$keys);
            return redirect()->to('/admin/surat-jalan/offline/'.$id.'/edit')->with('msg','SJ draft dibuat. Isi qty lalu finalisasi.');
        } catch (\Throwable $e) { log_message('error','Interior SJ: {error}',['error'=>$e->getMessage()]); return redirect()->back()->with('msg','SJ gagal dibuat: '.$e->getMessage()); }
    }





    public function createSuratJalanOffline(string $idPesanan)
    {
        try {
            $id=(new \App\Services\ShipmentService(db_connect()))->createOffline($idPesanan);
            return redirect()->to('/admin/surat-jalan/offline/'.$id.'/edit')->with('msg','SJ draft dibuat. Periksa qty sebelum finalisasi.');
        } catch (\Throwable $e) { log_message('error','SJ create: {error}',['error'=>$e->getMessage()]); return redirect()->back()->with('msg','SJ gagal dibuat: '.$e->getMessage()); }
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
        try {
            $ids=$this->request->getPost('item_id'); $qty=$this->request->getPost('qty');
            if (!is_array($ids)||!is_array($qty)) throw new \RuntimeException('Payload item tidak valid.');
            (new \App\Services\ShipmentService(db_connect()))->save($suratJalanId,$ids,$qty,$this->request->getPost('tanggal'));
            return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')->with('msg','SJ berhasil disimpan.');
        } catch (\Throwable $e) { log_message('error','SJ save: {error}',['error'=>$e->getMessage()]); return redirect()->back()->with('msg','SJ gagal disimpan: '.$e->getMessage()); }
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
        try {
            (new \App\Services\ShipmentService(db_connect()))->finalize($suratJalanId);
            return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId)->with('msg','SJ final dan siap dicetak.');
        } catch (\Throwable $e) { log_message('error','SJ finalize: {error}',['error'=>$e->getMessage()]); return redirect()->back()->with('msg','Finalisasi gagal: '.$e->getMessage()); }
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
            ->whereIn('sj.status', ['final', 'printed'])
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
            ->whereIn('sj.status', ['final', 'printed'])
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

    public function freeShipping()
    {
        $service = new FreeShippingService();

        try {
            $provinsi = $this->provinsiModel->orderBy('label', 'asc')->findAll();
        } catch (\Throwable $th) {
            $provinsi = [];
        }

        return view('admin/freeShipping', [
            'title' => 'Gratis Ongkir',
            'config' => $service->getConfig(),
            'provinsi' => $provinsi,
            'msg' => session()->getFlashdata('msg'),
        ]);
    }

    public function actionFreeShipping()
    {
        $names = preg_split('/[\r\n,]+/', (string)$this->request->getPost('province_names'));
        $names = array_values(array_filter(array_map('trim', $names ?: [])));

        $service = new FreeShippingService();
        $service->save([
            'active' => (bool)$this->request->getPost('active'),
            'label' => $this->request->getPost('label'),
            'province_ids' => (array)$this->request->getPost('province_ids'),
            'province_names' => $names,
        ]);

        session()->setFlashdata('msg', 'Pengaturan gratis ongkir berhasil disimpan.');
        return redirect()->to('/admin/free-shipping');
    }

    public function metaCapi()
    {
        $service = new MetaCapiSettingsService();
        $config = $service->getConfig();

        return view('admin/metaCapi', [
            'title' => 'Meta CAPI',
            'config' => $config,
            'maskedToken' => $service->maskedToken((string)($config['access_token'] ?? '')),
            'msg' => session()->getFlashdata('msg'),
            'err' => session()->getFlashdata('err'),
        ]);
    }

    public function actionMetaCapi()
    {
        $service = new MetaCapiSettingsService();
        $current = $service->getConfig();

        $newToken = trim((string)$this->request->getPost('access_token'));
        $config = [
            'enabled' => (bool)$this->request->getPost('enabled'),
            'pixel_id' => $this->request->getPost('pixel_id'),
            'access_token' => $newToken !== '' ? $newToken : ($current['access_token'] ?? ''),
            'graph_version' => $this->request->getPost('graph_version'),
            'test_event_code' => $this->request->getPost('test_event_code'),
        ];

        $saved = $service->save($config);
        session()->setFlashdata(
            $saved ? 'msg' : 'err',
            $saved ? 'Pengaturan Meta CAPI berhasil disimpan.' : 'Pengaturan Meta CAPI gagal disimpan.'
        );

        return redirect()->to('/admin/meta-capi');
    }

    public function rbac()
    {
        $service = new AdminRbacService();

        return view('admin/rbac', [
            'title' => 'Role & Akses Admin',
            'permissions' => $service->permissions(),
            'roles' => $service->roles(),
            'assignments' => $service->assignments(),
            'decodePermissions' => fn($value) => $service->decodePermissions($value),
            'msg' => session()->getFlashdata('msg'),
            'err' => session()->getFlashdata('err'),
        ]);
    }

    public function actionRbacRole($id = null)
    {
        $service = new AdminRbacService();
        $name = trim((string)$this->request->getPost('role_name'));
        $permissions = (array)$this->request->getPost('permissions');

        if ($name === '') {
            session()->setFlashdata('err', 'Nama role harus diisi.');
            return redirect()->to('/admin/rbac');
        }

        $saved = $service->saveRole($id ? (int)$id : null, $name, $permissions);
        session()->setFlashdata($saved ? 'msg' : 'err', $saved ? 'Role berhasil disimpan.' : 'Role gagal disimpan.');
        return redirect()->to('/admin/rbac');
    }

    public function deleteRbacRole($id)
    {
        $deleted = (new AdminRbacService())->deleteRole((int)$id);
        session()->setFlashdata($deleted ? 'msg' : 'err', $deleted ? 'Role berhasil dihapus.' : 'Role gagal dihapus.');
        return redirect()->to('/admin/rbac');
    }

    public function assignRbacRole()
    {
        $emailRaw = trim((string)$this->request->getPost('email'));
        $roleId = (int)$this->request->getPost('role_id');

        if ($emailRaw === '' || $roleId <= 0) {
            session()->setFlashdata('err', 'Email dan role harus diisi.');
            return redirect()->to('/admin/rbac');
        }

        $emails = preg_split('/[\r\n,;]+/', $emailRaw);
        $emails = array_values(array_filter(array_map('trim', $emails ?: [])));
        $service = new AdminRbacService();
        $savedCount = 0;
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && $service->assignRole($email, $roleId)) {
                $savedCount++;
            }
        }

        $saved = $savedCount > 0;
        session()->setFlashdata($saved ? 'msg' : 'err', $saved ? 'Akses user berhasil disimpan.' : 'Akses user gagal disimpan.');
        return redirect()->to('/admin/rbac');
    }

    public function deleteRbacAssignment()
    {
        $email = trim((string)$this->request->getPost('email'));
        $deleted = (new AdminRbacService())->removeAssignment($email);
        session()->setFlashdata($deleted ? 'msg' : 'err', $deleted ? 'Akses user berhasil dihapus.' : 'Akses user gagal dihapus.');
        return redirect()->to('/admin/rbac');
    }

    public function syncRbacUserRoles()
    {
        $count = (new AdminRbacService())->syncAssignedUserRoles();
        session()->setFlashdata('msg', $count . ' akun RBAC disinkronkan ke role staff admin.');
        return redirect()->to('/admin/rbac');
    }

    public function spamCleanup()
    {
        $service = new SpamAccountCleanupService();
        $inactiveDays = max(1, (int)($this->request->getGet('inactive_days') ?? 7));

        return view('admin/spamCleanup', [
            'title' => 'Cleanup Spam Akun',
            'candidates' => $service->candidates(1000, $inactiveDays),
            'inactiveDays' => $inactiveDays,
            'msg' => session()->getFlashdata('msg'),
            'err' => session()->getFlashdata('err'),
        ]);
    }

    public function actionSpamCleanup()
    {
        $emails = (array)$this->request->getPost('emails');
        $deleted = (new SpamAccountCleanupService())->deleteEmails($emails);

        session()->setFlashdata(
            $deleted > 0 ? 'msg' : 'err',
            $deleted > 0 ? $deleted . ' akun spam berhasil dihapus.' : 'Belum ada akun spam yang dipilih/dihapus.'
        );

        return redirect()->to('/admin/spam-cleanup');
    }

    public function customers()
    {
        $page = max(1, (int)$this->request->getGet('page'));
        $search = trim((string)$this->request->getGet('q'));
        $builder = db_connect()->table('user u')
            ->select('u.email, u.role, u.active, u.suspended_until, u.suspension_reason, p.nama')
            ->join('pembeli p', 'p.email = u.email', 'left')
            ->whereIn('u.role', ['0', '4']);
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.email', $search)
                ->orLike('p.nama', $search)
                ->groupEnd();
        }
        $total = (clone $builder)->countAllResults();
        $rows = $builder->orderBy('u.email', 'ASC')->get(50, ($page - 1) * 50)->getResultArray();
        $suspensions = new \App\Services\AccountSuspensionService();
        foreach ($rows as &$row) {
            $row['suspension'] = $suspensions->state((string)$row['email']);
        }
        unset($row);

        return view('admin/customers', [
            'title' => 'Akun Pelanggan',
            'customers' => $rows,
            'search' => $search,
            'page' => $page,
            'pages' => max(1, (int)ceil($total / 50)),
            'msg' => session()->getFlashdata('msg'),
            'err' => session()->getFlashdata('err'),
        ]);
    }

    public function actionSuspendCustomer()
    {
        $email = strtolower(trim((string)$this->request->getPost('email')));
        $days = (int)$this->request->getPost('days');
        $reason = trim((string)$this->request->getPost('reason'));
        $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        $validReason = mb_strlen($reason) <= 500;
        $saved = $validEmail && $validReason
            && (new \App\Services\AccountSuspensionService())->suspendCustomer(
                $email,
                $days,
                $reason,
                (string)session()->get('email')
            );
        session()->setFlashdata($saved ? 'msg' : 'err', $saved
            ? 'Penangguhan akun berhasil disimpan.'
            : 'Penangguhan gagal. Periksa akun pelanggan, alasan, dan durasi (1–365 hari). Pastikan migrasi sudah dijalankan.');
        return redirect()->to('/admin/customers');
    }

    public function actionReinstateCustomer()
    {
        $email = strtolower(trim((string)$this->request->getPost('email')));
        $saved = filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            && (new \App\Services\AccountSuspensionService())->reinstateCustomer($email);
        session()->setFlashdata($saved ? 'msg' : 'err', $saved
            ? 'Penangguhan akun berhasil diakhiri.'
            : 'Akun tidak ditemukan atau penangguhan tidak dapat diakhiri.');
        return redirect()->to('/admin/customers');
    }

    public function activityLog()
    {
        $service = new AuditLogService();

        return view('admin/activityLog', [
            'title' => 'Log Aktivitas Admin',
            'logs' => $service->getRecent(250),
        ]);
    }



}
