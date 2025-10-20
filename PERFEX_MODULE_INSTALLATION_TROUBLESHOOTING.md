# 🔧 Perfex CRM Module Installation Troubleshooting Guide

## ❌ Problem: Module Not Appearing in Perfex CRM Modules List

If the ZATCA Invoice QR module doesn't appear after uploading the ZIP file, follow these troubleshooting steps:

---

## ✅ **Method 1: Manual Installation (RECOMMENDED)**

### Step 1: Extract the ZIP File
1. Download `zatca_invoice_qr_v1.0.0.zip`
2. Extract it on your computer
3. You should see a folder named `zatca_invoice_qr`

### Step 2: Upload to Perfex CRM via FTP/cPanel
1. Connect to your server via FTP (FileZilla, WinSCP) or use cPanel File Manager
2. Navigate to: `[perfex_root]/modules/`
   ```
   Example paths:
   - /public_html/modules/
   - /home/username/public_html/perfex/modules/
   - /var/www/html/perfex/modules/
   ```

3. Upload the **entire `zatca_invoice_qr` folder** into the `modules` directory

4. Final structure should look like:
   ```
   [perfex_root]/
   ├── modules/
   │   ├── zatca_invoice_qr/          ← Upload here
   │   │   ├── zatca_invoice_qr.php   ← Main module file
   │   │   ├── install.php
   │   │   ├── uninstall.php
   │   │   ├── controllers/
   │   │   ├── models/
   │   │   ├── views/
   │   │   ├── libraries/
   │   │   ├── assets/
   │   │   └── language/
   │   ├── [other_modules]/
   ```

### Step 3: Set Correct Permissions
Set folder permissions (via FTP or SSH):
```bash
chmod 755 /path/to/perfex/modules/zatca_invoice_qr
chmod 644 /path/to/perfex/modules/zatca_invoice_qr/*.php
chmod 755 /path/to/perfex/modules/zatca_invoice_qr/*/
```

### Step 4: Access Perfex CRM Admin
1. Login to Perfex CRM admin panel
2. Go to: **Setup → Modules**
3. You should now see **"ZATCA Invoice QR"** in the list
4. Click **"Install"** button
5. After installation, click **"Activate"** button

---

## ✅ **Method 2: Perfex Built-in ZIP Upload (If Available)**

**Note**: This method only works if your Perfex version supports ZIP upload.

### Requirements:
- Perfex CRM version 2.9.0 or higher
- PHP ZipArchive extension enabled
- Write permissions on `modules/` directory

### Steps:
1. Go to: **Setup → Modules**
2. Look for **"Upload Module"** or **"Install from ZIP"** button
3. Click and select `zatca_invoice_qr_v1.0.0.zip`
4. Wait for upload and extraction
5. Click **"Install"** when module appears
6. Click **"Activate"**

### If Upload Fails:
- Check PHP `upload_max_filesize` (ZIP is ~300KB, should be fine)
- Check `post_max_size` in php.ini
- Verify write permissions on `modules/` folder
- Check server error logs

---

## 🔍 **Common Issues & Solutions**

### Issue 1: Module Doesn't Appear After Upload
**Cause**: Incorrect folder structure in ZIP

**Solution**: Extract ZIP and verify structure:
```bash
# Correct structure:
zatca_invoice_qr/
├── zatca_invoice_qr.php  ← Must be at root of folder
├── install.php
└── [other files]

# WRONG structure (extra nested folder):
zatca_invoice_qr/
└── zatca_invoice_qr/
    ├── zatca_invoice_qr.php  ← Too deep!
    └── [other files]
```

**Fix**: If structure is wrong, manually extract and upload just the inner `zatca_invoice_qr` folder.

---

### Issue 2: "Module Already Exists" Error
**Cause**: Previous installation remnants

