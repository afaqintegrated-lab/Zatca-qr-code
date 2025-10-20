<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// --- Module Database Table Deletion ---
// Delete the module's custom settings table if it exists
if ($CI->db->table_exists(db_prefix() . 'zatca_qr_settings')) {
    $CI->db->query('DROP TABLE `' . db_prefix() . 'zatca_qr_settings`');
}

// --- Permissions Uninstallation ---
// Define the capability name for your module
$capability = 'zatca_qr_code';

// Check if the 'roles_capabilities' table exists before attempting to delete from it.
// This table stores the definition of your module's capabilities.
if ($CI->db->table_exists(db_prefix() . 'roles_capabilities')) {
    $CI->db->where('name', $capability);
    $CI->db->delete(db_prefix() . 'roles_capabilities');
}

// Check if the 'role_permissions' table exists before attempting to delete from it.
// This table stores which roles have which permissions.
// The error you received was related to this table not being found.
// This check (and the one above) will prevent the error if the table is missing.
if ($CI->db->table_exists(db_prefix() . 'role_permissions')) { // This corresponds to where line 28 likely falls
    $CI->db->where('capability', $capability);
    $CI->db->delete(db_prefix() . 'role_permissions');
}

// Log the uninstallation event in Perfex CRM's activity log.
log_activity('ZATCA QR Code Module Uninstalled');