# 🎯 How to Test ZATCA QR Code Locally on Your PC

## ✅ **What I Created for You**

I've built a **standalone HTML test page** that lets you test ZATCA QR code generation **directly on your PC** without any installation!

---

## 🚀 **3 Simple Steps to Test**

### **Step 1: Download the File**

The file is located at:
```
/home/user/webapp/zatca_test_local.html
```

**Or download from GitHub:**
```
https://github.com/afaqintegrated-lab/Zatca-qr-code/blob/main/zatca_test_local.html
```

### **Step 2: Open in Browser**

Just **double-click** the file `zatca_test_local.html` to open it in your default browser.

**Or:**
- Right-click → "Open with" → Chrome/Firefox/Edge
- Drag file into browser window

### **Step 3: Start Testing!**

The page opens with a pre-filled invoice form. Just click **"Generate Invoice"** button!

---

## ✨ **What You'll See**

### **Left Side: Invoice Form**
- ✅ Seller information (your company)
- ✅ VAT number (15 digits)
- ✅ Invoice number and date
- ✅ Customer information
- ✅ Invoice items with quantities, prices, VAT rates
- ✅ Add/remove items button
- ✅ Notes field

### **Right Side: After Clicking Generate**
- ✅ Beautiful invoice layout
- ✅ **ZATCA QR Code** (scannable!)
- ✅ All invoice details formatted
- ✅ Items table with calculations
- ✅ Subtotal, VAT, Grand Total
- ✅ QR code information breakdown

---

## 📱 **Test the QR Code**

### **With Your Phone:**
1. Click "Generate Invoice" on PC
2. QR code appears on invoice
3. Open phone camera app
4. Point camera at QR code on screen
5. Camera should detect and decode the QR code!

### **What's in the QR Code:**
- 🏢 Seller Name: ABC Trading Company
- 📋 VAT Number: 123456789012345
- 📅 Date/Time: 2024-10-20T12:00:00Z
- 💰 Invoice Total: 1150.00 SAR
- 💵 VAT Amount: 150.00 SAR

All encoded using **ZATCA Phase 1 TLV format**!

---

## 📥 **Download as PDF**

Click the **"Download as PDF"** button to:
- ✅ Download invoice as PDF file
- ✅ QR code embedded in PDF
- ✅ Professional formatting
- ✅ Scannable QR code from printed PDF

---

## 🎨 **Features**

### **✅ What Works:**
1. **Form Validation** - Checks required fields
2. **Multiple Items** - Add as many items as you want
3. **VAT Calculation** - Automatic calculation of all totals
4. **Different VAT Rates** - Each item can have different VAT %
5. **QR Generation** - Real ZATCA Phase 1 TLV encoding
6. **PDF Download** - Generate printable invoice
7. **Responsive Design** - Works on phone/tablet/PC
8. **No Installation** - Just open and test!

### **✅ No Requirements:**
- ❌ No PHP needed
- ❌ No database needed
- ❌ No web server needed
- ❌ No Perfex CRM needed
- ❌ No installation needed
- ✅ Just a web browser!

---

## 🧪 **Try These Tests**

### **Test 1: Default Demo**
1. Open file
2. Click "Generate Invoice"
3. See QR code appear
4. Scan with phone

**Expected:** Invoice with 1 item (1000 + 150 VAT = 1150 total)

---

### **Test 2: Multiple Items**
1. Click "+ Add Item" button
2. Add another item (e.g., "Consulting", Qty: 2, Price: 500, VAT: 15%)
3. Click "Generate Invoice"

**Expected:** Invoice with 2 items, totals calculated correctly

---

### **Test 3: Different VAT Rates**
1. Keep first item at 15% VAT
2. Add item with 0% VAT (tax exempt)
3. Add item with 5% VAT
4. Generate invoice

**Expected:** Each item calculates VAT correctly, total VAT is sum of all

---

### **Test 4: PDF Download**
1. Generate invoice
2. Click "Download as PDF"
3. Open PDF file

**Expected:** PDF downloads, QR code is visible and scannable from PDF

---

### **Test 5: Create Multiple Invoices**
1. Generate first invoice
2. Click "Create Another Invoice"
3. Change invoice number and details
4. Generate second invoice

**Expected:** Can create unlimited invoices in one session

---

## 📊 **Understanding the QR Code**

### **ZATCA Phase 1 Requirements:**

The QR code contains **5 fields** encoded in TLV format:

| Tag | Field | Example Value |
|-----|-------|---------------|
| 1 | Seller Name | ABC Trading Company |
| 2 | VAT Number | 123456789012345 |
| 3 | Date/Time (ISO 8601) | 2024-10-20T12:00:00Z |
| 4 | Invoice Total | 1150.00 |
| 5 | VAT Amount | 150.00 |

### **TLV Encoding:**

Each field is encoded as:
- **T**ag (1 byte): Field identifier
- **L**ength (1 byte): Size of value
- **V**alue (variable): Actual data

