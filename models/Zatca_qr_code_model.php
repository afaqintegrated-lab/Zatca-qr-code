<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Zatca_qr_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get ZATCA QR Code settings
     * @return object
     */
    public function get_settings()
    {
        return $this->db->get('tblacc_zatca_qr_settings')->row();
    }

    /**
     * Save ZATCA QR Code settings
     * @param array $data
     * @return boolean
     */
    public function save_settings($data)
    {
        // Unset csrf_token_name if present, as it's not a database field
        if (isset($data[$this->security->get_csrf_token_name()])) {
            unset($data[$this->security->get_csrf_token_name()]);
        }

        // Ensure enable_qr is 0 or 1
        $data['enable_qr'] = isset($data['enable_qr']) ? 1 : 0;
        $data['qr_size'] = (int) $data['qr_size']; // Cast to integer

        // Check if settings row exists
        $existing = $this->db->get_where('tblacc_zatca_qr_settings', array('id' => 1))->row();

        if ($existing) {
            // Update existing row
            $this->db->where('id', 1);
            $this->db->update('tblacc_zatca_qr_settings', $data);
            return true;
        } else {
            // Insert new row
            $data['id'] = 1;
            $this->db->insert('tblacc_zatca_qr_settings', $data);
            return $this->db->affected_rows() > 0;
        }
    }
}