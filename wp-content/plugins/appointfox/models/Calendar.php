<?php

namespace AppointFox\Model;

use DateTime;
use DatePeriod;
use DateInterval;

/**
 * Model class - Calendar
 */
class Calendar {

	private static $table_staffs           = 'afx_staffs';
	private static $table_staffs_services  = 'afx_staffs_services';
	private static $table_calendars        = 'afx_calendars';
	private static $table_calendars_staffs = 'afx_calendars_staffs';
	private static $table_calendars_days   = 'afx_calendars_days';

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_calendars = $wpdb->prefix . self::$table_calendars;

		$sql = "SELECT * FROM $table_calendars ORDER BY name ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findById( $id ) {
		global $wpdb;
		$table_calendars = $wpdb->prefix . self::$table_calendars;

		$sql    = "SELECT * FROM $table_calendars WHERE id =" . $id;
		$result = $wpdb->get_row( $sql );
		return $result;
	}

	public static function findAllByIds( $ids ) {
		global $wpdb;
		$table_calendars = $wpdb->prefix . self::$table_calendars;

		$sql     = "SELECT * FROM $table_calendars WHERE id IN (" . implode( ',', $ids ) . ')';
		$results = $wpdb->get_results( $sql );
		return $results;
	}

	public static function findAllByServiceId( $service_id ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;

		$sql = "SELECT DISTINCT b.staff_id, a.* 
		FROM $table_calendars a 
		LEFT JOIN $table_calendars_staffs b ON b.calendar_id = a.id 
		LEFT JOIN $table_staffs_services c ON c.staff_id = b.staff_id
		WHERE c.service_id = $service_id";

		$results = $wpdb->get_results( $sql );
		return $results;
	}

	public static function findAllAvailableByServiceId( $service_id ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;

		$sql = "SELECT b.staff_id, a.* 
		FROM $table_calendars a 
		LEFT JOIN $table_calendars_staffs b ON b.calendar_id = a.id 
		LEFT JOIN $table_staffs_services c ON c.staff_id = b.staff_id
		WHERE c.service_id = $service_id";

		$results = $wpdb->get_results( $sql );
		return $results;
	}

	public static function findDisabledDatesByStaffId( $staff_id ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// find staff calendar_staffs by staff id
		$sql           = "SELECT * FROM $table_calendars_staffs WHERE staff_id =" . $staff_id;
		$calendarStaff = $wpdb->get_row( $sql );

		$calendar_id = 0;

		if ( $calendarStaff ) {
			$calendar_id = $calendarStaff->calendar_id;
		}

		// find disabled dates by calendar_id
		$sql          = "SELECT day FROM $table_calendars_days WHERE calendar_id = $calendar_id AND hour = 'Closed'";
		$calendarDays = $wpdb->get_results( $sql );

		$disabledDates = array();

		foreach ( $calendarDays as $item ) {
			$disabledDates[] = $item->day;
		}

		return $disabledDates;
	}


	public static function findAllSimilarDisabledDates( $service_id ) {
		global $wpdb;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// get total number of calendar
		$calendars = self::findAllByServiceId( $service_id );

		$term = 0;

		if ( $calendars ) {
			$term = count( $calendars );
		}

		$sql = "SELECT a.day FROM $table_calendars_days a
				LEFT JOIN $table_calendars_staffs b ON b.calendar_id = a.calendar_id
				LEFT JOIN $table_staffs_services c ON c.staff_id = b.staff_id
				WHERE c.service_id = $service_id
				AND a.hour = 'Closed'
				GROUP BY day
				HAVING count(*) = $term";

		$calendars_days = $wpdb->get_results( $sql );

		$disabledDates = array();

		if ( count( $calendars_days ) > 0 ) {
			foreach ( $calendars_days as $item ) {
				$disabledDates[] = $item->day;
			}
		}

		return $disabledDates;
	}


	public static function findEnabledDatesByStaffId( $staff_id ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// find staff calendar_staffs by staff id
		$sql           = "SELECT * FROM $table_calendars_staffs WHERE staff_id =" . $staff_id;
		$calendarStaff = $wpdb->get_row( $sql );

		$calendar_id = 0;

		if ( $calendarStaff ) {
			$calendar_id = $calendarStaff->calendar_id;
		}

		// find disabled dates by calendar_id
		$sql          = "SELECT day FROM $table_calendars_days WHERE calendar_id = $calendar_id AND hour != 'Closed'";
		$calendarDays = $wpdb->get_results( $sql );

		$enabledDates = array();

		foreach ( $calendarDays as $item ) {
			$enabledDates[] = $item->day;
		}

		return $enabledDates;
	}

	public static function findEnabledDatesByServiceId( $service_id ) {
		global $wpdb;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// // find staff ids by service_id
		// $sql           = "SELECT * FROM $table_staffs_services WHERE service_id =" . $service_id;
		// $staffs_services = $wpdb->get_results( $sql );
		// $staff_ids = array();
		// if ( count($staffs_services) > 0 ) {
		// foreach ($staffs_services as $row) {
		// $staff_ids[] = $row->staff_id;
		// }
		// }
		// // find staff calendar_staffs by staff ids
		// $sql           = "SELECT * FROM $table_calendars_staffs WHERE staff_id IN (".implode(',', $staff_ids).")";
		// $calendars_staffs = $wpdb->get_results( $sql );
		// $calendar_ids = array();
		// if ( count($calendars_staffs) > 0 ) {
		// foreach ($calendars_staffs as $row) {
		// $calendar_ids[] = $row->calendar_id;
		// }
		// }
		// find similar (duplicates) disabled dates by calendar_ids
		$sql = "SELECT a.day FROM $table_calendars_days a
				LEFT JOIN $table_calendars_staffs b ON b.calendar_id = a.calendar_id
				LEFT JOIN $table_staffs_services c ON c.staff_id = b.staff_id
				WHERE c.service_id = $service_id
				AND a.hour != 'Closed'
				GROUP BY day
				HAVING count(*) > 0";

		// $sql = "SELECT day FROM $table_calendars_days
		// WHERE calendar_id IN (".implode(',', $calendar_ids).")
		// AND hour != 'Closed'
		// GROUP BY day
		// HAVING count(*) > 1;";
		$calendars_days = $wpdb->get_results( $sql );

		$enabledDates = array();

		if ( count( $calendars_days ) > 0 ) {
			foreach ( $calendars_days as $item ) {
				$enabledDates[] = $item->day;
			}
		}
		return $enabledDates;
	}

	public static function findDaysOfWeekDisabledByStaffId( $staff_id ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;

		// find staff calendar_staffs by staff id
		$sql           = "SELECT * FROM $table_calendars_staffs WHERE staff_id =" . $staff_id;
		$calendarStaff = $wpdb->get_row( $sql );

		$calendar_id = 0;

		if ( $calendarStaff ) {
			$calendar_id = $calendarStaff->calendar_id;
		}

		// find calendar
		$sql      = "SELECT * FROM $table_calendars WHERE id = $calendar_id";
		$calendar = $wpdb->get_row( $sql );

		$daysOfWeekDisabled = array();

		if ( ! $calendar ) {
			return $daysOfWeekDisabled;
		}

		if ( $calendar->hour_sunday == 'Closed' ) {
			$daysOfWeekDisabled[] = 0;
		}

		if ( $calendar->hour_monday == 'Closed' ) {
			$daysOfWeekDisabled[] = 1;
		}

		if ( $calendar->hour_tuesday == 'Closed' ) {
			$daysOfWeekDisabled[] = 2;
		}
		if ( $calendar->hour_wednesday == 'Closed' ) {
			$daysOfWeekDisabled[] = 3;
		}
		if ( $calendar->hour_thursday == 'Closed' ) {
			$daysOfWeekDisabled[] = 4;
		}
		if ( $calendar->hour_friday == 'Closed' ) {
			$daysOfWeekDisabled[] = 5;
		}
		if ( $calendar->hour_saturday == 'Closed' ) {
			$daysOfWeekDisabled[] = 6;
		}

		return $daysOfWeekDisabled;
	}

	public static function findDaysOfWeekDisabledByServiceId( $service_id ) {
		global $wpdb;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;

		// // find staff ids by service_id
		// $sql           = "SELECT * FROM $table_staffs_services WHERE service_id =" . $service_id;
		// $staffs_services = $wpdb->get_results( $sql );
		// $staff_ids = array();
		// if ( count($staffs_services) > 0 ) {
		// foreach ($staffs_services as $row) {
		// $staff_ids[] = $row->staff_id;
		// }
		// }
		// // find staff calendar_staffs by staff ids
		// $sql           = "SELECT * FROM $table_calendars_staffs WHERE staff_id IN (".implode(',', $staff_ids).")";
		// $calendars_staffs = $wpdb->get_results( $sql );
		// $calendar_ids = array();
		// if ( count($calendars_staffs) > 0 ) {
		// foreach ($calendars_staffs as $row) {
		// $calendar_ids[] = $row->calendar_id;
		// }
		// }
		// // find calendar
		// $sql          = "SELECT * FROM $table_calendars WHERE id IN (".implode(',', $calendar_ids).")";
		// $calendars = $wpdb->get_results( $sql );
		$calendars = self::findAllByServiceId( $service_id );

		$daysOfWeekDisabled = array();

		if ( ! $calendars ) {
			return $daysOfWeekDisabled;
		}

		$default = array(
			'Sunday'    => 'Closed',
			'Monday'    => 'Closed',
			'Tuesday'   => 'Closed',
			'Wednesday' => 'Closed',
			'Thursday'  => 'Closed',
			'Friday'    => 'Closed',
			'Saturday'  => 'Closed',
		);

		foreach ( $calendars as $calendar ) {
			if ( $calendar->hour_sunday != 'Closed' ) {
				$default['Sunday'] = 'Open';
			}
			if ( $calendar->hour_monday != 'Closed' ) {
				$default['Monday'] = 'Open';
			}
			if ( $calendar->hour_tuesday != 'Closed' ) {
				$default['Tuesday'] = 'Open';
			}
			if ( $calendar->hour_wednesday != 'Closed' ) {
				$default['Wednesday'] = 'Open';
			}
			if ( $calendar->hour_thursday != 'Closed' ) {
				$default['Thursday'] = 'Open';
			}
			if ( $calendar->hour_friday != 'Closed' ) {
				$default['Friday'] = 'Open';
			}
			if ( $calendar->hour_saturday != 'Closed' ) {
				$default['Saturday'] = 'Open';
			}
		}

		if ( $default['Sunday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 0;
		}

		if ( $default['Monday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 1;
		}

		if ( $default['Tuesday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 2;
		}
		if ( $default['Wednesday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 3;
		}
		if ( $default['Thursday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 4;
		}
		if ( $default['Friday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 5;
		}
		if ( $default['Saturday'] == 'Closed' ) {
			$daysOfWeekDisabled[] = 6;
		}

		return $daysOfWeekDisabled;
	}

	public static function saveStaffs( $calendar_id, $staffs ) {
		global $wpdb;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$wpdb->delete( $table_calendars_staffs, array( 'calendar_id' => $calendar_id ), array( '%d' ) );

		foreach ( $staffs as $staff ) {
			$result_staffs = $wpdb->insert(
				$table_calendars_staffs, array(
					'calendar_id' => $calendar_id,
					'staff_id'    => $staff,
				), array(
					'%d',
					'%d',
				)
			);
		}

		return true;
	}

	public static function getStaffs( $calendar_id ) {
		global $wpdb;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_staffs           = $wpdb->prefix . self::$table_staffs;

		$sql = $wpdb->prepare(
			"SELECT b.id, b.full_name FROM $table_calendars_staffs a INNER JOIN $table_staffs b ON a.calendar_id = %d AND a.staff_id = b.id", array(
				$calendar_id,
			)
		);

		$result = $wpdb->get_results( $sql );
		return $result;
	}

	public static function findStaffsByServiceId( $service_id ) {
		global $wpdb;
		$table_staffs_services = $wpdb->prefix . self::$table_staffs_services;

		// find staff ids by service_id
		$sql             = "SELECT * FROM $table_staffs_services WHERE service_id =" . $service_id;
		$staffs_services = $wpdb->get_results( $sql );

		$staff_ids = array();

		if ( count( $staffs_services ) > 0 ) {
			foreach ( $staffs_services as $row ) {
				$staff_ids[] = $row->staff_id;
			}
		}

		return $staff_ids;
	}

	public static function delete( $id ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;

		$result = $wpdb->delete( $table_calendars, array( 'ID' => $id ) );
		$wpdb->delete( $table_calendars_staffs, array( 'calendar_id' => $id ), array( '%d' ) );

		return $result;
	}

	public static function saveDay( $calendar_id, $day, $hours ) {
		global $wpdb;
		$table_calendars_days = $wpdb->prefix . self::$table_calendars_days;
		$wpdb->delete(
			$table_calendars_days, array(
				'calendar_id' => $calendar_id,
				'day'         => $day,
			), array( '%d', '%s' )
		);

		// foreach ( $hours as $hour ) {
			$result = $wpdb->insert(
				$table_calendars_days, array(
					'calendar_id' => $calendar_id,
					'day'         => $day,
					'hour'        => $hours,
				), array(
					'%d',
					'%s',
					'%s',
				)
			);
		// }
		return true;
	}

	public static function getDayHours( $calendar_id, $year, $month ) {
		global $wpdb;
		$table_calendars_days = $wpdb->prefix . self::$table_calendars_days;

		$sql = $wpdb->prepare(
			"SELECT DATE_FORMAT(day,'%Y-%c-%e') as day, hour FROM $table_calendars_days WHERE calendar_id = %d AND MONTH(day) = %d AND YEAR(day) = %d", array(
				$calendar_id,
				$month,
				$year,
			)
		);

		$result = $wpdb->get_results( $sql );
		return $result;
	}

	public static function findStaffWorkHours( $staff_id, $day ) {
		global $wpdb;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// find staff calendar_staffs by staff id
		$sql           = $wpdb->prepare( "SELECT * FROM $table_calendars_staffs WHERE staff_id = %d", array( $staff_id ) );
		$calendarStaff = $wpdb->get_row( $sql );

		$calendar_id = 0;

		if ( $calendarStaff !== null ) {
			$calendar_id = $calendarStaff->calendar_id;
		}

		// default work hours
		$hours = array( '9:00am-6:00pm' );

		// regular work hours
		$m       = new \Moment\Moment( $day );
		$dayname = $m->format( 'l' );

		$regular_hour = array();

		if ( $calendar_id ) {
			$calendar     = (Array) Calendar::findById( $calendar_id );
			$regular_hour = $calendar[ 'hour_' . strtolower( $dayname ) ];
		}

		if ( ! empty( $regular_hour ) ) {
			$hours = array_map( 'trim', explode( ',', $regular_hour ) );
		}

		// specific work hours
		// find working hours by calendar_id and day
		$sql            = $wpdb->prepare( "SELECT hour FROM $table_calendars_days WHERE calendar_id = %d AND day = %s", array( $calendar_id, $day ) );
		$calendars_days = $wpdb->get_row( $sql );

		if ( $calendars_days ) {
			$hours = array_map( 'trim', explode( ',', $calendars_days->hour ) );
		}

		return $hours;
	}

	public static function findWorkHoursByStaffs( $staff_ids, $day ) {
		global $wpdb;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// find staff calendar_staffs by staff ids
		$sql = "SELECT calendar_id FROM $table_calendars_staffs 
				WHERE staff_id IN (" . implode( ',', $staff_ids ) . ')';

		$calendars_staffs = $wpdb->get_results( $sql );

		$calendar_ids = array();

		if ( count( $calendars_staffs ) > 0 ) {
			foreach ( $calendars_staffs as $row ) {
				$calendar_ids[] = $row->calendar_id;
			}
		}

		// default work hours
		$hours = array( '9:00am-6:00pm' );

		// regular work hours
		$m       = new \Moment\Moment( $day );
		$dayname = $m->format( 'l' );

		$regular_hour = array();

		$calendars = Calendar::findAllByIds( $calendar_ids );

		$calendarArray = array();

		foreach ( $calendars as $calendar ) {
			$calendarArray  = (Array) $calendar;
			$regular_hour[] = $calendarArray[ 'hour_' . strtolower( $dayname ) ];
		}

		$temp = '';

		$hours_temp = array();

		if ( ! empty( $regular_hour ) ) {
			foreach ( $regular_hour as $item ) {
				$temp .= $item . ',';
			}
			$hours_temp = array_map( 'trim', explode( ',', $temp ) );
			$hours      = array_filter( array_unique( array_map( 'trim', $hours_temp ) ) );
		}

		// // specific work hours
		// // find working hours by calendar_id and day
		$sql = "SELECT hour FROM $table_calendars_days 
				WHERE calendar_id IN (" . implode( ',', $calendar_ids ) . ") 
				AND day = $day";

		$calendars_days = $wpdb->get_results( $sql );

		$temp = '';

		if ( ! empty( $calendars_days ) ) {
			foreach ( $calendars_days as $item ) {
				$temp .= $item . ',';
			}
			$hours_temp = array_map( 'trim', explode( ',', $temp ) );
			$hours      = array_filter( array_unique( array_map( 'trim', $hours_temp ) ) );
		}

		return $hours;
	}

	public static function findWorkHoursByServiceId( $service_id, $day ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// default work hours
		// $hours = array( '9:00am-6:00pm' );
		$hours = array();

		// regular work hours
		$m       = new \Moment\Moment( $day );
		$dayname = $m->format( 'l' );

		$regular_hour = array();

		$calendars = Calendar::findAllByServiceId( $service_id );

		$calendarArray = array();

		foreach ( $calendars as $calendar ) {
			$calendarArray  = (Array) $calendar;
			$regular_hour[] = $calendarArray[ 'hour_' . strtolower( $dayname ) ];
		}

		$temp = '';

		$hours_temp = array();

		if ( ! empty( $regular_hour ) ) {
			foreach ( $regular_hour as $item ) {
				$temp .= $item . ',';
			}
			$allHours = array_map( 'trim', explode( ',', $temp ) );
			// $hours = array_filter(array_unique(array_map('trim', $allHours)));
			$hours = array_filter( $allHours );
		}

		// specific work hours
		// find working hours by service_id
		$sql = "SELECT a.hour, c.staff_id 
				FROM $table_calendars_days a 
				LEFT JOIN $table_calendars_staffs b ON b.calendar_id = a.calendar_id 
				LEFT JOIN $table_staffs_services c ON c.staff_id = b.staff_id 
				WHERE c.service_id = $service_id AND a.day = \"$day\"";

		$calendars_days = $wpdb->get_results( $sql );

		$temp = '';

		if ( ! empty( $calendars_days ) ) {
			foreach ( $calendars_days as $item ) {
				$temp .= $item->hour . ',';
			}
			$allHours = array_map( 'trim', explode( ',', $temp ) );
			// $hours = array_filter(array_unique(array_map('trim', $allHours)));
			$hours = array_filter( $allHours );
		}

		return $hours;
	}

	public static function findWorkHoursByServiceId2( $service_id, $day ) {
		global $wpdb;
		$table_calendars        = $wpdb->prefix . self::$table_calendars;
		$table_calendars_staffs = $wpdb->prefix . self::$table_calendars_staffs;
		$table_staffs_services  = $wpdb->prefix . self::$table_staffs_services;
		$table_calendars_days   = $wpdb->prefix . self::$table_calendars_days;

		// default work hours
		$hours = array( '9:00am-6:00pm' );

		// regular work hours
		$m       = new \Moment\Moment( $day );
		$dayname = $m->format( 'l' );

		$regular_hour = array();

		$calendars = Calendar::findAllAvailableByServiceId( $service_id );

		$calendarArray = array();

		$count = 0;

		if ( $calendars ) {
			foreach ( $calendars as $calendar ) {
				$calendarArray = (Array) $calendar;
				$hour          = $calendarArray[ 'hour_' . strtolower( $dayname ) ];
				if ( $hour != 'Closed' ) {
					$regular_hour[ $count ]['hour']     = $hour;
					$regular_hour[ $count ]['staff_id'] = $calendar->staff_id;
					$count++;
				}
			}
		}

		// replace with specific hour
		// // find working hours by service_id
		$sql = "SELECT a.hour, c.staff_id 
				FROM $table_calendars_days a 
				LEFT JOIN $table_calendars_staffs b ON b.calendar_id = a.calendar_id 
				LEFT JOIN $table_staffs_services c ON c.staff_id = b.staff_id 
				WHERE c.service_id = $service_id AND a.day = \"$day\"";

		$specific_hour = $wpdb->get_results( $sql, ARRAY_A );

		$temp = '';

		if ( $specific_hour ) {
			foreach ( $regular_hour as $key => $value ) {
				foreach ( $specific_hour as $key2 => $value2 ) {
					if ( $specific_hour[ $key2 ]['staff_id'] == $regular_hour[ $key ]['staff_id'] ) {
						unset( $regular_hour[ $key ] );
					}
				}
			}
		}

		$hours_merge = array();
		$hours_merge = array_merge( $regular_hour, $specific_hour );

		if ( $hours_merge ) {
			$hours = $hours_merge;
		}

		if ( ! empty( $hours ) ) {
			$new_hours = array();
			$count     = 0;

			foreach ( $hours as $key => $value ) {
				$new_arr = array_map( 'trim', explode( ',', $hours[ $key ]['hour'] ) );

				foreach ( $new_arr as $hour ) {
					$new_hours[ $count ]['staff_id'] = $hours[ $key ]['staff_id'];
					$new_hours[ $count ]['hour']     = $hour;
					$count ++;
				}
			}
		}

		return $new_hours;
	}

	public static function findStaffAvailableHours( $service_id, $staff_id, $day, $appointment_id = null ) {
		// get work hours
		$work_hours = Calendar::findStaffWorkHours( $staff_id, $day );

		// calculate times period
		$service  = Service::findById( $service_id );
		$duration = $service->duration;

		$times    = array();
		$interval = new DateInterval( 'PT' . $duration . 'S' );

		foreach ( $work_hours as $work_hour ) {
			$hours = array_map( 'trim', explode( '-', $work_hour ) );
			if ( count( $hours ) == 2 ) {
				// range Ex. (9:00am-6:00pm)
				$begin     = new DateTime( $day . ' ' . $hours[0] );
				$end       = new DateTime( $day . ' ' . $hours[1] );
				$daterange = new DatePeriod( $begin, $interval, $end );

				foreach ( $daterange as $date ) {
					$times[] = $date->format( 'g:ia' );
				}
			} else {
				// specific time Ex. (8:00pm)
				if ( $hours[0] != 'Closed' ) {
					$begin   = new DateTime( $day . ' ' . $hours[0] );
					$times[] = $begin->format( 'g:ia' );
				}
			}
		}

		// Check against booked appointment
		$bookedAppointments = Appointment::findBookedAppointments( $service_id, $staff_id, $day );

		foreach ( $times as $key => $value ) {
			foreach ( $bookedAppointments as $appointment ) {
				$appointment_hour = strtolower( $appointment->hour );

				if ( $value == $appointment_hour ) {
					if ( isset( $appointment_id ) ) {
						if ( $appointment->id != $appointment_id ) {
							unset( $times[ $key ] );
						}
					} else {
						unset( $times[ $key ] );
					}
				}
			}
		}

		return $times;
	}

	public static function findStaffAvailableHoursByServiceId( $service_id, $day ) {

		// get work hours
		$work_hours = Calendar::findWorkHoursByServiceId( $service_id, $day );

		// write_log( 'work_hours' );
		// write_log( $work_hours );

		// calculate times period
		$service  = Service::findById( $service_id );
		$duration = $service->duration;

		$times      = array();
		$times_temp = array();
		$interval   = new DateInterval( 'PT' . $duration . 'S' );

		foreach ( $work_hours as $work_hour ) {
			$hours = array_map( 'trim', explode( '-', $work_hour ) );
			if ( count( $hours ) == 2 ) {
				// range Ex. (9:00am-6:00pm)
				$begin     = new DateTime( $day . ' ' . $hours[0] );
				$end       = new DateTime( $day . ' ' . $hours[1] );
				$daterange = new DatePeriod( $begin, $interval, $end );

				foreach ( $daterange as $date ) {
					// $times[] = $date->format( 'g:ia' );
					$times_temp[] = $date->format( 'H:i' );
				}
			} else {
				// specific time Ex. (8:00pm)
				if ( $hours[0] != 'Closed' ) {
					$begin = new DateTime( $day . ' ' . $hours[0] );
					// $times[] = $begin->format( 'g:ia' );
					$times_temp[] = $begin->format( 'H:i' );
				}
			}
		}

		sort( $times_temp );

		foreach ( $times_temp as $time ) {
			$dt_temp = new DateTime( $day . ' ' . $time );
			$times[] = $dt_temp->format( 'g:ia' );
		}

		// write_log( 'times' );
		// write_log( $times );

		// Get existing booked appointments
		$bookedAppointments = Appointment::findBookedAppointmentsByServiceId( $service_id, $day );

		// write_log( 'bookedAppointments' );
		// write_log( $bookedAppointments );

		// Check against booked appointment
		foreach ( $times as $key1 => $time ) {
			foreach ( $bookedAppointments as $key2 => $appointment ) {
				$appointment_hour = trim( strtolower( $appointment['hour'] ) );
                
				if ( $time === $appointment_hour ) {
                    // write_log( '$bookedAppointments['.$key2.']'.' - $times['.$key1.']'.' time: ' . $time . ' : ' . $appointment_hour.' <== matched' );
					unset( $times[ $key1 ] );
                    unset( $bookedAppointments[ $key2 ] );
                    break;
				} else {
                    // write_log( 'time: ' . $time . ' : ' . $appointment_hour );
                }
			}
		}

		$times = array_filter( array_unique( array_map( 'trim', $times ) ) );

		// write_log( 'final times' );
		// write_log( $times );

		return $times;
	}

	public static function findStaffAvailable( $service_id, $day, $time ) {

		// get work hours
		$work_hours = Calendar::findWorkHoursByServiceId2( $service_id, $day );

		// // calculate times period
		$service  = Service::findById( $service_id );
		$duration = $service->duration;

		$times      = array();
		$times_temp = array();
		$interval   = new DateInterval( 'PT' . $duration . 'S' );

		$count = 0;

		foreach ( $work_hours as $work_hour ) {
			$hours = array_map( 'trim', explode( '-', $work_hour['hour'] ) );
			if ( count( $hours ) == 2 ) {
				// range Ex. (9:00am-6:00pm)
				$begin     = new DateTime( $day . ' ' . $hours[0] );
				$end       = new DateTime( $day . ' ' . $hours[1] );
				$daterange = new DatePeriod( $begin, $interval, $end );

				foreach ( $daterange as $date ) {
					// $times[] = $date->format( 'g:ia' );
					$times_temp[ $count ]['staff_id'] = $work_hour['staff_id'];
					$times_temp[ $count ]['hour']     = $date->format( 'H:i' );
					$count++;
				}
			} else {
				// specific time Ex. (8:00pm)
				if ( $hours[0] != 'Closed' ) {
					$begin = new DateTime( $day . ' ' . $hours[0] );
					// $times[] = $begin->format( 'g:ia' );
					$times_temp[ $count ]['staff_id'] = $work_hour['staff_id'];
					$times_temp[ $count ]['hour']     = $begin->format( 'H:i' );
					$count++;
				}
			}
		}

		// Obtain a list of columns
		foreach ( $times_temp as $key => $row ) {
			$staff_id_array[ $key ] = $row['staff_id'];
			$hour_array[ $key ]     = $row['hour'];
		}

		// sort by hour
		array_multisort( $hour_array, SORT_ASC, $staff_id_array, SORT_ASC, $times_temp );

		$count = 0;

		foreach ( $times_temp as $key => $value ) {
			$dt_temp                     = new DateTime( $day . ' ' . $times_temp[ $key ]['hour'] );
			$times[ $count ]['staff_id'] = $times_temp[ $key ]['staff_id'];
			$times[ $count ]['hour']     = $dt_temp->format( 'g:ia' );
			$count++;
		}

		// Check against booked appointment
		$bookedAppointments = Appointment::findBookedAppointmentsByServiceId( $service_id, $day );

		foreach ( $times as $key => $value ) {
			foreach ( $bookedAppointments as $key => $appointment ) {
				$appointment_hour = strtolower( $appointment['hour'] );

				if ( isset( $times[ $key ]['hour'] ) ) {
					if ( $times[ $key ]['hour'] == $appointment_hour ) {
						unset( $times[ $key ] );
						unset( $bookedAppointments [ $key ] );
					}
				}
			}
		}

		$times_final = array();

		// Only list selected time
		$count = 0;
		foreach ( $times as $key => $value ) {
			if ( $times[ $key ]['hour'] == $time ) {
				$times_final[ $count ]['hour']     = $times[ $key ]['hour'];
				$times_final[ $count ]['staff_id'] = $times[ $key ]['staff_id'];
				$count++;
			}
		}

		// $times = array_filter(array_unique(array_map('trim', $times)));
		return $times_final;
	}
}
