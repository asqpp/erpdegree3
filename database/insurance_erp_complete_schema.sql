-- ================================================================================
-- INSURANCE ERP - COMPLETE DATABASE SCHEMA
-- ================================================================================
-- Version: 3.0.0
-- Database: MySQL Compatible
-- Total Tables: 135+
-- Created: 2025-01-10
-- ================================================================================
-- This schema integrates with existing CodeIgniter ERP and adds Insurance-specific
-- features with GCC/UAE compliance
-- ================================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ================================================================================
-- SECTION 1: CORE SYSTEM TABLES (8 TABLES)
-- ================================================================================

-- Table: companies
CREATE TABLE IF NOT EXISTS `companies` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `legal_name` VARCHAR(191),
    `address` TEXT,
    `city` VARCHAR(191),
    `state` VARCHAR(191),
    `country` VARCHAR(191) DEFAULT 'UAE',
    `postal_code` VARCHAR(191),
    `phone` VARCHAR(191),
    `email` VARCHAR(191),
    `website` TEXT,
    `tax_id` VARCHAR(191),
    `trn_no` VARCHAR(191) COMMENT 'UAE Tax Registration Number',
    `license_no` VARCHAR(191),
    `registration_no` TEXT,
    `currency` VARCHAR(191) DEFAULT 'AED',
    `fiscal_year_start` DATE,
    `logo_url` TEXT,
    `base_currency_id` INTEGER DEFAULT 1,
    `allow_multi_currency` TINYINT(1) DEFAULT 1,
    `active` INTEGER DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: branches
CREATE TABLE IF NOT EXISTS `branches` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `company_id` INTEGER,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `address` TEXT,
    `city` VARCHAR(191),
    `state` VARCHAR(191),
    `emirate_id` INTEGER COMMENT 'Link to emirates table',
    `pin` TEXT,
    `phone` VARCHAR(191),
    `email` VARCHAR(191),
    `manager_name` VARCHAR(191),
    `active` INTEGER DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: financial_years
