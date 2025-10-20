# ZATCA Invoice QR Module - Testing Guide

Complete testing procedure for the ZATCA Invoice QR module.

## 📋 Pre-Testing Checklist

Before starting tests, ensure you have:

- [ ] Perfex CRM 2.3.0+ installed
- [ ] PHP 7.4+ with GD and mbstring extensions
- [ ] Admin access to Perfex CRM
- [ ] Test VAT number (15 digits)
- [ ] Mobile device with QR scanner app
- [ ] Browser dev tools knowledge (optional)

---

## 🧪 TEST PLAN

### PHASE 1: Module Installation Tests

#### Test 1.1: File Upload
**Objective**: Verify module can be uploaded successfully

**Steps**:
1. Login to Perfex CRM admin
2. Navigate to **Setup → Modules**
3. Click **"Upload New Module"**
4. Select `zatca_invoice_qr_v1.0.0.zip`
5. Click **Upload**

**Expected Result**:
- ✅ Upload completes without errors
- ✅ Module appears in modules list
- ✅ Status shows "Not Installed"

**Pass/Fail**: ___________

---

#### Test 1.2: Module Installation
**Objective**: Verify database tables are created

**Steps**:
1. Click **"Install"** button next to module
2. Wait for installation to complete
3. Check for success message

**Expected Result**:
- ✅ Installation completes successfully
- ✅ No error messages
- ✅ Status changes to "Installed" but "Inactive"

**Database Verification**:
```sql
SHOW TABLES LIKE '%zatca%';
```

Should show 3 tables:
- `tblzatca_settings`
- `tblzatca_invoice_qr`
- `tblzatca_certificates`

**Pass/Fail**: ___________

---

#### Test 1.3: Module Activation
**Objective**: Verify module activates and menu appears

**Steps**:
1. Click **"Activate"** button
2. Wait for activation to complete
3. Check admin menu

**Expected Result**:
- ✅ Activation successful
- ✅ New menu item appears: **Setup → ZATCA Invoice QR**
- ✅ Module status shows "Active"

**Pass/Fail**: ___________

---

### PHASE 2: Configuration Tests

#### Test 2.1: Access Settings Page
**Objective**: Verify settings page loads correctly

**Steps**:
1. Click **Setup → ZATCA Invoice QR**
2. Settings page should load

**Expected Result**:
- ✅ Page loads without errors
- ✅ Form displays all fields:
  - Enable Module (checkbox)
  - Phase selection (Phase 1/Phase 2)
  - Environment (Sandbox/Production)
  - Seller Name (text field)
  - VAT Number (text field)
  - Company Address (textarea)
  - QR Position (dropdown)
  - QR Size (number input)
  - Auto-generate (checkbox)
- ✅ Statistics section shows:
  - Total Invoices
  - Invoices with QR
  - Invoices without QR
  - Success Rate
- ✅ Action buttons visible:
  - Save Settings
  - Test QR Generation
  - Batch Generate QR Codes

**Pass/Fail**: ___________

---

#### Test 2.2: Configure Basic Settings
**Objective**: Test saving basic configuration

**Steps**:
1. Fill in required fields:
   - ✅ Enable Module
   - Phase: Phase 1
   - Environment: Sandbox
   - Seller Name: `مؤسسة آفاق المتكاملة لتقنية المعلومات`
   - VAT Number: `310122393500003`
   - QR Position: Top Right
   - QR Size: `150`
   - ✅ Auto-generate
2. Click **Save Settings**

**Expected Result**:
- ✅ Success message appears: "Settings updated"
- ✅ Page reloads with saved values
- ✅ Configuration status shows "Configured"

**Pass/Fail**: ___________

---

#### Test 2.3: VAT Number Validation
**Objective**: Test input validation

**Steps**:
1. Enter invalid VAT number: `12345`
2. Click **Save Settings**

