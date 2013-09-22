<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '0~Xn|:UOQ^+R<s ZH%`^( {/(X1*,Hk)/MlyNV2T5*<oLzPxHN(pa#=:W>3-[DMI');
define('SECURE_AUTH_KEY',  'm-J+39SH&Oi7^jJ%|wM8^EHDr|n6IDx@<ANmH!?Mo/4;=*WtT9)%3S=JGHkH6rBL');
define('LOGGED_IN_KEY',    '}LoI%rZCC26bD?y-LPwa:bV+hN qAdy)5>Sz/$tE?CGvHOq0uBDOmS:Zza|/}A!m');
define('NONCE_KEY',        '/_6[3>Ms1;bSqL74nM+;/F>d?Q]]ACH*=S:GF^OABxqx|k8JS8CK|hh!sY3|HrCZ');
define('AUTH_SALT',        '.13t4D/=5oy%_P~d8+^t+]{|k[qS!a<`Q.ZUW-Jk+R|?_V-+Qi8t|=0YA]Bxf^.,');
define('SECURE_AUTH_SALT', '^0?+x~2++ J-%E--t+MY!U($h& [z?R|0!%Onh^?R%3+x#rDua=/p|bzd{`69ul4');
define('LOGGED_IN_SALT',   'sY^,=Wt/.<~,f]?/]e*Lb/lYR-68n]Qw0o$m/ox3q)D^iXa+LosU-/1OgGCgq40h');
define('NONCE_SALT',       '(r}po&U+b:A*2-+znf_|+)mfK(7-H03+-)CCAf0?I|2<=&bF##d]L?^4:>.,Ers[');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
