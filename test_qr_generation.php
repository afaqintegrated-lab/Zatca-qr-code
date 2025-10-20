<?php
/**
 * ZATCA QR Code Generation Test Script
 * This script tests if the QR code generation is working correctly
 */

// Basic diagnostics
echo "<h2>ZATCA QR Code Diagnostics</h2>";
echo "<hr>";

// 1. Check if required libraries exist
echo "<h3>1. Library Check</h3>";
$qrlib_path = __DIR__ . '/libraries/phpqrcode/qrlib.php';
if (file_exists($qrlib_path)) {
    echo "✓ QR Code library found at: $qrlib_path<br>";
} else {
    echo "✗ QR Code library NOT found at: $qrlib_path<br>";
}

// 2. Check temp directory
echo "<h3>2. Temp Directory Check</h3>";
$temp_dir = __DIR__ . '/../../temp/';
if (is_dir($temp_dir)) {
    echo "✓ Temp directory exists: $temp_dir<br>";
    if (is_writable($temp_dir)) {
        echo "✓ Temp directory is writable<br>";
    } else {
        echo "✗ Temp directory is NOT writable<br>";
    }
} else {
    echo "✗ Temp directory does NOT exist: $temp_dir<br>";
    echo "Attempting to create...<br>";
    if (mkdir($temp_dir, 0755, true)) {
        echo "✓ Temp directory created successfully<br>";
    } else {
        echo "✗ Failed to create temp directory<br>";
    }
}

// 3. Test QR Code Generation
echo "<h3>3. QR Code Generation Test</h3>";
require_once($qrlib_path);

try {
    // Sample ZATCA data (matching your database)
    $seller_name = 'مؤسسة آفاق المتكاملة لتقنية المعلومات';
    $vat_number = '311859643900003';
    $invoice_datetime_utc = date('Y-m-d\TH:i:s\Z');
    $invoice_total = 100.00;
    $vat_total = 15.00;

    // Generate ZATCA TLV String
    $tlv_array = [
        pack('C', 1), // Tag 1 (Seller Name)
        pack('C', strlen($seller_name)),
        $seller_name,

        pack('C', 2), // Tag 2 (VAT Registration Number)
        pack('C', strlen($vat_number)),
        $vat_number,

        pack('C', 3), // Tag 3 (Timestamp)
        pack('C', strlen($invoice_datetime_utc)),
        $invoice_datetime_utc,

        pack('C', 4), // Tag 4 (Invoice Total with VAT)
        pack('C', strlen(sprintf('%.2f', $invoice_total))),
        sprintf('%.2f', $invoice_total),

        pack('C', 5), // Tag 5 (VAT Total)
        pack('C', strlen(sprintf('%.2f', $vat_total))),
        sprintf('%.2f', $vat_total),
    ];

    $tlv_string_binary = implode('', $tlv_array);
    $qr_content_base64 = base64_encode($tlv_string_binary);

    echo "✓ TLV data generated successfully<br>";
    echo "Base64 Content: " . substr($qr_content_base64, 0, 50) . "...<br>";

    // Generate QR Code
    $qr_size_pixels = 200;
    $module_size = max(1, round($qr_size_pixels / 25));
    $quiet_zone = 2;

    $temp_png_file = $temp_dir . 'zatca_qr_test_' . uniqid() . '.png';

    QRcode::png($qr_content_base64, $temp_png_file, QR_ECLEVEL_L, $module_size, $quiet_zone);

    if (file_exists($temp_png_file)) {
        echo "✓ QR Code image generated successfully at: $temp_png_file<br>";

        // Create base64 image
        $qr_image_data = 'data:image/png;base64,' . base64_encode(file_get_contents($temp_png_file));

        echo "<h3>4. Generated QR Code Preview</h3>";
        echo '<img src="' . $qr_image_data . '" style="border: 2px solid #ccc; padding: 10px;" alt="ZATCA QR Code"><br>';

        // Clean up
        @unlink($temp_png_file);
        echo "✓ Temp file cleaned up<br>";
    } else {
        echo "✗ Failed to generate QR Code image<br>";
    }

} catch (Exception $e) {
    echo "✗ Error during QR code generation: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Test Complete</h3>";
echo "<p>If all checks passed and you see a QR code above, the module is working correctly!</p>";
?>
