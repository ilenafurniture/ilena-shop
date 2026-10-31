<?php

namespace Tests\Unit;

use App\Services\Auth\TransactionalEmailTemplate;
use CodeIgniter\Test\CIUnitTestCase;

final class TransactionalEmailTemplateTest extends CIUnitTestCase
{
    public function testSecurityEmailHasBrandedHtmlAndPlainTextFallback(): void
    {
        $html = TransactionalEmailTemplate::html('Konfirmasi email', 'Selesaikan perubahan akun.', '<p>Tautan berlaku 30 menit.</p>', 'Konfirmasi', 'https://ilenafurniture.com/account/confirm/token');
        $plain = TransactionalEmailTemplate::plain('Konfirmasi email', 'Selesaikan perubahan akun.', 'Tautan berlaku 30 menit.', 'Konfirmasi', 'https://ilenafurniture.com/account/confirm/token');

        $this->assertStringContainsString('ILENA FURNITURE', $html);
        $this->assertStringContainsString('https://ilenafurniture.com/account/confirm/token', html_entity_decode($html, ENT_QUOTES | ENT_HTML5));
        $this->assertStringContainsString('Konfirmasi: https://ilenafurniture.com/account/confirm/token', $plain);
        $this->assertStringContainsString('Tautan berlaku 30 menit.', $plain);
    }

    public function testActionLinkRejectsNonHttpUrl(): void
    {
        $html = TransactionalEmailTemplate::html('Reset', 'Intro.', '<p>Detail</p>', 'Buka', 'javascript:alert(1)');
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('>Buka</a>', $html);
    }
}
