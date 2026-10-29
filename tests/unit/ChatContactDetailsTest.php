<?php

namespace Tests\Unit;

use App\Services\ChatContactDetails;
use CodeIgniter\Test\CIUnitTestCase;
use InvalidArgumentException;

final class ChatContactDetailsTest extends CIUnitTestCase
{
    public function testNormalizesIndonesianLocalAndInternationalPhoneNumbers(): void
    {
        $local = ChatContactDetails::normalize('  Siti   Aminah ', '0812-3456-7890');
        $international = ChatContactDetails::normalize('Jordan Lee', '+1 (415) 555-2671');

        $this->assertSame(['name' => 'Siti Aminah', 'phone' => '+6281234567890'], $local);
        $this->assertSame('+14155552671', $international['phone']);
    }

    public function testRejectsInvalidNamesAndPhoneNumbers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChatContactDetails::normalize('<script>alert(1)</script>', 'abc');
    }

    public function testRejectsTooShortPhoneNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChatContactDetails::normalize('Siti Aminah', '08123');
    }
}
