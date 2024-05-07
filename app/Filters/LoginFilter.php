<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ses_data = ["kurir", "alamatTerpilih", "hargaKeseluruhan", "kurirTerpilih"];
        session()->remove($ses_data);
        if (!session("isLogin")) {
            return redirect()->to(site_url('login'));
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
