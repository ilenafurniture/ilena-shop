<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerEmailAliases extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('customer_email_aliases')) return;

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'alias_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'customer_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('alias_email', 'uq_customer_email_alias');
        $this->forge->addKey('customer_email', false, false, 'idx_customer_email_primary');
        $this->forge->createTable('customer_email_aliases', true);
    }

    public function down()
    {
        // Preserve identity aliases on rollback so historical customer data remains addressable.
    }
}
