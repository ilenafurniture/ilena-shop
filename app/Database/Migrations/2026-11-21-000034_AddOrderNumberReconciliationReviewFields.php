<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Add auditable evidence-review fields to the non-transactional gap register. */
class AddOrderNumberReconciliationReviewFields extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('order_number_reconciliation')) {
            return;
        }

        if (! $this->db->fieldExists('evidence_type', 'order_number_reconciliation')) {
            $this->forge->addColumn('order_number_reconciliation', [
                'evidence_type' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            ]);
        }
        if (! $this->db->fieldExists('evidence_reference', 'order_number_reconciliation')) {
            $this->forge->addColumn('order_number_reconciliation', [
                'evidence_reference' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            ]);
        }
        if (! $this->db->fieldExists('reviewed_by', 'order_number_reconciliation')) {
            $this->forge->addColumn('order_number_reconciliation', [
                'reviewed_by' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            ]);
        }
        if (! $this->db->fieldExists('reviewed_at', 'order_number_reconciliation')) {
            $this->forge->addColumn('order_number_reconciliation', [
                'reviewed_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
        }
    }

    public function down()
    {
        // Preserve evidence review history on rollback.
    }
}
