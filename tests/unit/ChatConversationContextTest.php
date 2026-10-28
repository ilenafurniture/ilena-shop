<?php

namespace Tests\Unit;

use App\Services\ChatConversationContext;
use CodeIgniter\Test\CIUnitTestCase;

final class ChatConversationContextTest extends CIUnitTestCase
{
    public function testContextIncludesOnlyBoundedCustomerAndAssistantTurns(): void
    {
        $rows = [
            ['sender_type' => 'system', 'body' => 'private routing metadata'],
            ['sender_type' => 'admin', 'body' => 'internal staff note'],
            ['sender_type' => 'visitor', 'body' => '<b>Sofa Cody warna apa?</b>'],
            ['sender_type' => 'assistant', 'body' => 'Tersedia dalam warna netral.'],
        ];

        $messages = ChatConversationContext::normalize($rows);

        $this->assertSame([
            ['role' => 'user', 'content' => 'Sofa Cody warna apa?'],
            ['role' => 'assistant', 'content' => 'Tersedia dalam warna netral.'],
        ], $messages);
        $this->assertStringNotContainsString('internal', json_encode($messages));
    }

    public function testContextIsCappedAtEightMessagesAndSevenHundredCharacters(): void
    {
        $rows = [];
        for ($i = 0; $i < 12; $i++) $rows[] = ['sender_type' => 'visitor', 'body' => str_repeat((string)$i, 900)];

        $messages = ChatConversationContext::normalize($rows);

        $this->assertCount(8, $messages);
        $this->assertSame(700, mb_strlen($messages[0]['content']));
        $this->assertSame(str_repeat('4', 700), $messages[0]['content']);
    }
}
