<?php

namespace Tests\Unit;

use App\Database\Migrations\CreateHybridChatProviderSettings;
use CodeIgniter\Test\CIUnitTestCase;

require_once APPPATH . 'Database/Migrations/2026-10-28-000006_CreateHybridChatProviderSettings.php';

final class HybridChatProviderSettingsMigrationTest extends CIUnitTestCase
{
    public function testCreatesAndDropsTheProviderSettingsTable(): void
    {
        $forge = \Config\Database::forge('tests');
        $migration = new CreateHybridChatProviderSettings($forge);

        try {
            $migration->up();
            $this->assertTrue($forge->getConnection()->tableExists('ilena_chat_provider_settings'));
            $fields = $forge->getConnection()->getFieldNames('ilena_chat_provider_settings');
            $this->assertContains('api_key_encrypted', $fields);
            $this->assertContains('is_enabled', $fields);
        } finally {
            $migration->down();
        }

        $this->assertFalse($forge->getConnection()->tableExists('ilena_chat_provider_settings'));
    }
}
