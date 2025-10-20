<?php

require_once __DIR__ . '/bootstrap.php';

use ZATCA\EGS;
use ZATCA\ZATCASimplifiedTaxInvoice;

// Example EGS (Electronic Generation System) configuration
$egs_info = [
    'uuid' => '6f4d20e0-6bfe-4a80-9389-7c5e09357a26', // Unique identifier for your device
    'custom_id' => 'EGS1-886431145-1',
    'model' => 'Model Name',
    'CRN_number' => '1234567891',
    'VAT_name' => 'Company Name',
    'VAT_number' => '310122393500003',
    'location' => [
        'city' => 'Riyadh',
        'city_subdivision' => 'Al Olaya',
        'street' => 'King Fahd Road',
        'plot_identification' => '1234',
        'building' => '1234',
        'postal_zone' => '12345'
    ],
    'branch_name' => 'Main Branch',
    'branch_industry' => 'Retail'
];

// Example invoice data
$invoice_data = [
    'invoice_serial_number' => 'INV-2024-001',
    'invoice_counter_number' => 1,
    'issue_date' => date('Y-m-d'),
    'issue_time' => date('H:i:s'),
    'previous_invoice_hash' => 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==', // Base64 encoded
    'line_items' => [
        [
            'id' => 1,
            'name' => 'Product 1',
            'quantity' => 2,
            'tax_exclusive_price' => 100.00,
            'VAT_percent' => 0.15, // 15% VAT
            'discounts' => [],
            'other_taxes' => []
        ],
        [
            'id' => 2,
            'name' => 'Product 2',
            'quantity' => 1,
            'tax_exclusive_price' => 50.00,
            'VAT_percent' => 0.15,
            'discounts' => [
                [
                    'amount' => 5.00,
                    'reason' => 'Seasonal discount'
                ]
            ],
            'other_taxes' => []
        ]
    ]
];

// EGS unit configuration for cancelation
$egs_unit = array_merge($egs_info, [
    'cancelation' => [
        'cancelation_type' => 'INVOICE', // or 'DEBIT_NOTE' or 'CREDIT_NOTE'
        'canceled_invoice_number' => null // Set this if canceling an invoice
    ]
]);

try {
    echo "=== ZATCA E-Invoice Generation Example ===\n\n";

    // Step 1: Create EGS instance
    $egs = new EGS($egs_info);
    echo "✓ EGS instance created\n";

    // Step 2: Generate keys and CSR (only needed once during onboarding)
    echo "\nGenerating private key and CSR...\n";
    list($private_key, $csr) = $egs->generateNewKeysAndCSR('Solution Name');
    echo "✓ Private key generated\n";
    echo "✓ CSR generated\n";
    echo "\nCSR (for ZATCA onboarding):\n";
    echo substr($csr, 0, 100) . "...\n";

    // Step 3: Issue compliance certificate (requires OTP from ZATCA portal)
    // Uncomment when you have an OTP
    /*
    $otp = 'YOUR_OTP_HERE';
    list($request_id, $certificate, $secret) = $egs->issueComplianceCertificate($otp, $csr);
    echo "\n✓ Compliance certificate issued\n";
    echo "Request ID: $request_id\n";
    */

    // For testing, use a valid dummy certificate
    // In production, you MUST use a real certificate from ZATCA
    $certificate = "MIIBkTCCATigAwIBAgIGAYriRHN1MAoGCCqGSM49BAMCMBUxEzARBgNVBAMM
ClRTVFpBVENBLTEwHhcNMjMwODMwMDkxMzM5WhcNMjgwODI4MjEwMDAwWjBT
MQswCQYDVQQGEwJTQTEaMBgGA1UECwwRUml5YWRoIEJyYW5jaCAxMTEaMBgG
A1UECgwRQXR0YXdoZWVkIENvbXBhbnkxDDAKBgNVBAMMA1RTVDBWMBAGByqG
SM49AgEGBSuBBAAKA0IABHVCasFCvHZ3+YmqMHJvOiPCz43XHCC3VxxZP82+
J5h4oM7Xj/OOi2z5KPNFsT7EZKJCCVNJCOnnNfLJ7VDx6bqjJjAkMCIGCSsG
AQQBgjcVBAQVMBMKAQCkDhMMCDMwMDAwMDAwMDAKBggqhkjOPQQDAgNHADBE
AiBwOUNz6F5MIB5PHVR0u9fXLJLLYQNz1x8fOkKpHrBnqgIgPl1HbzLKGLBC
wH8uu/bKAcNKLJH5GNJVF0iXPxMYNFw=";
    $secret = "Xlj15LyMCgSC66OYNqJKJaGb+5HLp/cp+G3+8nSH3DE=";

    // Step 4: Sign invoice
    echo "\n\nSigning invoice...\n";
    list($signed_invoice_xml, $invoice_hash, $qr_code) = $egs->signInvoice(
        $invoice_data,
        $egs_unit,
        $certificate,
        $private_key
    );

    echo "✓ Invoice signed successfully\n";
    echo "\n--- Invoice Hash ---\n";
    echo $invoice_hash . "\n";

    echo "\n--- QR Code (Base64) ---\n";
    echo $qr_code . "\n";

    echo "\n--- Signed Invoice XML (first 500 chars) ---\n";
    echo substr($signed_invoice_xml, 0, 500) . "...\n";

    // Step 5: Save signed invoice to file
    $output_file = ROOT_PATH . '/signed_invoice.xml';
    file_put_contents($output_file, $signed_invoice_xml);
    echo "\n✓ Signed invoice saved to: $output_file\n";

    // Step 6: Check compliance (requires certificate and secret)
    // Uncomment when you have valid certificate and secret
    /*
    echo "\nChecking invoice compliance...\n";
    $compliance_result = $egs->checkInvoiceCompliance(
        $signed_invoice_xml,
        $invoice_hash,
        $certificate,
        $secret
    );
    echo "✓ Compliance check result:\n";
    echo $compliance_result . "\n";
    */

    echo "\n=== Complete! ===\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
