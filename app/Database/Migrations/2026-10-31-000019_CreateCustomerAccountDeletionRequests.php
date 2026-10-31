<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerAccountDeletionRequests extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('customer_account_deletion_requests')) return;

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'pending'],
            'requested_at' => ['type' => 'DATETIME'],
            'reviewed_at' => ['type' => 'DATETIME', 'null' => true],
            'reviewed_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'cancelled_at' => ['type' => 'DATETIME', 'null' => true],
            'handled_at' => ['type' => 'DATETIME', 'null' => true],
            'handled_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'resolution_note' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_email', 'status'], false, false, 'idx_account_deletion_owner_status');
        $this->forge->createTable('customer_account_deletion_requests', true);
    }

    public function down()
    {
        // Keep account deletion audit requests on rollback.
    }
}
