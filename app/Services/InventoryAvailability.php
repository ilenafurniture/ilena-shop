<?php
namespace App\Services;

final class InventoryAvailability
{
    /** Caller must already hold the product-row lock in its stock transaction. */
    public static function assertRemaining($db, string $productId, string $variant, int $remaining, string $ownOrder = ''): void
    {
        if ($remaining<0) throw new \RuntimeException('Stok tidak mencukupi.');
        if (!$db->tableExists('checkout_stock_reservations')) throw new \RuntimeException('Migrasi reservasi stok belum tersedia.');
        $row=$db->table('checkout_stock_reservations')->selectSum('quantity','reserved')
            ->where('product_id',$productId)->where('LOWER(variant_name)',mb_strtolower($variant))
            ->where('status','reserved')->where('expires_at >',gmdate('Y-m-d H:i:s'))->where('order_id !=',$ownOrder)->get()->getRowArray();
        if ($remaining<(int)($row['reserved']??0)) throw new \RuntimeException('Stok sedang dialokasikan untuk checkout lain.');
    }
}