**Expected Result**:
- ✅ Error message: "Invalid VAT number format. Must be 15 digits."
- ✅ Settings not saved

**Steps (Valid VAT)**:
1. Enter valid VAT: `310122393500003`
2. Click **Save Settings**

**Expected Result**:
- ✅ Settings saved successfully

**Pass/Fail**: ___________

---

### PHASE 3: QR Generation Tests

#### Test 3.1: Test QR Generation
**Objective**: Verify test QR generation works

**Steps**:
1. Click **"Test QR Generation"** button
2. Modal should appear

**Expected Result**:
- ✅ Modal opens with title "Test QR Generation"
- ✅ Success message displayed
- ✅ QR code image displayed (150x150px)
- ✅ Decoded data table shows 5 fields:
  - Tag 1: Seller Name (your company name)
  - Tag 2: VAT Number (15 digits)
  - Tag 3: Date/Time (ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ)
  - Tag 4: Invoice Total (1150.00)
  - Tag 5: VAT Amount (150.00)
- ✅ Size validation message: "QR code size is valid (X bytes)"
- ✅ Raw Base64 data visible in collapsible section

**Visual Check**:
- QR code should be scannable
- Data should match test values

**Pass/Fail**: ___________

---

#### Test 3.2: Scan Test QR Code
**Objective**: Verify QR code is scannable and readable

**Steps**:
1. Use mobile QR scanner app
2. Scan the QR code from test modal
3. View decoded data

**Expected Result**:
- ✅ QR code scans successfully
- ✅ App displays Base64 string
- ✅ String starts with expected characters

**Note**: Standard QR apps show Base64, not decoded fields. 
To see decoded fields, use ZATCA-specific scanner or online TLV decoder.

**Pass/Fail**: ___________

---

#### Test 3.3: Create New Invoice with QR
**Objective**: Test automatic QR generation

**Steps**:
1. Go to **Sales → Invoices → Create Invoice**
2. Fill invoice details:
   - Customer: Select any
   - Items: Add at least one item with VAT
   - Total should include VAT
3. Save invoice
4. View the invoice

**Expected Result**:
- ✅ Invoice saved successfully
- ✅ Check database:
  ```sql
  SELECT * FROM tblzatca_invoice_qr WHERE invoice_id = [YOUR_INVOICE_ID];
  ```
- ✅ Record exists with:
  - invoice_id: Your invoice ID
  - qr_data: Base64 string
  - qr_base64: Data URI (data:image/png;base64,...)
  - status: 'generated'
  - generation_date: Current date/time

**Pass/Fail**: ___________

---

#### Test 3.4: View Invoice PDF with QR
**Objective**: Verify QR appears on PDF

**Steps**:
1. Open the invoice you created
2. Click **"Download PDF"** or **"View PDF"**
3. Check PDF for QR code

**Expected Result**:
- ✅ PDF opens successfully
- ✅ QR code visible in top-right corner (or configured position)
- ✅ QR code size approximately 150px
- ✅ QR code is clear and scannable

**Visual Check**:
- QR should not overlap other content
- QR should be properly aligned
- QR should be high quality (not blurry)

**Pass/Fail**: ___________

---

#### Test 3.5: Scan Invoice QR Code
**Objective**: Verify invoice QR contains correct data

**Steps**:
1. Scan QR code from invoice PDF
2. Check decoded data matches invoice

**Expected Data (from scanner)**:
- Seller Name: Your company name
- VAT Number: Your VAT number
- Invoice Date/Time: Invoice date in ISO format
- Invoice Total: Total from invoice (with VAT)
- VAT Amount: Calculated VAT amount

**Pass/Fail**: ___________

---

### PHASE 4: Batch Operations Tests

#### Test 4.1: Batch Generate for Existing Invoices
**Objective**: Test bulk QR generation

**Prerequisites**:
- Have at least 3-5 existing invoices without QR codes

