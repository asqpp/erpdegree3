# 🚀 Insurance ERP - Installation Guide

## Quick Installation (5 Minutes)

### Step 1: Extract Files
Extract the ERP package to your web server directory:
```
/var/www/html/erpdegree3/
```
OR
```
C:\xampp\htdocs\erpdegree3\
```

### Step 2: Database Setup (Automated)

1. Open your browser and visit:
   ```
   http://localhost/erpdegree3/setup.php
   ```

2. You'll see a beautiful setup page with:
   - Project statistics (135+ tables, 7 modules, 60+ reports)
   - "Start Installation" button

3. Click **"Start Installation"**

4. Watch the automated process:
   - ✅ Connecting to MySQL Server
   - ✅ Creating Database (cybor432_erpnew)
   - ✅ Creating Core Tables (8 tables)
   - ✅ Creating Master Data Tables (20 tables)
   - ✅ Creating Insurance Tables (30 tables)
   - ✅ Creating GCC/UAE & Transaction Tables (20+ tables)
   - ✅ Inserting Sample Data & Creating Indexes

5. Installation Complete! (1-2 minutes)
   - Shows success message
   - Database created: `cybor432_erpnew`
   - 135+ tables ready
   - Sample data inserted

### Step 3: UI Build

```bash
cd /path/to/erpdegree3
npm install
npm run build
```

This will:
- Install Tailwind CSS and dependencies
- Build the CSS file
- Setup Alpine.js and other frontend libraries

### Step 4: Configuration

1. **Database Config** - Edit `application/config/database.php`:
   ```php
   $db['default'] = array(
       'hostname' => 'localhost',
       'username' => 'root',
       'password' => '',
       'database' => 'cybor432_erpnew',
       'dbdriver' => 'mysqli',
   );
   ```

2. **Base URL** - Edit `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/erpdegree3/';
   ```

### Step 5: Access System

Visit:
```
http://localhost/erpdegree3/dashboard
```

**Default Login** (if created):
- Email: admin@example.com
- Password: admin123

---

## Manual Installation (Alternative)

If you prefer manual database setup:

### 1. Create Database
```sql
CREATE DATABASE cybor432_erpnew CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cybor432_erpnew;
```

### 2. Run Migrations
```bash
cd /path/to/erpdegree3/database

# Run each SQL file in order
mysql -u root -p cybor432_erpnew < insurance_erp_complete_schema.sql
mysql -u root -p cybor432_erpnew < 02_master_data_tables.sql
mysql -u root -p cybor432_erpnew < 03_insurance_tables.sql
mysql -u root -p cybor432_erpnew < 04_gcc_uae_tables.sql
mysql -u root -p cybor432_erpnew < 05_sample_data_indexes.sql
```

### 3. Verify Installation
```sql
USE cybor432_erpnew;
SHOW TABLES;
-- Should show 135+ tables
```

---

## What Gets Installed?

### Database: cybor432_erpnew

**135+ Tables** organized in:

1. **Core System** (8 tables)
   - companies, branches, financial_years
   - roles, users, settings
   - audit_logs

2. **Master Data** (20 tables)
   - customers, agents, brokers, suppliers
   - products, policy_types, claim_types
   - customer_groups, etc.

3. **Insurance Operations** (30 tables)
   - policies, policy_endorsements
   - claims, claim_documents
   - premium_schedule
   - reinsurance, underwriting, etc.

4. **GCC/UAE Compliance** (15 tables)
   - currencies, exchange_rates
   - vat_returns, ia_returns
   - emirates, etc.

5. **Accounting** (15 tables)
   - chart_of_accounts
   - journal_entries
   - transactions, payments, etc.

6. **HR & Payroll** (10+ tables)
   - employees, departments
   - leave management, payroll, etc.

7. **Additional** (37+ tables)
   - Documents, workflow, approvals
   - Notifications, reports, etc.

### Sample Data Included

✅ 10 GCC Currencies (AED, SAR, KWD, BHD, OMR, QAR, USD, EUR, GBP, INR)
✅ 7 UAE Emirates
✅ Policy Types (Motor, Health, Life, Property, etc.)
✅ Claim Types
✅ Leave Types
✅ Chart of Accounts Structure
✅ Default Settings

---

## Post-Installation

