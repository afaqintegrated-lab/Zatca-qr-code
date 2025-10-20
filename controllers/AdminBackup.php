<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA_Qr_Code Admin Controller
 * Manages the settings for the ZATCA QR Code module in the Perfex CRM admin panel.
 */
class Admin extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the custom model responsible for interacting with the zatca_qr_settings table
        $this->load->model('zatca_qr_code/zatca_qr_code_model');
        // Load the custom library for QR code generation logic (though not directly used in settings page, good to have it loaded for module context)
        $this->load->library('zatca_qr_code/zatca_qr_code_lib');
        // Helper for common Perfex functions like set_alert, admin_url etc. (often autoloaded, but good to ensure if specific functions are used)
        $this->load->helper('perfex_misc');
    }

    /**
     * Display and handle ZATCA QR Code module settings.
     */
    public function settings()
    {
        // Check for necessary permissions to view module settings
        // The capability 'zatca_qr_code' and feature 'view' should be defined in install.php
        if (!has_permission('zatca_qr_code', '', 'view')) {
            access_denied('ZATCA QR Code'); // Display access denied message and redirect
        }

        // Handle POST request for saving settings
        if ($this->input->post()) {
            $post_data = $this->input->post();

            // Prepare data for updating in the database
            $data_to_save = [
                // Checkbox value: 1 if checked, 0 if not (checkboxes don't send value if unchecked)
                'enable_qr'   => isset($post_data['enable_qr']) ? 1 : 0,
                'seller_name' => trim($post_data['seller_name']), // Trim whitespace
                'vat_number'  => trim($post_data['vat_number']), // Trim whitespace
                'qr_size'     => (int)$post_data['qr_size'],     // Cast to integer
            ];

            // Basic validation for QR size
            if ($data_to_save['qr_size'] < 50 || $data_to_save['qr_size'] > 500) {
                set_alert('danger', _l('zatca_qr_code_qr_size_invalid')); // Use a language string for this
                redirect(admin_url('zatca_qr_code/admin/settings'));
            }

            // Update settings using the model. Assuming there's always one row with ID 1.
            $success = $this->zatca_qr_code_model->update_settings($data_to_save);

            if ($success) {
                set_alert('success', _l('settings_updated')); // Perfex default success message
            } else {
                set_alert('warning', _l('settings_updated_but_no_changes')); // Perfex default message for no changes
                // Or set_alert('danger', _l('settings_update_failed')); // Custom error if update genuinely fails
            }

            // Redirect back to the settings page to show alerts and updated values
            redirect(admin_url('zatca_qr_code/admin/settings'));
        }

        // Fetch current settings for displaying in the form
        $current_settings = $this->zatca_qr_code_model->get_settings();

        // If for some reason settings are not found (e.g., install failed),
        // provide default fallback values to prevent errors in the view.
        if (!$current_settings) {
            $current_settings = (object)[
                'enable_qr'   => 0,
                'seller_name' => get_option('companyname'), // Fallback to company name
                'vat_number'  => '',
                'qr_size'     => 150,
            ];
            // Optionally, try to re-insert default settings if they are missing
            // $this->zatca_qr_code_model->update_settings((array)$current_settings);
        }

        // Prepare data to be passed to the view
        $data['title']                   = _l('zatca_qr_code_settings_title'); // Page title
        $data['zatca_qr_code_enable']    = $current_settings->enable_qr;
        $data['zatca_qr_code_seller_name'] = $current_settings->seller_name;
        $data['zatca_qr_code_vat_number']  = $current_settings->vat_number;
        $data['zatca_qr_code_qr_size']     = $current_settings->qr_size;

        // Load the view for the settings page
        $this->load->view('zatca_qr_code/admin/settings', $data);
    }
}