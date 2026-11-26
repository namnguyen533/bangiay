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
