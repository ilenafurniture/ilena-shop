<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerRegistrationMetadata extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('user')) {
            throw new \RuntimeException('Customer account table is missing.');
        }

        $columns = array_map('strtolower', $this->db->getFieldNames('user'));
        $fields = [];
        if (! in_array('created_at', $columns, true)) {
            $fields['created_at'] = ['type' => 'DATETIME', 'null' => true];
        }
        if (! in_array('registration_method', $columns, true)) {
            $fields['registration_method'] = ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true];
        }

        if ($fields !== []) {
            $this->forge->addColumn('user', $fields);
        }
    }

    public function down()
    {
        // Preserve registration metadata if the application is rolled back.
    }
}
