<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Prevents duplicate native-app orders when clients retry a timed-out checkout request. */
class CreateCustomerCheckoutIdempotency extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('customer_checkout_idempotency')) return;
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'key_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'request_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'order_id' => ['type' => 'VARCHAR', 'constraint' => 64],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_email', 'key_hash'], 'uq_customer_checkout_idempotency');
        $this->forge->addUniqueKey('order_id', 'uq_customer_checkout_idempotency_order');
        $this->forge->addKey('created_at', false, false, 'idx_customer_checkout_idempotency_created');
        $this->forge->createTable('customer_checkout_idempotency', true, $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : []);
    }

    public function down()
    {
        // Keep financial idempotency evidence during rollback.
    }
}
