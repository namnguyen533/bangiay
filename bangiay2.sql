-- ==============================================================
-- Khởi tạo Database bangiay2 cho SoleVibe Sneaker Store
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `bangiay2` 
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bangiay2`;

-- 1. Bảng ROLES
CREATE TABLE IF NOT EXISTS `roles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng USERS
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(190) NOT NULL UNIQUE,
  `phone` VARCHAR(30) NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `status` ENUM('active','blocked') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng USER_ROLES
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng USER_ADDRESSES
CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `province` VARCHAR(100) NOT NULL,
  `district` VARCHAR(100) NOT NULL,
  `ward` VARCHAR(100) NOT NULL,
  `address_line` VARCHAR(255) NOT NULL,
  `is_default` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_address_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng BRANDS
CREATE TABLE IF NOT EXISTS `brands` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL UNIQUE,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng CATEGORIES
CREATE TABLE IF NOT EXISTS `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng PRODUCTS
CREATE TABLE IF NOT EXISTS `products` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `brand_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL,
  INDEX `idx_products_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bảng PRODUCT_CATEGORIES
CREATE TABLE IF NOT EXISTS `product_categories` (
  `product_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`, `category_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Bảng PRODUCT_VARIANTS
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `sku` VARCHAR(80) NOT NULL UNIQUE,
  `size` VARCHAR(20) NOT NULL,
  `color` VARCHAR(50) NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `sale_price` DECIMAL(12,2) NULL,
  `stock_quantity` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_variant_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Bảng PRODUCT_IMAGES
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `alt_text` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_primary` BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Bảng CARTS
CREATE TABLE IF NOT EXISTS `carts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `session_token` VARCHAR(128) NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Bảng CART_ITEMS
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id` BIGINT UNSIGNED NOT NULL,
  `variant_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  UNIQUE KEY `uq_cart_variant` (`cart_id`, `variant_id`),
  FOREIGN KEY (`cart_id`) REFERENCES `carts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variant_id`) REFERENCES `product_variants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Bảng VOUCHERS
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('fixed','percent') NOT NULL,
  `discount_value` DECIMAL(12,2) NOT NULL,
  `min_order_value` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `max_discount` DECIMAL(12,2) NULL,
  `usage_limit` INT UNSIGNED NULL,
  `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `starts_at` DATETIME NULL,
  `ends_at` DATETIME NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Bảng ORDERS
CREATE TABLE IF NOT EXISTS `orders` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(40) NOT NULL UNIQUE,
  `user_id` BIGINT UNSIGNED NULL,
  `voucher_id` BIGINT UNSIGNED NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `recipient_phone` VARCHAR(30) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `shipping_fee` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `payment_method` VARCHAR(40) NOT NULL,
  `payment_status` ENUM('unpaid','pending','paid','failed','refunded','partially_refunded') NOT NULL DEFAULT 'unpaid',
  `order_status` ENUM('pending','confirmed','processing','shipping','completed','cancelled','returned') NOT NULL DEFAULT 'pending',
  `note` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`voucher_id`) REFERENCES `vouchers`(`id`) ON DELETE SET NULL,
  INDEX `idx_orders_user` (`user_id`),
  INDEX `idx_orders_status` (`order_status`),
  INDEX `idx_orders_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Bảng ORDER_ITEMS
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `variant_id` BIGINT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `sku` VARCHAR(80) NOT NULL,
  `size` VARCHAR(20) NOT NULL,
  `color` VARCHAR(50) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `line_total` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variant_id`) REFERENCES `product_variants`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Bảng SETTINGS
CREATE TABLE IF NOT EXISTS `settings` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(120) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- DỮ LIỆU MẪU (SEED DATA)
-- ==============================================================

-- 1. Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Super Admin', 'Toàn quyền quản trị hệ thống'),
(2, 'Admin', 'Quản trị viên cửa hàng'),
(3, 'Staff', 'Nhân viên vận hành'),
(4, 'Customer', 'Khách hàng mua hàng')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 2. Users (Mật khẩu mặc định: 123456)
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `status`) VALUES
(1, 'Quản Trị Viên', 'admin@solemate.vn', '0988888888', '$2y$10$UwipFup9myQFZT7q6iMTze0go5L/2kk//S36T/RoXfu.wkOYGTmGm', 'active'),
(2, 'Nguyễn Văn Phong', 'phong@gmail.com', '0977777777', '$2y$10$UwipFup9myQFZT7q6iMTze0go5L/2kk//S36T/RoXfu.wkOYGTmGm', 'active')
ON DUPLICATE KEY UPDATE `email`=VALUES(`email`);

