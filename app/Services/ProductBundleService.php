<?php

namespace App\Services;

use App\Models\BarangModel;

/** Reads live bundle offers and prices only their real catalog components. */
final class ProductBundleService
{
    public function liveOffers(): array
    {
        $db = db_connect();
        if (!$db->tableExists('product_bundles') || !$db->tableExists('product_bundle_items')) return [];

        $now = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
        $bundles = $db->table('product_bundles')
            ->where('active', 1)
            ->groupStart()->where('starts_at IS NULL', null, false)->orWhere('starts_at <=', $now)->groupEnd()
            ->groupStart()->where('ends_at IS NULL', null, false)->orWhere('ends_at >=', $now)->groupEnd()
            ->orderBy('id', 'DESC')->limit(50)->get()->getResultArray();
        if ($bundles === []) return [];

        $bundleIds = array_map(static fn (array $row): int => (int) $row['id'], $bundles);
        $itemsByBundle = [];
        foreach ($db->table('product_bundle_items')->whereIn('bundle_id', $bundleIds)->orderBy('id')->get()->getResultArray() as $item) {
            $itemsByBundle[(int) $item['bundle_id']][] = $item;
        }
        $productIds = [];
        foreach ($itemsByBundle as $items) foreach ($items as $item) $productIds[] = (string) $item['product_id'];
        $products = (new BarangModel())->getActiveProductsByIds($productIds);
        $reserved = [];
        if ($productIds !== [] && $db->tableExists('checkout_stock_reservations')) {
            foreach ($db->table('checkout_stock_reservations')
                ->select('product_id, variant_name, SUM(quantity) AS quantity')
                ->whereIn('product_id', array_values(array_unique($productIds)))
                ->where('status', 'reserved')->where('expires_at >', gmdate('Y-m-d H:i:s'))
                ->groupBy('product_id, variant_name')->get()->getResultArray() as $reservation) {
                $key = (string) $reservation['product_id'] . "\0" . mb_strtolower((string) $reservation['variant_name']);
                $reserved[$key] = (int) $reservation['quantity'];
            }
        }

        $offers = [];
        foreach ($bundles as $bundle) {
            $bundleId = (int) $bundle['id'];
            $definitions = $itemsByBundle[$bundleId] ?? [];
            if ($definitions === [] || count($definitions) < 2 || count($definitions) > 20) continue;
            $components = [];
            $valid = true;
            foreach ($definitions as $definition) {
                $product = $products[(string) $definition['product_id']] ?? null;
                $variantName = trim((string) $definition['variant_name']);
                $quantity = (int) $definition['quantity'];
                if (!$product || $variantName === '' || $quantity < 1 || $quantity > 100) { $valid = false; break; }
                $variant = $this->findVariant($product, $variantName);
                if (!$variant) { $valid = false; break; }
                $stockKey = (string) $product['id'] . "\0" . mb_strtolower($variantName);
                $availableStock = max(0, (int) ($variant['stok'] ?? 0) - (int) ($reserved[$stockKey] ?? 0));
                $components[] = [
                    'product_id' => (string) $product['id'],
                    'product_name' => (string) $product['nama'],
                    'variant_name' => $variantName,
                    'quantity' => $quantity,
                    'unit_price' => CatalogPricing::unitPrice($product),
                    'stock' => $availableStock,
                    'product' => $product,
                ];
            }
            if (!$valid) continue;
            try {
                $price = BundlePricing::calculate($components, (float) $bundle['discount_percent']);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $offer = $bundle + $price;
            $offer['components'] = array_map(static function (array $priced, array $component): array {
                return $priced + [
                    'product_name' => $component['product_name'],
                    'stock' => $component['stock'],
                    'product' => $component['product'],
                ];
            }, $price['components'], $components);
            $offer['available_sets'] = min(array_map(
                static fn (array $component): int => intdiv($component['stock'], $component['quantity']),
                $components
            ));
            $offers[] = $offer;
        }

        return $offers;
    }

    public function liveOffer(string $slug): ?array
    {
        foreach ($this->liveOffers() as $offer) {
            if (hash_equals((string) ($offer['slug'] ?? ''), $slug)) return $offer;
        }
        return null;
    }

    private function findVariant(array $product, string $wanted): ?array
    {
        $variants = json_decode((string) ($product['varian'] ?? '[]'), true);
        if (!is_array($variants)) return null;
        foreach ($variants as $variant) {
            if (is_array($variant) && strcasecmp(trim((string) ($variant['nama'] ?? '')), $wanted) === 0) return $variant;
        }
        return null;
    }
}
