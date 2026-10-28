<?php

namespace Tests\Unit;

use App\Database\Migrations\CreateHybridChatProviderSettings;
use App\Services\ChatProviderSettings;
use CodeIgniter\Test\CIUnitTestCase;

require_once APPPATH . 'Database/Migrations/2026-10-28-000006_CreateHybridChatProviderSettings.php';

final class ChatProviderSettingsTest extends CIUnitTestCase
{
    public function testDevProfileKeepsSecretEncryptedAndWorkerCanReadIt(): void
    {
        $key = bin2hex(random_bytes(32));
        putenv('ILENA_CHAT_CREDENTIAL_KEY=' . $key);
        $_ENV['ILENA_CHAT_CREDENTIAL_KEY'] = $key;
        $_SERVER['ILENA_CHAT_CREDENTIAL_KEY'] = $key;

        $forge = \Config\Database::forge('tests');
        $migration = new CreateHybridChatProviderSettings($forge);
        $settings = new ChatProviderSettings($forge->getConnection());
        $secret = 'free-provider-secret-test-value';

        try {
            $migration->up();
            $saved = $settings->save('https://api.example.test/v1/chat/completions', 'provider/free-model', $secret, true, false, 'dev@example.test');
            $this->assertTrue($saved['ok']);

            $row = $forge->getConnection()->table('ilena_chat_provider_settings')->where('id', 1)->get()->getRowArray();
            $this->assertNotSame($secret, $row['api_key_encrypted']);
            $this->assertSame($secret, $settings->forWorker()['apiKey']);
            $this->assertArrayNotHasKey('apiKey', $settings->adminSummary());

            $settings->save('https://api.example.test/v1/chat/completions', 'provider/free-model', '', true, false, 'dev@example.test');
            $this->assertSame($secret, $settings->forWorker()['apiKey'], 'Blank key input preserves the stored secret.');

            $settings->save('https://api.example.test/v1/chat/completions', 'provider/free-model', '', false, true, 'dev@example.test');
            $this->assertSame('', $settings->forWorker()['apiKey'], 'Clearing and disabling the profile disables provider use.');
        } finally {
            $migration->down();
            putenv('ILENA_CHAT_CREDENTIAL_KEY');
            unset($_ENV['ILENA_CHAT_CREDENTIAL_KEY'], $_SERVER['ILENA_CHAT_CREDENTIAL_KEY']);
        }
    }
}
