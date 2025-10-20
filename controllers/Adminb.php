<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * ZATCA_Qr_Code Admin Controller
 * Manages the settings for the ZATCA QR Code module in the Perfex CRM admin panel.
 */
class Admin extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('zatca_qr_code/zatca_qr_code_model');
        $this->load->library('zatca_qr_code/zatca_qr_code_lib');
        $this->load->helper('perfex_misc');
        $this->lang->load('zatca_qr_code', 'english'); 
    }

    /**
     * Display and handle ZATCA QR Code module settings,
     * and handle PDF template copy feature.
     */
    public function settings()
    {
        if (!has_permission('zatca_qr_code', '', 'view')) {
            access_denied('ZATCA QR Code');
        } 

        // Handle PDF template copy request
        if ($this->input->post('copy_pdf_templates')) {
            $this->handle_pdf_template_copy();
            redirect(admin_url('zatca_qr_code/admin/settings'));
        }

        // Handle normal settings save
        if ($this->input->post() && !$this->input->post('copy_pdf_templates')) {
            $post_data = $this->input->post();

            $data_to_save = [
                'enable_qr'   => isset($post_data['enable_qr']) ? 1 : 0,
                'seller_name' => trim($post_data['seller_name']),
                'vat_number'  => trim($post_data['vat_number']),
                'qr_size'     => (int)$post_data['qr_size'],
            ];

            if ($data_to_save['qr_size'] < 50 || $data_to_save['qr_size'] > 500) {
                set_alert('danger', _l('zatca_qr_code_qr_size_invalid'));
                redirect(admin_url('zatca_qr_code/admin/settings'));
            }

            $success = $this->zatca_qr_code_model->update_settings($data_to_save);

            if ($success) {
                set_alert('success', _l('settings_updated'));
            } else {
                set_alert('warning', _l('settings_updated_but_no_changes'));
            }

            redirect(admin_url('zatca_qr_code/admin/settings'));
        }

        // Fetch current settings
        $current_settings = $this->zatca_qr_code_model->get_settings();
        if (!$current_settings) {
            $current_settings = (object)[
                'enable_qr'   => 0,
                'seller_name' => get_option('companyname'),
                'vat_number'  => '',
                'qr_size'     => 150,
            ];
        }

        $data['title']                      = _l('zatca_qr_code_settings_title');
        $data['zatca_qr_code_enable']       = $current_settings->enable_qr;
        $data['zatca_qr_code_seller_name']  = $current_settings->seller_name;
        $data['zatca_qr_code_vat_number']   = $current_settings->vat_number;
        $data['zatca_qr_code_qr_size']      = $current_settings->qr_size;

        // Check if template files exist to warn the user in the view
        $data['pdf_templates_exist'] = $this->check_pdf_templates_exist();

        $this->load->view('zatca_qr_code/admin/settings', $data);
    }

    /**
     * Check if PDF templates already exist in the destination.
     * @return array
     */
    private function check_pdf_templates_exist()
    {
        $dest_dir = APPPATH . 'views/themes/perfex/views/';
        $files = ['invoicepdf.php', 'proposalpdf.php'];
        $exists = [];
        foreach ($files as $file) {
            $exists[$file] = file_exists($dest_dir . $file);
        }
        return $exists;
    }

    /**
     * Handle copying the PDF template files with overwrite warning.
     */
    private function handle_pdf_template_copy()
    {
        $source_dir = module_dir_path('zatca_qr_code', 'views/');
        $dest_dir   = APPPATH . 'views/themes/perfex/views/';
        $files = ['invoicepdf.php', 'proposalpdf.php'];

        $overwrite = $this->input->post('confirm_pdf_overwrite') == 'yes';

        // Check for existing files
        $existing = [];
        foreach ($files as $file) {
            if (file_exists($dest_dir . $file)) {
                $existing[] = $file;
            }
        }

        if (!empty($existing) && !$overwrite) {
            // User hasn't confirmed overwrite yet. Show warning.
            $msg = _l('zatca_qr_code_pdf_template_exists') . ': ' . implode(', ', $existing) . 
                '<br>' . _l('zatca_qr_code_pdf_template_overwrite_confirm');
            set_alert('warning', $msg);
            return;
        }

        // Proceed to copy files
        $copied = [];
        $failed = [];
        foreach ($files as $file) {
            $src = $source_dir . $file;
            $dest = $dest_dir . $file;
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0755, true);
            }
            if (copy($src, $dest)) {
                $copied[] = $file;
            } else {
                $failed[] = $file;
            }
        }

        if (!empty($copied)) {
            set_alert('success', _l('zatca_qr_code_pdf_templates_copied') . ': ' . implode(', ', $copied));
        }
        if (!empty($failed)) {
            set_alert('danger', _l('zatca_qr_code_pdf_templates_copy_failed') . ': ' . implode(', ', $failed));
        }
    }
}