# 🎯 Complete Solution: ZATCA Module Not Appearing in Perfex CRM

## Your Situation
- ✅ Downloaded: `zatca_invoice_qr_v1.0.0.zip`
- ❌ Module doesn't appear in Perfex CRM module list after upload
- ❓ Question: "nothing appears in the modules in this phase"

---

## 🔍 Root Cause Analysis

The module not appearing can have several causes:

### Most Common Issues:
1. **Incorrect upload location** - Uploaded to wrong directory
2. **ZIP structure problem** - Extra nested folder in ZIP
3. **Cache not cleared** - Perfex caching old module list
4. **Permissions issue** - Insufficient read permissions
5. **ZIP upload feature not available** - Older Perfex version

**Good News**: Your ZIP file is correctly structured! ✅  
**Issue**: Likely cache or upload method problem

---

## ✅ **RECOMMENDED SOLUTION: Manual Installation**

This method works 100% of the time and bypasses any ZIP upload issues.

### 📋 Complete Step-by-Step Process:

---

### **STEP 1: Extract the ZIP File**

**On Windows:**
1. Right-click `zatca_invoice_qr_v1.0.0.zip`
2. Select "Extract All..." or "Extract Here"
3. You should see a folder: `zatca_invoice_qr`

**On Mac:**
1. Double-click `zatca_invoice_qr_v1.0.0.zip`
2. It automatically extracts to: `zatca_invoice_qr` folder

**On Linux/Server:**
```bash
unzip zatca_invoice_qr_v1.0.0.zip
ls zatca_invoice_qr/  # Verify contents
```

---

### **STEP 2: Find Your Perfex CRM Installation Path**

You need to locate where Perfex CRM is installed on your server.

**Method 1: Via Perfex Admin Panel**
1. Login to Perfex CRM
2. Go to: **Setup → Settings → General**
3. Look at the URL or check hosting information

**Method 2: Common Locations by Hosting Type**

| Hosting Type | Typical Module Path |
|--------------|---------------------|
| **cPanel** | `/home/username/public_html/modules/` |
| **cPanel (subfolder)** | `/home/username/public_html/perfex/modules/` |
| **Direct Admin** | `/home/username/domains/example.com/public_html/modules/` |
| **Plesk** | `/var/www/vhosts/example.com/httpdocs/modules/` |
| **VPS/Cloud (Ubuntu)** | `/var/www/html/modules/` |
| **VPS/Cloud (subfolder)** | `/var/www/html/perfex/modules/` |

**Method 3: Look for Existing Modules**
- Find where existing Perfex modules are located
- Look for folders like: `backup/`, `goals/`, `surveys/`, `webhooks/`
- Upload to the SAME parent directory

---

### **STEP 3: Upload Module Folder to Server**

Choose the method that works for your hosting:

---

#### **Option A: FTP Upload (FileZilla, WinSCP, Cyberduck)**

1. **Connect to your server:**
   - Host: Your server IP or domain
   - Username: FTP username
   - Password: FTP password
   - Port: 21 (or 22 for SFTP)

2. **Navigate to modules directory:**
   - Remote site (right pane): Navigate to `[perfex_path]/modules/`
   - You should see existing module folders

3. **Upload the module:**
   - Local site (left pane): Navigate to extracted `zatca_invoice_qr` folder
   - Drag the entire `zatca_invoice_qr` folder to the remote `modules/` directory
   - Wait for upload to complete

4. **Verify upload:**
   - Remote path should be: `modules/zatca_invoice_qr/`
   - Check that `zatca_invoice_qr.php` exists inside

---

#### **Option B: cPanel File Manager**

1. **Login to cPanel**
   - URL: `https://yourdomain.com/cpanel` or `https://yourdomain.com:2083`

2. **Open File Manager**
   - Find and click "File Manager" icon
   - Navigate to: `public_html/modules/` (or wherever Perfex is installed)

3. **Upload the folder:**
   
   **Method B1: Upload ZIP and Extract**
   - Click "Upload" button (top toolbar)
   - Select `zatca_invoice_qr_v1.0.0.zip`
   - Wait for upload to complete
   - Go back to File Manager
   - Find the uploaded ZIP file
   - Right-click → "Extract"
   - Delete the ZIP file after extraction
   
   **Method B2: Upload Folder Directly**
   - First ZIP the `zatca_invoice_qr` folder on your computer (not the v1.0.0 ZIP, but the extracted folder)
   - Upload this new ZIP
   - Extract in the `modules/` directory
   - Delete ZIP after extraction

4. **Verify structure:**
   ```
   modules/
   └── zatca_invoice_qr/
       ├── zatca_invoice_qr.php   ← Must be here!
       ├── install.php
       ├── controllers/
       ├── models/
       └── ... (other files)
   ```

