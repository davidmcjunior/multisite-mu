<?php

namespace Site\Multisite;


use Site\DB_User;
use Site\Queries\Posts\Select\Post_Query;
use Site\Queries\Posts\Select\Posts_For_Terms_Query;
use Site\Queries\Posts\Select\Us\Us_Posts_Untagged_Query;
use Site\Queries\Posts\Delete\Post_Meta_And_Relationships_Delete_Query;

/**
 * Class Site_Cleaner
 * @package Site\Multisite
 */
class Site_Cleaner extends DB_User
{

	/**
	 * @var Site
	 */
	protected $source_site;

	/**
	 * @var Site
	 */
	protected $target_site;


	/**
	 * Copier constructor.
	 *
	 * @param Site $source_site
	 * @param Site $target_site
	 */
	public function __construct( $source_site, $target_site )
	{
		$this->source_site = $source_site;
		$this->target_site = $target_site;

		return $this;
	}


	/**
	 * Removes all attachment data from the target site.
	 *
	 * @param array $data
	 *
	 * @return $this
	 */
	public function clean_attachments( $data )
	{
		$attachments = ( new Post_Query( $this->target_site ) )
			->execute( array( 'post_type' => 'attachment', 'ids_only' ) );

		$ids = array_column( $attachments, 'ID' );

		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			( new Post_Meta_And_Relationships_Delete_Query( $this->target_site ) )
				->execute( array( 'post_ids' => $ids ) );
		}

		return $this;
	}


	/**
	 * Removes all term data from target site for an array of terms.
	 *
	 * @param array $data
	 *
	 * @return $this
	 */
	public function clean_term_data( $data )
	{
		$terms  = $data[ 'terms' ];
		$target = $this->target_site;

		$this->remove_us_posts( $target );

		$post_ids = ( new Posts_For_Terms_Query( $target ) )
			->execute( array( 'terms' => $terms, 'column_only' => 'ID' ) );

		if ( ! is_array( $post_ids ) ) {
			return null;
		}

		( new Post_Meta_And_Relationships_Delete_Query( $target ) )
			->execute( array( 'post_ids' => $post_ids ) );

		return $this;
	}


	/**
	 * Removes US-specific posts from target site.
	 *
	 * @param Site $target
	 *
	 * @return $this
	 */
	private function remove_us_posts( $target )
	{
		$terms = array( 'www-site-com', 'us' );
		$types = array( 'financials', 'surgeon_loc', 'item' );

		$us_posts = ( new Posts_For_Terms_Query( $target ) )
			->execute( array( 'terms' => $terms, 'post_types' => $types, 'column_only' => 'ID' ) );

		$untagged = ( new Us_Posts_Untagged_Query( $target ) )
			->execute( array( 'include_acf' => true, 'ids_only' ) );

		$us_posts = array_merge( $us_posts, $untagged );
		$us_posts = array_unique( $us_posts );

		if ( ! empty( $us_posts ) ) {
			( new Post_Meta_And_Relationships_Delete_Query( $target ) )
				->execute( array( 'post_ids' => $us_posts ) );
		}

		return $this;
	}

}