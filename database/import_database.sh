#!/bin/bash
# Insurance ERP - Database Import Script
# This script imports the database schema and data into a new or existing database

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}   Insurance ERP - Database Import Script${NC}"
echo -e "${GREEN}==================================================${NC}"
echo ""

# Default database credentials
DB_HOST="localhost"
DB_USER="root"
DB_PASS=""
DB_NAME="insurance_erp"

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo -e "${YELLOW}Database Import Configuration${NC}"
echo -e "Press Enter to use default values or type new values"
echo ""

read -p "Database Host [${DB_HOST}]: " input_host
DB_HOST=${input_host:-$DB_HOST}

read -p "Database User [${DB_USER}]: " input_user
DB_USER=${input_user:-$DB_USER}

read -sp "Database Password: " input_pass
echo ""
DB_PASS=${input_pass:-$DB_PASS}

read -p "Database Name [${DB_NAME}]: " input_name
DB_NAME=${input_name:-$DB_NAME}

echo ""
echo -e "${BLUE}Available import options:${NC}"
echo -e "  1) Schema Only (no data)"
echo -e "  2) Schema + Sample Data"
echo -e "  3) Full Backup Restore (from backup file)"
echo -e "  4) Complete Fresh Install (recommended)"
echo ""

read -p "Select option [4]: " import_option
import_option=${import_option:-4}

echo ""
echo -e "${YELLOW}Starting database import...${NC}"
echo ""

# MySQL connection string
MYSQL_CMD="mysql --host=${DB_HOST} --user=${DB_USER} --password=${DB_PASS}"

# Function to import SQL file
import_sql_file() {
    local file=$1
    local description=$2

    if [ -f "${file}" ]; then
        echo -e "${YELLOW}Importing: ${description}...${NC}"
        ${MYSQL_CMD} ${DB_NAME} < "${file}"

        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ ${description} imported successfully${NC}"
            return 0
        else
            echo -e "${RED}✗ Failed to import ${description}${NC}"
            return 1
        fi
    else
        echo -e "${RED}✗ File not found: ${file}${NC}"
        return 1
    fi
}

# Create database if it doesn't exist
echo -e "${YELLOW}Creating database if not exists...${NC}"
${MYSQL_CMD} -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -ne 0 ]; then
    echo -e "${RED}✗ Failed to create database${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Database ready${NC}"
echo ""

case $import_option in
    1)
        # Schema only
        echo -e "${BLUE}Importing Schema Only...${NC}"
        import_sql_file "${SCRIPT_DIR}/insurance_erp_complete_schema.sql" "Complete Schema"
        ;;

    2)
        # Schema + Sample Data
        echo -e "${BLUE}Importing Schema + Sample Data...${NC}"
        import_sql_file "${SCRIPT_DIR}/insurance_erp_complete_schema.sql" "Complete Schema"
        import_sql_file "${SCRIPT_DIR}/05_sample_data_indexes.sql" "Sample Data"
        ;;

    3)
        # Full Backup Restore
        echo -e "${BLUE}Restoring from backup file...${NC}"
        echo ""
        echo "Available backup files in ./backups/:"
        ls -lh "${SCRIPT_DIR}/backups/" 2>/dev/null || echo "No backups found"
        echo ""
        read -p "Enter backup filename: " backup_file

        if [ -f "${SCRIPT_DIR}/backups/${backup_file}" ]; then
            # Check if file is compressed
            if [[ $backup_file == *.gz ]]; then
                echo -e "${YELLOW}Decompressing backup...${NC}"
                gunzip -c "${SCRIPT_DIR}/backups/${backup_file}" | ${MYSQL_CMD} ${DB_NAME}
            else
                ${MYSQL_CMD} ${DB_NAME} < "${SCRIPT_DIR}/backups/${backup_file}"
            fi

            if [ $? -eq 0 ]; then
                echo -e "${GREEN}✓ Backup restored successfully${NC}"
            else
                echo -e "${RED}✗ Failed to restore backup${NC}"
                exit 1
            fi
        else
            echo -e "${RED}✗ Backup file not found${NC}"
            exit 1
        fi
        ;;

    4)
        # Complete Fresh Install
        echo -e "${BLUE}Performing Complete Fresh Install...${NC}"
        echo ""

        # Import in correct order
        import_sql_file "${SCRIPT_DIR}/insurance_erp_complete_schema.sql" "Main Schema" || exit 1
        echo ""

        import_sql_file "${SCRIPT_DIR}/02_master_data_tables.sql" "Master Data Tables" || exit 1
        echo ""

        import_sql_file "${SCRIPT_DIR}/03_insurance_tables.sql" "Insurance Tables" || exit 1
        echo ""

        import_sql_file "${SCRIPT_DIR}/04_gcc_uae_tables.sql" "GCC/UAE Tables" || exit 1
        echo ""

        import_sql_file "${SCRIPT_DIR}/06_receipt_payment_debit_credit_notes.sql" "Financial Documents" || exit 1
        echo ""

        import_sql_file "${SCRIPT_DIR}/05_sample_data_indexes.sql" "Sample Data & Indexes" || exit 1
        echo ""

        # Create default admin user
        echo -e "${YELLOW}Creating default admin user...${NC}"
        ${MYSQL_CMD} ${DB_NAME} <<EOF
