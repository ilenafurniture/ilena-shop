<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

final class Errors extends Controller
{
    public function show404(string $message = ''): ResponseInterface
    {
        $path = trim((string) service('uri')->getPath(), '/');
        $isEnglish = preg_match('#^en(?:/|$)#i', $path) === 1;

        if (preg_match('#^(?:en/)?api/#i', $path) === 1 || str_contains(strtolower($this->request->getHeaderLine('Accept')), 'application/json')) {
            return $this->response
                ->setStatusCode(404)
                ->setHeader('Cache-Control', 'no-store')
                ->setJSON([
                    'success' => false,
                    'error' => 'not_found',
                    'message' => $isEnglish ? 'The requested resource was not found.' : 'Halaman atau data yang diminta tidak ditemukan.',
                ]);
        }

        return $this->response
            ->setStatusCode(404)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Robots-Tag', 'noindex, follow')
            ->setBody(view('layout/notFound', ['isEnglish' => $isEnglish]));
    }
}
