<?php

namespace App\Database\Migrations;

use App\Config\MenuDescriptionDefaults;
use CodeIgniter\Database\Migration;

/** Seeds the existing menu copy into a setting so staff can maintain it. */
class SeedMenuShortDescriptions extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('app_settings')) {
            $this->forge->addField([
                'setting_key' => ['type' => 'VARCHAR', 'constraint' => 100],
                'setting_value' => ['type' => 'TEXT', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('setting_key', true);
            $options = $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci',
            ] : [];
            $this->forge->createTable('app_settings', true, $options);
        }

        $key = 'menu_short_descriptions';
        if ($this->db->table('app_settings')->where('setting_key', $key)->countAllResults() > 0) return;
        $value = json_encode(['entries' => MenuDescriptionDefaults::entries()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->db->table('app_settings')->insert([
            'setting_key' => $key,
            'setting_value' => $value,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        // Keep operator-managed copy; rollback never deletes live settings.
    }
}
