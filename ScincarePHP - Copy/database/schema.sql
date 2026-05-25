-- Create the database schema for the PureGlow backend.
CREATE DATABASE IF NOT EXISTS `pureglow` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pureglow`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `sku` VARCHAR(80) NOT NULL,
  `category` VARCHAR(80) NOT NULL,
  `brand` VARCHAR(120) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'EUR',
  `stock` INT UNSIGNED NOT NULL DEFAULT 0,
  `images` JSON NOT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `carts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `items` JSON NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `order_number` VARCHAR(64) NOT NULL UNIQUE,
  `status` ENUM('pending','confirmed','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'EUR',
  `payment_method` VARCHAR(60) NOT NULL,
  `shipping_address` JSON NOT NULL,
  `notes` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(60) NOT NULL,
  `email` VARCHAR(180),
  `skin_type` VARCHAR(60) NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` VARCHAR(80) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `subscribed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data
INSERT INTO products (name, slug, sku, category, brand, description, price, currency, stock, images, featured)
VALUES
('Low pH Good Morning Gel Cleanser', 'cosrx-good-morning-gel-cleanser', 'COSRX-001', 'cleanser', 'COSRX', 'Mild pH 5.0 cleanser with tea tree oil. Removes impurities without stripping the skin\'s natural barrier.', 12.90, 'EUR', 35, JSON_ARRAY('https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=900&q=80'), 1),
('Snail 96 Mucin Essence', 'cosrx-snail-96-mucin-essence', 'COSRX-002', 'serum', 'COSRX', 'Hydrating essence with 96% snail mucin to soothe redness and strengthen moisture retention.', 20.50, 'EUR', 24, JSON_ARRAY('https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=900&q=80'), 1),
('Green Tea Seed Cream', 'innisfree-green-tea-seed-cream', 'INNIS-001', 'moisturizer', 'Innisfree', 'Lightweight moisturizer with Jeju green tea extracts to hydrate and calm sensitive skin.', 18.75, 'EUR', 30, JSON_ARRAY('https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?w=900&q=80'), 0),
('Relief Sun', 'beauty-of-joseon-relief-sun', 'BOJ-001', 'spf', 'Beauty of Joseon', 'Mineral sunscreen with a silky finish and antioxidant-rich rice bran, perfect for everyday wear.', 16.40, 'EUR', 22, JSON_ARRAY('https://images.unsplash.com/photo-1512428559087-560fa5ceab42?w=900&q=80'), 0);

-- Create an admin account after installation with a password hashed in PHP.
-- Example: INSERT INTO users (name, email, password, role) VALUES ('Admin User', 'admin@pureglow.al', '<password_hash_here>', 'admin');
