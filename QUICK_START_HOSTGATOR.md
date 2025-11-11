# 🚀 Quick Start - HostGator Deployment (30 Minutes)

## Prerequisites
- HostGator account active
- Domain or subdomain configured
- cPanel access credentials

---

## Step 1: Database Setup (5 minutes)

1. **Login to cPanel:** https://yourdomain.com:2083
2. **Navigate to:** MySQL Database Wizard
3. **Create database:** `insurance_erp`
   - Full name will be: `cpanel_username_insurance_erp`
4. **Create user:** `erp_user`
   - Full name will be: `cpanel_username_erp_user`
   - Generate strong password → **SAVE IT!**
5. **Grant:** ALL PRIVILEGES

**Document your credentials:**
```
Database Host: localhost
Database Name: cpanel_username_insurance_erp
Database User: cpanel_username_erp_user
Database Password: [your password]
```

---

## Step 2: Upload Files (10 minutes)

### Option A: File Manager (Easiest)

1. **Compress locally:**
   ```bash
   zip -r insurance_erp.zip * .htaccess
   ```

2. **Upload to cPanel:**
   - cPanel → File Manager
   - Navigate to `public_html`
   - Click Upload
   - Select `insurance_erp.zip`
   - After upload, click Extract

3. **Verify structure:**
   ```
   public_html/
   ├── application/
   ├── system/
   ├── assets/
   ├── uploads/
   ├── index.php
   └── .htaccess
   ```

### Option B: FTP (Alternative)

1. **Connect with FileZilla:**
   - Host: `ftp.yourdomain.com`
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21

2. **Upload all files to:** `/public_html/`

---

## Step 3: Set Permissions (3 minutes)

**Using cPanel File Manager:**

Right-click → Permissions:
- `application/cache/` → 777 (all boxes checked)
- `application/logs/` → 777
- `uploads/` → 777 (check "Recurse into subdirectories")

---

## Step 4: Import Database (5 minutes)

1. **cPanel → phpMyAdmin**
2. **Select your database** (cpanel_username_insurance_erp)
3. **Click Import tab**
4. **Choose file:** `database/insurance_erp_complete_schema.sql`
5. **Click Go**
6. **Verify:** 150+ tables created

7. **Create admin user** (Run in SQL tab):
```sql
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

---

## Step 5: Update Configuration (5 minutes)

### 5.1 Edit database.php

**Using cPanel File Manager:**
- Navigate to: `application/config/database.php`
- Right-click → Edit

**Update these lines:**
```php
'hostname' => 'localhost',
'username' => 'cpanel_username_erp_user',  // YOUR database user
'password' => 'YOUR_DATABASE_PASSWORD',     // YOUR password
'database' => 'cpanel_username_insurance_erp', // YOUR database
'db_debug' => FALSE,
```

### 5.2 Edit config.php

**Edit:** `application/config/config.php`

**Update these lines:**
```php
$config['base_url'] = 'https://yourdomain.com/';  // YOUR domain
$config['encryption_key'] = 'GENERATE_32_CHAR_KEY'; // Generate new key
$config['csrf_protection'] = TRUE;
$config['cookie_secure'] = TRUE;
$config['cookie_httponly'] = TRUE;
```

**Generate encryption key:**
```bash
# Online: https://www.random.org/strings/?num=1&len=32
# Or use: ABCDEFGHIJKLMNOPQRSTUVWXYZabcdef
```

### 5.3 Edit index.php

**Edit:** `index.php` (around line 60)

**Change:**
```php
define('ENVIRONMENT', 'production');  // Change from 'development'
```

---

## Step 6: Setup SSL (2 minutes)

1. **cPanel → SSL/TLS Status**
2. **Select your domain**
3. **Click:** Run AutoSSL
4. **Wait:** 2-5 minutes for installation
5. **Verify:** Access https://yourdomain.com (should show padlock)

---

## Step 7: Test Installation (5 minutes)

### 7.1 Access Application
- **URL:** https://yourdomain.com
- **Should redirect to:** https://yourdomain.com/auth/login

### 7.2 Login
- **Username:** `admin`
- **Password:** `Admin@123`
- **Should:** Login successfully to dashboard

### 7.3 Quick Tests
- [ ] Dashboard displays
- [ ] Customers → View list works
- [ ] Policies → Create new policy works
- [ ] Upload test file works
- [ ] No errors displayed

---

## Step 8: Secure System (5 minutes)

### 8.1 Change Admin Password
1. Login as admin
2. Users → My Profile
3. Change password to strong password
4. **SAVE NEW PASSWORD SECURELY!**

### 8.2 Update Company Settings
1. Settings → Company Settings
2. Update:
   - Company name
   - Address
   - Contact info
   - Upload logo
3. Save

### 8.3 Verify Security
Test these URLs (should show 403 Forbidden):
- https://yourdomain.com/application/
- https://yourdomain.com/system/
- https://yourdomain.com/application/config/database.php

---

## ✅ Deployment Complete!

**Your application is now live!**

**Access:** https://yourdomain.com
**Admin:** https://yourdomain.com/auth/login

---

## 🆘 Quick Troubleshooting

### 500 Error
- Check file permissions (755 for dirs, 644 for files)
- Check .htaccess uploaded correctly
- Check PHP version (7.4+)
- View error log: cPanel → Error Log

### Database Connection Error
- Verify credentials in `database.php`
- Check database user has privileges
- Ensure database exists in phpMyAdmin

### 404 Not Found
- Ensure .htaccess is uploaded
- Check RewriteBase in .htaccess
- Verify mod_rewrite enabled

### Can't Login
- Check database imported correctly
- Run admin user creation SQL again
- Clear browser cache
- Check session path in config.php

---

## 📚 Full Documentation

For complete deployment guide, see:
- **HOSTGATOR_DEPLOYMENT_GUIDE.md** - Comprehensive guide
- **DEPLOYMENT_CHECKLIST.md** - 200+ item checklist

---

## 📞 Need Help?

**HostGator Support:**
- Live Chat: 24/7 at https://www.hostgator.com/contact
- Phone: Check your welcome email

**Documentation:**
- See HOSTGATOR_DEPLOYMENT_GUIDE.md for detailed instructions
- See DEPLOYMENT_CHECKLIST.md for complete checklist

---

## 🎉 Next Steps

1. Create additional users
2. Import your actual data
3. Train your team
4. Setup automatic backups
5. Configure email (if needed)

**Congratulations! Your Insurance ERP is live! 🚀**
