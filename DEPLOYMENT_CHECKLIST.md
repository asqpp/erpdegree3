# Insurance ERP - Production Deployment Checklist

## 🚀 Quick Start - HostGator Deployment

Follow these steps in order to deploy to HostGator hosting:

---

## ✅ Phase 1: Pre-Deployment Preparation (Local)

### 1.1 Files Preparation
- [ ] Review all code for hardcoded credentials
- [ ] Remove any development/testing files
- [ ] Update `.gitignore` to exclude sensitive files
- [ ] Create production configuration files
- [ ] Test application locally one final time

### 1.2 Database Preparation
- [ ] Run database export script: `./database/export_database.sh`
- [ ] Verify export file is complete
- [ ] Keep backup of current database
- [ ] Document any custom database changes

### 1.3 Configuration Files
- [ ] Copy `config.production.php` settings to `config.php`
- [ ] Copy `database.production.php` settings to `database.php`
- [ ] Generate unique 32-character encryption key
- [ ] Update base_url with production domain
- [ ] Enable CSRF protection
- [ ] Set cookie_secure and cookie_httponly to TRUE

### 1.4 Security Review
- [ ] All passwords are strong and unique
- [ ] No sensitive data in version control
- [ ] Error display disabled (ENVIRONMENT = 'production')
- [ ] Debug mode disabled (db_debug = FALSE)
- [ ] Log threshold set to 1 (errors only)
- [ ] Session security enabled

---

## ✅ Phase 2: HostGator Account Setup

### 2.1 Access HostGator
- [ ] Login to HostGator account: https://ob1.hostgator.com/
- [ ] Access cPanel for your domain
- [ ] Note cPanel username: `_______________`
- [ ] Verify PHP version (7.4+ required)

### 2.2 Domain Configuration
- [ ] Main domain: `_______________`
- [ ] Or subdomain: `_______________`
- [ ] Document root directory: `/public_html/` or `_______________`

### 2.3 Database Setup
- [ ] Navigate to: **MySQL Database Wizard**
- [ ] Database name created: `_______________`
- [ ] Database user created: `_______________`
- [ ] Database password (SAVE SECURELY): `_______________`
- [ ] User granted ALL PRIVILEGES
- [ ] Connection tested in phpMyAdmin

### 2.4 Email Setup (Optional)
- [ ] Email account created: `noreply@yourdomain.com`
- [ ] Email password (SAVE SECURELY): `_______________`
- [ ] SMTP settings documented
- [ ] Test email sent successfully

---

## ✅ Phase 3: File Upload

### 3.1 Choose Upload Method

**Option A: File Manager (Recommended for beginners)**
- [ ] cPanel → File Manager → public_html
- [ ] Upload project as ZIP file
- [ ] Extract ZIP file
- [ ] Delete ZIP file after extraction

**Option B: FTP/SFTP (Recommended for developers)**
- [ ] FTP client installed (FileZilla)
- [ ] Connected to: `ftp.yourdomain.com`
- [ ] All files uploaded to public_html
- [ ] File permissions verified

**Option C: Git (Advanced)**
- [ ] SSH access enabled
- [ ] Repository cloned
- [ ] .git directory removed
- [ ] Permissions set correctly

### 3.2 Verify File Structure
```
public_html/
├── application/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   ├── cache/        ← Check permissions (777)
│   └── logs/         ← Check permissions (777)
├── system/
├── assets/
├── uploads/          ← Check permissions (777)
│   ├── policies/     ← Check permissions (777)
│   ├── claims/       ← Check permissions (777)
│   ├── customers/    ← Check permissions (777)
│   ├── receipts/     ← Check permissions (777)
│   └── backups/      ← Check permissions (777)
├── index.php
└── .htaccess
```

### 3.3 File Permissions
- [ ] Directories: 755 (except writable ones)
- [ ] Files: 644
- [ ] application/cache/: 777
- [ ] application/logs/: 777
- [ ] uploads/ and all subdirectories: 777
- [ ] .htaccess: 644
- [ ] index.php: 644

