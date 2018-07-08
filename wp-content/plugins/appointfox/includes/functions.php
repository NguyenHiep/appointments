<?php

use AppointFox\Admin\Controller\NotificationsController;
/**
 * Plugin setup
 *
 * @return void
 */
function afx_setup_plugin() {
	// require_once AFX_PATH . 'include/class-helper.php';
	// add_action('shutdown', 'Bkm_Helper::output_debug_data');
	require_once AFX_PATH . 'admin/NotificationsController.php';
	new NotificationsController();

	if ( is_admin() ) {
		// plugin activator
		require_once AFX_PATH . 'admin/ActivatorController.php';
		new \AppointFox\Admin\Controller\ActivatorController();

		require_once AFX_PATH . 'admin/AdminController.php';

		add_action( 'plugins_loaded', 'afx_load_admin' );
	} else {
		require_once AFX_PATH . 'frontend/FrontendController.php';

		add_action( 'plugins_loaded', 'afx_load_frontend' );
	}

	add_action( 'plugins_loaded', 'afx_load_languages' );
}

/**
 * Load language files
 *
 * @return void
 */
function afx_load_languages() {
	// die( basename( dirname( __FILE__ ) ) . '/languages' );
	// load_plugin_textdomain( 'appointfox', false, basename( dirname( __FILE__ ) ) . '/languages' );
	load_plugin_textdomain( 'appointfox', false, 'appointfox/languages' );
}


/**
 * Load admin controller
 *
 * @return void
 */
function afx_load_admin() {
	new \AppointFox\Admin\Controller\AdminController();
}

/**
 * Load frontend controller
 *
 * @return void
 */
function afx_load_frontend() {
	new \AppointFox\Frontend\Controller\FrontendController();
}

/**
 * Enqueue common js and css files for frontend
 *
 * @return void
 */
function afx_init_scripts_for_frontend() {

}

/**
 * Enqueue common js and css files for admin
 *
 * @return void
 */
function afx_init_scripts_for_admin() {
	// Css
	// wp_enqueue_style('afx-tbs-css', AFX_URL . 'assets/css/appointfox-tbs_2.css');
	wp_enqueue_style( 'afx-tbs-css', AFX_URL . 'assets/css/appointfox-tbs_2.min.css' );
	// wp_enqueue_style('afx-tbs-modal-patch-css', AFX_URL . 'assets/css/bootstrap-modal-bs3patch.css', array('afx-tbs-css'));
	// wp_enqueue_style('afx-tbs-modal-css', AFX_URL . 'assets/css/bootstrap-modal.css', array('afx-tbs-css', 'afx-tbs-modal-patch-css'));
	// wp_enqueue_style('afx-tbs-extend', AFX_URL . 'assets/css/appointfox-tbs-extend.css', array('afx-tbs-css'));
	wp_enqueue_style( 'afx-font-awesome', AFX_URL . 'assets/css/font-awesome.min.css' );
	wp_enqueue_style( 'afx-toastr-css', AFX_URL . 'assets/css/toastr.min.css' );
	wp_enqueue_style( 'afx-sweetalert2-css', AFX_URL . 'assets/css/sweetalert2.min.css' );
	wp_enqueue_style( 'afx-ladda-css', AFX_URL . 'assets/css/ladda-themeless.min.css' );
	wp_enqueue_style( 'afx-common-css', AFX_URL . 'assets/css/appointfox-common.css' );

	// Js
	// wp_enqueue_script('jquery', AFX_URL . 'assets/js/jquery.min.js');
	// wp_enqueue_script( 'afx-moment-js', AFX_URL . 'assets/js/moment.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-moment-js', AFX_URL . 'assets/js/moment-with-locales.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-bootstrap-js', AFX_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ) );
	// wp_enqueue_script('afx-bootstrap-modalmanager-js', AFX_URL . 'assets/js/bootstrap-modalmanager.js', array('jquery', 'afx-bootstrap-js'));
	// wp_enqueue_script('afx-bootstrap-modal-js', AFX_URL . 'assets/js/bootstrap-modal.js', array('jquery', 'afx-bootstrap-modalmanager-js'));
	wp_enqueue_script( 'afx-jquery-form', AFX_URL . 'assets/js/jquery.form.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-sweetalert2-js', AFX_URL . 'assets/js/sweetalert2.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-toastr-js', AFX_URL . 'assets/js/toastr.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-spin', AFX_URL . 'assets/js/spin.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-ladda', AFX_URL . 'assets/js/ladda.min.js', array( 'jquery' ) );
	// wp_enqueue_script( 'afx-notify', AFX_URL . 'assets/js/notify.min.js', array( 'jquery' ) );
	// wp_enqueue_script('afx-jquery-ui', AFX_URL . 'js/jquery-ui.custom.min.js', array('jquery'));
}

