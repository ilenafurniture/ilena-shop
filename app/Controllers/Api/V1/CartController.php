<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Services\Auth\CustomerApiTokenService;
use App\Services\CatalogPricing;
use CodeIgniter\HTTP\ResponseInterface;

/** Persistent cart API; prices and product details are always reloaded from the catalog. */
final class CartController extends BaseController
{
    public function show(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $profile = $this->profile((string) $auth['user']['email']);
        if ($profile instanceof ResponseInterface) return $profile;
        $cart = $this->decodeCart($profile['keranjang'] ?? null);
        if ($cart === null) return $this->error('cart_data_invalid', 'Saved cart data needs support review.', 500);
        return $this->cartResponse($cart);
    }

    /** Replace the full cart; repeating the same request is safe after a network retry. */
    public function replace(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_array($body['items'] ?? null) || !array_is_list($body['items'])) {
            return $this->error('invalid_cart', 'Send an items array.', 400);
        }
        if (count($body['items']) > 100) return $this->error('cart_limit', 'A cart can contain up to 100 product variants.', 422);

        $currentProfile = $this->profile((string) $auth['user']['email']);
        if ($currentProfile instanceof ResponseInterface) return $currentProfile;
        $currentCart = $this->decodeCart($currentProfile['keranjang'] ?? null);
        if ($currentCart === null) return $this->error('cart_data_invalid', 'Saved cart data needs support review.', 500);
        foreach ($currentCart as $currentLine) {
            if (!empty($currentLine['bundle_token'])) return $this->error('bundle_removal_required', 'Remove or retain furniture sets through their dedicated cart action before replacing the cart.', 409);
        }

        $items = [];
        $keys = [];
        foreach ($body['items'] as $item) {
            if (!is_array($item) || !is_scalar($item['product_id'] ?? null) || !is_string($item['variant'] ?? null)
                || !is_int($item['quantity'] ?? null) || $item['quantity'] < 1 || $item['quantity'] > 100) {
                return $this->error('invalid_cart_item', 'Each item needs a product_id, variant, and quantity from 1 to 100.', 422);
            }
            $id = trim((string) $item['product_id']);
            $variant = trim($item['variant']);
            if ($id === '' || strlen($id) > 64 || preg_match('/\A[A-Za-z0-9_-]+\z/', $id) !== 1 || $variant === '' || mb_strlen($variant) > 100) {
                return $this->error('invalid_cart_item', 'A product or variant identifier is invalid.', 422);
            }
            $key = $id . "\0" . mb_strtolower($variant);
            if (isset($keys[$key])) return $this->error('duplicate_cart_item', 'Each product variant may appear only once.', 422);
            $keys[$key] = true;
            $items[] = ['id_barang' => $id, 'varian' => $variant, 'jumlah' => $item['quantity']];
        }

        $products = (new BarangModel())->getActiveProductsByIds(array_column($items, 'id_barang'));
        foreach ($items as $item) {
            $product = $products[$item['id_barang']] ?? null;
            if (!$product) return $this->error('product_unavailable', 'A product in the cart is no longer available.', 409);
            $variantFound = false;
            foreach (json_decode((string) ($product['varian'] ?? '[]'), true) ?: [] as $variant) {
                if (!is_array($variant) || strcasecmp(trim((string) ($variant['nama'] ?? '')), $item['varian']) !== 0) continue;
                $variantFound = true;
                if ((int) ($variant['stok'] ?? 0) < $item['jumlah']) return $this->error('insufficient_stock', 'A requested quantity is above current stock.', 409);
                break;
            }
            if (!$variantFound) return $this->error('variant_unavailable', 'A selected product variant is no longer available.', 409);
        }

