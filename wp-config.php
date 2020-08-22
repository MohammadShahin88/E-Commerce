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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'second_project' );

/** MySQL database username */
define( 'DB_USER', 'tamim' );

/** MySQL database password */
define( 'DB_PASSWORD', 'EGnHB32AKBztxyvH' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'M`0pJ2JXF?vEC{IhB[.v=IoJO;?;eUrCx~q+BOz`<BgC#{tL]i~3}_mr~K7: mu3' );
define( 'SECURE_AUTH_KEY',  'wde?t __o0*fK#2CDjg][l*wT?azG~90]DRJN*5CYeU r]RN_lE$U#b._N*aG+Ec' );
define( 'LOGGED_IN_KEY',    '.K7l.1jgx2X-L9H^Sn [bsWTF#*x_Wx_|VaRR6Ea&r7wZd[IcFVEplLD%Gvx[I*@' );
define( 'NONCE_KEY',        '@yg:`Wu,o#TcwW$wY6[^7]3auou26Z!|07s9uy$%I~x4^qA1@$gJN}$svlt^*3gU' );
define( 'AUTH_SALT',        '5t:@E0zO&+-(sBstBz%:Gi:<R[AoP}so:3_UQ7$hm?%W>ZZ8@7/DzI?Z?d-xlEWA' );
define( 'SECURE_AUTH_SALT', '[g?4JCUZeaE!~yNt_fFoy|3IU7Oi/_7{PfNOW#UMQuQg3ro{Rr6rfO@N1z(a&Z4}' );
define( 'LOGGED_IN_SALT',   'W|&9WR-vU|q)L5;}$KYx)eEAxrX%[f,:#b1})J&Oml/,tQ;ie`+/}dB$b30qzBwE' );
define( 'NONCE_SALT',       'C*zT/t9VKi[:P^ti@A}VaUiOS0eTh9K-P?q#e1~$j5YmJ`!h@LUJW/c9iT< Afq;' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ti_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
