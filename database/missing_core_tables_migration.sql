USE `bangiay2`;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `province` VARCHAR(100) NOT NULL,
  `district` VARCHAR(100) NOT NULL,
  `ward` VARCHAR(100) NOT NULL,
  `address_line` VARCHAR(255) NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_address_user` (`user_id`),
  CONSTRAINT `fk_address_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NULL,
  `discount_type` ENUM('fixed','percent') NOT NULL,
  `discount_value` DECIMAL(12,2) NOT NULL,
  `min_order_value` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `max_discount` DECIMAL(12,2) NULL,
  `usage_limit` INT UNSIGNED NULL,
  `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `starts_at` DATETIME NULL,
  `ends_at` DATETIME NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_voucher_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `vouchers`
  (`code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `usage_limit`, `used_count`, `starts_at`, `ends_at`, `status`)
VALUES
  ('KEUMEOMEODI', 'Giảm 10% tối đa 150.000đ cho đơn từ 500.000đ', 'percent', 10.00, 500000.00, 150000.00, 100, 0, '2026-01-01 00:00:00', '2027-12-31 00:00:00', 'active'),
  ('GIAM50K', 'Giảm trực tiếp 50.000đ cho đơn từ 300.000đ', 'fixed', 50000.00, 300000.00, NULL, 50, 0, '2026-01-01 00:00:00', '2027-12-31 00:00:00', 'active'),
  ('VIP200K', 'Giảm 200.000đ cho đơn hàng từ 2.000.000đ', 'fixed', 200000.00, 2000000.00, NULL, 30, 0, '2026-01-01 00:00:00', '2027-12-31 00:00:00', 'active');
