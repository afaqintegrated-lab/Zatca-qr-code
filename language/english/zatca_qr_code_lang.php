<?php

# Version 1.0.0
#
# General
$lang['zatca_qr_code_module_name']    = 'ZATCA QR Code';
$lang['zatca_qr_code_settings']       = 'ZATCA QR Code Settings';
$lang['zatca_qr_code_module_settings'] = 'ZATCA QR Code Module Settings';

# Settings View Fields
$lang['zatca_qr_code_enable_qr']      = 'Enable ZATCA QR Code on Invoices';
$lang['zatca_qr_code_seller_name']    = 'Seller Name';
$lang['zatca_qr_code_vat_number']     = 'VAT Registration Number';
$lang['zatca_qr_code_qr_size']        = 'QR Code Size (pixels)';

# Alerts
$lang['settings_updated']             = 'Settings updated successfully'; // This is a core Perfex string, but good to have consistency
$lang['settings_not_updated']         = 'Settings update failed'; // This is a core Perfex string

# Global submit button text (if you want to define it here, or use Perfex CRM's default)
$lang['submit']                       = 'Submit';
$lang['proposal_open_till']                     = 'Valid Till';
$lang['zatca_qr_code_enable_qr_heading']    = 'Enable ZATCA QR Code on Invoices';
 
$lang['zatca_qr_code_pdf_template_copy_title'] = 'PDF Template Management';
$lang['zatca_qr_code_pdf_template_exists'] = 'Warning: For the Better Layout and QR Code display ,we need to copy the files to your theme folder. The following PDF template files already exist in the theme folder';
$lang['zatca_qr_code_pdf_template_overwrite_confirm'] = 'Proceeding will overwrite the existing files. Are you sure you want to continue?';
$lang['zatca_qr_code_pdf_template_copy_info'] = 'This will copy invoicepdf.php and proposalpdf.php from the module to the active theme folder.';
$lang['zatca_qr_code_copy_templates_button'] = 'Copy PDF Templates to Theme Folder';
$lang['zatca_qr_code_pdf_template_copy_button_confirm'] = 'This will overwrite existing files if present. Do you want to continue?';
$lang['zatca_qr_code_qr_x_pos'] = 'QR Code X Position (mm)';
$lang['zatca_qr_code_qr_y_pos'] = 'QR Code Y Position (mm)';
$lang['zatca_qr_code_copy_templates_heading'] = 'Copy PDF Templates';
$lang['zatca_qr_code_copy_templates_description'] = 'This feature allows you to copy custom PDF templates (invoicepdf.php, proposalpdf.php) from this module to your active Perfex CRM themes views folder. This is necessary to apply ZATCA QR code changes to your invoices and proposals. If templates with the same name already exist in the destination, you will be prompted to confirm overwriting them. Make sure to back up your custom files if you have made modifications before proceeding.';