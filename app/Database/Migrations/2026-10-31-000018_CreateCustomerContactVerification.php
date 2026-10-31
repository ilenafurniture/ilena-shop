<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerContactVerification extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('account_contact_verifications')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'channel' => ['type' => 'VARCHAR', 'constraint' => 12],
                'target' => ['type' => 'VARCHAR', 'constraint' => 191],
                'code_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
                'attempts' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
                'created_at' => ['type' => 'DATETIME'],
                'expires_at' => ['type' => 'DATETIME'],
                'consumed_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['user_email', 'channel', 'consumed_at'], false, false, 'idx_contact_verify_owner');
            $this->forge->addKey(['expires_at', 'consumed_at'], false, false, 'idx_contact_verify_expiry');
            $this->forge->createTable('account_contact_verifications', true);
        }

        if (! $this->db->tableExists('customer_phone_identities')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'phone_e164' => ['type' => 'VARCHAR', 'constraint' => 20],
                'verified_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('user_email', 'uq_customer_phone_owner');
            $this->forge->addUniqueKey('phone_e164', 'uq_customer_phone_e164');
            $this->forge->createTable('customer_phone_identities', true);
        }
    }

    public function down()
    {
        // Keep verification history and verified identity mappings on rollback.
    }
}
