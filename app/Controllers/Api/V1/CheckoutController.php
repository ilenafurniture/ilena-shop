<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Services\Auth\AuthAttemptLimiter;
use App\Services\Auth\CustomerApiTokenService;
use App\Services\CatalogPricing;
use App\Services\CommerceJobQueue;
use App\Services\FreeShippingService;
use App\Services\OrderIdentifiers;
use App\Services\Payments\StockReservationService;
use App\Services\Payments\VoucherReservationService;
use App\Services\Payments\VoucherStackCalculator;
use App\Services\ShippingService;
use CodeIgniter\HTTP\ResponseInterface;

/** Native mobile checkout API. Prices, stock, vouchers, shipping, and payment payloads are server-derived. */
final class CheckoutController extends BaseController
{
    private const PAYMENT_METHODS = ['bca', 'bri', 'bni', 'mandiri', 'permata', 'cimb', 'qris', 'gopay', 'shopeepay', 'card'];
    private const TEST_EMAILS = ['galihsuks123@gmail.com','ilenafurniture@gmail.com','galih8.4.2001@gmail.com','adityaanugrah494@gmail.com','tipaun0605@gmail.com','uuua5021@gmail.com'];

    public function quote(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $body = $this->body();
        if ($body instanceof ResponseInterface) return $body;
        $prepared = $this->prepare($auth['user'], $body);
        if ($prepared instanceof ResponseInterface) return $prepared;

        $methods = [];
        foreach (self::PAYMENT_METHODS as $method) {
            $fee = $this->paymentFee($method, max(0, $prepared['subtotal'] - $prepared['discount'] - $prepared['flash_sale'] + $prepared['shipping']['harga']));
            $methods[] = ['id' => $method, 'fee' => $fee, 'total' => max(0, $prepared['subtotal'] - $prepared['discount'] - $prepared['flash_sale'] + $prepared['shipping']['harga'] + $fee), 'currency' => 'IDR'];
        }
        return $this->json(['data' => [
            'items' => $prepared['item_summary'], 'subtotal' => $prepared['subtotal'],
            'automatic_vouchers' => $prepared['vouchers'], 'discount' => $prepared['discount'],
            'flash_sale_discount' => $prepared['flash_sale'], 'shipping' => $prepared['shipping_summary'],
            'payment_methods' => $methods, 'expires_in' => 60, 'currency' => 'IDR',
        ]]);
    }

