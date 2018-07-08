<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php _e( 'Edit Appointment', 'appointfox' ); ?></h4>
		</div>
		<form id="FormAppointmentsEdit" method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=afx-appointments-save&id=<?php echo $appointment->id; ?>&_ajax_nonce=<?php echo $save_nonce; ?>">
			<div class="modal-body">
				<input type="hidden" name="data[Appointment][id]" value="<?php echo sanitize_text_field( $appointment->id ); ?>" />
				<div class="form-group">
					<label for="inputFullname"><?php _e( 'Full name', 'appointfox' ); ?></label>
					<input name="data[Appointment][full_name]" type="text" class="form-control" id="AppointmentFullName" placeholder="<?php _e( 'Full name', 'appointfox' ); ?>" value="<?php echo sanitize_text_field( $appointment->full_name ); ?>">
				</div>
				<div class="form-group">
					<label for="inputEmail"><?php _e( 'Email', 'appointfox' ); ?></label>
					<input name="data[Appointment][email]" type="email" class="form-control" id="AppointmentEmail" placeholder="<?php _e( 'Email', 'appointfox' ); ?>" value="<?php echo sanitize_text_field( $appointment->email ); ?>">
				</div>
				<div class="form-group">
					<label for="inputPhone"><?php _e( 'Phone', 'appointfox' ); ?></label>
					<input name="data[Appointment][phone]" type="text" class="form-control" id="AppointmentPhone" placeholder="<?php _e( 'Phone', 'appointfox' ); ?>" value="<?php echo sanitize_text_field( $appointment->phone ); ?>">
				</div>
				<div class="form-group">
					<label for="inputInfo"><?php _e( 'Info', 'appointfox' ); ?></label>
					<textarea name="data[Appointment][info]" id="AppointmentInfo" class="form-control" rows="3" placeholder="<?php _e( 'Info', 'appointfox' ); ?>"><?php echo sanitize_text_field( $appointment->info ); ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-dismiss="modal"><?php _e( 'Close', 'appointfox' ); ?></button>
				<button type="submit" class="btn btn-primary ladda-button" data-style="expand-left"><span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span></button>
				<!--<a class="btn" data-dismiss="modal">Close</a>-->
			</div>
		</form>
	</div>
</div>

<script>
	$('#FormAppointmentsEdit').ajaxForm({
		dataType: 'json',
		beforeSubmit: function (formData, jqForm, options) {
			jqForm.find('.text-danger').remove();
			jqForm.find('.form-group').removeClass('has-error');
			jqForm.find('.form-control').removeClass('form-error');
		},
		success: function (data) {
			if (data.success) {
				$('#mainmodal').modal('hide');
//                $.notify("Record successfully saved", {className: 'success', position: "right bottom"});

				$('#table_appointments').DataTable().ajax.reload();
				
                // prompt success
				swal(
					afx_dt.labels.success1 + '!',
					afx_dt.labels.success2 + '.',
					'success'
				);
			} else {

				$.each(data.errors, function () {
					var element = $('#Appointment' + camelize(this.field));
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
