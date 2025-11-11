#!/bin/bash
# Insurance ERP - Database Export Script
# This script exports the complete database with data

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}   Insurance ERP - Database Export Script${NC}"
echo -e "${GREEN}==================================================${NC}"
echo ""

# Default database credentials (update these)
DB_HOST="localhost"
DB_USER="root"
DB_PASS=""
DB_NAME="insurance_erp"

# Output directory
OUTPUT_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
OUTPUT_FILE="${OUTPUT_DIR}/insurance_erp_full_backup_${TIMESTAMP}.sql"

# Create backup directory if it doesn't exist
mkdir -p "${OUTPUT_DIR}"

# Prompt for database credentials
echo -e "${YELLOW}Database Export Configuration${NC}"
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
echo -e "${YELLOW}Starting database export...${NC}"
echo ""

# Export database with mysqldump
mysqldump \
    --host="${DB_HOST}" \
    --user="${DB_USER}" \
    --password="${DB_PASS}" \
    --databases "${DB_NAME}" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --add-drop-database \
    --add-drop-table \
    --complete-insert \
    --extended-insert \
    --default-character-set=utf8mb4 \
    --result-file="${OUTPUT_FILE}"

# Check if export was successful
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database export successful!${NC}"
    echo ""
    echo -e "File: ${GREEN}${OUTPUT_FILE}${NC}"
    echo -e "Size: ${GREEN}$(du -h ${OUTPUT_FILE} | cut -f1)${NC}"
    echo ""

    # Compress the backup
    echo -e "${YELLOW}Compressing backup...${NC}"
    gzip "${OUTPUT_FILE}"

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Backup compressed successfully!${NC}"
        echo -e "Compressed file: ${GREEN}${OUTPUT_FILE}.gz${NC}"
        echo -e "Compressed size: ${GREEN}$(du -h ${OUTPUT_FILE}.gz | cut -f1)${NC}"
    fi

    echo ""
    echo -e "${GREEN}==================================================${NC}"
    echo -e "${GREEN}   Database Export Complete!${NC}"
    echo -e "${GREEN}==================================================${NC}"
else
    echo -e "${RED}✗ Database export failed!${NC}"
    echo -e "${RED}Please check your database credentials and try again.${NC}"
    exit 1
fi