**Steps**:
1. Go to **Setup → ZATCA Invoice QR**
2. Check "Invoices without QR" statistic (should be > 0)
3. Click **"Batch Generate QR Codes"**
4. Confirm action
5. Wait for completion

**Expected Result**:
- ✅ Success message shows number generated
  Example: "5 QR codes generated successfully, 0 failed"
- ✅ Statistics update:
  - "Invoices with QR" increases
  - "Invoices without QR" decreases
  - "Success Rate" recalculated
- ✅ No error messages

**Database Verification**:
```sql
SELECT COUNT(*) as total_qr FROM tblzatca_invoice_qr WHERE status = 'generated';
```

**Pass/Fail**: ___________

---

#### Test 4.2: Regenerate QR for Invoice
**Objective**: Test QR regeneration

**Steps**:
1. Open invoice with existing QR
2. Modify invoice (change total, add item, etc.)
3. Save invoice
4. Check if QR was regenerated

**Expected Result**:
- ✅ New QR generated with updated data
- ✅ Database record updated (check generation_date)
- ✅ PDF shows updated QR code
- ✅ Scanned data matches new invoice values

**Pass/Fail**: ___________

---

### PHASE 5: Edge Cases and Error Handling

#### Test 5.1: Disable Module
**Objective**: Test module disable functionality

**Steps**:
1. Uncheck "Enable Module"
2. Save settings
3. Create new invoice

**Expected Result**:
- ✅ Settings saved
- ✅ No QR generated for new invoice
- ✅ Existing QR codes remain in database
- ✅ Statistics still visible

**Pass/Fail**: ___________

---

#### Test 5.2: Invalid Configuration
**Objective**: Test error handling for bad config

**Steps**:
1. Leave Seller Name empty
2. Try to generate test QR

**Expected Result**:
- ✅ Error message: "Module not properly configured: seller_name"
- ✅ No QR generated

**Pass/Fail**: ___________

---

#### Test 5.3: Large Invoice Values
**Objective**: Test with large numbers

**Test Data**:
- Invoice Total: 999,999.99
- VAT Amount: 149,999.99

**Steps**:
1. Create invoice with large values
2. Generate QR
3. Verify encoding

**Expected Result**:
- ✅ QR generates successfully
- ✅ Values formatted correctly (2 decimals)
- ✅ QR size remains valid (< 500 bytes)

**Pass/Fail**: ___________

---

#### Test 5.4: Special Characters in Company Name
**Objective**: Test Arabic and special character handling

**Test Data**:
```
Seller Name: شركة الاختبار للتجارة & التصدير (م.م.ح)
```

**Steps**:
1. Update seller name with Arabic and special chars
2. Generate test QR
3. Scan and verify

**Expected Result**:
- ✅ Characters saved correctly
- ✅ QR generates successfully
- ✅ Scanned data shows correct characters

**Pass/Fail**: ___________

---

### PHASE 6: Performance and Reliability

#### Test 6.1: Multiple Simultaneous Users
**Objective**: Test concurrent QR generation

**Steps**:
1. Have 2-3 users create invoices simultaneously
2. Check QR generation for all

**Expected Result**:
- ✅ All QR codes generated successfully
- ✅ No database locks or conflicts
- ✅ Each invoice has unique QR

**Pass/Fail**: ___________

---

#### Test 6.2: Batch Generate Large Number
**Objective**: Test performance with many invoices

**Prerequisites**:
- Have 50+ invoices without QR codes

**Steps**:
1. Click **Batch Generate**
2. Monitor execution time
3. Check results

**Expected Result**:
- ✅ Completes within reasonable time (< 2 minutes)
- ✅ All invoices processed
- ✅ No timeouts or memory errors
- ✅ Success rate > 95%

**Pass/Fail**: ___________

---

### PHASE 7: UI/UX Tests

#### Test 7.1: Responsive Design
**Objective**: Test on different screen sizes

