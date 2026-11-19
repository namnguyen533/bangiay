USE `bangiay2`;

CREATE TABLE IF NOT EXISTS `stock_receipts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `receipt_code` VARCHAR(50) NOT NULL UNIQUE,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `supplier` VARCHAR(150) NULL,
  `note` TEXT NULL,
  `received_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_stock_receipt_user`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_receipt_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `receipt_id` BIGINT UNSIGNED NOT NULL,
  `variant_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `unit_cost` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_stock_item_receipt`
    FOREIGN KEY (`receipt_id`) REFERENCES `stock_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_item_variant`
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE RESTRICT,
  INDEX `idx_stock_item_variant` (`variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `status` ENUM('pending','visible','hidden') NOT NULL DEFAULT 'pending',
  `admin_response` TEXT NULL,
  `responded_by` BIGINT UNSIGNED NULL,
  `responded_at` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_review_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_responder`
    FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_review_rating` CHECK (`rating` BETWEEN 1 AND 5),
  UNIQUE KEY `uq_product_review_user` (`product_id`, `user_id`),
  INDEX `idx_review_status_created` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
