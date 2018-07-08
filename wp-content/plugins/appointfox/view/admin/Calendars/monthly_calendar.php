<?php
// require AFX_PATH . 'lib/calendar/calendar.php';

date_default_timezone_set( 'UTC' );
setlocale( LC_ALL, 'en_US' );

// get the year and number of week from the query string and sanitize it
// $year  = filter_input( INPUT_GET, 'year', FILTER_VALIDATE_INT );
// $month = filter_input( INPUT_GET, 'month', FILTER_VALIDATE_INT );

// initialize the calendar object
$calendar = new Calendar();

// get the current month object by year and number of month
$todayMonth   = $calendar->month( null, null );
$currentMonth = $calendar->month( $year, $month );

// get the previous and next month for pagination
$prevMonth = $currentMonth->prev();
$nextMonth = $currentMonth->next();

// generate the URLs for pagination
$prevMonthURL = sprintf( '?year=%s&month=%s', $prevMonth->year()->int(), $prevMonth->int() );
$nextMonthURL = sprintf( '?year=%s&month=%s', $nextMonth->year()->int(), $nextMonth->int() );

// set the active tab for the header
$activeTab = 'month';
?>
	<div id="div-appointfox-calendar" class="appointfox-calendar-shortcode-wrap">
		<table class="appointfox-calendar">
			<thead>
				<tr>
					<th class="header" colspan="7">
						<span class="arrow-left">
							<a class="btn-arrow" data-year="<?php echo $prevMonth->year()->int(); ?>" data-month="<?php echo $prevMonth->month()->int(); ?>"
								href="#">
								<i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i>
							</a>
						</span>
						<span id="LoadingMonthlyCalendar" style="display: none">
							<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
						</span>
						<span class="nav-title">
							<?php echo $currentMonth->name(); ?>
							<?php echo $currentMonth->year()->int(); ?>

							<?php if ( $todayMonth != $currentMonth ) : ?>
							<span class="nav-back">
								<a class="btn-arrow" data-year="<?php echo $todayMonth->year()->int(); ?>" data-month="<?php echo $todayMonth->month()->int(); ?>"
									href="#">
									Back to
									<?php echo $todayMonth->name(); ?>
								</a>
							</span>
							<?php endif; ?>
						</span>

						<span class="nav-loading" style="display: none">
							<img style="width: 40px; height: 40px" src="<?php echo AFX_URL; ?>assets/images/Spinner.svg" />
						</span>
						<span class="arrow-right">
							<a class="btn-arrow" data-year="<?php echo $nextMonth->year()->int(); ?>" data-month="<?php echo $nextMonth->month()->int(); ?>"
								href="#">
								<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
							</a>
						</span>
					</th>
				</tr>
				<tr>
					<?php foreach ( $currentMonth->weeks()->first()->days() as $weekDay ) : ?>
					<th>
						<?php echo $weekDay->shortname(); ?>
					</th>
					<?php endforeach ?>
				</tr>
			</thead>

			<?php foreach ( $currentMonth->weeks( 6 ) as $week ) : ?>
			<tr>
				<?php foreach ( $week->days() as $day ) : ?>
				<?php
				$active = true;

				if ( $day->month() != $currentMonth ) {
					$active = false;
				}

				if ( $day->isInThePast() ) {
					$active = false;
				}

				$dayText = $day->month() . '-' . $day->int();
				
				$dayHourText = '';

				// check weekly availability
				$day_name = $day->name();

				switch ($day_name) {
					case 'Sunday':
						$dayHourText = $calendarSetting->hour_sunday;
						break;
					case 'Monday':
						$dayHourText = $calendarSetting->hour_monday;
						break;
					case 'Tuesday':
						$dayHourText = $calendarSetting->hour_tuesday;
						break;
					case 'Wednesday':
						$dayHourText = $calendarSetting->hour_wednesday;
						break;
					case 'Thursday':
						$dayHourText = $calendarSetting->hour_thursday;
						break;
					case 'Friday':
						$dayHourText = $calendarSetting->hour_friday;
						break;
					case 'Saturday':
						$dayHourText = $calendarSetting->hour_saturday;
						break;
					default:
				}

				// check specific availability
				if (isset($formatedDayHours[$dayText])) {
					$dayHourText = $formatedDayHours[$dayText];
				}
				?>
					<td data-hour="<?php echo $dayHourText ?>" data-day="<?php echo $dayText; ?>" <?php if ( ! $active ) {echo ' class="inactive"';} ?>
						<?php if ( $active ) { echo ' class="day-active"';} ?> >
						<?php echo ( $day->isToday() ) ? '<strong>' . $day->int() . '</strong>' : $day->int(); ?>
						<br />
						<p class="hour">
							<?php echo $dayHourText; ?>
							<!-- <?php echo $day->name() ?> -->
						</p>
					</td>
					<?php endforeach ?>
			</tr>
			<?php endforeach ?>
		</table>
	</div>

