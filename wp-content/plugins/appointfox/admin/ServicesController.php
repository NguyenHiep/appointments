<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Service.php';
require_once AFX_PATH . 'models/Category.php';
require_once AFX_PATH . 'models/Setting.php';
require_once AFX_PATH . 'models/Staff.php';

use AppointFox\Model\Service as Service;
use AppointFox\Model\Category as Category;
use AppointFox\Model\Setting as Setting;
use AppointFox\Model\Staff as Staff;

/**
 * ServicesController class
 */
class ServicesController {

	/**
	 * __construct function
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-services-save', array(
				$this,
				'save',
			)
		);

		add_action(
			'wp_ajax_afx-services-delete', array(
				$this,
				'delete',
			)
		);

		add_action(
			'wp_ajax_afx-services-list', array(
				$this,
				'get_list',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-services' ) {
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
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Services', 'appointfox' ), __( 'Services', 'appointfox' ), 'manage_appointfox_services', 'appointfox-services', array(
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

		wp_enqueue_style( 'wp-color-picker' );

		afx_init_scripts_for_vue();

		// Css
		wp_enqueue_style( 'afx-select2-css', AFX_URL . 'assets/css/select2.min.css' );
		wp_enqueue_style( 'afx-select2-bootstrap-css', AFX_URL . 'assets/css/select2-bootstrap.min.css', array( 'afx-select2-css', 'afx-tbs-css' ) );

		// Js
		// wp_enqueue_script('afx-vuejs-resource', AFX_URL . 'assets/js/vue-resource.min.js');
		wp_enqueue_script( 'afx-vue-money', AFX_URL . 'assets/js/v-money.js' );
		wp_enqueue_script( 'afx-select2-js', AFX_URL . 'assets/js/select2.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'afx-js-page-services', AFX_URL . 'assets/js/admin/services.js', array( 'jquery', 'afx-common-js', 'wp-color-picker', 'afx-vue', 'afx-axios', 'afx-vue-money', 'afx-promise', 'afx-promise-auto', 'afx-select2-js' ), false, true );

		$settings = Setting::findAll();

		// Translation array
		$translation_array = array(
			'service_saved'            => __( 'Service successfully saved', 'appointfox' ),
			'category_saved'           => __( 'Category successfully saved', 'appointfox' ),
			'you_wont_able_revert'     => __( 'You won\'t be able to revert this', 'appointfox' ),
			'category'                 => __( 'Category', 'appointfox' ),
			'and_all_services_deleted' => __( 'and all it\'s services will be deleted', 'appointfox' ),
			'yes_delete_it'            => __( 'Yes, delete it', 'appointfox' ),
			'deleted'                  => __( 'Deleted', 'appointfox' ),
			'the_category_deleted'     => __( 'The category has been deleted', 'appointfox' ),
			'service'                  => __( 'Service', 'appointfox' ),
            'the_service_deleted'      => __( 'The service has been deleted', 'appointfox' ),
            'are_you_sure'             => __( 'Are you sure', 'appointfox' ),
		);

		// Local JS
		wp_localize_script(
			'afx-js-page-services', 'afx_vars', array(
				'plugin_url'            => AFX_URL,
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'get_list_nonce'        => wp_create_nonce( 'get_list' ),
				'delete_category_nonce' => wp_create_nonce( 'delete_category' ),
				'delete_service_nonce'  => wp_create_nonce( 'delete_service' ),
				'save_category_nonce'   => wp_create_nonce( 'save_category' ),
				'save_service_nonce'    => wp_create_nonce( 'save_service' ),
				'currency'              => $settings['currency'],
				'labels'                => $translation_array,
			)
		);
	}

	/**
	 * Index page
	 *
	 * @return void
	 */
	public function index() {
		$add_process_nonce   = wp_create_nonce( 'add_process' );
		$save_category_nonce = wp_create_nonce( 'save_category' );

		include AFX_PATH . 'view/admin/Services/index.php';
	}

	/**
	 * Ajax - process add service
	 *
	 * @return void
	 */
	public function add_process() {
		return $this->save();
	}

