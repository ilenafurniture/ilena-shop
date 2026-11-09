<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Durable, deduplicated refresh requests for stale public caches. */
class CreateCacheRefreshQueue extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('cache_refresh_jobs')) return;

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'cache_name' => ['type' => 'VARCHAR', 'constraint' => 64],
            'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'queued'],
            'attempts' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'available_at' => ['type' => 'DATETIME'],
            'locked_at' => ['type' => 'DATETIME', 'null' => true],
            'last_error' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('cache_name');
        $this->forge->addKey(['status', 'available_at', 'id']);
        $this->forge->addKey(['status', 'locked_at']);
        $options = $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
        $this->forge->createTable('cache_refresh_jobs', true, $options);
    }

    public function down()
    {
        $this->forge->dropTable('cache_refresh_jobs', true);
    }
}
