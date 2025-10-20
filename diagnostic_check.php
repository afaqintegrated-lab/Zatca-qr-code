<?php
/**
 * ZATCA QR Code Diagnostic Script
 * Upload this file to: modules/zatca_qr_code/diagnostic_check.php
 * Access via: https://yoursite.com/modules/zatca_qr_code/diagnostic_check.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>ZATCA QR Code Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; background: #e8f5e9; padding: 10px; border-radius: 4px; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #4CAF50; color: white; }
        .code { background: #263238; color: #aed581; padding: 15px; border-radius: 4px; overflow-x: auto; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 ZATCA QR Code Diagnostic Report</h1>
    <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

<?php

// Define Perfex paths - try to detect automatically
$possible_paths = [
    __DIR__ . '/../../',  // If in modules/zatca_qr_code/
    __DIR__ . '/../../../',  // Alternative structure
    dirname(dirname(dirname(__FILE__))) . '/',  // Another alternative
];

$perfex_root = null;
foreach ($possible_paths as $path) {
    if (file_exists($path . 'application/config/database.php')) {
        $perfex_root = $path;
        break;
    }
}

if (!$perfex_root) {
    echo '<div class="info">⚠️ <strong>Note:</strong> Could not auto-detect Perfex CRM root. Database checks will be skipped.</div>';
}

echo "<h2>1️⃣ Server Environment Check</h2>";

// PHP Version
echo "<table>";
echo "<tr><th>Check</th><th>Status</th><th>Details</th></tr>";

$php_version = phpversion();
$php_ok = version_compare($php_version, '7.0.0', '>=');
echo "<tr><td>PHP Version</td><td class='" . ($php_ok ? 'success' : 'error') . "'>" . ($php_ok ? '✓ PASS' : '✗ FAIL') . "</td><td>$php_version</td></tr>";

// GD Library
$gd_loaded = extension_loaded('gd');
echo "<tr><td>GD Library</td><td class='" . ($gd_loaded ? 'success' : 'error') . "'>" . ($gd_loaded ? '✓ PASS' : '✗ FAIL') . "</td><td>" . ($gd_loaded ? 'Installed' : 'NOT Installed - Required for QR codes!') . "</td></tr>";

// MySQLi
$mysqli_loaded = extension_loaded('mysqli');
echo "<tr><td>MySQLi Extension</td><td class='" . ($mysqli_loaded ? 'success' : 'error') . "'>" . ($mysqli_loaded ? '✓ PASS' : '✗ FAIL') . "</td><td>" . ($mysqli_loaded ? 'Available' : 'NOT Available') . "</td></tr>";

echo "</table>";

// File System Checks
echo "<h2>2️⃣ Module Files Check</h2>";
echo "<table>";
echo "<tr><th>File</th><th>Status</th><th>Path</th></tr>";

$files_to_check = [
    'Main Module File' => __DIR__ . '/zatca_qr_code.php',
    'Model File' => __DIR__ . '/models/Zatca_qr_code_model.php',
    'Admin Controller' => __DIR__ . '/controllers/Admin.php',
    'QR Library' => __DIR__ . '/libraries/phpqrcode/qrlib.php',
];

foreach ($files_to_check as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    echo "<tr><td>$name</td><td class='" . ($exists && $readable ? 'success' : 'error') . "'>" . ($exists && $readable ? '✓ EXISTS & READABLE' : ($exists ? '⚠️ EXISTS BUT NOT READABLE' : '✗ NOT FOUND')) . "</td><td>$path</td></tr>";
}

echo "</table>";

// Check for trailing space issue
echo "<h2>3️⃣ Code Issues Check</h2>";
if (file_exists(__DIR__ . '/models/Zatca_qr_code_model.php')) {
    $model_content = file_get_contents(__DIR__ . '/models/Zatca_qr_code_model.php');

    echo "<table>";
    echo "<tr><th>Check</th><th>Status</th><th>Details</th></tr>";

    // Check for trailing space
    $has_trailing_space = strpos($model_content, "tblacc_zatca_qr_settings '") !== false;
    echo "<tr><td>Trailing Space Bug</td><td class='" . ($has_trailing_space ? 'error' : 'success') . "'>" . ($has_trailing_space ? '✗ FOUND - File not updated!' : '✓ FIXED') . "</td><td>" . ($has_trailing_space ? 'Model file still has the bug' : 'Model file is correct') . "</td></tr>";

    // Check for isset() in zatca_qr_code.php
    if (file_exists(__DIR__ . '/zatca_qr_code.php')) {
        $main_content = file_get_contents(__DIR__ . '/zatca_qr_code.php');
        $has_isset = strpos($main_content, 'isset($settings->qr_size)') !== false;
        echo "<tr><td>Error Handling (main file)</td><td class='" . ($has_isset ? 'success' : 'warning') . "'>" . ($has_isset ? '✓ PRESENT' : '⚠️ MISSING') . "</td><td>" . ($has_isset ? 'Has isset() check' : 'Missing isset() check') . "</td></tr>";
    }

    echo "</table>";
} else {
    echo "<p class='error'>✗ Cannot check code - model file not found</p>";
}

// Temp directory check
echo "<h2>4️⃣ Temp Directory Check</h2>";
$temp_paths_to_check = [
    'Perfex Temp (relative)' => $perfex_root . 'temp/',
    'Module Temp' => __DIR__ . '/temp/',
    'System Temp' => sys_get_temp_dir(),
];

echo "<table>";
echo "<tr><th>Directory</th><th>Status</th><th>Writable</th><th>Path</th></tr>";

foreach ($temp_paths_to_check as $name => $path) {
    $exists = is_dir($path);
    $writable = $exists ? is_writable($path) : false;
    echo "<tr><td>$name</td><td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? '✓ EXISTS' : '✗ NOT FOUND') . "</td><td class='" . ($writable ? 'success' : 'error') . "'>" . ($writable ? '✓ WRITABLE' : '✗ NOT WRITABLE') . "</td><td>$path</td></tr>";
}

echo "</table>";

// Database Check
if ($perfex_root && file_exists($perfex_root . 'application/config/database.php')) {
    echo "<h2>5️⃣ Database Connection Check</h2>";

    try {
        // Include Perfex database config
        include($perfex_root . 'application/config/database.php');

        if (isset($db['default'])) {
            $dbconfig = $db['default'];

            echo "<table>";
            echo "<tr><th>Parameter</th><th>Value</th></tr>";
            echo "<tr><td>Hostname</td><td>{$dbconfig['hostname']}</td></tr>";
            echo "<tr><td>Database</td><td>{$dbconfig['database']}</td></tr>";
            echo "<tr><td>Username</td><td>{$dbconfig['username']}</td></tr>";
            echo "<tr><td>DB Prefix</td><td>{$dbconfig['dbprefix']}</td></tr>";
            echo "</table>";

            // Try to connect
            $mysqli = @new mysqli($dbconfig['hostname'], $dbconfig['username'], $dbconfig['password'], $dbconfig['database']);

            if ($mysqli->connect_error) {
                echo "<p class='error'>✗ Database Connection Failed: " . htmlspecialchars($mysqli->connect_error) . "</p>";
            } else {
                echo "<p class='success'>✓ Database Connected Successfully</p>";

                echo "<h2>6️⃣ ZATCA Settings Table Check</h2>";

                // Check if table exists
                $table_name = 'tblacc_zatca_qr_settings';
                $result = $mysqli->query("SHOW TABLES LIKE '$table_name'");

                if ($result && $result->num_rows > 0) {
                    echo "<p class='success'>✓ Table '$table_name' exists</p>";

                    // Get table structure
                    echo "<h3>Table Structure:</h3>";
                    $columns = $mysqli->query("DESCRIBE $table_name");
                    if ($columns) {
                        echo "<table>";
                        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
                        while ($col = $columns->fetch_assoc()) {
                            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>" . ($col['Default'] ?? 'NULL') . "</td></tr>";
                        }
                        echo "</table>";
                    }

                    // Get current settings
                    echo "<h3>Current Settings Data:</h3>";
                    $settings = $mysqli->query("SELECT * FROM $table_name");

                    if ($settings && $settings->num_rows > 0) {
                        echo "<table>";
                        echo "<tr><th>ID</th><th>Enable QR</th><th>Seller Name</th><th>VAT Number</th><th>QR Size</th></tr>";
                        while ($row = $settings->fetch_assoc()) {
                            $enable_status = $row['enable_qr'] == 1 ? '<span class="success">✓ ENABLED</span>' : '<span class="error">✗ DISABLED</span>';
                            echo "<tr>";
                            echo "<td>{$row['id']}</td>";
                            echo "<td>$enable_status</td>";
                            echo "<td>" . htmlspecialchars($row['seller_name']) . "</td>";
                            echo "<td>{$row['vat_number']}</td>";
                            echo "<td>{$row['qr_size']}</td>";
                            echo "</tr>";
                        }
                        echo "</table>";

                        // Check if enabled
                        $settings->data_seek(0);
                        $current_settings = $settings->fetch_assoc();

                        if ($current_settings['enable_qr'] != 1) {
                            echo "<div class='info'><strong>⚠️ WARNING:</strong> QR Code generation is DISABLED in settings! Enable it in the admin panel.</div>";
                        }

                    } else {
                        echo "<p class='error'>✗ No settings data found in table!</p>";
                        echo "<div class='info'><strong>Fix:</strong> Go to module settings page and save the settings once.</div>";
                    }

                } else {
                    echo "<p class='error'>✗ Table '$table_name' does NOT exist!</p>";
                    echo "<div class='info'><strong>Fix:</strong> Deactivate and reactivate the module to create the table.</div>";
                }

                // Check Perfex modules table
                echo "<h2>7️⃣ Module Activation Check</h2>";
                $modules_result = $mysqli->query("SELECT * FROM {$dbconfig['dbprefix']}modules WHERE module_name = 'zatca_qr_code'");

                if ($modules_result && $modules_result->num_rows > 0) {
                    $module_data = $modules_result->fetch_assoc();
                    $active = $module_data['active'] == 1;
                    echo "<table>";
                    echo "<tr><th>Parameter</th><th>Value</th></tr>";
                    echo "<tr><td>Module Name</td><td>{$module_data['module_name']}</td></tr>";
                    echo "<tr><td>Active Status</td><td class='" . ($active ? 'success' : 'error') . "'>" . ($active ? '✓ ACTIVE' : '✗ INACTIVE') . "</td></tr>";
                    echo "</table>";
                } else {
                    echo "<p class='error'>✗ Module not found in modules table!</p>";
                }

                $mysqli->close();
            }
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error checking database: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Test QR Generation
echo "<h2>8️⃣ QR Code Generation Test</h2>";

if (file_exists(__DIR__ . '/libraries/phpqrcode/qrlib.php') && extension_loaded('gd')) {
    try {
        require_once(__DIR__ . '/libraries/phpqrcode/qrlib.php');

        $test_data = "Test QR Code - " . date('Y-m-d H:i:s');
        $temp_file = sys_get_temp_dir() . '/test_qr_' . uniqid() . '.png';

        QRcode::png($test_data, $temp_file, QR_ECLEVEL_L, 4, 2);

        if (file_exists($temp_file)) {
            $image_data = base64_encode(file_get_contents($temp_file));
            echo "<p class='success'>✓ QR Code library is working!</p>";
            echo "<div style='text-align:center; padding:20px; background:#f5f5f5; border-radius:8px;'>";
            echo "<img src='data:image/png;base64,$image_data' style='border:2px solid #333; padding:10px; background:white;'>";
            echo "<p>Test QR Code Generated Successfully</p>";
            echo "</div>";
            @unlink($temp_file);
        } else {
            echo "<p class='error'>✗ QR Code file was not created</p>";
        }

    } catch (Exception $e) {
        echo "<p class='error'>✗ QR Generation Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>✗ Cannot test QR generation - missing library or GD extension</p>";
}

// Recommendations
echo "<h2>9️⃣ Recommendations & Next Steps</h2>";
echo "<div class='info'>";
echo "<h3>Based on the diagnostic results:</h3>";
echo "<ol>";
echo "<li>If table doesn't exist: Deactivate and reactivate the module</li>";
echo "<li>If 'enable_qr' is 0: Go to Setup → Utilities → ZATCA QR Code and enable it</li>";
echo "<li>If files are not readable: Check file permissions (should be 644)</li>";
echo "<li>If temp directory is not writable: Run: <code>chmod 755 temp/</code></li>";
echo "<li>If GD library is missing: Install PHP GD extension</li>";
echo "<li>If trailing space bug exists: Re-upload the model file</li>";
echo "<li>Clear Perfex cache after making changes</li>";
echo "<li>Check Perfex error logs at: application/logs/</li>";
echo "</ol>";
echo "</div>";

echo "<h2>📋 Quick Fixes</h2>";
echo "<div class='code'>";
echo "# Fix file permissions:\n";
echo "chmod 644 modules/zatca_qr_code/zatca_qr_code.php\n";
echo "chmod 644 modules/zatca_qr_code/models/Zatca_qr_code_model.php\n";
echo "chmod 644 modules/zatca_qr_code/controllers/Admin.php\n\n";
echo "# Make temp writable:\n";
echo "chmod 755 temp/\n\n";
echo "# Enable QR in database:\n";
echo "UPDATE tblacc_zatca_qr_settings SET enable_qr = 1 WHERE id = 1;\n";
echo "</div>";

?>

<p style="text-align:center; margin-top:40px; color:#999;">
    Diagnostic completed. Share this report if you need further assistance.
</p>

</div>
</body>
</html>
