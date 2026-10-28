<?php

namespace Tests\Unit;

use App\Database\Migrations\CreateHybridChatFoundation;
use CodeIgniter\Test\CIUnitTestCase;

require_once APPPATH . 'Database/Migrations/2026-10-28-000005_CreateHybridChatFoundation.php';

final class HybridChatMigrationTest extends CIUnitTestCase
{
    public function testCreatesAndDropsTheChatPersistenceTables(): void
    {
        $forge = \Config\Database::forge('tests');
        $migration = new CreateHybridChatFoundation($forge);
        $tables = [
            'ilena_chat_conversations',
            'ilena_chat_messages',
            'ilena_chat_jobs',
            'ilena_chat_knowledge',
            'ilena_chat_product_specs',
        ];

        try {
            $migration->up();
            foreach ($tables as $table) {
                $this->assertTrue($forge->getConnection()->tableExists($table), $table . ' should exist after migration.');
            }
        } finally {
            $migration->down();
        }

        foreach ($tables as $table) {
            $this->assertFalse($forge->getConnection()->tableExists($table), $table . ' should be removed on rollback.');
        }
    }
}
