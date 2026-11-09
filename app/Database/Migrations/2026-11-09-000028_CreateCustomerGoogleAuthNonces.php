<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Short-lived, single-use Google sign-in challenges for customer API clients. */
class CreateCustomerGoogleAuthNonces extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('customer_google_auth_nonces')) return;

        $this->forge->addField([
            'nonce_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'created_at' => ['type' => 'DATETIME'],
            'expires_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('nonce_hash', true);
        $this->forge->addKey('expires_at', false, false, 'idx_customer_google_nonce_expiry');
        $this->forge->createTable('customer_google_auth_nonces', true, $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : []);
    }

    public function down()
    {
        // Keep short-lived authentication evidence; expired rows are pruned on issuance.
    }
}
