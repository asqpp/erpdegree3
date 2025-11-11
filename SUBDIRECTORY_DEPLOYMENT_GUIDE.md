# 🚀 Subdirectory Deployment Configuration Guide

## Target URL: https://erpdegree.com/erpdegreenew/

This guide covers all configuration changes needed to deploy the Insurance ERP system to a subdirectory instead of the root domain.

---

## ⚙️ Required Configuration Changes

### 1. **.htaccess File** - CRITICAL CHANGE

**File:** `/.htaccess`
**Line 228** needs to be changed:

**BEFORE (Root installation):**
```apache
RewriteBase /
```

**AFTER (Subdirectory installation):**
```apache
RewriteBase /erpdegreenew/
```

**Complete section should look like:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /erpdegreenew/

    # Remove index.php from URLs
    RewriteCond %{THE_REQUEST} ^GET.*index\.php [NC]
    RewriteRule ^(.*)index\.php(.*)$ /erpdegreenew/$1$2 [R=301,L]

    # Route all requests through index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L,QSA]
</IfModule>
```

---

### 2. **CodeIgniter Config** - ALREADY CONFIGURED ✅

**File:** `/application/config/config.php`
**Lines 28-43**

**Good News:** Your config.php already has auto-detection enabled! No changes needed.

```php
// Auto-detect base URL (already configured)
$root = (isset($_SERVER["HTTPS"]) ? "https://" : "http://") . $_SERVER["HTTP_HOST"];
$root .= str_replace(basename($_SERVER["SCRIPT_NAME"]), "", $_SERVER["SCRIPT_NAME"]);
$config["base_url"] = $root;
```

This will automatically detect:
- ✅ `https://erpdegree.com/erpdegreenew/`
- ✅ Protocol (http/https)
- ✅ Domain name
- ✅ Subdirectory path

**Manual Override (Optional):**
If auto-detection doesn't work, you can manually set:

```php
$config["base_url"] = "https://erpdegree.com/erpdegreenew/";
```

---

### 3. **Database Configuration**

**File:** `/application/config/database.php`

Update with your HostGator database credentials:

```php
$db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost',              // Usually 'localhost' on HostGator
    'username' => 'your_db_username',       // From cPanel
    'password' => 'your_db_password',       // From cPanel
    'database' => 'your_db_name',           // From cPanel
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => FALSE,                    // Set FALSE for production
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

---

### 4. **Environment Setting**

**File:** `/index.php`
**Line 53**

Change environment to production:

**BEFORE (Development):**
```php
define('ENVIRONMENT', 'development');
```

**AFTER (Production):**
```php
define('ENVIRONMENT', 'production');
```

---

## 📁 File Structure on Server

Your files should be uploaded to:

```
/home/username/public_html/erpdegreenew/
├── application/
├── assets/
├── database/
├── system/
├── uploads/
├── vendor/
├── .htaccess          ← MODIFY THIS
├── index.php          ← MODIFY THIS
├── .env.example
└── README.md
```

---

## 🔧 Step-by-Step Deployment Process

### Step 1: Upload Files via cPanel File Manager or FTP

```bash
# Option A: Via FTP
Upload all files to: /public_html/erpdegreenew/

# Option B: Via cPanel File Manager
1. Login to cPanel
2. File Manager → public_html
3. Create folder: erpdegreenew
4. Upload insurance.zip or sfin.zip
5. Extract archive
```

---

### Step 2: Modify .htaccess

```bash
# In cPanel File Manager:
1. Navigate to: /public_html/erpdegreenew/
2. Right-click .htaccess → Edit
3. Find line 228: RewriteBase /
4. Change to: RewriteBase /erpdegreenew/
5. Save
```

---

### Step 3: Create Database

```bash
# In cPanel → MySQL Databases:
1. Create new database: username_insuranceerp
2. Create new user: username_erpuser
3. Set strong password
4. Add user to database (All Privileges)
5. Note: hostname, username, password, database name
```

---

### Step 4: Import Database

```bash
# In cPanel → phpMyAdmin:
1. Select your database
2. Click "Import" tab
3. Choose file: database/fulldtabsescheme.sql
4. Click "Go"
5. Wait for import to complete (150+ tables)
```

**Alternative - Import separate files in order:**
1. `insurance_erp_complete_schema.sql`
2. `02_master_data_tables.sql`
3. `03_insurance_tables.sql`
4. `04_gcc_uae_tables.sql`
5. `05_sample_data_indexes.sql`
6. `06_receipt_payment_debit_credit_notes.sql`

---

### Step 5: Configure Database Connection

```bash
# Edit: /application/config/database.php
1. Set hostname: localhost
2. Set username: username_erpuser
3. Set password: your_password
4. Set database: username_insuranceerp
5. Set db_debug: FALSE
6. Save
```

---

### Step 6: Set Permissions

```bash
# In cPanel Terminal or SSH:
cd /home/username/public_html/erpdegreenew

