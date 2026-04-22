-- ============================================================
--  FoodHub — Complete MySQL Database
--  Tạo bởi: Nhóm dự án kết thúc môn Laravel
--  Cách dùng: mysql -u root -p < foodhub_mysql.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+07:00';

-- ------------------------------------------------------------
-- Tạo database
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS foodhub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE foodhub;

-- ============================================================
--  1. BẢNG users (Chỉ dùng để Xác thực Đăng nhập)
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`          VARCHAR(255)    NOT NULL,
  `password`       VARCHAR(255)    NOT NULL,
  `role`           ENUM('admin','shop','customer') NOT NULL,
  `is_active`      TINYINT(1)      NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100)    DEFAULT NULL,
  `created_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  2. BẢNG customers (Hồ sơ cá nhân của Khách mua hàng)
-- ============================================================
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        BIGINT UNSIGNED NOT NULL,
  `full_name`      VARCHAR(255)    NOT NULL,
  `phone`          VARCHAR(15)     DEFAULT NULL,
  `avatar`         VARCHAR(255)    DEFAULT NULL,
  `created_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_user_id_unique` (`user_id`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  2.1 BẢNG customer_addresses (Danh sách địa chỉ giao hàng)
-- ============================================================
DROP TABLE IF EXISTS `customer_addresses`;
CREATE TABLE `customer_addresses` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        BIGINT UNSIGNED NOT NULL,
  `phone_number`   VARCHAR(15)     NOT NULL,
  `address_line`   TEXT            NOT NULL,
  `is_default`     TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `customer_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  3. BẢNG categories
-- ============================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)    NOT NULL,
  `slug`        VARCHAR(100)    NOT NULL,
  `icon`        VARCHAR(255)    DEFAULT NULL,
  `sort_order`  INT             NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  4. BẢNG shops (Thông tin định danh quán ăn)
-- ============================================================
DROP TABLE IF EXISTS `shops`;
CREATE TABLE `shops` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `category_id`   BIGINT UNSIGNED NOT NULL,
  `name`          VARCHAR(100)    NOT NULL,
  `slug`          VARCHAR(120)    NOT NULL,
  `status`        ENUM('pending','active','banned') NOT NULL DEFAULT 'pending',
  `reject_reason` TEXT            DEFAULT NULL,
  `created_at`    TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shops_user_id_unique` (`user_id`),
  UNIQUE KEY `shops_name_unique` (`name`),
  UNIQUE KEY `shops_slug_unique` (`slug`),
  CONSTRAINT `shops_user_id_foreign`     FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)      ON DELETE CASCADE,
  CONSTRAINT `shops_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  4.1 BẢNG shop_details (Thông tin hiển thị mở rộng - 1:1)
-- ============================================================
DROP TABLE IF EXISTS `shop_details`;
CREATE TABLE `shop_details` (
  `shop_id`       BIGINT UNSIGNED NOT NULL,
  `phone`         VARCHAR(15)     NOT NULL,
  `address`       TEXT            NOT NULL,
  `description`   TEXT            DEFAULT NULL,
  `cover_image`   VARCHAR(255)    DEFAULT NULL,
  `logo`          VARCHAR(255)    DEFAULT NULL,
  `open_time`     TIME            DEFAULT NULL,
  `close_time`    TIME            DEFAULT NULL,
  PRIMARY KEY (`shop_id`),
  CONSTRAINT `shop_details_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  4.2 BẢNG shop_metrics (Thống kê đánh giá, đơn hàng - 1:1)
