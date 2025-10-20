# 🔧 Fix: 500 Internal Server Error on Module Activation

## ❌ **Error**
```
GET https://afaqinfotech.com/erp/admin/modules/activate/zatca_invoice_qr 
net::ERR_HTTP_RESPONSE_CODE_FAILURE 500 (Internal Server Error)
```

---

## 🔍 **Root Cause**

The 500 error during module activation is typically caused by:
1. **ENUM field compatibility** - Some MySQL/MariaDB versions don't support ENUM with quoted values
2. **Undefined constant** - `ZATCA_INVOICE_QR_VERSION` not available during installation
3. **PHP version incompatibility** - Array syntax or type declarations
4. **Database permission issues** - User lacks CREATE TABLE privileges
5. **Memory limit** - PHP memory exhausted during installation

---

## ✅ **SOLUTION 1: Replace install.php (Recommended)**

I've fixed the install.php file. You need to replace it on your server.

### **Step 1: Download Fixed File**

The fixed file is at:
```
/home/user/webapp/zatca_invoice_qr/install.php
```

Or download from GitHub (after I push):
```
https://github.com/afaqintegrated-lab/Zatca-qr-code/blob/main/zatca_invoice_qr/install.php
```

### **Step 2: Replace on Server**

**Via FTP:**
1. Connect to your server via FTP
2. Navigate to: `/erp/modules/zatca_invoice_qr/`
3. Delete old `install.php`
4. Upload new `install.php`

**Via cPanel File Manager:**
1. Login to cPanel
2. Open File Manager
3. Navigate to: `public_html/erp/modules/zatca_invoice_qr/`
4. Delete `install.php`
5. Upload new `install.php`

**Via SSH:**
```bash
cd /path/to/erp/modules/zatca_invoice_qr/
# Backup old file
mv install.php install.php.backup

# Upload new file (use your method)
# Then set permissions
chmod 644 install.php
```

### **Step 3: Try Activation Again**

1. Go to: **Setup → Modules**
2. Find "ZATCA Invoice QR"
3. Click **"Activate"** button
4. Should succeed now!

---

## ✅ **SOLUTION 2: Check PHP Error Log**

Before trying other solutions, let's see the exact error.

### **Find Error Log:**

**Perfex Error Log:**
```
/path/to/erp/uploads/errors.log
```

**PHP Error Log (varies by hosting):**
```bash
# cPanel
/home/username/public_html/error_log

# Ubuntu/Debian Apache
/var/log/apache2/error.log

# CentOS/RHEL Apache
/var/log/httpd/error_log

# Nginx
/var/log/nginx/error.log
```

**View last 50 lines:**
```bash
tail -50 /path/to/error.log
```

### **Common Errors & Fixes:**

#### **Error 1: Undefined constant**
```
PHP Fatal error: Undefined constant 'ZATCA_INVOICE_QR_VERSION'
```

**Fix:** Use the fixed install.php (Solution 1)

---

#### **Error 2: ENUM syntax error**
```
Error: You have an error in your SQL syntax near 'ENUM'
```

**Fix:** Use `install_safe.php` which uses VARCHAR instead of ENUM

---

#### **Error 3: Memory exhausted**
```
PHP Fatal error: Allowed memory size of X bytes exhausted
```

**Fix:** Increase PHP memory limit in `php.ini`:
```ini
memory_limit = 256M
```

Or add to `.htaccess`:
```apache
php_value memory_limit 256M
```

---

#### **Error 4: Permission denied**
```
Error: CREATE TABLE permission denied
```

