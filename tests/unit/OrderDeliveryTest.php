<?php

use App\Services\OrderDelivery;
use CodeIgniter\Test\CIUnitTestCase;

final class OrderDeliveryTest extends CIUnitTestCase
{
    public function testCourierRateAndEstimateSurviveWhenAdminAddsTrackingForSameService(): void
    {
        $original = [
            'nama' => 'JNE',
            'deskripsi' => 'JNE Trucking',
            'harga' => 125000,
            'estimasi' => '3-5',
            'service_code' => 'JTR',
        ];

        $updated = OrderDelivery::courierSnapshot($original, 'JNE', 'JNE Trucking');

        $this->assertSame(125000, $updated['harga']);
        $this->assertSame('3-5', $updated['estimasi']);
        $this->assertSame('JTR', $updated['service_code']);
    }

    public function testChangingCourierDoesNotKeepStaleRateOrEstimate(): void
    {
        $updated = OrderDelivery::courierSnapshot([
            'nama' => 'JNE', 'deskripsi' => 'JNE Trucking', 'harga' => 125000, 'estimasi' => '3-5',
        ], 'SiCepat', 'REG');

        $this->assertSame(['nama' => 'SiCepat', 'deskripsi' => 'REG'], $updated);
    }

    public function testReceiptCanOnlyBeConfirmedForDispatchedOrders(): void
    {
        $this->assertTrue(OrderDelivery::canBuyerConfirmReceipt('Dikirim'));
        $this->assertFalse(OrderDelivery::canBuyerConfirmReceipt('Proses'));
        $this->assertFalse(OrderDelivery::canBuyerConfirmReceipt('Selesai'));
        $this->assertFalse(OrderDelivery::canBuyerConfirmReceipt('Dibatalkan'));
        $this->assertTrue(OrderDelivery::isCustomerRole('0'));
        $this->assertTrue(OrderDelivery::isCustomerRole('4'));
        $this->assertFalse(OrderDelivery::isCustomerRole('1'));
        $this->assertFalse(OrderDelivery::isCustomerRole('3'));
    }
}
