# ZATCA Invoice QR - Installation Guide

Complete step-by-step installation guide for the ZATCA Invoice QR module.

## 📋 Pre-Installation Checklist

Before installing, ensure you have:

- [ ] Perfex CRM version 2.3.0 or higher
- [ ] PHP 7.4 or higher
- [ ] PHP GD extension enabled
- [ ] PHP mbstring extension enabled
- [ ] MySQL/MariaDB database access
- [ ] Admin access to Perfex CRM
- [ ] Your Saudi VAT registration number (15 digits)
- [ ] Company/seller name for invoices

## 🔍 Verify PHP Requirements

Run these commands to verify your PHP installation:

```bash
# Check PHP version
php -v

# Check required extensions
php -m | grep -i gd
php -m | grep -i mbstring
php -m | grep -i openssl
```

Expected output should show PHP 7.4+ and list the extensions.

## 📥 Installation Methods

### Method 1: Upload via Perfex CRM Admin (Recommended)

This is the easiest method for most users.

#### Step 1: Download the Module

1. Download the latest release ZIP file from:
   - GitHub Releases: `https://github.com/afaqintegrated-lab/zatca-invoice-qr/releases`
   - Or use the "Download ZIP" button

#### Step 2: Upload to Perfex CRM

1. Login to your Perfex CRM admin panel
2. Navigate to **Setup → Modules**
3. Click the **"Upload New Module"** button
4. Click **"Choose File"** and select the downloaded ZIP file
5. Click **"Upload"**

#### Step 3: Install the Module

1. After upload, you'll see "ZATCA Invoice QR" in the modules list
2. Click the **"Install"** button next to the module name
3. Wait for the installation to complete (usually 5-10 seconds)
4. You should see a success message

#### Step 4: Activate the Module

1. After installation, click the **"Activate"** button
2. The module is now active and ready to configure

#### Step 5: Verify Installation

1. Check that new menu item appears: **Setup → ZATCA Invoice QR**
2. Navigate to the settings page
3. Verify database tables were created (see Troubleshooting section)

---

### Method 2: Manual Installation via FTP/SSH

Use this method if you have direct server access.

#### Step 1: Download/Clone the Repository

**Option A: Download ZIP**
```bash
wget https://github.com/afaqintegrated-lab/zatca-invoice-qr/archive/main.zip
unzip main.zip
```

**Option B: Clone with Git**
```bash
git clone https://github.com/afaqintegrated-lab/zatca-invoice-qr.git
```

#### Step 2: Upload to Server

Upload the entire `zatca_invoice_qr` folder to your Perfex modules directory:

**Via FTP**:
- Use FileZilla or your preferred FTP client
- Navigate to `/path/to/perfex/modules/`
- Upload the `zatca_invoice_qr` folder

**Via SSH/Terminal**:
```bash
# Navigate to Perfex modules directory
cd /path/to/perfex/modules/

# Copy the module folder
cp -r /path/to/downloaded/zatca_invoice_qr ./

# Set correct permissions
chmod -R 755 zatca_invoice_qr
chown -R www-data:www-data zatca_invoice_qr  # Adjust user/group as needed
```

#### Step 3: Set File Permissions

```bash
cd /path/to/perfex/modules/zatca_invoice_qr

# Make directories writable
chmod 755 .
chmod -R 755 libraries/phpqrcode/cache

# Ensure PHP can write temp QR codes
mkdir -p ../../uploads/temp/zatca_qr
chmod 755 ../../uploads/temp/zatca_qr
```

#### Step 4: Install via Perfex Admin

1. Login to Perfex CRM admin panel
2. Go to **Setup → Modules**
3. Find "ZATCA Invoice QR" in the list
4. Click **"Install"**
5. Then click **"Activate"**

---

### Method 3: Manual Database Installation (Advanced)

If automatic installation fails, you can manually create database tables.

#### Step 1: Access MySQL/PhpMyAdmin

```bash
mysql -u username -p database_name
```

#### Step 2: Run Installation SQL

Execute the contents of `install.php` or manually run:

