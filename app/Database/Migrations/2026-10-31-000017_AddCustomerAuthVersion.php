<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerAuthVersion extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('user') && ! $this->db->fieldExists('auth_version', 'user')) {
            $this->forge->addColumn('user', [
                'auth_version' => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            ]);
        }
    }

    public function down()
    {
        // Keep the version column on rollback to avoid reviving stale sessions.
    }
}