        $email = strtolower(trim((string) $auth['user']['email']));
        $db = db_connect();
        $db->transBegin();
        try {
            $profile = $db->query('SELECT email, keranjang FROM pembeli WHERE LOWER(TRIM(email)) = ? LIMIT 1 FOR UPDATE', [$email])->getRowArray();
        } catch (\Throwable) {
            $db->transRollback();
            return $this->error('service_unavailable', 'The cart is temporarily unavailable.', 503);
        }
        if (!$profile) {
            $db->transRollback();
            return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
        }
        $lockedCart = $this->decodeCart($profile['keranjang'] ?? null);
        if ($lockedCart === null) {
            $db->transRollback();
            return $this->error('cart_data_invalid', 'Saved cart data needs support review.', 500);
        }
        foreach ($lockedCart as $lockedLine) {
            if (!empty($lockedLine['bundle_token'])) {
                $db->transRollback();
                return $this->error('bundle_removal_required', 'Remove or retain furniture sets through their dedicated cart action before replacing the cart.', 409);
            }
        }
        $updated = $db->table('pembeli')->where('LOWER(TRIM(email))', $email)->update([
            'keranjang' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        ]);
        if (!$updated || !$db->transStatus()) {
            $db->transRollback();
            return $this->error('cart_save_failed', 'The cart was not saved. Please try again.', 500);
        }
        $db->transCommit();

