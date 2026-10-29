<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Saves customer-provided contact details only when the customer requests a staff handoff. */
class AddChatHandoffContactDetails extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('ilena_chat_conversations')) return;
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

    public function down()
    {
        if ($this->db->fieldExists('customer_phone', 'ilena_chat_conversations')) {
            $this->forge->dropColumn('ilena_chat_conversations', 'customer_phone');
        }
        if ($this->db->fieldExists('customer_name', 'ilena_chat_conversations')) {
            $this->forge->dropColumn('ilena_chat_conversations', 'customer_name');
        }
    }
}
