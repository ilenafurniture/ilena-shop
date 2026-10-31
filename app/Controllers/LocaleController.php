<?php

namespace App\Controllers;

use App\Services\LocalePreference;

class LocaleController extends BaseController
{
    public function switch(string $locale)
    {
        $locale = strtolower($locale);
        if (! in_array($locale, ['id', 'en'], true)) {
            return redirect()->to('/');
        }

        $returnTo = (string) $this->request->getGet('return');
        $target = LocalePreference::localizePath($returnTo, $locale);
        $response = redirect()->to($target);
        $response->setCookie(
            'ilena_locale', $locale, 60 * 60 * 24 * 365, '', '/', '',
            $this->request->isSecure(), true, 'Lax'
        );

        return $response;
    }
}
