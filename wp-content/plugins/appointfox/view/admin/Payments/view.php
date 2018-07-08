<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				<i class="fa fa-times"></i>
			</button>
			<h4 class="modal-title" id="myModalLabel">
				<?php echo __('View Payment Details', 'appointfox'); ?>
			</h4>
		</div>
		<div class="modal-body">
			<table class="table table-striped-custom">
				<tbody>
					<tr>
						<td>
							<?php echo __('Payment Date/Time', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field(date( 'j-M-Y g:ia', strtotime( $payment->created ) )); ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td style="width: 150px">
							<?php echo __('Customer', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field($payment->customer_name); ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __('Service', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field($payment->service_name); ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __('Appointment Date/Time', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field(date( 'j-M-Y g:ia', strtotime( $payment->appointment_datetime ) )); ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __('Amount', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field($payment->payment_amount); ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __('Method', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field($payment->payment_type); ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __('Status', 'appointfox'); ?>
						</td>
						<td>
							<?php if ($payment->payment_status == 'Paid') : ?>
							<span class="label label-success">
                                <?php _e( $payment->payment_status, 'appointfox' ); ?>
								<?php //echo sanitize_text_field($payment->payment_status); ?>
							</span>
							<?php else : ?>
							<span class="label label-default">
                                <?php _e( $payment->payment_status, 'appointfox' ); ?>
								<?php //echo sanitize_text_field($payment->payment_status); ?>
							</span>
							<?php endif; ?> &nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __('Payment ID', 'appointfox'); ?>
						</td>
						<td>
							<?php echo sanitize_text_field($payment->txnid); ?> &nbsp;
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