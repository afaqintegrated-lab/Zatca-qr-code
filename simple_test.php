<?php
/**
 * Simple ZATCA Test - Access from Admin Panel
 * Upload to: modules/zatca_qr_code/simple_test.php
 * Access: https://yoursite.com/admin/utilities/zatca_qr_code
 * Then manually go to: https://yoursite.com/modules/zatca_qr_code/simple_test.php
 */

echo "<h1>ZATCA Quick Test</h1>";
echo "<p>If you can see this, the file is accessible!</p>";
echo "<hr>";

echo "<h2>Your Access URL is:</h2>";
echo "<p style='background:yellow; padding:10px; font-size:16px;'>";
echo "<strong>" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}</strong>";
echo "</p>";

echo "<p>Bookmark this URL for future access!</p>";
echo "<hr>";

echo "<h2>Quick Checks:</h2>";

// Check 1: PHP Version
echo "<p>PHP Version: <strong>" . phpversion() . "</strong></p>";

// Check 2: GD Library
echo "<p>GD Library: <strong>" . (extension_loaded('gd') ? '✓ Installed' : '✗ NOT Installed') . "</strong></p>";

// Check 3: Files exist
$files = [
    'Main File' => 'zatca_qr_code.php',
    'Model' => 'models/Zatca_qr_code_model.php',
    'Controller' => 'controllers/Admin.php',
    'QR Library' => 'libraries/phpqrcode/qrlib.php',
];

echo "<h3>Files Check:</h3>";
foreach ($files as $name => $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    echo "<p>$name: <strong>" . ($exists ? '✓ Found' : '✗ Missing') . "</strong></p>";
}

// Check 4: Try to load Perfex
echo "<hr>";
echo "<h3>Trying to connect to Perfex CRM...</h3>";

$perfex_paths = [
    __DIR__ . '/../../index.php',
    __DIR__ . '/../../../index.php',
];

$found = false;
foreach ($perfex_paths as $path) {
    if (file_exists($path)) {
        echo "<p style='color:green;'>✓ Found Perfex at: $path</p>";
        $found = true;
        break;
    }
}

if (!$found) {
    echo "<p style='color:red;'>✗ Could not locate Perfex installation</p>";
}

echo "<hr>";
echo "<h3>Next Step:</h3>";
echo "<p>1. Note down the URL shown in yellow above</p>";
echo "<p>2. Replace 'simple_test.php' with 'diagnostic_check.php' in the URL</p>";
echo "<p>3. Access that URL to run full diagnostics</p>";

?>
