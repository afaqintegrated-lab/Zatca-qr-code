# 🧪 ZATCA QR Code - Local Testing Instructions

## 📋 Overview

This is a **standalone HTML test page** that allows you to test ZATCA Phase 1 QR code generation **directly on your PC** without needing:
- ❌ No PHP installation required
- ❌ No database setup needed
- ❌ No web server required
- ❌ No Perfex CRM installation needed

Just open the HTML file in your browser and start testing!

---

## 🚀 Quick Start (3 Steps)

### **Step 1: Download the Test File**
The test file is located at:
```
/home/user/webapp/zatca_test_local.html
```

Or download from GitHub:
```
https://github.com/afaqintegrated-lab/Zatca-qr-code/blob/main/zatca_test_local.html
```

### **Step 2: Open in Browser**
Simply **double-click** the `zatca_test_local.html` file, or:
- Right-click → "Open with" → Choose your browser (Chrome, Firefox, Edge, Safari)
- Drag and drop the file into your browser window

### **Step 3: Test!**
The page will open and you can immediately start creating invoices with ZATCA QR codes!

---

## ✨ Features

### ✅ What This Test Page Does:

1. **Invoice Form** - Fill in invoice details:
   - Seller information (name, VAT number)
   - Customer information
   - Invoice date and number
   - Multiple invoice items with quantities, prices, VAT rates

2. **ZATCA Phase 1 QR Generation**:
   - Automatic TLV (Tag-Length-Value) encoding
   - 5-field QR code generation:
     - Tag 1: Seller Name
     - Tag 2: VAT Registration Number
     - Tag 3: Invoice Date/Time (ISO 8601)
     - Tag 4: Invoice Total (including VAT)
     - Tag 5: VAT Amount
   - Base64 encoding of TLV data

3. **Visual Invoice Preview**:
   - Professional invoice layout
   - QR code displayed on invoice
   - All invoice details formatted nicely
   - ZATCA compliance information shown

4. **PDF Download**:
   - Download invoice as PDF
   - QR code embedded in PDF
   - Professional formatting
   - Scannable QR code

5. **QR Code Scanning**:
   - Use your phone camera to scan
   - Verify QR contains correct data
   - Test ZATCA compliance

---

## 📖 How to Use

### **1. Fill in Seller Information**

The form comes with **pre-filled demo data**:
- **Seller Name**: ABC Trading Company
- **VAT Number**: 123456789012345 (15 digits)

You can change these to your actual company details.

### **2. Fill in Invoice Details**

- **Invoice Number**: Auto-filled (INV-2024-001), change as needed
- **Invoice Date**: Auto-set to today, click to change
- **Customer Name**: Pre-filled (XYZ Corporation)
- **Customer Address**: Optional
- **Customer VAT**: Optional (15 digits)

### **3. Add Invoice Items**

The form starts with **one item** pre-filled:
- Description: Professional Services
- Quantity: 1
- Unit Price: 1000
- VAT %: 15%

**To add more items:**
- Click the **"+ Add Item"** button
- Fill in the new item details
- Click **"✕"** button to remove items (minimum 1 item required)

### **4. Add Notes (Optional)**

Enter any invoice notes, terms, or conditions in the "Notes" field.

### **5. Generate Invoice**

Click the **"🎯 Generate Invoice with ZATCA QR Code"** button.

**What happens:**
- Form data is validated
- Invoice totals are calculated
- ZATCA QR code is generated using TLV encoding
- Invoice preview is displayed with QR code
- Page scrolls to show the generated invoice

### **6. Review the Invoice**

The invoice preview shows:
- ✅ Professional invoice layout
- ✅ QR code in top-right corner (scannable!)
- ✅ All invoice details
- ✅ Items table with quantities, prices, VAT
- ✅ Subtotal, VAT total, Grand total
- ✅ ZATCA QR information breakdown

### **7. Test QR Code**

**Scan the QR code with your phone:**
1. Open phone camera app
2. Point at the QR code on screen
3. QR scanner apps will decode the Base64 TLV data
4. You should see the encoded invoice information

**QR Code Contains:**
- Seller Name: ABC Trading Company
- VAT Number: 123456789012345
- Date/Time: 2024-10-20T12:00:00Z (ISO 8601)
- Invoice Total: 1150.00
- VAT Amount: 150.00

