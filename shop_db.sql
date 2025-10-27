create database shop_db;

USE shop_db;

-- phpMyAdmin SQL Dump (versión corregida con llaves foráneas)
-- Compatible con MySQL 8+ / MariaDB 10.4+
-- Proyecto: SmartShop
-- Autor: Cinthia Rivera / Adaptado por GPT-5
-- Fecha: 2025-10-26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

 /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `shop_db`
--

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `admins`
--
CREATE TABLE `admins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admins` (`name`, `password`) VALUES
('admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------
--
-- Estructura de tabla para `users`
--
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para `products`
--
CREATE TABLE `products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `details` VARCHAR(500) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image_01` VARCHAR(150) NOT NULL,
  `image_02` VARCHAR(150) DEFAULT NULL,
  `image_03` VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para `wishlist`
--
CREATE TABLE `wishlist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `pid` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_wishlist_user_idx` (`user_id`),
  KEY `fk_wishlist_product_idx` (`pid`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`pid`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para `cart`
--
CREATE TABLE `cart` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `pid` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL CHECK (`quantity` > 0),
  `image` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cart_user_idx` (`user_id`),
  KEY `fk_cart_product_idx` (`pid`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`pid`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para `messages`
--
CREATE TABLE `messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `number` VARCHAR(20) NOT NULL,
  `message` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_message_user_idx` (`user_id`),
  CONSTRAINT `fk_message_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para `orders`
--
CREATE TABLE `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `number` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `method` VARCHAR(50) NOT NULL,
  `address` VARCHAR(500) NOT NULL,
  `total_products` VARCHAR(1000) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `placed_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `fk_order_user_idx` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- AUTO_INCREMENT para tablas
--
ALTER TABLE `admins`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `users`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;

ALTER TABLE `wishlist`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;

ALTER TABLE `cart`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;

ALTER TABLE `messages`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;

COMMIT;

 /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



