-- ================================================================================
-- SECTION 7: SAMPLE DATA & INDEXES
-- ================================================================================

-- ============================================================
-- SAMPLE DATA FOR GCC/UAE
-- ============================================================

-- Sample GCC Currencies
INSERT INTO `currencies` (`code`, `name`, `name_ar`, `symbol`, `decimal_places`, `exchange_rate`, `is_base`, `country_code`) VALUES
('AED', 'UAE Dirham', 'درهم إماراتي', 'د.إ', 2, 1.000000, 1, 'AE'),
('SAR', 'Saudi Riyal', 'ريال سعودي', 'ر.س', 2, 1.020000, 0, 'SA'),
('KWD', 'Kuwaiti Dinar', 'دينار كويتي', 'د.ك', 3, 0.088000, 0, 'KW'),
('BHD', 'Bahraini Dinar', 'دينار بحريني', '.د.ب', 3, 0.108000, 0, 'BH'),
('OMR', 'Omani Rial', 'ريال عماني', 'ر.ع.', 3, 0.110000, 0, 'OM'),
('QAR', 'Qatari Riyal', 'ريال قطري', 'ر.ق', 2, 1.044000, 0, 'QA'),
('USD', 'US Dollar', 'دولار أمريكي', '$', 2, 0.272000, 0, 'US'),
('EUR', 'Euro', 'يورو', '€', 2, 0.251000, 0, 'EU'),
('GBP', 'British Pound', 'جنيه استرليني', '£', 2, 0.215000, 0, 'GB'),
('INR', 'Indian Rupee', 'روبية هندية', '₹', 2, 22.500000, 0, 'IN')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Sample Emirates
INSERT INTO `emirates` (`code`, `name_en`, `name_ar`, `abbreviation`) VALUES
('DXB', 'Dubai', 'دبي', 'DXB'),
('AUH', 'Abu Dhabi', 'أبو ظبي', 'AUH'),
('SHJ', 'Sharjah', 'الشارقة', 'SHJ'),
('AJM', 'Ajman', 'عجمان', 'AJM'),
('UAQ', 'Umm Al Quwain', 'أم القيوين', 'UAQ'),
('RAK', 'Ras Al Khaimah', 'رأس الخيمة', 'RAK'),
('FUJ', 'Fujairah', 'الفجيرة', 'FUJ')
ON DUPLICATE KEY UPDATE `name_en` = VALUES(`name_en`);

-- Sample VAT Configurations
INSERT INTO `vat_configurations` (`vat_code`, `description`, `vat_rate`, `effective_from`) VALUES
('STD', 'Standard VAT Rate', 5.00, '2018-01-01'),
('ZER', 'Zero Rated', 0.00, '2018-01-01'),
('EXM', 'Exempt', 0.00, '2018-01-01')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- Sample UAE Banks
INSERT INTO `uae_banks` (`bank_code`, `bank_name_en`, `bank_name_ar`, `swift_code`) VALUES
('NBAD', 'First Abu Dhabi Bank', 'بنك أبوظبي الأول', 'NBAD AEAD'),
('EBIL', 'Emirates NBD', 'بنك الإمارات دبي الوطني', 'EBIL AEAD'),
('ADCB', 'Abu Dhabi Commercial Bank', 'بنك أبوظبي التجاري', 'ADCB AEAD'),
('DIB', 'Dubai Islamic Bank', 'بنك دبي الإسلامي', 'DUIB AEAD'),
('ADIB', 'Abu Dhabi Islamic Bank', 'مصرف أبوظبي الإسلامي', 'ADIB AEAD'),
('CBD', 'Commercial Bank of Dubai', 'بنك دبي التجاري', 'CBDU AEAD'),
('MASHREQ', 'Mashreq Bank', 'بنك المشرق', 'BMEA AEAD'),
('RAK', 'RAK Bank', 'بنك رأس الخيمة الوطني', 'NRAK AEAD')
ON DUPLICATE KEY UPDATE `bank_name_en` = VALUES(`bank_name_en`);

-- ============================================================
-- INSURANCE SAMPLE DATA
-- ============================================================