### **8. Download PDF**

Click **"📥 Download as PDF"** to:
- Generate a PDF version of the invoice
- PDF includes the QR code
- QR code is embedded as image (scannable from PDF!)
- Professional formatting maintained

### **9. Create Another Invoice**

Click **"📝 Create Another Invoice"** to:
- Return to the form
- Keep your seller information
- Create a new invoice

---

## 🎯 Test Scenarios

### **Scenario 1: Basic Single Item Invoice**
Use the pre-filled data:
1. Click "Generate Invoice"
2. Verify QR code appears
3. Check totals: 1000 + 150 VAT = 1150 total
4. Scan QR code with phone

**Expected Result:**
- Invoice displays correctly
- QR code is scannable
- Totals match: 1150.00 SAR

---

### **Scenario 2: Multiple Items with Different VAT Rates**

1. Keep first item (Professional Services, 15% VAT)
2. Click "+ Add Item"
3. Add: "Hardware", Qty: 2, Price: 500, VAT: 15%
4. Click "+ Add Item"
5. Add: "Software License", Qty: 1, Price: 2000, VAT: 0% (VAT exempt)
6. Generate invoice

**Expected Result:**
- Item 1: 1000 + 150 VAT = 1150
- Item 2: 1000 + 150 VAT = 1150 (2 × 500)
- Item 3: 2000 + 0 VAT = 2000 (exempt)
- **Subtotal**: 4000.00 SAR
- **Total VAT**: 300.00 SAR
- **Grand Total**: 4300.00 SAR

---

### **Scenario 3: Test VAT Number Validation**

1. Change VAT number to: 12345 (only 5 digits)
2. Click "Generate Invoice"

**Expected Result:**
- Alert: "VAT number must be exactly 15 digits"
- Invoice not generated

Fix: Enter 15-digit number: 123456789012345

---

### **Scenario 4: Test Required Fields**

1. Clear "Seller Name" field
2. Click "Generate Invoice"

**Expected Result:**
- Alert: "Please fill in all required fields marked with *"
- Invoice not generated

---

### **Scenario 5: Test PDF Download**

1. Generate an invoice
2. Click "Download as PDF"
3. Open downloaded PDF

**Expected Result:**
- PDF file downloads: INV-2024-001_ZATCA.pdf
- Invoice displays correctly in PDF
- QR code is visible and scannable from PDF
- All formatting preserved

---

### **Scenario 6: Test QR Code Scanning**

1. Generate invoice
2. Open QR scanner app on phone (or use camera)
3. Scan the QR code from screen

**Expected Result:**
- QR scanner decodes Base64 string
- Data contains TLV encoded values
- Can verify using online Base64 decoder

**Online Decoder Test:**
1. Copy the QR code data (Base64 string)
2. Go to: https://base64.guru/converter/decode
3. Decode to view hex values
4. Verify TLV structure:
   - Tag 1 (0x01): Seller name
   - Tag 2 (0x02): VAT number
   - Tag 3 (0x03): Timestamp
   - Tag 4 (0x04): Total
   - Tag 5 (0x05): VAT amount

---

## 🔍 Understanding the ZATCA QR Code

### **What is TLV Encoding?**

TLV stands for **Tag-Length-Value**:
- **Tag**: Identifies the data type (1 byte)
- **Length**: Size of the value in bytes (1 byte)
- **Value**: The actual data (variable length)

### **ZATCA Phase 1 Tags:**

| Tag | Field | Example |
|-----|-------|---------|
| 1 | Seller Name | ABC Trading Company |
| 2 | VAT Registration Number | 123456789012345 |
| 3 | Invoice Date/Time | 2024-10-20T12:00:00Z |
| 4 | Invoice Total (incl. VAT) | 1150.00 |
| 5 | VAT Amount | 150.00 |

### **Encoding Process:**

1. **Create TLV for each field:**
   ```
   Tag 1: 0x01 (tag) + 0x13 (length=19) + "ABC Trading Company" (value)
   Tag 2: 0x02 (tag) + 0x0F (length=15) + "123456789012345" (value)
   Tag 3: 0x03 (tag) + 0x14 (length=20) + "2024-10-20T12:00:00Z" (value)
   Tag 4: 0x04 (tag) + 0x07 (length=7) + "1150.00" (value)
   Tag 5: 0x05 (tag) + 0x06 (length=6) + "150.00" (value)
   ```

