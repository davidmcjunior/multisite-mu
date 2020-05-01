<?php

namespace Site\Queries\Posts\Select\Us;

use Site\Query;

/**
 * Class News_And_Innovations_Query
 * @package Site\Queries\Posts\Select\Us
 */
class Us_Posts_Untagged_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$prefix    = $this->site->get_prefix();
		$ids_only  = false;
		$and_where = '';

		if ( in_array( 'include_acf', $data ) ) {
			$and_where .= " OR post_type LIKE 'acf%'";
		}

		if ( in_array( 'ids_only', $data ) ) {
			$ids_only = true;
		}

		$sql = "
			SELECT p.* 
			FROM {$prefix}posts p
			INNER JOIN {$prefix}term_relationships tr ON tr.object_id = p.ID
			INNER JOIN {$prefix}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$prefix}terms t ON t.term_id = tt.term_id
			WHERE t.slug IN ('news', 'innovations')
			AND t.slug != 'www-site-com'
			UNION
			SELECT * FROM {$prefix}posts
			WHERE post_type IN ('financials', 'surgeon_loc')
			{$and_where}";

		$results = $this->db()->get_results( $sql, ARRAY_A );

		if ( $ids_only ) {
			return array_column( $results, 'ID' );
		}

		return $results;
	}

}