**Fix:** Grant database permissions:
```sql
GRANT CREATE, ALTER, DROP ON database_name.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

---

#### **Error 5: Table already exists**
```
Error: Table 'tblzatca_settings' already exists
```

**Fix:** Drop existing tables and retry:
```sql
DROP TABLE IF EXISTS tblzatca_settings;
DROP TABLE IF EXISTS tblzatca_invoice_qr;
DROP TABLE IF EXISTS tblzatca_certificates;
```

---

## ✅ **SOLUTION 3: Use Safe Install Script**

If the fixed install.php still fails, use the safe version:

### **Step 1: Upload install_safe.php**

Upload `/home/user/webapp/zatca_invoice_qr/install_safe.php` to your server.

### **Step 2: Temporarily Rename**

**Via SSH:**
```bash
cd /path/to/erp/modules/zatca_invoice_qr/
mv install.php install.php.original
mv install_safe.php install.php
```

**Via cPanel:**
1. Rename `install.php` to `install.php.original`
2. Rename `install_safe.php` to `install.php`

### **Step 3: Try Activation**

Go to Perfex → Setup → Modules → Activate

### **Step 4: Restore (if needed)**

If successful, keep the new install.php. If not, restore:
```bash
mv install.php.original install.php
```

---

## ✅ **SOLUTION 4: Manual Database Installation**

If automatic installation keeps failing, install tables manually.

### **Step 1: Access phpMyAdmin**

1. Login to cPanel
2. Click **phpMyAdmin**
3. Select your Perfex database

### **Step 2: Run SQL Manually**

Copy and paste this SQL (replace `tbl` prefix if different):

```sql
-- Table 1: Settings
CREATE TABLE IF NOT EXISTS `tblzatca_settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Enable/disable QR code generation',
  `phase` VARCHAR(20) NOT NULL DEFAULT 'phase1' COMMENT 'ZATCA implementation phase',
  `environment` VARCHAR(20) NOT NULL DEFAULT 'sandbox' COMMENT 'Environment mode',
  `seller_name` VARCHAR(255) DEFAULT NULL COMMENT 'Company/Seller name for QR code',
  `vat_number` VARCHAR(50) DEFAULT NULL COMMENT 'VAT registration number',
  `company_address` TEXT DEFAULT NULL COMMENT 'Company address for Phase 2',
  `qr_position` VARCHAR(20) NOT NULL DEFAULT 'top-right' COMMENT 'QR code position on PDF',
  `qr_size` INT(11) NOT NULL DEFAULT 150 COMMENT 'QR code size in pixels',
  `auto_generate` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Auto-generate QR on invoice create/update',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `tblzatca_settings` 
(`enabled`, `phase`, `environment`, `seller_name`, `vat_number`, `qr_position`, `qr_size`, `auto_generate`, `created_at`, `updated_at`) 
VALUES 
(0, 'phase1', 'sandbox', '', '', 'top-right', 150, 1, NOW(), NOW());

-- Table 2: Invoice QR codes
CREATE TABLE IF NOT EXISTS `tblzatca_invoice_qr` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to tblinvoices.id',
  `qr_data` TEXT NOT NULL COMMENT 'TLV encoded QR data (Base64)',
  `qr_base64` LONGTEXT DEFAULT NULL COMMENT 'Base64 encoded QR code image',
  `invoice_hash` VARCHAR(255) DEFAULT NULL COMMENT 'SHA-256 hash of invoice (Phase 2)',
  `uuid` VARCHAR(100) DEFAULT NULL COMMENT 'Unique UUID for invoice (Phase 2)',
  `digital_signature` TEXT DEFAULT NULL COMMENT 'ECDSA digital signature (Phase 2)',
  `generation_date` DATETIME DEFAULT NULL COMMENT 'When QR code was generated',
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'Generation status',
  `error_message` TEXT DEFAULT NULL COMMENT 'Error message if generation failed',
  `phase` VARCHAR(20) NOT NULL DEFAULT 'phase1' COMMENT 'Which phase was used for generation',
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: Certificates (Phase 2)
CREATE TABLE IF NOT EXISTS `tblzatca_certificates` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_type` VARCHAR(20) NOT NULL COMMENT 'Type of certificate',
  `certificate` TEXT DEFAULT NULL COMMENT 'X.509 certificate (PEM format)',
  `private_key` TEXT DEFAULT NULL COMMENT 'Private key (encrypted, PEM format)',
  `csr` TEXT DEFAULT NULL COMMENT 'Certificate Signing Request',
  `secret` VARCHAR(255) DEFAULT NULL COMMENT 'API secret for ZATCA',
  `issued_date` DATETIME DEFAULT NULL COMMENT 'Certificate issue date',
  `expiry_date` DATETIME DEFAULT NULL COMMENT 'Certificate expiry date',
  `status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT 'Certificate status',
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `certificate_type` (`certificate_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Step 3: Add Module Options**

```sql
-- Check if options exist first
SELECT * FROM tbloptions WHERE name LIKE 'zatca_invoice_qr%';

-- If not exists, insert:
INSERT INTO `tbloptions` (`name`, `value`, `autoload`) 
VALUES 
('zatca_invoice_qr_version', '1.0.0', 1),
('zatca_invoice_qr_installed', NOW(), 1);
```

### **Step 4: Mark Module as Installed**

```sql
-- Check modules table
SELECT * FROM tblmodules WHERE module_name = 'zatca_invoice_qr';

-- If not exists, insert:
INSERT INTO `tblmodules` (`module_name`, `active`) 
VALUES ('zatca_invoice_qr', 1);

-- If exists, update:
UPDATE `tblmodules` SET `active` = 1 WHERE `module_name` = 'zatca_invoice_qr';
```

### **Step 5: Verify Tables Created**

```sql
SHOW TABLES LIKE 'tblzatca%';
```

Should return 3 tables:
- `tblzatca_settings`
- `tblzatca_invoice_qr`
- `tblzatca_certificates`

### **Step 6: Refresh Perfex**

1. Go to Perfex admin
2. Go to: **Setup → Modules**
3. Module should show as **"Active"**
4. If not, try clicking **"Activate"** again (should succeed now)

---

## ✅ **SOLUTION 5: Enable Debug Mode**

To see the exact error in browser:

### **Step 1: Enable Debug in Perfex**