2. **Concatenate all TLV structures**
3. **Encode to Base64**
4. **Generate QR code from Base64 string**

### **QR Code Information Display:**

After generating invoice, scroll down to see:
- ✅ Decoded QR information
- ✅ All 5 fields displayed
- ✅ ISO 8601 timestamp
- ✅ Amounts in SAR currency
- ✅ Encoding method confirmed

---

## 🛠️ Technical Details

### **Technologies Used:**

1. **HTML5** - Structure
2. **CSS3** - Styling with gradients and animations
3. **JavaScript (Vanilla)** - No frameworks required!
4. **QRCode.js** - QR code generation library (CDN)
5. **jsPDF** - PDF generation library (CDN)

### **CDN Libraries:**

```html
<!-- QR Code Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- PDF Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
```

**No installation required!** Libraries load from CDN when page opens.

### **Browser Compatibility:**

✅ **Supported Browsers:**
- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari
- Opera

✅ **Requirements:**
- JavaScript enabled
- Internet connection (for CDN libraries)
- Canvas support (all modern browsers)

### **File Size:**
- HTML file: ~34 KB
- No dependencies to install
- CDN libraries: ~200 KB (loaded once)

---

## 📊 Testing Checklist

Use this checklist to verify everything works:

### **Form Functionality:**
- [ ] All input fields are editable
- [ ] Date picker works
- [ ] Add Item button creates new rows
- [ ] Remove Item button works (keeps minimum 1 item)
- [ ] Form validation shows alerts for missing fields
- [ ] VAT number validation (15 digits)

### **Calculation Accuracy:**
- [ ] Item totals calculate correctly (qty × price)
- [ ] VAT amounts calculate correctly (amount × VAT%)
- [ ] Line totals include VAT
- [ ] Subtotal sums all item amounts (excl. VAT)
- [ ] Total VAT sums all VAT amounts
- [ ] Grand total = Subtotal + Total VAT

### **QR Code Generation:**
- [ ] QR code appears after clicking Generate
- [ ] QR code is scannable with phone
- [ ] QR code contains Base64 encoded data
- [ ] TLV encoding is correct (5 tags)
- [ ] Timestamp is in ISO 8601 format

### **Invoice Display:**
- [ ] Invoice layout is professional
- [ ] All data displays correctly
- [ ] Amounts formatted with 2 decimals
- [ ] Currency (SAR) is shown
- [ ] QR code positioned in top-right
- [ ] ZATCA info section shows all fields

### **PDF Generation:**
- [ ] PDF download button works
- [ ] PDF file downloads with correct name
- [ ] PDF opens without errors
- [ ] Invoice layout preserved in PDF
- [ ] QR code visible in PDF
- [ ] QR code scannable from PDF

### **Create Another:**
- [ ] Button returns to form
- [ ] Form fields retain seller info
- [ ] Can create multiple invoices in session

---

## 🐛 Troubleshooting

### **Issue: QR Code Doesn't Appear**

**Possible Causes:**
1. JavaScript disabled in browser
2. CDN library failed to load (no internet)
3. Browser doesn't support Canvas

**Solution:**
- Enable JavaScript in browser settings
- Check internet connection
- Try different browser (Chrome recommended)
- Check browser console for errors (F12)

---

### **Issue: PDF Download Not Working**

**Possible Causes:**
1. jsPDF library didn't load
2. Browser blocking downloads
3. Popup blocker active

**Solution:**
- Check internet connection
- Allow downloads in browser settings
- Disable popup blocker for this page
- Try different browser

---

### **Issue: QR Code Not Scannable**

**Possible Causes:**
1. QR code too small on screen
2. Screen brightness too low
3. QR scanner app issue

**Solution:**
- Zoom in on QR code (Ctrl/Cmd + Plus)
- Increase screen brightness
- Try different QR scanner app
- Print invoice and scan from paper
- Download PDF and scan from PDF

---

### **Issue: Numbers Not Calculating**

