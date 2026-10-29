<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKakWawaResolutionTimestamp extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('ilena_chat_conversations')) return;

        foreach ($this->db->getFieldData('ilena_chat_conversations') as $field) {
            if (($field->name ?? null) === 'resolved_at') return;
        }

        $this->forge->addColumn('ilena_chat_conversations', [
            'resolved_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down()
    {
        if (! $this->db->tableExists('ilena_chat_conversations')) return;
        foreach ($this->db->getFieldData('ilena_chat_conversations') as $field) {
            if (($field->name ?? null) === 'resolved_at') {
                $this->forge->dropColumn('ilena_chat_conversations', 'resolved_at');
                return;
            }
        }
    }
}
