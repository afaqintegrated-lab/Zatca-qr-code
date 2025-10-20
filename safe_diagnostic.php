<?php
/**
 * Safe ZATCA Diagnostic Script
 * This version has better error handling to prevent server errors
 */

// Prevent any errors from crashing the page
error_reporting(0);
ini_set('display_errors', 0);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZATCA Diagnostics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
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
            margin-top: 25px;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
        }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 ZATCA QR Code Safe Diagnostics</h1>
    <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

<?php

function safeCheck($callback, $errorMessage = "Check failed") {
    try {
        return $callback();
    } catch (Exception $e) {
        return "<span class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</span>";
    }
}

// 1. Basic Server Info
echo "<h2>1. Server Environment</h2>";
echo "<table>";
echo "<tr><th>Item</th><th>Value</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Server Software</td><td>" . (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown') . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Unknown') . "</td></tr>";
echo "<tr><td>Script Path</td><td>" . __FILE__ . "</td></tr>";
echo "</table>";

// 2. PHP Extensions
echo "<h2>2. Required PHP Extensions</h2>";
echo "<table>";
echo "<tr><th>Extension</th><th>Status</th></tr>";

$extensions = array('gd', 'mysqli', 'mbstring', 'openssl', 'json');
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<tr><td>$ext</td><td class='" . ($loaded ? 'success' : 'error') . "'>" . ($loaded ? '✓ Loaded' : '✗ Not Loaded') . "</td></tr>";
}
echo "</table>";

// 3. File Checks
echo "<h2>3. Module Files</h2>";
echo "<table>";
echo "<tr><th>File</th><th>Status</th></tr>";

$module_dir = __DIR__;

$files = array(
    'zatca_qr_code.php',
    'models/Zatca_qr_code_model.php',
    'controllers/Admin.php',
    'libraries/phpqrcode/qrlib.php',
    'install.php',
    'installnew.php',
);

foreach ($files as $file) {
    $path = $module_dir . '/' . $file;
    $exists = file_exists($path);
    $readable = $exists && is_readable($path);

    if ($exists && $readable) {
        $status = "<span class='success'>✓ OK</span>";
    } elseif ($exists) {
        $status = "<span class='warning'>⚠ Exists but not readable</span>";
    } else {
        $status = "<span class='error'>✗ Not found</span>";
    }

    echo "<tr><td>$file</td><td>$status</td></tr>";
}
echo "</table>";

// 4. Check for code issues
echo "<h2>4. Code Quality Check</h2>";

$model_file = $module_dir . '/models/Zatca_qr_code_model.php';
if (file_exists($model_file)) {
    $content = @file_get_contents($model_file);
    if ($content !== false) {
        $has_trailing_space = strpos($content, "tblacc_zatca_qr_settings '") !== false;
        $has_old_comment = strpos($content, "// return") !== false;

        echo "<table>";
        echo "<tr><th>Check</th><th>Status</th></tr>";
        echo "<tr><td>Trailing Space Bug</td><td class='" . ($has_trailing_space ? 'error' : 'success') . "'>" . ($has_trailing_space ? '✗ STILL EXISTS - File not updated!' : '✓ Fixed') . "</td></tr>";
        echo "<tr><td>Code Cleanup</td><td class='" . ($has_old_comment ? 'warning' : 'success') . "'>" . ($has_old_comment ? '⚠ Old comments still present' : '✓ Clean') . "</td></tr>";
        echo "</table>";

        if ($has_trailing_space) {
            echo "<div class='info'><strong>🚨 CRITICAL:</strong> The model file still has the bug! You uploaded the OLD file, not the FIXED one.</div>";
        }
    } else {
        echo "<p class='error'>Cannot read model file</p>";
    }
} else {
    echo "<p class='error'>Model file not found</p>";
}

// 5. Database Connection (Safe attempt)
echo "<h2>5. Database Connection</h2>";

$db_config_paths = array(
    $module_dir . '/../../application/config/database.php',
    dirname(dirname($module_dir)) . '/application/config/database.php',
);

$db_config_file = null;
foreach ($db_config_paths as $path) {
    if (file_exists($path)) {
        $db_config_file = $path;
        break;
    }
}

