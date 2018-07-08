<?php
namespace AppointFox\Admin\Controller;

/**
 * Class for tasks to do during plugin activation and deactivation phases
 *
 * @package AppointFox
 * @subpackage admin
 * @since 1.0.0
 * @author Neptune Plugins
 */
class ActivatorController {
	/**
	 * Registers activate and deactivate hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		register_activation_hook(
			AFX_PLUGIN_FILE, array(
				$this,
				'activate',
			)
		);
		register_deactivation_hook(
			AFX_PLUGIN_FILE, array(
				$this,
				'deactivate',
			)
		);
		add_filter(
			'plugin_action_links', array(
				$this,
				'action_links',
			), 10, 2
		);

		add_action( 'plugins_loaded', array( $this, 'update_db' ) );
		// add_action( 'plugins_loaded', array( $this, 'generate_sql' ) );
		if ( strpos( $_SERVER['HTTP_HOST'], 'wpbooking-plugin.com' ) !== false ) {
			add_action(
				'wp_ajax_nopriv_afx-ajax-resetdatabase', array(
					$this,
					'reset_database',
				)
			);

			add_action(
				'wp_ajax_afx-ajax-resetdatabase', array(
					$this,
					'reset_database',
				)
			);
		}

	}

	/**
	 * Activate hook calls activate_for_blog
	 *
	 * @since 1.0.0
	 */
	public function activate( $networkwide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $networkwide ) {
				if ( false == is_super_admin() ) {
					return;
				}
				$blogs = get_sites();
				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->blog_id );
					$this->activate_for_blog();
					restore_current_blog();
				}
			} else {
				if ( false == current_user_can( 'activate_plugins' ) ) {
					return;
				}
				$this->activate_for_blog();
			}
		} else {
			if ( false == current_user_can( 'activate_plugins' ) ) {
				return;
			}
			$this->activate_for_blog();
		}
	}

	/**
	 * Create tables, populate data and add default settings
	 *
	 * @ since 1.0.0
	 */
	public function activate_for_blog() {
		// if exists remove it
		$this->create_tables();
		$this->create_index();
		$this->populate_data();
		$this->add_settings();
		$this->add_roles();

		// Register uninstall
		// register_uninstall_hook(
		// AFX_PLUGIN_FILE, array(
		// $this,
		// 'uninstall',
		// )
		// );
	}

	/**
	 * Uninstallation
	 *
	 * @return void
	 */
	public function uninstall() {
		// $this->clear_data();
	}

	/**
	 * Create tables
	 *
	 * @ since 1.0.0
	 */
	public function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// table - afx_appointments
		$table_name = $wpdb->prefix . 'afx_appointments';

		$sql = "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				unique_id varchar(50) DEFAULT NULL,
				service_id int(11) NOT NULL,
				staff_id int(11) NULL,
				start_datetime datetime DEFAULT NULL,
				end_datetime datetime DEFAULT NULL,
				customer_id int(11) NULL,
				note text NULL,
				price DECIMAL(10,2) NULL,
				is_paid TINYINT(1) NOT NULL DEFAULT '0',
				status varchar(50) DEFAULT NULL,
				PRIMARY KEY  (id)
            ) $charset_collate;";

		// table - afx_staffs
		$table_name = $wpdb->prefix . 'afx_staffs';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				wp_user_id bigint(20) UNSIGNED DEFAULT NULL,
				full_name varchar(255) DEFAULT NULL,
				email varchar(255) DEFAULT NULL,
				phone varchar(255) DEFAULT NULL,
				info text NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_customers
		$table_name = $wpdb->prefix . 'afx_customers';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				full_name varchar(255) DEFAULT NULL,
				email varchar(255) DEFAULT NULL,
				phone varchar(255) DEFAULT NULL,
				info text NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_categories
		$table_name = $wpdb->prefix . 'afx_categories';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				name varchar(255) DEFAULT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_staffs_services
		$table_name = $wpdb->prefix . 'afx_staffs_services';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				staff_id int(11) NULL,
				service_id int(11) NULL,
				PRIMARY KEY  (id)
                ) $charset_collate;";

		// table - afx_services
		$table_name = $wpdb->prefix . 'afx_services';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				category_id int(11) NULL,
				title varchar(255) NOT NULL,
				duration int(11) DEFAULT '900',
				price decimal(10,2) DEFAULT '0.0',
				color varchar(255) DEFAULT '#FFFFFF',
				access varchar(50) DEFAULT 'public',
				note text NULL,
				position int(11) DEFAULT '9999',
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_calendars
		$table_name = $wpdb->prefix . 'afx_calendars';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				name varchar(255) DEFAULT NULL,
				note text NULL,
				hour_sunday varchar(255) DEFAULT NULL,
				hour_monday varchar(255) DEFAULT NULL,
				hour_tuesday varchar(255) DEFAULT NULL,
				hour_wednesday varchar(255) DEFAULT NULL,
				hour_thursday varchar(255) DEFAULT NULL,
				hour_friday varchar(255) DEFAULT NULL,
				hour_saturday varchar(255) DEFAULT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_calendars_days
		$table_name = $wpdb->prefix . 'afx_calendars_days';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				calendar_id int(11) NOT NULL,
				day date NOT NULL,
				hour varchar(255) NOT NULL,
				PRIMARY KEY  (id)
                ) $charset_collate;";

		// table - afx_calendars_staffs
		$table_name = $wpdb->prefix . 'afx_calendars_staffs';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				calendar_id int(11) NULL,
				staff_id int(11) NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_notifications
		$table_name = $wpdb->prefix . 'afx_notifications';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				type varchar(50) NULL,
				name varchar(255) NULL,
				is_active TINYINT(1) NOT NULL DEFAULT '1',
				is_copy TINYINT(1) NOT NULL DEFAULT '0',
				subject varchar(255) NULL,
				message text NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_sent_notifications
		$table_name = $wpdb->prefix . 'afx_sent_notifications';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				ref_id int(11) NOT NULL,
				type varchar(50) NULL,
				name varchar(255) NULL,
				created datetime DEFAULT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// table - afx_payments
		$table_name = $wpdb->prefix . 'afx_payments';

		$sql .= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				txnid varchar(100) NOT NULL,
				payment_type varchar(20) NOT NULL,
				payment_amount decimal(10,2) NOT NULL,
 				payment_status varchar(25) NOT NULL,
				appointment_id int(11) NOT NULL,
				created datetime NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";

		// No of tables to create: 12
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'afx_db_version', AFX_DB_VERSION );
	}

	/**
	 * Create index
	 *
	 * @return void
	 */
	public function create_index() {
		global $wpdb;

		// table - afx_appointments
		$table_name = $wpdb->prefix . 'afx_appointments';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`service_id`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`start_datetime`);";
		$wpdb->query( $sql );

		// table - afx_staffs_services
		$table_name = $wpdb->prefix . 'afx_staffs_services';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`staff_id`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`service_id`);";
		$wpdb->query( $sql );

		// table - afx_calendars_days
		$table_name = $wpdb->prefix . 'afx_calendars_days';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`calendar_id`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`hour`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`day`);";
		$wpdb->query( $sql );

		// table - afx_calendars_staffs
		$table_name = $wpdb->prefix . 'afx_calendars_staffs';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`calendar_id`);";
		$wpdb->query( $sql );
	}

	/**
	 * Populate data
	 *
	 * @return void
	 */
	public function populate_data() {
		$afx_db_version = get_option( 'afx_db_version' );

		if ( $afx_db_version ) {
			global $wpdb;

			// table - afx_appointments
			$table_name = $wpdb->prefix . 'afx_appointments';
			$day1       = date( 'Y' ) . '-' . date( 'm' ) . '-16';
			$uniq1      = uniqid();
			$day2       = date( 'Y' ) . '-' . date( 'm' ) . '-05';
			$uniq2      = uniqid();

			$result = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `unique_id`, `service_id`, `staff_id`, `start_datetime`, `end_datetime`, `customer_id`, `note`, `price`, `is_paid`, `status`) VALUES
					(1, '$uniq1', 1, 1, '$day1 09:00:00', '$day1 09:30:00', 1, '', '39.00', 1, NULL),
					(2, '$uniq2', 2, 1, '$day2 10:00:00', '$day2 10:30:00', 1, '', '39.00', 0, NULL);";
				$wpdb->query( $sql );
			}

			// table - afx_calendars
			$table_name = $wpdb->prefix . 'afx_calendars';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `name`, `note`, `hour_sunday`, `hour_monday`, `hour_tuesday`, `hour_wednesday`, `hour_thursday`, `hour_friday`, `hour_saturday`) VALUES
				(1, 'Default', NULL, '9:00am-5:00pm', '9:00am-5:00pm', '9:00am-5:00pm', '9:00am-5:00pm', '9:00am-5:00pm', 'Closed', 'Closed');";
				$wpdb->query( $sql );
			}

			// table - afx_calendars_days
			$table_name  = $wpdb->prefix . 'afx_calendars_days';
			$day_closed1 = date( 'Y' ) . '-' . date( 'm' ) . '-18';
			$day_closed2 = date( 'Y' ) . '-' . date( 'm' ) . '-19';

			$result = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `calendar_id`, `day`, `hour`) VALUES
				(1, 1, '$day_closed1', 'Closed'),
				(2, 1, '$day_closed2', 'Closed');";
				$wpdb->query( $sql );
			}

			// table - afx_calendars_staffs
			$table_name = $wpdb->prefix . 'afx_calendars_staffs';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `calendar_id`, `staff_id`) VALUES
				(1, 1, 1);";
				$wpdb->query( $sql );
			}

			// table - afx_categories
			$table_name = $wpdb->prefix . 'afx_categories';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `name`) VALUES
				(1, 'Hair'),
				(2, 'Face');";
				$wpdb->query( $sql );
			}

			// table - afx_customers
			$table_name = $wpdb->prefix . 'afx_customers';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `full_name`, `email`, `phone`, `info`) VALUES
				(1, 'Alana M. Fields', 'test@wpbooking-plugin.com', '195790800', 'For testing only');";
				$wpdb->query( $sql );
			}

			// table - afx_notifications
			$table_name = $wpdb->prefix . 'afx_notifications';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `type`, `name`, `is_active`, `is_copy`, `subject`, `message`) VALUES
				(1, 'email', 'Initial Confirmation', 1, 0, 'New Appointment: %service% (%name%) on %datetime%', '<p>Hi %name%,</p><p>This is just an email to confirm your appointment. For reference, here\'s the appointment information:</p><p>Service: <strong>%service%</strong><br /> When: <strong>%datetime%</strong></p><p>Sincerely,<br /> Your friends at %business_name%</p>'),
				(2, 'email', 'Cancellation', 1, 0, 'Appointment Cancelled: %name% on %datetime%', '<p>Hi %name%,</p><p>The appointment you requested at %business_name% has been cancelled. For reference, here\'s the appointment information:</p><p>Service: <strong>%service%</strong><br /> When: <strong>%datetime%</strong></p><p>Sincerely,<br /> Your friends at %business_name%</p>'),
				(4, 'email', 'Payment Paid', 1, 0, 'Payment Paid: %service% (%name%) on %datetime%', '<p>Hi %name%,</p><p>This is just an email to confirm your payment. For reference, here\'s the payment information:</p><p>Service: <strong>%service%</strong><br /> When: <strong>%datetime%</strong></p><p><strong><span style=\"text-decoration: underline;\">Payment details:</span></strong><br /><br />Payment Method: <strong>%payment_method%</strong><br />Payment Amount: <strong>%payment_amount%</strong><br /> Payment Status: <strong>%payment_status%</strong><br /> Payment ID: <strong>%payment_id%</strong></p><p>Sincerely,<br /> Your friends at %business_name%</p>'),
				(3, 'email', 'Reminder', 1, 0, 'Appointment Reminder: %service% is on %datetime%', '<p>Hi %name%,</p><p>Just a friendly reminder that you have an appointment coming up soon! Here\'s the appointment information:</p><p>Service: <strong>%service%</strong> <br />When: <strong>%datetime%</strong> </p><p>Sincerely, <br />Your friends at %business_name%</p>'),
				(5, 'email', 'Pending Payment', 1, 0, 'Pending Payment: %service% (%name%) on %datetime%', '<p>Hi %name%,</p><p>Thank you for your booking. Your appointment is pending payment. Here\'s the appointment information:</p><p>Service: <strong>%service%</strong><br /> When: <strong>%datetime%</strong><br /> Price: <strong>%price%</strong></p><p><a href=\"%url%\">Click here to proceed with the payment</a></p><p>Sincerely,<br /> Your friends at %business_name%</p>');";
				$wpdb->query( $sql );
			}

			// table - afx_payments
			$table_name = $wpdb->prefix . 'afx_payments';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `txnid`, `payment_type`, `payment_amount`, `payment_status`, `appointment_id`, `created`) VALUES
				(1, 'PAY-6BR160329E7638330LKDIXNI', 'PayPal', '39.00', 'Paid', 1, '2018-02-16 07:44:38');";
				$wpdb->query( $sql );
			}

			// table - afx_services
			$table_name = $wpdb->prefix . 'afx_services';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `category_id`, `title`, `duration`, `price`, `color`, `access`, `note`, `position`) VALUES
				(1, 1, 'Hair Cut', 1800, '39.00', '#81d742', 'Public', 'For testing only', 9999),
				(2, 2, 'Facial Wash', 1800, '49.00', '#1e73be', 'Public', 'For testing only', 9999);";
				$wpdb->query( $sql );
			}

			// table - afx_staffs
			$table_name = $wpdb->prefix . 'afx_staffs';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `wp_user_id`, `full_name`, `email`, `phone`, `info`) VALUES
				(1, NULL, 'James M. McCray', 'test@wpbooking-plugin.com', '0192855000', 'For  testing only');";
				$wpdb->query( $sql );
			}

			// table - afx_staffs_services
			$table_name = $wpdb->prefix . 'afx_staffs_services';
			$result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1" );
			if ( $result == null ) {
				$sql = "INSERT INTO `$table_name` (`id`, `staff_id`, `service_id`) VALUES
				(1, 1, 1),
				(2, 1, 2);";
				$wpdb->query( $sql );
			}
		}
	}

	/**
	 * Deactivate hook - calls deactivate_for_blog
	 * for networkwide deactivation, do nothing
	 * for blog level, calls method for the current blog
	 *
	 * @since 1.0.0
	 */
	public function deactivate( $networkwide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( ! $networkwide ) {
				if ( false == current_user_can( 'activate_plugins' ) ) {
					return;
				}
				$this->deactivate_for_blog();
			}
		} else {
			if ( false == current_user_can( 'activate_plugins' ) ) {
				return;
			}
			$this->deactivate_for_blog();
		}
	}

	public function deactivate_for_blog() {
		// deactive stuff here
	}

	/**
	 * Add Settings link to plugin action links.
	 * Settings link is shown under the plugin in plugins page
	 *
	 * @since 1.0.0
	 *
	 * @param array  $links
	 *            - list of links to show
	 * @param string $file
	 *            - basefile for the plugin such as
	 *            share-on-social/share-on-social.php
	 * @return array $links - modified list of links
	 */
	public function action_links( $links, $file ) {
		// add Settings link only to this plugin
		if ( AFX_PLUGIN_BASENAME == $file ) {
			$settings_link = '<a href="' . get_bloginfo( 'wpurl' ) .
				'/wp-admin/admin.php?page=appointfox-settings">Settings</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Clear data
	 *
	 * @since 1.0.0
	 */
	public function clear_data() {
		// $post_id = Sos_Helper::get_locker_post_id('basic');
		// if ($post_id) {
		// wp_delete_post($post_id);
		// }
		// remove event
		$timestamp = wp_next_scheduled( 'afx_run_notifications' );
		wp_unschedule_event( $timestamp, 'afx_run_notifications' );

		// Remove settings
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

	}

	/**
	 * Adds default options
	 *
	 * @ since 1.0.0
	 */
	public function add_settings() {
		$options = get_option( AFX_OPTIONS_GROUP );
		if ( false == $options ) {
			$options = array(
				'version' => AFX_VERSION,
			);
			add_option( AFX_OPTIONS_GROUP, $options );
		} else {
			$options['version'] = AFX_VERSION;
			delete_option( AFX_OPTIONS_GROUP, $options );
		}
	}

	/**
	 * Update database
	 *
	 * @return void
	 */
	public function update_db() {
		$installed_ver = get_option( 'afx_db_version' );

		if ( version_compare( $installed_ver, AFX_DB_VERSION, '<' ) ) {

			// DB upgrade for version 1.0.0
			if ( version_compare( $installed_ver, '1.0.0', '==' ) ) {
				$this->update_db_1_0_1();
			}

			// update afx_db_version
			update_option( 'afx_db_version', AFX_DB_VERSION );
		}
	}

	/**
	 * Update database version 1.0.1
	 */
	public function update_db_1_0_1() {
		global $wpdb;

		//
		// UPDATE INDICES
		//
		// table - afx_appointments
		$table_name = $wpdb->prefix . 'afx_appointments';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`service_id`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`start_datetime`);";
		$wpdb->query( $sql );

		// table - afx_staffs_services
		$table_name = $wpdb->prefix . 'afx_staffs_services';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`staff_id`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`service_id`);";
		$wpdb->query( $sql );

		// table - afx_calendars_days
		$table_name = $wpdb->prefix . 'afx_calendars_days';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`calendar_id`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`hour`);";
		$wpdb->query( $sql );
		$sql = "ALTER TABLE $table_name ADD INDEX(`day`);";
		$wpdb->query( $sql );

		// table - afx_calendars_staffs
		$table_name = $wpdb->prefix . 'afx_calendars_staffs';
		$sql        = "ALTER TABLE $table_name ADD INDEX(`calendar_id`);";
		$wpdb->query( $sql );
	}

	/**
	 * Generate SQL statement for manual db creation
	 * for development purpose only
	 *
	 * @return void
	 */
	public function generate_sql() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// table - afx_appointments
		$table_name = $wpdb->prefix . 'afx_appointments';
		$sql        = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			txnid varchar(100) NOT NULL,
			payment_type varchar(20) NOT NULL,
			payment_amount decimal(10,2) NOT NULL,
			payment_status varchar(25) NOT NULL,
			appointment_id int(11) NOT NULL,
			created datetime NOT NULL,
			PRIMARY KEY  (id)
			) $charset_collate;";

		echo esc_html_e( $sql );
		die();
	}

	public function add_roles() {
		// Add capabilites to admin role
		$role = get_role( 'administrator' );
		$role->add_cap( 'manage_appointfox_appointments' );
		$role->add_cap( 'manage_appointfox_customers' );
		$role->add_cap( 'manage_appointfox_services' );
		$role->add_cap( 'manage_appointfox_staffs' );
		$role->add_cap( 'manage_appointfox_payments' );
		$role->add_cap( 'manage_appointfox_availability' );
		$role->add_cap( 'manage_appointfox_settings' );

		// Add staff role
		$result = add_role(
			'appointfox_staff',
			__( 'AppointFox Staff' ),
			array(
				'read'                           => true,
				'manage_appointfox_appointments' => true,
				'manage_appointfox_customers'    => true,
			)
		);

		// Add admin role
		$result = add_role(
			'appointfox_admin',
			__( 'AppointFox Administrator' ),
			array(
				'read'                           => true,
				'manage_appointfox_appointments' => true,
				'manage_appointfox_customers'    => true,
				'manage_appointfox_services'     => true,
				'manage_appointfox_staffs'       => true,
				'manage_appointfox_payments'     => true,
				'manage_appointfox_availability' => true,
				'manage_appointfox_settings'     => true,
			)
		);
	}

	public function reset_database() {
		// Remove settings
		delete_option( 'afx_db_version' );
		delete_option( 'afx_common_options' );
		delete_option( 'afx_business_name' );
		delete_option( 'afx_instructions' );
		delete_option( 'afx_week_start_on' );
		delete_option( 'afx_time_format' );
		delete_option( 'afx_currency' );
		delete_option( 'afx_reminder_hours' );
		// delete_option( 'afx_payment_method' );
		// delete_option( 'afx_paypal_prod_clientid' );
		// delete_option( 'afx_paypal_prod_secret' );
		// delete_option( 'afx_paypal_sandbox_clientid' );
		// delete_option( 'afx_paypal_sandbox_secret' );
		// delete_option( 'afx_is_paypal_sandbox' );
		delete_option( 'afx_background_color' );
		delete_option( 'afx_font_color' );
		delete_option( 'afx_font_size' );

		// Remove tables
		global $wpdb;
		$table_name = $wpdb->prefix . 'afx_appointments';
		$sql        = "TRUNCATE TABLE $table_name;";
		$wpdb->query( $sql );
		$table_name = $wpdb->prefix . 'afx_calendars';
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_calendars_days';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_calendars_staffs';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_categories';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_customers';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_notifications';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_payments';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_sent_notifications';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_services';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_staffs';
		$wpdb->query( $sql );
		$sql        = "TRUNCATE TABLE $table_name;";
		$table_name = $wpdb->prefix . 'afx_staffs_services';
		$wpdb->query( $sql );
		$sql = "TRUNCATE TABLE $table_name;";
		$wpdb->query( $sql );

		// Create tables
		$this->create_tables();

		// Populate data
		$this->populate_data();
	}
}
