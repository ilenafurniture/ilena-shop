<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Dev-managed OpenAI-compatible provider configuration; API secrets stay encrypted. */
class CreateHybridChatProviderSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ilena_chat_provider_settings')) return;

        $options = $this->db->DBDriver === 'MySQLi' ? [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ] : [];

        $this->forge->addField([
            'id' => ['type' => 'SMALLINT', 'unsigned' => true],
            'endpoint' => ['type' => 'VARCHAR', 'constraint' => 500],
            'model' => ['type' => 'VARCHAR', 'constraint' => 160],
            'api_key_encrypted' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'is_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ilena_chat_provider_settings', true, $options);
    }

    public function down()
    {
        $this->forge->dropTable('ilena_chat_provider_settings', true);
    }
}
