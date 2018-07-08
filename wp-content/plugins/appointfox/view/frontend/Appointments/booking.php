<div id="AppAppointFox">
	<div class="appointfox-tbs">
		<template v-if="$data">
			<h3>
				<?php echo $settings['business_name']; ?>
			</h3>

			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-12 col-md-12" style="padding-left: 5px; padding-right: 5px;">
						<section>
							<div class="wizard">
								<div class="header">
									<ol class="steps nav nav-tabs" role="tablist">
										<li role="presentation" class="active">
											<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
												<?php _e( 'Choose', 'appointfox' ); ?> <span class="hidden-xs"><?php _e( 'Appointment', 'appointfox' ); ?></span>
											</a>
										</li>
										<li role="presentation" class="disabled">
											<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
												<?php _e( 'Your Details', 'appointfox' ); ?>
											</a>
										</li>
										<li role="presentation" class="disabled">
											<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
												<?php _e( 'Confirm', 'appointfox' ); ?>
											</a>
										</li>
									</ol>
								</div>

								<!-- <div class="wizard-inner">
									<div class="connecting-line"></div>
									<ul class="nav nav-tabs" role="tablist">

										<li role="presentation" class="active">
											<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
												<span class="round-tab">
													<i class="fa fa-calendar"></i>
												</span>
											</a>
										</li>

										<li role="presentation" class="disabled">
											<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
												<span class="round-tab">
													<i class="fa fa-user"></i>
												</span>
											</a>
										</li>

										<li role="presentation" class="disabled">
											<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
												<span class="round-tab">
													<i class="fa fa-check-square-o "></i>
												</span>
											</a>
										</li>
									</ul>
								</div> -->

								<form role="form" :style="{ 'background-color': background_color, 'color': font_color, 'font-size': font_size }">
									<div class="tab-content">
										<div class="tab-pane active" role="tabpanel" id="step1">
											<div id="FormServices" v-show="isShowFormServices">

												<h3><?php _e( 'Choose Appointment', 'appointfox' ); ?></h3>
												<p style="margin: 20px 0 20px">
													<?php echo $settings['instructions']; ?>
												</p>
												<p style="margin-bottom: 20px"><?php _e( 'I would like to schedule', 'appointfox' ); ?>...</p>

												<div id="PanelLoading" style="margin: 20px 0px" v-show="isLoading">
													<img src="<?php echo AFX_URL; ?>assets/images/loading.gif" />
												</div>
												<div id="app-services-list">
													<div v-if="services.length > 0 || categories.length > 0">
														<div class="list-group">
															<!--List services-->
															<a v-for="service in services" :key="service.id" v-if="service.category_id == 0" class="list-group-item" @click.prevent="showDatePicker(service)"
																:class="{ active : selected_service == service.id }">
																{{ service.title }} ({{ displayDuration(service.duration) }} @ {{ currency }}{{ service.price }})
															</a>
														</div>

														<template v-for="(category, index) in categories">
															<h4 class="category-name">
																{{ category.name }}
															</h4>

															<div class="list-group">
																<!--List services-->
																<a v-for="service in services" :key="service.id" v-if="category.id == service.category_id" class="list-group-item" @click.prevent="showDatePicker(service)"
																	:class="{ active : selected_service == service.id }">
																	{{ service.title }} ({{ displayDuration(service.duration) }} @ {{ currency }}{{ service.price }})
																</a>
															</div>
														</template>
													</div>
													<div v-else>
														<p v-show="!isLoading">
															<?php _e( "Nothing left in the list. We'll let you know when it's ready.", 'appointfox' ); ?>
														</p>
													</div>
												</div>
											</div>

											<div id="FormDateTime" v-show="isShowFormDatePicker">
												<div>
													<a @click.prevent="showServices()" class="btn btn-link">
														&#8592; <?php _e( 'Change', 'appointfox' ); ?></a>
												</div>
												<div class="list-group">
													<a class="list-group-item" @click.prevent="showServices()">
														{{ formAppointment.service.title }} ({{ displayDuration(formAppointment.service.duration) }} @ {{ currency }}{{ formAppointment.service.price
														}})
													</a>
												</div>
												<div class="form-group">
													<div class="row">
														<div class="col-xs-12 col-md-6">
															<label for="AppointmentStartDate"><?php _e( 'Date', 'appointfox' ); ?></label>
															<p v-show="isLoadingDatePicker"><?php _e( 'Loading', 'appointfox' ); ?>...</p>
															<date-picker v-show="isShowDatePicker" v-model="formAppointment.start_date" :config="dateTimePickerConfig" @dp-change="showTimes"
																@dp-show="hideLoading"></date-picker>
														</div>
														<div class="col-xs-12 col-md-6">
															<label for="AppointmentStartTime"><?php _e( 'Time', 'appointfox' ); ?></label>
															<select v-model="formAppointment.start_time" id="FormAppointmentStartTime" class="form-control" required>
																<option value="">{{ defaultStartTimeLabel }}</option>
																<option v-for="time in times" :value="time">{{ time }}</option>
															</select>
														</div>
													</div>
												</div>
												<div class="pull-right">
													<button v-show="isShowDatePicker" type="button" class="btn btn-success" @click="showFormCustomer()"><?php _e( 'Confirm Date & Time', 'appointfox' ); ?></button>
												</div>
											</div>


										</div>
										<div class="tab-pane" role="tabpanel" id="step2">
											<div>
												<button type="button" class="btn btn-link" @click="backToAppointment">
													&#8592; <?php _e( 'Change', 'appointfox' ); ?></button>
											</div>
											<p>
												<b>{{ formAppointment.service.title }}</b> ({{ displayDuration(formAppointment.service.duration) }} @ {{ currency
												}}{{ formAppointment.service.price }}) on {{ formatDate(formAppointment.start_date) }} at {{ formAppointment.start_time
												}}
											</p>
											<h3><?php _e( 'Your Info', 'appointfox' ); ?></h3>

											<div class="form-group">
												<label for="inputName"><?php _e( 'Name', 'appointfox' ); ?> *</label>
												<input v-model="formAppointment.customer_name" type="text" class="form-control" id="inputName" placeholder="<?php _e( 'Enter your name', 'appointfox' ); ?>....">
											</div>
											<div class="form-group">
												<label for="inputPhone"><?php _e( 'Phone', 'appointfox' ); ?></label>
												<input v-model="formAppointment.customer_phone" type="text" class="form-control" id="inputPhone" placeholder="<?php _e( 'Enter your phone number', 'appointfox' ); ?>...">
											</div>
											<div class="form-group">
												<label for="inputEmail"><?php _e( 'Email', 'appointfox' ); ?> *</label>
												<input v-model="formAppointment.customer_email" type="email" class="form-control" id="inputEmail" placeholder="<?php _e( 'Enter your email', 'appointfox' ); ?>....">
											</div>

											<div class="pull-right">
												<button @click.prevent="saveAppointment()" type="button" class="btn btn-success ladda-button" data-style="expand-left">
													<span class="ladda-label"><?php _e( 'Complete Appointment', 'appointfox' ); ?></span>
												</button>
												<!-- <button type="button" class="btn btn-success" @click.prevent="saveAppointment()">Complete Appointment</button> -->
											</div>
										</div>

										<div class="tab-pane appointment-complete" role="tabpanel" id="complete">
											<div class="appointment-complete-tick">
												<div class="swal2-icon swal2-success swal2-animate-success-icon" style="display: block;">
													<div class="swal2-success-circular-line-left" style="background: rgb(255, 255, 255);"></div>
													<span class="swal2-success-line-tip swal2-animate-success-line-tip"></span>
													<span class="swal2-success-line-long swal2-animate-success-line-long"></span>
													<div class="swal2-success-ring"></div>
													<div class="swal2-success-fix" style="background: rgb(255, 255, 255);"></div>
													<div class="swal2-success-circular-line-right" style="background: rgb(255, 255, 255);"></div>
												</div>
											</div>
											<div class="appointment-complete-info">
												{{ formAppointment.customer_name }}
												<br/>
												<b>{{ formAppointment.service.title }}</b>
												<br/> {{ formatDate(formAppointment.start_date) }}
												<br /> {{ formAppointment.start_time_display }}
												<br/> {{ currency }}{{ formAppointment.service.price }} ({{ displayDuration(formAppointment.service.duration) }})
												<br />
												<span v-show="formAppointment.is_paid" class="label label-success"><?php _e( 'Payment Paid', 'appointfox' ); ?></span>
												<br />
												<div v-if="payment_method == 'PayPal'">
													<div v-show="!formAppointment.is_paid">
														<div id="paypal-button-container"></div>
													</div>
												</div>
												<div v-show="isLoadingCheckPayment">
													<br />
													<img style="margin-right: 10px; vertical-align: middle" src="<?php echo AFX_URL; ?>assets/images/loading.gif" /> <?php _e( 'Checking payment', 'appointfox' ); ?>... <?php _e( 'Please wait', 'appointfox' ); ?>...
													<br />
													<br />
												</div>

												<a href="#" @click.prevent="resetForm()" class="book-another"><?php _e( 'Book another appointment', 'appointfox' ); ?></a>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</form>
							</div>
						</section>
					</div>
				</div>
			</div>
		</template>
	</div>
</div>
