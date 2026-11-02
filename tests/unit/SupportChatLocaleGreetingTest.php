<?php

use CodeIgniter\Test\CIUnitTestCase;

final class SupportChatLocaleGreetingTest extends CIUnitTestCase
{
    public function testGreetingStateIsLocaleScopedAndLanguageChangesCanShowGreetingAgain(): void
    {
        $script = file_get_contents(FCPATH . 'js/ilena-support-chat.js');

        $this->assertStringContainsString('ilenaChatLastGreetingLocale', $script);
        $this->assertStringContainsString('localeChanged', $script);
        $this->assertStringContainsString('const greetingStorageKey = key =>', $script);
        $this->assertStringContainsString("greetingStorageKey('Seen')", $script);
        $this->assertStringContainsString("greetingStorageKey('Dismissed')", $script);
        $this->assertStringContainsString("greetingStorageKey('Opened')", $script);
        $this->assertStringContainsString('chatGreetingTitle', file_get_contents(APPPATH . 'Language/en/Store.php'));
        $this->assertStringContainsString('chatGreetingTitle', file_get_contents(APPPATH . 'Language/id/Store.php'));
    }
}
