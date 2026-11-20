<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Track missing historical order numbers without inventing customer transactions. */
class CreateOrderNumberReconciliation extends Migration
{
    private const MISSING_NUMBERS = [
        'IL00000063', 'IL00000064', 'IL00000065', 'IL00000066', 'IL00000067',
        'IL00000068', 'IL00000069', 'IL00000070', 'IL00000071', 'IL00000072',
    ];

    public function up()
    {
        if (! $this->db->tableExists('order_number_reconciliation')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'invoice_number' => ['type' => 'CHAR', 'constraint' => 10],
                'status' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'unverified_gap'],
                'note' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'Nomor tidak ditemukan pada data pesanan; menunggu rekonsiliasi.'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('invoice_number', 'uq_order_number_reconciliation_invoice');
            $this->forge->addKey(['status', 'invoice_number'], false, false, 'idx_order_number_reconciliation_status');
            $this->forge->createTable('order_number_reconciliation', true, $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci',
            ] : []);
        }

        $table = $this->db->table('order_number_reconciliation');
        $now = date('Y-m-d H:i:s');
        foreach (self::MISSING_NUMBERS as $invoiceNumber) {
            if ($table->where('invoice_number', $invoiceNumber)->countAllResults() > 0) {
                continue;
            }

            $table->insert([
                'invoice_number' => $invoiceNumber,
                'status' => 'unverified_gap',
                'note' => 'Nomor tidak ditemukan pada data pesanan; menunggu rekonsiliasi.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        // Preserve reconciliation history on rollback.
    }
}
