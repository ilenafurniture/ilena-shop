<?php

namespace App\Filters;

use App\Services\AuditLogService;
use App\Services\AdminRbacService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/');
        }

        if (session()->get('role') == '2') {
            session()->destroy();
            return redirect()->to('/login')->with('msg', 'Akses akun Gudang sudah dinonaktifkan. Hubungi administrator.');
        }

        $rbac = new AdminRbacService();
        $email = (string)session()->get('email');
        if ((string) session()->get('active') !== '1'
            || !$rbac->sessionAccountMatchesDatabase($email, (string) session()->get('role'))
        ) {
            session()->destroy();
            return redirect()->to('/login')->with('msg', 'Sesi admin sudah tidak aktif. Silakan masuk kembali.');
        }

        $isSuperAdmin = (string)session()->get('role') === '1';
        $hasRbacAccess = $rbac->hasAnyAdminAccess($email);

        if (!$isSuperAdmin && !$hasRbacAccess) {
            return redirect()->to('/');
        }

        if (!$rbac->canAccessPath($email, $request->getUri()->getPath())) {
            return redirect()->to($rbac->firstAllowedAdminUrl($email));
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        try {
            if ($response->getStatusCode() >= 400) {
                return;
            }

            $audit = new AuditLogService();
            $method = strtoupper($request->getMethod());
            $path = $request->getUri()->getPath();

            if (!$audit->shouldRecord($method, $path)) {
                return;
            }

            $activity = $audit->makeHumanActivity($method, $path);
            $requestData = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && method_exists($request, 'getPost')
                ? (array)$request->getPost()
                : [];

            $audit->record(array_merge($audit->currentActorData(), [
                'activity' => $activity,
                'description' => $audit->makeHumanDescription($activity, $method, $path, $requestData),
                'method' => $method,
                'url' => (string)$request->getUri(),
                'ip_address' => $request->getIPAddress(),
                'user_agent' => method_exists($request, 'getUserAgent') ? (string)$request->getUserAgent() : '',
                'request_data' => $requestData,
            ]));
        } catch (\Throwable $th) {
            log_message('error', 'Audit log filter error: ' . $th->getMessage());
        }
    }
}
