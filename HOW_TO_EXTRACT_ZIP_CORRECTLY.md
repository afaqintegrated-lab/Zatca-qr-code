# 🔧 How to Extract ZIP File Correctly

## ❌ **Problem: "The new folder is empty!"**

When you extract the ZIP file, you see an empty folder. This happens due to:
1. Extracting to wrong location
2. Hidden files not showing
3. Extraction method used
4. File permissions issue

---

## ✅ **SOLUTION: Proper Extraction Methods**

### **Method 1: cPanel File Manager (Recommended)**

This is the EASIEST and most reliable method:

#### **Step 1: Delete Old Module (if exists)**
1. Login to **cPanel**
2. Click **"File Manager"**
3. Navigate to: `public_html/erp/modules/`
4. Right-click `zatca_invoice_qr` folder → **Delete**
5. Confirm deletion

#### **Step 2: Upload ZIP File**
1. Still in File Manager at: `public_html/erp/modules/`
2. Click **"Upload"** button (top toolbar)
3. Select: `zatca_invoice_qr_v1.0.1_fixed.zip`
4. Wait for upload to complete (315 KB)
5. Close upload window

#### **Step 3: Extract ZIP in cPanel**
1. Back in File Manager
2. You should see: `zatca_invoice_qr_v1.0.1_fixed.zip`
3. **Right-click** the ZIP file
4. Select **"Extract"**
5. Dialog appears → Click **"Extract Files"** button
6. Wait for extraction to complete
7. Click **"Close"** when done

#### **Step 4: Verify Extraction**
1. You should now see folder: `zatca_invoice_qr/`
2. **Double-click** to open it
3. You should see files:
   - `zatca_invoice_qr.php` ✅
   - `install.php` ✅
   - `README.md` ✅
   - `controllers/` folder ✅
   - `models/` folder ✅
   - `views/` folder ✅
   - `libraries/` folder ✅
   - etc.

#### **Step 5: Delete ZIP File**
1. Go back to `modules/` folder
2. Delete `zatca_invoice_qr_v1.0.1_fixed.zip`
3. Keep only the extracted `zatca_invoice_qr/` folder

#### **Step 6: Set Permissions**
1. Select `zatca_invoice_qr/` folder
2. Click **"Permissions"** button (top toolbar)
3. Set to: **755**
4. Check **"Recurse into subdirectories"**
5. Click **"Change Permissions"**

---

### **Method 2: FTP Client (FileZilla, WinSCP)**

#### **Step 1: Extract ZIP on Your PC First**
**IMPORTANT**: Extract on your computer FIRST, then upload!

**On Windows:**
1. Right-click `zatca_invoice_qr_v1.0.1_fixed.zip`
2. Select "Extract All..." or "Extract Here"
3. You get folder: `zatca_invoice_qr/`
4. Open it to verify files are there

**On Mac:**
1. Double-click `zatca_invoice_qr_v1.0.1_fixed.zip`
2. Automatically extracts to `zatca_invoice_qr/`
3. Open to verify files

**On Linux:**
```bash
unzip zatca_invoice_qr_v1.0.1_fixed.zip
ls zatca_invoice_qr/
```

#### **Step 2: Connect via FTP**
1. Open FileZilla (or your FTP client)
2. Enter server details:
   - Host: your-server.com (or IP)
   - Username: FTP username
   - Password: FTP password
   - Port: 21 (or 22 for SFTP)
3. Click **"Quickconnect"**

#### **Step 3: Navigate to Modules Folder**
1. In **Remote Site** (right pane)
2. Navigate to: `/public_html/erp/modules/`
3. Or wherever your Perfex is installed

#### **Step 4: Delete Old Module (if exists)**
1. Right-click `zatca_invoice_qr` folder
2. Select **"Delete"**
3. Confirm

#### **Step 5: Upload Extracted Folder**
1. In **Local Site** (left pane)
2. Navigate to where you extracted the ZIP
3. Find the `zatca_invoice_qr/` folder
4. **Drag and drop** entire `zatca_invoice_qr/` folder to right pane
5. Wait for upload to complete (53 files)