**Devices to Test**:
- Desktop (1920x1080)
- Tablet (768x1024)
- Mobile (375x667)

**Expected Result**:
- ✅ Settings form responsive and usable
- ✅ Statistics cards stack properly on mobile
- ✅ Buttons accessible and clickable
- ✅ Modal fits screen

**Pass/Fail**: ___________

---

#### Test 7.2: Multi-language Support
**Objective**: Test English and Arabic

**Steps**:
1. Change Perfex language to Arabic
2. Access ZATCA settings
3. Check translations

**Expected Result**:
- ✅ All labels translated to Arabic
- ✅ RTL layout applied correctly
- ✅ Form inputs work in Arabic
- ✅ Messages display in Arabic

**Pass/Fail**: ___________

---

### PHASE 8: Security Tests

#### Test 8.1: Permission Checks
**Objective**: Verify permissions work

**Steps**:
1. Create non-admin user with no "zatca_invoice_qr" permissions
2. Login as that user
3. Try to access settings

**Expected Result**:
- ✅ Access denied message
- ✅ Settings page not accessible
- ✅ Menu item not visible

**Pass/Fail**: ___________

---

#### Test 8.2: SQL Injection Prevention
**Objective**: Test input sanitization

**Test Inputs**:
```
Seller Name: '; DROP TABLE tblzatca_settings; --
VAT Number: 1' OR '1'='1
```

**Expected Result**:
- ✅ Inputs properly escaped
- ✅ No SQL errors
- ✅ No database damage

**Pass/Fail**: ___________

---

## 📊 TEST RESULTS SUMMARY

### Overall Results

**Total Tests**: 30
**Passed**: _____
**Failed**: _____
**Skipped**: _____
**Success Rate**: _____%

### Critical Failures (if any)

1. Test ID: _____ - Description: _________________
2. Test ID: _____ - Description: _________________
3. Test ID: _____ - Description: _________________

### Recommendations

Based on test results:
- [ ] Ready for production use
- [ ] Needs minor fixes
- [ ] Requires major revisions
- [ ] Not ready for deployment

### Notes

_________________________________________________
_________________________________________________
_________________________________________________

---

## 🐛 TROUBLESHOOTING COMMON ISSUES

### Issue 1: QR Not Appearing on PDF

**Possible Causes**:
1. Module not enabled
2. Auto-generate disabled
3. Invoice created before module activation
4. PDF generation hooks not working

**Solutions**:
1. Check module enabled in settings
2. Enable auto-generate
3. Regenerate QR manually
4. Check Perfex hooks are working

---

### Issue 2: VAT Number Validation Failing

**Cause**: VAT number not exactly 15 digits

**Solution**:
- Remove spaces, dashes
- Ensure no letters
- Must be: `310122393500003` (15 digits exactly)

---

### Issue 3: Test QR Button Does Nothing

**Possible Causes**:
1. JavaScript not loading
2. AJAX endpoint not accessible
3. Browser console errors

**Solutions**:
1. Check browser console (F12)
2. Verify `/admin/zatca_invoice_qr/zatca_admin/test_qr` is accessible
3. Clear browser cache

---

### Issue 4: Batch Generation Fails

**Possible Causes**:
1. PHP timeout
2. Memory limit
3. Database connection issues

**Solutions**:
1. Increase PHP max_execution_time
2. Increase memory_limit
3. Process in smaller batches (< 50)

---

### Issue 5: Arabic Characters Showing as ???

**Cause**: UTF-8 encoding issue

**Solutions**:
1. Ensure database uses `utf8mb4` charset
2. Check PHP mbstring extension enabled
3. Verify HTML meta charset is UTF-8

---

## ✅ SIGN-OFF

**Tester Name**: _______________________
**Date**: _______________________
**Signature**: _______________________

**Approved for Production**: Yes / No

**Approver Name**: _______________________
**Date**: _______________________
**Signature**: _______________________

---

**END OF TESTING GUIDE**
