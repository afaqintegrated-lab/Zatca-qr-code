/**
 * ZATCA Invoice QR - Admin JavaScript
 */

$(function() {
    'use strict';

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Test QR Generation
    $('#test-qr-btn').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');

        $.ajax({
            url: admin_url + 'zatca_invoice_qr/zatca_admin/test_qr',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayTestQrResult(response);
                    $('#testQrModal').modal('show');
                } else {
                    alert_float('danger', response.message || 'QR generation test failed');
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error: ' + error);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-flask"></i> Test QR Generation');
            }
        });
    });

    // Batch Generate QR Codes
    $('#batch-generate-btn').on('click', function() {
        if (!confirm('Generate QR codes for all invoices without them? This may take a while.')) {
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: admin_url + 'zatca_invoice_qr/zatca_admin/batch_generate',
            type: 'POST',
            dataType: 'json',
            data: {
                limit: 50 // Process 50 at a time
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    // Reload page to update statistics
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert_float('danger', response.message || 'Batch generation failed');
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error: ' + error);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Batch Generate QR Codes');
            }
        });
    });

    // VAT Number Formatting
    $('#vat_number').on('input', function() {
        var val = $(this).val().replace(/\D/g, ''); // Remove non-digits
        $(this).val(val);
    });

    // Enable/Disable based on module enabled
    $('#enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#zatca-settings-form :input').not('#enabled').prop('disabled', false);
        } else {
            if (confirm('Disable the ZATCA QR module? Existing QR codes will be preserved.')) {
                // Keep disabled
            } else {
                $(this).prop('checked', true);
            }
        }
    });

    /**
     * Display Test QR Result
     */
    function displayTestQrResult(data) {
        var html = '';

        // Success message
        html += '<div class="alert alert-success">';
        html += '<i class="fa fa-check"></i> QR code generated successfully!';
        html += '</div>';

        // QR Code Image
        html += '<div class="row">';
        html += '<div class="col-md-6 text-center">';
        html += '<h4>Generated QR Code</h4>';
        html += '<img src="' + data.qr_image + '" alt="Test QR" class="img-responsive" style="max-width: 300px; margin: 0 auto;" />';
        html += '</div>';

        // Decoded Data
        html += '<div class="col-md-6">';
        html += '<h4>Decoded Data</h4>';
        html += '<table class="table table-bordered table-sm">';
        html += '<tr><th>Tag</th><th>Value</th></tr>';
        
        if (data.decoded) {
            for (var tag in data.decoded) {
                var tagName = getTagName(tag);
                html += '<tr>';
                html += '<td><strong>' + tagName + '</strong></td>';
                html += '<td>' + escapeHtml(data.decoded[tag]) + '</td>';
                html += '</tr>';
            }
        }
        
        html += '</table>';
        html += '</div>';
        html += '</div>';

        // Size Check
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<h4>Validation</h4>';
        html += '<div class="alert alert-' + (data.size_check.valid ? 'success' : 'warning') + '">';
        html += '<i class="fa fa-' + (data.size_check.valid ? 'check' : 'exclamation-triangle') + '"></i> ';
        html += data.size_check.message;
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // Raw Data (collapsible)
        html += '<div class="panel panel-default">';
        html += '<div class="panel-heading">';
        html += '<h4 class="panel-title">';
        html += '<a data-toggle="collapse" href="#rawData">Raw QR Data (Base64) <i class="fa fa-chevron-down"></i></a>';
        html += '</h4>';
        html += '</div>';
        html += '<div id="rawData" class="panel-collapse collapse">';
        html += '<div class="panel-body">';
        html += '<textarea class="form-control" rows="4" readonly>' + data.qr_data + '</textarea>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#test-qr-result').html(html);
    }

    /**
     * Get Tag Name from Tag Number
     */
    function getTagName(tag) {
        var tags = {
            '1': 'Tag 1: Seller Name',
            '2': 'Tag 2: VAT Number',
            '3': 'Tag 3: Date & Time',
            '4': 'Tag 4: Invoice Total',
            '5': 'Tag 5: VAT Amount',
            '6': 'Tag 6: Invoice Hash',
            '7': 'Tag 7: Digital Signature',
            '8': 'Tag 8: Public Key',
            '9': 'Tag 9: Certificate Signature'
        };
        return tags[tag] || 'Tag ' + tag;
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