-- Create default admin user (password: Admin@123)
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role, status, created_at)
VALUES (
    'admin',
    'admin@yourdomain.com',
    '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System',
    'Administrator',
    'admin',
    'active',
    NOW()
);

-- Grant all permissions to admin
INSERT IGNORE INTO user_permissions (user_id, module_name, can_view, can_create, can_edit, can_delete, can_approve, can_export)
SELECT
    (SELECT user_id FROM users WHERE username = 'admin' LIMIT 1),
    module,
    1, 1, 1, 1, 1, 1
FROM (
    SELECT 'customers' AS module UNION ALL
    SELECT 'policies' UNION ALL
    SELECT 'claims' UNION ALL
    SELECT 'sales' UNION ALL
    SELECT 'receipts' UNION ALL
    SELECT 'payments' UNION ALL
    SELECT 'debit_notes' UNION ALL
    SELECT 'credit_notes' UNION ALL
    SELECT 'accounting' UNION ALL
    SELECT 'reports' UNION ALL
    SELECT 'hr' UNION ALL
    SELECT 'settings' UNION ALL
    SELECT 'users' UNION ALL
    SELECT 'backup'
) AS modules;
EOF

        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Default admin user created${NC}"
            echo -e "${YELLOW}  Username: admin${NC}"
            echo -e "${YELLOW}  Password: Admin@123${NC}"
            echo -e "${RED}  ⚠️  CHANGE PASSWORD AFTER FIRST LOGIN!${NC}"
        fi
        ;;

    *)
        echo -e "${RED}✗ Invalid option${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}   Database Import Complete!${NC}"
echo -e "${GREEN}==================================================${NC}"
echo ""
echo -e "${BLUE}Database Statistics:${NC}"

# Get table count
TABLE_COUNT=$(${MYSQL_CMD} -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}';" -sN)
echo -e "  Total Tables: ${GREEN}${TABLE_COUNT}${NC}"

# Get record count for key tables
echo ""
echo -e "${BLUE}Key Tables:${NC}"

get_count() {
    local table=$1
    local count=$(${MYSQL_CMD} -e "SELECT COUNT(*) FROM ${DB_NAME}.${table};" -sN 2>/dev/null)
    echo -e "  ${table}: ${GREEN}${count:-0} records${NC}"
}

get_count "users"
get_count "customers"
get_count "policies"
get_count "claims"
get_count "receipts"

echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "  1. Update application/config/database.php with these credentials"
echo -e "  2. Change admin password after first login"
echo -e "  3. Configure company settings in the application"
echo -e "  4. Upload company logo"
echo -e "  5. Create additional users and assign permissions"
echo ""
