<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKakWawaResponseLocale extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ilena_chat_conversations') && ! $this->db->fieldExists('response_locale', 'ilena_chat_conversations')) {
            $this->forge->addColumn('ilena_chat_conversations', [
                'response_locale' => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'id', 'after' => 'context_product_id'],
            ]);
        }

        if ($this->db->tableExists('ilena_chat_jobs') && ! $this->db->fieldExists('response_locale', 'ilena_chat_jobs')) {
            $this->forge->addColumn('ilena_chat_jobs', [
                'response_locale' => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'id', 'after' => 'user_message_id'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('ilena_chat_jobs') && $this->db->fieldExists('response_locale', 'ilena_chat_jobs')) {
            $this->forge->dropColumn('ilena_chat_jobs', 'response_locale');
        }
        if ($this->db->tableExists('ilena_chat_conversations') && $this->db->fieldExists('response_locale', 'ilena_chat_conversations')) {
            $this->forge->dropColumn('ilena_chat_conversations', 'response_locale');
        }
    }
}
