<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductFeedbackNamePrivacy extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('product_feedback') && ! $this->db->fieldExists('author_is_anonymous', 'product_feedback')) {
            $this->forge->addColumn('product_feedback', [
                'author_is_anonymous' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => true,
                    'default'    => 0,
                    'after'      => 'author_name',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('product_feedback') && $this->db->fieldExists('author_is_anonymous', 'product_feedback')) {
            $this->forge->dropColumn('product_feedback', 'author_is_anonymous');
        }
    }
}
