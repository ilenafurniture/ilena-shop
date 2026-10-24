<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/** SiteVerify configuration for public web authentication forms. */
class AuthCaptcha extends BaseConfig
{
    public bool $enabled;
    public string $siteKey;
    public string $secretKey;
    public string $allowedHostnames;

    public function __construct()
    {
        parent::__construct();
        // Production fails closed. Add the site/secret keys and allowed Ilena hosts
        // to the server .env before deploying this feature.
        $this->enabled = filter_var(env('ILENA_RECAPTCHA_ENABLED', ENVIRONMENT === 'production'), FILTER_VALIDATE_BOOLEAN);
        $this->siteKey = (string) env('ILENA_RECAPTCHA_SITE_KEY', '');
        $this->secretKey = (string) env('ILENA_RECAPTCHA_SECRET_KEY', '');
        $this->allowedHostnames = (string) env('ILENA_RECAPTCHA_ALLOWED_HOSTNAMES', 'ilenafurniture.com,www.ilenafurniture.com');
    }
}
