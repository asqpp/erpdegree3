-- ================================================================================
-- SECTION 3: MASTER DATA TABLES (20 TABLES)
-- ================================================================================

-- Table: customers (Enhanced for Insurance)
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `first_name` VARCHAR(191),
    `last_name` VARCHAR(191),
    `company_name` VARCHAR(191),
    `email` VARCHAR(191),
    `phone` VARCHAR(191),
    `mobile` VARCHAR(191),
    `address` TEXT,
    `city` VARCHAR(191),
    `state` VARCHAR(191),
    `pin` VARCHAR(20),
    `country` VARCHAR(191) DEFAULT 'UAE',
    `emirate_id` INTEGER COMMENT 'Link to emirates table',
    `dob` DATE,
    `nationality` VARCHAR(100),
    `passport_no` VARCHAR(50),
    `emirates_id` VARCHAR(20) COMMENT 'UAE ID Number',
    `pan_aadhar` VARCHAR(191),
    `trn_no` VARCHAR(191) COMMENT 'Tax Registration Number',
    `gst_no` VARCHAR(191),
    `ctype` VARCHAR(1) DEFAULT 'I' COMMENT 'I=Individual, C=Corporate',
    `customer_group_id` INTEGER,
    `creditlimit` DECIMAL(15,2) DEFAULT 0.00,
    `credit_days` INTEGER DEFAULT 0,
    `kyc_status` VARCHAR(20) DEFAULT 'pending',
    `kyc_verified_date` DATE,
    `risk_rating` VARCHAR(20),
    `status` VARCHAR(191) DEFAULT 'active',
    `branch_id` INTEGER,
    `accode` VARCHAR(191),
    `portal_access` TINYINT(1) DEFAULT 0,
    `portal_username` VARCHAR(100),
    `portal_password` VARCHAR(191),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE SET NULL,
    INDEX `idx_customer_code` (`code`),
    INDEX `idx_customer_email` (`email`),
    INDEX `idx_customer_type` (`ctype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: customer_groups
CREATE TABLE IF NOT EXISTS `customer_groups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT,
    `discount_percentage` DECIMAL(5,2) DEFAULT 0.00,
    `credit_limit` DECIMAL(15,2) DEFAULT 0.00,
    `credit_days` INTEGER DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: customer_contacts
CREATE TABLE IF NOT EXISTS `customer_contacts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INTEGER NOT NULL,
    `contact_name` VARCHAR(191) NOT NULL,
    `designation` VARCHAR(100),
    `email` VARCHAR(191),
    `phone` VARCHAR(50),
    `mobile` VARCHAR(50),
    `is_primary` TINYINT(1) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: customer_addresses
CREATE TABLE IF NOT EXISTS `customer_addresses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INTEGER NOT NULL,
    `address_type` VARCHAR(50) COMMENT 'billing, shipping, communication',
    `address_line1` VARCHAR(255),
    `address_line2` VARCHAR(255),
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `emirate_id` INTEGER,
    `postal_code` VARCHAR(20),
    `country` VARCHAR(100) DEFAULT 'UAE',
    `is_default` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: customer_kyc
CREATE TABLE IF NOT EXISTS `customer_kyc` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INTEGER NOT NULL,
    `document_type` VARCHAR(50) COMMENT 'passport, emirates_id, trade_license, etc',
    `document_number` VARCHAR(100),
    `issue_date` DATE,
    `expiry_date` DATE,
    `issuing_authority` VARCHAR(255),
    `document_file_path` VARCHAR(255),
    `verification_status` VARCHAR(20) DEFAULT 'pending',
    `verified_by` INTEGER,
    `verified_date` DATE,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: agents
CREATE TABLE IF NOT EXISTS `agents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `email` VARCHAR(191),
    `phone` VARCHAR(191),
    `mobile` VARCHAR(191),
    `address` TEXT,
    `city` VARCHAR(100),
    `emirate_id` INTEGER,
    `commission_rate` DECIMAL(5,2) DEFAULT 0.00,
    `commission_type` VARCHAR(20) DEFAULT 'percentage' COMMENT 'percentage or fixed',
    `region` VARCHAR(100),
    `license_no` VARCHAR(100),
    `license_expiry` DATE,
    `status` VARCHAR(191) DEFAULT 'active',
    `branch_id` INTEGER,
    `accode` VARCHAR(191),
    `bank_name` VARCHAR(191),
    `bank_account` VARCHAR(191),
    `iban` VARCHAR(34),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE SET NULL,
    INDEX `idx_agent_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: brokers
CREATE TABLE IF NOT EXISTS `brokers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `company_name` VARCHAR(191),
    `email` VARCHAR(191),
    `phone` VARCHAR(191),
    `mobile` VARCHAR(191),
    `address` TEXT,
    `city` VARCHAR(100),
    `emirate_id` INTEGER,
    `commission_rate` DECIMAL(5,2) DEFAULT 0.00,
    `commission_type` VARCHAR(20) DEFAULT 'percentage',
    `license_no` VARCHAR(100),
    `license_expiry` DATE,
    `trn_no` VARCHAR(191),
    `status` VARCHAR(191) DEFAULT 'active',
    `branch_id` INTEGER,
    `accode` VARCHAR(191),
    `bank_name` VARCHAR(191),
    `bank_account` VARCHAR(191),
    `iban` VARCHAR(34),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE SET NULL,
    INDEX `idx_broker_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `contact_name` VARCHAR(191),
    `email` VARCHAR(191),
    `phone` VARCHAR(191),
    `mobile` VARCHAR(191),
    `address` TEXT,
    `city` VARCHAR(191),
    `state` VARCHAR(191),
    `pin` VARCHAR(20),
    `country` VARCHAR(100) DEFAULT 'UAE',
    `emirate_id` INTEGER,
    `trn_no` VARCHAR(191),
    `gst_no` VARCHAR(191),
    `pan_no` VARCHAR(191),
    `credit_days` INTEGER DEFAULT 0,
    `status` VARCHAR(191) DEFAULT 'active',
    `branch_id` INTEGER,
    `accode` VARCHAR(191),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`accode`) REFERENCES `accounts`(`accode`) ON DELETE SET NULL,
    INDEX `idx_supplier_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: products (Insurance Products)
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT,
    `category` VARCHAR(191),
    `product_type` VARCHAR(50) COMMENT 'insurance, service, goods',
    `policy_type_id` INTEGER COMMENT 'Link to policy_types for insurance products',
    `unit` VARCHAR(191),
    `hsn_code` VARCHAR(191),
    `base_premium` DECIMAL(15,2) DEFAULT 0.00,
    `minimum_sum_insured` DECIMAL(15,2) DEFAULT 0.00,
    `maximum_sum_insured` DECIMAL(15,2) DEFAULT 0.00,
    `default_period_months` INTEGER DEFAULT 12,
    `commission_percentage` DECIMAL(5,2) DEFAULT 0.00,
    `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
    `vat_applicable` TINYINT(1) DEFAULT 1,
    `status` VARCHAR(191) DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_product_type` (`product_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: items (Inventory)
CREATE TABLE IF NOT EXISTS `items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT,
    `category_id` INTEGER,
    `unit_id` INTEGER,
    `hsn_code` VARCHAR(191),
    `barcode` VARCHAR(191),
    `sale_price` DECIMAL(15,2) DEFAULT 0.00,
    `purchase_price` DECIMAL(15,2) DEFAULT 0.00,
    `mrp` DECIMAL(15,2) DEFAULT 0.00,
    `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
    `vat_rate` DECIMAL(5,2) DEFAULT 5.00,
    `reorder_level` INTEGER DEFAULT 0,
    `status` VARCHAR(191) DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_item_code` (`code`),
    INDEX `idx_item_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT,
    `parent_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: units
CREATE TABLE IF NOT EXISTS `units` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `symbol` VARCHAR(20),
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: departments
CREATE TABLE IF NOT EXISTS `departments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT,
    `manager_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: designations
CREATE TABLE IF NOT EXISTS `designations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================================
-- CONTINUE TO NEXT FILE...
-- ================================================================================
