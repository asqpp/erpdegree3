-- ================================================================================
-- SECTION 4: INSURANCE-SPECIFIC TABLES (30 TABLES)
-- ================================================================================

-- Table: policy_types
CREATE TABLE IF NOT EXISTS `policy_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `name_ar` VARCHAR(191) COMMENT 'Arabic name',
    `category` VARCHAR(191) COMMENT 'motor, health, life, property, marine, travel',
    `description` TEXT,
    `min_coverage_period` INTEGER DEFAULT 12 COMMENT 'in months',
    `max_coverage_period` INTEGER DEFAULT 12,
    `requires_inspection` TINYINT(1) DEFAULT 0,
    `requires_medical` TINYINT(1) DEFAULT 0,
    `default_commission_rate` DECIMAL(5,2) DEFAULT 0.00,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: policies
CREATE TABLE IF NOT EXISTS `policies` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_no` VARCHAR(191) UNIQUE NOT NULL,
    `policy_type_id` INTEGER NOT NULL,
    `customer_id` INTEGER NOT NULL,
    `agent_id` INTEGER,
    `broker_id` INTEGER,
    `product_id` INTEGER,
    `issue_date` DATE NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `sum_insured` DECIMAL(15,2) NOT NULL,
    `premium_amount` DECIMAL(15,2) NOT NULL,
    `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
    `total_premium` DECIMAL(15,2) NOT NULL COMMENT 'Premium + VAT',
    `premium_frequency` VARCHAR(20) DEFAULT 'annual' COMMENT 'annual, semi-annual, quarterly, monthly',
    `payment_mode` VARCHAR(50),
    `no_of_installments` INTEGER DEFAULT 1,
    `status` VARCHAR(191) DEFAULT 'active' COMMENT 'active, expired, cancelled, suspended',
    `underwriter_id` INTEGER,
    `risk_category` VARCHAR(50),
    `special_conditions` TEXT,
    `exclusions` TEXT,
    `policy_document_path` VARCHAR(255),
    `notes` TEXT,
    `branch_id` INTEGER,
    `currency_id` INTEGER DEFAULT 1,
    `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
    `premium_in_currency` DECIMAL(15,2),
    `premium_in_base` DECIMAL(15,2),
    `sum_insured_in_currency` DECIMAL(15,2),
    `sum_insured_in_base` DECIMAL(15,2),
    `issue_date_hijri` VARCHAR(20),
    `start_date_hijri` VARCHAR(20),
    `end_date_hijri` VARCHAR(20),
    `renewal_notice_sent` TINYINT(1) DEFAULT 0,
    `renewal_notice_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_type_id`) REFERENCES `policy_types`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`agent_id`) REFERENCES `agents`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`broker_id`) REFERENCES `brokers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`underwriter_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    INDEX `idx_policy_no` (`policy_no`),
    INDEX `idx_policy_customer` (`customer_id`),
    INDEX `idx_policy_status` (`status`),
    INDEX `idx_policy_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: policy_schedules
CREATE TABLE IF NOT EXISTS `policy_schedules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER NOT NULL,
    `item_description` TEXT,
    `item_make` VARCHAR(100),
    `item_model` VARCHAR(100),
    `item_year` INTEGER,
    `chassis_no` VARCHAR(100),
    `plate_no` VARCHAR(50),
    `sum_insured` DECIMAL(15,2) DEFAULT 0.00,
    `premium_amount` DECIMAL(15,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: policy_endorsements
CREATE TABLE IF NOT EXISTS `policy_endorsements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER NOT NULL,
    `endorsement_no` VARCHAR(191) UNIQUE NOT NULL,
    `endorsement_date` DATE NOT NULL,
    `endorsement_type` VARCHAR(191) COMMENT 'addition, deletion, modification, transfer',
    `description` TEXT,
    `premium_adjustment` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'can be negative',
    `sum_insured_adjustment` DECIMAL(15,2) DEFAULT 0.00,
    `effective_from` DATE,
    `status` VARCHAR(191) DEFAULT 'active',
    `approved_by` INTEGER,
    `approval_date` DATE,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: policy_renewals
CREATE TABLE IF NOT EXISTS `policy_renewals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `old_policy_id` INTEGER NOT NULL,
    `new_policy_id` INTEGER,
    `old_policy_no` VARCHAR(191),
    `renewal_date` DATE NOT NULL,
    `new_start_date` DATE NOT NULL,
    `new_end_date` DATE NOT NULL,
    `old_premium` DECIMAL(15,2) NOT NULL,
    `new_premium` DECIMAL(15,2) NOT NULL,
    `old_sum_insured` DECIMAL(15,2) NOT NULL,
    `new_sum_insured` DECIMAL(15,2) NOT NULL,
    `ncb_percentage` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'No Claim Bonus',
    `ncb_amount` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(191) DEFAULT 'pending' COMMENT 'pending, renewed, declined',
    `notes` TEXT,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`old_policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`new_policy_id`) REFERENCES `policies`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: policy_cancellations
CREATE TABLE IF NOT EXISTS `policy_cancellations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER NOT NULL,
    `cancellation_date` DATE NOT NULL,
    `cancellation_type` VARCHAR(50) COMMENT 'customer_request, non_payment, fraud, other',
    `cancellation_reason` TEXT,
    `earned_premium` DECIMAL(15,2) DEFAULT 0.00,
    `refund_amount` DECIMAL(15,2) DEFAULT 0.00,
    `cancellation_charges` DECIMAL(15,2) DEFAULT 0.00,
    `net_refund` DECIMAL(15,2) DEFAULT 0.00,
    `refund_status` VARCHAR(50) DEFAULT 'pending',
    `refund_date` DATE,
    `refund_reference` VARCHAR(100),
    `status` VARCHAR(191) DEFAULT 'pending',
    `approved_by` INTEGER,
    `approval_date` DATE,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claim_types
CREATE TABLE IF NOT EXISTS `claim_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(191) UNIQUE NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `name_ar` VARCHAR(191),
    `description` TEXT,
    `applicable_policy_types` TEXT COMMENT 'JSON array of policy type IDs',
    `requires_police_report` TINYINT(1) DEFAULT 0,
    `requires_medical_report` TINYINT(1) DEFAULT 0,
    `max_processing_days` INTEGER DEFAULT 30,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claims
CREATE TABLE IF NOT EXISTS `claims` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `claim_no` VARCHAR(191) UNIQUE NOT NULL,
    `policy_id` INTEGER NOT NULL,
    `customer_id` INTEGER NOT NULL,
    `claim_type_id` INTEGER,
    `date_of_loss` DATE NOT NULL,
    `date_reported` DATE NOT NULL,
    `date_registered` DATE NOT NULL,
    `loss_description` TEXT,
    `loss_location` VARCHAR(255),
    `police_report_no` VARCHAR(100),
    `police_station` VARCHAR(255),
    `claim_amount` DECIMAL(15,2) NOT NULL,
    `estimated_loss` DECIMAL(15,2),
    `approved_amount` DECIMAL(15,2) DEFAULT 0.00,
    `settled_amount` DECIMAL(15,2) DEFAULT 0.00,
    `deductible_amount` DECIMAL(15,2) DEFAULT 0.00,
    `outstanding_amount` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(191) DEFAULT 'registered' COMMENT 'registered, investigating, approved, rejected, settled, closed',
    `priority` VARCHAR(20) DEFAULT 'normal' COMMENT 'low, normal, high, urgent',
    `investigator_id` INTEGER,
    `investigation_required` TINYINT(1) DEFAULT 0,
    `investigation_date` DATE,
    `investigation_report` TEXT,
    `investigation_findings` TEXT,
    `investigation_recommendation` VARCHAR(50),
    `approval_date` DATE,
    `approved_by` INTEGER,
    `approval_notes` TEXT,
    `rejection_reason` TEXT,
    `settlement_date` DATE,
    `settlement_method` VARCHAR(50) COMMENT 'cash, cheque, bank_transfer, repair',
    `payment_reference` VARCHAR(100),
    `branch_id` INTEGER,
    `assigned_to` INTEGER COMMENT 'Claims handler',
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`claim_type_id`) REFERENCES `claim_types`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`investigator_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_claim_policy` (`policy_id`),
    INDEX `idx_claim_customer` (`customer_id`),
    INDEX `idx_claim_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claim_documents
CREATE TABLE IF NOT EXISTS `claim_documents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `claim_id` INTEGER NOT NULL,
    `document_type` VARCHAR(191) COMMENT 'claim_form, police_report, medical_report, photos, estimate, invoice, other',
    `document_name` VARCHAR(191),
    `file_path` VARCHAR(255),
    `file_size` INTEGER,
    `mime_type` VARCHAR(100),
    `uploaded_by` INTEGER,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`claim_id`) REFERENCES `claims`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claim_investigations
CREATE TABLE IF NOT EXISTS `claim_investigations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `claim_id` INTEGER NOT NULL,
    `investigator_id` INTEGER NOT NULL,
    `investigation_type` VARCHAR(50) COMMENT 'field, desktop, surveillance',
    `assigned_date` DATE NOT NULL,
    `investigation_date` DATE NOT NULL,
    `completion_date` DATE,
    `investigation_report` TEXT,
    `findings` TEXT,
    `fraud_suspected` TINYINT(1) DEFAULT 0,
    `fraud_indicators` TEXT,
    `recommendation` VARCHAR(50) COMMENT 'approve, reject, further_investigation',
    `recommended_amount` DECIMAL(15,2),
    `status` VARCHAR(191) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`claim_id`) REFERENCES `claims`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`investigator_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claim_approvals
CREATE TABLE IF NOT EXISTS `claim_approvals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `claim_id` INTEGER NOT NULL,
    `approval_level` INTEGER DEFAULT 1,
    `approver_id` INTEGER NOT NULL,
    `approval_date` DATE NOT NULL,
    `approved_amount` DECIMAL(15,2) NOT NULL,
    `action` VARCHAR(20) COMMENT 'approved, rejected, referred',
    `remarks` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`claim_id`) REFERENCES `claims`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approver_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claim_settlements
