<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Admin Controller
 * Handles admin settings and management interface
 */
class Zatca_admin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        // Load models
        $this->load->model('zatca_invoice_qr/zatca_settings_model');
        $this->load->model('zatca_invoice_qr/zatca_qr_model');
        
        // Load language
        $this->load->language('zatca_invoice_qr');
        
        // Load helper
        $this->load->helper('zatca_invoice_qr');
        
        // Check permissions
        if (!has_permission('zatca_invoice_qr', '', 'view') && !is_admin()) {
            access_denied('ZATCA Invoice QR');
        }
    }

    /**
     * Settings page
     */
    public function settings()
    {
        // Handle POST request
        if ($this->input->post()) {
            $this->handle_settings_save();
            return;
        }

        // Get current settings
        $data['settings'] = $this->zatca_settings_model->get_settings();
        
        // Get statistics
        $data['statistics'] = $this->zatca_qr_model->get_statistics();
        
        // Get position options
        $data['position_options'] = $this->zatca_settings_model->get_position_options();
        
        // Check configuration status
        $data['config_check'] = $this->zatca_settings_model->check_configuration();
        
        // Page title
        $data['title'] = _l('zatca_qr_module_name');
        
        // Load view
        $this->load->view('admin/settings', $data);
    }

    /**
     * Handle settings save
     */
    private function handle_settings_save()
    {
        // Check edit permission
        if (!has_permission('zatca_invoice_qr', '', 'edit') && !is_admin()) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('zatca_invoice_qr/zatca_admin/settings'));
            return;
        }

        $post_data = $this->input->post();
        
        // Prepare data for save
        $settings_data = [
            'enabled'         => isset($post_data['enabled']) ? 1 : 0,
            'phase'           => $post_data['phase'] ?? 'phase1',
            'environment'     => $post_data['environment'] ?? 'sandbox',
            'seller_name'     => trim($post_data['seller_name'] ?? ''),
            'vat_number'      => preg_replace('/[\s\-]/', '', $post_data['vat_number'] ?? ''),
            'company_address' => trim($post_data['company_address'] ?? ''),
            'qr_position'     => $post_data['qr_position'] ?? 'top-right',
            'qr_size'         => (int) ($post_data['qr_size'] ?? 150),
            'auto_generate'   => isset($post_data['auto_generate']) ? 1 : 0,
        ];

        // Validate VAT number
        if (!empty($settings_data['vat_number'])) {
            $validation = $this->zatca_settings_model->validate_vat_number($settings_data['vat_number']);
            if (!$validation['valid']) {
                set_alert('danger', $validation['message']);
                redirect(admin_url('zatca_invoice_qr/zatca_admin/settings'));
                return;
            }
        }

        // Save settings
        if ($this->zatca_settings_model->update_settings($settings_data)) {
            set_alert('success', _l('settings_updated'));
        } else {
            set_alert('warning', _l('settings_not_updated'));
        }

        redirect(admin_url('zatca_invoice_qr/zatca_admin/settings'));
    }

    /**
     * Test QR generation
     */
    public function test_qr()
    {
        // Check permission
        if (!has_permission('zatca_invoice_qr', '', 'view') && !is_admin()) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        try {
            // Load library
            $this->load->library('zatca_invoice_qr/libraries/zatca_core/Zatca_tlv_generator');
            $this->load->library('zatca_invoice_qr/libraries/Zatca_qr_image');
            
            // Get settings
            $settings = $this->zatca_settings_model->get_settings();
            
            // Test data
            $test_data = [
                'seller_name'    => $settings->seller_name ?: 'Test Company',
                'vat_number'     => $settings->vat_number ?: '310122393500003',
                'invoice_date'   => date('Y-m-d'),
                'invoice_time'   => date('H:i:s'),
                'invoice_total'  => 1150.00,
                'vat_amount'     => 150.00,
            ];
            
            // Generate TLV
            $tlv_base64 = $this->zatca_tlv_generator->generate_phase1_qr($test_data);
            
            // Generate QR image
            $qr_image = $this->zatca_qr_image->generate_base64($tlv_base64, $settings->qr_size);
            
            // Decode for verification
            $decoded = $this->zatca_tlv_generator->decode_tlv($tlv_base64);
            
            // Validate size
            $size_check = $this->zatca_tlv_generator->validate_qr_size($tlv_base64);
            
            echo json_encode([
                'success'     => true,
                'qr_image'    => $qr_image,
                'qr_data'     => $tlv_base64,
                'decoded'     => $decoded,
                'size_check'  => $size_check,
                'test_data'   => $test_data
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Batch generate QR codes for existing invoices
     */
    public function batch_generate()
    {
        // Check permission
        if (!has_permission('zatca_invoice_qr', '', 'create') && !is_admin()) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $limit = (int) ($this->input->post('limit') ?? 50);
        
        // Get invoices without QR
        $invoice_ids = $this->zatca_qr_model->get_invoices_without_qr($limit);
        
        if (empty($invoice_ids)) {
            echo json_encode([
                'success' => true,
                'message' => _l('zatca_no_invoices_to_generate'),
                'generated' => 0
            ]);
            return;
        }
        
        // Batch generate
        $result = $this->zatca_qr_model->batch_generate($invoice_ids);
        
        echo json_encode([
            'success'   => true,
            'message'   => sprintf(_l('zatca_batch_generated'), $result['success'], $result['failed']),
            'generated' => $result['success'],
            'failed'    => $result['failed'],
            'results'   => $result['results']
        ]);
    }

    /**
     * Regenerate QR for specific invoice
     */
    public function regenerate($invoice_id)
    {
        // Check permission
        if (!has_permission('zatca_invoice_qr', '', 'edit') && !is_admin()) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('invoices/list_invoices/' . $invoice_id));
            return;
        }

        $result = $this->zatca_qr_model->regenerate_invoice_qr($invoice_id);
        
        if ($result) {
            set_alert('success', _l('zatca_qr_regenerated'));
        } else {
            set_alert('danger', _l('zatca_qr_regeneration_failed'));
        }
        
        redirect(admin_url('invoices/list_invoices/' . $invoice_id));
    }

    /**
     * Delete QR for specific invoice
     */
    public function delete_qr($invoice_id)
    {
        // Check permission
        if (!has_permission('zatca_invoice_qr', '', 'delete') && !is_admin()) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $result = $this->zatca_qr_model->delete_invoice_qr($invoice_id);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? _l('zatca_qr_deleted') : _l('zatca_qr_delete_failed')
        ]);
    }

    /**
     * View QR details
     */
    public function view_qr($invoice_id)
    {
        $qr = $this->zatca_qr_model->get_invoice_qr($invoice_id);
        
        if (!$qr) {
            show_404();
            return;
        }
        
        // Decode QR data
        $this->load->library('zatca_invoice_qr/libraries/zatca_core/Zatca_tlv_generator');
        $decoded = $this->zatca_tlv_generator->decode_tlv($qr->qr_data);
        
        $data['qr'] = $qr;
        $data['decoded'] = $decoded;
        $data['invoice_id'] = $invoice_id;
        $data['title'] = _l('zatca_qr_details');
        
        $this->load->view('admin/view_qr', $data);
    }

    /**
     * Statistics dashboard
     */
    public function statistics()
    {
        $data['statistics'] = $this->zatca_qr_model->get_statistics();
        $data['settings'] = $this->zatca_settings_model->get_settings();
        $data['title'] = _l('zatca_statistics');
        
        $this->load->view('admin/statistics', $data);
    }
}
