<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Settings Model
 * Handles CRUD operations for module settings
 */
class Zatca_settings_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'zatca_settings';
    }

    /**
     * Get module settings
     * 
     * @return object|null Settings object
     */
    public function get_settings()
    {
        $settings = $this->db->get($this->table)->row();
        
        if (!$settings) {
            // Return default settings if not found
            return $this->get_default_settings();
        }
        
        return $settings;
    }

    /**
     * Get default settings structure
     * 
     * @return object Default settings
     */
    private function get_default_settings()
    {
        return (object) [
            'id'             => 1,
            'enabled'        => 0,
            'phase'          => 'phase1',
            'environment'    => 'sandbox',
            'seller_name'    => '',
            'vat_number'     => '',
            'company_address' => '',
            'qr_position'    => 'top-right',
            'qr_size'        => 150,
            'auto_generate'  => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Update module settings
     * 
     * @param array $data Settings data
     * @return bool Success status
     */
    public function update_settings($data)
    {
        // Remove CSRF token if present
        if (isset($data[$this->security->get_csrf_token_name()])) {
            unset($data[$this->security->get_csrf_token_name()]);
        }

        // Ensure boolean fields are 0 or 1
        if (isset($data['enabled'])) {
            $data['enabled'] = (int) $data['enabled'];
        }
        
        if (isset($data['auto_generate'])) {
            $data['auto_generate'] = (int) $data['auto_generate'];
        }

        // Set updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Check if settings exist
        $existing = $this->db->get_where($this->table, ['id' => 1])->row();

        if ($existing) {
            // Update existing settings
            $this->db->where('id', 1);
            $this->db->update($this->table, $data);
        } else {
            // Insert new settings
            $data['id'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $data);
        }

        if ($this->db->affected_rows() > 0) {
            log_activity('ZATCA Settings Updated');
            return true;
        }

        return false;
    }

    /**
     * Check if module is enabled
     * 
     * @return bool
     */
    public function is_enabled()
    {
        $settings = $this->get_settings();
        return isset($settings->enabled) && $settings->enabled == 1;
    }

    /**
     * Get current phase (phase1 or phase2)
     * 
     * @return string
     */
    public function get_phase()
    {
        $settings = $this->get_settings();
        return $settings->phase ?? 'phase1';
    }

    /**
     * Get seller information
     * 
     * @return array Seller details
     */
    public function get_seller_info()
    {
        $settings = $this->get_settings();
        
        return [
            'seller_name' => $settings->seller_name ?? '',
            'vat_number'  => $settings->vat_number ?? '',
            'address'     => $settings->company_address ?? '',
        ];
    }

    /**
     * Validate VAT number format
     * 
     * @param string $vat_number
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validate_vat_number($vat_number)
    {
        // Remove spaces and dashes
        $vat_clean = preg_replace('/[\s\-]/', '', $vat_number);
        
        // Saudi VAT numbers are 15 digits
        if (!preg_match('/^\d{15}$/', $vat_clean)) {
            return [
                'valid'   => false,
                'message' => 'VAT number must be exactly 15 digits'
            ];
        }

        return [
            'valid'   => true,
            'message' => 'VAT number is valid'
        ];
    }

    /**
     * Check if settings are properly configured
     * 
     * @return array ['configured' => bool, 'missing' => array]
     */
    public function check_configuration()
    {
        $settings = $this->get_settings();
        $missing = [];

        // Required fields
        if (empty($settings->seller_name)) {
            $missing[] = 'seller_name';
        }

        if (empty($settings->vat_number)) {
            $missing[] = 'vat_number';
        } else {
            // Validate VAT number
            $validation = $this->validate_vat_number($settings->vat_number);
            if (!$validation['valid']) {
                $missing[] = 'vat_number (invalid format)';
            }
        }

        return [
            'configured' => empty($missing),
            'missing'    => $missing
        ];
    }

    /**
     * Get QR positioning options
     * 
     * @return array
     */
    public function get_position_options()
    {
        return [
            'top-right'     => _l('zatca_qr_position_top_right'),
            'top-left'      => _l('zatca_qr_position_top_left'),
            'bottom-right'  => _l('zatca_qr_position_bottom_right'),
            'bottom-left'   => _l('zatca_qr_position_bottom_left'),
        ];
    }

    /**
     * Reset settings to defaults
     * 
     * @return bool Success status
     */
    public function reset_to_defaults()
    {
        $defaults = $this->get_default_settings();
        $defaults = (array) $defaults;
        
        unset($defaults['id']); // Don't reset ID
        
        $this->db->where('id', 1);
        $this->db->update($this->table, $defaults);

        log_activity('ZATCA Settings Reset to Defaults');
        
        return $this->db->affected_rows() > 0;
    }
}
