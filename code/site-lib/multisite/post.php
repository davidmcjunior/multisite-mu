<?php

namespace Site\Multisite;
use Site\Queries\Posts\Select\Post_Query;

/**
 * Class Post
 *
 * @package Site\Multisite
 */
class Post
{

	const ATTACHMENT_KEY = '_wp_attached_file';

	const CONTENT_POST_META_KEY = 'content_meta';

	/**
	 * @var string[]
	 */
	public static $content_post_types = array(
		// 'item',
		// 'surgeon_loc',
		// 'financials',
		// 'post',
		// 'ajde_events',
		// 'product',
		// 'people',
		// 'locations',
		// 'board_meeting',
		// 'board_announcement',
		// 'board_event',
		// 'board_documents',
		// 'committee_members'
	);

	/**
	 * @var string[]
	 */
	public static $us_post_types = array(
		
		// 'surgeon_loc',
		// 'financials',
		// 'post'
	);


	/**
	 * Get a post if it exists, false if not.
	 *
	 * @param int $content_post_id
	 * @param int $target_site_id
	 *
	 * @return array|false
	 */
	public static function get_target_post( $content_post_id, $target_site_id )
	{
		global $network;

		$content_post = ( new Post_Query( $network->content_site ) )
			->execute( array( 'post_id' => $content_post_id ) );

		if ( null === $content_post ) {
			return false;
		}

		$target_site = $network->get_site( $target_site_id );

		$target_post = ( new Post_Query( $target_site ) )
			->execute( array( 'guid' => $content_post[ 'guid' ] ) );

		if ( null === $target_post ) {
			return false;
		}

		return $target_post;
	}


	/**
	 * @param int $site_id
	 * @param int $post_id
	 * @param array $meta
	 */
	public static function insert_content_meta( $site_id, $post_id, $meta = array() )
	{
		switch_to_blog( $site_id );
		update_post_meta( $post_id, static::CONTENT_POST_META_KEY, $meta );
		restore_current_blog();
	}


	/**
	 * Static class -- no make any.
	 */
	private function __construct() {}

}