Edit `/path/to/erp/application/config/config.php`:

Find:
```php
define('APP_DEBUG', false);
```

Change to:
```php
define('APP_DEBUG', true);
```

### **Step 2: Show PHP Errors**

Add to top of `/path/to/erp/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### **Step 3: Try Activation Again**

Now you'll see the actual error message on screen.

### **Step 4: Disable Debug (After Fixing)**

**Important:** Turn off debug mode after fixing:
```php
define('APP_DEBUG', false);
error_reporting(0);
ini_set('display_errors', 0);
```

---

## ✅ **SOLUTION 6: Check PHP Version**

The module requires PHP 7.2 or higher.

### **Check PHP Version:**

**Via SSH:**
```bash
php -v
```

**Via PHP Info File:**
Create `info.php`:
```php
<?php phpinfo(); ?>
```

Upload to: `/erp/info.php`
Visit: `https://afaqinfotech.com/erp/info.php`
Delete after checking!

### **If PHP < 7.2:**
- Update PHP through cPanel or contact host
- Perfex requires PHP 7.2+ anyway

---

## ✅ **SOLUTION 7: Check Required Extensions**

The module needs these PHP extensions:

### **Required:**
- ✅ `gd` - For QR code image generation
- ✅ `mbstring` - For string handling
- ✅ `mysqli` - For database

### **Check Extensions:**

**Via SSH:**
```bash
php -m | grep -E 'gd|mbstring|mysqli'
```

**Via phpinfo():**
Look for "gd", "mbstring", "mysqli" sections

### **If Missing:**

**Ubuntu/Debian:**
```bash
sudo apt-get install php-gd php-mbstring php-mysqli
sudo service apache2 restart
```

**cPanel:**
Use "Select PHP Version" → Enable extensions

---

## 🔍 **Diagnostic Checklist**

Before trying solutions, check:

- [ ] **PHP Version** ≥ 7.2
- [ ] **MySQL Version** ≥ 5.6
- [ ] **PHP Extensions**: gd, mbstring, mysqli enabled
- [ ] **PHP Memory**: ≥ 128M (256M recommended)
- [ ] **Database Permissions**: CREATE, ALTER, DROP
- [ ] **File Permissions**: 644 for PHP files, 755 for directories
- [ ] **Error Log Location**: Know where to find PHP errors
- [ ] **Perfex Version**: ≥ 2.3.0

---

## 📊 **Quick Fix Flowchart**

```
500 Error on Activation
        ↓
1. Check PHP error log
        ↓
    See error?
     ↙    ↘
   YES    NO
    ↓      ↓
Apply  Try replacing
fix    install.php
        ↓
    Still fails?
        ↓
  Use install_safe.php
        ↓
    Still fails?
        ↓
Manual SQL installation
        ↓
    SUCCESS! ✅
```

---

## 🆘 **If Nothing Works**

### **Last Resort: Fresh Installation**

1. **Remove module completely:**
```sql
-- Drop tables
DROP TABLE IF EXISTS tblzatca_settings;
DROP TABLE IF EXISTS tblzatca_invoice_qr;
DROP TABLE IF EXISTS tblzatca_certificates;

-- Remove options
DELETE FROM tbloptions WHERE name LIKE 'zatca_invoice_qr%';

-- Remove module registration
DELETE FROM tblmodules WHERE module_name = 'zatca_invoice_qr';
```

2. **Delete module folder:**
```bash
rm -rf /path/to/erp/modules/zatca_invoice_qr/
```

3. **Re-upload fresh module**

4. **Try installation again**

---

## 📞 **Get Help**

If you're still stuck after trying all solutions:

### **Provide These Details:**

1. **PHP Version**: `php -v`
2. **MySQL Version**: `mysql --version`
3. **Perfex Version**: Setup → About
4. **Exact Error**: From error log (last 50 lines)
5. **PHP Extensions**: `php -m`
6. **What you tried**: Which solutions from above

### **Error Log:**
```bash
# Get error log
tail -50 /path/to/erp/uploads/errors.log

# Or PHP error log
tail -50 /var/log/apache2/error.log  # Or your server's location
```

---

## ✅ **After Successful Activation**

Once activated successfully:

1. ✅ Go to: **Setup → ZATCA Invoice QR**
2. ✅ Enable module
3. ✅ Enter Seller Name
4. ✅ Enter VAT Number (15 digits)
5. ✅ Save settings
6. ✅ Click "Test QR Generation"
7. ✅ Create invoice and verify QR appears

---

## 📝 **Summary**

**Most Common Fix:** Replace install.php with the fixed version (Solution 1)

**If that fails:** Try install_safe.php (Solution 3)

**Last resort:** Manual SQL installation (Solution 4)

**Always check:** PHP error logs for exact error message

---

**File**: FIX_500_ERROR_ACTIVATION.md  
**Last Updated**: 2025-10-20  
**Module Version**: 1.0.0
