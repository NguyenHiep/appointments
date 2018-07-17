<?php

namespace AppointFox\Model;

require_once AFX_PATH . 'models/SentNotification.php';
require_once AFX_PATH . 'models/Payment.php';

use Moment\Moment;
use AppointFox\Model\SentNotification;
use AppointFox\Model\Payment;

/**
 * Model class - Notification
 */
class Notification {

	private static $table_notifications = 'afx_notifications';

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public static function findAll() {
		global $wpdb;
		$table_notifications = $wpdb->prefix . self::$table_notifications;

		$sql = "SELECT * FROM $table_notifications";

		$results = $wpdb->get_results( $sql );

		return $results;
	}

	/**
	 * Save notification
	 *
	 * @param [type] $settings
	 * @return void
	 */
	public static function save( $notification ) {
		global $wpdb;
		$table_notifications = $wpdb->prefix . self::$table_notifications;

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		$result = $wpdb->update(
			$table_notifications, array(
				'subject' => $notification['subject'],
				'message' => $notification['message'],
			), array( 'ID' => $notification['id'] ), array(
				'%s',
				'%s',
			), array( '%d' )
		);

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to save to database';
		} else {
			$data['success'] = true;
		}

		return $data;
	}

	/**
	 * Find by name
	 *
	 * @param [type] $name
	 * @return void
	 */
	public static function findByName( $name ) {
		global $wpdb;
		$table_notifications = $wpdb->prefix . self::$table_notifications;

		$sql = "SELECT * FROM $table_notifications WHERE name = '$name'";

		$result = $wpdb->get_row( $sql );

		return $result;
	}

	/**
	 * Send Initial Confirmation notification to customer
	 *
	 * @param [type] $appointment_id
	 * @return void
	 */
	public static function notifyInitialConfirmation( $appointment_id ) {
		$notification = self::findByName( 'Initial Confirmation' );

		$settings = Setting::findAll();

		$appointment = Appointment::findById( $appointment_id );

		// init replaces
		$m = new Moment( $appointment->start_datetime );

		$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'g:ia' );

		if ( $settings['time_format'] == '24 hour' ) {
			$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'H:i' );
		}

		$replace = array(
			'%business_name%' => $settings['business_name'],
			'%name%'          => $appointment->customer_name,
			'%service%'       => $appointment->service_title,
			'%datetime%'      => $datetime_str,
			'%phone%'         => $appointment->customer_phone,
			'%email%'         => $appointment->customer_email,
		);

		// send email
		//$to      = $appointment->customer_email;
        $to      = 'nguyenminhhiep9x@gmail.com';
		$subject = strReplaceAssoc( $replace, $notification->subject );
		$body    = strReplaceAssoc( $replace, $notification->message );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$result = wp_mail( $to, $subject, $body, $headers );

		return $result;
	}

	/**
	 * Send Cancellation notification to customer
	 *
	 * @param [type] $appointment_id
	 * @return void
	 */
	public static function notifyCancellation( $appointment_id ) {
		$notification = self::findByName( 'Cancellation' );

		$settings = Setting::findAll();

		$appointment = Appointment::findById( $appointment_id );

		// init replaces
		$m = new Moment( $appointment->start_datetime );

		$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'g:ia' );

		if ( $settings['time_format'] == '24 hour' ) {
			$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'H:i' );
		}

		$replace = array(
			'%business_name%' => $settings['business_name'],
			'%name%'          => $appointment->customer_name,
			'%service%'       => $appointment->service_title,
			'%datetime%'      => $datetime_str,
		);

		// send email
		$to      = $appointment->customer_email;
		$subject = strReplaceAssoc( $replace, $notification->subject );
		$body    = strReplaceAssoc( $replace, $notification->message );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$result = wp_mail( $to, $subject, $body, $headers );

		return $result;
	}

	public static function notifyReminders() {
		$notification = self::findByName( 'Reminder' );

		$settings = Setting::findAll();

		$appointments = Appointment::findAllPendingInHours( $settings['reminder_hours'] );

		// send reminders
		foreach ( $appointments as $appointment ) {
			// init replaces
			$m = new Moment( $appointment->start_datetime );

			$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'g:ia' );

			if ( $settings['time_format'] == '24 hour' ) {
				$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'H:i' );
			}

			$replace = array(
				'%business_name%' => $settings['business_name'],
				'%name%'          => $appointment->customer_name,
				'%service%'       => $appointment->service_title,
				'%datetime%'      => $datetime_str,
			);

			// send email
			$to      = $appointment->customer_email;
			$subject = strReplaceAssoc( $replace, $notification->subject );
			$body    = strReplaceAssoc( $replace, $notification->message );
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );

			$result = wp_mail( $to, $subject, $body, $headers );

			// log sent notification
			$sentNotification = array(
				'ref_id'  => $appointment->id,
				'type'    => 'email',
				'name'    => 'Reminder',
				'created' => date( 'Y-m-d H:i:s' ),
			);

			$log = SentNotification::add( $sentNotification );
		}

		return true;
	}

	/**
	 * Send notification on payment paid
	 *
	 * @param [type] $payment_id
	 * @return void
	 */
	public static function notifyPaymentPaid( $payment_id ) {
		$notification = self::findByName( 'Payment Paid' );

		$settings = Setting::findAll();

		$payment = Payment::findById( $payment_id );

		// init replaces
		$m = new Moment( $payment->appointment_datetime );

		$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'g:ia' );

		if ( $settings['time_format'] == '24 hour' ) {
			$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'H:i' );
		}

		$replace = array(
			'%business_name%'  => $settings['business_name'],
			'%name%'           => $payment->customer_name,
			'%service%'        => $payment->service_name,
			'%datetime%'       => $datetime_str,
			'%payment_method%' => $payment->payment_type,
			'%payment_amount%' => $settings['currency'] . $payment->payment_amount,
			'%payment_status%' => $payment->payment_status,
			'%payment_id%'     => $payment->txnid,
		);

		// send email
		$to      = $payment->customer_email;
		$subject = strReplaceAssoc( $replace, $notification->subject );
		$body    = strReplaceAssoc( $replace, $notification->message );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$result = wp_mail( $to, $subject, $body, $headers );

		return $result;
	}

	/**
	 * Send notification on pending payment
	 *
	 * @param [type] $appointment_id
	 * @return void
	 */
	public static function notifyPendingPayment( $appointment_id ) {
		$notification = self::findByName( 'Pending Payment' );

		$settings = Setting::findAll();

		$appointment = Appointment::findById( $appointment_id );

		// init replaces
		$m = new Moment( $appointment->start_datetime );

		$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'g:ia' );

		if ( $settings['time_format'] == '24 hour' ) {
			$datetime_str = $m->format( 'l, F j, Y ' ) . ' at ' . $m->format( 'H:i' );
		}

		$replace = array(
			'%business_name%' => $settings['business_name'],
			'%name%'          => $appointment->customer_name,
			'%service%'       => $appointment->service_title,
			'%datetime%'      => $datetime_str,
			'%price%'         => $settings['currency'] . $appointment->price,
			'%url%'           => wp_get_referer() . '?id=' . $appointment->unique_id,
		);

		// send email
		$to      = $appointment->customer_email;
		$subject = strReplaceAssoc( $replace, $notification->subject );
		$body    = strReplaceAssoc( $replace, $notification->message );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$result = wp_mail( $to, $subject, $body, $headers );

		return $result;
	}
}
