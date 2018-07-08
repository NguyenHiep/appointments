<?php

namespace AppointFox\Model;

/**
 * Model class - Appointment
 */
class Appointment {

	private static $table_appointments       = 'afx_appointments';
	private static $table_staffs             = 'afx_staffs';
	private static $table_services           = 'afx_services';
	private static $table_customers          = 'afx_customers';
	private static $table_sent_notifications = 'afx_sent_notifications';

	public static function findById( $id ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;
		$table_staffs       = $wpdb->prefix . self::$table_staffs;
		$table_services     = $wpdb->prefix . self::$table_services;
		$table_customers    = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT  a.id, a.start_datetime, 
					a.end_datetime, a.staff_id, a.note, b.full_name as staff_name,
					a.service_id, c.title as service_title, c.duration, a.price, 
					a.status, c.color, a.customer_id, d.full_name as customer_name,
					d.phone as customer_phone, d.email as customer_email,
					a.is_paid, a.unique_id
					FROM $table_appointments a,
					$table_staffs b,
					$table_services c,
					$table_customers d
					WHERE a.service_id = c.id
					AND a.staff_id = b.id
					AND a.customer_id = d.id
					AND a.id = $id";

		$results = $wpdb->get_row( $sql );

		return $results;
	}

	public static function findByUniqId( $unique_id ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;
		$table_staffs       = $wpdb->prefix . self::$table_staffs;
		$table_services     = $wpdb->prefix . self::$table_services;
		$table_customers    = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT  a.id, a.start_datetime, 
					a.end_datetime, a.staff_id, a.note, b.full_name as staff_name,
					a.service_id, c.title as service_title, c.duration, a.price, 
					a.status, c.color, a.customer_id, d.full_name as customer_name,
					d.phone as customer_phone, d.email as customer_email,
					a.is_paid, a.unique_id
					FROM $table_appointments a,
					$table_staffs b,
					$table_services c,
					$table_customers d
					WHERE a.service_id = c.id
					AND a.staff_id = b.id
					AND a.customer_id = d.id
					AND a.unique_id = '$unique_id'";

		$results = $wpdb->get_row( $sql );

		return $results;
	}

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;
		$table_staffs       = $wpdb->prefix . self::$table_staffs;
		$table_services     = $wpdb->prefix . self::$table_services;
		$table_customers    = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT a.id, a.start_datetime, 
					a.end_datetime, a.staff_id, a.note, b.full_name as staff_name,
					a.service_id, c.title as service_title, c.duration, c.price, 
					c.color, a.customer_id, d.full_name as customer_name,
					d.phone as customer_phone, d.email as customer_email,
					a.is_paid
					FROM $table_appointments a,
					$table_staffs b,
					$table_services c,
					$table_customers d
					WHERE a.service_id = c.id
					AND a.staff_id = b.id
					AND a.customer_id = d.id
					ORDER BY a.start_datetime ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findByServiceIdAndStaffId( $service_id, $staff_id ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;

		$sql = "SELECT * FROM $table_appointments WHERE staff_id = $staff_id AND service_id = $service_id ORDER BY start_datetime ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findByServiceId( $service_id ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;

		$sql = "SELECT * FROM $table_appointments WHERE service_id = $service_id ORDER BY start_datetime ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findBookedAppointments( $service_id, $staff_id, $day ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;

		$sql = "SELECT DATE_FORMAT(start_datetime, '%l:%i%p') as hour FROM $table_appointments WHERE staff_id = $staff_id 
						AND service_id = $service_id 
						AND DATE_FORMAT(start_datetime, '%Y-%c-%e') = \"$day\" 
						ORDER BY start_datetime ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findBookedAppointmentsByServiceId( $service_id, $day ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;

		// $sql = "SELECT DATE_FORMAT(start_datetime, '%l:%i%p') as hour, staff_id FROM $table_appointments 
		// 				WHERE service_id = $service_id 
		// 				AND DATE_FORMAT(start_datetime, '%Y-%c-%e') = \"$day\" 
		// 				ORDER BY start_datetime ASC";

		$sql = "SELECT DATE_FORMAT(start_datetime, '%l:%i%p') as hour, staff_id FROM $table_appointments 
					WHERE service_id = $service_id 
					AND DATE(start_datetime) = \"$day\" 
					ORDER BY start_datetime ASC";				

		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results;
	}

	public static function validate( & $data, $appointment ) {
		// check service_id is empty
		$field = 'service_id';
		if ( empty( $appointment[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Service is required',
				'field' => $field,
			);
		}

		// check staff_id is empty
		$field = 'staff_id';
		if ( empty( $appointment[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Staff is required',
				'field' => $field,
			);
		}

		// check customer_id is empty
		$field = 'customer_id';
		if ( empty( $appointment[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Customer is required',
				'field' => $field,
			);
		}

		// check customer_id is empty
		$field = 'start_datetime';
		if ( empty( $appointment[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Start datetime is required',
				'field' => $field,
			);
		}
		// check customer_id is empty
		$field = 'end_datetime';
		if ( empty( $appointment[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'End datetime is required',
				'field' => $field,
			);
		}
	}

	public static function save( $appointment ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		self::validate( $data, $appointment );

		if ( empty( $data['errors'] ) ) {
			if ( empty( $appointment['id'] ) ) {
				$appointment['unique_id'] = uniqid();

				$result = $wpdb->insert(
					$table_appointments, array(
						'service_id'     => $appointment['service_id'],
						'staff_id'       => $appointment['staff_id'],
						'start_datetime' => $appointment['start_datetime']->format( 'Y-m-d H:i:s' ),
						'end_datetime'   => $appointment['end_datetime']->format( 'Y-m-d H:i:s' ),
						'customer_id'    => $appointment['customer_id'],
						'note'           => $appointment['note'],
						'price'          => $appointment['price'],
						'is_paid'        => $appointment['is_paid'],
						'unique_id'      => $appointment['unique_id'],
					), array(
						'%d',
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
					)
				);

				$data['insert_id'] = $wpdb->insert_id;
				$data['mode']      = 'add';
			} else {
				$result = $wpdb->update(
					$table_name, array(
						'service_id'     => $appointment['service_id'],
						'staff_id'       => $appointment['staff_id'],
						'start_datetime' => $start_datetime->format( 'Y-m-d H:i:s' ),
						'end_datetime'   => $end_datetime->format( 'Y-m-d H:i:s' ),
						'customer_id'    => $appointment['customer_id'],
						'note'           => $appointment['note'],
						'price'          => $appointment['price'],
						'is_paid'        => $appointment['is_paid'],
					), array( 'ID' => $appointment['id'] ), array(
						'%d',
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
						'%d',
					), array( '%d' )
				);

				$data['insert_id'] = $appointment['id'];
				$data['mode']      = 'update';
			}

			if ( $result === false ) {
				$data['success']  = false;
				$data['errors'][] = 'Error occured when trying to save to database';
			} else {
				$data['success'] = true;
			}
		}

		return $data;
	}

	/**
	 * Find all pending appointment in selected hours. Ex 24 hours
	 *
	 * @param [type] $hours
	 * @return void
	 */
	public static function findAllPendingInHours( $hours ) {
		global $wpdb;
		$table_appointments       = $wpdb->prefix . self::$table_appointments;
		$table_staffs             = $wpdb->prefix . self::$table_staffs;
		$table_services           = $wpdb->prefix . self::$table_services;
		$table_customers          = $wpdb->prefix . self::$table_customers;
		$table_sent_notifications = $wpdb->prefix . self::$table_sent_notifications;

		$sql = "SELECT a.id, a.start_datetime, 
					a.end_datetime, a.staff_id, a.note, b.full_name as staff_name,
					a.service_id, c.title as service_title, c.duration, a.price, 
					c.color, a.customer_id, d.full_name as customer_name,
					d.phone as customer_phone, d.email as customer_email,
					a.is_paid
					FROM $table_appointments a
					LEFT JOIN $table_staffs b ON a.staff_id = b.id
					LEFT JOIN $table_services c ON a.service_id = c.id
					LEFT JOIN $table_customers d ON a.customer_id = d.id
					LEFT JOIN $table_sent_notifications e ON a.id = e.ref_id
					WHERE 
					a.start_datetime <= DATE_ADD(NOW(), INTERVAL $hours HOUR)
					AND a.start_datetime >= NOW()
					AND e.ref_id is null
					ORDER BY a.start_datetime ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	/**
	 * Mark payment paid
	 *
	 * @param [type] $id
	 * @return void
	 */
	public static function markPaid( $id ) {
		global $wpdb;
		$table_appointments = $wpdb->prefix . self::$table_appointments;

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		$result = $wpdb->update(
			$table_appointments, array(
				'is_paid' => true,
			), array( 'ID' => $id ), array(
				'%s',
			), array( '%d' )
		);

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to update payment in database';
		} else {
			$data['success'] = true;
		}

		return $data;
	}
}
