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
            && $this->accessKey !== ''
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
        if (!$this->enabled() || !is_file($localPath)) {
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

        return $result['status'] >= 200 && $result['status'] < 300;
    }

    public function objectExists(string $key): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $cache = cache();
        $cacheKey = 'r2_product_exists_' . md5($key);
        $cached = $cache->get($cacheKey);
        if ($cached !== null) {
            return $cached === true || $cached === '1';
        }

        $result = $this->request('HEAD', $key);
        $exists = $result['status'] >= 200 && $result['status'] < 300;
        $cache->save($cacheKey, $exists ? '1' : '0', $exists ? 3600 : 120);
        return $exists;
    }

    public function redirectResponse(string $key)
    {
        $url = $this->publicUrl($key);
        if (!$url) {
            return null;
        }
        return redirect()->to($url, 302)->setHeader('Cache-Control', 'public, max-age=300');
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
            CURLOPT_TIMEOUT => 30,
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
