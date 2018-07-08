<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

afx_uninstall_plugin();

/**
 * tasks to do on plugin uninstall
 *
 * @ since 1.0.0
 */
function afx_uninstall_plugin() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		if ( false == is_super_admin() ) {
			return;
		}
		$blogs = wp_get_sites();
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			afx_delete_tables();
			afx_delete_site_options();
			afx_delete_cron();
			// restore_current_blog();
		}
	} else {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		afx_delete_tables();
		afx_delete_site_options();
		afx_delete_cron();
	}
}

/**
 * Delete tables
 *
 * @return void
 */
function afx_delete_tables() {
	// Remove tables
	global $wpdb;
	$table_name = $wpdb->prefix . 'afx_appointments';
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query( $sql );
	$table_name = $wpdb->prefix . 'afx_calendars';
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_calendars_days';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_calendars_staffs';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_categories';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_customers';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_notifications';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_payments';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_sent_notifications';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_services';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_staffs';
	$wpdb->query( $sql );
	$sql        = "DROP TABLE IF EXISTS $table_name;";
	$table_name = $wpdb->prefix . 'afx_staffs_services';
	$wpdb->query( $sql );
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query( $sql );
}

/**
 * Delete options
 *
 * @return void
 */
function afx_delete_site_options() {
	// Remove settings
	delete_option( 'afx_db_version' );
	delete_option( 'afx_common_options' );
	delete_option( 'afx_business_name' );
	delete_option( 'afx_instructions' );
	delete_option( 'afx_week_start_on' );
	delete_option( 'afx_time_format' );
	delete_option( 'afx_currency' );
	delete_option( 'afx_reminder_hours' );
	delete_option( 'afx_payment_method' );
	delete_option( 'afx_paypal_prod_clientid' );
	delete_option( 'afx_paypal_prod_secret' );
	delete_option( 'afx_paypal_sandbox_clientid' );
	delete_option( 'afx_paypal_sandbox_secret' );
	delete_option( 'afx_is_paypal_sandbox' );

	delete_option( 'afx_background_color' );
	delete_option( 'afx_font_color' );
	delete_option( 'afx_font_size' );

	// Remove settings - Multisites
	delete_site_option( 'afx_db_version' );
	delete_site_option( 'afx_common_options' );
	delete_site_option( 'afx_business_name' );
	delete_site_option( 'afx_instructions' );
	delete_site_option( 'afx_week_start_on' );
	delete_site_option( 'afx_time_format' );
	delete_site_option( 'afx_currency' );
	delete_site_option( 'afx_reminder_hours' );
	delete_site_option( 'afx_payment_method' );
	delete_site_option( 'afx_paypal_prod_clientid' );
	delete_site_option( 'afx_paypal_prod_secret' );
	delete_site_option( 'afx_paypal_sandbox_clientid' );
	delete_site_option( 'afx_paypal_sandbox_secret' );
	delete_site_option( 'afx_is_paypal_sandbox' );

	delete_site_option( 'afx_background_color' );
	delete_site_option( 'afx_font_color' );
	delete_site_option( 'afx_font_size' );

}

/**
 * Delete cron
 *
 * @return void
 */
function afx_delete_cron() {
	$timestamp = wp_next_scheduled( 'afx_run_notifications' );
	wp_unschedule_event( $timestamp, 'afx_run_notifications' );
}
