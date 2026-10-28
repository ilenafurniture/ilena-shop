<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Persist one Kak Wawa rating per owned support conversation. */
class AddKakWawaFeedback extends Migration
{
    private function hasColumn(string $table, string $column): bool
    {
        foreach ($this->db->getFieldData($table) as $field) {
            if (($field->name ?? null) === $column) return true;
        }
        return false;
    }

    public function up()
    {
        if ($this->db->tableExists('ilena_chat_conversations')
            && ! $this->hasColumn('ilena_chat_conversations', 'feedback_prompted_at')
        ) {
            $this->forge->addColumn('ilena_chat_conversations', [
                'feedback_prompted_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
        }

        if ($this->db->tableExists('ilena_chat_conversations') && ! $this->db->tableExists('ilena_chat_feedback')) {
            $options = $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB',
                'CHARSET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_general_ci',
            ] : [];

            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'conversation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'visitor_hash' => ['type' => 'CHAR', 'constraint' => 64],
                'rating' => ['type' => 'TINYINT', 'unsigned' => true],
                'rated_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('conversation_id', 'uq_ilena_chat_feedback_conversation');
            $this->forge->addKey(['rated_at', 'rating'], false, false, 'idx_ilena_chat_feedback_rated');
            $this->forge->createTable('ilena_chat_feedback', true, $options);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('ilena_chat_feedback')) {
            $this->forge->dropTable('ilena_chat_feedback', true);
        }
        if ($this->db->tableExists('ilena_chat_conversations')
            && $this->hasColumn('ilena_chat_conversations', 'feedback_prompted_at')
        ) {
            $this->forge->dropColumn('ilena_chat_conversations', 'feedback_prompted_at');
        }
    }
}
