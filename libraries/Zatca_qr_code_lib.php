<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Define the path to the PHP QR Code library.
// Make sure you have downloaded PHP QR Code (qrlib.php) and placed it correctly.
// A common structure is: modules/zatca_qr_code/libraries/phpqrcode/qrlib.php
define('QR_LIB_PATH', __DIR__ . '/phpqrcode/qrlib.php');

// Check if the QR library exists before requiring it.
// This prevents fatal errors if the library is missing.
if (!file_exists(QR_LIB_PATH)) {
    log_activity('ZATCA QR Code: QR Code library (qrlib.php) not found at ' . QR_LIB_PATH, 'module_error');
    // You might want to throw an exception or set a flag here to disable QR generation
    // if the critical dependency is missing.
    // For now, we'll just log and let subsequent calls potentially fail gracefully.
} else {
    require_once(QR_LIB_PATH);
}

/**
 * Zatca_qr_code_lib Class
 * Handles the generation of ZATCA Phase 1 compliant QR code strings and images.
 */
class Zatca_qr_code_lib
{
    protected $CI;
    protected $settings; // Cache module settings once fetched

    public function __construct()
    {
        $this->CI = &get_instance();
        // Load the custom model to fetch module settings
        $this->CI->load->model('zatca_qr_code/zatca_qr_code_model');

        // Fetch settings once in the constructor for efficiency
        $this->settings = $this->CI->zatca_qr_code_model->get_settings();

        // Log if settings are not found, indicating a potential installation issue
        if (!$this->settings) {
            log_activity('ZATCA QR Code: Module settings not found in database. Please re-activate module.', 'module_error');
        }
    }

    /**
     * Generates the ZATCA Phase 1 QR code string in TLV (Tag-Length-Value) format,
     * then Base64 encodes it.
     *
     * @param array $invoice_data An array containing relevant invoice details.
     * Expected keys: 'datecreated', 'total', 'total_tax'.
     * @return string|false Base64 encoded ZATCA QR code string on success, false on failure or if disabled.
     */
    public function generate_zatca_qr_string($invoice_data)
    {
        // 1. Check if module is enabled via settings
        if (!$this->settings || $this->settings->enable_qr != 1) {
            // log_activity('ZATCA QR Code: Generation is disabled via module settings.', 'info');
            return false;
        }

        // 2. Validate essential invoice data
        $required_invoice_keys = ['datecreated', 'total', 'total_tax'];
        foreach ($required_invoice_keys as $key) {
            if (!isset($invoice_data[$key])) {
                log_activity('ZATCA QR Code: Missing required invoice data for key: ' . $key, 'module_error');
                return false;
            }
        }

        // 3. Retrieve settings from the database
        $seller_name      = $this->settings->seller_name;
        $vat_number       = $this->settings->vat_number;

        // 4. Prepare invoice data for TLV encoding
        // Ensure values are strings for strlen calculation and pack() function
        $invoice_timestamp = date('Y-m-d\TH:i:s\Z', strtotime($invoice_data['datecreated']));
        $total_with_vat    = number_format((float)$invoice_data['total'], 2, '.', ''); // Format to 2 decimal places
        $vat_total         = number_format((float)$invoice_data['total_tax'], 2, '.', ''); // Format to 2 decimal places

        // Basic validation for critical ZATCA fields from settings
        if (empty($seller_name) || empty($vat_number) || empty($invoice_timestamp) || empty($total_with_vat) || empty($vat_total)) {
            log_activity('ZATCA QR Code: One or more critical ZATCA fields (seller name, VAT, timestamp, totals) are empty.', 'module_error');
            return false;
        }

        // 5. TLV (Tag-Length-Value) Encoding as per ZATCA Phase 1 requirements
        // Tag (1 byte)
        // Length (1-2 bytes, for simplicity we use 1 byte if length <= 255)
        // Value (variable length)

        $tlv_data = '';

        // Tag 1: Seller Name
        $tlv_data .= pack("C", 1); // Tag: 1
        $tlv_data .= pack("C", strlen($seller_name)); // Length
        $tlv_data .= $seller_name; // Value

        // Tag 2: VAT Registration Number of Seller
        $tlv_data .= pack("C", 2); // Tag: 2
        $tlv_data .= pack("C", strlen($vat_number)); // Length
        $tlv_data .= $vat_number; // Value

        // Tag 3: Timestamp of the electronic invoice or credit/debit note (date and time)
        $tlv_data .= pack("C", 3); // Tag: 3
        $tlv_data .= pack("C", strlen($invoice_timestamp)); // Length
        $tlv_data .= $invoice_timestamp; // Value

        // Tag 4: Electronic invoice or credit/debit note total (with VAT)
        $tlv_data .= pack("C", 4); // Tag: 4
        $tlv_data .= pack("C", strlen($total_with_vat)); // Length
        $tlv_data .= $total_with_vat; // Value

        // Tag 5: VAT total
        $tlv_data .= pack("C", 5); // Tag: 5
        $tlv_data .= pack("C", strlen($vat_total)); // Length
        $tlv_data .= $vat_total; // Value

        // 6. Base64 encode the complete TLV string
        return base64_encode($tlv_data);
    }

    /**
     * Generates a QR code image from the provided data string using PHP QR Code library.
     * The image is returned as a Base64 encoded PNG data URI.
     *
     * @param string $qr_data The data string to encode in the QR code (e.g., the Base64 encoded ZATCA TLV string).
     * @return string|false Base64 encoded PNG data URI on success, false on failure.
     */
    public function generate_qr_image($qr_data)
    {
        // 1. Check if the QR library file exists
        if (!file_exists(QR_LIB_PATH)) {
            log_activity('ZATCA QR Code: QR Code library (qrlib.php) not found for image generation.', 'module_error');
            return false;
        }

        // 2. Validate input QR data
        if (empty($qr_data)) {
            log_activity('ZATCA QR Code: No data provided for QR image generation.', 'module_error');
            return false;
        }

        // 3. Get QR code size from settings (default to 150 if settings not found or invalid)
        $qr_size = 150; // Default size
        if ($this->settings && isset($this->settings->qr_size) && is_numeric($this->settings->qr_size)) {
            $qr_size = (int)$this->settings->qr_size;
            // Ensure size is within reasonable bounds
            if ($qr_size < 50 || $qr_size > 500) {
                $qr_size = 150; // Revert to default if invalid
                log_activity('ZATCA QR Code: Invalid QR size (' . $this->settings->qr_size . ') in settings. Using default 150.', 'warning');
            }
        }

        // 4. Generate the QR code image
        try {
            // Start output buffering to capture the PNG image data generated by QRcode::png()
            ob_start();
            // QR_ECLEVEL_L: Error correction level (L, M, Q, H - Low, Medium, Quartile, High)
            // Lower error correction means smaller QR code but less robust. L is usually sufficient.
            // 2: Border size (quiet zone around the QR code)
            QRcode::png($qr_data, null, QR_ECLEVEL_L, $qr_size, 2);
            $image_string = ob_get_contents(); // Get the image data from the buffer
            ob_end_clean(); // Clean (delete) the output buffer contents and turn off buffering

            if (empty($image_string)) {
                log_activity('ZATCA QR Code: Failed to generate QR image (empty output).', 'module_error');
                return false;
            }

            // 5. Return the image as a Base64 encoded data URI
            return 'data:image/png;base64,' . base64_encode($image_string);

        } catch (Exception $e) {
            log_activity('ZATCA QR Code: Error generating QR image: ' . $e->getMessage(), 'module_error');
            return false;
        }
    }
}