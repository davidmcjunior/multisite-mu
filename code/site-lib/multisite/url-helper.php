<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 4/22/17
 * Time: 5:14 PM
 */

namespace Site\Multisite;
use Site\Multisite\Site;


/**
 * Class Url
 *
 * @package Site\Multisite
 */
class Url_Helper
{

	/**
	 * @return string
	 */
	public function get_404_link()
	{
		$url = $_SERVER[ 'HTTP_HOST' ] . '/error-404';

		return $url;
	}


	/**
	 * Strips the protocol scheme from a URL (e.g., 'http://' or 'https://').
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function strip_scheme( $url )
	{
		$separator = '://';

		if ( false === strpos( $url, $separator ) ) {
			return $url;
		}

		$parts = explode( $separator, $url );

		return array_pop( $parts );
	}


	/**
	 * * Re-formats URL strings to point to content site.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function get_content_url( $url )
	{
		if ( empty( $url ) ) {
			return $this->get_404_link();
		}

		/*
		 * URL parts...
		 */
		$content_id  = Site::CONTENT_ID;
		$content_dir = '/wp-content/uploads/';

		/*
		 * Get rid of the scheme substring and split the remaining URL
		 * into 2 parts separated by the content directory: the base
		 * site URL and the stub (post_name)...
		 */
		$parts = explode( $content_dir, $url );
		$base  = array_shift( $parts );
		$stub  = array_shift( $parts );

		/*
		 * Redirect to the Staging URL is this is a dev site (why dupe the content?)...
		 */
		if ( true === Site::is_dev_domain( $base ) ) {
			$base = 'content.site.staging.wpengine.com';

		} else {
			$base = get_site_url( $content_id );
		}

		/*
		 * Get rid of the site/** substring prepended to the stub...
		 */
		$stub = $this->strip_site_prepend( $stub );

		/*
		 * Add the content site uploads path...
		 */
		$stub = $content_dir . 'sites/' . $content_id . '/' . $stub;

		/*
		 * Put the pieces back together...
		 */
		$url = $base . $stub;

		return $url;
	}


	/**
	 * @param string $stub
	 *
	 * @return string
	 */
	public function strip_site_prepend( $stub )
	{
		return preg_replace( '/sites\/(\d+)\//', '', $stub );
	}

}