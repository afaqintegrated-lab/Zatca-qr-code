<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Invoice QR Module Installer
 * Creates necessary database tables and sets default options for the module.
 */

$CI = &get_instance();

// Load database forge to manage database operations
$CI->load->dbforge();

// Get database charset and collation from CodeIgniter's database config
$db_charset = $CI->db->char_set;
$db_collat = $CI->db->dbcollat;

// Define default table attributes
$table_attributes = [
    'ENGINE' => 'InnoDB',
];

if ($db_charset) {
    $table_attributes['CHARACTER SET'] = $db_charset;
}

if ($db_collat) {
    $table_attributes['COLLATE'] = $db_collat;
} else {
    $table_attributes['COLLATE'] = 'utf8mb4_unicode_ci';
}

// ====================================================================================
// TABLE 1: tblzatca_settings - Module configuration settings
// ====================================================================================
if (!$CI->db->table_exists(db_prefix() . 'zatca_settings')) {
    $fields = [
        'id' => [
            'type'           => 'INT',
            'constraint'     => '11',
            'unsigned'       => TRUE,
            'auto_increment' => TRUE
        ],
        'enabled' => [
            'type'       => 'TINYINT',
            'constraint' => '1',
            'default'    => 0,
            'comment'    => 'Enable/disable QR code generation'
        ],
        'phase' => [
            'type'       => 'ENUM',
            'constraint' => ["'phase1'", "'phase2'"],
            'default'    => 'phase1',
            'comment'    => 'ZATCA implementation phase'
        ],
        'environment' => [
            'type'       => 'ENUM',
            'constraint' => ["'sandbox'", "'production'"],
            'default'    => 'sandbox',
            'comment'    => 'Environment mode'
        ],
        'seller_name' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'null'       => FALSE,
            'default'    => '',
            'comment'    => 'Company/Seller name for QR code'
        ],
        'vat_number' => [
            'type'       => 'VARCHAR',
            'constraint' => '50',
            'null'       => FALSE,
            'default'    => '',
            'comment'    => 'VAT registration number (15 digits for Saudi Arabia)'
        ],
        'company_address' => [
            'type'    => 'TEXT',
            'null'    => TRUE,
            'comment' => 'Company address for Phase 2'
        ],
        'qr_position' => [
            'type'       => 'ENUM',
            'constraint' => ["'top-right'", "'top-left'", "'bottom-right'", "'bottom-left'"],
            'default'    => 'top-right',
            'comment'    => 'QR code position on PDF invoice'
        ],
        'qr_size' => [
            'type'       => 'INT',
            'constraint' => '11',
            'default'    => 150,
            'comment'    => 'QR code size in pixels'
        ],
        'auto_generate' => [
            'type'       => 'TINYINT',
            'constraint' => '1',
            'default'    => 1,
            'comment'    => 'Auto-generate QR on invoice create/update'
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
        'updated_at' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
    ];

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE); // Primary key
    $CI->dbforge->create_table(db_prefix() . 'zatca_settings', TRUE, $table_attributes);

    // Insert default settings
    $CI->db->insert(db_prefix() . 'zatca_settings', [
        'enabled'        => 0,
        'phase'          => 'phase1',
        'environment'    => 'sandbox',
        'seller_name'    => '',
        'vat_number'     => '',
        'qr_position'    => 'top-right',
        'qr_size'        => 150,
        'auto_generate'  => 1,
        'created_at'     => date('Y-m-d H:i:s'),
        'updated_at'     => date('Y-m-d H:i:s'),
    ]);

    log_activity('ZATCA Invoice QR: Settings table created successfully');
}

