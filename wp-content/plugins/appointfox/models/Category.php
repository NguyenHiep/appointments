<?php

namespace AppointFox\Model;

/**
 * Model class - Category
 */
class Category {

	private static $table_categories = 'afx_categories';

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_categories = $wpdb->prefix . self::$table_categories;

		$sql = "SELECT * FROM $table_categories ORDER BY name ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}
}
