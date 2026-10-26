<?php

use App\Services\ScriptJson;
use CodeIgniter\Test\CIUnitTestCase;

final class ScriptJsonTest extends CIUnitTestCase
{
    public function testInlineScriptPayloadCannotCloseItsScriptElement(): void
    {
        $payload = ['nama' => '</script><script>alert(1)</script>', 'deskripsi' => '<img src=x onerror=alert(1)>'];
        $encoded = ScriptJson::encode($payload);

        $this->assertStringNotContainsString('<', $encoded);
        $this->assertStringNotContainsString('>', $encoded);
        $this->assertSame($payload, json_decode($encoded, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testInvalidUtf8IsSafelySubstituted(): void
    {
        $encoded = ScriptJson::encode(["text" => "invalid-\xB1"]);
        $this->assertSame('invalid-�', json_decode($encoded, true, 512, JSON_THROW_ON_ERROR)['text']);
    }
}
