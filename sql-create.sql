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


-- Дамп структуры базы данных reactive_record
CREATE DATABASE IF NOT EXISTS `reactive_record` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `reactive_record`;

-- Дамп структуры для таблица reactive_record.account_types
CREATE TABLE IF NOT EXISTS `account_types` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список доступных полномочий (юзер/админ)';

-- Дамп данных таблицы reactive_record.account_types: ~2 rows (приблизительно)
DELETE FROM `account_types`;
/*!40000 ALTER TABLE `account_types` DISABLE KEYS */;
INSERT INTO `account_types` (`ID`, `title`) VALUES
	(1, 'admin'),
	(2, 'user');
/*!40000 ALTER TABLE `account_types` ENABLE KEYS */;

-- Дамп структуры для таблица reactive_record.partners
CREATE TABLE IF NOT EXISTS `partners` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kpp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица контрагентов';

-- Дамп данных таблицы reactive_record.partners: ~3 rows (приблизительно)
DELETE FROM `partners`;
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;
INSERT INTO `partners` (`ID`, `name`, `inn`, `kpp`, `comment`) VALUES
	(1, 'ООО "Молочный поставщик"', '7777777777', '777771001', ''),
	(2, 'ИП Володянкин', '7800000004', '', ''),
	(3, 'АО "Рубин"', '7111111117', '771111101', NULL);
/*!40000 ALTER TABLE `partners` ENABLE KEYS */;

-- Дамп структуры для таблица reactive_record.product_list
CREATE TABLE IF NOT EXISTS `product_list` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_code` int(11) DEFAULT NULL,
  `capacity` float DEFAULT NULL,
  `gtin` bigint(20) DEFAULT NULL,
  `product_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_product_list_units` (`unit_code`),
  KEY `FK_product_list_product_types` (`product_type`),
  CONSTRAINT `FK_product_list_product_types` FOREIGN KEY (`product_type`) REFERENCES `product_types` (`ID`),
  CONSTRAINT `FK_product_list_units` FOREIGN KEY (`unit_code`) REFERENCES `units` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='список номенклатур';

-- Дамп данных таблицы reactive_record.product_list: ~5 rows (приблизительно)
DELETE FROM `product_list`;
/*!40000 ALTER TABLE `product_list` DISABLE KEYS */;
INSERT INTO `product_list` (`ID`, `title`, `unit_code`, `capacity`, `gtin`, `product_type`) VALUES
	(2, 'Биокефир Домодедовский жир. 1% (930гр)', 1, 0.93, 4630008891087, 1),
	(3, 'Йогурт Домодедовский ВИШНЯ жир. 2,7% (250гр)', 1, 0.25, 4630008890882, 1),
	(4, 'Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр', 1, 0.25, 4630008890998, 1),
	(5, 'Йогурт Домодедовский КЛУБНИКА жир. 2,7% (250гр)', 1, 0.25, 4630008890929, 1),
	(6, 'Йогурт Домодедовский ПЕРСИК жир. 2,7% (250гр)', 1, 0.25, 4630008890936, 1);
/*!40000 ALTER TABLE `product_list` ENABLE KEYS */;

-- Дамп структуры для таблица reactive_record.product_registry
CREATE TABLE IF NOT EXISTS `product_registry` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='реестр продукции (складской журнал)';

-- Дамп данных таблицы reactive_record.product_registry: ~0 rows (приблизительно)
DELETE FROM `product_registry`;
/*!40000 ALTER TABLE `product_registry` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_registry` ENABLE KEYS */;

-- Дамп структуры для таблица reactive_record.product_types
CREATE TABLE IF NOT EXISTS `product_types` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='типы продуктов (готовая/полуфабрикат/сырье)';

-- Дамп данных таблицы reactive_record.product_types: ~2 rows (приблизительно)
DELETE FROM `product_types`;
/*!40000 ALTER TABLE `product_types` DISABLE KEYS */;
INSERT INTO `product_types` (`ID`, `type`) VALUES
	(1, 'Готовая продукция'),
	(2, 'Полуфабрикат'),
	(3, 'Сырье');
/*!40000 ALTER TABLE `product_types` ENABLE KEYS */;

-- Дамп структуры для таблица reactive_record.units
CREATE TABLE IF NOT EXISTS `units` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unit` (`unit`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица с единицами измерения, их краткая форма и полное название';

-- Дамп данных таблицы reactive_record.units: ~3 rows (приблизительно)
DELETE FROM `units`;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` (`ID`, `unit`, `full_title`) VALUES
	(1, 'шт', 'штука'),
	(2, 'кг', 'килограмм'),
	(3, 'л', 'литр');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;

-- Дамп структуры для таблица reactive_record.users
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `login` (`login`),
  KEY `FK_users_account_types` (`access_id`),
  CONSTRAINT `FK_users_account_types` FOREIGN KEY (`access_id`) REFERENCES `account_types` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='таблица с аккаунтами пользователей';

-- Дамп данных таблицы reactive_record.users: ~1 rows (приблизительно)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`ID`, `login`, `password`, `access_id`, `name`, `lastname`) VALUES
	(1, 'admin', 'admin', 1, 'Анатолий', 'Осипов');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
