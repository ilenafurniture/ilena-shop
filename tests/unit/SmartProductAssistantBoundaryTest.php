<?php

namespace Tests\Unit;

use App\Services\SmartProductAssistant;
use CodeIgniter\Test\CIUnitTestCase;

final class SmartProductAssistantBoundaryTest extends CIUnitTestCase
{
    public function testOffTopicQuestionGetsPoliteIlenaOnlyRedirectWithoutProviderCall(): void
    {
        $assistant = new SmartProductAssistant(new \stdClass());

        $result = $assistant->respond('Siapa yang akan menang pertandingan malam ini?');

        $this->assertStringContainsString('fokus membantu seputar furniture', $result['answer']);
        $this->assertStringContainsString('WhatsApp', $result['answer']);
        $this->assertSame([], $result['products']);
        $this->assertSame([], $result['sources']);
    }
}