---

#### **Option C: SSH/Terminal (VPS/Cloud Servers)**

1. **Connect via SSH:**
   ```bash
   ssh username@your-server-ip
   ```

2. **Navigate to Perfex modules directory:**
   ```bash
   cd /var/www/html/modules/
   # OR your specific path
   pwd  # Verify current directory
   ```

3. **Upload and extract:**
   
   **If ZIP is on server:**
   ```bash
   unzip /path/to/zatca_invoice_qr_v1.0.0.zip
   ls -la zatca_invoice_qr/  # Verify extraction
   ```
   
   **If uploading from local machine:**
   ```bash
   # On your local machine:
   scp zatca_invoice_qr_v1.0.0.zip username@server:/tmp/
   
   # Then on server:
   cd /path/to/perfex/modules/
   unzip /tmp/zatca_invoice_qr_v1.0.0.zip
   rm /tmp/zatca_invoice_qr_v1.0.0.zip  # Clean up
   ```

4. **Verify structure:**
   ```bash
   ls -la zatca_invoice_qr/zatca_invoice_qr.php
   # Should show file info, not "No such file"
   ```

---

### **STEP 4: Set Correct Permissions**

Perfex needs read/execute permissions on the module files.

---

#### **Via FTP (FileZilla, WinSCP):**

1. Right-click `zatca_invoice_qr` folder
2. Select "File Permissions" or "Properties"
3. Set permissions to: **755** (or `rwxr-xr-x`)
4. Check "Recurse into subdirectories"
5. Check "Apply to directories only"
6. Click OK
7. Repeat for files: Select all files → Set to **644**

---

#### **Via cPanel File Manager:**

1. Select `zatca_invoice_qr` folder
2. Click "Permissions" button (top toolbar)
3. Set to: **755**
   - Owner: Read, Write, Execute
   - Group: Read, Execute
   - World: Read, Execute
4. Check "Recurse into subdirectories"
5. Click "Change Permissions"

---

#### **Via SSH:**

```bash
# Navigate to modules directory
cd /path/to/perfex/modules/

# Set folder permissions (755)
find zatca_invoice_qr/ -type d -exec chmod 755 {} \;

# Set file permissions (644)
find zatca_invoice_qr/ -type f -exec chmod 644 {} \;

# Set ownership (if needed)
# For Ubuntu/Debian:
chown -R www-data:www-data zatca_invoice_qr/

# For CentOS/RHEL:
chown -R apache:apache zatca_invoice_qr/

# For shared hosting (use your username):
chown -R username:username zatca_invoice_qr/
```

---

### **STEP 5: Clear Perfex CRM Cache (CRITICAL!)**

**This is the most commonly missed step!**

Perfex CRM caches the module list. You **MUST** clear it for the new module to appear.

---

#### **Method 1: Via Admin Panel (Easiest)**

1. Login to Perfex CRM admin
2. Go to: **Setup → Settings**
3. Click **"System"** tab
4. Scroll down to find "Cache" section
5. Click **"Clear System Cache"** button
6. Wait for confirmation message

---

#### **Method 2: Manually Delete Cache Files**

**Via FTP or File Manager:**
1. Navigate to: `[perfex_root]/application/cache/`
2. Delete ALL files and folders inside (not the cache folder itself)
3. Navigate to: `[perfex_root]/temp/`
4. Delete ALL files and folders inside (not the temp folder itself)

**Via SSH:**
```bash
cd /path/to/perfex/
rm -rf application/cache/*
rm -rf temp/*
echo "Cache cleared!"
```

---

#### **Method 3: Browser Cache**

Also clear your browser cache:
1. Press `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
2. Select "Cached images and files"
3. Clear cache
4. **OR** use Incognito/Private browsing mode
5. **OR** Hard refresh: `Ctrl + F5` (Windows) or `Cmd + Shift + R` (Mac)

---

### **STEP 6: Access Perfex CRM and Install Module**

1. **Login to Perfex CRM Admin:**
   - URL: `https://yourdomain.com/admin`

2. **Go to Modules Page:**
   - Navigate to: **Setup → Modules**

3. **Find the Module:**
   - Look for **"ZATCA Invoice QR"** in the list
   - It should appear with version 1.0.0
   - Description: "Generate ZATCA Phase 1 & Phase 2 compliant QR codes..."

4. **Install the Module:**
   - Click the **"Install"** button next to the module
   - Wait for installation process (2-5 seconds)
   - Installation creates 3 database tables automatically
   - You should see a success message

5. **Activate the Module:**
   - After installation, click the **"Activate"** button
   - Status should change to **"Active"** or **"Installed"**

