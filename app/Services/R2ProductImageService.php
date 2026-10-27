<?php

namespace App\Services;

class R2ProductImageService
{
    private string $accountId;
    private string $accessKey;
    private string $secretKey;
    private string $bucket;
    private string $endpoint;
    private string $publicUrl;
    private string $region = 'auto';
    private string $service = 's3';

    public function __construct()
    {
        $this->accountId = trim((string) env('R2_ACCOUNT_ID', ''));
        $this->accessKey = trim((string) env('R2_ACCESS_KEY_ID', ''));
        $this->secretKey = trim((string) env('R2_SECRET_ACCESS_KEY', ''));
        $this->bucket = trim((string) env('R2_BUCKET', ''));
        $this->endpoint = rtrim(trim((string) env('R2_ENDPOINT', '')), '/');
        $this->publicUrl = rtrim(trim((string) env('R2_PUBLIC_URL', '')), '/');

        if ($this->endpoint === '' && $this->accountId !== '') {
            $this->endpoint = 'https://' . $this->accountId . '.r2.cloudflarestorage.com';
        }
    }

    public function enabled(): bool
    {
        return strtolower((string) env('PRODUCT_IMAGE_STORAGE', 'local')) === 'r2'
            && $this->configured();
    }

    /** Product image writes are local-only; R2 remains available for legacy reads. */
    public function productWritesEnabled(): bool
    {
        return false;
    }

    /** R2 credentials usable for independent static assets such as home photos. */
    public function configured(): bool
    {
        return $this->accessKey !== ''
            && $this->secretKey !== ''
            && $this->bucket !== ''
            && $this->endpoint !== '';
    }

    public function publicUrl(string $key): ?string
    {
        if ($this->publicUrl === '') {
            return null;
        }
        return $this->publicUrl . '/' . ltrim($key, '/');
    }

    public function uploadFile(string $localPath, string $key, string $contentType = 'image/webp'): bool
    {
        if (!$this->configured() || !is_file($localPath)) {
            return false;
        }

        $body = file_get_contents($localPath);
        if ($body === false) {
            return false;
        }

        $result = $this->request('PUT', $key, $body, [
            'content-type' => $contentType,
            'cache-control' => 'public, max-age=31536000, immutable',
        ]);

        $ok = $result['status'] >= 200 && $result['status'] < 300;
        if ($ok) {
            cache()->save('r2_product_exists_' . md5($key), '1', 3600);
        }
        return $ok;
    }

    public function objectExists(string $key): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $cached = $this->cachedObjectExists($key);
        if ($cached !== null) {
            return $cached;
        }

