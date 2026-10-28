<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Makes retried visitor chat sends idempotent after a network timeout. */
class AddKakWawaSendIdempotency extends Migration
{
    private const INDEX_NAME = 'uq_ilena_chat_client_request';

    private function hasColumn(string $table, string $column): bool
    {
        foreach ($this->db->getFieldData($table) as $field) {
            if (($field->name ?? null) === $column) return true;
        }

        return false;
    }

    public function up()
    {
        if (! $this->db->tableExists('ilena_chat_messages')) return;

        if (! $this->hasColumn('ilena_chat_messages', 'client_request_id')) {
            $this->forge->addColumn('ilena_chat_messages', [
                'client_request_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            ]);
        }

        $indexes = $this->db->getIndexData('ilena_chat_messages');
        if (! array_key_exists(self::INDEX_NAME, $indexes)) {
            $this->forge->addUniqueKey('client_request_id', self::INDEX_NAME);
            $this->forge->processIndexes('ilena_chat_messages');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('ilena_chat_messages')) return;

        if (array_key_exists(self::INDEX_NAME, $this->db->getIndexData('ilena_chat_messages'))) {
            // Explicit key names are stored without DBPrefix by Forge.
            $this->forge->dropKey('ilena_chat_messages', self::INDEX_NAME, false);
        }
        if ($this->hasColumn('ilena_chat_messages', 'client_request_id')) {
            $this->forge->dropColumn('ilena_chat_messages', 'client_request_id');
        }
    }
}
