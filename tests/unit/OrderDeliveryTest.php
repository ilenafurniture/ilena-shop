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

    public function testAutomaticCompletionRequiresSixFullDaysAndNoOpenCase(): void
    {
        $shippedAt = 1_800_000_000;

        $this->assertFalse(OrderDelivery::isAutoCompletionDue('Dikirim', $shippedAt, $shippedAt + (6 * 86400) - 1, false));
        $this->assertTrue(OrderDelivery::isAutoCompletionDue('Dikirim', $shippedAt, $shippedAt + (6 * 86400), false));
        $this->assertFalse(OrderDelivery::isAutoCompletionDue('Dikirim', $shippedAt, $shippedAt + (7 * 86400), true));
        $this->assertFalse(OrderDelivery::isAutoCompletionDue('Proses', $shippedAt, $shippedAt + (7 * 86400), false));
        $this->assertFalse(OrderDelivery::isAutoCompletionDue('Dikirim', null, $shippedAt + (7 * 86400), false));
    }

    public function testLegacyShipmentDateIsValidatedAndStartsAfterTheSelectedDay(): void
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $now = (new DateTimeImmutable('2026-11-08 12:00:00', $timezone))->getTimestamp();
        $shippedAt = OrderDelivery::legacyShipmentTimestamp('2026-11-01', $now);

        $this->assertNotNull($shippedAt);
        $this->assertSame('2026-11-01 23:59:59', (new DateTimeImmutable('@' . $shippedAt))->setTimezone($timezone)->format('Y-m-d H:i:s'));
        $this->assertFalse(OrderDelivery::isAutoCompletionDue('Dikirim', $shippedAt, $shippedAt + (6 * 86400) - 1, false));
        $this->assertTrue(OrderDelivery::isAutoCompletionDue('Dikirim', $shippedAt, $shippedAt + (6 * 86400), false));
        $this->assertNull(OrderDelivery::legacyShipmentTimestamp('2026-02-30', $now));
        $this->assertNull(OrderDelivery::legacyShipmentTimestamp('2026-11-09', $now));
        $this->assertNull(OrderDelivery::legacyShipmentTimestamp('not-a-date', $now));
    }
}