### 1. Security (Important!)

After successful installation:

```bash
# Remove or rename setup.php
rm setup.php
# OR
mv setup.php setup.php.bak
```

### 2. File Permissions

Set correct permissions:
```bash
chmod 755 application/config
chmod 644 application/config/*.php
chmod 777 uploads
chmod 777 assets/uploads
```

### 3. Create Admin User

If no default admin user exists, create one via database:
```sql
USE cybor432_erpnew;

INSERT INTO users (
    name, email, password, role_id, is_active, created_at
) VALUES (
    'Administrator',
    'admin@yourdomain.com',
    MD5('your-secure-password'),  -- Change this!
    1,  -- Admin role
    1,
    NOW()
);
```

---

## Verification Checklist

After installation, verify:

✅ Database created: `cybor432_erpnew`
✅ 135+ tables created
✅ Sample data inserted
✅ Dashboard accessible
✅ No PHP errors
✅ CSS/JS loading correctly
✅ Can navigate between modules
✅ Can view reports

---

## Troubleshooting

### Issue: Can't access setup.php

**Solution:**
- Check file permissions
- Ensure PHP is installed
- Check web server is running
- Verify path is correct

### Issue: Database connection failed

**Solution:**
- Check MySQL is running
- Verify username/password
- Check hostname (usually 'localhost')
- Ensure MySQL port is 3306

### Issue: SQL errors during installation

**Solution:**
- Check MySQL version (5.7+ required)
- Ensure database doesn't exist before installation
- Check file permissions
- Review error message in setup.php

### Issue: CSS not loading

**Solution:**
```bash
cd /path/to/erpdegree3
npm install
npm run build
```

### Issue: Dashboard shows errors

**Solution:**
- Check `application/config/database.php` settings
- Check `application/config/config.php` base_url
- Verify database tables exist
- Check PHP error logs

---

## System Requirements

### Server Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Node.js**: 14+ (for UI build only)

### PHP Extensions Required
- mysqli
- json
- mbstring
- curl
- zip
- gd (for image processing)

### Recommended
- PHP 8.0+
- MySQL 8.0+
- Apache mod_rewrite enabled
- 512MB PHP memory limit
- 120s PHP execution time

---

## Production Deployment

For production deployment:

### 1. Server Setup
- Use a production-grade server (Ubuntu 20.04+, CentOS 8+)
- Configure firewall
- Setup SSL certificate (Let's Encrypt)
- Enable HTTPS

### 2. Security Hardening
- Change default passwords
- Restrict file permissions
- Enable error logging (disable display_errors)
- Configure CSRF protection
- Setup regular backups

### 3. Performance Optimization
- Enable PHP OpCache
- Configure MySQL query cache
- Setup Redis/Memcached if needed
- Enable GZIP compression
- Optimize images

### 4. Monitoring
- Setup error monitoring
- Configure performance monitoring
- Enable audit logging
- Regular backup schedule

---

## Quick Commands Reference

```bash
# Database Migration
mysql -u root -p cybor432_erpnew < database/MASTER_MIGRATION.sql

# UI Build (Development)
npm run dev

# UI Build (Production)
npm run build

# Check Database
mysql -u root -p -e "USE cybor432_erpnew; SHOW TABLES;"

# Count Tables
mysql -u root -p -e "USE cybor432_erpnew; SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='cybor432_erpnew';"
```

---

## Need Help?

### Documentation
- [README.md](README.md) - Main documentation
- [DATABASE README](database/README.md) - Database guide
- [UI FRAMEWORK GUIDE](UI_FRAMEWORK_GUIDE.md) - UI components
- [QUICK START GUIDE](QUICK_START_GUIDE.md) - Quick start

### Support
- Check documentation first
- Review troubleshooting section
- Check error logs
- Verify system requirements

---

## Success!

Once installation is complete, you'll have:

✅ Complete Insurance ERP System
✅ 7 Modules Ready to Use
✅ 135+ Database Tables
✅ 60+ Reports
✅ Modern UI with Animations
✅ Multi-Currency Support
✅ UAE VAT Compliance
✅ Production Ready!

**Start using your system:**
```
http://localhost/erpdegree3/dashboard
```

---

**Installation takes only 5 minutes!**

🎉 **Welcome to Insurance ERP System!**
