<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('zatca_qr_code_settings_title'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('zatca_qr_code/admin/settings')); ?>

                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="enable_qr" name="enable_qr" <?php echo ($zatca_qr_code_enable == 1) ? 'checked' : ''; ?>>
                            <label for="enable_qr"><?php echo _l('zatca_qr_code_enable_option'); ?></label>
                        </div>

                        <?php echo render_input('seller_name', 'zatca_qr_code_seller_name', $zatca_qr_code_seller_name); ?>
                        <?php echo render_input('vat_number', 'zatca_qr_code_vat_number', $zatca_qr_code_vat_number); ?>
                        <?php echo render_input('qr_size', 'zatca_qr_code_qr_size', $zatca_qr_code_qr_size, 'number', ['min' => 50, 'max' => 500]); ?>

                        <button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>