<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerLoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ses_data = ["kurir", "alamatTerpilih", "hargaKeseluruhan", "kurirTerpilih"];
        session()->remove($ses_data);
        if(session()->get('role') == '2'){
            session()->destroy();
            return redirect()->to('/login')->with('msg', 'Akses akun Gudang sudah dinonaktifkan. Hubungi administrator.');
        }
        $suspension = (new \App\Services\AccountSuspensionService())->enforceSession();
        if ($suspension !== null) {
            $until = new \DateTimeImmutable($suspension['until'], new \DateTimeZone('UTC'));
            $when = $until->format('d M Y H:i') . ' UTC';
            return redirect()->to('/login')->with('msg', 'Akun Anda ditangguhkan ' . $when . '. Silakan hubungi admin.');
        }
        if(!session()->get('isLogin')) {
            return redirect()->to('/login');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
