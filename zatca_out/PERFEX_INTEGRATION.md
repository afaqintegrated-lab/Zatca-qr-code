# ZATCA E-Invoicing Integration Guide for Perfex CRM

This guide explains how to integrate the ZATCA E-Invoicing module with Perfex CRM.

## Installation

1. Copy the entire ZATCA folder to your Perfex CRM modules directory:
   ```
   /path/to/perfex/modules/zatca/
   ```

2. Ensure all files are present:
   - `bootstrap.php` - Autoloader and ROOT_PATH definition
   - `API.php` - ZATCA API integration
   - `EGS.php` - Electronic Generation System
   - `ZATCASimplifiedTaxInvoice.php` - Invoice generator
   - `GenerateQrCode.php`, `Tag.php` - QR code utilities
   - `tags/` directory with all 9 tag classes
   - `templates/` directory with XML templates

## Configuration

### Step 1: Module Integration

Create a Perfex CRM module file (e.g., `modules/zatca/zatca.php`):

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Include ZATCA bootstrap
require_once(__DIR__ . '/bootstrap.php');

// Register hooks
hooks()->add_action('after_invoice_added', 'zatca_generate_invoice');
hooks()->add_action('after_invoice_updated', 'zatca_update_invoice');
```

### Step 2: Database Setup

Add these fields to your invoices table or create a separate `zatca_invoices` table:

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
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `tbl invoices`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Store previous invoice hash for chaining
CREATE TABLE IF NOT EXISTS `zatca_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) UNIQUE,
    `setting_value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 3: Configuration Settings

Add these settings in Perfex CRM Settings (or zatca_settings table):

```php
$zatca_config = [
    'enabled' => true,
    'production' => false, // Set to true for production
    'egs_uuid' => 'YOUR-UUID',
    'egs_model' => 'Model Name',
    'crn_number' => '1234567891',
    'vat_name' => 'Company Name',
    'vat_number' => '310122393500003',
    'location' => [
        'city' => 'Riyadh',
        'city_subdivision' => 'District',
        'street' => 'Street Name',
        'plot_identification' => '1234',
        'building' => '1234',
        'postal_zone' => '12345'
    ],
    'branch_name' => 'Main Branch',
    'branch_industry' => 'Retail',
    'private_key' => '', // Store securely
    'certificate' => '', // From ZATCA
    'secret' => '' // From ZATCA
];
```

## Implementation

### Function 1: Generate ZATCA Invoice

```php
<?php
function zatca_generate_invoice($invoice_id) {
    $CI = &get_instance();
    $CI->load->model('invoices_model');

    // Get Perfex invoice data
    $invoice = $CI->invoices_model->get($invoice_id);

    if (!$invoice) {
        log_activity('ZATCA: Invoice not found - ID: ' . $invoice_id);
        return false;
    }

    try {
        require_once(FCPATH . 'modules/zatca/bootstrap.php');

        use ZATCA\EGS;

        // Load ZATCA configuration
        $zatca_config = get_zatca_config();

        // Initialize EGS
        $egs_info = [
            'uuid' => $zatca_config['egs_uuid'],
            'custom_id' => 'EGS1-' . $zatca_config['crn_number'] . '-1',
            'model' => $zatca_config['egs_model'],
            'CRN_number' => $zatca_config['crn_number'],
            'VAT_name' => $zatca_config['vat_name'],
            'VAT_number' => $zatca_config['vat_number'],
            'location' => $zatca_config['location'],
            'branch_name' => $zatca_config['branch_name'],
            'branch_industry' => $zatca_config['branch_industry']
        ];

        $egs = new EGS($egs_info);
        $egs->production = $zatca_config['production'];

        // Convert Perfex invoice to ZATCA format
        $zatca_invoice = convert_perfex_to_zatca_invoice($invoice);

        // Get previous invoice hash for chaining
        $previous_hash = get_setting('zatca_previous_invoice_hash') ?:
                        'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==';

        $zatca_invoice['previous_invoice_hash'] = $previous_hash;
        $zatca_invoice['invoice_counter_number'] = get_next_invoice_counter();

        // EGS unit configuration
        $egs_unit = array_merge($egs_info, [
            'cancelation' => [
                'cancelation_type' => 'INVOICE',
                'canceled_invoice_number' => null
            ]
        ]);

        // Sign invoice
        list($signed_xml, $invoice_hash, $qr_code) = $egs->signInvoice(
            $zatca_invoice,
            $egs_unit,
            $zatca_config['certificate'],
            $zatca_config['private_key']
        );

        // Save to database
        $CI->db->insert('zatca_invoices', [
            'invoice_id' => $invoice_id,
            'invoice_hash' => $invoice_hash,
            'qr_code' => $qr_code,
            'signed_xml' => $signed_xml,
            'zatca_uuid' => $zatca_config['egs_uuid'],
            'invoice_counter' => $zatca_invoice['invoice_counter_number'],
            'compliance_status' => 'pending'
        ]);

        // Update previous hash for next invoice
        update_option('zatca_previous_invoice_hash', $invoice_hash);

        // Check compliance (optional - can be done asynchronously)
        if ($zatca_config['enabled']) {
            $compliance_result = $egs->checkInvoiceCompliance(
                $signed_xml,
                $invoice_hash,
                $zatca_config['certificate'],
                $zatca_config['secret']
            );

            log_activity('ZATCA Compliance Check: ' . $compliance_result);
        }

        log_activity('ZATCA Invoice Generated: ' . $invoice_id);
        return true;

    } catch (Exception $e) {
        log_activity('ZATCA Error: ' . $e->getMessage());
        return false;
    }
}

