<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Staff.php';
require_once AFX_PATH . 'models/Calendar.php';

use AppointFox\Model\Staff as Staff;
use AppointFox\Model\Calendar as Calendar;

/**
 * CalendarsController class
 */
class CalendarsController {

	/**
	 * __construct function
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-calendars-list', array(
				$this,
				'get_list',
			)
		);

		add_action(
			'wp_ajax_afx-calendars-save', array(
				$this,
				'save',
			)
		);

		add_action(
			'wp_ajax_afx-calendars-daysave', array(
				$this,
				'day_save',
			)
		);

		add_action(
			'wp_ajax_afx-calendars-get', array(
				$this,
				'get',
			)
		);

		add_action(
			'wp_ajax_afx-calendars-monthlycalendar', array(
				$this,
				'monthly_calendar',
			)
		);

		add_action(
			'wp_ajax_afx-calendars-delete', array(
				$this,
				'delete',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-calendars' ) {
			add_action(
				'admin_init', array(
					$this,
					'init_scripts',
				)
			);
		}
	}

	/**
	 * Add menu
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Availability Calendars', 'appointfox' ), __( 'Availability', 'appointfox' ), 'manage_appointfox_availability', 'appointfox-calendars', array(
				$this,
				'index',
			)
		);
	}

	/**
	 * Index page
	 *
	 * @return void
	 */
	public function index() {
		$staffs = Staff::findAll();

		include AFX_PATH . 'view/admin/Calendars/index.php';
	}

