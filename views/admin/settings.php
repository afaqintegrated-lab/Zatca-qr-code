<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin-top"><?php echo _l('zatca_qr_code_module_name'); ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('zatca_qr_code/admin/settings'), ['id' => 'zatca-qr-code-settings-form']); ?>

                        <div class="form-group">
                            <label for="enable_qr" class="control-label clearfix">
                                <?php echo _l('zatca_qr_code_enable_qr_heading'); ?>
                            </label>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" id="enable_qr_yes" name="enable_qr" value="on" <?php if (isset($enable_qr) && $enable_qr == 1) { echo 'checked'; } ?>>
                                <label for="enable_qr_yes"><?php echo _l('yes'); ?></label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" id="enable_qr_no" name="enable_qr" value="off" <?php if (isset($enable_qr) && $enable_qr == 0) { echo 'checked'; } ?>>
                                <label for="enable_qr_no"><?php echo _l('no'); ?></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="seller_name" class="control-label"><?php echo _l('zatca_qr_code_seller_name'); ?></label>
                            <input type="text" id="seller_name" name="seller_name" class="form-control" value="<?php echo htmlspecialchars($seller_name ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="vat_number" class="control-label"><?php echo _l('zatca_qr_code_vat_number'); ?></label>
                            <input type="text" id="vat_number" name="vat_number" class="form-control" value="<?php echo htmlspecialchars($vat_number ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="qr_code_size" class="control-label"><?php echo _l('zatca_qr_code_qr_size'); ?> (mm)</label>
                            <input type="number" id="qr_code_size" name="qr_code_size" class="form-control" value="<?php echo htmlspecialchars($qr_code_size ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        <button type="submit" class="btn btn-primary" name="save_main_settings">Save Main Settings</button>
<button type="submit" class="btn btn-success" name="save_and_preview">Save and Preview</button>
                        <?php echo form_close(); ?>

                        <hr />

                        <h4 class="no-margin-top"><?php echo _l('zatca_qr_code_copy_templates_heading'); ?></h4>
                        <p class="text-muted"><?php echo _l('zatca_qr_code_copy_templates_description'); ?></p>
                        <button type="button" class="btn btn-info" id="copy-pdf-templates-btn">
                            <?php echo _l('zatca_qr_code_copy_templates_button'); ?>
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<div class="modal fade" id="confirmOverwriteModal" tabindex="-1" role="dialog" aria-labelledby="confirmOverwriteModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="confirmOverwriteModalLabel"><?php echo _l('zatca_qr_code_confirm_overwrite_title'); ?></h4>
            </div>
            <div class="modal-body">
                <p id="overwrite-warning-message"></p>
                <p><?php echo _l('zatca_qr_code_confirm_overwrite_proceed'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="button" class="btn btn-danger" id="confirm-overwrite-btn"><?php echo _l('confirm'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    // This script will be loaded via app_admin_footer hook from your module's main file
    // Make sure this is in modules/zatca_qr_code/assets/js/script.js
</script>