<?php

namespace App\Filters;

use App\Services\AdminRbacService;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginToko implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $rbac = new AdminRbacService();
        if (!session()->get('isLogin') || (string) session()->get('role') !== '5' || !$rbac->hasPermission((string) session()->get('email'), 'orders_online')) {
            return redirect()->to('/');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