        return $this->cartResponse($items);
    }

    private function presentCart(array $cart): array
    {
        $products = (new BarangModel())->getActiveProductsByIds(array_map(static fn ($item) => is_array($item) ? ($item['id_barang'] ?? null) : null, $cart));
        $items = [];
        $subtotal = 0;
        $quantityTotal = 0;
        foreach ($cart as $item) {
            if (!is_array($item)) continue;
            $id = (string) ($item['id_barang'] ?? '');
            $variantName = (string) ($item['varian'] ?? '');
            $quantity = max(1, min(100, (int) ($item['jumlah'] ?? 1)));
            $quantityTotal += $quantity;
            $product = $products[$id] ?? null;
            if ($product && !empty($item['bundle_token'])) $product = (array) ($item['detail'] ?? $product);
            $variantExists = false;
            if ($product) {
                foreach (json_decode((string) ($product['varian'] ?? '[]'), true) ?: [] as $variant) {
                    if (is_array($variant) && strcasecmp(trim((string) ($variant['nama'] ?? '')), $variantName) === 0) { $variantExists = true; break; }
                }
            }
            $available = $product !== null && $variantExists;
            $price = $available ? CatalogPricing::unitPrice($product) : null;
            $lineTotal = $available ? $price * $quantity : null;
            if ($available) $subtotal += $lineTotal;
            $items[] = [
                'product_id' => $id,
                'variant' => $variantName,
                'quantity' => $quantity,
                'available' => $available,
                'name' => $available ? ilena_product_display_name($product, $this->locale()) : null,
                'unit_price' => $price,
                'line_total' => $lineTotal,
                'currency' => 'IDR',
                'image_url' => $available ? site_url('product-cover/' . rawurlencode($id) . '?size=300') : null,
            ] + (!empty($item['bundle_token']) ? [
                'bundle' => ['id' => (int) $item['bundle_id'], 'slug' => (string) $item['bundle_slug'], 'token' => (string) $item['bundle_token'], 'name' => (string) ($item['_bundle_name'] ?? ''), 'savings' => (int) ($item['_bundle_discount'] ?? 0)],
            ] : []);
        }
        return ['items' => $items, 'item_count' => count($items), 'quantity' => $quantityTotal, 'subtotal' => $subtotal, 'currency' => 'IDR'];
    }

    private function cartResponse(array $cart): ResponseInterface
    {
        $bundleCheck = (new \App\Services\BundleCartService())->validate($cart);
        if (!$bundleCheck['ok']) return $this->error('bundle_changed', $bundleCheck['message'], 409);
        return $this->json(['data' => $this->presentCart($bundleCheck['cart'])]);
    }

    public function addBundle(string $slug): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        if (!preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $slug)) return $this->error('bundle_not_found', 'Furniture set not found.', 404);
        $offer = (new \App\Services\ProductBundleService())->liveOffer($slug);
        if (!$offer || (int) ($offer['available_sets'] ?? 0) < 1) return $this->error('bundle_unavailable', 'This furniture set is unavailable.', 409);

        $db = db_connect();
        $email = strtolower(trim((string) $auth['user']['email']));
        $db->transBegin();
        try {
            $profile = $db->query('SELECT email, keranjang FROM pembeli WHERE LOWER(TRIM(email)) = ? LIMIT 1 FOR UPDATE', [$email])->getRowArray();
            if (!$profile) { $db->transRollback(); return $this->error('profile_not_found', 'Customer profile is unavailable.', 404); }
            $cart = $this->decodeCart($profile['keranjang'] ?? null);
            if ($cart === null) { $db->transRollback(); return $this->error('cart_data_invalid', 'Saved cart data needs support review.', 500); }
            $valid = (new \App\Services\BundleCartService())->validate($cart);
            if (!$valid['ok']) { $db->transRollback(); return $this->error('bundle_changed', $valid['message'], 409); }
            $cart = $valid['cart'];
            if (count($cart) + count($offer['components']) > 100) { $db->transRollback(); return $this->error('cart_limit', 'The cart can contain up to 100 product variants.', 422); }
            $token = bin2hex(random_bytes(16));
            foreach ($offer['components'] as $component) $cart[] = [
                'id_barang' => (string) $component['product_id'], 'varian' => (string) $component['variant_name'],
                'jumlah' => (int) $component['quantity'], 'bundle_id' => (int) $offer['id'],
                'bundle_slug' => (string) $offer['slug'], 'bundle_token' => $token,
            ];
            $saved = $db->table('pembeli')->where('LOWER(TRIM(email))', $email)->update(['keranjang' => json_encode($cart, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
            if (!$saved || !$db->transStatus()) throw new \RuntimeException('Cart write failed.');
            $db->transCommit();
            return $this->cartResponse($cart);
        } catch (\Throwable $error) {
            $db->transRollback();
            log_message('error', 'API bundle cart update failed ({type}).', ['type' => get_class($error)]);
            return $this->error('cart_save_failed', 'The furniture set was not added. Please retry.', 500);
        }
    }

    public function removeBundle(string $token): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        if (!preg_match('/\A[a-f0-9]{32}\z/', $token)) return $this->error('bundle_not_found', 'Furniture set not found in the cart.', 404);
        $db = db_connect();
        $email = strtolower(trim((string) $auth['user']['email']));
        $db->transBegin();
        try {
            $profile = $db->query('SELECT email, keranjang FROM pembeli WHERE LOWER(TRIM(email)) = ? LIMIT 1 FOR UPDATE', [$email])->getRowArray();
            if (!$profile) { $db->transRollback(); return $this->error('profile_not_found', 'Customer profile is unavailable.', 404); }
            $cart = $this->decodeCart($profile['keranjang'] ?? null);
            if ($cart === null) { $db->transRollback(); return $this->error('cart_data_invalid', 'Saved cart data needs support review.', 500); }
            $before = count($cart);
            $cart = array_values(array_filter($cart, static fn (array $line): bool => !hash_equals($token, (string) ($line['bundle_token'] ?? ''))));
            if (count($cart) === $before) { $db->transRollback(); return $this->error('bundle_not_found', 'Furniture set not found in the cart.', 404); }
            $saved = $db->table('pembeli')->where('LOWER(TRIM(email))', $email)->update(['keranjang' => json_encode($cart, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
            if (!$saved || !$db->transStatus()) throw new \RuntimeException('Cart write failed.');
            $db->transCommit();
            return $this->cartResponse($cart);
        } catch (\Throwable $error) {
            $db->transRollback();
            log_message('error', 'API bundle removal failed ({type}).', ['type' => get_class($error)]);
            return $this->error('cart_save_failed', 'The furniture set was not removed. Please retry.', 500);
        }
    }

    private function profile(string $email): array|ResponseInterface
    {
        $row = db_connect()->table('pembeli')->where('LOWER(TRIM(email))', strtolower(trim($email)))->get(1)->getRowArray();
        return $row ?? $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
    }

    private function decodeCart(mixed $value): ?array
    {
        if (!is_string($value) || trim($value) === '') return [];
        $cart = json_decode($value, true);
        if (!is_array($cart) || !array_is_list($cart) || count($cart) > 100) return null;
        foreach ($cart as $item) {
            if (!is_array($item) || !is_scalar($item['id_barang'] ?? null) || !is_string($item['varian'] ?? null)
                || filter_var($item['jumlah'] ?? null, FILTER_VALIDATE_INT) === false || (int) $item['jumlah'] < 1) return null;
        }
        return $cart;
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