**Possible Causes:**
1. Non-numeric input in number fields
2. JavaScript error in console

**Solution:**
- Enter only numbers in qty/price fields
- Use decimal point (.) not comma (,)
- Check browser console (F12) for errors
- Refresh page and try again

---

### **Issue: Form Validation Not Working**

**Solution:**
- Make sure JavaScript is enabled
- Clear browser cache
- Try opening in incognito/private mode
- Check browser console for errors

---

## 📱 Mobile Testing

The test page is **responsive** and works on mobile devices!

### **Mobile Browsers:**
- ✅ Chrome (Android)
- ✅ Safari (iOS)
- ✅ Firefox (Android/iOS)
- ✅ Samsung Internet

### **Mobile Features:**
- Touch-friendly form inputs
- Responsive layout adjusts to screen size
- QR code scales appropriately
- PDF download works on mobile

### **Tips for Mobile:**
- Use landscape mode for better view
- QR code might be smaller, zoom if needed
- PDF opens in default PDF viewer

---

## 🎨 Customization

### **Change Colors:**

Edit the CSS gradient in `<style>` section:
```css
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    /* Change #667eea and #764ba2 to your colors */
}
```

### **Change Currency:**

Search and replace "SAR" with your currency code (USD, EUR, etc.)

### **Change Default Values:**

Edit the HTML input values:
```html
<input type="text" id="sellerName" value="Your Company Name">
<input type="text" id="vatNumber" value="Your VAT Number">
```

### **Change QR Code Size:**

In JavaScript, find:
```javascript
new QRCode(qrContainer, {
    width: 150,  // Change this
    height: 150, // Change this
```

---

## 🔒 Privacy & Security

### **Data Privacy:**
- ✅ **All processing happens locally** in your browser
- ✅ **No data sent to any server**
- ✅ **No cookies or tracking**
- ✅ **No external API calls** (except CDN libraries)
- ✅ **Completely offline** after libraries load

### **Safe to Use:**
- No personal data collected
- No database connections
- No server-side processing
- No registration required
- No login required

---

## 📚 Next Steps

### **After Testing Locally:**

1. ✅ **Verified QR codes work?** → Ready to install Perfex module
2. ✅ **Understand TLV encoding?** → Good to go!
3. ✅ **Tested calculations?** → Confidence in accuracy

### **Install in Perfex CRM:**

Follow the installation guide:
```
/home/user/webapp/MODULE_NOT_APPEARING_COMPLETE_SOLUTION.md
```

### **Module vs Test Page:**

| Feature | Test Page | Perfex Module |
|---------|-----------|---------------|
| Standalone | ✅ Yes | ❌ No (needs Perfex) |
| Database | ❌ No | ✅ Yes |
| PDF Templates | Basic | Perfex themes |
| Auto-generation | ❌ Manual | ✅ Automatic |
| Batch processing | ❌ No | ✅ Yes |
| Multi-user | ❌ No | ✅ Yes |
| Invoice management | ❌ No | ✅ Full CRM |

**The test page is perfect for:**
- Understanding how ZATCA QR works
- Testing TLV encoding
- Verifying calculations
- Demonstrating to clients
- Learning before implementation

**The Perfex module is for:**
- Production use
- Full CRM integration
- Automatic QR generation
- Database storage
- Multi-user environment

---

## 🎯 Summary

**This test page allows you to:**
1. ✅ Test ZATCA QR generation instantly
2. ✅ Verify TLV encoding correctness
3. ✅ See exactly how invoices will look
4. ✅ Scan QR codes with your phone
5. ✅ Download PDF with QR embedded
6. ✅ Understand the complete process

**No installation, no setup, just open and test!** 🚀

---

## 📞 Need Help?

If you encounter issues with the test page:

1. **Check browser console** (F12 → Console tab)
2. **Verify internet connection** (for CDN libraries)
3. **Try different browser** (Chrome recommended)
4. **Check JavaScript is enabled**
5. **Refer to troubleshooting section above**

---

**File Location**: `/home/user/webapp/zatca_test_local.html`  
**File Size**: ~34 KB  
**Version**: 1.0.0  
**Last Updated**: 2025-10-20  
**Requires**: Modern web browser with JavaScript enabled