```sql
-- Table 1: Settings
CREATE TABLE IF NOT EXISTS `tblzatca_settings` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `phase` enum('phase1','phase2') DEFAULT 'phase1',
  `environment` enum('sandbox','production') DEFAULT 'sandbox',
  `seller_name` varchar(255) NOT NULL DEFAULT '',
  `vat_number` varchar(50) NOT NULL DEFAULT '',
  `company_address` text,
  `qr_position` enum('top-right','top-left','bottom-right','bottom-left') DEFAULT 'top-right',
  `qr_size` int(11) DEFAULT 150,
  `auto_generate` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `tblzatca_settings` 
(`id`, `enabled`, `phase`, `environment`, `seller_name`, `vat_number`, `qr_position`, `qr_size`, `auto_generate`, `created_at`, `updated_at`)
VALUES
(1, 0, 'phase1', 'sandbox', '', '', 'top-right', 150, 1, NOW(), NOW());

-- Table 2: Invoice QR Codes
CREATE TABLE IF NOT EXISTS `tblzatca_invoice_qr` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) UNSIGNED NOT NULL,
  `qr_data` text NOT NULL,
  `qr_base64` longtext,
  `invoice_hash` varchar(255),
  `uuid` varchar(100),
  `digital_signature` text,
  `generation_date` datetime NOT NULL,
  `status` enum('pending','generated','failed') DEFAULT 'pending',
  `error_message` text,
  `phase` enum('phase1','phase2') DEFAULT 'phase1',
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: Certificates (for Phase 2)
CREATE TABLE IF NOT EXISTS `tblzatca_certificates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_type` enum('compliance','production') NOT NULL,
  `certificate` text,
  `private_key` text,
  `csr` text,
  `secret` varchar(255),
  `issued_date` datetime,
  `expiry_date` datetime,
  `status` enum('active','expired','revoked') DEFAULT 'active',
  `created_at` datetime,
  PRIMARY KEY (`id`),
  KEY `certificate_type` (`certificate_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## ⚙️ Post-Installation Configuration

### Step 1: Access Settings Page

1. Login to Perfex CRM admin
2. Go to **Setup → ZATCA Invoice QR**
3. You should see the settings page

### Step 2: Configure Module Settings

Fill in the required information:

#### Enable the Module
- ✅ Check "Enable Module" checkbox

#### Select Phase
- Select **Phase 1** (Phase 2 coming soon)

#### Set Environment
- **Sandbox**: For testing (recommended initially)
- **Production**: For live use

#### Enter Seller Information

**Seller/Company Name** (Required)
```
Example: مؤسسة آفاق المتكاملة لتقنية المعلومات
```

**VAT Registration Number** (Required)
```
Format: 15 digits
Example: 310122393500003
```

**Company Address** (Optional, required for Phase 2)
```
Example: Riyadh, Saudi Arabia
```

#### Configure QR Settings

**QR Position on PDF**
- Choose: Top Right, Top Left, Bottom Right, or Bottom Left
- Recommended: **Top Right**

**QR Size**
- Range: 100-300 pixels
- Recommended: **150 pixels**

**Auto-generate QR Codes**
- ✅ Enable to automatically generate QR for new invoices

### Step 3: Test QR Generation

1. Click **"Test QR Generation"** button
2. A modal will appear showing:
   - Generated QR code image
   - Decoded TLV data
   - Size validation
   - Raw Base64 data
3. Verify all 5 fields are correct:
   - Tag 1: Seller Name
   - Tag 2: VAT Number
   - Tag 3: Date/Time
   - Tag 4: Invoice Total
   - Tag 5: VAT Amount

### Step 4: Generate QR for Existing Invoices

If you have existing invoices without QR codes:

1. Click **"Batch Generate QR Codes"** button
2. The system will process up to 50 invoices at a time
3. Check statistics to see progress
4. Repeat if you have more than 50 invoices

### Step 5: Verify on Invoice

1. Create a new invoice or open an existing one
2. View the invoice PDF
3. QR code should appear in the configured position
4. Scan with a QR reader to verify data

---

## 🔍 Verification Checklist

After installation, verify:

- [ ] Module appears in **Setup → Modules** as "Active"
- [ ] Menu item **Setup → ZATCA Invoice QR** is visible
- [ ] Settings page loads without errors
- [ ] Test QR generation works successfully
- [ ] QR code appears on invoice PDF
- [ ] QR code can be scanned with mobile app
- [ ] Decoded data matches invoice information
- [ ] Statistics show correct counts

