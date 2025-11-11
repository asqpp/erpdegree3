# Insurance ERP - Configuration Guide

Complete guide to configuring your Insurance ERP System.

---

## 📁 Configuration Files Overview

### **Essential Configuration Files**

| File | Location | Purpose |
|------|----------|---------|
| database.php | `application/config/` | Database connection settings |
| config.php | `application/config/` | Main application configuration |
| autoload.php | `application/config/` | Auto-load libraries and helpers |
| routes.php | `application/config/` | URL routing configuration |
| .htaccess | Root directory | Apache URL rewriting |
| .env.example | Root directory | Environment variables template |

---

## 🗄️ 1. Database Configuration

### **File:** `application/config/database.php`

#### **Default Configuration:**

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'cybor432_erpdegree',
    'password' => 'tPJ$=]pJ^s)4',
    'database' => 'cybor432_erpnew',
    'dbdriver' => 'mysqli',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
);
```

#### **How to Configure:**

1. **For Local Development:**
   ```php
   'hostname' => 'localhost',
   'username' => 'root',
   'password' => '',
   'database' => 'insurance_erp',
   ```

2. **For Production (cPanel/Shared Hosting):**
   ```php
   'hostname' => 'localhost',
   'username' => 'cpanel_username_dbuser',
   'password' => 'your_secure_password',
   'database' => 'cpanel_username_dbname',
   ```

3. **For Production (VPS/Dedicated Server):**
   ```php
   'hostname' => '127.0.0.1',  // or IP address
   'username' => 'erp_user',
   'password' => 'strong_password_here',
   'database' => 'insurance_erp_production',
   ```

#### **Character Set:**
- **Always use:** `utf8mb4` and `utf8mb4_unicode_ci`
- Supports all languages and emojis
- Required for Arabic text in UAE/GCC markets

#### **Debug Mode:**
```php
'db_debug' => (ENVIRONMENT !== 'production'),
```
- Shows errors in development
- Hides errors in production

---

## ⚙️ 2. Main Application Configuration

### **File:** `application/config/config.php`

#### **Base URL (Auto-detected):**

The system automatically detects the base URL:
```php
$root = (isset($_SERVER["HTTPS"]) ? "https://" : "http://").$_SERVER["HTTP_HOST"];
$root .= str_replace(basename($_SERVER["SCRIPT_NAME"]), "", $_SERVER["SCRIPT_NAME"]);
$config["base_url"] = $root;
```

#### **Manual Base URL (if auto-detection fails):**

```php
// Development
$config['base_url'] = 'http://localhost/erpdegree3/';

// Production with domain
$config['base_url'] = 'https://yourdomain.com/';

// Production with subdirectory
$config['base_url'] = 'https://yourdomain.com/erp/';
```

#### **Index File:**
```php
$config['index_page'] = '';  // Remove index.php from URL
```

#### **Encryption Key:**
```php
$config['encryption_key'] = '';  // Auto-generated or set manually
```

To generate a secure key:
```bash
php -r "echo bin2hex(random_bytes(16));"
```

#### **Session Configuration:**
```php
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'erp_session';
$config['sess_expiration'] = 7200;  // 2 hours
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;  // 5 minutes
$config['sess_regenerate_destroy'] = FALSE;
```

**Session Storage Options:**
- `files` - Store in files (default)
- `database` - Store in database
- `memcached` - Use Memcached
- `redis` - Use Redis

#### **Cookie Settings:**
```php
$config['cookie_prefix']  = '';
$config['cookie_domain']  = '';
$config['cookie_path']    = '/';
$config['cookie_secure']  = FALSE;  // TRUE if using HTTPS
$config['cookie_httponly'] = TRUE;  // Security: prevent JavaScript access
```

#### **CSRF Protection:**
```php
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'erp_csrf_token';
$config['csrf_cookie_name'] = 'erp_csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
```

#### **Logging:**
```php
$config['log_threshold'] = 1;
// 0 = Disables logging
// 1 = Error Messages (including PHP errors)
// 2 = Debug Messages
// 3 = Informational Messages
// 4 = All Messages
```

#### **Date & Time:**
```php
$config['time_reference'] = 'local';  // or 'gmt'
```

Set PHP timezone in `index.php`:
```php
date_default_timezone_set('Asia/Dubai');
```

---

## 📦 3. Auto-load Configuration

### **File:** `application/config/autoload.php`

#### **Recommended Auto-load Settings:**

```php
// Libraries
$autoload['libraries'] = array(
    'database',          // Database access
    'session',           // Session management
    'form_validation'    // Form validation
);

