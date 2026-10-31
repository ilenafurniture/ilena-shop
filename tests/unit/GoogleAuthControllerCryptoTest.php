<?php

namespace Tests\Unit;

use App\Controllers\GoogleAuthController;
use CodeIgniter\Test\CIUnitTestCase;

final class GoogleAuthControllerCryptoTest extends CIUnitTestCase
{
    public function testJwkRsaPublicKeyEncodingVerifiesSignature(): void
    {
        $privateKey = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]);
        self::assertNotFalse($privateKey);
        $details = openssl_pkey_get_details($privateKey);
        self::assertIsArray($details);

        $controller = (new \ReflectionClass(GoogleAuthController::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod(GoogleAuthController::class, 'rsaJwkToPem');
        $method->setAccessible(true);
        $pem = $method->invoke($controller,
            rtrim(strtr(base64_encode($details['rsa']['n']), '+/', '-_'), '='),
            rtrim(strtr(base64_encode($details['rsa']['e']), '+/', '-_'), '=')
        );
        self::assertIsString($pem);

        $message = 'ilena-google-oidc-offline-signature-fixture';
        self::assertTrue(openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA256));
        self::assertSame(1, openssl_verify($message, $signature, $pem, OPENSSL_ALGO_SHA256));
    }

    public function testMalformedJwkIsRejected(): void
    {
        $controller = (new \ReflectionClass(GoogleAuthController::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod(GoogleAuthController::class, 'rsaJwkToPem');
        $method->setAccessible(true);
        self::assertNull($method->invoke($controller, '!!!', 'AQAB'));
    }
}
