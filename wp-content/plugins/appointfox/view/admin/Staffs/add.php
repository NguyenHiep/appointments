<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                <div>
                    <?php echo __( 'Add New Staff', 'appointfox' ); ?>
				</div>
			</h4>
		</div>
		<form id="FormStaffsAdd" method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=afx-staffs-save&_ajax_nonce=<?php echo $save_nonce; ?>">
			<div class="modal-body">
				<div class="form-group">
					<label for="inputFullname"><?php _e( 'Full name', 'appointfox' ); ?></label>
					<input name="data[Staff][full_name]" type="text" class="form-control" id="StaffFullName" placeholder="<?php _e( 'Full name', 'appointfox' ); ?>" required="">
				</div>
				<div class="form-group">
					<label for="inputEmail"><?php _e( 'Email', 'appointfox' ); ?></label>
					<input name="data[Staff][email]" type="email" class="form-control" id="StaffEmail" placeholder="<?php _e( 'Email', 'appointfox' ); ?>" required="">
				</div>
				<div class="form-group">
					<label for="inputPhone"><?php _e( 'Phone', 'appointfox' ); ?></label>
					<input name="data[Staff][phone]" type="text" class="form-control" id="StaffPhone" placeholder="<?php _e( 'Phone', 'appointfox' ); ?>">
				</div>
				<div class="form-group">
					<label for="inputInfo"><?php _e( 'Info', 'appointfox' ); ?></label>
					<textarea name="data[Staff][info]" id="StaffInfo" class="form-control" rows="3" placeholder="<?php _e( 'Info', 'appointfox' ); ?>"></textarea>
				</div>
				<div class="form-group">
					<label for="inputService"><?php _e( 'This staff can provide the following service(s)', 'appointfox' ); ?>:</label>
					<?php foreach ( $services as $service ) : ?>
					<?php if ( $service->category_id == 0 ) : ?>
					<div class="checkbox">
						<label>
							<input name="data[Staff][services][]" type="checkbox" value="<?php echo $service->id; ?>">
							<?php echo $service->title; ?>
						</label>
					</div>
					<?php endif; ?>
					<?php endforeach; ?>

					<?php foreach ( $categories as $category ) : ?>
					<h5>
						<?php echo $category->name; ?>
					</h5>
					<?php foreach ( $services as $service ) : ?>
					<?php if ( $service->category_id === $category->id ) : ?>
					<div class="checkbox">
						<label>
							<input name="data[Staff][services][]" type="checkbox" value="<?php echo $service->id; ?>">
							<?php echo $service->title; ?>
						</label>
					</div>
					<?php endif; ?>
					<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-dismiss="modal"><?php _e( 'Close', 'appointfox' ); ?></button>
				<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
					<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
				</button>
			</div>
		</form>
	</div>
</div>

<script>
	$('#FormStaffsAdd').ajaxForm({
		dataType: 'json',
		beforeSubmit: function (formData, jqForm, options) {
			jqForm.find('.text-danger').remove();
			jqForm.find('.form-group').removeClass('has-error');
			jqForm.find('.form-control').removeClass('form-error');
		},
		success: function (data) {
			if (data.success) {
				$('#mainmodal').modal('hide');

				$('#table_staffs').DataTable().ajax.reload();

				// prompt success
				swal(
					afx_dt.labels.success1 + '!',
					afx_dt.labels.success2 + '.',
					'success'
				);
				//                $.notify("Record successfully saved", {className: 'success', position: "right bottom"});

			} else {

				$.each(data.errors, function () {
					var element = $('#Staff' + camelize(this.field));
					element.addClass('form-error');
					element.parent().addClass('has-error');

					var _insert = $(document.createElement('span')).insertAfter(element);
					_insert.addClass('help-block text-danger').text(this.msg);

				});
			}
			Ladda.stopAll();
		}
	});

	Ladda.bind('button[type=submit]');
</script>
