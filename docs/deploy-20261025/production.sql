-- Ilena: companion SQL for migrations 2026-10-25-000001 through 000003.
-- Select the application's database in phpMyAdmin first. Export a backup first.
-- Run during maintenance before enabling the new code. No USE database is hardcoded.
-- Existing table definitions are not silently repaired by CREATE IF NOT EXISTS.

CREATE TABLE IF NOT EXISTS `checkout_stock_reservations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` VARCHAR(64) NOT NULL,
  `product_id` VARCHAR(64) NOT NULL,
  `variant_name` VARCHAR(191) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `status` VARCHAR(16) NOT NULL DEFAULT 'reserved',
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reservation_item` (`order_id`,`product_id`,`variant_name`),
  KEY `idx_reservation_stock` (`product_id`,`variant_name`,`status`,`expires_at`),
  KEY `idx_reservation_expiry` (`status`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `document_sequences` (
  `sequence_key` VARCHAR(100) NOT NULL,
  `counter` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`sequence_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

SET @ilena_sql = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='user' AND COLUMN_NAME='suspended_until'), 'SELECT ''suspended_until already exists'' AS migration_info', 'ALTER TABLE `user` ADD COLUMN `suspended_until` DATETIME NULL');
PREPARE ilena_stmt FROM @ilena_sql;
EXECUTE ilena_stmt;
DEALLOCATE PREPARE ilena_stmt;

SET @ilena_sql = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='user' AND COLUMN_NAME='suspension_reason'), 'SELECT ''suspension_reason already exists'' AS migration_info', 'ALTER TABLE `user` ADD COLUMN `suspension_reason` VARCHAR(500) NULL');
PREPARE ilena_stmt FROM @ilena_sql;
EXECUTE ilena_stmt;
DEALLOCATE PREPARE ilena_stmt;

SET @ilena_sql = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='user' AND COLUMN_NAME='suspended_by'), 'SELECT ''suspended_by already exists'' AS migration_info', 'ALTER TABLE `user` ADD COLUMN `suspended_by` VARCHAR(191) NULL');
PREPARE ilena_stmt FROM @ilena_sql;
EXECUTE ilena_stmt;
DEALLOCATE PREPARE ilena_stmt;

SET @ilena_sql = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='pemesanan_gudang' AND COLUMN_NAME='printed'), 'SELECT ''printed already exists'' AS migration_info', 'ALTER TABLE `pemesanan_gudang` ADD COLUMN `printed` TINYINT(1) NOT NULL DEFAULT 0');
PREPARE ilena_stmt FROM @ilena_sql;
EXECUTE ilena_stmt;
DEALLOCATE PREPARE ilena_stmt;

-- Review this result: transaction-participating tables must use InnoDB.
SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES
WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN (
 'barang','pemesanan','kartu_stok','pemesanan_gudang','pemesanan_offline',
 'pemesanan_offline_item','project_interior','project_interior_item',
 'project_interior_payment','surat_jalan','surat_jalan_item',
 'checkout_stock_reservations','document_sequences'
) ORDER BY TABLE_NAME;

SELECT TABLE_NAME,COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT
FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND (
 (TABLE_NAME='user' AND COLUMN_NAME IN ('suspended_until','suspension_reason','suspended_by'))
 OR (TABLE_NAME='pemesanan_gudang' AND COLUMN_NAME='printed')
) ORDER BY TABLE_NAME,COLUMN_NAME;

-- Do not manually insert migration history. A later `php spark migrate` checks
-- these objects and records migrations through the framework normally.
-- Application-code rollback retains these additive tables/columns and their data.
