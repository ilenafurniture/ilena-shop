<?php

namespace Tests\Unit;

use App\Services\Auth\AuthAttemptLimiter;
use App\Services\Auth\DisposableEmailDomainList;
use App\Services\Auth\RecaptchaVerifier;
use CodeIgniter\Test\CIUnitTestCase;
use Config\AuthCaptcha;

final class AuthProtectionServiceTest extends CIUnitTestCase
{
    public function testCaptchaAcceptsOnlyFreshSuccessfulChallengeForConfiguredHost(): void
    {
        $config = $this->captchaConfig();
        $client = new FakeCaptchaClient(json_encode([
            'success' => true,
            'hostname' => 'ilenafurniture.com',
            'challenge_ts' => gmdate('c'),
        ]));

        $this->assertNull((new RecaptchaVerifier($config, $client))->error('valid-test-token'));
        $this->assertSame('valid-test-token', $client->options['form_params']['response']);
        $this->assertSame('test-secret', $client->options['form_params']['secret']);
    }

    public function testCaptchaRejectsWrongHostStaleTokenAndMissingConfiguration(): void
    {
        $config = $this->captchaConfig();
        $wrongHost = new FakeCaptchaClient(json_encode([
            'success' => true,
            'hostname' => 'attacker.example',
            'challenge_ts' => gmdate('c'),
        ]));
        $this->assertNotNull((new RecaptchaVerifier($config, $wrongHost))->error('test-token'));

        $stale = new FakeCaptchaClient(json_encode([
            'success' => true,
            'hostname' => 'ilenafurniture.com',
            'challenge_ts' => gmdate('c', time() - 600),
        ]));
        $this->assertNotNull((new RecaptchaVerifier($config, $stale))->error('test-token'));
        $this->assertNotNull((new RecaptchaVerifier($this->captchaConfig('')))->error('test-token'));
        $this->assertNotNull((new RecaptchaVerifier($config, $wrongHost))->error(''));
    }

    public function testCaptchaCanBeExplicitlyDisabledOutsideProduction(): void
    {
        $config = $this->captchaConfig();
        $config->enabled = false;
        $this->assertNull((new RecaptchaVerifier($config))->error(null));
    }

    public function testAttemptLimiterUsesOpaqueKeysAndRejectsWhenThrottleSaysNo(): void
    {
        $keys = [];
        $limiter = new AuthAttemptLimiter(static function (string $key, int $limit, int $seconds) use (&$keys): bool {
            $keys[] = [$key, $limit, $seconds];
            return true;
        });
        $this->assertTrue($limiter->allow('login', '192.0.2.1', 'Example@Email.test', 20, 8, 900));
        $this->assertCount(2, $keys);
        $this->assertStringNotContainsString('Example@Email.test', $keys[1][0]);
        $this->assertFalse((new AuthAttemptLimiter(static fn(): bool => false))
            ->allow('login', '192.0.2.1', 'example@email.test', 20, 8, 900));
        $this->assertFalse($limiter->allow('../login', '192.0.2.1', 'x', 1, 1, 1));
    }

    public function testDisposableDomainListRefreshIsAtomicAndMatchesSubdomains(): void
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ilena-domain-test-' . bin2hex(random_bytes(5));
        $path = $directory . DIRECTORY_SEPARATOR . 'domains.txt';
        $lines = [];
        for ($i = 0; $i < 1000; $i++) $lines[] = 'disposable' . $i . '.example';

        try {
            $list = new DisposableEmailDomainList($path, static fn(): string => implode("\n", $lines));
            $this->assertSame(1000, $list->refresh());
            $this->assertTrue($list->isListed('disposable17.example'));
            $this->assertTrue($list->isListed('mail.sub.disposable17.example'));
            $this->assertFalse($list->isListed('legitimate.example'));
            $this->expectException(\RuntimeException::class);
            (new DisposableEmailDomainList($path, static fn(): string => "not a domain\n"))->refresh();
        } finally {
            if (is_file($path)) unlink($path);
            if (is_dir($directory)) rmdir($directory);
        }
    }

    private function captchaConfig(string $secret = 'test-secret'): AuthCaptcha
    {
        $config = new AuthCaptcha();
        $config->enabled = true;
        $config->siteKey = 'test-site';
        $config->secretKey = $secret;
        $config->allowedHostnames = 'ilenafurniture.com,www.ilenafurniture.com';
        return $config;
    }
}

final class FakeCaptchaClient
{
    public array $options = [];

    public function __construct(private string $body, private int $statusCode = 200)
    {
    }

    public function post(string $url, array $options): FakeCaptchaResponse
    {
        if ($url !== 'https://www.google.com/recaptcha/api/siteverify') {
            throw new \RuntimeException('Unexpected CAPTCHA endpoint.');
        }
        $this->options = $options;
        return new FakeCaptchaResponse($this->body, $this->statusCode);
    }
}

final class FakeCaptchaResponse
{
    public function __construct(private string $body, private int $statusCode)
    {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
