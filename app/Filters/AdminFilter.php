<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if(session()->get('role') == '0' || !session()->get('role')){
            return redirect()->to('/');
        }else if(session()->get('role') == '2'){
            return redirect()->to('/gudang/listorder');
        }else if(session()->get('role') == '3'){
            return redirect()->to('/market/product');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}