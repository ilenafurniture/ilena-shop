<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Services\Auth\CustomerApiTokenService;
use App\Services\CatalogPricing;
use App\Services\FreeShippingService;
use App\Services\ShippingService;
use CodeIgniter\HTTP\ResponseInterface;

/** Recalculate a non-binding estimate from the customer's saved cart and address. */
final class ShippingController extends BaseController
{
    public function estimate(): ResponseInterface
    {
        $auth = (new CustomerApiTokenService())->authenticate($this->request->getHeaderLine('Authorization'));
        if ($auth === null) return $this->error('unauthorized', 'Sign in again to continue.', 401);

        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body)) return $this->error('invalid_request', 'Send a valid JSON request.', 400);
        $addressId = $body['address_id'] ?? null;
        if (!is_int($addressId) || $addressId < 0 || $addressId > 49) {
            return $this->error('invalid_address', 'Choose a saved delivery address.', 422);
        }

        $email = strtolower(trim((string) $auth['user']['email']));
        $profile = db_connect()->table('pembeli')->select('alamat,keranjang')
            ->where('LOWER(TRIM(email))', $email)->get(1)->getRowArray();
        if (!$profile) return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);

        $addresses = json_decode((string) ($profile['alamat'] ?? ''), true);
        if (!is_array($addresses) || !array_is_list($addresses) || !isset($addresses[$addressId]) || !is_array($addresses[$addressId])) {
            return $this->error('address_not_found', 'Choose a saved delivery address again.', 404);
        }
        $saved = $addresses[$addressId];
        $address = [
            'prov_id' => (string) ($saved['prov_id'] ?? ''), 'prov' => (string) ($saved['prov'] ?? ''),
            'kab_id' => (string) ($saved['kab_id'] ?? ''), 'kab' => (string) ($saved['kab'] ?? ''),
            'kec_id' => (string) ($saved['kec_id'] ?? ''), 'kec' => (string) ($saved['kec'] ?? ''),
            'desa_id' => (string) ($saved['desa_id'] ?? ''), 'desa' => (string) ($saved['desa'] ?? ''),
            'kodepos' => (string) ($saved['kodepos'] ?? ''),
        ];
        if ($address['prov_id'] === '' || $address['kab_id'] === '' || $address['prov'] === '' || $address['kab'] === '') {
            return $this->error('address_incomplete', 'Update the saved address with a complete delivery location.', 422);
        }

        $rawCart = json_decode((string) ($profile['keranjang'] ?? ''), true);
        if (!is_array($rawCart) || !array_is_list($rawCart) || $rawCart === [] || count($rawCart) > 100) {
            return $this->error('cart_empty', 'Add available products to your cart first.', 422);
        }
        $ids = [];
        foreach ($rawCart as $line) {
            if (!is_array($line) || !is_scalar($line['id_barang'] ?? null) || !is_string($line['varian'] ?? null)
                || filter_var($line['jumlah'] ?? null, FILTER_VALIDATE_INT) === false
                || (int) $line['jumlah'] < 1 || (int) $line['jumlah'] > 100) {
                return $this->error('cart_invalid', 'Review your cart before estimating delivery.', 422);
            }
            $productId = trim((string) $line['id_barang']);
            if ($productId === '' || strlen($productId) > 64 || preg_match('/\A[A-Za-z0-9_-]+\z/', $productId) !== 1) {
                return $this->error('cart_invalid', 'Review your cart before estimating delivery.', 422);
            }
            $ids[] = $productId;
        }

        $products = (new BarangModel())->getActiveProductsByIds($ids);
        $reservationTotals = [];
        $db = db_connect();
        try {
            if ($db->tableExists('checkout_stock_reservations')) {
                $reservedRows = $db->table('checkout_stock_reservations')
                    ->select('product_id,variant_name,SUM(quantity) AS reserved_quantity')
                    ->whereIn('product_id', array_values(array_unique($ids)))
                    ->where('status', 'reserved')->where('expires_at >', gmdate('Y-m-d H:i:s'))
                    ->groupBy('product_id,variant_name')->get()->getResultArray();
                foreach ($reservedRows as $reservedRow) {
                    $key = (string) $reservedRow['product_id'] . "\0" . mb_strtolower((string) $reservedRow['variant_name']);
                    $reservationTotals[$key] = ($reservationTotals[$key] ?? 0) + (int) ($reservedRow['reserved_quantity'] ?? 0);
                }
            }
        } catch (\Throwable $error) {
            log_message('error', 'Customer API stock reservation lookup failed ({type}).', ['type' => get_class($error)]);
            return $this->error('estimate_unavailable', 'Delivery estimates are temporarily unavailable. Try again later.', 503);
        }
        $cart = [];
        $subtotal = 0;
        $requestedTotals = [];
        foreach ($rawCart as $line) {
            $key = (string) $line['id_barang'] . "\0" . mb_strtolower(trim($line['varian']));
            $requestedTotals[$key] = ($requestedTotals[$key] ?? 0) + (int) $line['jumlah'];
            if ($requestedTotals[$key] > 100) return $this->error('cart_invalid', 'A cart variant quantity exceeds the allowed limit.', 422);
        }
        foreach ($rawCart as $line) {
            $id = (string) $line['id_barang'];
            $product = $products[$id] ?? null;
            if (!$product) return $this->error('cart_changed', 'A cart item is no longer available. Refresh your cart.', 409);
            $requestedVariant = trim($line['varian']);
            $stock = null;
            foreach (json_decode((string) ($product['varian'] ?? '[]'), true) ?: [] as $variant) {
                if (is_array($variant) && strcasecmp(trim((string) ($variant['nama'] ?? '')), $requestedVariant) === 0) {
                    $key = $id . "\0" . mb_strtolower($requestedVariant);
                    $stock = max(0, (int) ($variant['stok'] ?? 0) - (int) ($reservationTotals[$key] ?? 0));
                    break;
                }
            }
            if ($stock === null) return $this->error('cart_changed', 'A selected variant is no longer available. Refresh your cart.', 409);
            $key = $id . "\0" . mb_strtolower($requestedVariant);
            if ($stock < (int) ($requestedTotals[$key] ?? 0)) return $this->error('insufficient_stock', 'A cart quantity exceeds current available stock.', 409);
            $cart[] = ['id_barang' => $id, 'varian' => $requestedVariant, 'jumlah' => (int) $line['jumlah'], 'detail' => $product];
            $subtotal += CatalogPricing::unitPrice($product) * (int) $line['jumlah'];
        }

        try {
            $service = new ShippingService();
            $rates = $service->rates($address, $cart);
            if ($rates === []) return $this->error('estimate_unavailable', 'Delivery estimates are not available for this address right now.', 422);
            $rates = (new FreeShippingService())->applyToRates($address, $rates, (float) $subtotal);
            $data = [];
            foreach ($rates as $rate) {
                if (!is_array($rate)) continue;
                $serviceName = trim((string) ($rate['nama'] ?? ''));
                if (($rate['provider'] ?? '') === 'apm') $serviceName = 'Jasa Kirim Toko';
                $optionId = hash('sha256', implode("\0", [(string) ($rate['provider'] ?? ''), (string) ($rate['service_code'] ?? ''), (string) ($rate['nama'] ?? ''), (string) ($rate['harga'] ?? ''), (string) ($rate['harga_asli'] ?? '')]));
                $data[] = [
                    'option_id' => $optionId,
                    'service' => $serviceName !== '' ? mb_substr($serviceName, 0, 80) : 'Pengiriman',
                    'price' => max(0, (int) ($rate['harga'] ?? 0)),
                    'currency' => 'IDR',
                    'delivery_days' => mb_substr((string) ($rate['estimasi'] ?? ''), 0, 30),
                    'free_shipping' => !empty($rate['gratis_ongkir']),
                    'free_shipping_label' => !empty($rate['gratis_ongkir']) ? mb_substr((string) ($rate['gratis_ongkir_label'] ?? ''), 0, 100) : null,
                ];
            }
            if ($data === []) return $this->error('estimate_unavailable', 'Delivery estimates are not available for this address right now.', 422);
            return $this->json(['data' => ['is_estimate' => true, 'rates' => $data]]);
        } catch (\Throwable $error) {
            log_message('error', 'Customer API shipping estimate failed ({type}).', ['type' => get_class($error)]);
            return $this->error('estimate_unavailable', 'Delivery estimates are temporarily unavailable. Try again later.', 503);
        }
    }

    private function json(array $payload, int $status = 200): ResponseInterface
    {
        $payload['meta'] = ['api_version' => 'v1'];
        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'private, no-store, max-age=0')
            ->setHeader('X-Content-Type-Options', 'nosniff')->setJSON($payload);
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->json(['error' => ['code' => $code, 'message' => $message]], $status);
    }
}
