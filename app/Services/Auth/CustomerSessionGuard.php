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
        $user = $email === '' ? null : db_connect()->table('user')->select('auth_version, active')
            ->where('email', (string) session()->get('email'))->get()->getRowArray();

        // Legacy sessions are invalidated once; all newly issued sessions carry a version.
        if (! $user || (string) ($user['active'] ?? '0') !== '1'
            || ! is_numeric($version) || (int) $version !== (int) $user['auth_version']) {
            session()->destroy();
            session()->setFlashdata('msg', lang('Store.sessionExpiredSignIn'));
            return redirect()->to(ilena_store_path('/login'));
        }

        return null;
    }
}