# Set directory permissions
chmod 755 application/cache
chmod 755 application/logs
chmod 755 uploads
chmod 755 uploads/backups
chmod 755 uploads/documents
chmod 755 uploads/policies
chmod 755 uploads/claims

# Set file permissions
chmod 644 .htaccess
chmod 644 index.php
chmod 644 application/config/database.php
chmod 644 application/config/config.php
```

---

### Step 7: Test Installation

**Test URL:** `https://erpdegree.com/erpdegreenew/`

**Expected Results:**
✅ Login page loads
✅ No 404 errors
✅ CSS/JS loads properly
✅ Images display correctly

**Default Admin Login:**
```
Username: admin
Password: Admin@123
```

**IMPORTANT:** Change admin password immediately after first login!

---

## 🐛 Troubleshooting

### Issue 1: 404 Not Found on all pages except home

**Cause:** RewriteBase not set correctly
**Solution:** Verify `.htaccess` line 228 is set to `/erpdegreenew/`

```apache
RewriteBase /erpdegreenew/
```

---

### Issue 2: CSS/JS not loading (broken styling)

**Cause:** Base URL not detected correctly
**Solution:** Manually set base_url in `application/config/config.php`:

```php
$config["base_url"] = "https://erpdegree.com/erpdegreenew/";
```

---

### Issue 3: Database connection error

**Cause:** Incorrect database credentials
**Solution:** Double-check credentials in cPanel → MySQL Databases

```php
// Verify these match cPanel:
'hostname' => 'localhost',
'username' => 'exact_username_from_cpanel',
'password' => 'exact_password_from_cpanel',
'database' => 'exact_dbname_from_cpanel',
```

---

### Issue 4: Permission denied errors

**Cause:** Incorrect folder permissions
**Solution:** Set correct permissions:

```bash
chmod 755 application/cache
chmod 755 application/logs
chmod 755 uploads
```

---

### Issue 5: White screen / blank page

**Cause:** PHP error with display_errors OFF
**Solution:** Check error log:

```bash
# In cPanel File Manager:
View: /application/logs/log-YYYY-MM-DD.php
```

**Temporary Debug Mode:**
```php
// In index.php (line 53):
define('ENVIRONMENT', 'development');  // Temporarily for debugging
```

**After fixing, change back to:**
```php
define('ENVIRONMENT', 'production');
```

---

## 🔐 Security Checklist

After deployment, verify:

- ✅ Changed admin password
- ✅ ENVIRONMENT set to 'production'
- ✅ db_debug set to FALSE
- ✅ .htaccess file present and active
- ✅ application/ and system/ folders protected
- ✅ uploads/ folder cannot execute PHP
- ✅ HTTPS working (SSL certificate active)
- ✅ Removed .env file (if present)
- ✅ Database user has limited privileges (not root)

---

## 📊 Verification Tests

### Test 1: Home Page
```
URL: https://erpdegree.com/erpdegreenew/
Expected: Login page loads with styling
```

### Test 2: Dashboard (after login)
```
URL: https://erpdegree.com/erpdegreenew/dashboard
Expected: Dashboard loads with menu
```

### Test 3: Static Assets
```
URL: https://erpdegree.com/erpdegreenew/assets/css/style.css
Expected: CSS file displays
```

### Test 4: Database Connection
```
Action: Login with admin credentials
Expected: Successful login, no database errors
```

### Test 5: Routing
```
URL: https://erpdegree.com/erpdegreenew/customers
Expected: Customers page loads (not 404)
```

---

## 📞 Support Information

If you encounter issues:

1. **Check error logs:**
   - `/application/logs/log-YYYY-MM-DD.php`
   - cPanel → Error Log

2. **Enable debug mode temporarily:**
   ```php
   // index.php
   define('ENVIRONMENT', 'development');

   // database.php
   'db_debug' => TRUE,
   ```

3. **Common HostGator paths:**
   - Root: `/home/username/`
   - Public HTML: `/home/username/public_html/`
   - App location: `/home/username/public_html/erpdegreenew/`

---

## ✅ Quick Reference - Files to Modify

| File | Line | Change |
|------|------|--------|
| `.htaccess` | 228 | `RewriteBase /erpdegreenew/` |
| `index.php` | 53 | `define('ENVIRONMENT', 'production');` |
| `application/config/database.php` | 78-81 | Database credentials |
| `application/config/config.php` | 43 | (Optional) Manual base_url |

---

## 🎯 Summary

**Minimum Required Changes:**
1. ✅ `.htaccess` → Change `RewriteBase /erpdegreenew/`
2. ✅ `database.php` → Set database credentials
3. ✅ `index.php` → Set ENVIRONMENT to production

**Auto-Configured (No Changes Needed):**
- ✅ Base URL detection
- ✅ HTTPS handling
- ✅ Security headers
- ✅ Performance optimization

Your installation is now ready for: **https://erpdegree.com/erpdegreenew/**

---

**Deployment Date:** 2025-11-11
**Target Environment:** HostGator Shared Hosting
**Application:** Insurance ERP System v3.0.0
