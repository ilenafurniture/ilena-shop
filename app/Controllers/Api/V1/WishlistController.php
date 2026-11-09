<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Services\Auth\CustomerApiTokenService;
use App\Services\CatalogPricing;
use CodeIgniter\HTTP\ResponseInterface;

/** Customer-owned saved products. */
final class WishlistController extends BaseController
{
    public function show(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $email = strtolower((string) $auth['user']['email']);
        $profile = db_connect()->table('pembeli')->where('LOWER(TRIM(email))', $email)->get(1)->getRowArray();
        if (!$profile) return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
        $ids = $this->decodeIds($profile['wishlist'] ?? null);
        if ($ids === null) return $this->error('wishlist_data_invalid', 'Saved products need support review.', 500);
        return $this->json(['data' => $this->products($ids)]);
    }

    public function replace(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_array($body['product_ids'] ?? null) || !array_is_list($body['product_ids'])) {
            return $this->error('invalid_wishlist', 'Send a product_ids array.', 400);
        }
        if (count($body['product_ids']) > 500) return $this->error('wishlist_limit', 'A wishlist can contain up to 500 products.', 422);

        $ids = [];
        foreach ($body['product_ids'] as $rawId) {
            if (!is_scalar($rawId)) return $this->error('invalid_product_id', 'Every product ID must be a string or integer.', 422);
            $id = trim((string) $rawId);
            if ($id === '' || strlen($id) > 64 || preg_match('/\A[A-Za-z0-9_-]+\z/', $id) !== 1) return $this->error('invalid_product_id', 'A product ID is invalid.', 422);
            if (!in_array($id, $ids, true)) $ids[] = $id;
        }
        $products = (new BarangModel())->getActiveProductsByIds($ids);
        if (count($products) !== count($ids)) return $this->error('product_unavailable', 'Every saved product must be active and available.', 409);

        $email = strtolower((string) $auth['user']['email']);
        $db = db_connect();
        $db->transBegin();
        try {
            $profile = $db->query('SELECT email FROM pembeli WHERE LOWER(TRIM(email)) = ? LIMIT 1 FOR UPDATE', [$email])->getRowArray();
        } catch (\Throwable) {
            $db->transRollback();
            return $this->error('service_unavailable', 'Wishlist is temporarily unavailable.', 503);
        }
        if (!$profile) {
            $db->transRollback();
            return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
        }
        $updated = $db->table('pembeli')->where('LOWER(TRIM(email))', $email)
            ->update(['wishlist' => json_encode($ids, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
        if (!$updated || !$db->transStatus()) {
            $db->transRollback();
            return $this->error('wishlist_save_failed', 'Wishlist was not saved. Please try again.', 500);
        }
        $db->transCommit();
        return $this->json(['data' => $this->products($ids)]);
    }

    private function products(array $ids): array
    {
        $products = (new BarangModel())->getActiveProductsByIds($ids);
        $result = [];
        foreach ($ids as $id) {
            $row = $products[(string) $id] ?? null;
            if (!$row) continue;
            $result[] = [
                'id' => (string) $row['id'],
                'name' => ilena_product_display_name($row, $this->locale()),
                'category' => (string) ($row['kategori'] ?? ''),
                'collection' => (string) ($row['subkategori'] ?? ''),
                'price' => ['currency' => 'IDR', 'original' => (int) round((float) ($row['harga'] ?? 0)), 'current' => CatalogPricing::unitPrice($row)],
                'url' => site_url(($this->locale() === 'en' ? 'en/' : '') . 'product/' . strtolower(str_replace(' ', '-', (string) $row['nama']))),
                'image_url' => site_url('product-cover/' . rawurlencode((string) $row['id']) . '?size=300'),
            ];
        }
        return $result;
    }

    private function decodeIds(mixed $value): ?array
    {
        if (!is_string($value) || trim($value) === '') return [];
        $ids = json_decode($value, true);
        if (!is_array($ids) || !array_is_list($ids) || count($ids) > 500) return null;
        foreach ($ids as $id) if (!is_scalar($id) || trim((string) $id) === '') return null;
        return array_values(array_unique(array_map('strval', $ids)));
    }

    private function authenticated(): array|ResponseInterface
    {
        return (new CustomerApiTokenService())->authenticate($this->request->getHeaderLine('Authorization'))
            ?? $this->error('unauthorized', 'Sign in again to continue.', 401);
    }

    private function locale(): string
    {
        return strtolower((string) $this->request->getGet('locale')) === 'en' ? 'en' : 'id';
    }

    private function json(array $payload, int $status = 200): ResponseInterface
    {
        $payload['meta'] = ['api_version' => 'v1'];
        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')->setJSON($payload);
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->json(['error' => ['code' => $code, 'message' => $message]], $status);
    }
}