**Solution**:
```sql
-- Run in phpMyAdmin or MySQL client
DELETE FROM tblmodules WHERE module_name = 'zatca_invoice_qr';

-- Then drop tables (if exist)
DROP TABLE IF EXISTS tblzatca_settings;
DROP TABLE IF EXISTS tblzatca_invoice_qr;
DROP TABLE IF EXISTS tblzatca_certificates;
```

Then re-upload and install.

---

### Issue 3: Permission Denied Errors
**Cause**: Incorrect file/folder permissions

**Solution** (via SSH):
```bash
cd /path/to/perfex/modules/zatca_invoice_qr
chmod 755 .
chmod 644 *.php
chmod 755 controllers models views libraries assets language helpers config
chmod 644 controllers/*.php models/*.php views/**/*.php
```

---

### Issue 4: Module Appears but "Install" Button Doesn't Work
**Cause**: Missing or corrupted `install.php` file

**Solution**:
1. Verify `install.php` exists in module root
2. Check file isn't corrupted (should be ~10KB)
3. Check PHP error logs: `/path/to/perfex/uploads/errors.log`
4. Enable debug mode in Perfex: `config/app-config.php`
   ```php
   define('APP_CSRF_PROTECTION', false); // Temporarily for testing
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

---

### Issue 5: White Screen / 500 Error After Activation
**Cause**: PHP syntax error or missing dependencies

**Check**:
1. PHP version ≥ 7.2 (module requires 7.2+)
2. Required PHP extensions:
   ```bash
   php -m | grep -E 'gd|zip|mbstring|mysqli'
   ```
3. Check PHP error log for details

---

### Issue 6: Database Tables Not Created
**Cause**: Installation script didn't run

**Manual Fix** (run in phpMyAdmin):
```sql
-- Create settings table
CREATE TABLE IF NOT EXISTS `tblzatca_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `enabled` TINYINT(1) DEFAULT 0,
  `phase` ENUM('phase1', 'phase2') DEFAULT 'phase1',
  `seller_name` VARCHAR(255) DEFAULT NULL,
  `vat_number` VARCHAR(50) DEFAULT NULL,
  `qr_position` ENUM('top-right', 'top-left', 'bottom-right', 'bottom-left') DEFAULT 'top-right',
  `qr_size` INT DEFAULT 150,
  `auto_generate` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tblzatca_settings` (`id`, `enabled`, `phase`, `qr_position`, `qr_size`, `auto_generate`) 
VALUES (1, 0, 'phase1', 'top-right', 150, 1);

