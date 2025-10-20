<?php
/**
 * ZATCA QR Code Integration Test
 * Upload to: modules/zatca_qr_code/test_integration.php
 * Access: https://afaqinfotech.com/erp/modules/zatca_qr_code/test_integration.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZATCA Integration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 25px; background: #e8f5e9; padding: 10px; border-radius: 4px; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #4CAF50; color: white; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .test-pass { background: #d4edda; border-left: 4px solid #28a745; }
        .test-fail { background: #f8d7da; border-left: 4px solid #dc3545; }
        .code { background: #263238; color: #aed581; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔬 ZATCA QR Code Integration Test</h1>
    <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

<?php

// Try to bootstrap Perfex
$perfex_bootstrapped = false;
$CI = null;

$possible_paths = [
    __DIR__ . '/../../index.php',
    __DIR__ . '/../../../index.php',
];

foreach ($possible_paths as $index_path) {
    if (file_exists($index_path)) {
        define('ENVIRONMENT', 'development');

        // Try to load Perfex
        try {
            require_once(dirname($index_path) . '/application/libraries/App_Controller.php');
            $perfex_bootstrapped = true;
            break;
        } catch (Exception $e) {
            // Continue trying
        }
    }
}

echo "<h2>Test 1: Module Files</h2>";
echo "<table>";
echo "<tr><th>File</th><th>Status</th><th>Details</th></tr>";

$critical_files = [
    'Main Module' => 'zatca_qr_code.php',
    'Model' => 'models/Zatca_qr_code_model.php',
    'Controller' => 'controllers/Admin.php',
    'QR Library' => 'libraries/phpqrcode/qrlib.php',
    'JavaScript' => 'assets/js/script.js',
    'Invoice Template' => 'views/invoicepdf.php',
];

$all_files_exist = true;
foreach ($critical_files as $name => $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $all_files_exist = $all_files_exist && $exists;

    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? '✓ EXISTS' : '✗ MISSING') . "</td>";
    echo "<td>" . ($exists ? filesize($path) . ' bytes' : 'Not found') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<div class='test-result " . ($all_files_exist ? 'test-pass' : 'test-fail') . "'>";
echo $all_files_exist ? '✓ All module files exist' : '✗ Some files are missing';
echo "</div>";

// Test 2: Hook Registration
echo "<h2>Test 2: Hook Registration Check</h2>";

$main_file = __DIR__ . '/zatca_qr_code.php';
if (file_exists($main_file)) {
    $main_content = file_get_contents($main_file);

    $hooks_to_check = [
        'invoice_html_pdf_data' => "hooks()->add_filter('invoice_html_pdf_data'",
        'pdf_invoice_html_view_path' => "hooks()->add_filter('pdf_invoice_html_view_path'",
        'admin_init' => "hooks()->add_action('admin_init'",
    ];

    echo "<table>";
    echo "<tr><th>Hook</th><th>Status</th></tr>";

    $all_hooks_registered = true;
    foreach ($hooks_to_check as $hook_name => $hook_code) {
        $registered = strpos($main_content, $hook_code) !== false;
        $all_hooks_registered = $all_hooks_registered && $registered;

        echo "<tr>";
        echo "<td>$hook_name</td>";
        echo "<td class='" . ($registered ? 'success' : 'error') . "'>" . ($registered ? '✓ REGISTERED' : '✗ NOT FOUND') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<div class='test-result " . ($all_hooks_registered ? 'test-pass' : 'test-fail') . "'>";
    echo $all_hooks_registered ? '✓ All hooks are registered' : '✗ Some hooks are missing';
    echo "</div>";
}

// Test 3: Database Check
echo "<h2>Test 3: Database Settings</h2>";

$db_config_paths = [
    __DIR__ . '/../../application/config/database.php',
    dirname(dirname(__DIR__)) . '/application/config/database.php',
];

$db_config_file = null;
foreach ($db_config_paths as $path) {
    if (file_exists($path)) {
        $db_config_file = $path;
        break;
    }
}

if ($db_config_file) {
    echo "<p class='success'>✓ Found database config</p>";

    $db = [];
    @include($db_config_file);

    if (isset($db['default'])) {
        $dbconfig = $db['default'];

        $mysqli = @new mysqli(
            $dbconfig['hostname'],
            $dbconfig['username'],
            $dbconfig['password'],
            $dbconfig['database']
        );

        if (!$mysqli->connect_error) {
            echo "<p class='success'>✓ Database connected</p>";

            // Check settings table
            $table = 'tblacc_zatca_qr_settings';
            $result = @$mysqli->query("SHOW TABLES LIKE '$table'");

            if ($result && $result->num_rows > 0) {
                echo "<p class='success'>✓ Settings table exists</p>";

                // Get settings
                $settings = @$mysqli->query("SELECT * FROM $table LIMIT 1");
                if ($settings && $settings->num_rows > 0) {
                    $row = $settings->fetch_assoc();

                    echo "<h3>Current Settings:</h3>";
                    echo "<table>";
                    echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

                    $qr_enabled = $row['enable_qr'] == 1;
                    $has_seller = !empty($row['seller_name']);
                    $has_vat = !empty($row['vat_number']);

                    echo "<tr><td>Enable QR</td><td>" . ($qr_enabled ? 'Yes' : 'No') . "</td>";
                    echo "<td class='" . ($qr_enabled ? 'success' : 'error') . "'>" . ($qr_enabled ? '✓ ENABLED' : '✗ DISABLED') . "</td></tr>";

                    echo "<tr><td>Seller Name</td><td>" . htmlspecialchars(substr($row['seller_name'], 0, 50)) . "</td>";
                    echo "<td class='" . ($has_seller ? 'success' : 'error') . "'>" . ($has_seller ? '✓ SET' : '✗ EMPTY') . "</td></tr>";

                    echo "<tr><td>VAT Number</td><td>" . htmlspecialchars($row['vat_number']) . "</td>";
                    echo "<td class='" . ($has_vat ? 'success' : 'error') . "'>" . ($has_vat ? '✓ SET' : '✗ EMPTY') . "</td></tr>";

                    echo "<tr><td>QR Size</td><td>" . $row['qr_size'] . " px</td>";
                    echo "<td class='success'>✓ OK</td></tr>";

                    echo "</table>";

                    $settings_ok = $qr_enabled && $has_seller && $has_vat;
                    echo "<div class='test-result " . ($settings_ok ? 'test-pass' : 'test-fail') . "'>";
                    echo $settings_ok ? '✓ Settings are configured correctly' : '✗ Settings need to be configured';
                    echo "</div>";

                    if (!$qr_enabled) {
                        echo "<div class='info'><strong>⚠️ ACTION REQUIRED:</strong> QR generation is DISABLED! Enable it in settings.</div>";
                    }

                } else {
                    echo "<p class='error'>✗ No settings data in table</p>";
                    echo "<div class='info'><strong>Fix:</strong> Go to module settings and save once.</div>";
                }
            } else {
                echo "<p class='error'>✗ Settings table does not exist</p>";
                echo "<div class='info'><strong>Fix:</strong> Deactivate and reactivate the module.</div>";
            }

            // Check module activation
            echo "<h3>Module Activation:</h3>";
            $module_check = @$mysqli->query("SELECT * FROM {$dbconfig['dbprefix']}modules WHERE module_name = 'zatca_qr_code'");

            if ($module_check && $module_check->num_rows > 0) {
                $module = $module_check->fetch_assoc();
                $is_active = $module['active'] == 1;

                echo "<table>";
                echo "<tr><th>Property</th><th>Value</th></tr>";
                echo "<tr><td>Module Name</td><td>" . $module['module_name'] . "</td></tr>";
                echo "<tr><td>Active</td><td class='" . ($is_active ? 'success' : 'error') . "'>" . ($is_active ? '✓ YES' : '✗ NO') . "</td></tr>";
                echo "</table>";

                echo "<div class='test-result " . ($is_active ? 'test-pass' : 'test-fail') . "'>";
                echo $is_active ? '✓ Module is active' : '✗ Module is NOT active!';
                echo "</div>";
            } else {
                echo "<p class='error'>✗ Module not found in modules table</p>";
            }

            $mysqli->close();
        } else {
            echo "<p class='error'>✗ Database connection failed</p>";
        }
    }
} else {
    echo "<p class='error'>✗ Cannot find database config</p>";
}

// Test 4: QR Generation Capability
echo "<h2>Test 4: QR Code Generation Test</h2>";

if (extension_loaded('gd')) {
    echo "<p class='success'>✓ GD Library loaded</p>";

    $qrlib = __DIR__ . '/libraries/phpqrcode/qrlib.php';
    if (file_exists($qrlib)) {
        require_once($qrlib);

        try {
            $test_data = "ZATCA Test " . time();
            $temp_file = sys_get_temp_dir() . '/zatca_test_' . uniqid() . '.png';

            @QRcode::png($test_data, $temp_file, QR_ECLEVEL_L, 4, 2);

            if (file_exists($temp_file)) {
                echo "<p class='success'>✓ QR code generation works!</p>";
                echo "<div style='text-align:center; padding:20px; background:#f9f9f9; border-radius:8px;'>";
                echo "<img src='data:image/png;base64," . base64_encode(file_get_contents($temp_file)) . "' style='border:2px solid #333; padding:10px; background:white;'>";
                echo "<p>Test QR Code Generated Successfully</p>";
                echo "</div>";
                @unlink($temp_file);

                echo "<div class='test-result test-pass'>✓ QR library is fully functional</div>";
            } else {
                echo "<p class='error'>✗ QR file was not created</p>";
                echo "<div class='test-result test-fail'>✗ QR generation failed</div>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<div class='test-result test-fail'>✗ QR generation error</div>";
        }
    } else {
        echo "<p class='error'>✗ QR library not found</p>";
    }
} else {
    echo "<p class='error'>✗ GD Library NOT loaded - required for QR codes!</p>";
    echo "<div class='test-result test-fail'>✗ GD extension missing</div>";
}

// Test 5: PDF Template Check
echo "<h2>Test 5: PDF Template Integration</h2>";

$theme_paths = [
    dirname(dirname(dirname(__FILE__))) . '/application/views/themes/perfex/views/invoices/invoicepdf.php',
    dirname(dirname(dirname(__FILE__))) . '/application/views/themes/default/views/invoices/invoicepdf.php',
];

echo "<table>";
echo "<tr><th>Location</th><th>Status</th></tr>";

$template_copied = false;
foreach ($theme_paths as $path) {
    $exists = file_exists($path);
    $template_copied = $template_copied || $exists;

    echo "<tr>";
    echo "<td>" . basename(dirname(dirname($path))) . " theme</td>";
    echo "<td class='" . ($exists ? 'success' : 'warning') . "'>" . ($exists ? '✓ Template exists' : '⚠️ Not found') . "</td>";
    echo "</tr>";
}
echo "</table>";

if ($template_copied) {
    echo "<div class='test-result test-pass'>✓ PDF template found in theme directory</div>";
    echo "<div class='info'><strong>Good!</strong> The custom PDF template is in place.</div>";
} else {
    echo "<div class='test-result test-fail'>✗ PDF template NOT copied to theme</div>";
    echo "<div class='info'><strong>⚠️ ACTION REQUIRED:</strong> Click 'Copy PDF Templates to Theme Folder' button in module settings!</div>";
}

// Summary
echo "<h2>📊 Test Summary</h2>";

$tests_passed = 0;
$total_tests = 5;

if ($all_files_exist) $tests_passed++;
if (isset($all_hooks_registered) && $all_hooks_registered) $tests_passed++;
if (isset($settings_ok) && $settings_ok) $tests_passed++;
if (extension_loaded('gd') && file_exists($qrlib)) $tests_passed++;
if ($template_copied) $tests_passed++;

$pass_percentage = ($tests_passed / $total_tests) * 100;

echo "<div style='text-align:center; padding:30px; background:" . ($pass_percentage == 100 ? '#d4edda' : ($pass_percentage >= 60 ? '#fff3cd' : '#f8d7da')) . "; border-radius:8px;'>";
echo "<h1 style='margin:0; color:" . ($pass_percentage == 100 ? '#155724' : ($pass_percentage >= 60 ? '#856404' : '#721c24')) . ";'>$tests_passed / $total_tests Tests Passed</h1>";
echo "<p style='font-size:48px; margin:10px 0;'>" . round($pass_percentage) . "%</p>";
echo "</div>";

// Action Items
echo "<h2>🎯 Action Items</h2>";
echo "<div class='info'>";
echo "<ol style='line-height: 2;'>";

if (!isset($qr_enabled) || !$qr_enabled) {
    echo "<li><strong>Enable QR in Settings:</strong> Go to module settings and set 'Enable QR' to YES</li>";
}

if (!$template_copied) {
    echo "<li><strong>Copy PDF Templates:</strong> Click 'Copy PDF Templates to Theme Folder' button in settings</li>";
}

if (!isset($is_active) || !$is_active) {
    echo "<li><strong>Activate Module:</strong> Go to Setup → Modules and activate ZATCA QR Code</li>";
}

if (!isset($settings_ok) || !$settings_ok) {
    echo "<li><strong>Configure Settings:</strong> Fill in Seller Name and VAT Number in settings</li>";
}

echo "<li><strong>Clear Cache:</strong> Go to Setup → Settings → Clear Cache</li>";
echo "<li><strong>Test Invoice PDF:</strong> Generate an invoice PDF and check for QR code</li>";
echo "</ol>";
echo "</div>";

?>

<p style="text-align:center; margin-top:40px; padding-top:20px; border-top:1px solid #ddd; color:#999;">
    Integration test completed. Fix any failed items above.
</p>

</div>
</body>
</html>
