<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('zatca_qr_code_module_settings'); ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('zatca_qr_code/settings'), ['id' => 'zatca-settings-form']); ?>

                            <div class="form-group">
                                <label for="seller_name" class="control-label"><?php echo _l('zatca_qr_code_seller_name'); ?></label>
                                <input type="text" id="seller_name" name="seller_name" class="form-control" value="<?php echo (isset($settings) ? $settings->seller_name : ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="vat_number" class="control-label"><?php echo _l('zatca_qr_code_vat_number'); ?></label>
                                <input type="text" id="vat_number" name="vat_number" class="form-control" value="<?php echo (isset($settings) ? $settings->vat_number : ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="qr_size" class="control-label"><?php echo _l('zatca_qr_code_qr_size'); ?></label>
                                <input type="number" id="qr_size" name="qr_size" class="form-control" value="<?php echo (isset($settings) ? $settings->qr_size : ''); ?>">
                            </div>

<div class="form-group">
                                <label for="enable_qr_test" class="control-label"><?php echo _l('zatca_qr_code_enable_qr'); ?></label>
                                <input type="checkbox" name="enable_qr" id="enable_qr_test" value="1" <?php echo (isset($settings) && $settings->enable_qr == 1 ? 'checked' : ''); ?>>
                                <label for="enable_qr_test"> (Check this box)</label>
                            </div>
                            

                            <button type="submit" class="btn btn-primary" style="color:black"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>