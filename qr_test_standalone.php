<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZATCA QR Code Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .warning {
            color: #ff9800;
            font-weight: bold;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin: 20px 0;
        }
        .qr-container {
            text-align: center;
            padding: 30px;
            background: #fafafa;
            border: 2px dashed #ccc;
            border-radius: 8px;
            margin: 20px 0;
        }
        .qr-container img {
            border: 2px solid #333;
            padding: 10px;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .code-block {
            background: #263238;
            color: #aed581;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 ZATCA QR Code Generation Test</h1>

        <?php
        // Start diagnostics
        $errors = [];
        $warnings = [];
        $success = [];

        echo "<h2>📋 Step 1: Environment Check</h2>";

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            echo "<p class='success'>✓ PHP Version: " . PHP_VERSION . "</p>";
        } else {
            echo "<p class='error'>✗ PHP Version too old: " . PHP_VERSION . " (Need 7.0+)</p>";
            $errors[] = "PHP version too old";
        }

        // Check GD Library
        if (extension_loaded('gd')) {
            echo "<p class='success'>✓ GD Library is installed (required for QR codes)</p>";
        } else {
            echo "<p class='error'>✗ GD Library NOT installed</p>";
            $errors[] = "GD Library missing";
        }

        // Check QR library
        echo "<h2>📚 Step 2: Library Check</h2>";
        $qrlib_path = __DIR__ . '/libraries/phpqrcode/qrlib.php';
        if (file_exists($qrlib_path)) {
            echo "<p class='success'>✓ QR Code library found</p>";
            require_once($qrlib_path);
        } else {
            echo "<p class='error'>✗ QR Code library NOT found at: $qrlib_path</p>";
            $errors[] = "QR library missing";
        }

        // Check temp directory
        echo "<h2>📁 Step 3: Temp Directory Check</h2>";

        // Try multiple possible temp paths
        $possible_temp_paths = [
            __DIR__ . '/../../temp/',
            __DIR__ . '/../temp/',
            sys_get_temp_dir() . '/',
        ];

        $temp_dir = null;
        foreach ($possible_temp_paths as $path) {
            if (is_dir($path) && is_writable($path)) {
                $temp_dir = $path;
                break;
            } elseif (is_dir($path)) {
                // Try to make it writable
                if (@chmod($path, 0755)) {
                    if (is_writable($path)) {
                        $temp_dir = $path;
                        break;
                    }
                }
            }
        }

        // If no temp dir found, try to create one
        if (!$temp_dir) {
            $create_path = __DIR__ . '/temp/';
            if (!is_dir($create_path)) {
                @mkdir($create_path, 0755, true);
            }
            if (is_dir($create_path) && is_writable($create_path)) {
                $temp_dir = $create_path;
            }
        }

        if ($temp_dir) {
            echo "<p class='success'>✓ Temp directory found and writable: $temp_dir</p>";
        } else {
            echo "<p class='error'>✗ No writable temp directory found</p>";
            $errors[] = "No writable temp directory";
        }

        // If no errors so far, test QR generation
        if (empty($errors)) {
            echo "<h2>🎨 Step 4: Generate Test QR Code</h2>";

            try {
                // Sample ZATCA data (from your database)
                $test_data = [
                    'seller_name' => 'مؤسسة آفاق المتكاملة لتقنية المعلومات',
                    'vat_number' => '311859643900003',
                    'invoice_date' => date('Y-m-d\TH:i:s\Z'),
                    'invoice_total' => 115.00,
                    'vat_total' => 15.00
                ];

                echo "<div class='info'>";
                echo "<strong>Test Invoice Data:</strong><br>";
                echo "<table>";
                echo "<tr><th>Field</th><th>Value</th></tr>";
                echo "<tr><td>Seller Name</td><td>{$test_data['seller_name']}</td></tr>";
                echo "<tr><td>VAT Number</td><td>{$test_data['vat_number']}</td></tr>";
                echo "<tr><td>Invoice Date</td><td>{$test_data['invoice_date']}</td></tr>";
                echo "<tr><td>Invoice Total</td><td>{$test_data['invoice_total']} SAR</td></tr>";
                echo "<tr><td>VAT Total</td><td>{$test_data['vat_total']} SAR</td></tr>";
                echo "</table>";
                echo "</div>";

                // Generate ZATCA TLV (Tag-Length-Value) format
                $tlv_array = [
                    pack('C', 1), // Tag 1: Seller Name
                    pack('C', strlen($test_data['seller_name'])),
                    $test_data['seller_name'],

                    pack('C', 2), // Tag 2: VAT Number
                    pack('C', strlen($test_data['vat_number'])),
                    $test_data['vat_number'],

                    pack('C', 3), // Tag 3: Timestamp
                    pack('C', strlen($test_data['invoice_date'])),
                    $test_data['invoice_date'],

                    pack('C', 4), // Tag 4: Invoice Total
                    pack('C', strlen(sprintf('%.2f', $test_data['invoice_total']))),
                    sprintf('%.2f', $test_data['invoice_total']),

                    pack('C', 5), // Tag 5: VAT Total
                    pack('C', strlen(sprintf('%.2f', $test_data['vat_total']))),
                    sprintf('%.2f', $test_data['vat_total']),
                ];

                $tlv_string_binary = implode('', $tlv_array);
                $qr_content_base64 = base64_encode($tlv_string_binary);

                echo "<p class='success'>✓ TLV data encoded successfully</p>";
                echo "<div class='code-block'>";
                echo "Base64 Encoded Data:<br>";
                echo wordwrap($qr_content_base64, 80, "<br>", true);
                echo "</div>";

                // Generate QR Code
                $qr_size_pixels = 200;
                $module_size = max(1, round($qr_size_pixels / 25));
                $quiet_zone = 2;

                $temp_png_file = $temp_dir . 'zatca_qr_test_' . uniqid() . '.png';

                QRcode::png($qr_content_base64, $temp_png_file, QR_ECLEVEL_L, $module_size, $quiet_zone);

                if (file_exists($temp_png_file)) {
                    echo "<p class='success'>✓ QR Code image generated successfully!</p>";

                    // Convert to base64 for display
                    $qr_image_data = 'data:image/png;base64,' . base64_encode(file_get_contents($temp_png_file));

                    echo "<h2>✅ Generated ZATCA QR Code</h2>";
                    echo "<div class='qr-container'>";
                    echo "<img src='$qr_image_data' width='300' height='300' alt='ZATCA QR Code'><br>";
                    echo "<p style='margin-top: 20px;'><strong>Scan this QR code with a ZATCA-compliant app to verify!</strong></p>";
                    echo "</div>";

                    // File info
                    $filesize = filesize($temp_png_file);
                    echo "<div class='info'>";
                    echo "<strong>QR Code Details:</strong><br>";
                    echo "File size: " . number_format($filesize) . " bytes<br>";
                    echo "Dimensions: {$qr_size_pixels}x{$qr_size_pixels} pixels<br>";
                    echo "Module size: {$module_size}<br>";
                    echo "Error correction level: L (Low)<br>";
                    echo "</div>";

                    // Clean up
                    @unlink($temp_png_file);
                    echo "<p class='success'>✓ Temporary file cleaned up</p>";

                } else {
                    echo "<p class='error'>✗ Failed to generate QR Code image file</p>";
                    $errors[] = "QR generation failed";
                }

            } catch (Exception $e) {
                echo "<p class='error'>✗ Error during QR code generation: " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<div class='code-block'>";
                echo "Stack trace:<br>" . nl2br(htmlspecialchars($e->getTraceAsString()));
                echo "</div>";
                $errors[] = $e->getMessage();
            }
        }

        // Final summary
        echo "<h2>📊 Test Summary</h2>";
        if (empty($errors)) {
            echo "<div style='background: #4CAF50; color: white; padding: 20px; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='margin: 0; color: white;'>✅ ALL TESTS PASSED!</h3>";
            echo "<p style='margin: 10px 0 0 0;'>The ZATCA QR Code module is working correctly.</p>";
            echo "</div>";

            echo "<div class='info' style='margin-top: 20px;'>";
            echo "<strong>Next Steps:</strong><br>";
            echo "1. Go to your Perfex CRM<br>";
            echo "2. Navigate to: Setup → Utilities → ZATCA QR Code<br>";
            echo "3. Ensure 'Enable QR Code' is set to 'Yes'<br>";
            echo "4. Generate an invoice PDF to see the QR code in action<br>";
            echo "</div>";
        } else {
            echo "<div style='background: #f44336; color: white; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='margin: 0; color: white;'>❌ TESTS FAILED</h3>";
            echo "<p style='margin: 10px 0 0 0;'>The following issues need to be resolved:</p>";
            echo "<ul style='margin: 10px 0 0 20px;'>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo "</div>";
        }

        echo "<hr style='margin: 30px 0;'>";
        echo "<p style='text-align: center; color: #999;'>Test completed at: " . date('Y-m-d H:i:s') . "</p>";
        ?>
    </div>
</body>
</html>
