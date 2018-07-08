<?php

namespace AppointFox\Model;

/**
 * Model class - Staff
 */
class Staff {

	private static $table_staffs = 'afx_staffs';
	private static $table_services = 'afx_services';
	private static $table_staffs_services = 'afx_staffs_services';

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_staffs = $wpdb->prefix . self::$table_staffs;

		$sql = "SELECT * FROM $table_staffs ORDER BY full_name ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findById( $id ) {
		global $wpdb;
		$table_staffs = $wpdb->prefix . self::$table_staffs;

		// get staff
		$sql = $wpdb->prepare(
			"SELECT id, full_name, email, phone, info FROM $table_staffs  WHERE id = %d", array(
				$id,
			)
		);

		$result = $wpdb->get_row( $sql );
		return $result;
	}

	public static function getServices( $id ) {
		global $wpdb;
		// get staffs_services
		$table_services			= $wpdb->prefix . self::$table_services;
		$table_staffs_services	= $wpdb->prefix . self::$table_staffs_services;

		$sql = $wpdb->prepare(
			"SELECT b.id, b.title FROM $table_staffs_services a INNER JOIN $table_services b ON a.staff_id = %d AND a.service_id = b.id", array(
				$id,
			)
		);

		$result = $wpdb->get_results( $sql );
		return $result;
	}

	public static function delete( $id ) {
		global $wpdb;
		$table_staffs = $wpdb->prefix . self::$table_staffs;
		$result = $wpdb->delete( $table_staffs, array( 'ID' => $id ), array( '%d' ) );
		return $result;
	}

	public static function deleteServices( $staff_id ) {
		global $wpdb;
		$table_staffs_services	= $wpdb->prefix . self::$table_staffs_services;
		$result = $wpdb->delete( $table_staffs_services, array( 'staff_id' => $staff_id ), array( '%d' ) );
		return $result;
	}

	public static function deleteStaffs( $staff_ids ) {
		global $wpdb;
		$ids        = implode( ',', array_map( 'absint', $staff_ids ) );
		$table_staffs = $wpdb->prefix . self::$table_staffs;
		$result     = $wpdb->query( "DELETE FROM $table_staffs WHERE ID IN($ids)" );

		// delete staffs_services
		$table_staffs_services = $wpdb->prefix . self::$table_staffs_services;
		$result     = $wpdb->query( "DELETE FROM $table_staffs_services WHERE staff_id IN($ids)" );

		return $result;
	}

}
