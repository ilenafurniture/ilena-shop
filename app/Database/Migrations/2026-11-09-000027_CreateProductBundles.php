<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Fixed furniture sets reference ordinary catalog products and variants. */
class CreateProductBundles extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('product_bundles')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'slug' => ['type' => 'VARCHAR', 'constraint' => 100],
                'name' => ['type' => 'VARCHAR', 'constraint' => 180],
                'name_en' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
                'description' => ['type' => 'TEXT', 'null' => true],
                'description_en' => ['type' => 'TEXT', 'null' => true],
                'discount_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
                'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'starts_at' => ['type' => 'DATETIME', 'null' => true],
                'ends_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug', 'uq_product_bundles_slug');
            $this->forge->addKey(['active', 'starts_at', 'ends_at'], false, false, 'idx_product_bundles_live_window');
            $this->forge->createTable('product_bundles', true, $this->options());
        }

        if (!$this->db->tableExists('product_bundle_items')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'bundle_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'product_id' => ['type' => 'VARCHAR', 'constraint' => 64],
                'variant_name' => ['type' => 'VARCHAR', 'constraint' => 100],
                'quantity' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 1],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['bundle_id', 'product_id', 'variant_name'], 'uq_product_bundle_component');
            $this->forge->addKey(['bundle_id', 'id'], false, false, 'idx_product_bundle_items_order');
            $this->forge->createTable('product_bundle_items', true, $this->options());
        }
    }

    public function down()
    {
        // Campaigns can be referenced by order history; rollback preserves them.
    }

    private function options(): array
    {
        return $this->db->DBDriver === 'MySQLi' ? [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ] : [];
    }
}
