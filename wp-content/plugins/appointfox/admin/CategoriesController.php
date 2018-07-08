<?php
namespace AppointFox\Admin\Controller;

/**
 * CategoriesController class
 */
class CategoriesController {

	/**
	 * __construct function
	 */
	public function __construct() {
		// add_action('admin_menu', array($this, 'add_menu'));
		add_action(
			'wp_ajax_afx-categories-save', array(
				$this,
				'save',
			)
		);

		add_action(
			'wp_ajax_afx-categories-delete', array(
				$this,
				'delete',
			)
		);

		add_action(
			'wp_ajax_afx-categories-list', array(
				$this,
				'get_list',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-categories' ) {
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
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Categories', 'appointfox' ), __( 'Categories', 'appointfox' ), 'manage_appointfox_categories', 'appointfox-categories', array(
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
		// afx_init_scripts_for_admin();
	}

	/**
	 * Validate inputs
	 */
	public function validate( & $data, $inputs ) {
		// check name is empty
		$field = 'name';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Category name is required',
				'field' => $field,
			);
		}
	}

	/**
	 * Ajax - save category
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( ! isset( $_POST['data'] ) ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_category', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_category nonce' );
		}

		// init inputs
		$inputs = $_POST['data']['Category'];

		$inputs['name'] = sanitize_text_field( $inputs['name'] );

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
			$table_name = $wpdb->prefix . 'afx_categories';

			if ( empty( $inputs['id'] ) ) {
				$result            = $wpdb->insert(
					$table_name, array(
						'name' => $inputs['name'],
					), array(
						'%s',
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
			} else {
				$result            = $wpdb->update(
					$table_name, array(
						'name' => $inputs['name'],
					), array( 'ID' => $inputs['id'] ), array(
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
	 * Ajax - list categories
	 *
	 * @return void
	 */
	public function get_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_categories';
		$sql        = 'SELECT id, name FROM ' . $table_name . ' ORDER BY name ASC';

		$rows = $wpdb->get_results( $sql );

		$data['success'] = true;

		if ( count( $rows ) > 0 ) {
			foreach ( $rows as $row ) {
				$data['data'][] = array(
					'id'   => $row->id,
					'name' => $row->name,
				);
			}
			$data['total_records'] = count( $data['data'] );
		} else {
			$data['total_records'] = 0;
		}

		wp_send_json( $data );
	}

	/**
	 * Ajax - delete category
	 *
	 * @return void
	 */
	public function delete() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'delete_category', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid delete_category nonce' );
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

		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_categories';
		$result     = $wpdb->delete( $table_name, array( 'ID' => $id ) );

		$table_name = $wpdb->prefix . 'afx_services';
		$result     = $wpdb->delete( $table_name, array( 'category_id' => $id ) );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}

	/**
	 * Index page
	 *
	 * @return void
	 */
	public function index() {
		include AFX_PATH . 'view/admin/Categories/index.php';
	}
}
