<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Customer product alerts, moderated product feedback, and after-sales cases. */
class CreateStoreEngagementFeatures extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('product_interest_alerts')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'product_id' => ['type' => 'VARCHAR', 'constraint' => 64],
                'variant_name' => ['type' => 'VARCHAR', 'constraint' => 191, 'default' => ''],
                'email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'alert_type' => ['type' => 'VARCHAR', 'constraint' => 20],
                'baseline_price' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'pending'],
                'consented_at' => ['type' => 'DATETIME'],
                'confirmed_at' => ['type' => 'DATETIME', 'null' => true],
                'notified_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['email', 'product_id', 'variant_name', 'alert_type'], 'uq_product_interest_once');
            $this->forge->addKey(['product_id', 'alert_type', 'status'], false, false, 'idx_product_interest_match');
            $this->forge->addKey(['email', 'product_id', 'alert_type'], false, false, 'idx_product_interest_owner');
            $this->forge->createTable('product_interest_alerts', true);
        }

        if (! $this->db->tableExists('product_feedback')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'product_id' => ['type' => 'VARCHAR', 'constraint' => 64],
                'author_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'author_name' => ['type' => 'VARCHAR', 'constraint' => 120],
                'order_id' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'feedback_type' => ['type' => 'VARCHAR', 'constraint' => 12],
                'rating' => ['type' => 'TINYINT', 'unsigned' => true, 'null' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
                'body' => ['type' => 'TEXT'],
                'verified_purchase' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'pending'],
                'moderated_by' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'moderated_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['product_id', 'status', 'feedback_type'], false, false, 'idx_product_feedback_public');
            $this->forge->addKey(['author_email', 'product_id'], false, false, 'idx_product_feedback_owner');
            $this->forge->createTable('product_feedback', true);
        }

        if (! $this->db->tableExists('customer_order_service_cases')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'order_id' => ['type' => 'VARCHAR', 'constraint' => 191],
                'customer_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'case_type' => ['type' => 'VARCHAR', 'constraint' => 16],
                'reason' => ['type' => 'VARCHAR', 'constraint' => 80],
                'message' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'received'],
                'staff_note' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['customer_email', 'created_at'], false, false, 'idx_order_case_owner');
            $this->forge->addKey(['order_id', 'case_type', 'status'], false, false, 'idx_order_case_status');
            $this->forge->createTable('customer_order_service_cases', true);
        }
    }

    public function down()
    {
        // Preserve customer service history and opt-in records on rollback.
    }
}