	/**
	 * Ajax - save service
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( ! isset( $_POST['data'] ) ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_service', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_service nonce' );
		}

		// init inputs
		$inputs = $_POST['data']['Service'];
		$inputs['title']       = sanitize_text_field( $inputs['title'] );
		$inputs['category_id'] = intval( $inputs['category_id'] );
		$inputs['duration']    = intval( $inputs['duration'] );

		// $tmp = array('$','MYR','RM');
		// $inputs['price'] = str_replace($tmp, '', $inputs['price']);
		$inputs['price']  = floatval( $inputs['price'] );
		$inputs['color']  = sanitize_hex_color( $inputs['color'] );
		$inputs['access'] = sanitize_text_field( $inputs['access'] );
		$inputs['note']   = sanitize_textarea_field( $inputs['note'] );
        /*if (filter_var($inputs['image'], FILTER_VALIDATE_URL)) {
            $inputs['image'] = $inputs['image'];
        }else{
            $inputs['image'] = null;
        }*/

		if ( ! empty( $inputs['staffs'] ) ) {
			$inputs['staffs'] = explode( ',', $inputs['staffs'] );
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
			$table_name = $wpdb->prefix . 'afx_services';

			if ( empty( $inputs['id'] ) ) {
				$result            = $wpdb->insert(
					$table_name, array(
						'category_id' => $inputs['category_id'],
						'title'       => $inputs['title'],
						'duration'    => $inputs['duration'],
						'price'       => $inputs['price'],
						'color'       => $inputs['color'],
						'access'      => $inputs['access'],
						'note'        => $inputs['note'],
						/*'image'       => $inputs['image'],*/
					), array(
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s',
                        /*'%s',*/
					)
				);
				$data['insert_id'] = $wpdb->insert_id;
			} else {
				$result            = $wpdb->update(
					$table_name, array(
						'category_id' => $inputs['category_id'],
						'title'       => $inputs['title'],
						'duration'    => $inputs['duration'],
						'price'       => $inputs['price'],
						'color'       => $inputs['color'],
						'access'      => $inputs['access'],
						'note'        => $inputs['note'],
						/*'image'       => $inputs['image'],*/
					), array( 'ID' => $inputs['id'] ), array(
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s',
                        /*'%s',*/
					), array( '%d' )
				);
				$data['insert_id'] = $inputs['id'];
			}

			if ( $result === false ) {
				$data['success']  = false;
				$data['errors'][] = 'Error occured when trying to save to database';
			} else {
				$data['success'] = true;

				// update staffs_services
				if ( ! empty( $inputs['staffs'] ) ) {
					Service::saveAssignedStaffs( $data['insert_id'], $inputs['staffs'] );
				}
			}
		}

		wp_send_json( $data );
	}

	/**
	 * Validate inputs
	 */
	public function validate( & $data, $inputs ) {
		// check title is empty
		$field = 'title';
		if ( empty( $inputs[ $field ] ) ) {
			$data['errors'][] = array(
				'msg'   => 'Title is required',
				'field' => $field,
			);
		}
	}

	/**
	 * Ajax - list services
	 *
	 * @return void
	 */
	public function get_list() {
		$valid_req = check_ajax_referer( 'get_list', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid get_list nonce' );
		}

		$services = Service::findAll();

		$categories = Category::findAll();

		$staffs = Staff::findAll();

		$data = array(
			'success'    => true,
			'categories' => $categories,
			'services'   => $services,
			'staffs'     => $staffs,
			'errors'     => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Ajax - delete service
	 *
	 * @return void
	 */
	public function delete() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'delete_service', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid delete_service nonce' );
		}
		if ( ! isset( $_POST['id'] ) ) {
			wp_die( 'Invalid Id' );
		}

		$id = $_POST['id'];

		$data = array(
			'id'      => $id,
			'success' => false,
			'errors'  => array(),
		);

		global $wpdb;
		// Delete service
		$table_name = $wpdb->prefix . 'afx_services';
		$result     = $wpdb->delete( $table_name, array( 'ID' => $id ) );

		// Delete assigned staffs's service
		$table_name = $wpdb->prefix . 'afx_staffs_services';
		$result     = $wpdb->delete( $table_name, array( 'service_id' => $id ) );

		if ( $result === false ) {
			$data['success']  = false;
			$data['errors'][] = 'Error occured when trying to delete the record';
		} else {
			$data['success'] = true;
		}

		wp_send_json( $data );
	}
}
