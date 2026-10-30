<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerShippingFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('role') == '2') {
            session()->destroy();
            return redirect()->to(ilena_store_path('/login'))->with('msg', 'Akses akun Gudang sudah dinonaktifkan. Hubungi administrator.');
        }
        $suspension = (new \App\Services\AccountSuspensionService())->enforceSession();
        if ($suspension !== null) {
            $until = new \DateTimeImmutable($suspension['until'], new \DateTimeZone('UTC'));
            $when = $until->format('d M Y H:i') . ' UTC';
            return redirect()->to(ilena_store_path('/login'))->with('msg', 'Akun Anda ditangguhkan ' . $when . '. Silakan hubungi admin.');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
