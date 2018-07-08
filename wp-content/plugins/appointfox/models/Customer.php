<?php

namespace AppointFox\Model;

/**
 * Model class - Customer
 */
class Customer {

	private static $table_customers = 'afx_customers';

	/**
	 * List customers
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_customers = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT * FROM $table_customers ORDER BY full_name ASC";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	public static function findByEmail( $email ) {
		global $wpdb;
		$table_customers = $wpdb->prefix . self::$table_customers;

		$sql = "SELECT * FROM $table_customers WHERE email = '$email'";

		$results = $wpdb->get_row( $sql );

		return $results;
	}

	public static function validate( & $data, $customer ) {
		// check full_name is empty
		$field = 'full_name';
		if ( empty( $customer[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Fullname is required',
				'field' => $field,
			);
		}

		// check email is empty
		$field = 'email';
		if ( empty( $customer[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Email is required',
				'field' => $field,
			);
		}

		// check email is valid
		if ( ! empty( $customer[ $field ] ) && ! filter_var( $customer[ $field ], FILTER_VALIDATE_EMAIL ) ) {
			$data['errors'][] = array(
				'msg'   => 'Email is not valid',
				'field' => $field,
			);
		}
	}

	public static function save( $customer ) {
		global $wpdb;
		$table_customers = $wpdb->prefix . self::$table_customers;

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		self::validate( $data, $customer );

		if ( empty( $data['errors'] ) ) {
			if ( empty( $customer['id'] ) ) {
				$result            = $wpdb->insert(
					$table_customers, array(
						'full_name' => $customer['full_name'],
						'email'     => $customer['email'],
						'phone'     => $customer['phone'],
						'info'     => $customer['info'],
					), array(
						'%s',
						'%s',
						'%s',
						'%s',
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
				$data['mode']      = 'add';
			} else {
				$result            = $wpdb->update(
					$table_customers, array(
						'full_name' => $customer['full_name'],
						'email'     => $customer['email'],
						'phone'     => $customer['phone'],
						'info'     => $customer['info'],
					), array( 'ID' => $customer['id'] ), array(
						'%s',
						'%s',
						'%s',
						'%s',
					), array( '%d' )
				);
				$data['insert_id'] = $customer['id'];
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

	public static function saveOrUpdate( $customer ) {
		global $wpdb;
		$table_customers = $wpdb->prefix . self::$table_customers;

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		if ( empty($customer['email'])) {
			$data['errors'][] = array(
				'msg'   => 'Email is required (2)',
				'field' => 'email',
			);
			return $data;
		}
		
		$exitingCustomer = self::findByEmail($customer['email']);

		if ( $exitingCustomer ) {
			$data['insert_id'] = (string) $exitingCustomer->id;
		} else {
			$data = self::save( $customer );
		}
		
		return $data;
	}
}
