<div id="app">

	<div class="wrap">
		<h2 class='opt-title'>
			<span id='icon-options-general' class='analytics-options'>
				<img src="<?php echo AFX_URL.'/assets/images/icon16.png';  ?>" alt="">
			</span>
            AppointFox - <?php _e( 'Appointments Calendar', 'appointfox' ); ?>
		</h2>

		<div class="appointfox-tbs">
			<div class="container-fluid">
				<div class="appointfox-wrap">
					<div class="row">
						<template v-if="!$data">
							<div class="loading-bar">
								<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
								<br> <?php _e( 'Loading', 'appointfox' ); ?>.....
							</div>
						</template>

						<template v-cloak>
							<div class="col-md-12 text-right">
								<a id="btnAddAppointment" @click="showAddAppointmentForm()" href="#" class="btn btn-primary">
									<i class="fa fa-plus"></i> <?php _e( 'Add Appointment', 'appointfox' ); ?></a>
							</div>
							<div style="margin-top: 20px" class="col-md-12">
								<div id="fullcalendar"></div>
							</div>
						</template>
					</div>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal" id="ModalViewAppointment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title">
								<span><?php _e( 'View Appointment', 'appointfox' ); ?></span>
							</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label><?php _e( 'Service', 'appointfox' ); ?></label>
								<p class="form-control-static">{{ viewAppointment.service_title }}</p>
							</div>
							<div class="form-group">
								<label><?php _e( 'Staff', 'appointfox' ); ?></label>
								<p class="form-control-static">{{ viewAppointment.staff_name }}</p>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label for="AppointmentStartDate"><?php _e( 'Date', 'appointfox' ); ?></label>
										<p class="form-control-static">{{ viewAppointment.start_date }}</p>
									</div>
									<div class="col-md-6">
										<label for="AppointmentStartTime"><?php _e( 'Time', 'appointfox' ); ?></label>
										<p class="form-control-static">{{ viewAppointment.start_time }}</p>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="AppointmentCustomer"><?php _e( 'Customer', 'appointfox' ); ?></label>
								<p class="form-control-static">{{ viewAppointment.customer_name }}</p>
								<p class="form-control-static">
									<b><?php _e( 'Phone', 'appointfox' ); ?>:</b> {{ viewAppointment.customer_phone }}</p>
								<p class="form-control-static">
									<b><?php _e( 'Email', 'appointfox' ); ?>:</b> {{ viewAppointment.customer_email }}</p>
							</div>
							<div class="form-group">
								<label for="AppointmentNote"><?php _e( 'Note', 'appointfox' ); ?></label>
								<p v-show="!isShowEditAppointmentNote" class="form-control-static">{{ viewAppointment.note }}</p>
								<button v-show="!isShowEditAppointmentNote" @click="showEditAppointmentNote" class="btn btn-default"><?php _e( 'Edit note', 'appointfox' ); ?></button>
								<div v-show="isShowEditAppointmentNote">
									<textarea rows="4" class="form-control" name="data[Appointment][note]" v-model="viewAppointment.new_note" id="FormAppointmentNote2">
									</textarea>
									<div style="margin-top: 10px">
										<button @click="cancelSaveAppointmentNote" type="button" class="btn btn-link"><?php _e( 'Cancel', 'appointfox' ); ?></button>
										<button @click="saveAppointmentNote" type="button" class="btn btn-success ladda-button" data-style="expand-left">
											<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
										</button>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<b><?php _e( 'Price', 'appointfox' ); ?>:</b> {{ currency }}{{ viewAppointment.price }}
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<b><?php _e( 'Price', 'appointfox' ); ?>:</b> 
										<span v-if="viewAppointment.is_paid" class="label label-success"><?php _e( 'Paid', 'appointfox' ); ?></span>
										<span v-else class="label label-default"><?php _e( 'Pending', 'appointfox' ); ?></span>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-link" data-dismiss="modal"><?php _e( 'Close', 'appointfox' ); ?></button>
							<!-- <button type="button" class="btn btn-danger ladda-button" data-style="expand-left" @click.prevent="deleteAppointment()">
									<span class="ladda-label">Cancel</span>
								</button> -->
							<button type="button" class="btn btn-danger ladda-button" @click.prevent="deleteAppointment()" data-style="expand-left">
								<span class="ladda-label"><?php _e( 'Cancel this appointment', 'appointfox' ); ?></span>
							</button>
							<button v-if="!viewAppointment.is_paid" type="button" class="btn btn-success" data-style="expand-left" @click.prevet="markPaid()">
								<?php _e( 'Mark payment paid', 'appointfox' ); ?>
							</button>
							<button type="button" class="btn btn-primary" data-style="expand-left" @click.prevet="showAppointmentForm()">
								<?php _e( 'Edit', 'appointfox' ); ?>
							</button>
						</div>
					</div>
					</form>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal" id="ModalFormAppointment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<form id="FormAppointment" method="POST" v-on:submit.prevent="saveAppointment">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4 class="modal-title" id="ModalFormAppointmentTitle">
									<span v-if="modeFormAppointment === 'add'"><?php _e( 'Create a new Appointment', 'appointfox' ); ?>...</span>
									<span v-else><?php _e( 'Edit Appointment', 'appointfox' ); ?></span>
								</h4>
							</div>
							<div class="modal-body">
								<input id="FormAppointmentId" name="data[Appointment][id]" type="hidden" v-model="formAppointment.id">
								<div class="form-group">
									<label for="AppointmentServiceId"><?php _e( 'Service', 'appointfox' ); ?></label>
									<select name="data[Appointment][service_id]" v-model="formAppointment.service_id" id="FormAppointmentServiceId" class="form-control"
									    @change="showStaffs" required>
										<option value=""><?php _e( 'Choose service', 'appointfox' ); ?>...</option>
										<option v-for="service in services" :key="service.id" v-if="service.category_id == 0" :value="service.id">{{ service.title }} ({{ displayDuration(service.duration) }} @ {{ currency }}{{ service.price }})
										</option>

										<template v-for="(category, index) in categories">
											<optgroup :label="category.name">
												<option v-for="service in services" :key="service.id" v-if="category.id == service.category_id" :value="service.id">{{ service.title }} ({{ displayDuration(service.duration) }} @ {{ currency }}{{ service.price }})
												</option>
											</optgroup>
										</template>
									</select>
								</div>
								<div class="form-group">
									<label for="AppointmentStaffId"><?php _e( 'Staff', 'appointfox' ); ?></label>
									<select @change="refreshDatePicker" name="data[Appointment][staff_id]" v-model="formAppointment.staff_id" id="FormAppointmentStaffId"
									    class="form-control" required>
										<option value="">{{ defaultStaffLabel }}</option>
										<option v-for="staff in staffs" :value="staff.id">{{ staff.full_name }}</option>
									</select>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-md-6">
											<label for="AppointmentStartDate"><?php _e( 'Date', 'appointfox' ); ?></label>
											<p v-show="isLoadingDatePicker"><?php _e( 'Loading', 'appointfox' ); ?>...</p>
											<!-- <div id="datetimepicker"></div> -->
											<date-picker v-show="isShowDatePicker" v-model="formAppointment.start_date" name="data[Appointment][start_date]" :config="dateTimePickerConfig"
											    @dp-change="showTimes" @dp-show="hideLoading"></date-picker>
										</div>
										<div class="col-md-6">
											<label for="AppointmentStartTime"><?php _e( 'Time', 'appointfox' ); ?></label>
											<select name="data[Appointment][start_time]" v-model="formAppointment.start_time" id="FormAppointmentStartDateTime" class="form-control"
											    required>
												<option value="">{{ defaultStartTimeLabel }}</option>
												<option v-for="time in times" :value="time">{{ time }}</option>
												<!-- <option value="10:00">10:00 am</option> -->
												<!-- <option value="15:00">3:00 pm</option> -->
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="AppointmentCustomer"><?php _e( 'Customer', 'appointfox' ); ?></label>
									<select name="data[Appointment][customer_id]" v-model="formAppointment.customer_id" id="FormAppointmentCustomer" class="form-control"
									    required>
										<option value=""><?php _e( 'Choose customer', 'appointfox' ); ?>...</option>
										<option v-for="customer in customers" :value="customer.id">{{ customer.full_name }}</option>
									</select>
									<p>
										<a data-toggle="modal" href="#ModalFormCustomer" class="btn btn-link"><?php _e( 'Create new customer', 'appointfox' ); ?></a>
									</p>
								</div>
								<div class="form-group">
									<label for="AppointmentNote"><?php _e( 'Note', 'appointfox' ); ?></label>
									<textarea rows="4" class="form-control" name="data[Appointment][note]" v-model="formAppointment.note" id="FormAppointmentNote">
									</textarea>
								</div>
								<div class="form-group">
									<label for="AppointmentPrice"><?php _e( 'Price', 'appointfox' ); ?></label>
									<input class="form-control" name="data[Appointment][price]" v-model="formAppointment.price" v-money="money" id="AppointmentPrice"
									    placeholder="0.00">
								</div>
								<div class="form-group">
									<label for="AppointmentIsPaid"><?php _e( 'Payment', 'appointfox' ); ?></label>
									<br /> <?php _e( 'Paid', 'appointfox' ); ?>?
									<input type="checkbox" name="data[Appointment][is_paid]" v-model="formAppointment.is_paid" id="AppointmentIsPaid">
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-link" data-dismiss="modal"><?php _e( 'Close', 'appointfox' ); ?></button>
								<!-- <button v-show="formAppointment.id" type="button" class="btn btn-danger ladda-button" data-style="expand-left" @click.prevent="deleteAppointment()">
									<span class="ladda-label">Delete</span>
								</button> -->
								<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
									<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="modal" id="ModalFormCustomer" data-backdrop="static">
				<div class="modal-dialog">
					<div class="modal-content">
						<form id="FormCustomer" method="POST" v-on:submit.prevent="saveCustomer">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4><?php _e( 'Create a new Customer', 'appointfox' ); ?>...</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="CustomerFullName"><?php _e( 'Name', 'appointfox' ); ?></label>
									<input id="CustomerFullName" name="data[Customer][full_name]" v-model="formCustomer.full_name" type="text" class="form-control"
									    placeholder="<?php _e( 'Full Name', 'appointfox' ); ?>">
								</div>
								<div class="form-group">
									<label for="CustomerEmail"><?php _e( 'Email', 'appointfox' ); ?></label>
									<input id="CustomerEmail" name="data[Customer][email]" v-model="formCustomer.email" type="email" class="form-control" placeholder="<?php _e( 'Email', 'appointfox' ); ?>">
								</div>
								<div class="form-group">
									<label for="CustomerPhone"><?php _e( 'Phone', 'appointfox' ); ?></label>
									<input id="CustomerPhone" name="data[Customer][phone]" v-model="formCustomer.phone" type="text" class="form-control" placeholder="<?php _e( 'Phone', 'appointfox' ); ?>">
								</div>
								<div class="form-group">
									<label for="CustomerInfo"><?php _e( 'Info', 'appointfox' ); ?></label>
									<textarea rows="4" class="form-control" name="data[Appointment][info]" v-model="formCustomer.info" id="CustomerInfo">
									</textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button @click="cancelCustomer" type="button" class="btn btn-link"><?php _e( 'Cancel', 'appointfox' ); ?></button>
								<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
									<span class="ladda-label"><?php _e( 'Create', 'appointfox' ); ?></span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>

	</div>
</div>