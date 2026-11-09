<?php

use CodeIgniter\Test\CIUnitTestCase;

final class CustomerApiContractTest extends CIUnitTestCase
{
    public function testVersionOneExposesCustomerRoutesWithoutAdminRoutes(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');
        $this->assertIsString($routes);
        $apiStart = strpos($routes, '$routes->group(\'api/v1\'');
        $this->assertNotFalse($apiStart);
        $apiEnd = strpos($routes, "});", $apiStart);
        $this->assertNotFalse($apiEnd);
        $apiRoutes = substr($routes, $apiStart, $apiEnd - $apiStart);
        foreach ([
            'CatalogController::index', 'CatalogController::show', 'CatalogController::categories',
            'RegionsController::provinces', 'RegionsController::cities', 'RegionsController::districts', 'RegionsController::villages',
            'AuthController::register', 'AuthController::verifyEmail', 'AuthController::resendVerification', 'AuthController::login',
            'AuthController::googleNonce', 'AuthController::googleLogin',
            'AccountController::addresses', 'CartController::show', 'WishlistController::show',
            'OrdersController::index', 'ShippingController::estimate',
            'CheckoutController::quote', 'CheckoutController::create',
        ] as $handler) {
            $this->assertStringContainsString($handler, $apiRoutes, $handler . ' is routed under /api/v1');
        }
        $this->assertStringNotContainsString('AdminController', $apiRoutes);
        $this->assertStringNotContainsString('admin/', $apiRoutes);
    }

    public function testOpenApiContractIsCustomerOnlyAndDocumentsAuthAndCheckoutBoundaries(): void
    {
        $contract = file_get_contents(ROOTPATH . 'docs/api/openapi-v1.yaml');
        $guide = file_get_contents(ROOTPATH . 'docs/api/README.md');
        $this->assertIsString($contract);
        $this->assertIsString($guide);
        foreach ([
            '/auth/register:', '/auth/verify-email:', '/auth/login:', '/auth/google/nonce:', '/auth/google:', '/account/cart:',
            '/account/orders:', '/account/shipping/estimate:', '/account/checkout/quote:',
            '/account/checkout/orders:', '/regions/villages:',
        ] as $path) {
            $this->assertStringContainsString($path, $contract);
        }
        $this->assertStringNotContainsString('/admin/', $contract);
        $this->assertStringContainsString('Native checkout quote and order creation', $guide);
        $this->assertStringContainsString('Keystore', $guide);
        $this->assertStringContainsString('server-issued single-use nonce', strtolower($guide));
    }


    public function testGoogleNonceIsSingleUseAndOnlyItsHashIsStored(): void
    {
        $service = file_get_contents(APPPATH . 'Services/Auth/GoogleAuthNonceService.php');
        $migration = file_get_contents(APPPATH . 'Database/Migrations/2026-11-09-000028_CreateCustomerGoogleAuthNonces.php');
        $this->assertIsString($service);
        $this->assertIsString($migration);
        $this->assertStringContainsString("'nonce_hash' => hash('sha256', \$nonce)", $service);
        $this->assertStringContainsString("->delete()", $service);
        $this->assertStringContainsString("'nonce_hash' => ['type' => 'CHAR'", $migration);
    }

    public function testCustomerBearerTokensAreOnlyPersistedAsHashesAndExpire(): void
    {
        $service = file_get_contents(APPPATH . 'Services/Auth/CustomerApiTokenService.php');
        $migration = file_get_contents(APPPATH . 'Database/Migrations/2026-11-09-000027_CreateCustomerApiTokens.php');
        $this->assertIsString($service);
        $this->assertIsString($migration);
        $this->assertStringContainsString("'token_hash' => hash('sha256', \$plainToken)", $service);
        $this->assertStringContainsString("modify('+30 days')", $service);
        $this->assertStringContainsString("'token_hash' => ['type' => 'CHAR'", $migration);
    }

    public function testNativeCheckoutUsesIdempotencyAndNeverTrustsClientAmounts(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Api/V1/CheckoutController.php');
        $migration = file_get_contents(APPPATH . 'Database/Migrations/2026-11-09-000030_CreateCustomerCheckoutIdempotency.php');
        $worker = file_get_contents(APPPATH . 'Services/MidtransCheckoutJobHandler.php');
        $this->assertIsString($controller);
        $this->assertIsString($migration);
        $this->assertIsString($worker);
        $this->assertStringContainsString("getHeaderLine('Idempotency-Key')", $controller);
        $this->assertStringContainsString("hash('sha256', \$idempotency)", $controller);
        $this->assertStringContainsString("getActiveProductsByIds", $controller);
        $this->assertStringContainsString("StockReservationService(\$db))->reserve", $controller);
        $this->assertStringContainsString("'midtrans.charge'", $controller);
        $this->assertStringContainsString("'uq_customer_checkout_idempotency'", $migration);
        $this->assertStringContainsString("service('encrypter')->decrypt", $worker);
        $createMethod = substr($controller, strpos($controller, 'public function create(): ResponseInterface'));
        $this->assertLessThan(strpos($createMethod, '$prepared = $this->prepare($auth[\'user\'], $body);'), strpos($createMethod, '$prior = $db->table(\'customer_checkout_idempotency\')'));
        $this->assertStringContainsString('one outer transaction', $controller);
    }
}
