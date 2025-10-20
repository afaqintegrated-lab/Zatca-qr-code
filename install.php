<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * QR Code Generator Module Installer
 * Creates necessary database tables and sets default options for the module.
 */

$CI = &get_instance();

// Load databaseforge to manage database operations
$CI->load->dbforge();

// Get database char_set and dbcollat from CodeIgniter's database config
// This ensures compatibility with the existing database setup
$db_charset = $CI->db->char_set;
$db_collat = $CI->db->dbcollat;

// Define default table options using the retrieved charset and collation
// This must be an array for CI_DB_forge::create_table()
$table_attributes = [
    'ENGINE' => 'InnoDB', // Common default for Perfex CRM tables
];

if ($db_charset) {
    $table_attributes['CHARACTER SET'] = $db_charset;
}
if ($db_collat) {
    $table_attributes['COLLATE'] = $db_collat;
} else {
    // Fallback if collation is empty, using a common default or ensuring it's not set if explicitly empty.
    // If you have a specific default collation for your database (e.g., utf8mb4_unicode_ci), use it here.
    // Otherwise, leaving it unset might allow MySQL to use its server default.
    // Forcing a default if it's empty to prevent issues:
    // This is often utf8mb4_unicode_ci for modern MySQL/MariaDB
    $table_attributes['COLLATE'] = 'utf8mb4_unicode_ci'; 
}


// Check if tblqr_codes table exists, if not, create it
if (!$CI->db->table_exists(db_prefix() . 'qr_codes')) {
    $fields = [
        'id' => [
            'type'           => 'INT',
            'constraint'     => '11',
            'unsigned'       => TRUE,
            'auto_increment' => TRUE
        ],
        'rel_id' => [
            'type'           => 'INT',
            'constraint'     => '11',
            'unsigned'       => TRUE,
            'comment'        => 'ID of the related record (e.g., invoice ID, estimate ID)',
        ],
        'rel_type' => [
            'type'           => 'VARCHAR',
            'constraint'     => '50',
            'comment'        => 'Type of the related record (e.g., "invoice", "estimate", "contact")',
        ],
        'qr_data' => [
            'type'           => 'TEXT',
            'comment'        => 'The actual data encoded in the QR code (URL, text, TLV string)',
        ],
        'file_name' => [
            'type'           => 'VARCHAR',
            'constraint'     => '255',
            'comment'        => 'Filename of the generated QR code image',
        ],
        'date_generated datetime default CURRENT_TIMESTAMP',
    ];

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE); // Primary key
    $CI->dbforge->add_key(['rel_id', 'rel_type']); // Index for quicker lookups by entity
    $CI->dbforge->create_table(db_prefix() . 'qr_codes', TRUE, $table_attributes); // Changed $table_options to $table_attributes

    log_message('debug', 'QR Code Module Installer: tblqr_codes table created successfully with attributes: ' . json_encode($table_attributes));
} else {
    log_message('debug', 'QR Code Module Installer: tblqr_codes table already exists. Skipping creation.');
}

// Set default module options if they don't exist
add_option('qr_code_default_size', 200); // Default QR code size in pixels
add_option('qr_code_error_correction', 'L'); // Default error correction level (L, M, Q, H)
add_option('qr_code_enable_for_invoices', 1); // Enable QR codes for invoices by default
add_option('qr_code_enable_for_estimates', 0); // Disable for estimates by default
add_option('qr_code_enable_for_contacts', 0); // Disable for contacts by default
add_option('qr_code_public_access_key', bin2hex(random_bytes(16))); // Unique key for public access links

// ZATCA Phase 1 options


add_option('qr_code_enable_zatca_invoices', 0); // Disable ZATCA QR codes for invoices by default
add_option('qr_code_zatca_seller_name', ''); // Default empty seller name
add_option('qr_code_zatca_vat_number', ''); // Default empty VAT number

// New VAT Details for Company (Optional, for general company info not directly ZATCA)
add_option('qr_code_company_vat_name', '');
add_option('qr_code_company_vat_percentage', '');

log_message('debug', 'QR Code Module Installer: Default options set.');

