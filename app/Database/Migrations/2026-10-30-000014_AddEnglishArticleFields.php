<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnglishArticleFields extends Migration
{
    private const FIELDS = [
        'judul_en' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
        'deskripsi_en' => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
        'keywords_en' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
        'isi_en' => ['type' => 'MEDIUMTEXT', 'null' => true],
    ];

    public function up()
    {
        if (! $this->db->tableExists('artikel')) return;

        foreach (self::FIELDS as $field => $definition) {
            if (! $this->db->fieldExists($field, 'artikel')) {
                $this->forge->addColumn('artikel', [$field => $definition]);
            }
        }
    }

    public function down()
    {
        // Keep authored translations when rolling application code back.
    }
}
