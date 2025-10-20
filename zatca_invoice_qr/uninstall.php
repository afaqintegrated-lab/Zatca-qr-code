<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Invoice QR Module Uninstaller
 * Clean up database tables and options when module is deactivated
 */

$CI = &get_instance();

// Load database forge
$CI->load->dbforge();

// Note: We're NOT dropping tables on deactivation to preserve data
// If you want to completely remove data, uncomment the lines below

/*
// Drop tables
if ($CI->db->table_exists(db_prefix() . 'zatca_settings')) {
    $CI->dbforge->drop_table(db_prefix() . 'zatca_settings', TRUE);
}

if ($CI->db->table_exists(db_prefix() . 'zatca_invoice_qr')) {
    $CI->dbforge->drop_table(db_prefix() . 'zatca_invoice_qr', TRUE);
}

if ($CI->db->table_exists(db_prefix() . 'zatca_certificates')) {
    $CI->dbforge->drop_table(db_prefix() . 'zatca_certificates', TRUE);
}

// Remove options
delete_option('zatca_invoice_qr_version');
delete_option('zatca_invoice_qr_installed');
*/

// Just log deactivation
log_activity('ZATCA Invoice QR Module: Deactivated (data preserved)');
