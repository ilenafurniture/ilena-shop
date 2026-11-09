<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\ProductBundleService;
use CodeIgniter\HTTP\ResponseInterface;

/** Public, read-only catalog of active furniture sets for the storefront and native app. */
final class BundlesController extends BaseController
{
    public function index(): ResponseInterface
    {
        $locale = strtolower((string) $this->request->getGet('locale')) === 'en' ? 'en' : 'id';
        $offers = (new ProductBundleService())->liveOffers();
        $data = array_map(static function (array $offer) use ($locale): array {
            return [
                'id' => (int) $offer['id'], 'slug' => (string) $offer['slug'],
                'name' => $locale === 'en' && trim((string) ($offer['name_en'] ?? '')) !== '' ? $offer['name_en'] : $offer['name'],
                'description' => $locale === 'en' && trim((string) ($offer['description_en'] ?? '')) !== '' ? $offer['description_en'] : ($offer['description'] ?? ''),
                'discount_percent' => (float) $offer['discount_percent'],
                'total_before' => (int) $offer['total_before'], 'savings' => (int) $offer['discount'],
                'total' => (int) $offer['total_after'], 'available_sets' => (int) $offer['available_sets'],
                'components' => array_map(static fn (array $item): array => [
                    'product_id' => (string) $item['product_id'], 'name' => (string) ($item['product_name'] ?? ''),
                    'variant' => (string) $item['variant_name'], 'quantity' => (int) $item['quantity'],
                    'unit_price' => (int) $item['unit_price_after'], 'line_total' => (int) $item['line_total_after'],
                ], $offer['components']),
            ];
        }, $offers);
        return $this->response->setStatusCode(200)
            ->setHeader('Cache-Control', 'public, max-age=30')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setJSON(['data' => $data, 'meta' => ['locale' => $locale, 'currency' => 'IDR', 'api_version' => 'v1']]);
    }
}