**Set via cPanel File Manager:**
1. Right-click folder → Permissions
2. For 777: Check all Read, Write, Execute
3. For 755: Check User (all), Group (read+execute), World (read+execute)
4. For 644: Check User (read+write), Group (read), World (read)

**Or via SSH:**
```bash
chmod 755 application system assets
chmod 777 application/cache application/logs uploads uploads/*
chmod 644 .htaccess index.php
find application -type f -exec chmod 644 {} \;
find application -type d -exec chmod 755 {} \;
```

---

## ✅ Phase 4: Database Import

### 4.1 Import via phpMyAdmin (Recommended)
- [ ] cPanel → phpMyAdmin
- [ ] Select your database
- [ ] Click **Import** tab
- [ ] Choose file: `insurance_erp_complete_schema.sql`
- [ ] Format: SQL
- [ ] Character set: utf8mb4
- [ ] Click **Go**
- [ ] Verify 150+ tables created

### 4.2 Import Additional Schema Files
Import in this order:
- [ ] `02_master_data_tables.sql`
- [ ] `03_insurance_tables.sql`
- [ ] `04_gcc_uae_tables.sql`
- [ ] `06_receipt_payment_debit_credit_notes.sql`
- [ ] `05_sample_data_indexes.sql`

### 4.3 Create Admin User
Run in phpMyAdmin SQL tab:
```sql
-- Create default admin (password: Admin@123)
INSERT INTO users (username, email, password, first_name, last_name, role, status, created_at)
VALUES (
    'admin',
    'admin@yourdomain.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System',
    'Administrator',
    'admin',
    'active',
    NOW()
);
```

- [ ] Admin user created successfully
- [ ] Default username: `admin`
- [ ] Default password: `Admin@123`

---

## ✅ Phase 5: Configuration Update

### 5.1 Update database.php
Edit: `application/config/database.php`

```php
'hostname' => 'localhost',
'username' => 'YOUR_CPANEL_USERNAME_dbuser',
'password' => 'YOUR_DATABASE_PASSWORD',
'database' => 'YOUR_CPANEL_USERNAME_dbname',
'db_debug' => FALSE,
'char_set' => 'utf8mb4',
'dbcollat' => 'utf8mb4_unicode_ci',
```

- [ ] hostname: `localhost`
- [ ] username: `_______________`
- [ ] password: `_______________`
- [ ] database: `_______________`
- [ ] db_debug: `FALSE`
- [ ] save_queries: `FALSE`

### 5.2 Update config.php
Edit: `application/config/config.php`

```php
$config['base_url'] = 'https://yourdomain.com/';
$config['index_page'] = '';
$config['encryption_key'] = 'YOUR_UNIQUE_32_CHAR_KEY';
$config['log_threshold'] = 1;
$config['csrf_protection'] = TRUE;
$config['cookie_secure'] = TRUE;
$config['cookie_httponly'] = TRUE;
```

- [ ] base_url: `https://_______________/`
- [ ] encryption_key: `_______________` (32 chars)
- [ ] index_page: `` (empty)
- [ ] log_threshold: `1`
- [ ] csrf_protection: `TRUE`
- [ ] cookie_secure: `TRUE`
- [ ] cookie_httponly: `TRUE`

### 5.3 Update index.php
Edit: `index.php` (line ~60)

```php
define('ENVIRONMENT', 'production');
```

- [ ] ENVIRONMENT set to 'production'
- [ ] Error reporting disabled

---

## ✅ Phase 6: SSL Certificate Setup

### 6.1 Install SSL (HostGator Free SSL)
- [ ] cPanel → SSL/TLS Status
- [ ] Select domain
- [ ] Click **Run AutoSSL**
- [ ] Wait for installation (2-5 minutes)
- [ ] Certificate installed successfully

### 6.2 Force HTTPS
Verify in `.htaccess` (already configured):
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

- [ ] HTTPS redirect working
- [ ] No mixed content warnings
- [ ] Padlock icon visible in browser

---

## ✅ Phase 7: Testing