if ($db_config_file) {
    echo "<p class='success'>✓ Found database config</p>";

    // Try to include it safely
    $db = array();
    @include($db_config_file);

    if (isset($db['default'])) {
        $dbconfig = $db['default'];

        echo "<table>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        echo "<tr><td>Host</td><td>" . htmlspecialchars($dbconfig['hostname']) . "</td></tr>";
        echo "<tr><td>Database</td><td>" . htmlspecialchars($dbconfig['database']) . "</td></tr>";
        echo "<tr><td>Username</td><td>" . htmlspecialchars($dbconfig['username']) . "</td></tr>";
        echo "<tr><td>Prefix</td><td>" . htmlspecialchars($dbconfig['dbprefix']) . "</td></tr>";
        echo "</table>";

        // Try connection
        if (function_exists('mysqli_connect')) {
            $mysqli = @new mysqli(
                $dbconfig['hostname'],
                $dbconfig['username'],
                $dbconfig['password'],
                $dbconfig['database']
            );

            if ($mysqli->connect_error) {
                echo "<p class='error'>✗ Connection failed: " . htmlspecialchars($mysqli->connect_error) . "</p>";
            } else {
                echo "<p class='success'>✓ Database connected</p>";

                // Check table
                $table = 'tblacc_zatca_qr_settings';
                $result = @$mysqli->query("SHOW TABLES LIKE '$table'");

                if ($result && $result->num_rows > 0) {
                    echo "<p class='success'>✓ Table '$table' exists</p>";

                    // Get settings
                    $settings = @$mysqli->query("SELECT * FROM $table LIMIT 1");
                    if ($settings && $settings->num_rows > 0) {
                        $row = $settings->fetch_assoc();

                        echo "<h3>Current Settings:</h3>";
                        echo "<table>";
                        echo "<tr><th>Setting</th><th>Value</th></tr>";
                        echo "<tr><td>Enable QR</td><td class='" . ($row['enable_qr'] == 1 ? 'success' : 'error') . "'>" . ($row['enable_qr'] == 1 ? '✓ ENABLED' : '✗ DISABLED') . "</td></tr>";
                        echo "<tr><td>Seller Name</td><td>" . htmlspecialchars($row['seller_name']) . "</td></tr>";
                        echo "<tr><td>VAT Number</td><td>" . htmlspecialchars($row['vat_number']) . "</td></tr>";
                        echo "<tr><td>QR Size</td><td>" . htmlspecialchars($row['qr_size']) . "</td></tr>";
                        echo "</table>";

                        if ($row['enable_qr'] != 1) {
                            echo "<div class='info'><strong>⚠️ WARNING:</strong> QR generation is DISABLED! You need to enable it.</div>";
                        }

                    } else {
                        echo "<p class='error'>✗ No data in settings table</p>";
                    }
                } else {
                    echo "<p class='error'>✗ Table '$table' does not exist</p>";
                }

                $mysqli->close();
            }
        } else {
            echo "<p class='error'>✗ MySQLi extension not available</p>";
        }
    }
} else {
    echo "<p class='error'>✗ Cannot find database config file</p>";
}

// 6. Simple QR Test
echo "<h2>6. QR Library Test</h2>";

$qrlib = $module_dir . '/libraries/phpqrcode/qrlib.php';
if (file_exists($qrlib) && extension_loaded('gd')) {
    try {
        require_once($qrlib);

        $test_data = "ZATCA Test " . time();
        $temp_file = sys_get_temp_dir() . '/test_' . uniqid() . '.png';

        @QRcode::png($test_data, $temp_file, QR_ECLEVEL_L, 4, 2);

        if (file_exists($temp_file)) {
            echo "<p class='success'>✓ QR library works!</p>";
            echo "<div style='text-align:center;padding:20px;background:#f9f9f9;'>";
            echo "<img src='data:image/png;base64," . base64_encode(file_get_contents($temp_file)) . "' style='border:2px solid #333;padding:10px;background:white;'>";
            echo "<p>Test QR generated successfully</p>";
            echo "</div>";
            @unlink($temp_file);
        } else {
            echo "<p class='error'>✗ Failed to generate QR file</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    if (!file_exists($qrlib)) {
        echo "<p class='error'>✗ QR library not found</p>";
    } elseif (!extension_loaded('gd')) {
        echo "<p class='error'>✗ GD extension not loaded</p>";
    }
}

// 7. Recommendations
echo "<h2>7. Action Items</h2>";
echo "<div class='info'>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>If trailing space bug exists:</strong> Re-upload the FIXED model file</li>";
echo "<li><strong>If enable_qr is 0:</strong> Go to module settings and enable QR</li>";
echo "<li><strong>If table doesn't exist:</strong> Deactivate/reactivate module</li>";
echo "<li><strong>Check error logs at:</strong> application/logs/</li>";
echo "<li><strong>Clear cache:</strong> Setup → Settings → Clear Cache</li>";
echo "</ol>";
echo "</div>";

?>

<p style="text-align:center; margin-top:40px; color:#999; border-top:1px solid #ddd; padding-top:20px;">
    Diagnostic completed successfully
</p>

</div>
</body>
</html>
