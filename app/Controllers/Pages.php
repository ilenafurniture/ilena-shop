<?php

namespace App\Controllers;

use App\Services\Payments\PaymentSafety;

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
use App\Models\PartnerModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\VoucherModel;
use App\Models\GambarHeaderModel;
use App\Models\VoucherUsageModel;
use App\Libraries\MetaCapi;
use App\Services\ShippingService;
use App\Services\FreeShippingService;
use App\Services\AdminRbacService;
use App\Services\SpamAccountCleanupService;
use App\Services\CheckoutEmailPolicy;
use App\Services\R2ProductImageService;
use App\Services\HomeAssetManager;


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
    protected R2ProductImageService $r2ProductImages;

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
        $this->r2ProductImages = new R2ProductImageService();
        $this->provinsiModel = new ProvinsiModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->kecamatanModel = new KecamatanModel();
        $this->kelurahanModel = new KelurahanModel();
    }
    public function getNavbarData()
    {
        // This data is shared by public pages and only changes when catalog
        // metadata changes. Cache it briefly to avoid repeating a full
        // representative-product aggregation on every PHP request.
        $navbarCacheKey = 'ilena_public_navbar_data_v2';
        $cachedNavbar = cache()->get($navbarCacheKey);
        if (is_array($cachedNavbar)) {
            return $cachedNavbar;
        }

        // Ambil satu produk representatif per pasangan subkategori/kategori
        // dalam satu query, bukan query tambahan untuk setiap pasangan menu.
        $db = db_connect();
        $previewJoin = $db->tableExists('menu_collection_previews')
            ? "LEFT JOIN menu_collection_previews AS selected_menu
                    ON selected_menu.category_name COLLATE utf8mb4_general_ci = b.kategori COLLATE utf8mb4_general_ci
                   AND selected_menu.subcategory_name COLLATE utf8mb4_general_ci = b.subkategori COLLATE utf8mb4_general_ci
             LEFT JOIN barang AS selected_product
                    ON selected_product.id = selected_menu.product_id
                   AND selected_product.kategori = b.kategori
                   AND selected_product.subkategori = b.subkategori
                   AND selected_product.active = '1'"
            : '';
        $selectedProductId = $previewJoin !== '' ? 'COALESCE(selected_product.id, b.id)' : 'b.id';
        $hasSelectedPreview = $previewJoin !== '' ? 'selected_product.id IS NOT NULL AS has_selected_preview' : '0 AS has_selected_preview';
        $produkNavbar = $this->barangModel->query(
            "SELECT chosen.id, chosen.nama, chosen.deskripsi, chosen.varian, chosen.tgl_update,
                    {$hasSelectedPreview},
                    chosen.subkategori AS jenis, chosen.kategori AS koleksi
             FROM barang AS b
             INNER JOIN (
                 SELECT subkategori, kategori, MIN(id) AS id
                 FROM barang WHERE active = '1'
                 GROUP BY subkategori, kategori
             ) AS representative ON representative.id = b.id
             {$previewJoin}
             INNER JOIN barang AS chosen ON chosen.id = {$selectedProductId}
             WHERE b.active = '1' ORDER BY b.subkategori ASC, b.kategori ASC"
        )->getResultArray();
        $hasil = [];
        foreach ($produkNavbar as $produk) {
            $jenis = (string) ($produk['jenis'] ?? '');
            $koleksi = (string) ($produk['koleksi'] ?? '');
            $deskripsi = json_decode($produk['deskripsi'] ?? '{}', true) ?? [];
            $deskripsi['perawatan'] = '';
            $rawText = $deskripsi['deskripsi'] ?? '';
            $text = strip_tags($rawText);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = trim($text);
            $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);
            $deskripsi['deskripsi'] = $text;
            $varian = json_decode($produk['varian'] ?? '[]', true) ?: [];
            $coverSlot = '1';
            if (!empty($varian[0]['urutan_gambar'])) {
                $slots = array_values(array_filter(array_map('trim', explode(',', (string) $varian[0]['urutan_gambar']))));
                $coverSlot = $slots[0] ?? '1';
            }
            $itemKoleksi = [
                'id' => $produk['id'],
                'nama' => $produk['nama'],
                'deskripsi' => $deskripsi,
                'koleksi' => $koleksi,
                'cover_slot' => $coverSlot,
                'preview_image' => !empty($produk['has_selected_preview']) ? $this->menuPreviewImageUrl($produk, $coverSlot) : '',
            ];

            if (str_contains($jenis, 'dresser')) {
                $hasil['dresser'][$jenis][] = $itemKoleksi;
            } elseif (str_contains($jenis, 'bed')) {
                $hasil['bed'][$jenis][] = $itemKoleksi;
            } else {
                $hasil[$jenis][] = $itemKoleksi;
            }
        }
        cache()->save($navbarCacheKey, $hasil, 60);
        return $hasil;
    }

    private function menuPreviewImageUrl(array $product, string $slot): string
    {
        $id = rawurlencode((string) ($product['id'] ?? ''));
        $candidates = [
            'uploads/product-images/300/' . ($product['id'] ?? '') . '.webp',
            'img/barang/300/' . ($product['id'] ?? '') . '.webp',
            'uploads/product-images/1000/' . ($product['id'] ?? '') . '-' . $slot . '.webp',
            'img/barang/1000/' . ($product['id'] ?? '') . '-' . $slot . '.webp',
        ];
        foreach ($candidates as $relativePath) {
            $absolutePath = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
            if (is_file($absolutePath)) {
                return base_url($relativePath) . '?v=' . (string) filemtime($absolutePath);
            }
        }

        $storage = strtolower((string) env('PRODUCT_IMAGE_STORAGE', 'local'));
        $publicUrl = rtrim((string) env('R2_PUBLIC_URL', ''), '/');
        $version = !empty($product['tgl_update']) ? (string) strtotime((string) $product['tgl_update']) : '';
        if ($storage === 'r2' && $publicUrl !== '') {
            return $publicUrl . '/products/300/' . $id . '.webp' . ($version !== '' ? '?v=' . rawurlencode($version) : '');
        }

        return base_url('product-cover/' . $id) . '?slot=' . rawurlencode($slot) . '&size=300' . ($version !== '' ? '&v=' . rawurlencode($version) : '');
    }
    public function index()
    {
        $sliders = $this->gambarHeaderModel->findAll();
        $produk  = $this->barangModel->where('active','1')->orderBy('pengunjung', 'desc')->findAll(4, 0);
        $wishlist = $this->session->get('wishlist');
        if (!isset($wishlist)) {
            $wishlist = [];
        }
        $data = [
            'title' => 'Home',
            'navbar' => $this->getNavbarData(),
            'metaKeyword' => 'ilena Furniture, Toko Furniture, Sorely Ilena Semarang, Cabana Ilena Semarang, Orca Ilena Semarang, Plint Base Ilena Semarang, Cutout Ilena Semarang, Living Room Ilena Semarang, Bed Room Ilena Semarang, Lounge Room Ilena Semarang',
            'produk' => $produk,
            'wishlist' => $wishlist,
            'sliders' => $sliders,
            'homeAssets' => (new HomeAssetManager())->publicUrls(),
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
        $query = mb_substr(trim((string) $this->request->getVar('cari')), 0, 100);
        return redirect()->to('/find?' . http_build_query(['q' => $query]));
    }
    public function find($teks)
    {
        $cari = str_replace('-', ' ', (string) $teks);
        return redirect()->to('/find?' . http_build_query(['q' => $cari]));
    }

    private function normalizeSmartCatalogQuery(string $value): string
    {
        $ascii = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : $value;
        $value = mb_strtolower($ascii !== false ? $ascii : $value);
        return trim((string) preg_replace('/[^a-z0-9]+/u', ' ', $value));
    }

    private function rankSmartCatalog(string $query, array $products, bool $includeFallback = false): array
    {
        $synonyms = [
            ['mebel', 'meubel', 'furniture', 'furnitur', 'perabot'],
            ['sofa', 'couch', 'loveseat'],
            ['meja', 'table'],
            ['kursi', 'chair', 'stool'],
            ['lemari', 'cabinet', 'wardrobe'],
            ['rak', 'shelf', 'bookshelf', 'bookcase'],
            ['nakas', 'nightstand', 'bedside'],
            ['laci', 'drawer'],
            ['tv', 'televisi'],
        ];
        $stopWords = ['di', 'ke', 'dari', 'dan', 'yang', 'untuk', 'dengan', 'ada', 'cari', 'carikan', 'mencari', 'tolong', 'saya', 'produk', 'produkkan', 'please', 'find', 'the', 'a', 'an', 'of', 'for'];
        $queryWords = preg_split('/\s+/', $this->normalizeSmartCatalogQuery($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = array_values(array_unique(array_filter($queryWords, static fn (string $word): bool => strlen($word) > 1 && !in_array($word, $stopWords, true))));
        $ranked = [];

        foreach ($products as $product) {
            $variantNames = [];
            $variants = json_decode((string) ($product['varian'] ?? '[]'), true);
            if (is_array($variants)) {
                foreach ($variants as $variant) {
                    if (is_array($variant) && isset($variant['nama'])) $variantNames[] = (string) $variant['nama'];
                }
            }
            $fields = [
                [(string) ($product['nama'] ?? ''), 14],
                [(string) ($product['pencarian'] ?? ''), 11],
                [(string) ($product['kategori'] ?? ''), 8],
                [(string) ($product['subkategori'] ?? ''), 8],
                [implode(' ', $variantNames), 6],
                [(string) ($product['deskripsi'] ?? ''), 2],
            ];
            $normalizedFields = [];
            $candidateWords = [];
            foreach ($fields as [$field, $weight]) {
                $normalized = $this->normalizeSmartCatalogQuery($field);
                $words = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $normalizedFields[] = [$normalized, $words, $weight];
                $candidateWords = array_merge($candidateWords, $words);
            }
            $score = 0;
            $totalDistance = 0;
            foreach ($tokens as $token) {
                $equivalents = [$token];
                foreach ($synonyms as $group) if (in_array($token, $group, true)) $equivalents = array_merge($equivalents, $group);
                $best = 0;
                $bestDistance = PHP_INT_MAX;
                foreach ($normalizedFields as [$field, $words, $weight]) {
                    foreach (array_unique($equivalents) as $equivalent) {
                        if ($field !== '' && str_contains($field, $equivalent)) $best = max($best, $weight + 3);
                        foreach ($words as $word) {
                            $distance = levenshtein($equivalent, $word);
                            $bestDistance = min($bestDistance, $distance);
                            $maxDistance = strlen($equivalent) >= 8 ? 2 : (strlen($equivalent) >= 5 ? 1 : 0);
                            if ($distance <= $maxDistance) $best = max($best, $weight - $distance);
                        }
                    }
                }
                $score += $best;
                $totalDistance += $bestDistance === PHP_INT_MAX ? strlen($token) : $bestDistance;
            }
            if ($tokens === []) $score = 1;
            if ($score > 0 || $includeFallback) {
                $ranked[] = [
                    'product' => $product,
                    'score' => $score,
                    'distance' => $totalDistance,
                    'popularity' => (int) ($product['pengunjung'] ?? 0),
                ];
            }
        }

        usort($ranked, static function (array $a, array $b): int {
            return ($b['score'] <=> $a['score']) ?: (($a['distance'] <=> $b['distance']) ?: ($b['popularity'] <=> $a['popularity']));
        });
        return $ranked;
    }

    public function searchSuggestions()
    {
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 100);
        if (mb_strlen($this->normalizeSmartCatalogQuery($query)) < 2) {
            return $this->response->setJSON(['items' => []]);
        }
        $products = $this->barangModel->where('active', '1')->findAll();
        $ranked = array_slice($this->rankSmartCatalog($query, $products), 0, 7);
        $items = array_map(static function (array $item): array {
            $product = $item['product'];
            return [
                'name' => (string) ($product['nama'] ?? ''),
                'category' => trim((string) ($product['subkategori'] ?? '')),
                'collection' => trim((string) ($product['kategori'] ?? '')),
            ];
        }, $ranked);
        return $this->response->setJSON(['items' => $items]);
    }

    public function findSmart()
    {
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 100);
        $productRows = $this->barangModel->where('active', '1')->findAll();
        $queryForMatching = $query;
        $smartFilters = [];
        $normalizedQuery = $this->normalizeSmartCatalogQuery($query);

        $roomPatterns = [
            'tamu' => ['ruang tamu', 'living room', 'untuk tamu'],
            'keluarga' => ['ruang keluarga', 'family room'],
            'tidur' => ['ruang tidur', 'kamar tidur', 'bedroom'],
        ];
        if (empty($_GET['ruang'])) {
            foreach ($roomPatterns as $room => $phrases) {
                foreach ($phrases as $phrase) {
                    if (str_contains($normalizedQuery, $phrase)) {
                        $_GET['ruang'] = $room;
                        $smartFilters[] = 'Ruang ' . ucfirst($room);
                        $queryForMatching = str_ireplace($phrase, ' ', $queryForMatching);
                        break 2;
                    }
                }
            }
        }

        $matchedType = '';
        if (empty($_GET['jenis'])) {
            foreach ($this->jenisModel->findAll() as $type) {
                $typeName = trim((string) ($type['nama'] ?? ''));
                $typePhrase = $this->normalizeSmartCatalogQuery($typeName);
                if ($typePhrase !== '' && str_contains($normalizedQuery, $typePhrase)) {
                    $matchedType = str_replace(' ', '-', $typePhrase);
                    $_GET['jenis'] = $matchedType;
                    $smartFilters[] = 'Jenis ' . $typeName;
                    $queryForMatching = str_ireplace($typeName, ' ', $queryForMatching);
                    break;
                }
            }
        }

        if (empty($_GET['koleksi'])) {
            foreach ($this->koleksiModel->findAll() as $collection) {
                $collectionName = trim((string) ($collection['nama'] ?? ''));
                $collectionPhrase = $this->normalizeSmartCatalogQuery($collectionName);
                if ($collectionPhrase !== '' && str_contains($normalizedQuery, $collectionPhrase)) {
                    $_GET['koleksi'] = str_replace(' ', '-', $collectionPhrase);
                    $smartFilters[] = 'Koleksi ' . $collectionName;
                    $queryForMatching = str_ireplace($collectionName, ' ', $queryForMatching);
                    break;
                }
            }
        }

        $smartMaxPrice = null;
        $smartMinPrice = null;
        if (!empty($_GET['harga'])) {
            $smartFilters[] = 'Rentang harga';
        } elseif (preg_match('/\b(?:di bawah|dibawah|kurang dari|maksimal|max)\s*(\d+(?:[.,]\d+)?)\s*(juta|jt|juta-an|ribu|rb)?\b/u', $normalizedQuery, $priceMatch)) {
            $amount = (float) str_replace(',', '.', $priceMatch[1]);
            $unit = $priceMatch[2] ?? '';
            $smartMaxPrice = $amount * (in_array($unit, ['juta', 'jt', 'juta-an'], true) ? 1000000 : (in_array($unit, ['ribu', 'rb'], true) ? 1000 : 1));
            $queryForMatching = preg_replace('/\b(?:di bawah|dibawah|kurang dari|maksimal|max)\s*\d+(?:[.,]\d+)?\s*(?:juta|jt|juta-an|ribu|rb)?\b/u', ' ', $queryForMatching, 1) ?? $queryForMatching;
            $smartFilters[] = 'Harga hingga Rp ' . number_format($smartMaxPrice, 0, ',', '.');
        } elseif (preg_match('/\b(?:murah|terjangkau|hemat|budget)\b/u', $normalizedQuery, $cheapMatch)) {
            $smartMaxPrice = 5000000;
            $queryForMatching = preg_replace('/\b(?:murah|terjangkau|hemat|budget)\b/u', ' ', $queryForMatching, 1) ?? $queryForMatching;
            $smartFilters[] = 'Harga terjangkau (di bawah Rp 5.000.000)';
        } elseif (preg_match('/\b(?:mahal|premium)\b/u', $normalizedQuery)) {
            $smartMinPrice = 20000000;
            $queryForMatching = preg_replace('/\b(?:mahal|premium)\b/u', ' ', $queryForMatching, 1) ?? $queryForMatching;
            $smartFilters[] = 'Harga premium (mulai Rp 20.000.000)';
        }

        $ranked = $this->rankSmartCatalog($queryForMatching, $productRows);
        $ranked = array_values(array_filter($ranked, static function (array $item) use ($smartMaxPrice, $smartMinPrice): bool {
            $price = (float) ($item['product']['harga'] ?? 0);
            return ($smartMaxPrice === null || $price <= $smartMaxPrice) && ($smartMinPrice === null || $price >= $smartMinPrice);
        }));
        $products = array_column($ranked, 'product');
        $suggestions = [];
        if ($products === [] && $query !== '') {
            $fallback = $this->rankSmartCatalog($queryForMatching, $productRows, true);
            $suggestions = array_column(array_slice(array_filter($fallback, static fn (array $item): bool => $item['score'] > 0 || $item['distance'] <= 2), 0, 5), 'product');
            if ($suggestions === []) $suggestions = array_column(array_slice($this->rankSmartCatalog('', $productRows), 0, 5), 'product');
        }

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
            'produk' => $products,
            'smartSuggestions' => $suggestions,
            'smartFilters' => $smartFilters,
            'wishlist' => $wishlist,
            'find' => $query,
        ];
        return view('pages/all', $data);
    }

    private function fixId()
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
    private function fixSet()
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
    private function fixNama()
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

    private function gantiJenis($nama_lama, $nama_baru)
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
            $variantIndex = 0;
            if ($ind_nama !== false && $ind_nama !== null && $ind_nama !== '') {
                $parsedVariantIndex = filter_var($ind_nama, FILTER_VALIDATE_INT);
                if ($parsedVariantIndex === false) {
                    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                }
                $variantIndex = $parsedVariantIndex;
            }
            if ($variantIndex < 0 || !isset($productsemua[$variantIndex])) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            $product = $productsemua[$variantIndex];
            $product['deskripsi'] = json_decode($product['deskripsi'] ?? '{}', true) ?: [];
            $variants = json_decode($product['varian'] ?? '[]', true);
            $variants = is_array($variants) ? $variants : [];
            $variantsByName = [];
            foreach ($variants as $variantIndexInProduct => $variant) {
                if (!is_array($variant)) continue;
                $variantName = trim((string) ($variant['nama'] ?? ''));
                $variantKey = $variantName !== '' ? mb_strtolower($variantName) : 'unnamed-' . $variantIndexInProduct;
                if (!isset($variantsByName[$variantKey])) {
                    $variant['nama'] = $variantName !== '' ? $variantName : 'Varian ' . ($variantIndexInProduct + 1);
                    $variant['stok'] = max(0, (int) ($variant['stok'] ?? 0));
                    $variant['urutan_gambar'] = (string) ($variant['urutan_gambar'] ?? '1');
                    $variant['kode'] = (string) ($variant['kode'] ?? '#ddd');
                    $variantsByName[$variantKey] = $variant;
                    continue;
                }
                $existingSlots = array_filter(array_map('trim', explode(',', (string) ($variantsByName[$variantKey]['urutan_gambar'] ?? ''))));
                $duplicateSlots = array_filter(array_map('trim', explode(',', (string) ($variant['urutan_gambar'] ?? ''))));
                $variantsByName[$variantKey]['urutan_gambar'] = implode(',', array_values(array_unique(array_merge($existingSlots, $duplicateSlots))));
                $variantsByName[$variantKey]['stok'] = max((int) ($variantsByName[$variantKey]['stok'] ?? 0), (int) ($variant['stok'] ?? 0));
            }
            $product['varian'] = array_values($variantsByName);
            $ind_nama = $variantIndex;

            $produkSejenis = $this->barangModel
                ->where(['subkategori' => $product['subkategori']])
                ->where('id !=', $product['id'])
                ->orderBy('pengunjung', 'desc')
                ->findAll(8, 0);

            $seluruhBarangFilter = [];
            $namaTerpilih = [];
            foreach ($produkSejenis as $s) {
                $namaKey = 'nama:' . (string) $s['nama'];
                if (!isset($namaTerpilih[$namaKey])) {
                    array_push($seluruhBarangFilter, $s);
                    $namaTerpilih[$namaKey] = true;
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
                            ->set('pengunjung', 'COALESCE(pengunjung, 0) + 1', false)
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
        $namaTerpilih = [];
        foreach ($product as $s) {
            $namaKey = 'nama:' . (string) $s['nama'];
            if (!isset($namaTerpilih[$namaKey])) {
                array_push($seluruhBarangFilter, $s);
                $namaTerpilih[$namaKey] = true;
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
                        if ((int)$v['stok'] <= 0) {
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
            session()->setFlashdata('msg', 'Ada produk/varian yang tidak tersedia dan telah dikeluarkan dari keranjang. Periksa kembali pesanan Anda.');
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
            session()->setFlashdata('msg', 'Jumlah produk disesuaikan dengan stok terbaru. Periksa jumlah dan subtotal sebelum melanjutkan.');
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
                if (strcasecmp((string)$v['nama'], (string)$item['varian']) === 0) {
                    $slot = preg_replace('/[^0-9]/', '', (string)(explode(',', $v['urutan_gambar'])[0] ?? '1')) ?: '1';
                    $version = !empty($produk['tgl_update']) ? strtotime($produk['tgl_update']) : time();
                    $keranjang[$i]['src_gambar'] = "/viewvar/{$item['id_barang']}/{$slot}?v={$version}";
                    break;
                }
            }
            $keranjang[$i]['detail'] = $produk;
            $linePrice = \App\Services\CatalogPricing::unitPrice($produk) * $item['jumlah'];
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
        $varian = rawurldecode((string)$varian);
        $jumlah = max(1, (int)$jumlah);
        $product = $this->barangModel->getBarang($idbarang);
        if (!$product) {
            return redirect()->to('/cart')->with('msg', 'Produk sudah tidak tersedia. Keranjang tidak berubah.');
        }

        $variants = json_decode((string) ($product['varian'] ?? '[]'), true);
        $availableStock = null;
        foreach (is_array($variants) ? $variants : [] as $variant) {
            if (is_array($variant) && strcasecmp(trim((string) ($variant['nama'] ?? '')), trim($varian)) === 0) {
                $availableStock = max(0, (int) ($variant['stok'] ?? 0));
                break;
            }
        }
        if ($availableStock === null || $availableStock < 1) {
            return redirect()->to('/cart')->with('msg', 'Varian tidak tersedia atau stoknya habis. Keranjang tidak berubah.');
        }

        $keranjang = session()->get('keranjang') ?? [];
        $existingIndex = null;
        $existingQuantity = 0;
        foreach ($keranjang as $index => $item) {
            if ((string) ($item['id_barang'] ?? '') === (string) $idbarang
                && strcasecmp((string) ($item['varian'] ?? ''), $varian) === 0) {
                $existingIndex = $index;
                $existingQuantity = max(0, (int) ($item['jumlah'] ?? 0));
                break;
            }
        }

        $requestedTotal = $existingQuantity + $jumlah;
        $finalQuantity = min($availableStock, $requestedTotal);
        if ($existingIndex !== null) {
            $keranjang[$existingIndex]['jumlah'] = $finalQuantity;
        } else {
            $keranjang[] = [
                'id_barang' => $idbarang,
                'varian'    => $varian,
                'jumlah'    => $finalQuantity,
            ];
        }
        session()->set('keranjang', $keranjang);
        $this->syncCartToUser($keranjang);
        if ($requestedTotal > $availableStock) {
            session()->setFlashdata('msg', 'Jumlah disesuaikan dengan stok tersedia (' . $availableStock . ').');
        }
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
            'openAddAddressModal' => session()->getFlashdata('openAddAddressModal'),
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
        $checkPage = $this->request->getPost('checkpage') === 'account' ? 'account' : 'address';
        $redirectPath = $checkPage === 'account' ? '/account' : '/address';
        $emailPem = CheckoutEmailPolicy::normalize($this->request->getPost('emailPem'));
        if ($emailPem === null) {
            session()->setFlashdata('msg', 'Email tidak valid atau termasuk email sementara. Gunakan email pribadi yang aktif.');
            if ($checkPage === 'address') session()->setFlashdata('openAddAddressModal', true);
            return redirect()->to($redirectPath)->withInput();
        }
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
        $email = strtolower(trim((string)session()->get('email')));
        if ($email) {
            if ($email != $emailPem) {
                session()->setFlashdata('msg', 'Email yang dimasukan harus sesuai dengan email akun member Anda');
                return redirect()->to($redirectPath)->withInput();
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
        return redirect()->to($redirectPath);
    }
    public function deleteAddress($ind_add, $pathname)
    {
        $alamat = $this->session->get('alamat');
        $redirectPath = $pathname === 'account' ? '/account' : '/address';
        if (!is_array($alamat) || !array_key_exists($ind_add, $alamat)) {
            return redirect()->to($redirectPath);
        }
        unset($alamat[$ind_add]);
        $alamatBaru = array_values($alamat);
        $this->session->set(['alamat' => $alamatBaru]);

        $email = strtolower(trim((string)session()->get('email')));
        if ($email)
            $this->pembeliModel->where('email', $email)->set(['alamat' => json_encode($alamatBaru)])->update();
        return redirect()->to($redirectPath);
    }
    public function editAddress($ind_add)
    {
        $emailPem = CheckoutEmailPolicy::normalize($this->request->getPost('emailPem'));
        $nama = $this->request->getVar('nama');
        $nohp = $this->request->getVar('nohp');
        $provinsi = explode("-", $this->request->getVar('provinsiEdit'));
        $kota = explode("-", $this->request->getVar('kotaEdit'));
        $kecamatan = explode("-", $this->request->getVar('kecamatanEdit'));
        $kodepos = explode("-", $this->request->getVar('kodeposEdit'));
        $alamatAdd = $this->request->getVar('alamat_add');
        $pathnameUrl = in_array($this->request->getVar('url'), ['/address', '/account'], true)
            ? $this->request->getVar('url')
            : '/address';

        if ($emailPem === null) {
            session()->setFlashdata('msg', 'Email tidak valid atau termasuk email sementara. Gunakan email pribadi yang aktif.');
            return redirect()->to($pathnameUrl)->withInput();
        }

        $alamat = $this->session->get('alamat');
        if (!is_array($alamat) || !array_key_exists($ind_add, $alamat)) {
            session()->setFlashdata('msg', 'Alamat tidak ditemukan.');
            return redirect()->to($pathnameUrl);
        }
        $email = strtolower(trim((string)session()->get('email')));
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
        if (CheckoutEmailPolicy::normalize($alamatselected['email_pemesan'] ?? null) === null) {
            session()->setFlashdata('msg', 'Email pemesan tidak valid atau termasuk email sementara. Perbarui alamat dengan email pribadi yang aktif.');
            return redirect()->to('/address');
        }
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
        $kurir = (new FreeShippingService())->applyToRates($alamatselected, $kurir);

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
            'hargaKeseluruhan' => $hargaTotal + $kurir[0]['harga'],
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
            $lineTotal = \App\Services\CatalogPricing::unitPrice($produk) * $jumlah;
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

        $pendingId=(string)session()->get('checkout_pending_order_id');
        if ($pendingId!=='') {
            $pendingOrder=$this->pemesananModel->getPemesanan($pendingId);
            if ($pendingOrder && $this->canAccessOrder($pendingOrder) && in_array($pendingOrder['status'],['Menunggu Pembayaran','Proses','Dikirim','Selesai'],true)) {
                if ($pendingOrder['status']!=='Menunggu Pembayaran') session()->remove(['checkout_pending_order_id','checkout_operation_fingerprint','checkout_operation_key','checkout_order_id']);
                return redirect()->to('/orderdetail/'.rawurlencode(strtolower($pendingOrder['status'])).'?idorder='.rawurlencode($pendingId).'&access_token='.rawurlencode($this->orderAccessToken($pendingId,$pendingOrder)));
            }
            session()->remove(['checkout_pending_order_id','checkout_operation_fingerprint','checkout_operation_key','checkout_order_id']);
        }

        $keranjang = $this->session->get('keranjang');
        $alamatselected = $this->session->get('alamatTerpilih');
        $kurir = $this->session->get('kurir');

        if (!is_array($alamatselected) || empty($alamatselected)) return redirect()->to('/address');
        $paymentEmailCandidate = session()->get('isLogin') && (string)session()->get('email') !== '' && (string)session()->get('email') !== 'tamu'
            ? session()->get('email')
            : ($alamatselected['email_pemesan'] ?? null);
        if (CheckoutEmailPolicy::normalize($paymentEmailCandidate) === null
            || CheckoutEmailPolicy::normalize($alamatselected['email_pemesan'] ?? null) === null) {
            session()->setFlashdata('msg', 'Email pemesan tidak valid atau termasuk email sementara. Perbarui alamat dengan email pribadi yang aktif.');
            return redirect()->to('/address');
        }
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
        $kurirTerpilih = (new FreeShippingService())->applyToRates($alamatselected, [$kurirTerpilih])[0] ?? $kurirTerpilih;
        $hargaOngkir = (float)($kurirTerpilih['harga'] ?? 0);

        // ====== legacy voucher untuk email uji (tetap, tidak auto-apply) ======
        $voucher      = [];
        $emailUjiCoba = $this->midtransTestEmails();
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
        $checkoutNonce = bin2hex(random_bytes(32));
        session()->set('checkout_nonce', $checkoutNonce);
        $shopOrderToken = null;
        if ((string)session()->get('role') === '5'
            && (new AdminRbacService())->hasPermission((string)session()->get('email'), 'orders_online')
        ) {
            $shopOrderToken = (string)session()->get('shop_order_token');
            if ($shopOrderToken === '') {
                $shopOrderToken = bin2hex(random_bytes(32));
                session()->set('shop_order_token', $shopOrderToken);
            }
        }
        $checkoutFingerprint = \App\Services\CheckoutState::fingerprint($keranjang, $alamatselected, $kurirTerpilih, $method, session()->get('voucher'));
        $operationCreated = (int)session()->get('checkout_operation_created_at');
        if (!hash_equals((string)session()->get('checkout_operation_fingerprint'), $checkoutFingerprint)
            || $operationCreated <= 0 || time() - $operationCreated >= 285) {
            session()->set([
                'checkout_operation_fingerprint' => $checkoutFingerprint,
                'checkout_operation_created_at' => time(),
                'checkout_operation_key' => bin2hex(random_bytes(16)),
                'checkout_order_id' => \App\Services\OrderIdentifiers::web(),
            ]);
        }

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
            'emailUji'          => session()->get('isLogin') && session()->get('email') !== 'tamu' && PaymentSafety::isTestEmail((string)session()->get('email'), $emailUjiCoba),
            'msg'               => session()->getFlashdata('msg'),
            'hargaOngkir'       => $hargaOngkir,
            'kurir'             => $kurirTerpilih,
            'flashSale'         => $flashSale,
            'biayaAdmin'        => $biayaAdmin,
            'paymentMethod'     => $method,
            'listPaymentMethod' => ['bca','bri','bni','mandiri','permata','cimb','gopay','shopeepay','qris'],
            'grossAmount'       => $grossAmount,
            'checkoutNonce'     => $checkoutNonce,
            'shopOrderToken'    => $shopOrderToken,
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
            $allowedEmails = is_array($list) ? array_map(static fn($value) => strtolower(trim((string) $value)), $list) : [];
            if (!in_array(strtolower(trim($email)), $allowedEmails, true)) {
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

            // list_email is the allow-list for target=spesifik, not a usage ledger.
            // Usage history belongs to voucher_usage; retain the legacy fallback only
            // for older non-targeted vouchers.
            if (strtolower((string) ($row['target'] ?? 'semua')) !== 'spesifik') {
                $listEmail = json_decode($row['list_email'] ?? '[]', true);
                $usedEmails = is_array($listEmail) ? array_map('strtolower', $listEmail) : [];
                if (in_array(strtolower(trim($email)), $usedEmails, true)) {
                    return ['ok'=>false,'msg'=>'Voucher ini sudah pernah kamu pakai.'];
                }
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
            $allowedEmails = is_array($wl) ? array_map(static fn($value) => strtolower(trim((string) $value)), $wl) : [];
            if (!in_array(strtolower(trim($email)), $allowedEmails, true)) {
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
        return ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','adityaanugrah494@gmail.com','tipaun0605@gmail.com','uuua5021@gmail.com'];
    }

    private function midtransServerKey(string $email = '', ?string $environment = null): string
    {
        $sandbox = $environment === null ? PaymentSafety::isTestEmail($email, $this->midtransTestEmails()) : $environment === 'sandbox';
        return $sandbox ? (string)env('MIDTRANS_SANDBOX_KEY', '') : (string)env('MIDTRANS_PRODUCTION_KEY', '');
    }

    private function isSandboxOrder(array $order): bool
    {
        return PaymentSafety::environment($order, $this->midtransTestEmails(), 'email') === 'sandbox';
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
        if (!empty($item['variant']) && is_string($item['variant'])) return trim($item['variant']);
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
        $orderId = (string)($order['id_midtrans'] ?? '');
        if ($orderId === '') return;
        $db = \Config\Database::connect();
        $alreadyQueued = $this->pemesananGudangModel->where('id_pesanan', $orderId)->countAllResults();
        if ($alreadyQueued > 0) {
            (new \App\Services\Payments\StockReservationService($db))->consume($orderId);
            return;
        }

        $items = json_decode((string)($order['items'] ?? '[]'), true);
        $dataMid = json_decode((string)($order['data_mid'] ?? '[]'), true);
        if (!is_array($items)) throw new \RuntimeException('Invalid paid-order items.');
        $tanggal = is_array($dataMid) ? ($dataMid['transaction_time'] ?? date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
        $items = array_values(array_filter($items, fn($item) => $this->isChargeableProductItem($item)));
        usort($items, fn($a, $b) => [(string)($a['id'] ?? ''), strtolower($this->itemVariantName($a))]
            <=> [(string)($b['id'] ?? ''), strtolower($this->itemVariantName($b))]);

        foreach ($items as $item) {
            $productId = (string)($item['id'] ?? '');
            $variantName = $this->itemVariantName($item);
            $qty = max(0, (int)($item['quantity'] ?? 0));
            if ($productId === '' || $variantName === '' || $qty <= 0) continue;

            // The same product-row lock is used during reserve(), so a paid order and a
            // competing checkout cannot both consume the final available unit.
            $db->query('SELECT id FROM barang WHERE id = ? FOR UPDATE', [$productId]);
            $barang = $this->barangModel->getBarangAdmin($productId);
            if (!$barang) throw new \RuntimeException('Paid order product no longer exists: ' . $productId);
            $varians = json_decode((string)($barang['varian'] ?? '[]'), true);
            if (!is_array($varians)) throw new \RuntimeException('Paid order variant data is invalid: ' . $productId);

            $saldo = null;
            foreach ($varians as $idx => $variant) {
                if (strtolower((string)($variant['nama'] ?? '')) === strtolower($variantName)) {
                    $currentStock = max(0, (int)($variant['stok'] ?? 0));
                    if ($currentStock < $qty) throw new \RuntimeException('Paid order stock underflow prevented for ' . $productId . '/' . $variantName);
                    \App\Services\InventoryAvailability::assertRemaining($db,$productId,$variantName,$currentStock-$qty,$orderId);
                    $varians[$idx]['stok'] = $currentStock - $qty;
                    $saldo = $varians[$idx]['stok'];
                    break;
                }
            }
            if ($saldo === null) throw new \RuntimeException('Paid order variant no longer exists: ' . $productId . '/' . $variantName);
            if (!$this->barangModel->update($barang['id'], ['varian' => json_encode($varians)])) {
                throw new \RuntimeException('Could not persist paid-order stock for ' . $productId);
            }

            for ($x = 1; $x <= $qty; $x++) {
                $this->pemesananGudangModel->insert([
                    'id_pesanan' => $orderId,
                    'tanggal' => $tanggal,
                    'nama' => $item['name'] ?? $barang['nama'],
                    'id_barang' => $productId,
                    'packed' => false,
                    'printed' => false,
                ]);
            }
            $this->kartuStokModel->insert([
                'id_barang' => $productId,
                'tanggal' => $tanggal,
                'keterangan' => date('YmdHis', strtotime($tanggal)) . '-' . $productId . '-' . strtoupper($variantName) . '-' . $orderId,
                'debit' => 0,
                'kredit' => $qty,
                'saldo' => $saldo,
                'pending' => true,
                'id_pesanan' => $orderId,
                'varian' => strtoupper($variantName),
            ]);
        }

        (new \App\Services\Payments\StockReservationService($db))->consume($orderId);
    }

    private function syncOrderToIlenaSistem(array $order): void
    {
        if ((string)env('ILENA_SYSTEM_WEB_ORDER_ENABLED', 'false') !== 'true') {
            return;
        }

        $url = trim((string)env('ILENA_SYSTEM_WEB_ORDER_URL', ''));
        $token = trim((string)env('ILENA_SYSTEM_WEB_ORDER_TOKEN', ''));
        if ($url === '' || $token === '') {
            log_message('error', 'Sync Sistem Ilena dilewati: URL/token belum diisi.');
            return;
        }

        $items = json_decode($order['items'] ?? '[]', true);
        if (!is_array($items)) $items = [];

        $kurir = json_decode($order['kurir'] ?? '[]', true);
        if (!is_array($kurir)) $kurir = [];

        $dataMid = json_decode($order['data_mid'] ?? '[]', true);
        if (!is_array($dataMid)) $dataMid = [];
        unset($dataMid['_app_order_access_token']);

        $payload = [
            'order_id' => (string)($order['id_midtrans'] ?? ''),
            'status' => (string)($order['status'] ?? ''),
            'nama_pen' => (string)($order['nama'] ?? ''),
            'email' => (string)($order['email'] ?? ''),
            'hp_pen' => (string)($order['nohp'] ?? ''),
            'alamat_pen' => (string)($order['alamat'] ?? ''),
            'items' => $items,
            'kurir' => $kurir,
            'data_mid' => $dataMid,
            'is_test' => $this->isSandboxOrder($order),
            'payment_environment' => $this->isSandboxOrder($order) ? 'sandbox' : 'production',
            'source' => 'ilenafurniture.com',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Ilena-Webhook-Token: ' . $token,
            ],
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'Sync order ke Sistem Ilena gagal untuk {order}: HTTP {http} ERR {err} RESP {resp}', [
                'order' => (string)($order['id_midtrans'] ?? ''),
                'http' => $httpCode,
                'err' => $err,
                'resp' => substr((string)$response, 0, 500),
            ]);
            return;
        }

        log_message('info', 'Sync order ke Sistem Ilena berhasil untuk ' . (string)($order['id_midtrans'] ?? ''));
    }

    private function deleteOrderFromIlenaSistem(array $order): void
    {
        if ((string)env('ILENA_SYSTEM_WEB_ORDER_ENABLED', 'false') !== 'true') {
            return;
        }

        $baseUrl = trim((string)env('ILENA_SYSTEM_WEB_ORDER_URL', ''));
        $token = trim((string)env('ILENA_SYSTEM_WEB_ORDER_TOKEN', ''));
        $orderId = (string)($order['id_midtrans'] ?? '');
        if ($baseUrl === '' || $token === '' || $orderId === '') {
            return;
        }

        $url = rtrim($baseUrl, '/');
        if (str_ends_with($url, '/ilena-web-order')) {
            $url .= '/' . rawurlencode($orderId);
        } else {
            $url .= '/ilena-web-order/' . rawurlencode($orderId);
        }

        $payload = [
            'email' => (string)($order['email'] ?? ''),
            'is_test' => $this->isSandboxOrder($order),
            'payment_environment' => $this->isSandboxOrder($order) ? 'sandbox' : 'production',
            'reason' => (string)($order['status'] ?? 'Dibatalkan'),
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Ilena-Webhook-Token: ' . $token,
            ],
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'Hapus order dari Sistem Ilena gagal untuk {order}: HTTP {http} ERR {err} RESP {resp}', [
                'order' => $orderId,
                'http' => $httpCode,
                'err' => $err,
                'resp' => substr((string)$response, 0, 500),
            ]);
            return;
        }

        log_message('info', 'Order ' . $orderId . ' dihapus dari Sistem Ilena karena status website batal/gagal.');
    }

    private function sendOrderPaymentEmail(array $order, string $status): void
    {
        $emailCus = (string)($order['email'] ?? '');
        if (!filter_var($emailCus, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $orderId = (string)($order['id_midtrans'] ?? '');
        $nama = (string)($order['nama'] ?? 'Customer');
        $statusText = $status;
        $subjectStatus = $status === 'Proses' ? 'Pembayaran Berhasil' : 'Status Pembayaran ' . $status;
        $accessToken = $this->orderAccessToken($orderId, $order);

        if ($status === 'Proses') {
            $pesan = 'Pembayaran kamu sudah berhasil kami terima. Pesanan akan segera kami proses.';
        } elseif ($status === 'Menunggu Pembayaran') {
            $pesan = 'Pesanan kamu sudah dibuat dan sedang menunggu pembayaran.';
        } elseif (in_array($status, ['Kadaluarsa', 'Ditolak', 'Gagal', 'Dibatalkan'], true)) {
            $pesan = 'Pembayaran pesanan kamu berstatus ' . strtolower($status) . '. Silakan cek halaman pesanan untuk detailnya.';
        } else {
            $pesan = 'Status pembayaran pesanan kamu telah berubah menjadi ' . $status . '.';
        }

        $email = \Config\Services::email();
        $emailConfig = config('Email');
        $fromEmail = $emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com';
        $email->clear(true);
        $email->setFrom($fromEmail, 'Ilena Furniture');
        $email->setReplyTo('info@ilenafurniture.com', 'Customer Care Ilena Furniture');
        $email->setTo($emailCus);
        $email->setSubject('ILENA Store - ' . $subjectStatus . ' #' . $orderId);
        $email->setMessage('
            <div style="font-family:Arial,sans-serif;color:#222;line-height:1.6">
                <h2>Halo ' . esc($nama) . ',</h2>
                <p>' . esc($pesan) . '</p>
                <p><b>Kode Pesanan:</b> ' . esc($orderId) . '</p>
                <p><b>Status:</b> ' . esc($statusText) . '</p>
                <p><a href="https://ilenafurniture.com/orderdetail/' . rawurlencode(strtolower($status)) . '?idorder=' . rawurlencode($orderId) . '&amp;access_token=' . rawurlencode($accessToken) . '">Lihat detail pesanan</a></p>
                <p>Terima kasih sudah berbelanja di Ilena Furniture.</p>
            </div>
        ');
        $email->setAltMessage("Halo {$nama}, {$pesan}\nKode Pesanan: {$orderId}\nStatus: {$statusText}\nhttps://ilenafurniture.com/orderdetail/" . rawurlencode(strtolower($status)) . '?idorder=' . rawurlencode($orderId) . '&access_token=' . rawurlencode($accessToken));

        if (!$email->send(false)) {
            log_message('error', 'Gagal mengirim email status pembayaran Ilena ke {email}: {debug}', [
                'email' => $emailCus,
                'debug' => $email->printDebugger(['headers']),
            ]);
        }
    }

    private function sendAdminOrderPaymentEmail(array $order, string $status): void
    {
        $adminEmail = (string)env('ORDER_ADMIN_EMAIL', 'info@ilenafurniture.com');
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $orderId = (string)($order['id_midtrans'] ?? '');
        $nama = (string)($order['nama'] ?? '-');
        $emailCus = (string)($order['email'] ?? '-');
        $dataMid = json_decode($order['data_mid'] ?? '[]', true);
        $total = number_format((float)($dataMid['gross_amount'] ?? 0), 0, ',', '.');

        $email = \Config\Services::email();
        $emailConfig = config('Email');
        $fromEmail = $emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com';
        $email->clear(true);
        $email->setFrom($fromEmail, 'Ilena Furniture');
        $email->setReplyTo('info@ilenafurniture.com', 'Customer Care Ilena Furniture');
        $email->setTo($adminEmail);
        $email->setSubject('Admin Ilena - Update Pembayaran #' . $orderId);
        $email->setMessage('
            <div style="font-family:Arial,sans-serif;color:#222;line-height:1.6">
                <h2>Update pembayaran masuk</h2>
                <p>Midtrans mengirim perubahan status pembayaran untuk pesanan berikut:</p>
                <p><b>Kode Pesanan:</b> ' . esc($orderId) . '</p>
                <p><b>Status:</b> ' . esc($status) . '</p>
                <p><b>Customer:</b> ' . esc($nama) . '</p>
                <p><b>Email Customer:</b> ' . esc($emailCus) . '</p>
                <p><b>Total:</b> Rp ' . esc($total) . '</p>
                <p><a href="https://ilenafurniture.com/admin/order/online">Buka admin order</a></p>
            </div>
        ');
        $email->setAltMessage("Update pembayaran masuk\nKode Pesanan: {$orderId}\nStatus: {$status}\nCustomer: {$nama}\nEmail: {$emailCus}\nTotal: Rp {$total}");

        if (!$email->send(false)) {
            log_message('error', 'Gagal mengirim email status pembayaran admin Ilena ke {email}: {debug}', [
                'email' => $adminEmail,
                'debug' => $email->printDebugger(['headers']),
            ]);
        }
    }

    private function sendMetaCapiPurchase(array $order): void
    {
        try {
            (new MetaCapi())->sendPurchase($order);
        } catch (\Throwable $th) {
            log_message('error', 'Meta CAPI Purchase error for order {order}: {error}', [
                'order' => (string)($order['id_midtrans'] ?? ''),
                'error' => $th->getMessage(),
            ]);
        }
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
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setBody('Metode checkout tidak valid.');
        }
        $deCodeToken = base64_decode($token, true);
        if (!is_string($deCodeToken)) {
            session()->setFlashdata('msg', 'Token checkout tidak valid.');
            return redirect()->to('/address');
        }
        $parts = explode(':', $deCodeToken);
        if (count($parts) < 2) {
            session()->setFlashdata('msg', 'Token tidak valid');
            return redirect()->to('/address');
        }

        [$stamp, $index_kurir] = $parts;
        if (!is_numeric($stamp) || (time() - (int)$stamp) > 600 || (int)$stamp > time() + 60) {
            session()->setFlashdata('msg', 'Token kadaluarsa, silakan ulangi pembayaran');
            return redirect()->to('/payment/' . $index_kurir);
        }
        $expectedNonce = (string)session()->get('checkout_nonce');
        $submittedNonce = (string)$this->request->getPost('checkout_nonce');
        if ($expectedNonce === '' || $submittedNonce === '' || !hash_equals($expectedNonce, $submittedNonce)) {
            session()->setFlashdata('msg', 'Permintaan checkout sudah digunakan atau kedaluwarsa. Periksa status pesanan sebelum mencoba lagi.');
            return redirect()->to('/payment/' . $index_kurir);
        }

        $pembayaran = (string)session()->get('payment_method');
        $alamatselected = session()->get('alamatTerpilih');
        $kurir = session()->get('kurir');
        if (!is_array($alamatselected) || empty($alamatselected)) {
            session()->setFlashdata('msg','Alamat tidak ditemukan, silakan ulangi.');
            return redirect()->to('/address');
        }
        $checkoutContactEmail = CheckoutEmailPolicy::normalize($alamatselected['email_pemesan'] ?? null);
        if ($checkoutContactEmail === null) {
            session()->setFlashdata('msg', 'Email pemesan tidak valid atau termasuk email sementara. Perbarui alamat dengan email pribadi yang aktif.');
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

        $sessionEmail = (string)session()->get('email');
        $paymentEmailCandidate = $sessionEmail !== '' && $sessionEmail !== 'tamu' ? $sessionEmail : $checkoutContactEmail;
        $paymentEmail = CheckoutEmailPolicy::normalize($paymentEmailCandidate);
        if ($paymentEmail === null) {
            session()->setFlashdata('msg', 'Email pemesan tidak valid atau termasuk email sementara. Perbarui alamat dengan email pribadi yang aktif.');
            return redirect()->to('/address');
        }
        $email = PaymentSafety::normalizeEmail($paymentEmail);
        $isSandboxCheckout = session()->get('isLogin') && $sessionEmail !== 'tamu' && PaymentSafety::isTestEmail($sessionEmail, $this->midtransTestEmails());
        $nama = $alamatselected['nama_penerima'];
        $nohp = $alamatselected['nohp_penerima'];
        $alamat = $alamatselected['alamat_lengkap'];
        $kurirTerpilih = $kurir[$index_kurir];
        $kurirTerpilih = (new FreeShippingService())->applyToRates($alamatselected, [$kurirTerpilih])[0] ?? $kurirTerpilih;
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
            $hasil = \App\Services\CatalogPricing::unitPrice($produk);
            $hargaAwal = max(0, (int)round((float)($produk['harga'] ?? 0)));
            $desc = json_decode($produk['deskripsi'] ?? '[]', true);
            $dimensi = is_array($desc['dimensi']['asli'] ?? null) ? $desc['dimensi']['asli'] : [];
            $dim = $dimensi['panjang'] ?? '';
            $varLbl = ucfirst($element['varian'] ?? '');

            $itemDetails[] = [
                'id' => $produk['id'],
                'price' => $hasil,
                // Persist the checkout-time price basis so invoice discounts remain
                // historically accurate if catalog pricing changes later.
                'original_price' => $hargaAwal,
                'collection' => (string)($produk['kategori'] ?? ''),
                'product_name' => (string)($produk['nama'] ?? ''),
                'dimensions' => [
                    'panjang' => (string)($dimensi['panjang'] ?? ''),
                    'lebar' => (string)($dimensi['lebar'] ?? ''),
                    'tinggi' => (string)($dimensi['tinggi'] ?? ''),
                ],
                'weight_kg' => (string)($dimensi['berat'] ?? ''),
                'quantity' => $jumlah,
                'name' => $produk['nama'] . " " . $dim . " (" . $varLbl . ")",
                'variant' => (string)$element['varian'],
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

        $total = 0;
        foreach ($itemDetails as &$item) {
            $item['id'] = substr((string)($item['id'] ?? ''), 0, 64);
            $item['price'] = (int)round((float)($item['price'] ?? 0));
            $item['quantity'] = max(1, (int)($item['quantity'] ?? 1));
            $item['name'] = substr((string)($item['name'] ?? $item['id']), 0, 50);
            unset($item['packed']);
            $total += $item['price'] * $item['quantity'];
        }
        unset($item);

        $idFix = (string)session()->get('checkout_order_id');
        $idempotencyKey = (string)session()->get('checkout_operation_key');
        $currentFingerprint = \App\Services\CheckoutState::fingerprint($cartCheck['cart'], $alamatselected, $kurirTerpilih, $pembayaran, session()->get('voucher'));
        if (!hash_equals((string)session()->get('checkout_operation_fingerprint'), $currentFingerprint)) {
            session()->setFlashdata('msg', 'Keranjang atau pilihan pembayaran berubah. Periksa kembali ringkasan sebelum membayar.');
            return redirect()->to('/payment/' . $index_kurir);
        }
        if ($idFix === '' || $idempotencyKey === '') {
            session()->setFlashdata('msg', 'Sesi checkout kedaluwarsa. Muat ulang pembayaran sebelum melanjutkan.');
            return redirect()->to('/payment/' . $index_kurir);
        }
        $existingOrder = $this->pemesananModel->getPemesanan($idFix);
        if ($existingOrder) {
            return redirect()->to('/orderdetail/' . rawurlencode(strtolower((string)$existingOrder['status']))
                . '?idorder=' . rawurlencode($idFix) . '&access_token=' . rawurlencode($this->orderAccessToken($idFix)));
        }

        $customField = json_encode(['e'=>$email,'n'=>$nama,'h'=>$nohp,'a'=>$alamat,'i'=>session()->get('keranjang'),'k'=>$kurirTerpilih,'v'=>$voucher]);
        $arrPostField = [
            "transaction_details" => ["order_id"=>$idFix, "gross_amount"=>(int)$total],
            "customer_details" => ["email"=>$email,"phone"=>$nohp,"first_name"=>$nama],
            "item_details" => array_map(static function ($item) {
                return [
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                ];
            }, $itemDetails),
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
                $arrPostField["credit_card"] = ["token_id"=>$this->request->getPost('tokencc')];
                break;
            default:
                return redirect()->to('/payment/' . $index_kurir);
        }

        if (!$isSandboxCheckout) {
            try {
                $reservation = (new \App\Services\Payments\StockReservationService())->reserve($idFix, $cartCheck['cart']);
            } catch (\Throwable $error) {
                log_message('error', 'Could not reserve checkout stock for {order}: {error}', ['order' => $idFix, 'error' => $error->getMessage()]);
                session()->setFlashdata('msg', 'Stok belum dapat diamankan. Coba kembali setelah beberapa saat.');
                return redirect()->to('/payment/' . $index_kurir);
            }
            if (empty($reservation['ok'])) {
                session()->setFlashdata('msg', $reservation['message'] ?? 'Stok berubah. Perbarui keranjang sebelum membayar.');
                return redirect()->to('/cart');
            }
        }

        // Persist the order BEFORE contacting the provider so early callbacks and
        // ambiguous network failures have a stable local reconciliation target.
        $environment=$isSandboxCheckout?'sandbox':'production';
        $accessToken=bin2hex(random_bytes(32));
        $attemptMid=['order_id'=>$idFix,'gross_amount'=>(int)$total,'payment_type'=>$arrPostField['payment_type'],
            'transaction_time'=>gmdate('Y-m-d H:i:s'),'_app_payment_environment'=>$environment,
            '_app_order_access_token'=>$accessToken,'_app_payment_effects_applied'=>false];
        try {
            if (!$this->pemesananModel->insert(['nama'=>$nama,'email'=>$email,'nohp'=>$nohp,'alamat'=>$alamat,
                'resi'=>'Menunggu pengiriman','items'=>json_encode($itemDetails),'kurir'=>json_encode($kurirTerpilih),
                'id_midtrans'=>$idFix,'status'=>'Menunggu Pembayaran','data_mid'=>json_encode($attemptMid),
                'id_marketplace'=>'','status_print'=>'siap','keterangan_suratjalan'=>''])) throw new \RuntimeException('Checkout attempt could not be saved');
        } catch (\Throwable $error) {
            if (!$isSandboxCheckout) (new \App\Services\Payments\StockReservationService())->release($idFix);
            log_message('error','Checkout persistence failed: {error}',['error'=>$error->getMessage()]);
            return redirect()->to('/payment/'.$index_kurir)->with('msg','Pesanan belum tersimpan; belum ada permintaan pembayaran dikirim.');
        }
        $orderTokens=(array)session()->get('checkout_order_tokens'); $orderTokens[$idFix]=$accessToken;
        session()->set('checkout_order_tokens',array_slice($orderTokens,-20,null,true));
        session()->set('checkout_pending_order_id',$idFix);
        // One-time token prevents repeated form submissions; gateway key protects network retries.
        session()->remove('checkout_nonce');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $isSandboxCheckout ? "https://api.sandbox.midtrans.com/v2/charge" : "https://api.midtrans.com/v2/charge",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arrPostField),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_HTTPHEADER => array_values(array_filter([
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode($this->midtransServerKey($email, $isSandboxCheckout ? 'sandbox' : 'production') . ":"),
                !in_array($pembayaran, ['permata', 'cimb'], true) ? "Idempotency-Key: " . $idempotencyKey : null,
            ])),
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err !== '') {
            log_message('error', 'Midtrans checkout request failed for {order}: HTTP {http}; {error}', [
                'order' => $idFix,
                'http' => $httpCode,
                'error' => $err,
            ]);
            session()->setFlashdata('msg', 'Pembayaran belum mendapat konfirmasi. Pesanan belum ditampilkan sebagai berhasil; muat ulang dan periksa status sebelum mencoba lagi.');
            return redirect()->to('/orderdetail/menunggu%20pembayaran?idorder='.rawurlencode($idFix).'&access_token='.rawurlencode($accessToken));
        }
        $hasilMidtrans = json_decode((string)$response, true);
        if (substr($hasilMidtrans['status_code'] ?? '', 0, 1) !== '2') {
            // Unknown/duplicate/provider failures must not start a second payment.
            if ($httpCode >= 500 || $httpCode === 409 || in_array((string)($hasilMidtrans['status_code']??''),['406','409','500','502','503','504'],true) || !is_array($hasilMidtrans)) {
                return redirect()->to('/orderdetail/menunggu%20pembayaran?idorder='.rawurlencode($idFix).'&access_token='.rawurlencode($accessToken));
            }
            $this->pemesananModel->where('id_midtrans',$idFix)->where('status','Menunggu Pembayaran')->set(['status'=>'Gagal'])->update();
            if (db_connect()->affectedRows()===1 && !$isSandboxCheckout) (new \App\Services\Payments\StockReservationService())->release($idFix);
            session()->remove(['checkout_pending_order_id','checkout_operation_fingerprint','checkout_order_id','checkout_operation_key']);
            session()->setFlashdata('msg', $hasilMidtrans['status_message'] ?? 'Transaksi gagal');
            return redirect()->to('/payment/' . $index_kurir);
        }

        try {
            $result=(new \App\Services\Payments\PaymentTransitionService(db_connect()))->apply(
                $hasilMidtrans,$isSandboxCheckout?'sandbox':'production',fn($paid)=>$this->processPaidOrder($paid)
            );
            $trx=$result['order']; $status=(string)$trx['status'];
            if ($result['applied'] && $status==='Proses' && !$isSandboxCheckout) { $this->markVoucherUsed($trx); $this->sendMetaCapiPurchase($trx); }
            if (in_array($status,['Proses','Menunggu Pembayaran'],true)) $this->syncOrderToIlenaSistem($trx);
        } catch (\Throwable $e) {
            log_message('error','Charge response reconciliation pending for {order}: {error}',['order'=>$idFix,'error'=>$e->getMessage()]);
            return redirect()->to('/orderdetail/menunggu%20pembayaran?idorder='.rawurlencode($idFix).'&access_token='.rawurlencode($this->orderAccessToken($idFix)));
        }
        session()->remove(['voucher', 'voucher_claimed', 'keranjang', 'kurir', 'kurirTerpilih', 'alamatTerpilih', 'hargaKeseluruhan', 'checkout_nonce', 'checkout_operation_fingerprint', 'checkout_operation_created_at', 'checkout_operation_key', 'checkout_order_id', 'checkout_pending_order_id']);
        $this->syncCartToUser([]);

        return redirect()->to('/orderdetail/' . rawurlencode(strtolower($status)) . '?idorder=' . rawurlencode($idFix) . '&access_token=' . rawurlencode($this->orderAccessToken($idFix)));
    }

    public function updateTransaction()
    {
        $body=json_decode($this->request->getBody(),true);
        if (!is_array($body)||!is_string($body['order_id']??null)) return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Invalid notification']);
        $order=$this->pemesananModel->getPemesanan($body['order_id']);
        if (!$order) return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'Order not found']);
        $environment=PaymentSafety::environment($order,$this->midtransTestEmails(),'email');
        $key=$this->midtransServerKey((string)$order['email'],$environment);
        if (!PaymentSafety::validNotification($order,$body,$key)) return $this->response->setStatusCode(403)->setJSON(['success'=>false,'message'=>'Invalid notification signature or amount']);
        try {
            // Signature fields do not cover transaction_status: paid events are
            // reconciled with the provider's authenticated status endpoint as well.
            $body=(new \App\Services\Payments\MidtransStatusClient())->fetch((string)$order['id_midtrans'],$environment,$key);
            $result=(new \App\Services\Payments\PaymentTransitionService(db_connect()))->apply($body,$environment,fn($paid)=>$this->processPaidOrder($paid));
            $updated=$result['order']; $status=(string)$updated['status'];
            if ($result['applied']) {
                if ($environment!=='sandbox' && $status==='Proses') { $this->markVoucherUsed($updated); $this->sendMetaCapiPurchase($updated); }
                if (in_array($status,['Kadaluarsa','Ditolak','Gagal','Dibatalkan'],true)) $this->deleteOrderFromIlenaSistem($updated);
                $this->sendOrderPaymentEmail($updated,$status);
                if ($environment!=='sandbox') $this->sendAdminOrderPaymentEmail($updated,$status);
            }
            if (in_array($status,['Proses','Menunggu Pembayaran'],true)) $this->syncOrderToIlenaSistem($updated);
            return $this->response->setJSON(['success'=>true,'ignored'=>!$result['applied']]);
        } catch (\Throwable $e) {
            log_message('error','Payment reconciliation failed for {order}: {error}',['order'=>$body['order_id'],'error'=>$e->getMessage()]);
            return $this->response->setStatusCode(503)->setJSON(['success'=>false,'message'=>'Notification will be retried']);
        }
    }

    public function midtransNotificationDispatcher()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        if (!is_array($body)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid JSON']);
        }

        $orderId = (string)($body['order_id'] ?? '');
        if ($this->isLunareaOrderId($orderId)) {
            return $this->forwardMidtransNotificationToLunarea($bodyJson);
        }

        return $this->updateTransaction();
    }

    private function isLunareaOrderId(string $orderId): bool
    {
        return $orderId !== '' && str_starts_with(strtoupper($orderId), 'L') && !str_starts_with(strtoupper($orderId), 'IL');
    }

    private function forwardMidtransNotificationToLunarea(string $bodyJson)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://lunareafurniture.com/updatetransaction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $bodyJson,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            log_message('error', 'Forward Midtrans notification to Lunarea failed: {err}', ['err' => $err]);
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Forward to Lunarea failed',
            ]);
        }

        return $this->response
            ->setStatusCode($httpCode >= 200 && $httpCode < 600 ? $httpCode : 200)
            ->setBody($response ?: json_encode(['success' => true]));
    }

    public function paymentFinishRedirect()
    {
        return $this->redirectFromMidtrans('Menunggu Pembayaran');
    }

    public function paymentUnfinishRedirect()
    {
        return $this->redirectFromMidtrans('Menunggu Pembayaran');
    }

    public function paymentErrorRedirect()
    {
        return $this->redirectFromMidtrans('Gagal');
    }

    private function orderAccessToken(string $orderId, ?array $order = null): string
    {
        $order = $order ?? $this->pemesananModel->getPemesanan($orderId);
        $dataMid = json_decode((string)($order['data_mid'] ?? ''), true);
        $token = is_array($dataMid) ? (string)($dataMid['_app_order_access_token'] ?? '') : '';
        return preg_match('/^[a-f0-9]{64}$/i', $token) === 1 ? strtolower($token) : '';
    }

    private function canAccessOrder(array $order): bool
    {
        $orderId = (string)($order['id_midtrans'] ?? '');
        $orderEmail = PaymentSafety::normalizeEmail((string)($order['email'] ?? ''));
        $sessionEmail = PaymentSafety::normalizeEmail((string)session()->get('email'));
        $verified = session()->get('isLogin') && (string)session()->get('active') === '1';
        // Staff still need to service orders belonging to suspended customers, but
        // the cached session role must still match the active account in the DB.
        $sessionRole = (string) session()->get('role');
        if ($verified && in_array($sessionRole, ['1', '5'], true)) {
            if (!(new AdminRbacService())->canAccessOnlineOrdersAsStaff($sessionEmail, $sessionRole)) {
                return false;
            }
            return true;
        }
        $allowed = $verified && $sessionEmail !== '' && $sessionEmail !== 'tamu' && hash_equals($orderEmail, $sessionEmail);
        $expected = $this->orderAccessToken($orderId, $order);
        $rawToken = $this->request->getGet('access_token') ?? ((array)session()->get('checkout_order_tokens'))[$orderId] ?? '';
        $provided = is_string($rawToken) ? $rawToken : '';
        $allowed = $allowed || ($expected !== '' && preg_match('/^[a-f0-9]{64}$/i', $provided) === 1 && hash_equals($expected, strtolower($provided)));
        if (!$allowed) return false;
        return $orderEmail === '' || !(new \App\Services\AccountSuspensionService())->isSuspended($orderEmail);
    }

    public function paymentStatus($orderId)
    {
        $orderId = (string)$orderId;
        $order = $this->pemesananModel->getPemesanan($orderId);
        if (!$order || !$this->canAccessOrder($order)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Order not found',
            ]);
        }

        $mid=json_decode((string)($order['data_mid']??'{}'),true)?:[];
        $environment=PaymentSafety::environment($order,$this->midtransTestEmails(),'email');
        $pollKey='payment-reconcile-'.hash('sha256',$orderId);
        if (in_array($mid['payment_type']??'',['bank_transfer','permata','echannel','qris','gopay','shopeepay','credit_card'],true)
            && (($order['status']??'')==='Menunggu Pembayaran' || PaymentSafety::needsFirstPaidEffects($order,'Proses'))
            && !cache()->get($pollKey)) {
            cache()->save($pollKey,1,15);
            try {
                $provider=(new \App\Services\Payments\MidtransStatusClient())->fetch($orderId,$environment,$this->midtransServerKey((string)$order['email'],$environment));
                $result=(new \App\Services\Payments\PaymentTransitionService(db_connect()))->apply($provider,$environment,fn($paid)=>$this->processPaidOrder($paid));
                $order=$result['order'];
                if ($result['applied'] && $order['status']==='Proses' && $environment!=='sandbox') { $this->markVoucherUsed($order); $this->sendMetaCapiPurchase($order); }
                if ($result['applied'] && in_array($order['status'],['Proses','Menunggu Pembayaran'],true)) $this->syncOrderToIlenaSistem($order);
                if ($result['applied'] && in_array($order['status'],['Kadaluarsa','Ditolak','Gagal','Dibatalkan'],true)) $this->deleteOrderFromIlenaSistem($order);
            } catch (\Throwable $e) { log_message('error','Payment status reconciliation deferred for {order}: {error}',['order'=>$orderId,'error'=>$e->getMessage()]); }
        }
        $status = (string)($order['status'] ?? 'Menunggu Pembayaran');
        return $this->response->setJSON([
            'success' => true,
            'order_id' => $orderId,
            'status' => $status,
            'paid' => in_array($status, ['Proses', 'Dikirim', 'Selesai'], true),
            'detail_url' => site_url('/orderdetail/' . rawurlencode(strtolower($status)) . '?idorder=' . rawurlencode($orderId) . '&access_token=' . rawurlencode($this->orderAccessToken($orderId, $order))),
        ]);
    }

    private function redirectFromMidtrans(string $fallbackStatus = 'Menunggu Pembayaran')
    {
        $orderId = (string)(
            $this->request->getGet('order_id')
            ?? $this->request->getGet('idorder')
            ?? ''
        );

        if ($orderId === '') {
            return redirect()->to('/order');
        }

        if ($this->isLunareaOrderId($orderId)) {
            return redirect()->to('https://lunareafurniture.com/order/' . rawurlencode($orderId));
        }
        $order = $this->pemesananModel->getPemesanan($orderId);
        if (!$order || !$this->canAccessOrder($order)) return redirect()->to('/order');
        $status = $fallbackStatus;
        $order = $this->pemesananModel->getPemesanan($orderId);
        if ($order && !empty($order['status'])) {
            $status = (string)$order['status'];
        } else {
            $transactionStatus = (string)($this->request->getGet('transaction_status') ?? '');
            $fraudStatus = (string)($this->request->getGet('fraud_status') ?? 'accept');
            $mappedStatus = $this->midtransStatusToOrderStatus($transactionStatus, $fraudStatus);
            if ($mappedStatus !== 'No Status' && $mappedStatus !== 'Forbidden') {
                $status = $mappedStatus;
            }
        }

        if ($this->isLunareaOrderId($orderId)) {
            return redirect()->to('https://lunareafurniture.com/order/' . rawurlencode($orderId));
        }

        return redirect()->to('/orderdetail/' . rawurlencode(strtolower($status)) . '?idorder=' . rawurlencode($orderId) . '&access_token=' . rawurlencode($this->orderAccessToken($orderId, $order)));
    }

    public function cancelCustomerOrder(string $orderId)
    {
        $order = $this->pemesananModel->getPemesanan($orderId);
        if (!$order || !$this->canAccessOrder($order)) {
            return $this->response->setStatusCode(404)->setBody('Pesanan tidak ditemukan.');
        }
        if (($order['status'] ?? '') !== 'Menunggu Pembayaran') {
            session()->setFlashdata('order_action_message', 'Pesanan ini sudah tidak dapat dibatalkan dari halaman ini.');
            return redirect()->to('/orderdetail/' . rawurlencode(strtolower((string)$order['status']))
                . '?idorder=' . rawurlencode($orderId) . '&access_token=' . rawurlencode($this->orderAccessToken($orderId, $order)));
        }

        $environment = PaymentSafety::environment($order, $this->midtransTestEmails(), 'email');
        $serverKey = $this->midtransServerKey((string)$order['email'], $environment);
        if ($serverKey === '') {
            session()->setFlashdata('order_action_message', 'Pembatalan belum dapat diproses. Silakan hubungi Customer Care.');
            return redirect()->back();
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => ($environment === 'sandbox' ? 'https://api.sandbox.midtrans.com/v2/' : 'https://api.midtrans.com/v2/') . rawurlencode($orderId) . '/cancel',
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Basic ' . base64_encode($serverKey . ':')],
        ]);
        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $gateway = json_decode((string)$response, true);
        if ($curlError !== '' || $httpCode < 200 || $httpCode >= 300 || !is_array($gateway)) {
            log_message('error', 'Customer order cancellation request failed for {order}: HTTP {http}; {error}', [
                'order' => $orderId,
                'http' => $httpCode,
                'error' => $curlError,
            ]);
            session()->setFlashdata('order_action_message', 'Pembatalan belum terkonfirmasi. Periksa status pesanan sebelum mencoba lagi.');
            return redirect()->back();
        }

        $newStatus = PaymentSafety::notificationStatus($gateway);
        if ($newStatus !== 'Dibatalkan') {
            session()->setFlashdata('order_action_message', 'Midtrans belum mengonfirmasi pembatalan. Status pesanan tetap mengikuti pembaruan pembayaran.');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $locked = PaymentSafety::lockedOrder($db, $orderId);
            if (!$locked || !PaymentSafety::canTransition((string)$locked['status'], 'Dibatalkan')) {
                $db->transRollback();
                session()->setFlashdata('order_action_message', 'Status pesanan berubah saat pembatalan diproses. Periksa status terbaru.');
                return redirect()->back();
            }
            $dataMid = json_decode((string)($locked['data_mid'] ?? ''), true) ?: [];
            $dataMid = array_merge($dataMid, $gateway);
            $dataMid['_app_payment_environment'] = $environment;
            if (!PaymentSafety::claim($db, $orderId, (string)$locked['status'], 'Dibatalkan', $dataMid) || !$db->transStatus()) {
                throw new \RuntimeException('Could not apply buyer cancellation transition');
            }
            (new \App\Services\Payments\StockReservationService($db))->release($orderId);
            $db->transCommit();
        } catch (\Throwable $error) {
            $db->transRollback();
            log_message('error', 'Customer cancellation transition failed for {order}: {error}', [
                'order' => $orderId,
                'error' => $error->getMessage(),
            ]);
            session()->setFlashdata('order_action_message', 'Pembatalan sedang disinkronkan. Cek status kembali beberapa saat lagi.');
            return redirect()->back();
        }

        $updatedOrder = $this->pemesananModel->getPemesanan($orderId);
        if ($updatedOrder) {
            $this->deleteOrderFromIlenaSistem($updatedOrder);
            $this->sendOrderPaymentEmail($updatedOrder, 'Dibatalkan');
        }
        return redirect()->to('/orderdetail/dibatalkan?idorder=' . rawurlencode($orderId)
            . '&access_token=' . rawurlencode($this->orderAccessToken($orderId, $updatedOrder)));
    }

    public function cancelOrder($id_midtrans)
    {
        $order=$this->pemesananModel->getPemesanan($id_midtrans);
        if (!$order || !$this->canAccessOrder($order)) return $this->response->setStatusCode(404)->setBody('Pesanan tidak ditemukan.');
        $mid=json_decode($order['data_mid']??'{}',true)?:[];
        if (in_array($mid['payment_type']??'',['admin','market','toko'],true)) {
            $db=db_connect(); $db->transBegin();
            try {
                $locked=PaymentSafety::lockedOrder($db,(string)$id_midtrans);
                if (!$locked || $locked['status']!=='Menunggu Pembayaran' || $this->pemesananGudangModel->where('id_pesanan',$id_midtrans)->countAllResults()>0) throw new \RuntimeException('Pesanan sudah diproses dan tidak dapat dibatalkan sebagai order menunggu.');
                $this->pemesananModel->where('id_midtrans',$id_midtrans)->set(['status'=>'Dibatalkan'])->update();
                $this->kartuStokModel->where('id_pesanan',$id_midtrans)->where('pending',true)->delete();
                if (!$db->transStatus()) throw new \RuntimeException('Pembatalan gagal disimpan.');
                $db->transCommit(); return redirect()->to('/admin/order/online')->with('msg','Pesanan manual dibatalkan.');
            } catch (\Throwable $e) { $db->transRollback(); return redirect()->back()->with('msg',$e->getMessage()); }
        }
        return $this->cancelCustomerOrder((string)$id_midtrans);
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
        if ($id_order && (!$pemesanan || !$this->canAccessOrder($pemesanan))) {
            return $this->response->setStatusCode(404)->setBody('Pesanan tidak ditemukan.');
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
        $order = null;
        if ($idOrder !== '') {
            $order = $this->pemesananModel->getPemesanan($idOrder);
            if ($order && !$this->canAccessOrder($order)) {
                return $this->response->setStatusCode(404)->setBody('Pesanan tidak ditemukan.');
            }
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
        $statusAll = ['Proses', 'Dikirim', 'Selesai', 'Menunggu Pembayaran', 'Kadaluarsa', 'Ditolak', 'Gagal', 'Refund', 'Partial Refund', 'Dibatalkan'];
        $statusSelain = array_filter($statusAll, function ($s) use ($status) {
            return strtolower($s) != strtolower($status);
        });
        foreach ($pemesanan as $ind_p => $p) {
            $pemesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            unset($pemesanan[$ind_p]['data_mid']['_app_order_access_token']);
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
            unset($pemesananAll[$ind_p]['data_mid']['_app_order_access_token']);
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
            'orderAccessToken' => $order ? $this->orderAccessToken((string)$order['id_midtrans'], $order) : '',
            'orderActionMessage' => session()->getFlashdata('order_action_message'),
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

            case 'dikirim':
            case 'selesai':
            case 'refund':
            case 'partial refund':
            case 'proses':
                return view('pages/detailOrderProses', $data);
                break;

            case 'ditolak':
            case 'gagal':
                return view('pages/detailOrderBatal', $data);
            default:
                return redirect()->to('/order');
                break;
        }
    }
    private function invoiceViewData(string $idMid): ?array
    {
        $transaksi = $this->pemesananModel->getPemesanan($idMid);
        if (!$transaksi || !$this->canAccessOrder($transaksi)) {
            return null;
        }
        $items = json_decode((string)($transaksi['items'] ?? '[]'), true);
        if (!is_array($items)) $items = [];
        $dataMid = json_decode((string)($transaksi['data_mid'] ?? '{}'), true);
        if (!is_array($dataMid)) $dataMid = [];
        unset($dataMid['_app_order_access_token']);
        $kurir = json_decode((string)($transaksi['kurir'] ?? '{}'), true);
        if (!is_array($kurir)) $kurir = [];
        if ($kurir === []) $kurir = ['nama' => 'Menunggu pengiriman', 'deskripsi' => ''];
        $arr = [
            'id' => $transaksi['id'],
            'nama' => $transaksi['nama'],
            'email' => $transaksi['email'],
            'nohp' => $transaksi['nohp'],
            'alamat' => $transaksi['alamat'],
            'resi' => $transaksi['resi'],
            'id_midtrans' => $transaksi['id_midtrans'],
            'items' => $items,
            'status' => $transaksi['status'],
            'kurir' => $kurir,
            'data_mid' => $dataMid,
        ];
        foreach ($arr['items'] as $ind_i => $i) {
            if (!is_array($i)) {
                unset($arr['items'][$ind_i]);
                continue;
            }
            $itemId = $i['id'] ?? null;
            $itemName = strtolower(trim((string)($i['name'] ?? '')));
            if (!in_array($itemName, ['voucher', 'biaya admin', 'biaya ongkir', 'flash sale'], true)) {
                $barangCur = $this->barangModel->getBarang($itemId);

                // Jika produk sudah nonaktif / terhapus, getBarang() bisa mengembalikan null.
                // Ambil juga dari getBarangAdmin() agar invoice order lama tetap bisa dibuka.
                if (!$barangCur && $itemId) {
                    $barangCur = $this->barangModel->getBarangAdmin($itemId);
                }

                $arr['items'][$ind_i]['collection'] = $i['collection'] ?? ($barangCur['kategori'] ?? '');
                $arr['items'][$ind_i]['detail'] = $barangCur ?? [];
                $arr['items'][$ind_i]['product_name'] = $i['product_name'] ?? ($barangCur['nama'] ?? $i['name'] ?? 'Produk');
                $arr['items'][$ind_i]['variant'] = $i['variant'] ?? $i['varian'] ?? '';
                $description = json_decode((string)($barangCur['deskripsi'] ?? '{}'), true);
                $fallbackDimensions = is_array($description['dimensi']['asli'] ?? null) ? $description['dimensi']['asli'] : [];
                if (!is_array($i['dimensions'] ?? null)) {
                    $arr['items'][$ind_i]['dimensions'] = [
                        'panjang' => (string)($fallbackDimensions['panjang'] ?? ''),
                        'lebar' => (string)($fallbackDimensions['lebar'] ?? ''),
                        'tinggi' => (string)($fallbackDimensions['tinggi'] ?? ''),
                    ];
                }
                $arr['items'][$ind_i]['weight_kg'] = (string)($i['weight_kg'] ?? $fallbackDimensions['berat'] ?? '');
            }
        }

        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        return [
            'title' => 'Print Preview',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'transaksi' => $arr,
            'bulan' => $bulan
        ];
    }

    public function invoice($id_mid)
    {
        $data = $this->invoiceViewData((string)$id_mid);
        if ($data === null) return $this->response->setStatusCode(404)->setBody('Pesanan tidak ditemukan.');
        return view('pages/invoice', $data);
    }

    public function invoicePdf($id_mid)
    {
        $data = $this->invoiceViewData((string)$id_mid);
        if ($data === null) return $this->response->setStatusCode(404)->setBody('Pesanan tidak ditemukan.');

        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)$data['transaksi']['id_midtrans']);
        $data['title'] = 'Invoice Ilena Furniture';
        $data['logoPath'] = FCPATH . 'img' . DIRECTORY_SEPARATOR . 'LogoIlena.png';
        $html = view('pages/invoice_pdf', $data);
        $pdf = new \Dompdf\Dompdf(['chroot' => FCPATH, 'isRemoteEnabled' => false]);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="invoice-' . $safeId . '.pdf"')
            ->setBody($pdf->output());
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
        $authFormGuard = new \App\Services\Auth\AuthFormGuard();
        $data = [
            'title' => 'Akun',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'authFormNonce' => $authFormGuard->issue('login'),
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
        $emailInput = $this->request->getPost('email');
        $emailIdentity = is_string($emailInput) ? trim($emailInput) : '';
        $guard = new \App\Services\Auth\AuthFormGuard();
        $validForm = $guard->verify('login', $this->request->getPost('auth_form_nonce'), [
            $this->request->getPost('company_website'),
        ]);
        if (!$validForm) {
            session()->setFlashdata('msg', 'Form login kedaluwarsa. Silakan muat ulang halaman dan coba lagi.');
            return redirect()->to('/login');
        }
        if (!(new \App\Services\Auth\AuthAttemptLimiter())->allow('login', (string)$this->request->getIPAddress(), $emailIdentity, 20, 8, 900)) {
            session()->setFlashdata('msg', 'Terlalu banyak percobaan login. Tunggu beberapa saat lalu coba lagi.');
            return redirect()->to('/login');
        }
        $captchaError = (new \App\Services\Auth\RecaptchaVerifier())->error($this->request->getPost('g-recaptcha-response'));
        if ($captchaError !== null) {
            session()->setFlashdata('msg', $captchaError);
            return redirect()->to('/login')->withInput();
        }

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
            session()->setFlashdata('msg', 'Email atau kata sandi salah');
            return redirect()->to('/login');
        }
        $authSandi = password_verify($sandi, $getUser['sandi']);
        if (!$authSandi) {
            session()->setFlashdata('msg', 'Email atau kata sandi salah');
            return redirect()->to('/login');
        }

        if (in_array((string)($getUser['role'] ?? ''), ['0', '4'], true)) {
            $suspension = (new \App\Services\AccountSuspensionService())->state((string)$getUser['email']);
            if ($suspension !== null) {
                $until = new \DateTimeImmutable($suspension['until'], new \DateTimeZone('UTC'));
                $when = $until->format('d M Y H:i') . ' UTC';
                session()->setFlashdata('msg', 'Akun Anda ditangguhkan ' . $when . '. Silakan hubungi admin.');
                return redirect()->to('/login');
            }
        }

        // Only merge anonymous browsing data, never another authenticated account's state.
        $guestCart = !session()->get('isLogin') ? (array)session()->get('keranjang') : [];
        $guestWishlist = !session()->get('isLogin') ? (array)session()->get('wishlist') : [];
        $guestAddresses = !session()->get('isLogin') ? (array)session()->get('alamat') : [];
        session()->set('pending_guest_cart', $guestCart);
        session()->set('pending_guest_wishlist', $guestWishlist);
        session()->set('pending_guest_addresses', $guestAddresses);
        $ses_data = ['alamat', 'wishlist', 'keranjang'];
        session()->remove($ses_data);
        if ($getUser['active'] == '0') {
            $ses_data = [
                'email' => $getUser['email'],
                'active' => '0',
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->regenerate(true);
            session()->set($ses_data);
            session()->setFlashdata('msg', "Email " . $email . " perlu diverifikasi");
            return redirect()->to('/verify');
        }
        $rbacService = new AdminRbacService();
        $userRole = (string) ($getUser['role'] ?? '');
        $hasAdminAssignment = in_array($userRole, ['0', '3', '4', '5'], true)
            && $rbacService->hasAnyAdminAccess((string) $getUser['email']);
        if ($userRole === '5' || $hasAdminAssignment) {
            if (!$hasAdminAssignment) {
                session()->setFlashdata('msg', 'Akses admin belum aktif untuk email ini.');
                return redirect()->to('/login');
            }

            // Legacy/user roles with an explicit RBAC assignment are promoted
            // before the session is issued. Disabled warehouse and unknown
            // roles never enter the admin branch.
            if ($userRole !== '5') {
                $this->userModel->where('email', (string) $getUser['email'])
                    ->whereIn('role', ['0', '3', '4'])
                    ->set(['role' => '5'])
                    ->update();
                $getUser = $this->userModel->getUser((string) $getUser['email']);
                if (!$getUser || (string) ($getUser['role'] ?? '') !== '5') {
                    session()->setFlashdata('msg', 'Akses admin belum aktif untuk email ini.');
                    return redirect()->to('/login');
                }
            }

            $ses_data = [
                'active' => '1',
                'nama' => 'Admin Ilena',
                'email' => $getUser['email'],
                'role' => '5',
                'admin_rbac' => true,
                'admin_home' => $rbacService->firstAllowedAdminUrl((string)$getUser['email']),
                'isLogin' => true
            ];
            $buyerProfile = $this->pembeliModel->getPembeli((string)$getUser['email']);
            if ($buyerProfile) {
                $ses_data += [
                    'alamat' => json_decode((string)($buyerProfile['alamat'] ?? '[]'), true) ?: [],
                    'nohp' => $buyerProfile['nohp'] ?? '',
                    'wishlist' => json_decode((string)($buyerProfile['wishlist'] ?? '[]'), true) ?: [],
                    'keranjang' => json_decode((string)($buyerProfile['keranjang'] ?? '[]'), true) ?: [],
                ];
            }
            session()->regenerate(true);
            session()->set($ses_data);
            return redirect()->to($ses_data['admin_home']);
        }

        if ($getUser['role'] == '0' || $getUser['role'] == '4') {
            $getPembeli = $this->pembeliModel->getPembeli($email);
            if (!$getPembeli) {
                $this->pembeliModel->insert([
                    'nama' => ucwords(str_replace(['_', '.'], ' ', strtok((string)$email, '@') ?: 'Customer')),
                    'email' => $email,
                    'nohp' => '',
                    'alamat' => json_encode([]),
                    'wishlist' => json_encode([]),
                    'keranjang' => json_encode([]),
                ]);
                $getPembeli = $this->pembeliModel->getPembeli($email) ?? [
                    'nama' => '', 'alamat' => '[]', 'nohp' => '', 'wishlist' => '[]', 'keranjang' => '[]',
                ];
            }
            $ses_data = [
                'active' => '1',
                'email' => $getUser['email'],
                'role' => '0',
                'nama' => $getPembeli['nama'],
                'alamat' => json_decode($getPembeli['alamat'], true),
                'nohp' => $getPembeli['nohp'],
                'wishlist' => json_decode($getPembeli['wishlist'], true),
                'keranjang' => json_decode($getPembeli['keranjang'], true),
                'isLogin' => true
            ];
            session()->regenerate(true);
            session()->set($ses_data);
            $this->mergePendingGuestState();
            return redirect()->to(!empty(session()->get('keranjang')) ? '/cart' : site_url('/'));
        } else if ($getUser['role'] == '1') {
            $ses_data = [
                'active' => '1',
                'nama' => 'Dev Ilena',
                'email' => $getUser['email'],
                'role' => $getUser['role'],
                'isLogin' => true
            ];
            session()->regenerate(true);
            session()->set($ses_data);
            return redirect()->to('/admin');
        } else if (in_array((string)$getUser['role'], ['2', '3'], true)) {
            return redirect()->to('/login')->with('msg', 'Akses akun ini belum diatur. Hubungi Admin utama.');
        }
    }
    private function mergePendingGuestState(): void
    {
        if (!session()->get('isLogin') || (string)session()->get('active') !== '1') return;
        $cart = \App\Services\CheckoutState::mergeCart((array)session()->get('keranjang'), (array)session()->get('pending_guest_cart'));
        $wishlist = array_values(array_unique(array_merge((array)session()->get('wishlist'), (array)session()->get('pending_guest_wishlist')), SORT_REGULAR));
        $addresses = (array)session()->get('alamat');
        foreach ((array)session()->get('pending_guest_addresses') as $address) {
            if (!is_array($address) || empty($address['alamat_lengkap'])) continue;
            $address['email_pemesan'] = (string)session()->get('email');
            if (!in_array($address, $addresses, true)) $addresses[] = $address;
        }
        $addresses = array_slice($addresses, 0, 50);
        session()->set(['keranjang'=>$cart,'wishlist'=>$wishlist,'alamat'=>$addresses]);
        $this->pembeliModel->where('email', (string)session()->get('email'))->set([
            'keranjang'=>json_encode($cart),'wishlist'=>json_encode($wishlist),'alamat'=>json_encode($addresses),
        ])->update();
        session()->remove(['pending_guest_cart','pending_guest_wishlist','pending_guest_addresses']);
    }

    public function editSandi($path)
    {
        $sandiLama = (string) $this->request->getPost('sandiLama');
        $sandi = (string) $this->request->getPost('sandi');
        $sandiKonfirm = (string) $this->request->getPost('sandiKonfirm');
        $email = strtolower(trim((string) session()->get('email')));
        $path = in_array($path, ['account', 'order'], true) ? $path : 'account';
        $user = $email !== '' ? $this->userModel->findByEmailIdentity($email) : null;
        if (!$user || empty($user['sandi']) || !password_verify($sandiLama, (string) $user['sandi'])) {
            session()->setFlashdata('msg-sandi', 'Kata sandi saat ini tidak sesuai.');
            return redirect()->to('/' . $path);
        }
        if (strlen($sandi) < 12 || strlen($sandi) > 72 || str_contains($sandi, "\0")) {
            session()->setFlashdata('msg-sandi', 'Kata sandi baru harus terdiri dari 12–72 byte.');
            return redirect()->to('/' . $path);
        }
        if (!hash_equals($sandi, $sandiKonfirm)) {
            session()->setFlashdata('msg-sandi', 'Sandi yang terkonfirmasi tidak cocok');
            return redirect()->to('/' . $path);
        }
        $updated = $this->userModel->where(['email' => $user['email']])->set([
            'sandi' => password_hash($sandi, PASSWORD_DEFAULT),
        ])->update();
        if (!$updated) {
            session()->setFlashdata('msg-sandi', 'Perubahan kata sandi gagal. Silakan coba kembali.');
            return redirect()->to('/' . $path);
        }
        // Rotate the current session ID after a credential change.
        session()->regenerate(true);
        session()->setFlashdata('msg-sandi', 'Sandi Anda berhasil diubah');
        return redirect()->to('/' . $path);
    }
    public function register()
    {
        $authFormGuard = new \App\Services\Auth\AuthFormGuard();
        $data = [
            'title' => 'Membuat Akun',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'authFormNonce' => $authFormGuard->issue('register'),
            'val' => [
                'val_nama' => session()->getFlashdata('val-nama'),
                'val_email' => session()->getFlashdata('val-email'),
                'val_sandi' => session()->getFlashdata('val-sandi'),
                'val_nohp' => session()->getFlashdata('val-nohp'),
                'val_syarat' => session()->getFlashdata('val-syarat'),
                'msg' => session()->getFlashdata('msg'),
                // 'val_alamat' => session()->getFlashdata('val-alamat'),
            ]
        ];
        return view('pages/register', $data);
    }
    public function actionRegister()
    {
        $registration = \App\Services\Auth\RegistrationInput::normalize((array) $this->request->getPost());
        $emailIdentity = $registration['email'];
        $guard = new \App\Services\Auth\AuthFormGuard();
        $validForm = $guard->verify('register', $this->request->getPost('auth_form_nonce'), [
            $this->request->getPost('website'),
            $this->request->getPost('ref_code'),
            $this->request->getPost('company_website'),
        ], 3);
        if (!$validForm) {
            session()->setFlashdata('msg', 'Form pendaftaran kedaluwarsa atau tidak valid. Silakan muat ulang halaman.');
            return redirect()->to('/register');
        }
        if (!(new \App\Services\Auth\AuthAttemptLimiter())->allow('register', (string)$this->request->getIPAddress(), $emailIdentity, 8, 3, 3600)) {
            session()->setFlashdata('msg', 'Terlalu banyak percobaan pendaftaran. Coba lagi nanti.');
            return redirect()->to('/register')->withInput();
        }
        $captchaError = (new \App\Services\Auth\RecaptchaVerifier())->error($this->request->getPost('g-recaptcha-response'));
        if ($captchaError !== null) {
            session()->setFlashdata('msg', $captchaError);
            return redirect()->to('/register')->withInput();
        }

        $errors = (new \App\Services\Auth\RegistrationInput())->errors($registration);
        if (!isset($errors['email']) && $this->userModel->findByEmailIdentity($emailIdentity)) {
            $errors['email'] = 'Email sudah terdaftar. Silakan masuk dengan akun Anda.';
        }
        if ($errors !== []) {
            foreach (['nama', 'email', 'sandi', 'nohp', 'validasi-syarat'] as $field) {
                session()->setFlashdata('val-' . ($field === 'validasi-syarat' ? 'syarat' : $field), $errors[$field] ?? '');
            }
            return redirect()->to('/register')->withInput();
        }

        $emailUser = $registration['email'];
        $atomic = null;
        try {
            $atomic = new \App\Services\AtomicOperation(\Config\Database::connect(), 'ilena-register-' . substr(hash('sha256', $emailUser), 0, 48));
            // Recheck under a per-email lock: two concurrent signups must not create two identities.
            if ($this->userModel->findByEmailIdentity($emailUser)) {
                session()->setFlashdata('val-email', 'Email sudah terdaftar. Silakan masuk dengan akun Anda.');
                return redirect()->to('/register')->withInput();
            }

            $otp_number = random_int(100000, 999999);
            $waktu_otp = time() + 300;
            $d = strtotime("+425 Minutes");
            $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
            $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

            $namaUser = $registration['nama'];
            $nohpUser = $registration['nohp'];

            $email = \Config\Services::email();
            $emailConfig = config('Email');
            $email->setFrom($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com', 'Ilena Furniture');
            $email->setTo($emailUser);
            $email->setSubject('ILENA Store - Verifikasi OTP');
            $email->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
            if (!$email->send()) {
                log_message('error', 'Registration verification email could not be sent.');
                session()->setFlashdata('msg', 'Email verifikasi belum dapat dikirim. Coba lagi nanti.');
                return redirect()->to('/register')->withInput();
            }

            $insertedUser = $this->userModel->insert([
                'email' => $emailUser,
                'sandi' => password_hash($registration['sandi'], PASSWORD_DEFAULT),
                'role' => '0',
                'otp' => $otp_number,
                'active' => '0',
                'waktu_otp' => $waktu_otp
            ]);
            if ($insertedUser === false) throw new \RuntimeException('Registration user insert failed.');
            $pembeliPayload = [
                'nama' => $namaUser,
                'email' => $emailUser,
                'nohp' => $nohpUser,
                'alamat' => json_encode([]),
                'wishlist' => json_encode([]),
                'keranjang' => json_encode([])
            ];

            $existingPembeli = $this->pembeliModel->where('LOWER(TRIM(email))', $emailUser)->first();
            if ($existingPembeli) {
                $savedProfile = $this->pembeliModel->where('email', $existingPembeli['email'])->set([
                    'email' => $emailUser,
                    'nama' => $namaUser,
                    'nohp' => $nohpUser,
                ])->update();
            } else {
                $savedProfile = $this->pembeliModel->insert($pembeliPayload);
            }
            if ($savedProfile === false) throw new \RuntimeException('Registration profile save failed.');
            $getUser = $this->userModel->getUser($emailUser);
            if (!$getUser) throw new \RuntimeException('Registration account missing.');
            $atomic->commit();
        } catch (\Throwable $error) {
            unset($atomic); // Roll back account/profile together before returning an error.
            log_message('error', 'Registration persistence failed: {type}', ['type' => get_class($error)]);
            session()->setFlashdata('msg', 'Pendaftaran belum berhasil disimpan. Silakan coba lagi.');
            return redirect()->to('/register')->withInput();
        }

        if (!session()->get('isLogin')) session()->set([
            'pending_guest_cart'=>(array)session()->get('keranjang'),'pending_guest_wishlist'=>(array)session()->get('wishlist'),'pending_guest_addresses'=>(array)session()->get('alamat'),
        ]);
        $ses_data = ['alamat', 'wishlist', 'keranjang'];
        session()->remove($ses_data);
        $ses_data = [
            'email' => $getUser['email'],
            'active' => '0',
            'role' => '0',
            'admin_rbac' => false,
            'admin_home' => '',
            'isLogin' => true
        ];
        session()->regenerate(true);
        session()->set($ses_data);
        session()->set('otp_last_sent_at', time());
        session()->setFlashdata('msg', "OTP telah dikirim ke email " . $emailUser . " dan berlaku hingga " . $waktu_otp_tanggal);
        return redirect()->to('/verify');
    }
    public function verify()
    {
        $email = session()->get('email');
        if (!$email || !$this->userModel->getUser($email)) {
            session()->setFlashdata('msg', 'Sesi verifikasi sudah berakhir. Silakan daftar/login ulang.');
            return redirect()->to('/login');
        }

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
        if (!$email || !$getUser) {
            session()->setFlashdata('msg', 'Sesi verifikasi sudah berakhir. Silakan daftar/login ulang.');
            return redirect()->to('/login');
        }
        if (in_array((string)($getUser['role'] ?? ''), ['0', '4'], true)
            && (new \App\Services\AccountSuspensionService())->isSuspended((string)$email)) {
            session()->destroy();
            session()->setFlashdata('msg', 'Akun Anda ditangguhkan. Silakan hubungi admin.');
            return redirect()->to('/login');
        }
        if ((string)($getUser['active'] ?? '1') !== '0') {
            return redirect()->to('/');
        }

        if (!(new \App\Services\Auth\AuthAttemptLimiter())->allow('otp_verify', (string)$this->request->getIPAddress(), (string)$email, 30, 8, 300)) {
            return redirect()->to('/verify')->with('msg', 'Terlalu banyak percobaan OTP. Tunggu beberapa saat sebelum mencoba lagi.');
        }
        if (preg_match('/^\d{6}$/', (string)$otp) !== 1 || !hash_equals((string)$getUser['otp'], (string)$otp)) {
            session()->setFlashdata('val_verify', "OTP salah");
            return redirect()->to("/verify");
        }
        $waktu_otp = time();
        if ($waktu_otp > (int)$getUser['waktu_otp']) {
            $otp_number = random_int(100000, 999999);
            $waktu_otp = time() + 300;
            $d = strtotime("+425 Minutes");
            $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
            $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

            $sendemail = \Config\Services::email();
            $emailConfig = config('Email');
            $sendemail->setFrom($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com', 'Ilena Furniture');
            $sendemail->setTo($email);
            $sendemail->setSubject('ILENA Store - Verifikasi OTP');
            $sendemail->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
            if (!$sendemail->send()) {
                log_message('error', 'Expired verification OTP email could not be sent.');
                session()->setFlashdata('msg', 'Email verifikasi belum dapat dikirim. Coba lagi nanti.');
                return redirect()->to('/verify');
            }

            $this->userModel->where('email', $email)->set([
                'otp' => $otp_number,
                'waktu_otp' => $waktu_otp
            ])->update();
            session()->set('otp_last_sent_at', time());
            session()->setFlashdata('msg', "OTP telah diperbarui dan sudah dikirim kembali ke email " . $email);
            return redirect()->to("/verify");
        }

        $getPembeli = $this->pembeliModel->getPembeli($email);
        if (!$getPembeli) {
            $this->pembeliModel->insert([
                'nama' => ucwords(str_replace(['_', '.'], ' ', strtok((string)$email, '@') ?: 'Customer')),
                'email' => $email,
                'nohp' => '',
                'alamat' => json_encode([]),
                'wishlist' => json_encode([]),
                'keranjang' => json_encode([]),
            ]);
            $getPembeli = $this->pembeliModel->getPembeli($email);
        }

        $ses_data = [
            'active' => '1',
            'role' => $getUser['role'],
            'nama' => $getPembeli['nama'] ?? '',
            'alamat' => json_decode($getPembeli['alamat'] ?? '[]', true) ?: [],
            'nohp' => $getPembeli['nohp'] ?? '',
            'wishlist' => json_decode($getPembeli['wishlist'] ?? '[]', true) ?: [],
            'keranjang' => json_decode($getPembeli['keranjang'] ?? '[]', true) ?: []
        ];
        $this->userModel->where('email', $email)->set([
            'active' => '1',
            'otp' => '0',
            'waktu_otp' => '0'
        ])->update();
        session()->regenerate(true);
        session()->set($ses_data);
        session()->setFlashdata('msg_active', true);
        $this->mergePendingGuestState();
        return redirect()->to(!empty(session()->get('keranjang')) ? '/cart' : site_url('/'));
    }
    public function kirimOTP()
    {
        $emailUser = session()->get('email');
        $getUser = $this->userModel->getUser($emailUser);
        if (!$emailUser || !$getUser) {
            session()->setFlashdata('msg', 'Sesi verifikasi sudah berakhir. Silakan daftar/login ulang.');
            return redirect()->to('/login');
        }
        if (in_array((string)($getUser['role'] ?? ''), ['0', '4'], true)
            && (new \App\Services\AccountSuspensionService())->isSuspended((string)$emailUser)) {
            session()->destroy();
            session()->setFlashdata('msg', 'Akun Anda ditangguhkan. Silakan hubungi admin.');
            return redirect()->to('/login');
        }
        if ((string)($getUser['active'] ?? '1') !== '0') {
            return redirect()->to('/');
        }
        if (!(new \App\Services\Auth\AuthAttemptLimiter())->allow('otp_resend', (string)$this->request->getIPAddress(), (string)$emailUser, 10, 5, 3600)) {
            session()->setFlashdata('msg', 'Terlalu banyak permintaan kode verifikasi. Coba lagi nanti.');
            return redirect()->to('/verify');
        }
        $lastSent = (int)session()->get('otp_last_sent_at');
        if ($lastSent > 0 && time() - $lastSent < 60) {
            session()->setFlashdata('msg', 'Tunggu sebentar sebelum meminta kode OTP baru.');
            return redirect()->to('/verify');
        }

        $otp_number = random_int(100000, 999999);
        $waktu_otp = time() + 300;
        $d = strtotime("+425 Minutes");
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $waktu_otp_tanggal = date("d", $d) . " " . $bulan[date("m", $d) - 1] . " " . date("Y H:i:s", $d);

        $email = \Config\Services::email();
        $emailConfig = config('Email');
        $email->setFrom($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com', 'Ilena Furniture');
        $email->setTo($emailUser);
        $email->setSubject('ILENA Store - Verifikasi OTP');
        $email->setMessage("<p>Berikut kode OTP verifikasi</p><h1>" . $otp_number . "</h1><p>Kode ini berlaku hingga " . $waktu_otp_tanggal . "</p>");
        if (!$email->send()) {
            log_message('error', 'Verification OTP resend email could not be sent.');
            session()->setFlashdata('msg', 'Email verifikasi belum dapat dikirim. Coba lagi nanti.');
            return redirect()->to('/verify');
        }

        $this->userModel->where('email', $emailUser)->set([
            'otp' => $otp_number,
            'waktu_otp' => $waktu_otp
        ])->update();
        session()->set('otp_last_sent_at', time());

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
        return view('pages/tentang-clean', $data);
    }
    public function partner()
    {
        $this->ensurePartnerTable();
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 80);
        $requestedCity = mb_substr(trim((string) $this->request->getGet('city')), 0, 100);
        $perPage = 10;
        $requestedPage = max(1, (int) $this->request->getGet('page'));
        $allPartnerRows = (new PartnerModel())
            ->where('active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $normalizeSearch = static function (string $value): string {
            $ascii = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : $value;
            $value = mb_strtolower($ascii !== false ? $ascii : $value);
            return trim((string) preg_replace('/[^a-z0-9]+/u', ' ', $value));
        };
        $searchWords = static function (string $value) use ($normalizeSearch): array {
            $words = preg_split('/\s+/', $normalizeSearch($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            return array_values(array_unique($words));
        };
        $synonymGroups = [
            ['toko', 'gerai', 'outlet', 'store', 'stores', 'mitra', 'partner', 'showroom', 'dealer', 'reseller'],
            ['mebel', 'meubel', 'furniture', 'furnitur', 'perabot'],
            ['dekat', 'sekitar', 'near', 'lokasi'],
        ];
        $stopWords = ['di', 'ke', 'dari', 'dan', 'yang', 'ada', 'mana', 'dimana', 'berada', 'cari', 'mencari', 'carikan', 'tolong', 'saya', 'untuk', 'wilayah', 'daerah', 'kota', 'kabupaten', 'provinsi', 'in', 'where', 'is', 'the', 'please', 'find', 'around'];
        $cities = [];
        foreach ($allPartnerRows as $row) {
            $cityName = trim((string) ($row['city'] ?? ''));
            if ($cityName !== '') $cities[$normalizeSearch($cityName)] = $cityName;
        }
        natcasesort($cities);

        $genericSearchWords = array_merge($synonymGroups[0], $synonymGroups[2]);
        $tokens = array_values(array_filter($searchWords($query), static fn (string $word): bool => !in_array($word, $stopWords, true)));
        $cityFilter = '';
        if ($requestedCity !== '') {
            foreach ($cities as $cityKey => $cityName) {
                if ($normalizeSearch($requestedCity) === $cityKey) { $cityFilter = $cityName; break; }
            }
        }
        if ($cityFilter === '' && $tokens !== []) {
            foreach ($cities as $cityKey => $cityName) {
                foreach ($tokens as $token) {
                    $cityTokens = $searchWords($cityKey);
                    $cityTypoMatch = false;
                    foreach ($cityTokens as $cityToken) {
                        if (strlen($token) >= 4 && levenshtein($token, $cityToken) <= (strlen($token) >= 8 ? 2 : 1)) {
                            $cityTypoMatch = true;
                            break;
                        }
                    }
                    if (str_contains($normalizeSearch($query), $cityKey) || $cityTypoMatch) {
                        $cityFilter = $cityName;
                        $tokens = array_values(array_diff($tokens, $searchWords($cityName)));
                        break 2;
                    }
                }
            }
        }
        $tokens = array_values(array_filter($tokens, static function (string $token) use ($genericSearchWords): bool {
            if (in_array($token, $genericSearchWords, true)) return false;
            return strlen($token) > 1;
        }));

        $scorePartner = static function (array $row) use ($tokens, $cityFilter, $normalizeSearch, $searchWords, $synonymGroups): int {
            $name = $normalizeSearch((string) ($row['name'] ?? ''));
            $city = $normalizeSearch((string) ($row['city'] ?? ''));
            $address = $normalizeSearch((string) ($row['address'] ?? ''));
            if ($cityFilter !== '' && $city !== $normalizeSearch($cityFilter)) return 0;
            if ($tokens === []) return 1;
            $fields = [[$name, 10], [$city, 9], [$address, 4]];
            $score = 0;
            foreach ($tokens as $token) {
                $equivalents = [$token];
                foreach ($synonymGroups as $group) if (in_array($token, $group, true)) $equivalents = array_merge($equivalents, $group);
                $best = 0;
                foreach ($fields as [$field, $weight]) {
                    $fieldWords = $searchWords($field);
                    foreach (array_unique($equivalents) as $equivalent) {
                        if (str_contains($field, $equivalent)) $best = max($best, $weight + 3);
                        foreach ($fieldWords as $fieldWord) {
                            $distance = levenshtein($equivalent, $fieldWord);
                            $maxDistance = strlen($equivalent) >= 8 ? 2 : (strlen($equivalent) >= 5 ? 1 : 0);
                            if ($distance <= $maxDistance) $best = max($best, $weight - $distance);
                        }
                    }
                }
                $score += $best;
            }
            return $score;
        };

        $rankedRows = [];
        foreach ($allPartnerRows as $row) {
            $score = $scorePartner($row);
            if (($query === '' && $cityFilter === '') || $score > 0) $rankedRows[] = ['row' => $row, 'score' => $score];
        }
        usort($rankedRows, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
        $filteredRows = array_column($rankedRows, 'row');
        $totalPartners = count($filteredRows);
        $totalPages = max(1, (int) ceil($totalPartners / $perPage));
        $page = min($requestedPage, $totalPages);
        $pageRows = array_slice($filteredRows, ($page - 1) * $perPage, $perPage);
        $suggestionRows = [];
        if ($totalPartners === 0 && $tokens !== []) {
            $nearMatches = [];
            foreach ($allPartnerRows as $row) {
                $candidateWords = array_merge(
                    $searchWords((string) ($row['name'] ?? '')),
                    $searchWords((string) ($row['city'] ?? '')),
                    $searchWords((string) ($row['address'] ?? ''))
                );
                $distance = 0;
                foreach ($tokens as $token) {
                    $bestDistance = PHP_INT_MAX;
                    foreach ($candidateWords as $candidateWord) $bestDistance = min($bestDistance, levenshtein($token, $candidateWord));
                    $distance += $bestDistance;
                }
                $nearMatches[] = ['row' => $row, 'distance' => $distance];
            }
            usort($nearMatches, static fn (array $a, array $b): int => $a['distance'] <=> $b['distance']);
            $suggestionRows = array_column(array_slice($nearMatches, 0, 3), 'row');
        }
        $partners = array_map(static function (array $row): array {
            $lat = $row['lat'] ?? null;
            $lng = $row['lng'] ?? null;
            $hasCoords = $lat !== null && $lat !== '' && $lng !== null && $lng !== '';

            return [
                'name' => $row['name'] ?? '',
                'city' => $row['city'] ?? '',
                'address' => $row['address'] ?? '',
                'href' => $row['maps_url'] ?: ('https://www.google.com/maps/search/?api=1&query=' . rawurlencode($row['address'] ?? '')),
                'img' => $row['image_url'] ?? '',
                'coords' => $hasCoords ? [(float) $lat, (float) $lng] : null,
            ];
        }, $pageRows);

        // The partner directory is paginated, but the overview map must include every
        // active store that has coordinates so the map represents the full network.
        $mapPartnerRows = $allPartnerRows;
        $mapPartners = array_values(array_filter(array_map(static function (array $row): array {
            $lat = (float) ($row['lat'] ?? 0);
            $lng = (float) ($row['lng'] ?? 0);
            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ($lat === 0.0 && $lng === 0.0)) {
                return [];
            }

            return [
                'name' => (string) ($row['name'] ?? ''),
                'city' => (string) ($row['city'] ?? ''),
                'address' => (string) ($row['address'] ?? ''),
                'lat' => $lat,
                'lng' => $lng,
            ];
        }, $mapPartnerRows), static fn (array $partner): bool => $partner !== []));

        $data = [
            'title' => 'Mitra Kami',
            'navbar' => $this->getNavbarData(),
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'partners' => $partners,
            'mapPartners' => $mapPartners,
            'partnerSearchIndex' => array_map(static fn (array $row): array => [
                'name' => (string) ($row['name'] ?? ''),
                'city' => (string) ($row['city'] ?? ''),
                'address' => (string) ($row['address'] ?? ''),
            ], $allPartnerRows),
            'partnerCities' => array_values($cities),
            'partnerCityFilter' => $cityFilter,
            'partnerSuggestions' => $suggestionRows,
            'partnerQuery' => $query,
            'partnerTotal' => $totalPartners,
            'partnerPage' => $page,
            'partnerPages' => $totalPages,
            'partnerPerPage' => $perPage,
        ];
        return view('pages/mitra-clean', $data);
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

    private function gantinamakekecil($batchSize = 20)
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



    public function productCoverImage($idBarang)
    {
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $idBarang);
        if ($safeId === '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Gambar tidak ditemukan');
        }

        $requestedSlot = trim((string) $this->request->getGet('slot'));
        $product = $requestedSlot === ''
            ? ($this->barangModel->getBarangAdmin($safeId) ?: $this->barangModel->where('id', $safeId)->first())
            : null;
        $slot = preg_replace('/[^0-9]/', '', $requestedSlot) ?: '1';
        $preferThumbnail = $this->request->getGet('size') === '300';
        if ($product && !empty($product['varian']) && $requestedSlot === '') {
            $varian = json_decode($product['varian'], true) ?: [];
            if (!empty($varian[0]['urutan_gambar'])) {
                $slots = array_values(array_filter(array_map('trim', explode(',', (string) $varian[0]['urutan_gambar']))));
                $slot = $slots[0] ?? '1';
            }
        }

        // Avoid a remote R2 HEAD request when the requested derivative is
        // already present locally. This route is a compatibility/fallback
        // path; the storefront normally loads public R2 URLs directly.
        $localImageCandidates = $preferThumbnail
            ? [
                "uploads/product-images/300/{$safeId}-{$slot}.webp",
                "img/barang/300/{$safeId}-{$slot}.webp",
                "uploads/product-images/300/{$safeId}.webp",
                "img/barang/300/{$safeId}.webp",
                "uploads/product-images/1000/{$safeId}-{$slot}.webp",
                "uploads/product-images/3000/{$safeId}-{$slot}.webp",
                "img/barang/1000/{$safeId}-{$slot}.webp",
                "img/barang/3000/{$safeId}-{$slot}.webp",
            ]
            : ($requestedSlot !== ''
                ? [
                    "uploads/product-images/1000/{$safeId}-{$slot}.webp",
                    "uploads/product-images/3000/{$safeId}-{$slot}.webp",
                    "img/barang/1000/{$safeId}-{$slot}.webp",
                    "img/barang/3000/{$safeId}-{$slot}.webp",
                ]
                : [
                    "uploads/product-images/300/{$safeId}.webp",
                    "img/barang/300/{$safeId}.webp",
                    "uploads/product-images/1000/{$safeId}-{$slot}.webp",
                    "uploads/product-images/3000/{$safeId}-{$slot}.webp",
                    "img/barang/1000/{$safeId}-{$slot}.webp",
                    "img/barang/3000/{$safeId}-{$slot}.webp",
                ]);
        $hasLocalImageCandidate = false;
        foreach ($localImageCandidates as $relativePath) {
            $absolutePath = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
            if (is_file($absolutePath)) {
                $hasLocalImageCandidate = true;
                break;
            }
        }

        if ($this->r2ProductImages->enabled() && !$hasLocalImageCandidate) {
            $hasSlotRequest = $requestedSlot !== '';
            // Public image fallbacks must never wait on remote R2 HEAD probes.
            // A known-positive cache entry is safe to redirect; only explicit
            // admin proxy requests may perform a fresh existence check.
            $allowRemoteProbe = $this->request->getGet('proxy') === '1';
            if ($preferThumbnail) {
                $r2Candidates = ["products/300/{$safeId}-{$slot}.webp", "products/300/{$safeId}.webp", "products/1000/{$safeId}-{$slot}.webp", "products/3000/{$safeId}-{$slot}.webp"];
            } else {
                $r2Candidates = $hasSlotRequest
                    ? ["products/1000/{$safeId}-{$slot}.webp", "products/3000/{$safeId}-{$slot}.webp"]
                    : ["products/300/{$safeId}.webp", "products/1000/{$safeId}-{$slot}.webp", "products/3000/{$safeId}-{$slot}.webp"];
            }
            foreach ($r2Candidates as $key) {
                $exists = $allowRemoteProbe
                    ? $this->r2ProductImages->objectExists($key)
                    : $this->r2ProductImages->cachedObjectExists($key) === true;
                if ($exists) {
                    if ($this->request->getGet('proxy') === '1') {
                        $object = $this->r2ProductImages->getObject($key);
                        if ($object) {
                            $etag = '"' . md5($object['body']) . '"';
                            if ($this->request->getHeaderLine('If-None-Match') === $etag) {
                                return $this->response
                                    ->setStatusCode(304)
                                    ->setHeader('ETag', $etag)
                                    ->setHeader('Cache-Control', 'private, max-age=60');
                            }
                            return $this->response
                                ->setHeader('Content-Type', $object['contentType'])
                                ->setHeader('Content-Length', (string) strlen($object['body']))
                                ->setHeader('Cache-Control', 'private, max-age=60')
                                ->setHeader('ETag', $etag)
                                ->setBody($object['body']);
                        }
                    }
                    $redirect = $this->r2ProductImages->redirectResponse(
                        $key,
                        (string) $this->request->getGet('v'),
                    );
                    if ($redirect) {
                        return $redirect;
                    }
                }
            }
        }

        $publicPath = function (string $relativePath): string {
            return rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
        };

        $upload1000 = $publicPath("uploads/product-images/1000/{$safeId}-{$slot}.webp");
        $upload3000 = $publicPath("uploads/product-images/3000/{$safeId}-{$slot}.webp");
        $upload300Slot = $publicPath("uploads/product-images/300/{$safeId}-{$slot}.webp");
        $upload300 = $publicPath("uploads/product-images/300/{$safeId}.webp");
        $source1000 = $publicPath("img/barang/1000/{$safeId}-{$slot}.webp");
        $source3000 = $publicPath("img/barang/3000/{$safeId}-{$slot}.webp");
        $source300Slot = $publicPath("img/barang/300/{$safeId}-{$slot}.webp");
        $fallback300 = $publicPath("img/barang/300/{$safeId}.webp");
        $thumbnailSource = is_file($upload300Slot) ? $upload300Slot : (is_file($source300Slot) ? $source300Slot : (is_file($upload300) ? $upload300 : (is_file($fallback300) ? $fallback300 : null)));
        $fullSizeSource = is_file($upload1000) ? $upload1000 : (is_file($upload3000) ? $upload3000 : (is_file($source1000) ? $source1000 : (is_file($source3000) ? $source3000 : null)));
        $thumbnailIsCurrent = $thumbnailSource !== null
            && ($fullSizeSource === null || filemtime($thumbnailSource) >= filemtime($fullSizeSource));
        // A stale 300px derivative is safe to remove when its full-resolution source exists.
        if ($thumbnailSource !== null && $fullSizeSource !== null && !$thumbnailIsCurrent
            && is_writable(dirname($thumbnailSource)) && @unlink($thumbnailSource)
        ) {
            $thumbnailSource = null;
        }
        $source = $preferThumbnail && $thumbnailIsCurrent
            ? $thumbnailSource
            : $fullSizeSource;

        if (!$source && $product && !empty($product['varian'])) {
            $varian = json_decode($product['varian'], true) ?: [];
            foreach ($varian as $v) {
                $slots = array_values(array_filter(array_map('trim', explode(',', (string)($v['urutan_gambar'] ?? '')))));
                foreach ($slots as $candidateSlot) {
                    $candidateUpload1000 = $publicPath("uploads/product-images/1000/{$safeId}-{$candidateSlot}.webp");
                    $candidateUpload3000 = $publicPath("uploads/product-images/3000/{$safeId}-{$candidateSlot}.webp");
                    $candidate1000 = $publicPath("img/barang/1000/{$safeId}-{$candidateSlot}.webp");
                    $candidate3000 = $publicPath("img/barang/3000/{$safeId}-{$candidateSlot}.webp");
                    if (is_file($candidateUpload1000) || is_file($candidateUpload3000) || is_file($candidate1000) || is_file($candidate3000)) {
                        $slot = $candidateSlot;
                        $source = is_file($candidateUpload1000) ? $candidateUpload1000 : (is_file($candidateUpload3000) ? $candidateUpload3000 : (is_file($candidate1000) ? $candidate1000 : $candidate3000));
                        break 2;
                    }
                }
            }
        }

        if (!$source) {
            $source = $thumbnailSource;
        }

        if (!$source) {
            $legacyCandidates = [];
            $legacy3000 = $this->gambarBarang3000Model->getGambar($safeId);
            $legacy1000 = $this->gambarBarangModel->getGambar($safeId);
            if (!empty($legacy1000['gambar' . $slot])) {
                $legacyCandidates[] = ['value' => $legacy1000['gambar' . $slot], 'dir' => 'img/barang/1000'];
            }
            if (!empty($legacy3000['gambar' . $slot])) {
                $legacyCandidates[] = ['value' => $legacy3000['gambar' . $slot], 'dir' => 'img/barang/3000'];
            }
            if (!empty($product['gambar'])) {
                $legacyCandidates[] = ['value' => $product['gambar'], 'dir' => 'img/barang/300'];
            }

            foreach ($legacyCandidates as $candidate) {
                $value = $candidate['value'];
                if (!is_string($value) || $value === '') {
                    continue;
                }

                if (preg_match('#^[A-Za-z0-9._-]+\.(?:jpe?g|png|webp|avif)$#i', $value)) {
                    $candidateFile = $publicPath($candidate['dir'] . '/' . $value);
                    if (is_file($candidateFile)) {
                        $source = $candidateFile;
                        break;
                    }
                }

                if (@getimagesizefromstring($value) !== false) {
                    $etag = '"' . md5($value) . '"';
                    if ($this->request->getHeaderLine('If-None-Match') === $etag) {
                        return $this->response
                            ->setStatusCode(304)
                            ->setHeader('ETag', $etag)
                            ->setHeader('Cache-Control', 'public, max-age=86400');
                    }

                    return $this->response
                        ->setHeader('Content-Type', 'image/webp')
                        ->setHeader('Content-Length', (string) strlen($value))
                        ->setHeader('Cache-Control', 'public, max-age=86400')
                        ->setHeader('ETag', $etag)
                        ->setBody($value);
                }
            }
        }

        if (!$source) {
            log_message('error', 'Product cover gambar tidak ditemukan: {id} slot {slot}', ['id' => $safeId, 'slot' => $slot]);
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Gambar tidak ditemukan');
        }

        $mtime = (string) filemtime($source);
        $requestVersion = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $this->request->getGet('v'));
        $cacheVersion = $requestVersion !== '' ? $requestVersion : $mtime;
        $cacheDir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'product-covers';
        if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $safeId . '-' . $slot . '-' . $mtime . '-' . $cacheVersion . '.webp';

        $sourceIsRequestedThumbnail = $preferThumbnail && $thumbnailSource !== null && $source === $thumbnailSource;
        if ($sourceIsRequestedThumbnail) {
            $cacheFile = $source;
        } elseif (!is_file($cacheFile) && is_writable($cacheDir)) {
            try {
                \Config\Services::image()
                    ->withFile($source)
                    ->resize(300, 300, true, 'height')
                    ->save($cacheFile);
            } catch (\Throwable $e) {
                log_message('error', 'Product cover cache gagal: {msg}', ['msg' => $e->getMessage()]);
                $cacheFile = $source;
            }
        }

        $serveFile = is_file($cacheFile) ? $cacheFile : $source;
        $etag = '"' . md5($serveFile . '|' . filemtime($serveFile) . '|' . filesize($serveFile)) . '"';
        if ($this->request->getHeaderLine('If-None-Match') === $etag) {
            return $this->response
                ->setStatusCode(304)
                ->setHeader('ETag', $etag)
                ->setHeader('Cache-Control', $requestVersion !== '' ? 'public, max-age=31536000, immutable' : 'public, max-age=86400');
        }

        return $this->response
            ->setHeader('Content-Type', 'image/webp')
            ->setHeader('Content-Length', (string) filesize($serveFile))
            ->setHeader('Cache-Control', $requestVersion !== '' ? 'public, max-age=31536000, immutable' : 'public, max-age=86400')
            ->setHeader('ETag', $etag)
            ->setBody(file_get_contents($serveFile));
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
