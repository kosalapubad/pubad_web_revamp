<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pubad' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Qe%}C1ud/*@>,zegQw]5D1 6hPDvs?+c`uzeRu(9h^zGAva1@^[/C7_C<%(A[ {|' );
define( 'SECURE_AUTH_KEY',  '((ab25O~~Z%AHq%B/ibK~)8WL]Xy4}OJVqhqIp5%[ds1V=i,PvR]y]JDc*}1b0PR' );
define( 'LOGGED_IN_KEY',    '[xA4w:cRWc}NAdp9Uq;XUUOlH5PY*em6lkeN[6|@}|yZRG0a#}gPs5l%8twl(sj}' );
define( 'NONCE_KEY',        'm}40 %:/MirD^~L1N0jZ.^L[ep7NyV*`}5bcopLlb>};,]%p0FyJ`L1hG*T{p0N%' );
define( 'AUTH_SALT',        'M=N}/@3Hj-]M.v[gsNKX#0Xr4(D?D?_Dpn$.z&I38|m }Q{x.Wz1hEEht5;O0 +.' );
define( 'SECURE_AUTH_SALT', 's6X6YUqsFqZ<o*-Z|;^#3YByb8K(O]=-M:Z04PF$o7h7kB[dYpjvLJS,.*lOCrN9' );
define( 'LOGGED_IN_SALT',   'g1v*VqBf)SIvCdgP!73JN}JYyQr>Eii|6`s,{U,V9isH4tm=H#Oo{dAS4n+>tZo!' );
define( 'NONCE_SALT',       'm.~[zVgC VO]YgO)v9)&0l+pa+Lk$43?}N{!4x`{^Zu{su+9O:2c:;Ra!eg{3f]B' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
