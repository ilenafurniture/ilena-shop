<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Durable application queue; database remains the source of truth. */
class CreateCommerceJobQueue extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('commerce_jobs')) return;

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'job_key' => ['type' => 'VARCHAR', 'constraint' => 191],
            'job_type' => ['type' => 'VARCHAR', 'constraint' => 48],
            'order_id' => ['type' => 'VARCHAR', 'constraint' => 64],
            'payload' => ['type' => 'LONGTEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'queued'],
            'attempts' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'available_at' => ['type' => 'DATETIME'],
            'locked_at' => ['type' => 'DATETIME', 'null' => true],
            'completed_at' => ['type' => 'DATETIME', 'null' => true],
            'last_error' => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('job_key');
        $this->forge->addKey(['status', 'available_at', 'id']);
        $this->forge->addKey(['order_id', 'job_type']);
        $options = $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
        $this->forge->createTable('commerce_jobs', true, $options);
    }

    public function down()
    {
        // Keep financial processing records during rollback; never drop production jobs.
    }
}
