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
define( 'DB_NAME', 'wp_dropshipme' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '-}Kfi7hLcs:EljHC$>>{9Z9[gE^12?(g8F1VdUfHQ3q1B[<;F#8qIAE9ks5txQcE' );
define( 'SECURE_AUTH_KEY',  '?P.,AGtC@}Q9l,sqI0V{bF4^4#@]ZFlC}V3!6)4CUwZ~$;~7ZB;p^%k7> {I$#qQ' );
define( 'LOGGED_IN_KEY',    'Zw(UFVBmHR2S)bbB[yqY|Npv,![,H>tt*E?iWYe3GK[no-gNk:64?9+e#8-9c>{q' );
define( 'NONCE_KEY',        '@[[Lfje`@HNyF[l{!Q2.T3srO3!IRx1W9aa$<JDxo,hA{s32xo]Y?;5{/plPTgq ' );
define( 'AUTH_SALT',        'N gWS{a2[A=y6Or`2*;<);<E/(O)1G853vA@qcbf~ 0%aNaR.LZ?pp?Uj*Y[Zhb3' );
define( 'SECURE_AUTH_SALT', ',.9HE>GcrdC_botk]?vsPuxOWqDtaXp-!pp&[hnlH9eYlm_%Ot2`g@fM]sC7VGA?' );
define( 'LOGGED_IN_SALT',   'Ms]+m+;KQL}v4YCea=WQWPJJz5}Zb)dWn6N_e=^H*QN#=#;RvMP2Fvo@GqW %2<K' );
define( 'NONCE_SALT',       'D5weTn#JY:4{n.`)d?8ggrf[7IluGTk$vf,):m)g!hHQuDR{M%{-W$],(,91lt]C' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
