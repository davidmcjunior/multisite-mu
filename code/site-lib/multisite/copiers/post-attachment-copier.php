<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 4/13/17
 * Time: 1:43 PM
 */

namespace Site\Multisite\Copiers;

use Site\Multisite\Copier;
use Site\Multisite\Post;
use Site\Queries\Posts\Select\Post_Meta_Query;
use Site\Queries\Posts\Select\Post_Query;

/**
 * Class Post_Attachment_Copier
 * @package Site\Multisite\Copiers
 */
class Post_Attachment_Copier extends Copier
{

	const META_KEY = '_wp_attached_file';

	/**
	 * Post_Attachment_Copier constructor.
	 *
	 * @param \Site\Multisite\Site $source_site
	 * @param \Site\Multisite\Site $target_site
	 */
	public function __construct( $source_site, $target_site  )
	{
		parent::__construct( $source_site, $target_site );

		$this->table = $this->target_prefix . 'posts';
	}


	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function copy_single( $data )
	{
		$db            = $this->db();
		$new_id        = null;
		$org_post_id   = $data[ 'org_post_id' ];
		$new_parent_id = $data[ 'new_parent_id' ];
		$meta_table    = $this->target_prefix . 'postmeta';
		$post_table    = $this->target_prefix . 'posts';

		$attachment = ( new Post_Query( $this->source_site ) )
			->execute( array( 'post_id' => $org_post_id ) );

		if ( null === $attachment ) {
			return null;
		}

		// Does this same 'attachment' already exist here?
		$target_exists = ( new Post_Query( $this->target_site ) )
			->execute( array( 'guid' => $attachment[ 'guid' ] ) );

		if ( is_array( $target_exists ) ) {
			return $target_exists[ 'ID' ];
		}

		$attachment[ 'post_parent' ] = $new_parent_id;
		array_shift( $attachment );

		$db->insert( $post_table, $attachment );
		$new_id = $db->insert_id;

		$meta = ( new Post_Meta_Query( $this->source_site ) )
			->execute( array( 'post_id' => $org_post_id, 'sorted' ) );

		foreach ( $meta as $key => $val ) {
			$data = array(
				'post_id'    => $new_id,
				'meta_key'   => $key
			);

			$db->delete( $meta_table, $data );

			$data[ 'meta_value' ] = $val;

			$db->insert( $meta_table, $data );
		}

		Post::insert_content_meta( $this->target_site->id, $new_id );

		return $new_id;
	}


	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function copy_all( $data )
	{
		// TODO: Implement copy_all() method.
	}

}