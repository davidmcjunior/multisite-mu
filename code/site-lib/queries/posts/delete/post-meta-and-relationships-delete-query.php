<?php

namespace Site\Queries\Posts\Delete;

use Site\Query;

/**
 * Class Post_Meta_And_Relationships_Delete_Query
 * @package Site\Queries\Posts\Delete
 */
class Post_Meta_And_Relationships_Delete_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		if ( ! array_key_exists( 'post_ids', $data ) && $this->is_array_of_ints( $data[ 'post_ids' ] )) {
			return;
		}

		if ( count( $data[ 'post_ids' ] ) < 1) {
			return;
		}

		$db    = $this->db();
		$pre   = $this->site->get_prefix();
		$posts = '(' . implode( ',', $data[ 'post_ids' ] ) . ')';

		$db->query( "DELETE FROM {$pre}posts WHERE ID IN {$posts}" );
		$db->query( "DELETE FROM {$pre}postmeta WHERE post_id IN {$posts}" );
		$db->query( "DELETE FROM {$pre}term_relationships WHERE object_id IN {$posts}" );
	}

}