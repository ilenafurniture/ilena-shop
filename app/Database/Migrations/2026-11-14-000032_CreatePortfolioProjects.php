<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Editorial portfolio entries managed by authorized content administrators. */
class CreatePortfolioProjects extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('portfolio_projects')) return;

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 180],
            'title_id' => ['type' => 'VARCHAR', 'constraint' => 180],
            'title_en' => ['type' => 'VARCHAR', 'constraint' => 180, 'default' => ''],
            'category_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => ''],
            'category_en' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => ''],
            'summary_id' => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'summary_en' => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'description_id' => ['type' => 'TEXT', 'null' => true],
            'description_en' => ['type' => 'TEXT', 'null' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => 180, 'default' => ''],
            'completed_year' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'cover_image' => ['type' => 'VARCHAR', 'constraint' => 2048, 'default' => ''],
            'gallery_json' => ['type' => 'TEXT', 'null' => true],
            'video_url' => ['type' => 'VARCHAR', 'constraint' => 2048, 'default' => ''],
            'featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug', 'uq_portfolio_projects_slug');
        $this->forge->addKey(['active', 'sort_order', 'id'], false, false, 'idx_portfolio_public_order');
        $this->forge->createTable('portfolio_projects', true, $this->db->DBDriver === 'MySQLi' ? [
            'ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci',
        ] : []);

        if ($this->db->tableExists('app_settings')) {
            $key = \App\Services\PortfolioService::PAGE_SETTING_KEY;
            $exists = $this->db->table('app_settings')->where('setting_key', $key)->countAllResults() > 0;
            if (!$exists) {
                $this->db->table('app_settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => '0',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // Portfolio content is editorial data; deployments never drop it on rollback.
    }
}
