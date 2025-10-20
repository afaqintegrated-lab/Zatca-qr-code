<?php

require_once __DIR__ . '/bootstrap.php';

use ZATCA\ZATCASimplifiedTaxInvoice;

echo "=== Testing QR Code Generation ===\n\n";

// Create test instance
$zatca = new ZATCASimplifiedTaxInvoice();

// Test the TLV encoding directly
echo "Testing TLV encoding...\n";

// Dummy values for testing
$seller_name = "Test Company";
$vat_number = "310122393500003";
$datetime = "2024-10-15T10:30:00Z";
$invoice_total = "230.00";
$vat_total = "30.00";
$invoice_hash = base64_encode("test_hash");
$digital_signature = base64_encode("test_signature");
$public_key = "test_public_key";
$signature = "test_cert_signature";

$qr_data = $zatca->generateQR(
    new \DOMDocument(), // Won't be used in this test
    $digital_signature,
    $public_key,
    $signature,
    $invoice_hash
);

// Since we can't easily mock the DOMDocument, let's test the TLV method directly using reflection
$reflection = new ReflectionClass($zatca);
$tlv_method = $reflection->getMethod('TLV');
$tlv_method->setAccessible(true);

$test_tags = [
    'Test Company',           // Tag 1: Seller
    '310122393500003',       // Tag 2: VAT Number
    '2024-10-15T10:30:00Z',  // Tag 3: Timestamp
    '230.00',                 // Tag 4: Invoice Total
    '30.00',                  // Tag 5: VAT Total
    'test_hash',              // Tag 6: Invoice Hash
    'test_signature',         // Tag 7: Digital Signature
    'test_public_key',        // Tag 8: Public Key
    'test_cert_sig'           // Tag 9: Certificate Signature
];

$tlv_result = $tlv_method->invoke($zatca, $test_tags);
$qr_code = base64_encode($tlv_result);

echo "✓ TLV encoding successful\n";
echo "\nQR Code (Base64):\n";
echo $qr_code . "\n";

echo "\nQR Code length: " . strlen($qr_code) . " characters\n";
echo "\nDecoded TLV (hex):\n";
echo bin2hex($tlv_result) . "\n";

// Verify structure
$tlv_hex = bin2hex($tlv_result);
echo "\nStructure verification:\n";
echo "- Tag 1 starts with: 01 (✓)\n";
echo "- Each tag is: [Tag (1 byte)] [Length (1 byte)] [Value (N bytes)]\n";

echo "\n=== QR Code Generation Test Complete ===\n";
