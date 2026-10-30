<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Redirect the legacy www hostname to the single public canonical host. */
class CanonicalHost implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! in_array(strtoupper($request->getMethod()), ['GET', 'HEAD'], true)) {
            return null;
        }

        $requestHost = strtolower(trim((string) $request->getServer('HTTP_HOST')));
        $requestHost = preg_replace('/:\\d+$/', '', $requestHost) ?? $requestHost;
        if ($requestHost !== 'www.ilenafurniture.com') {
            return null;
        }

        $uri = $request->getUri();
        $target = 'https://ilenafurniture.com' . ($uri->getPath() !== '' ? $uri->getPath() : '/');
        $query = $uri->getQuery();
        if ($query !== '') {
            $target .= '?' . $query;
        }

        return redirect()->to($target, 301);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
