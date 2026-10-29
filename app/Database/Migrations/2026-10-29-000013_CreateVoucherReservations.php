<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVoucherReservations extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('ilena_voucher_quota_locks')) {
            $lockOptions = $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB',
                'CHARSET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_general_ci',
            ] : [];
            $this->forge->addField([
                'voucher_id' => ['type' => 'BIGINT', 'unsigned' => true],
            ]);
            $this->forge->addKey('voucher_id', true);
            $this->forge->createTable('ilena_voucher_quota_locks', true, $lockOptions);
        }

        if ($this->db->tableExists('ilena_voucher_reservations')) {
            return;
        }

        $options = $this->db->DBDriver === 'MySQLi' ? [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ] : [];

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'order_id' => ['type' => 'VARCHAR', 'constraint' => 191],
            'voucher_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'voucher_code' => ['type' => 'VARCHAR', 'constraint' => 191],
            'email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'once_per_user' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'reserved'],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_id', 'uq_ilena_voucher_reservation_order');
        $this->forge->addKey(['voucher_id', 'status', 'expires_at'], false, false, 'idx_ilena_voucher_reservation_quota');
        $this->forge->addKey(['voucher_id', 'email', 'status', 'expires_at'], false, false, 'idx_ilena_voucher_reservation_email');
        $this->forge->createTable('ilena_voucher_reservations', true, $options);
    }

    public function down()
    {
        if ($this->db->tableExists('ilena_voucher_reservations')) {
            $this->forge->dropTable('ilena_voucher_reservations', true);
        }
        if ($this->db->tableExists('ilena_voucher_quota_locks')) {
            $this->forge->dropTable('ilena_voucher_quota_locks', true);
        }
    }
}
