-- Jalankan satu kali di database Ilena yang dipakai aplikasi.
-- Reservasi tidak mengurangi stok katalog; stok dikurangi pada transisi pembayaran berhasil.
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
  UNIQUE KEY `uq_checkout_reservation_item` (`order_id`, `product_id`, `variant_name`),
  KEY `idx_checkout_reservation_stock` (`product_id`, `variant_name`, `status`, `expires_at`),
  KEY `idx_checkout_reservation_expiry` (`status`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