// Helper function to convert Perfex invoice to ZATCA format
function convert_perfex_to_zatca_invoice($perfex_invoice) {
    $line_items = [];

    foreach ($perfex_invoice->items as $item) {
        $line_items[] = [
            'id' => $item['id'],
            'name' => $item['description'],
            'quantity' => $item['qty'],
            'tax_exclusive_price' => $item['rate'],
            'VAT_percent' => ($item['taxrate'] / 100), // Convert percentage to decimal
            'discounts' => [],
            'other_taxes' => []
        ];
    }

    return [
        'invoice_serial_number' => format_invoice_number($perfex_invoice->id),
        'invoice_counter_number' => 1, // Will be set later
        'issue_date' => date('Y-m-d', strtotime($perfex_invoice->date)),
        'issue_time' => date('H:i:s', strtotime($perfex_invoice->date)),
        'previous_invoice_hash' => '', // Will be set later
        'line_items' => $line_items
    ];
}

// Helper function to get next invoice counter
function get_next_invoice_counter() {
    $CI = &get_instance();
    $CI->db->select_max('invoice_counter');
    $query = $CI->db->get('zatca_invoices');
    $result = $query->row();

    return ($result && $result->invoice_counter) ? ($result->invoice_counter + 1) : 1;
}
```

### Function 2: Display QR Code on Invoice PDF

Modify your invoice PDF template to include the QR code:

```php
<?php
// In your invoice PDF generation (e.g., application/views/admin/invoices/invoice_pdf.php)

$CI = &get_instance();
$CI->db->where('invoice_id', $invoice->id);
$zatca_data = $CI->db->get('zatca_invoices')->row();

if ($zatca_data && $zatca_data->qr_code) {
    // Generate QR code image using a library like phpqrcode or chillerlan/php-qrcode
    // Example with chillerlan/php-qrcode (install via composer):

    use chillerlan\QRCode\QRCode;
    use chillerlan\QRCode\QROptions;

    $options = new QROptions([
        'version' => 5,
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_L,
    ]);

    $qrcode = new QRCode($options);
    $qr_image = $qrcode->render($zatca_data->qr_code);

    // Display in PDF
    echo '<div class="zatca-qr-code">';
    echo '<img src="' . $qr_image . '" alt="ZATCA QR Code" />';
    echo '<p>Scan for invoice verification</p>';
    echo '</div>';
}
?>
```

## Testing

1. **Test with Sandbox Environment**:
   - Set `production => false` in configuration
   - Use ZATCA sandbox API endpoints

2. **Generate Test Invoice**:
   ```php
   php test_tlv_simple.php // Verify TLV encoding works
   ```

3. **Check Compliance**:
   - Generate a real invoice in Perfex
   - Check `zatca_invoices` table for QR code
   - Verify QR code scans correctly

## Production Checklist

- [ ] Obtain production certificate from ZATCA portal
- [ ] Store private key securely (encrypted)
- [ ] Set `production => true`
- [ ] Test with real invoices
- [ ] Monitor compliance status
- [ ] Set up error logging and notifications
- [ ] Backup signed invoices regularly

## Troubleshooting

### QR Code Not Generating
- Check that all 9 tag classes exist in `tags/` directory
- Verify ROOT_PATH is correctly defined
- Check PHP error logs for exceptions

### Certificate Errors
- Ensure certificate is properly formatted with line breaks
- Use `cleanUpCertificateString()` method before processing
- Verify certificate is from ZATCA and not expired

### API Errors
- Check network connectivity to ZATCA servers
- Verify credentials (certificate + secret)
- Check API endpoint URLs (sandbox vs production)

## Support

For ZATCA specifications: https://zatca.gov.sa/en/E-Invoicing/Pages/default.aspx

## Security Notes

1. **Never commit** private keys or certificates to version control
2. Store sensitive data encrypted in database
3. Use environment variables for configuration
4. Implement proper access controls for ZATCA settings
5. Log all ZATCA operations for audit trail

## Files Created/Fixed

✅ Created missing tag classes:
- `tags/InvoiceHash.php`
- `tags/DigitalSignature.php`
- `tags/PublicKey.php`
- `tags/SignatureValue.php`

✅ Created supporting files:
- `bootstrap.php` - Autoloader and ROOT_PATH
- `API.php` - ZATCA API integration
- `test_tlv_simple.php` - Test TLV encoding

✅ Fixed path issues:
- Updated all template paths from `/ZATCA/templates/` to `/templates/`
- Fixed ROOT_PATH references throughout codebase

✅ Verified QR code generation works correctly
