# ZATCA E-Invoicing Implementation

This is a PHP implementation for ZATCA (Zakat, Tax and Customs Authority) E-Invoicing system for Saudi Arabia.

## Issues Fixed

### 1. Missing QR Code Tag Classes
Created the following missing tag classes required for QR code generation:
- `tags/InvoiceHash.php` (Tag 6)
- `tags/DigitalSignature.php` (Tag 7)
- `tags/PublicKey.php` (Tag 8)
- `tags/SignatureValue.php` (Tag 9)

### 2. Missing ROOT_PATH Constant
Created `bootstrap.php` file that:
- Defines the `ROOT_PATH` constant
- Implements autoloader for ZATCA namespace classes

### 3. Missing API Class
Created `API.php` class with:
- Certificate issuance functionality
- Invoice compliance checking
- Support for both production and sandbox environments

## Requirements

- PHP 7.4 or higher
- OpenSSL extension
- cURL extension
- ext-json

## Installation

1. Include the bootstrap file in your application:
```php
require_once __DIR__ . '/bootstrap.php';
```

## Usage

### Basic Example

```php
<?php
require_once __DIR__ . '/bootstrap.php';

use ZATCA\EGS;

// Configure your EGS unit
$egs_info = [
    'uuid' => 'YOUR-UUID',
    'custom_id' => 'EGS1-886431145-1',
    'model' => 'Your Model',
    'CRN_number' => '1234567891',
    'VAT_name' => 'Company Name',
    'VAT_number' => '310122393500003',
    'location' => [
        'city' => 'Riyadh',
        'city_subdivision' => 'District',
        'street' => 'Street Name',
        'plot_identification' => '1234',
        'building' => '1234',
        'postal_zone' => '12345'
    ],
    'branch_name' => 'Main Branch',
    'branch_industry' => 'Retail'
];

// Create EGS instance
$egs = new EGS($egs_info);

// Generate private key and CSR
list($private_key, $csr) = $egs->generateNewKeysAndCSR('Your Solution Name');

// Issue compliance certificate (requires OTP from ZATCA)
list($request_id, $certificate, $secret) = $egs->issueComplianceCertificate($otp, $csr);

// Prepare invoice data
$invoice_data = [
    'invoice_serial_number' => 'INV-001',
    'invoice_counter_number' => 1,
    'issue_date' => date('Y-m-d'),
    'issue_time' => date('H:i:s'),
    'previous_invoice_hash' => 'BASE64_HASH',
    'line_items' => [
        [
            'id' => 1,
            'name' => 'Product Name',
            'quantity' => 1,
            'tax_exclusive_price' => 100.00,
            'VAT_percent' => 0.15,
            'discounts' => [],
            'other_taxes' => []
        ]
    ]
];

// EGS unit configuration
$egs_unit = array_merge($egs_info, [
    'cancelation' => [
        'cancelation_type' => 'INVOICE',
        'canceled_invoice_number' => null
    ]
]);

// Sign invoice
list($signed_invoice_xml, $invoice_hash, $qr_code) = $egs->signInvoice(
    $invoice_data,
    $egs_unit,
    $certificate,
    $private_key
);

echo "QR Code: $qr_code\n";
echo "Invoice Hash: $invoice_hash\n";

// Check compliance
$compliance_result = $egs->checkInvoiceCompliance(
    $signed_invoice_xml,
    $invoice_hash,
    $certificate,
    $secret
);
```

## QR Code Structure

The QR code contains 9 TLV-encoded fields:

1. Seller Name
2. VAT Registration Number
3. Invoice Date & Time (ISO 8601)
4. Invoice Total (with VAT)
5. VAT Total
6. Invoice Hash
7. Digital Signature
8. Public Key
9. Certificate Signature

## File Structure

```
ZATCA/
├── bootstrap.php                 # Bootstrap and autoloader
├── API.php                       # ZATCA API integration
├── EGS.php                       # Electronic Generation System
├── ZATCASimplifiedTaxInvoice.php # Invoice generation and signing
├── GenerateQrCode.php            # QR code generator
├── Tag.php                       # Base TLV tag class
├── example.php                   # Usage example
├── tags/                         # QR code tag classes
│   ├── Seller.php
│   ├── TaxNumber.php
│   ├── InvoiceDate.php
│   ├── InvoiceTotalAmount.php
│   ├── InvoiceTaxAmount.php
│   ├── InvoiceHash.php
│   ├── DigitalSignature.php
│   ├── PublicKey.php
│   └── SignatureValue.php
└── templates/                    # XML templates
    ├── simplified_tax_invoice_template.php
    ├── csr_template.php
    ├── ubl_signature.php
    └── ...
```

## Testing

Run the example file to test the implementation:

```bash
php example.php
```

## Notes

- The inline `TLV()` method in `ZATCASimplifiedTaxInvoice.php` is currently used for QR generation
- The `GenerateQrCode` class exists but is not currently integrated (can be refactored later)
- For production use, obtain proper certificates from ZATCA portal
- Keep your private keys secure and never commit them to version control

## ZATCA Compliance

This implementation follows:
- UBL 2.1 standard
- ZATCA e-invoicing specifications
- ISO 8601 date/time format
- SHA-256 hashing
- ECDSA digital signatures (secp256k1 curve)
- XAdES signature format

## Support

For ZATCA API documentation, visit: https://zatca.gov.sa/en/E-Invoicing/Pages/default.aspx
