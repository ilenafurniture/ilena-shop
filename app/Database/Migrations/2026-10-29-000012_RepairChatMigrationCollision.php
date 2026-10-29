<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Repairs both idempotent schema changes that shared migration version 000010. */
class RepairChatMigrationCollision extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ilena_chat_conversations')) {
            if (! $this->db->fieldExists('customer_name', 'ilena_chat_conversations')) {
                $this->forge->addColumn('ilena_chat_conversations', [
                    'customer_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'customer_email'],
                ]);
            }
            if (! $this->db->fieldExists('customer_phone', 'ilena_chat_conversations')) {
                $this->forge->addColumn('ilena_chat_conversations', [
                    'customer_phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'customer_name'],
                ]);
            }
        }

        if (! $this->db->tableExists('ilena_chat_reads')) {
            $options = $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB',
                'CHARSET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_general_ci',
            ] : [];
            $this->forge->addField([
                'conversation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'staff_id' => ['type' => 'VARCHAR', 'constraint' => 191],
                'last_read_message_id' => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey(['conversation_id', 'staff_id'], true, false, 'pk_ilena_chat_reads');
            $this->forge->addKey(['staff_id', 'updated_at'], false, false, 'idx_ilena_chat_reads_staff');
            $this->forge->createTable('ilena_chat_reads', true, $options);
        }
    }

    public function down()
    {
        // Intentionally non-destructive: both repaired structures may contain live data.
    }
}
