/* modules/zatca_qr_code/assets/js/script.js */

(function($) {
    'use strict';

$(document).ready(function() {
    // This function will run when the DOM is fully loaded.

    // Handle PDF Template Copy Button
    $('#copy-pdf-templates-btn').on('click', function() {
        var btn = $(this);
        var originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Copying...');

        $.post(admin_url + 'zatca_qr_code/admin/copy_pdf_templates', {})
            .done(function(response) {
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.status === 'success') {
                        alert_float('success', data.message);
                    } else if (data.status === 'warning') {
                        // Show confirmation modal for overwrite
                        $('#overwrite-warning-message').text(data.message);
                        $('#confirmOverwriteModal').modal('show');
                    } else {
                        alert_float('danger', data.message || 'Failed to copy templates');
                    }
                } catch (e) {
                    alert_float('danger', 'Error processing response');
                    console.error('Response parsing error:', e);
                }
            })
            .fail(function(xhr, status, error) {
                alert_float('danger', 'Failed to copy templates. Please check file permissions.');
                console.error('AJAX Error:', error);
            })
            .always(function() {
                btn.prop('disabled', false).html(originalText);
            });
    });

    // Handle confirmation for overwrite
    $('#confirm-overwrite-btn').on('click', function() {
        var btn = $('#copy-pdf-templates-btn');
        var originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Copying...');
        $('#confirmOverwriteModal').modal('hide');

        $.post(admin_url + 'zatca_qr_code/admin/copy_pdf_templates', {
            confirm_overwrite: true
        })
            .done(function(response) {
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.status === 'success') {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message || 'Failed to copy templates');
                    }
                } catch (e) {
                    alert_float('danger', 'Error processing response');
                    console.error('Response parsing error:', e);
                }
            })
            .fail(function(xhr, status, error) {
                alert_float('danger', 'Failed to copy templates. Please check file permissions.');
                console.error('AJAX Error:', error);
            })
            .always(function() {
                btn.prop('disabled', false).html(originalText);
            });
    });

    // Get references to the input fields and preview area
    var $enableQrToggle = $('#enable_qr');
    var $sellerNameInput = $('input[name="seller_name"]');
    var $vatNumberInput = $('input[name="vat_number"]');
    var $qrSizeInput = $('input[name="qr_size"]');
    var $qrPreviewArea = $('#qr_preview_area');

    // Function to update the QR code preview (if implemented)
    function updateQrPreview() {
        // This is a placeholder. A real live preview would require:
        // 1. An AJAX call to a module controller method that generates the QR code.
        // 2. The controller returning the base64 image data.
        // 3. This JS then updates the <img> src.

        // For now, let's just update a text message or show/hide the preview area.
        if ($enableQrToggle.is(':checked')) {
            $qrPreviewArea.html('<p><i class="fa fa-info-circle"></i> Enable and save settings to see live preview.</p>');
            $qrPreviewArea.show(); // Show if hidden
        } else {
            $qrPreviewArea.html('<p><i class="fa fa-ban"></i> QR Code generation is disabled.</p>');
            // Optionally, $qrPreviewArea.hide(); if you want to completely hide it.
        }
    }

    // Initial update when the page loads
    updateQrPreview();

    // Attach event listeners to update preview on change
    $enableQrToggle.on('change', updateQrPreview);
    $sellerNameInput.on('keyup', updateQrPreview);
    $vatNumberInput.on('keyup', updateQrPreview);
    $qrSizeInput.on('change', updateQrPreview); // Use 'change' for number inputs

    // Example of simple client-side validation for QR size before submission
    // Perfex CRM's built-in validation (via render_input) handles min/max,
    // but you can add custom JS validation here if needed.
    $('form').on('submit', function(e) {
        var qrSize = parseInt($qrSizeInput.val());
        if (qrSize < 50 || qrSize > 500) {
            alert_float('danger', 'QR Code Size must be between 50 and 500 pixels.');
            $qrSizeInput.focus();
            e.preventDefault(); // Prevent form submission
            return false;
        }
    });

    // You can add more complex JS here, like an actual AJAX call for live preview.
    // Example:
    /*
    function fetchLiveQrPreview() {
        if (!$enableQrToggle.is(':checked')) {
            updateQrPreview(); // Show disabled message
            return;
        }

        var sellerName = $sellerNameInput.val();
        var vatNumber = $vatNumberInput.val();
        var qrSize = $qrSizeInput.val();

        // Simulate invoice data (you'd need real data or simplified mock for preview)
        var mockInvoiceData = {
            datecreated: new Date().toISOString().slice(0, 19).replace('T', ' '),
            total: 100.00,
            total_tax: 15.00
        };

        // This would require a new controller endpoint (e.g., zatca_qr_code/admin/preview_qr)
        // that takes these parameters and returns the base64 image.
        $.post(admin_url + 'zatca_qr_code/admin/preview_qr', {
            seller_name: sellerName,
            vat_number: vatNumber,
            qr_size: qrSize,
            invoice_data: mockInvoiceData // Pass mock data
        }).done(function(response) {
            if (response.success && response.image_data) {
                $qrPreviewArea.html('<img src="' + response.image_data + '" alt="Live QR Preview" style="width:' + qrSize + 'px; height:' + qrSize + 'px;"><p>Live Preview</p>');
            } else {
                $qrPreviewArea.html('<p><i class="fa fa-exclamation-triangle"></i> Failed to generate live preview: ' + (response.message || 'Unknown error') + '</p>');
            }
        }).fail(function() {
            $qrPreviewArea.html('<p><i class="fa fa-times-circle"></i> Error contacting server for preview.</p>');
        });
    }

    // Call fetchLiveQrPreview on changes
    // $enableQrToggle.on('change', fetchLiveQrPreview);
    // $sellerNameInput.on('blur', fetchLiveQrPreview); // Use blur to reduce server calls
    // $vatNumberInput.on('blur', fetchLiveQrPreview);
    // $qrSizeInput.on('change', fetchLiveQrPreview);
    */
});

})(jQuery);