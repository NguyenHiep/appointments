<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Setting.php';
require_once AFX_PATH . 'models/Notification.php';

use AppointFox\Model\Setting as Setting;
use AppointFox\Model\Notification as Notification;

/**
 * SettingsController class
 */
class SettingsController {

	/**
	 * __construct function
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action(
			'wp_ajax_afx-settings-get', array(
				$this,
				'get_settings',
			)
		);

		add_action(
			'wp_ajax_afx-settings-save', array(
				$this,
				'save',
			)
		);

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'appointfox-settings' ) {
			add_action(
				'admin_init', array(
					$this,
					'init_scripts',
				)
			);

			add_filter( 'user_can_richedit', '__return_true' );
		}

	}

	/**
	 * Add menu
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'appointfox-appointmentcalendar', 'AppointFox - ' . __( 'Settings', 'appointfox' ), __( 'Settings', 'appointfox' ), 'manage_appointfox_settings', 'appointfox-settings', array(
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
		include AFX_PATH . 'view/admin/Settings/index.php';
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
		wp_enqueue_style( 'afx-switch-css', AFX_URL . 'assets/css/switch.css', array( 'afx-tbs-css' ) );

		// Js
		wp_enqueue_script( 'afx-clipboard-js', AFX_URL . 'assets/js/clipboard.min.js', array( 'jquery' ) );
		// wp_enqueue_script( 'afx-vue-select2', 'https://unpkg.com/vue-select@latest', array('afx-vue' ) );
		wp_enqueue_script( 'afx-select2-js', AFX_URL . 'assets/js/select2.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'afx-js-page-settings', AFX_URL . 'assets/js/admin/settings.js', array( 'jquery', 'afx-common-js', 'afx-vue', 'afx-axios', 'afx-promise', 'afx-promise-auto', 'afx-clipboard-js', 'afx-select2-js', 'wp-color-picker' ), false, true );

		// Translation array
		$translation_array = array(
			'settings_saved' => __( 'Settings successfully saved', 'appointfox' ),
			'copied'         => __( 'Copied', 'appointfox' ),
		);

		// Local JS
		wp_localize_script(
			'afx-js-page-settings', 'afx_vars', array(
				'plugin_url'          => AFX_URL,
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'get_settings_nonce'  => wp_create_nonce( 'get_settings' ),
				'save_settings_nonce' => wp_create_nonce( 'save_settings' ),
				'labels'              => $translation_array,
			)
		);

	}

	/**
	 * Ajax - List settings
	 *
	 * @return void
	 */
	public function get_settings() {
		$valid_req = check_ajax_referer( 'get_settings', false, false );

		if ( false == $valid_req ) {
			wp_die( 'Invalid get_settings nonce' );
		}

		// load settings
		$settings = Setting::findAll();

		// load notifications templates
		$notifications = Notification::findAll();

		$data = array(
			'success'       => true,
			'settings'      => $settings,
			'notifications' => $notifications,
			'errors'        => array(),
		);

		wp_send_json( $data );
	}

	/**
	 * Ajax - Save settings
	 *
	 * @return void
	 */
	public function save() {
		// only post method allow
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			wp_die( 'Method not allowed' );
		}

		$valid_req = check_ajax_referer( 'save_settings', false, false );
		if ( false == $valid_req ) {
			wp_die( 'Invalid save_settings nonce' );
		}

		// init inputs
		$inputs             = json_decode( file_get_contents( 'php://input' ), true );
		$inputsNotification = $inputs['notification'];
		$inputsSettings     = $inputs['settings'];

		$settings                            = array();
		$settings['business_name']           = sanitize_text_field( $inputsSettings['business_name'] );
		$settings['instructions']            = sanitize_textarea_field( $inputsSettings['instructions'] );
		$settings['week_start_on']           = sanitize_text_field( $inputsSettings['week_start_on'] );
		$settings['time_format']             = $inputsSettings['time_format'];
		$settings['currency']                = $inputsSettings['currency'];
		$settings['reminder_hours']          = $inputsSettings['reminder_hours'];
		$settings['payment_method']          = $inputsSettings['payment_method'];
		$settings['paypal_prod_clientid']    = $inputsSettings['paypal_prod_clientid'];
		$settings['paypal_prod_secret']      = $inputsSettings['paypal_prod_secret'];
		$settings['paypal_sandbox_clientid'] = $inputsSettings['paypal_sandbox_clientid'];
		$settings['paypal_sandbox_secret']   = $inputsSettings['paypal_sandbox_secret'];
		$settings['is_paypal_sandbox']       = $inputsSettings['is_paypal_sandbox'];
		$settings['background_color']        = $inputsSettings['background_color'];
		$settings['font_color']              = $inputsSettings['font_color'];
		$settings['font_size']               = $inputsSettings['font_size'];

		$data = Setting::save( $settings );

		if ( $inputsNotification['id'] ) {
			$notification            = array();
			$notification['id']      = $inputsNotification['id'];
			$notification['subject'] = $inputsNotification['subject'];
			$notification['message'] = $inputsNotification['message'];

			$data = Notification::save( $notification );
		}

		wp_send_json( $data );
	}
}