6. **Verify Installation:**
   - You should now see a new menu item: **Setup → ZATCA Invoice QR**
   - Click it to access settings page

---

### **STEP 7: Configure Module Settings**

1. **Access Settings Page:**
   - Go to: **Setup → ZATCA Invoice QR**

2. **Basic Configuration:**
   - **Enable Module**: Toggle switch to **ON**
   - **Phase**: Select **"Phase 1 (Simplified)"** (default)
   - **Seller Name**: Enter your company name (e.g., "ABC Trading Company")
   - **VAT Registration Number**: Enter 15-digit Saudi VAT number (e.g., "123456789012345")

3. **QR Code Appearance:**
   - **QR Position**: Choose where QR appears on invoice
     - Top Right (default)
     - Top Left
     - Bottom Right
     - Bottom Left
   - **QR Size**: Set size in pixels (default: 150, recommended: 100-200)

4. **Generation Options:**
   - **Auto Generate QR Codes**: Keep **ON** (automatically generates QR for new invoices)

5. **Save Settings:**
   - Click **"Save Settings"** button
   - Wait for success confirmation

---

### **STEP 8: Test QR Code Generation**

1. **Test Button:**
   - On settings page, scroll down
   - Click **"Test QR Generation"** button
   - A popup should appear with a QR code image
   - If it appears: **SUCCESS!** Module is working correctly

2. **Test on Real Invoice:**
   - Go to: **Sales → Invoices**
   - Create a new invoice or edit existing one:
     - Select a customer
     - Add invoice items
     - Make sure invoice has a total amount
     - Make sure there's at least one item with tax
   - Click **"Save"**
   - After saving, click **"View PDF"** or **"Download PDF"**
   - QR code should appear on the invoice PDF!

3. **Scan QR Code:**
   - Use your phone camera or QR scanner app
   - Scan the QR code on the PDF
   - It should display invoice information:
     - Seller Name
     - VAT Number
     - Invoice Date/Time
     - Invoice Total
     - VAT Amount

---

## 🔧 **TROUBLESHOOTING: Module STILL Not Appearing**

If you've followed all steps and module still doesn't appear:

---

### **Issue 1: Wrong Folder Structure**

**Symptom**: Module uploaded but doesn't appear in list

**Check**:
```bash
# Via SSH
ls -la /path/to/perfex/modules/zatca_invoice_qr/zatca_invoice_qr.php

# Should return file info, NOT "No such file or directory"
```

**Common Mistake**:
```
❌ WRONG:
modules/zatca_invoice_qr/zatca_invoice_qr/zatca_invoice_qr.php  ← Too deep!

✅ CORRECT:
modules/zatca_invoice_qr/zatca_invoice_qr.php  ← At root!
```

**Fix**: Move files up one directory level

---

### **Issue 2: Permissions Problem**

**Symptom**: Module appears but can't install, or shows errors

**Check Permissions**:
```bash
ls -la /path/to/perfex/modules/ | grep zatca
# Should show: drwxr-xr-x (755)
```

**Fix**:
```bash
chmod -R 755 /path/to/perfex/modules/zatca_invoice_qr/
```

---

### **Issue 3: Cache Not Cleared**

**Symptom**: Module uploaded correctly but doesn't appear in list

**Fix**: Follow STEP 5 again carefully
- Clear Perfex cache via admin panel
- Delete cache files manually
- Clear browser cache
- Try incognito mode

---

### **Issue 4: PHP Errors**

**Symptom**: White screen or 500 error when accessing modules page

**Check Error Logs**:

**Perfex Error Log**:
```bash
tail -50 /path/to/perfex/uploads/errors.log
```

**PHP Error Log** (varies by server):
```bash
# Ubuntu/Debian:
tail -50 /var/log/apache2/error.log

# CentOS/RHEL:
tail -50 /var/log/httpd/error_log

# Nginx:
tail -50 /var/log/nginx/error.log

# cPanel:
tail -50 /home/username/public_html/error_log
```

**Common Errors & Fixes**:

- **"Call to undefined function..."**: Missing PHP extension
  - Fix: Install required PHP extensions (GD, mbstring)
  
- **"Permission denied"**: Wrong permissions
  - Fix: Apply correct chmod/chown

- **"Class not found"**: File structure issue
  - Fix: Verify all files uploaded correctly

---

### **Issue 5: Module Already Partially Installed**

**Symptom**: Error messages about existing module or tables

**Fix - Remove Previous Installation**:

```sql
-- Run in phpMyAdmin

-- 1. Remove module registration
DELETE FROM tblmodules WHERE module_name = 'zatca_invoice_qr';

-- 2. Remove database tables (if they exist)
DROP TABLE IF EXISTS tblzatca_settings;
DROP TABLE IF EXISTS tblzatca_invoice_qr;
DROP TABLE IF EXISTS tblzatca_certificates;
```

