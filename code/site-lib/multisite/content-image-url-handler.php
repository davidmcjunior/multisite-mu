<?php

namespace Site\Multisite;

/**
 * An instances of this object rewrite resource URLs to reference the content domain.
 *
 * Class Content_Image_Url_Handler
 * @package Site\Multisite
 */
class Content_Image_Url_Handler
{

	/**
	 * Content_Image_Url_Handler constructor.
	 */
	public function __construct()
	{
		//add_action( 'wp_get_attachment_image_src', array( $this, 'wp_get_attachment_image_src_action' ), 10, 2 );
	}


	/**
	 * @param string[] $image
	 * @param int $attachment_id
	 *
	 * @return array
	 */
	public function wp_get_attachment_image_src_action( $image, $attachment_id )
	{
		/* We want this action performed if and only if this is a post from content...
		 */
		if ( ! get_post_meta( $attachment_id, Post::CONTENT_POST_META_KEY ) ) {
			return $image;
		}

		if ( 3 !== get_current_blog_id() ) {
			$image[ 0 ] = $this->format_url( $image[ 0 ] );
		}

		return $image;
	}


	/**
	 * @param string $url
	 *
	 * @return string
	 */
	private function format_url( $url )
	{
		/* Lord... So in order to avoid double-prepending the '<scheme>://' to the
		 * URL string, we first need to make sure it's not already there...
		 */
		$scheme = '';

		if ( false === strpos( $url, 'http' ) ) {
			$scheme .= is_ssl() ? 'https://' : 'http://';
		}

		/* Regurgitate the 'www' URL into a Content Site URL...
		 */
		$url = $scheme . ( new Url_Helper() )->get_content_url( $url );

		return $url;
	}

}

new Content_Image_Url_Handler();