// Helpers
$autoload['helper'] = array(
    'url',               // URL helpers
    'file',              // File operations
    'form',              // Form helpers
    'security',          // Security helpers
    'text',              // Text manipulation
    'date'               // Date/time helpers
);
```

#### **Additional Libraries (load as needed):**
```php
// In controllers:
$this->load->library('email');        // Email sending
$this->load->library('upload');       // File uploads
$this->load->library('pagination');   // Pagination
$this->load->library('image_lib');    // Image manipulation
```

#### **Additional Helpers (load as needed):**
```php
// In controllers:
$this->load->helper('download');      // File downloads
$this->load->helper('cookie');        // Cookie management
$this->load->helper('string');        // String functions
$this->load->helper('array');         // Array functions
```

---

## 🔀 4. URL Routing Configuration

### **File:** `application/config/routes.php`

#### **Default Routes:**

```php
$route['default_controller'] = 'dashboard/auth';
$route['login'] = 'dashboard/auth/index';
$route['logout'] = 'dashboard/auth/logout';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
```

#### **Custom Routes Examples:**

```php
// Dashboard
$route['dashboard'] = 'dashboard/index';
$route['home'] = 'dashboard/index';

// Customers
$route['customers'] = 'customers/index';
$route['customers/add'] = 'customers/add';
$route['customers/(:num)'] = 'customers/view/$1';

// Policies
$route['policies'] = 'policies/index';
$route['policies/add'] = 'policies/add';
$route['policies/(:num)'] = 'policies/view/$1';

// Reports
$route['reports'] = 'reports/index';
$route['reports/(:any)'] = 'reports/$1';

// API routes (if applicable)
$route['api/(:any)'] = 'api/$1';
```

---

## 🌐 5. URL Rewriting (.htaccess)

### **File:** `.htaccess` (root directory)

#### **Standard Configuration:**

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

#### **With HTTPS Redirect:**

```apache
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

#### **With WWW Redirect:**

```apache
RewriteEngine On

# Force WWW
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^(.*)$ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

#### **Security Headers:**

```apache
# Disable directory browsing
Options -Indexes

# Prevent access to .htaccess and other hidden files
<Files ~ "^\.*">
    Require all denied
</Files>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

---

## 🔐 6. Environment Variables (.env)

### **File:** `.env` (create from `.env.example`)

#### **How to Use:**

1. **Copy the example file:**
   ```bash
   cp .env.example .env
   ```

2. **Edit .env with your settings:**
   ```bash
   nano .env
   ```

3. **Never commit .env to Git:**
   Add to `.gitignore`:
   ```
   .env
   ```

#### **Key Settings:**

```ini
# Environment
CI_ENVIRONMENT = production

# Database
DB_HOSTNAME = localhost
DB_USERNAME = your_db_user
DB_PASSWORD = your_secure_password
DB_DATABASE = your_db_name

# Application
APP_NAME = "Insurance ERP"
BASE_URL = https://yourdomain.com/

# Company
COMPANY_NAME = "Your Insurance Company"
COMPANY_CURRENCY = AED
COMPANY_VAT_RATE = 5.00

# Email
MAIL_SMTP_HOST = smtp.gmail.com
MAIL_SMTP_USER = your-email@gmail.com
MAIL_SMTP_PASS = your-app-password

# Security
CSRF_PROTECTION = TRUE

# Backups
BACKUP_ENABLED = true
BACKUP_FREQUENCY = daily
BACKUP_KEEP_COUNT = 30
```

---

## 📧 7. Email Configuration

### **Gmail SMTP:**

```php
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'your-email@gmail.com';
$config['smtp_pass'] = 'your-app-password';  // Use App Password
$config['smtp_crypto'] = 'tls';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
```

**How to get Gmail App Password:**
1. Go to Google Account settings
2. Security → 2-Step Verification
3. App passwords → Generate

### **SendGrid:**

```php
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.sendgrid.net';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'apikey';
$config['smtp_pass'] = 'your-sendgrid-api-key';
$config['smtp_crypto'] = 'tls';
```

### **AWS SES:**

```php
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'email-smtp.us-east-1.amazonaws.com';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'your-ses-smtp-username';
$config['smtp_pass'] = 'your-ses-smtp-password';
$config['smtp_crypto'] = 'tls';
```

---

## 🗂️ 8. File Upload Configuration

### **Upload Settings:**

```php
$config['upload_path'] = './uploads/';
$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
$config['max_size'] = 2048;  // KB (2MB)
$config['max_width'] = 2048;  // pixels
$config['max_height'] = 2048;  // pixels
```

### **Create Upload Directories:**

```bash
mkdir -p uploads/company
mkdir -p uploads/documents
mkdir -p uploads/policies
mkdir -p uploads/claims
mkdir -p uploads/customers
mkdir -p uploads/reports
mkdir -p backups

chmod 755 uploads
chmod 755 backups
```

### **Security:**

Add `.htaccess` in uploads directory:
```apache
# Prevent PHP execution in uploads
<FilesMatch "\.(php|php3|php4|php5|phtml)$">
    Require all denied
</FilesMatch>
```

---

## 🔒 9. Security Configuration

### **Essential Security Settings:**

#### **1. Disable Error Display in Production:**

In `index.php`:
```php
if (ENVIRONMENT == 'production') {
    ini_set('display_errors', 0);
    error_reporting(0);
}
```

