<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Zatca_qr_code_controller extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Load your ZATCA QR Code model
        $this->load->model('zatca_qr_code/zatca_qr_code_model', 'zatca_model');

        // Ensure permissions are checked for this controller
        if (!has_permission('zatca_qr_code', '', 'view')) {
            access_denied('zatca_qr_code');
        }
    }

    public function settings()
    {
        // Check if the form was submitted for saving settings
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->zatca_model->save_settings($data)) {
                set_alert('success', _l('settings_updated'));
            } else {
                set_alert('warning', _l('settings_not_updated'));
            }
            redirect(admin_url('zatca_qr_code/settings')); // Redirect back to settings page
        }

        // Fetch settings from your database table to pre-fill the form
        $data['settings'] = $this->zatca_model->get_settings();

        $data['title'] = _l('zatca_qr_code_module_settings'); // Title for the page
        $this->load->view('zatca_qr_code/settings_view', $data); // Load your settings view
      //  $this->load->view('admin/includes/footer'); // Load admin footer
    }
}