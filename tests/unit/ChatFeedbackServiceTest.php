<?php

namespace Tests\Unit;

use App\Services\ChatFeedbackService;
use CodeIgniter\Test\CIUnitTestCase;

final class ChatFeedbackServiceTest extends CIUnitTestCase
{
    public function testFeedbackWaitsUntilMoreThanFiveMinutesAfterAnIlenaReply(): void
    {
        $now = strtotime('2026-10-28 12:10:00');

        $this->assertFalse(ChatFeedbackService::isEligible('assistant', date('Y-m-d H:i:s', $now - 300), $now));
        $this->assertTrue(ChatFeedbackService::isEligible('assistant', date('Y-m-d H:i:s', $now - 301), $now));
        $this->assertTrue(ChatFeedbackService::isEligible('admin', date('Y-m-d H:i:s', $now - 301), $now));
    }

    public function testVisitorAndSystemMessagesNeverTriggerTheFeedbackPrompt(): void
    {
        $now = strtotime('2026-10-28 12:10:00');
        $old = date('Y-m-d H:i:s', $now - 3600);

        $this->assertFalse(ChatFeedbackService::isEligible('visitor', $old, $now));
        $this->assertFalse(ChatFeedbackService::isEligible('system', $old, $now));
        $this->assertFalse(ChatFeedbackService::isEligible('assistant', 'invalid', $now));
    }
}
