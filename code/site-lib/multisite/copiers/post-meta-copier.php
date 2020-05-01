<?php

namespace Site\Multisite\Copiers;

use Site\Multisite\Copier;
use Site\Multisite\Destination_Mapper;
use Site\Multisite\Post;
use Site\Queries\Posts\Select\Post_Meta_Query;
use Site\Queries\Posts\Select\Post_Query;

/**
 * Class Post_Meta_Copier
 * @package Site\Multisite\Copiers
 */
class Post_Meta_Copier extends Copier
{

	/**
	 * @var Post_Attachment_Copier
	 */
	protected $attachment_copier;

	/**
	 * @var array
	 */
	public static $file_attachment_keys = array(
		// 'financials_attach_report', 'news_attach_file', 'library_item_file', '_thumbnail_id'
	);


	/**
	 * Post_Meta_Copier constructor.
	 *
	 * @param \Site\Multisite\Site $source_site
	 * @param \Site\Multisite\Site $target_site
	 */
	public function __construct( $source_site, $target_site )
	{
		parent::__construct( $source_site, $target_site );

		$this->attachment_copier = new Post_Attachment_Copier( $source_site, $target_site );
	}


	/**
	 * @param $data
	 *
	 * @return void
	 */
	public function copy_all( $data )
	{
		$source_post_id  = $data[ 'source_post_id' ];
		$target_post_id  = $data[ 'target_post_id' ];

		$post_meta_table = $this->target_prefix . 'postmeta';
		$att_meta_keys   = static::$file_attachment_keys;

		$attachments = array();
		$meta = null;

		if ( array_key_exists( 'meta', $data ) && is_array( $data[ 'meta' ] ) ) {
			$meta = $data[ 'meta' ];

		} else {
			$meta = ( new Post_Meta_Query( $this->source_site ) )
				->execute( array( 'post_id' => $source_post_id, 'sorted' ) );
		}

		if ( ! is_array( $meta ) ) {
			return;
		}

		$this->db()->delete(
			"{$this->target_prefix}postmeta", array( 'post_id' => $target_post_id )
		);

		foreach ( $meta as $key => $value ) {
			// Don't copy this meta -- it's only meaningful on Content...
			if ( Destination_Mapper::META_KEY === $key ) {
				continue;
			}

			$data = array(
				'post_id'    => $target_post_id,
				'meta_key'   => $key,
				'meta_value' => $value
			);

			if ( in_array( $key, $att_meta_keys ) ) {
				$attachments[] = $data;
				continue;
			}

			$this->db()->insert( $post_meta_table, $data );
		}

		if ( 0 < count( $attachments ) ) {
			foreach ( $attachments as $att ) {
				$att[ 'meta_value' ] =
					$this->copy_attachment( $att[ 'meta_value' ], $target_post_id );

				$this->db()->insert( $post_meta_table, $att );
			}
		}
	}


	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function copy_single( $data )
	{
		// TODO: Implement copy_single() method.
	}


	/**
	 * @param int $org_post_id
	 * @param int $new_parent_id
	 *
	 * @return int|null
	 */
	private function copy_attachment( $org_post_id, $new_parent_id )
	{
		$data = array(
			'org_post_id'   => $org_post_id,
			'new_parent_id' => $new_parent_id
		);

		return $this->attachment_copier->copy_single( $data );
	}

}