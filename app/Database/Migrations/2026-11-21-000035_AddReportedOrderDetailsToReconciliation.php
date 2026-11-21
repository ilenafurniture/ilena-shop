<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Store the operator-reported snapshot separately from real orders. */
class AddReportedOrderDetailsToReconciliation extends Migration
{
    private const NUMBERS = [
        'IL00000063', 'IL00000064', 'IL00000065', 'IL00000066', 'IL00000067',
        'IL00000068', 'IL00000069', 'IL00000070', 'IL00000071', 'IL00000072',
    ];

    public function up()
    {
        if (! $this->db->tableExists('order_number_reconciliation')) {
            return;
        }

        if (! $this->db->fieldExists('reported_details_json', 'order_number_reconciliation')) {
            $this->forge->addColumn('order_number_reconciliation', [
                'reported_details_json' => ['type' => 'LONGTEXT', 'null' => true],
            ]);
        }

        $reportedDetails = [
            'source_label' => 'Data yang disampaikan pengguna; perlu dicocokkan dengan arsip sumber.',
            'recipient_name' => 'Jenny Blackett',
            'recipient_address' => 'Jl. H Batong Raya 10 Kav A CILANDAK BARAT, Cilandak, Jakarta Selatan, DKI Jakarta 12430',
            'email' => '-',
            'product_name' => 'Console Table Ilena Water Case 1600',
            'variant' => 'TEAK VENEER WHITE WASH',
            'quantity' => 1,
            'unit_price' => 2224100,
            'line_total' => 2224100,
            'reported_order_status' => 'Dibatalkan',
        ];
        $encoded = json_encode($reportedDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $table = $this->db->table('order_number_reconciliation');

        foreach (self::NUMBERS as $invoiceNumber) {
            $row = $table->select('id, reported_details_json')
                ->where('invoice_number', $invoiceNumber)
                ->get(1)
                ->getRowArray();
            if ($row && trim((string) ($row['reported_details_json'] ?? '')) === '') {
                $table->where('id', (int) $row['id'])->update([
                    'reported_details_json' => $encoded,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // Keep the supplied reconciliation snapshot available for audit.
    }
}
