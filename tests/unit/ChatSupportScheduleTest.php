<?php

namespace Tests\Unit;

use App\Services\ChatSupportSchedule;
use CodeIgniter\Test\CIUnitTestCase;

final class ChatSupportScheduleTest extends CIUnitTestCase
{
    public function testCustomersCanStillChatOutsideStaffHoursAndReceiveAnOfflineReply(): void
    {
        $closed = ChatSupportSchedule::availability(new \DateTimeImmutable('2026-10-28 18:30:00', new \DateTimeZone('Asia/Jakarta')));
        $reply = ChatSupportSchedule::afterHoursReply('https://wa.me/628112938158?text=Halo');

        $this->assertFalse($closed['open']);
        $this->assertStringContainsString('di luar jam layanan', mb_strtolower($reply['body']));
        $this->assertStringContainsString('Kak Wawa tetap siap membantu Kakak di sini.', $reply['body']);
        $this->assertStringNotContainsString('jam layanan berikutnya', $reply['body']);
        $this->assertStringContainsString('Kakak dapat menghubungi kami melalui WhatsApp.', $reply['body']);
        $this->assertStringContainsString('08.00–16.00 WIB', $reply['body']);
        $this->assertSame('https://wa.me/628112938158?text=Halo', $reply['whatsappUrl']);
    }

    public function testStaffHoursAreOpenFromEightUntilJustBeforeFour(): void
    {
        $open = ChatSupportSchedule::availability(new \DateTimeImmutable('2026-10-28 08:00:00', new \DateTimeZone('Asia/Jakarta')));
        $closed = ChatSupportSchedule::availability(new \DateTimeImmutable('2026-10-28 16:00:00', new \DateTimeZone('Asia/Jakarta')));

        $this->assertTrue($open['open']);
        $this->assertFalse($closed['open']);
    }
}
