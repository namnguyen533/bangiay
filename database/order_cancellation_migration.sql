USE `bangiay2`;

SET @cancellation_reason_exists = (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'orders'
      AND column_name = 'cancellation_reason'
);

SET @cancellation_reason_sql = IF(
    @cancellation_reason_exists = 0,
    'ALTER TABLE `orders` ADD COLUMN `cancellation_reason` VARCHAR(500) NULL AFTER `note`',
    'SELECT 1'
);

PREPARE add_cancellation_reason FROM @cancellation_reason_sql;
EXECUTE add_cancellation_reason;
DEALLOCATE PREPARE add_cancellation_reason;

ALTER TABLE `orders`
  MODIFY COLUMN `order_status` ENUM(
    'pending','confirmed','processing','shipping','completed','cancelled','returned','return_requested'
  ) NOT NULL DEFAULT 'pending';

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='cancel_requested_at')=0,
    'ALTER TABLE `orders` ADD COLUMN `cancel_requested_at` DATETIME NULL AFTER `cancellation_reason`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='cancellation_status')=0,
    "ALTER TABLE `orders` ADD COLUMN `cancellation_status` ENUM('none','pending','approved','rejected') NOT NULL DEFAULT 'none' AFTER `cancel_requested_at`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='cancellation_rejection_reason')=0,
    'ALTER TABLE `orders` ADD COLUMN `cancellation_rejection_reason` VARCHAR(500) NULL AFTER `cancellation_status`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='return_status')=0,
    "ALTER TABLE `orders` ADD COLUMN `return_status` ENUM('none','pending','approved','rejected','received') NOT NULL DEFAULT 'none' AFTER `cancellation_rejection_reason`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='return_reason')=0,
    'ALTER TABLE `orders` ADD COLUMN `return_reason` VARCHAR(500) NULL AFTER `return_status`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='return_images')=0,
    'ALTER TABLE `orders` ADD COLUMN `return_images` TEXT NULL AFTER `return_reason`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='return_rejection_reason')=0,
    'ALTER TABLE `orders` ADD COLUMN `return_rejection_reason` VARCHAR(500) NULL AFTER `return_images`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='completed_at')=0,
    'ALTER TABLE `orders` ADD COLUMN `completed_at` DATETIME NULL AFTER `return_rejection_reason`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='return_received_at')=0,
    'ALTER TABLE `orders` ADD COLUMN `return_received_at` DATETIME NULL AFTER `completed_at`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
