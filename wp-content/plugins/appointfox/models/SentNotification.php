<?php

namespace AppointFox\Model;

/**
 * Model class - SentNotification
 */
class SentNotification {

	private static $table_sent_notifications = 'afx_sent_notifications';

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_sent_notifications = $wpdb->prefix . self::$table_sent_notifications;

		$sql        = "SELECT * FROM $table_sent_notifications";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function add( $sentNotification ) {
		global $wpdb;
		$table_sent_notifications = $wpdb->prefix . self::$table_sent_notifications;

		$result = $wpdb->insert(
			$table_sent_notifications, array(
				'ref_id' => $sentNotification['ref_id'],
				'type'     => $sentNotification['type'],
				'name'     => $sentNotification['name'],
				'created'     => $sentNotification['created'],
			), array(
				'%d',
				'%s',
				'%s',
				'%s',
			)
		);

		return $result;
	}
}