CREATE TABLE IF NOT EXISTS `claim_settlements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `claim_id` INTEGER NOT NULL,
    `settlement_date` DATE NOT NULL,
    `settlement_type` VARCHAR(50) COMMENT 'cash, repair, replacement',
    `settlement_amount` DECIMAL(15,2) NOT NULL,
    `payment_method` VARCHAR(50),
    `cheque_no` VARCHAR(100),
    `reference_no` VARCHAR(100),
    `bank_account_id` INTEGER,
    `beneficiary_name` VARCHAR(255),
    `beneficiary_account` VARCHAR(100),
    `beneficiary_iban` VARCHAR(34),
    `notes` TEXT,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`claim_id`) REFERENCES `claims`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: claim_recoveries
CREATE TABLE IF NOT EXISTS `claim_recoveries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `claim_id` INTEGER NOT NULL,
    `recovery_from` VARCHAR(255) COMMENT 'Third party, salvage, subrogation',
    `recovery_date` DATE NOT NULL,
    `recovery_amount` DECIMAL(15,2) NOT NULL,
    `recovery_type` VARCHAR(191) COMMENT 'salvage, subrogation, deductible',
    `description` TEXT,
    `status` VARCHAR(191) DEFAULT 'pending',
    `received_date` DATE,
    `user_id` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`claim_id`) REFERENCES `claims`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: underwriting_records
