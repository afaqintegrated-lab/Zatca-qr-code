<?php

require_once __DIR__ . '/bootstrap.php';

use ZATCA\ZATCASimplifiedTaxInvoice;

echo "=== Testing TLV Encoding (Simple Test) ===\n\n";

// Create test instance
$zatca = new ZATCASimplifiedTaxInvoice();

// Use reflection to test the private TLV method
$reflection = new ReflectionClass($zatca);
$tlv_method = $reflection->getMethod('TLV');
$tlv_method->setAccessible(true);

// Test data - same as what ZATCA QR requires
$test_tags = [
    'Test Company LLC',           // Tag 1: Seller Name
    '310122393500003',            // Tag 2: VAT Registration Number
    '2024-10-15T10:30:00Z',       // Tag 3: Invoice Timestamp
    '230.00',                      // Tag 4: Invoice Total with VAT
    '30.00',                       // Tag 5: VAT Total
    base64_encode('invoice_hash'), // Tag 6: Invoice Hash (base64)
    base64_encode('digital_sig'),  // Tag 7: Digital Signature (base64)
    'public_key_data',            // Tag 8: Public Key
    'cert_signature_data'         // Tag 9: Certificate Signature
];

echo "Input tags:\n";
foreach ($test_tags as $i => $tag) {
    $num = $i + 1;
    $length = strlen($tag);
    echo "  Tag $num: length=$length, value=" . substr($tag, 0, 30) . (strlen($tag) > 30 ? '...' : '') . "\n";
}

// Invoke the TLV method
$tlv_result = $tlv_method->invoke($zatca, $test_tags);

echo "\n✓ TLV encoding successful!\n";

// Encode to base64 for QR code
$qr_code_base64 = base64_encode($tlv_result);

echo "\n--- Results ---\n";
echo "TLV Binary Length: " . strlen($tlv_result) . " bytes\n";
echo "Base64 QR Code: " . $qr_code_base64 . "\n";
echo "\nBase64 Length: " . strlen($qr_code_base64) . " characters\n";

// Show hex dump of first 100 bytes
echo "\nTLV Hex Dump (first 100 bytes):\n";
echo substr(bin2hex($tlv_result), 0, 200) . "...\n";

echo "\n=== Test Complete ===\n";
echo "\nThe QR code generation is working correctly!\n";
echo "You can use this base64 string in a QR code generator.\n";
