<?php

namespace App\Services;

/** Revalidates bundle groups from server-owned campaign definitions at checkout. */
final class BundleCartService
{
    /**
     * @return array{ok:bool,cart:list<array>,has_bundles:bool,message:string}
     */
    public function validate(array $rawCart): array
    {
        $groups = [];
        foreach ($rawCart as $index => $line) {
            if (!is_array($line)) return $this->invalid();
            $hasBundleFields = isset($line['bundle_id']) || isset($line['bundle_slug']) || isset($line['bundle_token']);
            if (!$hasBundleFields) continue;
            $id = filter_var($line['bundle_id'] ?? null, FILTER_VALIDATE_INT);
            $slug = is_string($line['bundle_slug'] ?? null) ? trim($line['bundle_slug']) : '';
            $token = is_string($line['bundle_token'] ?? null) ? $line['bundle_token'] : '';
            if ($id === false || $id < 1 || !preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $slug)
                || !preg_match('/\A[a-f0-9]{32}\z/', $token)) return $this->invalid();
            $groups[$token]['id'] = (int) $id;
            $groups[$token]['slug'] = $slug;
            $groups[$token]['indexes'][] = $index;
        }

        if ($groups === []) return ['ok' => true, 'cart' => $rawCart, 'has_bundles' => false, 'message' => ''];
        $offers = [];
        foreach ((new ProductBundleService())->liveOffers() as $offer) $offers[(string) $offer['id']] = $offer;

        foreach ($groups as $token => $group) {
            $offer = $offers[(string) $group['id']] ?? null;
            if (!$offer || (int) ($offer['available_sets'] ?? 0) < 1 || !hash_equals((string) $offer['slug'], $group['slug'])) return $this->invalid();
            $expected = [];
            foreach ($offer['components'] as $component) {
                $key = self::lineKey((string) $component['product_id'], (string) $component['variant_name']);
                $expected[$key] = $component;
            }
            if (count($expected) !== count($group['indexes'])) return $this->invalid();
            $seen = [];
            foreach ($group['indexes'] as $index) {
                $line = $rawCart[$index];
                $id = trim((string) ($line['id_barang'] ?? ''));
                $variant = trim((string) ($line['varian'] ?? ''));
                $key = self::lineKey($id, $variant);
                $component = $expected[$key] ?? null;
                if (!$component || isset($seen[$key]) || (int) ($line['jumlah'] ?? 0) !== (int) $component['quantity']) return $this->invalid();
                $seen[$key] = true;
                $product = (array) $component['product'];
                $before = (int) $component['unit_price'];
                $after = (int) $component['unit_price_after'];
                $product['harga'] = $before;
                $product['diskon'] = $before > 0 ? (($before - $after) / $before) * 100 : 0;
                $product['_validated_bundle_unit_price'] = $after;
                $rawCart[$index]['detail'] = $product;
                $rawCart[$index]['_bundle_name'] = (string) $offer['name'];
                $rawCart[$index]['_bundle_discount'] = (int) $component['discount'];
            }
        }

        return ['ok' => true, 'cart' => array_values($rawCart), 'has_bundles' => true, 'message' => ''];
    }

    private static function lineKey(string $id, string $variant): string
    {
        return $id . "\0" . mb_strtolower(trim($variant));
    }

    private function invalid(): array
    {
        return ['ok' => false, 'cart' => [], 'has_bundles' => true, 'message' => 'Paket di keranjang berubah atau sudah tidak tersedia. Hapus paket tersebut, lalu pilih kembali dari halaman paket.'];
    }
}
