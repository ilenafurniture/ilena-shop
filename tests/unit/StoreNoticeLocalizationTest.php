<?php

use CodeIgniter\Test\CIUnitTestCase;

final class StoreNoticeLocalizationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('ilena_store');
    }

    public function testEnglishLocaleTranslatesLegacyCartAndCheckoutNotices(): void
    {
        $this->assertSame(
            'Your cart is empty.',
            ilena_localize_notice('Keranjangmu kosong.', 'en')
        );
        $this->assertSame(
            'Payment has not been confirmed, so the order is not marked as successful. Reload and check its status before trying again.',
            ilena_localize_notice('Pembayaran belum mendapat konfirmasi. Pesanan belum ditampilkan sebagai berhasil; muat ulang dan periksa status sebelum mencoba lagi.', 'en')
        );
    }

    public function testDynamicNoticesPreserveCustomerSpecificDetailsInEnglish(): void
    {
        $this->assertSame(
            'There is not enough stock for Cody (White).',
            ilena_localize_notice('Stok Cody varian White tidak mencukupi.', 'en')
        );
        $this->assertSame(
            'Please verify the email address buyer@example.com.',
            ilena_localize_notice('Email buyer@example.com perlu diverifikasi', 'en')
        );
    }

    public function testIndonesianAndAlreadyTranslatedMessagesRemainUnchanged(): void
    {
        $this->assertSame('Keranjangmu kosong.', ilena_localize_notice('Keranjangmu kosong.', 'id'));
        $this->assertSame('Alert saved.', ilena_localize_notice('Alert saved.', 'en'));
    }
}
