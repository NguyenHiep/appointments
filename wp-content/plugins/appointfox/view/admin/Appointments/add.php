<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				<i class="fa fa-times"></i>
			</button>
			<h4 class="modal-title" id="myModalLabel">
				<?php _e( 'Add New Appointment', 'appointfox' ); ?>
			</h4>
		</div>
		<form id="FormAppointmentsAdd" method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=afx-appointments-save&_ajax_nonce=<?php echo $save_nonce; ?>">
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="inputService"><?php _e( 'Service', 'appointfox' ); ?></label>
							<select name="data[Appointment][service_id]" class="form-control" id="AppointmentServiceId" required>
								<option value="">(<?php _e( 'Select service', 'appointfox' ); ?>)</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="inputStaff"><?php _e( 'Staff', 'appointfox' ); ?></label>
							<select name="data[Appointment][staff_id]" class="form-control" id="AppointmentStaffId" required>
								<option value="">(<?php _e( 'Select staff', 'appointfox' ); ?>)</option>
							</select>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="inputDate"><?php _e( 'Date', 'appointfox' ); ?></label>
							<input name="data[Appointment][start_date]" type="text" class="form-control" id="AppointmentStartDate" required="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="inputStartTime"><?php _e( 'Start Time', 'appointfox' ); ?></label>
							<input name="data[Appointment][start_time]" type="text" class="form-control" id="AppointmentStartTime" required="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="inputEndTime"><?php _e( 'End Time', 'appointfox' ); ?></label>
							<input name="data[Appointment][end_time]" type="text" class="form-control" id="AppointmentEndTime" required="">
						</div>
					</div>
				</div>


				<div class="form-group">
					<label for="inputEmail"><?php _e( 'Email', 'appointfox' ); ?></label>
					<input name="data[Appointment][email]" type="email" class="form-control" id="AppointmentEmail" placeholder="<?php _e( 'Email', 'appointfox' ); ?>" required="">
				</div>
				<div class="form-group">
					<label for="inputPhone"><?php _e( 'Phone', 'appointfox' ); ?></label>
					<input name="data[Appointment][phone]" type="text" class="form-control" id="AppointmentPhone" placeholder="<?php _e( 'Phone', 'appointfox' ); ?>">
				</div>
				<div class="form-group">
					<label for="inputInfo"><?php _e( 'Info', 'appointfox' ); ?></label>
					<textarea name="data[Appointment][info]" id="AppointmentInfo" class="form-control" rows="3" placeholder="<?php _e( 'Info', 'appointfox' ); ?>"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-dismiss="modal"><?php _e( 'Close', 'appointfox' ); ?></button>
				<button type="submit" class="btn btn-primary ladda-button" data-style="expand-left">
					<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
				</button>
			</div>
		</form>
	</div>
</div>

<script>
	$('#FormAppointmentsAdd').ajaxForm({
		dataType: 'json',
		beforeSubmit: function (formData, jqForm, options) {
			jqForm.find('.text-danger').remove();
			jqForm.find('.form-group').removeClass('has-error');
			jqForm.find('.form-control').removeClass('form-error');
		},
		success: function (data) {
			if (data.success) {
				$('#mainmodal').modal('hide');

				$('#table_appointments').DataTable().ajax.reload();

				// prompt success
				swal(
					afx_dt.labels.success1 + '!',
					afx_dt.labels.success2 + '.',
					'success'
				);
				//                $.notify("Record successfully saved", {className: 'success', position: "right bottom"});

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
