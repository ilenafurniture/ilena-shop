<?php

namespace App\Controllers;

use App\Models\PemesananModel;
use App\Services\AccountSuspensionService;

final class GuestOrderAccessController extends BaseController
{
    public function bridge()
    {
        $rawOrderId = $this->request->getGet('idorder');
        $rawLocale = $this->request->getGet('locale');
        $orderId = is_string($rawOrderId) ? trim($rawOrderId) : '';
        $locale = is_string($rawLocale) && strtolower($rawLocale) === 'en' ? 'en' : 'id';

        if (! preg_match('/^[A-Za-z0-9_-]{1,80}$/', $orderId)) {
            return $this->response->setStatusCode(404)->setBody('Order link is invalid.');
        }

        return $this->response
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Cache-Control', 'no-store, private')
            ->setBody(view('pages/orderAccessBridge', [
                'orderId' => $orderId,
                'locale' => $locale,
            ]));
    }

    public function exchange()
    {
        $rawOrderId = $this->request->getPost('idorder');
        $rawToken = $this->request->getPost('access_token');
        $orderId = is_string($rawOrderId) ? trim($rawOrderId) : '';
        $token = is_string($rawToken) ? strtolower(trim($rawToken)) : '';

        if (! preg_match('/^[A-Za-z0-9_-]{1,80}$/', $orderId)
            || ! preg_match('/^[a-f0-9]{64}$/', $token)
        ) {
            return $this->response->setStatusCode(404)->setBody('Order link is invalid or expired.');
        }

        $order = (new PemesananModel())->getPemesanan($orderId);
        $data = json_decode((string) ($order['data_mid'] ?? ''), true);
        $expected = is_array($data) ? strtolower((string) ($data['_app_order_access_token'] ?? '')) : '';

        if (! $order || ! preg_match('/^[a-f0-9]{64}$/', $expected)
            || ! hash_equals($expected, $token)
            || (new AccountSuspensionService())->isSuspended((string) ($order['email'] ?? ''))
        ) {
            return $this->response->setStatusCode(404)->setBody('Order link is invalid or expired.');
        }

        $session = session();
        $session->regenerate(true);
        $tokens = (array) $session->get('checkout_order_tokens');
        $tokens[$orderId] = $token;
        $session->set('checkout_order_tokens', array_slice($tokens, -20, null, true));

        $expiries = (array) $session->get('checkout_order_token_expiries');
        $expiries[$orderId] = time() + 7 * 24 * 60 * 60;
        $session->set('checkout_order_token_expiries', array_slice($expiries, -20, null, true));

        $rawLocale = $this->request->getPost('locale');
        $locale = is_string($rawLocale) && strtolower($rawLocale) === 'en' ? 'en' : 'id';
        $status = strtolower(rawurlencode((string) ($order['status'] ?? 'Menunggu Pembayaran')));
        $path = '/orderdetail/' . $status . '?idorder=' . rawurlencode($orderId);

        return redirect()->to(ilena_store_path($path, $locale))
            ->setHeader('Referrer-Policy', 'no-referrer')
            ->setHeader('Cache-Control', 'no-store, private');
    }
}