    public function create(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $body = $this->body();
        if ($body instanceof ResponseInterface) return $body;
        $email = strtolower(trim((string) $auth['user']['email']));
        $idempotency = trim((string) $this->request->getHeaderLine('Idempotency-Key'));
        if (!preg_match('/\A[A-Za-z0-9._:-]{16,128}\z/D', $idempotency)) return $this->error('idempotency_key_required', 'Send a unique Idempotency-Key header and reuse it only when retrying this checkout.', 422);
        $method = is_string($body['payment_method'] ?? null) ? strtolower(trim($body['payment_method'])) : '';
        if (!in_array($method, self::PAYMENT_METHODS, true)) return $this->error('payment_method_invalid', 'Choose one of the available payment methods.', 422);
        if ($method === 'card' && (!is_string($body['card_token'] ?? null) || trim($body['card_token']) === '' || strlen($body['card_token']) > 4096)) {
            return $this->error('card_token_required', 'Complete card verification in the app and send the provider token.', 422);
        }
        if (!(new AuthAttemptLimiter())->allow('api_checkout', (string) $this->request->getIPAddress(), $email, 12, 8, 900)) return $this->error('rate_limited', 'Too many checkout attempts. Please try again shortly.', 429);
        if (!db_connect()->tableExists('commerce_jobs') || !db_connect()->tableExists('customer_checkout_idempotency')) return $this->error('checkout_unavailable', 'Checkout is not ready yet. Please try again later.', 503);

        $normalizedRequest = [
            'address_id' => filter_var($body['address_id'] ?? null, FILTER_VALIDATE_INT) ?: 0,
            'shipping_option_id' => is_string($body['shipping_option_id'] ?? null) ? trim($body['shipping_option_id']) : '',
            'payment_method' => $method, 'card_token_hash' => $method === 'card' ? hash('sha256', trim($body['card_token'])) : null,
        ];
        $requestHash = hash('sha256', json_encode($normalizedRequest, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
        $keyHash = hash('sha256', $idempotency);
        $db = db_connect();
        $lock = null;
        $reservedVouchers = [];
        $orderId = '';
        try {
            // Check the idempotency record before requiring a non-empty cart: the first
            // successful request clears the account cart, so a timed-out client retry
            // must still receive the original order instead of a misleading cart_empty.
            $lock = new \App\Services\AtomicOperation($db, 'ilena-api-checkout-' . substr(hash('sha256', $email . $keyHash), 0, 42), false);
            $prior = $db->table('customer_checkout_idempotency')->where('user_email', $email)->where('key_hash', $keyHash)->get(1)->getRowArray();
            if ($prior) {
                $lock->releaseLock();
                if (!hash_equals((string) $prior['request_hash'], $requestHash)) return $this->error('idempotency_conflict', 'This key was already used with different checkout details.', 409);
                $existing = $db->table('pemesanan')->where('id_midtrans', (string) $prior['order_id'])->where('email', $email)->get(1)->getRowArray();
                if (!$existing) return $this->error('order_unavailable', 'The previous checkout is being reconciled. Check your order history shortly.', 503);
                $existingMid = json_decode((string) ($existing['data_mid'] ?? '{}'), true) ?: [];
                return $this->json(['data' => [
                    'order_id' => (string) $prior['order_id'], 'status' => (string) $existing['status'],
                    'payment_state' => $this->paymentState($existingMid), 'poll_after_seconds' => 2, 'replayed' => true,
                ]], 202);
            }

            $prepared = $this->prepare($auth['user'], $body);
            if ($prepared instanceof ResponseInterface) {
                $lock->releaseLock();
                return $prepared;
            }
            $selectedRate = $prepared['shipping'];
            $base = max(0, $prepared['subtotal'] - $prepared['discount'] - $prepared['flash_sale'] + (int) $selectedRate['harga']);
            $fee = $this->paymentFee($method, $base);
            $gross = max(0, $base + $fee);
            if ($gross < 1 || $gross > 2000000000) {
                $lock->releaseLock();
                return $this->error('amount_invalid', 'The order total is outside the supported payment range.', 422);
            }

            $orderId = OrderIdentifiers::web();
            // Keep voucher reservations, stock reservations, the order, queue job,
            // idempotency record, and cart clearing in one outer transaction.
            $db->transBegin();
            foreach ($prepared['vouchers'] as $voucher) {
                $result = (new VoucherReservationService($db))->reserve((int) $voucher['id'], $orderId, $email);
                if (empty($result['ok'])) throw new \DomainException((string) ($result['message'] ?? 'A promotion could not be reserved.'));
                $reservedVouchers[] = (int) $voucher['id'];
            }
            $reservation = (new StockReservationService($db))->reserve($orderId, $prepared['cart']);
            if (empty($reservation['ok'])) throw new \DomainException((string) ($reservation['message'] ?? 'Stock changed. Refresh the cart and retry.'));

            $items = $prepared['item_details'];
            foreach ($prepared['vouchers'] as $voucher) $items[] = ['id' => (string) $voucher['kode'], 'price' => -(int) $voucher['discount'], 'quantity' => 1, 'name' => 'Voucher'];
            if ($prepared['flash_sale'] > 0) $items[] = ['id' => 'Flash Sale', 'price' => -(int) $prepared['flash_sale'], 'quantity' => 1, 'name' => 'Flash Sale'];
            $items[] = ['id' => 'Biaya Ongkir', 'price' => (int) $selectedRate['harga'], 'quantity' => 1, 'name' => 'Biaya Ongkir'];
            $items[] = ['id' => 'Biaya Admin', 'price' => $fee, 'quantity' => 1, 'name' => 'Biaya Admin'];
            foreach ($items as &$item) { $item['id'] = mb_substr((string) $item['id'], 0, 64); $item['name'] = mb_substr((string) $item['name'], 0, 50); }
            unset($item);
            $lineAmount = array_sum(array_map(static fn (array $item): int => (int) $item['price'] * (int) $item['quantity'], $items));
            if ($lineAmount !== $gross) throw new \RuntimeException('Checkout line items do not match the server-calculated amount.');

            $charge = $this->chargePayload($method, $orderId, $gross, $prepared['address'], $items, (string) ($body['card_token'] ?? ''));
            $environment = in_array($email, self::TEST_EMAILS, true) ? 'sandbox' : 'production';
            $accessToken = bin2hex(random_bytes(32));
            $mid = [
                'order_id' => $orderId, 'gross_amount' => $gross, 'payment_type' => $charge['payment_type'],
                'transaction_time' => gmdate('Y-m-d H:i:s'), '_app_payment_environment' => $environment,
                '_app_order_access_token' => $accessToken, '_app_payment_effects_applied' => false,
                '_app_checkout_queue_status' => 'queued',
            ];
            $db->table('pemesanan')->insert([
                'nama' => (string) $prepared['address']['nama_penerima'], 'email' => $email,
                'nohp' => (string) $prepared['address']['nohp_penerima'], 'alamat' => json_encode($prepared['address'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'resi' => 'Menunggu pengiriman', 'items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'kurir' => json_encode($selectedRate, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'id_midtrans' => $orderId,
                'status' => 'Menunggu Pembayaran', 'data_mid' => json_encode($mid, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'id_marketplace' => '', 'status_print' => 'siap', 'keterangan_suratjalan' => '',
            ]);
            if (!$db->transStatus()) throw new \RuntimeException('Could not persist checkout order.');
            $queueKey = 'checkout-charge:' . $orderId;
            $providerIdempotency = in_array($method, ['permata', 'cimb'], true) ? null : bin2hex(random_bytes(16));
            if ($method === 'card') {
                try { $charge['credit_card']['token_id'] = base64_encode(service('encrypter')->encrypt((string) $body['card_token'])); }
                catch (\Throwable $e) { throw new \RuntimeException('Secure card-token storage is unavailable.', 0, $e); }
                unset($charge['_raw_card_token']);
                $charge['_encrypted_card_token'] = true;
            }
            (new CommerceJobQueue($db))->enqueue($queueKey, 'midtrans.charge', $orderId, [
                'environment' => $environment, 'idempotency_key' => $providerIdempotency, 'charge' => $charge,
            ]);
            $db->table('customer_checkout_idempotency')->insert([
                'user_email' => $email, 'key_hash' => $keyHash, 'request_hash' => $requestHash,
                'order_id' => $orderId, 'created_at' => gmdate('Y-m-d H:i:s'),
            ]);
            $db->table('pembeli')->where('LOWER(TRIM(email))', $email)->update(['keranjang' => '[]']);
            if (!$db->transStatus()) throw new \RuntimeException('Could not persist checkout queue and idempotency record.');
            $db->transCommit();
            $lock->releaseLock();
            return $this->json(['data' => ['order_id' => $orderId, 'status' => 'Menunggu Pembayaran', 'payment_state' => 'processing', 'poll_after_seconds' => 2, 'replayed' => false]], 202);
        } catch (\DomainException $error) {
            if ($db->transStatus() !== false) $db->transRollback();
            if ($orderId !== '') {
                (new StockReservationService($db))->release($orderId);
                foreach ($reservedVouchers as $voucherId) (new VoucherReservationService($db))->release($orderId, $voucherId);
            }
            if ($lock instanceof \App\Services\AtomicOperation) $lock->releaseLock();
            return $this->error('checkout_conflict', $error->getMessage(), 409);
        } catch (\Throwable $error) {
            if ($db->transStatus() !== false) $db->transRollback();
            if ($orderId !== '') {
                (new StockReservationService($db))->release($orderId);
                foreach ($reservedVouchers as $voucherId) (new VoucherReservationService($db))->release($orderId, $voucherId);
            }
            if ($lock instanceof \App\Services\AtomicOperation) $lock->releaseLock();
            log_message('error', 'Native customer checkout failed ({type}).', ['type' => get_class($error)]);
            return $this->error('checkout_unavailable', 'Checkout could not be started. Check your order status before retrying.', 503);
        }
    }

    private function prepare(array $user, array $body): array|ResponseInterface
    {
        $addressId = filter_var($body['address_id'] ?? null, FILTER_VALIDATE_INT);
        $shippingOption = is_string($body['shipping_option_id'] ?? null) ? trim($body['shipping_option_id']) : '';
        if ($addressId === false || $addressId < 0 || $addressId > 49 || $shippingOption === '') return $this->error('checkout_input_invalid', 'Choose a saved address and delivery option.', 422);
        $email = strtolower(trim((string) $user['email']));
        $db = db_connect();
        $profile = $db->table('pembeli')->select('alamat,keranjang')->where('LOWER(TRIM(email))', $email)->get(1)->getRowArray();
        if (!$profile) return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
        $addresses = json_decode((string) ($profile['alamat'] ?? ''), true);
        if (!is_array($addresses) || !array_is_list($addresses) || !isset($addresses[$addressId]) || !is_array($addresses[$addressId])) return $this->error('address_not_found', 'Choose a saved address again.', 404);
        $address = $addresses[$addressId];
        $address['email_pemesan'] = strtolower(trim((string) ($address['email_pemesan'] ?? $email)));
        if (empty($address['prov_id']) || empty($address['kab_id']) || empty($address['alamat_lengkap']) || empty($address['nama_penerima']) || empty($address['nohp_penerima'])) return $this->error('address_incomplete', 'Update the selected address before checkout.', 422);
        $cartRaw = json_decode((string) ($profile['keranjang'] ?? ''), true);
        if (!is_array($cartRaw) || !array_is_list($cartRaw) || $cartRaw === [] || count($cartRaw) > 50) return $this->error('cart_empty', 'Add products to your cart before checkout.', 422);
        $ids = [];
        foreach ($cartRaw as $line) {
            if (!is_array($line) || !is_scalar($line['id_barang'] ?? null) || !is_string($line['varian'] ?? null) || filter_var($line['jumlah'] ?? null, FILTER_VALIDATE_INT) === false || (int) $line['jumlah'] < 1 || (int) $line['jumlah'] > 100) return $this->error('cart_invalid', 'Review product quantities in your cart.', 422);
            $ids[] = trim((string) $line['id_barang']);
        }
        $products = (new BarangModel())->getActiveProductsByIds(array_values(array_unique($ids)));
        $reservations = [];
        try {
            if ($db->tableExists('checkout_stock_reservations')) {
                foreach ($db->table('checkout_stock_reservations')->select('product_id,variant_name,SUM(quantity) AS qty')->whereIn('product_id', array_values(array_unique($ids)))->where('status', 'reserved')->where('expires_at >', gmdate('Y-m-d H:i:s'))->groupBy('product_id,variant_name')->get()->getResultArray() as $row) {
                    $reservations[(string) $row['product_id'] . "\0" . mb_strtolower((string) $row['variant_name'])] = (int) $row['qty'];
                }
            }
        } catch (\Throwable) { return $this->error('checkout_unavailable', 'Stock is temporarily unavailable. Try again shortly.', 503); }
        $cart = []; $items = []; $summary = []; $subtotal = 0; $flashSaleBase = 0; $quantityByVariant = [];
        foreach ($cartRaw as $line) {
            $id = trim((string) $line['id_barang']); $variantName = trim($line['varian']); $qty = (int) $line['jumlah'];
            if ($id === '' || strlen($id) > 64 || preg_match('/\A[A-Za-z0-9_-]+\z/', $id) !== 1 || $variantName === '' || mb_strlen($variantName) > 100) return $this->error('cart_invalid', 'A cart item is invalid.', 422);
            $product = $products[$id] ?? null;
            if (!$product) return $this->error('product_unavailable', 'A cart product is no longer available.', 409);
            $variant = null;
            foreach (json_decode((string) ($product['varian'] ?? '[]'), true) ?: [] as $candidate) if (is_array($candidate) && strcasecmp(trim((string) ($candidate['nama'] ?? '')), $variantName) === 0) { $variant = $candidate; break; }
            if (!$variant) return $this->error('variant_unavailable', 'A selected product variant is no longer available.', 409);
            $key = $id . "\0" . mb_strtolower($variantName); $quantityByVariant[$key] = ($quantityByVariant[$key] ?? 0) + $qty;
            if ((int) ($variant['stok'] ?? 0) - (int) ($reservations[$key] ?? 0) < $quantityByVariant[$key]) return $this->error('insufficient_stock', 'A cart quantity exceeds available stock.', 409);
            $price = CatalogPricing::unitPrice($product); $lineTotal = $price * $qty; $subtotal += $lineTotal;
            if (str_contains(mb_strtolower((string) ($product['nama'] ?? '')), 'bundling')) $flashSaleBase += $lineTotal;
            $cart[] = ['id_barang' => $id, 'varian' => $variantName, 'jumlah' => $qty, 'detail' => $product];
            $description = json_decode((string) ($product['deskripsi'] ?? '[]'), true) ?: [];
            $actual = is_array($description['dimensi']['asli'] ?? null) ? $description['dimensi']['asli'] : [];
            $items[] = [
                'id' => $id, 'price' => $price, 'original_price' => max(0, (int) round((float) ($product['harga'] ?? 0))),
                'collection' => (string) ($product['kategori'] ?? ''), 'product_name' => (string) ($product['nama'] ?? ''),
                'dimensions' => ['panjang' => (string) ($actual['panjang'] ?? ''), 'lebar' => (string) ($actual['lebar'] ?? ''), 'tinggi' => (string) ($actual['tinggi'] ?? '')],
                'weight_kg' => (string) ($actual['berat'] ?? ''), 'quantity' => $qty,
                'name' => mb_substr((string) ($product['nama'] ?? 'Furniture') . ' (' . $variantName . ')', 0, 50), 'variant' => $variantName,
            ];
            $summary[] = ['product_id' => $id, 'name' => (string) ($product['nama'] ?? ''), 'variant' => $variantName, 'quantity' => $qty, 'unit_price' => $price, 'line_total' => $lineTotal];
        }
        $flash = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta')))->format('H:i');
        $flashSale = $flashSaleBase > 0 && $flash >= '03:00' && $flash <= '07:00' ? (int) floor($flashSaleBase * 0.15) : 0;
        $rateService = new ShippingService();
        $rates = $rateService->rates($address, $cart);
        $rates = array_values((new FreeShippingService())->applyToRates($address, $rates, (float) $subtotal));
        if ($rates === []) return $this->error('shipping_unavailable', 'Delivery is not available for this address right now.', 422);
        $selected = null;
        foreach ($rates as $rate) {
            $rateId = hash('sha256', implode("\0", [(string) ($rate['provider'] ?? ''), (string) ($rate['service_code'] ?? ''), (string) ($rate['nama'] ?? ''), (string) ($rate['harga'] ?? ''), (string) ($rate['harga_asli'] ?? '')]));
            if (hash_equals($rateId, $shippingOption)) { $selected = $rate; $selected['rate_id'] = $rateId; break; }
        }
        if (!$selected) return $this->error('shipping_rate_changed', 'Delivery options changed. Refresh the quote and choose again.', 409);
        if (($selected['provider'] ?? '') === 'rajaongkir') {
            $fresh = $rateService->revalidateRate($selected, $address, $cart);
            if ($fresh === null) return $this->error('shipping_rate_changed', 'Delivery price changed. Refresh the quote and choose again.', 409);
            $selected = $fresh + ['rate_id' => $shippingOption];
        }
        $vouchers = $this->automaticVouchers($email, (float) $subtotal);
        if ($vouchers instanceof ResponseInterface) return $vouchers;
        $voucherResult = (new VoucherStackCalculator())->calculate($vouchers, null, false, (float) $subtotal);
        return [
            'address' => $address, 'cart' => $cart, 'item_details' => $items, 'item_summary' => $summary,
            'subtotal' => (int) $subtotal, 'flash_sale' => $flashSale,
            'vouchers' => $voucherResult['applied'], 'discount' => (int) $voucherResult['discount'],
            'shipping' => $selected,
            'shipping_summary' => ['option_id' => $shippingOption, 'service' => ($selected['provider'] ?? '') === 'apm' ? 'Jasa Kirim Toko' : (string) ($selected['nama'] ?? 'Pengiriman'), 'price' => max(0, (int) ($selected['harga'] ?? 0)), 'delivery_days' => (string) ($selected['estimasi'] ?? ''), 'free_shipping' => !empty($selected['gratis_ongkir'])],
        ];
    }

    private function automaticVouchers(string $email, float $subtotal): array|ResponseInterface
    {
        try {
            $db = db_connect();
            $rows = $db->table('voucher')->where('aktif', 1)->where('auto_apply', 1)->get()->getResultArray();
            $emails = (new \App\Services\Auth\CustomerEmailIdentity())->allFor($email);
        } catch (\Throwable $error) {
            log_message('error', 'Native checkout voucher eligibility failed ({type}).', ['type' => get_class($error)]);
            return $this->error('promotion_unavailable', 'Promotions could not be verified. Please retry shortly.', 503);
        }
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d'); $eligible = [];
        foreach ($rows as $row) {
            if ($subtotal < (float) ($row['minimal_belanja'] ?? 0)) continue;
            if (!empty($row['mulai']) && $now < substr((string) $row['mulai'], 0, 10)) continue;
            if (!empty($row['berakhir']) && $now > substr((string) $row['berakhir'], 0, 10)) continue;
            $target = strtolower(trim((string) ($row['target'] ?? 'semua')));
            if ($target === 'spesifik') {
                $allowed = json_decode((string) ($row['list_email'] ?? '[]'), true) ?: [];
                $allowedEmails = array_map(static fn ($v): string => strtolower(trim((string) $v)), $allowed);
                if (array_intersect($emails, $allowedEmails) === []) continue;
            }
            if (in_array($target, ['baru', 'lama'], true)) {
                $orders = $db->table('pemesanan')->whereIn('LOWER(TRIM(email))', $emails)->countAllResults();
                if (($target === 'baru') !== ($orders === 0)) continue;
            }
            if ((int) ($row['max_pakai'] ?? 0) > 0) {
                $used = $db->table('voucher_usage')->where('kode_voucher', (string) $row['kode'])->countAllResults();
                $reserved = $db->table('ilena_voucher_reservations')->where('voucher_id', (int) $row['id'])->where('status', 'reserved')->where('expires_at >', gmdate('Y-m-d H:i:s'))->countAllResults();
                if ($used + $reserved >= (int) $row['max_pakai']) continue;
            }
            if (!empty($row['sekali_pakai_per_user']) && $db->table('voucher_usage')->where('kode_voucher', (string) $row['kode'])->whereIn('LOWER(TRIM(email))', $emails)->countAllResults() > 0) continue;
            $eligible[] = $row;
        }
        return $eligible;
    }

    private function paymentFee(string $method, int $base): int
    {
        $fixed = ['bca', 'bri', 'bni', 'mandiri', 'permata', 'cimb'];
        if (in_array($method, $fixed, true)) return (int) ceil(4000 * 1.11);
        $rate = ['gopay' => 2.0, 'shopeepay' => 2.0, 'qris' => 0.7, 'card' => 2.9][$method] ?? 0;
        return (int) ceil($base * $rate / 100 + ($method === 'card' ? 2000 : 0));
    }

    private function chargePayload(string $method, string $orderId, int $gross, array $address, array $items, string $cardToken): array
    {
        $chargeItems = [];
        foreach ($items as $item) $chargeItems[] = ['id' => $item['id'], 'price' => (int) $item['price'], 'quantity' => (int) $item['quantity'], 'name' => $item['name']];
        $charge = [
            'transaction_details' => ['order_id' => $orderId, 'gross_amount' => $gross],
            'customer_details' => ['email' => (string) $address['email_pemesan'], 'phone' => (string) $address['nohp_penerima'], 'first_name' => (string) $address['nama_penerima']],
            'item_details' => $chargeItems,
        ];
        if (in_array($method, ['bca', 'bri', 'bni', 'cimb'], true)) { $charge['payment_type'] = 'bank_transfer'; $charge['bank_transfer'] = ['bank' => $method]; $charge['custom_expiry'] = ['expiry_duration' => 60, 'unit' => 'minute']; }
        elseif ($method === 'permata') { $charge['payment_type'] = 'permata'; $charge['custom_expiry'] = ['expiry_duration' => 60, 'unit' => 'minute']; }
        elseif ($method === 'mandiri') { $charge['payment_type'] = 'echannel'; $charge['echannel'] = ['bill_info1' => 'Payment:', 'bill_info2' => 'Online purchase']; $charge['custom_expiry'] = ['expiry_duration' => 60, 'unit' => 'minute']; }
        elseif ($method === 'qris') { $charge['payment_type'] = 'qris'; $charge['qris'] = ['acquirer' => 'gopay']; $charge['custom_expiry'] = ['expiry_duration' => 15, 'unit' => 'minute']; }
        elseif ($method === 'gopay') { $charge['payment_type'] = 'gopay'; $charge['gopay'] = ['enable_callback' => true, 'callback_url' => 'https://ilenafurniture.com/']; $charge['custom_expiry'] = ['expiry_duration' => 15, 'unit' => 'minute']; }
        elseif ($method === 'shopeepay') { $charge['payment_type'] = 'shopeepay'; $charge['shopeepay'] = ['callback_url' => 'https://ilenafurniture.com/']; $charge['custom_expiry'] = ['expiry_duration' => 15, 'unit' => 'minute']; }
        else { $charge['payment_type'] = 'credit_card'; $charge['credit_card'] = ['token_id' => $cardToken]; }
        return $charge;
    }

    private function authenticated(): array|ResponseInterface
    {
        return (new CustomerApiTokenService())->authenticate($this->request->getHeaderLine('Authorization')) ?? $this->error('unauthorized', 'Sign in again to continue.', 401);
    }
    private function body(): array|ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        return is_array($body) ? $body : $this->error('invalid_request', 'Send a valid JSON request.', 400);
    }
    private function json(array $data, int $status = 200): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'private, no-store, max-age=0')->setHeader('X-Content-Type-Options', 'nosniff')->setJSON($data + ['meta' => ['api_version' => 'v1']]);
    }
    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->json(['error' => ['code' => $code, 'message' => $message]], $status);
    }

    private function paymentState(array $mid): string
    {
        return match ((string) ($mid['_app_checkout_queue_status'] ?? '')) {
            'queued' => 'processing',
            'payment_ready' => 'awaiting_payment',
            'uncertain' => 'needs_reconciliation',
            'rejected', 'failed' => 'failed',
            default => !empty($mid['transaction_status']) ? 'awaiting_payment' : 'processing',
        };
    }
}
