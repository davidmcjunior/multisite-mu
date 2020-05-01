<?php

// Include image.php
namespace Site\Multisite;

/**
 * Class Thumbnail_Handler
 * @package Site\Multisite
 */
class Thumbnail_Handler
{

	/**
	 * Thumbnail_Handler constructor.
	 */
	public function __construct()
	{
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}
	}


	/**
	 * @param int $post_id
	 *
	 * @return int|false
	 */
	private function generate_video_thumbnail( $post_id )
	{
		$url          = get_field( 'library_item_video_url', $post_id );
		$length_to_id = null;
		$vimeo_url    = array(
			'http'  => 'http://vimeo.com/',
			'https' => 'https://vimeo.com/'
		);

		if ( false !== strpos( $url, 'https:' ) ) {
			$length_to_id = strlen( $vimeo_url[ 'https' ] );

		} else if ( false !== strpos( $url, 'http:' ) ) {
			$length_to_id = strlen( $vimeo_url[ 'http' ] );
		}

		$id = substr( $url, $length_to_id );

		$response = wp_remote_get( $vimeo_url[ 'https' ] . 'api/v2/video/' . $id . '.json' );

		if ( \WP_Error::class === $response || ! is_array( $response ) ) {
			return false;
		}

		if ( array_key_exists( 'code', $response[ 'response' ] ) &&
		     404 === (int) $response[ 'response' ][ 'code' ] ) {
			return false;
		}

		$response  = array_shift( json_decode( $response[ 'body' ], ARRAY_A ) );
		$thumb_url = $response[ 'thumbnail_small' ];
		$upload    = null;

		// get the image binary data
		if ( ! $upload = wp_upload_bits( basename( $thumb_url ), null, $thumb_url ) ) {
			return false;
		}

		$file_path = $upload[ 'file' ];
		$file_name = basename( $file_path );
		$file_type = wp_check_filetype( $file_name, null );

		$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
		$wp_upload_dir    = wp_upload_dir( null, true );

		$attachment_data = array(
			'guid'           => $wp_upload_dir[ 'url' ] . '/' . $file_name,
			'post_mime_type' => $file_type[ 'type' ],
			'post_title'     => $attachment_title,
			'post_content'   => 'Hi there!',
			'post_status'    => 'inherit',
		);

		$attachment_id   = wp_insert_attachment( $attachment_data, $file_path, $post_id );
		$attachment_meta = wp_generate_attachment_metadata( $attachment_id, $file_path );
		wp_update_attachment_metadata( $attachment_id, $attachment_meta );

		return $attachment_id;
	}


	/**
	 * @param int $post_id
	 *
	 * @return bool
	 */
	private function generate_file_pdf( $post_id )
	{
		require_once ABSPATH . 'wp-content/plugins/regenerate-thumbnails/regenerate-thumbnails.php';

		$generator = \RegenerateThumbnails_Regenerator::get_instance( $post_id );
		$generator->regenerate();

		return true;
	}


	/**
	 * @param int $post_id
	 * @param string $resource_type
	 *
	 * @return bool
	 */
	public function generate_thumbnail( $post_id, $resource_type = '' )
	{
		if ( '' === $resource_type || ! in_array( $resource_type, array( 'Video', 'File' ) ) ) {
			$resource_type = get_field( 'resource_type', $post_id ); // Arg 2 probably not necessary...
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		if ( 'File' === $resource_type ) {
			return $this->generate_file_pdf( $post_id );

		} else if ( 'Video' === $resource_type ) {
			return $this->generate_video_thumbnail( $post_id );

		} else {
			return false;
		}
	}

}