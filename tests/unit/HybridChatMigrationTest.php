<?php

namespace Tests\Unit;

use App\Database\Migrations\CreateHybridChatFoundation;
use App\Database\Migrations\AddKakWawaFeedback;
use CodeIgniter\Test\CIUnitTestCase;

require_once APPPATH . 'Database/Migrations/2026-10-28-000005_CreateHybridChatFoundation.php';
require_once APPPATH . 'Database/Migrations/2026-10-28-000007_AddKakWawaFeedback.php';

final class HybridChatMigrationTest extends CIUnitTestCase
{
    public function testCreatesAndDropsTheChatPersistenceTables(): void
    {
        $forge = \Config\Database::forge('tests');
        $migration = new CreateHybridChatFoundation($forge);
        $feedbackMigration = new AddKakWawaFeedback($forge);
        $tables = [
            'ilena_chat_conversations',
            'ilena_chat_messages',
            'ilena_chat_jobs',
            'ilena_chat_knowledge',
            'ilena_chat_product_specs',
        ];

        try {
            $migration->up();
            $feedbackMigration->up();
            foreach ($tables as $table) {
                $this->assertTrue($forge->getConnection()->tableExists($table), $table . ' should exist after migration.');
            }
            $this->assertTrue($forge->getConnection()->tableExists('ilena_chat_feedback'));
            $fieldNames = array_map(static fn ($field) => $field->name, $forge->getConnection()->getFieldData('ilena_chat_conversations'));
            $this->assertContains('feedback_prompted_at', $fieldNames, implode(', ', $fieldNames));
        } finally {
            $feedbackMigration->down();
            $migration->down();
        }
        $this->assertFalse($forge->getConnection()->tableExists('ilena_chat_feedback'));

        foreach ($tables as $table) {
            $this->assertFalse($forge->getConnection()->tableExists($table), $table . ' should be removed on rollback.');
        }
    }
}
