<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title" id="myModalLabel"><?php _e( 'View Appointment Details', 'appointfox' ); ?></h4>
        </div>
        <div class="modal-body">
            <table class="table table-striped-custom">
                <tbody>
                    <!-- <tr>
                        <td style="width: 150px"><?php echo __('Id', 'appointfox'); ?></td>
                        <td>
                            <?php echo sanitize_text_field($appointment->id); ?>
                            &nbsp;
                        </td>
                    </tr> -->
                    <tr>
                        <td style="width: 150px"><?php echo __('Name', 'appointfox'); ?></td>
                        <td>
                            <?php echo sanitize_text_field($appointment->full_name); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Email', 'appointfox'); ?></td>
                        <td>
                            <?php echo sanitize_text_field($appointment->email); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Info', 'appointfox'); ?></td>
                        <td>
                            <?php echo sanitize_text_field($appointment->info); ?>
                            &nbsp;
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <a class="btn" data-dismiss="modal"><?php _e( 'Close', 'appointfox' ); ?></a>
        </div>
    </div>
</div>