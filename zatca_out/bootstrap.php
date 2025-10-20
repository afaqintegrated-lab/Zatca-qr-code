<?php

// Define ROOT_PATH constant
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Autoloader for ZATCA classes
spl_autoload_register(function ($class) {
    $prefix = 'ZATCA\\';
    $base_dir = ROOT_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);

    // Convert namespace separators to directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