---

## 🐛 Troubleshooting

### Module Won't Install

**Problem**: Installation fails or gets stuck

**Solutions**:
1. Check PHP error logs: `/path/to/perfex/application/logs/`
2. Verify file permissions:
   ```bash
   chmod -R 755 modules/zatca_invoice_qr
   ```
3. Ensure database user has CREATE TABLE privileges
4. Try manual database installation (see Method 3 above)

### Database Tables Not Created

**Problem**: Tables `tblzatca_*` don't exist

**Check**:
```sql
SHOW TABLES LIKE 'tblzatca%';
```

**Solution**:
1. Run manual SQL installation (see Method 3)
2. Or deactivate and reactivate the module
3. Check database user permissions

### Settings Page Shows Errors

**Problem**: 500 error or blank page on settings

**Solutions**:
1. Enable PHP error display:
   ```php
   // In application/config/config.php
   $config['debug'] = 1;
   ```
2. Check error logs
3. Verify all required files uploaded correctly
4. Clear Perfex cache: **Setup → Clear Cache**

### QR Code Not Appearing on PDF

**Problem**: Invoice PDF has no QR code

**Solutions**:
1. Ensure module is **enabled** in settings
2. Check seller name and VAT number are filled
3. Verify invoice was saved/updated after enabling module
4. Check GD extension is installed:
   ```bash
   php -m | grep -i gd
   ```
5. Manually regenerate QR for the invoice

### Permission Denied Errors

**Problem**: Cannot write to directories

**Solutions**:
```bash
# Create temp directory
mkdir -p /path/to/perfex/uploads/temp/zatca_qr
chmod 755 /path/to/perfex/uploads/temp/zatca_qr

# Fix phpqrcode cache
chmod 755 /path/to/perfex/modules/zatca_invoice_qr/libraries/phpqrcode/cache

# Ensure web server user owns files
chown -R www-data:www-data /path/to/perfex/modules/zatca_invoice_qr
```

### VAT Number Validation Fails

**Problem**: "Invalid VAT number" error

**Solution**:
- Ensure exactly 15 digits
- Remove spaces, dashes, or special characters
- Valid format: `310122393500003`
- Don't include country code

---

## 🔄 Updating the Module

### Via Admin Panel

1. Deactivate the current module (data is preserved)
2. Delete the old module
3. Upload and install the new version
4. Activate the module
5. Verify settings are intact

### Via Manual Upload

1. Backup the current module:
   ```bash
   cp -r modules/zatca_invoice_qr modules/zatca_invoice_qr.backup
   ```
2. Replace files with new version
3. Clear Perfex cache
4. No database changes needed (unless mentioned in release notes)

---

## 🗑️ Uninstallation

### To Remove Module (Keep Data)

1. Go to **Setup → Modules**
2. Find "ZATCA Invoice QR"
3. Click **"Deactivate"**
4. Data remains in database for future use

### To Completely Remove (Delete Everything)

1. Deactivate the module
2. Access database via phpMyAdmin or MySQL:
   ```sql
   DROP TABLE IF EXISTS tblzatca_settings;
   DROP TABLE IF EXISTS tblzatca_invoice_qr;
   DROP TABLE IF EXISTS tblzatca_certificates;
   ```
3. Delete module folder:
   ```bash
   rm -rf /path/to/perfex/modules/zatca_invoice_qr
   ```

---

## 📞 Getting Help

If you encounter issues during installation:

1. **Check Documentation**: Review README.md and this guide
2. **Search Issues**: [GitHub Issues](https://github.com/afaqintegrated-lab/zatca-invoice-qr/issues)
3. **Create Issue**: Provide:
   - Perfex CRM version
   - PHP version
   - Error messages
   - Steps to reproduce
4. **Contact Support**: support@afaqintegrated-lab.com

---

## ✅ Next Steps

After successful installation:

1. ✅ Configure your settings
2. ✅ Test QR generation
3. ✅ Generate QR for existing invoices
4. ✅ Create a test invoice and verify
5. ✅ Train your team on the new feature
6. ✅ Monitor statistics regularly

---

**Installation Complete! 🎉**

Your ZATCA Invoice QR module is now ready to use.
