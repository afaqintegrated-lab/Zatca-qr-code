# ZATCA Invoice QR - Perfex CRM Module

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/afaqintegrated-lab/zatca-invoice-qr)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg)](https://php.net/)

Generate **ZATCA (Zakat, Tax and Customs Authority)** compliant QR codes for Saudi Arabia invoices in Perfex CRM.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [ZATCA Phases](#zatca-phases)
- [Screenshots](#screenshots)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## ✨ Features

### Phase 1 (Current Implementation)
- ✅ **Automatic QR Generation**: Auto-generate QR codes on invoice creation/update
- ✅ **TLV Encoding**: Proper Tag-Length-Value encoding as per ZATCA specifications
- ✅ **5-Field QR Codes**: 
  - Seller Name
  - VAT Registration Number
  - Invoice Date & Time (ISO 8601)
  - Invoice Total (with VAT)
  - VAT Amount
- ✅ **PDF Integration**: Overlay QR codes on invoice PDFs
- ✅ **Configurable Position**: Place QR code in any corner of the PDF
- ✅ **Customizable Size**: Adjust QR code size (100-300px)
- ✅ **Batch Generation**: Generate QR codes for existing invoices
- ✅ **Multi-language**: English and Arabic support
- ✅ **Statistics Dashboard**: Track QR generation success rates
- ✅ **Test Mode**: Test QR generation before going live
- ✅ **VAT Number Validation**: Automatic validation of 15-digit Saudi VAT numbers

### Phase 2 (Coming Soon)
- 🔜 Digital Signatures (ECDSA)
- 🔜 Invoice Hash (SHA-256)
- 🔜 Public Key Integration
- 🔜 Certificate Management
- 🔜 ZATCA API Integration
- 🔜 Production Environment Support

## 📦 Requirements

- **Perfex CRM**: Version 2.3.0 or higher
- **PHP**: Version 7.4 or higher
- **PHP Extensions**:
  - GD Library (for QR code image generation)
  - mbstring
  - OpenSSL (for Phase 2)
- **Database**: MySQL 5.7+ or MariaDB 10.2+

## 🚀 Installation

### Method 1: Via Perfex CRM Admin Panel

1. Download the latest release ZIP file
2. Login to your Perfex CRM admin panel
3. Navigate to **Setup → Modules**
4. Click **Upload New Module**
5. Select the downloaded ZIP file
6. Click **Install**
7. Activate the module

### Method 2: Manual Installation

1. Clone or download this repository:
   ```bash
   git clone https://github.com/afaqintegrated-lab/zatca-invoice-qr.git
   ```

2. Copy the `zatca_invoice_qr` folder to your Perfex CRM modules directory:
   ```bash
   cp -r zatca-invoice-qr /path/to/perfex/modules/zatca_invoice_qr
   ```

3. Login to Perfex CRM admin panel

4. Navigate to **Setup → Modules**

5. Find "ZATCA Invoice QR" and click **Activate**

6. The module will automatically create required database tables

## ⚙️ Configuration

### Initial Setup

1. After activation, go to **Setup → ZATCA Invoice QR**

2. Configure the following settings:

#### Module Settings
- **Enable Module**: Toggle to activate QR generation
- **Phase**: Select Phase 1 (Phase 2 coming soon)
- **Environment**: Sandbox (for testing) or Production

#### Seller Information
- **Seller Name**: Your company name (required)
- **VAT Number**: Your 15-digit Saudi VAT registration number (required)
- **Company Address**: Full address (optional, required for Phase 2)

#### QR Code Settings
- **QR Position**: Choose where to place QR on invoice PDF
  - Top Right
  - Top Left
  - Bottom Right
  - Bottom Left
- **QR Size**: Size in pixels (recommended: 150-200px)
- **Auto-generate**: Automatically create QR codes for new invoices

3. Click **Save Settings**

4. Click **Test QR Generation** to verify your setup

## 📖 Usage

### Automatic Generation

If **Auto-generate** is enabled:
- QR codes are automatically generated when invoices are created
- QR codes are regenerated when invoices are updated
- QR codes appear automatically on invoice PDFs

### Manual Generation

#### Generate for Single Invoice
1. Open any invoice
2. The QR code will be visible if generated
3. To regenerate, use the regenerate button (if available)

#### Batch Generation for Existing Invoices
1. Go to **Setup → ZATCA Invoice QR**
2. Check the statistics to see invoices without QR codes
3. Click **Batch Generate QR Codes**
4. The system will process up to 50 invoices at a time

### Viewing QR Codes

QR codes are automatically embedded in:
- Invoice PDFs
- Invoice preview pages
- Client invoice views

### Testing

Use the **Test QR Generation** button to:
- Verify your settings are correct
- See a sample QR code
- View decoded TLV data
- Check QR code size compliance

## 🎯 ZATCA Phases

### Phase 1: Simplified Tax Invoice (Current)

**Implementation Status**: ✅ Complete

Phase 1 generates simplified QR codes with 5 basic fields:

1. **Seller Name** (Tag 1): Company/Business name
2. **VAT Number** (Tag 2): 15-digit registration number
3. **Invoice Date/Time** (Tag 3): ISO 8601 format
4. **Invoice Total** (Tag 4): Total amount including VAT
5. **VAT Amount** (Tag 5): VAT/Tax amount

**QR Code Structure**:
```
[Tag 1][Length][Seller Name]
[Tag 2][Length][VAT Number]
[Tag 3][Length][ISO DateTime]
[Tag 4][Length][Total Amount]
[Tag 5][Length][VAT Amount]
```

All encoded using TLV (Tag-Length-Value) format and Base64 encoded.

### Phase 2: Signed Tax Invoice (Coming Soon)

**Implementation Status**: 🔜 In Development

Phase 2 adds cryptographic signatures and additional fields:

6. **Invoice Hash** (Tag 6): SHA-256 hash of invoice XML
7. **Digital Signature** (Tag 7): ECDSA signature
8. **Public Key** (Tag 8): ECDSA public key
9. **Certificate Signature** (Tag 9): X.509 certificate info

**Additional Requirements**:
- Certificate from ZATCA portal
- Private/Public key pair generation
- UBL 2.1 XML invoice format
- API integration with ZATCA

## 📸 Screenshots

### Settings Page
![Settings](docs/images/settings.png)

### Test QR Generation
![Test QR](docs/images/test-qr.png)

### Invoice with QR Code
![Invoice QR](docs/images/invoice-qr.png)

## 🔧 Troubleshooting

### QR Code Not Appearing on Invoice

1. **Check Module Status**
   - Ensure module is enabled in settings
   - Verify seller name and VAT number are configured

2. **Check Invoice**
   - Ensure invoice has been saved/updated after enabling module
   - Try regenerating the QR code

3. **Check PHP Extensions**
   ```bash
   php -m | grep -i gd
   php -m | grep -i mbstring
   ```

4. **Check Permissions**
   - Ensure `uploads/temp/zatca_qr/` directory is writable
   ```bash
   chmod 755 uploads/temp/zatca_qr/
   ```

### Invalid VAT Number Error

- VAT numbers must be exactly 15 digits
- Remove any spaces, dashes, or special characters
- Example valid format: `310122393500003`

### QR Code Generation Failed

1. Check error logs: `application/logs/`
2. Ensure GD library is installed
3. Verify database tables were created:
   ```sql
   SHOW TABLES LIKE '%zatca%';
   ```

### Batch Generation Not Working

- Process batches of 50 at a time
- For large numbers, run multiple times
- Check PHP execution time limits
- Monitor for memory issues

## 🏗️ Development

### File Structure

```
zatca_invoice_qr/
├── zatca_invoice_qr.php       # Main module file
├── install.php                 # Installation script
├── uninstall.php               # Uninstallation script
├── controllers/
│   └── Zatca_admin.php        # Admin controller
├── models/
│   ├── Zatca_settings_model.php
│   └── Zatca_qr_model.php
├── views/
│   └── admin/
│       └── settings.php       # Settings page
├── libraries/
│   ├── Zatca_qr_image.php     # QR image generator
│   ├── phpqrcode/             # QR code library
│   └── zatca_core/
│       └── Zatca_tlv_generator.php
├── helpers/
│   └── zatca_invoice_qr_helper.php
├── assets/
│   ├── css/
│   └── js/
└── language/
    ├── english/
    └── arabic/
```

### Database Tables

1. **tblzatca_settings**: Module configuration
2. **tblzatca_invoice_qr**: Generated QR codes
3. **tblzatca_certificates**: Certificate management (Phase 2)

### Hooks Used

- `admin_init`: Register menu and permissions
- `app_admin_head`: Load assets
- `invoice_html_pdf_data`: Add QR to PDF
- `after_invoice_added`: Auto-generate QR
- `after_invoice_updated`: Regenerate QR

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-2 coding standards
- Use meaningful variable names
- Comment complex logic
- Write PHPDoc blocks for functions

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 💬 Support

### Documentation

- [Official ZATCA Guidelines](https://zatca.gov.sa/en/E-Invoicing/Pages/default.aspx)
- [Module Wiki](https://github.com/afaqintegrated-lab/zatca-invoice-qr/wiki)
- [FAQ](https://github.com/afaqintegrated-lab/zatca-invoice-qr/wiki/FAQ)

### Issues

Found a bug? Have a feature request?

- [Report Issues](https://github.com/afaqintegrated-lab/zatca-invoice-qr/issues)
- [Feature Requests](https://github.com/afaqintegrated-lab/zatca-invoice-qr/issues/new?template=feature_request.md)

### Contact

- **Email**: support@afaqintegrated-lab.com
- **Website**: [https://afaqintegrated-lab.com](https://afaqintegrated-lab.com)

## 🙏 Acknowledgments

- [Perfex CRM](https://www.perfexcrm.com/) - Amazing CRM platform
- [phpqrcode](http://phpqrcode.sourceforge.net/) - QR code generation library
- ZATCA - For e-invoicing standards and guidelines

## 🎯 Roadmap

- [x] Phase 1 Implementation
- [x] TLV Encoding
- [x] PDF Integration
- [x] Batch Generation
- [x] Multi-language Support
- [ ] Phase 2 Implementation
- [ ] Digital Signatures
- [ ] Certificate Management
- [ ] ZATCA API Integration
- [ ] Automated Testing
- [ ] REST API for Developers

---

**Made with ❤️ by [Afaq Integrated Lab](https://afaqintegrated-lab.com)**

**⭐ Star us on GitHub if this helps you!**
