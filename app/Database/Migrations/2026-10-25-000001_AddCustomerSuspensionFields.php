<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerSuspensionFields extends Migration
{
    public function up()
    {
        $columns = [];
        if (!$this->db->fieldExists('suspended_until', 'user')) {
            $columns['suspended_until'] = ['type' => 'DATETIME', 'null' => true];
        }
        if (!$this->db->fieldExists('suspension_reason', 'user')) {
            $columns['suspension_reason'] = ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true];
        }
        if (!$this->db->fieldExists('suspended_by', 'user')) {
            $columns['suspended_by'] = ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true];
        }

        if ($columns !== []) {
            $this->forge->addColumn('user', $columns);
        }
    }

    public function down()
    {
        foreach (['suspended_by', 'suspension_reason', 'suspended_until'] as $column) {
            if ($this->db->fieldExists($column, 'user')) {
                $this->forge->dropColumn('user', $column);
            }
        }
    }
}
