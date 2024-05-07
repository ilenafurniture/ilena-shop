<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class KeamananRoute implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ses_data = ["kurir", "alamatTerpilih", "hargaKeseluruhan", "kurirTerpilih"];
        session()->remove($ses_data);
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
