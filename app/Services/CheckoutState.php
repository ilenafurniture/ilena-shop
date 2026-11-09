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
                    && strcasecmp((string)($raw['varian'] ?? ''),$item['varian'])===0
                    && (string) ($raw['bundle_token'] ?? '') === (string) ($item['bundle_token'] ?? '')) { $product=(array)($raw['detail'] ?? []); break; }
            }
            $items[]=['id'=>$item['id_barang'],'variant'=>mb_strtolower($item['varian']), 'quantity'=>$item['jumlah'],'unit_price'=>CatalogPricing::unitPrice($product), 'bundle_id'=>$item['bundle_id'] ?? null, 'bundle_slug'=>$item['bundle_slug'] ?? null, 'bundle_token'=>$item['bundle_token'] ?? null];
        }
        usort($items,static fn($a,$b)=>[$a['bundle_token'] ?? '',$a['id'],$a['variant']]<=>[$b['bundle_token'] ?? '',$b['id'],$b['variant']]);
        return hash('sha256',json_encode(self::canonical(['items'=>$items,'address'=>$address,'shipping'=>$shipping,'method'=>$method,'voucher'=>$voucher]),JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /** Persist identifiers, quantities, and the minimal server-revalidated bundle group identity. */
    public static function mergeCart(array $saved, array $guest): array
    {
        $result=[];
        foreach (array_merge($saved,$guest) as $item) {
            if (!is_array($item)) continue;
            $id=trim((string)($item['id_barang'] ?? '')); $variant=trim((string)($item['varian'] ?? '')); $qty=(int)($item['jumlah'] ?? 0);
            if ($id==='' || $variant==='' || $qty<=0) continue;
            $token = is_string($item['bundle_token'] ?? null) && preg_match('/\A[a-f0-9]{32}\z/', $item['bundle_token']) ? $item['bundle_token'] : '';
            $bundleId = filter_var($item['bundle_id'] ?? null, FILTER_VALIDATE_INT);
            $bundleSlug = is_string($item['bundle_slug'] ?? null) && preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $item['bundle_slug']) ? $item['bundle_slug'] : '';
            $isBundle = $token !== '' && $bundleId !== false && (int) $bundleId > 0 && $bundleSlug !== '';
            $key=($isBundle ? 'bundle:'.$token : 'item')."\0".$id."\0".mb_strtolower($variant);
            $result[$key] ??= ['id_barang'=>$id,'varian'=>$variant,'jumlah'=>0];
            if ($isBundle) {
                $result[$key]['bundle_id'] = (int) $bundleId;
                $result[$key]['bundle_slug'] = $bundleSlug;
                $result[$key]['bundle_token'] = $token;
                // Same saved group may arrive from account and guest snapshots;
                // keep one exact component rather than doubling bundle quantity.
                $result[$key]['jumlah'] = max($result[$key]['jumlah'], min(100, $qty));
            } else {
                $result[$key]['jumlah']=min(100000,$result[$key]['jumlah']+$qty);
            }
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
