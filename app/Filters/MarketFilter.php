<?php

namespace App\Filters;

use App\Services\AdminRbacService;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MarketFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/');
        }

        if ((string) session()->get('role') === '1') {
            return redirect()->to('/admin/product');
        }

        $rbac = new AdminRbacService();
        $email = (string) session()->get('email');
        if ((string) session()->get('role') !== '5' || !$rbac->hasPermission($email, 'marketplace')) {
            if (in_array((string) session()->get('role'), ['2', '3'], true)) {
                session()->destroy();
                return redirect()->to('/login')->with('msg', 'Akses ini belum diatur oleh Admin utama.');
            }
            return redirect()->to('/');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