-- 3. Phân quyền User Roles
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 2), -- admin@solemate.vn -> Admin
(2, 4)  -- phong@gmail.com -> Customer
ON DUPLICATE KEY UPDATE `role_id`=VALUES(`role_id`);

-- 4. Brands
INSERT INTO `brands` (`id`, `name`, `slug`, `status`) VALUES
(1, 'Nike', 'nike', 'active'),
(2, 'Adidas', 'adidas', 'active'),
(3, 'Puma', 'puma', 'active'),
(4, 'Converse', 'converse', 'active'),
(5, 'Vans', 'vans', 'active'),
(6, 'New Balance', 'new-balance', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 5. Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `status`) VALUES
(1, 'Sneakers', 'sneakers', 'active'),
(2, 'Giày Chạy Bộ (Running)', 'running', 'active'),
(3, 'Giày Bóng Rổ (Basketball)', 'basketball', 'active'),
(4, 'Giày Thể Thao Thời Trang', 'lifestyle', 'active'),
(5, 'Giày Cổ Cao (High-Top)', 'high-top', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 6. Products
INSERT INTO `products` (`id`, `brand_id`, `name`, `slug`, `description`, `status`) VALUES
(1, 1, 'Nike Air Force 1 \'07 Triple White', 'nike-air-force-1-07-triple-white', 'Mẫu sneaker kinh điển mọi thời đại với chất liệu da cao cấp và đệm Nike Air êm ái cả ngày dài.', 'active'),
(2, 2, 'Adidas Ultraboost Light Core Black', 'adidas-ultraboost-light-core-black', 'Đôi giày chạy bộ cao cấp thế hệ mới, nhẹ hơn 30% với công nghệ đệm hạt Boost hoàn trả năng lượng tối đa.', 'active'),
(3, 1, 'Nike Air Jordan 1 Low Chicago', 'nike-air-jordan-1-low-chicago', 'Phối màu Chicago huyền thoại mang tính biểu tượng văn hóa đường phố và bóng rổ thế giới.', 'active'),
(4, 4, 'Converse Chuck 70 Vintage Canvas', 'converse-chuck-70-vintage-canvas', 'Phiên bản nâng cấp của Chuck Taylor cổ điển với vải canvas 12oz dày dặn và đế cao su vintage bóng nhẹ.', 'active'),
(5, 5, 'Vans Old Skool Classic Black/White', 'vans-old-skool-classic-black-white', 'Đôi giày trượt ván kinh điển với đường kẻ sọc jazz side stripe huyền thoại, bền bỉ và cá tính.', 'active'),
(6, 6, 'New Balance 550 White Grey', 'new-balance-550-white-grey', 'Kiểu dáng retro thập niên 80-90 gây sốt toàn cầu với thiết kế thanh lịch, dễ phối mọi trang phục.', 'active'),
(7, 3, 'Puma Suede Classic XXI', 'puma-suede-classic-xxi', 'Da lộn mềm mại sang trọng, thiết kế low-profile thể hiện trọn vẹn chất hip-hop và b-boy style.', 'active'),
(8, 1, 'Nike Pegasus 40 Road Running', 'nike-pegasus-40-road-running', 'Dòng giày chạy biểu tượng 40 năm của Nike với sự êm ái, bám đường và hỗ trợ nhịp chạy bền bỉ.', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 7. Product Categories
INSERT INTO `product_categories` (`product_id`, `category_id`) VALUES
(1, 1), (1, 4),
(2, 2),
(3, 1), (3, 3), (3, 4),
(4, 1), (4, 4),
(5, 1), (5, 4),
(6, 1), (6, 4),
(7, 1), (7, 4),
(8, 2)
ON DUPLICATE KEY UPDATE `category_id`=VALUES(`category_id`);

-- 8. Product Images
INSERT INTO `product_images` (`product_id`, `image_url`, `alt_text`, `sort_order`, `is_primary`) VALUES
(1, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&auto=format&fit=crop&q=80', 'Nike Air Force 1 07', 1, 1),
(2, 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600&auto=format&fit=crop&q=80', 'Adidas Ultraboost Light', 1, 1),
(3, 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600&auto=format&fit=crop&q=80', 'Nike Air Jordan 1 Low', 1, 1),
(4, 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&auto=format&fit=crop&q=80', 'Converse Chuck 70', 1, 1),
(5, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&auto=format&fit=crop&q=80', 'Vans Old Skool', 1, 1),
(6, 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&auto=format&fit=crop&q=80', 'New Balance 550', 1, 1),
(7, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80', 'Puma Suede Classic', 1, 1),
(8, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80', 'Nike Pegasus 40', 1, 1);

-- 9. Product Variants (Biến thể Size & Màu sắc & Tồn kho)
INSERT INTO `product_variants` (`product_id`, `sku`, `size`, `color`, `price`, `sale_price`, `stock_quantity`, `status`) VALUES
-- AF1
(1, 'AF1-WHT-39', '39', 'Trắng', 2900000, 2690000, 20, 'active'),
(1, 'AF1-WHT-40', '40', 'Trắng', 2900000, 2690000, 25, 'active'),
(1, 'AF1-WHT-41', '41', 'Trắng', 2900000, 2690000, 30, 'active'),
(1, 'AF1-WHT-42', '42', 'Trắng', 2900000, 2690000, 15, 'active'),
-- Ultraboost
(2, 'UB-BLK-40', '40', 'Đen', 4500000, 3990000, 10, 'active'),
(2, 'UB-BLK-41', '41', 'Đen', 4500000, 3990000, 18, 'active'),
(2, 'UB-BLK-42', '42', 'Đen', 4500000, 3990000, 12, 'active'),
(2, 'UB-BLK-43', '43', 'Đen', 4500000, 3990000, 8, 'active'),
-- Jordan 1
(3, 'AJ1-CHI-40', '40', 'Đỏ Trắng Đen', 3800000, 3490000, 15, 'active'),
(3, 'AJ1-CHI-41', '41', 'Đỏ Trắng Đen', 3800000, 3490000, 22, 'active'),
(3, 'AJ1-CHI-42', '42', 'Đỏ Trắng Đen', 3800000, 3490000, 17, 'active'),
-- Chuck 70
(4, 'C70-BLK-38', '38', 'Đen', 2100000, 1850000, 25, 'active'),
(4, 'C70-BLK-39', '39', 'Đen', 2100000, 1850000, 30, 'active'),
(4, 'C70-BLK-40', '40', 'Đen', 2100000, 1850000, 20, 'active'),
(4, 'C70-BLK-41', '41', 'Đen', 2100000, 1850000, 15, 'active'),
-- Vans Old Skool
(5, 'VOS-BLK-39', '39', 'Đen Trắng', 1950000, 1750000, 25, 'active'),
(5, 'VOS-BLK-40', '40', 'Đen Trắng', 1950000, 1750000, 35, 'active'),
(5, 'VOS-BLK-41', '41', 'Đen Trắng', 1950000, 1750000, 28, 'active'),
(5, 'VOS-BLK-42', '42', 'Đen Trắng', 1950000, 1750000, 18, 'active'),
-- NB 550
(6, 'NB550-GRY-40', '40', 'Trắng Xám', 3200000, 2950000, 12, 'active'),
(6, 'NB550-GRY-41', '41', 'Trắng Xám', 3200000, 2950000, 16, 'active'),
(6, 'NB550-GRY-42', '42', 'Trắng Xám', 3200000, 2950000, 14, 'active'),
-- Puma Suede
(7, 'PMS-BLK-40', '40', 'Đen', 2200000, 1890000, 10, 'active'),
(7, 'PMS-BLK-41', '41', 'Đen', 2200000, 1890000, 15, 'active'),
(7, 'PMS-BLK-42', '42', 'Đen', 2200000, 1890000, 12, 'active'),
-- Pegasus 40
(8, 'PEG40-RED-40', '40', 'Đỏ Đen', 3500000, 3100000, 14, 'active'),
(8, 'PEG40-RED-41', '41', 'Đỏ Đen', 3500000, 3100000, 20, 'active'),
(8, 'PEG40-RED-42', '42', 'Đỏ Đen', 3500000, 3100000, 16, 'active')
ON DUPLICATE KEY UPDATE `stock_quantity`=VALUES(`stock_quantity`);
