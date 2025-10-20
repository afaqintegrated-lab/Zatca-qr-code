<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: ZATCA Invoice QR
Description: Generate ZATCA Phase 1 & Phase 2 compliant QR codes for Saudi Arabia invoices in Perfex CRM
Version: 1.0.0
Requires at least: 2.3.*
Author: Afaq Integrated Lab
Author URI: https://afaqintegrated-lab.com
*/

define('ZATCA_INVOICE_QR_MODULE_NAME', 'zatca_invoice_qr');
define('ZATCA_INVOICE_QR_VERSION', '1.0.0');
define('ZATCA_INVOICE_QR_PATH', module_dir_path(ZATCA_INVOICE_QR_MODULE_NAME));

/**
 * Register activation hook
 */
register_activation_hook(ZATCA_INVOICE_QR_MODULE_NAME, 'zatca_invoice_qr_activation_hook');

function zatca_invoice_qr_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register deactivation hook
 */
register_deactivation_hook(ZATCA_INVOICE_QR_MODULE_NAME, 'zatca_invoice_qr_deactivation_hook');

function zatca_invoice_qr_deactivation_hook()
{
    // Cleanup if needed
    require_once(__DIR__ . '/uninstall.php');
}

/**
 * Load module language files
 */
hooks()->add_action('app_admin_head', 'zatca_invoice_qr_load_language');

function zatca_invoice_qr_load_language()
{
    $CI = &get_instance();
    $CI->load->helper('zatca_invoice_qr');
    
    // Load language file
    $language = $CI->config->item('active_language');
    $lang_file = ZATCA_INVOICE_QR_PATH . 'language/' . $language . '/zatca_invoice_qr_lang.php';
    
    if (file_exists($lang_file)) {
        $CI->lang->load('zatca_invoice_qr', $language);
    } else {
        $CI->lang->load('zatca_invoice_qr', 'english');
    }
}

/**
 * Register admin menu
 */
hooks()->add_action('admin_init', 'zatca_invoice_qr_init_menu_items');

function zatca_invoice_qr_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('zatca_invoice_qr', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('zatca-invoice-qr', [
            'name'     => _l('zatca_qr_module_name'),
            'href'     => admin_url('zatca_invoice_qr/zatca_admin/settings'),
            'position' => 35,
            'icon'     => 'fa fa-qrcode',
        ]);
    }
}

/**
 * Register module permissions
 */
hooks()->add_action('admin_init', 'zatca_invoice_qr_permissions');

function zatca_invoice_qr_permissions()
{
    $capabilities = [];
    
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('zatca_invoice_qr', $capabilities, _l('zatca_qr_module_name'));
}

/**
 * Load admin assets (CSS & JS)
 */
hooks()->add_action('app_admin_head', 'zatca_invoice_qr_load_admin_assets');

function zatca_invoice_qr_load_admin_assets()
{
    $CI = &get_instance();
    
    // Only load on module pages
    if (strpos($CI->router->fetch_class(), 'zatca') !== false) {
        echo '<link href="' . module_dir_url(ZATCA_INVOICE_QR_MODULE_NAME, 'assets/css/zatca_admin.css') . '" rel="stylesheet" type="text/css" />';
        echo '<script src="' . module_dir_url(ZATCA_INVOICE_QR_MODULE_NAME, 'assets/js/zatca_admin.js') . '"></script>';
    }
}

/**
 * Hook into invoice PDF generation to add QR code
 */
hooks()->add_filter('invoice_html_pdf_data', 'zatca_invoice_qr_add_to_pdf');

function zatca_invoice_qr_add_to_pdf($invoice_data)
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_qr_model');
    
    // Check if module is enabled
    $settings = $CI->zatca_qr_model->get_settings();
    
    if (!$settings || !$settings->enabled) {
        return $invoice_data;
    }
    
    $invoice_id = $invoice_data->id ?? null;
    
    if (!$invoice_id) {
        return $invoice_data;
    }
    
    // Try to get existing QR code
    $qr_record = $CI->zatca_qr_model->get_invoice_qr($invoice_id);
    
    // If no QR exists or auto-generate is enabled, generate new one
    if (!$qr_record || $settings->auto_generate) {
        $qr_record = $CI->zatca_qr_model->generate_invoice_qr($invoice_id);
    }
    
    if ($qr_record && $qr_record->qr_base64) {
        // Add QR code data to invoice
        $invoice_data->zatca_qr_code = $qr_record->qr_base64;
        $invoice_data->zatca_qr_position = $settings->qr_position;
        $invoice_data->zatca_qr_size = $settings->qr_size;
    }
    
    return $invoice_data;
}

/**
 * Hook to add QR code to invoice view
 */
hooks()->add_action('after_invoice_view_as_client_link', 'zatca_invoice_qr_display_on_invoice');

function zatca_invoice_qr_display_on_invoice($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_qr_model');
    
    $settings = $CI->zatca_qr_model->get_settings();
    
    if (!$settings || !$settings->enabled) {
        return;
    }
    
    $qr_record = $CI->zatca_qr_model->get_invoice_qr($invoice_id);
    
    if ($qr_record && $qr_record->qr_base64) {
        echo '<div class="zatca-qr-container">';
        echo '<h4>' . _l('zatca_qr_code') . '</h4>';
        echo '<img src="' . $qr_record->qr_base64 . '" alt="ZATCA QR Code" style="max-width: 200px;" />';
        echo '</div>';
    }
}

/**
 * Hook after invoice is created
 */
hooks()->add_action('after_invoice_added', 'zatca_invoice_qr_on_invoice_created');

function zatca_invoice_qr_on_invoice_created($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_qr_model');
    
    $settings = $CI->zatca_qr_model->get_settings();
    
    if ($settings && $settings->enabled && $settings->auto_generate) {
        // Generate QR code automatically
        $CI->zatca_qr_model->generate_invoice_qr($invoice_id);
    }
}

/**
 * Hook after invoice is updated
 */
hooks()->add_action('after_invoice_updated', 'zatca_invoice_qr_on_invoice_updated');

function zatca_invoice_qr_on_invoice_updated($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_qr_model');
    
    $settings = $CI->zatca_qr_model->get_settings();
    
    if ($settings && $settings->enabled && $settings->auto_generate) {
        // Regenerate QR code on invoice update
        $CI->zatca_qr_model->regenerate_invoice_qr($invoice_id);
    }
}

/**
 * Add settings link in module list
 */
hooks()->add_filter('module_' . ZATCA_INVOICE_QR_MODULE_NAME . '_action_links', 'zatca_invoice_qr_module_action_links');

function zatca_invoice_qr_module_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('zatca_invoice_qr/zatca_admin/settings') . '">' . _l('settings') . '</a>';
    return $actions;
}
