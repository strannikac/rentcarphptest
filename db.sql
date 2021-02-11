-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 11, 2021 at 12:29 PM
-- Server version: 10.2.36-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dreamsfm_pg`
--

-- --------------------------------------------------------

--
-- Table structure for table `basket`
--

CREATE TABLE `basket` (
  `id` bigint(20) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `price` decimal(9,2) NOT NULL DEFAULT 0.00,
  `one_time_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `holidays` text NOT NULL DEFAULT '',
  `holidays_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `discount_days_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(9,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `basket`
--

INSERT INTO `basket` (`id`, `session_id`, `car_id`, `start_date`, `end_date`, `price`, `one_time_fee`, `holidays`, `holidays_fee`, `discount_days_fee`, `final_price`) VALUES
(48, '3a3713e37e7631613027896379f141829c74', 2, '2021-02-11', '2021-02-11', '50.00', '0.00', '', '0.00', '0.00', '50.00'),
(49, '3a3713e37e7631613027896379f141829c74', 3, '2021-02-11', '2021-02-13', '150.00', '0.00', '2021-02-13', '10.00', '0.00', '160.00');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `sku` varchar(35) NOT NULL DEFAULT '' COMMENT 'articul, unique string in owner system for this car',
  `country_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `producer_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `color_id` int(11) DEFAULT NULL,
  `model` varchar(75) NOT NULL DEFAULT '',
  `title` varchar(75) NOT NULL DEFAULT '',
  `year` year(4) NOT NULL DEFAULT 2000,
  `pos` int(11) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `sku`, `country_id`, `region_id`, `producer_id`, `category_id`, `color_id`, `model`, `title`, `year`, `pos`, `status_id`) VALUES
