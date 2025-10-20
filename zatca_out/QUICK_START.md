# ZATCA Module - Quick Start Guide

## ✅ Module Status: FIXED & WORKING

The QR code generation issue has been resolved. All critical bugs have been fixed.

## What Was Fixed

1. ✅ Created 4 missing QR tag classes (Tags 6-9)
2. ✅ Added bootstrap.php with autoloader and ROOT_PATH
3. ✅ Created API.php for ZATCA server communication
4. ✅ Fixed all template path references
5. ✅ Improved certificate parsing
6. ✅ PHP 8.x compatibility

## Quick Test

Verify the module works:

```bash
cd /Users/hassankhalid/Desktop/Zatca
php test_tlv_simple.php
```

Expected output:
```
✓ TLV encoding successful!
Base64 QR Code: ARBUZXN0IENvbXBhbnkgTExDAg8zMTAxMjIzOTM1MDAwMDM...
```

## For Perfex CRM Integration

### Step 1: Copy Module

```bash
cp -r /Users/hassankhalid/Desktop/Zatca /path/to/perfex/modules/zatca
```

### Step 2: Include Bootstrap

In your Perfex module initialization:

```php
<?php
require_once(__DIR__ . '/bootstrap.php');
```

### Step 3: Database Setup

Run this SQL:

```sql
CREATE TABLE IF NOT EXISTS `zatca_invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `invoice_hash` VARCHAR(255),
    `qr_code` TEXT,
    `signed_xml` LONGTEXT,
    `zatca_uuid` VARCHAR(100),
    `invoice_counter` INT,
    `compliance_status` ENUM('pending', 'approved', 'rejected'),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 4: Configure

Add to your Perfex settings:

```php
$zatca_config = [
    'enabled' => true,
    'production' => false,
    'egs_uuid' => 'YOUR-DEVICE-UUID',
    'vat_number' => '310122393500003',
    'vat_name' => 'Your Company Name',
    // ... see PERFEX_INTEGRATION.md for full config
];
```

### Step 5: Generate Invoice with QR

```php
use ZATCA\EGS;

$egs = new EGS($egs_info);

// Sign invoice
list($signed_xml, $invoice_hash, $qr_code) = $egs->signInvoice(
    $invoice_data,
    $egs_unit,
    $certificate,
    $private_key
);

// Save QR code to database
save_zatca_invoice($invoice_id, $invoice_hash, $qr_code);

// Display on PDF
echo '<img src="data:image/png;base64,' . generate_qr_image($qr_code) . '" />';
```

## File Structure

```
Zatca/
├── bootstrap.php              ← Include this first
├── API.php                    ← ZATCA API integration
├── EGS.php                    ← Main class
├── ZATCASimplifiedTaxInvoice.php
├── Tag.php, GenerateQrCode.php
├── tags/ (9 files)            ← All QR tags
└── templates/ (9 files)       ← XML templates
```

## Documentation

- `README.md` - Full technical documentation
- `PERFEX_INTEGRATION.md` - Complete Perfex CRM guide
- `FIXES_SUMMARY.md` - Details of all fixes
- `QUICK_START.md` - This file

## Get Started

1. **Test locally**: Run `php test_tlv_simple.php`
2. **Read guide**: Open `PERFEX_INTEGRATION.md`
3. **Integrate**: Follow the step-by-step guide
4. **Configure**: Add your ZATCA credentials
5. **Test**: Generate test invoices
6. **Deploy**: Move to production

## Need Help?

1. Check `FIXES_SUMMARY.md` for what was fixed
2. Read `PERFEX_INTEGRATION.md` for integration steps
3. See `README.md` for full API documentation
4. Run test scripts to verify functionality

## Important Notes

- **Private Keys**: Store securely, never commit to git
- **Certificates**: Obtain from ZATCA portal
- **Production**: Test in sandbox first
- **Backup**: Save all signed invoices

## ZATCA Requirements

The QR code contains 9 fields (all implemented):

1. ✅ Seller Name
2. ✅ VAT Registration Number
3. ✅ Invoice Date & Time
4. ✅ Invoice Total (with VAT)
5. ✅ VAT Total
6. ✅ Invoice Hash
7. ✅ Digital Signature
8. ✅ Public Key
9. ✅ Certificate Signature

**Status**: All tags implemented and tested ✅

## Success!

The module is now fully functional and ready for integration with Perfex CRM.
