<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Appointment.php';
require_once AFX_PATH . 'models/Calendar.php';
require_once AFX_PATH . 'models/Service.php';
require_once AFX_PATH . 'models/Staff.php';
require_once AFX_PATH . 'models/Setting.php';

use AppointFox\Model\Appointment as Appointment;
use AppointFox\Model\Calendar as Calendar;
use AppointFox\Model\Service as Service;
use AppointFox\Model\Staff as Staff;
use AppointFox\Model\Setting as Setting;

/**
 * AppointmentsCalendarController class
 */
class AppointmentsCalendarController {
	/**
	 * __construct function
	 */
	public function __construct() {

		// Setup menu.
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-calendar-selectslist', array(
				$this,
				'get_selects_list',
			)
		);

		add_action(
			'wp_ajax_afx-calendar-staffslist', array(
				$this,
				'get_staffs_list',
			)
		);

		add_action(
			'wp_ajax_afx-appointmentscalendar-fullcalendardata', array(
				$this,
				'get_fullcalendar_data',
			)
		);

		add_action(
			'wp_ajax_afx-calendar-disableddateslist', array(
				$this,
				'get_disableddates_list',
			)
		);

		add_action(
			'wp_ajax_afx-calendar-gettimes', array(
				$this,
				'get_times',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'appointfox-appointmentcalendar' ) {
			add_action(
				'admin_init', array(
					$this,
					'init_scripts',
				)
			);
		}
	}

	/**
	 * Add_menu function
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Appointments Calendar', 'appointfox' ), __( 'Appointments', 'appointfox' ), 'manage_appointfox_appointments', 'appointfox-appointmentcalendar', array(
				$this,
				'index',
			)
		);
	}

	/**
	 * Init_scripts function
	 *
	 * @return void
	 */
	public function init_scripts() {
		afx_init_scripts_for_admin();

		afx_init_scripts_for_vue();

		// Css
		// wp_enqueue_style('afx-tbs-css', AFX_URL . 'css/appointfox-tbs-fc-2.css');
		wp_enqueue_style( 'afx-fullcalendar-css', AFX_URL . 'assets/css/fullcalendar.min.css' );
		wp_enqueue_style( 'afx-bootstrap-datetimepicker-css', AFX_URL . 'assets/css/bootstrap-datetimepicker.min.css' );
		wp_enqueue_style( 'afx-page-calendars-css', AFX_URL . 'assets/css/admin/page-calendars.css' );

		// Js
		// wp_enqueue_script('afx-jquery-ui', AFX_URL . 'js/jquery-ui.custom.min.js', array('jquery'));
		wp_enqueue_script( 'afx-fullcalendar', AFX_URL . 'assets/js/fullcalendar.min.js', array( 'jquery', 'afx-moment-js' ) );
		wp_enqueue_script( 'afx-fullcalendar-lang', AFX_URL . 'assets/js/fullcalendar-locale-all.js', array( 'jquery', 'afx-moment-js', 'afx-fullcalendar' ) );
		// wp_enqueue_script('afx-fullcalendar-resource', 'https://rawgithub.com/azam/fullcalendar-resource/master/fullcalendar-resource.js', array('jquery'));
		wp_enqueue_script( 'afx-fullcalendar-resource', AFX_URL . 'assets/js/fullcalendar-resource.min.js', array( 'jquery' ) );
		// wp_enqueue_script( 'afx-bootstrap-datetimepicker', AFX_URL . 'assets/js/bootstrap-datetimepicker.min.js', array( 'jquery', 'afx-moment-js' ) );
		wp_enqueue_script( 'afx-bootstrap-datetimepicker', AFX_URL . 'assets/js/bootstrap-datetimepicker.min.js', array( 'jquery', 'afx-moment-js' ) );
		// wp_enqueue_script( 'afx-bootstrap-datetimepicker-vue', 'https://unpkg.com/vue-bootstrap-datetimepicker', array( 'jquery', 'afx-moment-js', 'afx-bootstrap-datetimepicker', 'afx-vue' ) );
		wp_enqueue_script( 'afx-bootstrap-datetimepicker-vue', AFX_URL . 'assets/js/vue-bootstrap-datetimepicker.min.js', array( 'jquery', 'afx-moment-js', 'afx-bootstrap-datetimepicker', 'afx-vue' ) );
		wp_enqueue_script( 'afx-vue-money', AFX_URL . 'assets/js/v-money.js' );
		wp_enqueue_script( 'afx-page-appointments-calendar-js', AFX_URL . 'assets/js/admin/appointments-calendar.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'afx-page-appointments-calendar-vue-js', AFX_URL . 'assets/js/admin/appointments-calendar-vue.js', array( 'jquery', 'afx-page-appointments-calendar-js', 'afx-vue', 'afx-promise', 'afx-promise-auto', 'afx-vue-money' ), false, true );

		$settings = Setting::findAll();

		// Translation array
		$translation_array = array(
			'choose_service_first'  => __( 'Choose service first', 'appointfox' ),
			'choose_time'           => __( 'Choose time', 'appointfox' ),
			'choose_staff'          => __( 'Choose staff', 'appointfox' ),
			'loading'               => __( 'Loading', 'appointfox' ),
			'appointment_saved'     => __( 'Appointment successfully saved', 'appointfox' ),
			'are_you_sure'          => __( 'Are you sure', 'appointfox' ),
			'you_wont_able_revert'  => __( 'You won\'t be able to revert this', 'appointfox' ),
			'customer'              => __( 'Customer', 'appointfox' ),
			'yes_cancel_it'         => __( 'Yes, cancel it', 'appointfox' ),
			'no_keep_it'            => __( 'No, keep it', 'appointfox' ),
			'cancelled'             => __( 'Cancelled', 'appointfox' ),
			'appointment_cancelled' => __( 'The appointment has been cancelled', 'appointfox' ),
			'customer_saved'        => __( 'Customer successfully saved', 'appointfox' ),
			'note_saved'            => __( 'Appointment\'s note successfully saved', 'appointfox' ),
			'appointment_paid'      => __( 'Appointment\'s payment paid', 'appointfox' ),
			'today'                 => __( 'Today', 'appointfox' ),
			'month'                 => __( 'Month', 'appointfox' ),
			'week'                  => __( 'Week', 'appointfox' ),
			'day'                   => __( 'Day', 'appointfox' ),

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
			'afx-page-appointments-calendar-js', 'afx_vars', array(
				'plugin_url'               => AFX_URL,
				'ajax_url'                 => admin_url( 'admin-ajax.php' ),
				'save_customer_nonce'      => wp_create_nonce( 'add_process' ),
				'save_appointment_nonce'   => wp_create_nonce( 'save_appointment' ),
				'delete_appointment_nonce' => wp_create_nonce( 'delete_appointment' ),
				'get_list_nonce'           => wp_create_nonce( 'get_list' ),
				'week_start_on'            => $settings['week_start_on'],
				'time_format'              => $settings['time_format'],
				'currency'                 => $settings['currency'],
				'payment_method'           => $settings['payment_method'],
				'labels'                   => $translation_array,
				'locale'                   => strtolower( $current_locale ),
			)
		);
	}

	/**
	 * Index page function
	 *
	 * @return void
	 */
	public function index() {
		include AFX_PATH . 'view/admin/AppointmentsCalendar/index.php';
	}

	/**
	 * Selections listing
	 *
	 * @return void
	 */
	public function get_selects_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		global $wpdb;
		$table_categories = $wpdb->prefix . 'afx_categories';
		$table_services   = $wpdb->prefix . 'afx_services';
		// $table_staffs = $wpdb->prefix . 'afx_staffs';
		// $table_staffs_services = $wpdb->prefix . 'afx_staffs_services';
		$table_customers = $wpdb->prefix . 'afx_customers';

		$sql        = "SELECT * FROM $table_categories ORDER BY name ASC";
		$categories = $wpdb->get_results( $sql );

		$sql      = "SELECT * FROM $table_services ORDER BY title ASC";
		$services = $wpdb->get_results( $sql );

		$sql       = "SELECT * FROM $table_customers ORDER BY full_name ASC";
		$customers = $wpdb->get_results( $sql );

		$data = array(
			'success'    => true,
			'categories' => $categories,
			'services'   => $services,
			// 'staffs' => $staffs,
			// 'staffs_services' => $staffs_services,
			'customers'  => $customers,
			'errors'     => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * List staffs
	 *
	 * @return void
	 */
	public function get_staffs_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		if ( ! isset( $_GET['service_id'] ) ) {
			wp_die( 'Invalid service_id' );
		}

		$service_id = $_GET['service_id'];

		global $wpdb;
		$table_staffs          = $wpdb->prefix . 'afx_staffs';
		$table_staffs_services = $wpdb->prefix . 'afx_staffs_services';

		$sql    = "SELECT DISTINCT a.id, a.full_name FROM $table_staffs a, $table_staffs_services b WHERE a.id = b.staff_id AND b.service_id = $service_id ORDER BY full_name ASC";
		$staffs = $wpdb->get_results( $sql );

		$data = array(
			'success' => true,
			'staffs'  => $staffs,
			'errors'  => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * List disabled dates
	 *
	 * @return void
	 */
	public function get_disableddates_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		if ( ! isset( $_GET['service_id'] ) ) {
			wp_die( 'Invalid service_id' );
		}

		$service_id = $_GET['service_id'];

		if ( ! isset( $_GET['staff_id'] ) ) {
			wp_die( 'Invalid staff_id' );
		}

		$staff_id = $_GET['staff_id'];

		$appointments = Appointment::findByServiceIdAndStaffId( $service_id, $staff_id );

		$disabled_dates = Calendar::findDisabledDatesByStaffId( $staff_id );

		$enabledDates = Calendar::findEnabledDatesByStaffId( $staff_id );

		$daysOfWeekDisabled = Calendar::findDaysOfWeekDisabledByStaffId( $staff_id );

		$data = array(
			'success'            => true,
			'appointments'       => $appointments,
			'disabled_dates'     => $disabled_dates,
			'daysOfWeekDisabled' => $daysOfWeekDisabled,
			'enabledDates'       => $enabledDates,
			'errors'             => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * List times
	 *
	 * @return void
	 */
	public function get_times() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		if ( ! isset( $_GET['service_id'] ) ) {
			wp_die( 'Invalid service_id' );
		}

		$service_id = $_GET['service_id'];

		if ( ! isset( $_GET['staff_id'] ) ) {
			wp_die( 'Invalid staff_id' );
		}

		$staff_id = $_GET['staff_id'];

		if ( ! isset( $_GET['start_date'] ) ) {
			wp_die( 'Invalid start_date' );
		}

		$start_date = $_GET['start_date'];

		if ( ! isset( $_GET['appointment_id'] ) ) {
			$appointment_id = $_GET['appointment_id'];
		}

		$appointment_id = $_GET['appointment_id'];

		$times = Calendar::findStaffAvailableHours( $service_id, $staff_id, $start_date, $appointment_id );

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
	 * Ajax data for full calendar
	 *
	 * @return void
	 */
	public function get_fullcalendar_data() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		$staffs       = Staff::findAll();
		$appointments = Appointment::findAll();

		$data = array(
			'success'      => true,
			'staffs'       => $staffs,
			'appointments' => $appointments,
			'errors'       => array(),
		);

		wp_send_json( $data );
	}
}
