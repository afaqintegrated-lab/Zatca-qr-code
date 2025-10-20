<?php
// helpers/zatca_helper.php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Encodes data into ZATCA TLV format.
 * @param array $data_array Key-value pairs where key is tag number (1-9) and value is the data.
 * @return string Binary TLV string.
 */
function zatca_helper_encode_tlv($data_array) {
    $tlv_string = '';
    foreach ($data_array as $tag => $value) {
        $tag_byte = chr($tag);
        $value_bytes = $value; // Assuming value is already a string
        $length_byte = chr(strlen($value_bytes));
        $tlv_string .= $tag_byte . $length_byte . $value_bytes;
    }
    return $tlv_string;
}