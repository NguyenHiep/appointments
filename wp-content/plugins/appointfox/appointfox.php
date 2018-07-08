<?php

/**
 * Plugin Name: AppointFox - The Best WordPress Booking Plugin
 * Plugin URI: http://wpbooking-plugin.com
 * Description: An easy-to-use and user friendly appointment booking tool for your WordPress website.
 * Version: 1.2
 * Author: Neptune Plugins
 * Author URI: http://neptuneplugins.com
 * Text Domain: appointfox
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) or die( 'Access denied !' );

/**
 * Global Variables
 */
define( 'AFX_NAME', 'appointfox' );
define( 'AFX_VERSION', '1.2' );
define( 'AFX_DB_VERSION', '1.0.1' );
define( 'AFX_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'AFX_OPTIONS_GROUP', 'afx_common_options' );

// plugin path
define( 'AFX_PATH', plugin_dir_path( __FILE__ ) );

// basename AppointFox/AppointFox.php
define( 'AFX_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// main file - AppointFox.php
define( 'AFX_PLUGIN_FILE', __FILE__ );

require 'vendor/autoload.php';

require_once 'includes/functions.php';

// Plugin update checker
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'http://wpbooking-plugin.com/updates/?action=get_metadata&slug=appointfox', // Metadata URL.
	__FILE__, // Full path to the main plugin file.
	'appointfox' // Plugin slug. Usually it's the same as the name of the directory.
);

// plugin entry point
AFX_setup_plugin();
