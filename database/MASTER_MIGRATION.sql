-- ================================================================================
-- INSURANCE ERP - MASTER MIGRATION SCRIPT
-- ================================================================================
-- Version: 3.0.0
-- Database: MySQL 5.7+
-- Total Tables: 135+
-- Author: Insurance ERP Team
-- Created: 2025-01-10
-- ================================================================================
--
-- IMPORTANT INSTRUCTIONS:
-- 1. Backup your existing database before running this migration
-- 2. Review all SQL statements before execution
-- 3. Run this script in a test environment first
-- 4. This migration is designed to work alongside existing tables
-- 5. UTF-8 encoding is required
--
-- ================================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ================================================================================
-- STEP 1: CREATE CORE SYSTEM TABLES
-- ================================================================================

SOURCE insurance_erp_complete_schema.sql;

-- ================================================================================
-- STEP 2: CREATE MASTER DATA TABLES
-- ================================================================================

SOURCE 02_master_data_tables.sql;

-- ================================================================================
-- STEP 3: CREATE INSURANCE TABLES
-- ================================================================================

SOURCE 03_insurance_tables.sql;

-- ================================================================================
-- STEP 4: CREATE GCC/UAE TABLES & TRANSACTIONS
-- ================================================================================

SOURCE 04_gcc_uae_tables.sql;

-- ================================================================================
-- STEP 5: INSERT SAMPLE DATA & CREATE INDEXES
-- ================================================================================

SOURCE 05_sample_data_indexes.sql;

-- ================================================================================
-- STEP 6: VERIFY INSTALLATION
-- ================================================================================

SELECT 'Migration completed successfully!' AS status;

SELECT COUNT(*) AS table_count
FROM information_schema.tables
WHERE table_schema = DATABASE()
AND table_type = 'BASE TABLE';

-- ================================================================================
-- COMMIT TRANSACTION
-- ================================================================================

COMMIT;

SELECT '========================================' AS '';
SELECT 'INSURANCE ERP DATABASE MIGRATION COMPLETE' AS '';
SELECT '========================================' AS '';
SELECT 'Next steps:' AS '';
SELECT '1. Verify all tables were created' AS '';
SELECT '2. Check sample data insertion' AS '';
SELECT '3. Test application connectivity' AS '';
SELECT '4. Configure application settings' AS '';
SELECT '========================================' AS '';
