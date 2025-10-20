<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$three_columnsqr = '';
$info_right_column = '';
$info_left_column   = '';
 $organization_info = '';
 $company_name    = get_option('invoice_company_name');
$company_address = get_option('invoice_company_address');
$city            = get_option('invoice_company_city');
$state           = get_option('invoice_company_state');
$zip             = get_option('invoice_company_postal_code');
$country_id      = get_option('invoice_company_country');
$vat_number      = get_option('company_vat');

$organization_info .= format_organization_info();
$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('invoice_pdf_heading') . '</span><br />';
//$info_right_column .= '<b style="color:#4e4e4e;"># ' . $invoice_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
   // $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
}

if ($status != Invoices_model::STATUS_PAID && $status != Invoices_model::STATUS_CANCELLED && get_option('show_pay_link_to_invoice_pdf') == 1
    && found_invoice_mode($payment_modes, $invoice->id, false)) {
    $info_right_column .= ' - <a style="color:#84c529;text-decoration:none;text-transform:uppercase;" href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '"><1b>' . _l('view_invoice_pdf_link_pay') . '</1b></a>';
}

// Add logo
 
 
//        <td width="35%" valign="top">
   //         ' . pdf_logo_url() . '
  //          <span style="margin-top: 5px; margin-bottom: 2px; font-weight:bold; font-size: 16px;">
  //              ' . strtoupper(_l('invoice_pdf_heading')) . ': # ' . $invoice_number . '
   //         </span>'; 
  
 
$vat = get_option('company_vat');

$comptext = '<span style="font-size:10px"><br> VAT :'. $vat_number .' <br> Address : '. $company_address .'</span>';
// Add logo
$info_left_column .= pdf_logo_url() . $comptext;


// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
 
// Reduced vertical gap after the first row
//$pdf->ln(4);
$pdf->ln(1);
$invoice_info = '<div style="font-size:12px"> <b>' . _l('invoice_bill_to') . ':</b>  ';
$invoice_info .= '&nbsp;';
$invoice_info .= format_customer_info($invoice, 'invoice', 'billing');
$invoice_info .= '</div>';

if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
    $invoice_info .= '<span style="font-size:12px"><b>' . _l('ship_to') . ':</b></span>';
    $invoice_info .= '';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'shipping');
    $invoice_info .= '';
}

$pdf->ln(1);

 

// Add Bill No, Bill Date, and Due Date using spans
 $billinfo = '
<table cellpadding="2" cellspacing="0" style="font-size:13px;">
    <tr>
        <td><b>Invoice No :</b></td>
        <td>' . format_invoice_number($invoice->id) . '</td>
    </tr>
    <tr>
        <td><b>Invoice Date :</b></td>
        <td>' . _d($invoice->date) . '</td>
    </tr>
    
    <tr>
        <td><b>Status :</b></td>
        <td><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>
</td>
    </tr>
    <tr>
        <td><b>Due Date :</b></td>
        <td>' . _d($invoice->duedate) . '</td>
    </tr>
</table>';
//

$left_info  = $invoice_info; //$organization_info;
$right_info = $billinfo;
//start shihab

// Add QR code if exists
if (isset($invoice->zatca_qr_code_image)) {
    $three_columnsqr .= '
    <div style="float:right; margin-right:5px;float:right;top-margin:-5px">
        <img src="' . $invoice->zatca_qr_code_image . '"
            style="width: ' . $invoice->zatca_qr_code_qr_size . 'px;
                    height: 100px;
                    top-margin:-5px
                    max-width: 110px;
                    border: 1px solid #e0e0e0;
                    padding: 5px;
                    background-color: #ffffff;"
             alt="ZATCA QR Code">
    </div>';
}
$three_columns = '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="33%" valign="top">
           '. $left_info .'
        </td>

        <td width="33%" valign="top" align="center">
            '. $three_columnsqr .'
        </td>

        <td width="33%" valign="top" align="right">
           '. $billinfo .'
        </td>
    </tr>
</table>';

// Output to PDF
$pdf->writeHTML($three_columns, true, false, false, false, '');
$pdf->ln(1);


//end shihab

// Second Row: Company Address (Left) and Customer Name/Address (Right)
// This row will now naturally move up due to the removal of the separate "new row" for invoice # and status.




//pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// Additional invoice details (duedate, sale agent, custom fields) printed below the address blocks
$additional_invoice_details = '';
 

if ($invoice->sale_agent && get_option('show_sale_agent_on_invoices') == 1) {
    $additional_invoice_details .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
    $additional_invoice_details = hooks()->apply_filters('invoice_pdf_header_after_sale_agent', $additional_invoice_details, $invoice);
}

