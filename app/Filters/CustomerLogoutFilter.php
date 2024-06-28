<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerLogoutFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ses_data = ["kurir", "alamatTerpilih", "hargaKeseluruhan", "kurirTerpilih"];
        session()->remove($ses_data);
        if(session()->get('role') == '2'){
            return redirect()->to('/gudang/listorder');
        }
        if(session()->get('isLogin')) {
            return redirect()->to('/account');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}