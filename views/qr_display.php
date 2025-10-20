<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
/**
 * ZATCA QR Code Display View
 *
 * This view is intended to display the generated ZATCA QR code image.
 * It expects the following variables to be passed to it:
 * - $qr_image_data (string): The Base64 encoded PNG data URI of the QR code.
 * - $qr_size (int): The desired width and height for the QR code image in pixels.
 *
 * It will display the QR code only if $qr_image_data is provided and not empty.
 */

// Ensure the QR code image data is available before attempting to display it.
if (isset($qr_image_data) && !empty($qr_image_data)) {
    // Determine the QR code size.
    // Fallback to a default of 150px if $qr_size is not provided or is invalid.
    $display_qr_size = 150; // Default size
    if (isset($qr_size) && is_numeric($qr_size)) {
        $parsed_qr_size = (int)$qr_size;
        // Basic validation for reasonable QR code size (e.g., between 50 and 500 pixels)
        if ($parsed_qr_size >= 50 && $parsed_qr_size <= 500) {
            $display_qr_size = $parsed_qr_size;
        }
    }
?>Test
    <div class="zatca-qr-code-wrapper" style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
        <img
            src="<?php echo htmlspecialchars($qr_image_data); ?>"
            alt="<?php echo _l('zatca_qr_code_alt_text', 'ZATCA QR Code'); ?>"
            width="<?php echo $display_qr_size; ?>px"
            height="<?php echo $display_qr_size; ?>px"
            style="border: 1px solid #ccc; padding: 5px; background-color: #fff; display: block; margin: 0 auto;"
        >
        <p style="font-size: 11px; color: #555; margin-top: 5px; text-align: center;"><?php echo _l('zatca_qr_code_scan_text', 'Scan for Invoice Details'); ?></p>
    </div>
<?php
} else {
    // This block will be executed if no QR code image data is provided.
    // In a production environment, you might just want to silently fail or log this.
    // For debugging, you could uncomment the echo line below.
    // echo '<div class="alert alert-warning">' . _l('zatca_qr_code_not_available', 'QR Code not available.') . '</div>';
    log_activity('ZATCA QR Code: QR image data not available for display in qr_display.php.', 'warning');
}
?>