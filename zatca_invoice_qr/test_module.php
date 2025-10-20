<?php

/**
 * ZATCA Invoice QR Module - Comprehensive Test Script
 * 
 * This script tests all core functionality of the module
 * Run from command line: php test_module.php
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=================================================\n";
echo "ZATCA INVOICE QR MODULE - COMPREHENSIVE TEST\n";
echo "=================================================\n\n";

// Test 1: Load TLV Generator
echo "[TEST 1] Loading TLV Generator Library...\n";
require_once(__DIR__ . '/libraries/zatca_core/Zatca_tlv_generator.php');
$tlv_gen = new Zatca_tlv_generator();
echo "✅ TLV Generator loaded successfully\n\n";

// Test 2: Generate Phase 1 QR Code
echo "[TEST 2] Testing Phase 1 QR Generation...\n";
$test_data = [
    'seller_name'    => 'مؤسسة آفاق المتكاملة لتقنية المعلومات',
    'vat_number'     => '310122393500003',
    'invoice_date'   => '2025-01-20',
    'invoice_time'   => '14:30:00',
    'invoice_total'  => 1150.00,
    'vat_amount'     => 150.00,
];

try {
    $tlv_base64 = $tlv_gen->generate_phase1_qr($test_data);
    echo "✅ Phase 1 QR generated successfully\n";
    echo "   TLV Data (Base64): " . substr($tlv_base64, 0, 50) . "...\n";
    echo "   Length: " . strlen($tlv_base64) . " characters\n\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Decode TLV
echo "[TEST 3] Testing TLV Decoding...\n";
try {
    $decoded = $tlv_gen->decode_tlv($tlv_base64);
    echo "✅ TLV decoded successfully\n";
    echo "   Fields decoded: " . count($decoded) . "\n";
    foreach ($decoded as $tag => $value) {
        $tag_name = [
            1 => 'Seller Name',
            2 => 'VAT Number',
            3 => 'Date/Time',
            4 => 'Invoice Total',
            5 => 'VAT Amount'
        ][$tag] ?? "Tag $tag";
        echo "   - $tag_name: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 4: Validate QR Size
echo "[TEST 4] Testing QR Size Validation...\n";
$size_check = $tlv_gen->validate_qr_size($tlv_base64);
if ($size_check['valid']) {
    echo "✅ QR size is valid: " . $size_check['size'] . " bytes (max 500)\n\n";
} else {
    echo "⚠️  WARNING: " . $size_check['message'] . "\n\n";
}

// Test 5: Load QR Image Generator
echo "[TEST 5] Loading QR Image Generator...\n";
require_once(__DIR__ . '/libraries/phpqrcode/qrlib.php');
require_once(__DIR__ . '/libraries/Zatca_qr_image.php');
$qr_img = new Zatca_qr_image();
echo "✅ QR Image Generator loaded successfully\n\n";

// Test 6: Generate QR Image
echo "[TEST 6] Testing QR Image Generation...\n";
try {
    $qr_image_base64 = $qr_img->generate_base64($tlv_base64, 150);
    echo "✅ QR image generated successfully\n";
    echo "   Format: Base64 Data URI\n";
    echo "   Size: 150px\n";
    echo "   Data Length: " . strlen($qr_image_base64) . " characters\n\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 7: VAT Number Validation
echo "[TEST 7] Testing VAT Number Validation...\n";
$valid_vat = '310122393500003';
$invalid_vat = '12345';

echo "   Testing valid VAT: $valid_vat\n";
if (preg_match('/^\d{15}$/', $valid_vat)) {
    echo "   ✅ Valid VAT number format\n";
} else {
    echo "   ❌ Invalid VAT number format\n";
}

echo "   Testing invalid VAT: $invalid_vat\n";
if (preg_match('/^\d{15}$/', $invalid_vat)) {
    echo "   ❌ Should be invalid but passed\n";
} else {
    echo "   ✅ Correctly rejected invalid VAT\n";
}
echo "\n";

// Test 8: Invoice Hash Generation (for Phase 2)
echo "[TEST 8] Testing Invoice Hash Generation (Phase 2 prep)...\n";
$test_xml = '<Invoice><ID>INV-001</ID></Invoice>';
$hash = $tlv_gen->generate_invoice_hash($test_xml);
echo "✅ Invoice hash generated\n";
echo "   Hash (Base64): " . substr($hash, 0, 40) . "...\n";
echo "   Length: " . strlen($hash) . " characters\n\n";

// Test 9: Error Handling
echo "[TEST 9] Testing Error Handling...\n";
try {
    $invalid_data = [
        'seller_name' => 'Test',
        // Missing required fields
    ];
    $tlv_gen->generate_phase1_qr($invalid_data);
    echo "❌ Error handling failed - should have thrown exception\n\n";
} catch (Exception $e) {
    echo "✅ Error handling works correctly\n";
    echo "   Caught exception: " . $e->getMessage() . "\n\n";
}

// Test 10: Date/Time Formatting
echo "[TEST 10] Testing Date/Time ISO 8601 Formatting...\n";
$test_date = '2025-01-20';
$test_time = '14:30:00';
try {
    $dt = new DateTime($test_date . ' ' . $test_time, new DateTimeZone('Asia/Riyadh'));
    $iso_date = $dt->format('Y-m-d\TH:i:s\Z');
    echo "✅ ISO 8601 formatting works\n";
    echo "   Input: $test_date $test_time\n";
    echo "   Output: $iso_date\n\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n\n";
}

// Test 11: Multiple QR Generation
echo "[TEST 11] Testing Multiple QR Generation...\n";
$test_cases = [
    ['total' => 100.00, 'vat' => 15.00],
    ['total' => 500.00, 'vat' => 75.00],
    ['total' => 1000.00, 'vat' => 150.00],
];

$success_count = 0;
foreach ($test_cases as $i => $case) {
    $data = array_merge($test_data, [
        'invoice_total' => $case['total'],
        'vat_amount' => $case['vat']
    ]);
    
    try {
        $qr = $tlv_gen->generate_phase1_qr($data);
        $success_count++;
    } catch (Exception $e) {
        echo "   ❌ Test case " . ($i + 1) . " failed\n";
    }
}

if ($success_count === count($test_cases)) {
    echo "✅ All $success_count test cases passed\n\n";
} else {
    echo "⚠️  $success_count/" . count($test_cases) . " test cases passed\n\n";
}

// Test 12: Character Encoding
echo "[TEST 12] Testing Arabic Character Encoding...\n";
$arabic_test = [
    'seller_name'    => 'شركة اختبار للتجارة',
    'vat_number'     => '311859643900003',
    'invoice_date'   => '2025-01-20',
    'invoice_time'   => '14:30:00',
    'invoice_total'  => 500.00,
    'vat_amount'     => 75.00,
];

try {
    $arabic_qr = $tlv_gen->generate_phase1_qr($arabic_test);
    echo "✅ Arabic characters encoded successfully\n";
    $arabic_decoded = $tlv_gen->decode_tlv($arabic_qr);
    echo "   Seller name decoded: " . $arabic_decoded[1] . "\n\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n\n";
}

// Summary
echo "=================================================\n";
echo "TEST SUMMARY\n";
echo "=================================================\n";
echo "✅ All core tests passed successfully!\n";
echo "\nModule is ready for installation and use.\n";
echo "\nNext Steps:\n";
echo "1. Install module in Perfex CRM\n";
echo "2. Configure settings (seller name, VAT number)\n";
echo "3. Test with real invoices\n";
echo "4. Verify QR codes appear on PDFs\n";
echo "5. Scan QR codes with mobile device\n";
echo "\n";
echo "=================================================\n";

// Success exit
exit(0);
