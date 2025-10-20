<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA QR Model
 * Handles QR code generation and management for invoices
 */
class Zatca_qr_model extends App_Model
{
    private $table;
    private $settings_model;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'zatca_invoice_qr';
        
        // Load dependencies
        $this->load->model('zatca_invoice_qr/zatca_settings_model');
        $this->load->library('zatca_invoice_qr/libraries/zatca_core/Zatca_tlv_generator');
        $this->load->library('zatca_invoice_qr/libraries/Zatca_qr_image');
        
        $this->settings_model = $this->zatca_settings_model;
    }

    /**
     * Get settings (shorthand method)
     * 
     * @return object Settings
     */
    public function get_settings()
    {
        return $this->settings_model->get_settings();
    }

    /**
     * Generate QR code for an invoice
     * 
     * @param int $invoice_id Invoice ID
     * @return object|false QR record or false on failure
     */
    public function generate_invoice_qr($invoice_id)
    {
        try {
            // Get invoice data
            $invoice = $this->db->get_where(db_prefix() . 'invoices', ['id' => $invoice_id])->row();
            
            if (!$invoice) {
                throw new Exception('Invoice not found');
            }

            // Get settings
            $settings = $this->get_settings();
            
            if (!$settings->enabled) {
                throw new Exception('ZATCA QR module is not enabled');
            }

            // Check configuration
            $config_check = $this->settings_model->check_configuration();
            if (!$config_check['configured']) {
                throw new Exception('Module not properly configured: ' . implode(', ', $config_check['missing']));
            }

            // Prepare invoice data for QR generation
            $qr_data = $this->prepare_invoice_data($invoice, $settings);

            // Generate QR based on phase
            if ($settings->phase === 'phase1') {
                $tlv_base64 = $this->zatca_tlv_generator->generate_phase1_qr($qr_data);
            } else {
                // Phase 2 (will be implemented later)
                $tlv_base64 = $this->zatca_tlv_generator->generate_phase2_qr($qr_data);
            }

            // Generate QR code image
            $qr_image_base64 = $this->zatca_qr_image->generate_base64($tlv_base64, $settings->qr_size);

            // Save to database
            $qr_record = [
                'invoice_id'      => $invoice_id,
                'qr_data'         => $tlv_base64,
                'qr_base64'       => $qr_image_base64,
                'generation_date' => date('Y-m-d H:i:s'),
                'status'          => 'generated',
                'phase'           => $settings->phase,
            ];

            // Check if QR already exists for this invoice
            $existing = $this->get_invoice_qr($invoice_id);
            
            if ($existing) {
                // Update existing
                $this->db->where('invoice_id', $invoice_id);
                $this->db->update($this->table, $qr_record);
                $qr_id = $existing->id;
            } else {
                // Insert new
                $this->db->insert($this->table, $qr_record);
                $qr_id = $this->db->insert_id();
            }

            log_activity('ZATCA QR Code Generated', [
                'invoice_id' => $invoice_id,
                'phase'      => $settings->phase
            ]);

            // Return the generated record
            return $this->db->get_where($this->table, ['id' => $qr_id])->row();

        } catch (Exception $e) {
            // Log error
            $error_data = [
                'invoice_id'      => $invoice_id,
                'qr_data'         => '',
                'generation_date' => date('Y-m-d H:i:s'),
                'status'          => 'failed',
                'error_message'   => $e->getMessage(),
            ];

            // Check if record exists
            $existing = $this->get_invoice_qr($invoice_id);
            
            if ($existing) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->update($this->table, $error_data);
            } else {
                $this->db->insert($this->table, $error_data);
            }

            log_activity('ZATCA QR Code Generation Failed: ' . $e->getMessage(), [
                'invoice_id' => $invoice_id
            ]);

            return false;
        }
    }

    /**
     * Prepare invoice data for QR generation
     * 
     * @param object $invoice Invoice object
     * @param object $settings Module settings
     * @return array Prepared data
     */
    private function prepare_invoice_data($invoice, $settings)
    {
        // Calculate totals
        $subtotal = $invoice->subtotal ?? 0;
        $total = $invoice->total ?? 0;
        $vat_amount = $total - $subtotal;

        // Format invoice date and time
        $invoice_date = date('Y-m-d', strtotime($invoice->date ?? 'now'));
        $invoice_time = date('H:i:s'); // Use current time if not available

        return [
            'seller_name'    => $settings->seller_name,
            'vat_number'     => $settings->vat_number,
            'invoice_date'   => $invoice_date,
            'invoice_time'   => $invoice_time,
            'invoice_total'  => (float) $total,
            'vat_amount'     => (float) $vat_amount,
        ];
    }

    /**
     * Get QR code for an invoice
     * 
     * @param int $invoice_id Invoice ID
     * @return object|null QR record
     */
    public function get_invoice_qr($invoice_id)
    {
        return $this->db->get_where($this->table, ['invoice_id' => $invoice_id])->row();
    }

    /**
     * Regenerate QR code for an invoice
     * 
     * @param int $invoice_id Invoice ID
     * @return object|false QR record or false
     */
    public function regenerate_invoice_qr($invoice_id)
    {
        // Delete existing QR
        $this->delete_invoice_qr($invoice_id);
        
        // Generate new QR
        return $this->generate_invoice_qr($invoice_id);
    }

    /**
     * Delete QR code for an invoice
     * 
     * @param int $invoice_id Invoice ID
     * @return bool Success status
     */
    public function delete_invoice_qr($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete($this->table);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Batch generate QR codes for multiple invoices
     * 
     * @param array $invoice_ids Array of invoice IDs
     * @return array ['success' => int, 'failed' => int, 'results' => array]
     */
    public function batch_generate($invoice_ids)
    {
        $success = 0;
        $failed = 0;
        $results = [];

        foreach ($invoice_ids as $invoice_id) {
            $result = $this->generate_invoice_qr($invoice_id);
            
            if ($result) {
                $success++;
                $results[$invoice_id] = ['status' => 'success', 'qr_id' => $result->id];
            } else {
                $failed++;
                $results[$invoice_id] = ['status' => 'failed'];
            }
        }

        log_activity("ZATCA Batch Generation: {$success} succeeded, {$failed} failed");

        return [
            'success' => $success,
            'failed'  => $failed,
            'results' => $results
        ];
    }

    /**
     * Get all invoices without QR codes
     * 
     * @param int $limit Limit results
     * @return array Invoice IDs
     */
    public function get_invoices_without_qr($limit = 100)
    {
        $sql = "SELECT i.id 
                FROM " . db_prefix() . "invoices i 
                LEFT JOIN " . $this->table . " q ON i.id = q.invoice_id 
                WHERE q.id IS NULL 
                LIMIT ?";
        
        $query = $this->db->query($sql, [$limit]);
        
        $invoice_ids = [];
        foreach ($query->result() as $row) {
            $invoice_ids[] = $row->id;
        }
        
        return $invoice_ids;
    }

    /**
     * Get statistics
     * 
     * @return array Statistics
     */
    public function get_statistics()
    {
        // Total invoices
        $total_invoices = $this->db->count_all_results(db_prefix() . 'invoices');
        
        // Invoices with QR
        $invoices_with_qr = $this->db->where('status', 'generated')->count_all_results($this->table);
        
        // Failed QR generations
        $failed_qr = $this->db->where('status', 'failed')->count_all_results($this->table);
        
        // Invoices without QR
        $invoices_without_qr = $total_invoices - $invoices_with_qr;

        return [
            'total_invoices'       => $total_invoices,
            'invoices_with_qr'     => $invoices_with_qr,
            'invoices_without_qr'  => $invoices_without_qr,
            'failed_generations'   => $failed_qr,
            'success_rate'         => $total_invoices > 0 ? round(($invoices_with_qr / $total_invoices) * 100, 2) : 0
        ];
    }
}
