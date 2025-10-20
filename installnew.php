<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA QR Code Module Installer
 * Creates necessary database table for module settings and copies PDF template files.
 */

$CI = &get_instance();

$CI->load->dbforge();

$db_charset = $CI->db->char_set;
$db_collat = $CI->db->dbcollat;

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

$settings_table_name = db_prefix() . 'zatca_qr_settings'; // Use db_prefix() for dynamic prefixing

// --- START: Database Table Creation (Existing Code) ---
if (!$CI->db->table_exists($settings_table_name)) {
    $fields = [
        'id' => [
            'type'           => 'INT',
            'constraint'     => '11',
            'unsigned'       => true,
            'auto_increment' => true,
            'null'           => false,
        ],
        'enable_qr' => [
            'type'    => 'INT',
            'constraint' => '1',
            'default' => 0,
            'null'    => false,
        ],
        'seller_name' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'default'    => '',
            'null'       => false,
        ],
        'vat_number' => [
            'type'       => 'VARCHAR',
            'constraint' => '50',
            'default'    => '',
            'null'       => false,
        ],
        'qr_code_x_pos' => [
            'type'       => 'INT',
            'constraint' => '11',
            'default'    => 0,
            'null'       => false,
        ],
        'qr_code_y_pos' => [
            'type'       => 'INT',
            'constraint' => '11',
            'default'    => 0,
            'null'       => false,
        ],
        'qr_code_size' => [
            'type'    => 'INT',
            'constraint' => '11',
            'default' => 200,
            'null'    => false,
        ],
        'created_at' => [
            'type'    => 'DATETIME',
            'null'    => true,
        ],
        'updated_at' => [
            'type'    => 'DATETIME',
            'null'    => true,
        ],
    ];

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', true);

    $CI->dbforge->create_table($settings_table_name, true, $table_attributes);

    log_message('debug', 'ZATCA QR Code Module Installer: ' . $settings_table_name . ' table created successfully.');

    if ($CI->db->table_exists($settings_table_name) && $CI->db->count_all_results($settings_table_name) == 0) {
        $data = [
            'enable_qr'       => 0,
            'seller_name'     => 'Afaq Integrated for Information Technology',
            'vat_number'      => '311859643900003',
            'qr_code_x_pos'   => 0,
            'qr_code_y_pos'   => 0,
            'qr_code_size'    => 200,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        $CI->db->insert($settings_table_name, $data);
        log_message('debug', 'ZATCA QR Code Module Installer: Default settings inserted into ' . $settings_table_name . '.');
    }
} else {
    log_message('debug', 'ZATCA QR Code Module Installer: ' . $settings_table_name . ' table already exists. Skipping creation.');
    // You might add ALTER TABLE statements here if your module updates in the future
    // and new columns are needed.
}
// --- END: Database Table Creation ---


// --- START: FILE COPYING CODE ---
log_message('debug', 'ZATCA QR Code Module Installer: Attempting to copy PDF template files.');

$theme_name = get_option('pdf_logo_width') ? 'perfex' : 'perfex'; // Default to 'perfex', adjust if custom themes are involved
                                                                  // A more robust way might involve getting the active theme dynamically
                                                                  // or letting the admin choose. For simplicity, assume 'perfex'.

// Define source paths within your module
$source_invoice_pdf = MODULES_PATH . 'zatca_qr_code/views/invoices/invoicepdf.php';
$source_proposal_pdf = MODULES_PATH . 'zatca_qr_code/views/proposals/proposalpdf.php'; // Assuming proposals template exists in your module

// Define destination directories in the theme
$destination_invoice_pdf_dir = FCPATH . 'application/views/themes/' . $theme_name . '/views/invoices/';
$destination_proposal_pdf_dir = FCPATH . 'application/views/themes/' . $theme_name . '/views/proposals/';

// Helper function to copy files and log
function copy_pdf_template($source_path, $destination_dir) {
    global $CI; // Access CodeIgniter instance

    $filename = basename($source_path);
    $destination_path = $destination_dir . $filename;

    if (!file_exists($source_path)) {
        log_message('error', 'ZATCA QR Code Module Installer: Source PDF template file not found at: ' . $source_path);
        set_alert('warning', 'ZATCA QR Code Module Installer: Source PDF template file not found: ' . $filename . '. Template not copied.');
        return false;
    }

    // Ensure the destination directory exists
    if (!is_dir($destination_dir)) {
        if (!mkdir($destination_dir, 0755, true)) { // 0755 is common for directories
            log_message('error', 'ZATCA QR Code Module Installer: Failed to create destination directory: ' . $destination_dir . '. Check folder permissions.');
            set_alert('danger', 'ZATCA QR Code Module Installer: Failed to create directory for ' . $filename . '. Check permissions for /application/views/themes/.');
            return false;
        }
    }

    // Attempt to copy
    if (copy($source_path, $destination_path)) {
        log_message('debug', 'ZATCA QR Code Module Installer: Successfully copied ' . $source_path . ' to ' . $destination_path);
        set_alert('success', 'ZATCA QR Code Module Installer: PDF template "' . $filename . '" copied successfully to your theme.');
        return true;
    } else {
        log_message('error', 'ZATCA QR Code Module Installer: Failed to copy ' . $source_path . ' to ' . $destination_path . '. Check file permissions for theme views.');
        set_alert('danger', 'ZATCA QR Code Module Installer: Failed to copy PDF template "' . $filename . '". Check permissions for /application/views/themes/.');
        return false;
    }
}

// Perform copy for Invoice PDF
copy_pdf_template($source_invoice_pdf, $destination_invoice_pdf_dir);

// Perform copy for Proposal PDF
copy_pdf_template($source_proposal_pdf, $destination_proposal_pdf_dir);

// --- END: FILE COPYING CODE ---