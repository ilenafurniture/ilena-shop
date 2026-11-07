<?php

namespace App\Services\Auth;

final class CustomerSessionGuard
{
    public function enforce(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('isLogin') || ! in_array((string) session()->get('role'), ['0', '4'], true)) {
            return null;
        }

        $email = strtolower(trim((string) session()->get('email')));
        $version = session()->get('auth_version');
        $user = $email === '' ? null : db_connect()->table('user')->select('auth_version, active, role')
            ->where('email', (string) session()->get('email'))->get()->getRowArray();

        // New registrations use a short-lived authenticated session to reach the
        // OTP screen. That pending session has no auth_version yet, so permit it
        // only on the verification endpoints; never treat it as a full login.
        if ($user && $this->isPendingVerificationRequest()
            && (string) ($user['active'] ?? '1') === '0'
            && (string) session()->get('active') === '0'
            && in_array((string) ($user['role'] ?? ''), ['0', '4'], true)
            && (string) session()->get('role') === (string) $user['role']
        ) {
            return null;
        }

        // Legacy sessions are invalidated once; all newly issued sessions carry a version.
        if (! $user || (string) ($user['active'] ?? '0') !== '1'
            || ! is_numeric($version) || (int) $version !== (int) $user['auth_version']) {
            session()->destroy();
            session()->setFlashdata('msg', lang('Store.sessionExpiredSignIn'));
            return redirect()->to(ilena_store_path('/login'));
        }

        return null;
    }

    private function isPendingVerificationRequest(): bool
    {
        return self::isPendingVerificationPath((string) service('request')->getUri()->getPath());
    }

    public static function isPendingVerificationPath(string $path): bool
    {
        $path = '/' . trim($path, '/');
        $path = preg_replace('#^/(?:en|id)(?=/|$)#i', '', $path) ?: '/';

        return in_array(strtolower($path), ['/verify', '/actionverify', '/kirimotp'], true);
    }
}
