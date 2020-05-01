<?php

namespace Site\Queries\Posts\Select;

use Site\Query;

/**
 * Class Term_Relationships_Query
 * @package Site\Queries
 */
class Term_Relationships_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function execute( $data = array() )
	{
		$pre   = $this->site->get_prefix();
		$where = '';

		if ( ! array_key_exists( 'post_id', $data ) ) {
			return null;
		}

		if ( in_array( 'destinations_only', $data ) ) {
			$where .= " AND tt.taxonomy = 'destination'";
		}

		$id = $data[ 'post_id' ];

		$sql = $this->db()->prepare("
			SELECT t.slug, t.name, tt.term_taxonomy_id 
			FROM {$pre}term_relationships tr
			INNER JOIN {$pre}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$pre}terms t          ON t.term_id = tt.term_id
			WHERE tr.object_id = %d{$where}",
			array( $id )
		);

		return $this->db()->get_results( $sql, ARRAY_A );
	}

}