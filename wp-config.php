<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_booking');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'R},c}NDt`*Z#<p[{g(aRJ6<[yqR3PFfVSuG{7nuDV?>tX&XF{&wayhtH3UIJ l~i');
define('SECURE_AUTH_KEY',  '[-*.4w_(qmu?NUFXi#t(-l6D=0uj^C~?pt>x$=;zSvbxAau($vr+R<tyW}9.,:d.');
define('LOGGED_IN_KEY',    'bE8rl41l(nGkrQYw;/REfMD@eraV)Df!&8bI-R0|I>_FfrvSYjR~b-vmOB$iYs9U');
define('NONCE_KEY',        '8g -k}/n+=*AdMyc% G%V[]dW=71R;4L[.<>S_IU#Ry3;*o)](2;A15_2b|6KR=1');
define('AUTH_SALT',        'jK1bw2{&<i/*LHZlA.m3G-GzTJ}X)h07fax$)aI1E}y.&@Koe oXgH~W8{gFTBCG');
define('SECURE_AUTH_SALT', 'Gj2UjXxRo_vGeltYSL-UA]xtnpE>-Ly^Gb}C=m]6yN-MQ^R)JvLg,3Qc)VhyCU[7');
define('LOGGED_IN_SALT',   '2;cU&}!j&%f[1YUFG/)TT|DR!8Ok2r6uPH-?H?}!33[UHmkjX`eXhe9#=gr4Z<p2');
define('NONCE_SALT',       '4$#^u*Ke^nsv!Tx^ppW+ g$&GjyRxT-$FeZ.3QIK*NuWFc7}xEKfR;^y,(iMDTZ6');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
