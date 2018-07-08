<?php
namespace AppointFox\Admin\Controller;

/**
 * CustomersController class
 */
class CustomersController {

	/**
	 * __construct function
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-customers-table', array(
				$this,
				'get_table_customers',
			)
		);

		add_action(
			'wp_ajax_afx-customers-view', array(
				$this,
				'view',
			)
		);

		add_action(
			'wp_ajax_afx-customers-add', array(
				$this,
				'add',
			)
		);

		add_action(
			'wp_ajax_afx-customers-edit', array(
				$this,
				'edit',
			)
		);

		add_action(
			'wp_ajax_afx-customers-delete-process', array(
				$this,
				'delete_process',
			)
		);

		add_action(
			'wp_ajax_afx-customers-bulkdelete-process', array(
				$this,
				'bulkdelete_process',
			)
		);

		add_action(
			'wp_ajax_afx-customers-save', array(
				$this,
				'save',
			)
		);

		add_action(
			'wp_ajax_afx-customers-list', array(
				$this,
				'get_list',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-customers' ) {
			add_action(
				'admin_init', array(
					$this,
					'init_scripts',
				)
			);
		}

		// add_action('admin_init', array($this, 'generateData'));
	}

	/**
	 * Add menu
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Customers', 'appointfox' ), __( 'Customers', 'appointfox' ), 'manage_appointfox_customers', 'appointfox-customers', array(
				$this,
				'index',
			)
		);
	}

	/**
	 * Enqueue js and css files
	 *
	 * @return void
	 */
	public function init_scripts() {
		afx_init_scripts_for_admin();

		afx_init_scripts_for_datatables();

		wp_enqueue_script( 'afx-js-page-customers', AFX_URL . 'assets/js/admin/customers.js', array( 'jquery', 'afx-dt', 'afx-dt-bs', 'afx-common-js' ), false, true );

		// Translation array
		$translation_array = array(
			'success1'  => __( 'Success', 'appointfox' ),
			'success2'  => __( 'Record successfully saved', 'appointfox' ),
			'notfound'  => __( 'No record(s) found', 'appointfox' ),
			'customers' => __( 'Customers', 'appointfox' ),
			'edit'      => __( 'Edit', 'appointfox' ),
			'delete'    => __( 'Delete', 'appointfox' ),
		);

		// Local JS
		wp_localize_script(
			'afx-js-page-customers', 'afx_dt', array(
				'plugin_url'                => AFX_URL,
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'get_table_customers_nonce' => wp_create_nonce( 'get_table_customers' ),
				'view_nonce'                => wp_create_nonce( 'view' ),
				'edit_nonce'                => wp_create_nonce( 'edit' ),
				'delete_nonce'              => wp_create_nonce( 'delete' ),
				'labels'                    => $translation_array,
				'locale_display'            => locale_get_display_language( get_locale(), 'en' ),
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

		include AFX_PATH . 'view/admin/Customers/index.php';
	}

	/**
	 * Ajax - datatable - list customers
	 *
	 * @return void
	 */
	public function get_table_customers() {
		$valid_req = check_ajax_referer( 'get_table_customers', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_table_customers nonce' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_customers';
		$sql        = 'SELECT id, full_name, email FROM ' . $table_name;

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

			wp_send_json( $data );
		}
	}

	/**
	 * Ajax - list customers
	 *
	 * @return void
	 */
	public function get_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_customers';
		$sql        = 'SELECT id, full_name, email, phone, info FROM ' . $table_name . ' ORDER BY full_name ASC';

		$rows = $wpdb->get_results( $sql );

		$data['success'] = true;

		if ( count( $rows ) > 0 ) {
			foreach ( $rows as $row ) {
				$data['data'][] = array(
					'id'        => $row->id,
					'full_name' => $row->full_name,
					'email'     => $row->email,
					'phone'     => $row->phone,
					'info'      => $row->info,
				);
			}
			$data['total_records'] = count( $data['data'] );
		} else {
			$data['total_records'] = 0;
		}

		wp_send_json( $data );
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
		$table_name = $wpdb->prefix . 'afx_customers';

		$id = $_GET['id'];

		$sql = $wpdb->prepare(
			"SELECT id, full_name, email, phone, info FROM $table_name WHERE id = %d", array(
				$id,
			)
		);

		$customer = $wpdb->get_row( $sql );

		require_once AFX_PATH . 'view/admin/Customers/view.php';

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

		require_once AFX_PATH . 'view/admin/Customers/add.php';

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
		$table_name = $wpdb->prefix . 'afx_customers';

		$id = $_GET['id'];

		$sql = $wpdb->prepare(
			"SELECT id, full_name, email, phone, info FROM $table_name WHERE id = %d", array(
				$id,
			)
		);

		$customer = $wpdb->get_row( $sql );

		$save_nonce = wp_create_nonce( 'edit_process' );

		require_once AFX_PATH . 'view/admin/Customers/edit.php';

		die();
	}

	/**
	 * Ajax - process delete
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

		// delete customer
		$table_name = $wpdb->prefix . 'afx_customers';
		$result     = $wpdb->delete( $table_name, array( 'ID' => $id ) );

		// delete customer's appointment
		$table_name = $wpdb->prefix . 'afx_appointments';
		$result     = $wpdb->delete( $table_name, array( 'customer_id' => $id ) );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
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

		// delete customer
		$table_name = $wpdb->prefix . 'afx_customers';
		$ids        = implode( ',', array_map( 'absint', $ids ) );
		$result     = $wpdb->query( "DELETE FROM $table_name WHERE ID IN($ids)" );

		// delete customer's appointment
		$table_name = $wpdb->prefix . 'afx_appointments';
		$result     = $wpdb->query( "DELETE FROM $table_name WHERE customer_id IN($ids)" );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record(s)';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Ajax - save customer
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( ! isset( $_POST['data'] ) ) {
			wp_die( 'Method not allowed' );
		}

		// init inputs
		$inputs = $_POST['data']['Customer'];

		if ( isset( $inputs['id'] ) ) {
			$inputs['id'] = intval( $inputs['id'] );
		}

		$inputs['full_name'] = sanitize_text_field( $inputs['full_name'] );
		$inputs['email']     = sanitize_text_field( $inputs['email'] );
		$inputs['phone']     = sanitize_text_field( $inputs['phone'] );
		$inputs['info']      = sanitize_textarea_field( $inputs['info'] );

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
			$table_name = $wpdb->prefix . 'afx_customers';

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
	 * Generate fake data
	 *
	 * @return void
	 */
	// public function generateData() {
	// $faker = \Faker\Factory::create();
	// global $wpdb;
	// $table_name = $wpdb->prefix . 'afx_customers';
	// for ( $i = 0; $i < 5; $i++ ) {
	// echo $faker->name . '<br>';
	// $result = $wpdb->insert(
	// $table_name, array(
	// 'full_name' => $faker->name,
	// 'email'     => $faker->email,
	// 'phone'     => $faker->phoneNumber,
	// 'info'      => $faker->text,
	// ), array(
	// '%s',
	// '%s',
	// '%s',
	// '%s',
	// )
	// );
	// }
	// }
}
