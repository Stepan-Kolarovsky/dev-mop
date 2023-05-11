-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'česká republika',
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `house_number` int NOT NULL,
  `psc` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `address` (`id`, `user_id`, `country`, `city`, `street`, `house_number`, `psc`) VALUES
(2,	8,	'Česká republika',	'Baziny',	'Srekova',	420,	42069),
(3,	10,	'česká republika',	'konarovice',	'nevim',	69,	28151);

DROP TABLE IF EXISTS `cards`;
CREATE TABLE `cards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `expiration` varchar(5) COLLATE utf8mb4_bin DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `cvc` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `cards` (`id`, `user_id`, `number`, `expiration`, `name`, `cvc`) VALUES
(1,	8,	'1111',	'2019',	'zakop',	151);

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `categories` (`id`, `name`) VALUES
(1,	'Sport Cars'),
(2,	'Luxury Cars'),
(3,	'Hyper Cars');

DROP TABLE IF EXISTS `order_products`;
CREATE TABLE `order_products` (
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_quantity` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `order_products_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `order_products` (`order_id`, `product_id`, `product_quantity`, `created_at`) VALUES
(38,	2,	2,	'2022-12-13 09:35:37'),
(38,	4,	2,	'2022-12-13 09:35:41'),
(40,	2,	1,	'2023-01-07 16:01:57'),
(41,	2,	2,	'2023-01-17 08:16:35'),
(41,	4,	1,	'2023-01-17 08:16:37'),
(42,	2,	1,	'2023-01-17 08:17:43'),
(44,	2,	1,	'2023-02-08 16:37:59'),
(44,	8,	1,	'2023-02-08 16:38:00'),
(43,	2,	1,	'2023-04-04 11:48:59'),
(43,	8,	1,	'2023-04-04 11:49:06'),
(46,	9,	1,	'2023-04-26 13:11:40');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `payment_metod` int DEFAULT NULL,
  `is_finished` int NOT NULL DEFAULT '0',
  `completed` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `orders` (`id`, `user_id`, `created_at`, `payment_metod`, `is_finished`, `completed`) VALUES
(38,	8,	'2022-12-13 09:24:12',	NULL,	1,	0),
(40,	6,	'2022-12-13 11:06:51',	NULL,	0,	0),
(41,	8,	'2023-01-17 08:16:29',	2,	1,	0),
(42,	8,	'2023-01-17 08:17:20',	2,	1,	1),
(43,	8,	'2023-01-17 08:17:54',	2,	1,	0),
(44,	10,	'2023-01-18 00:11:39',	1,	1,	1),
(45,	10,	'2023-02-08 16:38:24',	NULL,	0,	0),
(46,	8,	'2023-04-04 11:49:55',	1,	0,	0);

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `prize` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `products` (`id`, `name`, `description`, `prize`, `category_id`, `created_at`, `img`) VALUES
(2,	'Bmw e70 x5',	'x5 m50d',	12,	2,	'2022-11-15 00:00:00',	'upload/bmw-bmw-white-wheels-wallpaper-preview.jpeg'),
(4,	'product88',	'mozna',	63,	1,	'2022-11-15 00:00:00',	'upload/maxresdefault.jpeg'),
(7,	'Lamborghini',	'lorem',	200000,	1,	'2022-11-15 00:00:00',	'upload/lamborghini.jpeg'),
(8,	'Mercedes S class',	'mansory',	250000,	2,	'2023-02-08 00:00:00',	'upload/mansorysclass.webp'),
(9,	'Bmw M5 CS',	'G-Power version',	130000,	1,	'2023-04-17 15:56:38',	'upload/bmw-m5-gpower.webp'),
(10,	'Bmw M5 F10',	'4.4L V8',	40000,	1,	'2023-04-17 15:58:14',	'upload/2015-BMW-M5-F10-Featured-1.jpeg');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sub` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `given_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `family_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `password` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `role` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT 'user',
  `last_post` datetime DEFAULT NULL,
  `active` tinyint DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

INSERT INTO `users` (`id`, `sub`, `username`, `given_name`, `family_name`, `picture`, `email`, `password`, `role`, `last_post`, `active`) VALUES
(6,	'108342205840343758931',	'108342205840343758931',	'Štěpán',	'Kolarovský',	'https://lh3.googleusercontent.com/a-/ACNPEu_khwSHyhZBOQVGeriIJChvmxpeNKRvDyzcjmJ_=s96-c',	'stepan.kolarovsky2004@gmail.com',	'$2y$10$eMY7f.a49c8f/yWZkk7IeO.cgf2vEphSJZZO.VMcsG3iclj9x50dG',	'user',	NULL,	1),
(7,	NULL,	'digimon',	NULL,	NULL,	NULL,	'digimonov@necum.cz',	'$2y$10$DutOznhsfO.BZWmtmdm5rOMiN5cNhpkyty93CihxKXuc2/OwLxAre',	'user',	NULL,	1),
(8,	NULL,	'franta',	'Jonas',	'Skocildopole',	'upload/dig.png',	'mraz@coe.cz',	'$2y$10$3TZnh1XANpNZ.XwSJIfy4enKogNsdbrBgSviZnW5WoQYGkabVTfHe',	'admin',	NULL,	1),
(10,	NULL,	'mihal',	'miguel',	'naskocil',	NULL,	'mihal@vole.com',	'$2y$10$2ClGEis4ATD4q6ISEZc7VeYyXll/2jfdvraO2.xA.kscNgaG9DIkO',	'user',	NULL,	0);

-- 2023-05-05 05:46:31