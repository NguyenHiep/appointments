<?php

namespace AppointFox\Model;

/**
 * Model class - Service
 */
class Service {

	private static $table_services        = 'afx_services';
	private static $table_staffs          = 'afx_staffs';
	private static $table_staffs_services = 'afx_staffs_services';

	/**
	 * List all services
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_services        = $wpdb->prefix . self::$table_services;
		$table_staffs          = $wpdb->prefix . self::$table_staffs;
		$table_staffs_services = $wpdb->prefix . self::$table_staffs_services;

		$sql = "SELECT * FROM $table_services ORDER BY title ASC";

		$results = $wpdb->get_results( $sql );

		foreach ( $results as $key => $value ) {
			$service_id = $results[ $key ]->id;

			$sql = "SELECT a.staff_id FROM $table_staffs_services a 
					LEFT JOIN $table_staffs b 
					ON a.staff_id = b.id
					WHERE a.service_id = $service_id
					ORDER BY b.full_name ASC";

			$staff_ids = $wpdb->get_results( $sql );

			$temp_staffs_id = array();

			if ( ! empty( $staff_ids ) ) {
				foreach ( $staff_ids as $staff ) {
					$temp_staffs_id[] = $staff->staff_id;
				}
			}

			$results[ $key ]->staffs = $temp_staffs_id;
		}

		return $results;
	}

	/**
	 * List all services (Only public)
	 *
	 * @return void
	 */
	public static function findAllPublic() {
		global $wpdb;
		$table_services        = $wpdb->prefix . self::$table_services;
		$table_staffs          = $wpdb->prefix . self::$table_staffs;
		$table_staffs_services = $wpdb->prefix . self::$table_staffs_services;

		$sql = "SELECT * FROM $table_services 
                WHERE access = 'Public'
                ORDER BY title ASC";

		$results = $wpdb->get_results( $sql );

		// foreach ( $results as $key => $value ) {
		// $service_id = $results[ $key ]->id;
		// $sql = "SELECT a.staff_id FROM $table_staffs_services a
		// LEFT JOIN $table_staffs b
		// ON a.staff_id = b.id
		// WHERE a.service_id = $service_id
		// ORDER BY b.full_name ASC";
		// $staff_ids = $wpdb->get_results( $sql );
		// $temp_staffs_id = array();
		// if ( ! empty( $staff_ids ) ) {
		// foreach ( $staff_ids as $staff ) {
		// $temp_staffs_id[] = $staff->staff_id;
		// }
		// }
		// $results[ $key ]->staffs = $temp_staffs_id;
		// }
		return $results;
	}

	/**
	 * Find service by id
	 *
	 * @param int $id
	 * @return void
	 */
	public static function findById( $id ) {
		global $wpdb;
		$table_services = $wpdb->prefix . self::$table_services;

		$sql = "SELECT * FROM $table_services WHERE id = $id";

		$result = $wpdb->get_row( $sql );

		return $result;
	}

	/**
	 * Save assigned staffs by service id
	 *
	 * @param int $service_id
	 * @param int $staff_ids
	 * @return void
	 */
	public function saveAssignedStaffs( $service_id, $staff_ids ) {
		global $wpdb;
		$table_staffs_services = $wpdb->prefix . self::$table_staffs_services;

		// Delete existing records
		$wpdb->delete( $table_staffs_services, array( 'service_id' => $service_id ), array( '%d' ) );

		// Save assigned staffs
		foreach ( $staff_ids as $staff_id ) {
			$wpdb->insert(
				$table_staffs_services,
				array(
					'staff_id'   => $staff_id,
					'service_id' => $service_id,
				),
				array(
					'%d',
					'%d',
				)
			);
		}
	}
}
