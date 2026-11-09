<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Hashed, revocable bearer credentials for customer mobile clients only. */
class CreateCustomerApiTokens extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('customer_api_tokens')) return;

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'token_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'auth_version' => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'created_at' => ['type' => 'DATETIME'],
            'expires_at' => ['type' => 'DATETIME'],
            'last_used_at' => ['type' => 'DATETIME', 'null' => true],
            'revoked_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token_hash', 'uq_customer_api_token_hash');
        $this->forge->addKey(['user_email', 'revoked_at', 'expires_at'], false, false, 'idx_customer_api_token_user');
        $this->forge->createTable('customer_api_tokens', true, $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : []);
    }

    public function down()
    {
        // Keep revocation evidence and credentials when rolling back application code.
    }
}
