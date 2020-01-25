-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.3.13-MariaDB-log - mariadb.org binary distribution
-- Операционная система:         Win64
-- HeidiSQL Версия:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных
CREATE DATABASE IF NOT EXISTS `%YOU_DATABASE_NAME%` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `%YOU_DATABASE_NAME%`;

-- Дамп структуры для таблица account_types
CREATE TABLE IF NOT EXISTS `account_types` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список доступных полномочий (юзер/админ)';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_add
CREATE TABLE IF NOT EXISTS `operation_add` (
  `operation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `count` float NOT NULL DEFAULT 0,
  `create_date` date NOT NULL,
  `expire_date` date NOT NULL,
  `milk_fat` float DEFAULT NULL,
  `milk_solidity` float DEFAULT NULL,
  `milk_acidity` float DEFAULT NULL,
  KEY `FK_operation_add_operation_history` (`operation_id`),
  KEY `FK_operation_add_product_list` (`product_id`),
  CONSTRAINT `FK_operation_add_operation_history` FOREIGN KEY (`operation_id`) REFERENCES `operation_history` (`operation_id`),
  CONSTRAINT `FK_operation_add_product_list` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список добавленных в приходе позиций';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_history
CREATE TABLE IF NOT EXISTS `operation_history` (
  `operation_id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_type` int(11) NOT NULL,
  `document_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_date` date NOT NULL,
  `partner_code` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_code` int(11) NOT NULL,
  PRIMARY KEY (`operation_id`),
  UNIQUE KEY `document_number` (`document_number`),
  KEY `FK_operation_history_operation_types` (`operation_type`),
  KEY `FK_operation_history_partners` (`partner_code`),
  KEY `FK_operation_history_users` (`user_code`),
  CONSTRAINT `FK_operation_history_operation_types` FOREIGN KEY (`operation_type`) REFERENCES `operation_types` (`operation_type_id`),
  CONSTRAINT `FK_operation_history_partners` FOREIGN KEY (`partner_code`) REFERENCES `partners` (`partner_id`),
  CONSTRAINT `FK_operation_history_users` FOREIGN KEY (`user_code`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='история операций (поступления/продажи/производства)';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_inventory
CREATE TABLE IF NOT EXISTS `operation_inventory` (
  `operation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `create_date` date NOT NULL,
  `count_before` float NOT NULL,
  `count_after` float NOT NULL,
  KEY `FK_operation_inventory_operation_history` (`operation_id`),
  KEY `FK_operation_inventory_product_list` (`product_id`),
  CONSTRAINT `FK_operation_inventory_operation_history` FOREIGN KEY (`operation_id`) REFERENCES `operation_history` (`operation_id`),
  CONSTRAINT `FK_operation_inventory_product_list` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='история списаний/пополнений по результатам инвентаризаций';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_prod_add
CREATE TABLE IF NOT EXISTS `operation_prod_add` (
  `operation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `count` float NOT NULL DEFAULT 0,
  `create_date` date NOT NULL,
  `expire_date` date NOT NULL,
  KEY `FK_operation_prod_add_operation_history` (`operation_id`),
  KEY `FK_operation_prod_add_product_list` (`product_id`),
  CONSTRAINT `FK_operation_prod_add_operation_history` FOREIGN KEY (`operation_id`) REFERENCES `operation_history` (`operation_id`),
  CONSTRAINT `FK_operation_prod_add_product_list` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список добавленных в производстве позиций';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_prod_consume
CREATE TABLE IF NOT EXISTS `operation_prod_consume` (
  `operation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `count` float NOT NULL DEFAULT 0,
  `create_date` date NOT NULL,
  `expire_date` date NOT NULL,
  KEY `FK_operation_prod_consume_operation_history` (`operation_id`),
  KEY `FK_operation_prod_consume_product_list` (`product_id`),
  CONSTRAINT `FK_operation_prod_consume_operation_history` FOREIGN KEY (`operation_id`) REFERENCES `operation_history` (`operation_id`),
  CONSTRAINT `FK_operation_prod_consume_product_list` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список израсходованного в производстве сырья';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_sell
CREATE TABLE IF NOT EXISTS `operation_sell` (
  `operation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `count` float NOT NULL DEFAULT 0,
  `create_date` date NOT NULL,
  `expire_date` date NOT NULL,
  KEY `FK_operaion_sell_operation_history` (`operation_id`),
  KEY `FK_operation_sell_product_list` (`product_id`),
  CONSTRAINT `FK_operaion_sell_operation_history` FOREIGN KEY (`operation_id`) REFERENCES `operation_history` (`operation_id`),
  CONSTRAINT `FK_operation_sell_product_list` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список проданных в продаже позиций';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица operation_types
CREATE TABLE IF NOT EXISTS `operation_types` (
  `operation_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`operation_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица с типами операций (пока их 3: приход, продажа, производство)';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица partners
CREATE TABLE IF NOT EXISTS `partners` (
  `partner_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kpp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`partner_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица контрагентов';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица product_list
CREATE TABLE IF NOT EXISTS `product_list` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_code` int(11) DEFAULT NULL,
  `capacity` float NOT NULL COMMENT 'объем или "сколько литров в единице товара"',
  `gtin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_type` int(11) DEFAULT NULL,
  `valid_days` int(11) DEFAULT NULL COMMENT 'срок годности (всегда в сутках)',
  `extended_milk_fields` bit(1) DEFAULT b'0' COMMENT 'Обладает ли дополнительными свойствами молока (Bool 1 или 0)',
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `title` (`title`),
  KEY `FK_product_list_units` (`unit_code`),
  KEY `FK_product_list_product_types` (`product_type`),
  CONSTRAINT `FK_product_list_product_types` FOREIGN KEY (`product_type`) REFERENCES `product_types` (`type_id`),
  CONSTRAINT `FK_product_list_units` FOREIGN KEY (`unit_code`) REFERENCES `units` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список номенклатур с их общими свойствами (в роли справочника)';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица product_registry
CREATE TABLE IF NOT EXISTS `product_registry` (
  `registry_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `count` float NOT NULL DEFAULT 0,
  `create_date` date NOT NULL,
  `expire_date` date NOT NULL,
  PRIMARY KEY (`registry_id`),
  KEY `FK_product_registry_product_list` (`product_id`),
  CONSTRAINT `FK_product_registry_product_list` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='реестр продукции (складской журнал)';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица product_types
CREATE TABLE IF NOT EXISTS `product_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='типы продуктов (готовая/полуфабрикат/сырье)';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица units
CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`unit_id`),
  UNIQUE KEY `unit` (`unit`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица с единицами измерения, их краткая форма и полное название';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`),
  KEY `FK_users_account_types` (`access_id`),
  CONSTRAINT `FK_users_account_types` FOREIGN KEY (`access_id`) REFERENCES `account_types` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица с аккаунтами пользователей';

INSERT INTO `users` (`user_id`, `login`, `password`, `access_id`, `name`, `lastname`) VALUES (1, 'admin', 'haiEfouOeUYNU', 1, 'Тестовый', 'Администратор');

-- Экспортируемые данные не выделены.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
