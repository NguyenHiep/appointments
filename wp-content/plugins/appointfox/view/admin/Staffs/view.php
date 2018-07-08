<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                <?php echo __('View Staff Details', 'appointfox'); ?>
            </h4>
        </div>
        <div class="modal-body">
            <table class="table table-striped-custom">
                <tbody>
                    <!-- <tr>
                        <td style="width: 150px"><?php echo __('Id', 'appointfox'); ?></td>
                        <td>
                            <?php echo sanitize_text_field($staff->id); ?>
                            &nbsp;
                        </td>
                    </tr> -->
                    <tr>
                        <td style="width: 150px">
                            <?php echo __('Name', 'appointfox'); ?>
                        </td>
                        <td>
                            <?php echo sanitize_text_field($staff->full_name); ?> &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo __('Email', 'appointfox'); ?>
                        </td>
                        <td>
                            <?php echo sanitize_text_field($staff->email); ?> &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo __('Info', 'appointfox'); ?>
                        </td>
                        <td>
                            <?php echo sanitize_text_field($staff->info); ?> &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo __('Services', 'appointfox'); ?>
                        </td>
                        <td>
                            <?php if (count($staffs_services) > 0) : ?>
                            <ul>
                                <?php foreach ($staffs_services as $service) : ?>
                                <li>
                                    <?php echo $service->title; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else:  ?> -
                            <?php endif; ?>
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