-- ============================================================
-- Receipt & Payment Vouchers, Debit Notes, Credit Notes
-- ============================================================

-- Receipt/Payment Vouchers Table
CREATE TABLE IF NOT EXISTS `receipt_vouchers` (
  `receipt_id` INT(11) NOT NULL AUTO_INCREMENT,
  `voucher_number` VARCHAR(50) NOT NULL,
  `voucher_type` ENUM('receipt', 'payment') NOT NULL DEFAULT 'receipt',
  `voucher_date` DATE NOT NULL,
  `party_name` VARCHAR(255) NOT NULL,
  `party_type` ENUM('customer', 'supplier', 'agent', 'broker', 'employee', 'other') DEFAULT NULL,
  `party_id` INT(11) DEFAULT NULL,
  `payment_method` ENUM('cash', 'cheque', 'bank_transfer', 'card', 'online') NOT NULL DEFAULT 'cash',
  `bank_account_id` INT(11) DEFAULT NULL,
  `cheque_number` VARCHAR(50) DEFAULT NULL,
  `cheque_date` DATE DEFAULT NULL,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `narration` TEXT,
  `status` ENUM('draft', 'approved', 'posted', 'cancelled') NOT NULL DEFAULT 'draft',
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_id` INT(11) NOT NULL,
  `branch_id` INT(11) DEFAULT NULL,
  `financial_year_id` INT(11) NOT NULL,
  PRIMARY KEY (`receipt_id`),
  UNIQUE KEY `voucher_number` (`voucher_number`),
  KEY `idx_voucher_type` (`voucher_type`),
  KEY `idx_voucher_date` (`voucher_date`),
  KEY `idx_party` (`party_type`, `party_id`),
  KEY `idx_status` (`status`),
  KEY `idx_company_branch` (`company_id`, `branch_id`),
  KEY `fk_receipt_bank_account` (`bank_account_id`),
  KEY `fk_receipt_created_by` (`created_by`),
  CONSTRAINT `fk_receipt_bank_account` FOREIGN KEY (`bank_account_id`) REFERENCES `chart_of_accounts` (`account_id`),
  CONSTRAINT `fk_receipt_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Receipt/Payment Items Table
CREATE TABLE IF NOT EXISTS `receipt_items` (
  `item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` INT(11) NOT NULL,
  `account_id` INT(11) NOT NULL,
  `description` TEXT,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`item_id`),
  KEY `fk_receipt_item_receipt` (`receipt_id`),
  KEY `fk_receipt_item_account` (`account_id`),
  CONSTRAINT `fk_receipt_item_receipt` FOREIGN KEY (`receipt_id`) REFERENCES `receipt_vouchers` (`receipt_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_receipt_item_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Debit Notes Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `debit_notes` (
  `debit_note_id` INT(11) NOT NULL AUTO_INCREMENT,
  `debit_note_number` VARCHAR(50) NOT NULL,
  `debit_note_date` DATE NOT NULL,
  `supplier_id` INT(11) DEFAULT NULL,
  `supplier_name` VARCHAR(255) NOT NULL,
  `reference_type` ENUM('purchase', 'payment', 'other') DEFAULT 'purchase',
  `reference_id` INT(11) DEFAULT NULL,
  `reference_number` VARCHAR(50) DEFAULT NULL,
  `reason` TEXT,
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `vat_percentage` DECIMAL(5,2) DEFAULT 5.00,
  `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('draft', 'issued', 'posted', 'cancelled') NOT NULL DEFAULT 'draft',
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_id` INT(11) NOT NULL,
  `branch_id` INT(11) DEFAULT NULL,
  `financial_year_id` INT(11) NOT NULL,
  PRIMARY KEY (`debit_note_id`),
  UNIQUE KEY `debit_note_number` (`debit_note_number`),
  KEY `idx_debit_note_date` (`debit_note_date`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_reference` (`reference_type`, `reference_id`),
  KEY `idx_status` (`status`),
  KEY `idx_company_branch` (`company_id`, `branch_id`),
  KEY `fk_debit_note_created_by` (`created_by`),
  CONSTRAINT `fk_debit_note_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Debit Note Items Table
CREATE TABLE IF NOT EXISTS `debit_note_items` (
  `item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `debit_note_id` INT(11) NOT NULL,
  `account_id` INT(11) NOT NULL,
  `description` TEXT,
  `quantity` DECIMAL(10,2) DEFAULT 1.00,
  `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`item_id`),
  KEY `fk_debit_item_note` (`debit_note_id`),
  KEY `fk_debit_item_account` (`account_id`),
  CONSTRAINT `fk_debit_item_note` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`debit_note_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_debit_item_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Credit Notes Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `credit_notes` (
  `credit_note_id` INT(11) NOT NULL AUTO_INCREMENT,
  `credit_note_number` VARCHAR(50) NOT NULL,
  `credit_note_date` DATE NOT NULL,
  `customer_id` INT(11) DEFAULT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `reference_type` ENUM('policy', 'invoice', 'other') DEFAULT 'policy',
  `reference_id` INT(11) DEFAULT NULL,
  `reference_number` VARCHAR(50) DEFAULT NULL,
  `reason` TEXT,
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `vat_percentage` DECIMAL(5,2) DEFAULT 5.00,
  `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('draft', 'issued', 'posted', 'cancelled') NOT NULL DEFAULT 'draft',
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_id` INT(11) NOT NULL,
  `branch_id` INT(11) DEFAULT NULL,
  `financial_year_id` INT(11) NOT NULL,
  PRIMARY KEY (`credit_note_id`),
  UNIQUE KEY `credit_note_number` (`credit_note_number`),
  KEY `idx_credit_note_date` (`credit_note_date`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_reference` (`reference_type`, `reference_id`),
  KEY `idx_status` (`status`),
  KEY `idx_company_branch` (`company_id`, `branch_id`),
  KEY `fk_credit_note_customer` (`customer_id`),
  KEY `fk_credit_note_created_by` (`created_by`),
  CONSTRAINT `fk_credit_note_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `fk_credit_note_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Credit Note Items Table
CREATE TABLE IF NOT EXISTS `credit_note_items` (
  `item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `credit_note_id` INT(11) NOT NULL,
  `account_id` INT(11) NOT NULL,
  `description` TEXT,
  `quantity` DECIMAL(10,2) DEFAULT 1.00,
  `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`item_id`),
  KEY `fk_credit_item_note` (`credit_note_id`),
  KEY `fk_credit_item_account` (`account_id`),
  CONSTRAINT `fk_credit_item_note` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`credit_note_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_credit_item_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Enhance Chart of Accounts with Subgroups
-- ============================================================
ALTER TABLE `chart_of_accounts`
ADD COLUMN IF NOT EXISTS `account_subgroup` VARCHAR(100) DEFAULT NULL AFTER `account_group`,
ADD KEY `idx_account_subgroup` (`account_subgroup`);

-- Sample Account Subgroups Data
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Current Assets' WHERE `account_group` = 'Assets' AND `account_name` LIKE '%Cash%';
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Current Assets' WHERE `account_group` = 'Assets' AND `account_name` LIKE '%Bank%';
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Current Assets' WHERE `account_group` = 'Assets' AND `account_name` LIKE '%Receivable%';
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Fixed Assets' WHERE `account_group` = 'Assets' AND (`account_name` LIKE '%Building%' OR `account_name` LIKE '%Equipment%' OR `account_name` LIKE '%Furniture%' OR `account_name` LIKE '%Vehicle%');
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Current Liabilities' WHERE `account_group` = 'Liabilities' AND (`account_name` LIKE '%Payable%' OR `account_name` LIKE '%VAT%');
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Long Term Liabilities' WHERE `account_group` = 'Liabilities' AND (`account_name` LIKE '%Loan%' OR `account_name` LIKE '%Mortgage%');
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Equity' WHERE `account_group` = 'Equity';
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Premium Income' WHERE `account_group` = 'Income' AND `account_name` LIKE '%Premium%';
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Commission Income' WHERE `account_group` = 'Income' AND `account_name` LIKE '%Commission%';
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Operating Expenses' WHERE `account_group` = 'Expenses' AND (`account_name` LIKE '%Salary%' OR `account_name` LIKE '%Rent%' OR `account_name` LIKE '%Utilities%');
UPDATE `chart_of_accounts` SET `account_subgroup` = 'Claims Expenses' WHERE `account_group` = 'Expenses' AND `account_name` LIKE '%Claims%';

-- ============================================================
-- User Permissions Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `permission_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `module_name` VARCHAR(100) NOT NULL,
  `can_view` TINYINT(1) DEFAULT 0,
  `can_create` TINYINT(1) DEFAULT 0,
  `can_edit` TINYINT(1) DEFAULT 0,
  `can_delete` TINYINT(1) DEFAULT 0,
  `can_approve` TINYINT(1) DEFAULT 0,
  `can_export` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `user_module` (`user_id`, `module_name`),
  KEY `idx_module` (`module_name`),
  CONSTRAINT `fk_permission_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role Permissions Table
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_permission_id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` INT(11) NOT NULL,
  `module_name` VARCHAR(100) NOT NULL,
  `can_view` TINYINT(1) DEFAULT 0,
  `can_create` TINYINT(1) DEFAULT 0,
  `can_edit` TINYINT(1) DEFAULT 0,
  `can_delete` TINYINT(1) DEFAULT 0,
  `can_approve` TINYINT(1) DEFAULT 0,
  `can_export` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_permission_id`),
  UNIQUE KEY `role_module` (`role_id`, `module_name`),
  KEY `idx_role_module` (`module_name`),
  CONSTRAINT `fk_role_permission` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default permissions for Admin role
INSERT INTO `role_permissions` (`role_id`, `module_name`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_approve`, `can_export`) VALUES
(1, 'Customers', 1, 1, 1, 1, 1, 1),
(1, 'Policies', 1, 1, 1, 1, 1, 1),
(1, 'Claims', 1, 1, 1, 1, 1, 1),
(1, 'Sales', 1, 1, 1, 1, 1, 1),
(1, 'Receipts', 1, 1, 1, 1, 1, 1),
(1, 'Payments', 1, 1, 1, 1, 1, 1),
(1, 'Debit Notes', 1, 1, 1, 1, 1, 1),
(1, 'Credit Notes', 1, 1, 1, 1, 1, 1),
(1, 'Accounting', 1, 1, 1, 1, 1, 1),
(1, 'Reports', 1, 0, 0, 0, 0, 1),
(1, 'HR', 1, 1, 1, 1, 1, 1),
(1, 'Settings', 1, 1, 1, 1, 1, 1),
(1, 'Users', 1, 1, 1, 1, 1, 1),
(1, 'Backup', 1, 1, 0, 0, 0, 1);

-- ============================================================
-- Company Settings Table (Enhanced)
-- ============================================================
CREATE TABLE IF NOT EXISTS `company_settings` (
  `setting_id` INT(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) NOT NULL,
  `company_name` VARCHAR(255) NOT NULL,
  `trade_license_number` VARCHAR(100) DEFAULT NULL,
  `tax_registration_number` VARCHAR(100) DEFAULT NULL,
  `address_line1` VARCHAR(255) DEFAULT NULL,
  `address_line2` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `emirate_id` INT(11) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'United Arab Emirates',
  `po_box` VARCHAR(50) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `fax` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `fiscal_year_start` VARCHAR(5) DEFAULT '01-01',
  `fiscal_year_end` VARCHAR(5) DEFAULT '12-31',
  `base_currency` VARCHAR(3) DEFAULT 'AED',
  `date_format` VARCHAR(20) DEFAULT 'd/m/Y',
  `time_zone` VARCHAR(50) DEFAULT 'Asia/Dubai',
  `default_vat_percentage` DECIMAL(5,2) DEFAULT 5.00,
  `backup_enabled` TINYINT(1) DEFAULT 1,
  `backup_frequency` ENUM('daily', 'weekly', 'monthly') DEFAULT 'daily',
  `backup_path` VARCHAR(255) DEFAULT '/backups',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `company_id` (`company_id`),
  KEY `fk_company_setting_emirate` (`emirate_id`),
  CONSTRAINT `fk_company_setting` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_company_setting_emirate` FOREIGN KEY (`emirate_id`) REFERENCES `emirates` (`emirate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default company settings
INSERT INTO `company_settings` (`company_id`, `company_name`, `base_currency`, `default_vat_percentage`, `backup_enabled`)
SELECT `company_id`, `company_name`, 'AED', 5.00, 1
FROM `companies`
WHERE NOT EXISTS (SELECT 1 FROM `company_settings` WHERE `company_settings`.`company_id` = `companies`.`company_id`)
LIMIT 1;

-- Database Backups Table
CREATE TABLE IF NOT EXISTS `database_backups` (
  `backup_id` INT(11) NOT NULL AUTO_INCREMENT,
  `backup_name` VARCHAR(255) NOT NULL,
  `backup_file_path` VARCHAR(500) NOT NULL,
  `backup_size` BIGINT DEFAULT 0,
  `backup_type` ENUM('manual', 'automatic', 'scheduled') DEFAULT 'manual',
  `status` ENUM('in_progress', 'completed', 'failed') DEFAULT 'in_progress',
  `error_message` TEXT,
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_id` INT(11) NOT NULL,
  PRIMARY KEY (`backup_id`),
  KEY `idx_backup_date` (`created_at`),
  KEY `idx_status` (`status`),
  KEY `fk_backup_created_by` (`created_by`),
  CONSTRAINT `fk_backup_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Indexes for Performance
-- ============================================================
CREATE INDEX `idx_receipt_company_date` ON `receipt_vouchers` (`company_id`, `voucher_date`);
CREATE INDEX `idx_debit_note_company_date` ON `debit_notes` (`company_id`, `debit_note_date`);
CREATE INDEX `idx_credit_note_company_date` ON `credit_notes` (`company_id`, `credit_note_date`);
