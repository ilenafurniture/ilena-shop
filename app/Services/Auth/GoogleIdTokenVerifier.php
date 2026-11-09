<?php

namespace App\Services\Auth;

/** Validates Google OpenID Connect ID tokens against Google's rotating RSA keys. */
final class GoogleIdTokenVerifier
{
    private const JWKS_URL = 'https://www.googleapis.com/oauth2/v3/certs';

    public function verify(string $jwt, string $clientId, string $expectedNonce): ?array
    {
        if (strlen($jwt) > 12000 || $clientId === '' || $expectedNonce === '') return null;
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        $header = json_decode($this->decodeBase64Url($parts[0]), true);
        $claims = json_decode($this->decodeBase64Url($parts[1]), true);
        $signature = $this->decodeBase64Url($parts[2]);
        if (!is_array($header) || !is_array($claims) || ($header['alg'] ?? '') !== 'RS256'
            || !is_string($header['kid'] ?? null) || $header['kid'] === '' || $signature === '') return null;

        $jwks = $this->keys();
        if (!is_array($jwks)) return null;
        $jwk = $this->findKey($jwks, $header['kid']);
        if ($jwk === null) {
            $jwks = $this->keys(true);
            $jwk = is_array($jwks) ? $this->findKey($jwks, $header['kid']) : null;
        }
        if (!$jwk || empty($jwk['n']) || empty($jwk['e'])) return null;
        $pem = $this->rsaJwkToPem((string) $jwk['n'], (string) $jwk['e']);
        if ($pem === null || openssl_verify($parts[0] . '.' . $parts[1], $signature, $pem, OPENSSL_ALGO_SHA256) !== 1) return null;

        $issuer = (string) ($claims['iss'] ?? '');
        $audiences = is_array($claims['aud'] ?? null) ? $claims['aud'] : [(string) ($claims['aud'] ?? '')];
        if (!in_array($issuer, ['https://accounts.google.com', 'accounts.google.com'], true)
            || !in_array($clientId, $audiences, true)
            || (isset($claims['azp']) && !hash_equals($clientId, (string) $claims['azp']))
            || !is_numeric($claims['exp'] ?? null) || !is_numeric($claims['iat'] ?? null)
            || (int) $claims['exp'] < time() || (int) $claims['iat'] < time() - 600 || (int) $claims['iat'] > time() + 60
            || !is_string($claims['nonce'] ?? null) || !hash_equals($expectedNonce, $claims['nonce'])
            || ($claims['email_verified'] ?? false) !== true) return null;
        return $claims;
    }

    private function keys(bool $force = false): ?array
    {
        try {
            $jwks = $force ? null : cache()->get('ilena_google_oidc_jwks');
            if (!is_array($jwks) || !is_array($jwks['keys'] ?? null) || (int) ($jwks['_cached_at'] ?? 0) < time() - 3600) {
                $response = service('curlrequest', ['timeout' => 6, 'connect_timeout' => 3, 'verify' => true, 'http_errors' => false, 'allow_redirects' => false])->get(self::JWKS_URL);
                if ($response->getStatusCode() !== 200) return null;
                $jwks = json_decode((string) $response->getBody(), true);
                if (!is_array($jwks) || !is_array($jwks['keys'] ?? null)) return null;
                $jwks['_cached_at'] = time();
                cache()->save('ilena_google_oidc_jwks', $jwks, \App\Services\CacheTtl::jitter(3600));
            }
            return $jwks;
        } catch (\Throwable $error) {
            log_message('warning', 'Google ID-token key retrieval failed ({type}).', ['type' => get_class($error)]);
            return null;
        }
    }

    private function findKey(array $jwks, string $kid): ?array
    {
        foreach ($jwks['keys'] ?? [] as $key) {
            if (is_array($key) && ($key['kid'] ?? '') === $kid && ($key['kty'] ?? '') === 'RSA') return $key;
        }
        return null;
    }

    private function rsaJwkToPem(string $n, string $e): ?string
    {
        $modulus = $this->decodeBase64Url($n);
        $exponent = $this->decodeBase64Url($e);
        if ($modulus === '' || $exponent === '') return null;
        $integer = static function (string $bytes): string {
            $bytes = ltrim($bytes, "\0");
            if ($bytes === '') $bytes = "\0";
            if ((ord($bytes[0]) & 0x80) !== 0) $bytes = "\0" . $bytes;
            return "\x02" . self::derLength(strlen($bytes)) . $bytes;
        };
        $rsaBody = $integer($modulus) . $integer($exponent);
        $rsa = "\x30" . self::derLength(strlen($rsaBody)) . $rsaBody;
        $bitString = "\x03" . self::derLength(strlen($rsa) + 1) . "\0" . $rsa;
        $algorithm = hex2bin('300d06092a864886f70d0101010500');
        $spkiBody = $algorithm . $bitString;
        $spki = "\x30" . self::derLength(strlen($spkiBody)) . $spkiBody;
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($spki), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    private static function derLength(int $length): string
    {
        if ($length < 128) return chr($length);
        $bytes = ltrim(pack('N', $length), "\0");
        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    private function decodeBase64Url(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);
        return $decoded === false ? '' : $decoded;
    }
}
