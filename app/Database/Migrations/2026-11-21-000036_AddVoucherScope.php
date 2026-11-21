<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVoucherScope extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('voucher') && ! $this->db->fieldExists('cakupan', 'voucher')) {
            $this->forge->addColumn('voucher', [
                'cakupan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 16,
                    'default' => 'produk',
                    'null' => false,
                    'after' => 'deskripsi',
                ],
            ]);
        }
    }

    public function down()
    {
        // Keep the column when rolling back application code; existing voucher rows
        // remain readable by older releases and avoid destructive data loss.
    }
}
