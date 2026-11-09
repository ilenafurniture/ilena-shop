<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\CatalogPricing;
use CodeIgniter\HTTP\ResponseInterface;

/** Read-only public catalog endpoints for customer apps. */
final class CatalogController extends BaseController
{
    private const MAX_PAGE_SIZE = 50;

    public function index(): ResponseInterface
    {
        $page = min(10000, $this->positiveInt($this->request->getGet('page'), 1));
        $perPage = min(self::MAX_PAGE_SIZE, $this->positiveInt($this->request->getGet('per_page'), 20));
        $query = $this->stringQueryValue('q');
        $category = $this->stringQueryValue('category');

        if (mb_strlen($query) > 100 || mb_strlen($category) > 100) {
            return $this->error('invalid_query', 'Search and category filters must be at most 100 characters.', 400);
        }

        $builder = db_connect()->table('barang')->where('active', '1');
        if ($query !== '') {
            $builder->groupStart()
                ->like('nama', $query)
                ->orLike('pencarian', $query)
                ->groupEnd();
        }
        if ($category !== '') {
            $builder->where('kategori', $category);
        }

        $total = (clone $builder)->countAllResults();
        $rows = $builder->select('id,nama,harga,deskripsi,kategori,subkategori,diskon,pakai_jadwal_diskon,diskon_mulai,diskon_selesai,varian')
            ->orderBy('nama', 'ASC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()->getResultArray();

        $locale = $this->locale();
        return $this->respond([
            'data' => array_map(fn (array $row): array => $this->serializeProduct($row, $locale, false), $rows),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
                'locale' => $locale,
            ],
        ], 200, 15);
    }

    public function show(string $id): ResponseInterface
    {
        if ($id === '' || strlen($id) > 64 || preg_match('/\A[A-Za-z0-9_-]+\z/', $id) !== 1) {
            return $this->error('not_found', 'Product not found.', 404);
        }

        $row = db_connect()->table('barang')
            ->select('id,nama,harga,deskripsi,kategori,subkategori,diskon,pakai_jadwal_diskon,diskon_mulai,diskon_selesai,varian')
            ->where('id', $id)->where('active', '1')->get()->getRowArray();
        if (!$row) {
            return $this->error('not_found', 'Product not found.', 404);
        }

        return $this->respond([
            'data' => $this->serializeProduct($row, $this->locale(), true),
            'meta' => ['locale' => $this->locale()],
        ], 200, 15);
    }

    public function categories(): ResponseInterface
    {
        $rows = db_connect()->table('barang')
            ->select('kategori')
            ->distinct()
            ->where('active', '1')
            ->where('kategori !=', '')
            ->orderBy('kategori', 'ASC')
            ->get()->getResultArray();

        return $this->respond([
            'data' => array_values(array_map(static fn (array $row): string => (string) $row['kategori'], $rows)),
            'meta' => ['locale' => $this->locale()],
        ], 200, 60);
    }

    private function serializeProduct(array $row, string $locale, bool $includeVariants): array
    {
        $pricing = CatalogPricing::forSale($row);
        $descriptionData = $row['deskripsi'] ?? [];
        if (is_string($descriptionData)) {
            $decoded = json_decode($descriptionData, true);
            $descriptionData = is_array($decoded) ? $decoded : ['deskripsi' => $descriptionData];
        }
        if (!is_array($descriptionData)) $descriptionData = [];

        $description = trim((string) ($locale === 'en'
            ? ($descriptionData['deskripsi_en'] ?? $descriptionData['deskripsi'] ?? '')
            : ($descriptionData['deskripsi'] ?? '')));

        $product = [
            'id' => (string) $row['id'],
            'name' => ilena_product_display_name($row, $locale),
            'slug' => strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', (string) $row['nama']), '-')),
            'description' => $description,
            'category' => (string) ($row['kategori'] ?? ''),
            'collection' => (string) ($row['subkategori'] ?? ''),
            'price' => [
                'currency' => 'IDR',
                'original' => (int) round((float) ($row['harga'] ?? 0)),
                'current' => CatalogPricing::unitPrice($pricing),
                'discount_percent' => (float) ($pricing['diskon'] ?? 0),
            ],
            'url' => site_url(($locale === 'en' ? 'en/' : '') . 'product/' . strtolower(str_replace(' ', '-', (string) $row['nama']))),
            'image_url' => site_url('product-cover/' . rawurlencode((string) $row['id']) . '?size=300'),
        ];

        if ($includeVariants) {
            $variants = json_decode((string) ($row['varian'] ?? '[]'), true);
            $product['variants'] = [];
            if (is_array($variants)) {
                $byName = [];
                foreach ($variants as $variant) {
                    if (!is_array($variant)) continue;
                    $name = trim((string) ($variant['nama'] ?? ''));
                    if ($name === '') continue;
                    $key = mb_strtolower($name);
                    $stock = max(0, (int) ($variant['stok'] ?? 0));
                    if (!isset($byName[$key])) $byName[$key] = ['name' => $name, 'stock' => $stock];
                    else $byName[$key]['stock'] = max((int) $byName[$key]['stock'], $stock);
                }
                foreach ($byName as $variant) {
                    $available = $this->availableStock((string) $row['id'], $variant['name'], (int) $variant['stock']);
                    $product['variants'][] = ['name' => $variant['name'], 'available_quantity' => $available, 'in_stock' => $available > 0];
                }
            }
        }

        return $product;
    }

    private function availableStock(string $productId, string $variant, int $stock): int
    {
        $db = db_connect();
        if (!$db->tableExists('checkout_stock_reservations')) return $stock;
        $row = $db->table('checkout_stock_reservations')->selectSum('quantity', 'reserved')
            ->where('product_id', $productId)->where('LOWER(variant_name)', mb_strtolower($variant))
            ->where('status', 'reserved')->where('expires_at >', gmdate('Y-m-d H:i:s'))->get()->getRowArray();
        return max(0, $stock - (int) ($row['reserved'] ?? 0));
    }

    private function locale(): string
    {
        $value = $this->request->getGet('locale');
        $locale = is_scalar($value) ? strtolower(trim((string) $value)) : 'id';
        return $locale === 'en' ? 'en' : 'id';
    }

    private function stringQueryValue(string $key): string
    {
        $value = $this->request->getGet($key);
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function positiveInt(mixed $value, int $default): int
    {
        if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) return $default;
        $number = (int) $value;
        return $number > 0 ? $number : $default;
    }

    private function respond(array $payload, int $status = 200, int $maxAge = 0): ResponseInterface
    {
        $payload['meta']['api_version'] ??= 'v1';
        $cache = $maxAge > 0 ? 'public, max-age=' . $maxAge : 'no-store';
        return $this->response->setStatusCode($status)
            ->setHeader('Cache-Control', $cache)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setJSON($payload);
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->response->setStatusCode($status)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setJSON(['error' => ['code' => $code, 'message' => $message], 'meta' => ['api_version' => 'v1']]);
    }
}
