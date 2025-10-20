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

 

// Fix quantity heading
$qty_heading = _l('estimate_table_quantity_heading', '', false);
if ($proposal->show_quantity_as == 2) {
    $qty_heading = _l('proposal_table_hours_heading', '', false);
} elseif ($proposal->show_quantity_as == 3) {
    $qty_heading = _l('estimate_table_quantity_heading', '', false) . '/' . _l('estimate_table_hours_heading', '', false);
}

// Left Column: Logo + Company VAT + Address
$vat_number = get_option('company_vat') ?: '-'; 
$company_address = get_option('invoice_company_address') ?: '-';
$info_left_column = pdf_logo_url();
$info_left_column .= '<span style="font-size:10px"><br> VAT: ' . $vat_number . '<br> Address: ' . $company_address . '</span>';

// Right Column: Quotation Header
$info_right_column  = '<span style="font-weight:bold;font-size:27px;">Quotation<br>فاتورة مبدئية</span>';
$info_right_column .= '<br /><span style="font-size:14px;"># ' . format_proposal_number($proposal->id) . '</span>';

// Write header
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
//$pdf->ln(4);
$y = $pdf->getY();

// Client Info Block
$arraycount = 0;
$client_details  = '<b><span style="font-size:12px">' . _l('proposal_to') . '</span></b><br>';
$client_details .= '';

$client = $proposal->rel_type == 'customer' ? get_client($proposal->rel_id) : null;

if ($client) {
    $client_details .= '<span style="font-size:12px">' . $client->company . '<br>';
    if(isset($client->company)){
    $arraycount = $arraycount + 1;
    }


$arr = explode("\n", trim($client->address));
$countarrays = count($arr); 
if(isset($countarrays)){
    $arraycount = $arraycount + $countarrays;
    
}
    
    $client_details .= trim($client->address) . '<br>';

    if (!empty($client->email)) {
        $client_details .= _l('email') . ': ' . $client->email . ',';
         $arraycount = $arraycount + 1;
    }
    if (!empty($client->phonenumber)) {
        $client_details .= _l('Phone') . ': ' . $client->phonenumber . '';
         $arraycount = $arraycount + 1;
       
    }
       $client_details .= '</span>';
}
 $addresswordcount  = strlen(trim($client->address));
  
  if(($addresswordcount>130) & ($arraycount<3)) {
      $arraycount = $arraycount + 2;
  }
 

// Proposal Info Block
$proposal_info = '<div style="color:#424242;font-size:13px">';
$proposal_info .= "<span style='text-align:left'>" . _l('proposal_date') . '</span>: <span style="text-align:left">' . _d($proposal->date) . '</span><br>';

if (!empty($proposal->open_till)) {
    $proposal_info .= _l('proposal_open_till') . ': ' . _d($proposal->open_till) . '<br>';
}
$projectid = trim(get_project_name_by_id($proposal->project_id));
if ($projectid != '' && get_option('show_project_on_proposal') == 1) {
    $proposal_info .= _l('project') . ': ' . get_project_name_by_id($proposal->project_id) . '';
}
$proposal_info .= '</div>'. $arraycount;
// Write left/right info
$pdf->writeHTMLCell(($swap == '0' ? (($dimensions['wk'] / 2) - $dimensions['rm']) : ''), '', '', ($swap == '0' ? $y : ''), $client_details, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$rowcount = max([$pdf->getNumLines($proposal_info, 80)]);
$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * $arraycount, '', ($swap == '1' ? $y : ''), $proposal_info, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);
//$pdf->ln(1);


// Generate Item Table
$items = get_items_table_data($proposal, 'proposal', 'pdf')->set_headings('estimate');
$items_html = $items->table();

$items_html .= '<table cellpadding="3" style="font-size:' . ($font_size + 4) . 'px;">';
$items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->subtotal, $proposal->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($proposal)) {
    $items_html .= '<tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        $items_html .= ' (' . app_format_number($proposal->discount_percent, true) . '%)';
    }
    $items_html .= '</strong></td>
        <td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
        <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)</strong></td>
        <td align="right" width="15%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
    </tr>';
}

if ((int) $proposal->adjustment != 0) {
    $items_html .= '<tr>
        <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
        <td align="right" width="15%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
    </tr>';
}

$items_html .= '<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
</tr>';
$items_html .= '</table>';

// Convert total to words
if (get_option('total_to_words_enabled') == 1) {
    $items_html .= '<br /><br /><strong style="text-align:center;">' . _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name) . '</strong>';
}



 
$signature_path = FCPATH . 'uploads/company/signature.png';
$stamp_path = FCPATH . 'assets/images/stamp.png';
$rightContent = '';
$rightContent .= ''
    . '<strong>Authorized Signature:</strong><br>';

 
// Output the signature content.
// Use writeHTML to ensure any HTML tags within the signature are rendered correctly by TCPDF.
//$pdf->writeHTML($companySignature, true, false, true, false, '');

if (file_exists($signature_path)) {
    $rightContent .= '<img src="' . $signature_path . '" >';
} else {
  //  $rightContent .= '[Signature Placeholder]<br>';
}
//<p>{$project}</p>

$rightContent .= '';
 
// Inject item table into content
$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);


 
// Final Content Block (styled)
$html = <<<EOF
<style>
    .proposal-header { font-size: 14px; font-weight: bold; margin-bottom: 1px; }
    .proposal-sub { font-size: 11px; color: #555; margin-bottom: 1px; }
    .proposal-meta { font-size: 11px; margin-bottom: 1px; }
    .proposal-content { font-size: 11px; margin-top: 1px;float:left }
</style>


<div class="proposal-content">   
    {$proposal->content}
    {$rightContent}
</div>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');

 


// Get the proposals css
// Theese lines should aways at the end of the document left side. Dont indent these lines


 