$additional_invoice_details = hooks()->apply_filters('invoice_pdf_header_before_custom_fields', $additional_invoice_details, $invoice);

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $additional_invoice_details .= $field['name'] . ': ' . $value . '<br />';
}

$additional_invoice_details = hooks()->apply_filters('invoice_pdf_header_after_custom_fields', $additional_invoice_details, $invoice);

if (!empty($additional_invoice_details)) {
    $pdf->writeHTMLCell('', '', '', '', $additional_invoice_details, 0, 1, false, true, 'L', true);
}

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_items_table_data($invoice, 'invoice', 'pdf');

$tblhtml = $items->table();

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(2);

$tbltotal = '';
$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($invoice)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_discount');
    if (is_sale_discount($invoice, 'percent')) {
        $tbltotal .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
    }
    $tbltotal .= '</strong>';
    $tbltotal .= '</td>';
    $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
</tr>';
}

if ((int) $invoice->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#e5e7eb;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
</tr>';

if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', [
        'field' => 'amount',
        'where' => [
            'invoiceid' => $invoice->id,
        ],
    ]), $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
    </tr>';
}



$tbltotal .= '</table>';
$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
   // $pdf->Ln(4);
}

if (count($invoice->payments) > 0 && get_option('show_transactions_on_invoice_pdf') == 1) {
    $pdf->Ln(4);
    $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_received_payments') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
    $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
        <tr height="20"  style="color:#000;border:1px solid #000;">
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
    </tr>';
    $tblhtml .= '<tbody>';

    foreach ($invoice->payments as $payment) {
        $payment_name = $payment['name'];
        if (! empty($payment['paymentmethod'])) {
            $payment_name .= ' - ' . $payment['paymentmethod'];
        }
        $tblhtml .= '
            <tr>
            <td>' . $payment['paymentid'] . '</td>
            <td>' . $payment_name . '</td>
            <td>' . _d($payment['date']) . '</td>
            <td>' . app_format_money($payment['amount'], $invoice->currency_name) . '</td>
            </tr>
        ';
    }
    $tblhtml .= '</tbody>';
    $tblhtml .= '</table>';
    $pdf->writeHTML($tblhtml, true, false, false, false, '');
}

 
$pdf->Ln(5); // spacing before columns

// Capture left content (payment modes, notes, terms) into a variable
$leftContent = '';

// Payment modes block
if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
    $leftContent .= '<b>' . _l('invoice_html_offline_payment') . ':</b><br>';
    foreach ($payment_modes as $mode) {
        if (is_numeric($mode['id']) && !is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
            continue;
        }
        if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
            $leftContent .= '<b>' . $mode['name'] . '</b><br>';
            $leftContent .= $mode['description'] . '<br><br>';
        }
    }
}

// Client note block
if (!empty($invoice->clientnote)) {
    $leftContent .= '<b>' . _l('invoice_note') . ':</b><br>';
    $leftContent .= nl2br($invoice->clientnote) . '<br><br>';
}

// Terms and conditions block
if (!empty($invoice->terms)) {
    $leftContent .= '<b>' . _l('terms_and_conditions') . ':</b><br>';
    $leftContent .= nl2br($invoice->terms) . '<br><br>';
}

// Write left column (Payment info, notes, terms)
$xLeft = $pdf->GetX();
$y = $pdf->GetY();
$widthLeft = 110;  // adjust width as needed

$pdf->writeHTMLCell($widthLeft, 0, $xLeft, $y, $leftContent, 0, 0, false, true, 'L', true);

// Prepare right column: signature + stamp block

 
$signature_path = FCPATH . 'uploads/company/signature.png';
$stamp_path = FCPATH . 'assets/images/stamp.png';
$rightContent = '<div style="">';
$rightContent .= ''
    . '<strong>Authorized Signature:</strong><br>';

 
// Output the signature content. 
// Use writeHTML to ensure any HTML tags within the signature are rendered correctly by TCPDF.
//$pdf->writeHTML($companySignature, true, false, true, false, '');

if (file_exists($signature_path)) {
    $rightContent .= '<img src="' . $signature_path . '" ><br>';
} else {
  //  $rightContent .= '[Signature Placeholder]<br>';
}

$rightContent .= '';

$rightContent .= '</div>';

// Write right column (signature + stamp)
$xRight = $xLeft + $widthLeft + 5; // +5mm gap
$widthRight = 80; // adjust width as needed

$pdf->writeHTMLCell($widthRight, 0, $xRight, $y, $rightContent, 0, 1, false, true, 'L', true);

$pdf->Ln(4); // spacing after