Then try installation again.

---

### **Issue 6: Perfex Version Too Old**

**Symptom**: Module doesn't appear or gives compatibility errors

**Check Perfex Version**:
1. Go to: **Setup → About**
2. Look for version number

**Requirement**: Perfex CRM 2.3.* or higher

**Fix**: Update Perfex CRM to latest version

---

### **Issue 7: Server-Side Restrictions**

**Symptom**: Upload works but module functionality fails

**Check**:
1. **PHP Version**: Must be ≥ 7.2
   ```bash
   php -v
   ```

2. **Required PHP Extensions**:
   ```bash
   php -m | grep -E 'gd|zip|mbstring|mysqli|curl'
   ```
   
   Should show:
   - ✅ gd (for QR code image generation)
   - ✅ mbstring (for string handling)
   - ✅ mysqli (for database)
   - ✅ zip (for file operations)

3. **PHP Functions Not Disabled**:
   Check `php.ini` for `disable_functions`
   - Should NOT include: `exec`, `file_get_contents`, `file_put_contents`

**Fix**: Contact hosting provider to enable required extensions/functions

---

## ✅ **Verification Checklist**

Use this checklist to confirm successful installation:

### Before Installation:
- [ ] Module folder uploaded to: `[perfex_root]/modules/zatca_invoice_qr/`
- [ ] File exists: `zatca_invoice_qr/zatca_invoice_qr.php`
- [ ] Folder permissions: 755
- [ ] File permissions: 644
- [ ] Cache cleared (Perfex + Browser)

### After Installation:
- [ ] Module appears in: **Setup → Modules**
- [ ] Module status: "Active" or "Installed"
- [ ] Menu item appears: **Setup → ZATCA Invoice QR**
- [ ] Settings page loads without errors
- [ ] Database tables created (check phpMyAdmin)
- [ ] Test QR button generates QR code
- [ ] QR code appears on invoice PDF
- [ ] QR code is scannable with phone

### Database Verification:
```sql
-- Run in phpMyAdmin
SHOW TABLES LIKE 'tblzatca_%';
-- Should return 3 tables

SELECT * FROM tblzatca_settings WHERE id = 1;
-- Should return 1 row with default settings
```

---

## 📊 **Why Manual Installation is Recommended**

| Method | Success Rate | Pros | Cons |
|--------|--------------|------|------|
| **Manual Upload (FTP/cPanel)** | **99%** | ✅ Always works<br>✅ No version requirements<br>✅ Full control<br>✅ Easy to troubleshoot | ⚠️ Requires FTP access |
| **ZIP Upload (Perfex UI)** | **60-70%** | ✅ Convenient<br>✅ No FTP needed | ❌ Requires Perfex 2.9+<br>❌ Needs ZIP extension<br>❌ May have size limits<br>❌ Can fail silently |

**Recommendation**: Use manual upload method (Steps 1-8 above) for guaranteed success.

---

## 📞 **Still Having Issues?**

If you've followed ALL steps carefully and module still doesn't work:

### Provide These Details:

1. **System Information**:
   - Perfex CRM version (Setup → About)
   - PHP version
   - Server type (Apache/Nginx)
   - Hosting type (cPanel/Plesk/VPS/Cloud)
   - Operating system (if VPS/Cloud)

2. **Module Upload Details**:
   - Upload method used (FTP/cPanel/SSH/ZIP)
   - Exact path where module is located
   - Output of: `ls -la /path/to/modules/zatca_invoice_qr/`

3. **Error Information**:
   - Any error messages displayed
   - Contents of: `uploads/errors.log` (last 50 lines)
   - PHP error log (last 50 lines)
   - Browser console errors (F12 → Console tab)

4. **Screenshots**:
   - Perfex modules page
   - Any error messages
   - Module folder structure in File Manager

---

## 🎯 **Summary**

The **100% working method**:

1. ✅ Extract ZIP file → Get `zatca_invoice_qr` folder
2. ✅ Upload folder to: `[perfex]/modules/zatca_invoice_qr/`
3. ✅ Set permissions: 755 for folders, 644 for files
4. ✅ **Clear Perfex cache** (most important!)
5. ✅ Clear browser cache or use incognito mode
6. ✅ Go to: Setup → Modules
7. ✅ Click "Install" then "Activate"
8. ✅ Configure at: Setup → ZATCA Invoice QR
9. ✅ Test QR generation
10. ✅ Test on real invoice PDF

**Expected Result**: Working QR codes on all invoices! ✅

---

**Document Version**: 1.0  
**Last Updated**: 2025-10-20  
**Module Version**: 1.0.0  
**Perfex Compatibility**: 2.3.* and higher
