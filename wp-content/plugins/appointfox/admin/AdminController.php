<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'admin/AppointmentsCalendarController.php';
require_once AFX_PATH . 'admin/AppointmentsController.php';
require_once AFX_PATH . 'admin/CustomersController.php';
require_once AFX_PATH . 'admin/StaffsController.php';
require_once AFX_PATH . 'admin/PaymentsController.php';
require_once AFX_PATH . 'admin/CalendarsController.php';
require_once AFX_PATH . 'admin/CategoriesController.php';
require_once AFX_PATH . 'admin/ServicesController.php';
require_once AFX_PATH . 'admin/SettingsController.php';
require_once AFX_PATH . 'admin/AjaxController.php';

/**
 * Admin controller class
 */
class AdminController {

	protected $CalendarAppointments;

	/**
	 * __construct function
	 */
	public function __construct() {
		// Setup menu
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		if ( isset( $_GET['page'] ) ) {
			if ( strpos( $_GET['page'], 'appointfox' ) !== false ) {
				add_action(
					'admin_init', array(
						$this,
						'init_scripts',
					)
				);
			}
		}

		$this->CalendarAppointments = new AppointmentsCalendarController();
		new AppointmentsController();
		new CustomersController();
		new CategoriesController();
		new ServicesController();
		new StaffsController();
		new PaymentsController();
		new CalendarsController();
		new SettingsController();
		new AjaxController();
	}

	/**
	 * Add menu
	 *
	 * @return void
	 */
	public function add_menu() {
		add_menu_page( 'AppointFox', 'AppointFox', 'manage_appointfox_appointments', 'appointfox-appointmentcalendar', array( $this->CalendarAppointments, 'index' ), AFX_URL . 'assets/images/icon16.png', '2.2.9' );
	}

	/**
	 * Enqueue js and css files
	 *
	 * @return void
	 */
	public function init_scripts() {
		// Js
		wp_enqueue_script( 'afx-common-js', AFX_URL . 'assets/js/admin/common.js', array( 'jquery' ) );
		// wp_enqueue_script('afx-jquery-ui', AFX_URL . 'js/jquery-ui.custom.min.js', array('jquery'));
		// Translation array
		$translation_array = array(
			'are_you_sure'             => __( 'Are you sure', 'appointfox' ),
			'record'                   => __( 'Record', 'appointfox' ),
			'yes_delete_it'            => __( 'Yes, delete it', 'appointfox' ),
			'you_wont_able_revert'     => __( 'You won\'t be able to revert this', 'appointfox' ),
			'deleted'                  => __( 'Deleted', 'appointfox' ),
			'record_deleted'           => __( 'Record has been deleted', 'appointfox' ),
			'create_new_category'      => __( 'Create a new Category', 'appointfox' ),
			'service'                  => __( 'Service', 'appointfox' ),
			'category'                 => __( 'Category', 'appointfox' ),
			'success'                  => __( 'Success', 'appointfox' ),
			'and_all_services_deleted' => __( 'and all it\'s services will be deleted', 'appointfox' ),
			'service_saved'            => __( 'Service successfully saved', 'appointfox' ),
			'the_category_deleted'     => __( 'The category has been deleted', 'appointfox' ),
			'oops'                     => __( 'Oops', 'appointfox' ),
			'records_deleted'          => __( 'Record(s) has been deleted', 'appointfox' ),
			'no_record_selected'       => __( 'No record(s) selected', 'appointfox' ),
		);

		// Local JS
		wp_localize_script(
			'afx-common-js', 'afx_common', array(
				'labels' => $translation_array,
			)
		);

	}
}