#### **Step 6: Verify Upload**
In Remote Site, you should see:
```
modules/
└── zatca_invoice_qr/
    ├── zatca_invoice_qr.php
    ├── install.php
    ├── controllers/
    ├── models/
    ├── views/
    └── ... (all files)
```

---

### **Method 3: SSH / Command Line**

#### **Step 1: Upload ZIP to Server**
```bash
# From your PC, upload via SCP
scp zatca_invoice_qr_v1.0.1_fixed.zip username@your-server.com:/tmp/
```

#### **Step 2: Connect via SSH**
```bash
ssh username@your-server.com
```

#### **Step 3: Navigate to Modules Directory**
```bash
cd /path/to/your/erp/modules/

# Examples:
cd /home/username/public_html/erp/modules/
# OR
cd /var/www/html/erp/modules/
```

#### **Step 4: Remove Old Module**
```bash
rm -rf zatca_invoice_qr/
```

#### **Step 5: Extract ZIP**
```bash
unzip /tmp/zatca_invoice_qr_v1.0.1_fixed.zip
```

#### **Step 6: Verify Extraction**
```bash
ls -la zatca_invoice_qr/
```

Should show:
```
total 120
drwxr-xr-x 10 user user  4096 zatca_invoice_qr/
-rw-r--r--  1 user user  6626 zatca_invoice_qr.php
-rw-r--r--  1 user user  9888 install.php
drwxr-xr-x  2 user user  4096 controllers/
drwxr-xr-x  2 user user  4096 models/
... etc
```

#### **Step 7: Set Permissions**
```bash
chmod -R 755 zatca_invoice_qr/
chown -R www-data:www-data zatca_invoice_qr/  # Ubuntu
# OR
chown -R apache:apache zatca_invoice_qr/      # CentOS
# OR
chown -R username:username zatca_invoice_qr/  # Shared hosting
```

#### **Step 8: Cleanup**
```bash
rm /tmp/zatca_invoice_qr_v1.0.1_fixed.zip
```

---

## 🔍 **Troubleshooting Empty Folder**

### **Issue 1: Hidden Files**

If folder looks empty but has files:

**Windows:**
1. Open folder
2. Go to: View → Options → Change folder and search options
3. View tab → Show hidden files
4. Click OK

**Mac:**
1. Open folder in Finder
2. Press: `Cmd + Shift + .` (period)
3. Hidden files appear

**cPanel File Manager:**
1. Click **Settings** (top right)
2. Check **"Show Hidden Files (dotfiles)"**
3. Click **Save**

---

### **Issue 2: Nested Folders**

Sometimes extraction creates extra nesting:

**Wrong Structure:**
```
zatca_invoice_qr/
└── zatca_invoice_qr/  ← Extra folder!
    ├── zatca_invoice_qr.php
    └── ... files
```

**Correct Structure:**
```
zatca_invoice_qr/
├── zatca_invoice_qr.php  ← Files at root
├── install.php
├── controllers/
└── ... folders
```

**Fix:**
1. Move all files from inner folder to outer folder
2. Delete empty inner folder

---

### **Issue 3: Wrong Extraction Location**

**Problem**: Extracted to wrong place

**Check Where You Extracted:**
- Should be: `/erp/modules/zatca_invoice_qr/`
- NOT: `/erp/zatca_invoice_qr/` ❌
- NOT: `/home/username/zatca_invoice_qr/` ❌

**Fix:**
1. Delete wrong location
2. Extract to correct location

---

### **Issue 4: Permissions Problem**

**Check Permissions:**
```bash
ls -la /path/to/erp/modules/
```

Should show:
```
drwxr-xr-x  10 user user  4096 zatca_invoice_qr/
```

**If shows different:**
```bash
chmod 755 zatca_invoice_qr/
chmod 644 zatca_invoice_qr/*.php
```

---

### **Issue 5: ZIP Corruption**

**Verify ZIP is valid:**

**Windows:**
- Right-click ZIP → Properties
- Size should be: ~315 KB
- Try opening with 7-Zip or WinRAR

**Mac/Linux:**
```bash
unzip -t zatca_invoice_qr_v1.0.1_fixed.zip
```

