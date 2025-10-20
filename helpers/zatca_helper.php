<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA QR Code Module Custom Helper Functions
 */

function zatca_qr_code_init_menu_items()
{
    $CI = &get_instance();

    // Use your module's permission, or check if admin
    if (has_permission('zatca_qr_code', '', 'view') || is_admin()) {
        // Main menu item for ZATCA QR Code
        $CI->app_menu->add_sidebar_menu_item('zatca_qr_code_main', [ // Use a unique slug for your main item
            'name'     => _l('zatca_qr_code_module_name'), // Use your module's defined language string
            'href'     => admin_url('zatca_qr_code/settings'), // Link to your main settings page
            'icon'     => 'fa fa-qrcode', // Using the QR code icon from earlier
            'position' => 30,
        ]);

        // Child item for ZATCA QR Code Settings (if you want it nested)
        $CI->app_menu->add_sidebar_children_item('zatca_qr_code_main', [ // Parent slug
            'slug'     => 'zatca-qr-code-settings-child', // Unique child slug
            'name'     => _l('zatca_qr_code_settings'), // Language string for settings
            'href'     => admin_url('zatca_qr_code/settings'), // Link to settings page
            'position' => 1,
        ]);

        // You can add more child items here if your module has multiple admin pages, e.g.:
        /*
        $CI->app_menu->add_sidebar_children_item('zatca_qr_code_main', [
            'slug'     => 'zatca-qr-code-invoices',
            'name'     => _l('zatca_qr_code_invoices_list'), // Define this in your lang file
            'href'     => admin_url('zatca_qr_code/invoices'), // Assuming you'll have an 'invoices' method
            'position' => 2,
        ]);
        */
    }
}

// Example of a function for processing invoices within your module
function zatca_qr_code_process_invoice($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('zatca_qr_code/zatca_qr_code_model', 'zatca_model');
    // Assuming you have a method like generate_zatca_invoice in your model
    // $CI->zatca_model->generate_zatca_invoice($invoice_id);
    // Add your QR generation logic here or call your model
}

// Example of a function for adding custom CSS/JS (already in zatca_qr_code.php)
// You could move these from zatca_qr_code.php into here if desired,
// but they would need to be hooked from zatca_qr_code.php to this helper function.
// For now, it's simpler to keep them in zatca_qr_code.php
/*
function zatca_qr_code_add_head_components()
{
    echo '<link href="' . module_dir_url('zatca_qr_code', 'assets/css/style.css') . '" rel="stylesheet" type="text/css" />';
    echo '<script src="' . module_dir_url('zatca_qr_code', 'assets/js/script.js') . '"></script>';
}
*/