<?php

/* Exit if accessed anywhere but site-lib.php */
if ( ! defined( 'ABSPATH' ) || ! defined( 'SITE_LIBRARY_DIR' ) ) {
	exit;
}

/**
 * Include all files needed to run Site plugins. (Can't be scanned
 * recursively, because loading order matters.)
 */
$dirs = array(
	'exceptions/',
	'traits/',
	'',
	'auth/',
	'auth/api/',
	'auth/group-mappers/',
	'auth/oidc/',
	'utils/',
	'controllers/',
	'https/',
	'queries/terms/select/',
	'queries/terms/insert/',
	'queries/posts/select/',
	'queries/posts/select/us/',
	'queries/posts/delete/',
	'queries/posts/update/',
	'queries/posts/insert/',
	'queries/options/select/',
	'queries/options/update/',
	'multisite/interfaces/',
	'multisite/',
	'multisite/sites/',
	'multisite/copiers/',
	'custom/',
	'custom/terms/',
	'custom/terms/meta/',
	'custom/posts/',
	'custom/posts/acf/',
	'custom/shortcodes/',
	'custom/taxonomies/',
	'api/',
	'ebucks/',
	'payment-gateways/',
	'payment-gateways/traits/',
	'payment-gateways/fields/',
	'shortcodes/',
	'shortcodes/iframes/',
	'shortcodes/iframes/sales-reports/'
);


/**
 * Class Library_Classfile_Autoloader
 */
class Library_Classfile_Autoloader
{

	/**
	 * Takes an array of directories and reads in all .php files within.
	 *
	 * @param string[] $dirs
	 */
	public function load( $dirs = array() )
	{
		foreach ( $dirs as $dir ) {
			$dir   = SITE_LIBRARY_DIR . $dir;
			$files = null;

			if ( is_dir( $dir ) ) {
				$files = scandir( $dir );
			}

			if ( ! is_array( $files ) ) {
				continue;
			}

			// remove . and ..
			array_shift( $files );
			array_shift( $files );

			foreach ( $files as $file ) {
				if ( 'autoload.php' === $file ) {
					continue;
				}

				$file = $dir . $file;

				if ( file_exists( $file ) && ! is_dir( $file ) ) {
					require_once $file;
				}
			}
		}
	}

}

( new Library_Classfile_Autoloader() )->load( $dirs );