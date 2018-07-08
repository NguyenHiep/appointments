<div>
	<div class="wrap">
		<h2 class="opt-title">
			<span id="icon-options-general">
				<img src="<?php echo AFX_URL.'/assets/images/icon16.png';  ?>" alt="">
			</span>
            AppointFox - <?php _e( 'Availability Calendars', 'appointfox' ); ?>
		</h2>
		<div class="appointfox-tbs">
			<div class="container-fluid">
				<div class="appointfox-wrap">
					<main id="app">
						<router-view></router-view>
					</main>
				</div>
			</div>
		</div>
	</div>
</div>

<template id="list">
	<div>
		<div v-if="loading" class="loading-bar">
			<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
			<br> <?php _e( 'Loading', 'appointfox' ); ?>.....
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<input v-model="searchKey" class="form-control" id="search-element" placeholder="<?php _e( 'Search', 'appointfox' ); ?>..." />
				</div>
			</div>
			<div class="col-md-6">
				<div class="text-right">
					<router-link class="btn btn-primary" :to="{path: '/add'}">
						<span class="glyphicon glyphicon-plus"></span>
						<?php _e( 'Add New Availability Calendar', 'appointfox' ); ?>
					</router-link>
					<!-- <button class="btn btn-primary">Add New Availability Calendar</button> -->
				</div>
			</div>
		</div>
		<?php $id = 1; ?>
		<div v-if="calendars.length > 0" class="row" style="margin-top: 20px">
			<div v-for="(calendar, index) in filteredCalendars" class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-body text-center">
						<img style="margin: auto" src="<?php echo esc_url( AFX_URL. 'assets/images/calendar.png' ); ?>" alt="" class="img-responsive" />
						<h3>
							{{ calendar.name | truncate }}
						</h3>
					</div>
					<div class="panel-footer text-center">
						<!-- <a href="<?php echo esc_url( admin_url( 'admin.php?page=appointfox-availability&id=' . $id ) ); ?>" class="btn btn-default">Edit</a> -->
						<router-link class="btn btn-default" :to="{name: 'edit', params: {id: calendar.id}}"><?php _e( 'Edit', 'appointfox' ); ?></router-link>
						<router-link class="btn btn-primary" :to="{name: 'settings', params: {id: calendar.id}}"><?php _e( 'Settings', 'appointfox' ); ?></router-link>
						<button class="btn btn-danger" @click.prevent="deleteCalendar(calendar, index)"><?php _e( 'Delete', 'appointfox' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<!-- <div v-if="calendars.length == 0">
			<div style="height: 200px; line-height: 200px; text-align: center">
				Click
				<span class="label label-default">Add</span> button above to add new availability calendar
			</div>
		</div> -->
	</div>
</template>

<template id="add">
	<div>
		<div v-if="loading" class="loading-bar">
			<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
			<br> <?php _e( 'Loading', 'appointfox' ); ?>.....
		</div>
		<div class="row">
			<div class="col-md-12">
				<h4><?php _e( 'Add New Availability Calendar', 'appointfox' ); ?></h4>
				<br>
				<br>
				<form id="FormCalendarAdd" v-on:submit.prevent="saveCalendar" class="form-horizontal" role="form">
					<div class="form-group">
						<label for="inputName" class="col-sm-2 control-label"><?php _e( 'Name', 'appointfox' ); ?></label>
						<div class="col-sm-10">
							<input v-model="frmCalendar.name" key="add-name" type="text" id="inputName" placeholder="<?php _e( 'Name', 'appointfox' ); ?>" class="form-control" value=""
							    required="required" title="">
						</div>
					</div>
					<div class="form-group">
						<label for="inputName" class="col-sm-2 control-label"><?php _e( 'Assign to Staff(s)', 'appointfox' ); ?></label>
						<div class="col-sm-10">
							<?php foreach ($staffs as $staff) : ?>
							<div class="checkbox">
								<label>
									<input v-model="frmCalendar.staffs" id="checkbox_<?php echo $staff->id ?>" type="checkbox" value="<?php echo $staff->id ?>">
									<?php echo $staff->full_name ?>
								</label>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
								<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
							</button>
							<!-- <a href="javascript: window.history.go(-1)" class="btn btn-link">Cancel</a> -->
							<router-link class="btn btn-link" :to="{path: '/'}">
								<?php _e( 'Cancel', 'appointfox' ); ?>
							</router-link>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</template>

<template id="edit">
	<div>
		<div v-if="loading" class="loading-bar">
			<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
			<br> <?php _e( 'Loading', 'appointfox' ); ?>.....
		</div>
		<div v-if="frmCalendar" class="row">
			<div class="col-md-12">
				<h2><?php _e( 'Edit Availability Calendar', 'appointfox' ); ?></h2>
				<br>
				<br>
				<form id="FormCalendarEdit" v-on:submit.prevent="saveCalendar" class="form-horizontal" role="form">
					<div class="form-group">
						<input type="hidden" v-model="frmCalendar.id" value="">
						<label for="inputName" class="col-sm-2 control-label"><?php _e( 'Name', 'appointfox' ); ?></label>
						<div class="col-sm-10">
							<input v-model="frmCalendar.name" key="edit-name" type="text" id="inputName" placeholder="<?php _e( 'Name', 'appointfox' ); ?>" class="form-control" value=""
							    required="required">
						</div>
					</div>
					<div class="form-group">
						<label for="inputName" class="col-sm-2 control-label"><?php _e( 'Name', 'appointfox' ); ?></label>
						<div class="col-sm-10">
							<?php foreach ($staffs as $staff) : ?>
							<div class="checkbox">
								<label>
									<input v-model="frmCalendar.staffs" id="checkbox_<?php echo $staff->id ?>" type="checkbox" value="<?php echo $staff->id ?>">
									<?php echo $staff->full_name ?>
								</label>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
								<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
							</button>
							<!-- <a href="javascript: window.history.go(-1)" class="btn btn-link">Cancel</a> -->
							<router-link class="btn btn-link" :to="{path: '/'}">
								<?php _e( 'Cancel', 'appointfox' ); ?>
							</router-link>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<template id="settings">
	<div>
		<div v-if="loading" class="loading-bar">
			<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
			<br> <?php _e( 'Loading', 'appointfox' ); ?>.....
		</div>
		<div v-if="frmCalendar.id" class="row">
			<div class="col-md-12 monthly-calendar-wrap">
				<h2><?php _e( 'Availability Calendar', 'appointfox' ); ?> - <?php _e( 'Settings', 'appointfox' ); ?></h2>
				<br>
				<br>
				<form id="FormCalendarEdit" v-on:submit.prevent="saveCalendar" class="form-horizontal" role="form">
					<div class="form-group">
						<input type="hidden" id="FrmCalendarEditId" v-model="frmCalendar.id" value="">
						<label for="inputName" class="col-sm-2 control-label"><?php _e( 'Calendar Name', 'appointfox' ); ?></label>
						<div class="col-sm-10">
							<p class="form-control-static">{{ frmCalendar.name }}</p>
						</div>
					</div>
					<div class="row">
						<h4><?php _e( 'Regular Weekly Hours', 'appointfox' ); ?></h4>
						<p>
							<?php _e( 'Use this schedule if you have similar hours week-to-week', 'appointfox' ); ?>.
							<br/> <?php _e( 'Enter window(s) of time (9:00am-1:00pm, 2:00pm-6:00pm) or exact start times: (9:00am, 10:30am, 2:00pm)', 'appointfox' ); ?> .</p>
					</div>
					<div class="row">
						<!-- <div style="overflow-x:auto;"> -->
						<table class="table table-bordered table-weeklyhours">
							<thead>
								<tr>
									<th>
										<?php _e( 'Sunday', 'appointfox' ); ?>
									</th>
									<th>
										<?php _e( 'Monday', 'appointfox' ); ?>
									</th>
									<th>
										<?php _e( 'Tuesday', 'appointfox' ); ?>
									</th>
									<th>
										<?php _e( 'Wednesday', 'appointfox' ); ?>
									</th>
									<th>
										<?php _e( 'Thursday', 'appointfox' ); ?>
									</th>
									<th>
										<?php _e( 'Friday', 'appointfox' ); ?>
									</th>
									<th>
										<?php _e( 'Saturday', 'appointfox' ); ?>
									</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tr>
								<td>
									<textarea ref="hour_sunday" v-show="!isShow_hour_sunday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_sunday"
									    rows="4" @focus="focusHour('hour_sunday')" @blur="blurHour('hour_sunday')"></textarea>
									<div v-html="frmCalendar.hour_sunday_text" @click="showInput('hour_sunday')" style="height: 74px; witdh: 100%" v-show="isShow_hour_sunday">
									</div>
									<p class="text-error" v-show="checkHour('hour_sunday')">
										<?php _e( 'Invalid character', 'appointfox' ); ?>
									</p>
								</td>
								<td>
									<textarea ref="hour_monday" v-show="!isShow_hour_monday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_monday"
									    rows="4" @focus="focusHour('hour_monday')" @blur="blurHour('hour_monday')"></textarea>
									<div v-html="frmCalendar.hour_monday_text" @click="showInput('hour_monday')" class="text-hour" v-show="isShow_hour_monday">
									</div>
								</td>
								<td>
									<textarea ref="hour_tuesday" v-show="!isShow_hour_tuesday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_tuesday"
									    rows="4" @focus="focusHour('hour_tuesday')" @blur="blurHour('hour_tuesday')"></textarea>
									<div v-html="frmCalendar.hour_tuesday_text" @click="showInput('hour_tuesday')" class="text-hour" v-show="isShow_hour_tuesday">
									</div>
								</td>
								<td>
									<textarea ref="hour_wednesday" v-show="!isShow_hour_wednesday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_wednesday"
									    rows="4" @focus="focusHour('hour_wednesday')" @blur="blurHour('hour_wednesday')"></textarea>
									<div v-html="frmCalendar.hour_wednesday_text" @click="showInput('hour_wednesday')" class="text-hour" v-show="isShow_hour_wednesday">
									</div>
								</td>
								<td>
									<textarea ref="hour_thursday" v-show="!isShow_hour_thursday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_thursday"
									    rows="4" @focus="focusHour('hour_thursday')" @blur="blurHour('hour_thursday')"></textarea>
									<div v-html="frmCalendar.hour_thursday_text" @click="showInput('hour_thursday')" class="text-hour" v-show="isShow_hour_thursday">
									</div>
								</td>
								<td>
									<textarea ref="hour_friday" v-show="!isShow_hour_friday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_friday"
									    rows="4" @focus="focusHour('hour_friday')" @blur="blurHour('hour_friday')"></textarea>
									<div v-html="frmCalendar.hour_friday_text" @click="showInput('hour_friday')" class="text-hour" v-show="isShow_hour_friday">
									</div>
								</td>
								<td>
									<textarea ref="hour_saturday" v-show="!isShow_hour_saturday" :placeholder="hourPlaceholder" v-model="frmCalendar.hour_saturday"
									    rows="4" @focus="focusHour('hour_saturday')" @blur="blurHour('hour_saturday')"></textarea>
									<div v-html="frmCalendar.hour_saturday_text" @click="showInput('hour_saturday')" class="text-hour" v-show="isShow_hour_saturday">
									</div>
								</td>
							</tr>
							</tbody>
						</table>
						<!-- </div> -->
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
								<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
							</button>
							<!-- <a href="javascript: window.history.go(-1)" class="btn btn-link">Cancel</a> -->
							<router-link class="btn btn-link" :to="{path: '/'}">
								<?php _e( 'Close', 'appointfox' ); ?>
							</router-link>
						</div>
					</div>
				</form>
				<div class="row">
					<h4>
						<?php _e( 'Availability for Specific Days', 'appointfox' ); ?>
					</h4>
					<p>
						<?php _e( 'This shows your monthly availability', 'appointfox' ); ?>. <?php _e( 'Select a specific date to set, change or override your hours', 'appointfox' ); ?>. <?php _e( 'Enter window(s) of time
						(9:00am-1:00pm, 2:00pm-5:00pm) or exact start times: (9:00am, 10:30am, 2:00pm)', 'appointfox' ); ?>.
					</p>
				</div>
				<div class="row">
					<div id="MonthlyCalendar">

					</div>
				</div>

				<div id="PopupHour" style="display: none">
					<div class="appointfox-tbs">
						<!-- <form action="" id="FormPopupHour" method="post"> -->
						<h4><?php _e( 'Hour for', 'appointfox' ); ?>
							<span id="PopupHourDay"></span>
						</h4>
						<input type="hidden" id="PopupHourDayData" name="PopupHourDayData" value="">
						<textarea name="PopupHourTime" class="form-control" id="PopupHourTime" rows="4" cols="4" placeholder="<?php _e( 'Closed - Enter availability like: 9.00am-12.30pm, 1.30pm-6pm', 'appointfox' ); ?>"></textarea>
						<span style="margin-top: 5px; display: block; font-weight: bold" id="PopupHourDayName"></span>
						<div style="margin-top: 10px">
							<!-- <button id="PopupHourBtnSet" class="btn btn-success btn-sm">Set Hours</button> -->
							<button id="PopupHourBtnSet" class="btn btn-success btn-sm ladda-button" data-style="expand-left">
								<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
							</button>
							<button id="PopupHourBtnCancel" class="btn btn-link btn-sm"><?php _e( 'Cancel', 'appointfox' ); ?></button>
						</div>
						<!-- </form> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</template>