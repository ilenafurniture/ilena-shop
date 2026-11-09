<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\Auth\AuthAttemptLimiter;
use App\Services\Auth\CustomerApiTokenService;
use App\Services\Auth\GoogleIdTokenVerifier;
use App\Services\Auth\GoogleAuthNonceService;
use App\Services\AtomicOperation;
use App\Services\Auth\RegistrationInput;
use App\Services\Auth\RecaptchaVerifier;
use App\Services\Auth\TransactionalEmailTemplate;
use CodeIgniter\HTTP\ResponseInterface;

/** Customer mobile authentication; admin accounts stay on the website. */
final class AuthController extends BaseController
{
    public function register(): ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body)) return $this->error('invalid_request', 'Send a valid JSON request.', 400);
        if (($body['accept_terms'] ?? null) !== true) return $this->error('terms_required', 'Accept the terms to create an account.', 422);

        $input = RegistrationInput::normalize([
            'nama' => $body['name'] ?? null, 'email' => $body['email'] ?? null,
            'nohp' => $body['phone'] ?? '', 'sandi' => $body['password'] ?? null,
            'validasi-syarat' => '1',
        ]);
        $validationErrors = (new RegistrationInput())->errors($input);
        if ($validationErrors !== []) return $this->error('invalid_registration', 'Check the name, email, phone, and password requirements.', 422);
        $captchaError = (new RecaptchaVerifier())->error($body['captcha_token'] ?? null);
        if ($captchaError !== null) return $this->error('captcha_required', $captchaError, 422);

        $email = $input['email'];
        if (!(new AuthAttemptLimiter())->allow('api_register', (string) $this->request->getIPAddress(), $email, 8, 3, 3600)) {
            return $this->error('rate_limited', 'Too many account requests. Please try again later.', 429);
        }

        $locale = strtolower((string) ($body['locale'] ?? 'id')) === 'en' ? 'en' : 'id';
        $lock = null;
        try {
            $lock = new AtomicOperation(db_connect(), 'ilena-api-register-' . substr(hash('sha256', $email), 0, 44), false);
            $model = new UserModel();
            $existing = $model->findByEmailIdentity($email);
            if ($existing !== null) {
                if ((string) ($existing['active'] ?? '1') === '0' && in_array((string) ($existing['role'] ?? ''), ['0', '4'], true)) {
                    $code = (string) random_int(100000, 999999);
                    if (!$this->sendVerificationEmail($email, $code, $locale)) {
                        return $this->error('email_unavailable', 'Verification email could not be sent. Try again later.', 503);
                    }
                    $pendingDb = db_connect();
                    $pendingDb->transBegin();
                    $updated = $pendingDb->table('user')->where('email', (string) $existing['email'])->where('active', '0')->update([
                        'otp' => $code, 'waktu_otp' => time() + 600,
                    ]);
                    if (!$updated || !$pendingDb->transStatus()) {
                        $pendingDb->transRollback();
                        return $this->error('service_unavailable', 'Account requests are temporarily unavailable.', 503);
                    }
                    $pendingDb->transCommit();
                }
                return $this->json(['data' => ['verification_required' => true, 'message' => 'If verification is needed, check your inbox. Existing customers can sign in.']], 202);
            }

            $code = (string) random_int(100000, 999999);
            if (!$this->sendVerificationEmail($email, $code, $locale)) {
                return $this->error('email_unavailable', 'Verification email could not be sent. Try again later.', 503);
            }

            $lock->beginTransaction();
            if ($model->findByEmailIdentity($email) !== null) {
                $lock->rollback();
                return $this->json(['data' => ['verification_required' => true, 'message' => 'If verification is needed, check your inbox. Existing customers can sign in.']], 202);
            }
            $createdAt = gmdate('Y-m-d H:i:s');
            if ($model->insert([
                'email' => $email,
                'sandi' => password_hash($input['sandi'], PASSWORD_DEFAULT),
                'role' => '0', 'otp' => $code, 'active' => '0', 'waktu_otp' => time() + 600,
                'created_at' => $createdAt, 'registration_method' => 'email', 'auth_version' => 1,
            ]) === false) throw new \RuntimeException('Customer account insert failed.');

            $db = db_connect();
            $profile = $db->table('pembeli')->where('LOWER(TRIM(email))', $email)->get(1)->getRowArray();
            if (!$profile) {
                $ok = $db->table('pembeli')->insert([
                    'email' => $email, 'nama' => $input['nama'], 'nohp' => $input['nohp'],
                    'alamat' => '[]', 'wishlist' => '[]', 'keranjang' => '[]',
                ]);
                if (!$ok) throw new \RuntimeException('Customer profile insert failed.');
            } else {
                $ok = $db->table('pembeli')->where('email', (string) $profile['email'])->update([
                    'email' => $email, 'nama' => $input['nama'], 'nohp' => $input['nohp'],
                ]);
                if (!$ok) throw new \RuntimeException('Customer profile update failed.');
            }
            $lock->commit();
            return $this->json(['data' => ['verification_required' => true, 'message' => 'Check your email for the verification code.']], 202);
        } catch (\Throwable $error) {
            if ($lock instanceof AtomicOperation) $lock->rollback();
            log_message('error', 'Customer API registration failed ({type}).', ['type' => get_class($error)]);
            return $this->error('service_unavailable', 'Account registration is temporarily unavailable. Try again later.', 503);
        }
    }

    public function verifyEmail(): ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_string($body['email'] ?? null) || !is_string($body['code'] ?? null)) {
            return $this->error('invalid_request', 'Send an email address and six-digit code.', 400);
        }
        $email = strtolower(trim($body['email']));
        $code = trim($body['code']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 191 || preg_match('/\A[0-9]{6}\z/D', $code) !== 1) {
            return $this->error('verification_invalid', 'The email or verification code is invalid or expired.', 422);
        }
        if (!(new AuthAttemptLimiter())->allow('api_otp_verify', (string) $this->request->getIPAddress(), $email, 30, 8, 300)) {
            return $this->error('rate_limited', 'Too many verification attempts. Please wait and try again.', 429);
        }

        $lock = null;
        try {
            $lock = new AtomicOperation(db_connect(), 'ilena-api-verify-' . substr(hash('sha256', $email), 0, 45), false);
            $model = new UserModel();
            $user = $model->findByEmailIdentity($email);
            if (!$user || !in_array((string) ($user['role'] ?? ''), ['0', '4'], true)
                || !(new CustomerApiTokenService())->isCustomer($user)
                || (string) ($user['active'] ?? '1') !== '0'
                || !hash_equals((string) ($user['otp'] ?? ''), $code)
                || (int) ($user['waktu_otp'] ?? 0) < time()) {
                return $this->error('verification_invalid', 'The email or verification code is invalid or expired.', 422);
            }
            $lock->beginTransaction();
            $db = db_connect();
            $updated = $db->table('user')->where('email', (string) $user['email'])->where('active', '0')
                ->where('otp', $code)->where('waktu_otp >=', time())
                ->update(['active' => '1', 'otp' => '0', 'waktu_otp' => '0']);
            if (!$updated || $db->affectedRows() !== 1) throw new \RuntimeException('Verification code was already used.');

            $profile = $db->table('pembeli')->where('LOWER(TRIM(email))', strtolower((string) $user['email']))->get(1)->getRowArray();
            if (!$profile) {
                $local = strtok((string) $user['email'], '@') ?: 'Customer';
                $name = trim((string) preg_replace('/[._+-]+/', ' ', $local));
                if ($name === '') $name = 'Customer';
                if (!$db->table('pembeli')->insert([
                    'email' => (string) $user['email'], 'nama' => mb_substr($name, 0, 100), 'nohp' => '',
                    'alamat' => '[]', 'wishlist' => '[]', 'keranjang' => '[]',
                ])) throw new \RuntimeException('Customer profile creation failed.');
            }
            $lock->commit();
            $verified = $model->findByEmailIdentity((string) $user['email']);
            $issued = $verified ? (new CustomerApiTokenService())->issue($verified) : null;
            if ($issued === null) return $this->error('service_unavailable', 'Your account is verified. Sign in again in a moment to continue.', 503);
            return $this->json(['data' => $issued + ['verified' => true]], 200);
        } catch (\Throwable $error) {
            if ($lock instanceof AtomicOperation) $lock->rollback();
            log_message('error', 'Customer API email verification failed ({type}).', ['type' => get_class($error)]);
            return $this->error('service_unavailable', 'Verification is temporarily unavailable. Try again later.', 503);
        }
    }

    public function resendVerification(): ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_string($body['email'] ?? null)) return $this->error('invalid_request', 'Send a valid email address.', 400);
        $email = strtolower(trim($body['email']));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 191) {
            return $this->error('invalid_request', 'Send a valid email address.', 400);
        }
        $generic = ['data' => ['message' => 'If the account needs verification, a new code will be sent.']];
        if (!(new AuthAttemptLimiter())->allow('api_otp_resend', (string) $this->request->getIPAddress(), $email, 10, 5, 3600)) {
            return $this->error('rate_limited', 'Too many verification requests. Please try again later.', 429);
        }
        try {
            $lock = new AtomicOperation(db_connect(), 'ilena-api-verify-' . substr(hash('sha256', $email), 0, 45), false);
            $user = (new UserModel())->findByEmailIdentity($email);
            if ($user && in_array((string) ($user['role'] ?? ''), ['0', '4'], true) && (string) ($user['active'] ?? '1') === '0') {
                $locale = strtolower((string) ($body['locale'] ?? 'id')) === 'en' ? 'en' : 'id';
                $code = (string) random_int(100000, 999999);
                if (!$this->sendVerificationEmail((string) $user['email'], $code, $locale)) {
                    return $this->error('email_unavailable', 'Verification email could not be sent. Try again later.', 503);
                }
                $db = db_connect();
                $db->transBegin();
                $updated = $db->table('user')->where('email', (string) $user['email'])->where('active', '0')
                    ->update(['otp' => $code, 'waktu_otp' => time() + 600]);
                if (!$updated || !$db->transStatus()) {
                    $db->transRollback();
                    return $this->error('service_unavailable', 'Verification is temporarily unavailable.', 503);
                }
                $db->transCommit();
            }
            $lock->releaseLock();
            return $this->json($generic, 202);
        } catch (\Throwable $error) {
            log_message('error', 'Customer API verification resend failed ({type}).', ['type' => get_class($error)]);
            return $this->error('service_unavailable', 'Verification is temporarily unavailable. Try again later.', 503);
        }
    }

    public function requestPasswordReset(): ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_string($body['email'] ?? null)) return $this->error('invalid_request', 'Send a valid email address.', 400);
        $email = strtolower(trim($body['email']));
        $locale = strtolower((string) ($body['locale'] ?? 'id')) === 'en' ? 'en' : 'id';
        $generic = ['data' => ['message' => 'If the address belongs to an eligible customer account, reset instructions will be sent.']];
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 191) return $this->json($generic, 202);
        if (!(new AuthAttemptLimiter())->allow('api_password_reset', (string) $this->request->getIPAddress(), $email, 10, 3, 3600)) {
            return $this->json($generic, 202);
        }

        try {
            $user = (new UserModel())->findByEmailIdentity($email);
            if ($user && in_array((string) ($user['role'] ?? ''), ['0', '4'], true)
                && (string) ($user['active'] ?? '0') === '1' && (new CustomerApiTokenService())->isCustomer($user)) {
                $token = bin2hex(random_bytes(32));
                $db = db_connect();
                $db->transBegin();
                $db->table('user_password_resets')->where('email', (string) $user['email'])->where('consumed_at', null)
                    ->update(['consumed_at' => date('Y-m-d H:i:s')]);
                $inserted = $db->table('user_password_resets')->insert([
                    'email' => (string) $user['email'], 'token_hash' => hash('sha256', $token),
                    'expires_at' => date('Y-m-d H:i:s', time() + 1800), 'created_at' => date('Y-m-d H:i:s'), 'consumed_at' => null,
                ]);
                if (!$inserted || !$db->transStatus()) {
                    $db->transRollback();
                    return $this->error('service_unavailable', 'Password reset is temporarily unavailable.', 503);
                }
                $db->transCommit();
                $url = site_url(ilena_store_path('/reset-password/' . $token));
                $title = $locale === 'en' ? 'Reset your password' : 'Reset kata sandi Anda';
                $intro = $locale === 'en' ? 'Use the secure link below to choose a new password for your Ilena account.' : 'Gunakan tautan aman berikut untuk membuat kata sandi baru akun Ilena Anda.';
                $details = $locale === 'en' ? 'This single-use link expires in 30 minutes. Ignore this email if you did not request a reset.' : 'Tautan sekali pakai berlaku selama 30 menit. Abaikan email ini jika Anda tidak meminta reset.';
                $emailService = \Config\Services::email();
                $emailConfig = config('Email');
                $emailService->clear(true);
                $emailService->setFrom($emailConfig->fromEmail ?: ($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com'), $emailConfig->fromName ?: 'Ilena Furniture');
                $emailService->setReplyTo('info@ilenafurniture.com', 'Ilena Furniture');
                $emailService->setTo((string) $user['email']);
                $emailService->setSubject($locale === 'en' ? 'Ilena Furniture password reset' : 'Reset kata sandi Ilena Furniture');
                $emailService->setMessage(TransactionalEmailTemplate::html($title, $intro, '<p>' . esc($details) . '</p>',
                    $locale === 'en' ? 'Reset password' : 'Reset kata sandi', $url, null, $locale));
                $emailService->setAltMessage(TransactionalEmailTemplate::plain($title, $intro, $details,
                    $locale === 'en' ? 'Reset password' : 'Reset kata sandi', $url, null, $locale));
                if (!$emailService->send()) log_message('warning', 'Customer API password reset email delivery failed.');
            }
            return $this->json($generic, 202);
        } catch (\Throwable $error) {
            log_message('error', 'Customer API password reset request failed ({type}).', ['type' => get_class($error)]);
            return $this->error('service_unavailable', 'Password reset is temporarily unavailable.', 503);
        }
    }

    public function completePasswordReset(): ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_string($body['token'] ?? null) || !is_string($body['password'] ?? null)) {
            return $this->error('invalid_request', 'Send the reset token and a new password.', 400);
        }
        $token = strtolower(trim($body['token']));
        $password = $body['password'];
        if (preg_match('/\A[a-f0-9]{64}\z/D', $token) !== 1 || strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
            return $this->error('invalid_reset', 'The reset token or password is invalid.', 422);
        }
        if (!(new AuthAttemptLimiter())->allow('api_password_reset_complete', (string) $this->request->getIPAddress(), $token, 30, 8, 900)) {
            return $this->error('rate_limited', 'Too many reset attempts. Please try again later.', 429);
        }

        $db = db_connect();
        try {
            $lock = new AtomicOperation($db, 'ilena-api-reset-' . substr(hash('sha256', $token), 0, 46), false);
            $db->transBegin();
            $reset = $db->query('SELECT id,email FROM user_password_resets WHERE token_hash = ? AND consumed_at IS NULL AND expires_at > ? LIMIT 1 FOR UPDATE', [
                hash('sha256', $token), date('Y-m-d H:i:s'),
            ])->getRowArray();
            if (!$reset) {
                $db->transRollback();
                $lock->releaseLock();
                return $this->error('invalid_reset', 'The reset token is invalid or expired.', 422);
            }
            $model = new UserModel();
            $user = $model->findByEmailIdentity((string) $reset['email']);
            if (!$user || !in_array((string) ($user['role'] ?? ''), ['0', '4'], true)) {
                $db->transRollback();
                $lock->releaseLock();
                return $this->error('invalid_reset', 'The reset token is invalid or expired.', 422);
            }
            $now = date('Y-m-d H:i:s');
            $db->table('user_password_resets')->where('id', (int) $reset['id'])->where('consumed_at', null)
                ->where('expires_at >', $now)->update(['consumed_at' => $now]);
            if ($db->affectedRows() !== 1 || !$model->where('email', (string) $user['email'])->whereIn('role', ['0', '4'])
                ->set('auth_version', 'auth_version + 1', false)->set('sandi', password_hash($password, PASSWORD_DEFAULT))->update()) {
                $db->transRollback();
                $lock->releaseLock();
                return $this->error('invalid_reset', 'The reset token is invalid or expired.', 422);
            }
            $db->table('user_password_resets')->where('email', (string) $user['email'])->where('consumed_at', null)->update(['consumed_at' => $now]);
            $lock->commit();
            return $this->json(['data' => ['password_updated' => true, 'sign_in_required' => true]]);
        } catch (\Throwable $error) {
            if ($db->transStatus() !== false) $db->transRollback();
            log_message('error', 'Customer API password reset completion failed ({type}).', ['type' => get_class($error)]);
            return $this->error('service_unavailable', 'Password reset is temporarily unavailable. Try again later.', 503);
        }
    }

    public function login(): ResponseInterface
    {
        try {
            $body = $this->request->getJSON(true);
        } catch (\Throwable) {
            return $this->error('invalid_request', 'Send a valid JSON request.', 400);
        }
        if (!is_array($body)) return $this->error('invalid_request', 'Send a valid JSON request.', 400);

        $emailValue = $body['email'] ?? null;
        $password = $body['password'] ?? null;
        if (!is_string($emailValue) || !is_string($password)) {
            return $this->error('invalid_credentials', 'Email or password is incorrect.', 401);
        }
        $email = strtolower(trim($emailValue));
        if ($email === '' || mb_strlen($email) > 191 || filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($password) > 4096) {
            return $this->error('invalid_credentials', 'Email or password is incorrect.', 401);
        }

        if (!(new AuthAttemptLimiter())->allow('api_login', (string) $this->request->getIPAddress(), $email, 20, 8, 900)) {
            return $this->error('rate_limited', 'Too many sign-in attempts. Please wait and try again.', 429);
        }

        $user = (new UserModel())->findByEmailIdentity($email);
        if (!$user || !password_verify($password, (string) ($user['sandi'] ?? ''))) {
            return $this->error('invalid_credentials', 'Email or password is incorrect.', 401);
        }
        if ((string) ($user['active'] ?? '0') !== '1') {
            return $this->error('email_verification_required', 'Verify your email before signing in.', 403);
        }

        $tokens = new CustomerApiTokenService();
        if (!$tokens->isCustomer($user)) {
            return $this->error('customer_account_required', 'This sign-in is available to customer accounts only.', 403);
        }
        $issued = $tokens->issue($user);
        if ($issued === null) {
            return $this->error('service_unavailable', 'Customer app sign-in is temporarily unavailable.', 503);
        }

        return $this->response->setStatusCode(200)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setJSON(['data' => $issued, 'meta' => ['api_version' => 'v1']]);
    }

    public function googleLogin(): ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_string($body['id_token'] ?? null) || !is_string($body['nonce'] ?? null)) {
            return $this->error('invalid_request', 'Send a Google ID token and its one-time nonce.', 400);
        }
        $idToken = trim($body['id_token']);
        $nonce = trim($body['nonce']);
        $clientId = trim((string) env('GOOGLE_OAUTH_CLIENT_ID'));
        if ($clientId === '' || $idToken === '' || strlen($idToken) > 12000 || strlen($nonce) < 16 || strlen($nonce) > 256) {
            return $this->error('google_sign_in_unavailable', 'Google sign-in is temporarily unavailable.', 503);
        }
        if (!(new AuthAttemptLimiter())->allow('api_google', (string) $this->request->getIPAddress(), hash('sha256', $idToken), 30, 10, 900)) {
            return $this->error('rate_limited', 'Too many sign-in attempts. Please wait and try again.', 429);
        }

        // Consume first so even a successful ID token cannot be replayed later.
        if (!(new GoogleAuthNonceService())->consume($nonce)) {
            return $this->error('google_nonce_invalid', 'Start a new Google sign-in attempt and try again.', 401);
        }
        $claims = (new GoogleIdTokenVerifier())->verify($idToken, $clientId, $nonce);
        $email = is_array($claims) ? strtolower(trim((string) ($claims['email'] ?? ''))) : '';
        $subject = is_array($claims) ? (string) ($claims['sub'] ?? '') : '';
        $displayName = is_array($claims) ? trim((string) ($claims['name'] ?? '')) : '';
        if (!is_array($claims) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191
            || $subject === '' || strlen($subject) > 255) {
            return $this->error('google_identity_invalid', 'Google sign-in could not verify this account.', 401);
        }

        $db = db_connect();
        $lock = null;
        try {
            $lock = new AtomicOperation($db, 'ilena-api-google-' . substr(hash('sha256', $subject), 0, 43), false);
            $db->transBegin();
            $linked = $db->table('user_google_identities')->where(['provider' => 'google', 'subject' => $subject])->get(1)->getRowArray();
            $userModel = new UserModel();
            if ($linked) {
                $user = $userModel->findByEmailIdentity((string) $linked['user_email']);
            } else {
                // Auto-link only an exact primary email. A historical alias is not proof of current ownership.
                $user = $db->table('user')->where('LOWER(TRIM(email))', $email)->get(1)->getRowArray();
                if (!$user && $userModel->findByEmailIdentity($email) !== null) {
                    $db->transRollback();
                    $lock->releaseLock();
                    return $this->error('google_identity_conflict', 'Sign in to your existing account and connect Google from account settings.', 409);
                }
                if ($user && !in_array((string) ($user['role'] ?? ''), ['0', '4'], true)) {
                    $db->transRollback();
                    $lock->releaseLock();
                    return $this->error('customer_account_required', 'This sign-in is available to customer accounts only.', 403);
                }
                if (!$user) {
                    $inserted = $userModel->insert([
                        'email' => $email,
                        'sandi' => password_hash(bin2hex(random_bytes(48)), PASSWORD_DEFAULT),
                        'role' => '0', 'otp' => '0', 'active' => '1', 'waktu_otp' => '0',
                        'created_at' => gmdate('Y-m-d H:i:s'), 'registration_method' => 'google', 'auth_version' => 1,
                    ]);
                    if ($inserted === false) throw new \RuntimeException('Customer account creation failed.');
                    $user = $userModel->findByEmailIdentity($email);
                } elseif ((string) ($user['active'] ?? '0') !== '1') {
                    $db->table('user')->where('email', (string) $user['email'])->update(['active' => '1', 'otp' => '0', 'waktu_otp' => '0']);
                    $user['active'] = '1';
                }
            }

            if (!$user || !(new CustomerApiTokenService($db))->isCustomer($user)) {
                $db->transRollback();
                $lock->releaseLock();
                return $this->error('customer_account_required', 'This sign-in is available to eligible customer accounts only.', 403);
            }

            $profile = $db->table('pembeli')->where('LOWER(TRIM(email))', strtolower((string) $user['email']))->get(1)->getRowArray();
            if (!$profile) {
                $safeName = mb_substr($displayName !== '' ? $displayName : 'Member Ilena', 0, 100);
                if (!$db->table('pembeli')->insert([
                    'email' => (string) $user['email'], 'nama' => $safeName, 'nohp' => '',
                    'alamat' => '[]', 'wishlist' => '[]', 'keranjang' => '[]',
                ])) throw new \RuntimeException('Customer profile creation failed.');
            }
            if (!$linked) {
                $db->table('user_google_identities')->insert([
                    'user_email' => (string) $user['email'], 'provider' => 'google', 'subject' => $subject,
                    'created_at' => gmdate('Y-m-d H:i:s'),
                ]);
            }
            if (!$db->transStatus()) throw new \RuntimeException('Google identity transaction failed.');
            $lock->commit();

            $user = $userModel->findByEmailIdentity((string) $user['email']);
            $issued = $user ? (new CustomerApiTokenService($db))->issue($user) : null;
            if ($issued === null) return $this->error('service_unavailable', 'Google sign-in completed, but a session could not be created. Try again.', 503);
            return $this->json(['data' => $issued, 'meta' => ['api_version' => 'v1']]);
        } catch (\Throwable $error) {
            if ($lock instanceof AtomicOperation) $lock->rollback();
            log_message('error', 'Customer API Google sign-in failed ({type}).', ['type' => get_class($error)]);
            return $this->error('service_unavailable', 'Google sign-in is temporarily unavailable. Try again later.', 503);
        }
    }

    public function googleNonce(): ResponseInterface
    {
        if (!(new AuthAttemptLimiter())->allow('api_google_nonce', (string) $this->request->getIPAddress(), '', 30, 1, 900)) {
            return $this->error('rate_limited', 'Too many sign-in attempts. Please wait and try again.', 429);
        }

        try {
            $nonce = (new GoogleAuthNonceService())->issue();
            if ($nonce === null) return $this->error('google_sign_in_unavailable', 'Google sign-in is temporarily unavailable.', 503);
            return $this->json(['data' => ['nonce' => $nonce, 'expires_in' => 300]], 200);
        } catch (\Throwable $error) {
            log_message('error', 'Customer API Google nonce issue failed ({type}).', ['type' => get_class($error)]);
            return $this->error('google_sign_in_unavailable', 'Google sign-in is temporarily unavailable.', 503);
        }
    }

    public function me(): ResponseInterface
    {
        $authenticated = $this->authenticated();
        if ($authenticated instanceof ResponseInterface) return $authenticated;

        $user = $authenticated['user'];
        $profile = db_connect()->table('pembeli')
            ->where('LOWER(TRIM(email))', strtolower((string) $user['email']))
            ->get(1)->getRowArray() ?? [];
        return $this->json([
            'data' => [
                'email' => (string) $user['email'],
                'name' => (string) ($profile['nama'] ?? ''),
                'phone' => (string) ($profile['nohp'] ?? ''),
            ],
            'meta' => ['api_version' => 'v1'],
        ]);
    }

    public function updateMe(): ResponseInterface
    {
        $authenticated = $this->authenticated();
        if ($authenticated instanceof ResponseInterface) return $authenticated;
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        if (!is_array($body) || !is_string($body['name'] ?? null) || !is_string($body['phone'] ?? null)) {
            return $this->error('invalid_profile', 'Send a name and phone number.', 400);
        }
        $name = trim($body['name']);
        $phone = preg_replace('/\D+/', '', trim($body['phone'])) ?? '';
        if ($name === '' || mb_strlen($name) > 100 || preg_match('/[<>\x00-\x1F\x7F]/u', $name)
            || ($phone !== '' && !preg_match('/\A[0-9]{10,15}\z/D', $phone))) {
            return $this->error('invalid_profile', 'Check the name and phone number.', 422);
        }

        $email = strtolower((string) $authenticated['user']['email']);
        $updated = db_connect()->table('pembeli')->where('LOWER(TRIM(email))', $email)->update(['nama' => $name, 'nohp' => $phone]);
        if (!$updated) return $this->error('profile_save_failed', 'Profile was not updated. Please try again.', 500);
        return $this->json(['data' => ['email' => $email, 'name' => $name, 'phone' => $phone]]);
    }

    public function logout(): ResponseInterface
    {
        $authenticated = $this->authenticated();
        if ($authenticated instanceof ResponseInterface) return $authenticated;
        (new CustomerApiTokenService())->revoke($authenticated);
        return $this->json(['data' => ['signed_out' => true], 'meta' => ['api_version' => 'v1']]);
    }

    private function authenticated(): array|ResponseInterface
    {
        $authorization = (string) $this->request->getHeaderLine('Authorization');
        $authenticated = (new CustomerApiTokenService())->authenticate($authorization);
        return $authenticated ?? $this->error('unauthorized', 'Sign in again to continue.', 401);
    }

    private function sendVerificationEmail(string $email, string $code, string $locale): bool
    {
        try {
            $english = $locale === 'en';
            $title = $english ? 'Verify your Ilena account' : 'Verifikasi email akun Ilena';
            $intro = $english ? 'Enter this code to finish creating your Ilena account.' : 'Masukkan kode berikut untuk menyelesaikan pendaftaran akun Ilena.';
            $expiry = $english ? 'This code is valid for 10 minutes. Never share it with anyone.' : 'Kode berlaku selama 10 menit. Jangan bagikan kepada siapa pun.';
            $emailService = \Config\Services::email();
            $emailConfig = config('Email');
            $emailService->clear(true);
            $emailService->setFrom($emailConfig->fromEmail ?: ($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com'), $emailConfig->fromName ?: 'Ilena Furniture');
            $emailService->setReplyTo('info@ilenafurniture.com', 'Ilena Furniture');
            $emailService->setTo($email);
            $emailService->setSubject($title);
            $content = TransactionalEmailTemplate::verificationCode($code, $locale)
                . '<p style="margin:0;text-align:center;font-size:13px;color:#504b47">' . esc($expiry) . '</p>';
            $emailService->setMessage(TransactionalEmailTemplate::html($title, $intro, $content, null, null, null, $locale));
            $emailService->setAltMessage(TransactionalEmailTemplate::plain($title, $intro, ($english ? 'Verification code: ' : 'Kode verifikasi: ') . $code . "\n" . $expiry, null, null, null, $locale));
            if ($emailService->send()) return true;
            log_message('warning', 'Customer API verification email delivery failed.');
            return false;
        } catch (\Throwable $error) {
            log_message('warning', 'Customer API verification email failed ({type}).', ['type' => get_class($error)]);
            return false;
        }
    }

    private function json(array $payload, int $status = 200): ResponseInterface
    {
        $payload['meta'] = ['api_version' => 'v1'];
        return $this->response->setStatusCode($status)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setJSON($payload);
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->response->setStatusCode($status)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setJSON(['error' => ['code' => $code, 'message' => $message], 'meta' => ['api_version' => 'v1']]);
    }
}
