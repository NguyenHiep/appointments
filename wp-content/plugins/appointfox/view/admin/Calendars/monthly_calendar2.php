<?php 
$calendar = new CalendR\Calendar;
$calendar->setFirstWeekday(0);

$todayMonth = $calendar->getMonth(date('Y'), date('n'));
$currentMonth = $calendar->getMonth( $year, $month );
$today = $calendar->getDay(date('Y'), date('n'), date('j'));

// get the previous and next month for pagination
$prevMonth = $currentMonth->getPrevious();
$nextMonth = $currentMonth->getNext();

// generate the URLs for pagination
$prevMonthURL = sprintf( '?year=%s&month=%s', $prevMonth->format('Y'), $prevMonth->format('n') );
$nextMonthURL = sprintf( '?year=%s&month=%s', $nextMonth->format('Y'), $nextMonth->format('n') );

// set the active tab for the header
$activeTab = 'month';

?>

<div id="div-appointfox-calendar" class="appointfox-calendar-shortcode-wrap">
    <table class="appointfox-calendar">
        <thead>
            <tr>
                <th class="header" colspan="7">
                    <span class="arrow-left">
                        <a class="btn-arrow" data-year="<?php echo $prevMonth->format('Y'); ?>" data-month="<?php echo $prevMonth->format('n'); ?>"
                            href="#">
                            <i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i>
                        </a>
                    </span>
                    <span id="LoadingMonthlyCalendar" style="display: none">
                        <i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
                    </span>
                    <span class="nav-title">
                        <?php echo $currentMonth->format('F'); ?>
                        <?php echo $currentMonth->format('Y'); ?>

                        <?php if ( $todayMonth != $currentMonth ) : ?>
                        <span class="nav-back">
                            <a class="btn-arrow" data-year="<?php echo $todayMonth->format('Y'); ?>" data-month="<?php echo $todayMonth->format('n'); ?>"
                                href="#">
                                <?php _e( 'Back to', 'appointfox' ); ?>
                                <?php echo $todayMonth->format('F'); ?>
                            </a>
                        </span>
                        <?php endif; ?>
                    </span>

                    <span class="nav-loading" style="display: none">
                        <img style="width: 40px; height: 40px" src="<?php echo AFX_URL; ?>assets/images/Spinner.svg" />
                    </span>
                    <span class="arrow-right">
                        <a class="btn-arrow" data-year="<?php echo $nextMonth->format('Y'); ?>" data-month="<?php echo $nextMonth->format('n'); ?>"
                            href="#">
                            <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
                        </a>
                    </span>
                </th>
            </tr>
            <tr>
                <th><?php _e( 'Sunday', 'appointfox' ); ?></th>
                <th><?php _e( 'Monday', 'appointfox' ); ?></th>
                <th><?php _e( 'Tuesday', 'appointfox' ); ?></th>
                <th><?php _e( 'Wednesday', 'appointfox' ); ?></th>
                <th><?php _e( 'Thursday', 'appointfox' ); ?></th>
                <th><?php _e( 'Friday', 'appointfox' ); ?></th>
                <th><?php _e( 'Saturday', 'appointfox' ); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($currentMonth as $week): ?>
            <tr>
                <?php // Iterate over your month and get days ?>
                <?php foreach ($week as $day): ?>
                <?php //Check days that are out of your month ?>
                <?php 
                $active = true;

                if (!$currentMonth->includes($day)) {
                    $active = false;
                }

                if ($day < $today) {
                    $active = false;
                }

                $dayText = $day->format('Y') . '-' . $day->format('n') . '-' . $day->format('j');
				
                $dayHourText = '';
                
                $day_name = $day->format('l');

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
                <td data-hour="<?php echo $dayHourText ?>" data-day="<?php echo $dayText; ?>" <?php if ( ! $active ) {echo
                    ' class="inactive"';} ?>
                    <?php if ( $active ) { echo ' class="day-active"';} ?>>
                    <?php echo $day->format('j') ?>
                    <br />
                    <p class="hour">
                        <?php echo $dayHourText; ?>
                    </p>
                </td>
                <?php endforeach ?>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>