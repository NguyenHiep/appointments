<?php
namespace AppointFox\Frontend\Controller;

require_once AFX_PATH . 'models/Setting.php';

use AppointFox\Model\Setting as Setting;
use AppointFox\Model\Appointment;

/**
 * AppointmentsController class
 */
class AppointmentsController {

	var $shortcode_appointment_form = 'appointfox-appointment';

	/**
	 * __construct function
	 */
	public function __construct() {

		// enable shortcode
		add_shortcode(
			$this->shortcode_appointment_form, array(
				$this,
				'display_appointment_form',
			)
		);

		add_action(
			'wp_enqueue_scripts', array(
				$this,
				'init_scripts',
			)
		);
	}

	/**
	 * Enqueue js and css files
	 *
	 * @return void
	 */
	public function init_scripts() {

		afx_init_scripts_for_vue();

		// Css
		wp_enqueue_style( 'afx-bootstrap-datetimepicker-css', AFX_URL . 'assets/css/bootstrap-datetimepicker.min.css' );

		// Js
		wp_enqueue_script( 'afx-bootstrap-datetimepicker', AFX_URL . 'assets/js/bootstrap-datetimepicker.min.js', array( 'jquery', 'afx-moment-js' ) );
		// wp_enqueue_script( 'afx-bootstrap-datetimepicker-vue', 'https://unpkg.com/vue-bootstrap-datetimepicker', array( 'jquery', 'afx-moment-js', 'afx-bootstrap-datetimepicker', 'afx-vue' ) );
		wp_enqueue_script( 'afx-bootstrap-datetimepicker-vue', AFX_URL . 'assets/js/vue-bootstrap-datetimepicker.min.js', array( 'jquery', 'afx-moment-js', 'afx-bootstrap-datetimepicker', 'afx-vue' ) );
		wp_enqueue_script( 'afx-front-paypal-js', 'https://www.paypalobjects.com/api/checkout.js', array( 'jquery' ) );

		if ( isset( $_GET['id'] ) ) {
			// booking details page
			wp_enqueue_script( 'afx-front-appointment-js', AFX_URL . 'assets/js/frontend/appointment-details.js', array( 'jquery', 'afx-front-paypal-js' ) );
			wp_enqueue_script( 'afx-front-appointment-js-vue', AFX_URL . 'assets/js/frontend/appointment-details-vue.js', array( 'jquery', 'afx-vue', 'afx-promise', 'afx-promise-auto', 'afx-axios', 'afx-sweetalert2-js', 'afx-front-appointment-js', 'afx-front-paypal-js' ), false, true );
		} else {
			// booking form page
			wp_enqueue_script( 'afx-front-appointment-js', AFX_URL . 'assets/js/frontend/appointment.js', array( 'jquery', 'afx-front-paypal-js' ) );
			wp_enqueue_script( 'afx-front-appointment-js-vue', AFX_URL . 'assets/js/frontend/appointment-vue.js', array( 'jquery', 'afx-vue', 'afx-promise', 'afx-promise-auto', 'afx-axios', 'afx-sweetalert2-js', 'afx-front-appointment-js', 'afx-front-paypal-js' ), false, true );
		}

		$settings = Setting::findAll();

		if ( $settings['is_paypal_sandbox'] ) {
			$paypal_env = 'sandbox';
		} else {
			$paypal_env = 'production';
		}

		// Translation array
		$translation_array = array(
			'choose_time'      => __( 'Choose time', 'appointfox' ),
			'loading'          => __( 'Loading', 'appointfox' ),
			'not_available'    => __( 'Not available, please choose other date', 'appointfox' ),
			'choose_your_date' => __( 'Please choose your date', 'appointfox' ),
			'choose_your_time' => __( 'Please choose your time', 'appointfox' ),
			'enter_your_name'  => __( 'Please enter your name', 'appointfox' ),
			'enter_your_email' => __( 'Please enter your email', 'appointfox' ),
			'payment_paid'     => __( 'Payment Paid', 'appointfox' ),
			'thank_you'        => __( 'Thank you', 'appointfox' ),
		);

		// Processing Locale
		$current_locale_temp = get_locale();

		$findme = '_';
		$pos    = strpos( $current_locale_temp, $findme );

		if ( $pos === false ) {
			$current_locale = $current_locale_temp;
		} else {
			$temp           = explode( '_', $current_locale_temp );
			$current_locale = $temp[0];
		}

		// Local JS
		wp_localize_script(
			'afx-front-appointment-js', 'afx_vars', array(
				'plugin_url'              => AFX_URL,
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'get_formdata_nonce'      => wp_create_nonce( 'get_formdata' ),
				'save_appointment_nonce'  => wp_create_nonce( 'save_appointment' ),
				'check_payment_nonce'     => wp_create_nonce( 'check_payment' ),
				'week_start_on'           => $settings['week_start_on'],
				'time_format'             => $settings['time_format'],
				'currency'                => $settings['currency'],
				'payment_method'          => $settings['payment_method'],
				'paypal_prod_clientid'    => $settings['paypal_prod_clientid'],
				'paypal_sandbox_clientid' => $settings['paypal_sandbox_clientid'],
				'paypal_env'              => $paypal_env,
				'background_color'        => $settings['background_color'],
				'font_color'              => $settings['font_color'],
				'font_size'               => $settings['font_size'],
				'labels'                  => $translation_array,
				'locale'                  => strtolower( $current_locale ),
			)
		);
	}

	/**
	 * Display appointment form via short code ($shortcode_appointment_form)
	 *
	 * @param [type] $atts
	 * @param [type] $content
	 * @return void
	 */
	public function display_appointment_form( $atts, $content ) {
		$settings = Setting::findAll();

		$page = 'booking.php';

		if ( isset( $_GET['id'] ) ) {
			$id = $_GET['id'];

			if ( empty( $id ) ) {
				return( 'Invalid ID' );
			}

			$page = 'booking_details.php';

			$appointment = Appointment::findByUniqId( $id );

			if ( empty( $appointment ) ) {
				return( 'Invalid Appointment' );
			}
		}

		ob_start();
		include AFX_PATH . 'view/frontend/Appointments/' . $page;
		$content = ob_get_clean();
		return $content;
	}
}
