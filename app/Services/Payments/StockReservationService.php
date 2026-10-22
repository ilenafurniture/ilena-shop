<?php

namespace App\Services\Payments;

use Config\Database;
use RuntimeException;
use Throwable;

final class StockReservationService
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /** Reserve quantities atomically against the JSON variant stock in barang. */
    public function reserve(string $orderId, array $items, int $ttlSeconds = 4500): array
    {
        if ($orderId === '' || $items === []) return ['ok' => false, 'message' => 'Keranjang kosong.'];
        if ($this->db->DBDriver !== 'MySQLi') {
            throw new RuntimeException('Stock reservations require the MySQLi/InnoDB production database.');
        }

        $quantities = [];
        foreach ($items as $item) {
            $productId = trim((string)($item['id_barang'] ?? $item['id'] ?? ''));
            $variant = trim((string)($item['varian'] ?? $item['variant'] ?? ''));
            $quantity = (int)($item['jumlah'] ?? $item['quantity'] ?? 0);
            if ($productId === '' || $variant === '' || $quantity <= 0) {
                return ['ok' => false, 'message' => 'Data produk/varian checkout tidak valid.'];
            }
            $key = $productId . "\0" . mb_strtolower($variant, 'UTF-8');
            if (!isset($quantities[$key])) $quantities[$key] = ['product_id' => $productId, 'variant' => $variant, 'quantity' => 0];
            $quantities[$key]['quantity'] += $quantity;
        }
        uasort($quantities, static fn(array $a, array $b): int => [$a['product_id'], mb_strtolower($a['variant'], 'UTF-8')] <=> [$b['product_id'], mb_strtolower($b['variant'], 'UTF-8')]);

        $this->db->transBegin();
        try {
            $expiresAt = gmdate('Y-m-d H:i:s', time() + max(60, $ttlSeconds));
            foreach ($quantities as $item) {
                $product = $this->db->query('SELECT id, varian FROM barang WHERE id = ? FOR UPDATE', [$item['product_id']])->getRowArray();
                if (!$product) {
                    $this->db->transRollback();
                    return ['ok' => false, 'message' => 'Produk tidak tersedia lagi.'];
                }
                // Lock reservation cleanup in the same product-first order used by payment fulfillment.
                $this->db->query("UPDATE checkout_stock_reservations SET status='released' WHERE product_id = ? AND LOWER(variant_name) = LOWER(?) AND status='reserved' AND expires_at <= UTC_TIMESTAMP()", [$item['product_id'], $item['variant']]);
                $variants = json_decode((string)($product['varian'] ?? '[]'), true);
                if (!is_array($variants)) throw new RuntimeException('Invalid product variants JSON.');
                $stock = null;
                foreach ($variants as $variant) {
                    if (mb_strtolower((string)($variant['nama'] ?? ''), 'UTF-8') === mb_strtolower($item['variant'], 'UTF-8')) {
                        $stock = max(0, (int)($variant['stok'] ?? 0));
                        break;
                    }
                }
                if ($stock === null) {
                    $this->db->transRollback();
                    return ['ok' => false, 'message' => 'Varian produk sudah tidak tersedia.'];
                }
                $reservedRow = $this->db->query(
                    "SELECT COALESCE(SUM(quantity), 0) AS reserved FROM checkout_stock_reservations WHERE product_id = ? AND LOWER(variant_name) = LOWER(?) AND status = 'reserved' AND expires_at > UTC_TIMESTAMP() AND order_id <> ?",
                    [$item['product_id'], $item['variant'], $orderId]
                )->getRowArray();
                $reserved = (int)($reservedRow['reserved'] ?? 0);
                if ($stock - $reserved < $item['quantity']) {
                    $this->db->transRollback();
                    return ['ok' => false, 'message' => 'Stok varian ' . $item['variant'] . ' baru saja berubah. Perbarui keranjang sebelum membayar.'];
                }
                $this->db->query(
                    "INSERT INTO checkout_stock_reservations (order_id, product_id, variant_name, quantity, status, expires_at, created_at) VALUES (?, ?, ?, ?, 'reserved', ?, UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE quantity=VALUES(quantity), status='reserved', expires_at=VALUES(expires_at)",
                    [$orderId, $item['product_id'], $item['variant'], $item['quantity'], $expiresAt]
                );
            }
            if (!$this->db->transStatus()) throw new RuntimeException('Could not persist stock reservations.');
            $this->db->transCommit();
            return ['ok' => true, 'message' => ''];
        } catch (Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    public function release(string $orderId): void
    {
        if ($orderId !== '') {
            $this->db->table('checkout_stock_reservations')->where('order_id', $orderId)->where('status', 'reserved')->update(['status' => 'released']);
        }
    }

    public function consume(string $orderId): void
    {
        if ($orderId !== '') {
            $this->db->table('checkout_stock_reservations')->where('order_id', $orderId)->where('status', 'reserved')->update(['status' => 'consumed']);
        }
    }
}
