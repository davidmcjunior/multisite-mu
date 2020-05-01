<?php

namespace Site\Multisite;

use Site\DB_User;
use Site\Queries\Posts\Select\Post_Destination_Meta_Query;
use Site\Queries\Posts\Update\Post_Destination_Meta_Update_Query;

/**
 * Class Destination_Mapper
 * @package Site\Multisite
 */
class Destination_Mapper extends DB_User
{

	const META_KEY = 'destination_sites';

	/**
	 * @var Site
	 */
	private $content_site;


	/**
	 * Destination_Mapper constructor.
	 *
	 * @param Site $content_site
	 */
	public function __construct( $content_site )
	{
		$this->content_site = $content_site;
	}


	/**
	 * @param int $post_id
	 *
	 * @return int[]
	 */
	public function get_mapping( $post_id )
	{
		$site_ids = ( new Post_Destination_Meta_Query( $this->content_site ) )
			->execute( array( 'post_id' => $post_id ) );

		$site_ids = json_decode( $site_ids, true );

		return $site_ids;
	}


	/**
	 * @param int $post_id
	 * @param int[] $site_ids
	 */
	public function set_mapping( $post_id, $site_ids )
	{
		$site_ids = array_unique( $site_ids );
		$site_ids = json_encode( $site_ids );

		$data = array(
			'post_id'      => $post_id,
			'destinations' => $site_ids
		);

		( new Post_Destination_Meta_Update_Query( $this->content_site ) )
			->execute( $data );
	}


	/**
	 * @param int $post_id
	 * @param int[] $new_site_ids
	 */
	public function map_post_to_destinations( $post_id, $new_site_ids )
	{
		$site_ids = $this->get_mapping( $post_id );

		if ( is_array( $site_ids ) ) {
			$site_ids = array_merge( $site_ids, $new_site_ids );

		} else {
			$site_ids = $new_site_ids;
		}

		$this->set_mapping( $post_id, $site_ids );
	}


	/**
	 * @param int $post_id
	 * @param int[] $site_ids
	 */
	public function unmap_post_to_destinations( $post_id, $site_ids )
	{
		$old_meta = $this->get_mapping( $post_id );
		$new_meta = array();

		if ( ! is_array( $old_meta ) ) {
			return;
		}

		foreach ( $old_meta as $id ) {
			if ( ! in_array( $id, $site_ids) ) {
				$new_meta[] = $id;
			}
		}

		$this->set_mapping( $post_id, $new_meta );
	}

}