CREATE TABLE IF NOT EXISTS `underwriting_records` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER,
    `customer_id` INTEGER NOT NULL,
    `underwriting_date` DATE NOT NULL,
    `risk_factors` TEXT,
    `risk_score` INTEGER COMMENT '1-100',
    `risk_category` VARCHAR(191) COMMENT 'low, medium, high, very_high',
    `decision` VARCHAR(50) COMMENT 'accept, reject, refer, accept_with_conditions',
    `conditions` TEXT,
    `loading_percentage` DECIMAL(5,2) DEFAULT 0.00,
    `premium_suggested` DECIMAL(15,2),
    `notes` TEXT,
    `underwriter_id` INTEGER,
    `approved_by` INTEGER,
    `approval_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`underwriter_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: risk_assessments
CREATE TABLE IF NOT EXISTS `risk_assessments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER,
    `customer_id` INTEGER,
    `assessment_date` DATE NOT NULL,
    `assessment_type` VARCHAR(50) COMMENT 'pre_issuance, annual_review, claim_trigger',
    `risk_factors` TEXT,
    `risk_level` VARCHAR(20) COMMENT 'low, medium, high, very_high',
    `recommendations` TEXT,
    `action_required` TEXT,
    `assessed_by` INTEGER,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`assessed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: reinsurance_treaties
