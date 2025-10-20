<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin-top">
                                    <i class="fa fa-qrcode"></i> <?php echo _l('zatca_qr_module_name'); ?>
                                    <small class="text-muted">v<?php echo zatca_get_version(); ?></small>
                                </h4>
                                <p class="text-muted"><?php echo _l('zatca_qr_module_description'); ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($config_check['configured']): ?>
                                    <span class="label label-success">
                                        <i class="fa fa-check"></i> <?php echo _l('zatca_configured'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="label label-danger">
                                        <i class="fa fa-exclamation-triangle"></i> <?php echo _l('zatca_not_configured'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('zatca_invoice_qr/zatca_admin/settings'), ['id' => 'zatca-settings-form']); ?>

                        <!-- Enable Module -->
                        <div class="form-group">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="enabled" id="enabled" value="1" 
                                    <?php echo $settings->enabled ? 'checked' : ''; ?>>
                                <label for="enabled">
                                    <strong><?php echo _l('zatca_module_enabled'); ?></strong>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Phase Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phase" class="control-label">
                                        <?php echo _l('zatca_phase'); ?>
                                        <i class="fa fa-question-circle" data-toggle="tooltip" 
                                           title="<?php echo _l('zatca_help_phase1'); ?>"></i>
                                    </label>
                                    <select name="phase" id="phase" class="form-control selectpicker">
                                        <option value="phase1" <?php echo $settings->phase === 'phase1' ? 'selected' : ''; ?>>
                                            <?php echo _l('zatca_phase1'); ?>
                                        </option>
                                        <option value="phase2" <?php echo $settings->phase === 'phase2' ? 'selected' : ''; ?> disabled>
                                            <?php echo _l('zatca_phase2'); ?> (Coming Soon)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Environment Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="environment" class="control-label"><?php echo _l('zatca_environment'); ?></label>
                                    <select name="environment" id="environment" class="form-control selectpicker">
                                        <option value="sandbox" <?php echo $settings->environment === 'sandbox' ? 'selected' : ''; ?>>
                                            <?php echo _l('zatca_sandbox'); ?>
                                        </option>
                                        <option value="production" <?php echo $settings->environment === 'production' ? 'selected' : ''; ?>>
                                            <?php echo _l('zatca_production'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr />
                        <h4><?php echo _l('zatca_seller_information'); ?></h4>

                        <!-- Seller Name -->
                        <div class="form-group">
                            <label for="seller_name" class="control-label">
                                <?php echo _l('zatca_seller_name'); ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="seller_name" id="seller_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings->seller_name); ?>" required>
                            <p class="help-block"><?php echo _l('zatca_seller_name_help'); ?></p>
                        </div>

                        <!-- VAT Number -->
                        <div class="form-group">
                            <label for="vat_number" class="control-label">
                                <?php echo _l('zatca_vat_number'); ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vat_number" id="vat_number" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings->vat_number); ?>" 
                                   pattern="\d{15}" maxlength="15" required>
                            <p class="help-block"><?php echo _l('zatca_vat_number_help'); ?></p>
                        </div>

                        <!-- Company Address -->
                        <div class="form-group">
                            <label for="company_address" class="control-label"><?php echo _l('zatca_company_address'); ?></label>
                            <textarea name="company_address" id="company_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings->company_address); ?></textarea>
                            <p class="help-block"><?php echo _l('zatca_company_address_help'); ?></p>
                        </div>

                        <hr />
                        <h4><?php echo _l('zatca_qr_settings'); ?></h4>

                        <div class="row">
                            <!-- QR Position -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_position" class="control-label"><?php echo _l('zatca_qr_position'); ?></label>
                                    <select name="qr_position" id="qr_position" class="form-control selectpicker">
                                        <?php foreach ($position_options as $value => $label): ?>
                                            <option value="<?php echo $value; ?>" 
                                                <?php echo $settings->qr_position === $value ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- QR Size -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_size" class="control-label"><?php echo _l('zatca_qr_size'); ?></label>
                                    <input type="number" name="qr_size" id="qr_size" class="form-control" 
                                           value="<?php echo $settings->qr_size; ?>" min="100" max="300" step="10">
                                    <p class="help-block"><?php echo _l('zatca_qr_size_help'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Generate -->
                        <div class="form-group">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="auto_generate" id="auto_generate" value="1" 
                                    <?php echo $settings->auto_generate ? 'checked' : ''; ?>>
                                <label for="auto_generate">
                                    <?php echo _l('zatca_auto_generate'); ?>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" 
                                       title="<?php echo _l('zatca_help_auto_generate'); ?>"></i>
                                </label>
                            </div>
                        </div>

                        <hr />

                        <!-- Action Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?php echo _l('zatca_save_settings'); ?>
                            </button>
                            <button type="button" class="btn btn-info" id="test-qr-btn" 
                                    data-toggle="tooltip" title="<?php echo _l('zatca_tooltip_test'); ?>">
                                <i class="fa fa-flask"></i> <?php echo _l('zatca_test_qr'); ?>
                            </button>
                            <button type="button" class="btn btn-success" id="batch-generate-btn" 
                                    data-toggle="tooltip" title="<?php echo _l('zatca_tooltip_batch'); ?>">
                                <i class="fa fa-refresh"></i> <?php echo _l('zatca_batch_generate'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>

                        <hr />

                        <!-- Statistics -->
                        <h4><?php echo _l('zatca_statistics'); ?></h4>
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s text-center">
                                    <div class="panel-body">
                                        <h3 class="text-primary"><?php echo $statistics['total_invoices']; ?></h3>
                                        <p class="text-muted"><?php echo _l('zatca_total_invoices'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s text-center">
                                    <div class="panel-body">
                                        <h3 class="text-success"><?php echo $statistics['invoices_with_qr']; ?></h3>
                                        <p class="text-muted"><?php echo _l('zatca_invoices_with_qr'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s text-center">
                                    <div class="panel-body">
                                        <h3 class="text-warning"><?php echo $statistics['invoices_without_qr']; ?></h3>
                                        <p class="text-muted"><?php echo _l('zatca_invoices_without_qr'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s text-center">
                                    <div class="panel-body">
                                        <h3 class="text-info"><?php echo $statistics['success_rate']; ?>%</h3>
                                        <p class="text-muted"><?php echo _l('zatca_success_rate'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test QR Modal -->
<div class="modal fade" id="testQrModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('zatca_test_qr'); ?></h4>
            </div>
            <div class="modal-body">
                <div id="test-qr-result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
