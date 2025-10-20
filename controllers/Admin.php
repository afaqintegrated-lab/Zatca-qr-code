<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends AdminController
{
    private $settings_table_name;

    public function __construct()
    {
        parent::__construct();
        // Ensure this matches your actual table name in the database
        // Based on your install.php, this should be 'tblacc_zatca_qr_settings'
        $this->settings_table_name = 'tblacc_zatca_qr_settings';
        $this->load->language('zatca_qr_code', 'english');

        // Ensure user has 'view' permission for the module or is an admin
        if (!has_permission('zatca_qr_code', '', 'view') && !is_admin()) {
            access_denied('ZATCA QR Code Settings');
        }
    }

    /**
     * Handles displaying and saving the module settings.
     */
    public function settings()
    {
        $data = [];
        // Fetch current settings directly from the database
        $settings_data = $this->db->get($this->settings_table_name)->row();

        if ($settings_data) {
            $data['enable_qr']     = $settings_data->enable_qr;
            $data['seller_name']   = $settings_data->seller_name;
            $data['vat_number']    = $settings_data->vat_number;
            $data['qr_code_size']  = isset($settings_data->qr_size) ? $settings_data->qr_size : 200;
        } else {
            // Default values if no settings found
            $data['enable_qr']     = 0;
            $data['seller_name']   = '';
            $data['vat_number']    = '';
            $data['qr_code_size']  = 200;
        }

        // Handle POST request for saving settings
        if ($this->input->post()) {
            $post_data = $this->input->post();

            // Convert 'on'/'off' to 1/0 for enable_qr checkbox
            $post_data['enable_qr'] = (isset($post_data['enable_qr']) && $post_data['enable_qr'] == 'on') ? 1 : 0;

            // Prepare data for database update/insert. Ensure correct field names.
            $data_to_save = [
                'enable_qr'   => $post_data['enable_qr'],
                'seller_name' => trim($post_data['seller_name']),
                'vat_number'  => trim($post_data['vat_number']),
                'qr_size'     => (int)($post_data['qr_code_size'] ?? 0), // Use null coalescing operator
            ];

            // X and Y position fields are NOT saved here as per your request.

            // Update or insert settings directly using CodeIgniter's DB class
            if ($this->db->table_exists($this->settings_table_name)) {
                // Check if settings row exists
                $existing = $this->db->get_where($this->settings_table_name, array('id' => 1))->row();

                if ($existing) {
                    // Update existing row
                    $this->db->where('id', 1);
                    $this->db->update($this->settings_table_name, $data_to_save);
                } else {
                    // Insert new row if table is empty
                    $data_to_save['id'] = 1;
                    $this->db->insert($this->settings_table_name, $data_to_save);
                }
            } else {
                log_message('error', 'ZATCA QR Code Module: Settings table ' . $this->settings_table_name . ' does not exist during save attempt.');
            }

            if ($this->db->affected_rows() > 0) {
                set_alert('success', _l('settings_updated'));
            } else {
                set_alert('warning', _l('settings_not_updated'));
            }
           // redirect(admin_url('zatca_qr_code/admin/settings'));
           print '<h1>Successfully Updated </h1>';
            // Use JavaScript for redirection
            echo '<script>';
            echo 'window.location.href = "' . admin_url('zatca_qr_code/admin/settings') . '";';
            echo '</script>';
            exit();
        }

        // Set the page title
        $data['title'] = _l('zatca_qr_code_module_name') . ' ' . _l('settings');

        // Load the view for the settings page
        $this->load->view('admin/settings', $data);
    }

    /**
     * Handles copying PDF template files from module to theme directory.
     * This method is intended to be called via AJAX.
     */
    public function copy_pdf_templates()
    {
        // Permission check for editing settings, as this modifies files
        if (!has_permission('zatca_qr_code', '', 'edit') && !is_admin()) {
            echo json_encode(['status' => 'error', 'message' => _l('access_denied')]);
            die(); // Stop execution
        }

        // Define module source paths for the PDF templates
        $source_invoice_pdf = MODULES_PATH . 'zatca_qr_code/views/invoicepdf.php';
        $source_proposal_pdf = MODULES_PATH . 'zatca_qr_code/views/proposalpdf.php';

        // Define destination directories in the active theme
        $theme_name = 'perfex'; // Assuming 'perfex' theme; you can get this dynamically if needed: get_option('active_theme')
        $destination_invoice_pdf_dir = FCPATH . 'application/views/themes/' . $theme_name . '/views/invoices/';
        $destination_proposal_pdf_dir = FCPATH . 'application/views/themes/' . $theme_name . '/views/proposals/';

        $files_to_copy = [
            'invoicepdf.php' => ['src' => $source_invoice_pdf, 'dest_dir' => $destination_invoice_pdf_dir],
            'proposalpdf.php' => ['src' => $source_proposal_pdf, 'dest_dir' => $destination_proposal_pdf_dir],
        ];

        $warnings = [];
        $errors = [];
        $copied_files = [];

        $confirm_overwrite = $this->input->post('confirm_overwrite');

        // First pass: Check for existing files and gather warnings
        foreach ($files_to_copy as $filename => $paths) {
            $dest_path = $paths['dest_dir'] . $filename;
            if (file_exists($dest_path)) {
                $warnings[] = $filename;
            }
        }

        // If there are warnings and no confirmation to overwrite, return warning to front-end
        if (!empty($warnings) && !$confirm_overwrite) {
            echo json_encode([
                'status'   => 'warning',
                'message'  => _l('zatca_qr_code_confirm_overwrite_templates_message', implode(', ', $warnings)),
                'warnings' => $warnings,
            ]);
            die();
        }

        // Second pass: Perform the copy operation
        foreach ($files_to_copy as $filename => $paths) {
            $src = $paths['src'];
            $dest_dir = $paths['dest_dir'];
            $dest_path = $dest_dir . $filename;

            if (!file_exists($src)) {
                $errors[] = _l('zatca_qr_code_source_template_not_found', $filename);
                log_message('error', 'ZATCA QR Code Module: Source PDF template file not found at: ' . $src);
                continue;
            }

            // Ensure destination directory exists. Create it if it doesn't.
            if (!is_dir($dest_dir)) {
                if (!mkdir($dest_dir, 0755, true)) {
                    $errors[] = _l('zatca_qr_code_failed_to_create_dir', $dest_dir);
                    log_message('error', 'ZATCA QR Code Module: Failed to create destination directory: ' . $dest_dir . '. Check folder permissions.');
                    continue;
                }
            }

            // Attempt to copy the file
            if (copy($src, $dest_path)) {
                $copied_files[] = $filename;
                log_message('debug', 'ZATCA QR Code Module: Successfully copied ' . $src . ' to ' . $dest_path);
            } else {
                $errors[] = _l('zatca_qr_code_failed_to_copy_file', $filename);
                log_message('error', 'ZATCA QR Code Module: Failed to copy ' . $src . ' to ' . $dest_path . '. Check file permissions for theme views.');
            }
        }

        // Return final status to front-end
        if (!empty($errors)) {
            echo json_encode([
                'status'  => 'error',
                'message' => _l('zatca_qr_code_copy_failed_with_errors', implode('<br>', $errors)),
                'errors'  => $errors,
            ]);
        } elseif (!empty($copied_files)) {
            echo json_encode([
                'status'  => 'success',
                'message' => _l('zatca_qr_code_templates_copied_successfully', implode(', ', $copied_files)),
            ]);
        } else {
             echo json_encode([
                'status'  => 'info',
                'message' => _l('zatca_qr_code_no_templates_copied'),
            ]);
        }
        die();
    }
}