-- Sample Roles
INSERT INTO `roles` (`name`, `description`, `permissions`) VALUES
('Admin', 'System Administrator', '{"all": true}'),
('Manager', 'Branch Manager', '{"manage_users": true, "view_reports": true}'),
('Accountant', 'Accountant', '{"manage_accounts": true, "view_reports": true}'),
('Underwriter', 'Insurance Underwriter', '{"manage_policies": true, "underwriting": true}'),
('Claims Officer', 'Claims Processing Officer', '{"manage_claims": true}'),
('Agent', 'Insurance Agent', '{"create_policies": true, "view_own_data": true}'),
('Customer Service', 'Customer Service Representative', '{"view_customers": true, "view_policies": true}')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- Sample Policy Types
INSERT INTO `policy_types` (`code`, `name`, `name_ar`, `category`) VALUES
('MTR', 'Motor Insurance', 'تأمين السيارات', 'motor'),
('HLT', 'Health Insurance', 'التأمين الصحي', 'health'),
('LIF', 'Life Insurance', 'التأمين على الحياة', 'life'),
('TRV', 'Travel Insurance', 'تأمين السفر', 'travel'),
('HOM', 'Home Insurance', 'تأمين المنزل', 'property'),
('MAR', 'Marine Insurance', 'التأمين البحري', 'marine'),
('FIR', 'Fire Insurance', 'تأمين الحريق', 'property'),
('TPL', 'Third Party Liability', 'المسؤولية تجاه الغير', 'liability')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Sample Claim Types
INSERT INTO `claim_types` (`code`, `name`, `name_ar`, `description`) VALUES
('ACC', 'Accident', 'حادث', 'Motor vehicle accident'),
('THF', 'Theft', 'سرقة', 'Theft or burglary'),
('FIR', 'Fire', 'حريق', 'Fire damage'),
('MED', 'Medical', 'طبي', 'Medical treatment'),
('TTL', 'Total Loss', 'خسارة كلية', 'Total loss of insured item'),
('PTL', 'Partial Loss', 'خسارة جزئية', 'Partial damage')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Sample Payment Types
INSERT INTO `payment_types` (`code`, `name`, `name_ar`, `requires_reference`, `requires_bank_account`) VALUES
('CASH', 'Cash', 'نقد', 0, 0),
('CHQ', 'Cheque', 'شيك', 1, 1),
('CARD', 'Credit/Debit Card', 'بطاقة', 1, 0),
('BANK', 'Bank Transfer', 'تحويل بنكي', 1, 1),
('UPI', 'UPI', 'UPI', 1, 0),
('WALLET', 'Digital Wallet', 'محفظة رقمية', 1, 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Sample Account Groups (Insurance Specific)
INSERT INTO `account_groups` (`grcode`, `name`, `reserve`, `actype1`, `parent_grp`, `level`, `position`) VALUES
('CURA', 'CURRENT ASSETS', 'N', 'A', NULL, 1, 1),
('FIXA', 'FIXED ASSETS', 'N', 'A', NULL, 1, 2),
('CURL', 'CURRENT LIABILITIES', 'N', 'L', NULL, 1, 3),
('LONGL', 'LONG TERM LIABILITIES', 'N', 'L', NULL, 1, 4),
('PCAP', 'CAPITAL', 'N', 'L', NULL, 1, 5),
('DIREXP', 'DIRECT EXPENSES', 'N', 'E', NULL, 1, 6),
('INDEXP', 'INDIRECT EXPENSES', 'N', 'E', NULL, 1, 7),
('DIRINC', 'DIRECT INCOME', 'N', 'R', NULL, 1, 8),
('INDINC', 'INDIRECT INCOME', 'N', 'R', NULL, 1, 9)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Sample Accounts (Insurance Specific)
INSERT INTO `accounts` (`accode`, `name`, `actype1`, `actype2`, `reserve`, `grcode`, `opbal`, `curbal`) VALUES
('CASH', 'CASH IN HAND', 'A', 'H', 'Y', 'CURA', 0, 0),
('BANK', 'BANK ACCOUNT', 'A', 'B', 'Y', 'CURA', 0, 0),
('PRMREC', 'PREMIUM RECEIVABLE', 'A', NULL, 'Y', 'CURA', 0, 0),
('REINREC', 'REINSURANCE RECEIVABLE', 'A', NULL, 'Y', 'CURA', 0, 0),
('CLMPAY', 'CLAIMS PAYABLE', 'L', NULL, 'Y', 'CURL', 0, 0),
('COMMPAY', 'COMMISSION PAYABLE', 'L', NULL, 'Y', 'CURL', 0, 0),
('UPRES', 'UNEARNED PREMIUM RESERVE', 'L', NULL, 'Y', 'CURL', 0, 0),
('OCLRES', 'OUTSTANDING CLAIMS RESERVE', 'L', NULL, 'Y', 'CURL', 0, 0),
('CAPITAL', 'CAPITAL ACCOUNT', 'L', NULL, 'Y', 'PCAP', 0, 0),
('PRMINC', 'PREMIUM INCOME', 'R', NULL, 'Y', 'DIRINC', 0, 0),
('REINEXP', 'REINSURANCE EXPENSE', 'E', NULL, 'Y', 'DIREXP', 0, 0),
('CLMEXP', 'CLAIMS EXPENSE', 'E', NULL, 'Y', 'DIREXP', 0, 0),
('COMMEXP', 'COMMISSION EXPENSE', 'E', NULL, 'Y', 'DIREXP', 0, 0),
('VATPAY', 'VAT PAYABLE', 'L', NULL, 'Y', 'CURL', 0, 0),
('VATREC', 'VAT RECOVERABLE', 'A', NULL, 'Y', 'CURA', 0, 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ============================================================
-- PERFORMANCE INDEXES
-- ============================================================

-- Core System Indexes
CREATE INDEX IF NOT EXISTS `idx_users_username` ON `users`(`username`);
CREATE INDEX IF NOT EXISTS `idx_users_email` ON `users`(`email`);
CREATE INDEX IF NOT EXISTS `idx_users_branch` ON `users`(`branch_id`);
CREATE INDEX IF NOT EXISTS `idx_users_role` ON `users`(`role_id`);
CREATE INDEX IF NOT EXISTS `idx_audit_logs_user` ON `audit_logs`(`user_id`);
CREATE INDEX IF NOT EXISTS `idx_audit_logs_table` ON `audit_logs`(`table_name`);

-- Accounting Indexes
CREATE INDEX IF NOT EXISTS `idx_accounts_grcode` ON `accounts`(`grcode`);
CREATE INDEX IF NOT EXISTS `idx_daybook_date` ON `daybook`(`date`);
CREATE INDEX IF NOT EXISTS `idx_daybook_accode` ON `daybook`(`accode`);
CREATE INDEX IF NOT EXISTS `idx_daybook_voucher` ON `daybook`(`voucher_type`, `voucher_no`(50));
CREATE INDEX IF NOT EXISTS `idx_ledger_journal` ON `ledger`(`journal_id`);
CREATE INDEX IF NOT EXISTS `idx_ledger_accode` ON `ledger`(`accode`);

-- Master Data Indexes
CREATE INDEX IF NOT EXISTS `idx_customers_code` ON `customers`(`code`);
CREATE INDEX IF NOT EXISTS `idx_customers_email` ON `customers`(`email`);
CREATE INDEX IF NOT EXISTS `idx_customers_type` ON `customers`(`ctype`);
CREATE INDEX IF NOT EXISTS `idx_agents_code` ON `agents`(`code`);
CREATE INDEX IF NOT EXISTS `idx_brokers_code` ON `brokers`(`code`);
CREATE INDEX IF NOT EXISTS `idx_suppliers_code` ON `suppliers`(`code`);

-- Transaction Indexes
CREATE INDEX IF NOT EXISTS `idx_sales_date` ON `sales`(`date`);
CREATE INDEX IF NOT EXISTS `idx_sales_customer` ON `sales`(`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_sales_branch` ON `sales`(`branch_id`);
CREATE INDEX IF NOT EXISTS `idx_sales_status` ON `sales`(`status`);
CREATE INDEX IF NOT EXISTS `idx_purchases_date` ON `purchases`(`date`);
CREATE INDEX IF NOT EXISTS `idx_purchases_supplier` ON `purchases`(`supplier_id`);
CREATE INDEX IF NOT EXISTS `idx_receipts_date` ON `receipts`(`date`);
CREATE INDEX IF NOT EXISTS `idx_receipts_customer` ON `receipts`(`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_payments_date` ON `payments`(`date`);

-- Insurance Indexes
CREATE INDEX IF NOT EXISTS `idx_policies_customer` ON `policies`(`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_policies_agent` ON `policies`(`agent_id`);
CREATE INDEX IF NOT EXISTS `idx_policies_broker` ON `policies`(`broker_id`);
CREATE INDEX IF NOT EXISTS `idx_policies_status` ON `policies`(`status`);
CREATE INDEX IF NOT EXISTS `idx_policies_dates` ON `policies`(`start_date`, `end_date`);
CREATE INDEX IF NOT EXISTS `idx_claims_policy` ON `claims`(`policy_id`);
CREATE INDEX IF NOT EXISTS `idx_claims_customer` ON `claims`(`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_claims_status` ON `claims`(`status`);
CREATE INDEX IF NOT EXISTS `idx_premiums_policy` ON `premiums`(`policy_id`);
CREATE INDEX IF NOT EXISTS `idx_premiums_status` ON `premiums`(`status`);
CREATE INDEX IF NOT EXISTS `idx_commissions_policy` ON `commissions`(`policy_id`);
CREATE INDEX IF NOT EXISTS `idx_commissions_agent` ON `commissions`(`agent_id`);
CREATE INDEX IF NOT EXISTS `idx_commissions_broker` ON `commissions`(`broker_id`);

-- GCC/UAE Indexes
CREATE INDEX IF NOT EXISTS `idx_exchange_rate_currency` ON `exchange_rate_history`(`currency_id`);
CREATE INDEX IF NOT EXISTS `idx_exchange_rate_date` ON `exchange_rate_history`(`effective_date`);
CREATE INDEX IF NOT EXISTS `idx_vat_returns_period` ON `vat_returns`(`return_period`);
CREATE INDEX IF NOT EXISTS `idx_ia_returns_period` ON `ia_returns`(`period_from`, `period_to`);

-- ============================================================
-- DATABASE STATISTICS UPDATE
-- ============================================================

ANALYZE TABLE `accounts`;
ANALYZE TABLE `customers`;
ANALYZE TABLE `policies`;
ANALYZE TABLE `claims`;
ANALYZE TABLE `sales`;
ANALYZE TABLE `daybook`;

-- ================================================================================
-- END OF SAMPLE DATA & INDEXES
-- ================================================================================
