<?php

namespace Site\Multisite\Copiers;

use Site\Multisite\Destination_Mapper;
use Site\Multisite\Post;
use Site\Multisite\Site;
use Site\Multisite\Copier;
use Site\Queries\Posts\Select\Post_Query;
use Site\Queries\Posts\Select\Posts_For_Terms_Query;
use Site\Queries\Posts\Select\Us\Us_Posts_Untagged_Query;

/**
 * Class Post_Copier
 * @package Site\Multisite\Copiers
 */
class Post_Copier extends Copier
{

	/**
	 * @var Destination_Mapper
	 */
	private $destination_mapper;

	/**
	 * @var Post_Relationship_Copier
	 */
	private $relationship_copier;

	/**
	 * @var Post_Meta_Copier
	 */
	private $meta_copier;


	/**
	 * Post_Copier constructor.
	 *
	 * @param Site $source_site
	 * @param Site $target_site
	 */
	public function __construct( $source_site, $target_site = null )
	{
		parent::__construct( $source_site, $target_site );

		$this->table = $this->target_prefix . 'posts';

		$this->destination_mapper  = new Destination_Mapper( $source_site );
		$this->relationship_copier = new Post_Relationship_Copier( $source_site, $target_site );
		$this->meta_copier         = new Post_Meta_Copier( $source_site, $target_site );
	}


	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function copy_all( $data )
	{
		$terms   = $data[ 'terms' ];
		$types   = $data[ 'post_types' ];
		$source  = $this->source_site;
		$target  = $this->target_site;
		$new_pre = $this->target_prefix;

		if ( array_key_exists( 'terms', $data )  ) {
			$terms = $data[ 'terms' ];
		}

		if ( array_key_exists( 'post_types', $data ) ) {
			$types = $data[ 'post_types' ];
		}

		$posts = $this->get_posts( $source, $types, $terms );

		if ( in_array( 'www-site-com', $terms ) ) {
			$us_posts = ( new Us_Posts_Untagged_Query( $source ) )
				->execute( array( 'include_acf' ) );

			$posts = array_merge( $posts, $us_posts );

			// Serialize all the posts, ditch the duplicates, then unserialize...
			$posts = array_map(
				'unserialize', array_unique( array_map( 'serialize', $posts ) )
			);
		}

		foreach ( $posts as $post ) {
			$source_post_id = $post['ID'];

			$this->db()->insert( "{$new_pre}posts", $this->shift( $post ) );
			$target_post_id = $this->db()->insert_id;

			$this->copy_relationships( $source_post_id, $target_post_id )
				->copy_post_meta( $source_post_id, $target_post_id )
				->map_destination( $source_post_id, $target->domain );

			Post::insert_content_meta( $target->id, $target_post_id );
		}
	}


	/**
	 * @param array $post_data
	 *
	 * @return array
	 */
	private function shift( $post_data )
	{
		if ( array_key_exists( 'ID', $post_data ) ) {
			unset( $post_data[ 'ID' ] );
		}

		if ( array_key_exists( 'filter', $post_data ) ) {
			unset( $post_data[ 'filter' ] );
		}

		if ( array_key_exists( 'meta', $post_data ) ) {
			unset( $post_data[ 'meta' ] );
		}

		return $post_data;
	}


	/**
	 * Note: The destination mapping for this method is to be handled externally
	 *
	 * @param array $post_data
	 **
	 * @return int
	 */
	public function copy_single( $post_data )
	{
		$id     = $post_data[ 'ID' ];
		$table  = "{$this->target_prefix}posts";

		$post_meta = $post_data[ 'meta' ];
		$post_data = $this->shift( $post_data );

		$trgt_post_id = $this->check_for_existing( $post_data[ 'guid' ] );

		if ( false !== $trgt_post_id ) {
			$where = array( 'ID' => $trgt_post_id, 'guid' => $post_data[ 'guid' ] );
			$this->db()->update( $table, $post_data, $where );

		} else {
			$this->db()->insert( $table, $post_data );
			$trgt_post_id = $this->db()->insert_id;
		}

		$this->copy_relationships( $id, $trgt_post_id )
		     ->copy_post_meta( $id, $trgt_post_id, $post_meta );

		Post::insert_content_meta( $this->target_site->id, $trgt_post_id );

		return (int) $trgt_post_id;
	}


	/**
	 * @param string $guid
	 *
	 * @return int|false
	 */
	protected function check_for_existing( $guid )
	{
		$existing = ( new Post_Query( $this->target_site ) )
			->execute( array( 'guid' => $guid ) );

		if ( $existing ) {
			return $existing[ 'ID' ];
		}

		return false;
	}


	/**
	 * @param int $post_id
	 * @param string $domain
	 */
	protected function map_destination( $post_id, $domain )
	{
		$this->destination_mapper
			->map_post_to_destinations( $post_id, array( $domain ) );
	}


	/**
	 *
	 * @param Site $source
	 * @param string[] $types - method will fetch any post with a 'post_type' that's in this array
	 * @param string[] $terms - method will return any post with a taxonomy term 'slug' that's in this array
	 *
	 * @return array|null|object
	 */
	protected function get_posts( $source, $types = array(), $terms = array() )
	{
		$data = array(
			'post_types' => $types,
			'terms'      => $terms
		);

		return ( new Posts_For_Terms_Query( $source ) )
			->execute( $data );
	}


	/**
	 * @param int $source_post_id
	 * @param int $target_post_id
	 *
	 * @return $this
	 */
	protected function copy_relationships( $source_post_id, $target_post_id )
	{
		$data = array(
			'source_post_id' => $source_post_id,
			'target_post_id' => $target_post_id
		);

		$this->relationship_copier->copy_all( $data );

		return $this;
	}


	/**
	 * Copies the postmeta from the content site to the target site
	 * @param int $source_post_id
	 * @param int $target_post_id
	 * @param array $meta
	 *
	 * @return $this
	 */
	protected function copy_post_meta( $source_post_id, $target_post_id, $meta = null )
	{
		$data = array(
			'source_post_id' => $source_post_id,
			'target_post_id' => $target_post_id
		);

		if ( $meta ) {
			$data[ 'meta' ] = $meta;
		}

		$this->meta_copier->copy_all( $data );

		return $this;
	}

}