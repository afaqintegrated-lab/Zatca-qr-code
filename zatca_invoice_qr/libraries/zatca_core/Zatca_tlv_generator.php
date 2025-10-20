<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA TLV (Tag-Length-Value) Generator
 * 
 * Generates ZATCA compliant QR codes using TLV encoding
 * 
 * Phase 1 (Simplified): 5 fields
 * - Tag 1: Seller Name
 * - Tag 2: VAT Registration Number
 * - Tag 3: Invoice Date & Time (ISO 8601)
 * - Tag 4: Invoice Total (with VAT)
 * - Tag 5: VAT Amount
 * 
 * Phase 2 (Signed): Additional 4 fields
 * - Tag 6: Invoice Hash (SHA-256)
 * - Tag 7: Digital Signature (ECDSA)
 * - Tag 8: Public Key
 * - Tag 9: Certificate Signature
 */
class Zatca_tlv_generator
{
    /**
     * Generate Phase 1 QR Code (Simplified - 5 fields)
     * 
     * @param array $data Invoice data
     * @return string Base64 encoded TLV string
     */
    public function generate_phase1_qr($data)
    {
        // Validate required fields
        $required = ['seller_name', 'vat_number', 'invoice_date', 'invoice_total', 'vat_amount'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Validate VAT number format (15 digits for Saudi Arabia)
        if (!$this->validate_vat_number($data['vat_number'])) {
            throw new Exception("Invalid VAT number format. Expected 15 digits.");
        }

        // Validate invoice date format (ISO 8601)
        $invoice_datetime = $this->format_invoice_datetime($data['invoice_date'], $data['invoice_time'] ?? null);

        // Build TLV structure
        $tlv = '';
        
        // Tag 1: Seller Name
        $tlv .= $this->create_tlv(1, $data['seller_name']);
        
        // Tag 2: VAT Registration Number
        $tlv .= $this->create_tlv(2, $data['vat_number']);
        
        // Tag 3: Invoice Date & Time (ISO 8601 format: YYYY-MM-DDThh:mm:ssZ)
        $tlv .= $this->create_tlv(3, $invoice_datetime);
        
        // Tag 4: Invoice Total (with VAT)
        $tlv .= $this->create_tlv(4, number_format($data['invoice_total'], 2, '.', ''));
        
        // Tag 5: VAT Amount
        $tlv .= $this->create_tlv(5, number_format($data['vat_amount'], 2, '.', ''));

        // Encode to Base64
        return base64_encode($tlv);
    }

    /**
     * Generate Phase 2 QR Code (Signed - 9 fields)
     * 
     * @param array $data Invoice data with signature details
     * @return string Base64 encoded TLV string
     */
    public function generate_phase2_qr($data)
    {
        // First generate Phase 1 TLV (tags 1-5)
        $tlv_base64 = $this->generate_phase1_qr($data);
        $tlv = base64_decode($tlv_base64);

        // Validate Phase 2 required fields
        $required_phase2 = ['invoice_hash', 'digital_signature', 'public_key', 'certificate_signature'];
        foreach ($required_phase2 as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing Phase 2 required field: {$field}");
            }
        }

        // Tag 6: Invoice Hash (SHA-256, Base64)
        $tlv .= $this->create_tlv(6, $data['invoice_hash']);
        
        // Tag 7: Digital Signature (ECDSA, Base64)
        $tlv .= $this->create_tlv(7, $data['digital_signature']);
        
        // Tag 8: Public Key (Base64)
        $tlv .= $this->create_tlv(8, $data['public_key']);
        
        // Tag 9: Certificate Signature (Base64)
        $tlv .= $this->create_tlv(9, $data['certificate_signature']);

        // Encode to Base64
        return base64_encode($tlv);
    }

    /**
     * Create TLV (Tag-Length-Value) entry
     * 
     * @param int $tag Tag number (1-9)
     * @param string $value Value to encode
     * @return string Binary TLV string
     */
    private function create_tlv($tag, $value)
    {
        // Convert value to UTF-8 if not already
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        // Get byte length of value
        $length = strlen($value);
        
        // Tag (1 byte) + Length (1 byte) + Value (variable)
        // Using pack: C = unsigned char (1 byte)
        return pack('C', $tag) . pack('C', $length) . $value;
    }

    /**
     * Validate VAT number format
     * Saudi VAT numbers are 15 digits
     * 
     * @param string $vat_number
     * @return bool
     */
    private function validate_vat_number($vat_number)
    {
        // Remove any spaces or dashes
        $vat_number = preg_replace('/[\s\-]/', '', $vat_number);
        
        // Check if exactly 15 digits
        return preg_match('/^\d{15}$/', $vat_number);
    }

    /**
     * Format invoice date and time to ISO 8601
     * 
     * @param string $date Date in Y-m-d format
     * @param string|null $time Time in H:i:s format (optional)
     * @return string ISO 8601 formatted datetime
     */
    private function format_invoice_datetime($date, $time = null)
    {
        try {
            // If time is not provided, use current time
            if (!$time) {
                $time = date('H:i:s');
            }

            // Combine date and time
            $datetime = $date . ' ' . $time;
            
            // Create DateTime object
            $dt = new DateTime($datetime, new DateTimeZone('Asia/Riyadh'));
            
            // Format to ISO 8601: YYYY-MM-DDThh:mm:ssZ
            return $dt->format('Y-m-d\TH:i:s\Z');
            
        } catch (Exception $e) {
            throw new Exception("Invalid date/time format: " . $e->getMessage());
        }
    }

    /**
     * Decode TLV Base64 string (for debugging/verification)
     * 
     * @param string $tlv_base64 Base64 encoded TLV
     * @return array Decoded tags and values
     */
    public function decode_tlv($tlv_base64)
    {
        $tlv = base64_decode($tlv_base64);
        $result = [];
        $offset = 0;
        $length = strlen($tlv);

        while ($offset < $length) {
            // Read tag (1 byte)
            $tag = unpack('C', substr($tlv, $offset, 1))[1];
            $offset += 1;

            // Read length (1 byte)
            $value_length = unpack('C', substr($tlv, $offset, 1))[1];
            $offset += 1;

            // Read value
            $value = substr($tlv, $offset, $value_length);
            $offset += $value_length;

            $result[$tag] = $value;
        }

        return $result;
    }

    /**
     * Generate invoice hash (SHA-256) for Phase 2
     * 
     * @param string $invoice_xml UBL XML invoice
     * @return string Base64 encoded SHA-256 hash
     */
    public function generate_invoice_hash($invoice_xml)
    {
        // SHA-256 hash
        $hash = hash('sha256', $invoice_xml, true);
        
        // Encode to Base64
        return base64_encode($hash);
    }

    /**
     * Validate QR code size
     * 
     * @param string $qr_data Base64 encoded QR data
     * @return array ['valid' => bool, 'size' => int, 'message' => string]
     */
    public function validate_qr_size($qr_data)
    {
        $decoded = base64_decode($qr_data);
        $size = strlen($decoded);
        
        // ZATCA recommends QR data should not exceed 500 bytes
        $max_size = 500;
        
        return [
            'valid'   => $size <= $max_size,
            'size'    => $size,
            'message' => $size <= $max_size 
                ? "QR code size is valid ({$size} bytes)" 
                : "QR code exceeds maximum size ({$size}/{$max_size} bytes)"
        ];
    }
}