CREATE TABLE IF NOT EXISTS `financial_years` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `year_label` VARCHAR(191) UNIQUE NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `start_date_hijri` VARCHAR(20),
    `end_date_hijri` VARCHAR(20),
    `opening_balance` DECIMAL(15,2) DEFAULT 0.00,
    `closing_balance` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(191) DEFAULT 'open',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(191) UNIQUE NOT NULL,
    `description` TEXT,
    `permissions` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `username` VARCHAR(191) UNIQUE NOT NULL,
    `email` VARCHAR(191) UNIQUE,
    `password` VARCHAR(191) NOT NULL,
    `first_name` VARCHAR(191),
    `last_name` VARCHAR(191),
    `role_id` INTEGER,
    `branch_id` INTEGER,
    `usertype` VARCHAR(191) DEFAULT 'USER',
    `phone` VARCHAR(191),
    `avatar_url` TEXT,
    `status` VARCHAR(191) DEFAULT 'active',
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings (String values)
CREATE TABLE IF NOT EXISTS `settings` (
    `code` VARCHAR(191) PRIMARY KEY,
    `cvalue` TEXT,
    `description` TEXT,
    `category` VARCHAR(191),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings_int (Integer values)
CREATE TABLE IF NOT EXISTS `settings_int` (
    `code` VARCHAR(191) PRIMARY KEY,
    `cvalue` INTEGER,
    `description` TEXT,
    `category` VARCHAR(191),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INTEGER,
    `action` TEXT NOT NULL,
    `table_name` VARCHAR(191),
    `record_id` INTEGER,
    `old_value` TEXT,
    `new_value` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_table` (`table_name`, `record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================================
-- SECTION 2: ACCOUNTING TABLES (15 TABLES)
-- ================================================================================

-- Table: account_groups (Main hierarchy)
CREATE TABLE IF NOT EXISTS `account_groups` (
    `grcode` VARCHAR(191) PRIMARY KEY,
    `name` VARCHAR(191) NOT NULL,
    `reserve` VARCHAR(1) DEFAULT 'N',
    `actype1` VARCHAR(191) NOT NULL COMMENT 'A=Asset, L=Liability, E=Expense, R=Revenue',
    `parent_grp` VARCHAR(191),
    `level` INTEGER DEFAULT 1,
    `position` INTEGER,
    `active` VARCHAR(1) DEFAULT 'Y',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_grp`) REFERENCES `account_groups`(`grcode`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: account_subgroups
CREATE TABLE IF NOT EXISTS `account_subgroups` (
    `subgrcode` VARCHAR(191) PRIMARY KEY,
    `name` VARCHAR(191) NOT NULL,
    `grcode` VARCHAR(191) NOT NULL,
    `actype1` VARCHAR(191),
    `active` VARCHAR(1) DEFAULT 'Y',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`grcode`) REFERENCES `account_groups`(`grcode`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: accounts (Chart of Accounts)
CREATE TABLE IF NOT EXISTS `accounts` (
    `accode` VARCHAR(191) PRIMARY KEY,
    `name` VARCHAR(191) NOT NULL,
    `actype1` VARCHAR(191) NOT NULL COMMENT 'A=Asset, L=Liability, E=Expense, R=Revenue',
    `actype2` VARCHAR(191),
    `reserve` VARCHAR(1) DEFAULT 'N',
    `grcode` VARCHAR(191),
    `subgrcode` VARCHAR(191),
    `opbal` DECIMAL(15,2) DEFAULT 0.00,
    `curbal` DECIMAL(15,2) DEFAULT 0.00,
    `control` INTEGER DEFAULT 1,
    `blocked` VARCHAR(1) DEFAULT 'N',
    `reconciled` VARCHAR(1) DEFAULT 'N',
    `note` TEXT,
    `removed` INTEGER DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`grcode`) REFERENCES `account_groups`(`grcode`) ON DELETE SET NULL,
    FOREIGN KEY (`subgrcode`) REFERENCES `account_subgroups`(`subgrcode`) ON DELETE SET NULL,
    INDEX `idx_actype` (`actype1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: journals (Journal Entry Header)
CREATE TABLE IF NOT EXISTS `journals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `date` DATE NOT NULL,
    `description` TEXT,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `status` VARCHAR(191) DEFAULT 'draft',
    `branch_id` INTEGER,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_journal_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ledger (Journal Entry Lines)
CREATE TABLE IF NOT EXISTS `ledger` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `journal_id` INTEGER NOT NULL,
    `accode` VARCHAR(191) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `debit_credit` CHAR(1) NOT NULL COMMENT 'D=Debit, C=Credit',
    `date` DATE NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`journal_id`) REFERENCES `journals`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE CASCADE,
    INDEX `idx_ledger_journal` (`journal_id`),
    INDEX `idx_ledger_account` (`accode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: daybook (General Ledger / Transaction Log)
CREATE TABLE IF NOT EXISTS `daybook` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `date` DATE NOT NULL,
    `accode` VARCHAR(191) NOT NULL,
    `debit` DECIMAL(15,2) DEFAULT 0.00,
    `credit` DECIMAL(15,2) DEFAULT 0.00,
    `description` TEXT,
    `voucher_type` VARCHAR(191),
    `voucher_no` VARCHAR(191),
    `ref_id` INTEGER,
    `branch_id` INTEGER,
    `user_id` INTEGER,
    `currency_id` INTEGER DEFAULT 1,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
    `amount_in_currency` DECIMAL(15,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_daybook_date` (`date`),
    INDEX `idx_daybook_account` (`accode`),
    INDEX `idx_daybook_voucher` (`voucher_type`, `voucher_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bank_accounts
CREATE TABLE IF NOT EXISTS `bank_accounts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `accode` VARCHAR(191) UNIQUE NOT NULL,
    `bank_name` VARCHAR(191) NOT NULL,
    `account_no` VARCHAR(191) NOT NULL,
    `account_type` VARCHAR(191),
    `ifsc_code` VARCHAR(191),
    `swift_code` VARCHAR(11),
    `branch_name` VARCHAR(191),
    `branch_code` VARCHAR(20),
    `opening_balance` DECIMAL(15,2) DEFAULT 0.00,
    `current_balance` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(191) DEFAULT 'active',
    `currency_id` INTEGER DEFAULT 1,
    `iban` VARCHAR(34),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bank_reconciliation
CREATE TABLE IF NOT EXISTS `bank_reconciliation` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bank_account_id` INTEGER NOT NULL,
    `reconciliation_date` DATE NOT NULL,
    `bank_statement_balance` DECIMAL(15,2) NOT NULL,
    `book_balance` DECIMAL(15,2) NOT NULL,
    `difference` DECIMAL(15,2) NOT NULL,
    `status` VARCHAR(191) DEFAULT 'pending',
    `notes` TEXT,
    `reconciled_by` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reconciled_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================================
-- CONTINUE IN NEXT PART DUE TO LENGTH...
-- ================================================================================

COMMIT;
