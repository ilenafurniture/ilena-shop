<?php

namespace Tests\Unit;

use App\Database\Migrations\CreateHybridChatFoundation;
use App\Database\Migrations\AddKakWawaFeedback;
use App\Database\Migrations\AddKakWawaQualityControls;
use App\Database\Migrations\AddKakWawaSendIdempotency;
use CodeIgniter\Test\CIUnitTestCase;

require_once APPPATH . 'Database/Migrations/2026-10-28-000005_CreateHybridChatFoundation.php';
require_once APPPATH . 'Database/Migrations/2026-10-28-000007_AddKakWawaFeedback.php';
require_once APPPATH . 'Database/Migrations/2026-10-28-000008_AddKakWawaQualityControls.php';
require_once APPPATH . 'Database/Migrations/2026-10-29-000009_AddKakWawaSendIdempotency.php';

final class HybridChatMigrationTest extends CIUnitTestCase
{
    public function testCreatesAndDropsTheChatPersistenceTables(): void
    {
        $forge = \Config\Database::forge('tests');
        $migration = new CreateHybridChatFoundation($forge);
        $feedbackMigration = new AddKakWawaFeedback($forge);
        $qualityMigration = new AddKakWawaQualityControls($forge);
        $idempotencyMigration = new AddKakWawaSendIdempotency($forge);
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
            $qualityMigration->up();
            $idempotencyMigration->up();
            $idempotencyMigration->up();
            foreach ($tables as $table) {
                $this->assertTrue($forge->getConnection()->tableExists($table), $table . ' should exist after migration.');
            }
            $this->assertTrue($forge->getConnection()->tableExists('ilena_chat_feedback'));
            $this->assertTrue($forge->getConnection()->tableExists('ilena_chat_events'));
            $knowledgeFields = $forge->getConnection()->getFieldNames('ilena_chat_knowledge');
            $this->assertContains('valid_from', $knowledgeFields);
            $this->assertContains('expires_at', $knowledgeFields);
            $this->assertContains('reviewed_at', $knowledgeFields);
            $this->assertContains('revision', $knowledgeFields);
            $fieldNames = array_map(static fn ($field) => $field->name, $forge->getConnection()->getFieldData('ilena_chat_conversations'));
            $this->assertContains('feedback_prompted_at', $fieldNames, implode(', ', $fieldNames));
            $messageFields = array_map(static fn ($field) => $field->name, $forge->getConnection()->getFieldData('ilena_chat_messages'));
            $this->assertContains('client_request_id', $messageFields, implode(', ', $messageFields));
            $this->assertArrayHasKey('uq_ilena_chat_client_request', $forge->getConnection()->getIndexData('ilena_chat_messages'));
        } finally {
            $idempotencyMigration->down();
            $qualityMigration->down();
            $feedbackMigration->down();
            $migration->down();
        }
        $this->assertFalse($forge->getConnection()->tableExists('ilena_chat_feedback'));

        foreach ($tables as $table) {
            $this->assertFalse($forge->getConnection()->tableExists($table), $table . ' should be removed on rollback.');
        }
    }
}
