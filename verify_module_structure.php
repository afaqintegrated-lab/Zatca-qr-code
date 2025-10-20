<?php
/**
 * ZATCA Invoice QR Module Structure Verification Script
 * 
 * This script verifies that the module has the correct structure
 * for Perfex CRM installation.
 * 
 * Usage: Place this file in the same directory as the module folder
 *        and run: php verify_module_structure.php
 */

echo "====================================\n";
echo "ZATCA Module Structure Verification\n";
echo "====================================\n\n";

$module_dir = __DIR__ . '/zatca_invoice_qr';

// Check if module directory exists
if (!is_dir($module_dir)) {
    echo "❌ ERROR: Module directory 'zatca_invoice_qr' not found!\n";
    echo "   Expected path: $module_dir\n";
    exit(1);
}

echo "✅ Module directory found: $module_dir\n\n";

// Required files check
$required_files = [
    'zatca_invoice_qr.php' => 'Main module file',
    'install.php' => 'Installation script',
    'uninstall.php' => 'Uninstallation script',
];

echo "Checking required files:\n";
echo "------------------------\n";

$all_files_exist = true;
foreach ($required_files as $file => $description) {
    $filepath = $module_dir . '/' . $file;
    if (file_exists($filepath)) {
        $size = filesize($filepath);
        echo "✅ $file ($size bytes) - $description\n";
    } else {
        echo "❌ MISSING: $file - $description\n";
        $all_files_exist = false;
    }
}

echo "\n";

// Required directories check
$required_dirs = [
    'controllers' => 'Controller files',
    'models' => 'Model files',
    'views' => 'View templates',
    'libraries' => 'Library files',
    'assets' => 'CSS/JS/Images',
    'language' => 'Language files',
    'helpers' => 'Helper functions',
];

echo "Checking required directories:\n";
echo "------------------------------\n";

$all_dirs_exist = true;
foreach ($required_dirs as $dir => $description) {
    $dirpath = $module_dir . '/' . $dir;
    if (is_dir($dirpath)) {
        $count = count(scandir($dirpath)) - 2; // Exclude . and ..
        echo "✅ $dir/ ($count items) - $description\n";
    } else {
        echo "❌ MISSING: $dir/ - $description\n";
        $all_dirs_exist = false;
    }
}

echo "\n";

// Check main module file structure
echo "Analyzing main module file:\n";
echo "---------------------------\n";

$main_file = $module_dir . '/zatca_invoice_qr.php';
if (file_exists($main_file)) {
    $content = file_get_contents($main_file);
    
    // Check for required module header
    if (preg_match('/Module Name:\s*(.+)/i', $content, $matches)) {
        echo "✅ Module Name: " . trim($matches[1]) . "\n";
    } else {
        echo "❌ WARNING: Module Name not found in header\n";
    }
    
    if (preg_match('/Version:\s*([\d.]+)/i', $content, $matches)) {
        echo "✅ Version: " . trim($matches[1]) . "\n";
    } else {
        echo "❌ WARNING: Version not found in header\n";
    }
    
    if (preg_match('/Description:\s*(.+)/i', $content, $matches)) {
        echo "✅ Description: " . trim($matches[1]) . "\n";
    } else {
        echo "❌ WARNING: Description not found in header\n";
    }
    
    // Check for activation hook
    if (strpos($content, 'register_activation_hook') !== false) {
        echo "✅ Activation hook registered\n";
    } else {
        echo "❌ WARNING: Activation hook not found\n";
    }
    
    // Check for deactivation hook
    if (strpos($content, 'register_deactivation_hook') !== false) {
        echo "✅ Deactivation hook registered\n";
    } else {
        echo "⚠️  Optional: Deactivation hook not found\n";
    }
} else {
    echo "❌ ERROR: Cannot read main module file\n";
}

echo "\n";

// Check install.php structure
echo "Checking installation script:\n";
echo "-----------------------------\n";

$install_file = $module_dir . '/install.php';
if (file_exists($install_file)) {
    $content = file_get_contents($install_file);
    
    if (strpos($content, 'CREATE TABLE') !== false) {
        echo "✅ Contains database table creation\n";
        
        // Count tables
        preg_match_all('/CREATE TABLE.*?`(tbl[^`]+)`/i', $content, $matches);
        if (!empty($matches[1])) {
            echo "✅ Creates " . count($matches[1]) . " table(s):\n";
            foreach ($matches[1] as $table) {
                echo "   - $table\n";
            }
        }
    } else {
        echo "⚠️  WARNING: No table creation found (might be intentional)\n";
    }
} else {
    echo "❌ ERROR: install.php not found\n";
}