        $result = $this->request('HEAD', $key);
        $exists = $result['status'] >= 200 && $result['status'] < 300;
        cache()->save('r2_product_exists_' . md5($key), $exists ? '1' : '0', $exists ? 3600 : 120);
        return $exists;
    }

    /**
     * Return a previously recorded object-existence result without making a
     * network request. Public image fallbacks use this to keep R2 HEAD latency
     * out of customer-facing PHP requests.
     */
    public function cachedObjectExists(string $key): ?bool
    {
        $cached = cache()->get('r2_product_exists_' . md5($key));
        if ($cached === null) {
            return null;
        }

        return $cached === true || $cached === '1';
    }

    public function deleteObject(string $key): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $key = ltrim($key, '/');
        $result = $this->request('DELETE', $key);
        $ok = $result['status'] >= 200 && $result['status'] < 300;
        if ($ok || $result['status'] === 404) {
            cache()->delete('r2_product_exists_' . md5($key));
            return true;
        }

        log_message('error', 'Hapus gambar produk R2 gagal: {key} status {status}', [
            'key' => $key,
            'status' => $result['status'],
        ]);
        return false;
    }

    public function deleteProductImages(string $productId, int $maxSlots = 20): void
    {
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $productId);
        if ($safeId === '') {
            return;
        }

        $this->deleteObject("products/300/{$safeId}.webp");
        $this->deleteObject("products/hover/{$safeId}.webp");

        for ($slot = 1; $slot <= $maxSlots; $slot++) {
            $this->deleteObject("products/300/{$safeId}-{$slot}.webp");
            $this->deleteObject("products/1000/{$safeId}-{$slot}.webp");
            $this->deleteObject("products/3000/{$safeId}-{$slot}.webp");
        }
    }

    /** Delete only the image slots still referenced by a product record. */
    public function deleteProductImagesForSlots(string $productId, array $slots): void
    {
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $productId);
        if ($safeId === '') {
            return;
        }

        $this->deleteObject("products/300/{$safeId}.webp");
        $this->deleteObject("products/hover/{$safeId}.webp");
        $normalizedSlots = [];
        foreach ($slots as $slot) {
            $slot = filter_var($slot, FILTER_VALIDATE_INT);
            if ($slot !== false && $slot >= 1 && $slot <= 9999) {
                $normalizedSlots[$slot] = $slot;
            }
        }
        foreach ($normalizedSlots as $slot) {
            $this->deleteObject("products/300/{$safeId}-{$slot}.webp");
            $this->deleteObject("products/1000/{$safeId}-{$slot}.webp");
            $this->deleteObject("products/3000/{$safeId}-{$slot}.webp");
        }
    }

    /** Remove unreferenced size variants after a product edit has committed. */
    public function deleteProductImageSlots(string $productId, array $slots): void
    {
        if (!$this->enabled()) return;
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $productId);
        if ($safeId === '') return;
        foreach (array_unique(array_filter(array_map('intval', $slots), static fn(int $slot): bool => $slot >= 1 && $slot <= 9999)) as $slot) {
            $this->deleteObject("products/300/{$safeId}-{$slot}.webp");
            $this->deleteObject("products/1000/{$safeId}-{$slot}.webp");
            $this->deleteObject("products/3000/{$safeId}-{$slot}.webp");
        }
    }

    /** Remove obsolete large zoom derivatives after a replacement commits. */
    public function deleteProductZoomVariants(string $productId, array $slots): void
    {
        if (!$this->enabled()) return;
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $productId);
        if ($safeId === '') return;
        foreach (array_unique(array_filter(array_map('intval', $slots), static fn(int $slot): bool => $slot >= 1 && $slot <= 9999)) as $slot) {
            $this->deleteObject("products/3000/{$safeId}-{$slot}.webp");
        }
    }

    public function redirectResponse(string $key, string $version = '')
    {
        $url = $this->publicUrl($key);
        if (!$url) {
            return null;
        }
        $version = preg_replace('/[^A-Za-z0-9_-]/', '', $version);
        if ($version !== '') {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'v=' . rawurlencode($version);
        }
        $response = redirect()->to($url, 302);
        $response->removeHeader('Cache-Control');
        $response->setHeader('Cache-Control', 'public, max-age=300');
        $response->setBody('');

        return $response;
    }

    public function getObject(string $key): ?array
    {
        if (!$this->enabled()) {
            return null;
        }

        $result = $this->request('GET', $key);
        if ($result['status'] < 200 || $result['status'] >= 300 || $result['body'] === false) {
            return null;
        }

        cache()->save('r2_product_exists_' . md5($key), '1', 3600);
        return [
            'body' => (string) $result['body'],
            'contentType' => 'image/webp',
        ];
    }

    /** Read fixed home-asset objects even when product-image R2 writes are disabled. */
    public function getHomeAssetObject(string $key): ?array
    {
        if (!$this->configured() || !preg_match('#^home/assets/[A-Za-z0-9._-]+\.webp$#', $key)) {
            return null;
        }

        $result = $this->request('GET', $key);
        if ($result['status'] < 200 || $result['status'] >= 300 || !is_string($result['body'])) {
            return null;
        }

        return ['body' => $result['body'], 'contentType' => 'image/webp'];
    }

    /** Read the real current object state without relying on the positive/negative cache. */
    public function snapshotObject(string $key): array
    {
        if (!$this->enabled()) {
            return ['exists' => false, 'body' => null];
        }

        $head = $this->request('HEAD', $key);
        if ($head['status'] === 404) {
            return ['exists' => false, 'body' => null];
        }
        if ($head['status'] < 200 || $head['status'] >= 300) {
            throw new \RuntimeException('Could not verify existing R2 image before edit.');
        }

        $result = $this->request('GET', $key);
        if ($result['status'] < 200 || $result['status'] >= 300 || $result['body'] === false) {
            throw new \RuntimeException('Could not snapshot existing R2 image before edit.');
        }

        return ['exists' => true, 'body' => (string)$result['body']];
    }

    private function request(string $method, string $key, string $body = '', array $extraHeaders = []): array
    {
        $key = ltrim($key, '/');
        $encodedKey = implode('/', array_map('rawurlencode', explode('/', $key)));
        $host = parse_url($this->endpoint, PHP_URL_HOST);
        $url = $this->endpoint . '/' . rawurlencode($this->bucket) . '/' . $encodedKey;
        $payloadHash = hash('sha256', $body);
        $amzDate = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');

        $headers = array_change_key_case(array_merge([
            'host' => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date' => $amzDate,
        ], $extraHeaders), CASE_LOWER);
        ksort($headers);

        $canonicalHeaders = '';
        foreach ($headers as $name => $value) {
            $canonicalHeaders .= $name . ':' . trim((string) $value) . "\n";
        }
        $signedHeaders = implode(';', array_keys($headers));
        $canonicalRequest = strtoupper($method) . "\n"
            . '/' . rawurlencode($this->bucket) . '/' . $encodedKey . "\n"
            . "\n"
            . $canonicalHeaders . "\n"
            . $signedHeaders . "\n"
            . $payloadHash;

        $credentialScope = $date . '/' . $this->region . '/' . $this->service . '/aws4_request';
        $stringToSign = "AWS4-HMAC-SHA256\n"
            . $amzDate . "\n"
            . $credentialScope . "\n"
            . hash('sha256', $canonicalRequest);

        $signingKey = $this->signingKey($date);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);
        $headers['authorization'] = 'AWS4-HMAC-SHA256 Credential=' . $this->accessKey . '/' . $credentialScope
            . ', SignedHeaders=' . $signedHeaders
            . ', Signature=' . $signature;

        $curlHeaders = [];
        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            // HEAD requests only select a remote image candidate. Keep them
            // short so a slow/unreachable R2 endpoint cannot stall page loads
            // before local-image fallback is attempted.
            CURLOPT_CONNECTTIMEOUT => strtoupper($method) === 'HEAD' ? 2 : 8,
            CURLOPT_TIMEOUT => strtoupper($method) === 'HEAD' ? 4 : 30,
        ]);
        if (strtoupper($method) !== 'HEAD') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'R2 request error: {error}', ['error' => $error]);
        }

        return ['status' => $status, 'body' => $response];
    }

    private function signingKey(string $date): string
    {
        $kDate = hash_hmac('sha256', $date, 'AWS4' . $this->secretKey, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', $this->service, $kRegion, true);
        return hash_hmac('sha256', 'aws4_request', $kService, true);
    }
}
