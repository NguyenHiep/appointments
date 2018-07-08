<div id="AppAppointFoxDetails">
	<div class="appointfox-tbs">
		<h3><?php _e( 'Appointment Details', 'appointfox' ); ?></h3>
		<br>
		<input type="hidden" id="AppointmentId" value="<?php echo $appointment->id; ?>">
		<input type="hidden" id="AppointmentPrice" value="<?php echo $appointment->price; ?>">
		<input type="hidden" id="AppointmentIsPaid" value="<?php echo $appointment->is_paid; ?>">
		<table class="table">
			<tbody>
				<tr>
					<td style="width: 150px"><?php _e( 'Customer', 'appointfox' ); ?></td>
					<td>
						<?php echo $appointment->customer_name; ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Service', 'appointfox' ); ?></td>
					<td>
						<?php echo $appointment->service_title; ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'When', 'appointfox' ); ?></td>
					<td>
						<?php echo $appointment->start_datetime; ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Price', 'appointfox' ); ?></td>
					<td>
						<?php echo $settings['currency'] . $appointment->price; ?> (
						<?php echo formatDuration( $appointment->duration ); ?>)</td>
				</tr>
				<tr>
					<td><?php _e( 'Status', 'appointfox' ); ?></td>
					<td>
						<span v-if="isPaid" class="label label-success"><?php _e( 'Payment Paid', 'appointfox' ); ?></span>
						<span v-else class="label label-default"><?php _e( 'Pending Payment', 'appointfox' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div v-show="!isPaid">
			<div id="paypal-button-container"></div>
		</div>
		<div v-show="isLoadingCheckPayment">
			<br />
			<img style="margin-right: 10px; vertical-align: middle" src="<?php echo AFX_URL; ?>assets/images/loading.gif" /> <?php _e( 'Checking payment', 'appointfox' ); ?>... <?php _e( 'Please wait', 'appointfox' ); ?>...
			<br />
			<br />
		</div>
		<p>
			<?php $url     = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
			<?php $baseurl = strtok( $url, '?' ); ?>
			<a href="<?php echo $baseurl; ?>"><?php _e( 'Book another apppointment', 'appointfox' ); ?></a>
		</p>
	</div>
</div>