// ====================================================================================
// TABLE 2: tblzatca_invoice_qr - Generated QR codes for invoices
// ====================================================================================
if (!$CI->db->table_exists(db_prefix() . 'zatca_invoice_qr')) {
    $fields = [
        'id' => [
            'type'           => 'INT',
            'constraint'     => '11',
            'unsigned'       => TRUE,
            'auto_increment' => TRUE
        ],
        'invoice_id' => [
            'type'       => 'INT',
            'constraint' => '11',
            'unsigned'   => TRUE,
            'null'       => FALSE,
            'comment'    => 'Reference to tblinvoices.id'
        ],
        'qr_data' => [
            'type'    => 'TEXT',
            'null'    => FALSE,
            'comment' => 'TLV encoded QR data (Base64)'
        ],
        'qr_base64' => [
            'type'    => 'LONGTEXT',
            'null'    => TRUE,
            'comment' => 'Base64 encoded QR code image (data:image/png;base64,...)'
        ],
        'invoice_hash' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'null'       => TRUE,
            'comment'    => 'SHA-256 hash of invoice (Phase 2)'
        ],
        'uuid' => [
            'type'       => 'VARCHAR',
            'constraint' => '100',
            'null'       => TRUE,
            'comment'    => 'Unique UUID for invoice (Phase 2)'
        ],
        'digital_signature' => [
            'type'    => 'TEXT',
            'null'    => TRUE,
            'comment' => 'ECDSA digital signature (Phase 2)'
        ],
        'generation_date' => [
            'type'    => 'DATETIME',
            'null'    => FALSE,
            'comment' => 'When QR code was generated'
        ],
        'status' => [
            'type'       => 'ENUM',
            'constraint' => ["'pending'", "'generated'", "'failed'"],
            'default'    => 'pending',
            'comment'    => 'Generation status'
        ],
        'error_message' => [
            'type'    => 'TEXT',
            'null'    => TRUE,
            'comment' => 'Error message if generation failed'
        ],
        'phase' => [
            'type'       => 'ENUM',
            'constraint' => ["'phase1'", "'phase2'"],
            'default'    => 'phase1',
            'comment'    => 'Which phase was used for generation'
        ],
    ];

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE); // Primary key
    $CI->dbforge->add_key('invoice_id'); // Index for quick lookup
    $CI->dbforge->add_key('status'); // Index for status queries
    $CI->dbforge->create_table(db_prefix() . 'zatca_invoice_qr', TRUE, $table_attributes);

    log_activity('ZATCA Invoice QR: Invoice QR table created successfully');
}

// ====================================================================================
// TABLE 3: tblzatca_certificates - For Phase 2 certificate management
// ====================================================================================
if (!$CI->db->table_exists(db_prefix() . 'zatca_certificates')) {
    $fields = [
        'id' => [
            'type'           => 'INT',
            'constraint'     => '11',
            'unsigned'       => TRUE,
            'auto_increment' => TRUE
        ],
        'certificate_type' => [
            'type'       => 'ENUM',
            'constraint' => ["'compliance'", "'production'"],
            'null'       => FALSE,
            'comment'    => 'Type of certificate'
        ],
        'certificate' => [
            'type'    => 'TEXT',
            'null'    => TRUE,
            'comment' => 'X.509 certificate (PEM format)'
        ],
        'private_key' => [
            'type'    => 'TEXT',
            'null'    => TRUE,
            'comment' => 'Private key (encrypted, PEM format)'
        ],
        'csr' => [
            'type'    => 'TEXT',
            'null'    => TRUE,
            'comment' => 'Certificate Signing Request'
        ],
        'secret' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'null'       => TRUE,
            'comment'    => 'API secret for ZATCA'
        ],
        'issued_date' => [
            'type' => 'DATETIME',
            'null' => TRUE,
            'comment' => 'Certificate issue date'
        ],
        'expiry_date' => [
            'type' => 'DATETIME',
            'null' => TRUE,
            'comment' => 'Certificate expiry date'
        ],
        'status' => [
            'type'       => 'ENUM',
            'constraint' => ["'active'", "'expired'", "'revoked'"],
            'default'    => 'active',
            'comment'    => 'Certificate status'
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
    ];

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE); // Primary key
    $CI->dbforge->add_key('certificate_type'); // Index
    $CI->dbforge->add_key('status'); // Index
    $CI->dbforge->create_table(db_prefix() . 'zatca_certificates', TRUE, $table_attributes);

    log_activity('ZATCA Invoice QR: Certificates table created successfully');
}

// ====================================================================================
// Set module options
// ====================================================================================
if (!get_option('zatca_invoice_qr_version')) {
    add_option('zatca_invoice_qr_version', '1.0.0');
}
if (!get_option('zatca_invoice_qr_installed')) {
    add_option('zatca_invoice_qr_installed', date('Y-m-d H:i:s'));
}

log_activity('ZATCA Invoice QR Module: Installation completed successfully');