CREATE TABLE IF NOT EXISTS `reinsurance_treaties` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `treaty_no` VARCHAR(191) UNIQUE NOT NULL,
    `treaty_name` VARCHAR(255) NOT NULL,
    `reinsurer_name` VARCHAR(191) NOT NULL,
    `reinsurer_code` VARCHAR(50),
    `treaty_type` VARCHAR(191) NOT NULL COMMENT 'quota_share, surplus, excess_of_loss, stop_loss',
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `applicable_policy_types` TEXT COMMENT 'JSON array',
    `retention_limit` DECIMAL(15,2) NOT NULL,
    `cession_rate` DECIMAL(5,2) NOT NULL COMMENT 'Percentage ceded to reinsurer',
    `commission_rate` DECIMAL(5,2) DEFAULT 0.00,
    `profit_commission_rate` DECIMAL(5,2) DEFAULT 0.00,
    `claims_limit` DECIMAL(15,2),
    `aggregate_limit` DECIMAL(15,2),
    `status` VARCHAR(191) DEFAULT 'active',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: reinsurance_cessions
CREATE TABLE IF NOT EXISTS `reinsurance_cessions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER NOT NULL,
    `treaty_id` INTEGER NOT NULL,
    `cession_date` DATE NOT NULL,
    `gross_premium` DECIMAL(15,2) NOT NULL,
    `ceded_percentage` DECIMAL(5,2) NOT NULL,
    `ceded_premium` DECIMAL(15,2) NOT NULL,
    `commission_amount` DECIMAL(15,2) DEFAULT 0.00,
    `net_premium` DECIMAL(15,2),
    `status` VARCHAR(50) DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`treaty_id`) REFERENCES `reinsurance_treaties`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: premiums
CREATE TABLE IF NOT EXISTS `premiums` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER NOT NULL,
    `installment_no` INTEGER DEFAULT 1,
    `receipt_id` INTEGER,
    `due_date` DATE NOT NULL,
    `premium_amount` DECIMAL(15,2) NOT NULL,
    `vat_amount` DECIMAL(15,2) DEFAULT 0.00,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
    `balance_amount` DECIMAL(15,2) DEFAULT 0.00,
    `payment_date` DATE,
    `status` VARCHAR(191) DEFAULT 'unpaid' COMMENT 'unpaid, partial, paid, overdue',
    `late_fee` DECIMAL(15,2) DEFAULT 0.00,
    `grace_period_days` INTEGER DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    INDEX `idx_premium_policy` (`policy_id`),
    INDEX `idx_premium_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: commissions
CREATE TABLE IF NOT EXISTS `commissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `policy_id` INTEGER NOT NULL,
    `agent_id` INTEGER,
    `broker_id` INTEGER,
    `commission_type` VARCHAR(191) NOT NULL COMMENT 'direct, renewal, cancellation',
    `base_amount` DECIMAL(15,2) NOT NULL COMMENT 'Premium or sum insured',
    `commission_rate` DECIMAL(5,2) NOT NULL,
    `commission_amount` DECIMAL(15,2) NOT NULL,
    `vat_on_commission` DECIMAL(15,2) DEFAULT 0.00,
    `net_commission` DECIMAL(15,2),
    `payment_date` DATE,
    `payment_status` VARCHAR(191) DEFAULT 'unpaid' COMMENT 'unpaid, paid, cancelled',
    `payment_reference` VARCHAR(100),
    `payment_id` INTEGER,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`policy_id`) REFERENCES `policies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`agent_id`) REFERENCES `agents`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`broker_id`) REFERENCES `brokers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================================
-- CONTINUE TO NEXT FILE...
-- ================================================================================