(1, 'mini-cooper-se-1', NULL, NULL, 5, 1, 9, 'COOPER SE', 'MINI COOPER SE', 2020, 10, 1),
(2, 'toyota-rav4-1', 201, NULL, 1, 1, 1, 'RAV4', 'TOYOTA RAV4 BLACK', 2021, 20, 1),
(3, 'bmv-x7-1', 201, 1002, 2, 1, 7, 'X7', 'BMW X7 METALLIC', 2021, 30, 1),
(4, 'man-lions-city-1', 201, 1001, 6, 2, 8, 'Lion\'s City', 'MAN Lion\'s City', 2021, 40, 1),
(5, 'volvo-fm-d11-4x2-box-1', NULL, NULL, 4, 3, 7, 'FM D11 4x2 Box', 'Volvo FM D11 4x2 Box', 2020, 50, 1),
(6, 'john-deere-1010k-1', 201, NULL, 7, 4, 6, '1050K', 'John Deere 1050K', 2019, 60, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cars_categories`
--

CREATE TABLE `cars_categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `pos` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `day_price` decimal(9,2) NOT NULL COMMENT 'price per day',
  `one_time_fee` decimal(9,2) NOT NULL,
  `renewal_fee` decimal(9,2) NOT NULL,
  `holiday_coef_percent` tinyint(4) NOT NULL,
  `max_days` tinyint(4) NOT NULL,
  `min_days` tinyint(4) NOT NULL,
  `discount_days` tinyint(4) NOT NULL,
  `discount_add_days` tinyint(4) NOT NULL,
  `termination_penalty_percent` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_categories`
--

INSERT INTO `cars_categories` (`id`, `parent_id`, `title`, `pos`, `status_id`, `day_price`, `one_time_fee`, `renewal_fee`, `holiday_coef_percent`, `max_days`, `min_days`, `discount_days`, `discount_add_days`, `termination_penalty_percent`) VALUES
(1, 0, 'Everyday transport', 10, 1, '50.00', '0.00', '10.00', 20, 0, 1, 30, 3, 0),
(2, 0, 'For transporting passengers', 20, 1, '100.00', '20.00', '10.00', 20, 0, 1, 0, 0, 0),
(3, 0, 'Transport for the carriage of goods', 30, 1, '150.00', '40.00', '10.00', 20, 30, 1, 0, 0, 0),
(4, 0, 'Special equipment', 40, 1, '200.00', '100.00', '0.00', 0, 0, 10, 0, 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `cars_categories_langs`
--

CREATE TABLE `cars_categories_langs` (
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_categories_langs`
--

INSERT INTO `cars_categories_langs` (`category_id`, `language_id`, `title`, `descr`) VALUES
(1, 1, 'Everyday transport', 'Everyday transport description or html...'),
(1, 2, 'Igapäevane transport', 'Igapäevane transport description or html...'),
(1, 3, 'Повседневный транспорт', 'Повседневный транспорт description or html...'),
(2, 1, 'For transporting passengers', 'For transporting passengers description or html...'),
(2, 2, 'Transport reisijate veoks', 'Transport reisijate veoks description or html...'),
(2, 3, 'Транспорт для перевозки пасажиров', 'Транспорт для перевозки пасажиров description or html...'),
(3, 1, 'Transport for the carriage of goods', 'Transport for the carriage of goods description or html...'),
(3, 2, 'Transport kaupade veoks', 'Transport kaupade veoks description or html...'),
(3, 3, 'Транспорт для перевозки грузов', 'Транспорт для перевозки грузов description or html...'),
(4, 1, 'Special equipment', 'Special equipment description or html...'),
(4, 2, 'Erivarustus', 'Erivarustus description or html...'),
(4, 3, 'Спецтехника', 'Спецтехника description or html...');

-- --------------------------------------------------------

--
-- Table structure for table `cars_colors`
--

CREATE TABLE `cars_colors` (
  `id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `pos` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_colors`
--

INSERT INTO `cars_colors` (`id`, `title`, `pos`, `status_id`) VALUES
(1, 'Black', 10, 1),
(2, 'White', 20, 1),
(3, 'Green', 30, 1),
(4, 'Red', 40, 1),
(5, 'Blue', 50, 1),
(6, 'Yellow', 60, 1),
(7, 'Metallic', 70, 1),
(8, 'Light gray', 80, 1),
(9, 'Dark blue', 90, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cars_colors_langs`
--

CREATE TABLE `cars_colors_langs` (
  `color_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_colors_langs`
--

INSERT INTO `cars_colors_langs` (`color_id`, `language_id`, `title`) VALUES
(1, 1, 'Black'),
(1, 2, 'Must'),
(1, 3, 'Черный'),
(2, 1, 'White'),
(2, 2, 'Valge'),
(2, 3, 'Белый'),
(3, 1, 'Green'),
(3, 2, 'Roheline'),
(3, 3, 'Зеленый'),
(4, 1, 'Red'),
(4, 2, 'Punane'),
(4, 3, 'Красный'),
(5, 1, 'Blue'),
(5, 2, 'Sinine'),
(5, 3, 'Синий'),
(6, 1, 'Yellow'),
(6, 2, 'Kollane'),
(6, 3, 'Желтый'),
(7, 1, 'Metallic'),
(7, 2, 'Metallik'),
(7, 3, 'Металлик'),
(8, 1, 'Light gray'),
(8, 2, 'Helehall'),
(8, 3, 'Светло-серый'),
(9, 1, 'Dark blue'),
(9, 2, 'Tumesinine'),
(9, 3, 'Темно-синий');

-- --------------------------------------------------------

--
-- Table structure for table `cars_langs`
--

CREATE TABLE `cars_langs` (
  `car_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_langs`
--

INSERT INTO `cars_langs` (`car_id`, `language_id`, `title`) VALUES
(1, 1, 'MINI COOPER SE '),
(1, 2, 'MINI COOPER SE '),
(1, 3, 'MINI COOPER SE '),
(2, 1, 'Toyota Rav4 Black'),
(2, 2, 'Toyota Rav4 Must'),
(2, 3, 'TOYOTA RAV4 Черный'),
(3, 1, 'BMW X7 Metallic'),
(3, 2, 'BMW X7 Metallic'),
(3, 3, 'BMW X7 Металик'),
(4, 1, 'MAN Lion\'s City'),
(4, 2, 'MAN Lion\'s City'),
(4, 3, 'MAN Lion\'s City'),
(5, 1, 'Volvo FM D11 4x2 Box'),
(5, 2, 'Volvo FM D11 4x2 Box'),
(5, 3, 'Volvo FM D11 4x2 Box'),
(6, 1, 'John Deere 1050K'),
(6, 2, 'John Deere 1050K'),
(6, 3, 'John Deere 1050K');

-- --------------------------------------------------------

--
-- Table structure for table `cars_producers`
--

CREATE TABLE `cars_producers` (
  `id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `pos` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_producers`
--

INSERT INTO `cars_producers` (`id`, `title`, `pos`, `status_id`) VALUES
(1, 'Toyota', 10, 1),
(2, 'BMW', 20, 1),
(3, 'Mercedes', 30, 1),
(4, 'Volvo', 40, 1),
(5, 'MINI', 50, 1),
(6, 'MAN', 60, 1),
(7, 'John Deere', 70, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cars_producers_langs`
--

CREATE TABLE `cars_producers_langs` (
  `producer_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_producers_langs`
--

INSERT INTO `cars_producers_langs` (`producer_id`, `language_id`, `title`, `descr`) VALUES
(1, 1, 'Toyota', 'Toyota description or html...'),
(1, 2, 'Toyota', 'Toyota description voi html...'),
(1, 3, 'Toyota', 'Toyota описание или html...'),
(2, 1, 'BMW', 'BMW description or html...'),
(2, 2, 'BMW', 'BMW description voi html...'),
(2, 3, 'BMW', 'BMW описание или html...'),
(3, 1, 'Mercedes', 'Mercedes description or html...'),
(3, 2, 'Mercedes', 'Mercedes description voi html...'),
(3, 3, 'Mercedes', 'Mercedes описание или html...'),
(4, 1, 'Volvo', 'Volvo description or html...'),
(4, 2, 'Volvo', 'Volvo description voi html...'),
(4, 3, 'Volvo', 'Volvo описание или html...'),
(5, 1, 'MINI', 'MINI description or html...'),
(5, 2, 'MINI', 'MINI description voi html...'),
(5, 3, 'MINI', 'MINI описание или html...'),
(6, 1, 'MAN', 'MAN description or html...'),
(6, 2, 'MAN', 'MAN description voi html...'),
(6, 3, 'MAN', 'MAN описание или html...'),
(7, 1, 'John Deere', 'John Deere description or html...'),
(7, 2, 'John Deere', 'John Deere description voi html...'),
(7, 3, 'John Deere', 'John Deere описание или html...');

-- --------------------------------------------------------

--
-- Table structure for table `cars_statuses`
--

CREATE TABLE `cars_statuses` (
  `id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `pos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_statuses`
--

INSERT INTO `cars_statuses` (`id`, `title`, `pos`) VALUES
(1, 'Available', 10),
(2, 'Not available', 20),
(3, 'Rented', 30),
(4, 'Under repair', 40);

-- --------------------------------------------------------

--
-- Table structure for table `cars_statuses_langs`
--

CREATE TABLE `cars_statuses_langs` (
  `status_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cars_statuses_langs`
--

INSERT INTO `cars_statuses_langs` (`status_id`, `language_id`, `title`, `descr`) VALUES
(1, 1, 'Available', 'Available description or html...'),
(1, 2, 'Saadaval', 'Saadaval description or html...'),
(1, 3, 'Доступен', 'Доступен description or html...'),
(2, 1, 'Not available', 'Not available description or html...'),
(2, 2, 'Pole saadaval', 'Pole saadaval description or html...'),
(2, 3, 'Недоступен', 'Недоступен description or html...'),
(3, 1, 'Rented', 'Rented description or html...'),
(3, 2, 'Üüritud', 'Üüritud description or html...'),
(3, 3, 'Арендован', 'Арендован description or html...'),
(4, 1, 'Under repair', 'Under repair description or html...'),
(4, 2, 'Remondis', 'Remondis description or html...'),
(4, 3, 'В ремонте', 'В ремонте description or html...');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `iso` varchar(3) NOT NULL,
  `iso2` varchar(2) NOT NULL,
  `title` varchar(35) NOT NULL,
  `pos` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `iso`, `iso2`, `title`, `pos`, `status_id`) VALUES
(101, 'fin', 'fi', 'Finland', 900, 1),
(201, 'est', 'ee', 'Estonia', 1000, 1),
(301, 'lva', 'lv', 'Latvia', 2000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `countries_langs`
--

CREATE TABLE `countries_langs` (
  `country_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries_langs`
--

INSERT INTO `countries_langs` (`country_id`, `language_id`, `title`) VALUES
(101, 1, 'Finland'),
(101, 2, 'Soome'),
(101, 3, 'Финляндия'),
(201, 1, 'Estonia'),
(201, 2, 'Eestimaa'),
(201, 3, 'Эстония'),
(301, 1, 'Latvia'),
(301, 2, 'Läti'),
(301, 3, 'Латвия');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `iso` varchar(5) NOT NULL,
  `sign` varchar(5) NOT NULL,
  `title` varchar(15) NOT NULL,
  `pattern` varchar(25) NOT NULL,
  `in_euro` decimal(9,2) NOT NULL,
  `is_def` tinyint(4) NOT NULL,
  `pos` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `iso`, `sign`, `title`, `pattern`, `in_euro`, `is_def`, `pos`, `status_id`) VALUES
(1, 'eur', '€', 'Euro', '', '1.00', 1, 10, 1),
(2, 'usd', '$', 'US dollar', '', '0.83', 0, 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `currencies_langs`
--

CREATE TABLE `currencies_langs` (
  `currency_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `currencies_langs`
--

INSERT INTO `currencies_langs` (`currency_id`, `language_id`, `title`) VALUES
(1, 1, 'Euro'),
(1, 2, 'Euro'),
(1, 3, 'Евро'),
(2, 1, 'US dollar'),
(2, 2, 'US dollar'),
(2, 3, 'доллар США');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `country_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `lname` varchar(75) NOT NULL DEFAULT '',
  `fname` varchar(75) NOT NULL DEFAULT '',
  `personal_code` varchar(15) NOT NULL DEFAULT '',
  `email` varchar(75) NOT NULL,
  `phone` varchar(25) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `incorrect_password_count` tinyint(4) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `orders` int(11) NOT NULL DEFAULT 0,
  `confirmed_email` tinyint(4) NOT NULL DEFAULT 0,
  `login_time` bigint(20) NOT NULL DEFAULT 0,
  `creation_time` bigint(20) NOT NULL DEFAULT 0,
  `update_time` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `role_id`, `country_id`, `region_id`, `language_id`, `currency_id`, `lname`, `fname`, `personal_code`, `email`, `phone`, `password`, `incorrect_password_count`, `status_id`, `orders`, `confirmed_email`, `login_time`, `creation_time`, `update_time`) VALUES
(2, 1, 201, 1002, 1, 1, '', 'Alex', '', 'ivkiev@ya.ru', '', '3ps4cPRiCC3+7vwAe03EkcoM+gU', 0, 1, 8, 1, 1613035654, 1612274192, 1612967048);

-- --------------------------------------------------------

--
-- Table structure for table `customers_roles`
--

CREATE TABLE `customers_roles` (
  `id` int(11) NOT NULL,
  `title` varchar(35) NOT NULL,
  `descr` text NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `pos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers_roles`
--

INSERT INTO `customers_roles` (`id`, `title`, `descr`, `status_id`, `pos`) VALUES
(1, 'Customer', 'General rules for customers', 1, 10),
(2, 'Manager', 'Manager of project', 1, 20),
(3, 'Admin', 'Full access', 3, 1000),
(4, 'Watcher', 'Only read (watch)', 1, 30);

-- --------------------------------------------------------

--
-- Table structure for table `customers_roles_langs`
--

CREATE TABLE `customers_roles_langs` (
  `role_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(25) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers_roles_langs`
--

INSERT INTO `customers_roles_langs` (`role_id`, `language_id`, `title`, `descr`) VALUES
(1, 1, 'Customer', 'General rules for customers'),
(1, 2, 'Customer', 'General rules for customers'),
(1, 3, 'Клиент', 'Обычные права для клиента'),
(2, 1, 'Manager', 'Manager of project'),
(2, 2, 'Manager', 'Manager of project'),
(2, 3, 'Менеджер', 'Менеджер проекта'),
(3, 1, 'Admin', 'Full access'),
(3, 2, 'Admin', 'Full access'),
(3, 3, 'Админ', 'Полный доступ'),
(4, 1, 'Guest', 'Only read (watch)'),
(4, 2, 'Guest', 'Only read (watch)'),
(4, 3, 'Гость', 'Только чтение (просмотр)');

-- --------------------------------------------------------

--
-- Table structure for table `customers_tokens`
--

CREATE TABLE `customers_tokens` (
  `customer_id` bigint(20) NOT NULL,
  `type_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers_tokens`
--

INSERT INTO `customers_tokens` (`customer_id`, `type_id`, `token`, `date`) VALUES
(2, 1, '10fdbe75-695853bc-5e1dba70-f7f04386', 1613045503),
(2, 3, '4d010f7eff563294b9bf7e6808c', 1612274192);

-- --------------------------------------------------------

--
-- Table structure for table `customers_tokens_types`
--

CREATE TABLE `customers_tokens_types` (
  `id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers_tokens_types`
--

INSERT INTO `customers_tokens_types` (`id`, `title`) VALUES
(1, 'common, authentication'),
(2, 'recovery'),
(3, 'registration (confirmation) code');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `iso` varchar(5) NOT NULL,
  `title` varchar(25) NOT NULL,
  `pos` int(11) NOT NULL,
  `is_def` tinyint(4) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `iso`, `title`, `pos`, `is_def`, `status_id`) VALUES
(1, 'en', 'English', 10, 1, 1),
(2, 'et', 'Eesti', 20, 0, 1),
(3, 'ru', 'Русский', 30, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `locales`
--

CREATE TABLE `locales` (
  `id` varchar(55) NOT NULL,
  `language_id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locales`
--

INSERT INTO `locales` (`id`, `language_id`, `value`) VALUES
('EMPTY_FILE_SELECTED', 1, 'Empty file selected'),
('EMPTY_FILE_SELECTED', 2, 'Empty file selected'),
('EMPTY_FILE_SELECTED', 3, 'Выбран пустой файл'),
('ERR_1000', 1, 'Unspecified Error.'),
('ERR_1000', 2, 'Täpsustamata viga'),
('ERR_1000', 3, 'Неизвестная ошибка.'),
('ERR_1002', 1, 'Request used forbidden controller or action.'),
('ERR_1002', 2, 'Taotlege kasutatud keelatud kontrollerit või toimingut.'),
('ERR_1002', 3, 'Запрос использует запрещенный контроллер или метод.'),
('ERR_1003', 1, 'The token is invalid or expired.'),
('ERR_1003', 2, 'Luba on vale või aegunud.'),
('ERR_1003', 3, 'Токен некорректен или устарел.'),
('ERR_1004', 1, 'The token expired.'),
('ERR_1004', 2, 'Luba aegus.'),
('ERR_1004', 3, 'Токен устарел.'),
('ERR_1005', 1, 'There are no data in the request.'),
('ERR_1005', 2, 'Taotluses pole andmeid.'),
('ERR_1005', 3, 'Нет данных в запросе.'),
('ERR_1006', 1, 'The allowed amount of data in the request is exceeded.'),
('ERR_1006', 2, 'Taotluses lubatud andmemaht on ületatud.'),
('ERR_1006', 3, 'Превышен допустимый объем данных в запросе.'),
('ERR_1007', 1, 'Failed to create a session.'),
('ERR_1007', 2, 'Seansi loomine ebaõnnestus.'),
('ERR_1007', 3, 'Ошибка создания сессии.'),
('ERR_1008', 1, 'Data didn\'t inserted in table.'),
('ERR_1008', 2, 'Andmeid ei lisatud tabelisse.'),
('ERR_1008', 3, 'Данные не были добавлены в таблицу.'),
('ERR_1009', 1, 'Data didn\'t updated in table.'),
('ERR_1009', 2, 'Andmeid ei värskendatud tabelis.'),
('ERR_1009', 3, 'Данные не были обновлены в таблице.'),
('ERR_1010', 1, 'Data didn\'t removed from table.'),
('ERR_1010', 2, 'Andmeid ei eemaldatud tabelist.'),
('ERR_1010', 3, 'Данные не были удалены из таблицы.'),
('ERR_1011', 1, 'Customer didn\'t found.'),
('ERR_1011', 2, 'Klienti ei leitud.'),
('ERR_1011', 3, 'Пользователь не найден.'),
('ERR_1012', 1, 'This item is already in your basket'),
('ERR_1012', 2, 'This item is already in your basket'),
('ERR_1012', 3, 'Этот элемент уже содержится в Вашей корзине'),
('ERR_1013', 1, 'Some date is unavailable for rent.'),
('ERR_1013', 2, 'Some date is unavailable for rent.'),
('ERR_1013', 3, 'Одна из дат недоступна для аренды.'),
('ERR_2000', 1, 'Identifier is empty.'),
('ERR_2000', 2, 'Identifier on tühi.'),
('ERR_2000', 3, 'Пустой идентификатор.'),
('ERR_2001', 1, 'There is no such identifier in the system.'),
('ERR_2001', 2, 'Sellist identifikaatorit süsteemis pole.'),
('ERR_2001', 3, 'Не существует такого идентификатора в системе.'),
('ERR_2002', 1, 'Device with such identifier is blocked.'),
('ERR_2002', 2, 'Sellise identifikaatoriga seade on blokeeritud.'),
('ERR_2002', 3, 'Устройство с таким идентификатором заблокировано.'),
('ERR_2003', 1, 'Terms of the identifier expired.'),
('ERR_2003', 2, 'Identifikaatori tingimused on aegunud.'),
('ERR_2003', 3, 'Правила для идентификатора устарели.'),
('ERR_2004', 1, 'The limit of using the current identifier without registration in the system is exceeded.'),
('ERR_2004', 2, 'Praeguse identifikaatori kasutamise piir ilma süsteemis registreerimiseta on ületatud.'),
('ERR_2004', 3, 'Превышен лимит использования текущего идентификатора без регистрации.'),
('ERR_2005', 1, 'E-mail is incorrect.'),
('ERR_2005', 2, 'E-post on vale.'),
('ERR_2005', 3, 'Некорректный e-mail.'),
('ERR_2006', 1, 'Password must be between 8 and 20 characters long.'),
('ERR_2006', 2, 'Parool peab olema 8–20 tähemärki pikk.'),
('ERR_2006', 3, 'Длина пароля должна быть от 8 до 20 символов.'),
('ERR_2007', 1, 'Password must contain at least 1 letter, at least 1 digit.'),
('ERR_2007', 2, 'Parool peab sisaldama vähemalt ühte tähte ja vähemalt ühte numbrit.'),
('ERR_2007', 3, 'Пароль должен содержать хотя бы 1 букву, хотя бы 1 цифру.'),
('ERR_2008', 1, 'First name is incorrect.'),
('ERR_2008', 2, 'Eesnimi on vale.'),
('ERR_2008', 3, 'Некорректное имя.'),
('ERR_2009', 1, 'Nick is incorrect.'),
('ERR_2009', 2, 'Nick on vale.'),
('ERR_2009', 3, 'Некорректный ник.'),
('ERR_2010', 1, 'Last name is incorrect.'),
('ERR_2010', 2, 'Perekonnanimi on vale.'),
('ERR_2010', 3, 'Некорректная фамилия.'),
('ERR_2011', 1, 'Check password.'),
('ERR_2011', 2, 'Kontrollige parooli.'),
('ERR_2011', 3, 'Проверьте пароль.'),
('ERR_2013', 1, 'Confirmation code is empty.'),
('ERR_2013', 2, 'Kinnituskood on tühi.'),
('ERR_2013', 3, 'Пустой код подтверждения.'),
('ERR_2014', 1, 'Confirmation code is incorrect or expired.'),
('ERR_2014', 2, 'Kinnituskood on vale või aegunud.'),
('ERR_2014', 3, 'Некорректный или устаревший код подтверждения.'),
('ERR_2015', 1, 'Entered e-mail exists.'),
('ERR_2015', 2, 'Sisestatud e-post on olemas.'),
('ERR_2015', 3, 'Такой e-mail уже существует.'),
('ERR_2016', 1, 'Entered nick name exists.'),
('ERR_2016', 2, 'Sisestatud hüüdnimi on olemas.'),
('ERR_2016', 3, 'Такой ник уже существует.'),
('ERR_2017', 1, 'You need to confirm your e-mail.'),
('ERR_2017', 2, 'Peate oma e-posti aadressi kinnitama.'),
('ERR_2017', 3, 'Пожалуйста, подтвердите своей e-mail.'),
('ERR_2018', 1, 'Check date for rent.'),
('ERR_2018', 2, 'Check date for rent.'),
('ERR_2018', 3, 'Проверьте дату аренды.'),
('ERR_3000', 1, 'Login or password is incorrect.'),
('ERR_3000', 2, 'Sisselogimine või parool on vale.'),
('ERR_3000', 3, 'Некорректный логин или пароль.'),
('ERR_3001', 1, 'The limit of login attempts is exceeded.'),
('ERR_3001', 2, 'Sisselogimiskatsete piir on ületatud.'),
('ERR_3001', 3, 'Превышено количество попыток войти в систему.'),
('ERR_3002', 1, 'Account is locked.'),
('ERR_3002', 2, 'Konto on lukus.'),
('ERR_3002', 3, 'Профиль заблокирован.'),
('ERR_3003', 1, 'Account is blacklisted.'),
('ERR_3003', 2, 'Konto on musta nimekirja kantud.'),
('ERR_3003', 3, 'Профиль в черном списке.'),
('ERR_3004', 1, 'Customer is self-excluded.'),
('ERR_3004', 2, 'Klient on ise välistatud.'),
('ERR_3004', 3, 'Пользователь самоисключен.'),
('ERR_3006', 1, 'Customer must accept the T&Cs.'),
('ERR_3006', 2, 'Klient peab nõustuma tingimustega.'),
('ERR_3006', 3, 'Пользователь должен принять правила и условия.'),
('ERR_3007', 1, 'The IP address is restricted.'),
('ERR_3007', 2, 'IP-aadress on piiratud.'),
('ERR_3007', 3, 'Этот IP адрес запрещен к использованию.'),
('ERR_3008', 1, 'The IMEI is restricted.'),
('ERR_3008', 2, 'IMEI on piiratud.'),
('ERR_3008', 3, 'Этот IMEI запрещен.'),
('ERR_3009', 1, 'Account does not exist.'),
('ERR_3009', 2, 'Kontot pole olemas.'),
('ERR_3009', 3, 'Такого профиля не существует.'),
('ERR_4000', 1, 'Permissions is denied.'),
('ERR_4000', 2, 'Lubadest keeldutakse.'),
('ERR_4000', 3, 'В доступе отказано.'),
('ERR_4001', 1, 'Request used forbidden method.'),
('ERR_4001', 2, 'Taotlege kasutatud keelatud meetodit.'),
('ERR_4001', 3, 'Запрос исползует запрещенный метод.'),
('ERR_4002', 1, 'Incorrect data for operation.'),
('ERR_4002', 2, 'Valed andmed töötamiseks.'),
('ERR_4002', 3, 'Некорректные данные для операции.'),
('ERR_4003', 1, 'File size is too large.'),
('ERR_4003', 2, 'Faili suurus on liiga suur.'),
('ERR_4003', 3, 'Размер файла слишком большой.'),
('ERR_4004', 1, 'File type or extension is invalid.'),
('ERR_4004', 2, 'Failitüüp või laiend on vale.'),
('ERR_4004', 3, 'Недопустимый тип или недопустимое расширение файла.'),
('ERR_404', 1, 'Error 404'),
('ERR_404', 2, 'Viga 404'),
('ERR_404', 3, 'Ошибка 404'),
('FILE_SIZE_IS_LARGER', 1, 'File size is larger than permissible, {SIZE} maximum'),
('FILE_SIZE_IS_LARGER', 2, 'File size is larger than permissible, {SIZE} maximum'),
('FILE_SIZE_IS_LARGER', 3, 'Размер файла больше допустимого, максимум {SIZE}'),
('HDR_CARS', 1, 'Cars for rent'),
('HDR_CARS', 2, 'Autode rentimine'),
('HDR_CARS', 3, 'Машины в аренду'),
('HDR_FORGOT_EMAIL', 1, 'Recovery password on site'),
('HDR_FORGOT_EMAIL', 2, 'Taastamise parool kohapeal'),
('HDR_FORGOT_EMAIL', 3, 'Восстановление пароля на сайте'),
('HDR_FORGOT_PASSWORD', 1, 'Forgot password'),
('HDR_FORGOT_PASSWORD', 2, 'Unustasid parooli'),
('HDR_FORGOT_PASSWORD', 3, 'Забыли пароль'),
('HDR_PROFILE', 1, 'My profile'),
('HDR_PROFILE', 2, 'Minu profiil'),
('HDR_PROFILE', 3, 'Мой профиль'),
('HDR_PROFILE_DATA', 1, 'My personal data'),
('HDR_PROFILE_DATA', 2, 'Minu isikuandmed'),
('HDR_PROFILE_DATA', 3, 'Мои личные данные'),
('HDR_PROFILE_PASSWORD', 1, 'Change password'),
('HDR_PROFILE_PASSWORD', 2, 'Muuda salasõna'),
('HDR_PROFILE_PASSWORD', 3, 'Изменить пароль'),
('HDR_RECOVERY_PASSWORD', 1, 'Recovery password'),
('HDR_RECOVERY_PASSWORD', 2, 'Taasteparool'),
('HDR_RECOVERY_PASSWORD', 3, 'Восстановление пароля'),
('HDR_SIGNIN', 1, 'Authentication'),
('HDR_SIGNIN', 2, 'Autentimine'),
('HDR_SIGNIN', 3, 'Аутентификация'),
('HDR_SIGNUP', 1, 'Registration'),
('HDR_SIGNUP', 2, 'Registreerimine'),
('HDR_SIGNUP', 3, 'Регистрация'),
('HDR_SIGNUP_COMPLETE', 1, 'Thank you for registration'),
('HDR_SIGNUP_COMPLETE', 2, 'Täname registreerumise eest'),
('HDR_SIGNUP_COMPLETE', 3, 'Спасибо за регистрацию'),
('HDR_SIGNUP_CONFIRM', 1, 'Confirmation was send on your e-mail'),
('HDR_SIGNUP_CONFIRM', 2, 'Teie e-mailile saadeti kinnitus'),
('HDR_SIGNUP_CONFIRM', 3, 'На вашу почту было выслано письмо для подтверждения'),
('HDR_SIGNUP_EMAIL', 1, 'Registration on site'),
('HDR_SIGNUP_EMAIL', 2, 'Registreerimine kohapeal'),
('HDR_SIGNUP_EMAIL', 3, 'Регистрация на сайте'),
('INVALID_FILE_FORMAT', 1, 'Incorrect file format, only allowed '),
('INVALID_FILE_FORMAT', 2, 'Incorrect file format, only allowed '),
('INVALID_FILE_FORMAT', 3, 'Неверный формат файла, допускается только '),
('SIGNUP_EMAIL_SUBJECT', 1, 'Registration on site'),
('STR_ABOUT_US', 1, 'About us'),
('STR_ABOUT_US', 2, 'Meist'),
('STR_ABOUT_US', 3, 'О нас'),
('STR_ACTIONS', 1, 'Actions'),
('STR_ACTIONS', 2, 'Actions'),
('STR_ACTIONS', 3, 'Действия'),
('STR_ALL', 1, 'All'),
('STR_ALL', 2, 'Kõik'),
('STR_ALL', 3, 'Все'),
('STR_ANY', 1, 'Any'),
('STR_ANY', 2, 'Ükskõik'),
('STR_ANY', 3, 'Любой'),
('STR_BACK_ORDERS', 1, 'Back to orders'),
('STR_BACK_ORDERS', 2, 'Back to orders'),
('STR_BACK_ORDERS', 3, 'Назад к заказам'),
('STR_BASE_PRICE', 1, 'Base price'),
('STR_BASE_PRICE', 2, 'Base price'),
('STR_BASE_PRICE', 3, 'Базовая цена'),
('STR_BASKET', 1, 'Basket'),
('STR_BASKET', 2, 'Korv'),
('STR_BASKET', 3, 'Корзина'),
('STR_CAR', 1, 'Car'),
('STR_CAR', 2, 'Auto'),
('STR_CAR', 3, 'Машина'),
('STR_CARS', 1, 'Cars'),
('STR_CARS', 2, 'Autod'),
('STR_CARS', 3, 'Машины'),
('STR_CATEGORY', 1, 'Category'),
('STR_CATEGORY', 2, 'Kategooria'),
('STR_CATEGORY', 3, 'Категория'),
('STR_CHECKOUT', 1, 'Checkout'),
('STR_CHECKOUT', 2, 'Checkout'),
('STR_CHECKOUT', 3, 'Оформить заказ'),
('STR_COLOR', 1, 'Color'),
('STR_COLOR', 2, 'Värv'),
('STR_COLOR', 3, 'Цвет'),
('STR_CONTACTS', 1, 'Contacts'),
('STR_CONTACTS', 2, 'Kontaktid'),
('STR_CONTACTS', 3, 'Контакты'),
('STR_CONTINUE', 1, 'Continue'),
('STR_CONTINUE', 2, 'Jätka'),
('STR_CONTINUE', 3, 'Продолжить'),
('STR_DATE', 1, 'Date'),
('STR_DATE', 2, 'Kuupäev'),
('STR_DATE', 3, 'Дата'),
('STR_DELETE', 1, 'Delete'),
('STR_DELETE', 2, 'Delete'),
('STR_DELETE', 3, 'Удалить'),
('STR_DETAILS', 1, 'Details'),
('STR_DETAILS', 2, 'Details'),
('STR_DETAILS', 3, 'Подробнее'),
('STR_DISCOUNT_DAYS_FEE', 1, 'Discount days fee'),
('STR_DISCOUNT_DAYS_FEE', 2, 'Discount days fee'),
('STR_DISCOUNT_DAYS_FEE', 3, 'Плата за дни скидки'),
('STR_DOWNLOAD_PDF', 1, 'Download pdf'),
('STR_DOWNLOAD_PDF', 2, 'Download pdf'),
('STR_DOWNLOAD_PDF', 3, 'Загрузить pdf'),
('STR_EMAIL', 1, 'E-mail'),
('STR_EMAIL', 2, 'E-post'),
('STR_EMAIL', 3, 'E-mail'),
('STR_FNAME', 1, 'First name'),
('STR_FNAME', 2, 'Eesnimi'),
('STR_FNAME', 3, 'Имя'),
('STR_FORGOT_PASSWORD', 1, 'Forgot password?'),
('STR_FORGOT_PASSWORD', 2, 'Unustasite parooli?'),
('STR_FORGOT_PASSWORD', 3, 'Забыли пароль?'),
('STR_HELP', 1, 'Help'),
('STR_HELP', 2, 'Abi'),
('STR_HELP', 3, 'Помощь'),
('STR_HOLIDAYS_FEE', 1, 'Holidays fee'),
('STR_HOLIDAYS_FEE', 2, 'Holidays fee'),
('STR_HOLIDAYS_FEE', 3, 'Плата за выходные'),
('STR_INVOICE', 1, 'Invoice'),
('STR_INVOICE', 2, 'Invoice'),
('STR_INVOICE', 3, 'Счет'),
('STR_LNAME', 1, 'Last name'),
('STR_LNAME', 2, 'Perekonnanimi'),
('STR_LNAME', 3, 'Фамилия'),
('STR_MODEL', 1, 'Model'),
('STR_MODEL', 2, 'Mudel'),
('STR_MODEL', 3, 'Модель'),
('STR_MY_ORDERS', 1, 'My orders'),
('STR_MY_ORDERS', 2, 'Minu tellimused'),
('STR_MY_ORDERS', 3, 'Мои заказы'),
('STR_MY_PROFILE', 1, 'My profile'),
('STR_MY_PROFILE', 2, 'Minu profiil'),
('STR_MY_PROFILE', 3, 'Мой профиль'),
('STR_NEW_PASSWORD', 1, 'New password'),
('STR_NEW_PASSWORD', 2, 'Uus salasõna'),
('STR_NEW_PASSWORD', 3, 'Новый пароль'),
('STR_ONE_TIME_FEE', 1, 'One time fee'),
('STR_ONE_TIME_FEE', 2, 'One time fee'),
('STR_ONE_TIME_FEE', 3, 'Единовременная плата'),
('STR_ORDER_DATE', 1, 'Order date'),
('STR_ORDER_DATE', 2, 'Order date'),
('STR_ORDER_DATE', 3, 'Дата заказа'),
('STR_PASSWORD', 1, 'Password'),
('STR_PASSWORD', 2, 'Parool'),
('STR_PASSWORD', 3, 'Пароль'),
('STR_PERSONAL_CODE', 1, 'Personal code'),
('STR_PERSONAL_CODE', 2, 'Isikukood'),
('STR_PERSONAL_CODE', 3, 'Личный код'),
('STR_PHONE', 1, 'Phone'),
('STR_PHONE', 2, 'Telefon'),
('STR_PHONE', 3, 'Телефон'),
('STR_PRICE', 1, 'Price'),
('STR_PRICE', 2, 'Hind'),
('STR_PRICE', 3, 'Цена'),
('STR_PRODUCER', 1, 'Manufacturer'),
('STR_PRODUCER', 2, 'Tootja'),
('STR_PRODUCER', 3, 'Производитель'),
('STR_PROFILE', 1, 'Profile'),
('STR_PROFILE', 2, 'Profiil'),
('STR_PROFILE', 3, 'Профиль'),
('STR_RECOVERY_PASSWORD', 1, 'Recovery password'),
('STR_RECOVERY_PASSWORD', 2, 'Recovery password'),
('STR_RECOVERY_PASSWORD', 3, 'Восстановить пароль'),
('STR_RENEW', 1, 'Renew'),
('STR_RENEW', 2, 'Renew'),
('STR_RENEW', 3, 'Продлить'),
('STR_RENEWAL_FEE', 1, 'Renewal fee'),
('STR_RENEWAL_FEE', 2, 'Renewal fee'),
('STR_RENEWAL_FEE', 3, 'Плата за продление'),
('STR_RENEW_ORDER', 1, 'Renew order'),
('STR_RENEW_ORDER', 2, 'Renew order'),
('STR_RENEW_ORDER', 3, 'Продлить заказ'),
('STR_RENT', 1, 'Rent'),
('STR_RENT', 2, 'Rentima'),
('STR_RENT', 3, 'Арендовать'),
('STR_RENT_DATE', 1, 'Rent date'),
('STR_RENT_DATE', 2, 'Rent date'),
('STR_RENT_DATE', 3, 'Дата аренды'),
('STR_RESULTS', 1, 'Results'),
('STR_RESULTS', 2, 'Tulemused'),
('STR_RESULTS', 3, 'Результаты'),
('STR_RETYPE_NEW_PASSWORD', 1, 'Retype password'),
('STR_RETYPE_NEW_PASSWORD', 2, 'Retype password'),
('STR_RETYPE_NEW_PASSWORD', 3, 'Новый пароль еще раз'),
('STR_RETYPE_PASSWORD', 1, 'Retype password'),
('STR_RETYPE_PASSWORD', 2, 'Retype password'),
('STR_RETYPE_PASSWORD', 3, 'Пароль еще раз'),
('STR_SAVE', 1, 'Save'),
('STR_SAVE', 2, 'Save'),
('STR_SAVE', 3, 'Сохранить'),
('STR_SELECT_CATEGORY', 1, 'Select category'),
('STR_SELECT_CATEGORY', 2, 'Valige kategooria'),
('STR_SELECT_CATEGORY', 3, 'Выберите категорию'),
('STR_SELECT_REGION', 1, 'Select region'),
('STR_SELECT_REGION', 2, 'Valige piirkond'),
('STR_SELECT_REGION', 3, 'Выберите регион'),
('STR_SEND', 1, 'Send'),
('STR_SEND', 2, 'Saada'),
('STR_SEND', 3, 'Отправить'),
('STR_SIGNIN', 1, 'Sign in'),
('STR_SIGNIN', 2, 'Logi sisse'),
('STR_SIGNIN', 3, 'Войти'),
('STR_SIGNOUT', 1, 'Sign out'),
('STR_SIGNOUT', 2, 'Logi välja'),
('STR_SIGNOUT', 3, 'Выйти'),
('STR_SIGNUP', 1, 'Sign up'),
('STR_SIGNUP', 2, 'Registreeri'),
('STR_SIGNUP', 3, 'Зарегистрироваться'),
('STR_SORTBY', 1, 'Sort by'),
('STR_SORTBY', 2, 'Sort by'),
('STR_SORTBY', 3, 'Сортировать'),
('STR_STATUS', 1, 'Status'),
('STR_STATUS', 2, 'Status'),
('STR_STATUS', 3, 'Статус'),
('STR_TERMINATE', 1, 'Terminate'),
('STR_TERMINATE', 2, 'Terminate'),
('STR_TERMINATE', 3, 'Расторгнуть'),
('STR_TERMINATE_ORDER', 1, 'Terminate order'),
('STR_TERMINATE_ORDER', 2, 'Terminate order'),
('STR_TERMINATE_ORDER', 3, 'Прекратить заказ'),
('STR_TERMINATION_FEE', 1, 'Termination fee'),
('STR_TERMINATION_FEE', 2, 'Termination fee'),
('STR_TERMINATION_FEE', 3, 'Плата за расторжение'),
('STR_TERMINATION_PENALTY_FEE', 1, 'Termination penalty fee'),
('STR_TERMINATION_PENALTY_FEE', 2, 'Termination penalty fee'),
('STR_TERMINATION_PENALTY_FEE', 3, 'Штраф за расторжение'),
('STR_TERMINATION_PRICE', 1, 'Price after termination'),
('STR_TERMINATION_PRICE', 2, 'Price after termination'),
('STR_TERMINATION_PRICE', 3, 'Цена после прекращения'),
('STR_TOTAL', 1, 'Total'),
('STR_TOTAL', 2, 'Total'),
('STR_TOTAL', 3, 'Итого'),
('STR_TYPE_CODE', 1, 'Type code'),
('STR_TYPE_CODE', 2, 'Sisestage kood'),
('STR_TYPE_CODE', 3, 'Введите код'),
('STR_WEEKENDS_HOLIDAYS', 1, 'Weekends and holidays'),
('STR_WEEKENDS_HOLIDAYS', 2, 'Weekends and holidays'),
('STR_WEEKENDS_HOLIDAYS', 3, 'Выходные и праздники'),
('STR_YEAR', 1, 'Year'),
('STR_YEAR', 2, 'Aasta'),
('STR_YEAR', 3, 'Год'),
('SUCCESS_9000', 1, 'Data were added.'),
('SUCCESS_9000', 2, 'Andmed lisati.'),
('SUCCESS_9000', 3, 'Данные были добавлены.'),
('SUCCESS_9001', 1, 'Data were changed.'),
('SUCCESS_9001', 2, 'Andmeid muudeti.'),
('SUCCESS_9001', 3, 'Данные были изменены.'),
('SUCCESS_9002', 1, 'Data were removed.'),
('SUCCESS_9002', 2, 'Andmed eemaldati.'),
('SUCCESS_9002', 3, 'Данные были удалены.'),
('SUCCESS_9003', 1, 'Data were selected.'),
('SUCCESS_9003', 2, 'Valiti andmed.'),
('SUCCESS_9003', 3, 'Данные были удалены.'),
('SUCCESS_9004', 1, 'No data selected.'),
('SUCCESS_9004', 2, 'Andmeid pole valitud.'),
('SUCCESS_9004', 3, 'Нет данных для выбора.'),
('TXT_BASKET', 1, 'Here you can see selected rent cars. Also you can change rent period and remove items.'),
('TXT_BASKET', 2, 'Here you can see selected rent cars. Also you can change rent period and remove items.'),
('TXT_BASKET', 3, 'Здесь Вы можете видеть выбранные машины в аренду. Также Вы можете менять период аренды и удалять элементы.'),
('TXT_CARS', 1, 'Here you can select cars for rent by different paramenters.'),
('TXT_CARS', 2, 'Siin saate rentida autosid erinevate parameetrite järgi.'),
('TXT_CARS', 3, 'Здесь Вы можете выбрать автомобили в аренду по различным параметрам.'),
('TXT_FORGOT_EMAIL', 1, 'Hello. You can recovery password by link: {LINK}'),
('TXT_FORGOT_EMAIL', 2, 'Tere. Parooli saate taastada lingi kaudu: {LINK}'),
('TXT_FORGOT_EMAIL', 3, 'Здравствуйте. Вы можете восстановить пароль по ссылке: {LINK}'),
('TXT_FORGOT_PASSWORD', 1, 'Type your e-mail and we will send to you message with link for recovery password.'),
('TXT_FORGOT_PASSWORD', 2, 'Sisestage oma e-mail ja me saadame teile sõnumi taasteparooli lingiga'),
('TXT_FORGOT_PASSWORD', 3, 'Введите свой e-mail, и мы отправим вам сообщение с ссылкой для восстановления пароля.'),
('TXT_FORGOT_RESPONSE', 1, 'We sent link for recovery password to your e-mail.'),
('TXT_FORGOT_RESPONSE', 2, 'Saatsime teie e-posti aadressile parooli taastamise lingi.'),
('TXT_FORGOT_RESPONSE', 3, 'Мы отправили Вам ссылку для восстановления пароля на Ваш e-mail.'),
('TXT_MY_ORDERS', 1, 'Here you can see your orders, also you can download, renew or terminate it.'),
('TXT_MY_ORDERS', 2, 'Here you can see your orders, also you can download, renew or terminate it.'),
('TXT_MY_ORDERS', 3, 'Здесь Вы можете видеть свои заказы, а также загрузить, продлить или расторгнуть их.'),
('TXT_PROFILE', 1, 'Here you can see and change your data.'),
('TXT_PROFILE', 2, 'Siin saate oma andmeid vaadata ja muuta.'),
('TXT_PROFILE', 3, 'Здесь вы можете видеть и менять свои данные.'),
('TXT_RECOVERY_PASSWORD', 1, 'Type your new password and retype it.'),
('TXT_RECOVERY_PASSWORD', 2, 'Sisestage uus parool ja tippige see uuesti.'),
('TXT_RECOVERY_PASSWORD', 3, 'Введите свой новый пароль и повторите его.'),
('TXT_RECOVERY_RESPONSE', 1, 'Your password was changed. Now you can login.'),
('TXT_RECOVERY_RESPONSE', 2, 'Your password was changed. Now you can login.'),
('TXT_RECOVERY_RESPONSE', 3, 'Ваш пароль был изменен. Теперь Вы можете авторизоваться.'),
('TXT_RENEW_ORDER', 1, 'If you want, you can renew this order.'),
('TXT_RENEW_ORDER', 2, 'If you want, you can renew this order.'),
('TXT_RENEW_ORDER', 3, 'Если хотите, можете продлить этот заказ.'),
('TXT_SIGNUP', 1, 'After sending registration You will get letter by e-mail for activation.'),
('TXT_SIGNUP', 2, 'After sending registration You will get letter by e-mail for activation.'),
('TXT_SIGNUP', 3, 'После регистрации на Вашу почту будет выслано письмо для активации аккаунта'),
('TXT_SIGNUP_COMPLETE', 1, 'Now you can sign in and see or change your data in your profile.'),
('TXT_SIGNUP_COMPLETE', 2, 'Nüüd login sisse ja näete või saate oma andmeid oma profiilil muuta.'),
('TXT_SIGNUP_COMPLETE', 3, 'Теперь Вы можете войти и видеть или менять свои данные в Вашем профиле.'),
('TXT_SIGNUP_CONFIRM', 1, 'Type code or click link from letter for confirmation.'),
('TXT_SIGNUP_CONFIRM', 2, 'Sisestage kinnituseks kood või klõpsake kirjas oleval lingil.'),
('TXT_SIGNUP_CONFIRM', 3, 'Укажите код или пройдите по ссылке из полученного письма для подтверждения регистрации.'),
('TXT_SIGNUP_EMAIL', 1, 'You are registered on site {SITE}<br/> Your confirmation code is: {CODE}<br/>Or you can click link: <a href=\'{URL}\'>{URL}</a>'),
('TXT_SIGNUP_EMAIL', 2, 'You are registered on site {SITE}<br/> Your confirmation code is: {CODE}<br/>Or you can click link: <a href=\'{URL}\'>{URL}</a>'),
('TXT_SIGNUP_EMAIL', 3, 'Вы зареристрировались на сайте {SITE}<br/> Ваш код подтверждения: {CODE}<br/>Или пройдите по ссылке: <a href=\'{URL}\'>{URL}</a>'),
('TXT_SIGNUP_TERMS', 1, 'By sending registration you accept our terms and conditions'),
('TXT_SIGNUP_TERMS', 2, 'By sending registration you accept our terms and conditions'),
('TXT_SIGNUP_TERMS', 3, 'Регистрируясь, Вы принимаете наши правила и условия'),
('TXT_SOMETHING_WRONG', 1, 'Something went wrong...'),
('TXT_SOMETHING_WRONG', 2, 'Midagi läks valesti...'),
('TXT_SOMETHING_WRONG', 3, 'Что-то пошло не так...'),
('TXT_TERMINATE_ORDER', 1, 'If you want you can terminate this order.'),
('TXT_TERMINATE_ORDER', 2, 'If you want you can terminate this order.'),
('TXT_TERMINATE_ORDER', 3, 'Если Вы желаете, Вы можете прекратить этот заказ.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'for renew parent order',
  `invoice` varchar(35) NOT NULL COMMENT 'invoice number - unique string',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `price` decimal(9,2) NOT NULL,
  `one_time_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `renewal_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `holidays_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `discount_days_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `termination_penalty_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(9,2) NOT NULL DEFAULT 0.00,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `creation_time` bigint(20) NOT NULL,
  `update_time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `parent_id`, `invoice`, `start_date`, `end_date`, `termination_date`, `price`, `one_time_fee`, `renewal_fee`, `holidays_fee`, `discount_days_fee`, `termination_penalty_fee`, `final_price`, `status_id`, `creation_time`, `update_time`) VALUES
(5, 2, 0, '20210209-2-3', '2021-02-09', '2021-02-10', '2021-02-09', '300.00', '20.00', '0.00', '0.00', '0.00', '0.00', '170.00', 3, 1612848895, 1612875203),
(8, 2, 5, '20210209-2-5', '2021-02-13', '2021-02-17', '2021-02-10', '150.00', '0.00', '20.00', '30.00', '0.00', '0.00', '200.00', 3, 1612866314, 1612968696),
(9, 2, 8, '20210209-2-6', '2021-02-19', '2021-02-23', '2021-02-09', '150.00', '0.00', '20.00', '20.00', '0.00', '0.00', '190.00', 3, 1612866561, 1612875370),
(10, 2, 0, '20210210-2-7', '2021-02-10', '2021-02-23', '2021-02-10', '200.00', '0.00', '0.00', '10.00', '0.00', '0.00', '210.00', 3, 1612964980, 1612968899),
(11, 2, 10, '20210210-2-8', '2021-02-24', '2021-02-27', NULL, '450.00', '0.00', '40.00', '10.00', '0.00', '0.00', '500.00', 1, 1612967048, 1612967048);

-- --------------------------------------------------------

--
-- Table structure for table `orders_cars`
--

CREATE TABLE `orders_cars` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `price` decimal(9,2) NOT NULL,
  `one_time_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `renewal_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `holidays` text NOT NULL DEFAULT '',
  `holidays_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `discount_days_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `termination_penalty_fee` decimal(9,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(9,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders_cars`
--

INSERT INTO `orders_cars` (`id`, `order_id`, `car_id`, `start_date`, `end_date`, `termination_date`, `price`, `one_time_fee`, `renewal_fee`, `holidays`, `holidays_fee`, `discount_days_fee`, `termination_penalty_fee`, `final_price`) VALUES
(7, 5, 2, '2021-02-09', '2021-02-10', '2021-02-09', '100.00', '0.00', '0.00', '', '0.00', '0.00', '0.00', '50.00'),
(8, 5, 4, '2021-02-09', '2021-02-10', '2021-02-09', '200.00', '20.00', '0.00', '', '0.00', '0.00', '0.00', '120.00'),
(11, 8, 2, '2021-02-13', '2021-02-15', '2021-02-10', '50.00', '0.00', '10.00', '2021-02-13', '10.00', '0.00', '0.00', '70.00'),
(12, 8, 4, '2021-02-14', '2021-02-17', '2021-02-10', '100.00', '0.00', '10.00', '2021-02-14', '20.00', '0.00', '0.00', '130.00'),
(13, 9, 2, '2021-02-19', '2021-02-19', '2021-02-09', '50.00', '0.00', '10.00', '', '0.00', '0.00', '0.00', '60.00'),
(14, 9, 4, '2021-02-20', '2021-02-23', '2021-02-09', '100.00', '0.00', '10.00', '2021-02-20', '20.00', '0.00', '0.00', '130.00'),
(15, 10, 2, '2021-02-10', '2021-02-12', '2021-02-10', '50.00', '0.00', '0.00', '', '0.00', '0.00', '0.00', '50.00'),
(16, 10, 1, '2021-02-13', '2021-02-14', '2021-02-10', '50.00', '0.00', '0.00', '2021-02-13', '10.00', '0.00', '0.00', '60.00'),
(17, 10, 3, '2021-02-19', '2021-02-23', '2021-02-10', '50.00', '0.00', '0.00', '', '0.00', '0.00', '0.00', '50.00'),
(18, 10, 2, '2021-02-17', '2021-02-19', '2021-02-10', '50.00', '0.00', '0.00', '', '0.00', '0.00', '0.00', '50.00'),
(19, 11, 2, '2021-02-24', '2021-02-25', NULL, '100.00', '0.00', '10.00', '', '0.00', '0.00', '0.00', '110.00'),
(20, 11, 1, '2021-02-24', '2021-02-25', NULL, '100.00', '0.00', '10.00', '', '0.00', '0.00', '0.00', '110.00'),
(21, 11, 3, '2021-02-25', '2021-02-27', NULL, '150.00', '0.00', '10.00', '2021-02-27', '10.00', '0.00', '0.00', '170.00'),
(22, 11, 2, '2021-02-24', '2021-02-25', NULL, '100.00', '0.00', '10.00', '', '0.00', '0.00', '0.00', '110.00');

-- --------------------------------------------------------

--
-- Table structure for table `orders_statuses`
--

CREATE TABLE `orders_statuses` (
  `id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `pos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders_statuses`
--

INSERT INTO `orders_statuses` (`id`, `title`, `pos`) VALUES
(1, 'New', 10),
(2, 'In progress', 20),
(3, 'Terminated', 30),
(4, 'Paid', 40),
(5, 'Closed', 50);

-- --------------------------------------------------------

--
-- Table structure for table `orders_statuses_langs`
--

CREATE TABLE `orders_statuses_langs` (
  `status_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders_statuses_langs`
--

INSERT INTO `orders_statuses_langs` (`status_id`, `language_id`, `title`, `descr`) VALUES
(1, 1, 'New', 'New description or html...'),
(1, 2, 'Uus', 'Uus description or html...'),
(1, 3, 'Новый', 'Новый description or html...'),
(2, 1, 'In progress', 'In progress description or html...'),
(2, 2, 'Toimumisel\r\n', 'Toimumisel description or html...'),
(2, 3, 'В процессе', 'В процессе description or html...'),
(3, 1, 'Terminated', 'Terminated description or html...'),
(3, 2, 'Lõpetatud', 'Lõpetatud description or html...'),
(3, 3, 'Расторгнут', 'Расторгнут description or html...'),
(4, 1, 'Paid', 'Paid description or html...'),
(4, 2, 'Makstud', 'Makstud description or html...'),
(4, 3, 'Оплачен', 'Оплачен description or html...'),
(5, 1, 'Closed', 'Closed description or html...'),
(5, 2, 'Suletud', 'Suletud description or html...'),
(5, 3, 'Закрыт', 'Закрыт description or html...');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `url` varchar(35) NOT NULL,
  `country_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `pos` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `url`, `country_id`, `parent_id`, `title`, `pos`, `status_id`) VALUES
(1001, 'harju', 201, 0, 'Harju', 1000, 1),
(1002, 'harju-tallinn', 201, 1001, 'Tallinn', 10, 1),
(1003, 'harju-maardu', 201, 1001, 'Maardu', 20, 1),
(1004, 'harju-keila', 201, 1001, 'Keila', 30, 1),
(1005, 'harju-saue', 201, 1001, 'Saue', 40, 1),
(1006, 'harju-paldiski', 201, 1001, 'Paldiski', 50, 1),
(1007, 'harju-kehra', 201, 1001, 'Kehra', 60, 1),
(1008, 'harju-loksa', 201, 1001, 'Loksa', 70, 1),
(1009, 'hiiu', 201, 0, 'Hiiu', 1010, 1),
(1010, 'hiiu-kardla', 201, 1009, 'Kärdla', 10, 1),
(1011, 'idaviru', 201, 0, 'Ida-Viru', 1020, 1),
(1012, 'idaviru-narva', 201, 1011, 'Narva', 10, 1),
(1013, 'idaviru-kohtlajarve', 201, 1011, 'Kohtla-Järve', 20, 1),
(1014, 'idaviru-sillamae', 201, 1011, 'Sillamäe', 30, 1),
(1015, 'idaviru-johvi', 201, 1011, 'Jõhvi', 40, 1),
(1016, 'idaviru-kivioli', 201, 1011, 'Kiviõli', 50, 1),
(1017, 'idaviru-narvajoesuu', 201, 1011, 'Narva-Jõesuu', 60, 1),
(1018, 'idaviru-pussi', 201, 1011, 'Püssi', 70, 1),
(1019, 'jarva', 201, 0, 'Järva', 1030, 1),
(1020, 'jarva-paide', 201, 1019, 'Paide', 10, 1),
(1021, 'jarva-turi', 201, 1019, 'Türi', 20, 1),
(1022, 'jogeva', 201, 0, 'Jõgeva', 1040, 1),
(1023, 'jogeva-jogeva', 201, 1022, 'Jõgeva', 10, 1),
(1024, 'jogeva-poltsamaa', 201, 1022, 'Põltsamaa', 20, 1),
(1025, 'jogeva-mustvee', 201, 1022, 'Mustvee', 30, 1),
(1026, 'laane', 201, 0, 'Lääne', 1050, 1),
(1027, 'laane-haapsalu', 201, 1026, 'Haapsalu', 10, 1),
(1028, 'laane-lihula', 201, 1026, 'Lihula', 20, 1),
(1029, 'laaneviru', 201, 0, 'Lääne-Viru', 1060, 1),
(1030, 'laaneviru-rakvere', 201, 1029, 'Rakvere', 10, 1),
(1031, 'laaneviru-tapa', 201, 1029, 'Tapa', 20, 1),
(1032, 'laaneviru-kunda', 201, 1029, 'Kunda', 30, 1),
(1033, 'laaneviru-tamsalu', 201, 1029, 'Tamsalu', 40, 1),
(1034, 'parnu', 201, 0, 'Pärnu', 1070, 1),
(1035, 'parnu-parnu', 201, 1034, 'Pärnu', 10, 1),
(1036, 'parnu-sindi', 201, 1034, 'Sindi', 20, 1),
(1037, 'parnu-kilinginomme', 201, 1034, 'Kilingi-Nõmme', 30, 1),
(1038, 'polva', 201, 0, 'Põlva', 1080, 1),
(1039, 'polva-polva', 201, 1038, 'Põlva', 10, 1),
(1040, 'polva-rapina', 201, 1038, 'Räpina', 20, 1),
(1041, 'rapla', 201, 0, 'Rapla', 1090, 1),
(1042, 'rapla-rapla', 201, 1041, 'Rapla', 10, 1),
(1043, 'saare', 201, 0, 'Saare', 1100, 1),
(1044, 'saare-kuressaare', 201, 1043, 'Kuressaare', 10, 1),
(1045, 'tartu', 201, 0, 'Tartu', 1110, 1),
(1046, 'tartu-tartu', 201, 1045, 'Tartu', 10, 1),
(1047, 'tartu-elva', 201, 1045, 'Elva', 20, 1),
(1048, 'tartu-kallaste', 201, 1045, 'Kallaste', 30, 1),
(1049, 'valga', 201, 0, 'Valga', 1120, 1),
(1050, 'valga-valga', 201, 1049, 'Valga', 10, 1),
(1051, 'valga-torva', 201, 1049, 'Tõrva', 20, 1),
(1052, 'valga-otepaa', 201, 1049, 'Otepää', 30, 1),
(1053, 'viljandi', 201, 0, 'Viljandi', 1130, 1),
(1054, 'viljandi-viljandi', 201, 1053, 'Viljandi', 10, 1),
(1055, 'viljandi-karksinuia', 201, 1053, 'Karksi-Nuia', 20, 1),
(1056, 'viljandi-vohma', 201, 1053, 'Võhma', 30, 1),
(1057, 'viljandi-suurejaani', 201, 1053, 'Suure-Jaani', 40, 1),
(1058, 'viljandi-abjapaluoja', 201, 1053, 'Abja-Paluoja', 50, 1),
(1059, 'viljandi-moisakula', 201, 1053, 'Mõisaküla', 60, 1),
(1060, 'voru', 201, 0, 'Võru', 1140, 1),
(1061, 'voru-voru', 201, 1060, 'Võru', 10, 1),
(1062, 'voru-antsla', 201, 1060, 'Antsla', 20, 1),
(1063, 'riga', 301, 0, 'Riga', 10, 1),
(1064, 'riga-riga', 301, 1063, 'Riga', 10, 1),
(1065, 'riga-jurmala', 301, 1063, 'Jūrmala', 20, 1),
(1066, 'uusimaa', 101, 0, 'Uusimaa', 100, 1),
(1067, 'uusimaa-helsinki', 101, 1066, 'Helsinki', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `regions_langs`
--

CREATE TABLE `regions_langs` (
  `region_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `regions_langs`
--

INSERT INTO `regions_langs` (`region_id`, `language_id`, `title`) VALUES
(1001, 1, 'Harju'),
(1001, 2, 'Harju'),
(1001, 3, 'Харью'),
(1002, 1, 'Tallinn'),
(1002, 2, 'Tallinn'),
(1002, 3, 'Таллинн'),
(1003, 1, 'Maardu'),
(1003, 2, 'Maardu'),
(1003, 3, 'Марду'),
(1004, 1, 'Keila'),
(1004, 2, 'Keila'),
(1004, 3, 'Кейла'),
(1005, 1, 'Saue'),
(1005, 2, 'Saue'),
(1005, 3, 'Сауе'),
(1006, 1, 'Paldiski'),
(1006, 2, 'Paldiski'),
(1006, 3, 'Палдиски'),
(1007, 1, 'Kehra'),
(1007, 2, 'Kehra'),
(1007, 3, 'Кехра'),
(1008, 1, 'Loksa'),
(1008, 2, 'Loksa'),
(1008, 3, 'Локса'),
(1009, 1, 'Hiiu'),
(1009, 2, 'Hiiu'),
(1009, 3, 'Хиу'),
(1010, 1, 'Kardla'),
(1010, 2, 'Kärdla'),
(1010, 3, 'Кярдла'),
(1011, 1, 'Ida-Viru'),
(1011, 2, 'Ida-Viru'),
(1011, 3, 'Ида-Виру'),
(1012, 1, 'Narva'),
(1012, 2, 'Narva'),
(1012, 3, 'Нарва'),
(1013, 1, 'Kohtla-Jarve'),
(1013, 2, 'Kohtla-Järve'),
(1013, 3, 'Кохтла-Ярве'),
(1014, 1, 'Sillamae'),
(1014, 2, 'Sillamäe'),
(1014, 3, 'Силламяе'),
(1015, 1, 'Johvi'),
(1015, 2, 'Jõhvi'),
(1015, 3, 'Йыхви'),
(1016, 1, 'Kivioli'),
(1016, 2, 'Kiviõli'),
(1016, 3, 'Кивиыли'),
(1017, 1, 'Narva-Joesuu'),
(1017, 2, 'Narva-Jõesuu'),
(1017, 3, 'Нарва-Йыэсуу'),
(1018, 1, 'Pussi'),
(1018, 2, 'Püssi'),
(1018, 3, 'Пюсси'),
(1019, 1, 'Jarva'),
(1019, 2, 'Järva'),
(1019, 3, 'Ярва'),
(1020, 1, 'Paide'),
(1020, 2, 'Paide'),
(1020, 3, 'Пайде'),
(1021, 1, 'Turi'),
(1021, 2, 'Türi'),
(1021, 3, 'Тюри'),
(1022, 1, 'Jogeva'),
(1022, 2, 'Jõgeva'),
(1022, 3, 'Йыгева'),
(1023, 1, 'Jogeva'),
(1023, 2, 'Jõgeva'),
(1023, 3, 'Йыгева'),
(1024, 1, 'Poltsamaa'),
(1024, 2, 'Põltsamaa'),
(1024, 3, 'Пылтсама'),
(1025, 1, 'Mustvee'),
(1025, 2, 'Mustvee'),
(1025, 3, 'Мустве'),
(1026, 1, 'Lääne'),
(1026, 2, 'Lääne'),
(1026, 3, 'Ляне'),
(1027, 1, 'Haapsalu'),
(1027, 2, 'Haapsalu'),
(1027, 3, 'Хапсалу'),
(1028, 1, 'Lihula'),
(1028, 2, 'Lihula'),
(1028, 3, 'Лихула'),
(1029, 1, 'Lääne-Viru'),
(1029, 2, 'Lääne-Viru'),
(1029, 3, 'Ляне-Виру'),
(1030, 1, 'Rakvere'),
(1030, 2, 'Rakvere'),
(1030, 3, 'Раквере'),
(1031, 1, 'Tapa'),
(1031, 2, 'Tapa'),
(1031, 3, 'Тапа'),
(1032, 1, 'Kunda'),
(1032, 2, 'Kunda'),
(1032, 3, 'Кунда'),
(1033, 1, 'Tamsalu'),
(1033, 2, 'Tamsalu'),
(1033, 3, 'Тамсалу'),
(1034, 1, 'Parnu'),
(1034, 2, 'Pärnu'),
(1034, 3, 'Пярну'),
(1035, 1, 'Parnu'),
(1035, 2, 'Pärnu'),
(1035, 3, 'Пярну'),
(1036, 1, 'Sindi'),
(1036, 2, 'Sindi'),
(1036, 3, 'Синди'),
(1037, 1, 'Kilingi-Nomme'),
(1037, 2, 'Kilingi-Nõmme'),
(1037, 3, 'Килинги-Нымме'),
(1038, 1, 'Polva'),
(1038, 2, 'Põlva'),
(1038, 3, 'Пылва'),
(1039, 1, 'Polva'),
(1039, 2, 'Põlva'),
(1039, 3, 'Пылва'),
(1040, 1, 'Rapina'),
(1040, 2, 'Räpina'),
(1040, 3, 'Ряпина'),
(1041, 1, 'Rapla'),
(1041, 2, 'Rapla'),
(1041, 3, 'Рапла'),
(1042, 1, 'Rapla'),
(1042, 2, 'Rapla'),
(1042, 3, 'Рапла'),
(1043, 1, 'Saare'),
(1043, 2, 'Saare'),
(1043, 3, 'Сааре'),
(1044, 1, 'Kuressaare'),
(1044, 2, 'Kuressaare'),
(1044, 3, 'Курессааре'),
(1045, 1, 'Tartu'),
(1045, 2, 'Tartu'),
(1045, 3, 'Тарту'),
(1046, 1, 'Tartu'),
(1046, 2, 'Tartu'),
(1046, 3, 'Тарту'),
(1047, 1, 'Elva'),
(1047, 2, 'Elva'),
(1047, 3, 'Элва'),
(1048, 1, 'Kallaste'),
(1048, 2, 'Kallaste'),
(1048, 3, 'Калласте'),
(1049, 1, 'Valga'),
(1049, 2, 'Valga'),
(1049, 3, 'Валга'),
(1050, 1, 'Valga'),
(1050, 2, 'Valga'),
(1050, 3, 'Валга'),
(1051, 1, 'Torva'),
(1051, 2, 'Tõrva'),
(1051, 3, 'Тырва'),
(1052, 1, 'Otepaa'),
(1052, 2, 'Otepää'),
(1052, 3, 'Отепя'),
(1053, 1, 'Viljandi'),
(1053, 2, 'Viljandi'),
(1053, 3, 'Вильянди'),
(1054, 1, 'Viljandi'),
(1054, 2, 'Viljandi'),
(1054, 3, 'Вильянди'),
(1055, 1, 'Karksi-Nuia'),
(1055, 2, 'Karksi-Nuia'),
(1055, 3, 'Каркси-Нуя'),
(1056, 1, 'Vohma'),
(1056, 2, 'Võhma'),
(1056, 3, 'Выхма'),
(1057, 1, 'Suure-Jaani'),
(1057, 2, 'Suure-Jaani'),
(1057, 3, 'Суре-Яни'),
(1058, 1, 'Abja-Paluoja'),
(1058, 2, 'Abja-Paluoja'),
(1058, 3, 'Абя-Палуоя'),
(1059, 1, 'Moisakula'),
(1059, 2, 'Mõisaküla'),
(1059, 3, 'Мыйзакюла'),
(1060, 1, 'Voru'),
(1060, 2, 'Võru'),
(1060, 3, 'Выру'),
(1061, 1, 'Voru'),
(1061, 2, 'Võru'),
(1061, 3, 'Выру'),
(1062, 1, 'Antsla'),
(1062, 2, 'Antsla'),
(1062, 3, 'Антсла'),
(1063, 1, 'Riga'),
(1063, 2, 'Riga'),
(1063, 3, 'Рига'),
(1064, 1, 'Riga'),
(1064, 2, 'Riga'),
(1064, 3, 'Рига'),
(1065, 1, 'Jūrmala'),
(1065, 2, 'Jūrmala'),
(1065, 3, 'Юрмала'),
(1066, 1, 'Uusimaa'),
(1066, 2, 'Uusimaa'),
(1066, 3, 'Уусимаа'),
(1067, 1, 'Helsinki'),
(1067, 2, 'Helsinki'),
(1067, 3, 'Хельсинки');

-- --------------------------------------------------------

--
-- Table structure for table `site_pages`
--

CREATE TABLE `site_pages` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(95) NOT NULL,
  `url` varchar(35) NOT NULL,
  `status_id` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  `in_menu` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_pages`
--

INSERT INTO `site_pages` (`id`, `parent_id`, `title`, `url`, `status_id`, `pos`, `in_menu`) VALUES
(1, 0, 'Home page', 'main', 1, 10, 0),
(2, 0, 'Customer', 'customer', 1, 100, 0),
(3, 0, 'Profile (cabinet)', 'profile', 1, 200, 0),
(4, 0, 'Car', 'car', 1, 300, 1),
(5, 0, 'Basket', 'basket', 1, 400, 0),
(6, 0, 'Order', 'order', 1, 500, 0),
(7, 0, 'About Us', 'about', 1, 250, 1),
(8, 0, 'Contacts', 'contact', 1, 600, 1);

-- --------------------------------------------------------

--
-- Table structure for table `site_pages_langs`
--

CREATE TABLE `site_pages_langs` (
  `id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(95) NOT NULL,
  `content` text NOT NULL,
  `meta_title` varchar(155) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `menu_title` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_pages_langs`
--

INSERT INTO `site_pages_langs` (`id`, `language_id`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `menu_title`) VALUES
(1, 1, 'Welcome!', '<h1>Welcome!</h1><p>This project helps you to find car for rent in your region, country.</p>', 'Home page', 'Home page', 'Home page', 'Home'),
(1, 2, 'Tere!', '<h1>Tere!</h1><p>This project helps you to find car for rent in your region, country.</p>', 'Kodu leht', 'Kodu leht', 'Kodu leht', 'Kodu'),
(1, 3, 'Здравствуйте!', '<h1>Здравствуйте!</h1><p>Этот проект поможет Вам найти для Вас машину в аренду в Вашем регионе или стране.</p>', 'Главная страница', 'Главная страница', 'Главная страница', 'Главная'),
(2, 1, 'Authorization', '', 'Authorization', 'Authorization part for customers', 'Authorization part for customers', ''),
(2, 2, 'Volitamine', '', 'Volitamine', 'Volituste osa klientidele', 'Volituste osa klientidele', ''),
(2, 3, 'Авторизация', '', 'Авторизация', 'Авторизация для клиентов', 'Авторизация для клиентов', ''),
(3, 1, 'Profile', '', 'Profile', 'Profile for customers', 'Profile for customers', ''),
(3, 2, 'Volitamine', '', 'Volitamine', 'Volituste osa klientidele', 'Volituste osa klientidele', ''),
(3, 3, 'Профиль', '', 'Профиль', 'Профиль для клиентов', 'Профиль для клиентов', ''),
(4, 1, 'Cars Rent', '', 'Select Car for Rent', 'Select Car for Rent in your region, country', 'Select Car for Rent in your region, country', 'Cars'),
(4, 2, 'Autode rent', '', 'Valige auto rentimine', 'Valige oma piirkonnas ja riigis renditav auto', 'Valige oma piirkonnas ja riigis renditav auto', 'Autod'),
(4, 3, 'Аренда автомобилей', '', 'Выбери автомобиль в аренду', 'Выбери автомобиль, машину, авто в аренду в своем городе, стране', 'Выбери автомобиль, машину, авто в аренду в своем городе, стране', 'Автомобили'),
(5, 1, 'Basket', '', 'Basket', 'Basket, cars for rent in your basket', 'Basket, cars for rent in your basket', ''),
(5, 2, 'Korv', '', 'Korv', 'Korv, autode rentimine teie korvis', 'Korv, autode rentimine teie korvis', ''),
(5, 3, 'Корзина', '', 'Корзина', 'Корзина, автомобили в аренду в вашей корзине', 'ВКорзина, автомобили в аренду в вашей корзине', ''),
(6, 1, 'Orders', '', 'Your orders', 'Orders, new, in process, completed, terminated, closed', 'Orders, new, in process, completed, terminated, closed', ''),
(6, 2, 'Tellimused', '', 'Teie tellimused', 'Tellimused, uued, menetluses, täidetud, lõpetatud, suletud', 'Tellimused, uued, menetluses, täidetud, lõpetatud, suletud', ''),
(6, 3, 'Заказы', '', 'Мои заказы', 'Заказы, новый, в процессе, завершеный, прерванный, закрытый', 'Заказы, новый, в процессе, завершеный, прерванный, закрытый', ''),
(7, 1, 'About Us', '<p>About us content...</p>', 'About Us', 'About Us, what we are, our mission, our goals, services', 'About Us, what we are, our mission, our goals, services', 'About Us'),
(7, 2, 'Meist', '<p>About us content...</p>', 'Meist', 'Meist, mis me oleme, missioon, eesmärgid, teenused', 'Meist, mis me oleme, missioon, eesmärgid, teenused', 'Meist'),
(7, 3, 'О нас', '<p>Описательная часть о нас...</p>', 'О нас', 'О нас, кто мы есть, наша миссия, наши цели, услуги', 'О нас, кто мы есть, наша миссия, наши цели, услуги', 'О нас'),
(8, 1, 'Contacts', '<p>Here you can see our contact data, also you can send  to us message.</p>', 'Contacts, Feedback', 'Contacts, Feedback, write us', 'Contacts, Feedback, write us', 'Contacts'),
(8, 2, 'Kontaktid', '<p>Here you can see our contact data, also you can send  to us message.</p>', 'Kontaktid, Tagasiside', 'Kontaktid, Tagasiside, kirjuta meile', 'Kontaktid, Tagasiside, kirjuta meile', 'Kontaktid'),
(8, 3, 'Контакты', '<p>Здесь вы найдете наши контактные данные, а также сможете отправить нам сообщение</p>', 'Контакты, Обратная Связь', 'Контакты, Обратная Связь, пишите нам', 'Контакты, Обратная Связь, пишите нам', 'Контакты');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `title` varchar(35) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `title`, `descr`) VALUES
(1, 'Active', 'Normal status.'),
(2, 'Blocked', 'Blocked - not active some period (before some date)'),
(3, 'Invisible', 'Active, but invisible.'),
(4, 'Deleted', 'Removed.');

-- --------------------------------------------------------

--
-- Table structure for table `statuses_langs`
--

CREATE TABLE `statuses_langs` (
  `status_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(35) NOT NULL,
  `descr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `statuses_langs`
--

INSERT INTO `statuses_langs` (`status_id`, `language_id`, `title`, `descr`) VALUES
(1, 1, 'Active', 'Normal status.'),
(1, 2, 'Aktiivne', 'Noormal status.'),
(1, 3, 'Активный', 'Обычный статус.'),
(2, 1, 'Blocked', 'Blocked - not active some period (before some date)'),
(2, 2, 'Blocked', 'Blocked - not active some period (before some date)'),
(2, 3, 'Заблокирован', 'Заблокирован - неактивен какой-то период (до определенной даты)'),
(3, 1, 'Unvisible', 'Active, but unvisible.'),
(3, 2, 'Unvisible', 'Active, but unvisible.'),
(3, 3, 'Невидимый', 'Активный, но невидимый.'),
(4, 1, 'Deleted', 'Removed permanently.'),
(4, 2, 'Deleted', 'Removed permanently.'),
(4, 3, 'Удален', 'Удален навсегда.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `producer_id` (`producer_id`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `cars_categories`
--
ALTER TABLE `cars_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `cars_categories_langs`
--
ALTER TABLE `cars_categories_langs`
  ADD PRIMARY KEY (`category_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `cars_colors`
--
ALTER TABLE `cars_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `cars_colors_langs`
--
ALTER TABLE `cars_colors_langs`
  ADD PRIMARY KEY (`color_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `cars_langs`
--
ALTER TABLE `cars_langs`
  ADD PRIMARY KEY (`car_id`,`language_id`),
  ADD KEY `cars_langs_ibfk_2` (`language_id`);

--
-- Indexes for table `cars_producers`
--
ALTER TABLE `cars_producers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `cars_producers_langs`
--
ALTER TABLE `cars_producers_langs`
  ADD PRIMARY KEY (`producer_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `cars_statuses`
--
ALTER TABLE `cars_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cars_statuses_langs`
--
ALTER TABLE `cars_statuses_langs`
  ADD PRIMARY KEY (`status_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iso` (`iso`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `countries_langs`
--
ALTER TABLE `countries_langs`
  ADD PRIMARY KEY (`country_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iso` (`iso`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `currencies_langs`
--
ALTER TABLE `currencies_langs`
  ADD PRIMARY KEY (`currency_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `personal_code` (`personal_code`);

--
-- Indexes for table `customers_roles`
--
ALTER TABLE `customers_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `customers_roles_langs`
--
ALTER TABLE `customers_roles_langs`
  ADD PRIMARY KEY (`role_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `customers_tokens`
--
ALTER TABLE `customers_tokens`
  ADD PRIMARY KEY (`customer_id`,`type_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `customers_tokens_types`
--
ALTER TABLE `customers_tokens_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iso` (`iso`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice` (`invoice`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `orders_cars`
--
ALTER TABLE `orders_cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders_statuses`
--
ALTER TABLE `orders_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders_statuses_langs`
--
ALTER TABLE `orders_statuses_langs`
  ADD PRIMARY KEY (`status_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `regions_langs`
--
ALTER TABLE `regions_langs`
  ADD PRIMARY KEY (`region_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `site_pages`
--
ALTER TABLE `site_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `site_pages_langs`
--
ALTER TABLE `site_pages_langs`
  ADD PRIMARY KEY (`id`,`language_id`),
  ADD KEY `lang_id` (`language_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statuses_langs`
--
ALTER TABLE `statuses_langs`
  ADD PRIMARY KEY (`status_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `basket`
--
ALTER TABLE `basket`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cars_categories`
--
ALTER TABLE `cars_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cars_colors`
--
ALTER TABLE `cars_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cars_producers`
--
ALTER TABLE `cars_producers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cars_statuses`
--
ALTER TABLE `cars_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers_roles`
--
ALTER TABLE `customers_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers_tokens_types`
--
ALTER TABLE `customers_tokens_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders_cars`
--
ALTER TABLE `orders_cars`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders_statuses`
--
ALTER TABLE `orders_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1068;

--
-- AUTO_INCREMENT for table `site_pages`
--
ALTER TABLE `site_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `cars_categories` (`id`),
  ADD CONSTRAINT `cars_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `cars_colors` (`id`),
  ADD CONSTRAINT `cars_ibfk_3` FOREIGN KEY (`producer_id`) REFERENCES `cars_producers` (`id`),
  ADD CONSTRAINT `cars_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  ADD CONSTRAINT `cars_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  ADD CONSTRAINT `cars_ibfk_6` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `cars_categories`
--
ALTER TABLE `cars_categories`
  ADD CONSTRAINT `cars_categories_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `cars_categories_langs`
--
ALTER TABLE `cars_categories_langs`
  ADD CONSTRAINT `cars_categories_langs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `cars_categories` (`id`),
  ADD CONSTRAINT `cars_categories_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `cars_colors`
--
ALTER TABLE `cars_colors`
  ADD CONSTRAINT `cars_colors_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `cars_colors_langs`
--
ALTER TABLE `cars_colors_langs`
  ADD CONSTRAINT `cars_colors_langs_ibfk_1` FOREIGN KEY (`color_id`) REFERENCES `cars_colors` (`id`),
  ADD CONSTRAINT `cars_colors_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `cars_langs`
--
ALTER TABLE `cars_langs`
  ADD CONSTRAINT `cars_langs_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`),
  ADD CONSTRAINT `cars_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `cars_producers`
--
ALTER TABLE `cars_producers`
  ADD CONSTRAINT `cars_producers_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `cars_producers_langs`
--
ALTER TABLE `cars_producers_langs`
  ADD CONSTRAINT `cars_producers_langs_ibfk_1` FOREIGN KEY (`producer_id`) REFERENCES `cars_producers` (`id`),
  ADD CONSTRAINT `cars_producers_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `cars_statuses_langs`
--
ALTER TABLE `cars_statuses_langs`
  ADD CONSTRAINT `cars_statuses_langs_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `cars_statuses` (`id`),
  ADD CONSTRAINT `cars_statuses_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `countries`
--
ALTER TABLE `countries`
  ADD CONSTRAINT `countries_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `countries_langs`
--
ALTER TABLE `countries_langs`
  ADD CONSTRAINT `countries_langs_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `countries_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `currencies`
--
ALTER TABLE `currencies`
  ADD CONSTRAINT `currencies_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `currencies_langs`
--
ALTER TABLE `currencies_langs`
  ADD CONSTRAINT `currencies_langs_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `currencies_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  ADD CONSTRAINT `customers_ibfk_3` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  ADD CONSTRAINT `customers_ibfk_4` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `customers_ibfk_5` FOREIGN KEY (`role_id`) REFERENCES `customers_roles` (`id`),
  ADD CONSTRAINT `customers_ibfk_6` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `customers_roles`
--
ALTER TABLE `customers_roles`
  ADD CONSTRAINT `customers_roles_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `customers_roles_langs`
--
ALTER TABLE `customers_roles_langs`
  ADD CONSTRAINT `customers_roles_langs_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `customers_roles` (`id`),
  ADD CONSTRAINT `customers_roles_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `customers_tokens`
--
ALTER TABLE `customers_tokens`
  ADD CONSTRAINT `customers_tokens_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customers_tokens_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `customers_tokens_types` (`id`);

--
-- Constraints for table `languages`
--
ALTER TABLE `languages`
  ADD CONSTRAINT `languages_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `locales`
--
ALTER TABLE `locales`
  ADD CONSTRAINT `locales_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `orders_statuses` (`id`);

--
-- Constraints for table `orders_cars`
--
ALTER TABLE `orders_cars`
  ADD CONSTRAINT `orders_cars_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`),
  ADD CONSTRAINT `orders_cars_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `orders_statuses_langs`
--
ALTER TABLE `orders_statuses_langs`
  ADD CONSTRAINT `orders_statuses_langs_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `orders_statuses` (`id`),
  ADD CONSTRAINT `orders_statuses_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `regions`
--
ALTER TABLE `regions`
  ADD CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `regions_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `regions_langs`
--
ALTER TABLE `regions_langs`
  ADD CONSTRAINT `regions_langs_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  ADD CONSTRAINT `regions_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `site_pages`
--
ALTER TABLE `site_pages`
  ADD CONSTRAINT `site_pages_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `site_pages_langs`
--
ALTER TABLE `site_pages_langs`
  ADD CONSTRAINT `site_pages_langs_ibfk_1` FOREIGN KEY (`id`) REFERENCES `site_pages` (`id`),
  ADD CONSTRAINT `site_pages_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `statuses_langs`
--
ALTER TABLE `statuses_langs`
  ADD CONSTRAINT `statuses_langs_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  ADD CONSTRAINT `statuses_langs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
