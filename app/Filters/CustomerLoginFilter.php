<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerLoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $guard = (new \App\Services\Auth\CustomerSessionGuard())->enforce();
        if ($guard !== null) return $guard;

        $ses_data = ["kurir", "alamatTerpilih", "hargaKeseluruhan", "kurirTerpilih"];
        session()->remove($ses_data);
        if(session()->get('role') == '2'){
            session()->destroy();
            return redirect()->to(ilena_store_path('/login'))->with('msg', lang('Store.warehouseAccessDisabled'));
        }
        $suspension = (new \App\Services\AccountSuspensionService())->enforceSession();
        if ($suspension !== null) {
            $until = new \DateTimeImmutable($suspension['until'], new \DateTimeZone('UTC'));
            $when = $until->format('d M Y H:i') . ' UTC';
            return redirect()->to(ilena_store_path('/login'))->with('msg', sprintf(lang('Store.accountSuspendedUntil'), $when));
        }
        if (!session()->get('isLogin') || !in_array((string) session()->get('role'), ['0', '4'], true)) {
            return redirect()->to(ilena_store_path('/login'));
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
