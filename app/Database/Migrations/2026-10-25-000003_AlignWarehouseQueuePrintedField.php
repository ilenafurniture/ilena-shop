<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class AlignWarehouseQueuePrintedField extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('pemesanan_gudang') && !$this->db->fieldExists('printed','pemesanan_gudang')) {
            $this->forge->addColumn('pemesanan_gudang',['printed'=>['type'=>'TINYINT','constraint'=>1,'default'=>0]]);
        }
    }
    public function down() { /* Retain additive operational metadata on code rollback. */ }
}
