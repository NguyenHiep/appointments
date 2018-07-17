<?php

namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Staff.php';
require_once AFX_PATH . 'models/Service.php';
require_once AFX_PATH . 'models/Category.php';

use AppointFox\Model\Staff as Staff;
use AppointFox\Model\Category as Category;
use AppointFox\Model\Service as Service;

/**
 * StaffsController class
 */
class StaffsController {

	/**
	 * __construct function
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-staffs-table', array(
				$this,
				'get_table_staffs',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-view', array(
				$this,
				'view',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-add', array(
				$this,
				'add',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-edit', array(
				$this,
				'edit',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-delete-process', array(
				$this,
				'delete_process',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-bulkdelete-process', array(
				$this,
				'bulkdelete_process',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-save', array(
				$this,
				'save',
			)
		);

		add_action(
			'wp_ajax_afx-staffs-list', array(
				$this,
				'get_list',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-staffs' ) {
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
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Staffs', 'appointfox' ), __( 'Staffs', 'appointfox' ), 'manage_appointfox_staffs', 'appointfox-staffs', array(
				$this,
				'index',
			)
		);

		// add_submenu_page('appointfox-calendars', 'AppointFox' . ' Staffs - Generate', 'Staffs - Generate', 'manage_appointfox_staffs', 'appointfox-staffs-generate', array(
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

		wp_enqueue_script( 'afx-js-page-staffs', AFX_URL . 'assets/js/admin/staffs.js', array( 'jquery', 'afx-dt', 'afx-dt-bs', 'afx-common-js' ), false, true );

		// Translation array
		$translation_array = array(
			'success1' => __( 'Success', 'appointfox' ),
			'success2' => __( 'Record successfully saved', 'appointfox' ),
			'notfound' => __( 'No record(s) found', 'appointfox' ),
			'staffs'   => __( 'Staffs', 'appointfox' ),
			'fullname' => __( 'Fullname', 'appointfox' ),
			'email'    => __( 'Email', 'appointfox' ),
			'action'   => __( 'Action', 'appointfox' ),
			'edit'   => __( 'Edit', 'appointfox' ),
			'delete'   => __( 'Delete', 'appointfox' ),
		);

		// Local JS
		wp_localize_script(
			'afx-js-page-staffs', 'afx_dt', array(
				'plugin_url'             => AFX_URL,
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'get_table_staffs_nonce' => wp_create_nonce( 'get_table_staffs' ),
				'view_nonce'             => wp_create_nonce( 'view' ),
				'edit_nonce'             => wp_create_nonce( 'edit' ),
				'delete_nonce'           => wp_create_nonce( 'delete' ),
				'labels'                 => $translation_array,
				//'locale_display'         => locale_get_display_language( get_locale(), 'en' ),
				'locale_display'         => 'en',
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

		include AFX_PATH . 'view/admin/Staffs/index.php';
	}

	/**
	 * Ajax - Datatable - list staffs
	 *
	 * @return void
	 */
	public function get_table_staffs() {
		$valid_req = check_ajax_referer( 'get_table_staffs', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_table_staffs nonce' );
		}

		$staffs = Staff::findAll();

		$data = array();

		foreach ( $staffs as $row ) {
			$data['data'][] = array(
				'id'        => $row->id,
				'full_name' => $row->full_name,
				'email'     => $row->email,
			);
		}

		if ( count( $data ) > 0 ) {
			wp_send_json( $data );
		} else {
			$data['sEcho']                = 0;
			$data['iTotalRecords']        = 0;
			$data['iTotalDisplayRecords'] = 0;
			$data['aaData']               = array();
			wp_send_json( $data );
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

		$id = $_GET['id'];

		$staff = Staff::findById( $id );

		$staffs_services = Staff::getServices( $id );

		require_once AFX_PATH . 'view/admin/Staffs/view.php';

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

		$services = Service::findAll();

		$categories = Category::findAll();

		require_once AFX_PATH . 'view/admin/Staffs/add.php';

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

		$id = $_GET['id'];

		$staff = Staff::findById( $id );

		$staffs_services = Staff::getServices( $id );

		$staffs_services_array = array();
		foreach ( $staffs_services as $service ) {
			$staffs_services_array[] = $service->id;
		}

		// get services list
		$services = Service::findAll();

		// get categories list
		$categories = Category::findAll();

		$save_nonce = wp_create_nonce( 'edit_process' );

		require_once AFX_PATH . 'view/admin/Staffs/edit.php';

		die();
	}

	/**
	 * Ajax - process delete staff
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

		// delete staff
		$result = Staff::delete( $id );

		// delete staffs_services
		Staff::deleteServices( $id );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Ajax - process bulk delete staff
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

		$result = Staff::deleteStaffs( $ids );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record(s)';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Ajax - save staff
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( ! isset( $_POST['data'] ) ) {
			wp_die( 'Method not allowed' );
		}

		// init inputs
		$inputs = $_POST['data']['Staff'];

		if ( isset( $inputs['id'] ) ) {
			$inputs['id'] = intval( $inputs['id'] );
		}

		$inputs['full_name'] = sanitize_text_field( $inputs['full_name'] );
		$inputs['email']     = sanitize_text_field( $inputs['email'] );
		$inputs['phone']     = sanitize_text_field( $inputs['phone'] );
		$inputs['info']      = sanitize_textarea_field( $inputs['info'] );

		$inputs['services'] = array_map( 'sanitize_text_field', wp_unslash( $inputs['services'] ) );

		// check nonce
		if ( ! isset( $inputs['id'] ) ) {// if add mode
			$valid_req = check_ajax_referer( 'add_process', false, false );
			if ( false == $valid_req ) {
				wp_die( 'Invalid add_process nonce' );
			}
		} else { // if edit mode
			$valid_req = check_ajax_referer( 'edit_process', false, false );
			if ( false == $valid_req ) {
				wp_die( 'Invalid edit_process nonce' );
			}
		}

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
			$table_name = $wpdb->prefix . 'afx_staffs';

			if ( ! isset( $inputs['id'] ) ) {
				$result            = $wpdb->insert(
					$table_name, array(
						'full_name' => $inputs['full_name'],
						'email'     => $inputs['email'],
						'phone'     => $inputs['phone'],
						'info'      => $inputs['info'],
					), array(
						'%s',
						'%s',
						'%s',
						'%s',
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
			} else {
				$result            = $wpdb->update(
					$table_name, array(
						'full_name' => $inputs['full_name'],
						'email'     => $inputs['email'],
						'phone'     => $inputs['phone'],
						'info'      => $inputs['info'],
					), array( 'ID' => $inputs['id'] ), array(
						'%s',
						'%s',
						'%s',
						'%s',
					), array( '%d' )
				);
				$data['insert_id'] = $inputs['id'];
			}

			// save staffs_services
			$table_name = $wpdb->prefix . 'afx_staffs_services';
			$wpdb->delete( $table_name, array( 'staff_id' => $data['insert_id'] ), array( '%d' ) );

			foreach ( $inputs['services'] as $service ) {
				$result_services = $wpdb->insert(
					$table_name, array(
						'staff_id'   => $data['insert_id'],
						'service_id' => $service,
					), array(
						'%d',
						'%d',
					)
				);
			}

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
	 * Validate inputs
	 */
	public function validate( & $data, $inputs ) {
		// check full_name is empty
		$field = 'full_name';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Fullname is required',
				'field' => $field,
			);
		}

		// check email is empty
		$field = 'email';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Email is required',
				'field' => $field,
			);
		}

		// check email is valid
		if ( ! empty( $inputs[ $field ] ) && ! filter_var( $inputs[ $field ], FILTER_VALIDATE_EMAIL ) ) {
			$data['errors'][] = array(
				'msg'   => 'Email is not valid',
				'field' => $field,
			);
		}
	}

	/**
	 * Ajax - list staffs
	 *
	 * @return void
	 */
	public static function get_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		$staffs = Staff::findAll();

		$data = array(
			'success' => true,
			'staffs'  => $staffs,
			'errors'  => array(),
		);

		wp_send_json( $data );
	}

}
