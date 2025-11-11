# Insurance ERP - HostGator Deployment Guide

## 📋 Overview

This guide provides complete step-by-step instructions for deploying the Insurance ERP system to HostGator hosting (https://ob1.hostgator.com/).

---

## 🔧 Pre-Deployment Requirements

### HostGator Account Information Needed:
- [ ] HostGator cPanel URL (https://yourdomain.com:2083)
- [ ] cPanel username and password
- [ ] Domain name or subdomain for deployment
- [ ] Email address for admin account

### System Requirements (HostGator Supports):
- ✅ PHP 7.4 or higher (Recommended: PHP 8.0+)
- ✅ MySQL 5.7 or MariaDB 10.3+
- ✅ Apache with mod_rewrite enabled
- ✅ SSL Certificate (Free Let's Encrypt available)

---

## 📦 Step 1: Prepare Files for Upload

### 1.1 Create Production Configuration

Before uploading, update the following files:

**application/config/config.php**
```php
$config['base_url'] = 'https://yourdomain.com/'; // Change to your actual domain
$config['index_page'] = ''; // Remove index.php from URL
$config['encryption_key'] = 'GENERATE_NEW_32_CHAR_KEY_HERE'; // Generate new key
$config['sess_driver'] = 'files';
$config['sess_save_path'] = sys_get_temp_dir();
```

**application/config/database.php**
```php
$db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost', // HostGator uses localhost
    'username' => 'YOUR_CPANEL_USERNAME_dbuser', // From cPanel MySQL setup
    'password' => 'YOUR_DATABASE_PASSWORD',
    'database' => 'YOUR_CPANEL_USERNAME_dbname', // Database name from cPanel
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => FALSE, // Set to FALSE in production
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => FALSE
);
```

### 1.2 Files to Upload

**Include:**
- `/application/` - All application files
- `/system/` - CodeIgniter system files
- `/assets/` - CSS, JS, images
- `/uploads/` - Upload directory (create if not exists)
- `index.php` - Entry point
- `.htaccess` - Apache configuration

**Exclude (DO NOT upload):**
- `.git/` - Git repository
- `.gitignore`
- `README.md`
- `DEPLOYMENT_GUIDE.md`
- Any local configuration files

---

## 🗄️ Step 2: Database Setup on HostGator

### 2.1 Access cPanel

1. Login to HostGator cPanel: `https://yourdomain.com:2083`
2. Navigate to **Databases** section

### 2.2 Create MySQL Database

1. Click **MySQL Database Wizard** or **MySQL Databases**
2. Create new database:
   - Database name: `insurance_erp` (will be prefixed with your username)
   - Example: `username_insurance_erp`
3. Create database user:
   - Username: `erp_user`
   - Password: Generate strong password (save it!)
   - Example: `username_erp_user`
4. Add user to database with **ALL PRIVILEGES**
5. Note down:
   ```
   Database Host: localhost
   Database Name: username_insurance_erp
   Database User: username_erp_user
   Database Password: [your generated password]
   ```

### 2.3 Import Database Schema

#### Option A: Using phpMyAdmin (Recommended)

1. In cPanel, click **phpMyAdmin**
2. Select your database (`username_insurance_erp`)
3. Click **Import** tab
4. Upload `database/insurance_erp_schema.sql`
5. Click **Go** to import
6. Verify all 150+ tables are created

#### Option B: Using Terminal (SSH Access Required)

```bash
mysql -u username_erp_user -p username_insurance_erp < database/insurance_erp_schema.sql
```

### 2.4 Create Default Admin User

Run this SQL in phpMyAdmin:

```sql
-- Create default admin user
-- Password: Admin@123 (change immediately after first login!)
INSERT INTO users (username, email, password, first_name, last_name, role, status, created_at)
VALUES (
    'admin',
    'admin@yourdomain.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- bcrypt hash of 'Admin@123'
    'System',
    'Administrator',
    'admin',
    'active',
    NOW()
);

-- Grant all permissions
INSERT INTO user_permissions (user_id, module_name, can_view, can_create, can_edit, can_delete, can_approve, can_export)
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
```

---

## 📤 Step 3: Upload Files to HostGator

### 3.1 Using File Manager (cPanel)

1. Login to cPanel
2. Navigate to **File Manager**
3. Go to `public_html` directory (or your domain's root)
4. Upload the entire project:
   - Compress locally: `insurance_erp.zip`
   - Click **Upload** in cPanel File Manager
   - Select `insurance_erp.zip`
   - After upload, **Extract** the zip file
5. Verify structure:
   ```
   public_html/
   ├── application/
   ├── system/
   ├── assets/
   ├── uploads/
   ├── index.php
   └── .htaccess
   ```

### 3.2 Using FTP (FileZilla)

1. Open FileZilla
2. Connect to HostGator:
   - Host: `ftp.yourdomain.com` or `yourdomain.com`
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)
3. Navigate to `/public_html/`
4. Upload all project files
5. Preserve file permissions

### 3.3 Using Git (SSH Access - Advanced)

```bash
# SSH into HostGator
ssh username@yourdomain.com

# Navigate to public_html
cd public_html

# Clone repository
git clone https://github.com/yourusername/insurance-erp.git .

# Remove .git directory
rm -rf .git
```

---

## 🔐 Step 4: Configure File Permissions

Set proper permissions via cPanel File Manager or FTP:

```
Directories: 755
Files: 644
Special directories: 777 (writable)
```

### Directories that need write permissions (777):

```bash
chmod 777 application/logs
chmod 777 application/cache
chmod 777 uploads
chmod 777 uploads/policies
chmod 777 uploads/claims
chmod 777 uploads/customers
chmod 777 uploads/receipts
chmod 777 uploads/backups
```

**Using cPanel File Manager:**
1. Right-click on folder → **Permissions**
2. Check all boxes (Read, Write, Execute for User, Group, World)
3. For `uploads/` - check "Recurse into subdirectories"

---

## 🌐 Step 5: Configure Domain & SSL

### 5.1 Point Domain to Installation

**If using subdomain:**
1. cPanel → **Subdomains**
2. Create subdomain: `erp.yourdomain.com`
3. Document Root: `/public_html/` (or installation directory)

**If using main domain:**
- Ensure files are in `/public_html/`

### 5.2 Install SSL Certificate (Free)

1. cPanel → **SSL/TLS Status**
2. Select your domain
3. Click **Run AutoSSL** (HostGator provides free Let's Encrypt)
4. Wait for certificate installation
5. Update `config.php`:
   ```php
   $config['base_url'] = 'https://yourdomain.com/';
   ```

### 5.3 Force HTTPS

Add to `.htaccess` (at the very top):
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## ⚙️ Step 6: Configure PHP Settings

### 6.1 Increase PHP Limits

Create `php.ini` in `/public_html/` or use cPanel **Select PHP Version**:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
date.timezone = Asia/Dubai
```

**Using cPanel:**
1. **Select PHP Version**
2. Click **Options**
3. Set:
   - `upload_max_filesize` = 100M
   - `post_max_size` = 100M
   - `max_execution_time` = 300
   - `memory_limit` = 256M

### 6.2 Enable Required Extensions

Ensure enabled (usually enabled by default on HostGator):
- ✅ mysqli
- ✅ gd
- ✅ mbstring
- ✅ openssl
- ✅ zip
- ✅ curl

---

## 🧪 Step 7: Test the Deployment

### 7.1 Basic Functionality Test

1. **Access the application:**
   - URL: `https://yourdomain.com/`
   - Should redirect to: `https://yourdomain.com/auth/login`

2. **Test login:**
   - Username: `admin`
   - Password: `Admin@123`
   - ✅ Should successfully login to dashboard

3. **Test each module:**
   - ✅ Customers → Can view list
   - ✅ Policies → Can create new policy
   - ✅ Claims → Can file claim
   - ✅ Receipts → Can create receipt
   - ✅ Accounting → Can view reports
   - ✅ Settings → Can update company info

4. **Test file uploads:**
   - Upload customer document
   - Upload policy file
   - ✅ Files should save to `/uploads/` directory

5. **Test reports:**
   - Generate profit/loss report
   - Generate VAT report
   - ✅ Reports should display correctly

### 7.2 Security Checks

Run these tests:

```bash
# 1. Verify .htaccess is working
# Try accessing: https://yourdomain.com/application/config/database.php
# Should show 403 Forbidden ✅

# 2. Verify database connection
# Login should work ✅

# 3. Check file upload security
# Try uploading .php file - should be rejected ✅

# 4. Test SQL injection protection
# Try: admin' OR '1'='1
# Should not login ✅
```

---

## 🔒 Step 8: Security Hardening

### 8.1 Update Sensitive Files

**Change default admin password:**
1. Login as admin
2. Navigate to: Users → My Profile
3. Change password to strong password
4. Save

**Generate new encryption key:**
```php
// application/config/config.php
$config['encryption_key'] = 'NEW_RANDOM_32_CHAR_STRING_HERE';
```

### 8.2 Protect Sensitive Directories

Verify `.htaccess` in `/application/`:
```apache
<IfModule authz_core_module>
    Require all denied
</IfModule>
<IfModule !authz_core_module>
    Deny from all
</IfModule>
```

### 8.3 Disable Error Display

**application/config/config.php:**
```php
$config['log_threshold'] = 1; // Only errors
```

**index.php:**
```php
define('ENVIRONMENT', 'production'); // Change from development
```

### 8.4 Setup Automatic Backups

**Using cPanel Backup Wizard:**
1. cPanel → **Backup Wizard**
2. **Backup** → **Full Backup**
3. Schedule: Weekly
4. Destination: Remote FTP or Download

**Database Backups:**
1. cPanel → **phpMyAdmin**
2. Select database → **Export**
3. Format: SQL
4. Save as: `insurance_erp_YYYY-MM-DD.sql`

---

## 📧 Step 9: Configure Email (Optional)

### 9.1 Email Settings

**application/config/email.php:** (create if not exists)

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp';
$config['smtp_host'] = 'mail.yourdomain.com'; // HostGator SMTP
$config['smtp_port'] = 587; // or 465 for SSL
$config['smtp_user'] = 'noreply@yourdomain.com';
$config['smtp_pass'] = 'your_email_password';
$config['smtp_crypto'] = 'tls'; // or 'ssl'
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['wordwrap'] = TRUE;
```

### 9.2 Create Email Account

1. cPanel → **Email Accounts**
2. Create: `noreply@yourdomain.com`
3. Set password
4. Use in SMTP configuration above

---

## 📊 Step 10: Performance Optimization

### 10.1 Enable Caching

**application/config/config.php:**
```php
$config['cache_path'] = APPPATH . 'cache/';
```

**Enable OPcache (cPanel):**
1. Select PHP Version → **Options**
2. Enable `opcache`

### 10.2 Enable Compression

Add to `.htaccess`:
```apache
# Enable GZIP compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Enable browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 10.3 Optimize Database

Run in phpMyAdmin:
```sql
-- Optimize all tables
OPTIMIZE TABLE customers, policies, claims, receipts, payments, users;

-- Add indexes if not exists
ALTER TABLE claims ADD INDEX idx_claim_date (claim_date);
ALTER TABLE policies ADD INDEX idx_policy_number (policy_number);
ALTER TABLE receipts ADD INDEX idx_receipt_date (receipt_date);
```

---

## ✅ Post-Deployment Checklist

### Functionality
- [ ] Login system works
- [ ] All 14 modules accessible
- [ ] Create/Edit/Delete operations work
- [ ] File uploads work
- [ ] Reports generate correctly
- [ ] VAT calculations accurate
- [ ] Backup system functional
- [ ] Audit logs recording

### Security
- [ ] HTTPS enabled and forced
- [ ] Admin password changed
- [ ] Encryption key updated
- [ ] Error display disabled
- [ ] Directory protection active
- [ ] File upload restrictions in place
- [ ] SQL injection protection verified

### Performance
- [ ] Page load time < 3 seconds
- [ ] Database queries optimized
- [ ] Caching enabled
- [ ] Compression enabled
- [ ] Images optimized

### Backup
- [ ] Manual backup completed
- [ ] Automatic backups scheduled
- [ ] Backup restoration tested
- [ ] Offsite backup configured

---

## 🐛 Troubleshooting

### Issue: "500 Internal Server Error"

**Solution:**
1. Check `.htaccess` syntax
2. Verify file permissions (755 for dirs, 644 for files)
3. Check error logs: cPanel → **Error Log**
4. Verify PHP version (7.4+)

### Issue: "Database connection failed"

**Solution:**
1. Verify credentials in `database.php`
2. Ensure database user has ALL PRIVILEGES
3. Check hostname is `localhost`
4. Test connection in phpMyAdmin

### Issue: "404 Not Found" for all pages

**Solution:**
1. Verify `.htaccess` exists and uploaded
2. Check `mod_rewrite` enabled (usually enabled on HostGator)
3. Update `.htaccess`:
```apache
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L,QSA]
```

### Issue: File uploads fail

**Solution:**
1. Check directory permissions: `chmod 777 uploads`
2. Increase PHP limits in `php.ini`
3. Verify upload directory exists
4. Check disk space quota

### Issue: "Session expired" repeatedly

**Solution:**
1. Set session path in `config.php`:
```php
$config['sess_save_path'] = sys_get_temp_dir();
```
2. Or create custom session directory:
```php
$config['sess_save_path'] = APPPATH . 'cache/sessions/';
```

---

## 📞 Support

### HostGator Support
- **Live Chat:** 24/7 available at https://www.hostgator.com/contact
- **Phone:** Check your welcome email
- **Ticket System:** Via cPanel

### Application Support
- **Documentation:** See README.md
- **Email:** admin@yourdomain.com
- **Issues:** Report to development team

---

## 🎉 Deployment Complete!

Your Insurance ERP system is now live on HostGator!

**Access URLs:**
- **Application:** https://yourdomain.com/
- **Admin Login:** https://yourdomain.com/auth/login
- **cPanel:** https://yourdomain.com:2083/

**Default Credentials:**
- Username: `admin`
- Password: `Admin@123` (⚠️ CHANGE IMMEDIATELY!)

---

**Last Updated:** 2025-11-11
**Version:** 1.0 - Production Ready
**Status:** ✅ 100% Complete