-- Create QR storage table
CREATE TABLE IF NOT EXISTS `tblzatca_invoice_qr` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT UNSIGNED NOT NULL,
  `qr_data` TEXT COMMENT 'TLV encoded QR data (base64)',
  `qr_base64` LONGTEXT COMMENT 'Base64 encoded PNG image',
  `status` ENUM('pending', 'generated', 'failed') DEFAULT 'pending',
  `generation_date` DATETIME DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `invoice_id` (`invoice_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create certificates table (Phase 2)
CREATE TABLE IF NOT EXISTS `tblzatca_certificates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `certificate_type` ENUM('production', 'sandbox') DEFAULT 'sandbox',
  `certificate_data` LONGTEXT,
  `private_key` LONGTEXT,
  `csr` TEXT,
  `otp` VARCHAR(6),
  `compliance_request_id` VARCHAR(100),
  `secret` VARCHAR(255),
  `is_active` TINYINT(1) DEFAULT 0,
  `expires_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 📋 **Verification Checklist**

After installation, verify everything is working:

### ✅ Step 1: Check Module Status
- [ ] Module appears in **Setup → Modules**
- [ ] Status shows **"Installed"** or **"Active"**
- [ ] No error messages displayed

### ✅ Step 2: Check Database Tables
Run in phpMyAdmin:
```sql
SHOW TABLES LIKE 'tblzatca_%';
```
Should return:
```
tblzatca_settings
tblzatca_invoice_qr
tblzatca_certificates
```

### ✅ Step 3: Check Settings Page
- [ ] Go to **Setup → ZATCA Invoice QR** (menu item appears)
- [ ] Settings page loads without errors
- [ ] Form fields visible (Seller Name, VAT Number, etc.)

### ✅ Step 4: Test QR Generation
1. Configure settings:
   - Enable module: **ON**
   - Enter Seller Name: `Test Company`
   - Enter VAT Number: `123456789012345` (15 digits)
2. Click **"Test QR Generation"** button
3. QR code image should appear in popup

### ✅ Step 5: Test on Invoice
1. Create or edit any invoice
2. Save invoice
3. Click **"View PDF"**
4. QR code should appear on PDF (top-right by default)

---

## 🆘 **Still Not Working?**

### Get Detailed Error Information:

#### 1. Enable Perfex Debug Mode
Edit `config/app-config.php`:
```php
define('APP_ENV', 'development'); // Changed from 'production'
```

#### 2. Check Error Logs
- **Perfex Log**: `uploads/errors.log`
- **PHP Error Log**: `/var/log/php/error.log` or check cPanel
- **Apache/Nginx Log**: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`

#### 3. Browser Console
- Open browser DevTools (F12)
- Check Console tab for JavaScript errors
- Check Network tab for failed requests

#### 4. Database Query Log
Add to `config/app-config.php`:
```php
$db['debug'] = true; // Enable query logging
```

---

## 📞 **Support Information**

If you've tried all troubleshooting steps and module still doesn't appear:

1. **Provide the following information:**
   - Perfex CRM version (found in: Setup → About)
   - PHP version: Run `<?php echo phpversion(); ?>`
   - Server type (Apache/Nginx/Litespeed)
   - Hosting provider (shared/VPS/dedicated)
   - Error logs from `uploads/errors.log`

2. **Check module file integrity:**
   ```bash
   # Verify main file exists and is readable
   ls -lh /path/to/perfex/modules/zatca_invoice_qr/zatca_invoice_qr.php
   
   # Should show ~6-7KB file size
   ```

3. **Verify Perfex installation:**
   - Can you install other modules successfully?
   - Are other modules working properly?
   - Is Perfex CRM itself functioning normally?

---

## ✅ **Quick Reference: Installation Paths**

### Common Perfex Installation Locations:

| Hosting Type | Typical Path |
|--------------|--------------|
| **cPanel** | `/home/username/public_html/perfex/modules/` |
| **Direct Admin** | `/home/username/domains/example.com/public_html/perfex/modules/` |
| **Plesk** | `/var/www/vhosts/example.com/httpdocs/perfex/modules/` |
| **Ubuntu/Apache** | `/var/www/html/perfex/modules/` |
| **Docker** | `/var/www/html/modules/` (inside container) |

### How to Find Your Perfex Path:
1. Login to Perfex admin
2. Go to: **Setup → About**
3. Path shown or check `config/app-config.php`

---

## 🎯 **Expected Result After Successful Installation**

### Admin Panel:
- ✅ Menu item: **Setup → ZATCA Invoice QR**
- ✅ Module list shows: **"ZATCA Invoice QR v1.0.0"**
- ✅ Status: **"Active"** or **"Installed & Activated"**

### Database:
- ✅ 3 new tables created (tblzatca_*)
- ✅ Default settings row inserted

### Functionality:
- ✅ Settings page accessible
- ✅ Test QR button generates QR codes
- ✅ QR codes appear on invoice PDFs
- ✅ No errors in logs

---

## 📚 **Additional Resources**

- **Module README**: `/zatca_invoice_qr/README.md`
- **Installation Guide**: `/zatca_invoice_qr/INSTALLATION.md`
- **Testing Guide**: `/TESTING_GUIDE.md`
- **Perfex Modules Docs**: https://docs.perfexcrm.com/modules/

---

**Last Updated**: 2025-10-20  
**Module Version**: 1.0.0  
**Perfex Compatibility**: 2.3.* and higher