/**
 * Enqueue js and css files for vuejs
 *
 * @return void
 */
function afx_init_scripts_for_vue() {
	// Js
	// wp_enqueue_script( 'afx-vue', AFX_URL . 'assets/js/vue.js' );
	wp_enqueue_script( 'afx-vue', AFX_URL . 'assets/js/vue.min.js' );
	wp_enqueue_script( 'afx-promise', AFX_URL . 'assets/js/es6-promise.min.js' );
	wp_enqueue_script( 'afx-promise-auto', AFX_URL . 'assets/js/es6-promise.auto.min.js' );
	wp_enqueue_script( 'afx-axios', AFX_URL . 'assets/js/axios.min.js' );
}

/**
 * Enqueue js and css files for datatables
 *
 * @return void
 */
function afx_init_scripts_for_datatables() {
	// Css
	wp_enqueue_style( 'afx-css-bt-bs', AFX_URL . 'assets/css/datatable/dataTables.bootstrap.min.css' );
	wp_enqueue_style( 'afx-css-btn-bs', AFX_URL . 'assets/css/datatable/buttons.bootstrap.min.css' );
	wp_enqueue_style( 'afx-datatables-responsive', AFX_URL . 'assets/css/datatable/reponsive-table.css' );

	// Js
	wp_enqueue_script( 'afx-dt', AFX_URL . 'assets/js/datatable/jquery.dataTables.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-dt-bs', AFX_URL . 'assets/js/datatable/dataTables.bootstrap.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-dt-btn', AFX_URL . 'assets/js/datatable/dataTables.buttons.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-btn-bs', AFX_URL . 'assets/js/datatable/buttons.bootstrap.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-jszip', AFX_URL . 'assets/js/datatable/jszip.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-pdfmake', AFX_URL . 'assets/js/datatable/pdfmake.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-vfs-font', AFX_URL . 'assets/js/datatable/vfs_fonts.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-btn-html', AFX_URL . 'assets/js/datatable/buttons.html5.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-btn-print', AFX_URL . 'assets/js/datatable/buttons.print.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-btn-col', AFX_URL . 'assets/js/datatable/buttons.colVis.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'afx-dt-datetimemoment', AFX_URL . 'assets/js/datetime-moment.js', array( 'jquery', 'afx-dt' ) );

	wp_enqueue_script( 'afx-polyfill-js', AFX_URL . 'assets/js/core.js', array( 'jquery' ) );
}

/**
 * Replace string by given array function
 *
 * @param array  $replace
 * @param [type] $subject
 * @return void
 */
function strReplaceAssoc( array $replace, $subject ) {
	return str_replace( array_keys( $replace ), array_values( $replace ), $subject );
}

function formatDuration( $duration ) {
	switch ( $duration ) {
		case ( 60 * 5 ):
			return '5 min';
				break;
		case ( 60 * 10 ):
			return '10 min';
				break;
		case ( 60 * 15 ):
			return '15 min';
				break;
		case ( 60 * 30 ):
			return '30 min';
				break;
		case ( 60 * 60 ):
			return '1 hour';
				break;
		case ( 60 * 60 * 2 ):
			return '2 hours';
				break;
		case ( 60 * 60 * 3 ):
			return '3 hours';
				break;
		case ( 60 * 60 * 4 ):
			return '4 hours';
				break;
		case ( 60 * 60 * 5 ):
			return '5 hours';
				break;
		case ( 60 * 60 * 24 ):
			return 'Daily';
				break;
		default:
			return '';
	}
}
/**
 * Function for writing to php log file
 */
if ( ! function_exists( 'write_log' ) ) {

	function write_log( $log ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}
