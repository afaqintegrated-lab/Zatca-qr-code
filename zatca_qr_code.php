<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: ZATCA QR Code
Description: Generate ZATCA compliant QR codes on invoices.
Version: 1.0.0
Author: Valcomco
Author URI: valcomco.com
*/
define('QR_CODE_GENERATOR_MODULE_NAME', 'zatca_qr_code');
define('QR_CODE_UPLOAD_PATH', FCPATH . 'uploads/qr_codes/'); // Ensure this path is correct for your setup
// =============================================================================================
// IMPORTANT: ACTIVATION/DEACTIVATION HOOKS
// =============================================================================================
function zatca_qr_code_disable_pdf_signature($process_signature_default) {
    // Returning false will prevent the processSignature() method from being called.
    return false;
}
hooks()->add_filter('process_pdf_signature_on_close', 'zatca_qr_code_disable_pdf_signature');

register_activation_hook('zatca_qr_code', 'zatca_qr_code_activation_hook');
function zatca_qr_code_activation_hook() {
    require_once(__DIR__ . '/install.php');
}

register_deactivation_hook('zatca_qr_code', 'zatca_qr_code_deactivation_hook');
function zatca_qr_code_deactivation_hook() {
    require_once(__DIR__ . '/uninstall.php');
}



// Removed the old QR_CODE_GENERATOR_MODULE_NAME define as it's inconsistent.
// We will use 'zatca-qr-code' directly for the menu slug.

//hooks()->add_action('admin_init', 'zatca_qr_code_admin_init'); 

//hooks()->add_action('after_render_and_parse_sidebar_menu', 'zatca_qr_code_add_admin_menu');


//start shihab
 
hooks()->add_action('before_admin_init', function () {
    load_perfex_and_sub_language('zatca_qr_code', 'zatca_qr_code');
});
//end shihab
 
/**
 * ZATCA QR Code module admin initialization.
 * This function is called during the Perfex CRM admin area initialization.
 */
hooks()->add_action('admin_init', function() {
    $CI = &get_instance();

    log_message('debug', 'ZATCA QR Code Module: admin_init hook fired for menu addition and assets.');

    // --- Admin Menu Item - Placing under 'Utilities' ---
    // Check if the user has permission or is an admin before adding the menu item
    if (has_permission('zatca_qr_code', '', 'view') || is_admin()) {
        // Use add_sidebar_children_item to nest under a parent slug
        $CI->app_menu->add_sidebar_children_item('utilities', [ // <--- Parent slug is 'utilities'
            'slug'     => 'zatca-qr-code-settings', // Unique slug for your menu item
            'name'     => _l('zatca_qr_code_module_name'), // Language key for the menu item name
            'href'     => admin_url('zatca_qr_code/admin/settings'), // Link to your module's settings page
            'icon'     => 'fa fa-qrcode', // Font Awesome icon for the menu item
            'position' => 25, // Position within the 'Utilities' sub-menu (adjust as needed, Surveys is 26)
        ]);
        log_message('debug', 'ZATCA QR Code Module: Attempted to add menu item under Utilities.');
    }

    // --- Custom Assets (CSS/JavaScript) for Admin Area ---
    // Load CSS only when the current module's controller is active
    if ($CI->router->fetch_module() == 'zatca_qr_code') {
        echo '<link href="' . module_dir_url('zatca_qr_code', 'assets/css/style.css') . '" rel="stylesheet">' . PHP_EOL;
        // Load JS only when the current module's controller is active
        echo '<script src="' . module_dir_url('zatca_qr_code', 'assets/js/script.js') . '"></script>' . PHP_EOL;
    }
});


// =============================================================================================
// INVOICE PDF INTEGRATION (CORE FUNCTIONALITY)
// =============================================================================================

hooks()->add_filter('invoice_html_pdf_data', 'add_zatca_qr_to_invoice_data');

hooks()->add_filter('pdf_invoice_html_view_path', 'zatca_qr_code_pdf_view_path');

function zatca_qr_code_pdf_view_path($path) {
    // Check if the current module is active or if you want this override to be global.
    // For module-specific override, ensure this logic applies only when your module is active.
    // In Perfex CRM, module hooks are generally only called when the module is active.
    
    // Return the path to your module's custom invoice PDF view
    return 'zatca_qr_code/views/themes/perfex/views/invoices/invoice_pdf';
}
/**
 * Adds ZATCA QR code image data (Base64) to the $invoice object.
 */
function add_zatca_qr_to_invoice_data($invoice_data)
{
    $CI = &get_instance();

    if (!isset($CI->db)) {
        $CI->load->database();
    }

    // Fetch ZATCA QR settings from database
    $settings = $CI->db->get('tblacc_zatca_qr_settings')->row();

    if ($settings && $settings->enable_qr) {
        // Assuming $invoice_data IS the invoice object directly
        $invoice = $invoice_data;

        // --- 1. Gather ZATCA Required Fields ---
        $seller_name      = $settings->seller_name;
        $vat_number       = $settings->vat_number;
        $invoice_total    = (float) $invoice->total;
        $vat_total        = (float) $invoice->total_tax;

        $invoice_datetime_obj = new DateTime($invoice->date, new DateTimeZone('UTC'));
        $invoice_datetime_utc = $invoice_datetime_obj->format('Y-m-d\TH:i:s\Z');

        // --- 2. Generate ZATCA TLV String (Tag-Length-Value) ---
        // IMPORTANT: This is a placeholder. Implement the full ZATCA TLV encoding.
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

        // --- 3. Generate QR Code Image (Base64 Data URI) ---
        require_once(__DIR__ . '/libraries/phpqrcode/qrlib.php');

        $qr_size_pixels = isset($settings->qr_size) ? $settings->qr_size : 200;
        $module_size = max(1, round($qr_size_pixels / 25));
        $quiet_zone = 2;

        // --- ALTERNATIVE FIX FOR temp_dir() ---
        // Directly use FCPATH . 'temp/' as an alternative to temp_dir()
        $temp_dir_path = FCPATH . 'temp/';
        // Ensure the temporary directory exists
        if (!is_dir($temp_dir_path)) {
            mkdir($temp_dir_path, 0755, true);
        }
        $temp_png_file = $temp_dir_path . 'zatca_qr_' . uniqid() . '.png';
        // --- END ALTERNATIVE FIX ---

        QRcode::png($qr_content_base64, $temp_png_file, QR_ECLEVEL_L, $module_size, $quiet_zone);

        $qr_image_data = 'data:image/png;base64,' . base64_encode(file_get_contents($temp_png_file));

        @unlink($temp_png_file);

        // --- 4. Add QR Code Data to Invoice Object ---
        $invoice_data->zatca_qr_code_image = $qr_image_data;
        $invoice_data->zatca_qr_code_qr_size = $qr_size_pixels;
    }

    return $invoice_data;
}