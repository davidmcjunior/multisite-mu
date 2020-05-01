<?php

/**
 * Plugin Name: Exactech Class Library
 * Description: Includes support class files and objects for Exactech WP plugins.
 */

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SITE_LIBRARY_DIR', dirname( __FILE__ ) . '/exac-lib/' );
define( 'SITE_PLUGIN_ICON_URI', get_site_url() . '/wp-admin/images/generic.png' );

/**
 * This can hopefully go away once OIDC login is in production...
 */
define( 'LEGACY_SSO_PLUGIN', 'miniorange-saml-20-single-sign-on/login.php' );

/**
 * MAYBE someday inline HTML script elements can be hardened by nonce-ing them.
 * Probably can't herd these particular cats w/o Impreza doing it...
 */
define( 'SITE_CSP_NONCE', generate_nonce() );

/**
 * We need early access to FWP() to run its indexer...
 */
if ( ! defined( 'FACETWP_DIR' ) ) {
	define( 'FACETWP_DIR', ABSPATH . 'wp-content/plugins/facetwp' );
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once SITE_LIBRARY_DIR . 'autoload.php';


/**
 * Global singleton(s)...
 */
$network = \Exac\Multisite\Network::get_instance();


/**
 * @param mixed $details
 */
function exac_log( $details )
{
	global $wpdb;

	$wpdb->insert( 'wp_exac_log', array( 'details' => json_encode( $details ) ) );
}


/**
 * @return bool|string
 */
function generate_nonce()
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";

	return md5( substr( str_shuffle( $chars ), 0, 24 ) );
}