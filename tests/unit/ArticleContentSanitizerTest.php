<?php

use App\Services\ArticleContentSanitizer;
use App\Services\SpamAccountCleanupService;
use CodeIgniter\Test\CIUnitTestCase;

final class ArticleContentSanitizerTest extends CIUnitTestCase
{
    public function testDropsExecutableMarkupAndUnsafeAttributesButKeepsFormatting(): void
    {
        $clean = (new ArticleContentSanitizer())->sanitize('<p onclick="alert(1)">Hello <strong>Ilena</strong><script>alert(1)</script><a href="javascript:alert(1)">x</a></p>');
        $this->assertStringContainsString('<strong>Ilena</strong>', $clean);
        $this->assertStringNotContainsString('<script', strtolower($clean));
        $this->assertStringNotContainsString('onclick', strtolower($clean));
        $this->assertStringNotContainsString('javascript:', strtolower($clean));
    }

    public function testSpamCleanupEligibilityRequiresUnverifiedCustomerRoleAndExpiredOtp(): void
    {
        $cleanup = new SpamAccountCleanupService();
        $expired = ['role' => '0', 'active' => '0', 'waktu_otp' => time() - 10 * 86400];
        $this->assertTrue($cleanup->isInactiveExpiredAccount($expired, 7));
        $this->assertFalse($cleanup->isInactiveExpiredAccount(array_replace($expired, ['role' => '5']), 7));
        $this->assertFalse($cleanup->isInactiveExpiredAccount(array_replace($expired, ['active' => '1']), 7));
        $this->assertFalse($cleanup->isInactiveExpiredAccount(['role' => '0', 'active' => '0', 'waktu_otp' => time()], 7));
    }
}
