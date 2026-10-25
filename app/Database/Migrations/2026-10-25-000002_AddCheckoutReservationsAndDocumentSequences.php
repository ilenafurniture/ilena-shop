<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddCheckoutReservationsAndDocumentSequences extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('checkout_stock_reservations')) {
            $this->forge->addField([
                'id'=>['type'=>'BIGINT','unsigned'=>true,'auto_increment'=>true],
                'order_id'=>['type'=>'VARCHAR','constraint'=>64], 'product_id'=>['type'=>'VARCHAR','constraint'=>64],
                'variant_name'=>['type'=>'VARCHAR','constraint'=>191], 'quantity'=>['type'=>'INT','unsigned'=>true],
                'status'=>['type'=>'VARCHAR','constraint'=>16,'default'=>'reserved'], 'expires_at'=>['type'=>'DATETIME'], 'created_at'=>['type'=>'DATETIME'],
            ]);
            $this->forge->addKey('id',true);
            $this->forge->addUniqueKey(['order_id','product_id','variant_name']);
            $this->forge->addKey(['product_id','variant_name','status','expires_at']);
            $this->forge->addKey(['status','expires_at']);
            $this->forge->createTable('checkout_stock_reservations',true,$this->db->DBDriver==='MySQLi'?['ENGINE'=>'InnoDB']:[]);
        }
        if (!$this->db->tableExists('document_sequences')) {
            $this->forge->addField(['sequence_key'=>['type'=>'VARCHAR','constraint'=>100], 'counter'=>['type'=>'BIGINT','unsigned'=>true,'default'=>0]]);
            $this->forge->addKey('sequence_key',true);
            $this->forge->createTable('document_sequences',true,$this->db->DBDriver==='MySQLi'?['ENGINE'=>'InnoDB']:[]);
        }
    }
    public function down()
    {
        // Financial/stock history is intentionally retained on application rollback.
    }
}