-- ============================================================
DROP TABLE IF EXISTS `shop_metrics`;
CREATE TABLE `shop_metrics` (
  `shop_id`       BIGINT UNSIGNED NOT NULL,
  `rating_avg`    DECIMAL(2,1)    NOT NULL DEFAULT 0.0,
  `total_orders`  INT UNSIGNED    NOT NULL DEFAULT 0,
  `total_reviews` INT UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`shop_id`),
  CONSTRAINT `shop_metrics_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  5. BẢNG products
-- ============================================================
DROP TABLE IF EXISTS `products`;
-- 1. Tạo bảng products trước
CREATE TABLE `products` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id`        BIGINT UNSIGNED NOT NULL,
  `name`           VARCHAR(150)    NOT NULL,
  `description`    TEXT            DEFAULT NULL,
  `price`          DECIMAL(10,0)   NOT NULL,
  `image`          VARCHAR(255)    DEFAULT NULL,
  `product_group`  VARCHAR(100)    DEFAULT NULL,
  `is_available`   TINYINT(1)      NOT NULL DEFAULT 1,
  `sort_order`     INT             NOT NULL DEFAULT 0,
  `total_sold`     INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `products_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Xóa index ở bảng shops ở một câu lệnh độc lập bên dưới
ALTER TABLE `shops` DROP INDEX `shops_user_id_unique`;

-- ============================================================
--  6. BẢNG orders (Chỉ lưu tiền nong & trạng thái)
-- ============================================================
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code`       VARCHAR(20)     NOT NULL,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `shop_id`          BIGINT UNSIGNED NOT NULL,
  `subtotal`         DECIMAL(12,0)   NOT NULL,
  `shipping_fee`     DECIMAL(10,0)   NOT NULL DEFAULT 15000,
  `discount`         DECIMAL(10,0)   NOT NULL DEFAULT 0,
  `total`            DECIMAL(12,0)   NOT NULL,
  `voucher_code`     VARCHAR(50)     DEFAULT NULL,
  `payment_method`   ENUM('cod','bank') NOT NULL DEFAULT 'cod',
  `status`           ENUM('pending','confirmed','preparing','delivering','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `cancel_reason`    TEXT            DEFAULT NULL,
  `confirmed_at`     TIMESTAMP       NULL DEFAULT NULL,
  `created_at`       TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  6.1 BẢNG order_deliveries (Lưu thông tin giao nhận)
-- ============================================================
DROP TABLE IF EXISTS `order_deliveries`;
CREATE TABLE `order_deliveries` (
  `order_id`         BIGINT UNSIGNED NOT NULL,
  `delivery_address` TEXT            NOT NULL,
  `customer_phone`   VARCHAR(15)     NOT NULL,
  `note`             TEXT            DEFAULT NULL,
  `delivered_at`     TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  CONSTRAINT `order_deliveries_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  7. BẢNG order_items
-- ============================================================
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`      BIGINT UNSIGNED NOT NULL,
  `product_id`    BIGINT UNSIGNED NOT NULL,
  `product_name`  VARCHAR(150)    NOT NULL,
  `product_price` DECIMAL(10,0)   NOT NULL,
  `quantity`      INT UNSIGNED    NOT NULL,
  `subtotal`      DECIMAL(12,0)   NOT NULL,
  `created_at`    TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `order_items_order_id_foreign`   FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  8. BẢNG reviews
-- ============================================================
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED  NOT NULL,
  `shop_id`    BIGINT UNSIGNED  NOT NULL,
  `order_id`   BIGINT UNSIGNED  NOT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment`    TEXT             DEFAULT NULL,
  `is_visible` TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP        NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP        NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_user_order_unique` (`user_id`,`order_id`),
  CONSTRAINT `reviews_user_id_foreign`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `reviews_shop_id_foreign`  FOREIGN KEY (`shop_id`)  REFERENCES `shops`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  9. BẢNG vouchers & wishlists
-- ============================================================
DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE `vouchers` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id`           BIGINT UNSIGNED NOT NULL,
  `code`              VARCHAR(30)     NOT NULL,
  `description`       VARCHAR(200)    DEFAULT NULL,
  `type`              ENUM('percent','fixed') NOT NULL,
  `value`             DECIMAL(10,0)   NOT NULL,
  `min_order_amount`  DECIMAL(12,0)   NOT NULL DEFAULT 0,
  `max_discount`      DECIMAL(10,0)   DEFAULT NULL,
  `max_uses`          INT UNSIGNED    NOT NULL DEFAULT 100,
  `used_count`        INT UNSIGNED    NOT NULL DEFAULT 0,
  `is_active`         TINYINT(1)      NOT NULL DEFAULT 1,
  `starts_at`         TIMESTAMP       NULL DEFAULT NULL,
  `expires_at`        TIMESTAMP       NULL DEFAULT NULL,
  `conditions`        JSON            DEFAULT NULL,
  `created_at`        TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  CONSTRAINT `vouchers_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `shop_id`    BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_shop_unique` (`user_id`,`shop_id`),
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================
--  DỮ LIỆU SEEDER FOODHUB (ĐÃ CHUẨN HÓA THEO SCHEMA)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
--  1. BẢNG users (Chỉ chứa thông tin xác thực)
-- ------------------------------------------------------------
INSERT INTO `users` (`id`, `email`, `password`, `role`, `is_active`) VALUES
(1, 'admin@foodhub.vn', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),
(2, 'hoa.tran@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shop', 1),
(3, 'tuan.nguyen@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shop', 1),
(4, 'mai.le@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shop', 1),
(5, 'bao.pham@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shop', 1),
(6, 'thanh.vo@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shop', 1),
(7, 'long.dang@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'shop', 1),
(8, 'an.nguyen@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(9, 'binh.tran@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(10, 'khai.le@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(11, 'lan.pham@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(12, 'hung.vu@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(13, 'ngoc.hoang@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(14, 'dat.ngo@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(15, 'huong.bui@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(16, 'vinh.dinh@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(17, 'kimanh.do@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(18, 'minh.truong@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(19, 'thu.phan@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1),
(20, 'chau.ly@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1);

-- ------------------------------------------------------------
--  2. BẢNG customers (Hồ sơ của user có role = customer)
-- ------------------------------------------------------------
INSERT INTO `customers` (`user_id`, `full_name`, `phone`) VALUES
(8, 'Nguyễn Văn An', '0911111111'),
(9, 'Trần Thị Bình', '0912222222'),
(10, 'Lê Hoàng Khải', '0913333333'),
(11, 'Phạm Thị Lan', '0914444444'),
(12, 'Vũ Đình Hùng', '0915555555'),
(13, 'Hoàng Thị Ngọc', '0916666666'),
(14, 'Ngô Thành Đạt', '0917777777'),
(15, 'Bùi Thị Hương', '0918888888'),
(16, 'Đinh Quang Vinh', '0919999999'),
(17, 'Đỗ Thị Kim Anh', '0920000000'),
(18, 'Trương Văn Minh', '0921111111'),
(19, 'Phan Thị Thu', '0922222222'),
(20, 'Lý Minh Châu', '0923333333');

-- ------------------------------------------------------------
--  2.1 BẢNG customer_addresses (Tách địa chỉ từ bảng users cũ)
-- ------------------------------------------------------------
INSERT INTO `customer_addresses` (`user_id`, `phone_number`, `address_line`, `is_default`) VALUES
(8, '0911111111', '34 Lý Tự Trọng, Quận 1, TP.HCM', 1),
(9, '0912222222', '88 Nguyễn Đình Chiểu, Quận 3, TP.HCM', 1),
(10, '0913333333', '15 Pasteur, Quận 1, TP.HCM', 1),
(11, '0914444444', '67 Lê Văn Sỹ, Quận Phú Nhuận, TP.HCM', 1),
(12, '0915555555', '200 Phan Văn Trị, Quận Bình Thạnh, TP.HCM', 1),
(13, '0916666666', '42 Trường Chinh, Quận Tân Bình, TP.HCM', 1),
(14, '0917777777', '11 Nguyễn Thái Học, Quận 1, TP.HCM', 1),
(15, '0918888888', '300 Lê Hồng Phong, Quận 10, TP.HCM', 1),
(16, '0919999999', '7 Nguyễn Cư Trinh, Quận 1, TP.HCM', 1),
(17, '0920000000', '55 Bà Huyện Thanh Quan, Quận 3, TP.HCM', 1),
(18, '0921111111', '120 Nguyễn Thị Minh Khai, Quận 3, TP.HCM', 1),
(19, '0922222222', '88 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM', 1),
(20, '0923333333', '33 Nguyễn Bỉnh Khiêm, Quận 1, TP.HCM', 1);

-- ------------------------------------------------------------
--  3. BẢNG categories
-- ------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `sort_order`, `is_active`) VALUES
(1, 'Trà sữa', 'tra-sua', 'icons/trasua.png', 1, 1),
(2, 'Đồ ăn vặt', 'do-an-vat', 'icons/doanvat.png', 2, 1),
(3, 'Fast food', 'fast-food', 'icons/fastfood.png', 3, 1),
(4, 'Cơm văn phòng', 'com-van-phong', 'icons/com.png', 4, 1),
(5, 'Bánh mì', 'banh-mi', 'icons/banhmi.png', 5, 1),
(6, 'Tráng miệng', 'trang-mieng', 'icons/trangmieng.png', 6, 1),
(7, 'Bún phở', 'bun-pho', 'icons/bunpho.png', 7, 1),
(8, 'Pizza', 'pizza', 'icons/pizza.png', 8, 1),
(9, 'Cà phê', 'ca-phe', 'icons/cafe.png', 9, 1),
(10, 'Hải sản', 'hai-san', 'icons/haisan.png', 10, 1),
(11, 'Cơm tấm', 'com-tam', 'icons/comtam.png', 11, 1),
(12, 'Chay', 'chay', 'icons/chay.png', 12, 1),
(13, 'Nước ép', 'nuoc-ep', 'icons/nuocep.png', 13, 1),
(14, 'Mì', 'mi', 'icons/mi.png', 14, 1),
(15, 'Bánh ngọt', 'banh-ngot', 'icons/banhngot.png', 15, 1),
(16, 'Nướng', 'nuong', 'icons/nuong.png', 16, 1),
(17, 'Gà rán', 'ga-ran', 'icons/garan.png', 17, 1),
(18, 'Salad', 'salad', 'icons/salad.png', 18, 1);

-- ------------------------------------------------------------
--  4. BẢNG shops (Tách thông tin cơ bản)
-- ------------------------------------------------------------
INSERT INTO `shops` (`id`, `user_id`, `category_id`, `name`, `slug`, `status`, `reject_reason`) VALUES
(1, 2, 1, 'Trà Sữa Hoa Đào', 'tra-sua-hoa-dao', 'active', NULL),
(2, 2, 3, 'Burger Hoa - Fast Food', 'burger-hoa-fast-food', 'active', NULL),
(3, 3, 4, 'Cơm Tấm Sài Gòn Tuấn', 'com-tam-sai-gon-tuan', 'active', NULL),
(4, 3, 9, 'Cà Phê Tuấn House', 'ca-phe-tuan-house', 'active', NULL),
(5, 4, 2, 'Ăn Vặt Mai Béo', 'an-vat-mai-beo', 'active', NULL),
(6, 4, 6, 'Chè Mai - Tráng Miệng', 'che-mai-trang-mieng', 'active', NULL),
(7, 5, 5, 'Bánh Mì Phượng Bảo', 'banh-mi-phuong-bao', 'active', NULL),
(8, 5, 7, 'Phở Bắc Bảo Ngon', 'pho-bac-bao-ngon', 'active', NULL),
(9, 6, 8, 'Pizza Thanh House', 'pizza-thanh-house', 'active', NULL),
(10, 6, 12, 'Quán Chay Thanh Tâm', 'quan-chay-thanh-tam', 'active', NULL),
(11, 7, 1, 'The Long Tea House', 'the-long-tea-house', 'active', NULL),
(12, 7, 17, 'Gà Rán Long Vàng', 'ga-ran-long-vang', 'active', NULL),
(13, 2, 10, 'Hải Sản Tươi Sống Hoa', 'hai-san-tuoi-song-hoa', 'pending', NULL),
(14, 4, 15, 'Bánh Ngọt Mai Bakery', 'banh-ngot-mai-bakery', 'pending', NULL),
(15, 3, 16, 'Quán Nướng Vỉa Hè', 'quan-nuong-via-he', 'banned', 'Vi phạm quy định vệ sinh an toàn thực phẩm lần 2');

-- ------------------------------------------------------------
--  4.1 BẢNG shop_details (Tách thông tin hiển thị)
-- ------------------------------------------------------------
INSERT INTO `shop_details` (`shop_id`, `phone`, `address`, `description`, `open_time`, `close_time`) VALUES
(1, '0901111111', '12 Lê Lợi, Quận 1, TP.HCM', 'Trà sữa tươi ngon, pha chế mỗi ngày. Hơn 50 loại topping đặc biệt. Cam kết 100% nguyên liệu sạch, không phẩm màu.', '07:00:00', '22:00:00'),
(2, '0901111112', '34 Nguyễn Huệ, Quận 1, TP.HCM', 'Burger thủ công từ thịt bò tươi, không dùng thịt đông lạnh. Khoai tây chiên giòn, sauce đặc biệt.', '09:00:00', '21:30:00'),
(3, '0902222222', '45 Nguyễn Trãi, Quận 5, TP.HCM', 'Cơm tấm sườn nướng đặc biệt, bì chả thơm ngon. Mở cửa từ sáng sớm, phục vụ cả ngày.', '06:00:00', '21:00:00'),
(4, '0902222223', '78 Lê Thánh Tôn, Quận 1, TP.HCM', 'Cà phê rang xay tại chỗ. Không gian yên tĩnh, wifi miễn phí. Phù hợp làm việc và học bài.', '06:30:00', '22:30:00'),
(5, '0903333333', '78 Trần Hưng Đạo, Quận 1, TP.HCM', 'Đủ các loại đồ ăn vặt: bánh tráng trộn, bắp xào, trứng cút, chả cá, xiên que... Siêu ngon siêu rẻ!', '14:00:00', '23:00:00'),
(6, '0903333334', '90 Đinh Tiên Hoàng, Quận 1, TP.HCM', 'Chè 3 miền, kem trái cây, pudding matcha. Tất cả làm thủ công từ nguyên liệu tự nhiên mỗi ngày.', '10:00:00', '22:00:00'),
(7, '0904444444', '23 Hai Bà Trưng, Quận 3, TP.HCM', 'Bánh mì ổ giòn rụm, nhân đa dạng. Sốt đặc biệt gia truyền 20 năm.', '06:00:00', '11:00:00'),
(8, '0904444445', '56 Cống Quỳnh, Quận 1, TP.HCM', 'Phở bò tái chín nước dùng hầm xương 12 tiếng. Bún bò Huế cay đậm đà. Mì Quảng thơm ngon đặc trưng.', '06:00:00', '14:00:00'),
(9, '0905555555', '99 Cách Mạng Tháng 8, Quận 10, TP.HCM', 'Pizza kiểu Ý đế mỏng giòn. Nguyên liệu nhập khẩu: phô mai mozzarella, salami, pepperoni.', '10:00:00', '22:00:00'),
(10, '0905555556', '14 Nguyễn Đình Chiểu, Quận 3, TP.HCM', 'Thức ăn chay thuần túy: cơm chay, bún chay, bánh cuốn chay, nem chay. Tốt cho sức khỏe, giảm cân hiệu quả.', '07:00:00', '20:00:00'),
(11, '0906666666', '56 Đinh Tiên Hoàng, Quận Bình Thạnh, TP.HCM', 'Trà sữa Đài Loan chính gốc. Hơn 60 loại thức uống. Trân châu tươi mỗi ngày, không để qua đêm.', '08:00:00', '23:00:00'),
(12, '0906666667', '200 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM', 'Gà rán giòn tẩm bí quyết gia truyền. Combo cơm gà, cánh gà, đùi gà siêu giòn. Freeship nội thành.', '10:00:00', '22:30:00'),
(13, '0901111113', '88 Võ Văn Tần, Quận 3, TP.HCM', 'Hải sản tươi sống nhập từ Vũng Tàu mỗi ngày. Tôm hùm, cua, mực, cá...', '10:00:00', '22:00:00'),
(14, '0903333335', '12 Ngô Đức Kế, Quận 1, TP.HCM', 'Bánh ngọt Âu châu: croissant, tiramisu, mousse cake. Đặt bánh sinh nhật theo yêu cầu.', '08:00:00', '21:00:00'),
(15, '0902222224', '33 Trần Quý Cáp, Quận 3, TP.HCM', 'Thịt nướng vỉa hè, không gian ngoài trời.', '17:00:00', '00:00:00');

-- ------------------------------------------------------------
--  4.2 BẢNG shop_metrics (Tách thống kê)
-- ------------------------------------------------------------
INSERT INTO `shop_metrics` (`shop_id`, `rating_avg`, `total_orders`, `total_reviews`) VALUES
(1, 4.8, 320, 45), (2, 4.5, 180, 32), (3, 4.9, 560, 88),
(4, 4.6, 240, 41), (5, 4.7, 410, 67), (6, 4.8, 290, 52),
(7, 5.0, 750, 120), (8, 4.7, 380, 61), (9, 4.4, 210, 38),
(10, 4.6, 175, 29), (11, 4.9, 490, 78), (12, 4.5, 280, 45),
(13, 0.0, 0, 0), (14, 0.0, 0, 0), (15, 3.2, 45, 10);

-- ------------------------------------------------------------
--  5. BẢNG products
-- ------------------------------------------------------------
INSERT INTO `products` (`id`, `shop_id`, `name`, `description`, `price`, `product_group`, `is_available`, `sort_order`, `total_sold`) VALUES
(1, 1, 'Trà sữa trân châu đường đen', 'Trà sữa trân châu nâu đường đen thơm ngon, béo ngậy', 45000, 'Trà sữa', 1, 1, 180),
(2, 1, 'Matcha latte trân châu', 'Matcha Nhật Bản kết hợp sữa tươi béo ngậy, topping trân châu trắng', 50000, 'Trà sữa', 1, 1, 145),
(3, 1, 'Hồng trà sữa', 'Hồng trà Đài Loan kết hợp sữa tươi nhập khẩu, không đường hoặc ít đường', 40000, 'Trà sữa', 1, 2, 210),
(4, 1, 'Trà đào cam sả', 'Thanh mát vị đào tươi, cam vắt, sả thơm. Topping thạch trái cây', 45000, 'Trà trái cây', 1, 3, 165),
(5, 1, 'Cà phê sữa đá cuộn', 'Cà phê sữa đặc pha đá viên cuộn mịn. Đậm đà thơm béo.', 35000, 'Cà phê', 1, 4, 98),
(6, 3, 'Cơm tấm sườn bì chả', 'Sườn nướng mật ong, bì sợi, chả trứng. Kèm dưa leo, cà chua, nước mắm pha.', 55000, 'Cơm tấm', 1, 0, 520),
(7, 3, 'Cơm tấm sườn nướng đặc biệt', 'Sườn non nướng than hoa, ướp gia vị đặc biệt. Size lớn ăn no.', 65000, 'Cơm tấm', 1, 1, 380),
(8, 3, 'Cơm gà rau sống', 'Cơm trắng dẻo với gà luộc mềm ngọt, rau sống, nước mắm gừng.', 50000, 'Cơm gà', 1, 2, 290),
(9, 3, 'Canh chua cá lóc', 'Canh chua miền Nam truyền thống. Cá lóc tươi, cà chua, giá đỗ, bạc hà.', 35000, 'Canh', 1, 3, 145),
(10, 5, 'Bánh tráng trộn đặc biệt', 'Bánh tráng trộn tôm khô, xoài, sốt tương, hành phi, đậu phộng rang.', 25000, 'Bánh tráng', 1, 0, 450),
(11, 5, 'Xúc xích nướng 5 cái', 'Xúc xích Đức nướng than, kẹp bánh mì nho nhỏ. Sốt cà chua + tương ớt.', 30000, 'Xiên que', 1, 1, 380),
(12, 5, 'Trứng cút lắc muối tắc', 'Trứng cút chiên giòn lắc muối tắc ớt. Cay cay chua chua hấp dẫn.', 20000, 'Trứng cút', 1, 2, 310),
(13, 5, 'Khoai tây lắc phô mai', 'Khoai tây chiên giòn, tẩm phô mai và gia vị bí truyền. Size M đủ no.', 35000, 'Khoai tây', 1, 3, 275),
(14, 5, 'Bắp xào bơ đặc biệt', 'Bắp Mỹ xào bơ, muối, đường. Thêm phô mai tan chảy. Thơm béo ngậy.', 25000, 'Bắp', 1, 4, 220),
(15, 7, 'Bánh mì thịt nguội đặc biệt', 'Bánh mì ổ giòn nhân pate, giăm bông, chả lụa, dưa chuột, rau mùi. Sốt gia truyền.', 25000, 'Bánh mì', 1, 0, 680),
(16, 7, 'Bánh mì trứng ốp la', 'Bánh mì giòn, trứng ốp la chảy lòng đào, pate, bơ, xì dầu.', 20000, 'Bánh mì', 1, 1, 520),
(17, 7, 'Bánh mì xíu mại', 'Xíu mại thịt heo sốt cà chua đậm đà. Ổ bánh nóng hổi giòn tan.', 22000, 'Bánh mì', 1, 2, 440),
(18, 11, 'Trà sữa oolong topping đặc biệt', 'Trà oolong Đài Loan thơm hoa quê, sữa tươi nhập khẩu. Topping: trân châu nâu + pudding trứng + thạch cà phê.', 55000, 'Trà sữa cao cấp', 1, 0, 390),
(19, 11, 'Brown sugar milk tea', 'Trà sữa đường đen Đài Loan trứ danh. Trân châu đen dẻo dai siêu ngon.', 50000, 'Trà sữa cao cấp', 1, 1, 420),
(20, 11, 'Yakult dứa ổi', 'Yakult xay dứa ổi thanh mát. Topping thạch trái cây đầy màu sắc.', 45000, 'Trà trái cây', 1, 2, 280);

-- ------------------------------------------------------------
--  6. BẢNG orders (Chỉ lưu thông tin đơn & tài chính)
-- ------------------------------------------------------------
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `shop_id`, `subtotal`, `shipping_fee`, `discount`, `total`, `voucher_code`, `payment_method`, `status`, `cancel_reason`, `confirmed_at`, `created_at`) VALUES
(1, 'FH-20240301-001', 8, 1, 90000, 15000, 0, 105000, NULL, 'cod', 'delivered', NULL, '2024-03-01 08:15:00', '2024-03-01 08:00:00'),
(2, 'FH-20240302-002', 9, 3, 120000, 15000, 15000, 120000, 'SALE15K', 'cod', 'delivered', NULL, '2024-03-02 11:20:00', '2024-03-02 11:00:00'),
(3, 'FH-20240303-003', 10, 7, 67000, 10000, 0, 77000, NULL, 'cod', 'delivered', NULL, '2024-03-03 07:05:00', '2024-03-03 07:00:00'),
(4, 'FH-20240304-004', 11, 5, 75000, 15000, 0, 90000, NULL, 'cod', 'delivered', NULL, '2024-03-04 15:10:00', '2024-03-04 15:00:00'),
(5, 'FH-20240305-005', 12, 11, 160000, 15000, 20000, 155000, 'LONGTEN20K', 'cod', 'delivered', NULL, '2024-03-05 16:05:00', '2024-03-05 16:00:00'),
(6, 'FH-20240306-006', 13, 3, 130000, 15000, 0, 145000, NULL, 'cod', 'delivering', NULL, '2024-03-06 12:10:00', '2024-03-06 12:00:00'),
(7, 'FH-20240306-007', 14, 1, 135000, 15000, 0, 150000, NULL, 'cod', 'preparing', NULL, '2024-03-06 14:32:00', '2024-03-06 14:25:00'),
(8, 'FH-20240306-008', 15, 7, 90000, 10000, 0, 100000, NULL, 'cod', 'confirmed', NULL, '2024-03-06 06:35:00', '2024-03-06 06:30:00'),
(9, 'FH-20240306-009', 16, 5, 95000, 15000, 0, 110000, NULL, 'cod', 'pending', NULL, NULL, '2024-03-06 16:50:00'),
(10, 'FH-20240306-010', 17, 11, 100000, 15000, 0, 115000, NULL, 'cod', 'pending', NULL, NULL, '2024-03-06 17:10:00'),
(11, 'FH-20240228-011', 18, 1, 85000, 15000, 0, 100000, NULL, 'cod', 'cancelled', 'Khách huỷ do bận việc đột xuất', NULL, '2024-02-28 10:00:00'),
(12, 'FH-20240227-012', 19, 3, 55000, 15000, 0, 70000, NULL, 'cod', 'cancelled', 'Shop hết nguyên liệu sườn bì chả', NULL, '2024-02-27 11:30:00'),
(13, 'FH-20240225-013', 20, 5, 50000, 15000, 0, 65000, NULL, 'cod', 'delivered', NULL, '2024-02-25 15:05:00', '2024-02-25 15:00:00'),
(14, 'FH-20240224-014', 8, 11, 110000, 15000, 0, 125000, NULL, 'cod', 'delivered', NULL, '2024-02-24 09:10:00', '2024-02-24 09:00:00'),
(15, 'FH-20240223-015', 9, 7, 47000, 10000, 0, 57000, NULL, 'cod', 'delivered', NULL, '2024-02-23 06:35:00', '2024-02-23 06:30:00'),
(16, 'FH-20240222-016', 10, 1, 95000, 15000, 0, 110000, NULL, 'cod', 'delivered', NULL, '2024-02-22 14:15:00', '2024-02-22 14:00:00'),
(17, 'FH-20240221-017', 11, 3, 165000, 15000, 0, 180000, NULL, 'cod', 'delivered', NULL, '2024-02-21 11:05:00', '2024-02-21 11:00:00'),
(18, 'FH-20240220-018', 12, 5, 70000, 15000, 0, 85000, NULL, 'cod', 'delivered', NULL, '2024-02-20 16:05:00', '2024-02-20 16:00:00');

-- ------------------------------------------------------------
--  6.1 BẢNG order_deliveries (Tách thông tin giao nhận)
-- ------------------------------------------------------------
INSERT INTO `order_deliveries` (`order_id`, `delivery_address`, `customer_phone`, `note`, `delivered_at`) VALUES
(1, '34 Lý Tự Trọng, Quận 1, TP.HCM', '0911111111', 'Ít đường, nhiều đá', '2024-03-01 08:45:00'),
(2, '88 Nguyễn Đình Chiểu, Quận 3, TP.HCM', '0912222222', 'Thêm đồ chua', '2024-03-02 11:55:00'),
(3, '15 Pasteur, Quận 1, TP.HCM', '0913333333', '', '2024-03-03 07:25:00'),
(4, '67 Lê Văn Sỹ, Quận Phú Nhuận, TP.HCM', '0914444444', 'Thêm tương ớt riêng', '2024-03-04 15:40:00'),
(5, '200 Phan Văn Trị, Quận Bình Thạnh, TP.HCM', '0915555555', 'Để ở cổng giúp mình nhé', '2024-03-05 16:40:00'),
(6, '42 Trường Chinh, Quận Tân Bình, TP.HCM', '0916666666', '2 suất riêng, đóng hộp', NULL),
(7, '11 Nguyễn Thái Học, Quận 1, TP.HCM', '0917777777', '', NULL),
(8, '300 Lê Hồng Phong, Quận 10, TP.HCM', '0918888888', 'Giao trước 7h sáng', NULL),
(9, '7 Nguyễn Cư Trinh, Quận 1, TP.HCM', '0919999999', 'Cay vừa thôi', NULL),
(10, '55 Bà Huyện Thanh Quan, Quận 3, TP.HCM', '0920000000', '', NULL),
(11, '120 Nguyễn Thị Minh Khai, Quận 3, TP.HCM', '0921111111', '', NULL),
(12, '88 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM', '0922222222', '', NULL),
(13, '55 Bà Huyện Thanh Quan, Quận 3, TP.HCM', '0922222222', '', '2024-02-25 15:35:00'),
(14, '34 Lý Tự Trọng, Quận 1, TP.HCM', '0911111111', 'Trân châu nhiều nhé', '2024-02-24 09:45:00'),
(15, '88 Nguyễn Đình Chiểu, Quận 3, TP.HCM', '0912222222', '', '2024-02-23 06:55:00'),
(16, '15 Pasteur, Quận 1, TP.HCM', '0913333333', 'Không đường', '2024-02-22 14:50:00'),
(17, '67 Lê Văn Sỹ, Quận Phú Nhuận, TP.HCM', '0914444444', '3 suất cho cả nhóm', '2024-02-21 11:40:00'),
(18, '200 Phan Văn Trị, Quận Bình Thạnh, TP.HCM', '0915555555', '', '2024-02-20 16:35:00');

-- ------------------------------------------------------------
--  7. BẢNG order_items
-- ------------------------------------------------------------
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Trà sữa trân châu đường đen', 45000, 2, 90000),
(2, 6, 'Cơm tấm sườn bì chả', 55000, 1, 55000),
(2, 9, 'Canh chua cá lóc', 35000, 1, 35000),
(3, 15, 'Bánh mì thịt nguội đặc biệt', 25000, 2, 50000),
(3, 16, 'Bánh mì trứng ốp la', 20000, 1, 17000),
(4, 10, 'Bánh tráng trộn đặc biệt', 25000, 2, 50000),
(4, 13, 'Khoai tây lắc phô mai', 35000, 1, 25000),
(5, 18, 'Trà sữa oolong topping đặc biệt', 55000, 2, 110000),
(5, 20, 'Yakult dứa ổi', 45000, 1, 45000),
(6, 6, 'Cơm tấm sườn bì chả', 55000, 1, 55000),
(6, 7, 'Cơm tấm sườn nướng đặc biệt', 65000, 1, 65000),
(7, 1, 'Trà sữa trân châu đường đen', 45000, 2, 90000),
(7, 4, 'Trà đào cam sả', 45000, 1, 45000),
(8, 15, 'Bánh mì thịt nguội đặc biệt', 25000, 2, 50000),
(8, 17, 'Bánh mì xíu mại', 22000, 2, 40000),
(9, 11, 'Xúc xích nướng 5 cái', 30000, 2, 60000),
(9, 12, 'Trứng cút lắc muối tắc', 20000, 1, 20000),
(9, 14, 'Bắp xào bơ đặc biệt', 25000, 1, 15000),
(10, 19, 'Brown sugar milk tea', 50000, 2, 100000),
(11, 2, 'Matcha latte trân châu', 50000, 1, 50000),
(11, 3, 'Hồng trà sữa', 40000, 1, 35000),
(12, 6, 'Cơm tấm sườn bì chả', 55000, 1, 55000),
(13, 10, 'Bánh tráng trộn đặc biệt', 25000, 2, 50000),
(14, 18, 'Trà sữa oolong topping đặc biệt', 55000, 1, 55000),
(14, 20, 'Yakult dứa ổi', 45000, 1, 45000),
(15, 16, 'Bánh mì trứng ốp la', 20000, 1, 20000),
(15, 17, 'Bánh mì xíu mại', 22000, 1, 22000),
(16, 3, 'Hồng trà sữa', 40000, 2, 80000),
(16, 5, 'Cà phê sữa đá cuộn', 35000, 1, 15000),
(17, 6, 'Cơm tấm sườn bì chả', 55000, 2, 110000),
(17, 8, 'Cơm gà rau sống', 50000, 1, 50000),
(18, 11, 'Xúc xích nướng 5 cái', 30000, 2, 60000),
(18, 14, 'Bắp xào bơ đặc biệt', 25000, 1, 10000);

-- ------------------------------------------------------------
--  8. BẢNG reviews
-- ------------------------------------------------------------
INSERT INTO `reviews` (`user_id`, `shop_id`, `order_id`, `rating`, `comment`, `is_visible`) VALUES
(8, 1, 1, 5, 'Trà sữa ngon tuyệt, trân châu dẻo không bị cứng. Shipper giao nhanh, đóng gói cẩn thận. Sẽ order lại!', 1),
(9, 3, 2, 5, 'Cơm tấm ngon nhất Sài Gòn! Sườn nướng thơm đậm đà, nước mắm chua ngọt vừa miệng. Rất đáng tiền!', 1),
(10, 7, 3, 5, 'Bánh mì ngon hết sảy. Ổ bánh giòn, nhân đầy. Sốt gia truyền đặc biệt, không nơi nào có. 5 sao full!', 1),
(11, 5, 4, 4, 'Bánh tráng trộn ngon, đậu phộng rang thơm, xoài chua vừa. Khoai tây hơi nguội khi đến nơi, trừ 1 sao.', 1),
(12, 11, 5, 5, 'Trà oolong thơm, sữa béo vừa. Topping đầy đặn. Yakult dứa ổi tươi mát siêu ngon. Quán top 1 trà sữa!', 1),
(20, 5, 13, 5, 'Bánh tráng trộn siêu ngon! Phần nhiều topping, gia vị đậm đà. Giá rất hợp lý cho sinh viên.', 1),
(8, 11, 14, 5, 'Oolong thơm đặc biệt, trân châu dẻo dai cực phẩm. Yakult chua ngọt thanh mát. Order lần thứ 5 rồi!', 1),
(9, 7, 15, 4, 'Bánh mì ổ giòn tan. Trứng ốp la chín tới, xíu mại thơm ngon. Chỉ hơi mặn một chút với mình.', 1),
(10, 1, 16, 4, 'Hồng trà thơm nhẹ, ít ngọt theo yêu cầu. Cà phê sữa đá cuộn đậm đà. Giao hàng đúng giờ.', 1),
(11, 3, 17, 5, 'Order 3 suất cho cả nhóm, ai cũng khen ngon! Sườn mềm, bì giòn, chả trứng vừa. Tuyệt vời!', 1),
(12, 5, 18, 4, 'Xúc xích nướng thơm giòn. Bắp xào bơ béo thơm. Phần hơi ít so với giá, nhưng chất lượng ổn.', 1),
(19, 3, 2, 3, 'Cơm tấm ngon nhưng hôm nay shop hết sườn bì chả phải đổi sang sườn thường. Mong lần sau đủ hàng.', 1),
(13, 3, 6, 5, '2 suất cơm đóng hộp cẩn thận, không bị đổ. Sườn nướng thơm từ xa. Giá hợp lý, sẽ quay lại.', 1),
(15, 7, 8, 5, 'Bánh mì giao trước 7h đúng hẹn! Ổ bánh còn ấm, giòn tan. Nhân đầy đặn. Quán mình order mỗi sáng!', 1),
(18, 1, 11, 2, 'Huỷ đơn do bận nhưng được shop xử lý nhanh, không tính phí. Lần sau sẽ order lại bù nhé!', 1),
(14, 1, 7, 5, 'Trà sữa và trà đào đều ngon! Đóng gói 2 lớp bọc kín, đá không tan trên đường giao. Xuất sắc!', 1);

-- ------------------------------------------------------------
--  9. BẢNG vouchers
-- ------------------------------------------------------------
INSERT INTO `vouchers` (`shop_id`, `code`, `description`, `type`, `value`, `min_order_amount`, `max_discount`, `max_uses`, `used_count`, `is_active`, `starts_at`, `expires_at`) VALUES
(1, 'HOADAO10', 'Giảm 10% cho đơn từ 100k', 'percent', 10, 100000, 30000, 100, 38, 1, NULL, '2024-12-31 23:59:59'),
(1, 'HOAMOI', 'Tặng 15k cho khách mới - Chỉ dùng 1 lần', 'fixed', 15000, 50000, NULL, 200, 72, 1, NULL, '2024-12-31 23:59:59'),
(3, 'SALE15K', 'Giảm 15k cho đơn từ 80k', 'fixed', 15000, 80000, NULL, 150, 45, 1, NULL, '2024-06-30 23:59:59'),
(3, 'COMTAM20', 'Giảm 20% cho đơn hàng thứ 3 trở đi', 'percent', 20, 100000, 50000, 50, 12, 1, NULL, '2024-09-30 23:59:59'),
(5, 'SNACK30', 'Giảm 30% - Flash sale cuối tuần', 'percent', 30, 60000, 25000, 30, 30, 0, NULL, '2024-03-03 23:59:59'),
(7, 'BANHMI5K', 'Giảm 5k bất kỳ đơn nào - Không điều kiện', 'fixed', 5000, 0, NULL, 999, 210, 1, NULL, '2024-12-31 23:59:59'),
(11, 'LONGTEN20K', 'Giảm 20k cho đơn từ 120k', 'fixed', 20000, 120000, NULL, 80, 33, 1, NULL, '2024-08-31 23:59:59'),
(11, 'LONGVIP', 'Giảm 15% cho VIP - Đơn từ 200k', 'percent', 15, 200000, 60000, 20, 5, 1, NULL, '2024-12-31 23:59:59'),
(9, 'PIZZA25', 'Mừng khai trương - Giảm 25k đơn Pizza', 'fixed', 25000, 150000, NULL, 100, 18, 1, '2024-01-01 00:00:00', '2024-12-31 23:59:59'),
(4, 'CAFE10K', 'Giảm 10k - Uống cà phê buổi sáng', 'fixed', 10000, 50000, NULL, 200, 67, 1, NULL, '2024-12-31 23:59:59'),
(6, 'CHE15', 'Giảm 15% chè các loại - Đơn từ 50k', 'percent', 15, 50000, 20000, 60, 22, 1, NULL, '2024-07-31 23:59:59'),
(8, 'PHO20K', 'Giảm 20k cho đơn bún phở từ 100k', 'fixed', 20000, 100000, NULL, 40, 14, 1, NULL, '2024-12-31 23:59:59'),
(12, 'GARAN10', 'Giảm 10% gà rán - Áp dụng thứ 6, 7, CN', 'percent', 10, 100000, 35000, 120, 41, 1, NULL, '2024-12-31 23:59:59'),
(3, 'TETHOLIDAY', 'Voucher Tết - Giảm 50k đơn từ 200k', 'fixed', 50000, 200000, NULL, 50, 50, 0, '2024-02-08 00:00:00', '2024-02-14 23:59:59'),
(1, 'SUMMER30', 'Sale hè - Giảm 30% đồ uống, tối đa 40k', 'percent', 30, 80000, 40000, 200, 0, 1, '2024-06-01 00:00:00', '2024-08-31 23:59:59'),
(5, 'STUDENT', 'Ưu đãi sinh viên - Giảm 10k với thẻ SV', 'fixed', 10000, 40000, NULL, 500, 128, 1, NULL, '2024-12-31 23:59:59');

-- ------------------------------------------------------------
--  10. BẢNG wishlists
-- ------------------------------------------------------------
INSERT INTO `wishlists` (`user_id`, `shop_id`) VALUES
(8, 1), (8, 3), (8, 11),
(9, 3), (9, 7),
(10, 7), (10, 1),
(11, 5), (11, 3),
(12, 11), (12, 5),
(13, 3), (14, 1),
(15, 7), (16, 5),
(17, 11), (18, 1),
(19, 7), (20, 5), (20, 11);

SET FOREIGN_KEY_CHECKS = 1;
