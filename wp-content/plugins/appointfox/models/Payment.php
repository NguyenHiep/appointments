<?php

namespace AppointFox\Model;

/**
 * Model class - Payment
 */
class Payment {

	private static $table_payments = 'afx_payments';
	private static $table_appointments = 'afx_appointments';
	private static $table_customers = 'afx_customers';
	private static $table_services = 'afx_services';

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_payments = $wpdb->prefix . self::$table_payments;
		$table_appointments = $wpdb->prefix . self::$table_appointments;
		$table_services = $wpdb->prefix . self::$table_services;
		$table_customers = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT a.id, a.created, a.payment_amount, a.payment_type, a.payment_status, d.title as service_name,
					b.full_name as customer_name, c.start_datetime as appointment_datetime 
				FROM $table_payments a,
				$table_customers b,
				$table_appointments c,
				$table_services d
				WHERE a.appointment_id = c.id
				AND c.customer_id = b.id
				AND c.service_id = d.id
				ORDER BY created ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	/**
	 * Find payment by id
	 *
	 * @param [type] $id
	 * @return void
	 */
	public static function findById( $id ) {
		global $wpdb;
		$table_payments = $wpdb->prefix . self::$table_payments;
		$table_appointments = $wpdb->prefix . self::$table_appointments;
		$table_services = $wpdb->prefix . self::$table_services;
		$table_customers = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT a.id, a.created, a.payment_amount, a.payment_type, a.payment_status, a.txnid, 
					d.title as service_name, b.full_name as customer_name, b.email as customer_email,
					c.start_datetime as appointment_datetime 
				FROM $table_payments a,
				$table_customers b,
				$table_appointments c,
				$table_services d
				WHERE a.appointment_id = c.id
				AND c.customer_id = b.id
				AND c.service_id = d.id
				AND a.id = $id
				ORDER BY created ASC";

		$result = $wpdb->get_row( $sql );

		return $result;
	}

	/**
	 * Save payment
	 *
	 * @param [type] $payment
	 * @return void
	 */
	public static function save( $payment ) {
		global $wpdb;
		$table_payments = $wpdb->prefix . self::$table_payments;

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		if ( empty( $data['errors'] ) ) {
			if ( empty( $payment['id'] ) ) {
				$result            = $wpdb->insert(
					$table_payments, array(
						'txnid'          => $payment['txnid'],
						'payment_type'   => $payment['payment_type'],
						'payment_amount' => $payment['payment_amount'],
						'payment_status' => $payment['payment_status'],
						'appointment_id' => $payment['appointment_id'],
						'created'        => $payment['created'],
					), array(
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
						'%s',
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
				$data['mode']      = 'add';
			} else {
				$result            = $wpdb->update(
					$table_payments, array(
						'txnid'          => $payment['txnid'],
						'payment_type'   => $payment['payment_type'],
						'payment_amount' => $payment['payment_amount'],
						'payment_status' => $payment['payment_status'],
						'appointment_id' => $payment['appointment_id'],
						'created'        => $payment['created'],
					), array( 'ID' => $payment['id'] ), array(
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
						'%s',
					), array( '%d' )
				);
				$data['insert_id'] = $payment['id'];
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
}
