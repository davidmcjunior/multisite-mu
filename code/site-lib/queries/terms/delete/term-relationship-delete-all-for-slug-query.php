<?php

namespace Site\Queries\Terms\Delete;

use Site\Query;

/**
 * Class Term_Relationship_Delete_All_For_Slug_Query
 * @package Site\Queries\Terms\Delete
 */
class Term_Relationship_Delete_All_For_Slug_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$slug = null;

		if ( ! array_key_exists( 'slug', $data ) ) {
			return;
		}

		$db  = $this->db();
		$pre = $this->site->get_prefix();

		$sql = $db->prepare( "
			SELECT CONCAT('(', GROUP_CONCAT(tr.object_id), ')')
			FROM {$pre}term_relationship tr 
			INNER JOIN {$pre}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$pre}terms t ON t.term_id = tt.term_id
			WHERE t.slug = %s",
			array( $slug )
		);

		$ids = $db->get_results( $sql, ARRAY_A );

		if ( $ids ) {
			$db->query( "
				DELETE FROM {$pre}term_relationships WHERE object_id IN {$ids}",
				array()
			);
		}

	}

}