### 7.1 Basic Functionality Tests
- [ ] Website loads: `https://yourdomain.com/`
- [ ] Redirects to login: `https://yourdomain.com/auth/login`
- [ ] Login page displays correctly
- [ ] Can login with: `admin` / `Admin@123`
- [ ] Dashboard loads after login
- [ ] No PHP errors displayed

### 7.2 Module Tests
Test each module:
- [ ] **Dashboard** - Statistics display correctly
- [ ] **Customers** - Can view list, create new customer
- [ ] **Policies** - Can view list, create new policy
- [ ] **Claims** - Can view list, file new claim
- [ ] **Sales** - Sales pipeline displays
- [ ] **Receipts** - Can create receipt voucher
- [ ] **Payments** - Can create payment voucher
- [ ] **Debit Notes** - Can create debit note
- [ ] **Credit Notes** - Can create credit note
- [ ] **Accounting** - Reports generate (P&L, Balance Sheet, VAT)
- [ ] **HR** - Employees list displays
- [ ] **Users** - Can manage users
- [ ] **Settings** - Can update company settings
- [ ] **Backup** - Backup list displays

### 7.3 File Upload Tests
- [ ] Upload customer document - Success
- [ ] Upload policy file - Success
- [ ] Upload claim attachment - Success
- [ ] Upload company logo - Success
- [ ] Files saved to correct directories
- [ ] Files accessible via browser

### 7.4 Report Tests
- [ ] Generate Profit & Loss report
- [ ] Generate Balance Sheet
- [ ] Generate VAT Report (9-box format)
- [ ] Print receipt voucher
- [ ] Export data to CSV
- [ ] All calculations accurate

### 7.5 Security Tests
- [ ] Cannot access: `https://yourdomain.com/application/`
- [ ] Cannot access: `https://yourdomain.com/system/`
- [ ] Cannot access: `https://yourdomain.com/application/config/database.php`
- [ ] Session expires after 2 hours
- [ ] CSRF protection working
- [ ] SQL injection blocked (test: `admin' OR '1'='1`)
- [ ] XSS attempts blocked

### 7.6 Performance Tests
- [ ] Page load time < 3 seconds
- [ ] No console errors
- [ ] Images loading properly
- [ ] CSS/JS loading from CDN
- [ ] GZIP compression enabled (check in browser dev tools)

---

## ✅ Phase 8: Post-Deployment Configuration

### 8.1 Change Default Credentials
- [ ] Login as admin
- [ ] Navigate to: Users → My Profile
- [ ] Change password to strong password
- [ ] New admin password: `_______________` (SAVE SECURELY!)
- [ ] Test login with new password

### 8.2 Company Settings
- [ ] Navigate to: Settings → Company Settings
- [ ] Update company name
- [ ] Update company address
- [ ] Update contact information
- [ ] Upload company logo
- [ ] Set VAT registration number
- [ ] Save changes

### 8.3 Create Additional Users
- [ ] Navigate to: Users → Create User
- [ ] Create manager account
- [ ] Create accountant account
- [ ] Create employee accounts
- [ ] Assign appropriate permissions
- [ ] Test each user login

### 8.4 Configure Email
If using email features:
- [ ] Create email configuration file
- [ ] Update SMTP settings
- [ ] Test email sending
- [ ] Verify emails received

---

## ✅ Phase 9: Backup Configuration

### 9.1 Automatic Backups (cPanel)
- [ ] cPanel → Backup Wizard
- [ ] Setup full backup schedule
- [ ] Frequency: Weekly
- [ ] Destination: Download or Remote FTP
- [ ] Test backup creation
- [ ] Test backup restoration

### 9.2 Database Backups
- [ ] Schedule: Daily automated backup
- [ ] Method: cPanel cron job or application backup
- [ ] Retention: Keep last 30 days
- [ ] Off-site backup configured
- [ ] Test database restoration

### 9.3 Manual Backup (Now)
- [ ] Create full cPanel backup
- [ ] Export database via phpMyAdmin
- [ ] Download backup to local machine
- [ ] Store securely off-site
- [ ] Document backup location: `_______________`

