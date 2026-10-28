<?php

namespace Tests\Unit;

use App\Services\ChatSupportSchedule;
use CodeIgniter\Test\CIUnitTestCase;

final class ChatSupportScheduleTest extends CIUnitTestCase
{
    public function testCustomersCanStillChatOutsideStaffHoursWhileStaffIsUnavailable(): void
    {
        $closed = ChatSupportSchedule::availability(new \DateTimeImmutable('2026-10-28 18:30:00', new \DateTimeZone('Asia/Jakarta')));

        $this->assertFalse($closed['open']);
        $this->assertSame(ChatSupportSchedule::HOURS, $closed['hours']);
    }

    public function testStaffHoursAreOpenFromEightUntilJustBeforeFour(): void
    {
        $open = ChatSupportSchedule::availability(new \DateTimeImmutable('2026-10-28 08:00:00', new \DateTimeZone('Asia/Jakarta')));
        $closed = ChatSupportSchedule::availability(new \DateTimeImmutable('2026-10-28 16:00:00', new \DateTimeZone('Asia/Jakarta')));

        $this->assertTrue($open['open']);
        $this->assertFalse($closed['open']);
    }
}
