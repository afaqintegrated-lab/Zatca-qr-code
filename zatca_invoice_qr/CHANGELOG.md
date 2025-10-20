# Changelog

All notable changes to the ZATCA Invoice QR module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-20

### Added - Phase 1 Implementation

#### Core Features
- ✅ **ZATCA Phase 1 Compliance**: Complete implementation of simplified tax invoice QR codes
- ✅ **TLV Encoding**: Proper Tag-Length-Value encoding as per ZATCA specifications
- ✅ **5-Field QR Codes**:
  - Tag 1: Seller/Company Name
  - Tag 2: VAT Registration Number (15 digits)
  - Tag 3: Invoice Date & Time (ISO 8601 format)
  - Tag 4: Invoice Total (including VAT)
  - Tag 5: VAT Amount

#### Admin Interface
- ✅ **Settings Page**: Comprehensive settings interface
- ✅ **Configuration Options**:
  - Enable/Disable module
  - Phase selection (Phase 1/Phase 2)
  - Environment selection (Sandbox/Production)
  - Seller information management
  - QR positioning (4 corners)
  - QR size customization (100-300px)
  - Auto-generation toggle
- ✅ **Statistics Dashboard**: Real-time statistics showing:
  - Total invoices
  - Invoices with QR codes
  - Invoices without QR codes
  - Success rate percentage

#### QR Generation
- ✅ **Automatic Generation**: QR codes auto-generated on invoice create/update
- ✅ **Manual Generation**: Generate QR for specific invoices
- ✅ **Batch Generation**: Process up to 50 invoices at once
- ✅ **Regeneration**: Update QR codes when invoice data changes
- ✅ **Error Handling**: Comprehensive error logging and user feedback

#### PDF Integration
- ✅ **Invoice PDF Overlay**: QR codes automatically added to invoice PDFs
- ✅ **Configurable Position**: Place QR in any corner (top-right, top-left, bottom-right, bottom-left)
- ✅ **Scalable Size**: QR size adjustable from 100-300 pixels
- ✅ **Base64 Embedding**: QR codes embedded as data URIs in PDFs

#### Testing & Validation
- ✅ **Test QR Generator**: Test QR generation before going live
- ✅ **TLV Decoder**: View decoded QR data for verification
- ✅ **Size Validation**: Ensure QR codes meet ZATCA size requirements (max 500 bytes)
- ✅ **VAT Number Validation**: Automatic validation of 15-digit Saudi VAT numbers

#### Multi-language Support
- ✅ **English Language**: Complete English translation
- ✅ **Arabic Language**: Complete Arabic translation (RTL support)
- ✅ **Language Files**: Separate language files for easy customization

#### Database
- ✅ **Settings Table**: `tblzatca_settings` for module configuration
- ✅ **Invoice QR Table**: `tblzatca_invoice_qr` for storing generated QR codes
- ✅ **Certificates Table**: `tblzatca_certificates` (ready for Phase 2)

#### Developer Features
- ✅ **Helper Functions**: Reusable helper functions for QR operations
- ✅ **Hooks Integration**: Proper Perfex CRM hooks implementation
- ✅ **Models**: Separate models for settings and QR generation
- ✅ **Libraries**: 
  - `Zatca_tlv_generator`: TLV encoding/decoding
  - `Zatca_qr_image`: QR image generation
- ✅ **phpqrcode Library**: Integrated QR code generation library

#### Documentation
- ✅ **README**: Comprehensive project documentation
- ✅ **INSTALLATION**: Detailed installation guide with troubleshooting
- ✅ **Inline Comments**: Well-commented code
- ✅ **PHPDoc Blocks**: Function documentation

### Technical Details

#### Requirements
- Perfex CRM 2.3.0+
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.2+
- PHP GD Extension
- PHP mbstring Extension

#### Files Structure
```
zatca_invoice_qr/
├── zatca_invoice_qr.php       # Main module file
├── install.php                 # Installation & database setup
├── uninstall.php               # Cleanup script
├── controllers/
│   └── Zatca_admin.php        # Admin controller
├── models/
│   ├── Zatca_settings_model.php
│   └── Zatca_qr_model.php
├── views/
│   └── admin/settings.php     # Settings interface
├── libraries/
│   ├── Zatca_qr_image.php
│   ├── phpqrcode/            # QR generation library
│   └── zatca_core/
│       └── Zatca_tlv_generator.php
├── helpers/
│   └── zatca_invoice_qr_helper.php
├── assets/
│   ├── css/zatca_admin.css
│   └── js/zatca_admin.js
└── language/
    ├── english/
    └── arabic/
```

#### Database Schema
- **tblzatca_settings**: 12 fields
- **tblzatca_invoice_qr**: 11 fields  
- **tblzatca_certificates**: 10 fields

### Known Limitations (Phase 1)

- ⏳ Phase 2 features not yet implemented (digital signatures, invoice hash, etc.)
- ⏳ Certificate management not active (Phase 2 requirement)
- ⏳ ZATCA API integration pending (Phase 2)
- ⏳ UBL XML generation not included (Phase 2)

### Coming in Version 1.1.0 (Phase 2)

- 🔜 Digital signatures using ECDSA (secp256k1)
- 🔜 Invoice hash generation (SHA-256)
- 🔜 Public key management
- 🔜 X.509 certificate integration
- 🔜 ZATCA API connectivity
- 🔜 UBL 2.1 XML invoice format
- 🔜 Production environment support
- 🔜 Certificate expiry monitoring

### Security

- Input validation for all user inputs
- CSRF protection via CodeIgniter
- Prepared statements for database queries
- Base64 encoding for QR data
- Sanitized output in views

### Performance

- Lazy loading of QR generation
- Cached QR codes to prevent regeneration
- Optimized database queries with indexes
- Batch processing for bulk operations

---

## [Unreleased]

### Planned Features
- Unit tests for TLV encoding
- Integration tests with Perfex invoices
- REST API for external integration
- Export/Import settings
- QR code analytics
- Custom QR templates
- Email notifications for failures
- Webhook support

---

## Version History

- **v1.0.0** (2025-01-20): Initial release with Phase 1 implementation
- **v0.9.0** (2025-01-15): Beta testing phase
- **v0.1.0** (2025-01-10): Alpha development

---

## Upgrade Instructions

### From v0.x to v1.0.0

1. Backup your database
2. Deactivate the old version
3. Delete old module files
4. Install v1.0.0
5. Activate the module
6. Reconfigure settings (if needed)
7. Run batch generation for existing invoices

---

## Support

For issues, feature requests, or questions:
- GitHub Issues: https://github.com/afaqintegrated-lab/zatca-invoice-qr/issues
- Email: support@afaqintegrated-lab.com
- Documentation: https://github.com/afaqintegrated-lab/zatca-invoice-qr/wiki

---

**Note**: This module is compliant with ZATCA Phase 1 requirements as of January 2025. 
Always verify with official ZATCA documentation for the latest requirements.
