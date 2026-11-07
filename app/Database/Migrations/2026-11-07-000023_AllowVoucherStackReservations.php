<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowVoucherStackReservations extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ilena_voucher_reservations')) {
            if ($this->db->DBDriver === 'MySQLi') {
                $this->db->query('ALTER TABLE `ilena_voucher_reservations` DROP INDEX `uq_ilena_voucher_reservation_order`, ADD UNIQUE KEY `uq_ilena_voucher_reservation_order_voucher` (`order_id`, `voucher_id`)');
            } elseif ($this->db->DBDriver === 'SQLite3') {
                $this->db->query('DROP INDEX IF EXISTS `uq_ilena_voucher_reservation_order`');
                $this->db->query('CREATE UNIQUE INDEX `uq_ilena_voucher_reservation_order_voucher` ON `ilena_voucher_reservations` (`order_id`, `voucher_id`)');
            } else {
                throw new \RuntimeException('Database driver is not supported for voucher stack migration.');
            }
        }

        if (! $this->db->tableExists('ilena_voucher_order_locks')) {
            $options = $this->db->DBDriver === 'MySQLi' ? [
                'ENGINE' => 'InnoDB',
                'CHARSET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_general_ci',
            ] : [];
            $this->forge->addField([
                'order_id' => ['type' => 'VARCHAR', 'constraint' => 191],
            ]);
            $this->forge->addKey('order_id', true);
            $this->forge->createTable('ilena_voucher_order_locks', true, $options);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('ilena_voucher_reservations')) {
            if ($this->db->DBDriver === 'MySQLi') {
                $duplicates = $this->db->query('SELECT `order_id` FROM `ilena_voucher_reservations` GROUP BY `order_id` HAVING COUNT(*) > 1 LIMIT 1')->getRowArray();
                if ($duplicates) throw new \RuntimeException('Cannot roll back voucher stacking while an order has multiple voucher reservations.');
                $this->db->query('ALTER TABLE `ilena_voucher_reservations` DROP INDEX `uq_ilena_voucher_reservation_order_voucher`, ADD UNIQUE KEY `uq_ilena_voucher_reservation_order` (`order_id`)');
            } elseif ($this->db->DBDriver === 'SQLite3') {
                $duplicates = $this->db->query('SELECT `order_id` FROM `ilena_voucher_reservations` GROUP BY `order_id` HAVING COUNT(*) > 1 LIMIT 1')->getRowArray();
                if ($duplicates) throw new \RuntimeException('Cannot roll back voucher stacking while an order has multiple voucher reservations.');
                $this->db->query('DROP INDEX IF EXISTS `uq_ilena_voucher_reservation_order_voucher`');
                $this->db->query('CREATE UNIQUE INDEX `uq_ilena_voucher_reservation_order` ON `ilena_voucher_reservations` (`order_id`)');
            }
        }
        if ($this->db->tableExists('ilena_voucher_order_locks')) $this->forge->dropTable('ilena_voucher_order_locks', true);
    }
}
