<?php
namespace App\Services;

final class CheckoutState
{
    public static function fingerprint(array $cart, array $address, array $shipping, string $method, $voucher): string
    {
        // Checkout may now apply one automatic voucher plus one claimed voucher.
        // Normalize the IDs into a stable scalar/list before canonical JSON encoding.
        if (is_array($voucher)) {
            $voucherIds = [];
            foreach ($voucher as $voucherId) {
                if (! is_int($voucherId) && ! is_string($voucherId)) continue;
                $voucherId = trim((string) $voucherId);
                if ($voucherId !== '') $voucherIds[] = $voucherId;
            }
            $voucherIds = array_values(array_unique($voucherIds));
            sort($voucherIds, SORT_STRING);
            $voucher = count($voucherIds) === 1 ? $voucherIds[0] : $voucherIds;
            if ($voucher === []) $voucher = '';
        } else {
            $voucher = is_scalar($voucher) ? (string) $voucher : '';
        }

        $items=[];
        foreach (self::mergeCart([], $cart) as $item) {
            // Inputs at both boundaries must be revalidated/enriched by the server.
            $product=[];
            foreach ($cart as $raw) {
                if ((string)($raw['id_barang'] ?? '') === $item['id_barang']
                    && strcasecmp((string)($raw['varian'] ?? ''),$item['varian'])===0) { $product=(array)($raw['detail'] ?? []); break; }
            }
            $items[]=['id'=>$item['id_barang'],'variant'=>mb_strtolower($item['varian']), 'quantity'=>$item['jumlah'],'unit_price'=>CatalogPricing::unitPrice($product)];
        }
        usort($items,static fn($a,$b)=>[$a['id'],$a['variant']]<=>[$b['id'],$b['variant']]);
        return hash('sha256',json_encode(self::canonical(['items'=>$items,'address'=>$address,'shipping'=>$shipping,'method'=>$method,'voucher'=>$voucher]),JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /** Persist only identifiers and quantities; rendering metadata is never account state. */
    public static function mergeCart(array $saved, array $guest): array
    {
        $result=[];
        foreach (array_merge($saved,$guest) as $item) {
            if (!is_array($item)) continue;
            $id=trim((string)($item['id_barang'] ?? '')); $variant=trim((string)($item['varian'] ?? '')); $qty=(int)($item['jumlah'] ?? 0);
            if ($id==='' || $variant==='' || $qty<=0) continue;
            $key=$id."\0".mb_strtolower($variant);
            $result[$key] ??= ['id_barang'=>$id,'varian'=>$variant,'jumlah'=>0];
            $result[$key]['jumlah']=min(100000,$result[$key]['jumlah']+$qty);
        }
        return array_values($result);
    }

    private static function canonical(array $value): array
    {
        if (!array_is_list($value)) ksort($value);
        foreach ($value as &$v) if (is_array($v)) $v=self::canonical($v);
        return $value;
    }
}
