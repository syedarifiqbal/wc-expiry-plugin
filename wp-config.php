<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'expiry_plugin' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         'lEPK@%p@%ks!u,8^aq^r!hA*Xp]%|4a]K6ia}#``u9tE8)=Tai<0P%3N!t>~$<6v' );
define( 'SECURE_AUTH_KEY',  '-GMtE1i3onsc39dwC2ulKBv!PPANY2~rvDsNyHG#{z/h kOMADU&fM$N9Rs-%yWs' );
define( 'LOGGED_IN_KEY',    '0hb{WdcKp+.-(gdA/D_)nH!=GAr96+gsj5;(VRPR])e3fCU&1 *H,e(.{=MPQq{W' );
define( 'NONCE_KEY',        'G]6Tq {Z{v%9>5Q?AU%8Eu_Q2O6VSlHK%j4b:4f<AxQiQ-4{~_H%1FxiIG(b[D+x' );
define( 'AUTH_SALT',        'ie5EQ;gix`@MJ7W|qHSG&sp]n%*-^[B_e@4O,vu80k5:%4C%{q?p_fV[Si{BB=K7' );
define( 'SECURE_AUTH_SALT', '5_gQ8e)J<& 32._)`}KD{l$HFz@L>$,@bV>I=/W|nh!A*ZMx?Z5tg`h/X;F6FrK)' );
define( 'LOGGED_IN_SALT',   'Z/t~0L{JAnT5z}.S1HzO,:BMe[x&K{SjtNQE+,J419PuhejNplN+T9>37cEJ]`JP' );
define( 'NONCE_SALT',       'Ozs(J!p7dK6It*S<v@c^wV@Ue9KA<Qj}B^1o700!nzCiNTYnw^f`$!<a/umA%S=k' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