Should say: "No errors detected"

**If corrupt:**
1. Re-download ZIP from GitHub
2. Check download completed fully

---

## ✅ **Verification Checklist**

After extraction, verify these files exist:

### **Root Level Files:**
- [ ] `zatca_invoice_qr.php` (6.6 KB)
- [ ] `install.php` (9.9 KB) - **FIXED VERSION**
- [ ] `install_safe.php` (11.4 KB) - Alternative
- [ ] `uninstall.php` (1 KB)
- [ ] `README.md` (10.5 KB)
- [ ] `INSTALLATION.md` (12 KB)
- [ ] `CHANGELOG.md` (6.7 KB)
- [ ] `LICENSE` (1 KB)

### **Directories:**
- [ ] `controllers/` folder
  - [ ] `Zatca_admin.php` (9.2 KB)
- [ ] `models/` folder
  - [ ] `Zatca_settings_model.php` (6 KB)
  - [ ] `Zatca_qr_model.php` (9.2 KB)
- [ ] `views/` folder
  - [ ] `admin/settings.php` (13.8 KB)
- [ ] `libraries/` folder
  - [ ] `zatca_core/Zatca_tlv_generator.php` (7.4 KB)
  - [ ] `Zatca_qr_image.php` (7.9 KB)
  - [ ] `phpqrcode/` folder (complete QR library)
- [ ] `assets/` folder
  - [ ] `css/zatca_admin.css` (4.3 KB)
  - [ ] `js/zatca_admin.js` (6.4 KB)
- [ ] `language/` folder
  - [ ] `english/zatca_invoice_qr_lang.php` (5.5 KB)
  - [ ] `arabic/zatca_invoice_qr_lang.php` (5.3 KB)
- [ ] `helpers/` folder
  - [ ] `zatca_invoice_qr_helper.php`

**Total**: 53 files across multiple folders

---

## 🎯 **Quick Test**

After extraction, run this command to count files:

**Via SSH:**
```bash
cd /path/to/erp/modules/zatca_invoice_qr/
find . -type f | wc -l
```

**Should return**: 53 (or close to it)

**If returns 0**: Folder is truly empty, re-extract!

---

## 📦 **Alternative: Download Direct Files**

If ZIP extraction keeps failing, download files individually:

### **From GitHub:**

1. Go to: https://github.com/afaqintegrated-lab/Zatca-qr-code
2. Click on `zatca_invoice_qr/` folder
3. Download each file individually
4. Upload to your server manually

**Main files to download:**
- `zatca_invoice_qr.php`
- `install.php` (the fixed one!)
- `uninstall.php`
- All folders and their contents

---

## 🆘 **Still Empty After Extraction?**

### **Option 1: Use cPanel Extract (Most Reliable)**
Always works because server does the extraction.

### **Option 2: Extract on PC, Upload Folder**
Guarantees you see the files before uploading.

### **Option 3: Clone from GitHub**
```bash
cd /path/to/erp/modules/
git clone https://github.com/afaqintegrated-lab/Zatca-qr-code.git temp
mv temp/zatca_invoice_qr ./
rm -rf temp
```

---

## 📞 **Need Help?**

Provide these details:

1. **Extraction Method Used**: cPanel / FTP / SSH / Other?
2. **Where Extracted To**: Full path?
3. **What You See**: Completely empty or some files?
4. **File Count**: How many files show up?
5. **Server Details**: Hosting provider, server type?

**Check Extraction:**
```bash
ls -la /path/to/erp/modules/zatca_invoice_qr/
```

Copy and send the output.

---

## ✅ **Summary**

**Best Method**: cPanel File Manager → Upload ZIP → Right-click Extract

**Why It Works**:
- Server does the extraction
- No upload/download issues
- Correct permissions automatically
- Can see files immediately

**After Extraction**:
1. Verify 53+ files exist
2. Check main file `zatca_invoice_qr.php` is there
3. Set permissions to 755
4. Delete ZIP file
5. Go to Perfex → Setup → Modules
6. Activate module!

---

**The ZIP file is correct and complete!** The issue is just the extraction method. Use cPanel extraction for best results! 🎯
