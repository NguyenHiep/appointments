<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo __('Edit Payment', 'appointfox'); ?></h4>
		</div>
		<form id="FormPaymentsEdit" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>?action=afx-payments-save&id=<?php echo $payment->id ?>&_ajax_nonce=<?php echo $save_nonce; ?>">
			<div class="modal-body">
				<input type="hidden" name="data[Payment][id]" value="<?php echo sanitize_text_field($payment->id); ?>" />
				<div class="form-group">
					<label for="inputFullname"><?php _e( 'Full name', 'appointfox' ); ?></label>
					<input name="data[Payment][full_name]" type="text" class="form-control" id="PaymentFullName" placeholder="<?php _e( 'Full name', 'appointfox' ); ?>" value="<?php echo sanitize_text_field($payment->full_name); ?>">
				</div>
				<div class="form-group">
					<label for="inputEmail"><?php _e( 'Email', 'appointfox' ); ?></label>
					<input name="data[Payment][email]" type="email" class="form-control" id="PaymentEmail" placeholder="<?php _e( 'Email', 'appointfox' ); ?>" value="<?php echo sanitize_text_field($payment->email); ?>">
				</div>
				<div class="form-group">
					<label for="inputPhone"><?php _e( 'Phone', 'appointfox' ); ?></label>
					<input name="data[Payment][phone]" type="text" class="form-control" id="PaymentPhone" placeholder="<?php _e( 'Phone', 'appointfox' ); ?>" value="<?php echo sanitize_text_field($payment->phone); ?>">
				</div>
				<div class="form-group">
					<label for="inputInfo"><?php _e( 'Info', 'appointfox' ); ?></label>
					<textarea name="data[Payment][info]" id="PaymentInfo" class="form-control" rows="3" placeholder="<?php _e( 'Info', 'appointfox' ); ?>"><?php echo sanitize_text_field($payment->info); ?></textarea>
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
	$('#FormPaymentsEdit').ajaxForm({
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

				$('#table_payments').DataTable().ajax.reload();
				
				// prompt success
                swal(
                    afx_dt.labels.success1 + '!',
                    afx_dt.labels.success2 + '.',
                    'success'
                );
			} else {

				$.each(data.errors, function () {
					var element = $('#Payment' + camelize(this.field));
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