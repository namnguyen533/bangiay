-- ========================================================
-- CƠ SỞ DỮ LIỆU BÁN GIÀY (SHOE STORE)
-- Mô hình MVC - PHP OOP & PDO MySQL
-- ========================================================

CREATE DATABASE IF NOT EXISTS shoe_store
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shoe_store;

-- 1. Bảng Phân Quyền (Roles)
CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Bảng Người Dùng (Users)
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','blocked') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Bảng Phân Quyền Người Dùng
CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, role_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Bảng Thương Hiệu Giày (Brands)
CREATE TABLE IF NOT EXISTS brands (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  slug VARCHAR(150) NOT NULL UNIQUE,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

-- 5. Bảng Danh Mục Giày (Categories)
CREATE TABLE IF NOT EXISTS categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id BIGINT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. Bảng Sản Phẩm Giày (Products)
CREATE TABLE IF NOT EXISTS products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  brand_id BIGINT UNSIGNED NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. Bảng Liên Kết Sản Phẩm - Danh Mục
CREATE TABLE IF NOT EXISTS product_categories (
  product_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, category_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Bảng Biến Thể Giày (Size, Màu, Giá, Kho)
CREATE TABLE IF NOT EXISTS product_variants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  sku VARCHAR(80) NOT NULL UNIQUE,
  size VARCHAR(20) NOT NULL,
  color VARCHAR(50) NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  sale_price DECIMAL(12,2) NULL,
  stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Bảng Hình Ảnh Sản Phẩm
CREATE TABLE IF NOT EXISTS product_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  alt_text VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  is_primary TINYINT(1) DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Bảng Đơn Hàng (Orders)
CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_code VARCHAR(40) NOT NULL UNIQUE,
  user_id BIGINT UNSIGNED NULL,
  voucher_id BIGINT UNSIGNED NULL,
  recipient_name VARCHAR(150) NOT NULL,
  recipient_phone VARCHAR(30) NOT NULL,
  shipping_address TEXT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  discount_amount DECIMAL(12,2) DEFAULT 0.00,
  shipping_fee DECIMAL(12,2) DEFAULT 0.00,
  total_amount DECIMAL(12,2) NOT NULL,
  payment_method VARCHAR(40) NOT NULL DEFAULT 'cod',
  payment_status ENUM('unpaid','pending','paid','failed','refunded','partially_refunded') DEFAULT 'unpaid',
  order_status ENUM('pending','confirmed','processing','shipping','completed','cancelled','returned') DEFAULT 'pending',
  note TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. Bảng Chi Tiết Đơn Hàng (Order Items)
CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NULL,
  product_name VARCHAR(200) NOT NULL,
  sku VARCHAR(80) NOT NULL,
  size VARCHAR(20) NOT NULL,
  color VARCHAR(50) NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  line_total DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ========================================================
-- DỮ LIỆU MẪU (SEED DATA)
-- ========================================================

-- Roles
INSERT IGNORE INTO roles (id, name, description) VALUES
(1, 'Super Admin', 'Quản trị tối cao'),
(2, 'Admin', 'Quản trị viên'),
(3, 'Staff', 'Nhân viên kho'),
(4, 'Customer', 'Khách hàng');

-- Brands
INSERT IGNORE INTO brands (id, name, slug, status) VALUES
(1, 'Nike', 'nike', 'active'),
(2, 'Adidas', 'adidas', 'active'),
(3, 'Puma', 'puma', 'active'),
(4, 'Converse', 'converse', 'active'),
(5, 'Vans', 'vans', 'active');

-- Categories
INSERT IGNORE INTO categories (id, name, slug, status) VALUES
(1, 'Giày Sneaker', 'giay-sneaker', 'active'),
(2, 'Giày Chạy Bộ (Running)', 'giay-chay-bo', 'active'),
(3, 'Giày Bóng Rổ', 'giay-bong-ro', 'active'),
(4, 'Giày Thời Trang / Casual', 'giay-thoi-trang', 'active'),
(5, 'Giày Lười / Slip-on', 'giay-luoi', 'active');

-- Users (Mật khẩu: 123456)
INSERT IGNORE INTO users (id, full_name, email, phone, password_hash, status) VALUES
(1, 'Quản Trị Viên SoleVibe', 'admin@solemate.vn', '0901234567', '$2y$10$wE3z.oX5bLqT2eQnJg6zre1XG8QW2F6H.yF9jE0pC1eH4vA5h2y6q', 'active'),
(2, 'Nguyễn Văn Phong', 'phong@gmail.com', '0987654321', '$2y$10$wE3z.oX5bLqT2eQnJg6zre1XG8QW2F6H.yF9jE0pC1eH4vA5h2y6q', 'active');

INSERT IGNORE INTO user_roles (user_id, role_id) VALUES
(1, 1),
(2, 4);

-- Products
INSERT IGNORE INTO products (id, brand_id, name, slug, description, status) VALUES
(1, 1, 'Nike Air Force 1 \'07 Triple White', 'nike-air-force-1-07-triple-white', 'Mẫu giày sneaker huyền thoại của Nike với tông màu trắng tinh khiết thanh lịch, đệm Air êm ái suốt cả ngày.', 'active'),
(2, 1, 'Nike Air Jordan 1 Retro High OG Chicago', 'nike-air-jordan-1-retro-high-og', 'Biểu tượng văn hóa bóng rổ và streetwear toàn cầu với phối màu Chicago cổ điển.', 'active'),
(3, 2, 'Adidas Ultraboost Light Core Black', 'adidas-ultraboost-light-core-black', 'Giày chạy bộ công nghệ Boost siêu nhẹ, đàn hồi vượt trội hỗ trợ tối đa cho từng bước chạy.', 'active'),
(4, 2, 'Adidas Samba Classic OG White/Black', 'adidas-samba-classic-og-white-black', 'Đôi giày bóng đá đường phố kinh điển trở thành xu hướng thời trang retro hot nhất hiện nay.', 'active'),
(5, 3, 'Puma Suede Classic XXI Peacoat', 'puma-suede-classic-xxi-peacoat', 'Chất liệu da lộn cao cấp mang phong cách hip-hop cổ điển từ thập niên 60.', 'active'),
(6, 4, 'Converse Chuck Taylor All Star 70s Black', 'converse-chuck-70-black', 'Dòng Chuck 70 cao cấp với đế dầy, vải canvas bền bỉ và miếng lót đệm êm chân.', 'active');

-- Product Categories
INSERT IGNORE INTO product_categories (product_id, category_id) VALUES
(1, 1), (1, 4),
(2, 1), (2, 3),
(3, 2),
(4, 1), (4, 4),
(5, 1), (5, 4),
(6, 1), (6, 4);

-- Product Variants
INSERT IGNORE INTO product_variants (id, product_id, sku, size, color, price, sale_price, stock_quantity, status) VALUES
(1, 1, 'AF1-07-WHT-39', '39', 'Trắng', 2890000, 2590000, 15, 'active'),
(2, 1, 'AF1-07-WHT-40', '40', 'Trắng', 2890000, 2590000, 20, 'active'),
(3, 1, 'AF1-07-WHT-41', '41', 'Trắng', 2890000, 2590000, 18, 'active'),
(4, 1, 'AF1-07-WHT-42', '42', 'Trắng', 2890000, 2590000, 12, 'active'),
(5, 2, 'AJ1-HIGH-CHI-40', '40', 'Đỏ/Trắng/Đen', 5200000, 4850000, 8, 'active'),
(6, 2, 'AJ1-HIGH-CHI-41', '41', 'Đỏ/Trắng/Đen', 5200000, 4850000, 10, 'active'),
(7, 2, 'AJ1-HIGH-CHI-42', '42', 'Đỏ/Trắng/Đen', 5200000, 4850000, 5, 'active'),
(8, 3, 'UB-LT-BLK-40', '40', 'Đen', 4500000, 3990000, 12, 'active'),
(9, 3, 'UB-LT-BLK-41', '41', 'Đen', 4500000, 3990000, 10, 'active'),
(10, 3, 'UB-LT-BLK-42', '42', 'Đen', 4500000, 3990000, 14, 'active'),
(11, 4, 'SAMBA-OG-WHT-40', '40', 'Trắng/Đen', 2600000, 2350000, 18, 'active'),
(12, 4, 'SAMBA-OG-WHT-41', '41', 'Trắng/Đen', 2600000, 2350000, 25, 'active'),
(13, 5, 'PUMA-SD-BLU-41', '41', 'Xanh Navy', 1890000, 1590000, 16, 'active'),
(14, 6, 'CHUCK70-BLK-41', '41', 'Đen', 2000000, 1750000, 30, 'active');

-- Product Images
INSERT IGNORE INTO product_images (id, product_id, image_url, alt_text, sort_order, is_primary) VALUES
(1, 1, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&auto=format&fit=crop&q=80', 'Nike Air Force 1 07', 1, 1),
(2, 2, 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600&auto=format&fit=crop&q=80', 'Air Jordan 1 High', 1, 1),
(3, 3, 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600&auto=format&fit=crop&q=80', 'Adidas Ultraboost', 1, 1),
(4, 4, 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&auto=format&fit=crop&q=80', 'Adidas Samba Classic', 1, 1),
(5, 5, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80', 'Puma Suede Classic', 1, 1),
(6, 6, 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&auto=format&fit=crop&q=80', 'Converse Chuck 70', 1, 1);
