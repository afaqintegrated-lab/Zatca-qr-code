<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Invoice QR - English Language File
 */

// Module Information
$lang['zatca_qr_module_name'] = 'ZATCA Invoice QR';
$lang['zatca_qr_module_description'] = 'Generate ZATCA compliant QR codes for Saudi Arabia invoices';

// Settings
$lang['zatca_settings'] = 'ZATCA Settings';
$lang['zatca_module_enabled'] = 'Enable Module';
$lang['zatca_phase'] = 'ZATCA Phase';
$lang['zatca_phase1'] = 'Phase 1 (Simplified)';
$lang['zatca_phase2'] = 'Phase 2 (Signed)';
$lang['zatca_environment'] = 'Environment';
$lang['zatca_sandbox'] = 'Sandbox (Testing)';
$lang['zatca_production'] = 'Production (Live)';

// Seller Information
$lang['zatca_seller_information'] = 'Seller Information';
$lang['zatca_seller_name'] = 'Seller/Company Name';
$lang['zatca_seller_name_help'] = 'Your company or business name as registered';
$lang['zatca_vat_number'] = 'VAT Registration Number';
$lang['zatca_vat_number_help'] = '15-digit Saudi VAT number (e.g., 310122393500003)';
$lang['zatca_company_address'] = 'Company Address';
$lang['zatca_company_address_help'] = 'Full company address (required for Phase 2)';

// QR Code Settings
$lang['zatca_qr_settings'] = 'QR Code Settings';
$lang['zatca_qr_position'] = 'QR Position on PDF';
$lang['zatca_qr_position_top_right'] = 'Top Right';
$lang['zatca_qr_position_top_left'] = 'Top Left';
$lang['zatca_qr_position_bottom_right'] = 'Bottom Right';
$lang['zatca_qr_position_bottom_left'] = 'Bottom Left';
$lang['zatca_qr_size'] = 'QR Code Size (pixels)';
$lang['zatca_qr_size_help'] = 'Size of QR code on invoice (recommended: 150-200px)';
$lang['zatca_auto_generate'] = 'Auto-generate QR Codes';
$lang['zatca_auto_generate_help'] = 'Automatically generate QR codes when invoices are created or updated';

// Actions
$lang['zatca_save_settings'] = 'Save Settings';
$lang['zatca_test_qr'] = 'Test QR Generation';
$lang['zatca_batch_generate'] = 'Batch Generate QR Codes';
$lang['zatca_regenerate_qr'] = 'Regenerate QR Code';
$lang['zatca_delete_qr'] = 'Delete QR Code';
$lang['zatca_view_qr'] = 'View QR Details';

// Statistics
$lang['zatca_statistics'] = 'ZATCA Statistics';
$lang['zatca_total_invoices'] = 'Total Invoices';
$lang['zatca_invoices_with_qr'] = 'Invoices with QR';
$lang['zatca_invoices_without_qr'] = 'Invoices without QR';
$lang['zatca_failed_generations'] = 'Failed Generations';
$lang['zatca_success_rate'] = 'Success Rate';

// Messages
$lang['zatca_settings_saved'] = 'ZATCA settings saved successfully';
$lang['zatca_settings_not_saved'] = 'Failed to save ZATCA settings';
$lang['zatca_qr_generated'] = 'QR code generated successfully';
$lang['zatca_qr_regenerated'] = 'QR code regenerated successfully';
$lang['zatca_qr_regeneration_failed'] = 'Failed to regenerate QR code';
$lang['zatca_qr_deleted'] = 'QR code deleted successfully';
$lang['zatca_qr_delete_failed'] = 'Failed to delete QR code';
$lang['zatca_batch_generated'] = '%d QR codes generated successfully, %d failed';
$lang['zatca_no_invoices_to_generate'] = 'No invoices found without QR codes';

// Errors
$lang['zatca_module_not_configured'] = 'ZATCA module is not properly configured';
$lang['zatca_module_disabled'] = 'ZATCA module is currently disabled';
$lang['zatca_invalid_vat_number'] = 'Invalid VAT number format. Must be 15 digits.';
$lang['zatca_missing_seller_name'] = 'Seller name is required';
$lang['zatca_missing_vat_number'] = 'VAT number is required';
$lang['zatca_invoice_not_found'] = 'Invoice not found';
$lang['zatca_qr_generation_failed'] = 'QR code generation failed';

// QR Code Details
$lang['zatca_qr_code'] = 'ZATCA QR Code';
$lang['zatca_qr_data'] = 'QR Data (Base64)';
$lang['zatca_qr_decoded'] = 'Decoded QR Data';
$lang['zatca_qr_tag1'] = 'Tag 1: Seller Name';
$lang['zatca_qr_tag2'] = 'Tag 2: VAT Number';
$lang['zatca_qr_tag3'] = 'Tag 3: Invoice Date & Time';
$lang['zatca_qr_tag4'] = 'Tag 4: Invoice Total';
$lang['zatca_qr_tag5'] = 'Tag 5: VAT Amount';
$lang['zatca_qr_tag6'] = 'Tag 6: Invoice Hash';
$lang['zatca_qr_tag7'] = 'Tag 7: Digital Signature';
$lang['zatca_qr_tag8'] = 'Tag 8: Public Key';
$lang['zatca_qr_tag9'] = 'Tag 9: Certificate Signature';

// Status
$lang['zatca_status_pending'] = 'Pending';
$lang['zatca_status_generated'] = 'Generated';
$lang['zatca_status_failed'] = 'Failed';

// Configuration
$lang['zatca_configuration_status'] = 'Configuration Status';
$lang['zatca_configured'] = 'Configured';
$lang['zatca_not_configured'] = 'Not Configured';
$lang['zatca_missing_fields'] = 'Missing Required Fields';

// Help Text
$lang['zatca_help_phase1'] = 'Phase 1 generates simplified QR codes with 5 basic fields';
$lang['zatca_help_phase2'] = 'Phase 2 generates signed QR codes with digital signatures (requires certificate)';
$lang['zatca_help_batch'] = 'Generate QR codes for all existing invoices that don\'t have them';
$lang['zatca_help_auto_generate'] = 'When enabled, QR codes will be automatically generated for new and updated invoices';

// Batch Operations
$lang['zatca_batch_limit'] = 'Number of invoices to process';
$lang['zatca_batch_processing'] = 'Processing...';
$lang['zatca_batch_complete'] = 'Batch generation complete';

// Tooltips
$lang['zatca_tooltip_test'] = 'Test QR generation with current settings';
$lang['zatca_tooltip_batch'] = 'Generate QR codes for invoices without them';
$lang['zatca_tooltip_regenerate'] = 'Regenerate QR code for this invoice';
$lang['zatca_tooltip_view'] = 'View QR code details and decoded data';
