<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Service.php';
require_once AFX_PATH . 'models/Category.php';
require_once AFX_PATH . 'models/Appointment.php';
require_once AFX_PATH . 'models/Notification.php';

use AppointFox\Model\Service as Service;
use AppointFox\Model\Appointment as Appointment;
use AppointFox\Model\Category as Category;
use AppointFox\Model\Notification as Notification;

use DateTime;
use DatePeriod;
use DateInterval;

/**
 * AppointmentsController class
 */
class AppointmentsController {


	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-appointments-table', array(
				$this,
				'get_table_appointments',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-view', array(
				$this,
				'view',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-add', array(
				$this,
				'add',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-edit', array(
				$this,
				'edit',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-delete-process', array(
				$this,
				'delete_process',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-delete-process2', array(
				$this,
				'delete_process2',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-bulkdelete-process', array(
				$this,
				'bulkdelete_process',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-save', array(
				$this,
				'save',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-savenote', array(
				$this,
				'save_note',
			)
		);

		add_action(
			'wp_ajax_afx-appointments-markpaid', array(
				$this,
				'mark_paid',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-appointments' ) {
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
		// add_submenu_page('appointfox-appointmentcalendar', 'AppointFox' . ' Appointments', 'Appointments', 'manage_appointfox', 'appointfox-appointments', array(
		// $this,
		// 'index'
		// ));
		// add_submenu_page('appointfox-appointmentcalendar', 'AppointFox' . ' Appointments - Generate', 'Appointments - Generate', 'manage_appointfox', 'appointfox-appointments-generate', array(
		// $this,
		// 'generateData'
		// ));
	}

	/**
	 * Enqueue js and css files
	 *
	 * @return void
	 */
	public function init_scripts() {
		afx_init_scripts_for_admin();

		afx_init_scripts_for_datatables();

		wp_enqueue_script( 'afx-js-page-appointments', AFX_URL . 'assets/js/admin/appointments.js', array( 'jquery', 'afx-dt', 'afx-dt-bs', 'afx-common-js' ), false, true );

				// Translation array
        $translation_array = array(
            'success1' => __( 'Success', 'appointfox' ),
            'success2' => __( 'Record successfully saved', 'appointfox' ),
        );

		// Local JS
		wp_localize_script(
			'afx-js-page-appointments', 'afx_dt', array(
				'plugin_url'                   => AFX_URL,
				'ajax_url'                     => admin_url( 'admin-ajax.php' ),
				'get_table_appointments_nonce' => wp_create_nonce( 'get_table_appointments' ),
				'view_nonce'                   => wp_create_nonce( 'view' ),
				'edit_nonce'                   => wp_create_nonce( 'edit' ),
				'delete_nonce'                 => wp_create_nonce( 'delete' ),
				'labels'                       => $translation_array,
			)
		);
	}

	/**
	 * Index page
	 *
	 * @return void
	 */
	public function index() {
		$add_nonce        = wp_create_nonce( 'add' );
		$bulkdelete_nonce = wp_create_nonce( 'bulkdelete' );

		include AFX_PATH . 'view/admin/Appointments/index.php';
	}

	/**
	 * Datatable ajax data
	 *
	 * @return void
	 */
	public function get_table_appointments() {
		$valid_req = check_ajax_referer( 'get_table_appointments', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_table_appointments nonce' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';
		$sql        = 'SELECT * FROM ' . $table_name;

		$rows = $wpdb->get_results( $sql );

		$data = array();

		foreach ( $rows as $row ) {
			$data['data'][] = array(
				'id'        => $row->id,
				'full_name' => $row->full_name,
				'email'     => $row->email,
			);
		}

		if ( count( $data ) > 0 ) {
			wp_die( json_encode( $data ) );
		} else {
			$data['sEcho']                = 0;
			$data['iTotalRecords']        = 0;
			$data['iTotalDisplayRecords'] = 0;
			$data['aaData']               = array();
			wp_die( json_encode( $data ) );
		}
	}

	/**
	 * View page
	 *
	 * @return void
	 */
	public function view() {
		$valid_req = check_ajax_referer( 'view', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid view nonce' );
		}

		if ( ! isset( $_GET['id'] ) ) {
			wp_die( 'Invalid ID' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';

		$id = $_GET['id'];

		$sql = $wpdb->prepare(
			"SELECT id, full_name, email, phone, info FROM $table_name WHERE id = %d", array(
				$id,
			)
		);

		$appointment = $wpdb->get_row( $sql );

		require_once AFX_PATH . 'view/admin/Appointments/view.php';

		die();
	}

	/**
	 * Add page
	 *
	 * @return void
	 */
	public function add() {
		$valid_req = check_ajax_referer( 'add', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid add nonce' );
		}

		$save_nonce = wp_create_nonce( 'add_process' );

		require_once AFX_PATH . 'view/admin/Appointments/add.php';

		die();
	}

	/**
	 * Edit page
	 *
	 * @return void
	 */
	public function edit() {
		$valid_req = check_ajax_referer( 'edit', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid edit nonce' );
		}

		if ( ! isset( $_GET['id'] ) ) {
			wp_die( 'Invalid ID' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';

		$id = $_GET['id'];

		$sql = $wpdb->prepare(
			"SELECT id, full_name, email, phone, info FROM $table_name WHERE id = %d", array(
				$id,
			)
		);

		$appointment = $wpdb->get_row( $sql );

		$save_nonce = wp_create_nonce( 'edit_process' );

		require_once AFX_PATH . 'view/admin/Appointments/edit.php';

		die();
	}

	/**
	 * Ajax - process delete using Jquery Ajax
	 *
	 * @return void
	 */
	public function delete_process() {
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'delete', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid delete nonce' );
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_die( 'Invalid ID' );
		} else {
			$id = $_POST['id'];
		}

		$data = array(
			'id'      => $id,
			'success' => false,
			'errors'  => array(),
		);

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';
		$result     = $wpdb->delete( $table_name, array( 'ID' => $id ) );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_die( json_encode( $data ) );
	}

	/**
	 * Ajax - process delete using Axios
	 *
	 * @return void
	 */
	public function delete_process2() {
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'delete_appointment', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid delete_appointment nonce' );
		}

		$inputs = json_decode( file_get_contents( 'php://input' ), true );

		if ( ! isset( $inputs['id'] ) ) {
			wp_die( 'Invalid ID' );
		} else {
			$id = $inputs['id'];
		}

		$data = array(
			'id'      => $id,
			'success' => false,
			'errors'  => array(),
		);

		// Send notification to customer
		$sent = Notification::notifyCancellation( $id );

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';
		$result     = $wpdb->delete( $table_name, array( 'ID' => $id ) );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_die( json_encode( $data ) );
	}

	/**
	 * Ajax - process bulk delete
	 *
	 * @return void
	 */
	public function bulkdelete_process() {
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'bulkdelete', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid bulkdelete nonce' );
		}

		if ( ! isset( $_POST['ids'] ) ) {
			wp_die( 'Invalid IDs' );
		} else {
			$ids = $_POST['ids'];
		}

		$data = array(
			'ids'     => $ids,
			'success' => false,
			'errors'  => array(),
		);

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';
		$ids        = implode( ',', array_map( 'absint', $ids ) );
		$result     = $wpdb->query( "DELETE FROM $table_name WHERE ID IN($ids)" );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record(s)';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_appointment', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_appointment nonce' );
		}

		// init inputs
		// $inputs = $_POST['data']['Appointment'];
		$inputs = json_decode( file_get_contents( 'php://input' ), true );

		if ( ! empty( $inputs['id'] ) ) {
			$inputs['id'] = intval( $inputs['id'] );
		}

		$inputs['service_id'] = intval( $inputs['service_id'] );
		$inputs['staff_id']   = intval( $inputs['staff_id'] );
		$inputs['start_date'] = sanitize_text_field( $inputs['start_date'] );
		$inputs['start_time'] = sanitize_textarea_field( $inputs['start_time'] );
		$inputs['price']      = floatval( preg_replace( '/[^0-9.]/', '', $inputs['price'] ) );
		$inputs['is_paid']    = intval( $inputs['is_paid'] );

		// calculate times period
		$service = Service::findById( $inputs['service_id'] );

		// // set price
		// if (!isset($inputs['price'])) {
		// $inputs['price']  = $service->price;
		// }
		// // mark default as not paid yet
		// $inputs['is_paid']  = false;
		$duration         = $service->duration;
		$interval         = new DateInterval( 'PT' . $duration . 'S' );
		$start_datetime   = new DateTime( $inputs['start_date'] . ' ' . $inputs['start_time'] );
		$end_datetime_ori = new DateTime( $inputs['start_date'] . ' ' . $inputs['start_time'] );
		$end_datetime     = $end_datetime_ori->add( $interval );

		$inputs['customer_id'] = intval( $inputs['customer_id'] );
		$inputs['note']        = sanitize_textarea_field( $inputs['note'] );

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
			$table_name = $wpdb->prefix . 'afx_appointments';

			if ( empty( $inputs['id'] ) ) {
				$result            = $wpdb->insert(
					$table_name, array(
						'service_id'     => $inputs['service_id'],
						'staff_id'       => $inputs['staff_id'],
						'start_datetime' => $start_datetime->format( 'Y-m-d H:i:s' ),
						'end_datetime'   => $end_datetime->format( 'Y-m-d H:i:s' ),
						'customer_id'    => $inputs['customer_id'],
						'note'           => $inputs['note'],
						'price'          => $inputs['price'],
						'is_paid'        => $inputs['is_paid'],
					), array(
						'%d',
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
						'%d',
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
				$data['mode']      = 'add';
			} else {
				$result            = $wpdb->update(
					$table_name, array(
						'service_id'     => $inputs['service_id'],
						'staff_id'       => $inputs['staff_id'],
						'start_datetime' => $start_datetime->format( 'Y-m-d H:i:s' ),
						'end_datetime'   => $end_datetime->format( 'Y-m-d H:i:s' ),
						'customer_id'    => $inputs['customer_id'],
						'note'           => $inputs['note'],
						'price'          => $inputs['price'],
						'is_paid'        => $inputs['is_paid'],
					), array( 'ID' => $inputs['id'] ), array(
						'%d',
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
						'%d',
					), array( '%d' )
				);
				$data['insert_id'] = $inputs['id'];
				$data['mode']      = 'update';
			}

			if ( $result === false ) {
				$data['success']  = false;
				$data['errors'][] = 'Error occured when trying to save to database';
			} else {
				$appointment = Appointment::findById( $data['insert_id'] );

				$data['appointment']['id']             = strval( $data['insert_id'] );
				$data['appointment']['service_id']     = strval( $inputs['service_id'] );
				$data['appointment']['staff_id']       = strval( $inputs['staff_id'] );
				$data['appointment']['staff_name']     = $appointment->staff_name;
				$data['appointment']['start_datetime'] = $start_datetime->format( 'Y-m-d H:i:s' );
				$data['appointment']['end_datetime']   = $end_datetime->format( 'Y-m-d H:i:s' );
				$data['appointment']['customer_id']    = strval( $inputs['customer_id'] );
				$data['appointment']['note']           = $inputs['note'];
				$data['appointment']['price']          = $appointment->price;
				$data['appointment']['is_paid']        = $appointment->is_paid;
				$data['success']                       = true;
			}
		}

		wp_send_json( $data );
	}

	/**
	 * Ajax - save note
	 *
	 * @return void
	 */
	public function save_note() {
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

		if ( ! isset( $inputs['id'] ) ) {
			wp_die( 'Invalid ID' );
		} else {
			$inputs['id'] = intval( $inputs['id'] );
		}

		$inputs['note'] = sanitize_textarea_field( $inputs['note'] );

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		// update record
		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';

		$result = $wpdb->update(
			$table_name, array(
				'note' => $inputs['note'],
			), array( 'ID' => $inputs['id'] ), array(
				'%s',
			), array( '%d' )
		);

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to save to database';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	public function mark_paid() {
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

		if ( ! isset( $inputs['id'] ) ) {
			wp_die( 'Invalid ID' );
		} else {
			$inputs['id'] = intval( $inputs['id'] );
		}

		$data = array(
			'success' => false,
			'errors'  => array(),
		);

		// update record
		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';

		$result = $wpdb->update(
			$table_name, array(
				'is_paid' => true,
			), array( 'ID' => $inputs['id'] ), array(
				'%s',
			), array( '%d' )
		);

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to save to database';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Validate inputs
	 */
	public function validate( & $data, $inputs ) {
		// check service_id is empty
		$field = 'service_id';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Service is required',
				'field' => $field,
			);
		}

		// check staff_id is empty
		$field = 'staff_id';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Staff is required',
				'field' => $field,
			);
		}

		// check customer_id is empty
		$field = 'customer_id';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Customer is required',
				'field' => $field,
			);
		}

	}
}
