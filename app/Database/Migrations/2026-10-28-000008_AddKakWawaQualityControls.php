<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Adds validity/review fields for KB records and PII-free assistant telemetry. */
class AddKakWawaQualityControls extends Migration
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
        if ($this->db->tableExists('ilena_chat_knowledge')) {
            foreach ([
                'valid_from' => ['type' => 'DATE', 'null' => true],
                'expires_at' => ['type' => 'DATE', 'null' => true],
                'reviewed_at' => ['type' => 'DATETIME', 'null' => true],
                'reviewed_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'revision' => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            ] as $name => $field) {
                if (! $this->hasColumn('ilena_chat_knowledge', $name)) $this->forge->addColumn('ilena_chat_knowledge', [$name => $field]);
            }
        }

        if (! $this->db->tableExists('ilena_chat_events')) {
            $options = $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci',
            ] : [];
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'event_type' => ['type' => 'VARCHAR', 'constraint' => 16],
                'reason' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
                'duration_ms' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                'queue_delay_ms' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['created_at', 'event_type'], false, false, 'idx_ilena_chat_events_period');
            $this->forge->createTable('ilena_chat_events', true, $options);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('ilena_chat_events')) $this->forge->dropTable('ilena_chat_events', true);
        if ($this->db->tableExists('ilena_chat_knowledge')) {
            foreach (['revision', 'reviewed_by', 'reviewed_at', 'expires_at', 'valid_from'] as $column) {
                if ($this->hasColumn('ilena_chat_knowledge', $column)) $this->forge->dropColumn('ilena_chat_knowledge', $column);
            }
        }
    }
}
