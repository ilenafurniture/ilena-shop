<?php

namespace Tests\Unit;

use App\Services\CheckoutEmailPolicy;
use CodeIgniter\Test\CIUnitTestCase;

final class CheckoutEmailPolicyTest extends CIUnitTestCase
{
    public function testAcceptsAndNormalizesARegularEmail(): void
    {
        $this->assertSame('buyer@example.com', CheckoutEmailPolicy::normalize(' Buyer@Example.COM '));
        $this->assertSame('buyer@gmail.com', CheckoutEmailPolicy::normalize('buyer@gmail.com'));
    }

    public function testRejectsMalformedValuesAndDisposableDomains(): void
    {
        foreach ([null, ['buyer@mail.tm'], '', 'not-an-email', str_repeat('a', 192) . '@example.com', 'buyer@mail.tm', 'buyer@sub.mail.tm', 'buyer@tempmail.com'] as $email) {
            $this->assertNull(CheckoutEmailPolicy::normalize($email));
        }
    }
}
