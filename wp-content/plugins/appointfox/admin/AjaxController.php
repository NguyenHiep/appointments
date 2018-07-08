<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Service.php';
require_once AFX_PATH . 'models/Category.php';
require_once AFX_PATH . 'models/Appointment.php';
require_once AFX_PATH . 'models/Calendar.php';
require_once AFX_PATH . 'models/Customer.php';
require_once AFX_PATH . 'models/Notification.php';
require_once AFX_PATH . 'models/Setting.php';
require_once AFX_PATH . 'models/Payment.php';

use AppointFox\Model\Service;
use AppointFox\Model\Appointment;
use AppointFox\Model\Category;
use AppointFox\Model\Customer;
use AppointFox\Model\Calendar;
use AppointFox\Model\Notification;
use AppointFox\Model\Setting;
use AppointFox\Model\Payment;

use DateTime;
use DatePeriod;
use DateInterval;

/**
 * Ajax controller class
 */
class AjaxController {

	/**
	 * __construct function
	 */
	public function __construct() {
		// add_action(
		// 'wp_ajax_nopriv_afx-front-calendar', array(
		// $this,
		// 'monthly_calendar',
		// )
		// );
		// add_action(
		// 'wp_ajax_afx-front-calendar', array(
		// $this,
		// 'monthly_calendar',
		// )
		// );
		add_action(
			'wp_ajax_nopriv_afx-ajax-getformdata', array(
				$this,
				'get_formdata',
			)
		);

		add_action(
			'wp_ajax_afx-ajax-getformdata', array(
				$this,
				'get_formdata',
			)
		);

		add_action(
			'wp_ajax_nopriv_afx-ajax-disableddateslist', array(
				$this,
				'get_disableddates_list',
			)
		);

		add_action(
			'wp_ajax_afx-ajax-disableddateslist', array(
				$this,
				'get_disableddates_list',
			)
		);

		add_action(
			'wp_ajax_nopriv_afx-ajax-gettimes', array(
				$this,
				'get_times',
			)
		);

		add_action(
			'wp_ajax_afx-ajax-gettimes', array(
				$this,
				'get_times',
			)
		);

		add_action(
			'wp_ajax_nopriv_afx-ajax-saveappointment', array(
				$this,
				'save_appointment',
			)
		);

		add_action(
			'wp_ajax_afx-ajax-saveappointment', array(
				$this,
				'save_appointment',
			)
		);

		add_action(
			'wp_ajax_nopriv_afx-ajax-checkpayment', array(
				$this,
				'check_payment',
			)
		);

		add_action(
			'wp_ajax_afx-ajax-checkpayment', array(
				$this,
				'check_payment',
			)
		);
	}

