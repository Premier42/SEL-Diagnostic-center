DROP DATABASE IF EXISTS diagnostic_center;
CREATE DATABASE diagnostic_center;
USE diagnostic_center;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Table: consumable_info
CREATE TABLE `consumable_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumable_code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `consumable_code` (`consumable_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: doctors
CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `qualifications` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `workplace` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: invoices
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `patient_age` varchar(50) DEFAULT NULL,
  `patient_gender` enum('Male','Female','Other') DEFAULT NULL,
  `patient_contact_no` varchar(20) DEFAULT NULL,
  `referred_by` varchar(100) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `due` decimal(10,2) GENERATED ALWAYS AS (`total_amount` - `discount_amount` - `amount_paid`) VIRTUAL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: invoice_consumables
CREATE TABLE `invoice_consumables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `consumable_code` varchar(50) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `consumable_code` (`consumable_code`),
  KEY `idx_invoice_consumables` (`invoice_id`),
  CONSTRAINT `invoice_consumables_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_consumables_ibfk_2` FOREIGN KEY (`consumable_code`) REFERENCES `consumable_info` (`consumable_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tests_info
CREATE TABLE `tests_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_code` varchar(50) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `description` text NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `test_code` (`test_code`),
  UNIQUE KEY `test_name` (`test_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: invoice_tests
CREATE TABLE `invoice_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `test_code` varchar(50) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `test_code` (`test_code`),
  KEY `idx_invoice_tests` (`invoice_id`),
  CONSTRAINT `invoice_tests_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_tests_ibfk_2` FOREIGN KEY (`test_code`) REFERENCES `tests_info` (`test_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: test_parameters
CREATE TABLE `test_parameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_code` varchar(50) DEFAULT NULL,
  `parameter_name` varchar(100) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `normal_range` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_test_code` (`test_code`),
  CONSTRAINT `test_parameters_ibfk_1` FOREIGN KEY (`test_code`) REFERENCES `tests_info` (`test_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: test_reports
CREATE TABLE `test_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `test_code` varchar(50) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `report_status` enum('pending','completed','cancelled','verified') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `test_code` (`test_code`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `test_reports_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_reports_ibfk_2` FOREIGN KEY (`test_code`) REFERENCES `tests_info` (`test_code`) ON DELETE CASCADE,
  CONSTRAINT `test_reports_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: test_results
CREATE TABLE `test_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `normal_range` varchar(50) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `result_value` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parameter_id` (`parameter_id`),
  KEY `idx_report_id` (`report_id`),
  CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `test_reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_results_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `test_parameters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reset auto increment counters
ALTER TABLE `consumable_info` AUTO_INCREMENT = 1;
ALTER TABLE `doctors` AUTO_INCREMENT = 1;
ALTER TABLE `invoices` AUTO_INCREMENT = 1;
ALTER TABLE `invoice_consumables` AUTO_INCREMENT = 1;
ALTER TABLE `invoice_tests` AUTO_INCREMENT = 1;
ALTER TABLE `tests_info` AUTO_INCREMENT = 1;
ALTER TABLE `test_parameters` AUTO_INCREMENT = 1;
ALTER TABLE `test_reports` AUTO_INCREMENT = 1;
ALTER TABLE `test_results` AUTO_INCREMENT = 1;
ALTER TABLE `users` AUTO_INCREMENT = 1;
