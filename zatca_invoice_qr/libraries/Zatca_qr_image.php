<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA QR Code Image Generator
 * 
 * Generates QR code images from TLV encoded data using phpqrcode library
 */
class Zatca_qr_image
{
    private $qrcode_lib_path;
    private $temp_path;

    public function __construct()
    {
        // Path to phpqrcode library
        $this->qrcode_lib_path = ZATCA_INVOICE_QR_PATH . 'libraries/phpqrcode/qrlib.php';
        
        // Temp directory for QR code generation
        $this->temp_path = FCPATH . 'uploads/temp/zatca_qr/';
        
        // Create temp directory if it doesn't exist
        if (!is_dir($this->temp_path)) {
            mkdir($this->temp_path, 0755, true);
        }
    }

    /**
     * Generate QR code image from Base64 TLV data
     * 
     * @param string $tlv_base64 Base64 encoded TLV data
     * @param array $options QR generation options
     * @return string Base64 encoded image (data:image/png;base64,...)
     */
    public function generate($tlv_base64, $options = [])
    {
        // Load phpqrcode library
        if (!file_exists($this->qrcode_lib_path)) {
            throw new Exception('phpqrcode library not found');
        }
        
        require_once($this->qrcode_lib_path);

        // Default options
        $defaults = [
            'size'              => 150,           // Size in pixels
            'error_correction'  => 'L',           // Error correction: L, M, Q, H
            'margin'            => 2,             // Quiet zone
            'format'            => 'base64',      // Output: base64 or file
            'filename'          => null,          // Filename if format is 'file'
        ];

        $options = array_merge($defaults, $options);

        // Generate unique temp filename
        $temp_filename = $this->temp_path . 'qr_' . uniqid() . '.png';

        try {
            // Calculate module size based on desired pixel size
            // QR codes have ~25-33 modules depending on data size
            $module_size = max(1, round($options['size'] / 25));

            // Generate QR code to temp file
            QRcode::png(
                $tlv_base64,                    // Data to encode
                $temp_filename,                 // Output file
                $options['error_correction'],   // Error correction level
                $module_size,                   // Module size
                $options['margin']              // Margin (quiet zone)
            );

            // Check if file was created
            if (!file_exists($temp_filename)) {
                throw new Exception('Failed to generate QR code image');
            }

            // Return based on format
            if ($options['format'] === 'base64') {
                // Read image and convert to base64
                $image_data = file_get_contents($temp_filename);
                $base64_image = 'data:image/png;base64,' . base64_encode($image_data);
                
                // Clean up temp file
                @unlink($temp_filename);
                
                return $base64_image;
            } else {
                // Move to specified location if filename provided
                if ($options['filename']) {
                    rename($temp_filename, $options['filename']);
                    return $options['filename'];
                }
                
                return $temp_filename;
            }

        } catch (Exception $e) {
            // Clean up temp file on error
            if (file_exists($temp_filename)) {
                @unlink($temp_filename);
            }
            
            throw new Exception('QR code generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code and save to file
     * 
     * @param string $tlv_base64 Base64 encoded TLV data
     * @param string $filepath Full path where to save the file
     * @param array $options QR generation options
     * @return string Filepath of generated QR code
     */
    public function generate_to_file($tlv_base64, $filepath, $options = [])
    {
        $options['format'] = 'file';
        $options['filename'] = $filepath;
        
        return $this->generate($tlv_base64, $options);
    }

    /**
     * Generate QR code and return as base64 data URI
     * 
     * @param string $tlv_base64 Base64 encoded TLV data
     * @param int $size Size in pixels
     * @return string Base64 data URI (data:image/png;base64,...)
     */
    public function generate_base64($tlv_base64, $size = 150)
    {
        return $this->generate($tlv_base64, [
            'size'   => $size,
            'format' => 'base64'
        ]);
    }

    /**
     * Resize QR code image
     * 
     * @param string $source_file Source image file
     * @param string $dest_file Destination image file
     * @param int $new_width New width in pixels
     * @param int $new_height New height in pixels
     * @return bool Success status
     */
    public function resize_qr($source_file, $dest_file, $new_width, $new_height)
    {
        // Get source image info
        $image_info = getimagesize($source_file);
        
        if (!$image_info) {
            return false;
        }

        list($width, $height, $type) = $image_info;

        // Create source image resource
        switch ($type) {
            case IMAGETYPE_PNG:
                $source_image = imagecreatefrompng($source_file);
                break;
            case IMAGETYPE_JPEG:
                $source_image = imagecreatefromjpeg($source_file);
                break;
            case IMAGETYPE_GIF:
                $source_image = imagecreatefromgif($source_file);
                break;
            default:
                return false;
        }

        // Create destination image
        $dest_image = imagecreatetruecolor($new_width, $new_height);
        
        // Preserve transparency for PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
        }

        // Resize
        imagecopyresampled(
            $dest_image, 
            $source_image, 
            0, 0, 0, 0, 
            $new_width, 
            $new_height, 
            $width, 
            $height
        );

        // Save resized image
        $result = imagepng($dest_image, $dest_file);

        // Free memory
        imagedestroy($source_image);
        imagedestroy($dest_image);

        return $result;
    }

    /**
     * Clean up old temp QR code files
     * 
     * @param int $max_age Maximum age in seconds (default: 3600 = 1 hour)
     * @return int Number of files deleted
     */
    public function cleanup_temp_files($max_age = 3600)
    {
        $deleted = 0;
        $now = time();

        if (!is_dir($this->temp_path)) {
            return $deleted;
        }

        $files = glob($this->temp_path . 'qr_*.png');

        foreach ($files as $file) {
            if (is_file($file)) {
                $file_age = $now - filemtime($file);
                
                if ($file_age > $max_age) {
                    if (@unlink($file)) {
                        $deleted++;
                    }
                }
            }
        }

        return $deleted;
    }

    /**
     * Validate QR code readability (check if QR can be decoded)
     * Note: This is a basic check, actual QR scanning would require a QR decoder
     * 
     * @param string $image_path Path to QR code image
     * @return bool True if file exists and is a valid image
     */
    public function validate_qr_image($image_path)
    {
        if (!file_exists($image_path)) {
            return false;
        }

        $image_info = @getimagesize($image_path);
        
        return $image_info !== false && $image_info[0] > 0 && $image_info[1] > 0;
    }
}
