<?php
namespace AppointFox\Frontend\Controller;

require_once AFX_PATH . 'frontend/AppointmentsController.php';

/**
 * FrontendController class
 */
class FrontendController {

	protected $Appointments;

	/**
	 * __construct function
	 */
	public function __construct() {
		add_action(
			'wp_enqueue_scripts', array(
				$this,
				'init_scripts',
			)
		);

		$this->Appointments = new AppointmentsController();
	}

	/**
	 * Enqueue js and css files
	 *
	 * @return void
	 */
	public function init_scripts() {
		// Css
		wp_enqueue_style( 'afx-frontend-common', AFX_URL . 'assets/css/frontend/common.css' );
		wp_enqueue_style( 'afx-tbs-css', AFX_URL . 'assets/css/appointfox-tbs_2.min.css' );
		wp_enqueue_style( 'afx-font-awesome', AFX_URL . 'assets/css/font-awesome.min.css' );
		wp_enqueue_style( 'afx-sweetalert2-css', AFX_URL . 'assets/css/sweetalert2.min.css' );
		wp_enqueue_style( 'afx-ladda-css', AFX_URL . 'assets/css/ladda-themeless.min.css' );

		// Js
		// wp_enqueue_script( 'afx-moment-js', AFX_URL . 'assets/js/moment.min.js', array( 'jquery' ) );		// wp_enqueue_script( 'afx-moment-js', AFX_URL . 'assets/js/moment.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'afx-moment-js', AFX_URL . 'assets/js/moment-with-locales.min.js', array( 'jquery' ) );

		wp_enqueue_script( 'afx-bootstrap-js', AFX_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'afx-sweetalert2-js', AFX_URL . 'assets/js/sweetalert2.min.js', array( 'jquery' ) );

		wp_enqueue_script( 'afx-spin', AFX_URL . 'assets/js/spin.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'afx-ladda', AFX_URL . 'assets/js/ladda.min.js', array( 'jquery' ) );
		// wp_enqueue_script('afx-notify', AFX_URL . 'assets/js/notify.min.js', array('jquery'));
		// wp_enqueue_script('afx-common', AFX_URL . 'assets/js/admin/page-common.js', array('jquery'));
		// wp_enqueue_script('afx-jquery-ui', AFX_URL . 'js/jquery-ui.custom.min.js', array('jquery'));
	}
}
