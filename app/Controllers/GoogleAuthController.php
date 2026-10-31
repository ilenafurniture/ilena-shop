<?php

namespace App\Controllers;

use App\Models\PembeliModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/** Google OIDC sign-in for customer accounts only. */
class GoogleAuthController extends BaseController
{
    private const AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const JWKS_URL = 'https://www.googleapis.com/oauth2/v3/certs';

    public function start(): RedirectResponse
    {
        if (session()->get('isLogin')) {
            if (in_array((string)session()->get('role'), ['0', '4'], true)) return redirect()->to(ilena_store_path('/account'));
            return redirect()->to('/');
        }
        return $this->beginAuthorization(false);
    }

    public function link(): RedirectResponse
    {
        if (!session()->get('isLogin') || !in_array((string)session()->get('role'), ['0', '4'], true)) {
            return redirect()->to(ilena_store_path('/login'));
        }
        return $this->beginAuthorization(true);
    }

    private function beginAuthorization(bool $linkAccount): RedirectResponse
    {
        $config = $this->oauthConfig();
        if ($config === null) return $this->unavailable();

        $state = bin2hex(random_bytes(32));
        $nonce = bin2hex(random_bytes(32));
        $verifier = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $locale = service('request')->getLocale() === 'en' ? 'en' : 'id';
        $returnPath = $linkAccount
            ? ilena_store_path('/account')
            : (string) session()->get('url.intended');
        if (!preg_match('#^/(?:en/)?(?:cart|account)(?:/|\?|$)#', $returnPath)) $returnPath = $locale === 'en' ? '/en/account' : '/account';

        session()->set('google_oauth', [
            'state' => $state,
            'nonce' => $nonce,
            'verifier' => $verifier,
            'created' => time(),
            'return' => $returnPath,
            'mode' => $linkAccount ? 'link' : 'login',
            'account_email' => $linkAccount ? strtolower(trim((string)session()->get('email'))) : '',
        ]);

        return redirect()->to(self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
            'prompt' => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986));
    }

    public function callback(): RedirectResponse
    {
        $pending = session()->get('google_oauth');
        session()->remove('google_oauth');
        $state = (string) $this->request->getGet('state');
        $code = (string) $this->request->getGet('code');
        if (!is_array($pending) || empty($pending['state']) || empty($pending['nonce']) || empty($pending['verifier'])
            || (int)($pending['created'] ?? 0) < time() - 600 || $state === '' || !hash_equals((string)$pending['state'], $state)
            || $code === '' || strlen($code) > 2048 || $this->request->getGet('error') !== null) {
            return $this->failed();
        }

        $config = $this->oauthConfig();
        if ($config === null) return $this->unavailable();
        try {
            $client = service('curlrequest', ['timeout' => 8, 'connect_timeout' => 4, 'verify' => true]);
            $tokenResponse = $client->post(self::TOKEN_URL, [
                'form_params' => [
                    'code' => $code,
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret'],
                    'redirect_uri' => $config['redirect_uri'],
                    'grant_type' => 'authorization_code',
                    'code_verifier' => $pending['verifier'],
                ],
            ]);
            $tokens = json_decode((string)$tokenResponse->getBody(), true);
            $claims = is_array($tokens) && isset($tokens['id_token'])
                ? $this->verifyIdToken((string)$tokens['id_token'], $config['client_id'], (string)$pending['nonce'])
                : null;
        } catch (\Throwable $error) {
            log_message('warning', 'Google customer sign-in provider request failed: {type}', ['type' => get_class($error)]);
            $claims = null;
        }

        if (!is_array($claims)) return $this->failed();
        $email = strtolower(trim((string)($claims['email'] ?? '')));
        $subject = (string)($claims['sub'] ?? '');
        $name = trim((string)($claims['name'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191 || $subject === '' || strlen($subject) > 255
            || ($claims['email_verified'] ?? false) !== true) return $this->failed();

        $db = db_connect();
        $userModel = new UserModel();
        $buyerModel = new PembeliModel();
        $db->transBegin();
        try {
            $link = $db->table('user_google_identities')->where(['provider' => 'google', 'subject' => $subject])->get()->getRowArray();
            $isLinkRequest = ($pending['mode'] ?? 'login') === 'link';
            if ($isLinkRequest) {
                $accountEmail = strtolower(trim((string)session()->get('email')));
                $expectedEmail = strtolower(trim((string)($pending['account_email'] ?? '')));
                if (!session()->get('isLogin') || !in_array((string)session()->get('role'), ['0', '4'], true)
                    || $accountEmail === '' || !hash_equals($expectedEmail, $accountEmail)
                    || ($link && strtolower((string)$link['user_email']) !== $accountEmail)) {
                    $db->transRollback();
                    return $this->failed();
                }
                if (!$link) {
                    $db->table('user_google_identities')->insert([
                        'user_email' => $accountEmail, 'provider' => 'google', 'subject' => $subject,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                if (!$db->transStatus()) throw new \RuntimeException('Google identity link failed.');
                $db->transCommit();
                session()->setFlashdata('msg', service('request')->getLocale() === 'en'
                    ? 'Google sign-in is connected to your account.'
                    : 'Login Google berhasil dihubungkan ke akun Anda.');
                return redirect()->to(ilena_store_path('/account'));
            }

            $user = $link ? $userModel->findByEmailIdentity((string)$link['user_email']) : $userModel->findByEmailIdentity($email);
            if ($user && !in_array((string)($user['role'] ?? ''), ['0', '4'], true)) {
                $db->transRollback();
                return $this->failed();
            }
            if ($user && !$link) {
                $db->transRollback();
                session()->setFlashdata('msg', service('request')->getLocale() === 'en'
                    ? 'This email already has an Ilena account. Sign in with your password, then connect Google from your account.'
                    : 'Email ini sudah memiliki akun Ilena. Masuk dengan kata sandi, lalu hubungkan Google dari halaman akun.');
                return redirect()->to(ilena_store_path('/login'));
            }

            if (!$user) {
                $userEmail = $email;
                $user = $userModel->findByEmailIdentity($userEmail);
                if (!$user) {
                    $inserted = $userModel->insert([
                        'email' => $userEmail,
                        'sandi' => password_hash(bin2hex(random_bytes(48)), PASSWORD_DEFAULT),
                        'role' => '0',
                        'otp' => 0,
                        'active' => '1',
                        'waktu_otp' => 0,
                    ]);
                    if ($inserted === false) throw new \RuntimeException('Customer identity insert failed.');
                    $user = $userModel->findByEmailIdentity($userEmail);
                }
            }
            if (!$user || !in_array((string)($user['role'] ?? ''), ['0', '4'], true)) throw new \RuntimeException('Customer account unavailable.');

            $existingBuyer = $buyerModel->getPembeli((string)$user['email']);
            if (!$existingBuyer) {
                $buyerModel->insert([
                    'email' => $user['email'],
                    'nama' => $name !== '' ? mb_substr($name, 0, 100) : 'Member Ilena',
                    'nohp' => '', 'alamat' => '[]', 'wishlist' => '[]', 'keranjang' => '[]',
                ]);
                $existingBuyer = $buyerModel->getPembeli((string)$user['email']);
            }
            if (!$existingBuyer) throw new \RuntimeException('Customer profile unavailable.');

            if (!$link) {
                $db->table('user_google_identities')->insert([
                    'user_email' => (string)$user['email'], 'provider' => 'google', 'subject' => $subject,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if (!$db->transStatus()) throw new \RuntimeException('Google identity persistence failed.');
            $db->transCommit();

            $guestCart = !session()->get('isLogin') ? (array)session()->get('keranjang') : [];
            $guestWishlist = !session()->get('isLogin') ? (array)session()->get('wishlist') : [];
            $guestAddresses = !session()->get('isLogin') ? (array)session()->get('alamat') : [];
            $cart = \App\Services\CheckoutState::mergeCart(
                json_decode((string)($existingBuyer['keranjang'] ?? '[]'), true) ?: [], $guestCart
            );
            $wishlist = array_values(array_unique(array_merge(
                json_decode((string)($existingBuyer['wishlist'] ?? '[]'), true) ?: [], $guestWishlist
            ), SORT_REGULAR));
            $addresses = json_decode((string)($existingBuyer['alamat'] ?? '[]'), true) ?: [];
            foreach ($guestAddresses as $address) {
                if (!is_array($address) || empty($address['alamat_lengkap'])) continue;
                $address['email_pemesan'] = (string)$user['email'];
                if (!in_array($address, $addresses, true)) $addresses[] = $address;
            }
            $addresses = array_slice($addresses, 0, 50);
            $buyerModel->where('email', (string)$user['email'])->set([
                'keranjang' => json_encode($cart), 'wishlist' => json_encode($wishlist), 'alamat' => json_encode($addresses),
            ])->update();

            $suspension = (new \App\Services\AccountSuspensionService())->state((string)$user['email']);
            if ($suspension !== null) {
                session()->setFlashdata('msg', lang('Store.accountUnavailable'));
                return redirect()->to(ilena_store_path('/login'));
            }
            session()->regenerate(true);
            session()->set([
                'active' => '1', 'email' => (string)$user['email'], 'role' => '0', 'nama' => (string)$existingBuyer['nama'],
                'alamat' => $addresses, 'nohp' => (string)($existingBuyer['nohp'] ?? ''), 'wishlist' => $wishlist,
                'keranjang' => $cart, 'isLogin' => true,
            ]);
            session()->remove(['pending_guest_cart', 'pending_guest_wishlist', 'pending_guest_addresses', 'google_oauth']);
            $return = (string)($pending['return'] ?? '/account');
            if (!preg_match('#^/(?:en/)?(?:account|cart)(?:/|\?|$)#', $return)) $return = '/account';
            return redirect()->to($return);
        } catch (\Throwable $error) {
            $db->transRollback();
            log_message('error', 'Google customer sign-in could not complete: {type}', ['type' => get_class($error)]);
            return $this->failed();
        }
    }

    private function verifyIdToken(string $jwt, string $clientId, string $expectedNonce): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        $header = json_decode($this->decodeBase64Url($parts[0]), true);
        $claims = json_decode($this->decodeBase64Url($parts[1]), true);
        $signature = $this->decodeBase64Url($parts[2]);
        if (!is_array($header) || !is_array($claims) || ($header['alg'] ?? '') !== 'RS256' || empty($header['kid']) || $signature === '') return null;
        try {
            $jwks = cache()->get('ilena_google_oidc_jwks');
            if (!is_array($jwks) || (int)($jwks['_cached_at'] ?? 0) < time() - 3600) {
                $response = service('curlrequest', ['timeout' => 6, 'connect_timeout' => 3, 'verify' => true])->get(self::JWKS_URL);
                $jwks = json_decode((string)$response->getBody(), true);
                if (!is_array($jwks) || !is_array($jwks['keys'] ?? null)) return null;
                $jwks['_cached_at'] = time();
                cache()->save('ilena_google_oidc_jwks', $jwks, 3600);
            }
        } catch (\Throwable $error) { return null; }
        $jwk = null;
        foreach ($jwks['keys'] as $key) if (($key['kid'] ?? '') === $header['kid'] && ($key['kty'] ?? '') === 'RSA') { $jwk = $key; break; }
        if ($jwk === null) {
            try {
                $response = service('curlrequest', ['timeout' => 6, 'connect_timeout' => 3, 'verify' => true])->get(self::JWKS_URL);
                $fresh = json_decode((string)$response->getBody(), true);
                if (is_array($fresh) && is_array($fresh['keys'] ?? null)) {
                    $jwks = $fresh;
                    cache()->save('ilena_google_oidc_jwks', $jwks + ['_cached_at' => time()], 3600);
                    foreach ($jwks['keys'] as $key) if (($key['kid'] ?? '') === $header['kid'] && ($key['kty'] ?? '') === 'RSA') { $jwk = $key; break; }
                }
            } catch (\Throwable $error) { return null; }
        }
        if (!$jwk || empty($jwk['n']) || empty($jwk['e'])) return null;
        $pem = $this->rsaJwkToPem($jwk['n'], $jwk['e']);
        if ($pem === null || openssl_verify($parts[0] . '.' . $parts[1], $signature, $pem, OPENSSL_ALGO_SHA256) !== 1) return null;
        $issuer = (string)($claims['iss'] ?? '');
        if (!in_array($issuer, ['https://accounts.google.com', 'accounts.google.com'], true)
            || !in_array($clientId, (array)($claims['aud'] ?? []), true)
            || (isset($claims['azp']) && $claims['azp'] !== $clientId)
            || !is_numeric($claims['exp'] ?? null) || !is_numeric($claims['iat'] ?? null)
            || (int)$claims['exp'] < time() || (int)$claims['iat'] < time() - 600 || (int)$claims['iat'] > time() + 60
            || !isset($claims['nonce']) || !hash_equals($expectedNonce, (string)$claims['nonce'])) return null;
        return $claims;
    }

    private function rsaJwkToPem(string $n, string $e): ?string
    {
        $modulus = $this->decodeBase64Url($n); $exponent = $this->decodeBase64Url($e);
        if ($modulus === '' || $exponent === '') return null;
        $integer = static function (string $bytes): string {
            $bytes = ltrim($bytes, "\0");
            if ($bytes === '') $bytes = "\0";
            if ((ord($bytes[0]) & 0x80) !== 0) $bytes = "\0" . $bytes;
            return "\x02" . self::derLength(strlen($bytes)) . $bytes;
        };
        $rsa = "\x30" . self::derLength(strlen($integer($modulus) . $integer($exponent))) . $integer($modulus) . $integer($exponent);
        $bitString = "\x03" . self::derLength(strlen($rsa) + 1) . "\0" . $rsa;
        $algorithm = hex2bin('300d06092a864886f70d0101010500');
        $spki = "\x30" . self::derLength(strlen($algorithm . $bitString)) . $algorithm . $bitString;
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

    private function oauthConfig(): ?array
    {
        $id = trim((string)env('GOOGLE_OAUTH_CLIENT_ID'));
        $secret = trim((string)env('GOOGLE_OAUTH_CLIENT_SECRET'));
        $redirect = trim((string)env('GOOGLE_OAUTH_REDIRECT_URI'));
        $scheme = strtolower((string)parse_url($redirect, PHP_URL_SCHEME));
        $host = strtolower((string)parse_url($redirect, PHP_URL_HOST));
        $secureRedirect = $scheme === 'https' || ($scheme === 'http' && in_array($host, ['localhost', '127.0.0.1', '[::1]'], true));
        if ($id === '' || $secret === '' || !filter_var($redirect, FILTER_VALIDATE_URL) || !$secureRedirect) return null;
        return ['client_id' => $id, 'client_secret' => $secret, 'redirect_uri' => $redirect];
    }

    private function unavailable(): RedirectResponse
    {
        session()->setFlashdata('msg', lang('Store.googleUnavailable'));
        return redirect()->to(ilena_store_path('/login'));
    }

    private function failed(): RedirectResponse
    {
        session()->setFlashdata('msg', lang('Store.googleSignInFailed'));
        return redirect()->to(ilena_store_path('/login'));
    }
}
