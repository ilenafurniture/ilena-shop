<?php

use App\Services\TestOrderAccess;
use CodeIgniter\Test\CIUnitTestCase;

final class TestOrderAccessTest extends CIUnitTestCase
{
    public function testInternalTestOrderIsReadOnlyForOperationalAdmin(): void
    {
        $order = ['email' => ' GalihSuks123@gmail.com '];

        $this->assertTrue(TestOrderAccess::isTestOrder($order));
        $this->assertTrue(TestOrderAccess::isReadOnlyForRole($order, '5'));
        $this->assertFalse(TestOrderAccess::isReadOnlyForRole($order, '1'));
    }

    public function testNormalOrderIsNotClassifiedAsTestOrReadOnly(): void
    {
        $order = ['email' => 'buyer@example.test'];

        $this->assertFalse(TestOrderAccess::isTestOrder($order));
        $this->assertFalse(TestOrderAccess::isReadOnlyForRole($order, '5'));
    }
}
