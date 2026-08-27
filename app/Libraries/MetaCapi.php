<?php

namespace App\Libraries;

class MetaCapi
{
    private bool $enabled;
    private string $pixelId;
    private string $accessToken;
    private string $graphVersion;
    private string $testEventCode;

    public function __construct()
    {
        $this->enabled = filter_var(env('META_CAPI_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        $this->pixelId = trim((string) env('META_PIXEL_ID', ''));
        $this->accessToken = trim((string) env('META_ACCESS_TOKEN', ''));
        $this->graphVersion = trim((string) env('META_GRAPH_VERSION', 'v20.0')) ?: 'v20.0';
        $this->testEventCode = trim((string) env('META_TEST_EVENT_CODE', ''));
    }

    public function sendPurchase(array $order): bool
    {
        if (!$this->enabled || $this->pixelId === '' || $this->accessToken === '') {
            return false;
        }

        $orderId = (string)($order['id_midtrans'] ?? '');
        if ($orderId === '') {
            return false;
        }

        $items = json_decode((string)($order['items'] ?? '[]'), true);
        $dataMid = json_decode((string)($order['data_mid'] ?? '[]'), true);
        if (!is_array($items)) $items = [];
        if (!is_array($dataMid)) $dataMid = [];

        $value = $this->purchaseValue($items, $dataMid);
        $request = service('request');

        $event = [
            'event_name'       => 'Purchase',
            'event_time'       => time(),
            'event_id'         => $orderId,
            'action_source'    => 'website',
            'event_source_url' => base_url('orderdetail/proses?idorder=' . rawurlencode($orderId)),
            'user_data'        => array_filter([
                'em' => $this->hashList($order['email'] ?? ''),
                'ph' => $this->hashList($order['nohp'] ?? ''),
                'client_ip_address' => $request ? $request->getIPAddress() : null,
                'client_user_agent' => $request ? (string)$request->getUserAgent() : null,
                'fbp' => $_COOKIE['_fbp'] ?? null,
                'fbc' => $_COOKIE['_fbc'] ?? null,
            ]),
            'custom_data'      => [
                'currency' => 'IDR',
                'value' => $value,
                'order_id' => $orderId,
                'content_type' => 'product',
                'content_ids' => $this->contentIds($items),
                'contents' => $this->contents($items),
            ],
        ];

        return $this->sendEvents([$event]);
    }

    private function sendEvents(array $events): bool
    {
        $payload = ['data' => $events];
        if ($this->testEventCode !== '') {
            $payload['test_event_code'] = $this->testEventCode;
        }

        $url = 'https://graph.facebook.com/' . rawurlencode($this->graphVersion)
            . '/' . rawurlencode($this->pixelId)
            . '/events?access_token=' . rawurlencode($this->accessToken);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'Meta CAPI failed. HTTP: {http}. Error: {err}. Response: {response}', [
                'http' => $httpCode,
                'err' => $err,
                'response' => is_string($response) ? substr($response, 0, 1000) : '',
            ]);
            return false;
        }

        log_message('info', 'Meta CAPI Purchase sent. Response: {response}', [
            'response' => is_string($response) ? substr($response, 0, 1000) : '',
        ]);
        return true;
    }

    private function purchaseValue(array $items, array $dataMid): float
    {
        if (isset($dataMid['gross_amount']) && is_numeric($dataMid['gross_amount'])) {
            return (float)$dataMid['gross_amount'];
        }

        $total = 0;
        foreach ($items as $item) {
            $total += ((float)($item['price'] ?? 0)) * ((int)($item['quantity'] ?? 1));
        }
        return (float)max(0, $total);
    }

    private function contentIds(array $items): array
    {
        $ids = [];
        foreach ($items as $item) {
            if (!$this->isProductItem($item)) continue;
            $ids[] = (string)($item['id'] ?? '');
        }
        return array_values(array_filter(array_unique($ids)));
    }

    private function contents(array $items): array
    {
        $contents = [];
        foreach ($items as $item) {
            if (!$this->isProductItem($item)) continue;
            $contents[] = [
                'id' => (string)($item['id'] ?? ''),
                'quantity' => max(1, (int)($item['quantity'] ?? 1)),
                'item_price' => (float)($item['price'] ?? 0),
            ];
        }
        return $contents;
    }

    private function isProductItem(array $item): bool
    {
        $name = strtolower((string)($item['name'] ?? ''));
        $id = strtolower((string)($item['id'] ?? ''));
        $nonProducts = ['voucher', 'flash sale', 'biaya admin', 'biaya ongkir'];
        return !in_array($name, $nonProducts, true) && !in_array($id, $nonProducts, true);
    }

    private function hashList($value): array
    {
        $normalized = $this->normalizeUserValue((string)$value);
        return $normalized === '' ? [] : [hash('sha256', $normalized)];
    }

    private function normalizeUserValue(string $value): string
    {
        $value = trim(strtolower($value));
        if ($value === '') return '';

        if (str_contains($value, '@')) {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === '') return '';
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }
        return $digits;
    }
}
