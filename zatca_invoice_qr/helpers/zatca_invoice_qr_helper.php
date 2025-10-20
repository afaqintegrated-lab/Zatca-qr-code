<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Invoice QR Helper Functions
 */

/**
 * Get ZATCA QR code for invoice
 * 
 * @param int $invoice_id Invoice ID
 * @return object|null QR record
 */
function zatca_get_invoice_qr($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_qr_model');
    
    return $CI->zatca_qr_model->get_invoice_qr($invoice_id);
}

/**
 * Check if ZATCA module is enabled
 * 
 * @return bool
 */
function zatca_is_enabled()
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_settings_model');
    
    return $CI->zatca_settings_model->is_enabled();
}

/**
 * Get ZATCA settings
 * 
 * @return object Settings
 */
function zatca_get_settings()
{
    $CI = &get_instance();
    $CI->load->model('zatca_invoice_qr/zatca_settings_model');
    
    return $CI->zatca_settings_model->get_settings();
}

/**
 * Format VAT number for display
 * 
 * @param string $vat_number 15-digit VAT number
 * @return string Formatted VAT number
 */
function zatca_format_vat_number($vat_number)
{
    // Remove any existing formatting
    $clean = preg_replace('/[\s\-]/', '', $vat_number);
    
    // Format as: 123-456-789-01234
    if (strlen($clean) === 15) {
        return substr($clean, 0, 3) . '-' . 
               substr($clean, 3, 3) . '-' . 
               substr($clean, 6, 3) . '-' . 
               substr($clean, 9, 5);
    }
    
    return $vat_number;
}

/**
 * Decode ZATCA QR code TLV data (for debugging)
 * 
 * @param string $tlv_base64 Base64 encoded TLV
 * @return array Decoded tags
 */
function zatca_decode_qr($tlv_base64)
{
    $CI = &get_instance();
    $CI->load->library('zatca_invoice_qr/libraries/zatca_core/Zatca_tlv_generator');
    
    return $CI->zatca_tlv_generator->decode_tlv($tlv_base64);
}

/**
 * Get QR code image HTML
 * 
 * @param int $invoice_id Invoice ID
 * @param array $attributes HTML attributes
 * @return string HTML img tag or empty string
 */
function zatca_qr_image_html($invoice_id, $attributes = [])
{
    $qr = zatca_get_invoice_qr($invoice_id);
    
    if (!$qr || !$qr->qr_base64) {
        return '';
    }
    
    // Default attributes
    $defaults = [
        'alt'   => 'ZATCA QR Code',
        'class' => 'zatca-qr-code',
        'style' => 'max-width: 200px;'
    ];
    
    $attributes = array_merge($defaults, $attributes);
    
    // Build HTML
    $html = '<img src="' . $qr->qr_base64 . '"';
    
    foreach ($attributes as $key => $value) {
        $html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
    }
    
    $html .= ' />';
    
    return $html;
}

/**
 * Check if invoice has QR code
 * 
 * @param int $invoice_id Invoice ID
 * @return bool
 */
function zatca_invoice_has_qr($invoice_id)
{
    $qr = zatca_get_invoice_qr($invoice_id);
    
    return $qr && $qr->status === 'generated' && !empty($qr->qr_base64);
}

/**
 * Get module version
 * 
 * @return string Version number
 */
function zatca_get_version()
{
    return ZATCA_INVOICE_QR_VERSION;
}

/**
 * Format bytes to human readable size
 * 
 * @param int $bytes Bytes
 * @return string Formatted size
 */
function zatca_format_bytes($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $index = 0;
    
    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }
    
    return round($bytes, 2) . ' ' . $units[$index];
}