	/**
	 * Display monthly calendar
	 *
	 * @return void
	 */
	// public function monthly_calendar() {
	// if ( $_SERVER['REQUEST_METHOD'] != 'GET' ) {
	// wp_die( 'Method not allowed' );
	// }
	// get_calendar_nonce created in frontend class
	// $valid_req = check_ajax_referer( 'get_calendar', false, false );
	// if ( false == $valid_req ) {
	// wp_die( 'Invalid get_calendar nonce' );
	// }
	// if ( ! isset( $_GET['year'] ) ) {
	// wp_die( 'Invalid Year' );
	// }
	// if ( ! isset( $_GET['month'] ) ) {
	// wp_die( 'Invalid Month' );
	// }
	// include AFX_PATH . 'view/frontend/calendars/calendar.php';
	// die();
	// }
	/**
	 * Ajax method: Formdata
	 *
	 * @return void
	 */
	public function get_formdata() {
		$valid_req = check_ajax_referer( 'get_formdata', false, false );

		if ( false == $valid_req ) {
			wp_die( 'Invalid get_formdata nonce' );
		}

		$services = Service::findAllPublic();
		// $services = Service::findAll();
		$categories = Category::findAll();

		$data = array(
			'success'    => true,
			'categories' => $categories,
			'services'   => $services,
			'errors'     => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Ajax method: List disable dates
	 *
	 * @return void
	 */
	public function get_disableddates_list() {
		$valid_req = check_ajax_referer( 'get_formdata', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_formdata nonce' );
		}

		if ( ! isset( $_GET['service_id'] ) ) {
			wp_die( 'Invalid service_id' );
		}

		$service_id = $_GET['service_id'];

		// $appointments = Appointment::findByServiceId( $service_id );
		$disabled_dates = Calendar::findAllSimilarDisabledDates( $service_id );

		$enabledDates = Calendar::findEnabledDatesByServiceId( $service_id );

		$daysOfWeekDisabled = Calendar::findDaysOfWeekDisabledByServiceId( $service_id );

		$data = array(
			'success'            => true,
			// 'appointments' 		=> $appointments,
			'disabled_dates'     => $disabled_dates,
			'daysOfWeekDisabled' => $daysOfWeekDisabled,
			'enabledDates'       => $enabledDates,
			'errors'             => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Ajax method: List times
	 *
	 * @return void
	 */
	public function get_times() {
		$valid_req = check_ajax_referer( 'get_formdata', false, false );

		if ( false == $valid_req ) {
			wp_die( 'Invalid get_formdata nonce' );
		}

		if ( ! isset( $_GET['service_id'] ) ) {
			wp_die( 'Invalid service_id' );
		}

		$service_id = $_GET['service_id'];

		if ( ! isset( $_GET['start_date'] ) ) {
			wp_die( 'Invalid start_date' );
		}

		$start_date = $_GET['start_date'];

		$times = Calendar::findStaffAvailableHoursByServiceId( $service_id, $start_date );

		// $times = [ '10:00am', '10:30am', '11:00am', '11:30am' ];
		$data = array(
			'success' => true,
			'times'   => $times,
			// 'work_hours' => $work_hours,
			// 'duration' => $duration,
			'errors'  => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Ajax method: Save appointment
	 *
	 * @return void
	 */
	public function save_appointment() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_appointment', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_appointment nonce' );
		}

		// init inputs
		$inputs = json_decode( file_get_contents( 'php://input' ), true );
		$inputs = $inputs['appointment'];

		$appointment['service_id'] = intval( $inputs['service']['id'] );
		// $inputs['staff_id']   = intval( $inputs['staff_id'] );
		$inputs['start_date'] = sanitize_text_field( $inputs['start_date'] );
		$inputs['start_time'] = sanitize_textarea_field( $inputs['start_time'] );

		// calculate times period
		$service                       = Service::findById( $appointment['service_id'] );
		$duration                      = $service->duration;
		$interval                      = new DateInterval( 'PT' . $duration . 'S' );
		$appointment['start_datetime'] = new DateTime( $inputs['start_date'] . ' ' . $inputs['start_time'] );
		$end_datetime_ori              = new DateTime( $inputs['start_date'] . ' ' . $inputs['start_time'] );
		$appointment['end_datetime']   = $end_datetime_ori->add( $interval );
		$appointment['note']           = '';
		$appointment['price']          = $service->price;
		$appointment['is_paid']        = 0;

		// auto assign staff id
		$times = Calendar::findStaffAvailable( $appointment['service_id'], $inputs['start_date'], $inputs['start_time'] );

		if ( $times ) {
			$appointment['staff_id'] = $times[0]['staff_id'];
		}

		$customer['full_name'] = sanitize_text_field( $inputs['customer_name'] );
		$customer['phone']     = sanitize_text_field( $inputs['customer_phone'] );
		$customer['email']     = sanitize_text_field( $inputs['customer_email'] );
		$customer['info']      = '';

		// create customer record
		$data = Customer::saveOrUpdate( $customer );

		// Get settings
		$settings = Setting::findAll();

		// save appointment
		if ( empty( $data['errors'] ) ) {

			$appointment['customer_id'] = $data['insert_id'];

			$data = Appointment::save( $appointment );

			if ( $data['success'] ) {
				// Notify customer on appointment confirmation
				$sent = Notification::notifyInitialConfirmation( $data['insert_id'] );

				if ( $settings['payment_method'] == 'PayPal' ) {
					// Notify customer on pending payment
					$sent = Notification::notifyPendingPayment( $data['insert_id'] );
				}
			}
		}

		wp_send_json( $data );
	}

	/**
	 * Ajax method: Check payment
	 *
	 * @return void
	 */
	public function check_payment() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'check_payment', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid check_payment nonce' );
		}

		// init response
		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		// init inputs
		$inputs = json_decode( file_get_contents( 'php://input' ), true );

		if ( ! isset( $inputs['paymentID'] ) ) {
			wp_die( 'Invalid paymentID' );
		}
		$paymentID = $inputs['paymentID'];

		if ( ! isset( $inputs['pid'] ) ) {
			wp_die( 'Invalid pid' );
		}
		$pid = $inputs['pid'];

		if ( ! isset( $inputs['payerID'] ) ) {
			wp_die( 'Invalid payerID' );
		}
		$payerID = $inputs['payerID'];

		if ( ! isset( $inputs['paymentToken'] ) ) {
			wp_die( 'Invalid paymentToken' );
		}
		$paymentToken = $inputs['paymentToken'];

		$settings = Setting::findAll();

		// Live
		$clientId = $settings['paypal_prod_clientid'];
		$secret   = $settings['paypal_prod_secret'];
		$url      = 'https://api.paypal.com/v1/';

		// Sandbox
		if ( $settings['is_paypal_sandbox'] ) {
			$clientId = $settings['paypal_sandbox_clientid'];
			$secret   = $settings['paypal_sandbox_secret'];
			$url      = 'https://api.sandbox.paypal.com/v1/';
		}

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $url . 'oauth2/token' );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_USERPWD, $clientId . ':' . $secret );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials' );
		$result      = curl_exec( $ch );
		$accessToken = null;

		if ( empty( $result ) ) {
			wp_die( 'Invalid access' );
		}

		$json        = json_decode( $result );
		$accessToken = $json->access_token;
		$curl        = curl_init( $url . 'payments/payment/' . $paymentID );
		curl_setopt( $curl, CURLOPT_POST, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt(
			$curl, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer ' . $accessToken,
				'Accept: application/json',
				'Content-Type: application/xml',
			)
		);
		$response = curl_exec( $curl );
		$result   = json_decode( $response );

		$state    = $result->state;
		$total    = $result->transactions[0]->amount->total;
		$currency = $result->transactions[0]->amount->currency;
		// $subtotal       = $result->transactions[0]->amount->details->subtotal;
		// $recipient_name = $result->transactions[0]->item_list->shipping_address->recipient_name;
		curl_close( $ch );
		curl_close( $curl );

		// Get appointment details
		$appointment = Appointment::findById( $pid );

		if ( $state == 'approved' && $appointment->price == $total ) {
			// Update appointment to paid
			$data = Appointment::markPaid( $pid );

			// Save payment
			$payment['txnid']          = $paymentID;
			$payment['payment_type']   = 'PayPal';
			$payment['payment_amount'] = $total;
			$payment['payment_status'] = 'Paid';
			$payment['appointment_id'] = $pid;
			$payment['created']        = date( 'Y-m-d H:i:s' );

			$data = Payment::save( $payment );

			// Notify customer payment paid
			Notification::notifyPaymentPaid( $data['insert_id'] );

		} else {
			$data['errors'][] = 'Error - payment info not matched';
		}

		wp_send_json( $data );
	}
}