echo "\n";

// Check language files
echo "Checking language files:\n";
echo "------------------------\n";

$lang_dir = $module_dir . '/language';
if (is_dir($lang_dir)) {
    $languages = array_diff(scandir($lang_dir), ['.', '..']);
    
    if (count($languages) > 0) {
        echo "✅ Found " . count($languages) . " language(s):\n";
        foreach ($languages as $lang) {
            if (is_dir($lang_dir . '/' . $lang)) {
                $lang_file = $lang_dir . '/' . $lang . '/zatca_invoice_qr_lang.php';
                if (file_exists($lang_file)) {
                    $size = filesize($lang_file);
                    echo "   ✅ $lang ($size bytes)\n";
                } else {
                    echo "   ❌ $lang (missing language file)\n";
                }
            }
        }
    } else {
        echo "⚠️  WARNING: No language directories found\n";
    }
} else {
    echo "❌ ERROR: language/ directory not found\n";
}

echo "\n";

// Check phpqrcode library
echo "Checking dependencies:\n";
echo "----------------------\n";

$phpqrcode_dir = $module_dir . '/libraries/phpqrcode';
if (is_dir($phpqrcode_dir)) {
    echo "✅ phpqrcode library found\n";
    
    $qrlib = $phpqrcode_dir . '/qrlib.php';
    if (file_exists($qrlib)) {
        echo "✅ qrlib.php present\n";
    } else {
        echo "❌ WARNING: qrlib.php not found\n";
    }
} else {
    echo "❌ ERROR: phpqrcode library not found (required for QR generation)\n";
}

echo "\n";

// Check controllers
echo "Checking controllers:\n";
echo "--------------------\n";

$controllers_dir = $module_dir . '/controllers';
if (is_dir($controllers_dir)) {
    $controllers = array_diff(scandir($controllers_dir), ['.', '..']);
    
    if (count($controllers) > 0) {
        echo "✅ Found " . count($controllers) . " controller(s):\n";
        foreach ($controllers as $controller) {
            if (pathinfo($controller, PATHINFO_EXTENSION) === 'php') {
                $size = filesize($controllers_dir . '/' . $controller);
                echo "   ✅ $controller ($size bytes)\n";
            }
        }
    } else {
        echo "⚠️  WARNING: No controllers found\n";
    }
}

echo "\n";

// Check models
echo "Checking models:\n";
echo "----------------\n";

$models_dir = $module_dir . '/models';
if (is_dir($models_dir)) {
    $models = array_diff(scandir($models_dir), ['.', '..']);
    
    if (count($models) > 0) {
        echo "✅ Found " . count($models) . " model(s):\n";
        foreach ($models as $model) {
            if (pathinfo($model, PATHINFO_EXTENSION) === 'php') {
                $size = filesize($models_dir . '/' . $model);
                echo "   ✅ $model ($size bytes)\n";
            }
        }
    } else {
        echo "⚠️  WARNING: No models found\n";
    }
}

echo "\n";

// Final summary
echo "====================================\n";
echo "VERIFICATION SUMMARY\n";
echo "====================================\n\n";

if ($all_files_exist && $all_dirs_exist) {
    echo "✅ ✅ ✅ MODULE STRUCTURE IS CORRECT! ✅ ✅ ✅\n\n";
    echo "Your module is ready for installation in Perfex CRM.\n\n";
    echo "Next steps:\n";
    echo "1. ZIP the 'zatca_invoice_qr' folder (if not already zipped)\n";
    echo "2. Upload to Perfex CRM: Setup → Modules → Upload\n";
    echo "   OR manually copy to: [perfex_root]/modules/zatca_invoice_qr/\n";
    echo "3. Install and activate the module\n";
    echo "4. Configure settings at: Setup → ZATCA Invoice QR\n";
} else {
    echo "❌ ❌ ❌ ISSUES FOUND! ❌ ❌ ❌\n\n";
    echo "Please fix the issues listed above before installation.\n";
    echo "Missing files or directories will prevent proper module installation.\n";
}

echo "\n";
echo "====================================\n";
echo "For detailed installation instructions, see:\n";
echo "- zatca_invoice_qr/INSTALLATION.md\n";
echo "- zatca_invoice_qr/README.md\n";
echo "====================================\n";
?>
