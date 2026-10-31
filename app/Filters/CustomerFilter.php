<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Use Cloudflare's country signal for the visitor's first storefront visit.
        // A stored choice always wins; explicit /en URLs and the language switch are untouched.
        if (strtolower($request->getMethod()) === 'get') {
            $path = '/' . ltrim((string) $request->getUri()->getPath(), '/');
            $isEnglishPath = preg_match('#^/en(?:/|$)#i', $path) === 1;
            $isLanguageSwitch = preg_match('#^/language(?:/|$)#i', $path) === 1;
            $isChatApi = preg_match('#^/chat(?:/|$)#i', $path) === 1;
            $country = $request->getHeaderLine('CF-IPCountry');

            if (! $isEnglishPath && ! $isLanguageSwitch && ! $isChatApi
                && \App\Services\LocalePreference::choose($request->getCookie('ilena_locale'), $country) === 'en'
            ) {
                $query = $request->getUri()->getQuery();
                $destination = '/en' . ($path === '/' ? '' : $path)
                    . ($query !== '' ? '?' . $query : '');
                return redirect()->to($destination);
            }
        }

        $guard = (new \App\Services\Auth\CustomerSessionGuard())->enforce();
        if ($guard !== null) return $guard;

        $ses_data = ["kurir", "alamatTerpilih", "hargaKeseluruhan", "kurirTerpilih"];
        session()->remove($ses_data);
        if(session()->get('role') == '2'){
            session()->destroy();
            return redirect()->to(ilena_store_path('/login'))->with('msg', lang('Store.warehouseAccessDisabled'));
        }
        $suspension = (new \App\Services\AccountSuspensionService())->enforceSession();
        if ($suspension !== null) {
            $until = new \DateTimeImmutable($suspension['until'], new \DateTimeZone('UTC'));
            $when = $until->format('d M Y H:i') . ' UTC';
            return redirect()->to(ilena_store_path('/login'))->with('msg', sprintf(lang('Store.accountSuspendedUntil'), $when));
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
