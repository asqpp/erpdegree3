-- ================================================================================
-- SECTION 5: GCC/UAE SPECIFIC TABLES (15 TABLES)
-- ================================================================================

-- Table: currencies (10 GCC currencies + major international)
CREATE TABLE IF NOT EXISTS `currencies` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(3) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `name_ar` VARCHAR(100),
    `symbol` VARCHAR(10),
    `decimal_places` INTEGER DEFAULT 2,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000 COMMENT 'Rate against base currency (AED)',
    `is_base` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `country_code` VARCHAR(2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INTEGER,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: exchange_rate_history
CREATE TABLE IF NOT EXISTS `exchange_rate_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `currency_id` INTEGER NOT NULL,
    `from_currency_id` INTEGER NOT NULL COMMENT 'Usually base currency',
    `exchange_rate` DECIMAL(15,6) NOT NULL,
    `effective_date` DATE NOT NULL,
    `source` VARCHAR(100) COMMENT 'UAE Central Bank, Manual, API',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_by` INTEGER,
    FOREIGN KEY (`currency_id`) REFERENCES `currencies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`from_currency_id`) REFERENCES `currencies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_exchange_date` (`effective_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: emirates
CREATE TABLE IF NOT EXISTS `emirates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(10) NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_ar` VARCHAR(100),
    `abbreviation` VARCHAR(10),
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: uae_banks
CREATE TABLE IF NOT EXISTS `uae_banks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bank_code` VARCHAR(10) NOT NULL,
    `bank_name_en` VARCHAR(200) NOT NULL,
    `bank_name_ar` VARCHAR(200),
    `swift_code` VARCHAR(11),
    `routing_code` VARCHAR(20),
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: vat_configurations
CREATE TABLE IF NOT EXISTS `vat_configurations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vat_code` VARCHAR(10) UNIQUE NOT NULL,
    `description` VARCHAR(200),
    `vat_rate` DECIMAL(5,2) NOT NULL,
    `effective_from` DATE NOT NULL,
    `effective_to` DATE,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: vat_returns
CREATE TABLE IF NOT EXISTS `vat_returns` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `company_id` INTEGER NOT NULL,
    `return_period` VARCHAR(7) NOT NULL COMMENT 'YYYY-MM format',
    `period_from` DATE NOT NULL,
    `period_to` DATE NOT NULL,
    `box_1_standard_rated_supplies` DECIMAL(15,2) DEFAULT 0.00,
    `box_2_tax_refunds` DECIMAL(15,2) DEFAULT 0.00,
    `box_3_zero_rated_supplies` DECIMAL(15,2) DEFAULT 0.00,
    `box_4_exempt_supplies` DECIMAL(15,2) DEFAULT 0.00,
    `box_5_goods_imported_gcc` DECIMAL(15,2) DEFAULT 0.00,
    `box_6_output_vat` DECIMAL(15,2) DEFAULT 0.00,
    `box_7_input_vat_recoverable` DECIMAL(15,2) DEFAULT 0.00,
    `box_8_input_vat_corrections` DECIMAL(15,2) DEFAULT 0.00,
    `box_9_net_vat_due` DECIMAL(15,2) DEFAULT 0.00,
    `total_sales_excl_vat` DECIMAL(15,2) DEFAULT 0.00,
    `total_purchases_excl_vat` DECIMAL(15,2) DEFAULT 0.00,
    `filing_date` DATE,
    `filing_status` VARCHAR(20) DEFAULT 'draft' COMMENT 'draft, filed, paid',
    `submitted_by` INTEGER,
    `submission_date` TIMESTAMP,
    `confirmation_number` VARCHAR(100),
    `payment_date` DATE,
    `payment_reference` VARCHAR(100),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ia_returns (Insurance Authority Returns - UAE specific)
CREATE TABLE IF NOT EXISTS `ia_returns` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `company_id` INTEGER NOT NULL,
    `return_type` VARCHAR(50) NOT NULL COMMENT 'quarterly, annual, statistical',
    `period_from` DATE NOT NULL,
    `period_to` DATE NOT NULL,
    `gross_premium_written` DECIMAL(15,2) DEFAULT 0.00,
    `net_premium_earned` DECIMAL(15,2) DEFAULT 0.00,
    `reinsurance_premium` DECIMAL(15,2) DEFAULT 0.00,
    `total_claims_reported` INTEGER DEFAULT 0,
    `total_claims_settled` INTEGER DEFAULT 0,
    `total_claims_paid` DECIMAL(15,2) DEFAULT 0.00,
    `outstanding_claims_reserve` DECIMAL(15,2) DEFAULT 0.00,
    `policies_issued` INTEGER DEFAULT 0,
    `policies_cancelled` INTEGER DEFAULT 0,
    `policies_renewed` INTEGER DEFAULT 0,
    `active_policies` INTEGER DEFAULT 0,
    `total_sum_insured` DECIMAL(15,2) DEFAULT 0.00,
    `commission_paid` DECIMAL(15,2) DEFAULT 0.00,
    `investment_income` DECIMAL(15,2) DEFAULT 0.00,
    `operating_expenses` DECIMAL(15,2) DEFAULT 0.00,
    `filing_date` DATE,
    `filing_status` VARCHAR(20) DEFAULT 'draft',
    `submitted_by` INTEGER,
    `submission_date` TIMESTAMP,
    `acknowledgment_no` VARCHAR(100),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: consent_management (GDPR/Data Privacy)
CREATE TABLE IF NOT EXISTS `consent_management` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INTEGER,
    `consent_type` VARCHAR(100) NOT NULL COMMENT 'marketing, data_processing, third_party_sharing',
    `consent_given` TINYINT(1) DEFAULT 0,
    `consent_date` DATE,
    `consent_method` VARCHAR(50) COMMENT 'online, email, phone, written',
    `purpose` TEXT,
    `expiry_date` DATE,
    `revoked` TINYINT(1) DEFAULT 0,
    `revoked_date` DATE,
    `revoked_method` VARCHAR(50),
    `ip_address` VARCHAR(50),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: zakat_calculation
CREATE TABLE IF NOT EXISTS `zakat_calculation` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `company_id` INTEGER NOT NULL,
    `hijri_year` INTEGER NOT NULL,
    `gregorian_year` INTEGER NOT NULL,
    `zakatable_assets` DECIMAL(15,2) DEFAULT 0.00,
    `zakatable_liabilities` DECIMAL(15,2) DEFAULT 0.00,
    `net_zakatable_amount` DECIMAL(15,2) DEFAULT 0.00,
    `nisab_threshold` DECIMAL(15,2) COMMENT 'Current nisab value',
    `zakat_rate` DECIMAL(5,2) DEFAULT 2.5,
    `zakat_due` DECIMAL(15,2) DEFAULT 0.00,
    `payment_date` DATE,
    `payment_reference` VARCHAR(100),
    `status` VARCHAR(20) DEFAULT 'calculated',
    `notes` TEXT,
    `calculated_by` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`calculated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hijri_calendar
CREATE TABLE IF NOT EXISTS `hijri_calendar` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `gregorian_date` DATE UNIQUE NOT NULL,
    `hijri_date` VARCHAR(20),
    `hijri_day` INTEGER,
    `hijri_month` INTEGER,
    `hijri_year` INTEGER,
    `month_name_ar` VARCHAR(50),
    `month_name_en` VARCHAR(50),
    `is_holiday` TINYINT(1) DEFAULT 0,
    `holiday_name_en` VARCHAR(100),
    `holiday_name_ar` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_hijri_date` (`hijri_year`, `hijri_month`, `hijri_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================================
-- SECTION 6: TRANSACTION TABLES (Sales, Purchases, Receipts, Payments)
-- ================================================================================

-- Table: payment_types
CREATE TABLE IF NOT EXISTS `payment_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `name_ar` VARCHAR(191),
    `description` TEXT,
    `requires_reference` TINYINT(1) DEFAULT 0,
    `requires_bank_account` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sales (Sales Invoice Header)
CREATE TABLE IF NOT EXISTS `sales` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `invoice_no` VARCHAR(191) UNIQUE,
    `customer_id` INTEGER NOT NULL,
    `date` DATE NOT NULL,
    `due_date` DATE,
    `quotation_id` INTEGER COMMENT 'Link to quotation if converted',
    `policy_id` INTEGER COMMENT 'Link to policy for insurance invoices',
    `subtotal` DECIMAL(15,2) DEFAULT 0.00,
    `discount` DECIMAL(15,2) DEFAULT 0.00,
    `discount_percentage` DECIMAL(5,2) DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) DEFAULT 0.00,
    `vat_rate` DECIMAL(5,2) DEFAULT 5.00,
    `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `total_with_vat` DECIMAL(15,2),
    `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
    `balance_amount` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(191) DEFAULT 'pending' COMMENT 'pending, partial, paid, overdue, cancelled',
    `notes` TEXT,
    `terms_conditions` TEXT,
    `branch_id` INTEGER,
    `salesperson_id` INTEGER,
    `user_id` INTEGER,
    `currency_id` INTEGER DEFAULT 1,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
    `amount_in_currency` DECIMAL(15,2),
    `amount_in_base` DECIMAL(15,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_sales_date` (`date`),
    INDEX `idx_sales_customer` (`customer_id`),
    INDEX `idx_sales_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sales_items
CREATE TABLE IF NOT EXISTS `sales_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sale_id` INTEGER NOT NULL,
    `item_id` INTEGER,
    `product_id` INTEGER,
    `description` TEXT,
    `qty` DECIMAL(10,2) NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `discount` DECIMAL(15,2) DEFAULT 0.00,
    `discount_percentage` DECIMAL(5,2) DEFAULT 0.00,
    `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) DEFAULT 0.00,
    `vat_rate` DECIMAL(5,2) DEFAULT 5.00,
    `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
    `total` DECIMAL(15,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: quotations
CREATE TABLE IF NOT EXISTS `quotations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `quotation_no` VARCHAR(191) UNIQUE,
    `customer_id` INTEGER NOT NULL,
    `date` DATE NOT NULL,
    `valid_until` DATE,
    `reference` VARCHAR(100),
    `subtotal` DECIMAL(15,2) DEFAULT 0.00,
    `discount` DECIMAL(15,2) DEFAULT 0.00,
    `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `status` VARCHAR(191) DEFAULT 'pending' COMMENT 'pending, accepted, rejected, expired, converted',
    `converted_to_sale_id` INTEGER,
    `notes` TEXT,
    `terms_conditions` TEXT,
    `branch_id` INTEGER,
    `salesperson_id` INTEGER,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`converted_to_sale_id`) REFERENCES `sales`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchases
CREATE TABLE IF NOT EXISTS `purchases` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `invoice_no` VARCHAR(191),
    `supplier_id` INTEGER NOT NULL,
    `date` DATE NOT NULL,
    `due_date` DATE,
    `subtotal` DECIMAL(15,2) DEFAULT 0.00,
    `discount` DECIMAL(15,2) DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) DEFAULT 0.00,
    `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `total_with_vat` DECIMAL(15,2),
    `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
    `balance_amount` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(191) DEFAULT 'pending',
    `notes` TEXT,
    `branch_id` INTEGER,
    `user_id` INTEGER,
    `currency_id` INTEGER DEFAULT 1,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
    `amount_in_currency` DECIMAL(15,2),
    `amount_in_base` DECIMAL(15,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_purchase_date` (`date`),
    INDEX `idx_purchase_supplier` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: receipts
CREATE TABLE IF NOT EXISTS `receipts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `receipt_no` VARCHAR(191) UNIQUE,
    `date` DATE NOT NULL,
    `customer_id` INTEGER,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_type_id` INTEGER,
    `reference_no` VARCHAR(100),
    `cheque_no` VARCHAR(100),
    `cheque_date` DATE,
    `bank_account_id` INTEGER,
    `description` TEXT,
    `status` VARCHAR(191) DEFAULT 'posted',
    `branch_id` INTEGER,
    `user_id` INTEGER,
    `currency_id` INTEGER DEFAULT 1,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
    `amount_in_currency` DECIMAL(15,2),
    `amount_in_base` DECIMAL(15,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_receipt_date` (`date`),
    INDEX `idx_receipt_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: payments
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `payment_no` VARCHAR(191) UNIQUE,
    `date` DATE NOT NULL,
    `supplier_id` INTEGER,
    `payee_name` VARCHAR(191),
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_type_id` INTEGER,
    `reference_no` VARCHAR(100),
    `cheque_no` VARCHAR(100),
    `cheque_date` DATE,
    `bank_account_id` INTEGER,
    `description` TEXT,
    `status` VARCHAR(191) DEFAULT 'posted',
    `branch_id` INTEGER,
    `user_id` INTEGER,
    `currency_id` INTEGER DEFAULT 1,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
    `amount_in_currency` DECIMAL(15,2),
    `amount_in_base` DECIMAL(15,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_payment_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================================
-- END OF FILE
-- ================================================================================
