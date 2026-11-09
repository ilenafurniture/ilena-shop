<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\Auth\CustomerApiTokenService;
use App\Services\Auth\CustomerEmailIdentity;
use App\Services\OrderInvoiceNumber;
use CodeIgniter\HTTP\ResponseInterface;

/** Read-only order history, always constrained to the authenticated customer identity. */
final class OrdersController extends BaseController
{
    public function index(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $page = $this->positiveInt($this->request->getGet('page'), 1);
        $perPage = min(50, $this->positiveInt($this->request->getGet('per_page'), 20));
        $emails = (new CustomerEmailIdentity())->allFor((string) $auth['user']['email']);
        if ($emails === []) return $this->json(['data' => [], 'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => 0, 'total_pages' => 0]]);

        $builder = db_connect()->table('pemesanan')->whereIn('LOWER(TRIM(email))', $emails);
        $total = (clone $builder)->countAllResults();
        $rows = $builder->select('id,id_midtrans,status,resi,kurir,items,alamat,data_mid')
            ->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        return $this->json([
            'data' => array_map(fn (array $row): array => $this->presentOrder($row), $rows),
            'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => $total, 'total_pages' => (int) ceil($total / $perPage)],
        ]);
    }

    public function show(string $orderId): ResponseInterface
    {
        if ($orderId === '' || strlen($orderId) > 64 || preg_match('/\A[A-Za-z0-9_-]+\z/', $orderId) !== 1) {
            return $this->error('order_not_found', 'Order not found.', 404);
        }
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $emails = (new CustomerEmailIdentity())->allFor((string) $auth['user']['email']);
        $row = db_connect()->table('pemesanan')->select('id,id_midtrans,status,resi,kurir,items,alamat,data_mid')
            ->where('id_midtrans', $orderId)->whereIn('LOWER(TRIM(email))', $emails)->get(1)->getRowArray();
        if (!$row) return $this->error('order_not_found', 'Order not found.', 404);
        return $this->json(['data' => $this->presentOrder($row)]);
    }

    private function presentOrder(array $row): array
    {
        $itemsRaw = $this->decodeJsonArray($row['items'] ?? null);
        $items = [];
        $total = 0;
        foreach ($itemsRaw as $item) {
            if (!is_array($item)) continue;
            $price = (int) round((float) ($item['price'] ?? 0));
            $quantity = max(1, min(100000, (int) ($item['quantity'] ?? 1)));
            $lineTotal = $price * $quantity;
            $total += $lineTotal;
            $items[] = [
                'product_id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['product_name'] ?? $item['name'] ?? 'Item'),
                'variant' => (string) ($item['variant'] ?? $item['varian'] ?? ''),
                'quantity' => $quantity,
                'unit_price' => $price,
                'line_total' => $lineTotal,
                'currency' => 'IDR',
            ];
        }

        $courier = $this->decodeJsonArray($row['kurir'] ?? null);
        $payment = $this->decodeJsonArray($row['data_mid'] ?? null);
        $allowedPayment = [];
        foreach (['payment_type', 'transaction_status', 'transaction_time', 'expiry_time'] as $key) {
            if (isset($payment[$key]) && is_scalar($payment[$key])) $allowedPayment[$key] = $payment[$key];
        }
        $queueState = (string) ($payment['_app_checkout_queue_status'] ?? '');
        $allowedPayment['state'] = match ($queueState) {
            'queued', 'payment_ready' => $queueState === 'queued' ? 'processing' : 'awaiting_payment',
            'uncertain' => 'needs_reconciliation',
            'rejected', 'failed' => 'failed',
            default => !empty($payment['transaction_status']) ? 'awaiting_payment' : 'processing',
        };
        if (!empty($payment['gross_amount']) && is_numeric($payment['gross_amount'])) {
            $allowedPayment['amount'] = max(0, (int) round((float) $payment['gross_amount']));
        }
        foreach (['transaction_id', 'permata_va_number', 'bill_key', 'biller_code', 'qr_string'] as $key) {
            if (isset($payment[$key]) && is_scalar($payment[$key])) $allowedPayment[$key] = $payment[$key];
        }
        if (is_array($payment['va_numbers'] ?? null)) {
            $allowedPayment['va_numbers'] = array_values(array_filter(array_map(static function ($row): ?array {
                if (!is_array($row)) return null;
                $safe = [];
                foreach (['bank', 'va_number'] as $field) if (isset($row[$field]) && is_scalar($row[$field])) $safe[$field] = mb_substr((string) $row[$field], 0, 80);
                return $safe === [] ? null : $safe;
            }, $payment['va_numbers'])));
        }
        if (is_array($payment['actions'] ?? null)) {
            $allowedPayment['actions'] = array_values(array_filter(array_map(static function ($action): ?array {
                if (!is_array($action)) return null;
                $safe = [];
                foreach (['name', 'method'] as $field) if (isset($action[$field]) && is_scalar($action[$field])) $safe[$field] = mb_substr((string) $action[$field], 0, 40);
                if (isset($action['url']) && is_string($action['url'])) {
                    $parts = parse_url($action['url']);
                    if (is_array($parts) && strtolower((string) ($parts['scheme'] ?? '')) === 'https'
                        && in_array(strtolower((string) ($parts['host'] ?? '')), ['api.midtrans.com', 'api.sandbox.midtrans.com', 'app.midtrans.com', 'app.sandbox.midtrans.com', 'gopay.co.id'], true)) {
                        $safe['url'] = $action['url'];
                    }
                }
                return $safe === [] ? null : $safe;
            }, $payment['actions'])));
        }
        $addressRaw = $row['alamat'] ?? '';
        $addressData = is_string($addressRaw) ? json_decode($addressRaw, true) : null;
        if (is_array($addressData)) {
            $address = [
                'recipient_name' => (string) ($addressData['nama_penerima'] ?? ''),
                'phone' => (string) ($addressData['nohp_penerima'] ?? ''),
                'full_address' => (string) ($addressData['alamat_lengkap'] ?? ''),
            ];
        } else {
            $address = ['full_address' => is_string($addressRaw) ? $addressRaw : ''];
        }

        return [
            'order_id' => (string) ($row['id_midtrans'] ?? ''),
            'invoice_number' => OrderInvoiceNumber::fromPrimaryKey($row['id'] ?? null),
            'status' => (string) ($row['status'] ?? ''),
            'tracking_number' => (string) ($row['resi'] ?? ''),
            'courier' => $this->safeCourier($courier),
            'items' => $items,
            'total' => $total,
            'currency' => 'IDR',
            'shipping_address' => $address,
            'payment' => $allowedPayment,
        ];
    }

    private function safeCourier(array $courier): array
    {
        $result = [];
        foreach (['nama', 'courier', 'service', 'layanan', 'harga', 'currency', 'etd'] as $key) {
            if (isset($courier[$key]) && is_scalar($courier[$key])) $result[$key] = $courier[$key];
        }
        return $result;
    }

    private function decodeJsonArray(mixed $value): array
    {
        if (!is_string($value) || $value === '') return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function positiveInt(mixed $value, int $default): int
    {
        if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) return $default;
        $number = (int) $value;
        return $number > 0 ? min(10000, $number) : $default;
    }

    private function authenticated(): array|ResponseInterface
    {
        return (new CustomerApiTokenService())->authenticate($this->request->getHeaderLine('Authorization'))
            ?? $this->error('unauthorized', 'Sign in again to continue.', 401);
    }

    private function json(array $payload, int $status = 200): ResponseInterface
    {
        $payload['meta'] = ['api_version' => 'v1'] + ($payload['meta'] ?? []);
        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')->setJSON($payload);
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->json(['error' => ['code' => $code, 'message' => $message]], $status);
    }
}