	/**
	 * Enqueue js and css files
	 *
	 * @return void
	 */
	public function init_scripts() {
		afx_init_scripts_for_admin();

		afx_init_scripts_for_vue();

		wp_enqueue_style( 'afx-popover-css', AFX_URL . 'assets/css/jquery.webui-popover.min.css' );
		wp_enqueue_style( 'afx-calendar', AFX_URL . 'assets/css/calendar.css' );

		wp_enqueue_script( 'afx-popover-js', AFX_URL . 'assets/js/jquery.webui-popover.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'afx-vue-router', AFX_URL . 'assets/js/vue-router.min.js' );
		wp_enqueue_script( 'afx-monthly-calendar', AFX_URL . 'assets/js/admin/monthly-calendar.js', array( 'jquery', 'afx-common-js', 'afx-vue', 'afx-vue-router', 'afx-axios', 'afx-promise', 'afx-promise-auto', 'afx-toastr-js', 'afx-popover-js' ), false, true );

		wp_enqueue_script( 'afx-js-page-calendars', AFX_URL . 'assets/js/admin/calendars.js', array( 'jquery', 'afx-common-js', 'afx-vue', 'afx-vue-router', 'afx-axios', 'afx-promise', 'afx-promise-auto', 'afx-toastr-js', 'afx-monthly-calendar' ), false, true );

		// Local JS
		wp_localize_script(
			'afx-js-page-calendars', 'afx_vars', array(
				'plugin_url'            => AFX_URL,
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'get_list_nonce'        => wp_create_nonce( 'get_list' ),
				'save_calendar_nonce'   => wp_create_nonce( 'save_calendar' ),
				'delete_calendar_nonce' => wp_create_nonce( 'delete_calendar' ),
				'get_calendar_nonce'    => wp_create_nonce( 'get_calendar' ),
			)
		);
	}

	/**
	 * List calendars
	 *
	 * @return void
	 */
	public function get_list() {

		$valid_req = check_ajax_referer( 'get_list', false, false );

		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_calendars';

		$sql       = "SELECT * FROM $table_name ORDER BY name ASC";
		$calendars = $wpdb->get_results( $sql );

		$data = array(
			'success'   => true,
			'calendars' => $calendars,
			'errors'    => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Validate inputs
	 */
	public function validate( & $data, $inputs ) {
		// check name is empty
		$field = 'name';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Calendar name is required',
				'field' => $field,
			);
		}
	}

	/**
	 * Save calendar
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_calendar', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_calendar nonce' );
		}

		// init inputs
		// $inputs = $_POST['data']['Calendar'];
		$inputs = json_decode( file_get_contents( 'php://input' ), true );

		$inputs['name'] = sanitize_text_field( $inputs['name'] );

		$inputs['staffs'] = array_map( 'sanitize_text_field', wp_unslash( $inputs['staffs'] ) );

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		// validate data
		$this->validate( $data, $inputs );

		// success
		if ( empty( $data['errors'] ) ) {
			// update record
			global $wpdb;
			$table_name = $wpdb->prefix . 'afx_calendars';

			if ( empty( $inputs['id'] ) ) {
				$result            = $wpdb->insert(
					$table_name, array(
						'name'           => $inputs['name'],
						'hour_sunday'    => 'Closed',
						'hour_monday'    => '9:00am-5:00pm',
						'hour_tuesday'   => '9:00am-5:00pm',
						'hour_wednesday' => '9:00am-5:00pm',
						'hour_thursday'  => '9:00am-5:00pm',
						'hour_friday'    => '9:00am-5:00pm',
						'hour_saturday'  => 'Closed',
					), array(
						'%s',
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
			} else {
				$result            = $wpdb->update(
					$table_name, array(
						'name'           => $inputs['name'],
						'hour_sunday'    => $inputs['hour_sunday'],
						'hour_monday'    => $inputs['hour_monday'],
						'hour_tuesday'   => $inputs['hour_tuesday'],
						'hour_wednesday' => $inputs['hour_wednesday'],
						'hour_thursday'  => $inputs['hour_thursday'],
						'hour_friday'    => $inputs['hour_friday'],
						'hour_saturday'  => $inputs['hour_saturday'],
					), array( 'ID' => $inputs['id'] ), array(
						'%s',
					), array( '%d' )
				);
				$data['insert_id'] = $inputs['id'];
			}

			// save calendars_staffs
			Calendar::saveStaffs( $data['insert_id'], $inputs['staffs'] );

			if ( $result === false ) {
				$data['success']  = false;
				$data['errors'][] = 'Error occured when trying to save to database';
			} else {
				$data['success'] = true;
			}
		}

		wp_send_json( $data );
	}

	/**
	 * Get calendar
	 *
	 * @return void
	 */
	public function get() {
		$valid_req = check_ajax_referer( 'get_calendar', false, false );

		if ( false == $valid_req ) {
			wp_die( 'Invalid get_calendar nonce' );
		}

		if ( ! isset( $_GET['id'] ) ) {
			wp_die( 'Invalid ID' );
		}

		$id = $_GET['id'];

		$calendar = array();

		$calendar = (array) Calendar::findById( $id );

		$staffs = Calendar::getStaffs( $id );

		if ( count( $staffs ) > 0 ) {
			foreach ( $staffs as $staff ) {
				$calendar['staffs'][] = $staff->id;
			}
		} else {
			$calendar['staffs'] = array();
		}

		$data = array(
			'success'  => true,
			'calendar' => $calendar,
			'errors'   => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Delete calendar
	 *
	 * @return void
	 */
	public function delete() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'delete_calendar', false, false );

		if ( false == $valid_req ) {
			wp_die( 'Invalid delete_calendar nonce' );
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_die( 'Invalid ID' );
		}

		$id = $_POST['id'];

		$data = array(
			'id'      => $id,
			'success' => false,
			'errors'  => array(),
		);

		$result = Calendar::delete( $id );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Display month calendar
	 *
	 * @return void
	 */
	public static function monthly_calendar() {
		// get the year and number of week from the query string and sanitize it
		$calendar_id = filter_input( INPUT_GET, 'calendar_id', FILTER_VALIDATE_INT );
		$year        = filter_input( INPUT_GET, 'year', FILTER_VALIDATE_INT );
		$month       = filter_input( INPUT_GET, 'month', FILTER_VALIDATE_INT );

		$calendarSetting = Calendar::findById( $calendar_id );
		$day_hours       = Calendar::getDayHours( $calendar_id, $year, $month );

		$formatedDayHours = array();

		// process
		foreach ( $day_hours as $item ) {
			$formatedDayHours[ $item->day ] = $item->hour;
		}

		// require_once AFX_PATH . 'view/admin/Calendars/monthly_calendar.php';
		require_once AFX_PATH . 'view/admin/Calendars/monthly_calendar2.php';

		die();
	}

	/**
	 * Ajax - save day
	 *
	 * @return void
	 */
	public static function day_save() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_calendar', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_calendar nonce' );
		}

		if ( ! isset( $_POST['calendar_id'] ) ) {
			wp_die( 'Invalid calendar_id' );
		}

		// init inputs
		$inputs['calendar_id'] = sanitize_text_field( $_POST['calendar_id'] );
		$inputs['day']         = sanitize_text_field( $_POST['day'] );
		$inputs['hour']        = sanitize_text_field( $_POST['hour'] );

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		// process hour
		// $hours = array_map('trim', explode(',', $inputs['hour']));
		$result = Calendar::saveDay( $inputs['calendar_id'], $inputs['day'], $inputs['hour'] );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to save to database';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}
}