---

## ✅ Phase 10: Monitoring & Maintenance

### 10.1 Setup Monitoring
- [ ] Enable error logging
- [ ] Setup log rotation
- [ ] Monitor disk space usage
- [ ] Monitor database size
- [ ] Setup uptime monitoring (optional)

### 10.2 Document Production Details
Create document with:
- [ ] Production URL: `_______________`
- [ ] Admin username: `_______________`
- [ ] Admin email: `_______________`
- [ ] Database name: `_______________`
- [ ] Database user: `_______________`
- [ ] cPanel username: `_______________`
- [ ] FTP details: `_______________`
- [ ] SSL expiry date: `_______________`
- [ ] Hosting expiry date: `_______________`
- [ ] Support contact: `_______________`

### 10.3 Create Support Documentation
- [ ] User manual created
- [ ] Admin guide created
- [ ] Common issues documented
- [ ] Contact information provided
- [ ] Training schedule created

---

## ✅ Phase 11: Go-Live

### 11.1 Final Checks
- [ ] All previous checklist items completed
- [ ] All tests passed
- [ ] Backups configured
- [ ] Users created
- [ ] Documentation ready
- [ ] Support team briefed

### 11.2 Announce Go-Live
- [ ] Inform stakeholders
- [ ] Send access details to users
- [ ] Provide training if needed
- [ ] Share support contact
- [ ] Set expectation for support response time

### 11.3 Post Go-Live Monitoring
For first 48 hours:
- [ ] Monitor error logs every 4 hours
- [ ] Check user feedback
- [ ] Monitor system performance
- [ ] Check disk space
- [ ] Verify backups running
- [ ] Be available for urgent issues

---

## ✅ Phase 12: Optimization (Week 1)

### 12.1 Performance Optimization
- [ ] Review slow queries (if any)
- [ ] Add database indexes if needed
- [ ] Optimize images
- [ ] Enable additional caching
- [ ] Review server resources

### 12.2 User Feedback
- [ ] Collect user feedback
- [ ] Document common issues
- [ ] Create FAQ document
- [ ] Address critical issues
- [ ] Plan enhancements

---

## 🆘 Troubleshooting

### Issue: 500 Internal Server Error
**Check:**
1. `.htaccess` syntax errors
2. File permissions (755/644)
3. PHP version compatibility
4. Error logs: cPanel → Error Log

### Issue: Database Connection Failed
**Check:**
1. `database.php` credentials
2. Database user privileges
3. Database exists
4. Hostname is `localhost`

### Issue: 404 Not Found
**Check:**
1. `.htaccess` uploaded
2. `mod_rewrite` enabled
3. `RewriteBase` directive
4. File permissions

### Issue: Session Expired Repeatedly
**Fix:**
```php
// config.php
$config['sess_save_path'] = sys_get_temp_dir();
// or
$config['sess_save_path'] = APPPATH . 'cache/sessions/';
```

### Issue: File Upload Fails
**Check:**
1. Directory permissions (777)
2. PHP `upload_max_filesize`
3. PHP `post_max_size`
4. Disk space quota

---

## 📞 Support Contacts

### HostGator Support
- **Website:** https://www.hostgator.com/contact
- **Live Chat:** 24/7 available
- **Phone:** (Check your welcome email)

### Application Support
- **Developer:** `_______________`
- **Email:** `_______________`
- **Phone:** `_______________`

---

## 🎉 Deployment Complete!

Congratulations! Your Insurance ERP system is now live in production.

**Production URL:** `https://_______________`
**Admin Access:** `https://_______________/auth/login`

**Remember:**
- ✅ Keep backups updated
- ✅ Monitor error logs regularly
- ✅ Update passwords periodically
- ✅ Review security regularly
- ✅ Keep system documented

---

**Deployment Date:** `_______________`
**Deployed By:** `_______________`
**Version:** 1.0.0
**Status:** ✅ PRODUCTION LIVE