All TLV blocks are concatenated and Base64 encoded, then QR code is generated from the Base64 string.

---

## 📱 **Mobile Testing**

The test page works on mobile too!

**On Your Phone:**
1. Transfer the HTML file to phone
2. Open with Chrome/Safari
3. Fill form and generate
4. Save as PDF
5. QR code works on mobile!

**Or:**
- Upload HTML to any web hosting
- Open URL on phone
- Test directly on mobile device

---

## 🎯 **Why This is Useful**

### **Before Installing Perfex Module:**
- ✅ Test if QR codes work correctly
- ✅ Verify TLV encoding is correct
- ✅ See exactly how invoices will look
- ✅ Check calculations are accurate
- ✅ Test QR scanability with your phone
- ✅ Understand the complete process

### **For Demonstrations:**
- ✅ Show clients how ZATCA QR works
- ✅ Demo invoice generation
- ✅ Prove QR codes are scannable
- ✅ No need for full system setup

### **For Learning:**
- ✅ See TLV encoding in action
- ✅ Understand ZATCA requirements
- ✅ Learn Phase 1 specifications
- ✅ Test different scenarios

---

## 🔧 **Customization**

You can edit the HTML file to change:

### **Default Company Info:**
Find these lines and change values:
```html
<input type="text" id="sellerName" value="ABC Trading Company">
<input type="text" id="vatNumber" value="123456789012345">
```

### **Currency:**
Search for "SAR" and replace with your currency (USD, EUR, etc.)

### **Colors:**
Change the gradient colors in CSS:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### **QR Code Size:**
Find and change:
```javascript
width: 150,  // Change to 100, 200, etc.
height: 150,
```

---

## 📚 **Complete Documentation**

For detailed instructions, see:
```
LOCAL_TEST_INSTRUCTIONS.md
```

This includes:
- ✅ Complete feature list
- ✅ 6 test scenarios with expected results
- ✅ Troubleshooting guide
- ✅ Technical details
- ✅ Browser compatibility
- ✅ Mobile testing guide
- ✅ Customization options

---

## 🎉 **Summary**

**You now have:**
1. ✅ Standalone test page (`zatca_test_local.html`)
2. ✅ Complete instructions (`LOCAL_TEST_INSTRUCTIONS.md`)
3. ✅ No installation required
4. ✅ Works on any PC/Mac/Linux
5. ✅ Mobile-friendly
6. ✅ 100% functional ZATCA Phase 1 QR codes

**Just open the file and start testing!** 🚀

---

## 📍 **File Locations**

### **On Your System:**
```
/home/user/webapp/zatca_test_local.html
/home/user/webapp/LOCAL_TEST_INSTRUCTIONS.md
```

### **On GitHub:**
```
https://github.com/afaqintegrated-lab/Zatca-qr-code/blob/main/zatca_test_local.html
https://github.com/afaqintegrated-lab/Zatca-qr-code/blob/main/LOCAL_TEST_INSTRUCTIONS.md
```

---

## 🎯 **Next Steps**

### **After Testing:**

1. ✅ **Satisfied with QR codes?** → Install Perfex module
2. ✅ **Need to modify?** → Edit HTML file as needed
3. ✅ **Want to share?** → Upload to web hosting
4. ✅ **Ready for production?** → Follow Perfex installation guide

### **To Install in Perfex:**

See the complete installation guide:
```
MODULE_NOT_APPEARING_COMPLETE_SOLUTION.md
```

---

## 💡 **Tips**

1. **Test with real data** - Use your actual company name and VAT number
2. **Print and scan** - Download PDF, print, scan QR from paper
3. **Test on phone** - Transfer file to phone and test mobile experience
4. **Try edge cases** - Test with 0% VAT, very large amounts, many items
5. **Verify calculations** - Double-check all totals match expectations

---

## 🆘 **Having Issues?**

### **QR Code Doesn't Appear:**
- Check internet connection (needs CDN libraries)
- Enable JavaScript in browser
- Try Chrome browser

### **Can't Scan QR Code:**
- Increase screen brightness
- Zoom in on QR code
- Try different QR scanner app
- Download PDF and scan from paper

### **PDF Not Downloading:**
- Check browser download permissions
- Disable popup blocker
- Try different browser

### **Numbers Wrong:**
- Use decimal point (.) not comma (,)
- Check all items have valid quantities/prices
- Verify VAT percentages are correct

---

## ✅ **You're All Set!**

**Everything you need to test ZATCA QR codes locally is ready!**

1. Open `zatca_test_local.html`
2. Click "Generate Invoice"
3. Scan QR code with phone
4. Download PDF
5. Create more invoices!

**No installation, no setup, just test!** 🎯

---

**File**: zatca_test_local.html  
**Size**: ~34 KB  
**Requirements**: Web browser with JavaScript  
**Internet**: Only for CDN libraries (QRCode.js, jsPDF)  
**Works Offline**: After libraries load  
**Version**: 1.0.0  
**Last Updated**: 2025-10-20
