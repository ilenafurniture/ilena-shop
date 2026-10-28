<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Persistence foundation for the Ilena AI + human support inbox.
 * No foreign keys are used because legacy product/user key types vary between
 * the existing production tables; references are validated by the services.
 */
class CreateHybridChatFoundation extends Migration
{
    public function up()
    {
        $options = $this->db->DBDriver === 'MySQLi' ? [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ] : [];

        if (! $this->db->tableExists('ilena_chat_conversations')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'conversation_key' => ['type' => 'CHAR', 'constraint' => 64],
                'visitor_hash' => ['type' => 'CHAR', 'constraint' => 64],
                'customer_email' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'ai'],
                'assigned_to' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'context_product_id' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
                'last_message_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME'],
                'closed_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('conversation_key', 'uq_ilena_chat_key');
            $this->forge->addKey(['visitor_hash', 'status', 'last_message_at'], false, false, 'idx_ilena_chat_visitor');
            $this->forge->addKey(['status', 'last_message_at'], false, false, 'idx_ilena_chat_queue');
            $this->forge->addKey(['assigned_to', 'status'], false, false, 'idx_ilena_chat_assignment');
            $this->forge->createTable('ilena_chat_conversations', true, $options);
        }

        if (! $this->db->tableExists('ilena_chat_messages')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'conversation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'sender_type' => ['type' => 'VARCHAR', 'constraint' => 16],
                'sender_id' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'body' => ['type' => 'TEXT'],
                'metadata' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['conversation_id', 'id'], false, false, 'idx_ilena_chat_messages_cursor');
            $this->forge->createTable('ilena_chat_messages', true, $options);
        }

        if (! $this->db->tableExists('ilena_chat_jobs')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'conversation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'user_message_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'queued'],
                'attempts' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
                'available_at' => ['type' => 'DATETIME'],
                'locked_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('user_message_id', 'uq_ilena_chat_job_message');
            $this->forge->addKey(['status', 'available_at'], false, false, 'idx_ilena_chat_jobs_queue');
            $this->forge->createTable('ilena_chat_jobs', true, $options);
        }

        if (! $this->db->tableExists('ilena_chat_knowledge')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 191],
                'content' => ['type' => 'MEDIUMTEXT'],
                'source_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'updated_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['is_published', 'updated_at'], false, false, 'idx_ilena_chat_knowledge_published');
            $this->forge->createTable('ilena_chat_knowledge', true, $options);
        }

        if (! $this->db->tableExists('ilena_chat_product_specs')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'product_id' => ['type' => 'VARCHAR', 'constraint' => 64],
                'colors' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'materials' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'dimensions' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keywords' => ['type' => 'TEXT', 'null' => true],
                'updated_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'updated_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('product_id', 'uq_ilena_chat_product_specs');
            $this->forge->createTable('ilena_chat_product_specs', true, $options);
        }
    }

    public function down()
    {
        foreach ([
            'ilena_chat_product_specs',
            'ilena_chat_knowledge',
            'ilena_chat_jobs',
            'ilena_chat_messages',
            'ilena_chat_conversations',
        ] as $table) {
            if ($this->db->tableExists($table)) {
                $this->forge->dropTable($table, true);
            }
        }
    }
}
