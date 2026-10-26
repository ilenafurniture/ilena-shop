<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuCollectionPreviews extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('menu_collection_previews')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'pair_key' => ['type' => 'CHAR', 'constraint' => 64],
            'category_name' => ['type' => 'VARCHAR', 'constraint' => 191],
            'subcategory_name' => ['type' => 'VARCHAR', 'constraint' => 191],
            'product_id' => ['type' => 'VARCHAR', 'constraint' => 64],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('pair_key', 'uq_menu_preview_collection_pair');
        $this->forge->addKey('category_name', false, false, 'idx_menu_preview_category');
        $this->forge->addKey('subcategory_name', false, false, 'idx_menu_preview_subcategory');
        $this->forge->addKey('product_id', false, false, 'idx_menu_preview_product');
        $this->forge->createTable('menu_collection_previews', true, [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('menu_collection_previews')) {
            $this->forge->dropTable('menu_collection_previews', true);
        }
    }
}
