<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Versioned schema support for scheduled product Flash Sales. */
class AddFlashSaleScheduleToProducts extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('barang')) {
            throw new \RuntimeException('Table barang must exist before Flash Sale schedule migration.');
        }

        $fields = [];
        if (!$this->db->fieldExists('pakai_jadwal_diskon', 'barang')) {
            $fields['pakai_jadwal_diskon'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
            ];
        }
        if (!$this->db->fieldExists('diskon_mulai', 'barang')) {
            $fields['diskon_mulai'] = ['type' => 'DATETIME', 'null' => true];
        }
        if (!$this->db->fieldExists('diskon_selesai', 'barang')) {
            $fields['diskon_selesai'] = ['type' => 'DATETIME', 'null' => true];
        }

        if ($fields !== []) $this->forge->addColumn('barang', $fields);
    }

    public function down()
    {
        // These columns may already contain scheduled discounts; keep them on rollback.
    }
}