#### **2. Secure Session Configuration:**

```php
$config['sess_cookie_name'] = 'erp_session';
$config['cookie_httponly'] = TRUE;
$config['cookie_secure'] = TRUE;  // If using HTTPS
$config['cookie_samesite'] = 'Strict';
```

#### **3. Enable CSRF Protection:**

```php
$config['csrf_protection'] = TRUE;
$config['csrf_regenerate'] = TRUE;
```

#### **4. XSS Filtering:**

```php
$config['global_xss_filtering'] = TRUE;
```

#### **5. Secure File Permissions:**

```bash
# Files
find . -type f -exec chmod 644 {} \;

# Directories
find . -type d -exec chmod 755 {} \;

# Specific directories
chmod 777 uploads
chmod 777 backups
chmod 777 application/logs
chmod 777 application/cache
```

#### **6. Database User Privileges:**

Create dedicated database user with minimal privileges:
```sql
CREATE USER 'erp_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON insurance_erp.* TO 'erp_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 🌍 10. Multi-Environment Setup

### **Development, Testing, Production**

#### **Set Environment:**

In `index.php`:
```php
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
```

#### **Apache Virtual Host:**

```apache
<VirtualHost *:80>
    ServerName dev.erp.local
    DocumentRoot /var/www/erp
    SetEnv CI_ENV development
</VirtualHost>

<VirtualHost *:80>
    ServerName erp.company.com
    DocumentRoot /var/www/erp
    SetEnv CI_ENV production
</VirtualHost>
```

#### **Environment-Specific Database:**

```php
if (ENVIRONMENT == 'development') {
    $db['default']['database'] = 'erp_dev';
} elseif (ENVIRONMENT == 'testing') {
    $db['default']['database'] = 'erp_test';
} else {
    $db['default']['database'] = 'erp_production';
}
```

---

## 🚀 11. Performance Configuration

### **Enable Caching:**

```php
$config['cache_on'] = TRUE;
$config['cachedir'] = 'application/cache/';
```

### **Output Compression:**

```php
$config['compress_output'] = TRUE;
```

### **Query Caching:**

```php
$db['default']['cache_on'] = TRUE;
$db['default']['cachedir'] = 'application/cache/db/';
```

### **Reduce Autoload:**

Only autoload essential libraries. Load others on-demand in controllers.

---

## 📋 12. Configuration Checklist

### **Before Deployment:**

- [ ] Set `ENVIRONMENT` to `'production'`
- [ ] Update `base_url` with production URL
- [ ] Configure production database credentials
- [ ] Enable HTTPS and force SSL
- [ ] Set secure `encryption_key`
- [ ] Configure email settings
- [ ] Set up cron jobs for backups
- [ ] Configure file upload directories and permissions
- [ ] Enable CSRF protection
- [ ] Disable error display
- [ ] Set secure session settings
- [ ] Configure logging (level 1 or 2)
- [ ] Set up proper file permissions
- [ ] Create dedicated database user
- [ ] Test all modules
- [ ] Set up SSL certificate
- [ ] Configure firewall rules
- [ ] Set up monitoring
- [ ] Create backup strategy
- [ ] Document custom configurations

---

## 🆘 13. Troubleshooting

### **Common Issues:**

#### **1. Database Connection Failed**
```
Solution:
- Check database credentials in database.php
- Verify MySQL service is running
- Check database user privileges
- Test connection with: php test_connection.php
```

#### **2. 404 Errors / Routing Issues**
```
Solution:
- Check .htaccess file exists and is readable
- Verify mod_rewrite is enabled in Apache
- Set index_page to '' in config.php
- Check AllowOverride is set to All in Apache config
```

#### **3. Session Not Working**
```
Solution:
- Check session save path permissions
- Verify session cookies are enabled
- Check cookie domain settings
- Ensure HTTPS if cookie_secure is TRUE
```

#### **4. File Upload Errors**
```
Solution:
- Check upload directory exists and is writable (chmod 777)
- Verify file size is within limits
- Check file type is in allowed_types
- Increase PHP upload_max_filesize and post_max_size
```

#### **5. CSRF Token Mismatch**
```
Solution:
- Clear browser cookies
- Check session is working
- Verify CSRF settings in config.php
- Ensure form has csrf field
```

---

## 📞 Support & Resources

### **Documentation:**
- CodeIgniter User Guide: https://codeigniter.com/userguide3/
- Project README: `README.md`
- Installation Guide: `INSTALLATION_GUIDE.md`
- Module Documentation: `COMPLETE_MODULE_DOCUMENTATION.md`

### **Configuration Files:**
- Database: `application/config/database.php`
- Main Config: `application/config/config.php`
- Autoload: `application/config/autoload.php`
- Routes: `application/config/routes.php`
- Environment: `.env` (from `.env.example`)

---

**End of Configuration Guide**

Generated: November 11, 2025
Version: 1.0.0

For complete system documentation, refer to `COMPLETE_MODULE_DOCUMENTATION.md`
