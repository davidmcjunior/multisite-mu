<?php

namespace Site\Queries\Posts\Insert;

use Site\Query;

/**
 * Class Term_Relationship_Insert_Query
 * Associates multiple posts with a single term/taxonomy
 *
 * @package Site\Queries\Posts\Insert
 */
class Term_Relationship_Insert_Query extends Query
{
	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function execute( $data = array() )
	{
		if ( ! array_key_exists( 'slug', $data ) ) {
			return null;
		}

		if ( ! array_key_exists( 'post_id', $data ) ) {
			return null;
		}

		$slug = $data[ 'slug' ];
		$id   = $data[ 'post_id' ];
		$pre  = $this->site->get_prefix();
		$db   = $this->db();

		$sql = $db->prepare( "
			INSERT IGNORE INTO {$pre}term_relationships (object_id, term_taxonomy_id, term_order)
			SELECT %d, tt.term_taxonomy_id, 0 FROM {$pre}term_taxonomy tt 
			INNER JOIN {$pre}terms t ON tt.term_id = t.term_id
			WHERE t.slug = %s",
			array( $id, $slug )
		);

		$db->query( $sql );
	}

}