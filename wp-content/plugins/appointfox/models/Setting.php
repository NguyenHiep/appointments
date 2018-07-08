<?php

namespace AppointFox\Model;

/**
 * Model class - Setting
 */
class Setting {

	/**
	 * List settings
	 *
	 * @return void
	 */
	public static function findAll() {
		// set default settings
		if ( get_option( 'afx_business_name' ) === false ) {
			add_option( 'afx_business_name', 'Business Name' );
		}
		if ( get_option( 'afx_instructions' ) === false ) {
			add_option( 'afx_instructions', '' );
		}
		if ( get_option( 'afx_week_start_on' ) === false ) {
			add_option( 'afx_week_start_on', 'Sunday' );
		}
		if ( get_option( 'afx_time_format' ) === false ) {
			add_option( 'afx_time_format', 'AM/PM' );
		}
		if ( get_option( 'afx_currency' ) === false ) {
			add_option( 'afx_currency', 'USD' );
		}
		if ( get_option( 'afx_reminder_hours' ) === false ) {
			add_option( 'afx_reminder_hours', '24' );
		}
		if ( get_option( 'afx_payment_method' ) === false ) {
			add_option( 'afx_payment_method', 'None' );
		}
		if ( get_option( 'afx_paypal_prod_clientid' ) === false ) {
			add_option( 'afx_paypal_prod_clientid', '' );
		}
		if ( get_option( 'afx_paypal_prod_secret' ) === false ) {
			add_option( 'afx_paypal_prod_secret', '' );
		}
		if ( get_option( 'afx_paypal_sandbox_clientid' ) === false ) {
			add_option( 'afx_paypal_sandbox_clientid', '' );
		}
		if ( get_option( 'afx_paypal_sandbox_secret' ) === false ) {
			add_option( 'afx_paypal_sandbox_secret', '' );
		}
		if ( get_option( 'afx_is_paypal_sandbox' ) === false ) {
			add_option( 'afx_is_paypal_sandbox', '1' );
		}
		if ( get_option( 'afx_background_color' ) === false ) {
			add_option( 'afx_background_color', '#fff' );
		}
		if ( get_option( 'afx_font_color' ) === false ) {
			add_option( 'afx_font_color', '#000' );
		}
		if ( get_option( 'afx_font_size' ) === false ) {
			add_option( 'afx_font_size', '16px' );
		}

		// get settings
		$settings                            = array();
		$settings['business_name']           = get_option( 'afx_business_name' );
		$settings['instructions']            = get_option( 'afx_instructions' );
		$settings['week_start_on']           = get_option( 'afx_week_start_on' );
		$settings['time_format']             = get_option( 'afx_time_format' );
		$settings['currency']                = get_option( 'afx_currency' );
		$settings['reminder_hours']          = get_option( 'afx_reminder_hours' );
		$settings['payment_method']          = get_option( 'afx_payment_method' );
		$settings['paypal_prod_clientid']    = get_option( 'afx_paypal_prod_clientid' );
		$settings['paypal_prod_secret']      = get_option( 'afx_paypal_prod_secret' );
		$settings['paypal_sandbox_clientid'] = get_option( 'afx_paypal_sandbox_clientid' );
		$settings['paypal_sandbox_secret']   = get_option( 'afx_paypal_sandbox_secret' );
		$settings['is_paypal_sandbox']       = get_option( 'afx_is_paypal_sandbox' );

		$settings['background_color'] = get_option( 'afx_background_color' );
		$settings['font_color']       = get_option( 'afx_font_color' );
		$settings['font_size']        = get_option( 'afx_font_size' );

		return $settings;
	}

	/**
	 * Save settings
	 *
	 * @param [type] $settings
	 * @return void
	 */
	public static function save( $settings ) {
		$data = array(
			'success' => true,
			'errors'  => array(),
		);

		// save settings
		update_option( 'afx_business_name', $settings['business_name'] );
		update_option( 'afx_instructions', $settings['instructions'] );
		update_option( 'afx_week_start_on', $settings['week_start_on'] );
		update_option( 'afx_time_format', $settings['time_format'] );
		update_option( 'afx_currency', $settings['currency'] );
		update_option( 'afx_reminder_hours', $settings['reminder_hours'] );
		update_option( 'afx_payment_method', $settings['payment_method'] );
		update_option( 'afx_paypal_prod_clientid', $settings['paypal_prod_clientid'] );
		update_option( 'afx_paypal_prod_secret', $settings['paypal_prod_secret'] );
		update_option( 'afx_paypal_sandbox_clientid', $settings['paypal_sandbox_clientid'] );
		update_option( 'afx_paypal_sandbox_secret', $settings['paypal_sandbox_secret'] );
		update_option( 'afx_is_paypal_sandbox', $settings['is_paypal_sandbox'] );

		update_option( 'afx_background_color', $settings['background_color'] );
		update_option( 'afx_font_color', $settings['font_color'] );
		update_option( 'afx_font_size', $settings['font_size'] );

		return $data;
	}

	/**
	 * Get setting by name
	 *
	 * @param [type] $name
	 * @return void
	 */
	public static function get( $name ) {
		return get_option( 'afx_' . $name );
	}

	public static function getPayPalUrl() {
		$is_paypay_sandbox = get_option( 'afx_is_paypal_sandbox' );

		if ( $is_paypay_sandbox ) {
			return 'https://api.sandbox.paypal.com/v1/';
		}

		return 'https://api.paypal.com/v1/';
	}
}
